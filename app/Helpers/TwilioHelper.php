<?php
/**
 * TwilioHelper - Twilio API Integration for CRM
 *
 * Handles SMS sending, call initiation, and webhook processing
 * for the leads CRM system.
 */

namespace App\Helpers;

class TwilioHelper
{
    private static ?string $accountSid = null;
    private static ?string $authToken = null;
    private static ?string $phoneNumber = null;
    private static string $apiBase = 'https://api.twilio.com/2010-04-01';

    /**
     * Initialize Twilio credentials from environment
     */
    private static function init(): void
    {
        if (self::$accountSid === null) {
            self::$accountSid = setting('twilio_account_sid', '');
            self::$authToken = setting('twilio_auth_token', '');
            self::$phoneNumber = setting('twilio_phone_number', '');
        }
    }

    /**
     * Check if Twilio is configured
     */
    public static function isConfigured(): bool
    {
        self::init();
        return !empty(self::$accountSid) && !empty(self::$authToken) && !empty(self::$phoneNumber);
    }

    /**
     * Get the Twilio phone number
     */
    public static function getPhoneNumber(): string
    {
        self::init();
        return self::$phoneNumber;
    }

    /**
     * Get Account SID (for client-side token generation)
     */
    public static function getAccountSid(): string
    {
        self::init();
        return self::$accountSid;
    }

    /**
     * Send an SMS message
     *
     * @param string $to Phone number to send to (E.164 format)
     * @param string $message Message body
     * @param array $options Additional options (statusCallback, etc.)
     * @return array Response with success status and data
     */
    public static function sendSMS(string $to, string $message, array $options = []): array
    {
        self::init();

        if (!self::isConfigured()) {
            return [
                'success' => false,
                'error' => 'Twilio is not configured. Please add TWILIO_ACCOUNT_SID, TWILIO_AUTH_TOKEN, and TWILIO_PHONE_NUMBER to your .env file.'
            ];
        }

        // Format phone number
        $to = self::formatPhoneNumber($to);
        if (!$to) {
            return [
                'success' => false,
                'error' => 'Invalid phone number format'
            ];
        }

        $url = self::$apiBase . '/Accounts/' . self::$accountSid . '/Messages.json';

        $data = [
            'To' => $to,
            'From' => self::$phoneNumber,
            'Body' => $message
        ];

        // Add status callback if provided
        if (!empty($options['statusCallback'])) {
            $data['StatusCallback'] = $options['statusCallback'];
        }

        $response = self::makeRequest('POST', $url, $data);

        if (isset($response['sid'])) {
            return [
                'success' => true,
                'sid' => $response['sid'],
                'status' => $response['status'] ?? 'queued',
                'to' => $response['to'] ?? $to,
                'from' => $response['from'] ?? self::$phoneNumber,
                'body' => $response['body'] ?? $message
            ];
        }

        return [
            'success' => false,
            'error' => $response['message'] ?? 'Failed to send SMS',
            'code' => $response['code'] ?? null
        ];
    }

    /**
     * Initiate an outbound call
     *
     * @param string $to Phone number to call
     * @param string $twimlUrl URL that returns TwiML instructions
     * @param array $options Additional options
     * @return array Response with success status and data
     */
    public static function makeCall(string $to, string $twimlUrl, array $options = []): array
    {
        self::init();

        if (!self::isConfigured()) {
            return [
                'success' => false,
                'error' => 'Twilio is not configured'
            ];
        }

        $to = self::formatPhoneNumber($to);
        if (!$to) {
            return [
                'success' => false,
                'error' => 'Invalid phone number format'
            ];
        }

        $url = self::$apiBase . '/Accounts/' . self::$accountSid . '/Calls.json';

        $data = [
            'To' => $to,
            'From' => self::$phoneNumber,
            'Url' => $twimlUrl
        ];

        // Add optional parameters
        if (!empty($options['statusCallback'])) {
            $data['StatusCallback'] = $options['statusCallback'];
            $data['StatusCallbackEvent'] = ['initiated', 'ringing', 'answered', 'completed'];
        }

        if (!empty($options['record'])) {
            $data['Record'] = 'true';
        }

        if (!empty($options['timeout'])) {
            $data['Timeout'] = $options['timeout'];
        }

        $response = self::makeRequest('POST', $url, $data);

        if (isset($response['sid'])) {
            return [
                'success' => true,
                'sid' => $response['sid'],
                'status' => $response['status'] ?? 'queued',
                'to' => $response['to'] ?? $to,
                'from' => $response['from'] ?? self::$phoneNumber
            ];
        }

        return [
            'success' => false,
            'error' => $response['message'] ?? 'Failed to initiate call',
            'code' => $response['code'] ?? null
        ];
    }

    /**
     * Get call details by SID
     */
    public static function getCall(string $callSid): array
    {
        self::init();

        $url = self::$apiBase . '/Accounts/' . self::$accountSid . '/Calls/' . $callSid . '.json';

        return self::makeRequest('GET', $url);
    }

    /**
     * Get message details by SID
     */
    public static function getMessage(string $messageSid): array
    {
        self::init();

        $url = self::$apiBase . '/Accounts/' . self::$accountSid . '/Messages/' . $messageSid . '.json';

        return self::makeRequest('GET', $url);
    }

    /**
     * Generate TwiML for connecting a call
     *
     * @param string $to Phone number to connect to
     * @param array $options Options like callerId, record, timeout
     * @return string TwiML XML
     */
    public static function generateConnectTwiml(string $to, array $options = []): string
    {
        self::init();

        $callerId = $options['callerId'] ?? self::$phoneNumber;
        $timeout = $options['timeout'] ?? 30;
        $record = !empty($options['record']) ? 'record-from-answer' : 'do-not-record';

        $twiml = '<?xml version="1.0" encoding="UTF-8"?>';
        $twiml .= '<Response>';
        $twiml .= '<Dial callerId="' . htmlspecialchars($callerId) . '" timeout="' . $timeout . '" record="' . $record . '">';
        $twiml .= '<Number>' . htmlspecialchars($to) . '</Number>';
        $twiml .= '</Dial>';
        $twiml .= '</Response>';

        return $twiml;
    }

    /**
     * Generate TwiML for voicemail
     */
    public static function generateVoicemailTwiml(string $message, string $recordingCallback = ''): string
    {
        $twiml = '<?xml version="1.0" encoding="UTF-8"?>';
        $twiml .= '<Response>';
        $twiml .= '<Say voice="alice">' . htmlspecialchars($message) . '</Say>';
        if ($recordingCallback) {
            $twiml .= '<Record maxLength="120" action="' . htmlspecialchars($recordingCallback) . '" />';
        }
        $twiml .= '</Response>';

        return $twiml;
    }

    /**
     * Validate Twilio webhook signature
     *
     * @param string $signature X-Twilio-Signature header
     * @param string $url Full webhook URL
     * @param array $params POST parameters
     * @return bool Whether signature is valid
     */
    public static function validateWebhookSignature(string $signature, string $url, array $params): bool
    {
        self::init();

        // Sort params and build string
        ksort($params);
        $data = $url;
        foreach ($params as $key => $value) {
            $data .= $key . $value;
        }

        // Generate expected signature
        $expected = base64_encode(hash_hmac('sha1', $data, self::$authToken, true));

        return hash_equals($expected, $signature);
    }

    /**
     * Format phone number to E.164 format
     *
     * @param string $phone Raw phone number
     * @param string $defaultCountry Default country code (1 for US/Canada)
     * @return string|null Formatted number or null if invalid
     */
    public static function formatPhoneNumber(string $phone, string $defaultCountry = '1'): ?string
    {
        // Remove all non-numeric characters except +
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        // If already in E.164 format
        if (preg_match('/^\+[1-9]\d{10,14}$/', $phone)) {
            return $phone;
        }

        // Remove leading + if present
        $phone = ltrim($phone, '+');

        // Handle North American numbers
        if (strlen($phone) === 10) {
            // 10 digit number, add country code
            return '+' . $defaultCountry . $phone;
        } elseif (strlen($phone) === 11 && $phone[0] === '1') {
            // 11 digit starting with 1
            return '+' . $phone;
        } elseif (strlen($phone) > 10 && strlen($phone) <= 15) {
            // Assume it includes country code
            return '+' . $phone;
        }

        return null;
    }

    /**
     * Format phone number for display
     */
    public static function formatPhoneForDisplay(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($phone) === 11 && $phone[0] === '1') {
            // Format as +1 (XXX) XXX-XXXX
            return '+1 (' . substr($phone, 1, 3) . ') ' . substr($phone, 4, 3) . '-' . substr($phone, 7);
        } elseif (strlen($phone) === 10) {
            // Format as (XXX) XXX-XXXX
            return '(' . substr($phone, 0, 3) . ') ' . substr($phone, 3, 3) . '-' . substr($phone, 6);
        }

        return $phone;
    }

    /**
     * Make HTTP request to Twilio API
     */
    private static function makeRequest(string $method, string $url, array $data = []): array
    {
        $ch = curl_init();

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => self::$accountSid . ':' . self::$authToken,
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_TIMEOUT => 30
        ];

        if ($method === 'POST') {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = http_build_query($data);
        }

        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            return [
                'success' => false,
                'error' => 'cURL error: ' . $error
            ];
        }

        $decoded = json_decode($response, true);

        if ($httpCode >= 400) {
            return [
                'success' => false,
                'message' => $decoded['message'] ?? 'API error',
                'code' => $decoded['code'] ?? $httpCode
            ];
        }

        return $decoded ?? [];
    }

    /**
     * Get SMS templates for quick sending
     */
    public static function getSMSTemplates(): array
    {
        return [
            'follow_up' => [
                'name' => 'Follow-up',
                'name_fr' => 'Suivi',
                'body' => "Hi {name}, following up on OCSAPP. Do you have 10 mins this week to chat? - {sender}",
                'body_fr' => "Bonjour {name}, je fais suite à OCSAPP. Avez-vous 10 mins cette semaine pour discuter? - {sender}"
            ],
            'meeting_reminder' => [
                'name' => 'Meeting Reminder',
                'name_fr' => 'Rappel de rendez-vous',
                'body' => "Hi {name}, just a reminder about our meeting tomorrow. Looking forward to it! - {sender}",
                'body_fr' => "Bonjour {name}, rappel de notre rendez-vous demain. Au plaisir! - {sender}"
            ],
            'thank_you' => [
                'name' => 'Thank You',
                'name_fr' => 'Remerciement',
                'body' => "Hi {name}, thanks for your time today! I'll send over the info we discussed. - {sender}",
                'body_fr' => "Bonjour {name}, merci pour votre temps aujourd'hui! Je vous envoie les infos discutées. - {sender}"
            ],
            'missed_call' => [
                'name' => 'Missed Call',
                'name_fr' => 'Appel manqué',
                'body' => "Hi {name}, I just tried calling about OCSAPP. When's a good time to connect? - {sender}",
                'body_fr' => "Bonjour {name}, j'ai essayé de vous appeler concernant OCSAPP. Quand puis-je vous rejoindre? - {sender}"
            ],
            'quick_question' => [
                'name' => 'Quick Question',
                'name_fr' => 'Question rapide',
                'body' => "Hi {name}, quick question about your business - are you still interested in delivery? - {sender}",
                'body_fr' => "Bonjour {name}, question rapide - êtes-vous toujours intéressé par la livraison? - {sender}"
            ]
        ];
    }

    /**
     * Replace template placeholders
     */
    public static function processTemplate(string $template, array $data): string
    {
        $replacements = [
            '{name}' => $data['name'] ?? '',
            '{first_name}' => $data['first_name'] ?? '',
            '{last_name}' => $data['last_name'] ?? '',
            '{company}' => $data['company'] ?? '',
            '{sender}' => $data['sender'] ?? 'OCSAPP',
            '{date}' => date('M j'),
            '{time}' => date('g:i A')
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }
}
