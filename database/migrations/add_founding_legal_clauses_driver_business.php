<?php
/**
 * Migration: Founding Driver + Founding Business legal clauses (draft)
 *
 * Adds a new "Founding Partner Program (Draft)" section to the
 * driver_agreement and distribution_agreement legal_content rows, seeded by
 * setup_legal_agreements.php with generic Section 1-N numbering (not the
 * Sec 7.x/8.x split structure the source PDFs assume - that structure
 * doesn't exist in the live document, so this uses the document's own real
 * numbering instead of a section reference that wouldn't correspond to
 * anything). Explicitly flagged as a working draft pending counsel review,
 * same as the standalone Driver Contractor Agreement draft. Follows
 * AdminLegalController::update()'s revision-then-update sequence: snapshot
 * the current row into legal_content_revisions, then bump version.
 *
 * Idempotent: skips a row if it already contains "Founding" text.
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

$driverEnClause = <<<'HTML'

<h3>{{N}}. Founding Driver Partner Program (Draft)</h3>
<div style="background:#fef3c7;border-left:4px solid #f59e0b;padding:16px;margin:16px 0;border-radius:4px;">
  <strong>Draft provision:</strong> This section is a working draft pending final legal counsel review. It is not yet a finalized, counsel-reviewed term of this Agreement.
</div>
<ul>
  <li>OCSAPP is running a Founding Driver Partner cohort limited to the first fifty (50) drivers approved and activated under this Agreement.</li>
  <li>Eligible drivers receive a permanent "Founding Driver" badge on their profile, recognizing their status as an early platform partner.</li>
  <li>Additional benefits described in OCSAPP's Founding Driver Program materials (a milestone bonus, a referral bonus, and a priority dispatch tier) are planned but not yet active under this Agreement - they will be added by a future update once the corresponding operational mechanism is built.</li>
  <li>Founding Driver status does not modify the standard 70/30 earnings split described in Section 8, nor any other term of this Agreement.</li>
</ul>
HTML;

$driverFrClause = <<<'HTML'

<h3>{{N}}. Programme Partenaire Livreur Fondateur (Ébauche)</h3>
<div style="background:#fef3c7;border-left:4px solid #f59e0b;padding:16px;margin:16px 0;border-radius:4px;">
  <strong>Disposition en ébauche :</strong> Cette section est une ébauche de travail en attente de révision juridique finale. Elle ne constitue pas encore une modalité finalisée et révisée par un conseiller juridique de la présente entente.
</div>
<ul>
  <li>OCSAPP exploite une cohorte de Partenaires Livreurs Fondateurs limitée aux cinquante (50) premiers livreurs approuvés et activés en vertu de la présente entente.</li>
  <li>Les livreurs admissibles reçoivent un badge permanent « Livreur Fondateur » sur leur profil, reconnaissant leur statut de partenaire précoce de la plateforme.</li>
  <li>Les avantages supplémentaires décrits dans les documents du Programme Livreur Fondateur d'OCSAPP (une prime d'étape, une prime de parrainage et une priorité de répartition) sont prévus mais pas encore actifs en vertu de la présente entente - ils seront ajoutés par une mise à jour future une fois le mécanisme opérationnel correspondant construit.</li>
  <li>Le statut de Livreur Fondateur ne modifie pas la répartition standard des gains 70/30 décrite à la section 8, ni aucune autre modalité de la présente entente.</li>
</ul>
HTML;

$businessEnClause = <<<'HTML'

<h3>{{N}}. Founding Business Partner Program (Draft)</h3>
<div style="background:#fef3c7;border-left:4px solid #f59e0b;padding:16px;margin:16px 0;border-radius:4px;">
  <strong>Draft provision:</strong> This section is a working draft pending final legal counsel review. It is not yet a finalized, counsel-reviewed term of this Agreement.
</div>
<ul>
  <li>OCSAPP is running a Founding Business Partner cohort limited to the first five (5) business accounts approved under this Agreement.</li>
  <li>Eligible accounts have their Distribution commission rate locked to the Debutant tier (5%) with the corresponding monthly subscription fee waived, for six (6) months from the date of approval.</li>
  <li>After the six-month period, pricing reverts automatically to the Client's then-current Distribution plan under Section 4, with no further action required.</li>
  <li>Eligible accounts also receive a dedicated OCSAPP account manager from day one, normally reserved for higher plan tiers, assigned by OCSAPP's operations team.</li>
  <li>An additional Approvisionnement fee waiver described in OCSAPP's Founding Business Program materials is planned but not yet active under this Agreement - it will be added by a future update once the corresponding operational mechanism is built.</li>
</ul>
HTML;

$businessFrClause = <<<'HTML'

<h3>{{N}}. Programme Partenaire Commercial Fondateur (Ébauche)</h3>
<div style="background:#fef3c7;border-left:4px solid #f59e0b;padding:16px;margin:16px 0;border-radius:4px;">
  <strong>Disposition en ébauche :</strong> Cette section est une ébauche de travail en attente de révision juridique finale. Elle ne constitue pas encore une modalité finalisée et révisée par un conseiller juridique de la présente entente.
</div>
<ul>
  <li>OCSAPP exploite une cohorte de Partenaires Commerciaux Fondateurs limitée aux cinq (5) premiers comptes commerciaux approuvés en vertu de la présente entente.</li>
  <li>Les comptes admissibles voient leur taux de commission Distribution verrouillé au palier Débutant (5 %), avec les frais d'abonnement mensuels correspondants exemptés, pendant six (6) mois à compter de la date d'approbation.</li>
  <li>Après cette période de six mois, la tarification revient automatiquement au palier Distribution alors en vigueur pour le Client selon la section 4, sans démarche supplémentaire requise.</li>
  <li>Les comptes admissibles bénéficient également d'un gestionnaire de compte OCSAPP dédié dès le premier jour, normalement réservé aux paliers supérieurs, assigné par l'équipe des opérations d'OCSAPP.</li>
  <li>Une exemption additionnelle des frais d'Approvisionnement décrite dans les documents du Programme Commercial Fondateur d'OCSAPP est prévue mais pas encore active en vertu de la présente entente - elle sera ajoutée par une mise à jour future une fois le mécanisme opérationnel correspondant construit.</li>
</ul>
HTML;

/**
 * Insert $clause as a new section immediately before the heading whose text
 * (ignoring its number) matches $headingText, taking over that heading's
 * current number and pushing it - and every later section - up by one.
 *
 * Structure-agnostic on purpose: environments have drifted (e.g. staging's
 * driver_agreement is v2 with an extra "Language / Langue" section a fresh
 * local seed doesn't have), which shifts every trailing section's number by
 * an unpredictable amount. Rather than hardcode both ends of a renumbering
 * range per environment, this finds $headingText's ACTUAL current number via
 * regex, finds the highest "<h3>N." anywhere in the document, and renumbers
 * that whole real range - correct regardless of how many sections exist
 * after the insertion point in any given environment.
 */
function insertFoundingSection(string $content, string $headingText, string $clause): string
{
    if (!preg_match('/<h3>(\d+)\.\s*' . preg_quote($headingText, '/') . '<\/h3>/u', $content, $m)) {
        throw new Exception("heading not found: {$headingText}");
    }
    $insertAt = (int)$m[1];

    preg_match_all('/<h3>(\d+)\./', $content, $all);
    $maxNum = max(array_map('intval', $all[1]));

    for ($n = $maxNum; $n >= $insertAt; $n--) {
        $content = str_replace("<h3>{$n}.", "<h3>" . ($n + 1) . '.', $content);
    }

    $beforeMarker = '<h3>' . ($insertAt + 1) . ". {$headingText}</h3>";
    if (strpos($content, $beforeMarker) === false) {
        throw new Exception("post-renumber marker not found: {$beforeMarker}");
    }
    $clause = str_replace('{{N}}', (string)$insertAt, $clause);
    return str_replace($beforeMarker, $clause . "\n\n" . $beforeMarker, $content);
}

try {
    $db = Database::getConnection();

    // Looked up by page_type/language (not a hardcoded id) so this runs
    // correctly on staging/prod too, where auto-increment ids will differ.
    // ORDER BY version DESC per reference_legal_content_db_driven.md - always
    // take the latest version, never assume id order.
    //
    // 'headingText' anchors on the target heading's TEXT only (its number is
    // detected live by insertFoundingSection) - environments have drifted
    // (e.g. staging's agreements are v2+ with an extra "Language / Langue"
    // section a fresh local seed doesn't have), which shifts every trailing
    // section's number unpredictably. Anchoring on text, not number, makes
    // this correct regardless of that drift.
    $targets = [
        ['page_type' => 'driver_agreement', 'language' => 'en', 'clause' => $driverEnClause, 'headingText' => 'Governing Law'],
        ['page_type' => 'driver_agreement', 'language' => 'fr', 'clause' => $driverFrClause, 'headingText' => 'Droit applicable'],
        ['page_type' => 'distribution_agreement', 'language' => 'en', 'clause' => $businessEnClause, 'headingText' => 'Limitation of Liability'],
        ['page_type' => 'distribution_agreement', 'language' => 'fr', 'clause' => $businessFrClause, 'headingText' => 'Limitation de responsabilité'],
    ];

    foreach ($targets as $spec) {
        $stmt = $db->prepare("
            SELECT * FROM legal_content
            WHERE page_type = ? AND language = ?
            ORDER BY version DESC LIMIT 1
        ");
        $stmt->execute([$spec['page_type'], $spec['language']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            echo "legal_content {$spec['page_type']}/{$spec['language']} not found. Skipping.\n";
            continue;
        }
        $id = (int)$row['id'];
        if (strpos($row['content'], 'Founding') !== false || strpos($row['content'], 'Fondateur') !== false) {
            echo "legal_content id={$id} ({$row['page_type']}/{$row['language']}) already has Founding text. Skipping.\n";
            continue;
        }

        try {
            $newContent = insertFoundingSection($row['content'], $spec['headingText'], $spec['clause']);
        } catch (Exception $e) {
            echo "legal_content id={$id} ({$row['page_type']}/{$row['language']}): " . $e->getMessage() . " - skipping to avoid corrupting content.\n";
            continue;
        }
        $newVersion = (int)$row['version'] + 1;

        $db->prepare("
            INSERT INTO legal_content_revisions (legal_content_id, title, content, version, created_by, notes)
            VALUES (?, ?, ?, ?, NULL, 'Auto-saved before Founding Driver/Business clause insert')
        ")->execute([$id, $row['title'], $row['content'], $row['version']]);

        $db->prepare("
            UPDATE legal_content SET content = ?, version = ?, updated_at = NOW() WHERE id = ?
        ")->execute([$newContent, $newVersion, $id]);

        echo "legal_content id={$id} ({$row['page_type']}/{$row['language']}): Founding clause added, now v{$newVersion}.\n";
    }

    echo "Migration complete.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
