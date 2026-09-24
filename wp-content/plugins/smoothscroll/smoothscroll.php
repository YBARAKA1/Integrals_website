<?php
/*
 * Plugin Name: Smoothscroll
 * Plugin URI: https://wordpress.org/plugins/smoothscroll/
 * Description: Adds smoothscrolling to your website for better user experience.
 * Version: 1.0.3
 * Author: Mitch
 * Author URI: https://profiles.wordpress.org/lowest
 * License: GPL-2.0+
 * Text Domain: smoothscroll
 * Domain Path:
 * Network:
 * License: GPL-2.0+
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! defined( 'SS_FILE' ) ) { define( 'SS_FILE', __FILE__ ); }

$smoothscroll = get_option('smoothscroll');
$smoothscrollcustom = get_option('smoothscrollcustom');

add_action( 'admin_menu', function() {
	add_submenu_page(
		'options-general.php',
		'Smoothscroll',
		'Smoothscroll',
		'manage_options',
		'smoothscroll',
		'smoothscroll_page' );
});

function smoothscrollcustom_item( $default, $item ) {
	global $smoothscrollcustom;
	
	if(empty($smoothscrollcustom[$item])) {
		return $default;
	} else {
		return $smoothscrollcustom[$item];
	}
}

function smoothscroll_page() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	} else {
		global $smoothscroll;
		global $smoothscrollcustom;
		?>
<div class="wrap">
	<h1>Smoothscroll</h1>
	<h2 class="nav-tab-wrapper">
		<a class="nav-tab<?php if(empty($_GET['tab'])) { echo ' nav-tab-active'; } ?>" href="<?php echo admin_url( 'options-general.php?page=smoothscroll' ); ?>"><?php _e('Options'); ?></a>
		<?php if(isset($smoothscroll['customopt']) && $smoothscroll['customopt'] == '1') { ?><a class="nav-tab<?php if(isset($_GET['tab']) && $_GET['tab'] == 'custom') { echo ' nav-tab-active'; } ?>" href="<?php echo admin_url( 'options-general.php?page=smoothscroll&tab=custom' ); ?>"><?php _e('Custom Options'); ?></a><?php } else { ?><a class="nav-tab" style="opacity:0.5;pointer-events:none;cursor:default;" href="javascript:;"><?php _e('Custom Options'); ?></a><?php } ?>
	</h2>
	<?php
	if(isset($_GET['tab']) && $_GET['tab'] == 'custom') {
		if(isset($smoothscroll['customopt']) && $smoothscroll['customopt'] == '1') {
	?>
	<form method="post" action="options.php">
		<?php settings_fields( 'smoothscrollcustom_group' ); ?>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="smoothscrollcustom[framerate]"><?php _e( 'Frame Rate' ); ?></label></th>
				<td><input id="smoothscrollcustom[framerate]" name="smoothscrollcustom[framerate]" type="text" value="<?php if(empty($smoothscrollcustom['framerate'])) { echo '150'; } else { echo $smoothscrollcustom['framerate']; } ?>" /> Hz</td>
			</tr>
			<tr>
				<th scope="row"><label for="smoothscrollcustom[animationtime]"><?php _e( 'Animation Time' ); ?></label></th>
				<td><input id="smoothscrollcustom[animationtime]" name="smoothscrollcustom[animationtime]" type="text" value="<?php if(empty($smoothscrollcustom['animationtime'])) { echo '700'; } else { echo $smoothscrollcustom['animationtime']; } ?>" /> px</td>
			</tr>
			<tr>
				<th scope="row"><label for="smoothscrollcustom[stepsize]"><?php _e( 'Step Size' ); ?></label></th>
				<td><input id="smoothscrollcustom[stepsize]" name="smoothscrollcustom[stepsize]" type="text" value="<?php if(empty($smoothscrollcustom['stepsize'])) { echo '80'; } else { echo $smoothscrollcustom['stepsize']; } ?>" /> px</td>
			</tr>
			<tr>
				<th scope="row"><label for="smoothscrollcustom[pulsealgorithm]"><?php _e( 'Pulse Algorithm' ); ?></label></th>
				<td><select id="smoothscrollcustom[pulsealgorithm]" name="smoothscrollcustom[pulsealgorithm]"><option value="true"<?php if($smoothscrollcustom['pulsealgorithm'] == 'true') { echo ' selected'; } ?>>true</option><option value="false"<?php if($smoothscrollcustom['pulsealgorithm'] == 'false') { echo ' selected'; } ?>>false</option></select></td>
			</tr>
			<tr>
				<th scope="row"><label for="smoothscrollcustom[pulsescale]"><?php _e( 'Pulse Scale' ); ?></label></th>
				<td><input id="smoothscrollcustom[pulsescale]" name="smoothscrollcustom[pulsescale]" type="text" value="<?php if(empty($smoothscrollcustom['pulsescale'])) { echo '8'; } else { echo $smoothscrollcustom['pulsescale']; } ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="smoothscrollcustom[pulsenormalize]"><?php _e( 'Pulse Normalize' ); ?></label></th>
				<td><input id="smoothscrollcustom[pulsenormalize]" name="smoothscrollcustom[pulsenormalize]" type="text" value="<?php if(empty($smoothscrollcustom['pulsenormalize'])) { echo '1'; } else { echo $smoothscrollcustom['pulsenormalize']; } ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="smoothscrollcustom[accelerationdelta]"><?php _e( 'Acceleration Delta' ); ?></label></th>
				<td><input id="smoothscrollcustom[accelerationdelta]" name="smoothscrollcustom[accelerationdelta]" type="text" value="<?php if(empty($smoothscrollcustom['accelerationdelta'])) { echo '20'; } else { echo $smoothscrollcustom['accelerationdelta']; } ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="smoothscrollcustom[accelerationmax]"><?php _e( 'Acceleration Max' ); ?></label></th>
				<td><input id="smoothscrollcustom[accelerationmax]" name="smoothscrollcustom[accelerationmax]" type="text" value="<?php if(empty($smoothscrollcustom['accelerationmax'])) { echo '1'; } else { echo $smoothscrollcustom['accelerationmax']; } ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="smoothscrollcustom[keyboardsupport]"><?php _e( 'Keyboard Support' ); ?></label></th>
				<td><select id="smoothscrollcustom[keyboardsupport]" name="smoothscrollcustom[keyboardsupport]"><option value="true"<?php if($smoothscrollcustom['touchpadsupport'] == 'true') { echo ' selected'; } ?>>true</option><option value="false"<?php if($smoothscrollcustom['keyboardsupport'] == 'false') { echo ' selected'; } ?>>false</option></select></td>
			</tr>
			<tr>
				<th scope="row"><label for="smoothscrollcustom[arrowscroll]"><?php _e( 'Arrow Scroll' ); ?></label></th>
				<td><input id="smoothscrollcustom[arrowscroll]" name="smoothscrollcustom[arrowscroll]" type="text" value="<?php if(empty($smoothscrollcustom['arrowscroll'])) { echo '50'; } else { echo $smoothscrollcustom['arrowscroll']; } ?>" /> px</td>
			</tr>
			<tr>
				<th scope="row"><label for="smoothscrollcustom[touchpadsupport]"><?php _e( 'Touchpad Support' ); ?></label></th>
				<td><select id="smoothscrollcustom[touchpadsupport]" name="smoothscrollcustom[touchpadsupport]"><option value="true"<?php if($smoothscrollcustom['touchpadsupport'] == 'true') { echo ' selected'; } ?>>true</option><option value="false"<?php if($smoothscrollcustom['touchpadsupport'] == 'false') { echo ' selected'; } ?>>false</option></select></td>
			</tr>
			<tr>
				<th scope="row"><label for="smoothscrollcustom[fixedbackground]"><?php _e( 'Fixed Background' ); ?></label></th>
				<td><select id="smoothscrollcustom[fixedbackground]" name="smoothscrollcustom[fixedbackground]"><option value="true"<?php if($smoothscrollcustom['fixedbackground'] == 'true') { echo ' selected'; } ?>>true</option><option value="false"<?php if($smoothscrollcustom['fixedbackground'] == 'false') { echo ' selected'; } ?>>false</option></select></td>
			</tr>
			<tr>
				<th scope="row"><label for="smoothscrollcustom[excluded]"><?php _e( 'Excluded' ); ?></label></th>
				<td><input id="smoothscrollcustom[excluded]" name="smoothscrollcustom[excluded]" type="text" value="<?php if(empty($smoothscrollcustom['excluded'])) { echo ''; } else { echo $smoothscrollcustom['excluded']; } ?>" /></td>
			</tr>
		</table>
		<?php submit_button(); ?>
	</form>
	<?php
		}
	} else {
	?>
	<form method="post" action="options.php">
		<?php settings_fields( 'smoothscroll_group' ); ?>
		<table class="form-table">
			<tr>
				<th scope="row"><?php _e( 'Enable Smoothscrolling' ); ?></th>
				<td>
					<input id="smoothscroll[enable-front]" name="smoothscroll[enable-front]" type="checkbox" value="1" <?php checked( true, isset( $smoothscroll['enable-front'] ) ); ?> /><label for="smoothscroll[enable-front]"><?php _e('Front-end'); ?></label>
					<br />
					<input id="smoothscroll[enable-back]" name="smoothscroll[enable-back]" type="checkbox" value="1" <?php checked( true, isset( $smoothscroll['enable-back'] ) ); ?> /><label for="smoothscroll[enable-back]"><?php _e('Back-end'); ?></label>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="smoothscroll[file]"><?php _e( 'Use Minified File' ); ?></label></th>
				<td><input id="smoothscroll[file]" name="smoothscroll[file]" type="checkbox" value="1" <?php checked( true, isset( $smoothscroll['file'] ) ); ?> /></td>
			</tr>
			<tr>
				<th scope="row"><label for="smoothscroll[customopt]"><?php _e( 'Enable Custom Options' ); ?></label></th>
				<td><input id="smoothscroll[customopt]" name="smoothscroll[customopt]" type="checkbox" value="1" <?php checked( true, isset( $smoothscroll['customopt'] ) ); ?> /></td>
			</tr>
			<tr>
				<th scope="row"><label for="smoothscroll[testing]"><?php _e( 'Enable Testing' ); ?></label></th>
				<td><input id="smoothscroll[testing]" name="smoothscroll[testing]" type="checkbox" value="1" <?php checked( true, isset( $smoothscroll['testing'] ) ); ?> /></td>
			</tr>
		</table>
		<?php submit_button(); ?>
	</form>
	<?php
	}
	?>
</div>
		<?php
	}
}

add_action( 'admin_init', function() {
	register_setting( 'smoothscroll_group', 'smoothscroll' );
	register_setting( 'smoothscrollcustom_group', 'smoothscrollcustom' );
});

add_action('wp_head', function() {
	global $smoothscroll;
	global $smoothscrollcustom;
	
	if(isset($smoothscroll['enable-front']) && $smoothscroll['enable-front'] == '1') {
		if(isset($smoothscroll['customopt']) && $smoothscroll['customopt'] == '1') {
			echo '<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery.smoothScroll({
		frameRate: ' . smoothscrollcustom_item( '150', 'framerate' ) . ',
		animationTime: ' . smoothscrollcustom_item( '700', 'animationtime' ) . ',
		stepSize: ' . smoothscrollcustom_item( '80', 'stepsize' ) . ',

		pulseAlgorithm: ' . smoothscrollcustom_item( 'true', 'pulsealgorithm' ) . ',
		pulseScale: ' . smoothscrollcustom_item( '8', 'pulsescale' ) . ',
		pulseNormalize: ' . smoothscrollcustom_item( '1', 'pulsenormalize' ) . ',

		accelerationDelta: ' . smoothscrollcustom_item( '20', 'accelerationdelta' ) . ',
		accelerationMax: ' . smoothscrollcustom_item( '1', 'accelerationmax' ) . ',

		keyboardSupport: ' . smoothscrollcustom_item( 'true', 'keyboardsupport' ) . ',
		arrowScroll: ' . smoothscrollcustom_item( '50', 'arrowscroll' ) . ',

		touchpadSupport: ' . smoothscrollcustom_item( 'true', 'touchpadsupport' ) . ',
		fixedBackground: ' . smoothscrollcustom_item( 'true', 'fixedbackground' ) . ',
		excluded: "' . smoothscrollcustom_item( '', 'excluded' ) . '"
	});
});
</script>';
		} else {
			echo '<script type="text/javascript">jQuery(document).ready(function(){jQuery.smoothScroll();});</script>';
		}
	}
});

add_action('admin_head', function() {
	global $smoothscroll;
	global $smoothscrollcustom;
	
	if(isset($smoothscroll['enable-back']) && $smoothscroll['enable-back'] == '1') {
		if(isset($smoothscroll['customopt']) && $smoothscroll['customopt'] == '1') {
			echo '<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery.smoothScroll({
		frameRate: ' . smoothscrollcustom_item( '150', 'framerate' ) . ',
		animationTime: ' . smoothscrollcustom_item( '2000', 'animationtime' ) . ',
		stepSize: ' . smoothscrollcustom_item( '50', 'stepsize' ) . ',

		pulseAlgorithm: ' . smoothscrollcustom_item( 'true', 'pulsealgorithm' ) . ',
		pulseScale: ' . smoothscrollcustom_item( '8', 'pulsescale' ) . ',
		pulseNormalize: ' . smoothscrollcustom_item( '1', 'pulsenormalize' ) . ',

		accelerationDelta: ' . smoothscrollcustom_item( '20', 'accelerationdelta' ) . ',
		accelerationMax: ' . smoothscrollcustom_item( '1', 'accelerationmax' ) . ',

		keyboardSupport: ' . smoothscrollcustom_item( 'true', 'keyboardsupport' ) . ',
		arrowScroll: ' . smoothscrollcustom_item( '50', 'arrowscroll' ) . ',

		touchpadSupport: ' . smoothscrollcustom_item( 'true', 'touchpadsupport' ) . ',
		fixedBackground: ' . smoothscrollcustom_item( 'true', 'fixedbackground' ) . ',
		excluded: "' . smoothscrollcustom_item( '', 'excluded' ) . '"
	});
});
</script>';
		} else {
			echo '<script type="text/javascript">jQuery(document).ready(function(){jQuery.smoothScroll();});</script>';
		}
	}
});

function smoothscroll_enqscripts() {
	global $smoothscroll;

	if(isset($smoothscroll['file']) && $smoothscroll['file'] == '1') {
		wp_register_script( 'smoothscroll', plugins_url( 'smoothscroll.min.js', SS_FILE ), array('jquery'), '1.0.0', false );
		wp_enqueue_script( 'smoothscroll' );
	} else {
		wp_register_script( 'smoothscroll', plugins_url( 'smoothscroll.js', SS_FILE ), array('jquery'), '1.0.0', false );
		wp_enqueue_script( 'smoothscroll' );
	}
}

add_action('wp_enqueue_scripts', function() {
	global $smoothscroll;

	if(isset($smoothscroll['enable-front']) && $smoothscroll['enable-front'] == '1') {
		if(isset($smoothscroll['testing']) && $smoothscroll['testing'] == '1') {
			if(current_user_can( 'manage_options' )) {
				smoothscroll_enqscripts();
			}
		} else {
			smoothscroll_enqscripts();
		}
	}
});

add_action('admin_enqueue_scripts', function() {
	global $smoothscroll;
	
	if(isset($smoothscroll['enable-back']) && $smoothscroll['enable-back'] == '1') {
		if(isset($smoothscroll['testing']) && $smoothscroll['testing'] == '1') {
			if(current_user_can( 'manage_options' )) {
				smoothscroll_enqscripts();
			}
		} else {
			smoothscroll_enqscripts();
		}
	}
});

add_filter( 'plugin_action_links_' . plugin_basename( SS_FILE ), function($link) {
	return array_merge( $link, array('<a href="' . admin_url( 'options-general.php?page=smoothscroll' ) . '">Settings</a>','<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=2VYPRGME8QELC" target="_blank" rel="noopener noreferrer">Donate</a>') );
} );