<?php
/**
 * Grow → Integrations — partner sandbox + integration capabilities.
 * Mapped for slug wallet-integration (page or forced via template_include).
 */
get_header();
$sandbox = integral_partner_sandbox();
$integrations = integral_hmis_integrations();
?>

<section class="int-page-hero int-page-hero--hmis">
	<div class="int-container">
		<div class="int-eyebrow">Grow · Integrations</div>
		<h1>Partner APIs through Integral.</h1>
		<p class="int-lead">
			Payments, claims rails, and national schemes—plus a live partner sandbox where external developers
			request credentials and call SHA / HIE registries <strong>only through Integral</strong>.
		</p>
		<div class="int-hero__actions" style="margin-top:1.25rem;">
			<a class="int-btn int-btn--primary" href="<?php echo esc_url( $sandbox['request'] ); ?>" target="_blank" rel="noopener noreferrer">
				Request sandbox access
			</a>
		</div>
		<p class="int-sandbox-host">Invite-only after approval — no public sandbox link on this site</p>
	</div>
</section>

<section class="int-section" id="partner-sandbox">
	<div class="int-container">
		<div class="int-section__head int-reveal">
			<div class="int-eyebrow">Partner sandbox</div>
			<h2>Request. Get approved. Call registries.</h2>
			<p>
				External apps never use the hospital’s AfyaConnect Consumer Key or Secret.
				Integral issues a Partner Client, mints tokens, and proxies Facility / Health Worker / Client Registry.
			</p>
		</div>
		<div class="int-blocks cols-3 int-reveal">
			<div class="int-block">
				<h3>1. Request access</h3>
				<p>Submit your organization and work email on the demo host. Staff review under Integrations → API Partners.</p>
			</div>
			<div class="int-block">
				<h3>2. Receive credentials</h3>
				<p>After approval you get <code>client_id</code> / <code>client_secret</code> by email (secret once).</p>
			</div>
			<div class="int-block">
				<h3>3. Use your private invite</h3>
				<p>Open the invite link from your email (not a public URL), then generate a token and call <code>/api/v1/dev/…</code>.</p>
			</div>
		</div>
		<div class="int-sandbox-cta int-reveal">
			<div>
				<h3>Ready to integrate?</h3>
				<p>Submit a request. If approved, Integral emails you credentials and a <strong>private sandbox invite link</strong>. The sandbox URL alone stays locked.</p>
			</div>
			<div class="int-sandbox-cta__actions">
				<a class="int-btn int-btn--primary" href="<?php echo esc_url( $sandbox['request'] ); ?>" target="_blank" rel="noopener noreferrer">Request access</a>
			</div>
		</div>
	</div>
</section>

<section class="int-section int-section--slash" id="rails">
	<div class="int-container">
		<div class="int-section__head int-reveal">
			<div class="int-eyebrow">On the rails</div>
			<h2>What Integral already connects</h2>
			<p>National schemes, payments, labs, fiscalization, and messaging—built for Kenya and Malawi operating reality.</p>
		</div>
		<div class="int-integ-board int-integ-board--wide int-reveal">
			<?php foreach ( $integrations as $item ) : ?>
				<div class="int-integ-chip">
					<strong><?php echo esc_html( $item['name'] ); ?></strong>
					<span><?php echo esc_html( $item['hint'] ); ?></span>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="int-section">
	<div class="int-container">
		<div class="int-cta int-cta--hot int-reveal">
			<h2>Need a hospital-side integration?</h2>
			<p>Facility implementations still start with a product conversation—not a vague consulting call.</p>
			<div class="int-cta__actions">
				<a class="int-btn int-btn--primary" href="<?php echo esc_url( home_url( '/service-request-inquiry/' ) ); ?>">Inquire</a>
				<a class="int-btn int-btn--ghost" href="<?php echo esc_url( home_url( '/services/' ) ); ?>">All services</a>
			</div>
		</div>
	</div>
</section>

<?php get_footer(); ?>
