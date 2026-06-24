<?php
/**
 * Seed Driver Training Module Content
 * Populates content_html for all 7 modules and inserts all quiz questions.
 * Safe to re-run: clears existing questions for each module before re-inserting.
 */

require_once __DIR__ . '/../../bootstrap/init.php';
require_once __DIR__ . '/../../config/database.php';

$db = Database::getConnection();

// ─────────────────────────────────────────────────────────────────────────────
// MODULE CONTENT  (order_num => content_html)
// ─────────────────────────────────────────────────────────────────────────────

$moduleContent = [];

$moduleContent[1] = <<<'HTML'
<h2>Module 1: Customer Service</h2>

<p>As an OCSAPP driver, you are often the only face-to-face interaction a customer has with the platform. That means your attitude, appearance, and communication skills directly shape how customers feel about OCSAPP — and whether they order again. This module covers how to present yourself professionally and handle the situations that will inevitably come up on the job.</p>

<h2>First Impressions: Appearance, Punctuality, and Attitude</h2>

<p>You don't need a uniform, but you do need to look presentable. Clean clothing, good personal hygiene, and a calm, friendly manner go a long way. Customers form an impression within seconds of opening the door.</p>

<ul>
  <li><strong>Punctuality:</strong> Arrive within the estimated delivery window shown in the app. If you're running late due to traffic or a prior delay, the customer will see an updated ETA — but that doesn't mean they won't be frustrated. Acknowledge it briefly if they bring it up.</li>
  <li><strong>Attitude:</strong> Smile, make brief eye contact, and be relaxed. You're not a robot dropping off a box — you're a professional delivering a service.</li>
</ul>

<h2>How to Greet a Customer at the Door</h2>

<p>Keep it simple and warm. A good default greeting: <em>"Hi there! I have a delivery for [Name] from OCSAPP."</em> Then hand over the package, confirm signature or photo if required, and wish them a good day. That's it. Don't linger, don't overshare, don't comment on what they ordered.</p>

<blockquote>Tip: If the building has a concierge or buzzer system, use the customer's name exactly as it appears in the app. Nicknames or abbreviations may not be recognised.</blockquote>

<h2>Handling a Damaged or Missing Item Complaint</h2>

<p>If a customer says their order is damaged or an item is missing, <strong>do not argue and do not admit fault</strong>. Your job in that moment is to listen and document.</p>

<ul>
  <li>Acknowledge their concern: <em>"I'm sorry to hear that — let me help you get this sorted."</em></li>
  <li>Direct them to the OCSAPP in-app support chat or help line, where the issue can be reviewed with photos and order records.</li>
  <li>If the damage is visible (e.g., a crushed box you noticed before delivery), photograph it before handoff and flag it in the app immediately.</li>
</ul>

<p>You are not responsible for items damaged before they reached you, but you are responsible for damage caused by careless handling during transport. Treat every package as if it's fragile.</p>

<h2>What to Do if the Customer Is Rude or Aggressive</h2>

<p>It happens. A customer might be upset about a delay, a substitution, or something entirely unrelated to you. Stay calm and professional regardless.</p>

<ul>
  <li>Lower your voice slightly — this often de-escalates tension.</li>
  <li>Use neutral language: <em>"I understand you're frustrated. I want to make sure this gets resolved."</em></li>
  <li><strong>Do not engage in arguments or raise your voice.</strong></li>
  <li>If a customer becomes physically threatening or abusive, step back, end the interaction, and leave the area safely. Your safety comes first.</li>
</ul>

<h2>When to Escalate to OCSAPP Support</h2>

<p>Some situations go beyond what you can handle at the door. Escalate immediately when:</p>

<ul>
  <li>A customer claims they never placed an order (possible fraud)</li>
  <li>You feel unsafe at a delivery location</li>
  <li>The customer demands a refund or replacement on the spot</li>
  <li>There is a dispute about an age-restricted item (see Module 4)</li>
  <li>You've been involved in an incident (accident, theft, injury)</li>
</ul>

<p>Use the in-app support button or call the driver support line. Always document incidents in writing through the app — verbal reports are not sufficient on their own.</p>
HTML;

$moduleContent[2] = <<<'HTML'
<h2>Module 2: App Training — Using the OCSAPP Delivery App (ODA)</h2>

<p>The OCSAPP Delivery App (ODA) is your command centre on every shift. From accepting orders to submitting proof of delivery, everything runs through it. This module walks you through the core functions you'll use every day.</p>

<h2>Logging In and Going Online</h2>

<p>Open the ODA app and sign in with your registered email and password. Once logged in, you'll land on the <strong>Home Dashboard</strong>. To start receiving orders, tap <strong>"Go Online"</strong> in the top right. Your status indicator will turn green.</p>

<ul>
  <li>Make sure your location permissions are enabled — the app uses GPS to match you with nearby orders.</li>
  <li>Going offline mid-shift is allowed, but avoid doing so frequently during peak hours as it may affect your order priority ranking.</li>
  <li>If you need a break, go offline rather than ignoring incoming orders.</li>
</ul>

<h2>Accepting vs. Rejecting an Order</h2>

<p>When a new order comes in, you'll receive a notification with key details: pickup location, drop-off area, estimated payout, and time window. You have a limited window (typically 30 seconds) to accept or reject.</p>

<ul>
  <li><strong>Accept</strong> if the order fits your route and availability.</li>
  <li><strong>Reject</strong> sparingly — excessive rejections (above the platform threshold, shown in your account settings) can lower your priority for future orders and may trigger a review of your account.</li>
  <li>If you're already on a delivery, you may receive a stacked order offer. Only accept if you can complete the current delivery first without delay.</li>
</ul>

<h2>Using In-App Navigation</h2>

<p>The ODA app integrates with your phone's default maps (Google Maps or Apple Maps). Once you accept an order, tap <strong>"Navigate to Pickup"</strong> to get directions to the merchant. After pickup, tap <strong>"Navigate to Customer"</strong>.</p>

<blockquote>Tip: Set your preferred navigation app in ODA Settings before your first shift. This avoids fumbling with it while you're already on the road.</blockquote>

<p>Do not use the phone in your hand while driving — use a mounted device and voice navigation. See Module 6 for distracted driving laws.</p>

<h2>Marking Delivery Stages</h2>

<p>Keeping your status updated is critical. It keeps the customer informed and protects you in disputes. The four main stages are:</p>

<ul>
  <li><strong>Arrived at Pickup:</strong> Tap when you've reached the merchant location.</li>
  <li><strong>Picked Up:</strong> Tap after you've collected the order and confirmed the items with the merchant.</li>
  <li><strong>En Route:</strong> Tap when you're heading to the customer. This triggers the customer's real-time tracking.</li>
  <li><strong>Delivered:</strong> Tap only after the order is in the customer's hands or safely left as per drop instructions.</li>
</ul>

<p><strong>Never mark "Delivered" before completing the delivery.</strong> This is considered fraud and will result in account suspension.</p>

<h2>Submitting Proof of Delivery</h2>

<p>After delivery, the app will prompt you for proof. Depending on the order type:</p>

<ul>
  <li><strong>Photo:</strong> Take a clear photo of the package at the door or in the customer's hands. Ensure the door number or unit is visible where possible.</li>
  <li><strong>Signature:</strong> Required for some order types (e.g., age-restricted items). Have the customer sign directly on your phone screen.</li>
  <li>Both photo and signature may be required for high-value deliveries.</li>
</ul>

<h2>What to Do If the App Crashes Mid-Delivery</h2>

<p>If the app crashes while you're en route:</p>

<ul>
  <li>Close and reopen the app — it will usually restore your active order.</li>
  <li>If the order doesn't restore, contact driver support immediately with your order number (write it down at pickup if possible).</li>
  <li>Complete the delivery if you already have the package — do not leave it unattended. Report the crash through support so your pay is not affected.</li>
  <li>Do not mark any status manually through workarounds — wait for support guidance.</li>
</ul>
HTML;

$moduleContent[3] = <<<'HTML'
<h2>Module 3: On-Road Etiquette</h2>

<p>How you conduct yourself outside the vehicle — parking, entering buildings, taking elevators, and handing off parcels — reflects directly on OCSAPP. This module covers practical etiquette that keeps you safe, keeps customers happy, and keeps you on the right side of building management and local bylaws.</p>

<h2>Where and How to Park During a Delivery</h2>

<p>Quick deliveries don't justify illegal or inconsiderate parking. Follow these rules every time:</p>

<ul>
  <li><strong>Never block fire routes.</strong> This is a ticketable offence across all Canadian provinces and can result in towing. Fire routes are marked with red curbs or posted signage.</li>
  <li><strong>Never park in accessible (handicapped) spots</strong> without a valid permit. Fines are substantial — often $300–$500+ depending on the municipality.</li>
  <li>Use designated loading zones wherever available. Many urban buildings have them specifically for couriers.</li>
  <li>If no legal spot is available within a reasonable distance, circle the block once before double-parking briefly with hazard lights on — and only if local bylaws and building rules permit it.</li>
  <li>In residential areas, pull to the curb properly and do not block driveways.</li>
</ul>

<h2>Entering Apartment Buildings and Condos</h2>

<p>Many of your deliveries will be to multi-unit buildings. Here's how to navigate them smoothly:</p>

<ul>
  <li><strong>Buzzer systems:</strong> Use the customer's name exactly as it appears in the app. If they don't answer, try buzzing once more, then send an in-app message before calling.</li>
  <li><strong>Concierge buildings:</strong> Introduce yourself as a delivery driver for OCSAPP. Show the order reference if asked. Follow their specific procedures — some require you to leave packages at the desk.</li>
  <li><strong>Access codes:</strong> The customer may have provided an entry code in the delivery notes. Use it discreetly and do not share it with anyone.</li>
  <li>Do not tailgate behind residents entering a building — always use the proper access method.</li>
</ul>

<h2>Elevator Etiquette</h2>

<p>In busy buildings, elevators are shared spaces. Basic courtesies matter:</p>

<ul>
  <li>Hold the elevator for others who are approaching — a brief wait is worth the goodwill.</li>
  <li>Do not block the elevator doors with your body or parcels while loading.</li>
  <li>If you have a trolley or cart, allow passengers to exit before you wheel in.</li>
  <li>Face forward, keep your parcels to one side, and avoid taking up excessive space during peak hours.</li>
</ul>

<h2>Handing Off Parcels: To-Hand vs. Door-Drop Rules</h2>

<p>Not all deliveries are the same. The app will specify the delivery method:</p>

<ul>
  <li><strong>To-hand delivery:</strong> The package must be physically handed to the customer (or an adult at the address). Do not leave it at the door if no one answers.</li>
  <li><strong>Door-drop (safe drop):</strong> The customer has pre-authorized leaving the package at the door. Place it neatly, out of plain sight if possible (e.g., beside the door, not in the middle of the hallway), and take a clear proof-of-delivery photo.</li>
  <li>Check the delivery notes every time — customers sometimes leave specific instructions like "leave with concierge" or "ring twice."</li>
</ul>

<h2>Leaving a Delivery Unattended: When It's Allowed and When It's Not</h2>

<p>Unattended delivery (leaving a package without direct handoff) is only permitted when:</p>

<ul>
  <li>The order type in the app is designated as "safe drop"</li>
  <li>The location is reasonably secure (indoors, covered porch, concierge desk)</li>
  <li>You have photographic proof of where it was left</li>
</ul>

<p><strong>Do not leave packages unattended in the following situations:</strong></p>

<ul>
  <li>Age-restricted items (alcohol, cannabis, tobacco) — these always require an in-person handoff and ID check</li>
  <li>High-value items flagged in the app as requiring signature</li>
  <li>Unsecured outdoor locations in bad weather or high foot-traffic areas</li>
</ul>

<blockquote>When in doubt, do not drop. Contact the customer or support before leaving a package somewhere it could be stolen or damaged.</blockquote>
HTML;

$moduleContent[4] = <<<'HTML'
<h2>Module 4: Delivery Laws in Canada</h2>

<p>Delivering in Canada comes with legal obligations that go beyond good customer service. Some deliveries involve regulated products and specific legal requirements. Getting these wrong can result in fines, account suspension, or legal liability. This module covers what you need to know.</p>

<h2>Age-Restricted Deliveries: Alcohol, Tobacco, and Cannabis</h2>

<p>In Canada, the sale and delivery of alcohol, tobacco, and cannabis are provincially regulated. As a delivery driver, you are the last point of control in the supply chain — that makes you responsible for verifying the recipient's age.</p>

<ul>
  <li><strong>Always ask for government-issued photo ID</strong> (driver's licence, passport, provincial ID card) before handing over the item.</li>
  <li>The legal age varies by province: <strong>19+ in most provinces</strong>, <strong>18+ in Alberta, Manitoba, and Quebec</strong> for alcohol.</li>
  <li>If the recipient cannot produce valid ID, <strong>do not complete the delivery.</strong> Mark it as "ID not provided" in the app and follow the return instructions.</li>
  <li>Never accept verbal assurances like "I'm definitely over 19." ID must be physically presented and verified.</li>
  <li>If the person who answers is clearly a minor, do not hand over the item even if they claim the order is for an adult inside.</li>
</ul>

<blockquote>Important: Delivering an age-restricted product to a minor is a violation of provincial liquor and cannabis laws. You can be personally fined and OCSAPP's licence can be affected.</blockquote>

<h2>When a Signature Is Legally Required</h2>

<p>Beyond age-restricted items, signatures are required for:</p>

<ul>
  <li>High-value orders flagged in the app</li>
  <li>Parcels sent via specific carriers that mandate recipient confirmation</li>
  <li>Deliveries where the merchant has specifically requested a signature at checkout</li>
</ul>

<p>The app will always indicate if a signature is required before you accept the order. If a signature is required and the recipient refuses, do not complete the delivery — contact support for guidance.</p>

<h2>What to Do If No One Is Home</h2>

<p>If no one answers after reasonable attempts:</p>

<ul>
  <li><strong>Safe drop orders:</strong> Leave the package as instructed, take a proof photo, and mark delivered.</li>
  <li><strong>To-hand orders:</strong> Do not leave the package. Mark "Attempted Delivery" in the app. The customer will be notified to reschedule or arrange pickup.</li>
  <li><strong>Return-to-sender:</strong> Some orders require you to return the package to the merchant or a depot if delivery cannot be completed. Follow the app instructions.</li>
</ul>

<h2>Privacy Obligations</h2>

<p>Canada's <strong>Personal Information Protection and Electronic Documents Act (PIPEDA)</strong> and provincial privacy laws govern how personal information must be handled. As a driver, your obligations include:</p>

<ul>
  <li><strong>Do not photograph inside a customer's home</strong> — your proof-of-delivery photo should show the package at the threshold, not the interior.</li>
  <li><strong>Do not share or discuss customer information</strong> (name, address, order details) with anyone outside OCSAPP operations.</li>
  <li>Customer delivery addresses must not be saved, screenshotted, or used outside of completing the delivery.</li>
  <li>If a customer asks who gave you their address, the answer is: OCSAPP, as part of the order they placed.</li>
</ul>

<h2>Liability for Lost or Damaged Packages</h2>

<p>As an independent contractor, your liability is limited but real:</p>

<ul>
  <li>If a package is damaged due to <strong>negligent handling</strong> on your part (e.g., throwing it, leaving it in the rain), you may be subject to a chargeback through the platform.</li>
  <li>If a package is lost because you left it in an unauthorised location, you bear responsibility.</li>
  <li>OCSAPP carries insurance for certain losses, but this does not cover driver negligence.</li>
  <li>Always photograph proof of delivery. A timestamped photo at the correct address is your primary protection in a dispute.</li>
</ul>
HTML;

$moduleContent[5] = <<<'HTML'
<h2>Module 5: Safety</h2>

<p>Your safety is the most important part of this job. No delivery is worth an injury, an accident, or putting yourself in a dangerous situation. This module covers the practical safety habits that experienced drivers use every single day.</p>

<h2>Pre-Shift Vehicle Check</h2>

<p>Before you start your shift, take five minutes to check your vehicle. Catching a problem in your driveway is always better than on the highway.</p>

<ul>
  <li><strong>Tires:</strong> Check for visible damage, low pressure, or uneven wear. In winter, confirm you have winter tires installed (required by law in some provinces, including Quebec from December 1 to March 15).</li>
  <li><strong>Lights:</strong> Headlights, brake lights, and signal lights. Have someone walk around the car while you test them, or use a reflective surface.</li>
  <li><strong>Mirrors:</strong> Adjust side and rear-view mirrors before you move.</li>
  <li><strong>Fuel/charge:</strong> Do not start a shift on low fuel or a dying battery (for EVs). Run out mid-delivery and you're responsible for the delay.</li>
  <li><strong>Windshield and wipers:</strong> Clear all snow and ice completely before driving — this is a legal requirement across Canada.</li>
</ul>

<h2>Driving in Canadian Winter Conditions</h2>

<p>Winter driving in Canada is a skill in itself. Overconfidence causes most cold-weather accidents.</p>

<ul>
  <li><strong>Black ice:</strong> Forms invisibly on bridges, overpasses, and shaded road sections. If the road looks shiny but wet, treat it as ice — reduce speed significantly.</li>
  <li><strong>Increase following distance</strong> to at least 4–6 seconds in snowy or icy conditions (double the dry-road standard).</li>
  <li><strong>Reduced visibility:</strong> Use headlights during snowfall even in daytime. In a whiteout, pull off the road safely and wait — it is never worth driving blind.</li>
  <li>Brake gently and early. Slamming brakes on ice causes skidding. If you feel a skid, ease off the brakes and steer gently into the skid.</li>
</ul>

<h2>Safe Lifting and Carrying</h2>

<p>Back injuries are one of the most common workplace injuries for delivery drivers. Use proper technique every time:</p>

<ul>
  <li>Bend at the knees, not the waist. Keep your back straight and lift with your legs.</li>
  <li><strong>Do not lift packages over 23 kg (50 lbs) alone</strong> — ask for assistance or use a hand truck. Many merchants will have one available.</li>
  <li>Carry packages close to your body to reduce strain on your lower back.</li>
  <li>Make multiple trips rather than stacking awkwardly — one injury costs more time than two trips.</li>
</ul>

<h2>Personal Safety on the Job</h2>

<p>Most deliveries are uneventful, but it's smart to be aware of your surroundings.</p>

<ul>
  <li><strong>Unfamiliar buildings:</strong> If an address feels wrong or a building situation seems unsafe (e.g., no lighting, confrontational groups), you are not required to proceed. Contact support and document the concern.</li>
  <li><strong>Aggressive dogs:</strong> Do not enter a property if an unleashed dog is present. Ring from the street or message the customer to secure the dog before you approach.</li>
  <li><strong>Unsafe areas:</strong> Trust your instincts. If an area feels dangerous at night, you can notify support and request a safety check-in protocol.</li>
  <li>Keep your vehicle locked at all times when making deliveries. Packages left in an unlocked car are your responsibility.</li>
</ul>

<h2>What to Do in an Accident or Emergency</h2>

<p>If you are involved in a collision or emergency:</p>

<ul>
  <li><strong>Step 1:</strong> Check yourself and others for injuries. Call 911 if anyone is hurt.</li>
  <li><strong>Step 2:</strong> Move to safety if the vehicle is driveable and blocking traffic. Do not flee the scene.</li>
  <li><strong>Step 3:</strong> Preserve the scene — take photos of all vehicles involved, road conditions, and any damage.</li>
  <li><strong>Step 4:</strong> Exchange information with the other driver (name, contact, insurance, licence plate).</li>
  <li><strong>Step 5:</strong> Report the incident to OCSAPP driver support and your insurance provider as soon as possible. In Ontario and several other provinces, collisions over $2,000 in damage must be reported to police.</li>
</ul>

<blockquote>Do not admit fault at the scene. Simply exchange information, document everything, and let the insurance process run its course.</blockquote>
HTML;

$moduleContent[6] = <<<'HTML'
<h2>Module 6: Driving Laws</h2>

<p>Driving for OCSAPP means operating a vehicle as a professional, not just a commuter. Canadian road laws apply fully to delivery drivers, and ignorance of the rules is never a valid defence. This module covers the key laws you need to know and follow every shift.</p>

<h2>Distracted Driving Laws in Canada</h2>

<p>Canada has some of the strictest distracted driving laws in the world. Every province has its own legislation, but the core rules are consistent:</p>

<ul>
  <li><strong>You cannot hold or use a hand-held device while driving.</strong> This includes texting, calling, scrolling the app, or even glancing at your phone in your hand.</li>
  <li>Fines range from <strong>$300 to $1,000+</strong> depending on the province. In Ontario, a first offence carries a fine of up to $1,000, three demerit points, and a 3-day licence suspension.</li>
  <li>Repeated offences can lead to licence suspension and increased insurance premiums.</li>
  <li><strong>Exception:</strong> You may use a device mounted to the dash or windshield in hands-free mode — but only if the mount is secure and you do not touch the device while driving.</li>
</ul>

<blockquote>Rule of thumb: If it requires your hands or your eyes to leave the road, it's distracted driving.</blockquote>

<h2>Speed Limits in Key Zones</h2>

<p>Speed limits in Canada vary by zone and are strictly enforced:</p>

<ul>
  <li><strong>School zones:</strong> Typically 40 km/h when children are present (signage will specify hours). Fines in school zones are often doubled.</li>
  <li><strong>Construction zones:</strong> Posted limits must be followed — fines in construction zones are also doubled in most provinces, even when workers are not present.</li>
  <li><strong>Residential streets:</strong> Default limit in most municipalities is 40–50 km/h. Some cities (including Toronto and Ottawa) have implemented 30 km/h zones on many residential streets.</li>
  <li>Always look for posted signs — default limits vary by municipality and province.</li>
</ul>

<h2>Right-of-Way Rules at Intersections</h2>

<p>Intersection errors are a leading cause of collisions. Know these rules:</p>

<ul>
  <li><strong>Four-way stops:</strong> The driver who arrives first goes first. If two vehicles arrive simultaneously, the driver on the right has the right of way.</li>
  <li><strong>Uncontrolled intersections:</strong> Yield to the vehicle on your right.</li>
  <li><strong>Turning left:</strong> You must yield to oncoming traffic and pedestrians, even if you have a green light.</li>
  <li><strong>Pedestrians:</strong> Always have the right of way at crosswalks — marked or unmarked. Stop and wait until they have fully crossed your lane.</li>
</ul>

<h2>Using a GPS or Phone Mount Legally</h2>

<p>A mounted device is legal, but there are rules:</p>

<ul>
  <li>The mount must be fixed to the dash, windshield, or vent — not held in your lap or propped against the steering wheel.</li>
  <li>The device must be in a position that does not obstruct your view of the road or mirrors.</li>
  <li>You must not touch or interact with the device while the vehicle is in motion. Set your route before you start driving.</li>
  <li>Voice commands are permitted in most provinces as a hands-free method.</li>
</ul>

<h2>Driver's Obligation to Report Accidents</h2>

<p>As a driver, you have both legal and contractual reporting obligations:</p>

<ul>
  <li><strong>To police:</strong> In most provinces, collisions resulting in injury or damage over $2,000 must be reported to police. In some provinces (e.g., Ontario), you can report at a collision reporting centre if no injuries occurred.</li>
  <li><strong>To OCSAPP:</strong> Any collision or serious incident during an active shift must be reported to driver support immediately — regardless of fault or damage amount.</li>
  <li><strong>To your insurer:</strong> Your personal auto insurance must also be notified promptly. Failure to report can affect your coverage.</li>
</ul>

<p>Failure to remain at the scene of an accident is a criminal offence under the <em>Criminal Code of Canada</em>. Always stop, stay, and report.</p>
HTML;

$moduleContent[7] = <<<'HTML'
<h2>Module 7: Payments &amp; Earnings</h2>

<p>Understanding how you're paid is just as important as understanding how to deliver. This module covers how your earnings are calculated, when you get paid, what can affect your take-home amount, and how to resolve any payment issues.</p>

<h2>How Your Earnings Are Calculated</h2>

<p>Your pay as an OCSAPP driver is made up of three components:</p>

<ul>
  <li><strong>Base pay:</strong> A flat amount per delivery, set by OCSAPP. This covers the basic effort of completing a standard delivery.</li>
  <li><strong>Per-delivery variable:</strong> Additional earnings based on factors like distance, order size, or peak-hour multipliers. Longer or more complex deliveries earn more.</li>
  <li><strong>Tips:</strong> Customers can tip through the app after delivery is confirmed. Tips are paid directly to you with no platform deduction. They typically appear in your earnings 24–48 hours after the delivery.</li>
</ul>

<p>Your gross earnings for each delivery are visible in the order summary after completing it. Your total daily earnings appear on the <strong>Earnings tab</strong> in the ODA app.</p>

<h2>Payment Schedule</h2>

<p>OCSAPP pays on a <strong>weekly cycle</strong>:</p>

<ul>
  <li>The pay period runs from <strong>Monday to Sunday</strong>.</li>
  <li>Earnings are processed on Monday of the following week and deposited within <strong>2–3 business days</strong> via direct deposit.</li>
  <li>You must have a valid Canadian bank account set up in the app under <strong>Settings &gt; Payment Info</strong> before your first shift. Payments cannot be issued to foreign accounts or third-party services.</li>
  <li>You will receive a weekly earnings statement by email summarising completed deliveries, tips, and any adjustments.</li>
</ul>

<h2>What Deductions May Apply</h2>

<p>As an independent contractor, you are responsible for your own taxes — OCSAPP does not deduct income tax from your earnings. However, the following platform-level adjustments may reduce your payout:</p>

<ul>
  <li><strong>Chargebacks for failed deliveries:</strong> If a delivery is marked complete but the customer provides evidence it was not delivered (and OCSAPP confirms), the base pay for that delivery may be reversed.</li>
  <li><strong>Damage chargebacks:</strong> If you are found responsible for a damaged package, the cost of the claim may be deducted from future earnings.</li>
  <li><strong>Fraud reversals:</strong> Any pay associated with fraudulent activity (e.g., fake deliveries) will be clawed back.</li>
</ul>

<p><em>Note: Since you are an independent contractor, you are responsible for filing your own taxes. Keep records of your earnings — OCSAPP will provide a T4A (or equivalent) at year end, but your mileage, phone costs, and other eligible expenses may be deductible. Consider speaking with a tax professional.</em></p>

<h2>How to View Your Earnings in the App</h2>

<p>To see your earnings at any time:</p>

<ul>
  <li>Open the ODA app and tap the <strong>Earnings</strong> icon in the bottom navigation bar.</li>
  <li>Select <strong>This Week</strong>, <strong>Last Week</strong>, or a custom date range.</li>
  <li>Tap any individual delivery to see the breakdown: base pay, variable, and tip.</li>
  <li>Weekly statements are also emailed every Monday morning.</li>
</ul>

<h2>How to Dispute a Payment or Flag a Missing Payout</h2>

<p>If you believe a payment is incorrect or missing, here is the process:</p>

<ul>
  <li><strong>Step 1:</strong> Check the Earnings tab in the app to confirm the delivery was recorded correctly.</li>
  <li><strong>Step 2:</strong> If the delivery shows as completed but earnings are missing or incorrect, submit a dispute through <strong>Settings &gt; Help &gt; Payment Dispute</strong>, including the date, order number, and the amount you expected.</li>
  <li><strong>Step 3:</strong> OCSAPP support will review your dispute within 3–5 business days and respond by email.</li>
  <li><strong>Step 4:</strong> If the dispute is resolved in your favour, the corrected amount will be included in your next weekly payout.</li>
</ul>

<p><strong>Important:</strong> Disputes must be submitted within <strong>14 days</strong> of the pay period in question. Submissions outside this window may not be reviewed. Check your earnings statement every week — don't let issues stack up.</p>

<blockquote>You are running a small business. Treat your earnings records the same way — check them weekly, keep your own log of deliveries, and flag anything that looks off right away.</blockquote>
HTML;


// ─────────────────────────────────────────────────────────────────────────────
// QUIZ QUESTIONS  (order_num => array of questions)
// Each question: [text, a, b, c, d, correct (a/b/c/d), explanation]
// ─────────────────────────────────────────────────────────────────────────────

$moduleQuestions = [];

$moduleQuestions[1] = [
    [
        'text'        => 'A customer opens the door and immediately says their order is wrong — they ordered a large, not a medium. What should you do first?',
        'a'           => 'Offer to go back to the shop and swap it yourself',
        'b'           => 'Apologize, acknowledge their concern, and direct them to OCSAPP support through the app',
        'c'           => 'Tell them to check their order confirmation before complaining',
        'd'           => 'Give them a partial refund from your own tips',
        'correct'     => 'b',
        'explanation' => 'Drivers cannot correct order errors on the spot or issue refunds — only OCSAPP support can do that. Acknowledging the issue and directing the customer to support is the correct and professional response. Option A creates liability, C is dismissive, and D is not within your authority.',
    ],
    [
        'text'        => 'You arrive 20 minutes late to a delivery. The customer seems annoyed. What is the best approach?',
        'a'           => 'Explain in detail everything that caused the delay',
        'b'           => "Don't mention the delay — just hand over the package quickly",
        'c'           => 'Briefly acknowledge the wait with a friendly apology and complete the delivery professionally',
        'd'           => 'Tell the customer to contact OCSAPP if they have a problem',
        'correct'     => 'c',
        'explanation' => "A brief, sincere acknowledgment is the professional response — it shows you're aware and care about the customer's experience. Over-explaining (A) wastes their time, ignoring it (B) can seem dismissive, and deflecting (D) is unhelpful when you're standing right there.",
    ],
    [
        'text'        => 'A customer at the door becomes verbally aggressive and starts swearing at you about a delay. What should you do?',
        'a'           => 'Swear back to stand your ground',
        'b'           => 'Walk away immediately without completing the delivery',
        'c'           => 'Stay calm, use neutral language, and if the situation escalates to physical threats, leave safely and report it',
        'd'           => 'Call the police immediately for verbal aggression',
        'correct'     => 'c',
        'explanation' => 'Verbal aggression, while unpleasant, requires de-escalation first. Only if there is a physical threat should you leave. Walking away immediately (B) abandons the delivery unnecessarily; calling police for verbal frustration (D) is disproportionate; engaging in kind (A) will always make things worse.',
    ],
    [
        'text'        => 'Which of the following situations requires you to escalate to OCSAPP support?',
        'a'           => "A customer doesn't say thank you",
        'b'           => 'A customer seems surprised the order arrived so quickly',
        'c'           => 'A customer claims they never placed the order you are delivering',
        'd'           => 'A customer asks how much tip you received',
        'correct'     => 'c',
        'explanation' => "A customer denying they placed an order may indicate fraud, a security issue, or an app error — all of which OCSAPP support needs to investigate. The other scenarios are ordinary interactions that don't require escalation.",
    ],
    [
        'text'        => "Before knocking on a customer's door, you notice the package has a crushed corner and something is rattling inside. What is the correct action?",
        'a'           => "Deliver it anyway — it's not your problem since it was packed by the shop",
        'b'           => 'Photograph the damage, flag it in the app before delivery, then inform the customer at the door',
        'c'           => 'Return the package to the shop without notifying the customer',
        'd'           => 'Throw away the package and mark it as delivered',
        'correct'     => 'b',
        'explanation' => 'Photographing and flagging damage before delivery protects you from being blamed for it later, and informing the customer is transparent and professional. Returning without notification (C) creates confusion; option D is fraud; ignoring it (A) leaves you potentially liable.',
    ],
];

$moduleQuestions[2] = [
    [
        'text'        => "You've accepted an order and are halfway to the customer when the ODA app crashes and won't reopen. What should you do?",
        'a'           => 'Abandon the delivery and go home',
        'b'           => 'Leave the package at the nearest safe location and take a photo',
        'c'           => 'Continue to the customer, complete the delivery, then immediately contact driver support with your order number',
        'd'           => 'Mark the order as delivered on a second device to fix the record',
        'correct'     => 'c',
        'explanation' => 'Your priority is completing the delivery — the customer still needs their order. Driver support can correct the records if you report the crash promptly. Abandoning the delivery (A) harms the customer; leaving it unattended (B) may not be permitted; using a second device to alter records (D) could be flagged as fraudulent.',
    ],
    [
        'text'        => 'What happens if you regularly reject more orders than the platform threshold allows?',
        'a'           => "Nothing — you're an independent contractor and can reject freely",
        'b'           => 'Your account may be flagged for review and your order priority may decrease',
        'c'           => 'You will receive a warning by email but no other consequences',
        'd'           => 'You will be immediately deactivated',
        'correct'     => 'b',
        'explanation' => 'While drivers can reject orders, excessive rejections signal low availability and reliability. This can reduce your priority in the order matching queue and trigger an account review. Immediate deactivation (D) is unlikely for rejections alone, but consistent patterns are monitored.',
    ],
    [
        'text'        => 'At what stage should you tap "Delivered" in the app?',
        'a'           => "As soon as you park outside the customer's building",
        'b'           => "Once you're on your way to the customer",
        'c'           => "Only after the package is in the customer's hands or safely left per drop instructions",
        'd'           => 'Right after you pick up the order from the shop, to save time later',
        'correct'     => 'c',
        'explanation' => '"Delivered" must only be marked when the delivery is genuinely complete. Marking it early (A, B, or D) is considered fraudulent and can lead to account suspension, regardless of intent.',
    ],
    [
        'text'        => "A customer's order requires both a photo and a signature as proof of delivery. You take the photo but the customer says they don't want to sign. What do you do?",
        'a'           => "Mark it delivered with just the photo — it's close enough",
        'b'           => 'Do not complete the delivery; return the package to the shop',
        'c'           => 'Contact OCSAPP support through the app to get guidance before proceeding',
        'd'           => 'Ask the customer to write their name on a piece of paper instead',
        'correct'     => 'c',
        'explanation' => 'When required proof cannot be collected, contacting support is the correct step — they can advise on whether the delivery can proceed and how to document the refusal. Marking it delivered without full compliance (A) creates a record gap; returning without guidance (B) may not be required; a paper note (D) is not an accepted substitute.',
    ],
    [
        'text'        => 'Before your first shift, what should you do in ODA Settings?',
        'a'           => 'Turn off GPS to preserve battery life',
        'b'           => 'Set your preferred navigation app so it launches automatically with orders',
        'c'           => "Set your status to Offline so you don't get orders before you're ready",
        'd'           => 'Delete your saved payment info for security',
        'correct'     => 'b',
        'explanation' => 'Setting your navigation preference ahead of time means directions launch instantly when you accept an order — no fumbling mid-route. GPS must remain on (A is wrong), going offline is fine but unrelated to settings prep (C), and payment info is unrelated to navigation (D).',
    ],
];

$moduleQuestions[3] = [
    [
        'text'        => "You arrive at a condo building for a delivery and the only open spot near the entrance is a designated accessible parking space. You'll only be 3 minutes. What should you do?",
        'a'           => 'Park there briefly with your hazard lights on — 3 minutes is fine',
        'b'           => 'Find a legal parking spot, even if it means a slightly longer walk',
        'c'           => "Park there and leave a note on your dashboard explaining you're a delivery driver",
        'd'           => 'Double-park in the lane with hazards on while you run in',
        'correct'     => 'b',
        'explanation' => 'Accessible parking spaces are reserved by law for people with valid permits at all times — there are no exceptions for brief delivery stops. Notes and hazard lights (A, C) do not make it legal. Double-parking (D) may be acceptable in some situations per local bylaws but is not the first choice here — a legal spot is always the correct answer.',
    ],
    [
        'text'        => "You buzz a customer's apartment twice with no answer. According to on-road etiquette, what should you do next?",
        'a'           => 'Leave the package in the lobby and mark it delivered',
        'b'           => 'Send an in-app message to the customer before calling',
        'c'           => 'Go back to the shop and return the order',
        'd'           => "Knock on a neighbour's door to ask if they can take it",
        'correct'     => 'b',
        'explanation' => 'The correct escalation after two buzzer attempts is to send an in-app message, then call if there\'s still no response. Leaving it in the lobby (A) may not be safe or authorised; returning without attempting contact (C) is premature; involving neighbours (D) creates privacy and liability issues.',
    ],
    [
        'text'        => 'A customer\'s delivery notes say "leave at door." You notice the hallway is busy and the door is right beside a stairwell. What is the best approach?',
        'a'           => 'Leave it right in front of the door as instructed and take a photo',
        'b'           => 'Place it to the side of the door, out of direct traffic flow, and take a photo',
        'c'           => 'Return the package — the location is too risky',
        'd'           => 'Give it to a neighbour and note their unit number in the app',
        'correct'     => 'b',
        'explanation' => 'Safe drop means finding a reasonably secure spot within the spirit of the instruction. Placing it out of direct foot traffic is a professional judgment call that protects the customer\'s package. Option A follows instructions literally but doesn\'t protect the package; C and D are unnecessary escalations.',
    ],
    [
        'text'        => 'Which of the following items can NEVER be left unattended as a door-drop?',
        'a'           => 'A non-perishable grocery order',
        'b'           => 'A small electronics package with a safe-drop designation',
        'c'           => 'A cannabis delivery',
        'd'           => 'A clothing order from an online retailer',
        'correct'     => 'c',
        'explanation' => 'Age-restricted items — cannabis, alcohol, and tobacco — always require an in-person handoff and ID verification under Canadian law. They cannot be left unattended regardless of customer instructions or app settings. The other options may be safe-dropped if designated as such.',
    ],
    [
        'text'        => "You're entering a condo building and a resident is holding the door open for you to follow them in without buzzing. What should you do?",
        'a'           => 'Walk through — it saves time and the resident seems friendly',
        'b'           => 'Politely decline the hold and use the proper buzzer or access method',
        'c'           => 'Walk through, but only if you are carrying multiple packages',
        'd'           => 'Ask the resident to buzz you up to the floor you need',
        'correct'     => 'b',
        'explanation' => "Tailgating (following someone through a secured entrance without using proper access) is a security violation, even when the resident is helpful. Building managers and security take this seriously. Using the proper buzzer or access code protects both you and the residents, and keeps OCSAPP's relationship with buildings in good standing.",
    ],
];

$moduleQuestions[4] = [
    [
        'text'        => 'You arrive to deliver a cannabis order. The person who answers appears to be around 16 years old. They say, "It\'s for my dad — he\'s upstairs." What do you do?',
        'a'           => 'Hand it over since the adult placed the order',
        'b'           => 'Ask the dad to come to the door before handing it over',
        'c'           => 'Do not complete the delivery — mark "ID not provided" and follow return instructions',
        'd'           => 'Leave it with the building concierge instead',
        'correct'     => 'c',
        'explanation' => 'You must hand age-restricted items directly to a verified adult. If a minor answers the door, you cannot complete the delivery — even if an adult is supposedly nearby. The correct process is to mark it and follow return instructions, not improvise alternatives.',
    ],
    [
        'text'        => "A customer for a to-hand delivery doesn't answer after two buzzes and a phone call. What is the correct next step?",
        'a'           => 'Leave it at the door and take a photo',
        'b'           => "Give it to the neighbour who offers to take it",
        'c'           => 'Mark "Attempted Delivery" in the app and follow the return instructions',
        'd'           => 'Try again in 30 minutes before giving up',
        'correct'     => 'c',
        'explanation' => "To-hand orders cannot be left unattended. If the customer cannot be reached after reasonable attempts, mark the attempt and follow the app's return or rescheduling workflow. Option A is only valid for safe-drop orders; giving to a neighbour (B) creates privacy and liability issues; waiting 30 minutes (D) is not standard practice.",
    ],
    [
        'text'        => "While taking your proof-of-delivery photo, you accidentally capture the inside of the customer's apartment through the open door. What should you do?",
        'a'           => "Use the photo as-is — it still shows the package",
        'b'           => 'Retake the photo showing only the package at the threshold, without interior visible',
        'c'           => 'Crop the photo on your phone before uploading',
        'd'           => "It doesn't matter — proof photos aren't covered by privacy law",
        'correct'     => 'b',
        'explanation' => "Canadian privacy law and OCSAPP policy require that you do not photograph inside a customer's home. Retaking the photo correctly is the right solution. Cropping (C) might seem equivalent but retaking ensures the correct framing from the start. Interior photos (A, D) are a privacy violation regardless of intent.",
    ],
    [
        'text'        => 'You deliver a high-value electronics order. The recipient refuses to sign. What should you do?',
        'a'           => 'Leave the package anyway and take a photo as your proof',
        'b'           => 'Have the recipient initial a piece of paper instead',
        'c'           => 'Contact OCSAPP support for guidance before completing or abandoning the delivery',
        'd'           => 'Deliver it and note in the app that the customer refused to sign',
        'correct'     => 'c',
        'explanation' => "If a legally required signature is refused, you cannot unilaterally decide to proceed or abandon — support needs to advise you. Simply proceeding (A, D) leaves you exposed in a dispute; improvised alternatives (B) are not accepted as valid proof.",
    ],
    [
        'text'        => "Under Canadian privacy law, which of the following is a driver's obligation?",
        'a'           => "Memorise the customer's address so you can redeliver without checking the app",
        'b'           => 'Share the customer\'s delivery info with a friend who also does deliveries in the area',
        'c'           => "Ensure proof-of-delivery photos do not capture the interior of the customer's home",
        'd'           => 'Store customer phone numbers in your personal contacts for future communication',
        'correct'     => 'c',
        'explanation' => 'PIPEDA and OCSAPP policy both require that customer information is not shared, stored beyond its purpose, or used in ways the customer did not consent to. Proof photos must not show inside a home. Options A, B, and D all involve retaining or misusing customer information, which is a privacy violation.',
    ],
];

$moduleQuestions[5] = [
    [
        'text'        => "It's February and you start your shift to find your rear window has frost on it. What is the correct action?",
        'a'           => 'Drive slowly until it clears on its own with the defroster',
        'b'           => 'Clear all frost and ice completely before moving the vehicle',
        'c'           => 'Cover the rear window with a towel to reduce glare',
        'd'           => "Only clear the driver's side window — that's the legal minimum",
        'correct'     => 'b',
        'explanation' => "Canadian law requires a full, unobstructed view before driving. Driving with any obscured windows is illegal and dangerous. Relying on the defroster while moving (A) and partial clearing (D) are both legal violations; a towel (C) makes things worse.",
    ],
    [
        'text'        => "You're driving in winter and your car begins to skid on an icy patch. What is the correct response?",
        'a'           => 'Apply the brakes firmly to stop as quickly as possible',
        'b'           => 'Ease off the brakes and steer gently in the direction of the skid',
        'c'           => 'Accelerate to pull out of the skid',
        'd'           => 'Pull the handbrake to stop the rear wheels',
        'correct'     => 'b',
        'explanation' => 'Slamming brakes on ice worsens a skid. The correct technique is to release brake pressure and steer gently into the skid to regain traction. Accelerating (C) or using the handbrake (D) increases the risk of losing control entirely.',
    ],
    [
        'text'        => 'A package at the merchant weighs approximately 25 kg and is bulky. The app shows it is a standard delivery. What should you do?',
        'a'           => 'Lift it yourself — 25 kg is manageable with effort',
        'b'           => 'Refuse the delivery entirely and report the merchant',
        'c'           => 'Ask the merchant for assistance or use a hand truck, as the weight exceeds the solo lift guideline',
        'd'           => 'Drag it to your car rather than lifting',
        'correct'     => 'c',
        'explanation' => 'The solo lift guideline is 23 kg (50 lbs). At 25 kg, you should ask for help or use available equipment. Forcing the lift (A) risks injury; refusing the order (B) is an overreaction; dragging (D) risks damaging the package and causing injury.',
    ],
    [
        'text'        => 'You arrive at a residential address and an unleashed German Shepherd is in the front yard. What should you do?',
        'a'           => 'Walk quickly and confidently — dogs usually back down',
        'b'           => 'Ring the doorbell from the property entrance and hope the customer sees you',
        'c'           => 'Do not enter the property; message or call the customer to secure the dog first',
        'd'           => 'Leave the package at the end of the driveway and mark it delivered',
        'correct'     => 'c',
        'explanation' => 'You are never required to approach an unleashed dog. Messaging or calling the customer to secure the animal is the safe and professional approach. Walking in confidently (A) is a common myth about dog behaviour and can trigger an attack. Leaving the package unattended at the driveway (D) may not be authorised for this order type.',
    ],
    [
        'text'        => 'You are in a minor collision during your shift. No one is injured. What is your first step after moving to safety?',
        'a'           => 'Call OCSAPP support immediately to report the collision',
        'b'           => "Drive away quickly if there's no serious damage",
        'c'           => 'Check yourself and others for injuries, then document the scene with photos',
        'd'           => 'Admit responsibility to the other driver to speed up the insurance process',
        'correct'     => 'c',
        'explanation' => 'After ensuring safety, documenting the scene is critical. Photos of damage, road conditions, and vehicle positions protect you. Calling OCSAPP (A) is important but comes after immediate safety and documentation steps. Leaving the scene (B) is illegal; admitting fault (D) can jeopardise your insurance claim.',
    ],
];

$moduleQuestions[6] = [
    [
        'text'        => "You're driving and the ODA app sends you a new order notification. You pick up your phone to check it. Is this legal?",
        'a'           => 'Yes, as long as you just glance at it quickly',
        'b'           => "Yes, if you're stopped at a red light",
        'c'           => 'No — holding or using a hand-held device while driving is illegal in all Canadian provinces',
        'd'           => 'Yes, as long as you keep one hand on the wheel',
        'correct'     => 'c',
        'explanation' => 'Canadian distracted driving laws prohibit holding or using a device while driving — this includes at red lights in most provinces. The only legal option is a hands-free mounted device. Options A, B, and D all involve hand-held use, which is illegal.',
    ],
    [
        'text'        => "You're driving through a residential neighbourhood in Toronto and there's a posted 30 km/h sign. The street looks clear and it's late at night. What speed should you travel?",
        'a'           => '50 km/h — the standard Ontario residential limit',
        'b'           => '40 km/h — a compromise between the sign and standard limits',
        'c'           => '30 km/h — the posted limit applies regardless of conditions or time of day',
        'd'           => 'Whatever speed feels safe to you personally',
        'correct'     => 'c',
        'explanation' => 'Posted speed limits are legally binding regardless of conditions, traffic, or time of day. Many Toronto residential streets now have 30 km/h zones. Exceeding a posted limit — even by a small margin — is a traffic offence.',
    ],
    [
        'text'        => 'You arrive at a four-way stop at the same time as a driver to your left. Who goes first?',
        'a'           => 'You do, because you are on the more important road',
        'b'           => 'The other driver does, because they are to your left',
        'c'           => 'You do, because the driver on the right has the right of way',
        'd'           => 'Whoever flashes their lights first',
        'correct'     => 'c',
        'explanation' => 'At a four-way stop, when two vehicles arrive simultaneously, the driver on the right has the right of way. The other driver is to your left, which means you are to their right — so you go first. Flashing lights (D) is not a legally recognised right-of-way signal.',
    ],
    [
        'text'        => "You've mounted your phone on the dash using the ODA navigation. While driving, you want to update the destination. What should you do?",
        'a'           => 'Quickly tap the screen while keeping your eyes on the road',
        'b'           => 'Pull over safely and update the destination before continuing',
        'c'           => 'Ask a passenger to update it for you',
        'd'           => 'Update it at the next red light',
        'correct'     => 'b',
        'explanation' => 'You must not interact with a mounted device while the vehicle is in motion. The safe and legal option is to pull over. A passenger updating it (C) is acceptable if one is present, but B is the universally correct answer. Red lights (D) count as "driving" under most provincial distracted driving laws.',
    ],
    [
        'text'        => "You're involved in a minor collision at a parking lot with no injuries, and you estimate the damage at around $2,500. What are your reporting obligations?",
        'a'           => 'No reporting required since there are no injuries',
        'b'           => "Only report to OCSAPP — police don't need to know about parking lot incidents",
        'c'           => 'Report to police (or a collision reporting centre), OCSAPP, and your insurance provider',
        'd'           => 'Only report to your insurance provider once you have assessed the damage at home',
        'correct'     => 'c',
        'explanation' => 'Damage over $2,000 typically requires reporting to police or a collision reporting centre in most provinces, regardless of location. OCSAPP must be notified for any on-shift incident, and your insurer must be contacted promptly. Failing any of these steps can have legal and coverage consequences.',
    ],
];

$moduleQuestions[7] = [
    [
        'text'        => 'Your base pay for a delivery is $4.50. The customer also tips $3.00 through the app after you deliver. When will the tip appear in your earnings?',
        'a'           => 'Immediately when you mark the order delivered',
        'b'           => 'In your next weekly direct deposit only',
        'c'           => 'Typically within 24–48 hours after delivery confirmation',
        'd'           => 'Tips are split 50/50 between you and OCSAPP',
        'correct'     => 'c',
        'explanation' => 'Tips are paid directly to you with no platform deduction and typically appear 24–48 hours after delivery confirmation. They are not split with OCSAPP (D is false), do not appear instantly (A), and are not withheld until the weekly deposit (B).',
    ],
    [
        'text'        => 'When does OCSAPP process driver earnings and when can you expect the deposit?',
        'a'           => 'Daily — earnings from the previous day are deposited each morning',
        'b'           => 'Monday to Sunday pay period, processed the following Monday, deposited within 2–3 business days',
        'c'           => 'Bi-weekly — every second Friday',
        'd'           => 'Monthly — on the last business day of each month',
        'correct'     => 'b',
        'explanation' => 'OCSAPP operates on a weekly pay cycle: Monday–Sunday pay period, processed the following Monday, with direct deposit arriving within 2–3 business days. Daily, bi-weekly, and monthly schedules are incorrect.',
    ],
    [
        'text'        => 'A customer claims their package was never delivered, but you have a timestamped proof-of-delivery photo at their door. OCSAPP reviews the claim and finds the customer is wrong. What happens to your pay?',
        'a'           => 'Your pay is reversed pending a longer investigation',
        'b'           => 'You lose the base pay but keep the tip',
        'c'           => 'Your pay is protected — the proof-of-delivery photo supports your record',
        'd'           => 'You must resubmit your banking information to unlock the payment',
        'correct'     => 'c',
        'explanation' => 'A valid timestamped proof-of-delivery photo is your primary protection against false claims. If it supports your delivery, OCSAPP will not reverse your earnings. This is why photographing every delivery correctly is critical.',
    ],
    [
        'text'        => 'You completed 12 deliveries last Tuesday but only 11 appear in the Earnings tab. What should you do first?',
        'a'           => 'Call your bank to investigate the missing payment',
        'b'           => 'Wait until next month — the system sometimes catches up late',
        'c'           => 'Verify the delivery is missing in the Earnings tab, then submit a payment dispute through the app',
        'd'           => 'Ask another driver if they had the same issue',
        'correct'     => 'c',
        'explanation' => 'The first step is always to confirm the gap in your own Earnings tab, then submit a dispute promptly. Calling your bank (A) is irrelevant at this stage; waiting a month (B) would likely miss the 14-day dispute window; other drivers (D) cannot resolve your account issue.',
    ],
    [
        'text'        => 'What is the deadline for submitting a payment dispute for a missing or incorrect payout?',
        'a'           => '7 days from the delivery date',
        'b'           => '30 days from the pay period',
        'c'           => '14 days from the pay period in question',
        'd'           => 'There is no deadline — disputes can be submitted at any time',
        'correct'     => 'c',
        'explanation' => 'Disputes must be submitted within 14 days of the relevant pay period. Missing this window may mean the issue cannot be reviewed. Check your weekly earnings statement and flag anything off right away — do not let problems accumulate.',
    ],
];


// ─────────────────────────────────────────────────────────────────────────────
// IMPORT
// ─────────────────────────────────────────────────────────────────────────────

$updateHtml  = $db->prepare("UPDATE training_modules SET content_html = ? WHERE order_num = ? AND is_active = 1");
$getModuleId = $db->prepare("SELECT id FROM training_modules WHERE order_num = ? AND is_active = 1 LIMIT 1");
$deleteQs    = $db->prepare("DELETE FROM training_questions WHERE module_id = ?");
$insertQ     = $db->prepare("
    INSERT INTO training_questions (module_id, question_text, option_a, option_b, option_c, option_d, correct_option, explanation, order_num)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$totalModules    = 0;
$totalQuestions  = 0;

foreach ($moduleContent as $orderNum => $html) {
    // Update module HTML
    $updateHtml->execute([$html, $orderNum]);
    $affected = $updateHtml->rowCount();

    // Get module id
    $getModuleId->execute([$orderNum]);
    $moduleId = $getModuleId->fetchColumn();

    if (!$moduleId) {
        echo "  WARNING: Module with order_num={$orderNum} not found — skipping.\n";
        continue;
    }

    echo "  Module {$orderNum}: content_html updated (rows affected: {$affected}), module_id={$moduleId}\n";
    $totalModules++;

    // Clear existing questions
    $deleteQs->execute([$moduleId]);

    // Insert questions
    $questions = $moduleQuestions[$orderNum] ?? [];
    foreach ($questions as $i => $q) {
        $insertQ->execute([
            $moduleId,
            $q['text'],
            $q['a'],
            $q['b'],
            $q['c'],
            $q['d'],
            $q['correct'],
            $q['explanation'],
            $i + 1,
        ]);
        $totalQuestions++;
    }
    echo "    Inserted " . count($questions) . " questions.\n";
}

echo "\nDone! {$totalModules} modules updated, {$totalQuestions} questions inserted.\n";
