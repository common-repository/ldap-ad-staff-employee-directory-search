<?php
/**
 * This file contains a class with customer related functions.
 *
 * @package miniOrange_LDAP_Directory_Search
 * @subpackage Main
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Adding required files.
require_once 'class-mo-ldap-directory-search-plugin-constants.php';

if ( ! class_exists( 'MO_LDAP_Directory_Search_Customer_Setup' ) ) {
	/**
	 * MO_LDAP_Directory_Search_Customer_Setup Contains customer related functions.
	 */
	class MO_LDAP_Directory_Search_Customer_Setup {

		const TIMEOUT       = '100';
		const SUPPORT_EMAIL = 'ldapsupport@xecurify.com';

		/**
		 * Var default_customer_key
		 *
		 * @var string
		 */
		private $default_customer_key = '16555';
		/**
		 * Var default_api_key
		 *
		 * @var string
		 */
		private $default_api_key = 'fFd2XcvTGDemZvbw1bcUesNJWEqKbbUq';

		/**
		 * Function mo_ldap_ds_create_customer : Create customer.
		 *
		 * @return string
		 */
		public function mo_ldap_ds_create_customer() {

			$url = MO_LDAP_Directory_Search_Plugin_Constants::MO_LDAP_HOST_NAME . '/moas/rest/customer/add';

			$email    = get_option( 'mo_ldap_ds_admin_email' );
			$password = get_option( 'mo_ldap_ds_password' );

			$fields       = array(
				'areaOfInterest' => 'WP LDAP Directory Search',
				'email'          => $email,
				'password'       => $password,
			);
			$field_string = wp_json_encode( $fields );

			$headers = array(
				'Content-Type'  => 'application/json',
				'charset'       => 'UTF - 8',
				'Authorization' => 'Basic',
			);
			$args    = array(
				'method'      => 'POST',
				'body'        => $field_string,
				'timeout'     => self::TIMEOUT,
				'redirection' => '5',
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => $headers,
			);

			$response = wp_remote_post( $url, $args );
			if ( is_wp_error( $response ) ) {
				return wp_json_encode( array( 'status' => 'ERROR' ) );
			}
			return $response['body'];
		}

		/**
		 * Function mo_ldap_ds_get_customer_key : get the customer key of the user.
		 *
		 * @return string
		 */
		public function mo_ldap_ds_get_customer_key() {

			$url = MO_LDAP_Directory_Search_Plugin_Constants::MO_LDAP_HOST_NAME . '/moas/rest/customer/key';

			$email    = get_option( 'mo_ldap_ds_admin_email' );
			$password = get_option( 'mo_ldap_ds_password' );

			$fields       = array(
				'email'    => $email,
				'password' => $password,
			);
			$field_string = wp_json_encode( $fields );

			$headers = array(
				'Content-Type'  => 'application/json',
				'charset'       => 'UTF - 8',
				'Authorization' => 'Basic',
			);
			$args    = array(
				'method'      => 'POST',
				'body'        => $field_string,
				'timeout'     => self::TIMEOUT,
				'redirection' => '5',
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => $headers,

			);

			$response = wp_remote_post( $url, $args );
			if ( is_wp_error( $response ) ) {
				return wp_json_encode( array( 'status' => 'ERROR' ) );
			}
			return $response['body'];
		}

		/**
		 * Function mo_ldap_ds_submit_contact_us : Submit contact us form.
		 *
		 * @param  string $q_email Query email.
		 * @param  string $q_phone Query phone.
		 * @param  string $query Query content.
		 * @param  string $subject Email subject.
		 * @return string
		 */
		public function mo_ldap_ds_submit_contact_us( $q_email, $q_phone, $query, $subject ) {

			$url = MO_LDAP_Directory_Search_Plugin_Constants::MO_LDAP_HOST_NAME . '/moas/api/notify/send';

			$customer_key = $this->default_customer_key;
			$api_key      = $this->default_api_key;

			$current_time_in_millis = self::mo_ldap_ds_get_timestamp();
			$string_to_hash         = $customer_key . $current_time_in_millis . $api_key;
			$hash_value             = hash( 'sha512', $string_to_hash );
			$from_email             = $q_email;
			global $user;
			$user    = wp_get_current_user();
			$company = isset( $_SERVER['SERVER_NAME'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] ) ) : '';
			$content = '<div >First Name :' . $user->user_firstname . '<br><br>Last  Name :' . $user->user_lastname . '   <br><br>Company :<a href="' . esc_url( $company ) . '" target="_blank" >' . esc_html( $company ) . '</a><br><br>Phone Number :' . $q_phone . '<br><br>Email :<a href="mailto:' . $from_email . '" target="_blank">' . $from_email . '</a><br><br>' . $query . '</div>';

			$fields       = array(
				'customerKey' => $customer_key,
				'sendEmail'   => true,
				'email'       => array(
					'customerKey' => $customer_key,
					'fromEmail'   => $q_email,
					'bccEmail'    => self::SUPPORT_EMAIL,
					'fromName'    => 'miniOrange',
					'toEmail'     => self::SUPPORT_EMAIL,
					'toName'      => self::SUPPORT_EMAIL,
					'subject'     => $subject,
					'content'     => $content,
				),
			);
			$field_string = wp_json_encode( $fields );
			$headers      = array(
				'Content-Type'  => 'application/json',
				'Customer-Key'  => $customer_key,
				'Timestamp'     => $current_time_in_millis,
				'Authorization' => $hash_value,
			);
			$args         = array(
				'method'      => 'POST',
				'body'        => $field_string,
				'timeout'     => self::TIMEOUT,
				'redirection' => '5',
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => $headers,

			);

			$response = wp_remote_post( $url, $args );
			if ( is_wp_error( $response ) ) {
				return wp_json_encode( array( 'status' => 'ERROR' ) );
			}
			return $response['body'];
		}

		/**
		 * Function mo_ldap_ds_check_customer : Check the current customer.
		 *
		 * @return string
		 */
		public function mo_ldap_ds_check_customer() {

			$url   = MO_LDAP_Directory_Search_Plugin_Constants::MO_LDAP_HOST_NAME . '/moas/rest/customer/check-if-exists';
			$email = get_option( 'mo_ldap_ds_admin_email' );

			$fields       = array(
				'email' => $email,
			);
			$field_string = wp_json_encode( $fields );
			$headers      = array(
				'Content-Type'  => 'application/json',
				'charset'       => 'UTF - 8',
				'Authorization' => 'Basic',
			);
			$args         = array(
				'method'      => 'POST',
				'body'        => $field_string,
				'timeout'     => self::TIMEOUT,
				'redirection' => '5',
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => $headers,

			);

			$response = wp_remote_post( $url, $args );
			if ( is_wp_error( $response ) ) {
				return wp_json_encode( array( 'status' => 'ERROR' ) );
			}
			return $response['body'];
		}

		/**
		 * Function mo_ldap_ds_get_timestamp : Get the timestamp.
		 *
		 * @return string
		 */
		public function mo_ldap_ds_get_timestamp() {

			$url = MO_LDAP_Directory_Search_Plugin_Constants::MO_LDAP_HOST_NAME . '/moas/rest/mobile/get-timestamp';

			$response = wp_remote_post( $url );
			if ( is_wp_error( $response ) ) {
				$current_time_in_millis = round( microtime( true ) * 1000 );
				$current_time_in_millis = number_format( $current_time_in_millis, 0, '', '' );
				return $current_time_in_millis;
			} else {
				return $response['body'];
			}
		}
	}
}
