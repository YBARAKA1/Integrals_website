<?php
/**
 * Marketing contacts — save email + mobile from quote forms for later outreach.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'integral_register_marketing_contact_cpt' );

function integral_register_marketing_contact_cpt() {
	register_post_type(
		'integral_lead',
		array(
			'labels'              => array(
				'name'          => 'Marketing contacts',
				'singular_name' => 'Marketing contact',
				'menu_name'     => 'Marketing contacts',
				'add_new_item'  => 'Add contact',
				'edit_item'     => 'Edit contact',
				'search_items'  => 'Search contacts',
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 26,
			'menu_icon'           => 'dashicons-email-alt',
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'hierarchical'        => false,
			'supports'            => array( 'title' ),
			'has_archive'         => false,
			'exclude_from_search' => true,
		)
	);
}

/**
 * Persist email + mobile (and related quote context) for marketing follow-up.
 *
 * @param array $fields Quotation / contact fields.
 * @return int|false Post ID or false.
 */
function integral_save_marketing_contact( array $fields ) {
	$email = isset( $fields['email'] ) ? sanitize_email( $fields['email'] ) : '';
	$phone = isset( $fields['phone'] ) ? sanitize_text_field( $fields['phone'] ) : '';
	if ( ! is_email( $email ) && $phone === '' ) {
		return false;
	}

	$facility = isset( $fields['facility'] ) ? sanitize_text_field( $fields['facility'] ) : '';
	$title    = $facility !== '' ? $facility : ( $email ? $email : $phone );
	$title   .= ' — ' . current_time( 'Y-m-d H:i' );

	$existing = null;
	if ( is_email( $email ) ) {
		$found = get_posts(
			array(
				'post_type'      => 'integral_lead',
				'post_status'    => 'private',
				'posts_per_page' => 1,
				'meta_key'       => '_integral_email',
				'meta_value'     => $email,
				'fields'         => 'ids',
			)
		);
		if ( ! empty( $found[0] ) ) {
			$existing = (int) $found[0];
		}
	}

	$postarr = array(
		'post_type'   => 'integral_lead',
		'post_status' => 'private',
		'post_title'  => $title,
	);

	if ( $existing ) {
		$postarr['ID'] = $existing;
		$post_id       = wp_update_post( $postarr, true );
	} else {
		$post_id = wp_insert_post( $postarr, true );
	}

	if ( is_wp_error( $post_id ) || ! $post_id ) {
		return false;
	}

	$meta = array(
		'_integral_email'         => $email,
		'_integral_phone'         => $phone,
		'_integral_facility'      => $facility,
		'_integral_fr_code'       => isset( $fields['fr_code'] ) ? sanitize_text_field( $fields['fr_code'] ) : '',
		'_integral_facility_type' => isset( $fields['facility_type'] ) ? sanitize_text_field( $fields['facility_type'] ) : '',
		'_integral_level'         => isset( $fields['level'] ) ? (string) $fields['level'] : '',
		'_integral_quote_no'      => isset( $fields['quote_no'] ) ? sanitize_text_field( $fields['quote_no'] ) : '',
		'_integral_source'        => isset( $fields['source'] ) ? sanitize_text_field( $fields['source'] ) : 'quotation',
		'_integral_updated'       => current_time( 'mysql' ),
	);
	foreach ( $meta as $key => $value ) {
		update_post_meta( $post_id, $key, $value );
	}

	return (int) $post_id;
}

add_filter( 'manage_integral_lead_posts_columns', function ( $cols ) {
	return array(
		'cb'       => isset( $cols['cb'] ) ? $cols['cb'] : '<input type="checkbox" />',
		'title'    => 'Contact',
		'email'    => 'Email',
		'phone'    => 'Mobile',
		'facility' => 'Facility',
		'date'     => 'Date',
	);
} );

add_action(
	'manage_integral_lead_posts_custom_column',
	function ( $col, $post_id ) {
		if ( 'email' === $col ) {
			echo esc_html( (string) get_post_meta( $post_id, '_integral_email', true ) );
		} elseif ( 'phone' === $col ) {
			echo esc_html( (string) get_post_meta( $post_id, '_integral_phone', true ) );
		} elseif ( 'facility' === $col ) {
			echo esc_html( (string) get_post_meta( $post_id, '_integral_facility', true ) );
		}
	},
	10,
	2
);
