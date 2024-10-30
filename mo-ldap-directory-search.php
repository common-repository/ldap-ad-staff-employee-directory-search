<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Staff/Employee Business Directory for Active Directory
 *
 * This plugin allows you to search and display the users present in your Active Directory on a WordPress webpage using a shortcode.
 *
 * @package miniOrange_LDAP_Directory_Search
 * @subpackage Main
 */

/**
 * Plugin Name: Staff/Employee Business Directory for Active Directory
 * Description: Search and Display the users present in your LDAP/Active Directory on a WordPress page using a shortcode.
 * Author: miniOrange
 * Version: 1.4.2
 * Author URI: https://miniorange.com
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once 'mo-ldap-directory-search-pages.php';
require_once 'class-mo-ldap-directory-search-utility.php';
require_once 'class-mo-ldap-directory-search-config.php';
require_once 'class-mo-ldap-directory-search-customer-setup.php';
require_once 'mo-ldap-directory-search-feedback-form.php';
require_once 'class-mo-ldap-directory-search-plugin-constants.php';

if ( ! class_exists( 'MO_LDAP_Directory_Search' ) ) {
	/**
	 * MO_LDAP_Directory_Search : This is the main plugin class that contains all the plugin functions.
	 */
	class MO_LDAP_Directory_Search {

		/**
		 * __construct
		 *
		 * @return void
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'mo_ldap_dir_search_menu' ) );
			add_action( 'admin_init', array( $this, 'mo_ldap_dir_search_save_settings' ) );
			add_action( 'init', array( $this, 'mo_ldap_possible_search_bases' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'include_dir_search_settings_style' ) ); // For Plugin Page UI.
			add_shortcode( 'miniorange_ldap_directory_search', array( $this, 'show_miniorange_directory_search' ) );
			add_action( 'wp_ajax_dir_search_fetch_records', array( $this, 'mo_ldap_ds_fetch_records' ) );
			add_action( 'wp_ajax_nopriv_dir_search_fetch_records', array( $this, 'mo_ldap_ds_fetch_records' ) );
			remove_action( 'admin_notices', array( $this, 'success_message' ) );
			remove_action( 'admin_notices', array( $this, 'error_message' ) );
			register_activation_hook( __FILE__, array( $this, 'mo_ldap_directory_search_activate' ) );
			register_deactivation_hook( __FILE__, array( $this, 'mo_ldap_directory_search_deactivate' ) );
			add_action( 'admin_footer', array( $this, 'mo_ldap_directory_search_feedback_request' ) );
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'mo_ldap_ds_links' ) );
		}

		/**
		 * Function mo_ldap_ds_links : Display the settings icon in the plugin section of the admin dashboard.
		 *
		 * @param  array $links array of links to display.
		 * @return array
		 */
		public function mo_ldap_ds_links( $links ) {
			$links = array_merge(
				array(
					'<a href="' . esc_url( admin_url( '?page=miniorange-ldap-directory-search-settings' ) ) . '">' . __( 'Settings', 'miniorange-ldap-directory-search-settings' ) . '</a>',
				),
				$links
			);
			return $links;
		}

		/**
		 * Function mo_ldap_directory_search_feedback_request : Displays the deactivation feedback form.
		 *
		 * @return void
		 */
		public function mo_ldap_directory_search_feedback_request() {
			mo_ldap_directory_search_display_feedback_form();
		}

		/**
		 * Function mo_ldap_directory_search_activate : Handles the flow at the time of activation.
		 *
		 * @return void
		 */
		public function mo_ldap_directory_search_activate() {
			update_option( 'mo_ldap_ds_config_status', '0' );
			$mo_ldap_token_key = get_option( 'mo_ldap_ds_customer_token' );
			if ( empty( $mo_ldap_token_key ) ) {
				update_option( 'mo_ldap_ds_customer_token', MO_LDAP_Directory_Search_Utility::DEFAULT_CUSTOMER_TOKEN );
			}
		}

		/**
		 * Function mo_ldap_directory_search_deactivate : Handles the flow at the time of deactivation.
		 *
		 * @return void
		 */
		public function mo_ldap_directory_search_deactivate() {
			delete_option( 'mo_ldap_ds_show_message' );

			wp_safe_redirect( 'plugins.php' );
		}

		/**
		 * Function mo_ldap_possible_search_bases : Opens a new browser window that displays all possible search bases in AD.
		 *
		 * @return void
		 */
		public function mo_ldap_possible_search_bases() {
			if ( isset( $_REQUEST['option'] ) && 'mo_ldap_directory_search_search_base_list' === $_REQUEST['option'] ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Checking if the request value is set.
				$mo_ldap_config = new MO_LDAP_Directory_Search_Config();
				$mo_ldap_config->show_search_bases_list();
			}
		}

		/**
		 * Function mo_ldap_dir_search_menu : Add a menu icon in the admin dashboard.
		 *
		 * @return void
		 */
		public function mo_ldap_dir_search_menu() {
			add_menu_page( 'Staff/Employee Business Directory for Active Directory', 'Staff/Employee Business Directory for Active Directory', 'activate_plugins', 'miniorange-ldap-directory-search-settings', array( $this, 'mo_ldap_directory_search_widget_options' ), plugin_dir_url( __FILE__ ) . 'includes/images/miniorange_icon.png' );
			wp_enqueue_style( 'mo_ldap_admin_font_awsome', plugins_url( 'includes/fonts/css/font-awesome.min.css', __FILE__ ), array(), MO_LDAP_Directory_Search_Plugin_Constants::PLUGIN_VERSION );
		}

		/**
		 * Function mo_ldap_directory_search_widget_options : Callback function which handles the menu button functionality.
		 *
		 * @return void
		 */
		public function mo_ldap_directory_search_widget_options() {
			mo_ldap_directory_search_settings();
		}

		/**
		 * Function show_miniorange_directory_search : Renders the shortcode UI.
		 *
		 * @return string
		 */
		public function show_miniorange_directory_search() {
			wp_enqueue_script( 'directory-search-js', plugins_url( '/includes/js/mo-ldap-directory-search-plugin.min.js', __FILE__ ), array( 'jquery' ), MO_LDAP_Directory_Search_Plugin_Constants::PLUGIN_VERSION, false );
			wp_enqueue_style( 'directory-search-style', plugins_url( '/includes/css/mo-ldap-directory-search-page.min.css', __FILE__ ), array(), MO_LDAP_Directory_Search_Plugin_Constants::PLUGIN_VERSION );
			$mo_ldap_ds_custom_css_array = ! empty( get_option( 'mo_ldap_ds_custom_styling' ) ) ? maybe_unserialize( get_option( 'mo_ldap_ds_custom_styling' ) ) : array();
			wp_localize_script(
				'directory-search-js',
				'mo_ldap_ds_search_data',
				array(
					'site_url'                 => get_site_url(),
					'mo_ldap_ds_styling_array' => $mo_ldap_ds_custom_css_array,
				)
			);
			$search_option_labels = ! empty( get_option( 'mo_ldap_ds_search_by_options' ) ) ? maybe_unserialize( get_option( 'mo_ldap_ds_search_by_options' ) ) : array();

			$filter_attributes_array = get_option( 'mo_ldap_ds_config' ) ? maybe_unserialize( get_option( 'mo_ldap_ds_config' ) ) : array();

			$search_options = array();

			foreach ( $filter_attributes_array as $filter_attribute ) {
				if ( in_array( $filter_attribute['lable'], $search_option_labels, true ) ) {
					$search_options[ $filter_attribute['value'] ] = $filter_attribute['lable'];
				}
			}

			$attributes_arr = array(
				'mail',
				'telephonenumber',
				'hometelephone',
				'othertelephone',
				'mobile',
				'mobiletelephonenumber',
			);

			$search_options_html = '';

			foreach ( $search_options as $key => $display_option ) {
				if ( ! in_array( $key, $attributes_arr, true ) ) {
					$search_options_html .= '<option value="' . $key . '">' . $display_option . '</option>';
				}
			}
			$html = '<div class="mo-ldap-dir-search"><div class="mo-ldap-search-window">
						<h2 class="mo_ldap_search_employees_heading"> Search Employees </h2>
						<table class="mo-ldap-search-window-table">
							<tr>
							<td> <p class="search_by_search_value_labels" style="padding: 0px;margin: 0px;text-align: left;width:43%;"> Search By: </p> 
								
									<select id="modirsearchfield" class="modirsearchfield" type="text">';

			foreach ( $search_options as $key => $display_option ) {
				$html .= '<option value="' . $key . '">' . $display_option . '</option>';
			}

									$html .= '</select>
								</td>
						

								<td> <p class="search_by_search_value_labels" style="padding: 0px;margin: 0px;text-align: left;width:43%;">  Search Value: </p> 
								<input id="modirsearchstring" class="modirsearchstring" type ="text"></td>
								<td><button id="dirsearchbutton" type="button"  class="dirsearchbutton">Search</button></td>
								
								<td></td>
							</tr>
							<tr>
								<td><input type="hidden" value="' . site_url() . '/wp-admin/admin-ajax.php" id="ajaxcallurl"/></td>
							</tr>
						
						</table>
					</div>
						<div class="mo-dir-search-result-div" id="moldapdirsearchappendppoint"></div>
					</div>';
			return $html;
		}

		/**
		 * Function mo_ldap_ds_create_search_filter : Creates the search filter.
		 *
		 * @param  string $search_field Search Field.
		 * @param  string $search_value Search Value.
		 * @return string
		 */
		private function mo_ldap_ds_create_search_filter( $search_field, $search_value ) {
			$search_options = ! empty( get_option( 'mo_ldap_ds_search_by_options' ) ) ? maybe_unserialize( get_option( 'mo_ldap_ds_search_by_options' ) ) : array();
			$filter_block   = '';

			if ( ! empty( $search_options ) ) {
				foreach ( $search_options as $key => $search_attr ) {
					if ( $key === $search_field ) {
						$filter_block = $filter_block . '(' . $key . '=' . $search_value . '*)';
					}
				}
			}

			return $filter_block;
		}

		/**
		 * Function mo_ldap_ds_fetch_records : Fetch records from LDAP.
		 *
		 * @return object
		 */
		public function mo_ldap_ds_fetch_records() {
			if ( isset( $_REQUEST ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Checking if the request value is set.
				if ( ! MO_LDAP_Directory_Search_Utility::is_extension_installed( 'ldap' ) ) {
					echo wp_json_encode( 'ldap_extension_is_not_installed' );
					die();
				}
				$search_field_parameter = isset( $_REQUEST['selectValue'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['selectValue'] ) ) : '';  //phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Checking if the request value is set.
				$search_string_value    = isset( $_REQUEST['searchValue'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['searchValue'] ) ) : '';  //phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Checking if the request value is set.
				$search_string_value    = ldap_escape( $search_string_value, '', LDAP_ESCAPE_FILTER );

				if ( strcasecmp( $search_field_parameter, 'mo_ldap_default_call' ) === 0 || strcasecmp( $search_string_value, 'mo_ldap_default_call' ) === 0 ) {
					echo wp_json_encode( 'no_request' );
					die();
				}

				$search_base_string = ! empty( get_option( 'mo_ldap_ds_search_base' ) ) ? MO_LDAP_Directory_Search_Utility::decrypt( get_option( 'mo_ldap_ds_search_base' ) ) : '';
				$server_name        = ! empty( get_option( 'mo_ldap_ds_server_url' ) ) ? MO_LDAP_Directory_Search_Utility::decrypt( get_option( 'mo_ldap_ds_server_url' ) ) : '';
				$ldap_bind_dn       = ! empty( get_option( 'mo_ldap_ds_server_dn' ) ) ? MO_LDAP_Directory_Search_Utility::decrypt( get_option( 'mo_ldap_ds_server_dn' ) ) : '';
				$ldap_bind_password = ! empty( get_option( 'mo_ldap_ds_server_password' ) ) ? MO_LDAP_Directory_Search_Utility::decrypt( get_option( 'mo_ldap_ds_server_password' ) ) : '';

				$ldap_directory_server_value = get_option( 'mo_ldap_ds_directory_server_value' ) ? get_option( 'mo_ldap_ds_directory_server_value' ) : 'msad';
				if ( 'msad' === $ldap_directory_server_value ) {
					$filter = '(&(objectClass=user)(mosearchfield=?))';
				} elseif ( 'openldap' === $ldap_directory_server_value ) {
					$filter = '(&(objectClass=person)(mosearchfield=?))';
				} elseif ( 'freeipa' === $ldap_directory_server_value ) {
					$filter = '(&(|(objectClass=person)(objectClass=inetuser)(objectClass=organizationalperson)(objectClass=inetOrgPerson)(objectClass=posixaccount))(mosearchfield=?))';
				} else {
					$filter = '(&(objectClass=*)(mosearchfield=?))';
				}

				$filter_block = $this->mo_ldap_ds_create_search_filter( $search_field_parameter, $search_string_value );
				$filter       = str_replace( '(mosearchfield=?)', $filter_block, $filter );

				$ldapconn           = $this->get_connection_with( $server_name );
				$mo_ldap_dir_config = get_option( 'mo_ldap_ds_config' ) ? maybe_unserialize( get_option( 'mo_ldap_ds_config' ) ) : array();
				$send_data          = array();

				if ( $ldapconn ) {
					$bind = @ldap_bind( $ldapconn, $ldap_bind_dn, $ldap_bind_password ); //phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- Used to silence LDAP errors; handled them below using MO_LDAP_Directory_Search_Status_Response class
					$err  = ldap_error( $ldapconn );
					if ( strtolower( $err ) !== 'success' ) {
						echo wp_json_encode( 'connection_error' );
						die();
					}

					$attr              = array();
					$attr_values_array = array();

					foreach ( $mo_ldap_dir_config as $key => $attri_data ) {
						$attr[ $attri_data['lable'] ] = $attri_data['value'];
						array_push( $attr_values_array, $attri_data['value'] );
					}

					$entries = array();
					if ( ldap_search( $ldapconn, $search_base_string, $filter ) ) {
						$user_search_result = ldap_search( $ldapconn, $search_base_string, $filter, $attr_values_array );
					} else {
						echo wp_json_encode( 'search_base_error' );
						die();
					}
					$entry = ldap_get_entries( $ldapconn, $user_search_result );
					array_push( $entries, $entry );

					$result_obj                  = array();
					$pro_pic                     = 'none';
					$result_obj['mo_ldap_photo'] = $pro_pic;
					$user_field_data             = '';

					foreach ( $entries as $users ) {
						foreach ( $users as $user ) {
							foreach ( $attr as $key => $attr_values ) {
								if ( $attr_values && isset( $user[ $attr_values ] ) ) {
									$user_field_data = $user[ $attr_values ][0];
								}
								$user_field_data = ! empty( $user_field_data ) ? $user_field_data : 'nodata';

								$result_obj[ $key ] = esc_html( $user_field_data );
								if ( 'thumbnailphoto' === $attr_values ) {

									if ( 'nodata' === $user_field_data ) {
										$result_obj['mo_ldap_photo'] = 'none';
									} else {
										$result_obj['mo_ldap_photo'] = base64_encode( $user_field_data ); //phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- function not being used to obfuscate the code
									}
								}
								$user_field_data = '';
							}
							array_push( $send_data, $result_obj );
						}
					}

					unset( $send_data[0] );
					if ( count( $send_data ) === 0 ) {
						echo wp_json_encode( 'no_records_found' );
					}
				} else {
					echo wp_json_encode( 'Invalid Configuration. Could not connect to LDAP.' );
				}

				if ( ! empty( $send_data ) ) {
					echo wp_json_encode( $send_data );
				}
			} else {
				echo wp_json_encode( 'Error in Processing AJAX request' );
			}
			die();
		}

		/**
		 * Function get_connection_with : Get LDAP connection.
		 *
		 * @param  string $server_name Server Name.
		 * @return object
		 */
		private function get_connection_with( $server_name ) {

			if ( ! MO_LDAP_Directory_Search_Utility::is_extension_installed( 'openssl' ) ) {
				return null;
			}

			$ldapconn = ldap_connect( $server_name );
			if ( version_compare( PHP_VERSION, '5.3.0' ) >= 0 ) {
				ldap_set_option( $ldapconn, LDAP_OPT_NETWORK_TIMEOUT, 5 );
			}

			ldap_set_option( $ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3 );
			ldap_set_option( $ldapconn, LDAP_OPT_REFERRALS, 0 );
			return $ldapconn;
		}

		/**
		 * Function mo_ldap_dir_search_save_settings : Save options received through get or post requests.
		 *
		 * @return void
		 */
		public function mo_ldap_dir_search_save_settings() {
			if ( isset( $_POST['option'] ) && 'mo_ldap_save_directory_search_config' === $_POST['option'] && check_admin_referer( 'mo_ldap_save_directory_search_config_nonce' ) ) {
				$mo_ldap_attri_info    = array();
				$mo_ldap_attri_info[0] = array(
					'lable' => 'mo_ldap_photo',
					'value' => 'thumbnailphoto',
				);
				$attribute_count       = 1;
				$labels_array          = array( 'Name', 'Email', 'Phone' );

				if ( isset( $_POST['mo_ldap_dir_attri_lable_name_4'] ) ) {
					array_push( $labels_array, sanitize_text_field( wp_unslash( $_POST['mo_ldap_dir_attri_lable_name_4'] ) ) );
				}

				$search_vals_opt = ! empty( maybe_unserialize( get_option( 'mo_ldap_ds_search_by_options' ) ) ) ? maybe_unserialize( get_option( 'mo_ldap_ds_search_by_options' ) ) : array();

				$current_attribute_key   = $labels_array[0];
				$current_attribute_value = isset( $_POST[ 'mo_ldap_dir_attri_value_' . $attribute_count ] ) ? strtolower( sanitize_text_field( wp_unslash( $_POST[ 'mo_ldap_dir_attri_value_' . $attribute_count ] ) ) ) : '';
				$current_attribute_value = str_replace( ' ', '', $current_attribute_value );
				while ( isset( $current_attribute_key ) ) {
					if ( ! empty( $current_attribute_key ) ) {
						$mo_ldap_attri_info[ $attribute_count ] = array(
							'lable' => $current_attribute_key,
							'value' => $current_attribute_value,
						);

						$existing_key = array_search( $current_attribute_key, $search_vals_opt, true );

						if ( false !== $existing_key ) {
							unset( $search_vals_opt[ $existing_key ] );
							$search_vals_opt[ $current_attribute_value ] = $current_attribute_key;
						}
					}
					$attribute_count++;

					if ( isset( $_POST[ 'mo_ldap_dir_attri_lable_name_' . $attribute_count ] ) ) {
						$current_attribute_key   = $labels_array[ $attribute_count - 1 ];
						$current_attribute_value = strtolower( sanitize_text_field( wp_unslash( $_POST[ 'mo_ldap_dir_attri_value_' . $attribute_count ] ) ) );
					} else {
						break;
					}
				}

				update_option( 'mo_ldap_ds_search_by_options', maybe_serialize( $search_vals_opt ) );
				update_option( 'mo_ldap_ds_config', maybe_serialize( $mo_ldap_attri_info ) );
				update_option( 'mo_ldap_ds_config_status', '1' );
				update_option( 'mo_ldap_ds_show_message', 'The configuration has been saved successfully.' );
				$this->show_success_message();

			} elseif ( isset( $_POST['option'] ) && 'mo_ldap_save_directory_search_option_config' === $_POST['option'] && check_admin_referer( 'mo_ldap_save_directory_search_option_config_nonce' ) ) {
				$filter_attributes_array = ! empty( get_option( 'mo_ldap_ds_config' ) ) ? maybe_unserialize( get_option( 'mo_ldap_ds_config' ) ) : array();
				$search_vals_opt         = array();
				$fixed                   = array( 'Email', 'Phone' );

				foreach ( $_POST as $key => $value ) {
					$key = sanitize_key( $key );
					foreach ( $filter_attributes_array as $search_val ) {
						if ( ( strcasecmp( $key, $search_val['value'] ) === 0 ) && ! in_array( $search_val['lable'], $fixed, true ) ) {
							$search_vals_opt[ $key ] = $search_val['lable'];
						}
					}
				}

				update_option( 'mo_ldap_ds_search_by_options', maybe_serialize( $search_vals_opt ) );
				update_option( 'mo_ldap_ds_show_message', 'Search labels have been saved successfully.' );
				$this->show_success_message();
			} elseif ( isset( $_POST['option'] ) && ( 'mo_ldap_directory_search_tls_enable' === $_POST['option'] ) && check_admin_referer( 'mo_ldap_directory_search_tls_enable_nonce' ) ) {
				$enable_tls = isset( $_POST['mo_ldap_directory_search_tls_enable'] ) ? sanitize_text_field( wp_unslash( $_POST['mo_ldap_directory_search_tls_enable'] ) ) : '0';
				update_option( 'mo_ldap_ds_use_tls', $enable_tls );
				if ( '1' === $enable_tls ) {
					update_option( 'mo_ldap_ds_show_message', 'TLS has been enabled.' );
					$this->show_success_message();
				} else {
					update_option( 'mo_ldap_ds_show_message', 'TLS has been disabled.' );
					$this->show_error_message();
				}
			} elseif ( isset( $_POST['option'] ) && ( 'mo_ldap_directory_search_ldap_connection' === $_POST['option'] ) && check_admin_referer( 'mo_ldap_directory_search_ldap_connection_nonce' ) ) {
				$server_name         = '';
				$dn                  = '';
				$admin_ldap_password = '';
				if ( ! isset( $_POST['mo_ldap_directory_search_ldap_server'] ) || ! isset( $_POST['mo_ldap_directory_search_dn'] ) || ! isset( $_POST['mo_ldap_directory_search_admin_password'] ) || ! isset( $_POST['mo_ldap_directory_search_protocol'] ) || ! isset( $_POST['mo_ldap_directory_search_server_port_no'] ) ) {
					update_option( 'mo_ldap_ds_show_message', 'All the fields are required. Please enter valid entries.' );
					$this->show_error_message();
					return;
				} else {
					$ldap_protocol       = sanitize_text_field( wp_unslash( $_POST['mo_ldap_directory_search_protocol'] ) );
					$port_number         = sanitize_text_field( wp_unslash( $_POST['mo_ldap_directory_search_server_port_no'] ) );
					$server_address      = sanitize_text_field( wp_unslash( $_POST['mo_ldap_directory_search_ldap_server'] ) );
					$server_name         = $ldap_protocol . '://' . $server_address . ':' . $port_number;
					$dn                  = sanitize_text_field( wp_unslash( $_POST['mo_ldap_directory_search_dn'] ) );
					$admin_ldap_password = sanitize_text_field( wp_unslash( $_POST['mo_ldap_directory_search_admin_password'] ) );

				}

				if ( ! MO_LDAP_Directory_Search_Utility::is_extension_installed( 'openssl' ) ) {
					update_option( 'mo_ldap_ds_show_message', 'PHP OpenSSL extension is not installed or disabled. Please enable it first.' );
					$this->show_error_message();
				} else {
					$directory_server_value = isset( $_POST['mo_ldap_directory_search_directory_server_value'] ) ? sanitize_text_field( wp_unslash( $_POST['mo_ldap_directory_search_directory_server_value'] ) ) : '';
					if ( strcasecmp( $directory_server_value, 'other' ) === 0 ) {
						$directory_server_custom_value = isset( $_POST['mo_ldap__directory_search_directory_server_custom_value'] ) && ! empty( $_POST['mo_ldap__directory_search_directory_server_custom_value'] ) ? sanitize_text_field( wp_unslash( $_POST['mo_ldap__directory_search_directory_server_custom_value'] ) ) : 'other';
						update_option( 'mo_ldap_ds_directory_server_custom_value', $directory_server_custom_value );
					} else {
						delete_option( 'mo_ldap_ds_directory_server_custom_value' );
					}

					update_option( 'mo_ldap_ds_directory_server_value', $directory_server_value );
					update_option( 'mo_ldap_ds_ldap_protocol', $ldap_protocol );
					update_option( 'mo_ldap_ds_ldap_server_address', MO_LDAP_Directory_Search_Utility::encrypt( $server_address ) );

					if ( 'ldap' === $ldap_protocol ) {
						update_option( 'mo_ldap_ds_ldap_port_number', $port_number );
					} elseif ( 'ldaps' === $ldap_protocol ) {
						update_option( 'mo_ldap_ds_ldaps_port_number', $port_number );
						update_option( 'mo_ldap_ds_use_tls', 0 );
					}

					update_option( 'mo_ldap_ds_server_url', MO_LDAP_Directory_Search_Utility::encrypt( $server_name ) );
					update_option( 'mo_ldap_ds_server_dn', MO_LDAP_Directory_Search_Utility::encrypt( $dn ) );
					update_option( 'mo_ldap_ds_server_password', MO_LDAP_Directory_Search_Utility::encrypt( $admin_ldap_password ) );

					$mo_ldap_config = new MO_LDAP_Directory_Search_Config();

					$message = 'Your configuration has been saved.';

					$response = $mo_ldap_config->test_connection();

					if ( isset( $response->status_code ) && strcasecmp( $response->status_code, 'LDAP_BIND_SUCCESSFUL' ) === 0 ) {
						$troubleshooting_url = add_query_arg( array( 'tab' => 'attribute_config' ), htmlentities( isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '' ) );
						update_option( 'mo_ldap_ds_show_message', ' Connection was established successfully. ' . $message . ' Please proceed to the <a style="font-weight:600;" href=' . esc_url( $troubleshooting_url ) . '>Attribute Configuration</a>  Tab now.', '', 'no' );
						$this->show_success_message();
					} elseif ( isset( $response->status_code ) && strcasecmp( $response->status_code, 'LDAP_BIND_ERROR' ) === 0 ) {
						update_option( 'mo_ldap_ds_show_message', $response->status_message, '', 'no' );
						$this->show_error_message();
					} elseif ( isset( $response->status_code ) && strcasecmp( $response->status_code, 'LDAP_CONNECTION_ERROR' ) === 0 ) {
						update_option( 'mo_ldap_ds_show_message', $response->status_message, '', 'no' );
						$this->show_error_message();
					} elseif ( isset( $response->status_code ) && strcasecmp( $response->status_code, 'LDAP_EXTENSION_ERROR' ) === 0 ) {
						update_option( 'mo_ldap_ds_show_message', $response->status_message, '', 'no' );
						$this->show_error_message();
					} elseif ( isset( $response->status_code ) && strcasecmp( $response->status_code, 'OPENSSL_EXTENSION_ERROR' ) === 0 ) {
						update_option( 'mo_ldap_ds_show_message', $response->status_message, '', 'no' );
						$this->show_error_message();
					}
				}
			} elseif ( isset( $_POST['option'] ) && ( 'mo_ldap_directory_search_save_user_mapping' === $_POST['option'] ) && check_admin_referer( 'mo_ldap_directory_search_save_user_mapping_nonce' ) ) {
				if ( ! isset( $_POST['mo_ldap_directory_search_search_base'] ) ) {
					update_option( 'mo_ldap_ds_show_message', 'Search base is required.' );
					$this->show_error_message();
					return;
				} else {
					$search_base = strtolower( sanitize_text_field( wp_unslash( $_POST['mo_ldap_directory_search_search_base'] ) ) );
					if ( strpos( $search_base, ';' ) ) {
						$message = 'You have entered multiple search bases. Multiple Search Bases are supported in the <strong>Premium version</strong> of the plugin. <a href="' . esc_url( add_query_arg( array( 'tab' => 'pricing' ), htmlentities( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) ) ) . '">Click here to upgrade</a>.';
						update_option( 'mo_ldap_ds_show_message', $message );
						$this->show_error_message();
						return;
					}
				}

				update_option( 'mo_ldap_ds_search_base', MO_LDAP_Directory_Search_Utility::encrypt( $search_base ) );
				$message = 'LDAP User Mapping Configuration has been saved successfully.';
				update_option( 'mo_ldap_ds_show_message', $message, '', 'no' );
				$this->show_success_message();
			} elseif ( isset( $_POST['option'] ) && ( 'mo_ldap_directory_search_save_custom_css' === $_POST['option'] ) && check_admin_referer( 'mo_ldap_directory_search_save_custom_css_nonce' ) ) {

				$mo_ldap_dir_search_custom_styling = array();
				$bg_color                          = isset( $_POST['mo_ldap_ds_bg_color'] ) ? sanitize_text_field( wp_unslash( $_POST['mo_ldap_ds_bg_color'] ) ) : '';
				$font_color                        = isset( $_POST['mo_ldap_ds_font_color'] ) ? sanitize_text_field( wp_unslash( $_POST['mo_ldap_ds_font_color'] ) ) : '';

				$mo_ldap_dir_search_custom_styling = array(
					'background-color' => $bg_color,
					'font-color'       => $font_color,
				);
				update_option( 'mo_ldap_ds_custom_styling', $mo_ldap_dir_search_custom_styling );
				$message = 'LDAP custom styling has been saved successfully.';
				update_option( 'mo_ldap_ds_show_message', $message, '', 'no' );
				$this->show_success_message();
			} elseif ( isset( $_POST['option'] ) && ( 'mo_ldap_directory_search_send_query' === $_POST['option'] ) && check_admin_referer( 'mo_ldap_dir_search_send_query_nonce' ) ) {
				if ( ! isset( $_POST['mo_ldap_directory_search_query_email'] ) || ! isset( $_POST['mo_ldap_directory_search_query'] ) || ! filter_var( sanitize_text_field( wp_unslash( $_POST['mo_ldap_directory_search_query_email'] ) ), FILTER_VALIDATE_EMAIL ) ) {
					update_option( 'mo_ldap_ds_show_message', 'Please submit your query along with the valid email.' );
					$this->show_error_message();
					return;
				} else {
					$query = sanitize_text_field( wp_unslash( $_POST['mo_ldap_directory_search_query'] ) );
					$email = sanitize_text_field( wp_unslash( $_POST['mo_ldap_directory_search_query_email'] ) );
					$phone = isset( $_POST['mo_ldap_directory_search_query_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['mo_ldap_directory_search_query_phone'] ) ) : '';

					update_option( 'mo_ldap_ds_phone_number', $phone );

					$contact_us      = new MO_LDAP_Directory_Search_Customer_Setup();
					$current_version = MO_LDAP_Directory_Search_Plugin_Constants::PLUGIN_VERSION;
					$query           = 'Query : ' . $query . '<br><br>Current Version Installed: ' . $current_version;
					$subject         = 'Support Query - WordPress LDAP Staff/Employee Business Directory Plugin - ' . $email;
					$submitted       = json_decode( $contact_us->mo_ldap_ds_submit_contact_us( $email, $phone, $query, $subject ), true );

					if ( ! $submitted ) {
						update_option( 'mo_ldap_ds_show_message', 'There was an error in sending a query. Please send us an email on <a href=mailto:ldapsupport@xecurify.com><b>ldapsupport@xecurify.com</b></a>.' );
						$this->show_error_message();
					} else {
						update_option( 'mo_ldap_ds_show_message', 'Your query successfully sent.<br>In case we don\'t get back to you, there might be email delivery failures. You can send us email on <a href=mailto:ldapsupport@xecurify.com><b>ldapsupport@xecurify.com</b></a> in that case.' );
						$this->show_success_message();
					}
				}
			} elseif ( isset( $_POST['option'] ) && ( 'mo_ldap_dir_search_get_free_trial' === $_POST['option'] ) && check_admin_referer( 'mo_ldap_dir_search_get_free_trial_nonce' ) ) {
				if ( ! isset( $_POST['mo_ldap_dir_search_get_trial_email'] ) ) {
					update_option( 'mo_ldap_ds_show_message', 'Please submit your query along with the valid email.' );
					$this->show_error_message();
					return;
				} else {
					$query           = isset( $_POST['mo_ldap_dir_search_get_trial_desc'] ) ? sanitize_text_field( wp_unslash( $_POST['mo_ldap_dir_search_get_trial_desc'] ) ) : '';
					$email           = isset( $_POST['mo_ldap_dir_search_get_trial_email'] ) ? sanitize_text_field( wp_unslash( $_POST['mo_ldap_dir_search_get_trial_email'] ) ) : '';
					$phone           = ! empty( get_option( 'mo_ldap_ds_phone_number' ) ) ? get_option( 'mo_ldap_ds_phone_number' ) : '';
					$subject         = 'Trial Query - WordPress LDAP Staff/Employee Business Directory Plugin - ' . $email;
					$contact_us      = new MO_LDAP_Directory_Search_Customer_Setup();
					$current_version = MO_LDAP_Directory_Search_Plugin_Constants::PLUGIN_VERSION;
					$query           = $query . '<br><br>Current Version Installed: ' . $current_version;
					$submited        = json_decode( $contact_us->mo_ldap_ds_submit_contact_us( $email, $phone, $query, $subject ), true );

					if ( ! $submited ) {
						update_option( 'mo_ldap_ds_show_message', 'There was an error in sending a query. Please send us an email on <a href=mailto:ldapsupport@xecurify.com><b>ldapsupport@xecurify.com</b></a>.' );
						$this->show_error_message();
					} else {
						update_option( 'mo_ldap_ds_show_message', 'Your query successfully sent.<br>In case we dont get back to you, there might be email delivery failures. You can send us email on <a href=mailto:ldapsupport@xecurify.com><b>ldapsupport@xecurify.com</b></a> in that case.' );
						$this->show_success_message();
					}
				}
			} elseif ( isset( $_POST['option'] ) && ( 'mo_ldap_dir_search_upgrade_to_premium' === $_POST['option'] ) && check_admin_referer( 'mo_ldap_dir_search_upgrade_to_premium_nonce' ) ) {
				if ( ! isset( $_POST['mo_ldap_dir_search_query_email'] ) ) {
					update_option( 'mo_ldap_ds_show_message', 'Please submit your query along with the valid email.' );
					$this->show_error_message();
					return;
				} else {
					$query           = isset( $_POST['mo_ldap_dir_search_query_desc'] ) ? sanitize_text_field( wp_unslash( $_POST['mo_ldap_dir_search_query_desc'] ) ) : '';
					$email           = isset( $_POST['mo_ldap_dir_search_query_email'] ) ? sanitize_text_field( wp_unslash( $_POST['mo_ldap_dir_search_query_email'] ) ) : '';
					$phone           = ! empty( get_option( 'mo_ldap_ds_phone_number' ) ) ? get_option( 'mo_ldap_ds_phone_number' ) : '';
					$subject         = 'Upgrade To Premium - WordPress LDAP Staff/Employee Business Directory Plugin - ' . $email;
					$contact_us      = new MO_LDAP_Directory_Search_Customer_Setup();
					$current_version = MO_LDAP_Directory_Search_Plugin_Constants::PLUGIN_VERSION;
					$query           = $query . '<br><br>Current Version Installed: ' . $current_version;
					$submited        = json_decode( $contact_us->mo_ldap_ds_submit_contact_us( $email, $phone, $query, $subject ), true );

					if ( ! $submited ) {
						update_option( 'mo_ldap_ds_show_message', 'There was an error in sending the query. Please send us an email on <a href=mailto:ldapsupport@xecurify.com><b>ldapsupport@xecurify.com</b></a>.' );
						$this->show_error_message();
					} else {
						update_option( 'mo_ldap_ds_show_message', 'Your query successfully sent.<br>In case we don\t get back to you, there might be email delivery failures. You can send us email on <a href=mailto:ldapsupport@xecurify.com><b>ldapsupport@xecurify.com</b></a> in that case.' );
						$this->show_success_message();
					}
				}
			} elseif ( isset( $_POST['option'] ) && ( 'mo_ldap_directory_search_goto_login' === $_POST['option'] ) && check_admin_referer( 'mo_ldap_directory_search_goto_login' ) ) {
				update_option( 'mo_ldap_ds_verify_customer', 'true' );
			} elseif ( isset( $_POST['option'] ) && ( 'mo_ldap_directory_search_goto_registration' === $_POST['option'] ) && check_admin_referer( 'mo_ldap_directory_search_goto_registration_nonce' ) ) {
				delete_option( 'mo_ldap_ds_admin_email' );
				delete_option( 'mo_ldap_ds_verify_customer' );
			} elseif ( isset( $_POST['option'] ) && ( 'mo_ldap_directory_search_change_miniorange_account' === $_POST['option'] ) && check_admin_referer( 'mo_ldap_directory_search_change_miniorange_account_nonce' ) ) {
				delete_option( 'mo_ldap_ds_admin_customer_key' );
				delete_option( 'mo_ldap_ds_password' );
				delete_option( 'mo_ldap_ds_show_message' );
				delete_option( 'mo_ldap_ds_verify_customer' );
			} elseif ( isset( $_POST['option'] ) && ( 'mo_ldap_directory_search_registration' === $_POST['option'] ) && check_admin_referer( 'mo_ldap_directory_search_registration_nonce' ) ) {
				if ( ! isset( $_POST['email'] ) || ! isset( $_POST['password'] ) ) {
					update_option( 'mo_ldap_ds_show_message', 'Email and Passwords are required for registration.' );
					$this->show_error_message();
					return;
				} elseif ( ! filter_var( wp_unslash( $_POST['email'] ), FILTER_VALIDATE_EMAIL ) ) {
					update_option( 'mo_ldap_ds_show_message', 'Please enter a valid email address.' );
					$this->show_error_message();
					return;
				} elseif ( $this->check_password_pattern( wp_strip_all_tags( $_POST['password'] ) ) ) { //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- Should not be sanitized as Strong Passwords contains special characters
					update_option( 'mo_ldap_ds_show_message', 'Minimum 6 characters should be present. Maximum 15 characters should be present. Only the following symbols (!@#.$%^&*-_) should be present.' );
					$this->show_error_message();
					return;
				} else {
					$email            = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
					$password         = isset( $_POST['password'] ) ? sanitize_text_field( $_POST['password'] ) : ''; //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- Should not be sanitized as Strong Passwords contains special characters
					$confirm_password = isset( $_POST['confirmPassword'] ) ? sanitize_text_field( $_POST['confirmPassword'] ) : ''; //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- Should not be sanitized as Strong Passwords contains special characters
				}

				update_option( 'mo_ldap_ds_admin_email', $email );

				if ( strcmp( $password, $confirm_password ) === 0 ) {
					update_option( 'mo_ldap_ds_password', $password );
					$customer = new MO_LDAP_Directory_Search_Customer_Setup();
					$content  = $customer->mo_ldap_ds_check_customer();

					if ( ! empty( $content ) ) {
						$content = json_decode( $content, true );

						if ( strcasecmp( $content['status'], 'CUSTOMER_NOT_FOUND' ) === 0 ) {
							$content = $this->mo_ldap_ds_create_customer();
							if ( is_array( $content ) && array_key_exists( 'status', $content ) && 'SUCCESS' === $content['status'] ) {
								$pricing_url = add_query_arg( array( 'tab' => 'pricing' ), sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) );
								$message     = 'Your account has been created successfully. <a href="' . esc_url( $pricing_url ) . '">Click here to see our Premium Plans</a> ';
								update_option( 'mo_ldap_ds_show_message', $message );
								$this->show_success_message();
								return;
							}
						} else {
							$response = $this->get_current_customer();
							if ( is_array( $response ) && array_key_exists( 'status', $response ) && 'SUCCESS' === $response['status'] ) {
								$pricing_url = add_query_arg( array( 'tab' => 'pricing' ), sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) );
								$message     = 'Your account has been retrieved successfully. <a href="' . esc_url( $pricing_url ) . '">Click here to see our Premium Plans</a> ';
								update_option( 'mo_ldap_ds_show_message', $message );
								$this->show_success_message();
								return;
							}
						}
					} else {
						update_option( 'mo_ldap_ds_show_message', 'Password and Confirm password do not match.' );
						delete_option( 'mo_ldap_ds_verify_customer' );
						$this->show_error_message();
						return;
					}
				} else {
					update_option( 'mo_ldap_ds_show_message', 'Password and Confirm password do not match.' );
					delete_option( 'mo_ldap_ds_verify_customer' );
					$this->show_error_message();
					return;
				}
			} elseif ( isset( $_POST['option'] ) && ( 'mo_ldap_directory_search_verify_customer' === $_POST['option'] ) && check_admin_referer( 'mo_ldap_directory_search_verify_customer_nonce' ) ) {
				$email    = '';
				$password = '';
				if ( ! isset( $_POST['email'] ) || ! isset( $_POST['password'] ) ) {
					update_option( 'mo_ldap_ds_show_message', 'Email and Passwords are required for login.' );
					$this->show_error_message();
					return;
				} else {
					$email    = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
					$password = isset( $_POST['password'] ) ? sanitize_text_field( $_POST['password'] ) : ''; //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- Should not be sanitized as Strong Passwords contains special characters
				}

				update_option( 'mo_ldap_ds_admin_email', $email );
				update_option( 'mo_ldap_ds_password', $password );

				$customer = new MO_LDAP_Directory_Search_Customer_Setup();

					$content = $customer->mo_ldap_ds_get_customer_key();

				if ( ! is_null( $content ) ) {
					$customer_key = json_decode( $content, true );
					if ( json_last_error() === JSON_ERROR_NONE ) {
						$this->save_success_customer_config( $customer_key['id'], 'Your account has been retrieved successfully.' );
					} else {
						$message = 'Invalid username or password. Please try again.';
						update_option( 'mo_ldap_ds_show_message', $message );
						$this->show_error_message();
					}
					update_option( 'mo_ldap_ds_password', '' );
				}
			} elseif ( isset( $_POST['option'] ) && ( 'mo_ldap_dir_search_skip_feedback' === $_POST['option'] ) && check_admin_referer( 'mo_ldap_dir_search_skip_feedback' ) ) {
				deactivate_plugins( __FILE__ );
				update_option( 'mo_ldap_ds_show_message', 'Plugin deactivated successfully.' );
				$this->show_success_message();
			} elseif ( isset( $_POST['option'] ) && ( 'mo_ldap_dir_search_feedback' === $_POST['option'] ) && check_admin_referer( 'mo_ldap_dir_search_feedback' ) ) {
				$user                      = wp_get_current_user();
				$message                   = '';
				$deactivate_reason_message = isset( $_POST['mo_ldap_ds_query_feedback'] ) ? sanitize_textarea_field( wp_unslash( $_POST['mo_ldap_ds_query_feedback'] ) ) : '';
				$followp_needed            = isset( $_POST['mo_ldap_ds_get_reply'] ) ? sanitize_text_field( wp_unslash( $_POST['mo_ldap_ds_get_reply'] ) ) : '';

				if ( empty( $followp_needed ) ) {
					$followp_needed = 'NO';
					$message       .= '<strong><span style="color: red;">Follow up Needed : ' . $followp_needed . '</strong></span><br><br>';
				} else {
					$followp_needed = 'YES';
					$message       .= '<strong><span style="color: green;">Follow up Needed : ' . $followp_needed . '</strong></span><br><br>';
				}

				if ( ! empty( $deactivate_reason_message ) ) {
					$message .= 'Feedback: ' . $deactivate_reason_message . '<br><br>';
				}

				$current_version = MO_LDAP_Directory_Search_Plugin_Constants::PLUGIN_VERSION;
				$message        .= 'Current Version Installed: ' . $current_version . '<br><br>';

				if ( isset( $_POST['mo_ldap_ds_rate'] ) ) {
					$rate_value = sanitize_text_field( wp_unslash( $_POST['mo_ldap_ds_rate'] ) );
					$message   .= '[Rating : ' . $rate_value . ']<br>';
				}

				$email   = isset( $_POST['mo_ldap_ds_query_mail'] ) ? sanitize_email( wp_unslash( $_POST['mo_ldap_ds_query_mail'] ) ) : '';
				$phone   = ! empty( get_option( 'mo_ldap_ds_phone_number' ) ) ? get_option( 'mo_ldap_ds_phone_number' ) : '';
				$subject = 'Plugin Feedback - WordPress LDAP Staff/Employee Business Directory Plugin - ' . $email;

				if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
					$email = get_option( 'mo_ldap_ds_admin_email' );
					if ( empty( $email ) ) {
						$email = $user->user_email;
					}
				}
				$feedback_reasons = new MO_LDAP_Directory_Search_Customer_Setup();
				if ( ! is_null( $feedback_reasons ) ) {
					$submitted = json_decode( $feedback_reasons->mo_ldap_ds_submit_contact_us( $email, $phone, $message, $subject ) );
					if ( json_last_error() === JSON_ERROR_NONE ) {
						if ( is_array( $submitted ) && array_key_exists( 'status', $submitted ) && 'ERROR' === $submitted['status'] ) {
							update_option( 'mo_ldap_ds_show_message', $submitted['message'] );
							$this->show_error_message();
						} else {
							if ( ! $submitted ) {
								update_option( 'mo_ldap_ds_show_message', 'Error while submitting the query.' );
								$this->show_error_message();
							}
						}
					}
					deactivate_plugins( __FILE__ );
					update_option( 'mo_ldap_ds_show_message', 'Thank you for the feedback.' );
					$this->show_success_message();
				}
			}
		}

		/**
		 * Function include_dir_search_settings_style : Enqueue CSS files.
		 *
		 * @return void
		 */
		public function include_dir_search_settings_style() {
			wp_enqueue_style( 'include_dir_search_settings_style', plugins_url( 'includes/css/mo-ldap-directory-search-style-settings.min.css', __FILE__ ), array(), MO_LDAP_Directory_Search_Plugin_Constants::PLUGIN_VERSION );
			wp_enqueue_style( 'mo_ldap_dir_search_grid_layout', plugins_url( 'includes/css/mo-ldap-directory-search-licensing-grid.min.css', __FILE__ ), array(), MO_LDAP_Directory_Search_Plugin_Constants::PLUGIN_VERSION );
			if ( isset( $_GET['page'] ) && strcasecmp( sanitize_text_field( wp_unslash( $_GET['page'] ) ), 'miniorange-ldap-directory-search-settings' ) === 0 ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended -- fetching GET parameter for changing table layout.
				wp_enqueue_script( 'include_dir_search_settings_script', plugins_url( 'includes/js/mo-ldap-directory-search-settings-page.min.js', __FILE__ ), array( 'jquery' ), MO_LDAP_Directory_Search_Plugin_Constants::PLUGIN_VERSION, false );
				$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';

				wp_localize_script(
					'include_dir_search_settings_script',
					'mo_ldap_ds_settings_data',
					array(
						'isRegistered' => MO_LDAP_Directory_Search_Utility::is_customer_registered(),
						'myAccountTab' => add_query_arg( array( 'tab' => 'myaccount' ), htmlentities( $request_uri ) ),
					)
				);
			}
		}

		/**
		 * Function get_escape_tags_allowed : HTML tags allowed to be escaped.
		 *
		 * @return array
		 */
		private function get_escape_tags_allowed() {
			$escape_allowed = array(
				'a'      => array(
					'href'  => array(),
					'title' => array(),
				),
				'br'     => array(),
				'em'     => array(),
				'strong' => array(),
				'b'      => array(),
				'h1'     => array(),
				'h2'     => array(),
				'h3'     => array(),
				'h4'     => array(),
				'h5'     => array(),
				'h6'     => array(),
				'i'      => array(),
			);
			return $escape_allowed;
		}

		/**
		 * Function success_message : Displays the success messages.
		 *
		 * @return void
		 */
		public function success_message() {
			$class          = 'updated';
			$message        = get_option( 'mo_ldap_ds_show_message' );
			$escape_allowed = $this->get_escape_tags_allowed();
			echo "<div class='" . esc_attr( $class ) . "'><p>" . wp_kses( $message, $escape_allowed ) . '</p></div>';
		}

		/**
		 * Function error_message : Displays the error messages.
		 *
		 * @return void
		 */
		public function error_message() {
			$class          = 'error';
			$message        = get_option( 'mo_ldap_ds_show_message' );
			$escape_allowed = $this->get_escape_tags_allowed();
			echo "<div class='" . esc_attr( $class ) . "'> <p>" . wp_kses( $message, $escape_allowed ) . '</p></div>';
		}

		/**
		 * Function check_password_pattern : Check Password pattern.
		 *
		 * @param  string $password : Password to be checked.
		 * @return boolean
		 */
		public static function check_password_pattern( $password ) {
			$pattern = '/^[(\w)*(\!\@\#\$\%\^\&\*\.\-\_)*]+$/';

			return ! preg_match( $pattern, $password );
		}

		/**
		 * Function mo_ldap_ds_create_customer :
		 *
		 * @return object
		 */
		private function mo_ldap_ds_create_customer() {
			$customer     = new MO_LDAP_Directory_Search_Customer_Setup();
			$customer_key = $customer->mo_ldap_ds_create_customer();

			$response = array();

			if ( ! empty( $customer_key ) ) {
				$customer_key = json_decode( $customer_key, true );

				if ( strcasecmp( $customer_key['status'], 'CUSTOMER_USERNAME_ALREADY_EXISTS' ) === 0 ) {
					$api_response = $this->get_current_customer();
					if ( $api_response ) {
						$response['status'] = 'SUCCESS';
					} else {
						$response['status'] = 'ERROR';
					}
				} elseif ( strcasecmp( $customer_key['status'], 'SUCCESS' ) === 0 && strpos( $customer_key['message'], 'Customer successfully registered.' ) !== false ) {
					$this->save_success_customer_config( $customer_key['id'], 'Thanks for registering with the miniOrange.' );
					$response['status'] = 'SUCCESS';
					return $response;
				}
				update_option( 'mo_ldap_ds_password', '' );
				return $response;
			}
		}

		/**
		 * Function get_current_customer : get the details of the current customer.
		 *
		 * @return object
		 */
		private function get_current_customer() {
			$customer = new MO_LDAP_Directory_Search_Customer_Setup();
			$content  = $customer->mo_ldap_ds_get_customer_key();

			$response = array();

			if ( ! empty( $content ) ) {
				$customer_key = json_decode( $content, true );
				if ( json_last_error() === JSON_ERROR_NONE ) {
					$this->save_success_customer_config( $customer_key['id'], 'Your account has been retrieved successfully.' );
					update_option( 'mo_ldap_ds_password', '' );
					$response['status'] = 'SUCCESS';
				} else {
					$response['status'] = 'FAILED';
					update_option( 'mo_ldap_ds_show_message', 'You already have an account with miniOrange. Please enter a valid password.' );
					$this->show_error_message();
				}
			}

			if ( empty( $response ) ) {
				$response['status']     = 'ERROR';
				$response['status_msg'] = 'Error fetching account information.';
			}

			return $response;

		}

		/**
		 * Function save_success_customer_config : Save customer configuration.
		 *
		 * @param  string $id : Customer key.
		 * @param  string $message : Message to be displayed.
		 * @return void
		 */
		private function save_success_customer_config( $id, $message ) {
			update_option( 'mo_ldap_ds_admin_customer_key', $id );
			update_option( 'mo_ldap_ds_password', '' );
			update_option( 'mo_ldap_ds_show_message', $message );
			delete_option( 'mo_ldap_ds_verify_customer' );
			$this->show_success_message();
		}

		/**
		 * Function show_success_message : Manages the display of the success messages.
		 *
		 * @return void
		 */
		private function show_success_message() {
			remove_action( 'admin_notices', array( $this, 'error_message' ) );
			add_action( 'admin_notices', array( $this, 'success_message' ) );
		}

		/**
		 * Function show_error_message : Manages the display of the error messages.
		 *
		 * @return void
		 */
		private function show_error_message() {
			remove_action( 'admin_notices', array( $this, 'success_message' ) );
			add_action( 'admin_notices', array( $this, 'error_message' ) );
		}

	}
	new MO_LDAP_Directory_Search();
}
