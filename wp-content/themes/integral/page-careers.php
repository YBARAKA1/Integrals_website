<?php
/**
 * Careers — open roles with per-category applications.
 */
get_header();
$openings = integral_careers_openings();
$is_open  = integral_careers_are_open();
$closes   = ! empty( $openings[0]['closes_label'] ) ? $openings[0]['closes_label'] : '30 Oct 2026';
$raised   = ! empty( $openings[0]['raised_label'] ) ? $openings[0]['raised_label'] : '25 Sep 2026';
?>

<section class="int-page-hero int-page-hero--hmis int-careers-hero">
	<div class="int-container">
		<div class="int-eyebrow">Careers</div>
		<h1>Join the team behind Integral HMIS.</h1>
		<p class="int-lead">Four open tracks. Apply to the role that fits—we review every response while the window is open.</p>
		<div class="int-careers-hero__meta" aria-live="polite">
			<span>Raised <?php echo esc_html( $raised ); ?></span>
			<span class="int-careers-hero__dot" aria-hidden="true"></span>
			<?php if ( $is_open ) : ?>
				<span class="int-careers-status is-open">Open until <?php echo esc_html( $closes ); ?></span>
			<?php else : ?>
				<span class="int-careers-status is-closed">Closed <?php echo esc_html( $closes ); ?></span>
			<?php endif; ?>
		</div>
	</div>
</section>

<section class="int-section int-careers-section">
	<div class="int-container">
		<?php if ( ! $is_open ) : ?>
			<div class="int-careers-banner is-closed int-reveal" role="status">
				<strong>Applications closed</strong>
				<p>These openings closed on <?php echo esc_html( $closes ); ?>. Watch this page for the next recruitment window.</p>
			</div>
		<?php else : ?>
			<div class="int-careers-banner is-open int-reveal" role="status">
				<strong>Applications open</strong>
				<p>Respond by <?php echo esc_html( $closes ); ?>. After that date these roles close automatically.</p>
			</div>
		<?php endif; ?>

		<div class="int-careers-list" data-careers>
			<?php foreach ( $openings as $i => $role ) : ?>
				<article
					class="int-career-card int-reveal<?php echo $role['is_open'] ? '' : ' is-closed'; ?>"
					id="role-<?php echo esc_attr( $role['id'] ); ?>"
					data-career-role="<?php echo esc_attr( $role['id'] ); ?>"
				>
					<header class="int-career-card__head">
						<div class="int-career-card__index"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></div>
						<div class="int-career-card__titles">
							<h2><?php echo esc_html( $role['title'] ); ?></h2>
							<p><?php echo esc_html( $role['summary'] ); ?></p>
						</div>
						<div class="int-career-card__dates">
							<div>
								<span>Date raised</span>
								<strong><?php echo esc_html( $role['raised_label'] ); ?></strong>
							</div>
							<div>
								<span>Open until</span>
								<strong><?php echo esc_html( $role['closes_label'] ); ?></strong>
							</div>
							<div>
								<span>Status</span>
								<strong class="<?php echo $role['is_open'] ? 'is-open' : 'is-closed'; ?>">
									<?php echo $role['is_open'] ? 'Open' : 'Closed'; ?>
								</strong>
							</div>
						</div>
					</header>

					<ul class="int-career-card__focus">
						<?php foreach ( $role['focus'] as $item ) : ?>
							<li><?php echo esc_html( $item ); ?></li>
						<?php endforeach; ?>
					</ul>

					<?php if ( $role['is_open'] ) : ?>
						<button
							type="button"
							class="int-btn int-btn--primary int-career-card__toggle"
							data-career-toggle
							aria-expanded="false"
							aria-controls="apply-<?php echo esc_attr( $role['id'] ); ?>"
						>
							Respond to this role
						</button>

						<div
							class="int-career-apply"
							id="apply-<?php echo esc_attr( $role['id'] ); ?>"
							data-career-panel
							hidden
						>
							<form class="int-career-form" data-career-form>
								<input type="hidden" name="role_id" value="<?php echo esc_attr( $role['id'] ); ?>">
								<input type="hidden" name="role_title" value="<?php echo esc_attr( $role['title'] ); ?>">

								<div class="int-career-form__grid">
									<label>
										<span>Full name</span>
										<input type="text" name="name" required autocomplete="name" placeholder="Your name">
									</label>
									<label>
										<span>Email</span>
										<input type="email" name="email" required autocomplete="email" placeholder="you@example.com">
									</label>
									<label>
										<span>Phone</span>
										<input type="tel" name="phone" required autocomplete="tel" placeholder="+254 …">
									</label>
									<label>
										<span>CV / portfolio link</span>
										<input type="url" name="cv_url" placeholder="https://…">
									</label>
								</div>

								<label class="int-career-form__message">
									<span>Why this role</span>
									<textarea name="message" rows="4" required placeholder="Brief note on your experience and interest."></textarea>
								</label>

								<p class="int-career-form__alert" data-career-alert hidden></p>

								<div class="int-career-form__actions">
									<button type="submit" class="int-btn int-btn--primary" data-career-submit>Send application</button>
									<button type="button" class="int-btn int-btn--ghost" data-career-cancel>Cancel</button>
								</div>
							</form>
						</div>
					<?php else : ?>
						<p class="int-career-card__closed-note">Applications for this role closed on <?php echo esc_html( $role['closes_label'] ); ?>.</p>
					<?php endif; ?>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php get_footer(); ?>
