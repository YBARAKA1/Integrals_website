<?php
/**
 * Company pages (about, methodology, etc.)
 */
get_header();
$slug = get_post_field( 'post_name', get_queried_object_id() );
$data = integral_get_company( $slug );
if ( ! $data ) {
	$data = array(
		'eyebrow'  => 'Integral',
		'title'    => get_the_title(),
		'lead'     => '',
		'sections' => array(),
	);
}
?>

<section class="int-page-hero">
	<div class="int-container">
		<div class="int-eyebrow"><?php echo esc_html( $data['eyebrow'] ); ?></div>
		<h1><?php echo esc_html( $data['title'] ); ?></h1>
		<?php if ( ! empty( $data['lead'] ) ) : ?>
			<p class="int-lead"><?php echo esc_html( $data['lead'] ); ?></p>
		<?php endif; ?>
	</div>
</section>

<section class="int-section">
	<div class="int-container">
		<?php if ( ! empty( $data['sections'] ) ) : ?>
			<div class="int-blocks <?php echo count( $data['sections'] ) > 1 ? 'cols-2' : ''; ?>">
				<?php foreach ( $data['sections'] as $section ) : ?>
					<div class="int-block int-reveal">
						<h3><?php echo esc_html( $section['heading'] ); ?></h3>
						<p><?php echo esc_html( $section['body'] ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<div class="int-reveal">
				<?php
				while ( have_posts() ) :
					the_post();
					the_content();
				endwhile;
				?>
			</div>
		<?php endif; ?>

		<div style="margin-top:3rem;" class="int-reveal">
			<button type="button" class="int-btn int-btn--primary" data-demo-open>Request HMIS demo</button>
			<a class="int-btn int-btn--ghost" style="margin-left:0.5rem;" href="<?php echo esc_url( home_url( '/softwares/' ) ); ?>">See HMIS</a>
		</div>
	</div>
</section>

<?php get_footer(); ?>
