<?php
/**
 * Redesigned site content — HMIS-first Integral.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function integral_nav_tree() {
	return array(
		array(
			'label'    => 'HMIS',
			'url'      => home_url( '/softwares/' ),
			'children' => array(
				array( 'label' => 'Overview', 'url' => home_url( '/softwares/' ) ),
				array( 'label' => 'Modules', 'url' => home_url( '/softwares/#modules' ) ),
				array( 'label' => 'Integrations', 'url' => home_url( '/softwares/#integrations' ) ),
				array( 'label' => 'Find your facility', 'url' => home_url( '/softwares/#facility' ) ),
				array( 'label' => 'Request demo', 'url' => home_url( '/service-request-inquiry/' ) ),
			),
		),
		array(
			'label'    => 'Company',
			'url'      => home_url( '/about-us/' ),
			'children' => array(
				array( 'label' => 'Who We Are', 'url' => home_url( '/about-us/' ) ),
				array( 'label' => 'Methodology', 'url' => home_url( '/our-methodology/' ) ),
				array( 'label' => 'Business Model', 'url' => home_url( '/our-business-model/' ) ),
				array( 'label' => 'People', 'url' => home_url( '/our-people/' ) ),
				array( 'label' => 'Partners', 'url' => home_url( '/our-partners/' ) ),
				array( 'label' => 'Certifications', 'url' => home_url( '/certifications/' ) ),
				array( 'label' => 'Careers', 'url' => home_url( '/careers/' ) ),
			),
		),
		array(
			'label'    => 'Services',
			'url'      => home_url( '/services/' ),
			'children' => array(
				array( 'label' => 'All Services', 'url' => home_url( '/services/' ) ),
				array( 'label' => 'Build', 'url' => home_url( '/services/#build' ) ),
				array( 'label' => 'Operate', 'url' => home_url( '/services/#operate' ) ),
				array( 'label' => 'Grow', 'url' => home_url( '/services/#grow' ) ),
			),
		),
		array(
			'label' => 'Industries',
			'url'   => home_url( '/industries/' ),
		),
		array(
			'label'  => 'Pricing',
			'url'    => '#',
			'action' => 'demo',
		),
		array(
			'label' => 'Contact',
			'url'   => home_url( '/contacts/' ),
		),
		array(
			'label' => 'Careers',
			'url'   => home_url( '/careers/' ),
		),
		array(
			'label'  => 'Get a Quote',
			'url'    => '#',
			'cta'    => true,
			'action' => 'demo',
		),
	);
}

function integral_hmis_modules() {
	return array(
		array(
			'id'    => 'patients',
			'title' => 'Patient Management',
			'blurb' => 'Registration, encounters, admissions, referrals, and longitudinal records.',
			'panel' => array( 'Active patients', 'OPD queue', 'IPD beds', 'Referrals out' ),
		),
		array(
			'id'    => 'billing',
			'title' => 'Billing & Revenue',
			'blurb' => 'Cash, invoice, deposits, waivers, and clear revenue visibility.',
			'panel' => array( 'Today collections', 'Open invoices', 'Waivers', 'Cash points' ),
		),
		array(
			'id'    => 'claims',
			'title' => 'Insurance Claims',
			'blurb' => 'SHA and commercial claims with eligibility, visit capture, and submission workflows.',
			'panel' => array( 'Pending claims', 'SHA visits', 'Rejected lines', 'Resubmits' ),
		),
		array(
			'id'    => 'procurement',
			'title' => 'Procurement',
			'blurb' => 'Requisitions, POs, receiving, and supplier control for hospital supply chains.',
			'panel' => array( 'Open POs', 'Goods received', 'Stock alerts', 'Suppliers' ),
		),
		array(
			'id'    => 'hr',
			'title' => 'HR & Payroll',
			'blurb' => 'Staff rostering, credentials, attendance, and payroll-ready records.',
			'panel' => array( 'On duty', 'Leave', 'Credentials', 'Departments' ),
		),
		array(
			'id'    => 'pharmacy',
			'title' => 'Pharmacy & Inventory',
			'blurb' => 'Dispensing, stock, expiry, and controlled-item accountability.',
			'panel' => array( 'Dispensed today', 'Low stock', 'Expiries', 'Ward issues' ),
		),
		array(
			'id'    => 'accounts',
			'title' => 'Accounts & Finance',
			'blurb' => 'Ledgers, journals, receivables, payables, and month-end financial control.',
			'panel' => array( 'General ledger', 'Accounts receivable', 'Accounts payable', 'Bank & cash' ),
		),
		array(
			'id'    => 'integrations',
			'title' => 'Integrations',
			'blurb' => 'National schemes, payments, labs, fiscalization, and messaging—already on the rails.',
			'kind'  => 'cards',
			'panel' => integral_hmis_integrations(),
		),
		array(
			'id'    => 'reports',
			'title' => 'Reports & Analytics',
			'blurb' => 'Operational, clinical, and financial reports leadership can act on.',
			'panel' => array( 'Bed occupancy', 'Revenue mix', 'Turnaround', 'Quality KPIs' ),
		),
	);
}

function integral_hmis_integrations() {
	return array(
		array( 'name' => 'DHA', 'hint' => 'The Digital Health Agency' ),
		array( 'name' => 'SHA', 'hint' => 'Kenya social health authority' ),
		array( 'name' => 'MASM', 'hint' => 'Malawi medical aid' ),
		array( 'name' => 'M-Pesa', 'hint' => 'Mobile payments' ),
		array( 'name' => 'eTIMS', 'hint' => 'KRA fiscalization' ),
		array( 'name' => 'Smart', 'hint' => 'Smart integration' ),
		array( 'name' => 'Slade', 'hint' => 'Slade integration' ),
		array( 'name' => 'LIS', 'hint' => 'Lab information systems' ),
		array( 'name' => 'Bulk SMS', 'hint' => 'Patient & staff alerts' ),
		array( 'name' => 'AI Chatbot', 'hint' => 'DeepSeek-powered assist' ),
		array( 'name' => 'Mailing', 'hint' => 'Transactional email' ),
		array( 'name' => 'MRA', 'hint' => 'Malawi Revenue Authority' ),
	);
}

function integral_services() {
	return array(
		'build'   => array(
			'title' => 'Build',
			'intro' => 'Product engineering beside our HMIS stronghold.',
			'items' => array(
				array( 'title' => 'Web Development', 'slug' => 'web-development', 'blurb' => 'Hospital portals and operational web platforms.' ),
				array( 'title' => 'Web App Development', 'slug' => 'web-app-development', 'blurb' => 'Custom clinical and admin applications.' ),
				array( 'title' => 'Mobile Apps', 'slug' => 'mobile-apps-development', 'blurb' => 'Field, clinician, and patient mobile experiences.' ),
				array( 'title' => 'IoT Development', 'slug' => 'iot-development-services', 'blurb' => 'Device-aware hospital and logistics workflows.' ),
			),
		),
		'operate' => array(
			'title' => 'Operate',
			'intro' => 'Keep HMIS and adjacent systems reliable after go-live.',
			'items' => array(
				array( 'title' => 'Managed Services', 'slug' => 'managed-services', 'blurb' => 'Monitoring, support, and continuous improvement.' ),
				array( 'title' => 'Healthcare Systems', 'slug' => 'healthcare', 'blurb' => 'Implementation and optimization for care facilities.' ),
				array( 'title' => 'Business Automation', 'slug' => 'business-automation', 'blurb' => 'Replace manual hospital back-office friction.' ),
				array( 'title' => 'Business Consultation', 'slug' => 'business-consultation', 'blurb' => 'Roadmaps for digitizing facility operations.' ),
			),
		),
		'grow'    => array(
			'title' => 'Grow',
			'intro' => 'Extend reach around your clinical platform.',
			'items' => array(
				array( 'title' => 'Digital Marketing', 'slug' => 'digital-marketing', 'blurb' => 'Growth systems for health brands and networks.' ),
				array( 'title' => 'Integrations', 'slug' => 'wallet-integration', 'blurb' => 'Payments, claims rails, and national schemes.' ),
			),
		),
	);
}

function integral_industries() {
	return array(
		array( 'title' => 'Hospitals & Clinics', 'slug' => 'healthcare', 'blurb' => 'Full HMIS for Level 2–6 facilities and private hospitals.' ),
		array( 'title' => 'Finance & Banking', 'slug' => 'finance-banking', 'blurb' => 'Secure digital channels for financial institutions.' ),
		array( 'title' => 'Insurance', 'slug' => 'insurance', 'blurb' => 'Claims and member platforms.' ),
		array( 'title' => 'Government & Counties', 'slug' => 'government-counties', 'blurb' => 'Public service and county health systems.' ),
		array( 'title' => 'Education', 'slug' => 'education', 'blurb' => 'Institutional learning platforms.' ),
		array( 'title' => 'Manufacturing', 'slug' => 'manufacturing', 'blurb' => 'Operations digitization.' ),
	);
}

function integral_company_pages() {
	return array(
		'about-us'            => array(
			'eyebrow'  => 'The Company',
			'title'    => 'Built around hospital reality.',
			'lead'     => 'Integral Software Technology is a Nairobi-rooted team whose strongest product is a full Hospital Management Information System—wired into Kenya and Malawi national schemes, payments, fiscalization, labs, and AI assist.',
			'sections' => array(
				array( 'heading' => 'HMIS first', 'body' => 'Everything else we build sits next to a proven hospital operations platform—not a generic CRM dressed up for healthcare.' ),
				array( 'heading' => 'National rails', 'body' => 'SHA, MASM, eTIMS, M-Pesa, LIS, SMS, mailing, MRA—integrations facilities actually need to run.' ),
				array( 'heading' => 'Latest integration', 'body' => 'DHA Certification live on the platform — CERT-2026-4GFA8APJ. Also registered with the ODPC as a Data Processor (Serial 24255).' ),
			),
		),
		'our-methodology'     => array(
			'eyebrow'  => 'Methodology',
			'title'    => 'Go-live that hospital teams survive.',
			'lead'     => 'Discovery with clinical and admin owners. Configuration against real FR codes and workflows. Training, cutover, and managed run.',
			'sections' => array(
				array( 'heading' => 'Discover', 'body' => 'Map departments, claims mix, pharmacy, and reporting needs before configuration starts.' ),
				array( 'heading' => 'Configure', 'body' => 'Modules, integrations, and facility identity—including FR code and SHA readiness.' ),
				array( 'heading' => 'Launch', 'body' => 'Phased go-live with floor support, not a risky big-bang dump.' ),
				array( 'heading' => 'Operate', 'body' => 'Managed services, AI assist, and continuous module expansion.' ),
			),
		),
		'our-business-model'  => array(
			'eyebrow'  => 'Business Model',
			'title'    => 'License. Implement. Run.',
			'lead'     => 'HMIS licensing with implementation and optional managed operations.',
			'sections' => array(
				array( 'heading' => 'HMIS license', 'body' => 'Deploy Integral Hospital Management across your facility or network.' ),
				array( 'heading' => 'Implementation', 'body' => 'Scoped rollout: modules, data, integrations, training.' ),
				array( 'heading' => 'Managed run', 'body' => 'Ongoing support, monitoring, and enhancement capacity.' ),
			),
		),
		'our-people'          => array(
			'eyebrow'  => 'Our People',
			'title'    => 'Engineers who understand the ward and the till.',
			'lead'     => 'Product, clinical workflow, claims, and infrastructure talent in one delivery team.',
			'sections' => array(
				array( 'heading' => 'Hospital fluency', 'body' => 'We speak OPD, IPD, pharmacy, claims, and procurement without translation layers.' ),
				array( 'heading' => 'Accountable delivery', 'body' => 'Named ownership from demo through go-live.' ),
			),
		),
		'our-partners'        => array(
			'eyebrow'  => 'Partners',
			'title'    => 'Ecosystem that keeps facilities connected.',
			'lead'     => 'National schemes, payment rails, lab systems, fiscalization, and messaging partners.',
			'sections' => array(
				array( 'heading' => 'Scheme & rails', 'body' => 'SHA, MASM, M-Pesa, eTIMS, MRA, and related national integrations.' ),
				array( 'heading' => 'Clinical systems', 'body' => 'LIS and allied hospital technology partners.' ),
			),
		),
		'careers'             => array(
			'eyebrow'  => 'Careers',
			'title'    => 'Ship software that runs hospitals.',
			'lead'     => 'Join a team whose product sits in real clinical and revenue workflows every day.',
			'sections' => array(
				array( 'heading' => 'Open conversations', 'body' => 'Tell us what you build—engineering, implementation, support, or product.' ),
				array( 'heading' => 'How we work', 'body' => 'High ownership, Nairobi hub, respect for deep work.' ),
			),
		),
	);
}

/**
 * Official certifications shown on home + Certifications page.
 *
 * @return array<int,array<string,mixed>>
 */
function integral_certifications() {
	$uri = get_template_directory_uri();
	return array(
		array(
			'id'          => 'dha',
			'eyebrow'     => 'Digital Health Agency',
			'title'       => 'DHA Digital Health System Certification',
			'summary'     => 'Integral has satisfactorily met the compliance and conformance requirements of the Kenya Digital Health Certification Framework for hospital information systems.',
			'about'       => 'DHA is Kenya’s government digital health agency—advancing access, outcomes, and data security nationwide. Integral is certified under its Digital Health Certification Framework.',
			'meta'        => array(
				'Certificate' => 'CERT-2026-4GFA8APJ',
				'Certified'   => '24 Sep 2026',
				'Valid until' => '23 Sep 2028',
			),
			'image'       => $uri . '/assets/dha-certificate.jpg',
			'pdf'         => $uri . '/assets/dha-certificate.pdf',
			'image_alt'   => 'Digital Health Agency certification for Integral — CERT-2026-4GFA8APJ',
			'cta_label'   => 'Open DHA Certificate',
			'link_label'  => 'Visit DHA',
			'link_url'    => 'https://www.dha.go.ke/',
		),
		array(
			'id'          => 'odpc',
			'eyebrow'     => 'Office of the Data Protection Commissioner',
			'title'       => 'ODPC Certificate of Registration',
			'summary'     => 'Integral Software Technology Ltd is registered with the Office of the Data Protection Commissioner as a Data Processor under Kenya’s Data Protection Act, 2019.',
			'about'       => 'The ODPC safeguards personal data in Kenya—regulating how controllers and processors collect, use, and protect information, and giving individuals rights over their data. As a registered Data Processor, Integral processes facility and patient-related data on behalf of healthcare providers under that legal framework.',
			'meta'        => array(
				'Serial No.'  => '24255',
				'Registration'=> '463-9791-862C',
				'Role'        => 'Data Processor',
				'Valid'       => '21 Jul 2026 – 21 Jul 2028',
			),
			'image'       => $uri . '/assets/odpc-certificate.jpg',
			'pdf'         => $uri . '/assets/odpc-certificate.pdf',
			'image_alt'   => 'ODPC Certificate of Registration for Integral Software Technology Ltd — Serial 24255',
			'cta_label'   => 'Open ODPC Certificate',
			'link_label'  => 'Visit ODPC',
			'link_url'    => 'https://www.odpc.go.ke/',
		),
	);
}

/**
 * Render one certification block (WP 4.9–safe).
 *
 * @param array $cert    Certification data.
 * @param bool  $reverse Flip image/copy on desktop.
 */
function integral_render_certification( $cert, $reverse = false ) {
	if ( empty( $cert['id'] ) ) {
		return;
	}
	$anchor = 'cert-' . sanitize_html_class( $cert['id'] );
	?>
	<article class="int-dha int-cert<?php echo $reverse ? ' int-cert--reverse' : ''; ?>" id="<?php echo esc_attr( $anchor ); ?>">
		<div class="int-dha__layout int-reveal">
			<figure class="int-dha__figure">
				<a
					class="int-dha__frame"
					href="<?php echo esc_url( $cert['pdf'] ); ?>"
					target="_blank"
					rel="noopener noreferrer"
				>
					<img
						src="<?php echo esc_url( $cert['image'] ); ?>"
						alt="<?php echo esc_attr( $cert['image_alt'] ); ?>"
						width="800"
						height="1132"
						loading="lazy"
					>
				</a>
				<figcaption class="int-dha__caption">Official certificate · click to open PDF</figcaption>
			</figure>
			<div class="int-dha__copy">
				<div class="int-eyebrow"><?php echo esc_html( $cert['eyebrow'] ); ?></div>
				<h2><?php echo esc_html( $cert['title'] ); ?></h2>
				<p><?php echo esc_html( $cert['summary'] ); ?></p>
				<?php if ( ! empty( $cert['about'] ) ) : ?>
					<p class="int-cert__about"><?php echo esc_html( $cert['about'] ); ?></p>
				<?php endif; ?>
				<?php if ( ! empty( $cert['meta'] ) && is_array( $cert['meta'] ) ) : ?>
					<dl class="int-dha__meta int-dha__meta--<?php echo esc_attr( (string) count( $cert['meta'] ) ); ?>">
						<?php foreach ( $cert['meta'] as $label => $value ) : ?>
							<div>
								<dt><?php echo esc_html( $label ); ?></dt>
								<dd><?php echo esc_html( $value ); ?></dd>
							</div>
						<?php endforeach; ?>
					</dl>
				<?php endif; ?>
				<div class="int-dha__actions">
					<a class="int-btn int-btn--primary" href="<?php echo esc_url( $cert['pdf'] ); ?>" target="_blank" rel="noopener noreferrer">
						<?php echo esc_html( $cert['cta_label'] ); ?>
					</a>
					<?php if ( ! empty( $cert['link_url'] ) ) : ?>
						<a class="int-btn int-btn--ghost" href="<?php echo esc_url( $cert['link_url'] ); ?>" target="_blank" rel="noopener noreferrer">
							<?php echo esc_html( ! empty( $cert['link_label'] ) ? $cert['link_label'] : 'Learn more' ); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</article>
	<?php
}

/**
 * Open career roles. Applications auto-close after closes_at (end of day EAT).
 *
 * @return array<int,array<string,mixed>>
 */
function integral_careers_openings() {
	$raised = '2026-09-25';
	$closes = '2026-10-30';
	$tz     = new DateTimeZone( 'Africa/Nairobi' );
	$now    = new DateTimeImmutable( 'now', $tz );
	$close  = DateTimeImmutable::createFromFormat( 'Y-m-d H:i:s', $closes . ' 23:59:59', $tz );
	$open   = $close instanceof DateTimeImmutable && $now <= $close;

	$roles = array(
		array(
			'id'       => 'devops',
			'title'    => 'DevOps',
			'summary'  => 'Own cloud, CI/CD, monitoring, and the rails that keep HMIS environments healthy.',
			'focus'    => array( 'Linux / containers', 'CI/CD pipelines', 'Observability & uptime', 'Secure deployments' ),
		),
		array(
			'id'       => 'software-support',
			'title'    => 'Software Support Engineers',
			'summary'  => 'Front-line product support for hospitals—triage, diagnose, and close the loop with engineering.',
			'focus'    => array( 'Facility troubleshooting', 'Issue triage', 'SQL & logs', 'Customer communication' ),
		),
		array(
			'id'       => 'sales-marketing',
			'title'    => 'Sales And Marketing',
			'summary'  => 'Grow HMIS adoption across counties and hospital groups—demos, proposals, and campaigns.',
			'focus'    => array( 'Pipeline & demos', 'Proposal writing', 'Market outreach', 'Partner relationships' ),
		),
		array(
			'id'       => 'customer-care',
			'title'    => 'Customer Care',
			'summary'  => 'Be the steady voice for facilities—onboarding help, follow-ups, and service excellence.',
			'focus'    => array( 'Inbound support', 'Ticket follow-through', 'Training assistance', 'Satisfaction loops' ),
		),
	);

	foreach ( $roles as &$role ) {
		$role['raised_at']  = $raised;
		$role['closes_at']  = $closes;
		$role['is_open']    = $open;
		$role['raised_label'] = date_i18n( 'j M Y', strtotime( $raised . ' 12:00:00' ) );
		$role['closes_label'] = date_i18n( 'j M Y', strtotime( $closes . ' 12:00:00' ) );
	}
	unset( $role );

	return $roles;
}

/**
 * Whether careers applications are still open (through 30 Oct 2026 EAT).
 */
function integral_careers_are_open() {
	$openings = integral_careers_openings();
	return ! empty( $openings[0]['is_open'] );
}

/**
 * Resolve a career role by id.
 *
 * @param string $id Role slug.
 * @return array|null
 */
function integral_careers_role( $id ) {
	$id = sanitize_title( (string) $id );
	foreach ( integral_careers_openings() as $role ) {
		if ( $role['id'] === $id ) {
			return $role;
		}
	}
	return null;
}

function integral_get_company( $slug ) {
	$pages = integral_company_pages();
	return isset( $pages[ $slug ] ) ? $pages[ $slug ] : null;
}

function integral_contact_info() {
	return array(
		'phone'   => '+254 720 730 430 . +254 20 252 4342 ',
		'email'   => 'info@integral.co.ke',
		'address' => 'P.O. Box 19528–00100, Nairobi, Kenya',
		'hours'   => 'Mon–Sat, 8:00–17:00 EAT',
	);
}

/**
 * HMIS pricing — amounts in KES thousands (display as e.g. 25k).
 */
function integral_pricing() {
	return array(
		'currency' => 'KES',
		'rates'    => array(
			'usd' => 130, // KSh per 1 USD
			'eur' => 148, // KSh per 1 EUR
		),
		'modes'    => array(
			'cloud'  => array(
				'label'  => 'Cloud',
				'hint'   => 'Online',
				'blurb'  => 'Hosted Integral HMIS—faster go-live, lower setup.',
				'levels' => array(
					array( 'level' => 2, 'setup' => 50,  'quarterly' => 20, 'yearly' => 50 ),
					array( 'level' => 3, 'setup' => 100, 'quarterly' => 40, 'yearly' => 100 ),
					array( 'level' => 4, 'setup' => 150, 'quarterly' => 60, 'yearly' => 150 ),
					array( 'level' => 5, 'setup' => 200, 'quarterly' => 80, 'yearly' => 200 ),
				),
			),
			'onprem' => array(
				'label'  => 'On Premise',
				'hint'   => 'Offline',
				'blurb'  => 'Local install for facilities that need full offline control.',
				'levels' => array(
					array( 'level' => 2, 'setup' => 200, 'quarterly' => 20, 'yearly' => 50 ),
					array( 'level' => 3, 'setup' => 300, 'quarterly' => 40, 'yearly' => 100 ),
					array( 'level' => 4, 'setup' => 400, 'quarterly' => 60, 'yearly' => 150 ),
					array( 'level' => 5, 'setup' => 500, 'quarterly' => 80, 'yearly' => 200 ),
				),
			),
		),
		'notes'    => array(
			'Setup and licence fees are payable in advance.',
			'Setup covers installation, configuration, implementation, and training.',
			'Licence is billed quarterly or annually.',
		),
	);
}
