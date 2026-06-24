<?php

namespace App\Helpers;

class ImageUploadHelper {

    private string $uploadDir;
    private array $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    private array $allowedMimeTypes = [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp'
    ];
    private int $maxFileSize = 5242880; // 5MB

    public function __construct(string $uploadDir) {
        $this->uploadDir = rtrim($uploadDir, '/');

        // Create directory if it doesn't exist
        $fullPath = BASE_PATH . '/public/' . $this->uploadDir;
        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0755, true);
        }
    }

    /**
     * Upload an image file
     *
     * @param array $file The $_FILES array element
     * @return array ['success' => bool, 'path' => string|null, 'error' => string|null]
     */
    public function upload(array $file): array {
        // Check if file was uploaded
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return [
                'success' => false,
                'path' => null,
                'error' => 'No file was uploaded'
            ];
        }

        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return [
                'success' => false,
                'path' => null,
                'error' => $this->getUploadErrorMessage($file['error'])
            ];
        }

        // Check file size
        if ($file['size'] > $this->maxFileSize) {
            return [
                'success' => false,
                'path' => null,
                'error' => 'File size exceeds maximum allowed size (5MB)'
            ];
        }

        // Get and validate file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $this->allowedExtensions)) {
            return [
                'success' => false,
                'path' => null,
                'error' => 'Invalid file type. Allowed: ' . implode(', ', $this->allowedExtensions)
            ];
        }

        // Reject double extensions (e.g. shell.php.jpg)
        $filename = basename($file['name']);
        if (preg_match('/\.(php|phtml|php3|php4|php5|phar|exe|sh|bat|cmd|com|cgi|pl|py)/i', pathinfo($filename, PATHINFO_FILENAME))) {
            error_log("Suspicious file upload blocked: {$filename} from IP " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
            return [
                'success' => false,
                'path' => null,
                'error' => 'Invalid filename detected'
            ];
        }

        // Validate MIME type using finfo (not just extension)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $this->allowedMimeTypes, true)) {
            return [
                'success' => false,
                'path' => null,
                'error' => 'Invalid file type detected'
            ];
        }

        // Validate image using getimagesize
        $imageInfo = @getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            return [
                'success' => false,
                'path' => null,
                'error' => 'File is not a valid image'
            ];
        }

        // Scan for embedded PHP/script code
        $contents = file_get_contents($file['tmp_name']);
        if (preg_match('/<\?php|<\?=|<script\b/i', $contents)) {
            error_log("Malicious content in upload from IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
            return [
                'success' => false,
                'path' => null,
                'error' => 'File contains suspicious content'
            ];
        }

        // Generate unique filename (never use user-supplied name)
        $safeFilename = $this->generateUniqueFilename($extension);
        $fullPath = BASE_PATH . '/public/' . $this->uploadDir . '/' . $safeFilename;
        $relativePath = $this->uploadDir . '/' . $safeFilename;

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
            return [
                'success' => false,
                'path' => null,
                'error' => 'Failed to move uploaded file'
            ];
        }

        // Set permissions (not executable)
        chmod($fullPath, 0644);

        return [
            'success' => true,
            'path' => $relativePath,
            'error' => null
        ];
    }

    /**
     * Delete an uploaded image (with path traversal protection)
     *
     * @param string|null $path Relative path from public/ directory
     * @return bool True if deleted or doesn't exist, false on error
     */
    public function delete(?string $path): bool {
        if (empty($path)) {
            return true;
        }

        // Strip any path traversal sequences
        $path = str_replace(['../', '..\\'], '', $path);
        $path = ltrim($path, '/\\');

        $fullPath = BASE_PATH . '/public/' . $path;

        // Resolve to real path
        $realPath = realpath($fullPath);
        if ($realPath === false) {
            return true; // File doesn't exist
        }

        // Ensure the file is within the public/uploads directory
        $uploadsDir = realpath(BASE_PATH . '/public/uploads');
        if ($uploadsDir === false || strpos($realPath, $uploadsDir) !== 0) {
            error_log("Path traversal attempt blocked: {$path} resolved to {$realPath} from IP " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
            return false;
        }

        return @unlink($realPath);
    }

    /**
     * Generate a unique filename
     */
    private function generateUniqueFilename(string $extension): string {
        return uniqid('img_', true) . '_' . time() . '.' . $extension;
    }

    /**
     * Get human-readable error message for upload error code
     */
    private function getUploadErrorMessage(int $errorCode): string {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize directive',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE directive',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension',
        ];

        return $errors[$errorCode] ?? 'Unknown upload error';
    }

    /**
     * Set maximum file size in bytes
     */
    public function setMaxFileSize(int $bytes): void {
        $this->maxFileSize = $bytes;
    }

    /**
     * Set allowed file extensions
     */
    public function setAllowedExtensions(array $extensions): void {
        $this->allowedExtensions = array_map('strtolower', $extensions);
    }
}
