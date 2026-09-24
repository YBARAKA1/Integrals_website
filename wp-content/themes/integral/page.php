<?php
/**
 * Generic page fallback
 */
get_header();
?>

<section class="int-page-hero">
	<div class="int-container">
		<div class="int-eyebrow">Integral</div>
		<h1><?php the_title(); ?></h1>
	</div>
</section>

<section class="int-section">
	<div class="int-container" style="max-width:820px;">
		<div class="int-reveal">
			<?php
			while ( have_posts() ) :
				the_post();
				the_content();
			endwhile;
			?>
		</div>
	</div>
</section>

<?php get_footer(); ?>
