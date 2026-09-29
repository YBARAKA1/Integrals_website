<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
$integral_home = getenv( 'INTEGRAL_WP_HOME' ) ?: 'http://localhost:8080';
define( 'WP_HOME', $integral_home );
define( 'WP_SITEURL', getenv( 'INTEGRAL_WP_SITEURL' ) ?: $integral_home );
define('WP_HTTP_BLOCK_EXTERNAL', false);
define('WP_POST_REVISIONS', false);
define('AUTOSAVE_INTERVAL', 3000); // seconds


define('DB_NAME', 'wjdvkrvg_data');

/** MySQL database username */
define('DB_USER', 'wjdvkrvg_data');

/** MySQL database password */
define('DB_PASSWORD', '!!Down12many');

/** MySQL hostname — use TCP (127.0.0.1) so PHP built-in server can reach Docker MySQL */
define('DB_HOST', getenv('WORDPRESS_DB_HOST') ?: '127.0.0.1:3306');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

define('DISABLE_WP_CRON', true);

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '4aS+{-!Bg@#]&&$9W>@gP+F+4ibq!{P(u`n8 (rK|#aAe<lqp C<Z17 :;l;-z26');
define('SECURE_AUTH_KEY',  'N9>$ tK(`VWmp]ELCfAVFKyGz!O5RJ|.f1<:-mflU3*8F{c*F46kmRk?AuTj~|^o');
define('LOGGED_IN_KEY',    'xjC8Uf[II)&9Bn)Ebc<@JA|ILhFom4[!8,0yP81Ui6cz^r8z`K]:$IB?EicG3tMR');
define('NONCE_KEY',        '#Y(,S?8yU4]u}*@o#F.KF6xsDKB-nv3$zEtN/rS(l=%IBP}024.TB.wd4n%X,E/x');
define('AUTH_SALT',        'tgv((TB@S8&h7iNBfUw<VCE$H3*Xc|e3@Tyy:/;J2DWlJSFz+YqL.<5G$f{?i(oE');
define('SECURE_AUTH_SALT', '@zm%Hc2}|4`u#&{sr?1EuSR2UXxs<~gW0I{8>&,SR:|vY#R/-ySQ^zZh$ fTHQr;');
define('LOGGED_IN_SALT',   's{L*HBt4f3s1wTn{L]nl?c%6VI%5>Z9%T]n1uz%=Z/1&r14hY(_GW[a{vH/<KwOC');
define('NONCE_SALT',       '<U)Z@2aF:qaM/pU7AwDl(TIG+u_b%Lu7zz?H2*b}K]L[V1$B%[{2g]}{Oh3#s*Fk');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'ddhj7u_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/**
 * Live facility registry — same path as Integral HMIS institution setup:
 *   POST {HIE}/api/v1/tenants/token  (client_id + client_secret)
 *   GET  {HIE}/api/v1/facilities/search?identifier=&identifier-type=
 *
 * Defaults below are copied from local HMIS core.sha_setup (Institution → SHA Setup).
 * Env vars / .sha-hie.env still override when set.
 */
if ( ! defined( 'INTEGRAL_SHA_PORTAL_PROXY' ) ) {
	define( 'INTEGRAL_SHA_PORTAL_PROXY', getenv( 'INTEGRAL_SHA_PORTAL_PROXY' ) ?: 'http://host.docker.internal:18787/sha/facilities' );
}
if ( ! defined( 'INTEGRAL_SHA_PORTAL_API' ) ) {
	define( 'INTEGRAL_SHA_PORTAL_API', getenv( 'INTEGRAL_SHA_PORTAL_API' ) ?: 'https://api.provider.sha.go.ke/v1/facilities' );
}
if ( ! defined( 'INTEGRAL_FHIR_API_URL' ) ) {
	$fhir_default = getenv( 'INTEGRAL_FHIR_API_URL' );
	if ( ! $fhir_default ) {
		$fhir_default = 'http://host.docker.internal:18787/fhir';
	}
	define( 'INTEGRAL_FHIR_API_URL', $fhir_default );
}
if ( ! defined( 'INTEGRAL_SHA_HIE_URL' ) ) {
	define( 'INTEGRAL_SHA_HIE_URL', getenv( 'INTEGRAL_SHA_HIE_URL' ) ?: 'https://ilm-dev.dha.go.ke/uat-middleware' );
}
if ( ! defined( 'INTEGRAL_SHA_CLIENT_ID' ) ) {
	define( 'INTEGRAL_SHA_CLIENT_ID', getenv( 'INTEGRAL_SHA_CLIENT_ID' ) ?: 'afyaconnect-app-2fe59510-d3c2-49ff-a709-608260b61b1c' );
}
if ( ! defined( 'INTEGRAL_SHA_CLIENT_SECRET' ) ) {
	define( 'INTEGRAL_SHA_CLIENT_SECRET', getenv( 'INTEGRAL_SHA_CLIENT_SECRET' ) ?: 'kORj99ZzOgr4nr96DGnwpSW6KoovQaYS' );
}

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');