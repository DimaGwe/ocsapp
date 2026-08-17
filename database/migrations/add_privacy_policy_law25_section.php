<?php
/**
 * Migration: Privacy Policy Section 10 - Automated Signal-Based Suggestions
 * (Buyer Terms of Service Sec. 11.5, Law 25).
 *
 * Discovered mid-build that /privacy is DB-driven (legal_content table,
 * PageController::privacy()) - the static app/Views/legal/privacy.php file
 * is only a fallback rendered when no published DB row exists, and both EN
 * (id 2) and FR (id 5) rows are published, so editing the static file alone
 * had zero effect on the live page. This migration edits the real content,
 * following AdminLegalController's own edit pattern exactly (revision row
 * for the old version, update in place, version += 1, revision row for the
 * new version) rather than mutating legal_content directly and losing the
 * audit trail the app's own admin UI already maintains.
 *
 * Inserted so the new section lands as "Section 10" in both languages,
 * matching what the Buyer ToS Sec 11.5 already cites by number - both rows
 * get their downstream section numbers bumped by one accordingly.
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

function applyLaw25Section(PDO $db, int $legalContentId, string $insertAfterMarker, array $renumberMap, string $newSectionHtml, string $newHeadingCheck): void
{
    $stmt = $db->prepare("SELECT * FROM legal_content WHERE id = ? LIMIT 1");
    $stmt->execute([$legalContentId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo "legal_content id={$legalContentId} not found. Skipping.\n";
        return;
    }

    if (strpos($row['content'], $newHeadingCheck) !== false) {
        echo "legal_content id={$legalContentId} already has the Law 25 section. Skipping.\n";
        return;
    }

    $content = $row['content'];

    // Renumber downstream headings first (highest number first, so a lower
    // renumbered heading never accidentally matches a higher target string).
    krsort($renumberMap);
    foreach ($renumberMap as $from => $to) {
        $content = str_replace($from, $to, $content);
    }

    // Insert the new section right after the marker (end of Section 9).
    $pos = strpos($content, $insertAfterMarker);
    if ($pos === false) {
        echo "legal_content id={$legalContentId}: insertion marker not found - content may have changed since this migration was written. Skipping to avoid corrupting live content.\n";
        return;
    }
    // Match this row's own line-ending convention - EN content uses CRLF,
    // FR content uses LF-only, discovered while writing this migration.
    $eol = strpos($row['content'], "\r\n") !== false ? "\r\n" : "\n";
    $insertAt = $pos + strlen($insertAfterMarker);
    $content = substr($content, 0, $insertAt) . $eol . $newSectionHtml . substr($content, $insertAt);

    // Same revision-tracking pattern as AdminLegalController::update() -
    // preserve the pre-edit version as a revision before overwriting.
    $db->prepare("
        INSERT INTO legal_content_revisions (legal_content_id, title, content, version, created_by, notes)
        VALUES (?, ?, ?, ?, NULL, 'Auto-saved before Law 25 Section 10 migration')
    ")->execute([$legalContentId, $row['title'], $row['content'], $row['version']]);

    $newVersion = (int)$row['version'] + 1;
    $db->prepare("
        UPDATE legal_content SET content = ?, version = ?, updated_at = NOW() WHERE id = ?
    ")->execute([$content, $newVersion, $legalContentId]);

    $db->prepare("
        INSERT INTO legal_content_revisions (legal_content_id, title, content, version, created_by, notes)
        VALUES (?, ?, ?, ?, NULL, 'Added Section 10 - Automated Signal-Based Suggestions (Buyer ToS Sec 11.5, Law 25)')
    ")->execute([$legalContentId, $row['title'], $content, $newVersion]);

    echo "legal_content id={$legalContentId} updated to v{$newVersion} with the new Section 10.\n";
}

try {
    $db = Database::getConnection();

    $enSection = <<<'HTML'
<h2>10. Automated Signal-Based Suggestions (Claims)</h2>
<p>When you file a claim under OCSAPP's Returns &amp; Refund Policy (Marketplace or Business Distribution), our system may compute a <strong>suggestion</strong> as to the likely cause of the issue (vendor/supplier-caused or transit-caused) from structured information we already hold - specifically, whether a pickup photo, delivery proof, or signature was captured for your order.</p>
<ul>
<li>This suggestion is <strong>never a final decision</strong>: an OCSAPP team member always reviews the claim and confirms (or overrides) the suggestion before any refund, replacement, credit note, or deduction is carried out. No financial decision is made on a fully automated basis.</li>
<li>You have the right to be informed that a suggestion was generated for your claim.</li>
<li>You have the right to know the personal information used (for example, the presence or absence of the photo/signature evidence above) and the main factors behind the suggestion.</li>
<li>You have the right to request that a member of our team review the decision, and to submit your own observations - even though, as things stand today, a person already reviews every claim before a decision is finalized.</li>
</ul>
<p>To exercise these rights or get more detail on a specific claim, contact <a href="mailto:privacy@ocsapp.ca">privacy@ocsapp.ca</a> or <a href="mailto:support@ocsapp.ca">support@ocsapp.ca</a> with your claim number.</p>
HTML;

    applyLaw25Section(
        $db,
        2, // EN
        "<li>Marketing data: Until you opt out + 30 days</li>\r\n</ul>",
        [
            '10. Third-Party Links' => '11. Third-Party Links',
            '11. Canadian Privacy Laws' => '12. Canadian Privacy Laws',
            '12. Changes to This Privacy Policy' => '13. Changes to This Privacy Policy',
            '13. Contact Us' => '14. Contact Us',
        ],
        $enSection,
        'Automated Signal-Based Suggestions'
    );

    $frSection = <<<'HTML'
<h2>10. Suggestions fondées sur des signaux automatisés (réclamations)</h2>
<p>Lorsque vous déposez une réclamation en vertu de la Politique de retours et de remboursement d'OCSAPP (Marché ou Distribution aux entreprises), notre système peut calculer une <strong>suggestion</strong> quant à la cause probable du problème (responsabilité du vendeur/fournisseur ou incident survenu en transit) à partir de renseignements structurés déjà en notre possession - notamment la présence ou l'absence d'une photo de ramassage, d'une preuve de livraison ou d'une signature associée à votre commande.</p>
<ul>
<li>Cette suggestion n'est <strong>jamais une décision finale</strong> : un membre de l'équipe OCSAPP examine toujours la réclamation et confirme (ou infirme) la suggestion avant qu'un remboursement, un remplacement, une note de crédit ou une déduction ne soit exécuté. Aucune décision financière n'est prise de façon entièrement automatisée.</li>
<li>Vous avez le droit d'être informé qu'une suggestion a été générée pour votre réclamation.</li>
<li>Vous avez le droit de connaître les renseignements personnels utilisés (par exemple, la présence ou l'absence des preuves photo/signature ci-dessus) et les principaux facteurs ayant mené à la suggestion.</li>
<li>Vous avez le droit de demander une révision de la décision par un membre du personnel, et de lui présenter vos observations, même si - dans notre cas actuel - une personne examine déjà chaque réclamation avant qu'une décision ne soit finalisée.</li>
</ul>
<p>Pour exercer ces droits ou obtenir plus de détails sur une réclamation précise, contactez <a href="mailto:privacy@ocsapp.ca">privacy@ocsapp.ca</a> ou <a href="mailto:support@ocsapp.ca">support@ocsapp.ca</a> en indiquant le numéro de votre réclamation.</p>
HTML;

    applyLaw25Section(
        $db,
        5, // FR
        "<li>Données marketing : Jusqu'au retrait du consentement + 30 jours</li>\n</ul>",
        [
            '10. Lois canadiennes sur la protection des renseignements personnels' => '11. Lois canadiennes sur la protection des renseignements personnels',
            '11. Modifications de la politique de confidentialité' => '12. Modifications de la politique de confidentialité',
            '12. Nous contacter' => '13. Nous contacter',
        ],
        $frSection,
        'signaux automatisés'
    );

    echo "Migration complete.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
