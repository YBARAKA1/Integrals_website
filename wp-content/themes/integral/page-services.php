<?php
/**
 * Services hub — HMIS-adjacent
 */
get_header();
$groups = integral_services();
?>

<section class="int-page-hero int-page-hero--hmis">
	<div class="int-container">
		<div class="int-eyebrow">Services</div>
		<h1>Built beside the HMIS stronghold.</h1>
		<p class="int-lead">Custom software, managed operations, and growth systems—delivered by the same team that ships Integral Hospital Management.</p>
		<div class="int-hero__actions" style="margin-top:1.25rem;">
			<button type="button" class="int-btn int-btn--primary" data-demo-open>Request HMIS demo</button>
			<a class="int-btn int-btn--ghost" href="<?php echo esc_url( home_url( '/softwares/' ) ); ?>">Explore HMIS</a>
		</div>
	</div>
</section>

<?php foreach ( $groups as $key => $group ) : ?>
<section class="int-section" id="<?php echo esc_attr( $key ); ?>">
	<div class="int-container">
		<div class="int-section__head int-reveal">
			<div class="int-eyebrow"><?php echo esc_html( $group['title'] ); ?></div>
			<h2><?php echo esc_html( $group['title'] ); ?></h2>
			<p><?php echo esc_html( $group['intro'] ); ?></p>
		</div>
		<div class="int-tiles">
			<?php foreach ( $group['items'] as $item ) : ?>
				<a class="int-tile int-reveal" href="<?php echo esc_url( home_url( '/' . $item['slug'] . '/' ) ); ?>">
					<h3><?php echo esc_html( $item['title'] ); ?></h3>
					<p><?php echo esc_html( $item['blurb'] ); ?></p>
					<span class="int-tile__go">Open →</span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endforeach; ?>

<section class="int-section">
	<div class="int-container">
		<div class="int-cta int-cta--hot int-reveal">
			<h2>Most teams start with HMIS.</h2>
			<p>If your facility needs the full stack, begin with a product demo—not a vague consulting call.</p>
			<div class="int-cta__actions">
				<button type="button" class="int-btn int-btn--primary" data-demo-open>Start demo request</button>
			</div>
		</div>
	</div>
</section>

<?php get_footer(); ?>
