<?php
/**
 * TwilioController - API endpoints for Twilio integration
 *
 * Handles:
 * - Initiating calls
 * - Sending SMS
 * - Webhooks for call/SMS status updates
 * - TwiML generation
 */

namespace App\Controllers\Api;

use App\Helpers\TwilioHelper;

class TwilioController
{
    private \PDO $db;

    public function __construct()
    {
        // Verify CSRF token for state-changing requests
        verifyCsrfForApi();

        $this->db = \Database::getConnection();
    }

    /**
     * Check if Twilio is configured
     * GET /api/twilio/status
     */
    public function status(): void
    {
        $configured = TwilioHelper::isConfigured();

        jsonResponse([
            'success' => true,
            'configured' => $configured,
            'phone_number' => $configured ? TwilioHelper::formatPhoneForDisplay(TwilioHelper::getPhoneNumber()) : null
        ]);
    }

    /**
     * Send SMS to a lead
     * POST /api/twilio/send-sms
     */
    public function sendSMS(): void
    {
        // Verify authentication
        if (!$this->isAuthenticated()) {
            jsonResponse(['success' => false, 'error' => 'Unauthorized'], 401);
            return;
        }

        $leadId = (int) post('lead_id', 0);
        $message = trim(post('message', ''));
        $templateId = post('template_id', null);

        if (!$leadId) {
            jsonResponse(['success' => false, 'error' => 'Lead ID required']);
            return;
        }

        // Get lead info
        $lead = $this->getLead($leadId);
        if (!$lead) {
            jsonResponse(['success' => false, 'error' => 'Lead not found']);
            return;
        }

        if (empty($lead['phone'])) {
            jsonResponse(['success' => false, 'error' => 'Lead has no phone number']);
            return;
        }

        // Check opt-out
        if (!empty($lead['sms_opt_out'])) {
            jsonResponse(['success' => false, 'error' => 'Lead has opted out of SMS']);
            return;
        }

        // If using template, fetch and process it
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
                    'sender' => $_SESSION['user_name'] ?? 'OCSAPP'
                ]);

                // Increment template usage
                $this->db->prepare("UPDATE sms_templates SET use_count = use_count + 1 WHERE id = ?")->execute([$templateId]);
            }
        }

        if (empty($message)) {
            jsonResponse(['success' => false, 'error' => 'Message is required']);
            return;
        }

        // Send SMS via Twilio
        $baseUrl = rtrim($_ENV['APP_URL'] ?? '', '/');
        $result = TwilioHelper::sendSMS($lead['phone'], $message, [
            'statusCallback' => $baseUrl . '/api/twilio/sms-status'
        ]);

        if (!$result['success']) {
            jsonResponse(['success' => false, 'error' => $result['error'] ?? 'Failed to send SMS']);
            return;
        }

        // Log communication
        $commId = $this->logCommunication([
            'lead_id' => $leadId,
            'type' => 'sms',
            'direction' => 'outbound',
            'phone_number' => $lead['phone'],
            'status' => $result['status'],
            'content' => $message,
            'twilio_sid' => $result['sid'],
            'created_by' => $_SESSION['user_id'] ?? null
        ]);

        // Update lead stats
        $this->updateLeadStats($leadId, 'sms');

        // Log activity
        $this->logActivity($leadId, 'sms', 'SMS sent: "' . substr($message, 0, 100) . (strlen($message) > 100 ? '...' : '') . '"');

        jsonResponse([
            'success' => true,
            'message' => 'SMS sent successfully',
            'communication_id' => $commId,
            'twilio_sid' => $result['sid']
        ]);
    }

    /**
     * Initiate a call to a lead
     * POST /api/twilio/make-call
     */
    public function makeCall(): void
    {
        if (!$this->isAuthenticated()) {
            jsonResponse(['success' => false, 'error' => 'Unauthorized'], 401);
            return;
        }

        $leadId = (int) post('lead_id', 0);

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

        // Create communication record first
        $commId = $this->logCommunication([
            'lead_id' => $leadId,
            'type' => 'call',
            'direction' => 'outbound',
            'phone_number' => $lead['phone'],
            'status' => 'initiated',
            'created_by' => $_SESSION['user_id'] ?? null
        ]);

        $baseUrl = rtrim($_ENV['APP_URL'] ?? '', '/');

        // Initiate call
        $result = TwilioHelper::makeCall(
            $lead['phone'],
            $baseUrl . '/api/twilio/voice-connect?lead_id=' . $leadId,
            [
                'statusCallback' => $baseUrl . '/api/twilio/call-status?comm_id=' . $commId,
                'timeout' => 30
            ]
        );

        if (!$result['success']) {
            // Update communication as failed
            $this->updateCommunication($commId, ['status' => 'failed', 'notes' => $result['error']]);
            jsonResponse(['success' => false, 'error' => $result['error'] ?? 'Failed to initiate call']);
            return;
        }

        // Update with Twilio SID
        $this->updateCommunication($commId, ['twilio_sid' => $result['sid'], 'status' => $result['status']]);

        jsonResponse([
            'success' => true,
            'message' => 'Call initiated',
            'communication_id' => $commId,
            'twilio_sid' => $result['sid']
        ]);
    }

    /**
     * TwiML endpoint for connecting calls
     * GET /api/twilio/voice-connect
     */
    public function voiceConnect(): void
    {
        $leadId = (int) ($_GET['lead_id'] ?? 0);

        // Get the admin's phone number to connect to
        // For now, we'll use a simple dial-through where the call connects to the lead
        $lead = $leadId ? $this->getLead($leadId) : null;

        header('Content-Type: application/xml');

        if ($lead && !empty($lead['phone'])) {
            echo TwilioHelper::generateConnectTwiml($lead['phone'], [
                'timeout' => 30
            ]);
        } else {
            echo '<?xml version="1.0" encoding="UTF-8"?><Response><Say>Sorry, unable to connect this call.</Say></Response>';
        }
        exit;
    }

    /**
     * Webhook for call status updates
     * POST /api/twilio/call-status
     */
    public function callStatus(): void
    {
        $commId = (int) ($_GET['comm_id'] ?? 0);

        $callSid = post('CallSid', '');
        $callStatus = post('CallStatus', '');
        $duration = (int) post('CallDuration', 0);
        $answeredBy = post('AnsweredBy', null);

        if ($commId) {
            $updates = [
                'status' => $callStatus,
                'duration' => $duration
            ];

            if ($answeredBy) {
                $updates['answered_by'] = $answeredBy;
            }

            $this->updateCommunication($commId, $updates);

            // If call completed, update lead stats
            if (in_array($callStatus, ['completed', 'busy', 'no-answer', 'failed', 'canceled'])) {
                $comm = $this->getCommunication($commId);
                if ($comm) {
                    $this->updateLeadStats($comm['lead_id'], 'call');

                    // Log activity
                    $outcomeText = match($callStatus) {
                        'completed' => "Call completed ({$duration}s)",
                        'busy' => 'Call - Line busy',
                        'no-answer' => 'Call - No answer',
                        'failed' => 'Call failed',
                        'canceled' => 'Call canceled',
                        default => "Call status: {$callStatus}"
                    };
                    $this->logActivity($comm['lead_id'], 'call', $outcomeText);
                }
            }
        }

        // Twilio expects 200 OK
        http_response_code(200);
        echo '<?xml version="1.0" encoding="UTF-8"?><Response></Response>';
        exit;
    }

    /**
     * Webhook for SMS status updates
     * POST /api/twilio/sms-status
     */
    public function smsStatus(): void
    {
        $messageSid = post('MessageSid', '');
        $messageStatus = post('MessageStatus', '');
        $errorCode = post('ErrorCode', null);

        if ($messageSid) {
            $stmt = $this->db->prepare("
                UPDATE lead_communications
                SET status = ?, updated_at = NOW()
                WHERE twilio_sid = ?
            ");
            $stmt->execute([$messageStatus, $messageSid]);

            if ($errorCode) {
                $stmt = $this->db->prepare("
                    UPDATE lead_communications
                    SET notes = CONCAT(IFNULL(notes, ''), ' Error: ', ?)
                    WHERE twilio_sid = ?
                ");
                $stmt->execute([$errorCode, $messageSid]);
            }
        }

        http_response_code(200);
        exit;
    }

    /**
     * Webhook for incoming SMS
     * POST /api/twilio/sms-webhook
     */
    public function smsWebhook(): void
    {
        $from = post('From', '');
        $body = post('Body', '');
        $messageSid = post('MessageSid', '');

        if ($from && $body) {
            // Find lead by phone number
            $lead = $this->getLeadByPhone($from);

            if ($lead) {
                // Check for opt-out keywords
                $optOutKeywords = ['STOP', 'UNSUBSCRIBE', 'CANCEL', 'ARRET', 'ARRÊT'];
                $upperBody = strtoupper(trim($body));

                if (in_array($upperBody, $optOutKeywords)) {
                    // Mark lead as opted out
                    $this->db->prepare("UPDATE leads SET sms_opt_out = TRUE WHERE id = ?")->execute([$lead['id']]);

                    // Send confirmation
                    TwilioHelper::sendSMS($from, "You've been unsubscribed from OCSAPP messages. Reply START to resubscribe.");
                } else {
                    // Log incoming message
                    $this->logCommunication([
                        'lead_id' => $lead['id'],
                        'type' => 'sms',
                        'direction' => 'inbound',
                        'phone_number' => $from,
                        'status' => 'received',
                        'content' => $body,
                        'twilio_sid' => $messageSid
                    ]);

                    // Log activity
                    $this->logActivity($lead['id'], 'sms', 'SMS received: "' . substr($body, 0, 100) . '"');

                    // TODO: Notify admin of incoming message (email/push notification)
                }
            }
        }

        // Respond with empty TwiML
        header('Content-Type: application/xml');
        echo '<?xml version="1.0" encoding="UTF-8"?><Response></Response>';
        exit;
    }

    /**
     * Get SMS templates
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
        $templates = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        jsonResponse([
            'success' => true,
            'templates' => $templates
        ]);
    }

    /**
     * Get communication history for a lead
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
            SELECT
                lc.*,
                CONCAT(u.first_name, ' ', u.last_name) as created_by_name
            FROM lead_communications lc
            LEFT JOIN users u ON lc.created_by = u.id
            WHERE lc.lead_id = ?
            ORDER BY lc.created_at DESC
            LIMIT 50
        ");
        $stmt->execute([$leadId]);
        $communications = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        jsonResponse([
            'success' => true,
            'communications' => $communications
        ]);
    }

    /**
     * Update communication outcome/notes
     * POST /api/twilio/update-communication
     */
    public function updateCommunicationOutcome(): void
    {
        if (!$this->isAuthenticated()) {
            jsonResponse(['success' => false, 'error' => 'Unauthorized'], 401);
            return;
        }

        $commId = (int) post('communication_id', 0);
        $outcome = post('outcome', '');
        $notes = post('notes', '');

        if (!$commId) {
            jsonResponse(['success' => false, 'error' => 'Communication ID required']);
            return;
        }

        $updates = [];
        $params = [];

        if ($outcome) {
            $updates[] = 'outcome = ?';
            $params[] = $outcome;
        }

        if ($notes) {
            $updates[] = 'notes = ?';
            $params[] = $notes;
        }

        if (empty($updates)) {
            jsonResponse(['success' => false, 'error' => 'Nothing to update']);
            return;
        }

        $params[] = $commId;

        $stmt = $this->db->prepare("
            UPDATE lead_communications
            SET " . implode(', ', $updates) . ", updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute($params);

        jsonResponse(['success' => true, 'message' => 'Updated successfully']);
    }

    // ==================== Helper Methods ====================

    private function isAuthenticated(): bool
    {
        return !empty($_SESSION['user']['id']) && in_array($_SESSION['user']['role'] ?? '', ['admin', 'super_admin']);
    }

    private function getLead(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM leads WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    private function getLeadByPhone(string $phone): ?array
    {
        // Normalize phone for comparison
        $normalized = preg_replace('/[^0-9]/', '', $phone);
        $last10 = substr($normalized, -10);

        $stmt = $this->db->prepare("
            SELECT * FROM leads
            WHERE REPLACE(REPLACE(REPLACE(REPLACE(phone, '-', ''), ' ', ''), '(', ''), ')', '') LIKE ?
            LIMIT 1
        ");
        $stmt->execute(['%' . $last10]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    private function getTemplate($id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM sms_templates WHERE id = ? OR slug = ?");
        $stmt->execute([$id, $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    private function getCommunication(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM lead_communications WHERE id = ?");
        $stmt->execute([$id]);
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
        $sets = [];
        $params = [];

        foreach ($data as $key => $value) {
            $sets[] = "$key = ?";
            $params[] = $value;
        }

        $params[] = $id;

        $stmt = $this->db->prepare("
            UPDATE lead_communications
            SET " . implode(', ', $sets) . ", updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute($params);
    }

    private function updateLeadStats(int $leadId, string $type): void
    {
        if ($type === 'call') {
            $this->db->prepare("
                UPDATE leads
                SET total_calls = total_calls + 1,
                    last_call_at = NOW(),
                    last_contacted_at = NOW()
                WHERE id = ?
            ")->execute([$leadId]);
        } else {
            $this->db->prepare("
                UPDATE leads
                SET total_sms = total_sms + 1,
                    last_sms_at = NOW(),
                    last_contacted_at = NOW()
                WHERE id = ?
            ")->execute([$leadId]);
        }
    }

    private function logActivity(int $leadId, string $type, string $description): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO lead_activities (lead_id, activity_type, description, created_by)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $leadId,
            $type,
            $description,
            $_SESSION['user_id'] ?? null
        ]);
    }
}
