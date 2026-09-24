<?php
/**
 * Index fallback
 */
get_header();
?>

<section class="int-page-hero">
	<div class="int-container">
		<div class="int-eyebrow">Integral</div>
		<h1><?php echo esc_html( get_bloginfo( 'name' ) ); ?></h1>
	</div>
</section>

<section class="int-section">
	<div class="int-container">
		<?php if ( have_posts() ) : ?>
			<div class="int-tiles">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<a class="int-tile int-reveal" href="<?php the_permalink(); ?>">
						<h3><?php the_title(); ?></h3>
						<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18 ) ); ?></p>
						<span class="int-tile__go">Read →</span>
					</a>
				<?php endwhile; ?>
			</div>
		<?php else : ?>
			<p>No content found.</p>
		<?php endif; ?>
	</div>
</section>

<?php get_footer(); ?>
