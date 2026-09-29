<?php
/**
 * Live facility lookup — same as Integral HMIS institution setup.
 *
 * Primary (when SHA HIE credentials are set):
 *   POST {base}/api/v1/tenants/token
 *   GET  {base}/api/v1/facilities/search?identifier=&identifier-type=
 *   identifier-type: fr-code | fid | registration-number
 *
 * Fallbacks: SHA guest portal (registration), DHA FHIR + FR cache (FR codes).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_ajax_integral_facility_lookup', 'integral_facility_lookup' );
add_action( 'wp_ajax_nopriv_integral_facility_lookup', 'integral_facility_lookup' );

function integral_cfg( $key, $default = '' ) {
	if ( defined( $key ) && constant( $key ) ) {
		return constant( $key );
	}
	$env = getenv( $key );
	return $env ? $env : $default;
}

function integral_facility_lookup() {
	$code = isset( $_REQUEST['code'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['code'] ) ) : '';
	$type = isset( $_REQUEST['type'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['type'] ) ) : '';

	$code = trim( $code );
	if ( $code === '' ) {
		wp_send_json_error( array( 'message' => 'Enter a facility identifier to search.' ), 400 );
	}

	if ( strlen( $code ) < 3 ) {
		wp_send_json_error( array( 'message' => 'Enter at least 3 characters.' ), 400 );
	}

	// Name search is not offered — only FR code, FID, or registration.
	if ( $type === 'name' ) {
		$type = 'auto';
	}

	$tried = array();
	$result = null;

	if ( $type === '' || $type === 'auto' ) {
		// Same options as HMIS Identifier Type select — try each until one hits.
		foreach ( integral_auto_facility_search_attempts( $code ) as $attempt ) {
			$tried[] = $attempt['type'] . ':' . $attempt['code'];
			$hit     = integral_fetch_facility( $attempt['code'], $attempt['type'] );
			if ( ! is_wp_error( $hit ) && is_array( $hit ) && ! empty( $hit['name'] ) ) {
				$result              = $hit;
				$result['matchedType'] = $attempt['type'];
				$result['matchedCode'] = $attempt['code'];
				break;
			}
		}
	} else {
		$lookup_code = integral_normalize_code_for_type( $code, $type );
		$tried[]     = $type . ':' . $lookup_code;
		$hit         = integral_fetch_facility( $lookup_code, $type );
		if ( ! is_wp_error( $hit ) && is_array( $hit ) && ! empty( $hit['name'] ) ) {
			$result                = $hit;
			$result['matchedType'] = $type;
			$result['matchedCode'] = $lookup_code;
		}
	}

	if ( ! $result ) {
		wp_send_json_error(
			array(
				'message' => 'No facility found for that identifier. Tried: ' . implode( ', ', $tried ),
				'tried'   => $tried,
			),
			404
		);
	}

	$result['registryText'] = integral_facility_registry_text( $result );
	wp_send_json_success( $result );
}

/**
 * Auto-detect: try every HMIS identifier-type option (and sensible variants).
 * Order mirrors Institution → Facility Registry Search select.
 *
 * @return array<int, array{type:string,code:string}>
 */
function integral_auto_facility_search_attempts( $code ) {
	$code = trim( $code );
	$seen = array();
	$out  = array();

	$push = function ( $type, $value ) use ( &$seen, &$out ) {
		$value = trim( (string) $value );
		if ( $value === '' ) {
			return;
		}
		$key = $type . '|' . strtoupper( $value );
		if ( isset( $seen[ $key ] ) ) {
			return;
		}
		$seen[ $key ] = true;
		$out[]        = array(
			'type' => $type,
			'code' => $value,
		);
	};

	// 1) FR Code — full string (and uppercase FID- form).
	$push( 'fr-code', $code );
	if ( preg_match( '/^FID[-_]/i', $code ) ) {
		$push( 'fr-code', strtoupper( $code ) );
	}

	// 2) FID — raw value, or middle segment of FID-xx-yyyy-z.
	$push( 'fid', integral_normalize_code_for_type( $code, 'fid' ) );
	if ( preg_match( '/^FID[-_]\d+[-_](\d+)[-_]\d+$/i', $code, $m ) ) {
		$push( 'fid', $m[1] );
	}

	// 3) Registration Number.
	$push( 'registration-number', $code );
	if ( preg_match( '/^FID[-_]\d+[-_](\d+)[-_]\d+$/i', $code, $m ) ) {
		$push( 'registration-number', $m[1] );
	}

	return $out;
}

function integral_normalize_code_for_type( $code, $type ) {
	$code = trim( $code );
	if ( $type === 'fid' && preg_match( '/^FID[-_]\d+[-_](\d+)[-_]\d+$/i', $code, $m ) ) {
		return $m[1];
	}
	if ( $type === 'fr-code' && preg_match( '/^FID[-_]/i', $code ) ) {
		return strtoupper( $code );
	}
	return $code;
}

/**
 * Terminal-style block matching HMIS #shaDataOrganization output.
 */
function integral_facility_registry_text( $data ) {
	if ( ! is_array( $data ) ) {
		return '';
	}
	$lines = array(
		'FR CODE: ' . ( isset( $data['frCode'] ) ? $data['frCode'] : '' ),
		'FID CODE: ' . ( isset( $data['fidCode'] ) ? $data['fidCode'] : '' ),
		'FACILITY NAME: ' . ( isset( $data['name'] ) ? $data['name'] : '' ),
		'FACILITY TYPE: ' . ( isset( $data['facilityType'] ) ? $data['facilityType'] : '' ),
		'OWNERSHIP: ' . ( isset( $data['ownership'] ) ? $data['ownership'] : '' ),
		'REGULATOR: ' . ( isset( $data['regulator'] ) ? $data['regulator'] : '' ),
		'REGISTRATION NO: ' . ( isset( $data['registrationNumber'] ) ? $data['registrationNumber'] : '' ),
		'LICENSE NO: ' . ( isset( $data['licenseNumber'] ) ? $data['licenseNumber'] : '' ),
		'LICENSE STATUS: ' . ( isset( $data['licenseStatus'] ) ? $data['licenseStatus'] : '' ),
		'LICENSE END: ' . ( isset( $data['licenseEnd'] ) ? $data['licenseEnd'] : '' ),
		'LEVEL: ' . ( isset( $data['level'] ) ? $data['level'] : '' ),
		'COUNTY: ' . ( isset( $data['county'] ) ? $data['county'] : '' ),
		'SUB COUNTY: ' . ( isset( $data['subCounty'] ) ? $data['subCounty'] : '' ),
		'TOWN: ' . ( isset( $data['town'] ) ? $data['town'] : '' ),
		'PHONE: ' . ( isset( $data['phone'] ) ? $data['phone'] : '' ),
		'EMAIL: ' . ( isset( $data['email'] ) ? $data['email'] : '' ),
		'ADMIN: ' . ( isset( $data['admin'] ) ? $data['admin'] : '' ),
		'BEDS: ' . ( isset( $data['totalBeds'] ) ? $data['totalBeds'] : '' ),
		'SHA STATUS: ' . ( isset( $data['shaStatus'] ) ? $data['shaStatus'] : '' ),
		'SOURCE: ' . ( isset( $data['source'] ) ? $data['source'] : '' ),
		'MATCHED TYPE: ' . ( isset( $data['matchedType'] ) ? $data['matchedType'] : '' ),
		'-------------------------------',
	);
	return implode( "\n", $lines );
}

function integral_detect_facility_id_type( $code ) {
	$c = strtoupper( trim( $code ) );
	if ( preg_match( '/^FID[-_]/d', $c ) ) {
		return 'fr-code';
	}
	// Short pure numeric → FID (same as institution setup “FID” option).
	if ( preg_match( '/^\d{3,8}$/', $c ) ) {
		return 'fid';
	}
	// Padded / alphanumeric CoC / KMPDC registration numbers.
	return 'registration-number';
}

/**
 * Map UI type → SHA portal guest-search body key (dashes → underscores).
 */
function integral_sha_portal_body_key( $type ) {
	$map = array(
		'name'                 => 'name',
		'fr-code'              => 'fr_code',
		'fid'                  => 'fid',
		'registration-number'  => 'registration_number',
		'slade-code'           => 'slade_code',
	);
	return isset( $map[ $type ] ) ? $map[ $type ] : 'registration_number';
}

function integral_fr_cache_path() {
	return trailingslashit( ABSPATH ) . '.facility-fr-cache.json';
}

function integral_fr_cache_get( $code ) {
	$path = integral_fr_cache_path();
	if ( ! is_readable( $path ) ) {
		return null;
	}
	$raw = file_get_contents( $path );
	if ( ! $raw ) {
		return null;
	}
	$data = json_decode( $raw, true );
	if ( ! is_array( $data ) ) {
		return null;
	}
	$key = strtoupper( trim( $code ) );
	if ( empty( $data[ $key ] ) || ! is_array( $data[ $key ] ) ) {
		return null;
	}
	$hit = $data[ $key ];
	if ( empty( $hit['name'] ) ) {
		return null;
	}
	if ( empty( $hit['source'] ) ) {
		$hit['source'] = 'fr-cache';
	}
	return $hit;
}

function integral_fr_cache_put( $facility ) {
	if ( ! is_array( $facility ) || empty( $facility['frCode'] ) || empty( $facility['name'] ) ) {
		return;
	}
	$path = integral_fr_cache_path();
	$data = array();
	if ( is_readable( $path ) ) {
		$raw = file_get_contents( $path );
		$decoded = $raw ? json_decode( $raw, true ) : null;
		if ( is_array( $decoded ) ) {
			$data = $decoded;
		}
	}
	$key          = strtoupper( trim( $facility['frCode'] ) );
	$data[ $key ] = $facility;
	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
	@file_put_contents( $path, wp_json_encode( $data ) );
}

function integral_proxy_fr_lookup( $code ) {
	$base = rtrim( integral_cfg( 'INTEGRAL_SHA_PORTAL_PROXY', 'http://host.docker.internal:18787/sha/facilities' ), '/' );
	// Proxy FR route lives next to /sha/facilities → http://host:18787/fr?code=
	$root = preg_replace( '#/sha/facilities/?$#', '', $base );
	$url  = $root . '/fr?code=' . rawurlencode( $code );

	$response = wp_remote_get(
		$url,
		array(
			'timeout' => 25,
			'headers' => array( 'Accept' => 'application/json' ),
		)
	);
	if ( is_wp_error( $response ) ) {
		return null;
	}
	$status = wp_remote_retrieve_response_code( $response );
	$raw    = wp_remote_retrieve_body( $response );
	if ( $status < 200 || $status >= 300 || ! $raw ) {
		return null;
	}
	$data = json_decode( $raw, true );
	if ( ! is_array( $data ) || empty( $data['name'] ) ) {
		return null;
	}
	return $data;
}

function integral_fetch_facility( $code, $type = 'fr-code' ) {
	// 1) Same path as Institution → Facility Registry Search (SHA HIE middleware).
	$hie = integral_hie_facility_search( $code, $type );
	if ( $hie ) {
		$hie['source'] = 'sha-hie';
		integral_fr_cache_put( $hie );
		return $hie;
	}

	// 2) CoC / KMPDC registration → SHA provider portal guest search.
	if ( $type === 'registration-number' ) {
		$portal = integral_sha_portal_facility_lookup( $code, 'registration-number' );
		if ( $portal ) {
			$portal['source'] = 'sha-portal';
			integral_fr_cache_put( $portal );
			return $portal;
		}
		$fhir = integral_fhir_facility_lookup( $code, 'registration-number' );
		if ( $fhir ) {
			$fhir['source'] = 'dha-fhir';
			integral_fr_cache_put( $fhir );
			return $fhir;
		}
	}

	// 3) FR / FID without HIE credentials → cache + DHA FHIR.
	if ( in_array( $type, array( 'fr-code', 'fid' ), true ) || preg_match( '/^FID[-_]/i', $code ) ) {
		$cached = integral_fr_cache_get( $code );
		if ( $cached ) {
			return $cached;
		}

		$via_proxy = integral_proxy_fr_lookup( $code );
		if ( $via_proxy ) {
			integral_fr_cache_put( $via_proxy );
			return $via_proxy;
		}

		$fhir = integral_fhir_facility_lookup( $code, 'fr-code' );
		if ( $fhir ) {
			$fhir['source'] = 'dha-fhir';
			integral_fr_cache_put( $fhir );
			return $fhir;
		}

		if ( preg_match( '/^FID[-_]\d+[-_](\d+)[-_]\d+$/i', $code, $m ) ) {
			$fhir_mid = integral_fhir_facility_lookup( $m[1], 'fid' );
			if ( $fhir_mid ) {
				$fhir_mid['source'] = 'dha-fhir';
				if ( empty( $fhir_mid['frCode'] ) ) {
					$fhir_mid['frCode'] = strtoupper( $code );
				}
				integral_fr_cache_put( $fhir_mid );
				return $fhir_mid;
			}
		}

		// FID-only numeric: try FHIR identifier, then SHA portal won't help.
		if ( $type === 'fid' ) {
			$fhir_fid = integral_fhir_facility_lookup( $code, 'fid' );
			if ( $fhir_fid ) {
				$fhir_fid['source'] = 'dha-fhir';
				integral_fr_cache_put( $fhir_fid );
				return $fhir_fid;
			}
		}
	}

	return new WP_Error(
		'facility_not_found',
		'No facility found. Use an FID-… FR code, FID, or CoC / KMPDC registration number.'
	);
}

/**
 * SHA HIE facility search — mirrors ShaSetupServicesImpl.searchOrganization().
 */
function integral_hie_facility_search( $code, $type = 'fr-code' ) {
	$base           = rtrim( (string) integral_cfg( 'INTEGRAL_SHA_HIE_URL', 'https://ilm-hie.dha.go.ke/middleware' ), '/' );
	$client_id      = trim( (string) integral_cfg( 'INTEGRAL_SHA_CLIENT_ID' ) );
	$client_secret  = trim( (string) integral_cfg( 'INTEGRAL_SHA_CLIENT_SECRET' ) );

	$creds = integral_hie_creds_from_file();
	if ( $client_id === '' && ! empty( $creds['client_id'] ) ) {
		$client_id = $creds['client_id'];
	}
	if ( $client_secret === '' && ! empty( $creds['client_secret'] ) ) {
		$client_secret = $creds['client_secret'];
	}
	if ( ! empty( $creds['url'] ) ) {
		$base = rtrim( $creds['url'], '/' );
	}

	if ( $client_id === '' || $client_secret === '' ) {
		return null;
	}

	// Prefer host proxy when WordPress is in Docker (no egress / consistent TLS).
	$proxy_root = preg_replace( '#/sha/facilities/?$#', '', rtrim( integral_cfg( 'INTEGRAL_SHA_PORTAL_PROXY', 'http://host.docker.internal:18787/sha/facilities' ), '/' ) );
	$via_proxy  = $proxy_root . '/hie/facilities/search?identifier=' . rawurlencode( $code ) . '&identifier-type=' . rawurlencode( $type );

	$response = wp_remote_get(
		$via_proxy,
		array(
			'timeout' => 30,
			'headers' => array( 'Accept' => 'application/json' ),
		)
	);

	// If proxy is down or returns 501 (no creds on proxy), call HIE directly.
	$use_direct = is_wp_error( $response )
		|| wp_remote_retrieve_response_code( $response ) >= 500
		|| wp_remote_retrieve_response_code( $response ) === 501;

	if ( $use_direct ) {
		$token = integral_hie_access_token( $base, $client_id, $client_secret );
		if ( ! $token ) {
			return null;
		}
		$url      = $base . '/api/v1/facilities/search?identifier=' . rawurlencode( $code ) . '&identifier-type=' . rawurlencode( $type );
		$response = wp_remote_get(
			$url,
			array(
				'timeout' => 30,
				'headers' => array(
					'Accept'        => 'application/json',
					'Authorization' => 'Bearer ' . $token,
				),
			)
		);
	}

	if ( is_wp_error( $response ) ) {
		return null;
	}
	$status = wp_remote_retrieve_response_code( $response );
	$raw    = wp_remote_retrieve_body( $response );
	if ( $status < 200 || $status >= 300 || ! $raw ) {
		return null;
	}

	return integral_parse_hie_facility( $raw );
}

function integral_hie_creds_from_file() {
	$out  = array( 'url' => '', 'client_id' => '', 'client_secret' => '' );
	$path = trailingslashit( ABSPATH ) . '.sha-hie.env';
	if ( ! is_readable( $path ) ) {
		return $out;
	}
	$lines = file( $path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
	if ( ! is_array( $lines ) ) {
		return $out;
	}
	foreach ( $lines as $line ) {
		$line = trim( $line );
		if ( $line === '' || $line[0] === '#' || false === strpos( $line, '=' ) ) {
			continue;
		}
		list( $k, $v ) = array_map( 'trim', explode( '=', $line, 2 ) );
		$v = trim( $v, " \t\"'" );
		if ( $k === 'INTEGRAL_SHA_HIE_URL' || $k === 'SHA_HIE_URL' || $k === 'url' ) {
			$out['url'] = $v;
		} elseif ( $k === 'INTEGRAL_SHA_CLIENT_ID' || $k === 'SHA_CLIENT_ID' || $k === 'client_id' || $k === 'consumer_key' ) {
			$out['client_id'] = $v;
		} elseif ( $k === 'INTEGRAL_SHA_CLIENT_SECRET' || $k === 'SHA_CLIENT_SECRET' || $k === 'client_secret' || $k === 'consumer_secret' ) {
			$out['client_secret'] = $v;
		}
	}
	return $out;
}

function integral_hie_access_token( $base, $client_id, $client_secret ) {
	static $cache = array();
	$ck = md5( $base . '|' . $client_id );
	if ( ! empty( $cache[ $ck ]['token'] ) && ! empty( $cache[ $ck ]['exp'] ) && $cache[ $ck ]['exp'] > time() ) {
		return $cache[ $ck ]['token'];
	}

	$response = wp_remote_post(
		rtrim( $base, '/' ) . '/api/v1/tenants/token',
		array(
			'timeout' => 25,
			'headers' => array(
				'Accept'       => 'application/json',
				'Content-Type' => 'application/x-www-form-urlencoded',
			),
			'body'    => array(
				'client_id'     => $client_id,
				'client_secret' => $client_secret,
			),
		)
	);
	if ( is_wp_error( $response ) ) {
		return null;
	}
	$data = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( ! is_array( $data ) || empty( $data['access_token'] ) ) {
		return null;
	}
	$expires = isset( $data['expires_in'] ) ? (int) $data['expires_in'] : 300;
	$cache[ $ck ] = array(
		'token' => $data['access_token'],
		'exp'   => time() + max( 60, $expires - 30 ),
	);
	return $data['access_token'];
}

/**
 * Parse facilities/search JSON into the theme facility shape
 * (fields match ShaOrganizationDTO / institution.js).
 */
function integral_parse_hie_facility( $raw ) {
	$trimmed = trim( $raw );
	if ( $trimmed === '' ) {
		return null;
	}

	$row = null;
	if ( $trimmed[0] === '[' ) {
		$list = json_decode( $trimmed, true );
		if ( is_array( $list ) && ! empty( $list[0] ) && is_array( $list[0] ) ) {
			$row = $list[0];
		}
	} elseif ( $trimmed[0] === '{' ) {
		$data = json_decode( $trimmed, true );
		if ( ! is_array( $data ) ) {
			return null;
		}
		if ( isset( $data['message'] ) && is_array( $data['message'] ) ) {
			$row = $data['message'];
		} elseif ( ! empty( $data['officialName'] ) || ! empty( $data['frCode'] ) || ! empty( $data['facility_name'] ) || ! empty( $data['facility_code'] ) ) {
			$row = $data;
		}
	}
	if ( ! $row ) {
		return null;
	}

	$name = '';
	if ( ! empty( $row['officialName'] ) ) {
		$name = $row['officialName'];
	} elseif ( ! empty( $row['facility_name'] ) ) {
		$name = $row['facility_name'];
	} elseif ( ! empty( $row['name'] ) ) {
		$name = $row['name'];
	}
	if ( ! $name ) {
		return null;
	}

	$fr = '';
	if ( ! empty( $row['frCode'] ) ) {
		$fr = $row['frCode'];
	} elseif ( ! empty( $row['facility_code'] ) ) {
		$fr = $row['facility_code'];
	}

	$address = isset( $row['address'] ) && is_array( $row['address'] ) ? $row['address'] : array();
	$bed     = isset( $row['bedOccupancy'] ) && is_array( $row['bedOccupancy'] ) ? $row['bedOccupancy'] : array();

	$sha_status = '';
	if ( ! empty( $row['SHAOperationStatus']['operationalStatus'] ) ) {
		$sha_status = $row['SHAOperationStatus']['operationalStatus'];
	} elseif ( ! empty( $row['shaOperationStatus']['operationalStatus'] ) ) {
		$sha_status = $row['shaOperationStatus']['operationalStatus'];
	} elseif ( ! empty( $row['regulatoryOperationalStatus']['operationalStatus'] ) ) {
		$sha_status = $row['regulatoryOperationalStatus']['operationalStatus'];
	} elseif ( ! empty( $row['operational_status'] ) ) {
		$sha_status = $row['operational_status'];
	}

	$reg = '';
	if ( ! empty( $row['registrationNumber'] ) ) {
		$reg = $row['registrationNumber'];
	} elseif ( ! empty( $row['registration_number'] ) ) {
		$reg = $row['registration_number'];
	}

	return array(
		'frCode'        => $fr,
		'fidCode'       => isset( $row['fidCode'] ) ? $row['fidCode'] : ( isset( $row['fid_code'] ) ? $row['fid_code'] : ( isset( $row['fid'] ) ? $row['fid'] : '' ) ),
		'name'          => $name,
		'facilityType'  => isset( $row['facilityType'] ) ? $row['facilityType'] : ( isset( $row['facility_type'] ) ? $row['facility_type'] : '' ),
		'level'         => isset( $row['kephLevel'] ) ? $row['kephLevel'] : ( isset( $row['facility_level'] ) ? $row['facility_level'] : '' ),
		'ownership'     => isset( $row['facilityOwnership'] ) ? $row['facilityOwnership'] : ( isset( $row['facility_owner'] ) ? $row['facility_owner'] : '' ),
		'county'        => isset( $address['county'] ) ? $address['county'] : ( isset( $row['county'] ) ? $row['county'] : '' ),
		'subCounty'     => isset( $address['subCounty'] ) ? $address['subCounty'] : ( isset( $row['sub_county'] ) ? $row['sub_county'] : '' ),
		'town'          => isset( $address['town'] ) ? $address['town'] : '',
		'phone'         => isset( $row['facilityPhoneNumber'] ) ? $row['facilityPhoneNumber'] : '',
		'email'         => isset( $row['facilityEmail'] ) ? $row['facilityEmail'] : '',
		'admin'         => isset( $row['facilityAdministratorName'] ) ? $row['facilityAdministratorName'] : '',
		'licenseStatus' => isset( $row['facilityLicenseStatus'] ) ? $row['facilityLicenseStatus'] : ( $reg ? ( 'Reg #' . $reg ) : '' ),
		'licenseEnd'    => isset( $row['facilityLicenseEndDate'] ) ? $row['facilityLicenseEndDate'] : ( isset( $row['current_license_expiry_date'] ) ? $row['current_license_expiry_date'] : '' ),
		'totalBeds'     => isset( $bed['totalBeds'] ) ? $bed['totalBeds'] : '',
		'shaStatus'     => $sha_status,
		'regulator'     => isset( $row['regulatoryBody'] ) ? $row['regulatoryBody'] : ( isset( $row['regulator'] ) ? $row['regulator'] : '' ),
		'sladeCode'     => '',
		'uuid'          => '',
	);
}

/**
 * Live SHA Provider Portal guest facility search.
 * Mirrors https://portal.sha.go.ke/facility-lookup
 */
function integral_sha_portal_facility_lookup( $code, $type ) {
	$proxy = rtrim( integral_cfg( 'INTEGRAL_SHA_PORTAL_PROXY', 'http://host.docker.internal:18787/sha/facilities' ), '/' );
	$direct = rtrim( integral_cfg( 'INTEGRAL_SHA_PORTAL_API', 'https://api.provider.sha.go.ke/v1/facilities' ), '/' );

	$key  = integral_sha_portal_body_key( $type );
	$body = wp_json_encode( array( $key => $code ) );
	$url  = $proxy . '/?page_size=8&page=1';

	$response = wp_remote_post(
		$url,
		array(
			'timeout' => 30,
			'headers' => array(
				'Accept'       => 'application/json',
				'Content-Type' => 'application/json',
			),
			'body'    => $body,
		)
	);

	// If host proxy is down, try direct (works when PHP has egress).
	if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) >= 500 ) {
		$response = wp_remote_post(
			$direct . '/?page_size=8&page=1',
			array(
				'timeout' => 30,
				'headers' => array(
					'Accept'       => 'application/json',
					'Content-Type' => 'application/json',
				),
				'body'    => $body,
			)
		);
	}

	if ( is_wp_error( $response ) ) {
		return null;
	}

	$status = wp_remote_retrieve_response_code( $response );
	$raw    = wp_remote_retrieve_body( $response );
	if ( $status === 429 ) {
		return null; // throttled — caller may fall back
	}
	if ( $status < 200 || $status >= 300 || ! $raw ) {
		return null;
	}

	$data = json_decode( $raw, true );
	if ( ! is_array( $data ) || empty( $data['results'][0] ) || ! is_array( $data['results'][0] ) ) {
		return null;
	}

	return integral_parse_sha_portal_facility( $data['results'][0] );
}

function integral_parse_sha_portal_facility( $row ) {
	$fr = '';
	$fid = '';
	$reg = '';
	foreach ( (array) ( isset( $row['identifiers'] ) ? $row['identifiers'] : array() ) as $id ) {
		$t = isset( $id['identifier_type'] ) ? $id['identifier_type'] : '';
		$v = isset( $id['identifier'] ) ? $id['identifier'] : '';
		if ( $t === 'fr-code' ) {
			$fr = $v;
		} elseif ( $t === 'fid' ) {
			$fid = $v;
		} elseif ( $t === 'registration-number' ) {
			$reg = $v;
		}
	}
	if ( ! $fr && ! empty( $row['national_identifier'] ) ) {
		$fr = $row['national_identifier'];
	}

	$name = '';
	if ( ! empty( $row['name'] ) ) {
		$name = $row['name'];
	} elseif ( ! empty( $row['official_name'] ) ) {
		$name = $row['official_name'];
	}
	if ( ! $name ) {
		return null;
	}

	return array(
		'frCode'        => $fr,
		'fidCode'       => $fid,
		'name'          => $name,
		'facilityType'  => isset( $row['facility_type'] ) ? $row['facility_type'] : ( isset( $row['bp_type'] ) ? $row['bp_type'] : '' ),
		'level'         => isset( $row['bp_level'] ) ? $row['bp_level'] : ( isset( $row['keph_level'] ) ? $row['keph_level'] : '' ),
		'ownership'     => isset( $row['bp_ownership'] ) ? $row['bp_ownership'] : '',
		'county'        => isset( $row['county'] ) ? $row['county'] : '',
		'subCounty'     => isset( $row['sub_county'] ) ? $row['sub_county'] : '',
		'town'          => isset( $row['town'] ) ? $row['town'] : '',
		'phone'         => isset( $row['phone'] ) ? $row['phone'] : ( isset( $row['telephone'] ) ? $row['telephone'] : '' ),
		'email'         => isset( $row['email'] ) ? $row['email'] : '',
		'admin'         => '',
		'licenseStatus' => $reg ? ( 'Reg #' . $reg ) : '',
		'licenseEnd'    => '',
		'totalBeds'     => isset( $row['total_beds'] ) ? $row['total_beds'] : '',
		'shaStatus'     => isset( $row['status'] ) ? $row['status'] : '',
		'regulator'     => '',
		'sladeCode'     => isset( $row['slade_code'] ) ? $row['slade_code'] : '',
		'uuid'          => isset( $row['id'] ) ? $row['id'] : '',
	);
}

function integral_fhir_facility_lookup( $code, $type = 'fr-code' ) {
	$fhir_base = rtrim( integral_cfg( 'INTEGRAL_FHIR_API_URL', 'https://ilm-hie.dha.go.ke/fhir' ), '/' );
	$url       = $fhir_base . '/Organization?identifier=' . rawurlencode( $code ) . '&_count=5';

	$response = wp_remote_get(
		$url,
		array(
			'timeout' => 25,
			'headers' => array(
				'Accept' => 'application/fhir+json, application/json',
			),
		)
	);

	if ( is_wp_error( $response ) ) {
		return null;
	}

	$status = wp_remote_retrieve_response_code( $response );
	$raw    = wp_remote_retrieve_body( $response );
	if ( $status < 200 || $status >= 300 || ! $raw ) {
		return null;
	}

	$bundle = json_decode( $raw, true );
	if ( ! is_array( $bundle ) || empty( $bundle['entry'][0]['resource'] ) ) {
		return null;
	}

	return integral_parse_fhir_organization( $bundle['entry'][0]['resource'] );
}

function integral_parse_fhir_organization( $org ) {
	if ( ! is_array( $org ) ) {
		return null;
	}

	$name = isset( $org['name'] ) ? $org['name'] : '';
	if ( ! $name ) {
		return null;
	}

	$fr_code = '';
	$fid     = '';
	$reg     = '';
	foreach ( (array) ( isset( $org['identifier'] ) ? $org['identifier'] : array() ) as $id ) {
		$code = '';
		if ( ! empty( $id['type']['coding'][0]['code'] ) ) {
			$code = strtolower( $id['type']['coding'][0]['code'] );
		}
		$value = isset( $id['value'] ) ? $id['value'] : '';
		if ( $code === 'fr-code' ) {
			$fr_code = $value;
		} elseif ( $code === 'fid' ) {
			$fid = $value;
		} elseif ( $code === 'registration-number' ) {
			$reg = $value;
		} elseif ( ! $fr_code && preg_match( '/^FID[-_]/i', $value ) ) {
			$fr_code = $value;
		}
	}

	$facility_type = '';
	if ( ! empty( $org['type'][0]['text'] ) ) {
		$facility_type = $org['type'][0]['text'];
	} elseif ( ! empty( $org['type'][0]['coding'][0]['display'] ) ) {
		$facility_type = $org['type'][0]['coding'][0]['display'];
	}

	$level = '';
	$sha_status = '';
	foreach ( (array) ( isset( $org['extension'] ) ? $org['extension'] : array() ) as $ext ) {
		$url = isset( $ext['url'] ) ? $ext['url'] : '';
		$ccc = isset( $ext['valueCodeableConcept'] ) ? $ext['valueCodeableConcept'] : array();
		$text = '';
		if ( ! empty( $ccc['text'] ) ) {
			$text = $ccc['text'];
		} elseif ( ! empty( $ccc['coding'][0]['display'] ) ) {
			$text = $ccc['coding'][0]['display'];
		}
		if ( false !== strpos( $url, 'facility-level' ) ) {
			$level = $text;
		} elseif ( false !== strpos( $url, 'approval-status' ) || false !== strpos( $url, 'operational' ) ) {
			$sha_status = $text;
		}
	}

	$address = isset( $org['address'][0] ) && is_array( $org['address'][0] ) ? $org['address'][0] : array();
	$phone   = '';
	$email   = '';
	foreach ( (array) ( isset( $org['telecom'] ) ? $org['telecom'] : array() ) as $tel ) {
		$sys = isset( $tel['system'] ) ? $tel['system'] : '';
		$val = isset( $tel['value'] ) ? $tel['value'] : '';
		if ( $sys === 'phone' && ! $phone ) {
			$phone = $val;
		} elseif ( $sys === 'email' && ! $email ) {
			$email = $val;
		}
	}

	return array(
		'frCode'        => $fr_code,
		'fidCode'       => $fid,
		'name'          => $name,
		'facilityType'  => $facility_type,
		'level'         => $level,
		'ownership'     => '',
		'county'        => isset( $address['district'] ) ? $address['district'] : '',
		'subCounty'     => isset( $address['state'] ) ? $address['state'] : '',
		'town'          => isset( $address['city'] ) ? $address['city'] : '',
		'phone'         => $phone,
		'email'         => $email,
		'admin'         => '',
		'licenseStatus' => $reg ? ( 'Reg #' . $reg ) : '',
		'licenseEnd'    => '',
		'totalBeds'     => '',
		'shaStatus'     => $sha_status,
		'regulator'     => '',
	);
}

function integral_facility_types() {
	return array(
		'Level 2 — Dispensary / Clinic',
		'Level 3 — Health Centre',
		'Level 4 — Sub-county / Primary hospital',
		'Level 5 — County referral hospital',
		'Level 6 — National / teaching referral',
		'Private hospital',
		'Faith-based / Mission hospital',
		'Specialist clinic / Centre',
	);
}
