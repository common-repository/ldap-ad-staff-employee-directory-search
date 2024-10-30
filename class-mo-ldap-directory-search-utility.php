<?php
/**
 * This file contains the class for the utility functions.
 *
 * @package miniOrange_LDAP_Directory_Search
 * @subpackage Main
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MO_LDAP_Directory_Search_Utility' ) ) {
	/**
	 * MO_LDAP_Directory_Search_Utility : Contains utility functions.
	 */
	class MO_LDAP_Directory_Search_Utility {


		const DEFAULT_CUSTOMER_TOKEN = 'MlSdBdp1';

		/**
		 * Function is_customer_registered : Check if the customer is registered.
		 *
		 * @return boolean
		 */
		public static function is_customer_registered() {
			$email        = ! empty( get_option( 'mo_ldap_ds_admin_email' ) ) ? get_option( 'mo_ldap_ds_admin_email' ) : '';
			$customer_key = ! empty( get_option( 'mo_ldap_ds_admin_customer_key' ) ) ? get_option( 'mo_ldap_ds_admin_customer_key' ) : '';
			if ( ! $email || ! $customer_key || ! is_numeric( trim( $customer_key ) ) ) {
				return 0;
			} else {
				return 1;
			}
		}

		/**
		 * Function encrypt : Encrypts the string.
		 *
		 * @param  string $str String to be encrypted.
		 * @return string
		 */
		public static function encrypt( $str ) {
			if ( ! self::is_extension_installed( 'openssl' ) ) {
				return;
			}
			$key       = get_option( 'mo_ldap_ds_customer_token' );
			$method    = 'AES-128-ECB';
			$str_crypt = openssl_encrypt( $str, $method, $key, OPENSSL_RAW_DATA || OPENSSL_ZERO_PADDING );
			return base64_encode( $str_crypt ); //phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- function not being used to obfuscate the code
		}

		/**
		 * Function decrypt : Decrypts the string.
		 *
		 * @param  string $value String to be decrypted.
		 * @return string
		 */
		public static function decrypt( $value ) {
			if ( ! self::is_extension_installed( 'openssl' ) ) {
				return;
			}
			$str_in  = base64_decode( strval( $value ) ); //phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode -- function not being used to obfuscate the code
			$key     = get_option( 'mo_ldap_ds_customer_token' );
			$method  = 'AES-128-ECB';
			$iv_size = openssl_cipher_iv_length( $method );
			$data    = substr( $str_in, $iv_size );
			$clear   = openssl_decrypt( $data, $method, $key, OPENSSL_RAW_DATA || OPENSSL_ZERO_PADDING );

			return $clear;
		}

		/**
		 * Function is_extension_installed : check if the extension is installed.
		 *
		 * @param  string $name Name of the extention.
		 * @return boolean
		 */
		public static function is_extension_installed( $name ) {
			if ( in_array( $name, get_loaded_extensions(), true ) ) {
				return true;
			} else {
				return false;
			}
		}
	}
}
