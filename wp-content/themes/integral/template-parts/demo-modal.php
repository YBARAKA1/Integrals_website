<?php
/**
 * Interactive demo request modal
 */
$modules = integral_hmis_modules();
$types   = function_exists( 'integral_facility_types' ) ? integral_facility_types() : array();
?>
<div class="int-demo" data-demo-modal hidden>
	<div class="int-demo__backdrop" data-demo-close></div>
	<div class="int-demo__panel" role="dialog" aria-modal="true" aria-labelledby="demo-title">
		<button type="button" class="int-demo__x" data-demo-close aria-label="Close">&times;</button>

		<div class="int-demo__plan" data-demo-plan hidden>
			<div class="int-demo__plan-top">
				<span class="int-demo__plan-tag">Selected plan</span>
				<strong data-demo-plan-title>Cloud · Level 2</strong>
			</div>
			<div class="int-demo__plan-grid">
				<div>
					<small>Setup</small>
					<strong data-demo-plan-setup>KSh 25,000</strong>
				</div>
				<div>
					<small data-demo-plan-bill-label>Licence / quarter</small>
					<strong data-demo-plan-licence>KSh 20,000</strong>
				</div>
				<div>
					<small>Year-one</small>
					<strong data-demo-plan-total>KSh 105,000</strong>
				</div>
			</div>
			<p class="int-demo__plan-fx" data-demo-plan-fx hidden></p>
		</div>

		<div class="int-demo__progress" data-demo-progress>
			<span class="is-on"></span><span></span><span></span>
		</div>

		<div class="int-demo__step is-active" data-demo-step="1">
			<div class="int-eyebrow">Step 1</div>
			<h2 id="demo-title">Type of facility</h2>
			<p>Select your facility category, then look it up by FR code or CoC / KMPDC registration.</p>
			<div class="int-demo__roles">
				<?php foreach ( $types as $type ) : ?>
					<button type="button" class="int-demo__role" data-demo-facility-type="<?php echo esc_attr( $type ); ?>"><?php echo esc_html( $type ); ?></button>
				<?php endforeach; ?>
			</div>

			<form class="int-facility__form int-demo__lookup" data-facility-form data-demo-facility-form>
				<label for="demo-fr-code">Facility identifier</label>
				<div class="int-facility__row">
					<input id="demo-fr-code" name="code" type="text" placeholder="e.g. FID-12-345678-9 or 012345" autocomplete="off">
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

			<div class="int-demo__nav">
				<span></span>
				<button type="button" class="int-btn int-btn--primary" data-demo-next>Continue</button>
			</div>
		</div>

		<div class="int-demo__step" data-demo-step="2">
			<div class="int-eyebrow">Step 2</div>
			<h2>Which modules matter most?</h2>
			<p>Pick a few—your demo agenda follows.</p>
			<div class="int-demo__mods">
				<?php foreach ( $modules as $mod ) : ?>
					<label class="int-demo__mod">
						<input type="checkbox" name="modules[]" value="<?php echo esc_attr( $mod['title'] ); ?>">
						<span><?php echo esc_html( $mod['title'] ); ?></span>
					</label>
				<?php endforeach; ?>
			</div>
			<div class="int-demo__nav">
				<button type="button" class="int-btn int-btn--ghost" data-demo-back>Back</button>
				<button type="button" class="int-btn int-btn--primary" data-demo-next>Continue</button>
			</div>
		</div>

		<div class="int-demo__step" data-demo-step="3">
			<div class="int-eyebrow">Step 3</div>
			<h2>Contact details</h2>
			<form class="int-demo__form" data-demo-form>
				<label>Facility name</label>
				<input type="text" name="facility" data-demo-facility-name required placeholder="Hospital / clinic name">
				<label>Facility FR code</label>
				<input type="text" name="fr_code" data-demo-fr-code placeholder="e.g. FID-47-123456-0">
				<label>Your name</label>
				<input type="text" name="name" required>
				<label>Work email</label>
				<input type="email" name="email" required>
				<label>Phone</label>
				<input type="tel" name="phone">
				<input type="hidden" name="facility_type" data-demo-facility-type-field>
				<input type="hidden" name="modules" data-demo-modules-field>
				<input type="hidden" name="plan_mode" data-demo-plan-mode>
				<input type="hidden" name="plan_level" data-demo-plan-level>
				<input type="hidden" name="plan_billing" data-demo-plan-billing>
				<input type="hidden" name="plan_setup_ksh" data-demo-plan-setup-ksh>
				<input type="hidden" name="plan_licence_ksh" data-demo-plan-licence-ksh>
				<input type="hidden" name="plan_yearone_ksh" data-demo-plan-yearone-ksh>
				<input type="hidden" name="plan_summary" data-demo-plan-summary>
				<div class="int-demo__nav">
					<button type="button" class="int-btn int-btn--ghost" data-demo-back>Back</button>
					<button type="submit" class="int-btn int-btn--primary">Book my demo</button>
				</div>
			</form>
			<p class="int-demo__note">We’ll use your facility type, FR code, and module picks to shape the walkthrough.</p>
		</div>
	</div>
</div>
