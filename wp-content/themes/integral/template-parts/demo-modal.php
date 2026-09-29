<?php
/**
 * Quote / demo request drawer
 */
?>
<div class="int-demo" data-demo-modal hidden>
	<div class="int-demo__backdrop" data-demo-close></div>
	<div class="int-demo__panel" role="dialog" aria-modal="true" aria-labelledby="demo-title">
		<button type="button" class="int-demo__x" data-demo-close aria-label="Close quote form">&times;</button>

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
			<span class="is-on"></span><span></span>
		</div>

		<div class="int-demo__step is-active" data-demo-step="1">
			<div class="int-eyebrow">Step 1</div>
			<h2 id="demo-title">Facility Lookup</h2>
			<p class="int-demo__intro">Look up your facility using your FR Code, FID, or Registration Number. If you don’t have these details, click Continue to proceed.</p>

			<form class="int-facility__form int-demo__lookup" data-facility-form data-demo-facility-form>
				<input type="hidden" name="type" value="auto">

				<label class="int-facility__code-label" for="demo-fr-code">Identifier Value</label>
				<input id="demo-fr-code" name="code" type="text" placeholder="FID-xx-xxxxxx-x" autocomplete="off" required>
				<p class="int-facility__hint" data-facility-status>Lookup by FR Code, FID, Registration Number</p>

				<div class="int-demo__nav int-demo__nav--lookup">
					<button class="int-btn int-btn--lookup int-facility__search" type="submit">
						<svg class="int-btn__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
							<circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/>
							<path d="M20 20l-3.5-3.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
						</svg>
						<span class="int-btn__spinner" aria-hidden="true"></span>
						<span>Lookup</span>
					</button>
					<button type="button" class="int-btn int-btn--continue" data-demo-next>
						<span>Continue</span>
						<svg class="int-btn__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
							<path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</button>
				</div>

				<div class="int-facility__result" data-facility-result hidden></div>
			</form>
		</div>

		<div class="int-demo__step" data-demo-step="2">
			<div class="int-eyebrow">Step 2</div>
			<h2>Contact details</h2>
			<p class="int-demo__intro">Share your email and mobile so we can send your quotation—and reach you later with Integral updates and mobile marketing.</p>
			<form class="int-demo__form" data-demo-form>
				<label>Facility name</label>
				<input type="text" name="facility" data-demo-facility-name required placeholder="Hospital / clinic name">
				<label>Email</label>
				<input type="email" name="email" data-demo-email required placeholder="you@facility.co.ke">
				<p class="int-demo__field-note">We save your email from this form for quotation delivery, follow-up, and future marketing.</p>
				<label>Mobile phone</label>
				<input type="tel" name="phone" data-demo-phone required placeholder="+254 7…" autocomplete="tel">
				<p class="int-demo__field-note">We save mobile numbers from this form to follow up on your request and for future mobile marketing.</p>
				<label for="demo-message">Message</label>
				<textarea id="demo-message" name="message" data-demo-message rows="3" placeholder="Optional notes for your quotation…"></textarea>
				<input type="hidden" name="fr_code" data-demo-fr-code>
				<input type="hidden" name="facility_type" data-demo-facility-type-field>
				<input type="hidden" name="facility_level" data-demo-facility-level>
				<input type="hidden" name="facility_snapshot" data-demo-facility-snapshot value="">
				<input type="hidden" name="modules" data-demo-modules-field value="">
				<input type="hidden" name="plan_mode" data-demo-plan-mode>
				<input type="hidden" name="plan_level" data-demo-plan-level>
				<input type="hidden" name="plan_billing" data-demo-plan-billing>
				<input type="hidden" name="plan_setup_ksh" data-demo-plan-setup-ksh>
				<input type="hidden" name="plan_licence_ksh" data-demo-plan-licence-ksh>
				<input type="hidden" name="plan_yearone_ksh" data-demo-plan-yearone-ksh>
				<input type="hidden" name="plan_summary" data-demo-plan-summary>
				<div class="int-demo__nav">
					<button type="button" class="int-btn int-btn--ghost" data-demo-back>Back</button>
					<button type="submit" class="int-btn int-btn--primary int-btn--submit" data-demo-submit>
						<span class="int-btn__spinner" aria-hidden="true"></span>
						<span>Submit</span>
					</button>
				</div>
				<p class="int-demo__alert" data-demo-alert hidden role="status" aria-live="polite"></p>
			</form>
			<p class="int-demo__note">We’ll email an official Integral Quotation for your facility level (Cloud and On Premise options). Your email and mobile are stored for quotation follow-up and marketing.</p>
		</div>
	</div>
</div>
