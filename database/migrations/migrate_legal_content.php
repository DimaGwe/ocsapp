<?php
/**
 * Migration Script: Import Static Legal Pages to Database
 * Run this once to migrate existing static legal pages to the CMS
 *
 * Usage: php database/migrations/migrate_legal_content.php
 */

// Load config if available
if (file_exists(__DIR__ . '/../../bootstrap/app.php')) {
    require __DIR__ . '/../../bootstrap/app.php';
}

try {
    // Try to get database connection
    if (class_exists('Database')) {
        $db = Database::getConnection();
    } else {
        // Fallback direct connection
        $db = new PDO(
            'mysql:host=ocs-marketplace-db.c98w8ccoghgv.us-east-2.rds.amazonaws.com;dbname=ocs_marketplace',
            'admin',
            'admin123',
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
    }

    echo "===========================================\n";
    echo "Legal Content Migration Script\n";
    echo "===========================================\n\n";

    // Check if tables exist
    $stmt = $db->query("SHOW TABLES LIKE 'legal_content'");
    if ($stmt->rowCount() === 0) {
        echo "❌ Error: legal_content table does not exist!\n";
        echo "   Please run the create_legal_content_table.sql migration first.\n";
        exit(1);
    }

    echo "✓ Database tables found\n\n";

    // Check if data already exists
    $stmt = $db->query("SELECT COUNT(*) as count FROM legal_content");
    $existingCount = $stmt->fetch()['count'];

    if ($existingCount > 0) {
        echo "⚠️  Warning: Found {$existingCount} existing legal pages in database.\n";
        echo "   Do you want to continue and replace them? (yes/no): ";
        $handle = fopen("php://stdin", "r");
        $line = trim(fgets($handle));
        fclose($handle);

        if (strtolower($line) !== 'yes') {
            echo "\n❌ Migration cancelled by user.\n";
            exit(0);
        }

        echo "\n   Deleting existing content...\n";
        $db->exec("DELETE FROM legal_content_revisions");
        $db->exec("DELETE FROM legal_content");
        echo "   ✓ Existing content deleted\n\n";
    }

    // Define legal pages to migrate
    $legalPages = [
        [
            'page_type' => 'terms',
            'language' => 'en',
            'title' => 'Terms of Service',
            'meta_description' => 'Terms and conditions for using OCS Marketplace platform',
            'view_file' => __DIR__ . '/../../app/Views/legal/terms.php'
        ],
        [
            'page_type' => 'privacy',
            'language' => 'en',
            'title' => 'Privacy Policy',
            'meta_description' => 'How we collect, use, and protect your personal information',
            'view_file' => __DIR__ . '/../../app/Views/legal/privacy.php'
        ]
    ];

    echo "Starting migration...\n\n";

    $successCount = 0;
    $failCount = 0;

    foreach ($legalPages as $page) {
        echo "📄 Migrating: {$page['title']} ({$page['page_type']} - {$page['language']})\n";

        // Check if view file exists
        if (!file_exists($page['view_file'])) {
            echo "   ⚠️  Warning: View file not found at {$page['view_file']}\n";
            echo "   Creating placeholder content...\n";
            $content = generatePlaceholderContent($page['page_type'], $page['title']);
        } else {
            // Read the view file
            $fileContent = file_get_contents($page['view_file']);

            // Extract content between ob_start() and $content = ob_get_clean()
            // This is a simple extraction - content between <body> tags or entire file
            if (preg_match('/<body[^>]*>(.*?)<\/body>/is', $fileContent, $matches)) {
                $content = $matches[1];
            } else if (preg_match('/\?>(.+)$/s', $fileContent, $matches)) {
                // Content after closing PHP tag
                $content = trim($matches[1]);
            } else {
                // Use entire file as fallback
                $content = $fileContent;
            }

            $content = trim($content);

            if (empty($content)) {
                echo "   ⚠️  Warning: No content extracted from file\n";
                echo "   Creating placeholder content...\n";
                $content = generatePlaceholderContent($page['page_type'], $page['title']);
            }
        }

        try {
            // Insert into database
            $stmt = $db->prepare("
                INSERT INTO legal_content
                (page_type, language, title, content, meta_description, version, is_published, published_at, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, 1, 1, NOW(), NOW(), NOW())
            ");

            $stmt->execute([
                $page['page_type'],
                $page['language'],
                $page['title'],
                $content,
                $page['meta_description']
            ]);

            $pageId = $db->lastInsertId();

            // Create initial revision
            $stmt = $db->prepare("
                INSERT INTO legal_content_revisions
                (legal_content_id, title, content, version, created_at, notes)
                VALUES (?, ?, ?, 1, NOW(), 'Initial migration from static file')
            ");

            $stmt->execute([
                $pageId,
                $page['title'],
                $content
            ]);

            echo "   ✓ Migrated successfully (ID: {$pageId})\n";
            echo "   ✓ Created initial revision\n";
            $successCount++;

        } catch (PDOException $e) {
            echo "   ❌ Error: " . $e->getMessage() . "\n";
            $failCount++;
        }

        echo "\n";
    }

    echo "===========================================\n";
    echo "Migration Complete!\n";
    echo "===========================================\n";
    echo "✓ Successful: {$successCount}\n";
    echo "❌ Failed: {$failCount}\n";
    echo "\nNext steps:\n";
    echo "1. Visit /admin/legal to manage legal pages\n";
    echo "2. Update PageController to load from database\n";
    echo "3. Test the CMS functionality\n\n";

} catch (Exception $e) {
    echo "\n❌ Fatal Error: " . $e->getMessage() . "\n";
    exit(1);
}

/**
 * Generate placeholder content for a legal page
 */
function generatePlaceholderContent(string $type, string $title): string
{
    $content = "<h1>{$title}</h1>\n\n";
    $content .= "<p><strong>Effective Date:</strong> " . date('F d, Y') . "</p>\n\n";

    if ($type === 'terms') {
        $content .= "<h2>1. Acceptance of Terms</h2>\n";
        $content .= "<p>By accessing and using OCS Marketplace, you accept and agree to be bound by these Terms of Service.</p>\n\n";

        $content .= "<h2>2. Use of Service</h2>\n";
        $content .= "<p>You agree to use our services only for lawful purposes and in accordance with these Terms.</p>\n\n";

        $content .= "<h2>3. User Accounts</h2>\n";
        $content .= "<p>You are responsible for maintaining the confidentiality of your account credentials.</p>\n\n";

        $content .= "<h2>4. Prohibited Activities</h2>\n";
        $content .= "<ul>\n";
        $content .= "<li>Violating any applicable laws or regulations</li>\n";
        $content .= "<li>Infringing on intellectual property rights</li>\n";
        $content .= "<li>Transmitting malicious code or spam</li>\n";
        $content .= "</ul>\n\n";

        $content .= "<h2>5. Limitation of Liability</h2>\n";
        $content .= "<p>OCS Marketplace shall not be liable for any indirect, incidental, or consequential damages.</p>\n\n";

        $content .= "<h2>6. Contact Information</h2>\n";
        $content .= "<p>For questions about these Terms, please contact us at support@ocsapp.ca</p>\n";

    } else if ($type === 'privacy') {
        $content .= "<h2>1. Information We Collect</h2>\n";
        $content .= "<p>We collect information you provide directly to us, including name, email, and payment information.</p>\n\n";

        $content .= "<h2>2. How We Use Your Information</h2>\n";
        $content .= "<ul>\n";
        $content .= "<li>To provide and maintain our services</li>\n";
        $content .= "<li>To process your transactions</li>\n";
        $content .= "<li>To send you updates and marketing communications</li>\n";
        $content .= "</ul>\n\n";

        $content .= "<h2>3. Information Sharing</h2>\n";
        $content .= "<p>We do not sell your personal information. We may share your information with service providers who assist us.</p>\n\n";

        $content .= "<h2>4. Data Security</h2>\n";
        $content .= "<p>We implement appropriate security measures to protect your personal information.</p>\n\n";

        $content .= "<h2>5. Your Rights</h2>\n";
        $content .= "<p>You have the right to access, update, or delete your personal information.</p>\n\n";

        $content .= "<h2>6. Contact Us</h2>\n";
        $content .= "<p>If you have questions about this Privacy Policy, contact us at privacy@ocsapp.ca</p>\n";
    }

    return $content;
}
