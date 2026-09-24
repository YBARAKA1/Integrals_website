<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
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
			<?php endforeach; ?>
		</nav>

		<div class="int-header__actions">
			<button class="int-menu-toggle" type="button" aria-label="Open menu" aria-expanded="false">
				<span></span><span></span><span></span>
			</button>
		</div>
	</div>
</header>

<div class="int-drawer" aria-hidden="true">
	<?php foreach ( integral_nav_tree() as $item ) : ?>
		<div class="int-drawer__group">
			<a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['label'] ); ?></a>
			<?php if ( ! empty( $item['children'] ) ) : ?>
				<?php foreach ( $item['children'] as $child ) : ?>
					<a class="int-drawer__child" href="<?php echo esc_url( $child['url'] ); ?>"><?php echo esc_html( $child['label'] ); ?></a>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
</div>

<main id="content">
