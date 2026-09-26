<?php

namespace App\Helpers;

/**
 * Private media (avatars, delivery photos, claim evidence)
 *
 * Files live in storage/uploads/<folder>/ (outside the web root). Pages and API
 * responses that are already access-checked hand out signed, expiring links:
 *   /media?p=uploads/avatars/x.jpg&e=<expiry>&s=<signature>
 * MediaController serves a file only when the signature and expiry are valid,
 * so links work in <img> tags and in the driver app without extra headers.
 *
 * DB columns keep the same format as before ("uploads/avatars/x.jpg").
 */
class MediaUrlHelper
{
    public const FOLDERS = ['avatars', 'delivery', 'delivery/proof', 'claims'];

    // Links stay valid until the end of the next day (UTC), so they are stable within a day (browser cache)
    private const DAY = 86400;

    /**
     * Absolute directory for a media folder, created if missing
     */
    public static function dir(string $folder): string
    {
        if (!in_array($folder, self::FOLDERS, true)) {
            throw new \InvalidArgumentException('Unknown media folder: ' . $folder);
        }

        $dir = self::root() . '/storage/uploads/' . $folder;
        if (!is_dir($dir)) {
            mkdir($dir, 0750, true);
        }
        return $dir;
    }

    /**
     * Accepts the stored path in any of its historical forms and returns "uploads/<folder>/<file>" or null
     * ("uploads/x", "/uploads/x", "https://ocsapp.ca/uploads/x")
     */
    public static function normalize(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }
        $path = (string)preg_replace('#^https?://[^/]+#i', '', trim($path));
        $path = ltrim($path, '/');

        $folders = implode('|', array_map(fn($f) => preg_quote($f, '#'), self::FOLDERS));
        if (!preg_match('#^uploads/(' . $folders . ')/[A-Za-z0-9._-]+\.(jpg|jpeg|png|webp|pdf)$#i', $path)
            || strpos($path, '..') !== false) {
            return null;
        }
        return $path;
    }

    /**
     * Absolute file path for a stored media path, or null if the path is not valid
     */
    public static function absolutePath(?string $path): ?string
    {
        $path = self::normalize($path);
        return $path ? self::root() . '/storage/' . $path : null;
    }

    /**
     * Signed link for a stored media path. External URLs (e.g. social avatars) pass through unchanged.
     */
    public static function url(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }
        if (preg_match('#^https?://#i', $path) && !preg_match('#^https?://[^/]+/uploads/#i', $path)) {
            return $path;
        }

        $path = self::normalize($path);
        if (!$path) {
            return null;
        }

        $expires = (intdiv(time(), self::DAY) + 2) * self::DAY;
        return url('media') . '?' . http_build_query([
            'p' => $path,
            'e' => $expires,
            's' => self::sign($path, $expires),
        ]);
    }

    /**
     * Check a signed link's parameters; returns the normalized path or null
     */
    public static function verify(?string $path, $expires, ?string $signature): ?string
    {
        $path = self::normalize($path);
        if (!$path || !ctype_digit((string)$expires) || (int)$expires < time() || empty($signature)) {
            return null;
        }
        return hash_equals(self::sign($path, (int)$expires), (string)$signature) ? $path : null;
    }

    private static function sign(string $path, int $expires): string
    {
        return substr(hash_hmac('sha256', $path . '|' . $expires, self::key()), 0, 32);
    }

    /**
     * Signing key: MEDIA_SIGNING_KEY from .env, else a random key kept in storage/ (created once)
     */
    private static function key(): string
    {
        $envKey = function_exists('env') ? (string)env('MEDIA_SIGNING_KEY', '') : '';
        if ($envKey !== '') {
            return $envKey;
        }

        $file = self::root() . '/storage/media_signing.key';
        if (!is_file($file)) {
            $fp = @fopen($file, 'x');
            if ($fp) {
                fwrite($fp, bin2hex(random_bytes(32)));
                fclose($fp);
                @chmod($file, 0640);
            }
        }
        $key = trim((string)@file_get_contents($file));
        if ($key === '') {
            throw new \RuntimeException('Media signing key unavailable');
        }
        return $key;
    }

    /**
     * Project root (works in web requests and CLI scripts, BASE_PATH is web-only)
     */
    private static function root(): string
    {
        return dirname(__DIR__, 2);
    }
}
