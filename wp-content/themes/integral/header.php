<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script>
	(function () {
		try {
			if (localStorage.getItem("integral-theme") === "light") {
				document.documentElement.classList.add("int-theme-light");
			}
		} catch (e) {}
	})();
	</script>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php if ( function_exists( 'wp_body_open' ) ) { wp_body_open(); } ?>

<header class="int-header" role="banner">
	<div class="int-header__inner">
		<a class="int-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<img src="<?php echo esc_url( integral_logo_url() ); ?>" alt="Integral">
		</a>

		<nav class="int-nav" aria-label="Primary">
			<?php foreach ( integral_nav_tree() as $item ) : ?>
				<?php if ( ! empty( $item['action'] ) && 'demo' === $item['action'] ) : ?>
					<?php if ( ! empty( $item['cta'] ) ) : ?>
					<div class="int-nav__item int-nav__item--cta">
						<button type="button" class="int-nav__cta" data-demo-open><?php echo esc_html( $item['label'] ); ?></button>
					</div>
					<?php else : ?>
					<div class="int-nav__item">
						<button type="button" class="int-nav__link int-nav__link--btn" data-demo-open><?php echo esc_html( $item['label'] ); ?></button>
					</div>
					<?php endif; ?>
				<?php else : ?>
				<div class="int-nav__item">
					<a class="int-nav__link<?php echo integral_nav_is_active( $item ) ? ' is-active' : ''; ?>" href="<?php echo esc_url( $item['url'] ); ?>">
						<?php echo esc_html( $item['label'] ); ?>
						<?php if ( ! empty( $item['children'] ) ) : ?>
							<span class="int-nav__chevron" aria-hidden="true"></span>
						<?php endif; ?>
					</a>
					<?php if ( ! empty( $item['children'] ) ) : ?>
						<div class="int-nav__dropdown" role="menu">
							<?php foreach ( $item['children'] as $child ) : ?>
								<a href="<?php echo esc_url( $child['url'] ); ?>"><?php echo esc_html( $child['label'] ); ?></a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
				<?php endif; ?>
			<?php endforeach; ?>
		</nav>

		<div class="int-header__actions">
			<button
				type="button"
				class="int-theme-toggle"
				data-theme-toggle
				aria-label="Switch to white mode"
				title="White mode"
			>
				<span class="int-theme-toggle__icon int-theme-toggle__icon--sun" aria-hidden="true">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="1.75"/><path d="M12 2v2.5M12 19.5V22M4.93 4.93l1.77 1.77M17.3 17.3l1.77 1.77M2 12h2.5M19.5 12H22M4.93 19.07l1.77-1.77M17.3 6.7l1.77-1.77" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/></svg>
				</span>
				<span class="int-theme-toggle__icon int-theme-toggle__icon--moon" aria-hidden="true">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M21 14.5A8.5 8.5 0 0 1 9.5 3 7 7 0 1 0 21 14.5Z" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round"/></svg>
				</span>
			</button>
			<button class="int-menu-toggle" type="button" aria-label="Open menu" aria-expanded="false">
				<span></span><span></span><span></span>
			</button>
		</div>
	</div>
</header>

<div class="int-drawer" aria-hidden="true">
	<?php foreach ( integral_nav_tree() as $item ) : ?>
		<?php if ( ! empty( $item['action'] ) && 'demo' === $item['action'] ) : ?>
			<?php if ( ! empty( $item['cta'] ) ) : ?>
			<div class="int-drawer__group">
				<button type="button" class="int-drawer__cta" data-demo-open data-drawer-close><?php echo esc_html( $item['label'] ); ?></button>
			</div>
			<?php else : ?>
			<div class="int-drawer__group">
				<button type="button" class="int-drawer__link-btn" data-demo-open data-drawer-close><?php echo esc_html( $item['label'] ); ?></button>
			</div>
			<?php endif; ?>
		<?php else : ?>
		<div class="int-drawer__group">
			<a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['label'] ); ?></a>
			<?php if ( ! empty( $item['children'] ) ) : ?>
				<?php foreach ( $item['children'] as $child ) : ?>
					<a class="int-drawer__child" href="<?php echo esc_url( $child['url'] ); ?>"><?php echo esc_html( $child['label'] ); ?></a>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	<?php endforeach; ?>
</div>

<main id="content">
