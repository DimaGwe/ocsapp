<?php

namespace App\Controllers;

use Database;
use PDO;
use App\Middlewares\AuthMiddleware;

class AdminEmailController
{
    /**
     * List all email templates
     */
    public function index()
    {
        AuthMiddleware::handle('admin');

        $templatesPath = __DIR__ . '/../Views/emails/';

        // Get all email template files
        $templates = [];
        $files = glob($templatesPath . '*.php');

        foreach ($files as $file) {
            $filename = basename($file);
            $name = str_replace(['-', '_'], ' ', pathinfo($filename, PATHINFO_FILENAME));
            $name = ucwords($name);

            $templates[] = [
                'filename' => $filename,
                'name' => $name,
                'path' => $file,
                'size' => filesize($file),
                'modified' => filemtime($file),
            ];
        }

        // Sort by name
        usort($templates, function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        \view('admin.emails.index', [
            'templates' => $templates,
        ]);
    }

    /**
     * Edit email template
     */
    public function edit()
    {
        AuthMiddleware::handle('admin');

        $filename = $_GET['template'] ?? '';

        if (empty($filename) || !preg_match('/^[a-z0-9\-_]+\.php$/i', $filename)) {
            $_SESSION['error'] = 'Invalid template name';
            header('Location: ' . \url('admin/emails'));
            exit;
        }

        $templatesPath = __DIR__ . '/../Views/emails/';
        $filePath = $templatesPath . $filename;

        if (!file_exists($filePath)) {
            $_SESSION['error'] = 'Template not found';
            header('Location: ' . \url('admin/emails'));
            exit;
        }

        $content = file_get_contents($filePath);
        $name = str_replace(['-', '_'], ' ', pathinfo($filename, PATHINFO_FILENAME));
        $name = ucwords($name);

        \view('admin.emails.edit', [
            'filename' => $filename,
            'name' => $name,
            'content' => $content,
            'filePath' => $filePath,
        ]);
    }

    /**
     * Update email template
     */
    public function update()
    {
        AuthMiddleware::handle('admin');

        $filename = $_POST['filename'] ?? '';
        $content = $_POST['content'] ?? '';

        if (empty($filename) || !preg_match('/^[a-z0-9\-_]+\.php$/i', $filename)) {
            $_SESSION['error'] = 'Invalid template name';
            header('Location: ' . \url('admin/emails'));
            exit;
        }

        $templatesPath = __DIR__ . '/../Views/emails/';
        $filePath = $templatesPath . $filename;

        if (!file_exists($filePath)) {
            $_SESSION['error'] = 'Template not found';
            header('Location: ' . \url('admin/emails'));
            exit;
        }

        // Create backup
        $backupPath = $templatesPath . 'backups/';
        if (!is_dir($backupPath)) {
            mkdir($backupPath, 0755, true);
        }

        $backupFile = $backupPath . pathinfo($filename, PATHINFO_FILENAME) . '_' . date('Y-m-d_H-i-s') . '.php';
        copy($filePath, $backupFile);

        // Save updated content
        $result = file_put_contents($filePath, $content);

        if ($result === false) {
            $_SESSION['error'] = 'Failed to save template';
        } else {
            $_SESSION['success'] = 'Template updated successfully! Backup saved: ' . basename($backupFile);
        }

        header('Location: ' . \url('admin/emails/edit?template=' . urlencode($filename)));
        exit;
    }

    /**
     * Preview email template
     */
    public function preview()
    {
        AuthMiddleware::handle('admin');

        $filename = $_GET['template'] ?? '';

        if (empty($filename) || !preg_match('/^[a-z0-9\-_]+\.php$/i', $filename)) {
            echo 'Invalid template';
            exit;
        }

        $templatesPath = __DIR__ . '/../Views/emails/';
        $filePath = $templatesPath . $filename;

        if (!file_exists($filePath)) {
            echo 'Template not found';
            exit;
        }

        // Sample data for preview
        $user = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
        ];

        $order = [
            'order_number' => 'ORD-12345',
            'total' => 129.99,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $items = [
            [
                'name' => 'Sample Product 1',
                'quantity' => 2,
                'price' => 49.99,
            ],
            [
                'name' => 'Sample Product 2',
                'quantity' => 1,
                'price' => 29.99,
            ],
        ];

        $product = [
            'name' => 'Sample Product',
            'sku' => 'SKU-123',
        ];

        $current_stock = 5;
        $seller = $user;
        $shop = ['name' => 'OCS Store'];
        $old_status = 'processing';
        $new_status = 'shipped';
        $reason = 'Customer requested cancellation';

        // Include the template
        ob_start();
        include $filePath;
        $output = ob_get_clean();

        echo $output;
    }

    /**
     * Preview content from editor (for live preview)
     */
    public function previewContent()
    {
        AuthMiddleware::handle('admin');

        $content = $_POST['content'] ?? '';

        if (empty($content)) {
            echo 'No content provided';
            exit;
        }

        // Sample data for preview
        $user = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
        ];

        $order = [
            'order_number' => 'ORD-12345',
            'total' => 129.99,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $items = [
            [
                'name' => 'Sample Product 1',
                'quantity' => 2,
                'price' => 49.99,
            ],
            [
                'name' => 'Sample Product 2',
                'quantity' => 1,
                'price' => 29.99,
            ],
        ];

        $product = [
            'name' => 'Sample Product',
            'sku' => 'SKU-123',
        ];

        $current_stock = 5;
        $seller = $user;
        $shop = ['name' => 'OCS Store'];
        $old_status = 'processing';
        $new_status = 'shipped';
        $reason = 'Customer requested cancellation';

        // Evaluate the content
        ob_start();
        eval('?>' . $content);
        $output = ob_get_clean();

        echo $output;
    }
}
