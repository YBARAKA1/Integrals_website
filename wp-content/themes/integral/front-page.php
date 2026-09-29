<?php
/**
 * Home — HMIS-first interactive
 */
get_header();
$modules      = integral_hmis_modules();
$integrations = integral_hmis_integrations();
$first        = $modules[0];
?>

<section class="int-hero int-hero--hmis">
	<div class="int-hero__bg" aria-hidden="true"></div>
	<div class="int-hero__grid" aria-hidden="true"></div>
	<div class="int-hero__slash" aria-hidden="true"></div>
	<div class="int-hero__inner">
		<div class="int-hero__copy">
			<div class="int-hero__brand">Integral<em> HMIS</em></div>
			<h1>Hospital Management Information System</h1>
			<p>An integrated hospital management platform that connects clinical, financial, and administrative operations in one system—giving your hospital the control, visibility, and efficiency to deliver and extend health services.</p>
			<div class="int-hero__actions">
				<a class="int-btn int-btn--ghost" href="#presence">Our footprint</a>
			</div>
		</div>

		<div class="int-console" id="hmis-console" data-console>
			<div class="int-console__top">
				<div class="int-console__dots" aria-hidden="true"><i></i><i></i><i></i></div>
				<div class="int-console__title">Integral HMIS · Mission Control</div>
				<div class="int-console__live"><span></span> Live modules</div>
			</div>
			<div class="int-console__body">
				<aside class="int-console__rail" role="tablist" aria-label="HMIS modules">
					<?php foreach ( $modules as $i => $mod ) : ?>
						<button
							type="button"
							class="int-console__tab<?php echo $i === 0 ? ' is-active' : ''; ?>"
							role="tab"
							aria-selected="<?php echo $i === 0 ? 'true' : 'false'; ?>"
							data-module="<?php echo esc_attr( $mod['id'] ); ?>"
							data-title="<?php echo esc_attr( $mod['title'] ); ?>"
							data-blurb="<?php echo esc_attr( $mod['blurb'] ); ?>"
							data-kind="<?php echo esc_attr( ! empty( $mod['kind'] ) ? $mod['kind'] : 'kpis' ); ?>"
							data-panel="<?php echo esc_attr( wp_json_encode( $mod['panel'] ) ); ?>"
						>
							<span><?php echo esc_html( $mod['title'] ); ?></span>
						</button>
					<?php endforeach; ?>
				</aside>
				<div class="int-console__stage" role="tabpanel">
					<div class="int-console__stage-head">
						<h3 data-console-title><?php echo esc_html( $first['title'] ); ?></h3>
						<p data-console-blurb><?php echo esc_html( $first['blurb'] ); ?></p>
					</div>
					<div class="int-console__kpis" data-console-kpis>
						<?php foreach ( $first['panel'] as $kpi ) : ?>
							<div class="int-console__kpi">
								<strong data-kpi-pulse><?php echo esc_html( sprintf( '%d', wp_rand( 12, 98 ) ) ); ?></strong>
								<small><?php echo esc_html( $kpi ); ?></small>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="int-marquee" aria-label="Integrations">
	<div class="int-marquee__track">
		<?php
		$loop = array_merge( $integrations, $integrations );
		foreach ( $loop as $item ) :
			?>
			<span class="int-marquee__item"><strong><?php echo esc_html( $item['name'] ); ?></strong> <?php echo esc_html( $item['hint'] ); ?></span>
		<?php endforeach; ?>
	</div>
</section>

<section class="int-section" id="modules">
	<div class="int-container">
		<div class="int-section__head int-reveal">
			<div class="int-eyebrow">Full hospital stack</div>
			<h2>Not just modules. Your hospital operating system.</h2>
			<p>Click through the console above—or jump into any department Integral already covers.</p>
		</div>
		<div class="int-module-grid">
			<?php foreach ( $modules as $mod ) : ?>
				<?php if ( 'integrations' === $mod['id'] ) { continue; } ?>
				<button type="button" class="int-module-card int-reveal" data-jump-module="<?php echo esc_attr( $mod['id'] ); ?>">
					<span class="int-module-card__id"><?php echo esc_html( strtoupper( $mod['id'] ) ); ?></span>
					<h3><?php echo esc_html( $mod['title'] ); ?></h3>
					<p><?php echo esc_html( $mod['blurb'] ); ?></p>
					<span class="int-tile__go">Open in console →</span>
				</button>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="int-section int-section--slash" id="integrations">
	<div class="int-container">
		<div class="int-split int-reveal">
			<div>
				<div class="int-eyebrow">Integrations</div>
				<h2>Kenya ,Jamaica &amp; Malawi rails included.</h2>
				<p>SHA claims, MASM, M-Pesa, eTIMS, LIS, Bulk SMS, mailing, MRA, Smart, Slade—and an AI chatbot on DeepSeek APIs.</p>
				<a class="int-btn int-btn--ghost" href="<?php echo esc_url( home_url( '/softwares/#integrations' ) ); ?>">See integration map</a>
			</div>
			<div class="int-integ-board">
				<?php foreach ( $integrations as $item ) : ?>
					<div class="int-integ-chip" title="<?php echo esc_attr( $item['hint'] ); ?>">
						<strong><?php echo esc_html( $item['name'] ); ?></strong>
						<span><?php echo esc_html( $item['hint'] ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>

<section class="int-section int-certs" id="dha-certification">
	<div class="int-container">
		<div class="int-section__head int-reveal">
			<div class="int-eyebrow">Credentials</div>
			<h2>Certified for digital health. Registered for data protection.</h2>
			<p>Integral holds Kenya Digital Health Agency certification and is registered with the Office of the Data Protection Commissioner as a Data Processor.</p>
		</div>
		<?php
		$certs = integral_certifications();
		foreach ( $certs as $i => $cert ) {
			integral_render_certification( $cert, ( 1 === ( $i % 2 ) ) );
		}
		?>
	</div>
</section>

<section class="int-section int-presence" id="presence">
	<div class="int-presence__glow" aria-hidden="true"></div>
	<div class="int-container">
		<div class="int-section__head int-reveal">
			<div class="int-eyebrow">Presence</div>
			<h2>Built for hospitals. Proven across borders.</h2>
			<p>Integral HMIS runs where clinical and revenue operations can’t afford downtime.</p>
		</div>

		<div class="int-metrics int-reveal" data-metrics>
			<article class="int-metric" data-metric data-metric-tone="cyan" tabindex="0">
				<span class="int-metric__index" aria-hidden="true">01</span>
				<div class="int-metric__orb" aria-hidden="true"></div>
				<div class="int-metric__value">
					<span data-count-to="300" data-count-suffix="+">0</span>
				</div>
				<h3 class="int-metric__label">Healthcare facilities</h3>
				<p class="int-metric__detail">Facilities running Integral across clinical, billing, and national scheme workflows.</p>
				<span class="int-metric__rail" aria-hidden="true"></span>
			</article>

			<article class="int-metric int-metric--featured" data-metric data-metric-tone="purple" tabindex="0">
				<span class="int-metric__index" aria-hidden="true">02</span>
				<div class="int-metric__orb" aria-hidden="true"></div>
				<div class="int-metric__ring" aria-hidden="true"></div>
				<div class="int-metric__value">
					<span data-count-to="5">0</span>
				</div>
				<h3 class="int-metric__label">Countries</h3>
				<p class="int-metric__detail">Active deployments spanning East Africa and the Caribbean.</p>
				<ul class="int-metric__chips" aria-label="Countries">
					<li style="--i:0">Kenya</li>
					<li style="--i:1">Malawi</li>
					<li style="--i:2">Zambia</li>
					<li style="--i:3">Uganda</li>
					<li style="--i:4">Jamaica</li>
				</ul>
				<span class="int-metric__rail" aria-hidden="true"></span>
			</article>

			<article class="int-metric" data-metric data-metric-tone="violet" tabindex="0">
				<span class="int-metric__index" aria-hidden="true">03</span>
				<div class="int-metric__orb" aria-hidden="true"></div>
				<div class="int-metric__value">
					<span data-count-to="38">0</span>
				</div>
				<h3 class="int-metric__label">Kenyan counties</h3>
				<p class="int-metric__detail">County and private facilities covered nationwide—from referral hospitals to primary care.</p>
				<span class="int-metric__rail" aria-hidden="true"></span>
			</article>
		</div>
	</div>
</section>

<section class="int-section">
	<div class="int-container">
		<div class="int-cta int-cta--hot int-reveal">
			<div class="int-eyebrow" style="justify-content:center;">Demo theater</div>
			<h2>See Integral HMIS on your facility’s reality.</h2>
			<p>Interactive walkthrough for CEOs, IT, clinicians, and procurement—scoped to your modules.</p>
			<div class="int-cta__actions">
				<button type="button" class="int-btn int-btn--primary" data-demo-open>Start demo request</button>
				<a class="int-btn int-btn--ghost" href="<?php echo esc_url( home_url( '/softwares/' ) ); ?>">Deep dive HMIS</a>
			</div>
		</div>
	</div>
</section>

<?php get_footer(); ?>
