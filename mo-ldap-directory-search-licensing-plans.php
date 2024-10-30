<?php
/**
 * This file renders the licensing page in the plugin.
 *
 * @package miniOrange_LDAP_Directory_Search
 * @subpackage Main
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Adding the required files.
require_once 'class-mo-ldap-directory-search-pricing.php';
require_once 'class-mo-ldap-directory-search-plugin-constants.php';

/**
 * Function mo_ldap_directory_search_show_licensing_page : Display licensing page.
 *
 * @return void
 */
function mo_ldap_directory_search_show_licensing_page() {
	echo '<style>.update-nag, .updated, .error, .is-dismissible, .notice, .notice-error { display: none; }</style>';
	wp_enqueue_style( 'mo_ldap_license_page_style', plugins_url( 'includes/css/mo-ldap-directory-search-license-page.min.css', __FILE__ ), array(), MO_LDAP_Directory_Search_Plugin_Constants::PLUGIN_VERSION );
	wp_enqueue_style( 'mo_ldap_grid_layout_license_page', plugins_url( 'includes/css/mo-ldap-directory-search-licensing-grid.min.css', __FILE__ ), array(), MO_LDAP_Directory_Search_Plugin_Constants::PLUGIN_VERSION );
	?>
	<div class="mo_ldap_dir_search_local_main_head">
		<div>
			<a style="font-size: 16px; color: #000;text-align: center;text-decoration: none;display: inline-block;" href="<?php echo esc_url( add_query_arg( array( 'tab' => 'search_config' ), isset( $_SERVER['REQUEST_URI'] ) ? htmlentities( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) : '' ) ); ?>">
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

	<script>

		var selectArray = JSON.parse('<?php echo wp_json_encode( new MO_LDAP_Directory_Search_Pricing() ); ?>');

		function instance_wise_pricing(elemId) {
			var selectPricingArray = selectArray[elemId];
			var selectElem = "$" + selectArray[elemId]["1"] + '<div style="border:0;"><p class="instanceClass" ><span style="font-size:15px;font-weight: 500;margin-right:10px;"> No. of instances:</span>';
			var selectElem =selectElem + ' <select class="no_instance" required="true" onchange="mo_ldap_ds_change_pricing(this)" id="' + elemId + '">';
			jQuery.each(selectPricingArray, function (instances, price) {
				selectElem = selectElem + '<option value="' + instances + '" data-value="' + instances + '">' + instances + ' </option>';
			})
			selectElem = selectElem + '</select></p></div>';
			return document.write(selectElem);
		}

		function mo_ldap_ds_change_pricing($this) {
			var selectId = jQuery($this).attr("id");
			var e = document.getElementById(selectId);
			var strUser = e.options[e.selectedIndex].value;
			var strUserInstances = strUser != "UNLIMITED" ? strUser : 500;
			selectArrayElement = [];
			if (selectId == "premium_plan_pricing") {
				selectArrayElement = "$" + selectArray.premium_plan_pricing[strUser];
				jQuery("#" + selectId).parents("div.premium-licensing-plan-box").find(".cd-value").text(selectArrayElement);
			}
		}
	</script>
	<div class="tab-content">
		<div class="tab-pane active text-center" id="cloud">
			<div class="cd-pricing-container cd-has-margins" style="max-width: unset">
				<section class="section-licensing-plans js--section-plans" id="plans">
					<div class="row">
						<h2 class="mo-ldap-h2">
							Plans For Everyone
						</h2>
					</div>
					<div class="row">
						<div class="col span-1-of-2 mo-ldap-dir-search-plan-boxes">
							<div class="licensing-plan-box js--wp-4">
								<div>
									<h3>Free Plan</h3>
									<p class="plan-price">$0</p>
								</div>
								<div>
									<ul>
										<li><emp class="fa fa-check icon-small"></emp>Search All LDAP/AD Users</li>
										<li><emp class="fa fa-check icon-small"></emp>Search Using Custom Attribute</li>
										<li><emp class="fa fa-check icon-small"></emp>Add search base of your choice</li>
										<li><emp class="fa fa-check icon-small"></emp>LDAPS Secure Connection</li>
										<li><emp class="fa fa-times icon-small"></emp>Multi-language Support</li>
									</ul>
								</div>
								<div>
									<a href="javascript:void(0)" class="btn btn-ghost">Active Plan</a>
								</div>
							</div>
						</div>
						<div class="col span-1-of-2 mo-ldap-dir-search-plan-boxes">
							<div class="licensing-plan-box premium-licensing-plan-box">
								<div>
									<h3>Premium Plan</h3>
									<p class="plan-price cd-value" id="standardID"><script>  instance_wise_pricing('premium_plan_pricing'); </script></p>
								</div>
								<div>
									<ul>
										<li><emp class="fa fa-check icon-small"></emp>Unlimited Attributes Configuration</li>
										<li><emp class="fa fa-check icon-small"></emp>Search Using Any Attribute</li>
										<li><emp class="fa fa-check icon-small"></emp>Multiple Search Bases</li>
										<li><emp class="fa fa-check icon-small"></emp>Custom Search Filter</li>
										<li><emp class="fa fa-times icon-small"></emp>Multi-language Support</li>
									</ul>
								</div>
								<div>
									<a href="#" id="mo_ldap_ds_buy_now_btn" class="btn btn-full" target="_blank">Buy Now</a>
								</div>
							</div>
						</div>
					</div>
				</section>

				<div class="PricingCard-toggle ldap-plan-title mul-dir-heading" id="upgrade-steps">
					<h2 class="mo-ldap-h2">How to upgrade to premium</h2>
				</div>
				<section class="section-steps"  id="section-steps">
					<div class="row">
						<div class="col span-1-of-2 steps-box">
							<div class="works-step">
								<div>1</div>
								<p>
									Click on <a href="https://login.xecurify.com/moas/login?redirectUrl=https://login.xecurify.com/moas/initializepayment&requestOrigin=wp_ldap_search_directory_premium_plan" target="_blank">Buy Now</a> button for premium plan and you will be redirected to <strong> miniOrange login console.</strong>
								</p>
							</div>
							<div class="works-step">
								<div>2</div>
								<p>
									Enter your username and password with which you have created an account with us. After that, you will be redirected to the payment page.
								</p>
							</div>
							<div class="works-step">
								<div>3</div>
								<p>
									Enter your card details and proceed with payment. On successful payment completion, the premium plugin will be available to download.
								</p>
							</div>
							<div class="works-step">
								<div>4</div>
								<p>
									Download the premium plugin from Plugin Releases and Downloads section.
								</p>
							</div>
						</div>
						<div class="col span-1-of-2 steps-box">
							<div class="works-step">
								<div>5</div>
								<p>
									From the WordPress admin dashboard, delete the free version of the plugin currently installed.
									&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
								</p>
							</div>
							<div class="works-step">
								<div>6</div>
								<p style="padding-top:10px;">
									Unzip the downloaded premium plugin and extract the files. <br> <br>
								</p>
							</div>
							<div class="works-step">
								<div>7</div>
								<p>
									Upload the extracted files using FTP to path /wp-content/plugins/. Alternately, go to Add New → Upload Plugin in the plugin's section to install the .zip file directly.<br>
								</p>
							</div>
							<div class="works-step">
								<div>8</div>
								<p>
									After activating the premium plugin, log in using the account you have registered with us.
								</p>
							</div>
						</div>
					</div>
					<div class="row" style="font-size:16px;padding-bottom:25px;">
						<strong>Note: </strong>The premium plans are available in the miniOrange dashboard. Please don't update the premium plugin from the WordPress Marketplace. We'll notify you via email whenever a newer version of the plugin is available in the miniOrange dashboard.
					</div>    
				</section>

				<section class="payment-methods">
					<div class="row">
						<h2 class="mo-ldap-h2">Supported Payment Methods</h2>
					</div>
					<div class="row">
						<div class="col span-1-of-3">
							<div class="plan-box">
								<div>
									<img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) ); ?>includes/images/cards.png)" width="95%;" height="105%" alt="">
								</div>
								<div>
									If the payment is made through Credit Card/International Debit Card, the license will be created automatically once the payment is completed.
								</div>
							</div>
						</div>
						<div class="col span-1-of-3">
							<div class="plan-box">
								<div>
									<img class="payment-images" src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'includes/images/paypal.png' ); ?>" alt="image not found">
								</div>
								<div>
									Use the following PayPal ID <strong>info@xecurify.com</strong> for making the payment via PayPal.<br><br>
								</div>
							</div>
						</div>
						<div class="col span-1-of-3">
							<div class="plan-box">
								<div>
									<em style="font-size:30px;" class="fas fa-university" aria-hidden="true"><span style="font-size: 20px;font-weight:500;">&nbsp;&nbsp;Bank Transfer</span></em>
								</div>
								<div>
									If you want to use a bank transfer for the payment then contact us at <span style="color:blue;text-decoration:underline; word-wrap: break-word;">ldapsupport@xecurify.com</span>  so that we can provide you the bank details.
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<p style="margin-top:20px;font-size:16px;">
							<span style="font-weight:500;"> Note:</span> Once you have paid through PayPal/Net Banking, please inform us so that we can confirm and update your license.
						</p>
					</div>
				</section>

				<div class="PricingCard-toggle ldap-plan-title mul-dir-heading">
					<h2 class="mo-ldap-h2">Return Policy</h2>
				</div>
				<section class="return-policy">
					<p style="font-size:16px;">
						If the premium plugin you purchased is not working as advertised and you’ve attempted to resolve any feature issues with our support team, which couldn't get resolved, we will refund the whole amount within 10 days of the purchase. <br><br>
						<span style="color:red;font-weight:500;font-size:18px;">Note that this policy does not cover the following cases: </span> <br><br>
						<span> 1. Change of mind or change in requirements after purchase. <br>
								2. Infrastructure issues not allowing the functionality to work.
						</span> <br><br>
						Please email us at <a href="mailto:ldapsupport@xecurify.com">ldapsupport@xecurify.com</a> for any queries regarding the return policy.
						<a href="#" class="button button-primary button-large back-to-top" style="font-size:15px;">Top &nbsp;↑</a>
					</p>
				</section>
			</div>
		</div>
	</div>
	<?php
}
?>
