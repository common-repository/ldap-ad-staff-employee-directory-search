<?php
/**
 * This file contains plugin constants to be used in the plugin.
 *
 * @package miniOrange_LDAP_Directory_Search
 * @subpackage Main
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MO_LDAP_Directory_Search_Plugin_Constants' ) ) {
	/**
	 * MO_LDAP_Directory_Search_Plugin_Constants
	 */
	class MO_LDAP_Directory_Search_Plugin_Constants {
		const PLUGIN_VERSION    = '1.4.2';
		const MO_LDAP_HOST_NAME = 'https://login.xecurify.com';
	}
}
