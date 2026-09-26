<?php

namespace App\Controllers;

use App\Helpers\MediaUrlHelper;

/**
 * Serves private media through signed, expiring links (see MediaUrlHelper)
 * GET /media?p=uploads/avatars/x.jpg&e=<expiry>&s=<signature>
 */
class MediaController
{
    private const MIME_TYPES = [
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'webp' => 'image/webp',
        'pdf'  => 'application/pdf',
    ];

    public function show(): void
    {
        $path = MediaUrlHelper::verify($_GET['p'] ?? null, $_GET['e'] ?? '', $_GET['s'] ?? null);
        $fullPath = $path ? MediaUrlHelper::absolutePath($path) : null;

        if (!$fullPath || !is_file($fullPath)) {
            http_response_code(404);
            header('Content-Type: text/plain; charset=UTF-8');
            echo 'Not found.';
            exit;
        }

        $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

        header('Content-Type: ' . (self::MIME_TYPES[$ext] ?? 'application/octet-stream'));
        header('Content-Disposition: inline; filename="' . basename($fullPath) . '"');
        header('Content-Length: ' . filesize($fullPath));
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: private, max-age=3600');
        readfile($fullPath);
        exit;
    }
}
