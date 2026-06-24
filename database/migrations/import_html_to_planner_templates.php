<?php
/**
 * Import HTML files into planner_templates table
 *
 * Sources:
 *   - Root directory onboarding/ops/strategy docs
 *   - job-descriptions/ folder
 *   - job-descriptions/about-pages/ bilingual pages
 *
 * Run: php database/migrations/import_html_to_planner_templates.php
 */

require_once dirname(dirname(__DIR__)) . '/bootstrap/init.php';
require_once dirname(dirname(__DIR__)) . '/config/database.php';

$db = Database::getConnection();
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Resolve admin user ID to use as created_by
$stmt = $db->query("SELECT id FROM users WHERE role IN ('super_admin','admin') ORDER BY id ASC LIMIT 1");
$adminUser = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$adminUser) {
    echo "ERROR: No admin user found in users table. Cannot proceed.\n";
    exit(1);
}
$createdBy = (int) $adminUser['id'];
echo "Using created_by = $createdBy (admin user)\n\n";

$root = dirname(dirname(__DIR__));

// File map: [ file_path_relative_to_root, display_name, slug, category ]
$files = [
    // Onboarding
    ['BUSINESS_ONBOARDING_PACKAGE.html',                          'Business Onboarding Package',              'business-onboarding-package',          'onboarding'],
    ['BUYER_ONBOARDING_PACKAGE.html',                             'Buyer Onboarding Package',                 'buyer-onboarding-package',             'onboarding'],
    ['DRIVER_ONBOARDING_PACKAGE.html',                            'Driver Onboarding Package',                'driver-onboarding-package',            'onboarding'],
    ['SELLER_ONBOARDING_PACKAGE.html',                            'Seller Onboarding Package',                'seller-onboarding-package',            'onboarding'],
    ['SUPPLIER_ONBOARDING_PACKAGE.html',                          'Supplier Onboarding Package',              'supplier-onboarding-package',          'onboarding'],
    ['job-descriptions/founding-seller-onboarding-package.html', 'Founding Seller Onboarding Package',       'founding-seller-onboarding-package',   'onboarding'],

    // Pricing & Tiers
    ['SELLER_SERVICE_TIERS.html',                                 'Seller Service Tiers',                     'seller-service-tiers',                 'pricing'],
    ['SUPPLIER_SERVICE_TIERS.html',                               'Supplier Service Tiers',                   'supplier-service-tiers',               'pricing'],
    ['job-descriptions/ocs-distribution-pricing-model.html',     'Distribution Pricing Model',               'distribution-pricing-model',           'pricing'],

    // Strategy
    ['OCSAPP_INVESTOR_DECK.html',                                 'Investor Deck',                            'investor-deck',                        'strategy'],
    ['job-descriptions/investor-deck.html',                       'Investor Deck (Apr 13 Draft)',             'investor-deck-apr13-draft',            'strategy'],
    ['DISTRIBUTION_BUSINESS_EXPANSION.html',                      'Distribution Business Expansion',          'distribution-business-expansion',      'strategy'],
    ['job-descriptions/seller-pitch-deck.html',                  'Seller Pitch Deck',                        'seller-pitch-deck',                    'strategy'],

    // Operations
    ['OCSAPP_OPERATIONS_MASTER_PLAN.html',                        'Operations Master Plan',                   'operations-master-plan',               'operations'],
    ['FEBRUARY_OPERATIONS_PLAN.html',                             'February Operations Plan (Feb 2026)',      'february-operations-plan-2026',        'operations'],
    ['job-descriptions/ocs-delivery-distance-model.html',        'Delivery Distance Model',                  'delivery-distance-model',              'operations'],
    ['job-descriptions/ocs-distribution-order-flow.html',        'Distribution Order Flow',                  'distribution-order-flow',              'operations'],

    // Security
    ['SECURITY_AUDIT_REPORT.html',                                'Security Audit Report (Feb 2026)',         'security-audit-report-feb-2026',       'security'],

    // Job Descriptions
    ['job-descriptions/delivery-driver.html',                    'Delivery Driver Job Description',          'jd-delivery-driver',                   'job-descriptions'],
    ['job-descriptions/distribution-driver.html',                'Distribution Driver Job Description',      'jd-distribution-driver',               'job-descriptions'],
    ['job-descriptions/distribution-driver-quill.html',         'Distribution Driver (Quill Draft)',        'jd-distribution-driver-quill-draft',   'job-descriptions'],

    // Templates
    ['job-descriptions/ocs-meeting-notes-template.html',         'Meeting Notes Template',                   'meeting-notes-template',               'templates'],
    ['job-descriptions/ocs-meeting-template.html',               'Meeting Template (Draft)',                  'meeting-template-draft',               'templates'],
    ['job-descriptions/seller-email-templates.html',             'Seller Email Templates',                   'seller-email-templates',               'templates'],

    // About Pages (bilingual)
    ['job-descriptions/about-pages/about-buyer-central-en.html',  'About Buyer Central (EN)',               'about-buyer-central-en',               'about-pages'],
    ['job-descriptions/about-pages/about-buyer-central-fr.html',  'About Buyer Central (FR)',               'about-buyer-central-fr',               'about-pages'],
    ['job-descriptions/about-pages/about-distribution-en.html',   'About Distribution (EN)',                'about-distribution-en',                'about-pages'],
    ['job-descriptions/about-pages/about-distribution-fr.html',   'About Distribution (FR)',                'about-distribution-fr',                'about-pages'],
    ['job-descriptions/about-pages/about-ocsapp-en.html',         'About OCSAPP (EN)',                      'about-ocsapp-en',                      'about-pages'],
    ['job-descriptions/about-pages/about-ocsapp-fr.html',         'About OCSAPP (FR)',                      'about-ocsapp-fr',                      'about-pages'],
    ['job-descriptions/about-pages/about-seller-central-en.html', 'About Seller Central (EN)',              'about-seller-central-en',              'about-pages'],
    ['job-descriptions/about-pages/about-seller-central-fr.html', 'About Seller Central (FR)',              'about-seller-central-fr',              'about-pages'],
    ['job-descriptions/about-pages/about-supplier-portal-en.html','About Supplier Portal (EN)',             'about-supplier-portal-en',             'about-pages'],
    ['job-descriptions/about-pages/about-supplier-portal-fr.html','About Supplier Portal (FR)',             'about-supplier-portal-fr',             'about-pages'],
];

$inserted = 0;
$skipped  = 0;
$missing  = 0;
$errors   = 0;

$stmt = $db->prepare("
    INSERT IGNORE INTO planner_templates (name, slug, category, content, created_by)
    VALUES (:name, :slug, :category, :content, :created_by)
");

echo str_repeat('=', 60) . "\n";
echo "Importing HTML files into planner_templates\n";
echo str_repeat('=', 60) . "\n\n";

foreach ($files as [$relPath, $name, $slug, $category]) {
    $fullPath = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relPath);

    if (!file_exists($fullPath)) {
        echo "  MISSING  $relPath\n";
        $missing++;
        continue;
    }

    $content = file_get_contents($fullPath);
    if ($content === false) {
        echo "  ERROR    Could not read $relPath\n";
        $errors++;
        continue;
    }

    try {
        $stmt->execute([
            ':name'       => $name,
            ':slug'       => $slug,
            ':category'   => $category,
            ':content'    => $content,
            ':created_by' => $createdBy,
        ]);

        if ($stmt->rowCount() > 0) {
            echo "  INSERTED [$category] $name\n";
            $inserted++;
        } else {
            echo "  SKIPPED  [$category] $name (slug '$slug' already exists)\n";
            $skipped++;
        }
    } catch (\PDOException $e) {
        echo "  ERROR    $name: " . $e->getMessage() . "\n";
        $errors++;
    }
}

echo "\n" . str_repeat('=', 60) . "\n";
echo "Done.\n";
echo "  Inserted : $inserted\n";
echo "  Skipped  : $skipped (already in DB)\n";
echo "  Missing  : $missing (file not found)\n";
echo "  Errors   : $errors\n";
echo str_repeat('=', 60) . "\n";
