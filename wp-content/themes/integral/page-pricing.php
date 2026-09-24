<?php
/**
 * Pricing — interactive HMIS tiers
 */
get_header();
?>

<section class="int-pricing-hero">
	<div class="int-pricing-hero__glow" aria-hidden="true"></div>
	<div class="int-pricing-hero__grid" aria-hidden="true"></div>
	<div class="int-container">
		<div class="int-pricing-hero__copy">
			<div class="int-eyebrow">Pricing</div>
			<h1>Build your HMIS quote.</h1>
			<p class="int-lead">Toggle cloud or on-prem, pick a level, switch quarterly or yearly—watch the year-one number update live.</p>
		</div>
	</div>
</section>

<section class="int-section int-section--tight int-pricing-section">
	<div class="int-container">
		<?php get_template_part( 'template-parts/pricing' ); ?>
	</div>
</section>

<section class="int-section">
	<div class="int-container">
		<div class="int-cta int-cta--hot int-reveal">
			<div class="int-eyebrow" style="justify-content:center;">Not sure which level?</div>
			<h2>We’ll map your facility to the right tier.</h2>
			<p>Share bed count, modules, and SHA needs—we’ll recommend Cloud or On Premise with a scoped demo.</p>
			<div class="int-cta__actions">
				<button type="button" class="int-btn int-btn--primary" data-demo-open>Talk pricing</button>
				<a class="int-btn int-btn--ghost" href="<?php echo esc_url( home_url( '/softwares/' ) ); ?>">See HMIS modules</a>
			</div>
		</div>
	</div>
</section>

<?php get_footer(); ?>
