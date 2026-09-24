<?php
/**
 * Industries hub
 */
get_header();
$industries = integral_industries();
?>

<section class="int-page-hero">
	<div class="int-container">
		<div class="int-eyebrow">Industries</div>
		<h1>Domain depth. Technical rigor.</h1>
		<p class="int-lead">We bring sector context into product and platform delivery—so solutions fit how your organization actually operates.</p>
	</div>
</section>

<section class="int-section">
	<div class="int-container">
		<div class="int-tiles">
			<?php foreach ( $industries as $ind ) : ?>
				<a class="int-tile int-reveal" href="<?php echo esc_url( home_url( '/' . $ind['slug'] . '/' ) ); ?>">
					<h3><?php echo esc_html( $ind['title'] ); ?></h3>
					<p><?php echo esc_html( $ind['blurb'] ); ?></p>
					<span class="int-tile__go">Explore →</span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="int-section">
	<div class="int-container">
		<div class="int-cta int-reveal">
			<h2>Building for a specific sector?</h2>
			<p>Tell us about your environment, constraints, and goals.</p>
			<div class="int-cta__actions">
				<a class="int-btn int-btn--primary" href="<?php echo esc_url( home_url( '/service-request-inquiry/' ) ); ?>">Inquire</a>
			</div>
		</div>
	</div>
</section>

<?php get_footer(); ?>
