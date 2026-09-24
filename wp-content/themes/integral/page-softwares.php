<?php
/**
 * Softwares / HMIS deep dive
 */
get_header();
$modules      = integral_hmis_modules();
$integrations = integral_hmis_integrations();
?>

<section class="int-page-hero int-page-hero--hmis">
	<div class="int-container">
		<div class="int-eyebrow">Integral HMIS</div>
		<h1>Hospital Management Information System.</h1>
		<p class="int-lead">One platform for clinical, administrative, and financial operations—with SHA, MASM, M-Pesa, eTIMS, LIS, SMS, AI, and more already on the rails.</p>
		<div class="int-hero__actions" style="margin-top:1.5rem;">
			<button type="button" class="int-btn int-btn--primary" data-demo-open>Request a demo</button>
			<a class="int-btn int-btn--ghost" href="#facility">Lookup facility</a>
		</div>
	</div>
</section>

<section class="int-section" id="modules">
	<div class="int-container">
		<div class="int-section__head int-reveal">
			<div class="int-eyebrow">Modules</div>
			<h2>Every department. One source of truth.</h2>
		</div>
		<div class="int-module-grid">
			<?php foreach ( $modules as $mod ) : ?>
				<article class="int-module-card int-reveal" id="mod-<?php echo esc_attr( $mod['id'] ); ?>">
					<span class="int-module-card__id"><?php echo esc_html( strtoupper( $mod['id'] ) ); ?></span>
					<h3><?php echo esc_html( $mod['title'] ); ?></h3>
					<p><?php echo esc_html( $mod['blurb'] ); ?></p>
					<ul class="int-module-card__list">
						<?php foreach ( $mod['panel'] as $item ) : ?>
							<li><?php echo esc_html( $item ); ?></li>
						<?php endforeach; ?>
					</ul>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="int-section int-section--slash" id="integrations">
	<div class="int-container">
		<div class="int-section__head int-reveal">
			<div class="int-eyebrow">Integrations</div>
			<h2>National schemes. Payments. Labs. AI.</h2>
			<p>Built for Kenya and Malawi operating reality—not bolted-on afterthoughts.</p>
		</div>
		<div class="int-integ-board int-integ-board--wide int-reveal">
			<?php foreach ( $integrations as $item ) : ?>
				<div class="int-integ-chip">
					<strong><?php echo esc_html( $item['name'] ); ?></strong>
					<span><?php echo esc_html( $item['hint'] ); ?></span>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="int-section" id="facility">
	<div class="int-container">
		<div class="int-facility int-reveal">
			<div class="int-facility__copy">
				<div class="int-eyebrow">Facility level</div>
				<h2>Search by FR code or registration</h2>
				<p>Live SHA / DHA facility registry—look up by FR code (FID-…) or CoC / KMPDC registration number.</p>
			</div>
			<form class="int-facility__form" data-facility-form>
				<label for="fr-code-soft">Facility identifier</label>
				<div class="int-facility__row">
					<input id="fr-code-soft" name="code" type="text" placeholder="e.g. FID-12-345678-9 or 012345" required>
					<select name="type" aria-label="Identifier type">
						<option value="fr-code" selected>FR Code (Facility Registry Code)</option>
						<option value="fid">FID (Facility ID)</option>
						<option value="registration-number">Registration Number</option>
						<option value="auto">Auto-detect</option>
					</select>
					<button class="int-btn int-btn--primary" type="submit">Lookup</button>
				</div>
				<p class="int-facility__hint" data-facility-status>Same SHA / Afyalink registry search as Integral HMIS institution setup</p>
				<div class="int-facility__result" data-facility-result hidden></div>
			</form>
		</div>
	</div>
</section>

<section class="int-section">
	<div class="int-container">
		<div class="int-cta int-cta--hot int-reveal">
			<h2>Ready to see it on your wards?</h2>
			<p>Interactive demo for leadership, IT, clinicians, and revenue teams.</p>
			<div class="int-cta__actions">
				<button type="button" class="int-btn int-btn--primary" data-demo-open>Request demo</button>
			</div>
		</div>
	</div>
</section>

<?php get_footer(); ?>
