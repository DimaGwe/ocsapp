<?php
/**
 * TwilioController - Twilio calls and SMS for the admin contact center
 *
 * Admin endpoints (session + CSRF):
 *   status, sendSMS (lead), getSMSTemplates, getCommunications, updateCommunicationOutcome,
 *   startCall (click-to-call bridge), callState (live call polling), recording (voicemail audio)
 *
 * Twilio webhooks (no session; every request must carry a valid X-Twilio-Signature):
 *   Outbound bridge: bridgeConnect -> bridgeDial -> bridgeContactStatus / bridgeComplete, agentLegStatus
 *   Inbound calls:   voiceInbound -> inboundScreen -> inboundAccept -> inboundComplete
 *                    -> voicemailDone / recordingStatus, inboundStatus
 *   SMS:             smsWebhook (inbound), smsStatus (delivery), callStatus (legacy lead calls)
 *
 * Outbound calls ring the agent's own phone first ("press 1 to connect"), then dial the contact
 * from the OCSAPP number, so the contact never sees the agent's personal number.
 */

namespace App\Controllers\Api;

require_once __DIR__ . '/../../Helpers/TwilioHelper.php';
require_once __DIR__ . '/../../Helpers/ContactCenterHelper.php';

use App\Helpers\TwilioHelper;
use App\Helpers\ContactCenterHelper;

class TwilioController
{
    private \PDO $db;

    /** Final call_logs.call_status values */
    private const FINAL_STATES = ['completed', 'no_answer', 'busy', 'failed', 'canceled', 'agent_no_answer', 'agent_declined', 'missed', 'voicemail'];

    public function __construct()
    {
        // CSRF is checked per admin action; webhooks are authenticated by Twilio's signature instead
        $this->db = \Database::getConnection();
    }

    // =====================================================================
    // Admin: configuration + lead SMS (existing lead CRM features)
    // =====================================================================

    /**
     * GET /api/twilio/status
     */
    public function status(): void
    {
        if (!$this->isAuthenticated()) {
            jsonResponse(['success' => false, 'error' => 'Unauthorized'], 401);
            return;
        }

        $configured = TwilioHelper::isConfigured();
        $uid = (int)$_SESSION['user']['id'];

        jsonResponse([
            'success' => true,
            'configured' => $configured,
            'phone_number' => $configured ? TwilioHelper::formatPhoneForDisplay(TwilioHelper::getPhoneNumber()) : null,
            'agent_phone' => ContactCenterHelper::agentPhone($uid)
        ]);
    }

    /**
     * Send SMS to a lead
     * POST /api/twilio/send-sms
     */
    public function sendSMS(): void
    {
        if (!$this->isAuthenticated()) {
            jsonResponse(['success' => false, 'error' => 'Unauthorized'], 401);
            return;
        }
        verifyCsrfForApi();

        $leadId = (int) post('lead_id', 0);
        $message = trim(post('message', ''));
        $templateId = post('template_id', null);

        if (!$leadId) {
            jsonResponse(['success' => false, 'error' => 'Lead ID required']);
            return;
        }

        $lead = $this->getLead($leadId);
        if (!$lead) {
            jsonResponse(['success' => false, 'error' => 'Lead not found']);
            return;
        }

        if (empty($lead['phone'])) {
            jsonResponse(['success' => false, 'error' => 'Lead has no phone number']);
            return;
        }

        if (!empty($lead['sms_opt_out'])) {
            jsonResponse(['success' => false, 'error' => 'Lead has opted out of SMS']);
            return;
        }

        if ($templateId && empty($message)) {
            $template = $this->getTemplate($templateId);
            if ($template) {
                $lang = $_SESSION['language'] ?? 'en';
                $body = ($lang === 'fr' && !empty($template['body_fr'])) ? $template['body_fr'] : $template['body'];

                $message = TwilioHelper::processTemplate($body, [
                    'name' => $lead['first_name'],
                    'first_name' => $lead['first_name'],
                    'last_name' => $lead['last_name'],
                    'company' => $lead['company_name'],
                    'sender' => $this->agentName()
                ]);

                $this->db->prepare("UPDATE sms_templates SET use_count = use_count + 1 WHERE id = ?")->execute([$template['id']]);
            }
        }

        if (empty($message)) {
            jsonResponse(['success' => false, 'error' => 'Message is required']);
            return;
        }

        $result = ContactCenterHelper::sendSms($lead['phone'], $message);

        if (!$result['success']) {
            jsonResponse(['success' => false, 'error' => $result['error'] ?? 'Failed to send SMS']);
            return;
        }

        $commId = $this->logCommunication([
            'lead_id' => $leadId,
            'type' => 'sms',
            'direction' => 'outbound',
            'phone_number' => $lead['phone'],
            'status' => $result['status'],
            'content' => $message,
            'twilio_sid' => $result['sid'],
            'created_by' => $_SESSION['user']['id'] ?? null
        ]);

        $this->updateLeadStats($leadId, 'sms');
        $this->logActivity($leadId, 'sms', 'SMS sent: "' . mb_substr($message, 0, 100) . (mb_strlen($message) > 100 ? '...' : '') . '"');

        jsonResponse([
            'success' => true,
            'message' => 'SMS sent successfully',
            'communication_id' => $commId,
            'twilio_sid' => $result['sid']
        ]);
    }

    /**
     * GET /api/twilio/sms-templates
     */
    public function getSMSTemplates(): void
    {
        if (!$this->isAuthenticated()) {
            jsonResponse(['success' => false, 'error' => 'Unauthorized'], 401);
            return;
        }

        $stmt = $this->db->query("
            SELECT id, name, name_fr, slug, body, body_fr, category
            FROM sms_templates
            WHERE is_active = TRUE
            ORDER BY category, name
        ");

        jsonResponse(['success' => true, 'templates' => $stmt->fetchAll(\PDO::FETCH_ASSOC)]);
    }

    /**
     * GET /api/twilio/communications?lead_id=X
     */
    public function getCommunications(): void
    {
        if (!$this->isAuthenticated()) {
            jsonResponse(['success' => false, 'error' => 'Unauthorized'], 401);
            return;
        }

        $leadId = (int) ($_GET['lead_id'] ?? 0);
        if (!$leadId) {
            jsonResponse(['success' => false, 'error' => 'Lead ID required']);
            return;
        }

        $stmt = $this->db->prepare("
            SELECT lc.*, CONCAT(u.first_name, ' ', u.last_name) as created_by_name
            FROM lead_communications lc
            LEFT JOIN users u ON lc.created_by = u.id
            WHERE lc.lead_id = ?
            ORDER BY lc.created_at DESC
            LIMIT 50
        ");
        $stmt->execute([$leadId]);

        jsonResponse(['success' => true, 'communications' => $stmt->fetchAll(\PDO::FETCH_ASSOC)]);
    }

    /**
     * POST /api/twilio/update-communication
     */
    public function updateCommunicationOutcome(): void
    {
        if (!$this->isAuthenticated()) {
            jsonResponse(['success' => false, 'error' => 'Unauthorized'], 401);
            return;
        }
        verifyCsrfForApi();

        $commId = (int) post('communication_id', 0);
        $outcome = post('outcome', '');
        $notes = post('notes', '');

        if (!$commId) {
            jsonResponse(['success' => false, 'error' => 'Communication ID required']);
            return;
        }

        $updates = [];
        if ($outcome) {
            $updates['outcome'] = $outcome;
        }
        if ($notes) {
            $updates['notes'] = $notes;
        }
        if (empty($updates)) {
            jsonResponse(['success' => false, 'error' => 'Nothing to update']);
            return;
        }

        $this->updateCommunication($commId, $updates);
        jsonResponse(['success' => true, 'message' => 'Updated successfully']);
    }

    // =====================================================================
    // Admin: click-to-call bridge
    // =====================================================================

    /**
     * Start a call: Twilio rings the agent, the agent presses 1, then the contact is dialed.
     * POST /api/twilio/call  {phone, name, contact_type, contact_id, email, ticket_id}
     */
    public function startCall(): void
    {
        if (!$this->isAuthenticated()) {
            jsonResponse(['success' => false, 'error' => 'Unauthorized'], 401);
            return;
        }
        verifyCsrfForApi();

        $in = $this->input();
        $agentId = (int)$_SESSION['user']['id'];

        if (!TwilioHelper::isConfigured()) {
            jsonResponse(['success' => false, 'error' => 'Calling is not set up yet (Twilio settings missing).']);
            return;
        }

        $agentPhone = ContactCenterHelper::agentPhone($agentId);
        if (!$agentPhone) {
            jsonResponse(['success' => false, 'code' => 'no_agent_phone', 'error' => 'Set the phone Twilio should ring for you on the Agent Dashboard first.']);
            return;
        }

        $to = ContactCenterHelper::e164($in['phone'] ?? '');
        if (!$to) {
            jsonResponse(['success' => false, 'error' => 'That phone number is not valid.']);
            return;
        }
        if ($to === $agentPhone) {
            jsonResponse(['success' => false, 'error' => 'That is your own calling phone.']);
            return;
        }

        $types = ['buyer', 'seller', 'driver', 'supplier', 'lead', 'unknown'];
        $type = in_array($in['contact_type'] ?? '', $types, true) ? $in['contact_type'] : 'unknown';
        $contactId = (int)($in['contact_id'] ?? 0) ?: null;
        $name = trim((string)($in['name'] ?? ''));

        // Fill in who this is when the caller only passed a number
        if (!$contactId || $name === '') {
            if ($match = ContactCenterHelper::findContactByPhone($to)) {
                $type = $contactId ? $type : $match['type'];
                $contactId = $contactId ?: $match['id'];
                $name = $name !== '' ? $name : $match['name'];
            }
        }

        $ticketId = (int)($in['ticket_id'] ?? 0) ?: null;

        $this->db->prepare("
            INSERT INTO call_logs
                (agent_id, direction, contact_type, contact_id, contact_name, contact_phone, contact_email,
                 outcome, ticket_id, call_status, needs_outcome)
            VALUES (?, 'outbound', ?, ?, ?, ?, ?, 'other', ?, 'agent_ringing', 1)
        ")->execute([
            $agentId, $type, $contactId, mb_substr($name, 0, 120), $to,
            mb_substr((string)($in['email'] ?? ''), 0, 180), $ticketId
        ]);
        $callId = (int)$this->db->lastInsertId();

        $base = TwilioHelper::appUrl() . '/api/twilio';
        $result = TwilioHelper::makeCall($agentPhone, "$base/bridge-connect?call=$callId", [
            'statusCallback' => "$base/agent-leg-status?call=$callId",
            'timeout' => 25
        ]);

        if (!$result['success']) {
            $this->setCallStatus($callId, 'failed');
            $this->db->prepare("UPDATE call_logs SET needs_outcome = 0, notes = ? WHERE id = ?")
                ->execute(['Call could not start: ' . ($result['error'] ?? 'unknown error'), $callId]);
            jsonResponse(['success' => false, 'error' => $result['error'] ?? 'Failed to start the call']);
            return;
        }

        $this->db->prepare("UPDATE call_logs SET twilio_call_sid = ? WHERE id = ?")->execute([$result['sid'], $callId]);

        jsonResponse(['success' => true, 'call_log_id' => $callId, 'agent_phone' => TwilioHelper::formatPhoneForDisplay($agentPhone)]);
    }

    /**
     * Live state for the dialer bar.
     * GET /api/twilio/call-state?id=X
     */
    public function callState(): void
    {
        if (!$this->isAuthenticated()) {
            jsonResponse(['success' => false, 'error' => 'Unauthorized'], 401);
            return;
        }

        $call = $this->getCallLog((int)($_GET['id'] ?? 0));
        if (!$call || (int)$call['agent_id'] !== (int)$_SESSION['user']['id']) {
            jsonResponse(['success' => false, 'error' => 'Call not found'], 404);
            return;
        }

        $elapsed = null;
        if ($call['answered_at']) {
            $elapsed = in_array($call['call_status'], self::FINAL_STATES, true) && $call['duration_seconds'] !== null
                ? (int)$call['duration_seconds']
                : max(0, time() - strtotime($call['answered_at']));
        }

        jsonResponse([
            'success' => true,
            'id' => (int)$call['id'],
            'status' => $call['call_status'],
            'final' => in_array($call['call_status'], self::FINAL_STATES, true),
            'elapsed' => $elapsed,
            'duration' => $call['duration_seconds'] !== null ? (int)$call['duration_seconds'] : null,
            'suggested_outcome' => $this->suggestedOutcome($call['call_status']),
            'contact' => [
                'name' => $call['contact_name'],
                'phone' => $call['contact_phone'],
                'email' => $call['contact_email'],
                'type' => $call['contact_type'],
                'id' => $call['contact_id'] ? (int)$call['contact_id'] : 0
            ]
        ]);
    }

    /**
     * Stream a voicemail recording to an admin.
     * GET /api/twilio/recording?call=X
     */
    public function recording(): void
    {
        if (!$this->isAuthenticated()) {
            http_response_code(401);
            exit;
        }

        $call = $this->getCallLog((int)($_GET['call'] ?? 0));
        $audio = ($call && $call['recording_sid']) ? TwilioHelper::fetchRecordingMp3($call['recording_sid']) : null;

        if ($audio === null) {
            http_response_code(404);
            header('Content-Type: text/plain');
            echo 'Recording not available';
            exit;
        }

        header('Content-Type: audio/mpeg');
        header('Content-Length: ' . strlen($audio));
        header('Cache-Control: private, max-age=3600');
        header('Content-Disposition: inline; filename="voicemail-' . (int)$call['id'] . '.mp3"');
        echo $audio;
        exit;
    }

    // =====================================================================
    // Webhooks: outbound bridge
    // =====================================================================

    /**
     * Agent picked up: make sure it is a person (not voicemail) before dialing the contact.
     * POST /api/twilio/bridge-connect?call=X
     */
    public function bridgeConnect(): void
    {
        $this->requireTwilio();
        $call = $this->getCallLog((int)($_GET['call'] ?? 0));
        if (!$call) {
            $this->twiml('<Hangup/>');
        }

        $this->setCallStatus((int)$call['id'], 'agent_answered');

        $who = $call['contact_name'] !== '' ? $call['contact_name'] : 'the contact';
        $action = $this->hook('bridge-dial', ['call' => $call['id']]);

        $this->twiml(
            '<Gather numDigits="1" timeout="10" action="' . $this->xml($action) . '" method="POST">'
            . $this->say('OCSAPP call to ' . $who . '. Press 1 to connect.', 'en')
            . '</Gather>'
            . $this->say('No key pressed. The call was not connected.', 'en')
            . '<Hangup/>'
        );
    }

    /**
     * POST /api/twilio/bridge-dial?call=X  (Gather result)
     */
    public function bridgeDial(): void
    {
        $this->requireTwilio();
        $call = $this->getCallLog((int)($_GET['call'] ?? 0));
        if (!$call) {
            $this->twiml('<Hangup/>');
        }

        if (($_POST['Digits'] ?? '') !== '1') {
            $this->setCallStatus((int)$call['id'], 'agent_declined');
            $this->twiml('<Hangup/>');
        }

        $this->setCallStatus((int)$call['id'], 'contact_ringing');

        $callerId = TwilioHelper::getPhoneNumber();
        $this->twiml(
            $this->say('Connecting.', 'en')
            . '<Dial callerId="' . $this->xml($callerId) . '" timeout="30" action="' . $this->xml($this->hook('bridge-complete', ['call' => $call['id']])) . '" method="POST">'
            . '<Number statusCallbackEvent="answered" statusCallback="' . $this->xml($this->hook('bridge-contact-status', ['call' => $call['id']])) . '" statusCallbackMethod="POST">'
            . $this->xml($call['contact_phone'])
            . '</Number></Dial>'
        );
    }

    /**
     * Contact answered.
     * POST /api/twilio/bridge-contact-status?call=X
     */
    public function bridgeContactStatus(): void
    {
        $this->requireTwilio();
        $callId = (int)($_GET['call'] ?? 0);

        if (in_array($_POST['CallStatus'] ?? '', ['in-progress', 'answered'], true)) {
            $this->db->prepare("UPDATE call_logs SET call_status = 'in_progress', answered_at = COALESCE(answered_at, NOW()) WHERE id = ?")
                ->execute([$callId]);
        }
        $this->emptyOk();
    }

    /**
     * Dial to the contact ended.
     * POST /api/twilio/bridge-complete?call=X
     */
    public function bridgeComplete(): void
    {
        $this->requireTwilio();
        $call = $this->getCallLog((int)($_GET['call'] ?? 0));
        if ($call) {
            $status = match ($_POST['DialCallStatus'] ?? '') {
                'completed', 'answered' => 'completed',
                'busy' => 'busy',
                'no-answer' => 'no_answer',
                'canceled' => 'canceled',
                default => 'failed'
            };
            $duration = (int)($_POST['DialCallDuration'] ?? 0);
            $this->finishOutbound($call, $status, $status === 'completed' ? $duration : 0);
        }
        $this->twiml('<Hangup/>');
    }

    /**
     * Status of the leg to the agent's phone (covers "agent never answered" and hang-ups mid-call).
     * POST /api/twilio/agent-leg-status?call=X
     */
    public function agentLegStatus(): void
    {
        $this->requireTwilio();
        $call = $this->getCallLog((int)($_GET['call'] ?? 0));
        $twilioStatus = $_POST['CallStatus'] ?? '';

        if ($call && in_array($twilioStatus, ['completed', 'no-answer', 'busy', 'failed', 'canceled'], true)
            && !in_array($call['call_status'], self::FINAL_STATES, true)) {

            if (in_array($call['call_status'], ['agent_ringing', 'agent_answered'], true)) {
                $this->finishOutbound($call, $call['call_status'] === 'agent_answered' ? 'agent_declined' : 'agent_no_answer', 0);
            } elseif ($call['call_status'] === 'in_progress') {
                $duration = $call['answered_at'] ? max(0, time() - strtotime($call['answered_at'])) : 0;
                $this->finishOutbound($call, 'completed', $duration);
            } else {
                $this->finishOutbound($call, 'canceled', 0);
            }
        }
        $this->emptyOk();
    }

    // =====================================================================
    // Webhooks: inbound calls to the OCSAPP number
    // =====================================================================

    /**
     * Number's "A call comes in" webhook.
     * POST /api/twilio/voice-inbound
     */
    public function voiceInbound(): void
    {
        $this->requireTwilio();

        $from = ContactCenterHelper::e164($_POST['From'] ?? '') ?? (string)($_POST['From'] ?? '');
        $contact = ContactCenterHelper::findContactByPhone($from);

        $this->db->prepare("
            INSERT INTO call_logs
                (agent_id, direction, contact_type, contact_id, contact_name, contact_phone, contact_email,
                 outcome, twilio_call_sid, call_status, needs_outcome)
            VALUES (NULL, 'inbound', ?, ?, ?, ?, ?, 'other', ?, 'ringing_agents', 0)
        ")->execute([
            $contact['type'] ?? 'unknown', $contact['id'] ?? null, mb_substr($contact['name'] ?? '', 0, 120),
            $from, mb_substr($contact['email'] ?? '', 0, 180), $_POST['CallSid'] ?? null
        ]);
        $callId = (int)$this->db->lastInsertId();

        $greeting = $this->say('Bienvenue chez OCSAPP.', 'fr') . $this->say('Welcome to OCSAPP.', 'en');
        $agents = ContactCenterHelper::availableAgents();

        if (empty($agents)) {
            $this->twiml($greeting . $this->voicemailPrompt($callId));
        }

        $numbers = '';
        foreach ($agents as $agent) {
            $numbers .= '<Number url="' . $this->xml($this->hook('inbound-screen', ['call' => $callId, 'agent' => $agent['id']])) . '" method="POST">'
                . $this->xml($agent['phone']) . '</Number>';
        }

        $this->twiml(
            $greeting
            . $this->say('Veuillez patienter, nous vous mettons en communication.', 'fr')
            . $this->say('Please hold while we connect you.', 'en')
            . '<Dial timeout="20" answerOnBridge="true" action="' . $this->xml($this->hook('inbound-complete', ['call' => $callId])) . '" method="POST">'
            . $numbers
            . '</Dial>'
        );
    }

    /**
     * Played to the agent who picks up, before the caller is connected ("press 1 to accept").
     * POST /api/twilio/inbound-screen?call=X&agent=Y
     */
    public function inboundScreen(): void
    {
        $this->requireTwilio();
        $call = $this->getCallLog((int)($_GET['call'] ?? 0));
        $who = ($call && $call['contact_name'] !== '') ? $call['contact_name'] : 'a caller';
        $action = $this->hook('inbound-accept', ['call' => (int)($_GET['call'] ?? 0), 'agent' => (int)($_GET['agent'] ?? 0)]);

        $this->twiml(
            '<Gather numDigits="1" timeout="8" action="' . $this->xml($action) . '" method="POST">'
            . $this->say('Incoming OCSAPP call from ' . $who . '. Press 1 to accept.', 'en')
            . '</Gather>'
            . '<Hangup/>'
        );
    }

    /**
     * POST /api/twilio/inbound-accept?call=X&agent=Y
     */
    public function inboundAccept(): void
    {
        $this->requireTwilio();

        if (($_POST['Digits'] ?? '') !== '1') {
            $this->twiml('<Hangup/>');
        }

        $this->db->prepare("
            UPDATE call_logs SET agent_id = ?, call_status = 'in_progress', answered_at = NOW(), needs_outcome = 1
            WHERE id = ? AND direction = 'inbound'
        ")->execute([(int)($_GET['agent'] ?? 0) ?: null, (int)($_GET['call'] ?? 0)]);

        // Empty response: Twilio bridges the agent to the caller
        $this->twiml('');
    }

    /**
     * Dial to the agents ended: done if someone took it, otherwise voicemail.
     * POST /api/twilio/inbound-complete?call=X
     */
    public function inboundComplete(): void
    {
        $this->requireTwilio();
        $callId = (int)($_GET['call'] ?? 0);
        $call = $this->getCallLog($callId);

        if ($call && ($_POST['DialCallStatus'] ?? '') === 'completed' && $call['call_status'] === 'in_progress') {
            $this->db->prepare("UPDATE call_logs SET call_status = 'completed', duration_seconds = ? WHERE id = ?")
                ->execute([(int)($_POST['DialCallDuration'] ?? 0), $callId]);
            $this->twiml('<Hangup/>');
        }

        $this->twiml($this->voicemailPrompt($callId));
    }

    /**
     * Record verb finished (the audio may still be processing; recordingStatus files the ticket).
     * POST /api/twilio/voicemail-done?call=X
     */
    public function voicemailDone(): void
    {
        $this->requireTwilio();
        $this->twiml($this->say('Merci, nous vous rappellerons.', 'fr') . $this->say('Thank you, we will call you back.', 'en') . '<Hangup/>');
    }

    /**
     * Voicemail audio ready: file it as a support ticket (or on the caller's open ticket).
     * POST /api/twilio/recording-status?call=X
     */
    public function recordingStatus(): void
    {
        $this->requireTwilio();
        $call = $this->getCallLog((int)($_GET['call'] ?? 0));

        if (!$call || ($_POST['RecordingStatus'] ?? '') !== 'completed') {
            $this->emptyOk();
        }

        $seconds = (int)($_POST['RecordingDuration'] ?? 0);
        if ($seconds < 2) {
            // Hung up at the beep: treat as a missed call
            $this->markMissed($call);
            $this->emptyOk();
        }

        $this->db->prepare("
            UPDATE call_logs
            SET call_status = 'voicemail', outcome = 'voicemail', recording_sid = ?, recording_duration = ?,
                needs_outcome = 0, notes = CONCAT(IFNULL(notes, ''), ?)
            WHERE id = ?
        ")->execute([$_POST['RecordingSid'] ?? null, $seconds, 'Voicemail left (' . $this->mmss($seconds) . ').', $call['id']]);

        $who = $call['contact_name'] !== '' ? $call['contact_name'] : TwilioHelper::formatPhoneForDisplay($call['contact_phone']);
        $listen = '/api/twilio/recording?call=' . (int)$call['id'];
        $message = 'Voicemail (' . $this->mmss($seconds) . '). Listen: ' . $listen;

        $ticket = ContactCenterHelper::findActiveTicketByPhone($call['contact_phone']);
        if ($ticket) {
            $ticketId = (int)$ticket['id'];
            ContactCenterHelper::addTicketMessage($ticketId, $message, 'contact', null, $who, false, 'voicemail');
            if (in_array($ticket['status'], ['resolved', 'pending_contact'], true)) {
                $this->db->prepare("UPDATE support_tickets SET status = 'open', resolved_at = NULL WHERE id = ?")->execute([$ticketId]);
            }
        } else {
            $contact = $call['contact_id'] ? ['type' => $call['contact_type'], 'id' => (int)$call['contact_id'], 'name' => $call['contact_name'], 'email' => $call['contact_email']] : null;
            $ticketId = ContactCenterHelper::createTicket([
                'subject' => 'Voicemail from ' . $who,
                'channel' => 'phone',
                'contact' => $contact,
                'contact_phone' => $call['contact_phone'],
                'description' => $message,
                'created_by' => 0
            ]);
        }

        $this->db->prepare("UPDATE call_logs SET ticket_id = ?, ticket_subject = ? WHERE id = ?")
            ->execute([$ticketId, 'Voicemail from ' . $who, $call['id']]);

        ContactCenterHelper::notify('New voicemail', "Voicemail from {$who} (" . $this->mmss($seconds) . ')', '/admin/support/view?id=' . $ticketId,
            !empty($ticket['assigned_to']) ? (int)$ticket['assigned_to'] : null, 'voicemail');

        $this->emptyOk();
    }

    /**
     * Number's call status callback: catches callers who hang up before voicemail.
     * POST /api/twilio/inbound-status
     */
    public function inboundStatus(): void
    {
        $this->requireTwilio();

        if (($_POST['CallStatus'] ?? '') === 'completed' || in_array($_POST['CallStatus'] ?? '', ['busy', 'no-answer', 'failed', 'canceled'], true)) {
            $stmt = $this->db->prepare("SELECT * FROM call_logs WHERE twilio_call_sid = ? AND direction = 'inbound' LIMIT 1");
            $stmt->execute([$_POST['CallSid'] ?? '']);
            $call = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($call && in_array($call['call_status'], ['ringing_agents', 'voicemail_prompt'], true)) {
                // Give a voicemail that is still processing a moment to land before calling it missed
                if ($call['call_status'] === 'voicemail_prompt' && (int)($_POST['CallDuration'] ?? 0) > 8) {
                    $this->db->prepare("UPDATE call_logs SET call_status = 'voicemail_pending' WHERE id = ?")->execute([$call['id']]);
                } else {
                    $this->markMissed($call);
                }
            } elseif ($call && $call['call_status'] === 'in_progress') {
                $duration = $call['answered_at'] ? max(0, time() - strtotime($call['answered_at'])) : 0;
                $this->db->prepare("UPDATE call_logs SET call_status = 'completed', duration_seconds = ? WHERE id = ?")->execute([$duration, $call['id']]);
            }
        }
        $this->emptyOk();
    }

    // =====================================================================
    // Webhooks: SMS
    // =====================================================================

    /**
     * Inbound SMS: opt-out keywords, then route to the sender's open ticket, their lead record,
     * or a new SMS ticket.
     * POST /api/twilio/sms-webhook
     */
    public function smsWebhook(): void
    {
        $this->requireTwilio();

        $from = ContactCenterHelper::e164($_POST['From'] ?? '') ?? (string)($_POST['From'] ?? '');
        $body = trim((string)($_POST['Body'] ?? ''));
        $messageSid = (string)($_POST['MessageSid'] ?? '');

        if ($from === '' || $body === '') {
            $this->twiml('');
        }

        $keyword = ContactCenterHelper::normalizeKeyword($body);
        $contact = ContactCenterHelper::findContactByPhone($from);
        $lead = ($contact && $contact['type'] === 'lead') ? $this->getLead($contact['id']) : null;
        $ticket = ContactCenterHelper::findActiveTicketByPhone($from);
        $who = $contact['name'] ?? TwilioHelper::formatPhoneForDisplay($from);

        // Opt-out / opt-in ----------------------------------------------------
        if (in_array($keyword, ContactCenterHelper::OPT_OUT_KEYWORDS, true)) {
            ContactCenterHelper::optOut($from, $keyword);
            if ($lead) {
                $this->db->prepare("UPDATE leads SET sms_opt_out = 1 WHERE id = ?")->execute([$lead['id']]);
                $this->logActivity((int)$lead['id'], 'sms', "Opted out of SMS (replied {$keyword})");
            }
            if ($ticket) {
                ContactCenterHelper::addTicketMessage((int)$ticket['id'], "Contact opted out of SMS (replied {$keyword}).", 'system', null, null, true, 'sms');
            }
            // Twilio answers the English keywords itself; the French one is ours to confirm
            if (in_array($keyword, ['ARRET', 'ARRÊT'], true)) {
                $this->twiml('<Message>' . $this->xml('OCSAPP : vous ne recevrez plus de textos. Répondez START pour vous réabonner. You are unsubscribed; reply START to resubscribe.') . '</Message>');
            }
            $this->twiml('');
        }

        if (in_array($keyword, ContactCenterHelper::OPT_IN_KEYWORDS, true) && ContactCenterHelper::isOptedOut($from)) {
            ContactCenterHelper::optIn($from);
            if ($lead) {
                $this->db->prepare("UPDATE leads SET sms_opt_out = 0 WHERE id = ?")->execute([$lead['id']]);
                $this->logActivity((int)$lead['id'], 'sms', "Opted back in to SMS (replied {$keyword})");
            }
            $this->twiml('');
        }

        // Reply on an open ticket ---------------------------------------------
        if ($ticket) {
            $ticketId = (int)$ticket['id'];
            ContactCenterHelper::addTicketMessage($ticketId, $body, 'contact', null, $who, false, 'sms');
            if (in_array($ticket['status'], ['resolved', 'pending_contact'], true)) {
                $this->db->prepare("UPDATE support_tickets SET status = 'open', resolved_at = NULL WHERE id = ?")->execute([$ticketId]);
            }
            ContactCenterHelper::notify('New SMS reply', "{$who}: " . mb_substr($body, 0, 80), '/admin/support/view?id=' . $ticketId, $ticket['assigned_to'] ? (int)$ticket['assigned_to'] : null, 'comment-sms');
            $this->twiml('');
        }

        // Known lead: keep it in the lead's CRM history ------------------------
        if ($lead) {
            $this->logCommunication([
                'lead_id' => $lead['id'],
                'type' => 'sms',
                'direction' => 'inbound',
                'phone_number' => $from,
                'status' => 'received',
                'content' => $body,
                'twilio_sid' => $messageSid
            ]);
            $this->logActivity((int)$lead['id'], 'sms', 'SMS received: "' . mb_substr($body, 0, 100) . '"');
            ContactCenterHelper::notify('New SMS from a lead', "{$who}: " . mb_substr($body, 0, 80), '/admin/leads/view?id=' . (int)$lead['id'], null, 'comment-sms');
            $this->twiml('');
        }

        // Anyone else: new SMS ticket ------------------------------------------
        $ticketId = ContactCenterHelper::createTicket([
            'subject' => 'SMS from ' . $who,
            'channel' => 'sms',
            'contact' => $contact,
            'contact_phone' => $from,
            'description' => $body,
            'created_by' => 0
        ]);
        ContactCenterHelper::notify('New SMS ticket', "{$who}: " . mb_substr($body, 0, 80), '/admin/support/view?id=' . $ticketId, null, 'comment-sms');

        $this->twiml('');
    }

    /**
     * SMS delivery status (lead CRM history)
     * POST /api/twilio/sms-status
     */
    public function smsStatus(): void
    {
        $this->requireTwilio();

        $messageSid = $_POST['MessageSid'] ?? '';
        $messageStatus = $_POST['MessageStatus'] ?? '';
        $errorCode = $_POST['ErrorCode'] ?? null;

        if ($messageSid) {
            $this->db->prepare("UPDATE lead_communications SET status = ?, updated_at = NOW() WHERE twilio_sid = ?")
                ->execute([$messageStatus, $messageSid]);

            if ($errorCode) {
                $this->db->prepare("UPDATE lead_communications SET notes = CONCAT(IFNULL(notes, ''), ' Error: ', ?) WHERE twilio_sid = ?")
                    ->execute([$errorCode, $messageSid]);
            }
        }
        $this->emptyOk();
    }

    /**
     * Legacy lead call status (rows created before the bridge existed)
     * POST /api/twilio/call-status?comm_id=X
     */
    public function callStatus(): void
    {
        $this->requireTwilio();

        $commId = (int) ($_GET['comm_id'] ?? 0);
        if ($commId) {
            $this->updateCommunication($commId, [
                'status' => $_POST['CallStatus'] ?? '',
                'duration' => (int)($_POST['CallDuration'] ?? 0)
            ]);
        }
        $this->emptyOk();
    }

    // =====================================================================
    // Helpers
    // =====================================================================

    private function isAuthenticated(): bool
    {
        return !empty($_SESSION['user']['id']) && \AdminPermissionHelper::isAdminRole($_SESSION['user']['role'] ?? null);
    }

    /** Reject anything that is not a correctly signed Twilio request */
    private function requireTwilio(): void
    {
        if (!TwilioHelper::isValidWebhookRequest()) {
            http_response_code(403);
            header('Content-Type: text/plain');
            echo 'Invalid Twilio signature';
            exit;
        }
    }

    private function input(): array
    {
        $json = json_decode(file_get_contents('php://input'), true);
        return is_array($json) ? $json : $_POST;
    }

    private function agentName(): string
    {
        return trim(($_SESSION['user']['first_name'] ?? '') . ' ' . ($_SESSION['user']['last_name'] ?? '')) ?: 'OCSAPP';
    }

    /** Signed webhook URL on this server */
    private function hook(string $path, array $query = []): string
    {
        return TwilioHelper::appUrl() . '/api/twilio/' . $path . ($query ? '?' . http_build_query($query) : '');
    }

    private function xml(string $s): string
    {
        return htmlspecialchars($s, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    /** <Say> in Quebec French or English (Amazon Polly voices) */
    private function say(string $text, string $lang): string
    {
        return $lang === 'fr'
            ? '<Say voice="Polly.Chantal" language="fr-CA">' . $this->xml($text) . '</Say>'
            : '<Say voice="Polly.Joanna" language="en-US">' . $this->xml($text) . '</Say>';
    }

    private function voicemailPrompt(int $callId): string
    {
        $this->setCallStatus($callId, 'voicemail_prompt');

        return $this->say('Aucun agent n\'est disponible. Laissez un message après le bip et nous vous rappellerons.', 'fr')
            . $this->say('No one is available right now. Leave a message after the beep and we will call you back.', 'en')
            . '<Record maxLength="180" timeout="5" playBeep="true" trim="trim-silence"'
            . ' action="' . $this->xml($this->hook('voicemail-done', ['call' => $callId])) . '" method="POST"'
            . ' recordingStatusCallback="' . $this->xml($this->hook('recording-status', ['call' => $callId])) . '"'
            . ' recordingStatusCallbackMethod="POST"/>'
            . $this->say('Au revoir. Goodbye.', 'fr')
            . '<Hangup/>';
    }

    /** Output TwiML and stop */
    private function twiml(string $inner): never
    {
        header('Content-Type: text/xml');
        echo '<?xml version="1.0" encoding="UTF-8"?><Response>' . $inner . '</Response>';
        exit;
    }

    private function emptyOk(): never
    {
        $this->twiml('');
    }

    private function mmss(int $seconds): string
    {
        return floor($seconds / 60) . ':' . str_pad((string)($seconds % 60), 2, '0', STR_PAD_LEFT);
    }

    private function getCallLog(int $id): ?array
    {
        if (!$id) {
            return null;
        }
        $stmt = $this->db->prepare("SELECT * FROM call_logs WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    private function setCallStatus(int $callId, string $status): void
    {
        $this->db->prepare("UPDATE call_logs SET call_status = ? WHERE id = ?")->execute([$status, $callId]);
    }

    private function suggestedOutcome(?string $status): ?string
    {
        return match ($status) {
            'no_answer', 'busy', 'canceled' => 'no_answer',
            'failed' => 'wrong_number',
            'voicemail' => 'voicemail',
            default => null
        };
    }

    /**
     * Final state for an outbound bridge call: log duration, mirror to the lead CRM and the ticket thread.
     * Safe to call twice (bridge-complete and the agent leg status can both arrive).
     */
    private function finishOutbound(array $call, string $status, int $duration): void
    {
        $wasFinal = in_array($call['call_status'], self::FINAL_STATES, true);

        // Agent never reached the contact: nothing to disposition
        $needsOutcome = in_array($status, ['agent_no_answer', 'agent_declined'], true) ? 0 : 1;
        $suggested = $this->suggestedOutcome($status);

        $this->db->prepare("
            UPDATE call_logs
            SET call_status = ?, duration_seconds = ?, needs_outcome = IF(needs_outcome = 1, ?, 0),
                outcome = IF(outcome = 'other' AND ? IS NOT NULL, ?, outcome)
            WHERE id = ?
        ")->execute([$status, $duration ?: null, $needsOutcome, $suggested, $suggested, $call['id']]);

        if ($wasFinal || !$needsOutcome) {
            return;
        }

        $label = match ($status) {
            'completed' => 'Call completed (' . $this->mmss($duration) . ')',
            'busy' => 'Call: line busy',
            'no_answer' => 'Call: no answer',
            'canceled' => 'Call canceled',
            default => 'Call failed'
        };

        if ($call['contact_type'] === 'lead' && $call['contact_id']) {
            $this->db->prepare("
                INSERT INTO lead_communications (lead_id, type, direction, phone_number, status, duration, twilio_sid, created_by)
                VALUES (?, 'call', 'outbound', ?, ?, ?, ?, ?)
            ")->execute([$call['contact_id'], $call['contact_phone'], $status, $duration ?: null, $call['twilio_call_sid'], $call['agent_id']]);
            $this->updateLeadStats((int)$call['contact_id'], 'call');
            $this->logActivity((int)$call['contact_id'], 'call', $label, (int)$call['agent_id']);
        }

        if ($call['ticket_id']) {
            ContactCenterHelper::addTicketMessage((int)$call['ticket_id'], "Outbound {$label}", 'system', null, null, true, 'call');
        }
    }

    private function markMissed(array $call): void
    {
        $this->db->prepare("UPDATE call_logs SET call_status = 'missed', outcome = 'no_answer', needs_outcome = 0 WHERE id = ?")
            ->execute([$call['id']]);

        $who = $call['contact_name'] !== '' ? $call['contact_name'] : TwilioHelper::formatPhoneForDisplay($call['contact_phone']);
        ContactCenterHelper::notify('Missed call', "Missed call from {$who}. No voicemail left.", '/admin/call-log?q=' . urlencode(ContactCenterHelper::last10($call['contact_phone'])), null, 'phone-slash');
    }

    private function getLead(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM leads WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    private function getTemplate($id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM sms_templates WHERE id = ? OR slug = ?");
        $stmt->execute([$id, $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    private function logCommunication(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO lead_communications
            (lead_id, type, direction, phone_number, status, content, twilio_sid, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['lead_id'],
            $data['type'],
            $data['direction'] ?? 'outbound',
            $data['phone_number'],
            $data['status'] ?? 'initiated',
            $data['content'] ?? null,
            $data['twilio_sid'] ?? null,
            $data['created_by'] ?? null
        ]);

        return (int) $this->db->lastInsertId();
    }

    private function updateCommunication(int $id, array $data): void
    {
        $allowed = ['status', 'duration', 'answered_by', 'twilio_sid', 'outcome', 'notes'];
        $sets = [];
        $params = [];

        foreach ($data as $key => $value) {
            if (in_array($key, $allowed, true)) {
                $sets[] = "$key = ?";
                $params[] = $value;
            }
        }
        if (!$sets) {
            return;
        }

        $params[] = $id;
        $this->db->prepare("UPDATE lead_communications SET " . implode(', ', $sets) . ", updated_at = NOW() WHERE id = ?")
            ->execute($params);
    }

    private function updateLeadStats(int $leadId, string $type): void
    {
        if ($type === 'call') {
            $this->db->prepare("
                UPDATE leads SET total_calls = total_calls + 1, last_call_at = NOW(), last_contacted_at = NOW() WHERE id = ?
            ")->execute([$leadId]);
        } else {
            $this->db->prepare("
                UPDATE leads SET total_sms = total_sms + 1, last_sms_at = NOW(), last_contacted_at = NOW() WHERE id = ?
            ")->execute([$leadId]);
        }
    }

    private function logActivity(int $leadId, string $type, string $description, ?int $userId = null): void
    {
        $this->db->prepare("
            INSERT INTO lead_activities (lead_id, activity_type, description, created_by)
            VALUES (?, ?, ?, ?)
        ")->execute([$leadId, $type, $description, $userId ?? ($_SESSION['user']['id'] ?? null)]);
    }
}
