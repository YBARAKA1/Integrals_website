<?php
/**
 * Integral theme bootstrap.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'INTEGRAL_VERSION', '1.0.0' );
define( 'INTEGRAL_DIR', get_template_directory() );
define( 'INTEGRAL_URI', get_template_directory_uri() );

require_once INTEGRAL_DIR . '/inc/content.php';
require_once INTEGRAL_DIR . '/inc/facility-api.php';
require_once INTEGRAL_DIR . '/inc/mail.php';
require_once INTEGRAL_DIR . '/inc/leads.php';

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );
} );

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style(
		'integral-fonts',
		'https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&family=Syne:wght@500;600;700;800&display=swap',
		array(),
		null
	);
	wp_enqueue_style(
		'integral-main',
		INTEGRAL_URI . '/assets/css/main.css',
		array( 'integral-fonts' ),
		filemtime( INTEGRAL_DIR . '/assets/css/main.css' )
	);
	wp_enqueue_script(
		'integral-main',
		INTEGRAL_URI . '/assets/js/main.js',
		array(),
		filemtime( INTEGRAL_DIR . '/assets/js/main.js' ),
		true
	);
	wp_localize_script(
		'integral-main',
		'IntegralTheme',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'homeUrl' => home_url( '/' ),
			'demoUrl' => home_url( '/service-request-inquiry/' ),
		)
	);
}, 20 );

/**
 * Kill legacy scroll hijackers / unused builder assets on the front.
 */
add_action( 'wp_enqueue_scripts', function () {
	wp_dequeue_script( 'smoothscroll' );
	wp_deregister_script( 'smoothscroll' );

	// SmoothScroll plugin often registers under this handle too.
	global $wp_scripts;
	if ( $wp_scripts instanceof WP_Scripts ) {
		foreach ( $wp_scripts->registered as $handle => $script ) {
			$src = isset( $script->src ) ? $script->src : '';
			if ( $src && ( false !== strpos( $src, 'smoothscroll' ) || false !== strpos( $src, 'SmoothScroll' ) ) ) {
				wp_dequeue_script( $handle );
				wp_deregister_script( $handle );
			}
		}
	}

	wp_dequeue_script( 'gdlr-core-page-builder' );
	wp_dequeue_script( 'gdlr-core' );
	wp_dequeue_style( 'gdlr-core-page-builder' );
}, 100 );

/**
 * Prefer theme page templates by slug.
 */
add_filter( 'template_include', function ( $template ) {
	if ( is_front_page() ) {
		$custom = INTEGRAL_DIR . '/front-page.php';
		return file_exists( $custom ) ? $custom : $template;
	}

	if ( is_page() ) {
		$slug = get_post_field( 'post_name', get_queried_object_id() );
		$map  = array(
			'services'                  => 'page-services.php',
			'solutions'                 => 'page-services.php',
			'softwares'                 => 'page-softwares.php',
			'industries'                => 'page-industries.php',
			'contacts'                  => 'page-contacts.php',
			'service-request-inquiry'   => 'page-inquire.php',
			'pricing'                   => 'page-pricing.php',
			'about-us'                  => 'page-company.php',
			'our-methodology'           => 'page-company.php',
			'our-business-model'        => 'page-company.php',
			'our-people'                => 'page-company.php',
			'our-partners'              => 'page-company.php',
			'certifications'            => 'page-certifications.php',
			'careers'                   => 'page-careers.php',
		);
		if ( isset( $map[ $slug ] ) ) {
			$custom = INTEGRAL_DIR . '/' . $map[ $slug ];
			if ( file_exists( $custom ) ) {
				return $custom;
			}
		}
	}

	if ( is_single() ) {
		$custom = INTEGRAL_DIR . '/single.php';
		return file_exists( $custom ) ? $custom : $template;
	}

	return $template;
} );

function integral_logo_url() {
	$header = INTEGRAL_DIR . '/assets/logo-header.jpg';
	if ( file_exists( $header ) ) {
		return INTEGRAL_URI . '/assets/logo-header.jpg';
	}
	$path = WP_CONTENT_DIR . '/uploads/logomain.png';
	if ( file_exists( $path ) ) {
		return content_url( 'uploads/logomain.png' );
	}
	return INTEGRAL_URI . '/assets/logo.svg';
}

function integral_body_class_extra( $classes ) {
	$classes[] = 'integral-theme';
	return $classes;
}
add_filter( 'body_class', 'integral_body_class_extra' );

function integral_nav_is_active( $item ) {
	$current = trailingslashit( home_url( add_query_arg( array(), $GLOBALS['wp']->request ) ) );
	$target  = trailingslashit( $item['url'] );
	if ( $current === $target ) {
		return true;
	}
	if ( ! empty( $item['children'] ) ) {
		foreach ( $item['children'] as $child ) {
			if ( trailingslashit( $child['url'] ) === $current ) {
				return true;
			}
		}
	}
	$path = trim( parse_url( $item['url'], PHP_URL_PATH ), '/' );
	if ( $path && is_page( $path ) ) {
		return true;
	}
	return false;
}
