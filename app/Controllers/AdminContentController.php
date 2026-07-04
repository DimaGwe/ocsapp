<?php
namespace App\Controllers;

use App\Middlewares\AuthMiddleware;
use Database;
use PDO;

/**
 * AdminContentController
 * Powers the Marketing > Content Creator (chat-to-post + image gen)
 * and Content Library (DB-backed post manager / queue).
 *
 * AI providers are kept server-side. Keys live in .env:
 *   ANTHROPIC_API_KEY  - Claude (caption chat brain)
 *   GEMINI_API_KEY     - Google Gemini / "Nano Banana" (image generation)
 *
 * Veo 3 video is a Phase 2 stub behind a spend gate (see genVideo()).
 */
class AdminContentController
{
    public function __construct()
    {
        AuthMiddleware::adminTier(2); // super_admin (1) + admin (2)
    }

    /* ============================================================
     * PAGES
     * ============================================================ */

    public function create(): void
    {
        view('admin/content/create', []);
    }

    public function library(): void
    {
        $db = Database::getConnection();
        $posts = [];
        try {
            $stmt = $db->query("SELECT * FROM content_posts ORDER BY created_at DESC");
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            // Table may not exist yet - view handles empty state
        }
        view('admin/content/library', ['posts' => $posts]);
    }

    /* ============================================================
     * AJAX: CHAT (Claude) - drafts / refines captions
     * ============================================================ */

    public function chat(): void
    {
        $this->json();
        $this->requireCsrf();

        $raw = file_get_contents('php://input');
        $body = json_decode($raw, true) ?: [];
        $messages = $body['messages'] ?? [];

        if (!is_array($messages) || empty($messages)) {
            $this->out(['success' => false, 'error' => 'No message provided.']);
        }

        // Keep only role/content, cap history length to control token cost
        $clean = [];
        foreach (array_slice($messages, -16) as $m) {
            $role = ($m['role'] ?? '') === 'assistant' ? 'assistant' : 'user';
            $content = trim((string)($m['content'] ?? ''));
            if ($content !== '') {
                $clean[] = ['role' => $role, 'content' => $content];
            }
        }
        if (empty($clean)) {
            $this->out(['success' => false, 'error' => 'No message provided.']);
        }

        $apiKey = setting('anthropic_api_key', '');
        if (!$apiKey) {
            $this->out(['success' => false, 'error' => 'Claude is not connected. Add your Anthropic key in System > Integrations.']);
        }

        $result = $this->callClaude($apiKey, $this->chatSystemPrompt(), $clean);
        $this->out($result);
    }

    private function chatSystemPrompt(): string
    {
        return <<<'PROMPT'
You are the social media content writer for OCSAPP, a Canadian multi-vendor marketplace with zero-emission local delivery (ocsapp.ca), focused on the West Island of Montreal, Quebec.

You are in a CHAT with the marketing team. Help them shape one social post at a time: brainstorm the angle, write the caption, tighten the hook, suggest hashtags. Ask a short clarifying question only when you genuinely need it - otherwise just deliver a strong draft they can react to.

BRAND VOICE:
- Friendly, energetic, community-focused, trustworthy. Local, not corporate.
- Segments: local buyers, small-business sellers, wholesale suppliers, delivery drivers.
- Mission hooks: shop local, zero-emission delivery, support West Island businesses.

HARD RULES:
- Never use em dashes. Use a hyphen (-) instead.
- French must be Quebec French (fr-CA): "courriel" not "email", "vous" form, "livreur", "vendeur", "boutique".
- Never invent prices, stats, discounts, or promotions the user did not give you.
- Calls to action point to ocsapp.ca.
- Include 3-5 relevant hashtags when you give a finished caption.

IMAGES:
- This page HAS an image generator (Nano Banana / Google Gemini) in the panel on the RIGHT. You cannot render the image inside this chat yourself, but the team can generate it right here in seconds. NEVER say you are "text only" and NEVER suggest outside tools like Canva, DALL-E, or Midjourney - OCSAPP already has its own generator.
- When they ask for an image, hand them a vivid, ready-to-use image prompt in English (image models work best in English) describing the scene, mood, colours, and composition. Then tell them: paste it into the "Generate image" box on the right and click Generate.
- Keep image prompts concrete and on-brand: West Island local marketplace, zero-emission/bike delivery, warm community feel, bright and energetic. Do not put text in the image unless they specifically ask.

When you present a finished post, format it clearly like this so it is easy to copy:

TITLE: <short internal name>
--- EN ---
<English caption>
--- FR ---
<Quebec French caption>
HASHTAGS: <#tags>

Keep chat replies conversational and concise. Offer the finished block when the post is ready.
PROMPT;
    }

    /* ============================================================
     * AJAX: IMAGE GENERATION (Gemini / Nano Banana)
     * ============================================================ */

    public function genImage(): void
    {
        $this->json();
        $this->requireCsrf();

        $raw = file_get_contents('php://input');
        $body = json_decode($raw, true) ?: [];
        $prompt = trim((string)($body['prompt'] ?? ''));

        if ($prompt === '') {
            $this->out(['success' => false, 'error' => 'Describe the image you want to generate.']);
        }

        $apiKey = setting('gemini_api_key', '');
        if (!$apiKey) {
            $this->out(['success' => false, 'error' => 'Image generation is not connected. Add your Gemini key in System > Integrations.']);
        }

        // Brand wrapper so generated images stay on-brand and avoid text artifacts
        $fullPrompt = "High-quality social media marketing image for OCSAPP, a Canadian zero-emission local delivery marketplace in Montreal's West Island. "
            . "Clean, modern, bright, authentic local feel. Brand accent colour is green (#00b207). "
            . "Do not render any logos, watermarks, or garbled text. "
            . "Scene: " . $prompt;

        $result = $this->callGeminiImage($apiKey, $fullPrompt);
        $this->out($result);
    }

    private function callGeminiImage(string $apiKey, string $prompt): array
    {
        // Gemini 2.5 Flash Image ("Nano Banana")
        $model = env('GEMINI_IMAGE_MODEL', 'gemini-2.5-flash-image');
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . urlencode($apiKey);

        $payload = json_encode([
            'contents' => [[
                'parts' => [['text' => $prompt]],
            ]],
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT        => 120,
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
            $msg = $data['error']['message'] ?? 'Image API error (HTTP ' . $httpCode . ')';
            return ['success' => false, 'error' => $msg];
        }

        // Find the inline image data in the response parts
        $parts = $data['candidates'][0]['content']['parts'] ?? [];
        $b64 = null;
        $mime = 'image/png';
        foreach ($parts as $p) {
            if (isset($p['inlineData']['data'])) {
                $b64  = $p['inlineData']['data'];
                $mime = $p['inlineData']['mimeType'] ?? $mime;
                break;
            }
            if (isset($p['inline_data']['data'])) { // snake_case fallback
                $b64  = $p['inline_data']['data'];
                $mime = $p['inline_data']['mime_type'] ?? $mime;
                break;
            }
        }

        if (!$b64) {
            return ['success' => false, 'error' => 'No image was returned. Try rephrasing the description.'];
        }

        $binary = base64_decode($b64);
        if ($binary === false) {
            return ['success' => false, 'error' => 'Could not decode the generated image.'];
        }

        $ext = strpos($mime, 'jpeg') !== false ? 'jpg' : (strpos($mime, 'webp') !== false ? 'webp' : 'png');
        $dir = BASE_PATH . '/public/uploads/content';
        if (!is_dir($dir)) { @mkdir($dir, 0775, true); }

        $filename = 'content_' . date('Ymd_His') . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
        $path = $dir . '/' . $filename;
        if (file_put_contents($path, $binary) === false) {
            return ['success' => false, 'error' => 'Could not save the generated image to disk.'];
        }

        $rel = 'uploads/content/' . $filename;
        return ['success' => true, 'path' => $rel, 'url' => asset($rel)];
    }

    /* ============================================================
     * AJAX: MANUAL IMAGE UPLOAD (Content Library edit)
     * ============================================================ */

    public function uploadImage(): void
    {
        $this->json();
        $this->requireCsrf();

        if (empty($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $this->out(['success' => false, 'error' => 'No image uploaded.']);
        }
        $file = $_FILES['image'];

        $maxBytes = 8 * 1024 * 1024;
        if ($file['size'] > $maxBytes) {
            $this->out(['success' => false, 'error' => 'Image is too large (max 8MB).']);
        }

        $allowedExt = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp', 'gif' => 'image/gif'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!isset($allowedExt[$ext])) {
            $this->out(['success' => false, 'error' => 'Unsupported file type. Use JPG, PNG, WEBP, or GIF.']);
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp', 'image/gif'], true) || !getimagesize($file['tmp_name'])) {
            $this->out(['success' => false, 'error' => 'File does not look like a valid image.']);
        }

        $dir = BASE_PATH . '/public/uploads/content';
        if (!is_dir($dir)) { @mkdir($dir, 0775, true); }

        $filename = 'content_' . date('Ymd_His') . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
        $path = $dir . '/' . $filename;
        if (!move_uploaded_file($file['tmp_name'], $path)) {
            $this->out(['success' => false, 'error' => 'Could not save the uploaded image.']);
        }

        $rel = 'uploads/content/' . $filename;
        $this->out(['success' => true, 'path' => $rel, 'url' => asset($rel)]);
    }

    /* ============================================================
     * AJAX: VIDEO GENERATION (Veo 3) - Phase 2 stub w/ spend gate
     * ============================================================ */

    public function genVideo(): void
    {
        $this->json();
        $this->requireCsrf();
        $this->out([
            'success' => false,
            'phase2'  => true,
            'error'   => 'Video generation (Veo 3) is coming in Phase 2. It will run behind a per-day spend cap before going live.',
        ]);
    }

    /* ============================================================
     * AJAX: SAVE / UPDATE / DELETE POSTS
     * ============================================================ */

    public function save(): void
    {
        $this->json();
        $this->requireCsrf();

        $raw = file_get_contents('php://input');
        $b = json_decode($raw, true) ?: [];

        $captionEn = trim((string)($b['caption_en'] ?? ''));
        $captionFr = trim((string)($b['caption_fr'] ?? ''));
        if ($captionEn === '' && $captionFr === '') {
            $this->out(['success' => false, 'error' => 'A post needs at least one caption (EN or FR).']);
        }

        $platforms = $b['platforms'] ?? [];
        if (is_array($platforms)) { $platforms = implode(',', array_map('trim', $platforms)); }

        $status = in_array(($b['status'] ?? ''), ['idea','approved','scheduled','posted'], true)
            ? $b['status'] : 'idea';
        $postDate = trim((string)($b['post_date'] ?? ''));
        $postDate = preg_match('/^\d{4}-\d{2}-\d{2}$/', $postDate) ? $postDate : null;

        $fields = [
            'title'        => substr(trim((string)($b['title'] ?? 'Untitled')) ?: 'Untitled', 0, 200),
            'caption_en'   => $captionEn,
            'caption_fr'   => $captionFr,
            'hashtags'     => substr(trim((string)($b['hashtags'] ?? '')), 0, 500),
            'platforms'    => substr((string)$platforms, 0, 200),
            'status'       => $status,
            'post_date'    => $postDate,
            'image_path'   => substr(trim((string)($b['image_path'] ?? '')), 0, 500) ?: null,
            'video_path'   => substr(trim((string)($b['video_path'] ?? '')), 0, 500) ?: null,
            'generated_by' => substr(trim((string)($b['generated_by'] ?? 'manual')), 0, 50),
        ];

        $db = Database::getConnection();
        $id = (int)($b['id'] ?? 0);

        try {
            if ($id > 0) {
                $old = $db->prepare("SELECT image_path FROM content_posts WHERE id=?");
                $old->execute([$id]);
                $oldImg = $old->fetchColumn();

                $sql = "UPDATE content_posts SET title=?, caption_en=?, caption_fr=?, hashtags=?, platforms=?, status=?, post_date=?, image_path=?, video_path=?, generated_by=? WHERE id=?";
                $args = array_values($fields);
                $args[] = $id;
                $db->prepare($sql)->execute($args);

                if ($oldImg && $oldImg !== $fields['image_path'] && strpos($oldImg, 'uploads/content/') === 0) {
                    $file = BASE_PATH . '/public/' . $oldImg;
                    if (is_file($file)) { @unlink($file); }
                }
            } else {
                $fields['created_by'] = (int)($_SESSION['user']['id'] ?? 0) ?: null;
                $cols = implode(',', array_keys($fields));
                $ph   = implode(',', array_fill(0, count($fields), '?'));
                $db->prepare("INSERT INTO content_posts ($cols) VALUES ($ph)")->execute(array_values($fields));
                $id = (int)$db->lastInsertId();
            }
        } catch (\Throwable $e) {
            $this->out(['success' => false, 'error' => 'Could not save: ' . $e->getMessage()]);
        }

        $this->out(['success' => true, 'id' => $id]);
    }

    public function updateStatus(): void
    {
        $this->json();
        $this->requireCsrf();

        $b = json_decode(file_get_contents('php://input'), true) ?: [];
        $id = (int)($b['id'] ?? 0);
        $status = $b['status'] ?? '';
        if (!$id || !in_array($status, ['idea','approved','scheduled','posted'], true)) {
            $this->out(['success' => false, 'error' => 'Invalid request.']);
        }
        try {
            Database::getConnection()
                ->prepare("UPDATE content_posts SET status=? WHERE id=?")
                ->execute([$status, $id]);
        } catch (\Throwable $e) {
            $this->out(['success' => false, 'error' => $e->getMessage()]);
        }
        $this->out(['success' => true]);
    }

    public function delete(): void
    {
        $this->json();
        $this->requireCsrf();

        $b = json_decode(file_get_contents('php://input'), true) ?: [];
        $id = (int)($b['id'] ?? 0);
        if (!$id) { $this->out(['success' => false, 'error' => 'Invalid request.']); }

        $db = Database::getConnection();
        try {
            // Remove the generated image file if it lives in our uploads dir
            $stmt = $db->prepare("SELECT image_path FROM content_posts WHERE id=?");
            $stmt->execute([$id]);
            $img = $stmt->fetchColumn();
            if ($img && strpos($img, 'uploads/content/') === 0) {
                $file = BASE_PATH . '/public/' . $img;
                if (is_file($file)) { @unlink($file); }
            }
            $db->prepare("DELETE FROM content_posts WHERE id=?")->execute([$id]);
        } catch (\Throwable $e) {
            $this->out(['success' => false, 'error' => $e->getMessage()]);
        }
        $this->out(['success' => true]);
    }

    /* ============================================================
     * CLAUDE API
     * ============================================================ */

    private function callClaude(string $apiKey, string $system, array $messages): array
    {
        $payload = json_encode([
            'model'      => env('CONTENT_CHAT_MODEL', 'claude-sonnet-4-6'),
            'max_tokens' => 1500,
            'system'     => $system,
            'messages'   => $messages,
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
            CURLOPT_TIMEOUT        => 90,
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

    /* ============================================================
     * HELPERS
     * ============================================================ */

    private function json(): void
    {
        header('Content-Type: application/json');
    }

    private function out(array $payload): void
    {
        echo json_encode($payload);
        exit;
    }

    private function requireCsrf(): void
    {
        $name = env('CSRF_TOKEN_NAME', '_csrf_token');
        $token = $_SERVER['HTTP_X_CSRF_TOKEN']
            ?? ($_POST[$name] ?? ($_POST['_csrf_token'] ?? ''));
        if (!verifyCsrfToken((string)$token)) {
            http_response_code(419);
            $this->out(['success' => false, 'error' => 'Session expired. Refresh the page and try again.']);
        }
    }
}
