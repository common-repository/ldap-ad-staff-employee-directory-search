<?php
/**
 * This file renders the Main plugin UI in the plugin.
 *
 * @package miniOrange_LDAP_Directory_Search
 * @subpackage Main
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Adding the required files.
require_once 'mo-ldap-directory-search-licensing-plans.php';

/**
 * Function mo_ldap_directory_search_settings : Renders the main UI of the plugin.
 *
 * @return void
 */
function mo_ldap_directory_search_settings() {
	?>
	<script>
		var countAttributes=1;
		function set_counterAttribute(value) {
			countAttributes=value;
		}
	</script>
	<?php
	if ( isset( $_GET['tab'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Checking if the request value is set.
		$active_tab = sanitize_text_field( wp_unslash( $_GET['tab'] ) ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Checking if the request value is set.
	} else {
		$active_tab = 'search_config';
	}
	?>
	<div class = "mo_ldap_directory_search_container">
		<?php
		if ( ! MO_LDAP_Directory_Search_Utility::is_extension_installed( 'ldap' ) ) {
			?>
				<div class="notice notice-error is-dismissible">
					<span style="color:#FF0000">Warning: PHP LDAP extension is not installed or disabled.</span>
					<div id="help_mo_ldap_ds_warning_title" class="mo_ldap_title_panel">
						<p><a target="_blank" style="cursor: pointer;">Click here for instructions to enable it.</a></p>
					</div>
					<div hidden="" style="padding: 2px 2px 2px 12px" id="help_mo_ldap_ds_desc" class="mo_ldap_help_desc">
					<ul>
						<li style="font-size: large; font-weight: bold">Step 1 </li>
						<li style="font-size: medium; font-weight: bold">Loaded configuration file : <?php echo esc_html( php_ini_loaded_file() ); ?></li>
						<li style="list-style-type:square;margin-left:20px">Open php.ini file from above file path</strong></li><br/>
						<li style="font-size: large; font-weight: bold">Step 2</li>
						<li style="font-weight: bold;color: #C31111">For Windows users using Apache Server</li>
						<li style="list-style-type:square;margin-left:20px">Search for <strong>"extension=php_ldap.dll"</strong> in php.ini file. Uncomment this line, if not present then add this line to the file and save the file.</li>
						<li style="font-weight: bold;color: #C31111">For Windows users using IIS server</li>
						<li style="list-style-type:square;margin-left:20px">Search for <strong>"ExtensionList"</strong> in the php.ini file. Uncomment the <strong>"extension=php_ldap.dll"</strong> line, if not present then add this line to the file and save the file.</li>
						<li style="font-weight: bold;color: #C31111">For Linux users</li>
						<ul style="list-style-type:square;margin-left: 20px">
						<li style="margin-top: 5px">Install PHP LDAP extension (If not installed yet)
							<ul style="list-style-type:disc;margin-left: 15px;margin-top: 5px">
								<li>For Ubuntu/Debian, the installation command would be <strong>sudo apt-get -y install php-ldap</strong></li>
								<li>For RHEL based systems, the command would be <strong>yum install php-ldap</strong></li></ul></li>
						<li>Search for <strong>"extension=php_ldap.so"</strong> in php.ini file. Uncomment this line, if not present then add this line to the file and save the file.</li></ul><br/>
						<li style="margin-top: 5px;font-size: large; font-weight: bold">Step 3</li>
						<li style="list-style-type:square;margin-left:20px">Restart your server. After that refresh the "LDAP/AD" plugin configuration page.</li>
						</ul>
						<strong>For any further queries, please <a href="https://www.miniorange.com/contact" rel="noopener" target="_blank"> contact us. </a></strong>
					</div>
				</div>
				<?php
		}
		?>
		<?php
		if ( 'pricing' !== $active_tab && 'myaccount' !== $active_tab ) {
			?>

		<div hidden id="mo_ldap_dir_search_get_trial_modal" name="mo_ldap_dir_search_get_trial_modal" class="mo_ldap_modal" style="margin-left: 18%;z-index:11;">
			<div class="moldap-modal-contatiner-get-trial" style="color:black;"></div>
			<div class="mo_ldap_get_trial_modal_content" style="width: 700px; padding:30px;"> <span id="contact_us_title" style="font-size: 22px; margin-left: 26%; font-weight: bold;">Get 5 Days Full-Featured Trial</span>
			<p class="mo_ldap_get_trial_popup_create_acc_para"> Please <a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'myaccount' ), isset( $_SERVER['REQUEST_URI'] ) ? htmlentities( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) : '' ) ); ?>" target="_blank"> create an account </a> with us in order to start your free trial.</p>
				<form name="f" method="post" action="" id="mo_ldap_dir_search_get_trial_form" style="font-size: large;">
					<?php wp_nonce_field( 'mo_ldap_dir_search_get_free_trial_nonce' ); ?>
					<input type="hidden" name="option" value="mo_ldap_dir_search_get_free_trial" />
					<div>
						<p style="font-size: large;">
							<br> <strong>Email: </strong>
							<input style=" width: 77%; margin-left: 69px; " type="email" class="mo_ldap_dir_search_table_textbox" id="mo_ldap_dir_search_get_trial_email" name="mo_ldap_dir_search_get_trial_email" placeholder="Enter Email" required />
							<br>
							<br> <strong style="display:inline-block; vertical-align: top;">Description: </strong>
							<textarea style="width:77%; margin-left: 21px;padding:10px;" id="mo_ldap_dir_search_get_trial_desc" name="mo_ldap_dir_search_get_trial_desc" required rows="5" style="width: 100%" placeholder="I want to get a free trial of Staff/Employee Business Directory for Active Directory" value="I want to get a free trial of Staff/Employee Business Directory for Active Directory Plugin">I want to get a free trial of Staff/Employee Business Directory for Active Directory Plugin</textarea>
						</p>
						<br>
						<br>
						<div class="mo_ldap_modal-footer" style="text-align: center">
							<input type="button" style="font-size: medium" name="mo_ldap_dir_search_get_trial_submit" id="mo_ldap_dir_search_get_trial_submit" class="mo-ldap-dir-search-button-primary  mo_ldap_dir_search_main_buttons" onclick="validateRequirement()" value="Submit" />
							<input type="button" style="font-size: medium" name="mo_ldap_dir_search_get_trial_close" id="mo_ldap_dir_search_get_trial_close" class="button button-primary button-small" value="Close" /> 
						</div>
					</div>
				</form>
			</div>
		</div>

		<div class="mo_ldap_dir_search_local_main_head">
			<div class="mo_ldap_dir_search_title_container">
				<div class="mo_ldap_dir_search_logo_container">
					<img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'includes/images/login_logo.png' ); ?>"  width="50" height="50">
				</div>
				<div class="mo_ldap_dir_search_local_title">
					Staff/Employee Business Directory for Active Directory
				</div>
			</div>
			<div class="mo_ldap_directory_search_top_button_div">
				<a href="javascript:void(0)" class="mo_ldap_dir_search_licensing_plans_btn mo_ldap_dir_search_get_trial_btn" id="mo_ldap_dir_search_get_trial_btn" >Get Full-Featured Trial</a>
				<a id="mo_ldap_dir_search_my_account_btn" class="mo_ldap_dir_search_licensing_plans_btn mo_ldap_dir_search_my_account_btn" href="<?php echo esc_url( add_query_arg( array( 'tab' => 'myaccount' ), htmlentities( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) ) ); ?>">My Account</a>
				<a id="mo_ldap_dir_search_license_upgrade" class="mo_ldap_dir_search_licensing_plans_btn" href="<?php echo esc_url( add_query_arg( array( 'tab' => 'pricing' ), htmlentities( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) ) ); ?>" style="text-underline-offset: 3px;text-decoration: underline;text-decoration-thickness: 1.5px;">Premium<span class="mo_ldap_dir_search_crown_image_btn"><img src=" <?php echo esc_url( plugin_dir_url( 'ldap-ad-staff-employee-directory-search/mo-ldap-directory-search' ) . 'includes/images/crown.png' ); ?>" width="35px"> </span></a>
			</div>
		</div>

		<div id="mo_ldap_directory_search_add_on_layout" class="mo_ldap_directory_search_add_on_layout">
			<div class="mo-dir-search-nav-tab-wrapper">
				<a class="mo-ldap_dir-search-nav-tab <?php echo 'search_config' === $active_tab ? 'mo-ldap_dir-search-nav-tab-active' : ''; ?>" href="<?php echo esc_url( add_query_arg( array( 'tab' => 'search_config' ) ), sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ); ?>">LDAP/AD Connection</a>
				<a style="position: relative" class="mo-ldap_dir-search-nav-tab <?php echo 'attribute_config' === $active_tab ? 'mo-ldap_dir-search-nav-tab-active' : ''; ?>" href="<?php echo esc_url( add_query_arg( array( 'tab' => 'attribute_config' ) ), sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ); ?>">Attributes Configuration</a>
				<a style="position: relative" class="mo-ldap_dir-search-nav-tab <?php echo 'display_config' === $active_tab ? 'mo-ldap_dir-search-nav-tab-active' : ''; ?>" href="<?php echo esc_url( add_query_arg( array( 'tab' => 'display_config' ) ), sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ); ?>">Display Configuration</a>
			</div>
			<table class="mo-ldap-dir-search-table">
				<tr>
					<td style="width:70%;vertical-align:top;background-color:#fff;padding: 50px 20px 0 20px;background-color: #fff;" id="dirsearchconfigurationForm">
						<?php

						if ( 'search_config' === $active_tab ) {
							mo_ldap_dir_search_configuration_page();
						} elseif ( 'attribute_config' === $active_tab ) {
							mo_ldap_dir_search_attribute_configuration_page();
						} elseif ( 'display_config' === $active_tab ) {
							mo_ldap_dir_search_display_configuration_page();
						}
						?>
					</td>
					<td style="width:100%;float:left;">
						<section class="section_mo_ldap_dir_search_contact_us">

							<div class="mo_ldap_dir_search_support_layout">
								<div class="mo_ldap_dir_search_support_header">
									<img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'includes/images/24x7.png' ); ?>" alt="">
									<h3 class="mo_ldap_dir_search_contact_us_heading">Contact Us</h3>
									<div class="mo_ldap_dir_search_contact_us_desc">Need any help? Just send us a query so we can help you.<br /><br /></div>
								</div>
								<div class="mo_ldap_dir_search_support_box">
									<form name="f" method="post" action="">
										<div class="mo_ldap_dir_search_support__input_box">
											<table class="mo_ldap_dir_search_settings_table">
												<tr><td>
													<input type="email" class="mo_ldap_dir_search_table_textbox" id="mo_ldap_directory_search_query_email" name="mo_ldap_directory_search_query_email" value="<?php echo esc_attr( get_option( 'mo_ldap_ds_admin_email' ) ); ?>" placeholder="Enter your email" required />
													</td>
												</tr>
												<tr><td>
													<input type="text" class="mo_ldap_dir_search_table_textbox" name="mo_ldap_directory_search_query_phone" id="mo_ldap_directory_search_query_phone" value="<?php echo esc_attr( get_option( 'mo_ldap_ds_admin_phone' ) ); ?>" placeholder="Enter your phone"/>
													</td>
												</tr>
												<tr>
													<td>
														<textarea id="mo_ldap_directory_search_query" name="mo_ldap_directory_search_query" class="mo_ldap_settings_textarea" style="border-radius:4px;resize: vertical;width:95%;border: 2px solid #45497d" cols="52" rows="4" required placeholder="Write your query here"></textarea>
													</td>
												</tr>
											</table>
										</div>
										<?php wp_nonce_field( 'mo_ldap_dir_search_send_query_nonce' ); ?>
										<input type="hidden" name="option" value="mo_ldap_directory_search_send_query"/>
										<input type="submit" name="mo_ldap_send_query_btn" id="mo_ldap_send_query_btn" value="Submit Query" style="display: block; margin: auto; margin-top: 1.4rem;border-color: #ff7776 !important;" class="mo-ldap-dir-search-button-primary " />
									</form>
									<br>
								</div>
							</div>
						</section>
						<section class="section_mo_ldap_dir_search_intranet_ad">
							<div class="mo_ldap_dir_search_support_layout">
								<div class="mo_ldap_dir_search_support_header">
									<img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'includes/images/logo.png' ); ?>" alt="">
									<h3 class="mo_ldap_dir_search_intranet_ad_heading">miniOrange LDAP/Active Directory Login for Intranet Sites Plugin</h3>
								</div>
								<div class="mo_ldap_dir_search_support_box">
									<p class="mo_ldap_directory_search_intranet_ad_para">The <span style="font-weight:500;">miniOrange WP LDAP/AD Login for Intranet Sites  </span> plugin allows you to login into a WordPress website using the credentials which are stored in your LDAP/Active Directory.</p>
									<div class="mo_ldap_directory_search_intranet_ad_buttons" >
									<a class="mo-ldap-dir-search-button-primary  mo_ldap_dir_search_main_buttons intranet-ad-btns" href="https://wordpress.org/plugins/ldap-login-for-intranet-sites/" target="_blank">Download For Free</a>
									<a class="help  mo-ldap-dir-search-troubleshoot-button intranet-ad-btns" href="https://plugins.miniorange.com/wordpress-ldap-login-intranet-sites" target="_blank">More Details</a>
								</div>
								</div>
							</div>
						</section>
					</td>
				</tr>
			</table>	</div>
		</div>
		<script>

			jQuery('a[id=mo_ldap_dir_search_get_trial_btn]').click(function () {
				jQuery('#mo_ldap_dir_search_get_trial_modal').show();
			});

			jQuery('#mo_ldap_dir_search_get_trial_close').click(
				function(){
				jQuery('#mo_ldap_dir_search_get_trial_modal').hide();
			});

			function validateRequirement() {
				if (validateEmail()) {
					var requirement = document.getElementById("mo_ldap_dir_search_get_trial_desc").value;
					if (requirement.length <= 10) {
						alert("Please enter more details about your requirement.");
					} else {
						document.getElementById("mo_ldap_dir_search_get_trial_form").submit();
					}
				}
			}

			function validateEmail() {
				var email = document.getElementById('mo_ldap_dir_search_get_trial_email');
				if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email.value)) {
					return (true)
				} else if (email.value.length == 0) {
					alert("Please enter your email address!")
					return (false)
				} else {
					alert("You have entered an invalid email address!")
					return (false)
				}
			}

		</script>
			<?php
		} elseif ( 'pricing' === $active_tab ) {
			mo_ldap_directory_search_show_licensing_page();
		} elseif ( 'myaccount' === $active_tab ) {
			if ( get_option( 'mo_ldap_ds_verify_customer' ) === 'true' ) {
				mo_ldap_directory_search_login_page();
			} elseif ( ! MO_LDAP_Directory_Search_Utility::is_customer_registered() ) {
				mo_ldap_directory_search_show_myaccount_page();
			} else {
				mo_ldap_directory_search_show_customer_details();
			}
		}
		?>
		<div>
			<br>
		</div>
	<?php

}

/**
 * Function mo_ldap_directory_search_show_customer_details : Display my account page.
 *
 * @return void
 */
function mo_ldap_directory_search_show_customer_details() {
	?>
	<div class="mo_ldap_dir_search_local_main_head">
		<div>
			<a class="mo-ldap-dir-search-plugin-config-link" href="<?php echo esc_url( add_query_arg( array( 'tab' => 'search_config' ), isset( $_SERVER['REQUEST_URI'] ) ? htmlentities( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) : '' ) ); ?>">
				<button id="Back-To-Plugin-Configuration" type="button" value="Back-To-Plugin-Configuration" class="mo-ldap-dir-search-button-primary  mo_ldap_dir_search_main_buttons">
					<span class="dashicons dashicons-arrow-left-alt" style="vertical-align: middle;"></span> 
					Plugin Configuration
				</button> 
			</a> 
		</div>
		<div class="mo_ldap_dir_search_title_container" style="width:80%;">
			<div class="mo_ldap_dir_search_logo_container">
				<img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'includes/images/login_logo.png' ); ?>"  width="50" height="50">
			</div>
			<div class="mo_ldap_dir_search_local_title">
				Staff/Employee Business Directory for Active Directory
			</div>
		</div>
	</div>
	<section class="mo-ldap-dir-search-login_success-section">
		<div class="mo_ldap_table_layout" >
			<h2>Thank you for registering with miniOrange.</h2>

			<br>

			<table border="1" aria-hidden="true" style="background-color:#FFFFFF; border:1px solid #CCCCCC; border-collapse: collapse; padding:0px 0px 0px 10px; margin:2px; width:45%">
				<tr>
					<td style="width:45%; padding: 10px;">miniOrange Account Email</td>
					<td style="width:55%; padding: 10px;"><?php echo esc_html( get_option( 'mo_ldap_ds_admin_email' ) ); ?></td>
				</tr>
				<tr>
					<td style="width:45%; padding: 10px;">Customer ID</td>
					<td style="width:55%; padding: 10px;"><?php echo esc_html( get_option( 'mo_ldap_ds_admin_customer_key' ) ); ?></td>
				</tr>
			</table>
			<br /><br />

			<table aria-hidden="true">
				<tr>
					<td>
						<form name="f1" method="post" action="" id="mo_ldap_change_account_form">
							<?php wp_nonce_field( 'mo_ldap_directory_search_change_miniorange_account_nonce' ); ?>
							<input type="hidden" name="option" value="mo_ldap_directory_search_change_miniorange_account"/>
							<input type="submit" value="Change Account" class="mo-ldap-dir-search-button-primary  mo_ldap_dir_search_main_buttons"/>
						</form>
					</td><td>
						<a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'pricing' ), htmlentities( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) ) ); ?>"><input type="button" class="mo-ldap-dir-search-troubleshoot-button" value="Check Licensing Plans"/></a>
					</td>
				</tr>
			</table>

			<br />
		</div>
	</section>

	<?php
}

/**
 * Function mo_ldap_dir_search_configuration_page : Display plugin configuration page.
 *
 * @return void
 */
function mo_ldap_dir_search_configuration_page() {
	$ldap_server_protocol     = ( get_option( 'mo_ldap_ds_ldap_protocol' ) ? get_option( 'mo_ldap_ds_ldap_protocol' ) : 'ldap' );
	$directory_server_value   = ! empty( get_option( 'mo_ldap_ds_directory_server_value' ) ) ? get_option( 'mo_ldap_ds_directory_server_value' ) : '';
	$ldap_server_address      = ! empty( get_option( 'mo_ldap_ds_ldap_server_address' ) ) ? MO_LDAP_Directory_Search_Utility::decrypt( get_option( 'mo_ldap_ds_ldap_server_address' ) ) : '';
	$ldap_server_port_number  = ( get_option( 'mo_ldap_ds_ldap_port_number' ) ? get_option( 'mo_ldap_ds_ldap_port_number' ) : '389' );
	$ldaps_server_port_number = ( get_option( 'mo_ldap_ds_ldaps_port_number' ) ? get_option( 'mo_ldap_ds_ldaps_port_number' ) : '636' );

	$dn             = ! empty( get_option( 'mo_ldap_ds_server_dn' ) ) ? MO_LDAP_Directory_Search_Utility::decrypt( get_option( 'mo_ldap_ds_server_dn' ) ) : '';

	$mo_ldap_config = new MO_LDAP_Directory_Search_Config();
	$content        = $mo_ldap_config->test_connection();
	
	if ( isset( $content->status_code ) && strcasecmp( $content->status_code, 'LDAP_BIND_SUCCESSFUL' ) === 0 ) {
		$mo_ldap_connection_status = 'VALID';
	} else {
		$mo_ldap_connection_status = 'INVALID';
	}
	?>
	<section class="section_ldap_connection_info">
		<div class="ldap-sections-info-div ldap-sections-info-div-ldap-conn">
			<h2 class="mo-ldap-dir-search-sections-main-heading">LDAP/AD Connection Information</h2>
		</div>
		<p class="mo_ldap_ds_setup_guide_links"> Please <a href="https://plugins.miniorange.com/setup-active-directory-ldap-users-search-plugin" target="_blank">Click Here</a>  to check our documentation for setting up the plugin.</p>
		<form id="mo_ldap_dir_search_ldap_connection_form" name="mo_ldap_dir_search_ldap_connection_form" method="post" action="">
			<?php wp_nonce_field( 'mo_ldap_directory_search_ldap_connection_nonce' ); ?>
			<input type="hidden" name="option" value="mo_ldap_directory_search_ldap_connection" />
			<input id="mo_ldap_directory_search_ldap_port_number" type="hidden" name="mo_ldap_directory_search_ldap_port_number" value="<?php echo esc_attr( $ldap_server_port_number ); ?>" />
			<input id="mo_ldap_directory_search_ldaps_port_number" type="hidden" name="mo_ldap_directory_search_ldaps_port_number" value="<?php echo esc_attr( $ldaps_server_port_number ); ?>" />
			<div class="mo-ldap-directory-sever-selection-div">
				<?php if ( 'VALID' === $mo_ldap_connection_status ) { ?>
					<p class="mo_ldap_connection_status" style="color: green;">Note: &nbsp;&nbsp; LDAP Connection Successfully Established.</p>
				<?php } ?>
				<table style="border-spacing:2px;width:100%">
					<tbody>
						<tr>
							<td style="width:24%;"> <p style="font-weight:600;font-size:16px;"> Directory Server: </p></td>
							<td> 
								<select class="mo_ldap_directory_search_directory_server_value" name="mo_ldap_directory_search_directory_server_value" id="mo_ldap_directory_search_directory_server_value" onchange="mo_ldap_directory_search_show_custom_directory()" required>
									<option value="">Select</option>
									<option value="msad" 
									<?php
									if ( 'msad' === $directory_server_value ) {
										echo 'selected';
									}
									?>
									>Microsoft Active Directory</option>
									<option value="openldap" 
									<?php
									if ( 'openldap' === $directory_server_value ) {
										echo 'selected';
									}
									?>
									>OpenLDAP</option>
									<option value="freeipa" 
									<?php
									if ( 'freeipa' === $directory_server_value ) {
										echo 'selected';
									}
									?>
									>FreeIPA</option>
									<option value="jumpcloud" 
									<?php
									if ( 'jumpcloud' === $directory_server_value ) {
										echo 'selected';
									}
									?>
									>JumpCloud</option>
									<option value="other" 
									<?php
									if ( 'other' === $directory_server_value ) {
										echo 'selected';
									}
									?>
									>Other</option>
								</select>
								<?php
								if ( 'other' === $directory_server_value ) {
									?>
										<input class="mo_ldap_dir_search_table_textbox" style="width: 65%;" type="text" id="mo_ldap__directory_search_directory_server_custom_value" name="mo_ldap__directory_search_directory_server_custom_value"  placeholder="Enter your directory name"  value="<?php echo esc_attr( get_option( 'mo_ldap_ds_directory_server_custom_value' ) ); ?>">

										<?php
								} else {
									?>
										<input class="mo_ldap_dir_search_table_textbox" style="width: 65%;display: none;" type="text" id="mo_ldap__directory_search_directory_server_custom_value" name="mo_ldap__directory_search_directory_server_custom_value"  placeholder="Enter your directory name"  value="<?php echo esc_attr( get_option( 'mo_ldap_ds_directory_server_custom_value' ) ); ?>">
										<?php
								}
								?>
							</td>
							<script type="text/javascript">

							function mo_ldap_directory_search_show_custom_directory() {
								var element = document.getElementById("mo_ldap_directory_search_directory_server_value").selectedIndex;
								var allOptions = document.getElementById("mo_ldap_directory_search_directory_server_value").options;
								if (allOptions[element].index == 5){
									document.getElementById("mo_ldap__directory_search_directory_server_custom_value").style.display = "";
								} else {
									document.getElementById("mo_ldap__directory_search_directory_server_custom_value").style.display = "none";
								}
							}
						</script>
						</tr>
					</tbody>
				</table>
			</div>

			<div class="mo-ldap-dir-search-config-div">
				<table class="mo_ldap_dir_search_settings_table">
					<tr>
						<td style="width: 24%;"><h5 class="mo_ldap_dir_search_labels"><font color="#FF0000">*</font>LDAP Server:</h5></td>
						<td style="width: 76%;">
							<table>
								<tr>
									<td style="width:15%;">
										<select class="ldap_ldaps_dropdown" name="mo_ldap_directory_search_protocol" id="mo_ldap_directory_search_protocol">
											<?php if ( 'ldap' === $ldap_server_protocol ) { ?>
											<option value="ldap" selected>ldap</option>
											<option value="ldaps">ldaps</option>
											<?php } elseif ( 'ldaps' === $ldap_server_protocol ) { ?>
											<option value="ldap">ldap</option>
											<option value="ldaps" selected>ldaps</option>
											<?php } ?>
										</select>
									</td>
									<td style="width:65%;"><input class="mo_ldap_dir_search_table_textbox" type="text" id="mo_ldap_directory_search_ldap_server" name="mo_ldap_directory_search_ldap_server" style="width:95%" required placeholder="LDAP Server hostname or IP address" value="<?php echo esc_attr( $ldap_server_address ); ?>" /></td>
									<td style="width:20%;">
										<?php if ( 'ldap' === $ldap_server_protocol ) { ?>
										<input type="text" class="mo_ldap_dir_search_table_textbox" id="mo_ldap_directory_search_server_port_no" style="width: 100%;padding:2px 10px;border: 2px solid #45497d" name="mo_ldap_directory_search_server_port_no" required placeholder="port number" value="<?php echo esc_attr( $ldap_server_port_number ); ?>" />
										<?php } elseif ( 'ldaps' === $ldap_server_protocol ) { ?>
										<input type="text" class="mo_ldap_dir_search_table_textbox" id="mo_ldap_directory_search_server_port_no" style="width: 100%;" name="mo_ldap_directory_search_server_port_no" required placeholder="port number" value="<?php echo esc_attr( $ldaps_server_port_number ); ?>" />
										<?php } ?>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr><td></td></tr>
					<tr>
						<td>&nbsp;</td>
						<td colspan="2"><emp>Select ldap or ldaps from the above dropdown list. Specify the hostname for the LDAP server in the above text field. Edit the port number if you have a custom port number.</emp>
						</td>
					</tr>
					<tr><td></td></tr>
					<tr><td></td></tr>
					<tr id="mo_ldap_dir_search_enable_tls_row" class="mo_ldap_dir_search_enable_tls_row">
						<td>&nbsp;</td>
						<td>
							<input class="mo_ldap_dir_search_enable_tls_checkbox" style="padding: 9px !important;border: 2px solid #45497d;border-radius:0" type="checkbox" name="mo_ldap_directory_search_tls_enable" value="1" <?php checked( get_option( 'mo_ldap_ds_use_tls' ) === '1' ); ?>
							onchange="enabletls(this)"> <strong>Enable TLS</strong> (Check this only if your server uses TLS Connection.)
						</td>
					</tr>
				</table>

				<table style="width: 100%">
					<tr>
						<td></td>
					</tr>
					<tr><td></td></tr>
					<tr>
						<td style="width: 24%;"><h5 class="mo_ldap_dir_search_labels"><font color="#FF0000">*</font>Username:</h5></td>
						<td style="width: 76%;"><input style="width:77%;" class="mo_ldap_dir_search_table_textbox" type="text" id="mo_ldap_directory_search_dn" name="mo_ldap_directory_search_dn" required placeholder="Enter username" value="<?php echo esc_attr( $dn ); ?>" /></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td><emp>You can specify the Username of the LDAP server in either way as follows<br/><strong> Username@domainname or Distinguished Name(DN) format</strong></emp></td>
					</tr>
					<tr><td></td></tr>
					<tr><td></td></tr>
					<tr><td></td></tr>
					<tr>
						<td style="width: 24%"><h5 class="mo_ldap_dir_search_labels"><font color="#FF0000">*</font>Password:</h5></td>
						<td><input style="width:77%;" class="mo_ldap_dir_search_table_textbox" required type="password" name="mo_ldap_directory_search_admin_password" placeholder="Enter password"/></td>
					</tr>
					<tr><td></td></tr>
					<tr><td></td></tr>
					<tr>
						<td>&nbsp;</td>
						<td><strong>The above username and password will be used to establish the connection to your LDAP server.</strong></td>
					</tr>
					<tr><td></td></tr>
					<tr><td></td></tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td><input type="submit" class="mo-ldap-dir-search-button-primary  mo_ldap_dir_search_main_buttons" value="Test Connection & Save" />&nbsp;&nbsp;
							<input type="button" id="mo_ldap_dir_search_conn_help" class="help  mo-ldap-dir-search-troubleshoot-button" value="Troubleshooting" /></td>
					</tr>
					<tr>
						<td colspan="2" id="mo_ldap_dir_search_conn_troubleshoot" hidden>
							<p>
								<br>
								<strong>Are you having trouble connecting to your LDAP server from this plugin?</strong>
								<ol>
									<li>Please make sure that all the values entered are correct.</li>
									<li>If you are having a firewall, open the firewall to allow incoming requests to your LDAP from your WordPress <strong>Server IP</strong> and <strong>port 389.</strong> </li>
									<li>If you are still having problems, submit a query using the support panel on the right-hand side.</li>
								</ol>
							</p>
						</td>
					</tr>
				</table>
				<script>
					jQuery("#mo_ldap_directory_search_protocol").change(function() {

						var current_selected_protocol_name = jQuery("#mo_ldap_directory_search_protocol").val();
						var port_number_field = jQuery("#mo_ldap_directory_search_server_port_no").val();
						var ldap_port_number_value = jQuery("#mo_ldap_directory_search_ldap_port_number").val();
						var ldaps_port_number_value = jQuery("#mo_ldap_directory_search_ldaps_port_number").val();
						if (current_selected_protocol_name == "ldaps") {
							jQuery("#mo_ldap_directory_search_server_port_no").val(ldaps_port_number_value);

							document.getElementById("mo_ldap_dir_search_enable_tls_row").style.display = "none";
						} else {
							jQuery("#mo_ldap_directory_search_server_port_no").val(ldap_port_number_value);
							document.getElementById("mo_ldap_dir_search_enable_tls_row").style.display = "";
						}
					});
				</script>
			</div>
		</form>
		<form name="f" id="mo_ldap_directory_search_tls_enable_form" method="post" action="" style="display:none">
			<?php wp_nonce_field( 'mo_ldap_directory_search_tls_enable_nonce' ); ?>
			<input type="hidden" name="option" value="mo_ldap_directory_search_tls_enable">
			<input type="checkbox" id="mo_ldap_directory_search_tls_enable" name="mo_ldap_directory_search_tls_enable" value="1"> <strong>Enable
				TLS</strong> (Check this only if your server use TLS Connection.)
		</form>
	</section>
	<script>
		function enabletls(enabletls) {
			if (enabletls.checked)
				jQuery("#mo_ldap_directory_search_tls_enable").prop('checked', true);
			else
				jQuery("#mo_ldap_directory_search_tls_enable").prop('checked', false);
			jQuery("#mo_ldap_directory_search_tls_enable_form").submit();
		}

	</script>
	<?php
		$search_base_string = ( ! empty( get_option( 'mo_ldap_ds_search_base' ) ) ? MO_LDAP_Directory_Search_Utility::decrypt( get_option( 'mo_ldap_ds_search_base' ) ) : '' );
	?>
	<section class="section_ldap_user_mapping">
		<div class="ldap-sections-info-div">
			<h2 class="mo-ldap-dir-search-sections-main-heading">LDAP User Mapping Configuration </h2>
		</div>
		<br>
		<div class="mo_ldap_dir_search_user_mapping_form_div">
			<form id="mo_form1" name="f" method="post" action="">
				<?php wp_nonce_field( 'mo_ldap_directory_search_save_user_mapping_nonce' ); ?>
				<input id="Mo_Ldap_Directory_Search_Configuration_form_action" type="hidden" name="option" value="mo_ldap_directory_search_save_user_mapping"/>
				<div id="panel1">
					<table class="mo_ldap_dir_search_settings_table">
						<tr>
							<td style="width: 24%"></td>
							<td></td>
						</tr>

						<tr>
							<td style="width:30%;"><h5 class="mo_ldap_dir_search_labels"><font color="#FF0000">*</font>Base DN(s):</h5></td>
							<td style="width:40%;"><input style="width:100%" class="mo_ldap_dir_search_table_textbox"
									type="text" id="mo_ldap_directory_search_search_base" name="mo_ldap_directory_search_search_base" required
									placeholder="dc=domain,dc=com" value="<?php echo esc_attr( $search_base_string ); ?>" />
							</td>
							<td style="width:30%;">
								<input style="margin-left:30px;" type="button" id="searchbases" class="mo-ldap-dir-search-button-primary " name="Search Bases" value="Possible Search Bases / Base DNs">
							</td>
						</tr>
						<tr>
							<td><h5 class="mo_ldap_dir_search_labels">Custom Search Filter:</h5></td>
							<td>
								<div class="mo_ldap_dir_search_cus_toggle_div" id="mo_ldap_dir_search_cus_toggle_div">
									<label class="mo_ldap_dir_search_switch" id="mo_ldap_dir_search_switch">
									<input type="checkbox" class="" name="dir_search_custom_search_filter_checkbox" id="search_filter_check" disabled>
										<span id="mo_ldap_dir_search_slider" class="mo_ldap_dir_search_slider round"></span>
									</label>
								</div>
							</td>
							<td>
								<div id="mo_ldap_dir_search_custom_filter_premium" class="mo_ldap_dir_search_custom_filter_premium" style="display:none;">
									<p> <span style="font-weight:500"> Custom Search Filter </span> feature is available in the Premium Version <span class="mo_ldap_dir_search_crown_image"><img src=" <?php echo esc_url( plugin_dir_url( 'ldap-ad-staff-employee-directory-search/mo-ldap-directory-search' ) . 'includes/images/crown.png' ); ?>" width="35px"> </span></p>
								</div>
							</td>
						</tr>

						<script>
							jQuery("#searchbases").click(function (){
								showsearchbaselist();
							});
							function showsearchbaselist() {
								window.open('<?php echo esc_js( site_url() ) . '/?option=mo_ldap_directory_search_search_base_list'; ?>', "Search Base Lists", "width=600, height=600");
							}                          
						</script>


						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td colspan="2"><input type="submit" class="mo-ldap-dir-search-button-primary "
									value="Save Search Filter" />&nbsp;&nbsp;
								<input
										type="button" id="mo_ldap_dir_search_conn_help_user_mapping"
										class="help  mo-ldap-dir-search-troubleshoot-button"
										value="Troubleshooting" /></td>
						</tr>
						<tr>
							<td colspan="3" id="mo_ldap_dir_search_conn_user_mapping_troubleshoot" hidden>
								<br>
								<strong>Are you having trouble connecting to your LDAP server from this plugin?</strong>
								<ol>
									<li>The <strong>search base</strong> URL is typed incorrectly. Please verify if that search base
										is present.
									</li>
									<li>User is not present in that search base. The user may be present in the directory
										but in some other tree and you may have entered a tree where this user is not
										present.
									</li>
									<li>If you are still having problems, submit a query using the support panel on the right-hand side. </li>
								</ol>

							</td>
						</tr>
				</table>
				<script>

					let elmnt = document.getElementById('mo_ldap_dir_search_switch');

					let hiddenelmnt = document.getElementById('mo_ldap_dir_search_custom_filter_premium');
					elmnt.addEventListener('mouseover', function handleMouseOver() {
						hiddenelmnt.style.display = 'block';
					});

					elmnt.addEventListener('mouseout', function handleMouseOut() {
						hiddenelmnt.style.display = 'none';
					});

					var elements = [];

					elements.push(document.getElementById('mo_ldap_dir_search_lable_input_1'));
					elements.push(document.getElementById('mo_ldap_dir_search_lable_input_2'));
					elements.push(document.getElementById('mo_ldap_dir_search_lable_input_3'));

					let hiddenElement = document.getElementById('mo_ldap_dir_search_change_labels_premium');

					for (let i = 0; i < elements.length; i++) {
						if(elements[i]){
							elements[i].addEventListener('mouseover', function handleMouseOver() {
								hiddenElement.style.display = 'block';
							});

							elements[i].addEventListener('mouseout', function handleMouseOut() {
								hiddenElement.style.display = 'none';
							});
						}
					}

				</script>
			</form>
		</div>
	</section>
	<?php
}

/**
 * Function mo_ldap_dir_search_attribute_configuration_page : Configure LDAP function.
 *
 * @return void
 */
function mo_ldap_dir_search_attribute_configuration_page() {
	?>
	<section class="section_ldap_attribute_configurations">

		<div class="ldap-sections-info-div">
			<h2 class="mo-ldap-dir-search-sections-main-heading">LDAP/AD Attributes Configuration</h2>
		</div>

		<div class="mo_ldap_dir_search_attr_conf_div"> 
			<form name="f" method="post" id="login_message_config_form">
				<table id="mo_ldap_dir_search_config_table" class="mo_ldap_dir_search_config_table">
				<?php wp_nonce_field( 'mo_ldap_save_directory_search_config_nonce' ); ?>
					<input type="hidden" name="option" value="mo_ldap_save_directory_search_config" />
					<thead>
						<tr>
							<th style="text-align:center;"><h3>No.</h3></th>
							<th><h3>Attribute Label</h3></th>
							<th><h3>LDAP Attribute Name</h3></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<div id="mo_ldap_dir_search_change_labels_premium" class="mo_ldap_dir_search_change_labels_premium" style="display:none">
								<p>  Editing the labels <span style="font-weight:500"> Name, Phone and Email </span> is available in the Premium Version <span class="mo_ldap_dir_search_crown_image"><img src=" <?php echo esc_url( plugin_dir_url( 'ldap-ad-staff-employee-directory-search/mo-ldap-directory-search' ) . 'includes/images/crown.png' ); ?>" width="35px"> </span></p>
							</div>
						</tr>

						<?php

						$mo_ldap_dir_config             = ! empty( get_option( 'mo_ldap_ds_config' ) ) ? maybe_unserialize( get_option( 'mo_ldap_ds_config' ) ) : array();
						$mo_ldap_dir_config[1]['lable'] = 'Name';
						$mo_ldap_dir_config[2]['lable'] = 'Email';
						$mo_ldap_dir_config[3]['lable'] = 'Phone';
						for ( $i = 1; $i <= 3; $i++ ) {
							$lable = ! empty( $mo_ldap_dir_config[ $i ]['lable'] ) ? $mo_ldap_dir_config[ $i ]['lable'] : '';
							$value = ! empty( $mo_ldap_dir_config[ $i ]['value'] ) ? $mo_ldap_dir_config[ $i ]['value'] : '';
							?>
							<tr>
								<td style="text-align: center"> <?php echo esc_html( $i ); ?> </td>
								<td>
									<input class="mo_ldap_dir_search_label_textbox" id="mo_ldap_dir_search_lable_input_<?php echo esc_attr( $i ); ?>" type="text" name="mo_ldap_dir_attri_lable_name_<?php echo esc_attr( $i ); ?>"  placeholder="Attribute Label" value="<?php echo esc_attr( $lable ); ?>" readonly/>
								</td>
								<td>
									<input class="mo_ldap_dir_search_attribute_textbox" type="text" name="mo_ldap_dir_attri_value_<?php echo esc_attr( $i ); ?>" required placeholder="Attribute Value" value="<?php echo esc_attr( $value ); ?>"/>
								</td>
							</tr>
							<?php
						}
						$lable = ! empty( $mo_ldap_dir_config[4]['lable'] ) ? $mo_ldap_dir_config[4]['lable'] : '';
						$value = ! empty( $mo_ldap_dir_config[4]['value'] ) ? $mo_ldap_dir_config[4]['value'] : '';

						?>
						<tr>
							<td style="text-align: center"> 4. </td>
							<td>
								<input class="mo_ldap_dir_search_label_textbox" id="mo_ldap_dir_search_lable_input_4" type="text" name="mo_ldap_dir_attri_lable_name_4"  placeholder="Attribute Label" value="<?php echo esc_attr( $lable ); ?>"/>
							</td>
							<td>
								<input class="mo_ldap_dir_search_attribute_textbox" type="text" name="mo_ldap_dir_attri_value_4"  placeholder="Attribute Value" value="<?php echo esc_attr( $value ); ?>"/>    
							</td>
						</tr>
						<tr class="mo_ldap_dir_search_add_mappings_tr">
							<td></td>
							<td><a id="mo_ldap_dir_search_add_mappings_button" class="mo_ldap_dir_search_add_mappings_button" name="add_attribute">Add More Mappings</a></td>
							<td>
								<div id="mo_ldap_dir_search_add_more_mappings_premium" class="mo_ldap_dir_search_add_more_mappings_premium" style="display:none;">
									<p> <span style="font-weight:500"> Add More Mappings </span> feature is available in the Premium Version <span class="mo_ldap_dir_search_crown_image"><img src=" <?php echo esc_url( plugin_dir_url( 'ldap-ad-staff-employee-directory-search/mo-ldap-directory-search' ) . 'includes/images/crown.png' ); ?>" width="35px"> </span></p>
								</div>
							</td>	
						</tr>
					</tbody>
				</table>   
				<script>
					let el = document.getElementById('mo_ldap_dir_search_add_mappings_button');

					let hiddenEl = document.getElementById('mo_ldap_dir_search_add_more_mappings_premium');
					el.addEventListener('mouseover', function handleMouseOver() {
						hiddenEl.style.display = 'block';
					});

					el.addEventListener('mouseout', function handleMouseOut() {
						hiddenEl.style.display = 'none';
					});
				</script>

				<div class="mo_ldap_dir_search_save_conf_td"><input type="submit" value="Save Configuration" class="mo-ldap-dir-search-button-primary  mo_ldap_save_conf_btn"></div>
			</form>
		</div>
	</section>
	<?php
		$search_options          = ! empty( get_option( 'mo_ldap_ds_search_by_options' ) ) ? maybe_unserialize( get_option( 'mo_ldap_ds_search_by_options' ) ) : array();
		$search_options          = array_values( $search_options );
		$mo_directory_search_opt = maybe_unserialize( get_option( 'mo_ldap_ds_config' ) );
	?>

	<section class="section_ldap_search_users_using">
		<div class="ldap-sections-info-div">
			<h2 class="mo-ldap-dir-search-sections-main-heading">Attributes To Search By - </h2>
		</div>
		<div class="mo_ldap_dir_search_search_options_div">
			<form name="f" method="post" id="login_message_config_form">
				<?php wp_nonce_field( 'mo_ldap_save_directory_search_option_config_nonce' ); ?>
				<input type="hidden" name="option" value="mo_ldap_save_directory_search_option_config" />	   
				<?php
				$search_options_count = 1;
				if ( ! empty( $mo_directory_search_opt ) ) {
					echo '<p class="mo_ldap_dir_search_search_options_para">
                    Choose the LDAP attributes that will be used for your user search.
                     </p>';
					echo '<div class="mo-ldap-dir-search-search-labels-div">';
					foreach ( $mo_directory_search_opt as $key => $value ) {
						$id           = $value['value'];
						$check_status = '';
						$is_disabled  = '';
						$is_premium   = '';
						if ( 'Email' === $value['lable'] || 'Phone' === $value['lable'] ) {
							$is_disabled = 'disabled';
							$is_premium  = 'mo_ldap_dir_search_premium_feature';
						}

						if ( in_array( $value['lable'], $search_options, true ) ) {
							$check_status = 'checked';
						}
						if ( ! empty( $value ) && 'mo_ldap_photo' !== $value['lable'] ) {
							echo '<div class="dir_search_search_opt_fields ' . esc_attr( $is_premium ) . '" id="dir_search_search_opt_field_' . esc_attr( $search_options_count ) . '"> <input class="mo_ldap_dir_search_options_checkbox" style="padding: 9px !important;border: 2px solid #45497d;border-radius:0" type="checkbox" ' . esc_attr( $is_disabled ) . ' ' . esc_attr( $check_status ) . ' name="' . esc_attr( $id ) . '" id="' . esc_attr( $id ) . '" />';
							echo '<label class="dir_search_search_opt_labels">' . esc_attr( $value['lable'] ) . '</label></div>';
						}
						$search_options_count++;
					}
					echo '</div>';
				} else {
					echo '<h3 style="font-weight:400;font-style:italic;"> Please configure the LDAP/AD Attributes Configuration section </h3>';
				}
				?>
				<div id="mo_ldap_dir_search_search_options_premium" class="mo_ldap_dir_search_search_options_premium" style="display:none;">
					<p>  Search Using <span style="font-weight:500">Email and Phone </span> feature is available in the Premium Version <span class="mo_ldap_dir_search_crown_image"><img src=" <?php echo esc_url( plugin_dir_url( 'ldap-ad-staff-employee-directory-search/mo-ldap-directory-search' ) . 'includes/images/crown.png' ); ?>" width="35px"> </span></p>
				</div>
				<div class="mo-ldap-search-save-option-div">
					<input type="submit" value="Save Search Attributes" class="
					<?php
					if ( get_option( 'mo_ldap_ds_config_status' ) === '1' ) {
						?>
						mo-ldap-dir-search-button-primary
						<?php
					} else {
						?>
						mo-ldap-search-save-option
						<?php
					}
					?>
					"/>
				</div>
			</form>
			<script>
				var search_elements_id = [];
				let search_hidden_element = document.getElementById('mo_ldap_dir_search_search_options_premium');

				jQuery('.mo_ldap_dir_search_premium_feature').hover(function(){
						search_hidden_element.style.display = 'block';
				}, function(){
						search_hidden_element.style.display = 'none';
				});

			</script>
		</div>
	</section>

		<section class="section_ldap_user_search">
		<div class="ldap-sections-info-div">
			<h2 class="mo-ldap-dir-search-sections-main-heading">Test User Search Result</h2>
		</div>
		<br>
		<div class="mo_ldap_dir_search_user_mapping_form_div">
			<input id="Mo_Ldap_Directory_Search_Configuration_form_action" type="hidden" name="option" value="mo_ldap_directory_search_user_result" />
			<div id="panel1">
				<table class="mo_ldap_dir_search_settings_table">
					<tr>
						<td style="width: 24%"></td>
						<td></td>
					</tr>

					<tr>
						<td style="width: 24%;">
							<h5 class="mo_ldap_dir_search_labels">
								<font color="#FF0000">*</font>Search By:
							</h5>
						</td>
						<td style="width: 76%;">
							<select style="width: 60%;" class="mo_ldap_dir_search_table_dropdown" id="modirsearchfield" name="mo_ldap_directory_search_field" required>
								<?php
									$search_option_labels    = ! empty( get_option( 'mo_ldap_ds_search_by_options' ) ) ? maybe_unserialize( get_option( 'mo_ldap_ds_search_by_options' ) ) : array();
									$filter_attributes_array = get_option( 'mo_ldap_ds_config' ) ? maybe_unserialize( get_option( 'mo_ldap_ds_config' ) ) : array();
									$search_options          = array();

								foreach ( $filter_attributes_array as $filter_attribute ) {
									if ( in_array( $filter_attribute['lable'], $search_option_labels, true ) ) {
										$search_options[ $filter_attribute['value'] ] = $filter_attribute['lable'];
									}
								}

									$exclude_attributes = array( 'mail', 'telephonenumber', 'hometelephone', 'mobile' );

								foreach ( $search_options as $key => $display_option ) {
									if ( ! in_array( $key, $exclude_attributes, true ) ) {
										echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $display_option ) . '</option>';
									}
								}
								?>
							</select>
						</td>
					</tr>

					<tr>
						<td style="width: 24%;">
							<h5 class="mo_ldap_dir_search_labels">
								<font color="#FF0000">*</font>Search Value:
							</h5>
						</td>
						<td style="width: 76%;">
							<input style="width: 60%;" class="mo_ldap_dir_search_table_textbox" type="text" id="modirsearchvalue" name="mo_ldap_directory_search_value" required placeholder="Enter search value" value="" />
							&nbsp;&nbsp;
							<button id="dirtestsearchbutton" class="mo-ldap-dir-search-button-primary">Search</button>
							&nbsp;&nbsp;
						</td>
						<td>
													<input type="hidden" value="<?php echo esc_url( site_url() ); ?>/wp-admin/admin-ajax.php" id="ajaxcallurl" />
													</td>
						<td></td>
					</tr>
				</table>
				<div id="ldapSearchResultsContainer"></div>
				<script>
					
					let website_url = "<?php echo esc_url( get_admin_url() ); ?>";
					website_url = website_url.slice(0, -9);
					
				</script>
		</div>
</section>
</section>
	<?php
}
/**
 * Function mo_ldap_dir_search_display_configuration_page : Display configuration page.
 *
 * @return void
 */
function mo_ldap_dir_search_display_configuration_page() {
	$request_uri                 = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$mo_ldap_ds_custom_css_array = ! empty( get_option( 'mo_ldap_ds_custom_styling' ) ) ? maybe_unserialize( get_option( 'mo_ldap_ds_custom_styling' ) ) : array();
	$mo_ldap_ds_bg_color         = isset( $mo_ldap_ds_custom_css_array['background-color'] ) ? $mo_ldap_ds_custom_css_array['background-color'] : '#4d4671';
	$mo_ldap_ds_font_color       = isset( $mo_ldap_ds_custom_css_array['font-color'] ) ? $mo_ldap_ds_custom_css_array['font-color'] : '#ffffff';

	?>
	<section class="section_ldap_display_configurations">

		<div class="ldap-sections-info-div">
			<h2 class="mo-ldap-dir-search-sections-main-heading">Display Configuration</h2>
		</div>

		<div class="mo_ldap_dir_search_display_conf_div">
			<p class="mo_ldap_dir_search_shortcode_name_para">

			<p class="mo_ldap_dir_search_shortcode_desc">
				Please add the following shortcode to display the users on the WordPress page.
			</p>

			<span class="mo_ldap_dir_search_shortcode_name"> Shortcode Name: </span> &nbsp;&nbsp;
			<span class="mo_ldap_dir_search_shortcode_value"> [miniorange_ldap_directory_search] </span>
			</p>

		</div>

	</section>
	<section class="section_ldap_display_configurations">
		<div class="ldap-sections-info-div">
			<h2 class="mo-ldap-dir-search-sections-main-heading">Search Results Custom Styling</h2>
		</div>
		<div class="mo_ldap_dir_search_display_conf_div form-group"> 
		<form id="mo_form1" name="f" method="post" action="">
			<?php wp_nonce_field( 'mo_ldap_directory_search_save_custom_css_nonce' ); ?>
			<input id="Mo_Ldap_Directory_Search_styling_form_action" type="hidden" name="option" value="mo_ldap_directory_search_save_custom_css"/>
			<table style="width:100%">
				<tr>
					<td>
						<table class="mo_ldap_dir_search_settings_table">
							<tr>
								<td style="width:50%;"><h5 class="mo_ldap_dir_search_labels">Background color</h5></td>
								<td style="width:50%;">
								
									<input class="mo_ldap_ds_card_input" id="mo_ldap_ds_bg_color" type="color" name="mo_ldap_ds_bg_color" value="<?php echo esc_attr( $mo_ldap_ds_bg_color ); ?>"><br>
								</td>
								<td>
								</td>
							</tr>
							<tr>
								<td style="width:33%"><h5 class="mo_ldap_dir_search_labels">Font Color</h5></td>
								<td style="width:33%">
									<input class="mo_ldap_ds_card_input" id="mo_ldap_ds_font_color"type="color" name="mo_ldap_ds_font_color" value="<?php echo esc_attr( $mo_ldap_ds_font_color ); ?>" ><br>
								</td>
							</tr>
							<tr>
								<td style="width:33%"><h5 class="mo_ldap_dir_search_labels" >Font Size (in px)<span><img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'includes/images/crown.png' ); ?>" width="20px"></span></h5></td>
								<td style="width:33%">
								
									<input type="number" id="mo_ldap_ds_font_size" name="mo_ldap_ds_font_size" min="10" max="25" class="mo_ldap_ds_card_input" value="14" readonly>
								</td>
							</tr>
						</table>
					</td>
					<td class="mo_ldap_ds_preview_section">
						<h3 style="text-align: center;color: black;">Preview</h3>
						<div class="mo_ldap_ds_preview_box">
							<div class="mo_dir_search_user_data" id="mo_dir_search_user_data">
								<table>
									<tbody>
										<tr>
											<td class="mo_profile_picture_td"><p><img class="mo_dir_search_profile_picture" id="mo_dir_search_profile_picture" src="<?php echo esc_url( site_url() ) . '//wp-content/plugins/ldap-ad-staff-employee-directory-search/includes/images/userPicDefault.jpg'; ?>"></p></td>
											<td class="mo_user_data_para_td"><p class="mo_dir_search_user_data_para" ><span class="mo_dir_search_user_data_label">Name</span> : Adam</p><p class="mo_dir_search_user_data_para" style="color: rgb(46, 45, 45);"><span class="mo_dir_search_user_data_label">Email</span> : Adam@abc.com</p><p class="mo_dir_search_user_data_para" style="color: rgb(46, 45, 45);"><span class="mo_dir_search_user_data_label">Phone</span> : +99999999</p><p class="mo_dir_search_user_data_para" style="color: rgb(46, 45, 45);"><span class="mo_dir_search_user_data_label">Department</span> : Sales</p></td>
										</tr>
									</tbody>
								</table>
							</div>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<input type="submit" class="mo-ldap-dir-search-button-primary" value="Save Custom CSS">
						</td>
					</tr>
				</table>

			</form>
		</div>

		<div class="mo_ldap_ds_outer_login_settings mo_ldap_ds_premium_box">
			<div class="mo_ldap_ds_premium_role_mapping_banner mo_ldap_d_none mo_ldap_ds_login_settings_premium">
				<div>
					<h1>Premium Plan</h1>
				</div>
				<div style="font-size: 16px;">This is available in premium version of the plugin</div>
				<div>
					<a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'pricing' ), htmlentities( sanitize_text_field( wp_unslash( $request_uri ) ) ) ) ); ?>" class="mo_ldap_upgrade_now1 mo_ldap_ds_unset_link_affect">
						<span><img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'includes/images/arrow.svg' ); ?>" height="10px" width="20px"></span> Upgrade Today
					</a>
				</div>
			</div>
			<a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'pricing' ), htmlentities( sanitize_text_field( wp_unslash( $request_uri ) ) ) ) ); ?>" class="mo_ldap_ds_unset_link_affect">
				<div class="mo_ldap_ds_premium_feature_btn">
					<span><img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'includes/images/crown.svg' ); ?>" height="20px" width="20px"></span>Premium Features
				</div>
			</a>
			<div class="mo_ldap_ds_premium_feature_box">
				<div class="ldap-sections-info-div">
					<h2 class="mo-ldap-dir-search-sections-main-heading">Search Header Custom Styling</h2>
				</div>
				<table class="mo-ldap-dir-search-w100">
					<tr>
						<td>
							<table class="mo_ldap_dir_search_settings_table">
								<tr>
									<td class="mo-ldap-dir-search-w50">
										<h5 class="mo_ldap_dir_search_labels">Font color</h5>
									</td>
									<td class="mo-ldap-dir-search-w50">

										<input class="mo_ldap_ds_card_input" id="mo_ldap_ds_bg_color" type="color" name="mo_ldap_ds_bg_color" onchange="CustomCss()" value="#000000" disabled><br>
									</td>
									<td>
									</td>
								</tr>
								<tr>
									<td class="mo-ldap-dir-search-w33">
										<h5 class="mo_ldap_dir_search_labels">Search Button Color</h5>
									</td>
									<td class="mo-ldap-dir-search-w33">
										<input class="mo_ldap_ds_card_input" id="mo_ldap_ds_font_color" type="color" name="mo_ldap_ds_font_color" value="#0045b4" disabled><br>
									</td>
								</tr>
								<tr>
									<td class="mo-ldap-dir-search-w33">
										<h5 class="mo_ldap_dir_search_labels">Heading Color</h5>
									</td>
									<td class="mo-ldap-dir-search-w33">
										<input class="mo_ldap_ds_card_input" id="mo_ldap_ds_font_color" type="color" name="mo_ldap_ds_font_color" value="#194175" disabled><br>
									</td>
								</tr>
								<tr>
									<td class="mo-ldap-dir-search-w33">
										<h5 class="mo_ldap_dir_search_labels">Heading text</h5>
									</td>
									<td class="mo-ldap-dir-search-w33">

										<input type="text" id="mo_ldap_ds_font_size" name="mo_ldap_ds_font_size" class="mo_ldap_ds_card_input" value="Search Employees" disabled>
									</td>
								</tr>
								<tr>
									<td class="mo-ldap-dir-search-w33">
										<h5 class="mo_ldap_dir_search_labels">Search By text</h5>
									</td>
									<td class="mo-ldap-dir-search-w33">

										<input type="text" id="mo_ldap_ds_font_size" name="mo_ldap_ds_font_size" class="mo_ldap_ds_card_input" value="Search By" disabled>
									</td>
								</tr>
								<tr>
									<td class="mo-ldap-dir-search-w33">
										<h5 class="mo_ldap_dir_search_labels">Search Value text</h5>
									</td>
									<td class="mo-ldap-dir-search-w33">

										<input type="text" id="mo_ldap_ds_font_size" name="mo_ldap_ds_font_size" class="mo_ldap_ds_card_input" value="Search Value" disabled>
									</td>
								</tr>
							</table>
						</td>
						<td class="mo_ldap_ds_search_header_preview">
							<h3 style="text-align: center;color: black;">Preview</h3>
							<div class="mo_ldap_ds_preview_box">
								<div style="border:1px solid grey;margin:auto;">
									<img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'includes/images/search_widget.png' ); ?>" alt="search_widget" width="300" height="150">
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<input type="submit" class="mo-ldap-dir-search-button-primary" value="Save">
						</td>
					</tr>
				</table>
			</div>
		</div>
	</section>

	<?php
}


/**
 * Function mo_ldap_directory_search_show_myaccount_page : Display my account page.
 *
 * @return void
 */
function mo_ldap_directory_search_show_myaccount_page() {
	?>
	<div class="mo_ldap_dir_search_local_main_head">
		<div>
			<a class="mo-ldap-dir-search-plugin-config-link" href="<?php echo esc_url( add_query_arg( array( 'tab' => 'search_config' ), isset( $_SERVER['REQUEST_URI'] ) ? htmlentities( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) : '' ) ); ?>">
				<button id="Back-To-Plugin-Configuration" type="button" value="Back-To-Plugin-Configuration" class="mo-ldap-dir-search-button-primary  mo_ldap_dir_search_main_buttons">
					<span class="dashicons dashicons-arrow-left-alt" style="vertical-align: middle;"></span> 
					Plugin Configuration
				</button> 
			</a> 
		</div>
		<div class="mo_ldap_dir_search_title_container" style="width:80%;">
			<div class="mo_ldap_dir_search_logo_container">
				<img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'includes/images/login_logo.png' ); ?>"  width="50" height="50">
			</div>
			<div class="mo_ldap_dir_search_local_title">
				Staff/Employee Business Directory for Active Directory
			</div>
		</div>
	</div>
	<section class="mo-ldap-dir-search-my-account-section">
		<form name="mo_ldap_directory_search_registration_page" id="mo_ldap_directory_search_registration_page" method="post" action="">
			<?php wp_nonce_field( 'mo_ldap_directory_search_registration_nonce' ); ?>
			<input type="hidden" name="option" value="mo_ldap_directory_search_registration"/>
			<div class="" style="padding:0 20% 5% 20%;">


				<h2 class="mo-ldap-dir-search-h2">Register with miniOrange</h2>

				<div class="">
					<p style="font-size:16px;"><strong>Why should you register? </strong></p>
					<div id="help_register_desc" style="background: aliceblue; padding: 10px 10px 10px 10px; border-radius: 10px;">
						You should register so that in case you need help, we can help you with step by step
						instructions. We support all known directory systems like Active Directory, OpenLDAP, JumpCloud etc.
						We do not store any information except the email that you will use to register with us.
					</div>
					</p>
					<table class="mo_ldap_settings_table" aria-hidden="true">
						<tr>
							<td style="font-size:16px;width:40%"><strong><span style="color:#FF0000;">*</span>Email:</strong></td>
							<td style="width:60%;">
								<?php
								$current_user = wp_get_current_user();
								$admin_email  = ! empty( get_option( 'mo_ldap_ds_admin_email' ) ) ? get_option( 'mo_ldap_ds_admin_email' ) : $current_user->user_email;
								?>
								<input class="mo_ldap_table_textbox" type="email" name="email" style="width:100%"
									required placeholder="person@example.com"
									value="<?php echo esc_attr( $admin_email ); ?>" 
									/>
							</td>
						</tr>
						<tr>
							<td style="font-size:16px;width:40%;"><strong><span style="color:#FF0000;">*</span>Password:</strong></td>
							<td style="width:60%;"><input class="mo_ldap_table_textbox" required type="password"
									name="password" placeholder="Choose your password (Min. length 6)" style="width:100%;"
									minlength="6" pattern="^[(\w)*(!@#$.%^&*-_)*]+$"
									title="Minimum 6 characters should be present. A maximum of 15 characters should be present. Only the following symbols (!@#.$%^&*) should be present."
								/></td>
						</tr>
						<tr>
							<td style="font-size:16px;width:40%;"><strong><span style="color:#FF0000">*</span>Confirm Password:</strong></td>
							<td style="width:60%;"><input class="mo_ldap_table_textbox" required type="password"
									name="confirmPassword" placeholder="Confirm your password"
									minlength="6" pattern="^[(\w)*(!@#$.%^&*-_)*]+$" style="width:100%;"
									title="Minimum 6 characters should be present. A maximum of 15 characters should be present. Only the following symbols (!@#.$%^&*) should be present."
								/></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td><br><input type="submit" style="font-weight:500;" name="submit" value="Register"
										class="mo-ldap-dir-search-button-primary  mo_ldap_dir_search_main_buttons" 
										/>
								<input type="button" name="mo_ldap_directory_search_goto_login" id="mo_ldap_directory_search_goto_login" style="font-weight:500;"
									value="Already have an account?" class="mo-ldap-dir-search-troubleshoot-button" 
									/>&nbsp;&nbsp;

							</td>

						</tr>
						<tr>
							<td>&nbsp;</td>
							<td style="padding: 10px 0; font-size: 16px"> <strong style="margin: 0;">Trouble in registering account? click <a href="https://www.miniorange.com/businessfreetrial" rel="noopener" target="_blank">here</a> for more info.</strong></td>
						</tr>
					</table>
				</div>
			</div>
		</form>
		<form name="f1" method="post" action="" id="mo_ldap_directory_search_goto_login_form">
			<?php wp_nonce_field( 'mo_ldap_directory_search_goto_login' ); ?>
			<input type="hidden" name="option" value="mo_ldap_directory_search_goto_login"/>
		</form>
		<script>
			jQuery('#mo_ldap_directory_search_goto_login').click(function () {
				jQuery('#mo_ldap_directory_search_goto_login_form').submit();
			});
		</script>
	</section>
	<?php
}

/**
 * Function mo_ldap_directory_search_login_page : Display plugin login page.
 *
 * @return void
 */
function mo_ldap_directory_search_login_page() {
	?>
	<div class="mo_ldap_dir_search_local_main_head">
		<div>
			<a class="mo-ldap-dir-search-plugin-config-link" href="<?php echo esc_url( add_query_arg( array( 'tab' => 'search_config' ), isset( $_SERVER['REQUEST_URI'] ) ? htmlentities( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) : '' ) ); ?>">
				<button id="Back-To-Plugin-Configuration" type="button" value="Back-To-Plugin-Configuration" class="mo-ldap-dir-search-button-primary  mo_ldap_dir_search_main_buttons">
					<span class="dashicons dashicons-arrow-left-alt" style="vertical-align: middle;"></span> 
					Plugin Configuration
				</button> 
			</a> 
		</div>
		<div class="mo_ldap_dir_search_title_container" style="width:80%;">
			<div class="mo_ldap_dir_search_logo_container">
				<img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'includes/images/login_logo.png' ); ?>"  width="50" height="50">
			</div>
			<div class="mo_ldap_dir_search_local_title">
				Staff/Employee Business Directory for Active Directory
			</div>
		</div>
	</div>
	<section class="mo-ldap-dir-search-my-account-section">
		<form name="mo_ldap_verify_password" id="mo_ldap_verify_password" method="post" action="">
			<?php wp_nonce_field( 'mo_ldap_directory_search_verify_customer_nonce' ); ?>
			<input type="hidden" name="option" value="mo_ldap_directory_search_verify_customer"/>
			<div class="mo_ldap_table_layout" style="padding:0 20% 5% 20%;">
				<div id="toggle1" class="panel_toggle">
					<h3 class="mo-ldap-dir-search-h2">Login with miniOrange</h3>
				</div>
				<div class="mo_ldap_panel">
					<p style="font-size:16px;">It seems you already have an account with miniOrange. Please enter your miniOrange email and password. <a target="_blank" href="https://login.xecurify.com/moas/idp/resetpassword" rel="noopener">Click here if you forgot your password?</a></p>
					<br/>
					<table aria-hidden="true" style="width:100%;">
						<tr>
							<td style="font-size:16px;width:40%"><strong><span style="color:#FF0000;">*</span>Email:</strong></td>
							<td style="width:60%;">
								<input class="mo_ldap_table_textbox" type="email" name="email"
									required placeholder="person@example.com" style="width:100%"
									value="<?php echo esc_attr( get_option( 'mo_ldap_ds_admin_email' ) ); ?>" 
									/>
							</td>
						</tr>
						<tr>
						<td style="font-size:16px;width:40%"><strong><span style="color:#FF0000;">*</span>Password:</strong></td>
							<td style="width:60%;"><input class="mo_ldap_table_textbox" required type="password"
									name="password" placeholder="Enter your password" style="width:100%" 
									minlength="6" pattern="^[(\w)*(!@#$.%^&*-_)*]+$"
									title="Minimum 6 characters should be present. A maximum of 15 characters should be present. Only the following symbols (!@#.$%^&*) should be present."
								/></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>
							<br>
								<input type="submit" name="submit" value="Login"
									class="mo-ldap-dir-search-button-primary  mo_ldap_dir_search_main_buttons" 
									/>
								<input type="button" name="mo_ldap_directory_search_goto_registration" id="mo_ldap_directory_search_goto_registration" value="New User? Register Here"
									class="mo-ldap-dir-search-troubleshoot-button" 
									/>
						</tr>
					</table>
				</div>
			</div>
		</form>
		<form name="f" method="post" action="" id="mo_ldap_directory_search_goto_registration_form">
			<?php wp_nonce_field( 'mo_ldap_directory_search_goto_registration_nonce' ); ?>
			<input type="hidden" name="option" value="mo_ldap_directory_search_goto_registration"/>
		</form>
		<script>
			jQuery('#mo_ldap_directory_search_goto_registration').click(function () {
				jQuery('#mo_ldap_directory_search_goto_registration_form').submit();
			});
		</script>
	</section>
	<?php
}

?>
