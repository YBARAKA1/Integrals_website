<?php
/**
 * Contact — interactive
 */
get_header();
$info = integral_contact_info();
?>

<section class="int-page-hero int-page-hero--hmis int-contact-hero">
	<div class="int-container">
		<div class="int-eyebrow">Contact</div>
		<h1>Talk to the team behind the HMIS.</h1>
		<p class="int-lead">Sales demos, implementation, partnerships, or support—pick a lane and we’ll route you to the right people.</p>
	</div>
</section>

<section class="int-section int-section--tight">
	<div class="int-container">
		<div class="int-contact-intents int-reveal" data-contact-intents>
			<button type="button" class="int-contact-intent is-on" data-intent="HMIS Demo" data-hint="Book a product walkthrough for your facility.">
				<span class="int-contact-intent__tag">01</span>
				<strong>HMIS Demo</strong>
				<em>Product theater for your facility</em>
			</button>
			<button type="button" class="int-contact-intent" data-intent="Implementation" data-hint="Go-live, modules, FR setup, training.">
				<span class="int-contact-intent__tag">02</span>
				<strong>Implementation</strong>
				<em>Rollout &amp; configuration</em>
			</button>
			<button type="button" class="int-contact-intent" data-intent="Partnership" data-hint="Integrators, counties, hospital groups.">
				<span class="int-contact-intent__tag">03</span>
				<strong>Partnership</strong>
				<em>Channels &amp; alliances</em>
			</button>
			<button type="button" class="int-contact-intent" data-intent="General Inquiry" data-hint="Anything else—we’ll route it.">
				<span class="int-contact-intent__tag">04</span>
				<strong>General</strong>
				<em>Other questions</em>
			</button>
		</div>
	</div>
</section>

<section class="int-section">
	<div class="int-container">
		<div class="int-contact-stage">
			<aside class="int-contact-rail int-reveal">
				<div class="int-contact-rail__card">
					<span class="int-contact-rail__label">Direct lines</span>
					<a class="int-contact-rail__action" href="tel:+254202524342">
						<span>Phone</span>
						<strong><?php echo esc_html( $info['phone'] ); ?></strong>
					</a>
					<a class="int-contact-rail__action" href="mailto:<?php echo esc_attr( $info['email'] ); ?>">
						<span>Email</span>
						<strong><?php echo esc_html( $info['email'] ); ?></strong>
					</a>
					<div class="int-contact-rail__action is-static">
						<span>HQ</span>
						<strong><?php echo esc_html( $info['address'] ); ?></strong>
					</div>
					<div class="int-contact-rail__action is-static">
						<span>Hours</span>
						<strong><?php echo esc_html( $info['hours'] ); ?></strong>
					</div>
				</div>

				<div class="int-contact-rail__pulse" aria-hidden="true">
					<div class="int-contact-rail__pulse-head">
						<span class="is-live"></span> Nairobi ops
					</div>
					<div class="int-contact-rail__bars">
						<i></i><i></i><i></i><i></i><i></i>
					</div>
					<p>Average reply within one business day on demo requests.</p>
				</div>

				<button type="button" class="int-btn int-btn--primary" data-demo-open style="width:100%;">Open demo theater</button>
			</aside>

			<div class="int-contact-composer int-reveal">
				<div class="int-contact-composer__top">
					<div class="int-contact-composer__dots" aria-hidden="true"><i></i><i></i><i></i></div>
					<span>New message · <span data-contact-intent-label>HMIS Demo</span></span>
				</div>
				<p class="int-contact-composer__hint" data-contact-hint>Book a product walkthrough for your facility.</p>
				<div class="int-contact-composer__body int-form-panel int-form-panel--embed">
					<?php
					if ( shortcode_exists( 'contact-form-7' ) ) {
						echo do_shortcode( '[contact-form-7 id="122" title="Contact Us"]' );
					} else {
						?>
						<form class="int-fallback-form" action="mailto:info@integral.co.ke" method="post" enctype="text/plain">
							<label for="name">Name</label>
							<input id="name" name="name" required>
							<label for="email">Email</label>
							<input id="email" type="email" name="email" required>
							<label for="tel">Phone</label>
							<input id="tel" name="tel">
							<label for="subject">Subject</label>
							<input id="subject" name="subject" data-contact-subject value="HMIS Demo">
							<label for="message">Message</label>
							<textarea id="message" name="message" rows="5" required></textarea>
							<button class="int-btn int-btn--primary" type="submit">Send message</button>
						</form>
						<?php
					}
					?>
				</div>
			</div>
		</div>
	</div>
</section>


<?php get_footer(); ?>
