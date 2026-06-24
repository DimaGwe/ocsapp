<?php
/**
 * Migration: setup_legal_agreements.php
 * Adds supplier_agreement, driver_agreement, distribution_agreement, nda
 * to legal_content table with full EN+FR content.
 * Run: php database/migrations/setup_legal_agreements.php
 */

require_once __DIR__ . '/../../bootstrap/init.php';
require_once __DIR__ . '/../../config/database.php';

try {
$db = Database::getConnection();

// ── 1. Extend page_type ENUM ──────────────────────────────────────────────
echo "Extending page_type ENUM...\n";
$db->exec("
    ALTER TABLE legal_content
    MODIFY COLUMN page_type
        ENUM('terms','privacy','cookies','refund','shipping','seller_agreement',
             'supplier_agreement','driver_agreement','distribution_agreement','nda')
        NOT NULL
");
echo "  ✓ ENUM extended\n\n";

// ── Helper ────────────────────────────────────────────────────────────────
function insertAgreement(PDO $db, string $type, string $lang, string $title, string $content): void
{
    $check = $db->prepare("SELECT id FROM legal_content WHERE page_type = ? AND language = ?");
    $check->execute([$type, $lang]);
    if ($check->fetch()) {
        echo "  SKIP {$type}/{$lang} (already exists)\n";
        return;
    }
    $stmt = $db->prepare("
        INSERT INTO legal_content (page_type, language, title, content, is_published, version, published_at)
        VALUES (?, ?, ?, ?, 1, 1, NOW())
    ");
    $stmt->execute([$type, $lang, $title, $content]);
    echo "  ✓ Inserted {$type}/{$lang}\n";
}

// ═════════════════════════════════════════════════════════════════════════
// 2. SUPPLIER AGREEMENT
// ═════════════════════════════════════════════════════════════════════════
echo "Inserting Supplier Agreement (EN)...\n";
insertAgreement($db, 'supplier_agreement', 'en', 'Supplier Agreement', <<<HTML
<h2>Supplier Agreement</h2>
<p><strong>Last Updated: March 1, 2026</strong></p>

<p>This Supplier Agreement ("Agreement") is entered into between <strong>OCSAPP INC.</strong> ("OCSAPP", "we", "us") and the supplier entity identified during the onboarding process ("Supplier", "you"). By completing the supplier registration and activation process, you agree to be bound by the terms of this Agreement.</p>

<h3>1. Scope of Services</h3>
<p>OCSAPP provides an online marketplace platform that connects suppliers with buyers and sellers across Canada. As a supplier, you agree to make your products and/or services available through the OCSAPP platform in accordance with this Agreement.</p>

<h3>2. Purchase Orders (PO Terms)</h3>
<ul>
  <li>Purchase orders issued by OCSAPP or authorized sellers constitute binding purchase commitments upon your written acceptance.</li>
  <li>You must acknowledge receipt of each PO within <strong>2 business days</strong>.</li>
  <li>Partial fulfilment must be communicated prior to the agreed delivery date.</li>
  <li>OCSAPP reserves the right to cancel unfulfilled POs after 5 business days of non-response without penalty.</li>
</ul>

<h3>3. Delivery Timelines</h3>
<ul>
  <li>Standard delivery timelines will be specified in each individual PO.</li>
  <li>You must notify OCSAPP of any anticipated delays at least <strong>48 hours</strong> before the scheduled delivery date.</li>
  <li>Repeated late deliveries (3 or more instances in a rolling 90-day period) may result in account suspension.</li>
</ul>

<h3>4. Quality Standards</h3>
<ul>
  <li>All products must meet applicable Canadian federal and provincial safety and labelling standards.</li>
  <li>Products must match the descriptions, specifications, and images submitted during onboarding.</li>
  <li>OCSAPP reserves the right to reject any shipment that does not meet agreed quality standards.</li>
  <li>Rejected goods must be retrieved by the Supplier within 10 business days at the Supplier's expense.</li>
</ul>

<h3>5. Returns &amp; Defective Goods</h3>
<ul>
  <li>Defective or non-conforming goods may be returned to you within <strong>30 days</strong> of delivery.</li>
  <li>You agree to issue a full credit or replacement for confirmed defective items within 5 business days.</li>
  <li>Return shipping for defective goods is at the Supplier's expense.</li>
</ul>

<h3>6. Pricing</h3>
<ul>
  <li>Agreed pricing is fixed for the duration of each active PO.</li>
  <li>Price changes require <strong>30 days' written notice</strong> and apply only to future POs.</li>
  <li>All prices must be quoted in Canadian Dollars (CAD) and be inclusive of applicable taxes unless otherwise agreed.</li>
</ul>

<h3>7. Banking &amp; Payment</h3>
<ul>
  <li>Payment terms are <strong>Net 30</strong> from the date of approved delivery, unless otherwise stated in the PO.</li>
  <li>You must provide accurate Canadian banking information (institution, transit, and account number) for direct deposit.</li>
  <li>OCSAPP is not responsible for payment delays caused by inaccurate banking information.</li>
  <li>All payments are subject to a <strong>platform fee of 5%</strong> of the invoiced amount.</li>
</ul>

<h3>8. Confidentiality</h3>
<p>Both parties agree to keep confidential any proprietary information, pricing data, or business strategies shared during the course of this Agreement. This obligation survives termination of this Agreement for a period of <strong>2 years</strong>.</p>

<h3>9. Intellectual Property</h3>
<p>You grant OCSAPP a non-exclusive, royalty-free licence to use your brand name, logos, and product images solely for the purpose of listing and promoting your products on the OCSAPP platform.</p>

<h3>10. Term &amp; Termination</h3>
<ul>
  <li>This Agreement remains in effect until terminated by either party with <strong>30 days' written notice</strong>.</li>
  <li>OCSAPP may terminate immediately for: material breach, fraudulent activity, repeated quality failures, or insolvency.</li>
  <li>Upon termination, all outstanding POs and obligations must be fulfilled unless mutually agreed otherwise.</li>
</ul>

<h3>11. Liability</h3>
<p>The Supplier is solely responsible for product liability arising from defective goods, improper labelling, or non-compliance with applicable regulations. OCSAPP's liability is limited to the value of the relevant PO(s).</p>

<h3>12. Governing Law</h3>
<p>This Agreement is governed by the laws of the Province of Quebec and the applicable laws of Canada. Disputes shall be resolved by the courts of Quebec.</p>

<h3>13. Contact</h3>
<p>For questions regarding this Agreement: <strong>suppliers@ocsapp.ca</strong></p>
HTML);

echo "Inserting Supplier Agreement (FR)...\n";
insertAgreement($db, 'supplier_agreement', 'fr', 'Entente de fournisseur', <<<HTML
<h2>Entente de fournisseur</h2>
<p><strong>Dernière mise à jour : 1er mars 2026</strong></p>

<p>La présente entente de fournisseur (« Entente ») est conclue entre <strong>OCSAPP INC.</strong> (« OCSAPP », « nous ») et l'entité fournisseur identifiée lors du processus d'intégration (« Fournisseur », « vous »). En complétant l'inscription et l'activation en tant que fournisseur, vous acceptez d'être lié par les termes de cette Entente.</p>

<h3>1. Portée des services</h3>
<p>OCSAPP exploite une plateforme de marché en ligne qui met en relation les fournisseurs, vendeurs et acheteurs au Canada. En tant que fournisseur, vous acceptez de rendre vos produits et/ou services disponibles via la plateforme OCSAPP conformément à la présente Entente.</p>

<h3>2. Bons de commande (conditions)</h3>
<ul>
  <li>Les bons de commande (BC) émis par OCSAPP ou des vendeurs autorisés constituent des engagements d'achat fermes dès votre acceptation écrite.</li>
  <li>Vous devez accuser réception de chaque BC dans un délai de <strong>2 jours ouvrables</strong>.</li>
  <li>Toute exécution partielle doit être communiquée avant la date de livraison convenue.</li>
  <li>OCSAPP se réserve le droit d'annuler les BC non exécutés après 5 jours ouvrables sans réponse.</li>
</ul>

<h3>3. Délais de livraison</h3>
<ul>
  <li>Les délais de livraison standards seront précisés dans chaque BC individuel.</li>
  <li>Vous devez aviser OCSAPP de tout retard anticipé au moins <strong>48 heures</strong> avant la date de livraison prévue.</li>
  <li>Les retards répétés (3 ou plus sur une période de 90 jours) peuvent entraîner la suspension du compte.</li>
</ul>

<h3>4. Normes de qualité</h3>
<ul>
  <li>Tous les produits doivent satisfaire aux normes de sécurité et d'étiquetage fédérales et provinciales canadiennes applicables.</li>
  <li>Les produits doivent correspondre aux descriptions, spécifications et images soumises lors de l'intégration.</li>
  <li>OCSAPP se réserve le droit de refuser tout envoi ne respectant pas les normes de qualité convenues.</li>
  <li>Les marchandises refusées doivent être récupérées par le Fournisseur dans les 10 jours ouvrables à ses frais.</li>
</ul>

<h3>5. Retours et marchandises défectueuses</h3>
<ul>
  <li>Les marchandises défectueuses ou non conformes peuvent être retournées dans les <strong>30 jours</strong> suivant la livraison.</li>
  <li>Vous acceptez d'émettre un crédit complet ou un remplacement pour les articles défectueux confirmés dans les 5 jours ouvrables.</li>
  <li>Les frais de retour pour marchandises défectueuses sont à la charge du Fournisseur.</li>
</ul>

<h3>6. Tarification</h3>
<ul>
  <li>Les prix convenus sont fixes pour la durée de chaque BC actif.</li>
  <li>Toute modification de prix requiert un <strong>préavis écrit de 30 jours</strong> et s'applique uniquement aux futurs BC.</li>
  <li>Tous les prix doivent être indiqués en dollars canadiens (CAD) et inclure les taxes applicables, sauf entente contraire.</li>
</ul>

<h3>7. Coordonnées bancaires et paiement</h3>
<ul>
  <li>Les modalités de paiement sont <strong>Net 30</strong> à compter de la date de livraison approuvée, sauf indication contraire dans le BC.</li>
  <li>Vous devez fournir des coordonnées bancaires canadiennes exactes (institution, transit et numéro de compte) pour le dépôt direct.</li>
  <li>OCSAPP n'est pas responsable des retards de paiement causés par des informations bancaires inexactes.</li>
  <li>Tous les paiements sont assujettis à des <strong>frais de plateforme de 5 %</strong> du montant facturé.</li>
</ul>

<h3>8. Confidentialité</h3>
<p>Les deux parties s'engagent à garder confidentiels les informations propriétaires, les données tarifaires ou les stratégies commerciales partagées dans le cadre de cette Entente. Cette obligation survit à la résiliation de l'Entente pour une période de <strong>2 ans</strong>.</p>

<h3>9. Propriété intellectuelle</h3>
<p>Vous accordez à OCSAPP une licence non exclusive et libre de redevances pour utiliser votre nom de marque, vos logos et vos images de produits uniquement aux fins de l'inscription et de la promotion de vos produits sur la plateforme OCSAPP.</p>

<h3>10. Durée et résiliation</h3>
<ul>
  <li>La présente Entente reste en vigueur jusqu'à sa résiliation par l'une ou l'autre des parties avec un <strong>préavis écrit de 30 jours</strong>.</li>
  <li>OCSAPP peut résilier immédiatement en cas de : manquement grave, activité frauduleuse, défaillances de qualité répétées ou insolvabilité.</li>
  <li>À la résiliation, tous les BC et obligations en cours doivent être honorés, sauf accord mutuel contraire.</li>
</ul>

<h3>11. Responsabilité</h3>
<p>Le Fournisseur est seul responsable de la responsabilité du fait des produits découlant de marchandises défectueuses, d'un étiquetage inapproprié ou de la non-conformité aux réglementations applicables. La responsabilité d'OCSAPP est limitée à la valeur du ou des BC concernés.</p>

<h3>12. Droit applicable</h3>
<p>La présente Entente est régie par les lois de la province de Québec et les lois fédérales applicables du Canada. Les différends seront résolus par les tribunaux du Québec.</p>

<h3>13. Contact</h3>
<p>Pour toute question concernant cette Entente : <strong>suppliers@ocsapp.ca</strong></p>
HTML);

// ═════════════════════════════════════════════════════════════════════════
// 3. DRIVER / CONTRACTOR AGREEMENT
// ═════════════════════════════════════════════════════════════════════════
echo "Inserting Driver Agreement (EN)...\n";
insertAgreement($db, 'driver_agreement', 'en', 'Driver / Independent Contractor Agreement', <<<HTML
<h2>Driver / Independent Contractor Agreement</h2>
<p><strong>Last Updated: March 1, 2026</strong></p>

<p>This Agreement is entered into between <strong>OCSAPP INC.</strong> ("OCSAPP") and you, the delivery driver ("Driver", "Contractor"). By completing the driver application and activation process, you confirm that you have read, understood, and agree to be bound by the terms herein.</p>

<div style="background:#fef3c7;border-left:4px solid #f59e0b;padding:16px;margin:16px 0;border-radius:4px;">
  <strong>Important:</strong> You are an independent contractor, not an employee of OCSAPP. You are not entitled to employment benefits, vacation pay, or statutory deductions from OCSAPP.
</div>

<h3>1. Independent Contractor Classification</h3>
<ul>
  <li>You operate as an independent contractor and retain full control over how, when, and where you perform delivery services.</li>
  <li>OCSAPP does not set your hours, require exclusivity, or control your methods of work.</li>
  <li>You are solely responsible for reporting and remitting all applicable taxes, including income tax and HST/GST/QST if applicable.</li>
  <li>Nothing in this Agreement creates an employment relationship, partnership, or agency between you and OCSAPP.</li>
</ul>

<h3>2. Vehicle &amp; Insurance Requirements</h3>
<ul>
  <li>You must own or have lawful possession of a vehicle suitable for making deliveries.</li>
  <li>You must maintain valid automobile insurance that includes <strong>coverage for commercial or delivery use</strong> at all times while performing deliveries.</li>
  <li>Minimum liability coverage: <strong>$1,000,000 CAD</strong>.</li>
  <li>You must provide proof of insurance upon request and immediately notify OCSAPP of any lapse in coverage.</li>
  <li>You are responsible for all vehicle maintenance, fuel, and operating costs.</li>
</ul>

<h3>3. Driver Licence &amp; Legal Requirements</h3>
<ul>
  <li>You must hold a valid Canadian driver's licence appropriate for the class of vehicle used.</li>
  <li>You must comply with all applicable provincial and federal traffic laws at all times.</li>
  <li>You must notify OCSAPP immediately of any licence suspension, revocation, or criminal charge related to driving.</li>
</ul>

<h3>4. Background Check</h3>
<ul>
  <li>Activation as a driver requires a satisfactory background check (criminal record check).</li>
  <li>You consent to OCSAPP initiating or requesting a background check as part of the onboarding process.</li>
  <li>OCSAPP reserves the right to deny or suspend access based on background check results.</li>
</ul>

<h3>5. Training Requirements</h3>
<ul>
  <li>You must complete all mandatory OCSAPP driver training modules before accepting deliveries.</li>
  <li>Upon successful completion, a digital certificate (OCS-DRV-XXXXX) will be issued.</li>
  <li>OCSAPP may require periodic re-training to maintain active status.</li>
</ul>

<h3>6. Delivery Obligations</h3>
<ul>
  <li>You agree to handle all goods with care and deliver them in the condition received.</li>
  <li>You must follow all delivery instructions provided through the OCSAPP Driver App.</li>
  <li>You must obtain proof of delivery (photo or customer signature) as required by the App.</li>
  <li>You must maintain a professional and courteous manner when interacting with customers.</li>
</ul>

<h3>7. Liability</h3>
<ul>
  <li>You are liable for any damage to goods caused by your negligence during pickup, transport, or delivery.</li>
  <li>You indemnify and hold OCSAPP harmless from any third-party claims arising from your actions while performing deliveries.</li>
  <li>OCSAPP is not liable for accidents, injuries, or damages arising from your vehicle or driving.</li>
</ul>

<h3>8. Earnings &amp; Payout Schedule</h3>
<ul>
  <li>Earnings are calculated per delivery based on the rate schedule published in the OCSAPP Driver App.</li>
  <li>Payouts are processed <strong>weekly every Monday</strong> for the prior week's completed deliveries.</li>
  <li>You must provide accurate Canadian banking information for direct deposit.</li>
  <li>OCSAPP may deduct amounts for confirmed damage claims or reversed deliveries.</li>
</ul>

<h3>9. Availability &amp; Conduct</h3>
<ul>
  <li>You may set your own availability. Going "online" in the App indicates your availability to accept deliveries.</li>
  <li>Repeated rejection of assigned deliveries without valid reason may result in reduced dispatch priority or suspension.</li>
  <li>You agree not to engage in any conduct that could bring OCSAPP into disrepute.</li>
</ul>

<h3>10. Termination</h3>
<ul>
  <li>Either party may terminate this Agreement at any time with <strong>7 days' written notice</strong>.</li>
  <li>OCSAPP may terminate immediately for: criminal conviction, insurance lapse, fraudulent activity, repeated conduct violations, or safety concerns.</li>
  <li>Upon termination, any outstanding earnings for completed deliveries will be paid in the next regular payout cycle.</li>
</ul>

<h3>11. Governing Law</h3>
<p>This Agreement is governed by the laws of the Province of Quebec and applicable federal laws of Canada.</p>

<h3>12. Contact</h3>
<p>For questions: <strong>drivers@ocsapp.ca</strong></p>
HTML);

echo "Inserting Driver Agreement (FR)...\n";
insertAgreement($db, 'driver_agreement', 'fr', 'Entente de chauffeur / entrepreneur indépendant', <<<HTML
<h2>Entente de chauffeur / entrepreneur indépendant</h2>
<p><strong>Dernière mise à jour : 1er mars 2026</strong></p>

<p>La présente entente est conclue entre <strong>OCSAPP INC.</strong> (« OCSAPP ») et vous, le chauffeur-livreur (« Chauffeur », « Entrepreneur »). En complétant le processus de demande et d'activation de chauffeur, vous confirmez avoir lu, compris et accepté d'être lié par les termes ci-après.</p>

<div style="background:#fef3c7;border-left:4px solid #f59e0b;padding:16px;margin:16px 0;border-radius:4px;">
  <strong>Important :</strong> Vous êtes un entrepreneur indépendant et non un employé d'OCSAPP. Vous n'avez droit à aucun avantage social, congé payé ou retenue statutaire de la part d'OCSAPP.
</div>

<h3>1. Statut d'entrepreneur indépendant</h3>
<ul>
  <li>Vous exercez vos activités en tant qu'entrepreneur indépendant et conservez le plein contrôle sur la manière, le moment et le lieu d'exécution des services de livraison.</li>
  <li>OCSAPP ne fixe pas vos heures, n'exige pas d'exclusivité et ne contrôle pas vos méthodes de travail.</li>
  <li>Vous êtes seul responsable de la déclaration et du versement de tous les impôts et taxes applicables, incluant l'impôt sur le revenu et la TPS/TVQ si applicable.</li>
  <li>La présente entente ne crée aucun lien d'emploi, partenariat ou mandat entre vous et OCSAPP.</li>
</ul>

<h3>2. Exigences relatives au véhicule et aux assurances</h3>
<ul>
  <li>Vous devez être propriétaire ou avoir la possession légale d'un véhicule approprié pour effectuer des livraisons.</li>
  <li>Vous devez maintenir en tout temps une assurance automobile valide comprenant une <strong>couverture pour usage commercial ou de livraison</strong>.</li>
  <li>Couverture minimale en responsabilité civile : <strong>1 000 000 $ CAD</strong>.</li>
  <li>Vous devez fournir une preuve d'assurance sur demande et aviser immédiatement OCSAPP de toute interruption de couverture.</li>
  <li>Vous êtes responsable de tous les frais d'entretien, de carburant et d'exploitation du véhicule.</li>
</ul>

<h3>3. Permis de conduire et exigences légales</h3>
<ul>
  <li>Vous devez détenir un permis de conduire canadien valide approprié pour la classe de véhicule utilisée.</li>
  <li>Vous devez respecter en tout temps toutes les lois provinciales et fédérales sur la circulation routière.</li>
  <li>Vous devez aviser immédiatement OCSAPP de toute suspension, révocation de permis ou accusation criminelle liée à la conduite.</li>
</ul>

<h3>4. Vérification des antécédents</h3>
<ul>
  <li>L'activation en tant que chauffeur nécessite une vérification satisfaisante des antécédents (vérification du casier judiciaire).</li>
  <li>Vous consentez à ce qu'OCSAPP initie ou demande une vérification des antécédents dans le cadre du processus d'intégration.</li>
  <li>OCSAPP se réserve le droit de refuser ou de suspendre l'accès selon les résultats de la vérification.</li>
</ul>

<h3>5. Exigences de formation</h3>
<ul>
  <li>Vous devez compléter tous les modules de formation obligatoires OCSAPP avant d'accepter des livraisons.</li>
  <li>À la réussite, un certificat numérique (OCS-DRV-XXXXX) vous sera délivré.</li>
  <li>OCSAPP peut exiger une formation périodique pour maintenir le statut actif.</li>
</ul>

<h3>6. Obligations de livraison</h3>
<ul>
  <li>Vous acceptez de manipuler toutes les marchandises avec soin et de les livrer dans l'état dans lequel elles ont été reçues.</li>
  <li>Vous devez suivre toutes les instructions de livraison fournies via l'application chauffeur OCSAPP.</li>
  <li>Vous devez obtenir une preuve de livraison (photo ou signature du client) selon les exigences de l'application.</li>
  <li>Vous devez maintenir une attitude professionnelle et courtoise avec les clients.</li>
</ul>

<h3>7. Responsabilité</h3>
<ul>
  <li>Vous êtes responsable de tout dommage aux marchandises causé par votre négligence lors de la cueillette, du transport ou de la livraison.</li>
  <li>Vous indemnisez et dégagez OCSAPP de toute réclamation de tiers découlant de vos actions lors des livraisons.</li>
  <li>OCSAPP n'est pas responsable des accidents, blessures ou dommages liés à votre véhicule ou conduite.</li>
</ul>

<h3>8. Rémunération et calendrier de paiement</h3>
<ul>
  <li>La rémunération est calculée par livraison selon le barème publié dans l'application chauffeur OCSAPP.</li>
  <li>Les paiements sont traités <strong>chaque lundi</strong> pour les livraisons complétées la semaine précédente.</li>
  <li>Vous devez fournir des coordonnées bancaires canadiennes exactes pour le dépôt direct.</li>
  <li>OCSAPP peut déduire des montants pour des réclamations de dommages confirmées ou des livraisons annulées.</li>
</ul>

<h3>9. Disponibilité et conduite</h3>
<ul>
  <li>Vous pouvez définir vos propres disponibilités. Passer en mode « en ligne » dans l'application indique votre disponibilité à accepter des livraisons.</li>
  <li>Le refus répété de livraisons assignées sans raison valable peut entraîner une réduction de priorité de distribution ou une suspension.</li>
  <li>Vous vous engagez à ne pas vous livrer à des conduites pouvant nuire à la réputation d'OCSAPP.</li>
</ul>

<h3>10. Résiliation</h3>
<ul>
  <li>L'une ou l'autre des parties peut résilier la présente entente à tout moment avec un <strong>préavis écrit de 7 jours</strong>.</li>
  <li>OCSAPP peut résilier immédiatement en cas de : condamnation criminelle, lacune dans l'assurance, activité frauduleuse, violations répétées de conduite ou préoccupations de sécurité.</li>
  <li>À la résiliation, les gains en suspens pour les livraisons complétées seront versés lors du prochain cycle de paiement régulier.</li>
</ul>

<h3>11. Droit applicable</h3>
<p>La présente entente est régie par les lois de la province de Québec et les lois fédérales applicables du Canada.</p>

<h3>12. Contact</h3>
<p>Pour toute question : <strong>drivers@ocsapp.ca</strong></p>
HTML);

// ═════════════════════════════════════════════════════════════════════════
// 4. DISTRIBUTION SERVICE AGREEMENT
// ═════════════════════════════════════════════════════════════════════════
echo "Inserting Distribution Agreement (EN)...\n";
insertAgreement($db, 'distribution_agreement', 'en', 'Distribution Service Agreement', <<<HTML
<h2>Distribution Service Agreement</h2>
<p><strong>Last Updated: March 1, 2026</strong></p>

<p>This Distribution Service Agreement ("Agreement") is entered into between <strong>OCSAPP INC.</strong> ("OCSAPP") and the business client identified during onboarding ("Client"). By activating distribution services, you agree to the terms herein.</p>

<h3>1. Services Provided</h3>
<p>OCSAPP provides last-mile and local distribution services for businesses ("Distribution Services") including pickup from Client's location(s), warehousing/staging where applicable, and delivery to Client's customers within OCSAPP's service zones.</p>

<h3>2. Service Level Agreement (SLA)</h3>
<ul>
  <li><strong>Standard delivery:</strong> Within 24–48 hours of pickup within active service zones.</li>
  <li><strong>Express delivery:</strong> Same-day or next-day delivery (subject to availability and additional fees).</li>
  <li>OCSAPP will make commercially reasonable efforts to meet delivery timelines but does not guarantee delivery times for circumstances beyond our control (weather, road conditions, etc.).</li>
  <li>Service uptime target: <strong>95%</strong> of orders delivered within the quoted timeline per month.</li>
</ul>

<h3>3. Service Zones</h3>
<ul>
  <li>Distribution Services are available within zones designated by OCSAPP, as communicated during onboarding and updated from time to time.</li>
  <li>Deliveries outside designated zones may be arranged on a case-by-case basis at additional cost.</li>
</ul>

<h3>4. Pricing Tiers</h3>
<ul>
  <li>Pricing is based on volume tiers, delivery zone, and parcel weight/dimensions as specified in your account agreement.</li>
  <li>Prices are quoted in Canadian Dollars (CAD) and subject to applicable taxes (GST/QST).</li>
  <li>OCSAPP reserves the right to adjust pricing with <strong>30 days' written notice</strong>.</li>
  <li>Fuel surcharges may apply and will be itemised separately on invoices.</li>
</ul>

<h3>5. Client Obligations</h3>
<ul>
  <li>Client must ensure parcels are properly packaged, labelled, and ready for pickup at the agreed time.</li>
  <li>Client must provide accurate recipient addresses and contact information.</li>
  <li>Client is responsible for declaring any restricted or hazardous goods before shipment.</li>
  <li>OCSAPP reserves the right to refuse shipments that are improperly packaged or contain prohibited items.</li>
</ul>

<h3>6. Damage Liability</h3>
<ul>
  <li>OCSAPP's liability for damaged or lost parcels is limited to <strong>$100 CAD per parcel</strong> unless additional coverage is purchased.</li>
  <li>Claims for damage or loss must be submitted within <strong>5 business days</strong> of the scheduled delivery date with supporting documentation.</li>
  <li>OCSAPP is not liable for damage caused by improper packaging by the Client.</li>
</ul>

<h3>7. Payment Terms</h3>
<ul>
  <li>Invoices are issued <strong>weekly</strong> for all completed shipments in the prior week.</li>
  <li>Payment is due within <strong>Net 15</strong> of invoice date.</li>
  <li>Late payments are subject to a 1.5% monthly interest charge.</li>
  <li>OCSAPP may suspend services for accounts overdue by more than 30 days.</li>
</ul>

<h3>8. Termination</h3>
<ul>
  <li>Either party may terminate this Agreement with <strong>30 days' written notice</strong>.</li>
  <li>OCSAPP may terminate immediately for non-payment, fraudulent shipment declarations, or repeated SLA violations by the Client.</li>
  <li>Outstanding invoices remain payable upon termination.</li>
</ul>

<h3>9. Limitation of Liability</h3>
<p>OCSAPP's total liability under this Agreement shall not exceed the total fees paid by the Client in the 3 months preceding the claim. OCSAPP is not liable for indirect, consequential, or incidental damages.</p>

<h3>10. Governing Law</h3>
<p>This Agreement is governed by the laws of the Province of Quebec and applicable federal laws of Canada.</p>

<h3>11. Contact</h3>
<p>For questions: <strong>distribution@ocsapp.ca</strong></p>
HTML);

echo "Inserting Distribution Agreement (FR)...\n";
insertAgreement($db, 'distribution_agreement', 'fr', 'Entente de service de distribution', <<<HTML
<h2>Entente de service de distribution</h2>
<p><strong>Dernière mise à jour : 1er mars 2026</strong></p>

<p>La présente entente de service de distribution (« Entente ») est conclue entre <strong>OCSAPP INC.</strong> (« OCSAPP ») et le client commercial identifié lors de l'intégration (« Client »). En activant les services de distribution, vous acceptez les termes ci-après.</p>

<h3>1. Services fournis</h3>
<p>OCSAPP fournit des services de distribution locale et du dernier kilomètre pour les entreprises, incluant la cueillette au(x) lieu(x) du Client, l'entreposage/transit selon le cas, et la livraison aux clients du Client dans les zones de service d'OCSAPP.</p>

<h3>2. Accord de niveau de service (SLA)</h3>
<ul>
  <li><strong>Livraison standard :</strong> Dans les 24 à 48 heures suivant la cueillette dans les zones de service actives.</li>
  <li><strong>Livraison express :</strong> Le jour même ou le lendemain (sous réserve de disponibilité et de frais supplémentaires).</li>
  <li>OCSAPP fera des efforts commercialement raisonnables pour respecter les délais, mais ne garantit pas les délais pour des circonstances hors de notre contrôle (météo, état des routes, etc.).</li>
  <li>Objectif de disponibilité du service : <strong>95 %</strong> des commandes livrées dans le délai convenu par mois.</li>
</ul>

<h3>3. Zones de service</h3>
<ul>
  <li>Les services de distribution sont disponibles dans les zones désignées par OCSAPP, telles que communiquées lors de l'intégration et mises à jour périodiquement.</li>
  <li>Les livraisons hors zones désignées peuvent être arrangées au cas par cas à un coût supplémentaire.</li>
</ul>

<h3>4. Paliers tarifaires</h3>
<ul>
  <li>La tarification est basée sur des paliers de volume, la zone de livraison et le poids/dimensions des colis selon votre entente de compte.</li>
  <li>Les prix sont exprimés en dollars canadiens (CAD) et assujettis aux taxes applicables (TPS/TVQ).</li>
  <li>OCSAPP se réserve le droit d'ajuster les tarifs avec un <strong>préavis écrit de 30 jours</strong>.</li>
  <li>Des suppléments carburant peuvent s'appliquer et seront détaillés séparément sur les factures.</li>
</ul>

<h3>5. Obligations du Client</h3>
<ul>
  <li>Le Client doit s'assurer que les colis sont correctement emballés, étiquetés et prêts pour la cueillette à l'heure convenue.</li>
  <li>Le Client doit fournir des adresses de destinataires et des coordonnées exactes.</li>
  <li>Le Client est responsable de déclarer toute marchandise restreinte ou dangereuse avant l'expédition.</li>
  <li>OCSAPP se réserve le droit de refuser les envois mal emballés ou contenant des articles prohibés.</li>
</ul>

<h3>6. Responsabilité pour dommages</h3>
<ul>
  <li>La responsabilité d'OCSAPP pour les colis endommagés ou perdus est limitée à <strong>100 $ CAD par colis</strong>, sauf si une couverture supplémentaire est souscrite.</li>
  <li>Les réclamations doivent être soumises dans les <strong>5 jours ouvrables</strong> suivant la date de livraison prévue avec pièces justificatives.</li>
  <li>OCSAPP n'est pas responsable des dommages causés par un emballage inadéquat du Client.</li>
</ul>

<h3>7. Modalités de paiement</h3>
<ul>
  <li>Les factures sont émises <strong>chaque semaine</strong> pour tous les envois complétés la semaine précédente.</li>
  <li>Le paiement est dû dans les <strong>15 jours nets</strong> suivant la date de facturation.</li>
  <li>Les paiements en retard sont assujettis à des intérêts de 1,5 % par mois.</li>
  <li>OCSAPP peut suspendre les services pour les comptes en souffrance depuis plus de 30 jours.</li>
</ul>

<h3>8. Résiliation</h3>
<ul>
  <li>L'une ou l'autre des parties peut résilier la présente entente avec un <strong>préavis écrit de 30 jours</strong>.</li>
  <li>OCSAPP peut résilier immédiatement pour non-paiement, déclarations d'expédition frauduleuses ou violations répétées du SLA par le Client.</li>
  <li>Les factures impayées demeurent exigibles à la résiliation.</li>
</ul>

<h3>9. Limitation de responsabilité</h3>
<p>La responsabilité totale d'OCSAPP en vertu de la présente entente ne dépassera pas le total des frais payés par le Client au cours des 3 mois précédant la réclamation. OCSAPP n'est pas responsable des dommages indirects, consécutifs ou accessoires.</p>

<h3>10. Droit applicable</h3>
<p>La présente entente est régie par les lois de la province de Québec et les lois fédérales applicables du Canada.</p>

<h3>11. Contact</h3>
<p>Pour toute question : <strong>distribution@ocsapp.ca</strong></p>
HTML);

// ═════════════════════════════════════════════════════════════════════════
// 5. NON-DISCLOSURE AGREEMENT (NDA)
// ═════════════════════════════════════════════════════════════════════════
echo "Inserting NDA (EN)...\n";
insertAgreement($db, 'nda', 'en', 'Non-Disclosure Agreement (NDA)', <<<HTML
<h2>Non-Disclosure Agreement</h2>
<p><strong>Last Updated: March 1, 2026</strong></p>

<p>This Non-Disclosure Agreement ("NDA" or "Agreement") is entered into between <strong>OCSAPP INC.</strong> ("Disclosing Party") and the individual or entity ("Receiving Party") who is granted access to confidential information for the purpose of evaluating or performing a business relationship with OCSAPP. By accepting a role as staff, contractor, or partner, you agree to the terms of this Agreement.</p>

<h3>1. Definition of Confidential Information</h3>
<p>"Confidential Information" means any non-public information disclosed by OCSAPP to the Receiving Party, whether orally, in writing, or electronically, that is designated as confidential or that reasonably should be understood to be confidential given the nature of the information and circumstances of disclosure. This includes, without limitation:</p>
<ul>
  <li>Business plans, financial projections, and pricing strategies</li>
  <li>Source code, technical architecture, and system documentation</li>
  <li>Customer, supplier, and partner data and lists</li>
  <li>Marketing campaigns, product roadmaps, and unreleased features</li>
  <li>Internal processes, operating procedures, and trade secrets</li>
  <li>Employee and contractor compensation details</li>
</ul>

<h3>2. Exclusions from Confidential Information</h3>
<p>Confidential Information does not include information that:</p>
<ul>
  <li>Is or becomes publicly known through no breach of this Agreement;</li>
  <li>Was rightfully known to the Receiving Party prior to disclosure;</li>
  <li>Is independently developed by the Receiving Party without use of Confidential Information;</li>
  <li>Must be disclosed by law or court order (with prior written notice to OCSAPP where legally permitted).</li>
</ul>

<h3>3. Obligations of the Receiving Party</h3>
<p>The Receiving Party agrees to:</p>
<ul>
  <li>Hold all Confidential Information in strict confidence using at least the same degree of care used for their own confidential information (and no less than reasonable care).</li>
  <li>Not disclose Confidential Information to any third party without OCSAPP's prior written consent.</li>
  <li>Use Confidential Information solely for the purpose of fulfilling their role with OCSAPP.</li>
  <li>Limit access to Confidential Information to those with a need-to-know basis.</li>
  <li>Promptly notify OCSAPP upon becoming aware of any unauthorized disclosure or breach.</li>
</ul>

<h3>4. Permitted Disclosures</h3>
<p>The Receiving Party may disclose Confidential Information to their employees or contractors on a strict need-to-know basis, provided such persons are bound by confidentiality obligations at least as protective as those in this Agreement.</p>

<h3>5. Duration</h3>
<ul>
  <li>This Agreement remains in effect for the duration of the Receiving Party's engagement with OCSAPP.</li>
  <li>Confidentiality obligations survive termination of engagement for a period of <strong>3 years</strong> from the date of disclosure of each piece of Confidential Information.</li>
  <li>Obligations relating to trade secrets survive indefinitely.</li>
</ul>

<h3>6. Return or Destruction of Information</h3>
<p>Upon request by OCSAPP or upon termination of engagement, the Receiving Party shall promptly return or destroy all Confidential Information in their possession, including all copies and derivatives, and certify such destruction in writing if requested.</p>

<h3>7. Remedy for Breach</h3>
<p>The Receiving Party acknowledges that any breach of this Agreement may cause OCSAPP irreparable harm for which monetary damages would be an inadequate remedy. Accordingly, OCSAPP is entitled to seek injunctive or other equitable relief in addition to any other remedies available at law or equity.</p>

<h3>8. No Licence</h3>
<p>Nothing in this Agreement grants the Receiving Party any rights, by licence or otherwise, in or to any Confidential Information, intellectual property, or proprietary rights of OCSAPP.</p>

<h3>9. Governing Law</h3>
<p>This Agreement is governed by the laws of the Province of Quebec and applicable federal laws of Canada. Disputes will be resolved in the courts of Quebec.</p>

<h3>10. Contact</h3>
<p>For questions regarding this Agreement: <strong>legal@ocsapp.ca</strong></p>
HTML);

echo "Inserting NDA (FR)...\n";
insertAgreement($db, 'nda', 'fr', 'Accord de non-divulgation (NDA)', <<<HTML
<h2>Accord de non-divulgation</h2>
<p><strong>Dernière mise à jour : 1er mars 2026</strong></p>

<p>Le présent accord de non-divulgation (« AND » ou « Accord ») est conclu entre <strong>OCSAPP INC.</strong> (« Partie divulgatrice ») et la personne physique ou morale (« Partie destinataire ») à qui l'accès à des informations confidentielles est accordé aux fins d'évaluation ou d'exécution d'une relation d'affaires avec OCSAPP. En acceptant un rôle de membre du personnel, d'entrepreneur ou de partenaire, vous acceptez les termes du présent Accord.</p>

<h3>1. Définition des informations confidentielles</h3>
<p>Les « informations confidentielles » désignent toute information non publique communiquée par OCSAPP à la Partie destinataire, oralement, par écrit ou électroniquement, qui est désignée comme confidentielle ou qui devrait raisonnablement être comprise comme telle compte tenu de la nature de l'information et des circonstances de divulgation. Cela inclut notamment :</p>
<ul>
  <li>Plans d'affaires, projections financières et stratégies de tarification</li>
  <li>Code source, architecture technique et documentation des systèmes</li>
  <li>Données et listes de clients, fournisseurs et partenaires</li>
  <li>Campagnes marketing, feuilles de route de produits et fonctionnalités non publiées</li>
  <li>Processus internes, procédures opérationnelles et secrets commerciaux</li>
  <li>Détails de rémunération des employés et entrepreneurs</li>
</ul>

<h3>2. Exclusions des informations confidentielles</h3>
<p>Les informations confidentielles n'incluent pas les informations qui :</p>
<ul>
  <li>Sont ou deviennent publiques sans manquement au présent Accord ;</li>
  <li>Étaient légitimement connues de la Partie destinataire avant la divulgation ;</li>
  <li>Sont développées indépendamment par la Partie destinataire sans utilisation des informations confidentielles ;</li>
  <li>Doivent être divulguées par la loi ou ordonnance judiciaire (avec préavis écrit préalable à OCSAPP dans la mesure légalement permise).</li>
</ul>

<h3>3. Obligations de la Partie destinataire</h3>
<p>La Partie destinataire s'engage à :</p>
<ul>
  <li>Traiter toutes les informations confidentielles avec la même diligence qu'elle applique à ses propres informations confidentielles (et au moins avec un soin raisonnable).</li>
  <li>Ne pas divulguer les informations confidentielles à des tiers sans le consentement écrit préalable d'OCSAPP.</li>
  <li>Utiliser les informations confidentielles uniquement aux fins de l'exercice de son rôle auprès d'OCSAPP.</li>
  <li>Limiter l'accès aux informations confidentielles aux personnes ayant un besoin de savoir.</li>
  <li>Aviser rapidement OCSAPP dès qu'elle a connaissance de toute divulgation non autorisée ou violation.</li>
</ul>

<h3>4. Divulgations autorisées</h3>
<p>La Partie destinataire peut divulguer des informations confidentielles à ses employés ou entrepreneurs sur la base stricte du besoin de savoir, à condition que ces personnes soient liées par des obligations de confidentialité au moins aussi protectrices que celles du présent Accord.</p>

<h3>5. Durée</h3>
<ul>
  <li>Le présent Accord demeure en vigueur pendant toute la durée de l'engagement de la Partie destinataire auprès d'OCSAPP.</li>
  <li>Les obligations de confidentialité survivent à la fin de l'engagement pour une période de <strong>3 ans</strong> à compter de la date de divulgation de chaque information confidentielle.</li>
  <li>Les obligations relatives aux secrets commerciaux survivent indéfiniment.</li>
</ul>

<h3>6. Restitution ou destruction des informations</h3>
<p>Sur demande d'OCSAPP ou à la fin de l'engagement, la Partie destinataire doit promptement retourner ou détruire toutes les informations confidentielles en sa possession, y compris toutes copies et dérivés, et certifier par écrit cette destruction si demandé.</p>

<h3>7. Recours en cas de violation</h3>
<p>La Partie destinataire reconnaît que toute violation du présent Accord peut causer à OCSAPP un préjudice irréparable pour lequel les dommages-intérêts seraient un remède insuffisant. En conséquence, OCSAPP a droit de chercher une injonction ou autre réparation équitable en plus de tout autre recours disponible en droit.</p>

<h3>8. Absence de licence</h3>
<p>Rien dans le présent Accord ne confère à la Partie destinataire de droits, par licence ou autrement, sur les informations confidentielles, la propriété intellectuelle ou les droits de propriété d'OCSAPP.</p>

<h3>9. Droit applicable</h3>
<p>Le présent Accord est régi par les lois de la province de Québec et les lois fédérales applicables du Canada. Les différends seront résolus par les tribunaux du Québec.</p>

<h3>10. Contact</h3>
<p>Pour toute question concernant cet Accord : <strong>legal@ocsapp.ca</strong></p>
HTML);

echo "\nAll done! Summary:\n";
echo "  - ENUM extended with 4 new page types\n";
echo "  - supplier_agreement: EN + FR\n";
echo "  - driver_agreement: EN + FR\n";
echo "  - distribution_agreement: EN + FR\n";
echo "  - nda: EN + FR\n";
echo "\nURLs live at:\n";
echo "  /supplier-agreement\n";
echo "  /driver-agreement\n";
echo "  /distribution-agreement\n";
echo "  /nda\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
