<?php
/**
 * Inquire — demo-forward
 */
get_header();
?>

<section class="int-page-hero int-page-hero--hmis">
	<div class="int-container">
		<div class="int-eyebrow">Request a demo</div>
		<h1>Book an Integral HMIS walkthrough.</h1>
		<p class="int-lead">Prefer the interactive path? Launch the demo theater—or send a classic inquiry below.</p>
		<div class="int-hero__actions" style="margin-top:1.25rem;">
			<button type="button" class="int-btn int-btn--primary" data-demo-open>Open demo theater</button>
		</div>
	</div>
</section>

<section class="int-section">
	<div class="int-container" style="max-width:720px;">
		<div class="int-form-panel int-reveal">
			<h3 style="margin-top:0;">Or send a direct inquiry</h3>
			<?php
			if ( shortcode_exists( 'contact-form-7' ) ) {
				echo do_shortcode( '[contact-form-7 id="313" title="SERVICE REQUEST/INQUIRY"]' );
			} else {
				?>
				<form class="int-fallback-form" action="mailto:info@integral.co.ke" method="post" enctype="text/plain">
					<label for="org">Facility / organization</label>
					<input id="org" name="organization" required>
					<label for="fr">FR code</label>
					<input id="fr" name="fr_code">
					<label for="name">Your name</label>
					<input id="name" name="name" required>
					<label for="email">Email</label>
					<input id="email" type="email" name="email" required>
					<label for="need">What do you want to see?</label>
					<textarea id="need" name="need" rows="6" required></textarea>
					<button class="int-btn int-btn--primary" type="submit">Submit</button>
				</form>
				<?php
			}
			?>
		</div>
	</div>
</section>

<?php get_footer(); ?>
