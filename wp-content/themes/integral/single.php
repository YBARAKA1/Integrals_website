<?php
/**
 * Single post (services / industries) — redesigned layout
 */
get_header();

$slug  = get_post_field( 'post_name' );
$title = get_the_title();
$blurb = '';
$group_label = 'Capability';

foreach ( integral_services() as $key => $group ) {
	foreach ( $group['items'] as $item ) {
		if ( $item['slug'] === $slug ) {
			$blurb = $item['blurb'];
			$group_label = $group['title'];
			break 2;
		}
	}
}
if ( ! $blurb ) {
	foreach ( integral_industries() as $ind ) {
		if ( $ind['slug'] === $slug ) {
			$blurb       = $ind['blurb'];
			$group_label = 'Industry';
			break;
		}
	}
}
if ( ! $blurb ) {
	$blurb = 'Integral delivers focused technology engagements with clear ownership and measurable outcomes.';
}
?>

<section class="int-page-hero">
	<div class="int-container">
		<div class="int-eyebrow"><?php echo esc_html( $group_label ); ?></div>
		<h1><?php echo esc_html( $title ); ?></h1>
		<p class="int-lead"><?php echo esc_html( $blurb ); ?></p>
	</div>
</section>

<section class="int-section">
	<div class="int-container">
		<div class="int-blocks cols-2 int-reveal">
			<div class="int-block">
				<h3>Discovery</h3>
				<p>Clarify goals, constraints, integrations, stakeholders, and what success looks like in production.</p>
			</div>
			<div class="int-block">
				<h3>Architecture</h3>
				<p>Choose a stack and structure that can ship quickly without painting you into a corner.</p>
			</div>
			<div class="int-block">
				<h3>Build &amp; launch</h3>
				<p>Incremental delivery with quality gates, demos you can trust, and a clean path to go-live.</p>
			</div>
			<div class="int-block">
				<h3>Operate</h3>
				<p>Optional managed services—monitoring, iteration, and continuous improvement after launch.</p>
			</div>
		</div>
		<div style="margin-top:2.5rem;" class="int-reveal">
			<a class="int-btn int-btn--primary" href="<?php echo esc_url( home_url( '/service-request-inquiry/' ) ); ?>">Inquire about <?php echo esc_html( $title ); ?></a>
			<a class="int-btn int-btn--ghost" style="margin-left:0.5rem;" href="<?php echo esc_url( home_url( '/services/' ) ); ?>">All services</a>
		</div>
	</div>
</section>

<?php get_footer(); ?>
