<?php

namespace App\Helpers;

/**
 * Private uploads (business registration / verification documents)
 *
 * These files live in storage/uploads/<folder>/ (outside the web root) and are
 * only served through PrivateDocumentController (GET /documents/file), which
 * checks that the viewer is an admin or the account that owns the document.
 *
 * DB columns keep the same relative path format as before
 * ("uploads/supplier-applications/supapp_x.pdf"), now resolved under storage/.
 */
class PrivateUploadHelper
{
    public const FOLDERS = [
        'seller-applications',
        'supplier-applications',
        'distribution-applications',
        'distribution-docs',
    ];

    /**
     * Absolute directory for a private folder, created if missing
     */
    public static function dir(string $folder): string
    {
        if (!in_array($folder, self::FOLDERS, true)) {
            throw new \InvalidArgumentException('Unknown private upload folder: ' . $folder);
        }

        $dir = self::root() . '/storage/uploads/' . $folder;
        if (!is_dir($dir)) {
            mkdir($dir, 0750, true);
        }
        return $dir;
    }

    /**
     * Validate a stored relative path and return its absolute file path, or null
     */
    public static function absolutePath(?string $relPath): ?string
    {
        if (!self::isValidPath($relPath)) {
            return null;
        }
        return self::root() . '/storage/' . $relPath;
    }

    /**
     * Strict whitelist: known folder, simple file name, document extension only
     */
    public static function isValidPath(?string $relPath): bool
    {
        if (empty($relPath)) {
            return false;
        }
        $folders = implode('|', array_map('preg_quote', self::FOLDERS));
        return (bool)preg_match('#^uploads/(' . $folders . ')/[A-Za-z0-9._-]+\.(pdf|jpg|jpeg|png)$#i', $relPath)
            && strpos($relPath, '..') === false;
    }

    /**
     * Project root (works in web requests and CLI scripts, BASE_PATH is web-only)
     */
    private static function root(): string
    {
        return dirname(__DIR__, 2);
    }

    /**
     * Link to view a private document (checked by PrivateDocumentController)
     */
    public static function url(?string $relPath): string
    {
        return url('documents/file') . '?path=' . rawurlencode((string)$relPath);
    }
}
