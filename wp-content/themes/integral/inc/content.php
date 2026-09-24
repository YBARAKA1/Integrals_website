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
			'label' => 'Pricing',
			'url'   => home_url( '/pricing/' ),
		),
		array(
			'label' => 'Contact',
			'url'   => home_url( '/contacts/' ),
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
			'id'    => 'reports',
			'title' => 'Reports & Analytics',
			'blurb' => 'Operational, clinical, and financial reports leadership can act on.',
			'panel' => array( 'Bed occupancy', 'Revenue mix', 'Turnaround', 'Quality KPIs' ),
		),
		array(
			'id'    => 'hr',
			'title' => 'HR & Employees',
			'blurb' => 'Staff rostering, credentials, attendance, and payroll-ready records.',
			'panel' => array( 'On duty', 'Leave', 'Credentials', 'Departments' ),
		),
		array(
			'id'    => 'ambulance',
			'title' => 'Ambulance',
			'blurb' => 'Dispatch, trip logs, and emergency movement tied into facility operations.',
			'panel' => array( 'Active trips', 'Available units', 'ETA', 'Handovers' ),
		),
		array(
			'id'    => 'pharmacy',
			'title' => 'Pharmacy & Inventory',
			'blurb' => 'Dispensing, stock, expiry, and controlled-item accountability.',
			'panel' => array( 'Dispensed today', 'Low stock', 'Expiries', 'Ward issues' ),
		),
	);
}

function integral_hmis_integrations() {
	return array(
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
