<?php
/**
 * This file contains the class for the status response.
 *
 * @package miniOrange_LDAP_Directory_Search
 * @subpackage Main
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MO_LDAP_Directory_Search_Status_Response' ) ) {
	/**
	 * MO_LDAP_Directory_Search_Status_Response Contains response object variables.
	 */
	class MO_LDAP_Directory_Search_Status_Response {


		/**
		 * Var status
		 *
		 * @var string
		 */
		public $status;
		/**
		 * Var status_message
		 *
		 * @var string
		 */
		public $status_message;

		/**
		 * __construct
		 *
		 * @param  status $status Status.
		 * @param  status $status_message Status message.
		 * @return void
		 */
		public function __construct( $status, $status_message ) {
			$this->status         = $status;
			$this->status_message = $status_message;
		}
	}
}
