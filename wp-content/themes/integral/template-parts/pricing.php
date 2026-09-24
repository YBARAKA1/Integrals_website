<?php
/**
 * Interactive HMIS pricing board.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$pricing = integral_pricing();
$cloud   = $pricing['modes']['cloud'];
$onprem  = $pricing['modes']['onprem'];
$levels  = $cloud['levels'];
$rates   = $pricing['rates'];
?>
<div class="int-pricing" data-pricing id="pricing">
	<div class="int-pricing__stage">
		<div class="int-pricing__hud">
			<div class="int-pricing__hud-top">
				<span class="int-pricing__live"><i></i> Live quote</span>
				<div class="int-pricing__fx" role="group" aria-label="Display currency">
					<button type="button" class="is-on" data-pricing-fx="kes">KSh</button>
					<button type="button" data-pricing-fx="usd">USD</button>
					<button type="button" data-pricing-fx="eur">EUR</button>
				</div>
			</div>

			<div class="int-pricing__modes" role="tablist" aria-label="Deployment">
				<button type="button" class="int-pricing__mode is-on" role="tab" aria-selected="true" data-pricing-mode="cloud">
					<span class="int-pricing__mode-kicker">Online</span>
					<strong>Cloud</strong>
					<small data-mode-blurb-cloud><?php echo esc_html( $cloud['blurb'] ); ?></small>
				</button>
				<button type="button" class="int-pricing__mode" role="tab" aria-selected="false" data-pricing-mode="onprem">
					<span class="int-pricing__mode-kicker">Offline</span>
					<strong>On Premise</strong>
					<small data-mode-blurb-onprem><?php echo esc_html( $onprem['blurb'] ); ?></small>
				</button>
			</div>

			<div class="int-pricing__bill-row">
				<span class="int-pricing__bill-label">Licence</span>
				<div class="int-pricing__billing" role="group" aria-label="Licence billing">
					<button type="button" class="is-on" data-pricing-bill="quarterly">Quarterly</button>
					<button type="button" data-pricing-bill="yearly">Yearly</button>
					<span class="int-pricing__billing-thumb" aria-hidden="true" data-billing-thumb></span>
				</div>
			</div>

			<div class="int-pricing__summary" data-pricing-summary>
				<div class="int-pricing__summary-meta">
					<span data-summary-mode>Cloud · Online</span>
					<strong data-summary-level>Level 2</strong>
				</div>
				<div class="int-pricing__summary-figures">
					<div>
						<small>Setup</small>
						<strong><em data-summary-code>KSh</em> <span data-summary-setup>50,000</span></strong>
					</div>
					<div>
						<small data-summary-bill-label>Licence / quarter</small>
						<strong><em data-summary-code>KSh</em> <span data-summary-licence>20,000</span></strong>
					</div>
					<div class="int-pricing__summary-total">
						<small>Year-one</small>
						<strong><em data-summary-code>KSh</em> <span data-summary-total>130,000</span></strong>
					</div>
				</div>
				<span class="int-pricing__summary-save" data-summary-save hidden></span>
				<button type="button" class="int-btn int-btn--primary" data-pricing-cta>
					Request this plan
				</button>
			</div>
		</div>

		<div class="int-pricing__levels" role="tablist" aria-label="Facility level">
			<?php foreach ( $levels as $i => $tier ) : ?>
				<button
					type="button"
					class="int-pricing__level-btn<?php echo 0 === $i ? ' is-on' : ''; ?>"
					role="tab"
					aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>"
					data-pricing-level="<?php echo esc_attr( $tier['level'] ); ?>"
				>
					<span>0<?php echo esc_html( $tier['level'] ); ?></span>
					<small>Level <?php echo esc_html( $tier['level'] ); ?></small>
				</button>
			<?php endforeach; ?>
		</div>
	</div>

	<div class="int-pricing__includes">
		<div>
			<strong>Setup includes</strong>
			<span>Installation · Configuration · Implementation · Training</span>
		</div>
		<div>
			<strong>Payment</strong>
			<span>Setup and licence payable in advance</span>
		</div>
		<div>
			<strong>Licence</strong>
			<span>Billed quarterly or annually</span>
		</div>
	</div>

	<ul class="int-pricing__notes">
		<?php foreach ( $pricing['notes'] as $note ) : ?>
			<li><?php echo esc_html( $note ); ?></li>
		<?php endforeach; ?>
	</ul>

	<script type="application/json" data-pricing-data>
		<?php
		echo wp_json_encode(
			array(
				'rates' => $rates,
				'modes' => array(
					'cloud'  => array(
						'blurb'  => $cloud['blurb'],
						'label'  => 'Cloud · Online',
						'levels' => $cloud['levels'],
					),
					'onprem' => array(
						'blurb'  => $onprem['blurb'],
						'label'  => 'On Premise · Offline',
						'levels' => $onprem['levels'],
					),
				),
			)
		);
		?>
	</script>
</div>
