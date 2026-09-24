<?php
/**
 * Outbound mail — Contact Form 7 and theme forms use wp_mail().
 *
 * Recipients:
 *   Contact Us / Service request / Volunteer / Demo AJAX → support@integral.co.ke
 *
 * HTML layout matches Integral HMS transactional emails
 * (header #780080, footer #390049 — same as Login Verification).
 *
 * SMTP via .mail.env — see .mail.env.example.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'INTEGRAL_MAIL_HEADER_PURPLE', '#780080' );
define( 'INTEGRAL_MAIL_FOOTER_PURPLE', '#390049' );

add_action( 'phpmailer_init', 'integral_configure_phpmailer' );
add_action( 'phpmailer_init', 'integral_phpmailer_force_html', 100 );
add_filter( 'wp_mail_from', 'integral_mail_from' );
add_filter( 'wp_mail_from_name', 'integral_mail_from_name' );
add_action( 'wp_mail_failed', 'integral_log_mail_failure' );

add_filter( 'wpcf7_mail_components', 'integral_cf7_branded_mail', 20, 3 );

add_action( 'wp_ajax_integral_send_inquiry', 'integral_ajax_send_inquiry' );
add_action( 'wp_ajax_nopriv_integral_send_inquiry', 'integral_ajax_send_inquiry' );

function integral_mail_cfg( $key, $default = '' ) {
	if ( defined( $key ) && constant( $key ) !== '' && constant( $key ) !== false ) {
		return (string) constant( $key );
	}
	$env = getenv( $key );
	if ( $env ) {
		return $env;
	}
	static $file = null;
	if ( $file === null ) {
		$file = array();
		$path = trailingslashit( ABSPATH ) . '.mail.env';
		if ( is_readable( $path ) ) {
			foreach ( file( $path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES ) as $line ) {
				$line = trim( $line );
				if ( $line === '' || $line[0] === '#' || false === strpos( $line, '=' ) ) {
					continue;
				}
				list( $k, $v ) = array_map( 'trim', explode( '=', $line, 2 ) );
				$file[ $k ] = trim( $v, " \t\"'" );
			}
		}
	}
	return isset( $file[ $key ] ) ? $file[ $key ] : $default;
}

function integral_mail_recipient() {
	$to = integral_mail_cfg( 'INTEGRAL_MAIL_TO', 'support@integral.co.ke' );
	return $to ? $to : 'support@integral.co.ke';
}

function integral_mail_cc() {
	return trim( integral_mail_cfg( 'INTEGRAL_MAIL_CC', '' ) );
}

function integral_mail_from( $from ) {
	$configured = integral_mail_cfg( 'INTEGRAL_SMTP_FROM', 'no-reply@integral.co.ke' );
	return $configured ? $configured : $from;
}

function integral_mail_from_name( $name ) {
	$configured = integral_mail_cfg( 'INTEGRAL_SMTP_FROM_NAME', 'Integral Software' );
	return $configured ? $configured : $name;
}

function integral_mail_headers_html( $reply_to = '' ) {
	$headers = array(
		'Content-Type: text/html; charset=UTF-8',
		'From: ' . integral_mail_from_name( 'Integral Software' ) . ' <' . integral_mail_from( 'no-reply@integral.co.ke' ) . '>',
	);
	if ( $reply_to ) {
		$headers[] = 'Reply-To: ' . $reply_to;
	}
	$cc = integral_mail_cc();
	if ( $cc && strcasecmp( $cc, integral_mail_recipient() ) !== 0 ) {
		$headers[] = 'Cc: ' . $cc;
	}
	return $headers;
}

function integral_mail_esc( $value ) {
	return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' );
}

function integral_mail_nl2br_esc( $value ) {
	return nl2br( integral_mail_esc( $value ), false );
}

/**
 * Same shell as Integral HMS Login Verification emails.
 *
 * @param string $title   Header title (e.g. "New Website Inquiry").
 * @param string $icon    HTML entity / emoji for header (e.g. &#128231;).
 * @param string $intro   HTML paragraphs after greeting (already escaped where needed).
 * @param string $hero_label  Label above the dashed highlight box.
 * @param string $hero_value  Large centered value in the dashed box.
 * @param string $hero_note   Small note under the hero value.
 * @param string $details_title  e.g. "Inquiry Details:".
 * @param array  $details  label => value pairs (plain text; escaped here).
 * @param string $message_html Optional extra HTML block (message body).
 */
function integral_build_branded_email_html(
	$title,
	$icon,
	$intro,
	$hero_label,
	$hero_value,
	$hero_note,
	$details_title,
	$details,
	$message_html = ''
) {
	$header = INTEGRAL_MAIL_HEADER_PURPLE;
	$footer = INTEGRAL_MAIL_FOOTER_PURPLE;
	$title_e = integral_mail_esc( $title );
	$icon_e  = $icon; // intentional HTML entity
	$hero_label_e = integral_mail_esc( $hero_label );
	$hero_value_e = integral_mail_esc( $hero_value );
	$hero_note_e  = integral_mail_esc( $hero_note );
	$details_title_e = integral_mail_esc( $details_title );

	$details_rows = '';
	foreach ( $details as $label => $value ) {
		if ( $value === '' || $value === null ) {
			continue;
		}
		$details_rows .= '<div><strong>' . integral_mail_esc( $label ) . ':</strong> '
			. integral_mail_esc( $value ) . '</div>';
	}

	$message_block = '';
	if ( $message_html !== '' ) {
		$message_block =
			'<table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;margin-bottom:24px;">'
			. '<tr><td style="padding:18px 20px;font-size:14px;line-height:1.7;color:#374151;">'
			. '<div style="font-weight:700;color:#111827;margin-bottom:8px;">Message</div>'
			. $message_html
			. '</td></tr></table>';
	}

	return '<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>' . $title_e . '</title>
</head>
<body style="margin:0;padding:0;background-color:#f3f4f6;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
  <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color:#f3f4f6;padding:24px 12px;">
    <tr>
      <td align="center">
        <table role="presentation" width="600" border="0" cellspacing="0" cellpadding="0" style="max-width:600px;width:100%;border-collapse:collapse;background-color:#ffffff;border:1px solid #e5e7eb;">
          <tr>
            <td style="background-color:' . $header . ';padding:32px 28px;text-align:center;color:#ffffff;">
              <div style="font-size:28px;line-height:1;margin-bottom:14px;">' . $icon_e . '</div>
              <div style="font-size:28px;font-weight:700;line-height:1.2;margin-bottom:8px;">' . $title_e . '</div>
              <div style="font-size:14px;line-height:1.5;opacity:0.95;">Integral HMS &mdash; Integral Software Technology Ltd</div>
            </td>
          </tr>
          <tr>
            <td style="padding:32px 28px 24px;font-size:15px;line-height:1.7;color:#374151;">
              <p style="margin:0 0 16px;font-size:16px;font-weight:700;color:#111827;">Hello Support Team,</p>
              ' . $intro . '
              <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="border:2px dashed #93c5fd;border-radius:12px;background-color:#f8fbff;margin-bottom:24px;">
                <tr>
                  <td style="padding:28px 20px;text-align:center;">
                    <div style="font-size:13px;color:#64748b;margin-bottom:12px;">' . $hero_label_e . '</div>
                    <div style="font-size:22px;font-weight:700;letter-spacing:0;color:#111827;line-height:1.3;">' . $hero_value_e . '</div>
                    <div style="font-size:13px;color:#64748b;margin-top:12px;">' . $hero_note_e . '</div>
                  </td>
                </tr>
              </table>
              <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color:#eff6ff;border-left:4px solid #3b82f6;border-radius:8px;margin-bottom:24px;">
                <tr>
                  <td style="padding:18px 20px;font-size:14px;line-height:1.7;color:#1e3a8a;">
                    <div style="font-weight:700;color:#111827;margin-bottom:8px;">' . $details_title_e . '</div>
                    ' . $details_rows . '
                  </td>
                </tr>
              </table>
              ' . $message_block . '
              <p style="margin:0 0 8px;">Reply directly to this email to contact the sender. For assistance call <strong>+254 720 730 430</strong>.</p>
              <p style="margin:24px 0 0;">Best regards,<br><strong>Integral Software Technology Ltd</strong></p>
            </td>
          </tr>
          <tr>
            <td style="background-color:' . $footer . ';padding:24px 28px;text-align:center;color:#ffffff;font-size:13px;line-height:1.7;">
              <div style="font-weight:700;margin-bottom:8px;">Integral Auto Response</div>
              <div>Email: support@integral.co.ke</div>
              <div>Mobile: +254 720 730 430</div>
              <div>Url: www.integral.co.ke</div>
              <div style="margin-top:12px;font-size:11px;opacity:0.9;">Email automatically sent from Integral. Do not reply.</div>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>';
}

function integral_client_ip() {
	$keys = array( 'HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' );
	foreach ( $keys as $key ) {
		if ( empty( $_SERVER[ $key ] ) ) {
			continue;
		}
		$raw = explode( ',', (string) $_SERVER[ $key ] );
		$ip  = trim( $raw[0] );
		if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
			return $ip;
		}
	}
	return '';
}

function integral_client_device() {
	$ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? (string) $_SERVER['HTTP_USER_AGENT'] : '';
	if ( $ua === '' ) {
		return 'Unknown';
	}
	$browser = 'Browser';
	if ( preg_match( '/Edg\/([\d.]+)/', $ua, $m ) ) {
		$browser = 'Edge ' . $m[1];
	} elseif ( preg_match( '/Chrome\/([\d.]+)/', $ua, $m ) && stripos( $ua, 'Edg' ) === false ) {
		$browser = 'Chrome ' . $m[1];
	} elseif ( preg_match( '/Firefox\/([\d.]+)/', $ua, $m ) ) {
		$browser = 'Firefox ' . $m[1];
	} elseif ( preg_match( '/Safari\/([\d.]+)/', $ua ) && preg_match( '/Version\/([\d.]+)/', $ua, $m ) ) {
		$browser = 'Safari ' . $m[1];
	}
	$os = 'Unknown OS';
	if ( stripos( $ua, 'Android' ) !== false ) {
		$os = 'Android';
	} elseif ( stripos( $ua, 'iPhone' ) !== false || stripos( $ua, 'iPad' ) !== false ) {
		$os = 'iOS';
	} elseif ( stripos( $ua, 'Windows' ) !== false ) {
		$os = 'Windows';
	} elseif ( stripos( $ua, 'Mac OS' ) !== false ) {
		$os = 'macOS';
	} elseif ( stripos( $ua, 'Linux' ) !== false ) {
		$os = 'Linux';
	}
	return $browser . ' on ' . $os;
}

/**
 * Build branded HTML for a website / demo inquiry.
 *
 * @param array $fields name, email, phone, subject, message, source, service (optional).
 */
function integral_inquiry_email_html( array $fields ) {
	$name    = isset( $fields['name'] ) ? $fields['name'] : '';
	$email   = isset( $fields['email'] ) ? $fields['email'] : '';
	$phone   = isset( $fields['phone'] ) ? $fields['phone'] : '';
	$subject = isset( $fields['subject'] ) ? $fields['subject'] : 'Website inquiry';
	$message = isset( $fields['message'] ) ? $fields['message'] : '';
	$source  = isset( $fields['source'] ) ? $fields['source'] : 'Website';
	$service = isset( $fields['service'] ) ? $fields['service'] : '';
	$when    = current_time( 'j M Y, H:i:s' );

	$intro = '<p style="margin:0 0 24px;">A new inquiry was submitted on the Integral website'
		. ( $name !== '' ? ' by <strong>' . integral_mail_esc( $name ) . '</strong>' : '' )
		. '. Review the details below and reply to the sender as needed:</p>';

	$details = array(
		'Name'       => $name,
		'Email'      => $email,
		'Telephone'  => $phone,
		'Subject'    => $subject,
		'Service'    => $service,
		'Source'     => $source,
		'Time'       => $when,
		'IP Address' => integral_client_ip(),
		'Device'     => integral_client_device(),
	);

	return integral_build_branded_email_html(
		'New Website Inquiry',
		'&#128231;',
		$intro,
		'Subject',
		$subject !== '' ? $subject : 'Website inquiry',
		'Submitted via ' . $source,
		'Inquiry Details:',
		$details,
		$message !== '' ? integral_mail_nl2br_esc( $message ) : ''
	);
}

function integral_cf7_branded_mail( $components, $cf7 = null, $mail = null ) {
	$to = trim( (string) ( isset( $components['recipient'] ) ? $components['recipient'] : '' ) );
	if ( $to === '' ) {
		$components['recipient'] = integral_mail_recipient();
	}
	$from = integral_mail_from( 'no-reply@integral.co.ke' );
	$components['sender'] = integral_mail_from_name( 'Integral Software' ) . ' <' . $from . '>';

	$data = array();
	if ( class_exists( 'WPCF7_Submission' ) ) {
		$submission = WPCF7_Submission::get_instance();
		if ( $submission ) {
			$data = $submission->get_posted_data();
		}
	}

	$form_title = ( $cf7 && method_exists( $cf7, 'title' ) ) ? $cf7->title() : 'Contact Form';
	$name       = isset( $data['your-name'] ) ? $data['your-name'] : '';
	$email      = isset( $data['your-email'] ) ? $data['your-email'] : '';
	$phone      = isset( $data['your-tel'] ) ? $data['your-tel'] : '';
	$subject    = isset( $data['your-subject'] ) ? $data['your-subject'] : '';
	$message    = isset( $data['your-message'] ) ? $data['your-message'] : '';
	$service    = isset( $data['menu-832'] ) ? $data['menu-832'] : '';
	if ( $subject === '' && $service !== '' ) {
		$subject = $service;
	}
	if ( $subject === '' ) {
		$subject = $form_title;
	}

	$components['subject'] = '[Integral Contact] ' . $subject;
	$components['body']    = integral_inquiry_email_html(
		array(
			'name'    => $name,
			'email'   => $email,
			'phone'   => $phone,
			'subject' => $subject,
			'message' => $message,
			'service' => $service,
			'source'  => $form_title,
		)
	);

	$headers = isset( $components['additional_headers'] ) ? (string) $components['additional_headers'] : '';
	$headers = preg_replace( '/^Content-Type:.*$/mi', '', $headers );
	$headers = trim( $headers ) . "\nContent-Type: text/html; charset=UTF-8";
	if ( $email && is_email( $email ) ) {
		$reply = $name ? ( $name . ' <' . $email . '>' ) : $email;
		if ( stripos( $headers, 'Reply-To:' ) === false ) {
			$headers .= "\nReply-To: " . $reply;
		}
	}
	$cc = integral_mail_cc();
	if ( $cc && stripos( $headers, 'Cc:' ) === false && strcasecmp( $cc, $components['recipient'] ) !== 0 ) {
		$headers .= "\nCc: " . $cc;
	}
	$components['additional_headers'] = trim( $headers );

	return $components;
}

function integral_configure_phpmailer( $phpmailer ) {
	$host = integral_mail_cfg( 'INTEGRAL_SMTP_HOST' );
	if ( ! $host ) {
		return;
	}

	$phpmailer->isSMTP();
	$phpmailer->Host       = $host;
	$phpmailer->Port       = (int) integral_mail_cfg( 'INTEGRAL_SMTP_PORT', '587' );
	$phpmailer->SMTPAuth   = (bool) integral_mail_cfg( 'INTEGRAL_SMTP_USER' );
	$phpmailer->Username   = integral_mail_cfg( 'INTEGRAL_SMTP_USER' );
	$phpmailer->Password   = integral_mail_cfg( 'INTEGRAL_SMTP_PASS' );
	$secure                = strtolower( integral_mail_cfg( 'INTEGRAL_SMTP_SECURE', 'tls' ) );
	$phpmailer->SMTPSecure = in_array( $secure, array( 'tls', 'ssl' ), true ) ? $secure : '';
	$phpmailer->Timeout    = 30;

	if ( $phpmailer->Port === 465 && ! $phpmailer->SMTPSecure ) {
		$phpmailer->SMTPSecure = 'ssl';
	}

	if ( ! $phpmailer->Username && in_array( $phpmailer->Port, array( 1025, 1026, 2525 ), true ) ) {
		$phpmailer->SMTPAuth   = false;
		$phpmailer->SMTPSecure = '';
	}
}

/** Ensure branded templates render as HTML (CF7 may set text/plain). */
function integral_phpmailer_force_html( $phpmailer ) {
	$body = isset( $phpmailer->Body ) ? (string) $phpmailer->Body : '';
	if ( stripos( $body, '<!DOCTYPE html>' ) === false && stripos( $body, '<html' ) === false ) {
		return;
	}
	if ( method_exists( $phpmailer, 'isHTML' ) ) {
		$phpmailer->isHTML( true );
	}
	$phpmailer->CharSet = 'UTF-8';
	// Plain-text fallback for clients that prefer it.
	$phpmailer->AltBody = wp_strip_all_tags( str_replace( array( '<br>', '<br/>', '<br />', '</p>', '</div>' ), "\n", $body ) );
}

function integral_log_mail_failure( $error ) {
	if ( ! is_wp_error( $error ) ) {
		return;
	}
	error_log( '[integral mail] ' . $error->get_error_message() );
}

/**
 * Demo theater / plan request → branded HTML email.
 */
function integral_ajax_send_inquiry() {
	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$phone   = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$subject = isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : 'Website inquiry';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	if ( ! $name || ! is_email( $email ) || ! $message ) {
		wp_send_json_error( array( 'message' => 'Name, a valid email, and a message are required.' ), 400 );
	}

	$body    = integral_inquiry_email_html(
		array(
			'name'    => $name,
			'email'   => $email,
			'phone'   => $phone,
			'subject' => $subject,
			'message' => $message,
			'source'  => 'Website demo / plan request',
		)
	);
	$headers = integral_mail_headers_html( $name . ' <' . $email . '>' );

	$sent = wp_mail( integral_mail_recipient(), '[Integral Contact] ' . $subject, $body, $headers );
	if ( ! $sent ) {
		wp_send_json_error(
			array(
				'message' => 'Mail could not be sent. SMTP is not configured or the mail server rejected the message.',
			),
			500
		);
	}

	wp_send_json_success( array( 'message' => 'Sent. We will get back to you shortly.' ) );
}
