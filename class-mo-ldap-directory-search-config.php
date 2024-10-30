<?php
/**
 * This file contains a class with plugin config functions.
 *
 * @package miniOrange_LDAP_Directory_Search
 * @subpackage Main
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Adding the required files.
require_once 'class-mo-ldap-directory-search-auth-response.php';
require_once 'class-mo-ldap-directory-search-status-response.php';

if ( ! class_exists( 'MO_LDAP_Directory_Search_Config' ) ) {

	/**
	 * MO_LDAP_Directory_Search_Config Contains plugin config functions
	 */
	class MO_LDAP_Directory_Search_Config {

		/**
		 * Var ldapconn
		 *
		 * @var object
		 */
		protected $ldapconn;

		/**
		 * __construct
		 *
		 * @return void
		 */
		public function __construct() {
			$this->ldapconn = $this->get_connection();
		}

		/**
		 * Function required_extensions_installed :
		 *
		 * @return object
		 */
		private function required_extensions_installed() {
			$auth_response              = new MO_LDAP_Directory_Search_Auth_Response();
			$auth_response->status_code = 'EXTENSIONS_INSTALLED';

			if ( ! MO_LDAP_Directory_Search_Utility::is_extension_installed( 'ldap' ) ) {
				$auth_response->status_code    = 'LDAP_EXTENSION_ERROR';
				$auth_response->status_message = "<a target='_blank' href='https://faq.miniorange.com/knowledgebase/how-to-enable-php-ldap-extension/'>PHP LDAP extension</a> is not installed or disabled. Please enable it.";
			} elseif ( ! MO_LDAP_Directory_Search_Utility::is_extension_installed( 'openssl' ) ) {
				$auth_response->status_code    = 'OPENSSL_EXTENSION_ERROR';
				$auth_response->status_message = "<a target='_blank' href='http://php.net/manual/en/openssl.installation.php'>PHP OpenSSL extension</a> is not installed or disabled. Please enable it.";
			}
			return $auth_response;
		}

		/**
		 * Function get_connection :
		 *
		 * @return object
		 */
		private function get_connection() {
			$extension_response = $this->required_extensions_installed();
			if ( 'EXTENSIONS_INSTALLED' === $extension_response->status_code ) {
				$server_name = ! empty( get_option( 'mo_ldap_ds_server_url' ) ) ? MO_LDAP_Directory_Search_Utility::decrypt( get_option( 'mo_ldap_ds_server_url' ) ) : '';

				$ldapconn = ldap_connect( $server_name );
				if ( $ldapconn ) {
					if ( version_compare( PHP_VERSION, '5.3.0' ) >= 0 ) {
						ldap_set_option( $ldapconn, LDAP_OPT_NETWORK_TIMEOUT, 5 );
					}

					ldap_set_option( $ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3 );
					ldap_set_option( $ldapconn, LDAP_OPT_REFERRALS, 0 );
					return $ldapconn;
				}
			}
			return null;
		}

		/**
		 * Function show_search_bases_list :
		 *
		 * @return void
		 */
		public function show_search_bases_list() {
			if ( ! MO_LDAP_Directory_Search_Utility::is_extension_installed( 'openssl' ) ) {
				return;
			}

			$ldap_bind_dn       = ! empty( get_option( 'mo_ldap_ds_server_dn' ) ) ? MO_LDAP_Directory_Search_Utility::decrypt( get_option( 'mo_ldap_ds_server_dn' ) ) : '';
			$ldap_bind_password = ! empty( get_option( 'mo_ldap_ds_server_password' ) ) ? MO_LDAP_Directory_Search_Utility::decrypt( get_option( 'mo_ldap_ds_server_password' ) ) : '';

			if ( MO_LDAP_Directory_Search_Utility::is_extension_installed( 'ldap' ) ) {

				$ldapconn = $this->get_connection();

				if ( $ldapconn ) {
					@ldap_bind( $ldapconn, $ldap_bind_dn, $ldap_bind_password ); //phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- Used to silence LDAP error; handled them below using MO_LDAP_Directory_Search_Status_Response class
					$check_ldap_conn = get_option( 'mo_ldap_ds_service_account_status' );
					?>
				<style>
					table {
						border-collapse: collapse;
						width: 100%;
					}

					table, th, td {
						border: 1px solid black;
					}

					td {
						padding: 5px;
					}
				</style>
					<?php if ( 'VALID' === $check_ldap_conn ) { ?>
					<div style="color: #3c763d;background-color: #dff0d8; padding:2%;margin-bottom:20px;text-align:center; border:1px solid #AEDB9A; font-size:18pt;">
						List of Search Base(s)
					</div>
					<div style="display:block;text-align:center;margin-bottom:4%;"><img style="width:15%;" alt="Image not found" src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'includes/images/green_check.png' ); ?>"/>
					</div>
					<span><strong> &nbsp; &nbsp; Select your Search Base(s)/Base DNs from the below Search bases list: </strong></span></br></br>

					<div style="padding:0 3%;">
					<form method="post" action="">
						<?php wp_nonce_field( 'mo_ldap_directory_search_submit_search_base_nonce' ); ?>
						<table aria-hidden="true">
							<?php

							$previous_search_bases = ! empty( get_option( 'mo_ldap_ds_search_base' ) ) ? MO_LDAP_Directory_Search_Utility::decrypt( get_option( 'mo_ldap_ds_search_base' ) ) : '';
							$search_base_list      = array();
							$result                = ldap_read( $ldapconn, '', '(objectclass=*)', array( 'namingContexts' ) );
							$data                  = ldap_get_entries( $ldapconn, $result );
							$count                 = $data[0]['namingcontexts']['count'];
							for ( $i = 0; $i < $count; $i++ ) {
								if ( 0 === $i ) {
									$base_dn = $data[0]['namingcontexts'][ $i ];
								}
								$valuetext = $data[0]['namingcontexts'][ $i ];

								if ( strcasecmp( $valuetext, $previous_search_bases ) === 0 ) {
									echo "<tr><td><input type='radio' class='select_search_bases' name='mo_ldap_directory_search_select_ldap_search_bases[]' value='" . esc_attr( $valuetext ) . "' checked>" . esc_html( $valuetext ) . '</td></tr>';
									array_push( $search_base_list, $data[0]['namingcontexts'][ $i ] );
								} else {
									echo "<tr><td><input type='radio' class='select_search_bases' name='mo_ldap_directory_search_select_ldap_search_bases[]' value='" . esc_attr( $valuetext ) . "'>" . esc_html( $valuetext ) . '</td></tr>';
									array_push( $search_base_list, $data[0]['namingcontexts'][ $i ] );
								}
							}

							$filter      = '(|(objectclass=organizationalUnit)(&(objectClass=top)(cn=users)))';
							$search_attr = array( 'dn', 'ou' );
							$ldapsearch  = ldap_search( $ldapconn, $base_dn, $filter, $search_attr );
							$info        = ldap_get_entries( $ldapconn, $ldapsearch );

							for ( $i = 0; $i < $info['count']; $i++ ) {
								$textvalue = $info[ $i ]['dn'];
								if ( ( strcasecmp( $textvalue, $previous_search_bases ) ) === 0 ) {
									echo "<tr><td><input type='radio' class='select_search_bases' name='mo_ldap_directory_search_select_ldap_search_bases[]' value='" . esc_attr( $textvalue ) . "' checked>" . esc_html( $textvalue ) . '</td></tr>';
									array_push( $search_base_list, $info[ $i ]['dn'] );
								} else {
									echo "<tr><td><input type='radio' class='select_search_bases' name='mo_ldap_directory_search_select_ldap_search_bases[]' value='" . esc_attr( $textvalue ) . "'>" . esc_html( $textvalue ) . '</td></tr>';
									array_push( $search_base_list, $info[ $i ]['dn'] );
								}
							}
							?>
							</table><br>

						<div style="margin:3%;display:block;text-align:center;">
							<table style="border: none; width: 50%" aria-hidden="true">
								<tr style="border: none;"><td style="border: none;"> <input style="padding:1%;height:30px;width:100px;background: #0091CD none repeat scroll 0% 0%;cursor: pointer;font-size:15px;border-width: 1px;border-style: solid;border-radius: 3px;white-space: nowrap;box-sizing: border-box;border-color: #0073AA;box-shadow: 0px 1px 0px rgba(120, 200, 230, 0.6) inset;color: #FFF;" id="submitbase" type="submit" value="Submit" name="submitbase">
									</td>
									<td style="border: none;">
										<input style="padding:1%;height:30px;width:100px;background: #0091CD none repeat scroll 0% 0%;cursor: pointer;font-size:15px;border-width: 1px;border-style: solid;border-radius: 3px;white-space: nowrap;box-sizing: border-box;border-color: #0073AA;box-shadow: 0px 1px 0px rgba(120, 200, 230, 0.6) inset;color: #FFF;" type="button"  id ="searchbase" value="Close" onClick="self.close();"/>
									</td>
								</tr>
							</table>
						</div>
					</form>
						<?php

					} else {
						?>
					<div style="color: #a94442;background-color: #f2dede;padding: 15px;margin-bottom: 20px;text-align:center;border:1px solid #E6B3B2;font-size:18pt;">
						No Search Base(s) Found
					</div>
					<div style="display:block;text-align:center;margin-bottom:4%;"><img style="width:15%;" alt="Image not found" src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'includes/images/wrong.png' ); ?>"/>
					</div>
					<br><br><span>Please check :</span>
					<ul>
						<li>If your LDAP server configuration (LDAP server URL, Username & Password) is correct.</li>
						<li>If you have successfully saved your LDAP Connection Information.</li>
					</ul><br><br>
					<div style="margin:3%;display:block;text-align:center;">
						<input style="margin-top: -45px; padding:1%;width:100px;background: #0091CD none repeat scroll 0% 0%;cursor: pointer;font-size:15px;border-width: 1px;border-style: solid;border-radius: 3px;white-space: nowrap;box-sizing: border-box;border-color: #0073AA;box-shadow: 0px 1px 0px rgba(120, 200, 230, 0.6) inset;color: #FFF;" type="button"  id ="searchbase" value="Close" onClick="self.close();"/>
					</div>
					<?php } ?></div>
					<?php
				} else {
					?>
					<div style="color: #a94442;background-color: #f2dede;padding: 15px;margin-bottom: 20px;text-align:center;border:1px solid #E6B3B2;font-size:18pt;">
						No Search Base(s) Found
					</div>
					<div style="display:block;text-align:center;margin-bottom:4%;"><img style="width:15%;" alt="Image not found" src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'includes/images/wrong.png' ); ?>"/>
					</div>
					<br><br><span>Please check :</span>
					<ul>
						<li>If your LDAP server configuration (LDAP server URL, Username & Password) is correct.</li>
						<li>If you have successfully saved your LDAP Connection Information.</li>
					</ul><br><br>
					<div style="margin:3%;display:block;text-align:center;">
						<input style="margin-top: -45px; padding:1%;width:100px;background: #0091CD none repeat scroll 0% 0%;cursor: pointer;font-size:15px;border-width: 1px;border-style: solid;border-radius: 3px;white-space: nowrap;box-sizing: border-box;border-color: #0073AA;box-shadow: 0px 1px 0px rgba(120, 200, 230, 0.6) inset;color: #FFF;" type="button"  id ="searchbase" value="Close" onClick="self.close();"/>
					</div>
				<?php } ?></div>
				<?php
			} else {
				?>
				<h2>Search Base(s) List: </h2>
				<div style="color: #a94442;background-color: #f2dede;padding: 15px;margin-bottom: 20px;text-align:center;border:1px solid #E6B3B2;font-size:18pt;">
					No Search Base(s) Found
				</div>
				<div style="display:block;text-align:center;margin-bottom:4%;"><img style="width:15%;" alt="Image not found" src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'includes/images/wrong.png' ); ?>"/>
				</div>
				<br>
				<ul>
					<li><span><a href="https://faq.miniorange.com/knowledgebase/how-to-enable-php-ldap-extension/" rel="noopener" target="_blank">PHP LDAP extension</a> is not installed or is disabled. Please enable it.</span>
				</ul><br>
				<div style="margin:3%;display:block;text-align:center;">
					<input style="padding:1%;width:100px;background: #0091CD none repeat scroll 0% 0%;cursor: pointer;font-size:15px;border-width: 1px;border-style: solid;border-radius: 3px;white-space: nowrap;box-sizing: border-box;border-color: #0073AA;box-shadow: 0px 1px 0px rgba(120, 200, 230, 0.6) inset;color: #FFF;" type="button"  id ="searchbase" value="Close" onClick="self.close();"/>
				</div>
				<?php
			}
			if ( ! empty( $_POST['submitbase'] ) && check_admin_referer( 'mo_ldap_directory_search_submit_search_base_nonce' ) ) {
				if ( ! empty( $_POST['mo_ldap_directory_search_select_ldap_search_bases'] ) ) {
					$search_bases = isset( $_POST['mo_ldap_directory_search_select_ldap_search_bases'][0] ) ? strtolower( sanitize_text_field( wp_unslash( $_POST['mo_ldap_directory_search_select_ldap_search_bases'][0] ) ) ) : '';
					update_option( 'mo_ldap_ds_search_base', MO_LDAP_Directory_Search_Utility::encrypt( $search_bases ) );

					echo '<script>
                            window.close();
                            window.onunload = function(){
                                window.opener.location.reload();
                            };
                        </script>';
				} else {
					echo '<span"><script> alert("You have not selected any Search Base.")</script></span>';
				}
			}
			exit();
		}

		/**
		 * Function test_connection: Checks if the connection is established successfully.
		 *
		 * @return object
		 */
		public function test_connection() {
			$extension_response = $this->required_extensions_installed();
			if ( 'EXTENSIONS_INSTALLED' === $extension_response->status_code ) {
				delete_option( 'mo_ldap_ds_service_account_status' );

				$default_bind_response = $this->check_authenticated_bind( true );
				if ( 'LDAP_BIND_SUCCESSFUL' === $default_bind_response->status_code ) {
					add_option( 'mo_ldap_ds_service_account_status', 'VALID', '', 'no' );
					return $default_bind_response;
				} elseif ( 'LDAP_CONNECTION_ERROR' === $default_bind_response->status_code ) {
					add_option( 'mo_ldap_ds_service_account_status', 'INVALID', '', 'no' );
					return $default_bind_response;
				} else {
					add_option( 'mo_ldap_ds_service_account_status', 'INVALID', '', 'no' );
					return $default_bind_response;
				}
			} else {
				return $extension_response;
			}
		}

		/**
		 * Function check_authenticated_bind :
		 *
		 * @param  boolean $bind_default Default bind.
		 * @param  string  $userdn User Dn.
		 * @param  string  $password Password.
		 * @return object
		 */
		private function check_authenticated_bind( $bind_default = false, $userdn = null, $password = null ) {
			$auth_response = new MO_LDAP_Directory_Search_Auth_Response();

			if ( $bind_default ) {
				$userdn   = ! empty( get_option( 'mo_ldap_ds_server_dn' ) ) ? MO_LDAP_Directory_Search_Utility::decrypt( get_option( 'mo_ldap_ds_server_dn' ) ) : '';
				$password = ! empty( get_option( 'mo_ldap_ds_server_password' ) ) ? MO_LDAP_Directory_Search_Utility::decrypt( get_option( 'mo_ldap_ds_server_password' ) ) : '';
			}

			$current_ldap_connection = $this->ldapconn;
			if ( null === $current_ldap_connection ) {
				$current_ldap_connection = $this->get_connection();
			}
			if ( $current_ldap_connection ) {
				$enable_tls = ! empty( get_option( 'mo_ldap_ds_use_tls' ) ) ? get_option( 'mo_ldap_ds_use_tls' ) : '0';
				if ( '1' === $enable_tls ) {
					ldap_start_tls( $current_ldap_connection );
				}
				$bind     = @ldap_bind( $current_ldap_connection, $userdn, $password ); //phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- Used to silence LDAP errors; handled them below using MO_LDAP_Directory_Search_Status_Response class
				$error_no = ldap_errno( $current_ldap_connection );
				$err      = ldap_error( $current_ldap_connection );
				if ( -1 === $error_no ) {
					$auth_response->status_code    = 'LDAP_CONNECTION_ERROR';
					$troubleshooting_url           = '#mo_ldap_dir_search_conn_help';
					$auth_response->status_message = 'Cannot connect to LDAP Server. Make sure you have entered the correct LDAP server hostname or IP address. <br>If there is a firewall, please open the firewall to allow incoming requests to your LDAP server from your WordPress site IP address and the below-specified port number. <br>You can also check our <a href=' . $troubleshooting_url . '>Troubleshooting</a> steps. If you still face the same issue then contact us using the support form.';
				} elseif ( strtolower( $err ) !== 'success' ) {
					$auth_response->status_code    = 'LDAP_BIND_ERROR';
					$auth_response->status_message = 'Connection to your LDAP server is successful but unable to make authenticated bind to LDAP server. Make sure you have provided the correct username or password.';
				} else {
					$this->ldapconn             = $current_ldap_connection;
					$auth_response->status_code = 'LDAP_BIND_SUCCESSFUL';
				}
			} else {
				$auth_response->status_code    = 'LDAP_CONNECTION_ERROR';
				$troubleshooting_url           = add_query_arg( array( 'tab' => 'troubleshooting' ), isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '' );
				$auth_response->status_message = 'Cannot connect to LDAP Server. Make sure you have entered the correct LDAP server hostname or IP address. <br>If there is a firewall, please open the firewall to allow incoming requests to your LDAP server from your WordPress site IP address and the below-specified port number. <br>You can also check our <a href=' . $troubleshooting_url . '>Troubleshooting</a> steps. If you still face the same issue then contact us using the support form.';
			}
			return $auth_response;

		}

	}
}
