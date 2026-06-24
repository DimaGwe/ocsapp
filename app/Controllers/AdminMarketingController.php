<?php
namespace App\Controllers;

use App\Middlewares\AuthMiddleware;

class AdminMarketingController
{
    public function __construct()
    {
        AuthMiddleware::superAdmin();
    }

    public function index(): void
    {
        view('admin/marketing/index', []);
    }

    public function generate(): void
    {
        header('Content-Type: application/json');

        $platform    = trim($_POST['platform'] ?? '');
        $contentType = trim($_POST['content_type'] ?? '');
        $language    = trim($_POST['language'] ?? 'en');
        $tone        = trim($_POST['tone'] ?? 'friendly');
        $context     = trim($_POST['context'] ?? '');

        if (!$platform || !$contentType || !$context) {
            echo json_encode(['success' => false, 'error' => 'Please fill in all required fields.']);
            exit;
        }

        $apiKey = env('ANTHROPIC_API_KEY', '');
        if (!$apiKey) {
            echo json_encode(['success' => false, 'error' => 'API key not configured. Add ANTHROPIC_API_KEY to your .env file.']);
            exit;
        }

        $result = $this->callClaude(
            $apiKey,
            $this->buildSystemPrompt(),
            $this->buildUserPrompt($platform, $contentType, $language, $tone, $context)
        );

        echo json_encode($result);
        exit;
    }

    private function buildSystemPrompt(): string
    {
        return <<<'PROMPT'
You are the marketing content writer for OCSAPP, a Canadian multi-vendor marketplace.

BRAND:
- Platform: OCSAPP Marketplace (ocsapp.ca)
- Mission: Connect local Canadian sellers, suppliers, and buyers on one platform
- Colors: Green (#00b207), Dark navy (#0a0e27)
- Markets: Quebec and Canada — bilingual EN/FR audience
- Tone: Friendly, energetic, community-focused, trustworthy
- Segments: local buyers, small business sellers, wholesale suppliers, delivery drivers

CONTENT RULES:
- Never use em dashes (—) — use a hyphen (-) instead
- French must be Quebec French (fr-CA): use "courriel" not "email", "vous" form, vocabulary like "livreur", "vendeur", "boutique"
- Bilingual output: write EN block first, then FR block, each clearly labeled
- Include 3-5 relevant hashtags per platform
- Calls to action point to ocsapp.ca
- Never invent prices, stats, or promotions the user did not provide
- Keep copy authentic and local — no corporate jargon

PLATFORM GUIDELINES:
- Instagram / TikTok: punchy, visual-first, 100-150 words max, strong hook on the first line
- Facebook: conversational, 150-200 words, include a question to drive comments
- LinkedIn: professional, B2B-focused, 200-250 words
- Email: write subject line + body; CASL-compliant; bilingual structure

Respond with the requested content only — no preamble, no explanations, no meta-commentary.
PROMPT;
    }

    private function buildUserPrompt(string $platform, string $contentType, string $language, string $tone, string $context): string
    {
        $langMap = [
            'en'   => 'English only',
            'fr'   => 'Quebec French (fr-CA) only',
            'both' => 'Both languages — label English block "--- EN ---" and French block "--- FR ---"',
        ];
        $toneMap = [
            'friendly'     => 'friendly and approachable',
            'professional' => 'professional and polished',
            'urgent'       => 'urgent and action-driven',
        ];

        $langLabel = $langMap[$language] ?? 'English only';
        $toneLabel = $toneMap[$tone] ?? 'friendly and approachable';

        return "Platform: {$platform}\n"
             . "Content type: {$contentType}\n"
             . "Language: {$langLabel}\n"
             . "Tone: {$toneLabel}\n\n"
             . "What to promote / context:\n{$context}";
    }

    private function callClaude(string $apiKey, string $system, string $userPrompt): array
    {
        $payload = json_encode([
            'model'      => 'claude-haiku-4-5-20251001',
            'max_tokens' => 1024,
            'system'     => $system,
            'messages'   => [
                ['role' => 'user', 'content' => $userPrompt],
            ],
        ]);

        $ch = curl_init('https://api.anthropic.com/v1/messages');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'x-api-key: ' . $apiKey,
                'anthropic-version: 2023-06-01',
            ],
            CURLOPT_TIMEOUT        => 60,
        ]);

        $response  = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return ['success' => false, 'error' => 'Connection error: ' . $curlError];
        }

        $data = json_decode($response, true);

        if ($httpCode !== 200) {
            $msg = $data['error']['message'] ?? 'API error (HTTP ' . $httpCode . ')';
            return ['success' => false, 'error' => $msg];
        }

        return ['success' => true, 'content' => $data['content'][0]['text'] ?? ''];
    }
}
