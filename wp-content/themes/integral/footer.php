</main>

<?php get_template_part( 'template-parts/demo-modal' ); ?>

<footer class="int-footer" role="contentinfo">
	<div class="int-container">
		<div class="int-footer__grid">
			<div>
				<div class="int-footer__brand">Integral<em> HMIS</em></div>
				<p>Hospital Management Information System plus digital consultancy—for facilities that need the whole stack, not a partial charting tool.</p>
			</div>
			<div>
				<h4>HMIS</h4>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/softwares/' ) ); ?>">Platform overview</a></li>
					<li><a href="<?php echo esc_url( home_url( '/softwares/#modules' ) ); ?>">Modules</a></li>
					<li><a href="<?php echo esc_url( home_url( '/softwares/#integrations' ) ); ?>">Integrations</a></li>
					<li><a href="<?php echo esc_url( home_url( '/softwares/#facility' ) ); ?>">FR code lookup</a></li>
					<li><button type="button" class="int-footer__link-btn" data-demo-open>Pricing</button></li>
				</ul>
			</div>
			<div>
				<h4>Company</h4>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/about-us/' ) ); ?>">Who We Are</a></li>
					<li><a href="<?php echo esc_url( home_url( '/our-methodology/' ) ); ?>">Methodology</a></li>
					<li><a href="<?php echo esc_url( home_url( '/certifications/' ) ); ?>">Certifications</a></li>
					<li><a href="<?php echo esc_url( home_url( '/careers/' ) ); ?>">Careers</a></li>
					<li><a href="<?php echo esc_url( home_url( '/contacts/' ) ); ?>">Contact</a></li>
				</ul>
			</div>
			<div>
				<h4>Connect</h4>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/service-request-inquiry/' ) ); ?>">Request demo</a></li>
					<li><a href="mailto:info@integral.co.ke">info@integral.co.ke</a></li>
					<li><a href="tel:+254720730430">+254 720 730 430</a></li>
					<li><a href="tel:+254790518958">+254 790 518 958</a></li>
				</ul>
			</div>
		</div>
		<div class="int-footer__bar">
			<span>&copy; <?php echo esc_html( date( 'Y' ) ); ?> Integral Software Technology</span>
			<span>Nairobi · Kenya</span>
		</div>
	</div>
</footer>

<div class="int-toast" data-toast hidden role="status" aria-live="polite">
	<span class="int-toast__msg" data-toast-message></span>
	<button type="button" class="int-toast__close" data-toast-close aria-label="Dismiss">&times;</button>
</div>

<?php wp_footer(); ?>
</body>
</html>
