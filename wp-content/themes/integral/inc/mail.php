<?php
/**
 * Outbound mail — Contact Form 7 and theme forms use wp_mail().
 *
 * Recipients:
 *   Contact Us / Service request / Volunteer / Demo AJAX → online@integral.co.ke
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
add_action( 'wp_ajax_integral_send_quotation', 'integral_ajax_send_quotation' );
add_action( 'wp_ajax_nopriv_integral_send_quotation', 'integral_ajax_send_quotation' );
add_action( 'wp_ajax_integral_send_career', 'integral_ajax_send_career' );
add_action( 'wp_ajax_nopriv_integral_send_career', 'integral_ajax_send_career' );

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
	$to = integral_mail_cfg( 'INTEGRAL_MAIL_TO', 'online@integral.co.ke' );
	return $to ? $to : 'online@integral.co.ke';
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

function integral_mail_headers_html( $reply_to = '', $bcc = '' ) {
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
	$bcc = trim( (string) $bcc );
	if ( $bcc !== '' ) {
		$headers[] = 'Bcc: ' . $bcc;
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
              <div>Email: online@integral.co.ke</div>
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
	$phpmailer->Timeout    = 90;

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

/**
 * Career application email body.
 *
 * @param array $fields Application fields.
 */
function integral_career_email_html( array $fields ) {
	$intro = '<p style="margin:0 0 20px;">A candidate responded to an open role on the Integral careers page.</p>';
	return integral_build_branded_email_html(
		'Career Application',
		'&#128188;',
		$intro,
		'Role',
		isset( $fields['role_title'] ) ? $fields['role_title'] : 'Open role',
		'Careers response via www.integral.co.ke',
		'Applicant details:',
		array(
			'Name'     => isset( $fields['name'] ) ? $fields['name'] : '',
			'Email'    => isset( $fields['email'] ) ? $fields['email'] : '',
			'Phone'    => isset( $fields['phone'] ) ? $fields['phone'] : '',
			'CV / link'=> isset( $fields['cv_url'] ) ? $fields['cv_url'] : '',
			'Role ID'  => isset( $fields['role_id'] ) ? $fields['role_id'] : '',
			'IP'       => integral_client_ip(),
			'Device'   => integral_client_device(),
		),
		! empty( $fields['message'] ) ? integral_mail_nl2br_esc( $fields['message'] ) : ''
	);
}

/**
 * AJAX: submit a career application for an open role.
 */
function integral_ajax_send_career() {
	if ( ! function_exists( 'integral_careers_are_open' ) || ! integral_careers_are_open() ) {
		wp_send_json_error( array( 'message' => 'Applications for these roles are closed.' ), 410 );
	}

	$role_id    = isset( $_POST['role_id'] ) ? sanitize_title( wp_unslash( $_POST['role_id'] ) ) : '';
	$role_title = isset( $_POST['role_title'] ) ? sanitize_text_field( wp_unslash( $_POST['role_title'] ) ) : '';
	$name       = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$email      = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$phone      = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$cv_url     = isset( $_POST['cv_url'] ) ? esc_url_raw( wp_unslash( $_POST['cv_url'] ) ) : '';
	$message    = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	$role = function_exists( 'integral_careers_role' ) ? integral_careers_role( $role_id ) : null;
	if ( ! $role || empty( $role['is_open'] ) ) {
		wp_send_json_error( array( 'message' => 'That role is not open for applications.' ), 400 );
	}
	$role_title = $role['title'];

	if ( ! $name || ! is_email( $email ) || ! $phone || ! $message ) {
		wp_send_json_error( array( 'message' => 'Name, email, phone, and a short message are required.' ), 400 );
	}

	$body = integral_career_email_html(
		array(
			'role_id'    => $role_id,
			'role_title' => $role_title,
			'name'       => $name,
			'email'      => $email,
			'phone'      => $phone,
			'cv_url'     => $cv_url,
			'message'    => $message,
		)
	);
	$headers = integral_mail_headers_html( $name . ' <' . $email . '>' );
	$subject = '[Integral Careers] ' . $role_title . ' — ' . $name;

	$sent = wp_mail( integral_mail_recipient(), $subject, $body, $headers );
	if ( ! $sent ) {
		wp_send_json_error(
			array(
				'message' => 'Application could not be sent. Please try again or email online@integral.co.ke.',
			),
			500
		);
	}

	wp_send_json_success( array( 'message' => 'Application sent. We will review and get back to you.' ) );
}

/**
 * Format pricing schedule amounts (stored in KES thousands) as KSh labels.
 *
 * @param int|float $thousands Amount in thousands of KES.
 */
function integral_pricing_kes_label( $thousands ) {
	$amount = (int) round( (float) $thousands * 1000 );
	return 'KSh ' . number_format( $amount );
}

/**
 * Resolve KEPH / facility level to a pricing tier (2–5).
 *
 * @param mixed $raw Level from registry or form.
 * @return array{level:int,matched:bool,note:string}
 */
function integral_resolve_quote_level( $raw ) {
	$raw_s = trim( (string) $raw );
	$level = 0;
	if ( preg_match( '/([2-6])/', $raw_s, $m ) ) {
		$level = (int) $m[1];
	}
	$matched = ( $level >= 2 && $level <= 5 );
	$note    = '';
	if ( ! $matched ) {
		if ( $level === 6 ) {
			$note  = 'Facility reported as Level 6. Level 5 pricing is shown pending confirmation.';
			$level = 5;
		} elseif ( $level > 0 ) {
			$note  = 'Facility level ' . $level . ' is outside the published schedule. Level 2 pricing is shown pending confirmation.';
			$level = 2;
		} else {
			$note  = 'Facility level not confirmed from registry. Level 2 pricing is shown pending confirmation.';
			$level = 2;
		}
	}
	return array(
		'level'   => $level,
		'matched' => $matched,
		'note'    => $note,
	);
}

/**
 * Look up Cloud + On Premise tiers for a facility level from the price schedule.
 *
 * @param int $level Facility level 2–5.
 * @return array{level:int,cloud:?array,onprem:?array}
 */
function integral_quote_tiers_for_level( $level ) {
	$pricing = function_exists( 'integral_pricing' ) ? integral_pricing() : array();
	$modes   = isset( $pricing['modes'] ) ? $pricing['modes'] : array();
	$cloud   = null;
	$onprem  = null;
	foreach ( array( 'cloud', 'onprem' ) as $mode_key ) {
		if ( empty( $modes[ $mode_key ]['levels'] ) || ! is_array( $modes[ $mode_key ]['levels'] ) ) {
			continue;
		}
		foreach ( $modes[ $mode_key ]['levels'] as $tier ) {
			if ( (int) $tier['level'] === (int) $level ) {
				if ( 'cloud' === $mode_key ) {
					$cloud = $tier;
				} else {
					$onprem = $tier;
				}
				break;
			}
		}
	}
	return array(
		'level'  => (int) $level,
		'cloud'  => $cloud,
		'onprem' => $onprem,
	);
}

/**
 * Official accounting quotation HTML (customer-facing).
 *
 * @param array $fields Quotation fields.
 */
function integral_quotation_email_html( array $fields ) {
	$header = INTEGRAL_MAIL_HEADER_PURPLE;
	$footer = INTEGRAL_MAIL_FOOTER_PURPLE;

	$facility = isset( $fields['facility'] ) ? $fields['facility'] : '';
	$email    = isset( $fields['email'] ) ? $fields['email'] : '';
	$phone    = isset( $fields['phone'] ) ? $fields['phone'] : '';
	$fr_code  = isset( $fields['fr_code'] ) ? $fields['fr_code'] : '';
	$type     = isset( $fields['facility_type'] ) ? $fields['facility_type'] : '';
	$level    = isset( $fields['level'] ) ? (int) $fields['level'] : 2;
	$quote_no = isset( $fields['quote_no'] ) ? $fields['quote_no'] : '';
	$level_note = isset( $fields['level_note'] ) ? $fields['level_note'] : '';
	$cloud    = isset( $fields['cloud'] ) ? $fields['cloud'] : null;
	$onprem   = isset( $fields['onprem'] ) ? $fields['onprem'] : null;
	$client_msg = isset( $fields['message'] ) ? trim( (string) $fields['message'] ) : '';
	$when     = current_time( 'j M Y' );

	$dash = 'To be confirmed';
	$facility_d = $facility !== '' ? $facility : $dash;
	$email_d    = $email !== '' ? $email : $dash;
	$phone_d    = $phone !== '' ? $phone : $dash;
	$fr_d       = $fr_code !== '' ? $fr_code : $dash;
	$type_d     = $type !== '' ? $type : $dash;

	$snapshot = isset( $fields['snapshot'] ) && is_array( $fields['snapshot'] ) ? $fields['snapshot'] : array();
	$detail_map = array(
		'Facility'     => ! empty( $snapshot['name'] ) ? $snapshot['name'] : $facility_d,
		'FR code'      => ! empty( $snapshot['frCode'] ) ? $snapshot['frCode'] : $fr_d,
		'FID'          => isset( $snapshot['fidCode'] ) ? $snapshot['fidCode'] : '',
		'Type'         => ! empty( $snapshot['facilityType'] ) ? $snapshot['facilityType'] : $type_d,
		'Level'        => ! empty( $snapshot['level'] ) ? $snapshot['level'] : ( 'Level ' . (int) $level ),
		'Ownership'    => isset( $snapshot['ownership'] ) ? $snapshot['ownership'] : '',
		'County'       => isset( $snapshot['county'] ) ? $snapshot['county'] : '',
		'Sub-county'   => isset( $snapshot['subCounty'] ) ? $snapshot['subCounty'] : '',
		'Town'         => isset( $snapshot['town'] ) ? $snapshot['town'] : '',
		'Phone'        => ! empty( $snapshot['phone'] ) ? $snapshot['phone'] : $phone_d,
		'Email'        => ! empty( $snapshot['email'] ) ? $snapshot['email'] : $email_d,
		'SHA status'   => isset( $snapshot['shaStatus'] ) ? $snapshot['shaStatus'] : '',
		'Licence'      => isset( $snapshot['licenseStatus'] ) ? $snapshot['licenseStatus'] : '',
		'Matched as'   => isset( $snapshot['matchedType'] ) ? $snapshot['matchedType'] : '',
	);

	$details_rows = '';
	foreach ( $detail_map as $label => $value ) {
		$value = trim( (string) $value );
		if ( $value === '' ) {
			$value = $dash;
		}
		$details_rows .= '<div><strong>' . integral_mail_esc( $label ) . ':</strong> ' . integral_mail_esc( $value ) . '</div>';
	}

	$tier_rows = '';
	$schedules = array(
		'Option 1: Cloud Setup'       => $cloud,
		'Option 2: On-Premise Setup'  => $onprem,
	);
	foreach ( $schedules as $label => $tier ) {
		if ( ! is_array( $tier ) ) {
			continue;
		}
		$setup     = integral_pricing_kes_label( isset( $tier['setup'] ) ? $tier['setup'] : 0 );
		$quarterly = integral_pricing_kes_label( isset( $tier['quarterly'] ) ? $tier['quarterly'] : 0 );
		$yearly    = integral_pricing_kes_label( isset( $tier['yearly'] ) ? $tier['yearly'] : 0 );
		$year_one_q = integral_pricing_kes_label(
			( isset( $tier['setup'] ) ? (float) $tier['setup'] : 0 ) + ( ( isset( $tier['quarterly'] ) ? (float) $tier['quarterly'] : 0 ) * 4 )
		);
		$year_one_y = integral_pricing_kes_label(
			( isset( $tier['setup'] ) ? (float) $tier['setup'] : 0 ) + ( isset( $tier['yearly'] ) ? (float) $tier['yearly'] : 0 )
		);
		$tier_rows .=
			'<tr>'
			. '<td style="padding:14px 12px;border-bottom:1px solid #e5e7eb;font-weight:700;color:#111827;">' . integral_mail_esc( $label ) . '</td>'
			. '<td style="padding:14px 12px;border-bottom:1px solid #e5e7eb;text-align:right;">' . integral_mail_esc( $setup ) . '</td>'
			. '<td style="padding:14px 12px;border-bottom:1px solid #e5e7eb;text-align:right;">' . integral_mail_esc( $quarterly ) . '</td>'
			. '<td style="padding:14px 12px;border-bottom:1px solid #e5e7eb;text-align:right;">' . integral_mail_esc( $yearly ) . '</td>'
			. '</tr>'
			. '<tr>'
			. '<td colspan="4" style="padding:0 12px 14px;border-bottom:1px solid #e5e7eb;font-size:12px;color:#64748b;">'
			. 'Year-one estimate (setup + quarterly × 4): <strong>' . integral_mail_esc( $year_one_q ) . '</strong>'
			. ' &nbsp;|&nbsp; Year-one estimate (setup + yearly): <strong>' . integral_mail_esc( $year_one_y ) . '</strong>'
			. '</td>'
			. '</tr>';
	}

	$note_html = '';
	if ( $level_note !== '' ) {
		$note_html = '<p style="margin:0 0 16px;padding:12px 14px;background:#fff7ed;border-left:4px solid #f59e0b;color:#9a3412;font-size:13px;">'
			. integral_mail_esc( $level_note )
			. '</p>';
	}

	$message_html = '';
	if ( $client_msg !== '' ) {
		$message_html =
			'<div style="font-weight:700;color:#111827;margin:0 0 10px;">Client Message</div>'
			. '<table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;margin-bottom:24px;">'
			. '<tr><td style="padding:16px 18px;font-size:14px;line-height:1.7;color:#374151;">'
			. integral_mail_nl2br_esc( $client_msg )
			. '</td></tr></table>';
	}

	return '<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Integral Quotation</title>
</head>
<body style="margin:0;padding:0;background-color:#f3f4f6;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
  <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color:#f3f4f6;padding:24px 12px;">
    <tr>
      <td align="center">
        <table role="presentation" width="640" border="0" cellspacing="0" cellpadding="0" style="max-width:640px;width:100%;border-collapse:collapse;background-color:#ffffff;border:1px solid #e5e7eb;">
          <tr>
            <td style="background-color:' . $header . ';padding:32px 28px;text-align:center;color:#ffffff;">
              <div style="font-size:28px;line-height:1;margin-bottom:14px;">&#128196;</div>
              <div style="font-size:28px;font-weight:700;line-height:1.2;margin-bottom:8px;">Integral Quotation</div>
              <div style="font-size:14px;line-height:1.5;opacity:0.95;">Integral HMS &mdash; Integral Software Technology Ltd</div>
            </td>
          </tr>
          <tr>
            <td style="padding:32px 28px 24px;font-size:15px;line-height:1.7;color:#374151;">
              <p style="margin:0 0 16px;font-size:16px;font-weight:700;color:#111827;">Dear Valued Client,</p>
              <p style="margin:0 0 20px;">Please find below an official quotation for Integral Hospital Management Information System (HMIS), prepared from our published price schedule for your facility level.</p>
              ' . $note_html . '
              <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-bottom:22px;">
                <tr>
                  <td style="padding:12px 14px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;">
                    <div style="font-size:12px;color:#64748b;margin-bottom:4px;">Quotation No.</div>
                    <div style="font-size:18px;font-weight:700;color:#111827;">' . integral_mail_esc( $quote_no ) . '</div>
                    <div style="font-size:12px;color:#64748b;margin-top:8px;">Date: ' . integral_mail_esc( $when ) . ' &nbsp;|&nbsp; Validity: 30 days</div>
                  </td>
                </tr>
              </table>
              <div style="font-weight:700;color:#111827;margin:0 0 10px;">Facility Details</div>
              <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color:#eff6ff;border-left:4px solid #3b82f6;border-radius:8px;margin-bottom:24px;">
                <tr>
                  <td style="padding:16px 18px;font-size:14px;line-height:1.8;color:#1e3a8a;">
                    ' . $details_rows . '
                  </td>
                </tr>
              </table>
              <div style="font-weight:700;color:#111827;margin:0 0 10px;">Price Schedule &mdash; Level ' . (int) $level . '</div>
              <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;margin-bottom:8px;">
                <tr style="background:#f1f5f9;">
                  <th align="left" style="padding:12px;font-size:12px;color:#475569;text-transform:uppercase;">Deployment</th>
                  <th align="right" style="padding:12px;font-size:12px;color:#475569;text-transform:uppercase;">Setup (One-off)</th>
                  <th align="right" style="padding:12px;font-size:12px;color:#475569;text-transform:uppercase;">Licence (Quarterly)</th>
                  <th align="right" style="padding:12px;font-size:12px;color:#475569;text-transform:uppercase;">Licence (Yearly)</th>
                </tr>
                ' . $tier_rows . '
              </table>
              <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="margin:14px 0 18px;background-color:#f0fdf4;border-left:4px solid #16a34a;border-radius:8px;">
                <tr>
                  <td style="padding:14px 16px;font-size:14px;line-height:1.7;color:#14532d;">
                    Please choose the option that best suits your facility — <strong>Option 1: Cloud Setup</strong> or <strong>Option 2: On-Premise Setup</strong> — and respond to this quotation by emailing
                    <a href="mailto:sales@integral.co.ke" style="color:#166534;font-weight:700;">sales@integral.co.ke</a>.
                  </td>
                </tr>
              </table>
              <p style="margin:0 0 8px;font-size:13px;color:#64748b;">Amounts are in Kenya Shillings (KES) and exclude any applicable taxes unless otherwise stated. Missing client details above are marked &ldquo;To be confirmed&rdquo; and will be completed by our accounts team.</p>
              ' . $message_html . '
              <div style="font-weight:700;color:#111827;margin:20px 0 8px;">Payment Terms</div>
              <ul style="margin:0 0 20px;padding-left:18px;font-size:14px;line-height:1.7;color:#374151;">
                <li>Setup and licence fees are payable in advance.</li>
                <li>Setup covers installation, configuration, implementation, and training.</li>
                <li>Licence is billed quarterly or annually, as selected.</li>
                <li>Paybill: <strong>4023989</strong> &nbsp;|&nbsp; Account: <strong>integral</strong></li>
              </ul>
              <p style="margin:0 0 8px;">For clarification or to proceed, reply to <a href="mailto:sales@integral.co.ke" style="color:#780080;font-weight:700;">sales@integral.co.ke</a> or call <strong>+254 720 730 430</strong>.</p>
              <div style="font-weight:700;color:#111827;margin:24px 0 10px;">DHA Certification</div>
              <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="margin:0 0 20px;border:1px solid #e5e7eb;border-radius:8px;background:#f8fafc;">
                <tr>
                  <td style="padding:14px 16px;vertical-align:middle;width:72px;">
                    <img src="cid:dha-badge" alt="DHA Certified" width="64" height="64" style="display:block;border:0;border-radius:8px;" />
                  </td>
                  <td style="padding:14px 8px;vertical-align:middle;">
                    <div style="font-size:12px;color:#64748b;text-transform:uppercase;letter-spacing:0.04em;">Certificate number</div>
                    <div style="font-size:16px;font-weight:700;color:#111827;margin-top:4px;">CERT-2026-4GFA8APJ</div>
                    <div style="font-size:12px;color:#64748b;margin-top:6px;">Digital Health Agency &mdash; Kenya</div>
                  </td>
                  <td style="padding:14px 16px;vertical-align:middle;text-align:center;width:120px;">
                    <img src="cid:dha-qr" alt="Scan to verify DHA certificate" width="96" height="96" style="display:block;margin:0 auto;border:0;" />
                    <div style="font-size:11px;color:#64748b;margin-top:6px;">Scan to Verify</div>
                  </td>
                </tr>
              </table>
              <p style="margin:24px 0 0;">Yours faithfully,<br><strong>Integral Software Technology Ltd</strong><br>Accounts &amp; Sales</p>
            </td>
          </tr>
          <tr>
            <td style="background-color:' . $footer . ';padding:24px 28px;text-align:center;color:#ffffff;font-size:13px;line-height:1.7;">
              <div style="font-weight:700;margin-bottom:8px;">Integral Software Technology Ltd</div>
              <div>Email: info@integral.co.ke / online@integral.co.ke</div>
              <div>Mobile: 0720730430 / 0726871800 / 0790518958</div>
              <div>Url: www.integral.co.ke</div>
              <div style="margin-top:12px;font-size:11px;opacity:0.9;">Official quotation generated from the Integral website. DHA Certificate CERT-2026-4GFA8APJ.</div>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>';
}

/**
 * Get Quote → official Integral Quotation emailed to the client (copy to Integral).
 */
function integral_ajax_send_quotation() {
	$facility = isset( $_POST['facility'] ) ? sanitize_text_field( wp_unslash( $_POST['facility'] ) ) : '';
	$email    = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$phone    = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$fr_code  = isset( $_POST['fr_code'] ) ? sanitize_text_field( wp_unslash( $_POST['fr_code'] ) ) : '';
	$type     = isset( $_POST['facility_type'] ) ? sanitize_text_field( wp_unslash( $_POST['facility_type'] ) ) : '';
	$level_in = isset( $_POST['facility_level'] ) ? sanitize_text_field( wp_unslash( $_POST['facility_level'] ) ) : '';
	$message  = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
	$raw_snap = isset( $_POST['facility_snapshot'] ) ? wp_unslash( $_POST['facility_snapshot'] ) : '';

	$snapshot = array();
	if ( is_string( $raw_snap ) && $raw_snap !== '' ) {
		$decoded = json_decode( $raw_snap, true );
		if ( is_array( $decoded ) ) {
			$keys = array(
				'name',
				'frCode',
				'fidCode',
				'facilityType',
				'level',
				'ownership',
				'county',
				'subCounty',
				'town',
				'phone',
				'email',
				'shaStatus',
				'licenseStatus',
				'matchedType',
			);
			foreach ( $keys as $key ) {
				if ( ! isset( $decoded[ $key ] ) ) {
					continue;
				}
				$snapshot[ $key ] = sanitize_text_field( (string) $decoded[ $key ] );
			}
		}
	}

	if ( ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => 'A valid email is required to send the quotation.' ), 400 );
	}

	if ( $phone === '' && ! empty( $snapshot['phone'] ) ) {
		$phone = $snapshot['phone'];
	}
	if ( $phone === '' ) {
		wp_send_json_error( array( 'message' => 'A mobile phone number is required.' ), 400 );
	}

	$level_source = $level_in;
	if ( $level_source === '' && ! empty( $snapshot['level'] ) ) {
		$level_source = $snapshot['level'];
	}
	if ( $level_source === '' ) {
		$level_source = $type;
	}
	$resolved = integral_resolve_quote_level( $level_source );
	$tiers    = integral_quote_tiers_for_level( $resolved['level'] );

	if ( empty( $tiers['cloud'] ) && empty( $tiers['onprem'] ) ) {
		wp_send_json_error( array( 'message' => 'Pricing schedule is unavailable for this facility level.' ), 500 );
	}

	if ( $facility === '' && ! empty( $snapshot['name'] ) ) {
		$facility = $snapshot['name'];
	}
	if ( $fr_code === '' && ! empty( $snapshot['frCode'] ) ) {
		$fr_code = $snapshot['frCode'];
	}
	if ( $type === '' && ! empty( $snapshot['facilityType'] ) ) {
		$type = $snapshot['facilityType'];
	}

	$quote_no = 'QT-' . current_time( 'Ymd' ) . '-' . strtoupper( substr( md5( $email . $fr_code . microtime( true ) ), 0, 6 ) );

	$fields = array(
		'facility'      => $facility,
		'email'         => $email,
		'phone'         => $phone,
		'fr_code'       => $fr_code,
		'facility_type' => $type,
		'level'         => $resolved['level'],
		'level_note'    => $resolved['note'],
		'quote_no'      => $quote_no,
		'cloud'         => $tiers['cloud'],
		'onprem'        => $tiers['onprem'],
		'message'       => $message,
		'snapshot'      => $snapshot,
	);

	$body    = integral_quotation_email_html( $fields );
	$subject = 'Integral Quotation — Level ' . (int) $resolved['level'] . ( $facility !== '' ? ' — ' . $facility : '' );

	// From: sales@integral.co.ke | To: form email | Bcc: online@integral.co.ke
	$from_addr = 'sales@integral.co.ke';
	$bcc_addr  = 'online@integral.co.ke';
	$headers   = array(
		'Content-Type: text/html; charset=UTF-8',
		'From: Integral Software <' . $from_addr . '>',
		'Reply-To: ' . ( $facility !== '' ? ( $facility . ' <' . $email . '>' ) : $email ),
		'Bcc: ' . $bcc_addr,
	);

	// Ensure PHPMailer envelope From matches sales@ (not a cached no-reply).
	$from_filter = function () use ( $from_addr ) {
		return $from_addr;
	};
	$embed_cert = function ( $phpmailer ) {
		$assets = array(
			'dha-badge' => array( INTEGRAL_DIR . '/assets/dha-badge.jpg', 'dha-badge.jpg' ),
			'dha-qr'    => array( INTEGRAL_DIR . '/assets/dha-qr.png', 'dha-qr.png' ),
		);
		foreach ( $assets as $cid => $meta ) {
			$path = $meta[0];
			$name = $meta[1];
			if ( ! is_readable( $path ) ) {
				continue;
			}
			try {
				if ( method_exists( $phpmailer, 'addEmbeddedImage' ) ) {
					$phpmailer->addEmbeddedImage( $path, $cid, $name );
				} elseif ( method_exists( $phpmailer, 'AddEmbeddedImage' ) ) {
					$phpmailer->AddEmbeddedImage( $path, $cid, $name );
				}
			} catch ( Exception $e ) {
				error_log( '[integral mail] DHA asset embed skipped (' . $cid . '): ' . $e->getMessage() );
			}
		}
	};
	add_filter( 'wp_mail_from', $from_filter, 99 );
	add_action( 'phpmailer_init', $embed_cert, 50 );
	$sent_client = wp_mail( $email, $subject, $body, $headers );
	remove_action( 'phpmailer_init', $embed_cert, 50 );
	remove_filter( 'wp_mail_from', $from_filter, 99 );

	if ( ! $sent_client ) {
		wp_send_json_error(
			array(
				'message' => 'Could not deliver quotation to ' . $email . '. Please try again or contact online@integral.co.ke.',
			),
			500
		);
	}

	if ( function_exists( 'integral_save_marketing_contact' ) ) {
		$fields['source'] = 'quotation';
		integral_save_marketing_contact( $fields );
	}

	wp_send_json_success(
		array(
			'message'  => 'Quotation sent to ' . $email . '.',
			'quote_no' => $quote_no,
		)
	);
}
