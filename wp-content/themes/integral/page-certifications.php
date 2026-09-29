<?php
/**
 * Certifications — DHA + ODPC credentials.
 */
get_header();
$certs = integral_certifications();
?>

<section class="int-page-hero int-page-hero--hmis">
	<div class="int-container">
		<div class="int-eyebrow">Certifications</div>
		<h1>Official credentials for Integral HMIS.</h1>
		<p class="int-lead">National digital health certification and Kenya data protection registration—credentials facilities can verify.</p>
	</div>
</section>

<section class="int-section int-certs">
	<div class="int-container">
		<?php
		foreach ( $certs as $i => $cert ) {
			integral_render_certification( $cert, ( 1 === ( $i % 2 ) ) );
		}
		?>
	</div>
</section>

<?php get_footer(); ?>
