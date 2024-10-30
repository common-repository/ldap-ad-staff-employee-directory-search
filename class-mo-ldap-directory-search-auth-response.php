<?php
/**
 * This file contains the class for the auth response.
 *
 * @package miniOrange_LDAP_Directory_Search
 * @subpackage Main
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MO_LDAP_Directory_Search_Auth_Response' ) ) {
	/**
	 * MO_LDAP_Directory_Search_Auth_Response Contains response object variables
	 */
	class MO_LDAP_Directory_Search_Auth_Response {

		/**
		 * Var status_code
		 *
		 * @var string
		 */
		public $status_code;
		/**
		 * Var status_message
		 *
		 * @var string
		 */
		public $status_message;

	}
}
