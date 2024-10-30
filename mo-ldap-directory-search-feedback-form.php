<?php
/**
 * This file renders the Plugin deactivation form.
 *
 * @package miniOrange_LDAP_Directory_Search
 * @subpackage Main
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Function mo_ldap_directory_search_display_feedback_form : Display the deactivation feedback form.
 *
 * @return void
 */
function mo_ldap_directory_search_display_feedback_form() {
	if ( isset( $_SERVER['PHP_SELF'] ) && ( 'plugins.php' !== basename( sanitize_text_field( wp_unslash( $_SERVER['PHP_SELF'] ) ) ) ) ) {
		return;
	}
	?>
	</head>

	<body>
		<div id="ldapDirSearchModal" class="mo_ldap_dir_search_modal_feedback">
			<div class="mo_ldap_dir_search_modal_content_feedback">
				<h2 style="margin: 2%; text-align:center;font-weight: 600;display: inline-block;">We Value Your Feedback</h2><button class="close_local_feedback_form" onclick="getElementById('ldapDirSearchModal').style.display = 'none'">X</button></h3>
				<hr style="width:75%;">
				<form name="f" method="post" action="" id="mo_ldap_dir_search_feedback">
					<?php wp_nonce_field( 'mo_ldap_dir_search_feedback' ); ?>
					<input type="hidden" name="option" value="mo_ldap_dir_search_feedback" />
					<div>
						<h4 style="text-align:center;margin-bottom: 5px;">Please help us to improve our plugin by giving your opinion.</h4>
						<div class="mo_ldap_dir_search_emoji">
							<img class="sm" id="emoji_5" alt="Image not found" src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'includes/images/happy.png' ); ?>" />
							<img class="sm d-none" id="emoji_4" alt="Image not found" src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'includes/images/smile.png' ); ?>" />
							<img class="sm d-none" id="emoji_3" alt="Image not found" src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'includes/images/normal.png' ); ?>" />
							<img class="sm d-none" id="emoji_2" alt="Image not found" src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'includes/images/sad.png' ); ?>" />
							<img class="sm d-none" id="emoji_1" alt="Image not found" src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'includes/images/angry.png' ); ?>" />
						</div>
						<div id="mo_ldap_ds_smi_rate" class="mo_ldap_dir_sync_feedback_rating" style="text-align:center">
							<div id="mo_ldap_ds_outer" style="visibility:visible;margin-bottom:5px;"><p id="result" style="margin: 0;">Thank you for appreciating our work</p></div>
							<input type="radio" name="mo_ldap_ds_rate" class="mo_ldap_dir_search_rating_radio" id="angry" value="1" />
							<label for="angry">
								<span id="label-rate-1" class="fa fa-star mo_ldap_dir_search_rating_star_checked"></span>
							</label>

							<input type="radio" name="mo_ldap_ds_rate" class="mo_ldap_dir_search_rating_radio" id="sad" value="2" />
							<label for="sad">
								<span id="label-rate-2" class="fa fa-star mo_ldap_dir_search_rating_star_checked"></span>
							</label>


							<input type="radio" name="mo_ldap_ds_rate" class="mo_ldap_dir_search_rating_radio" id="neutral" value="3" />
							<label for="neutral">
								<span id="label-rate-3" class="fa fa-star mo_ldap_dir_search_rating_star_checked"></span>
							</label>

							<input type="radio" name="mo_ldap_ds_rate" class="mo_ldap_dir_search_rating_radio" id="smile" value="4" />
							<label for="smile">
								<span id="label-rate-4" class="fa fa-star mo_ldap_dir_search_rating_star_checked"></span>
							</label>

							<input type="radio" name="mo_ldap_ds_rate" class="mo_ldap_dir_search_rating_radio" id="happy" value="5" checked />
							<label for="happy">
								<span id="label-rate-5" class="fa fa-star mo_ldap_dir_search_rating_star_checked"></span>
							</label>

						</div>
						<hr style="width:75%;">
						<?php
						$email = get_option( 'mo_ldap_ds_admin_email' );
						if ( empty( $email ) ) {
							$user  = wp_get_current_user();
							$email = $user->user_email;
						}
						?>

						<div style="text-align:center;">
							<div style="display:inline-block; width:60%;">
								<div class="flex-center">
									<label for="mo_ldap_ds_query_mail"><strong>Email Address:</strong></label>
									<input type="email" id="mo_ldap_ds_query_mail" name="mo_ldap_ds_query_mail" class="mo_ldap_dir_search_email_field" placeholder="your email address" required value="<?php echo esc_attr( $email ); ?>" readonly="readonly" />

									<input type="radio" name="edit" id="edit" onclick="mo_ldap_ds_editName()" value="" />
									<label for="edit"><img class="editable" alt="Image not found" src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'includes/images/61456.png' ); ?>" />
									</label>
								</div>

							</div>

							<br><br>
							<div class="flex-center" style="width: 75%;margin: auto;">
								<input type="checkbox" id="mo_ldap_dir_search_need_followup" class="mo_ldap_dir_search_enable_tls_checkbox" style="padding: 9px !important;border: 2px solid #45497d;border-radius: 0;margin-top: 1px;" name="mo_ldap_ds_get_reply" value="YES" checked> 
								<label for="mo_ldap_dir_search_need_followup" style="font-size: 14px;margin-left: 5px;">I want to get in touch with your technical team for more assistance.</label>
							</div>
							<br>
							<textarea id="mo_ldap_ds_query_feedback" name="mo_ldap_ds_query_feedback" rows="4" style="width: 60%" placeholder="Tell us what happened!"></textarea>
							<div class="mo_ldap_dir_search_desc">On submitting the feedback your email address would be shared with the miniOrange team for contact purposes.</div></input>
							<br>
						</div><br>
						<div class="mo_ldap_modal-footer" style="text-align: center;margin-bottom: 2%">
							<input type="submit" style="font-weight:500;" name="miniorange_ldap_feedback_submit" id="miniorange_ldap_ds_feedback_submit" class="mo-ldap-dir-search-button-primary mo_ldap_dir_search_main_buttons" value="Submit" />
							<span width="30%">&nbsp;&nbsp;</span>
							<input type="button" name="miniorange_skip_feedback" class="mo_ldap_dir_search_skip_feedback_button" style="font-weight:500;" value="Skip feedback & deactivate" onclick="document.getElementById('mo_ldap_dir_search_feedback_form_close').submit();" />
						</div>
					</div>

					<script>
						const INPUTS = document.querySelectorAll('#mo_ldap_ds_smi_rate input');
						INPUTS.forEach(el => el.addEventListener('click', (e) => updateValue(e)));


						function mo_ldap_ds_editName() {
							document.querySelector('#mo_ldap_ds_query_mail').removeAttribute('readonly');
							document.querySelector('#mo_ldap_ds_query_mail').focus();
							return false;
						}

						function updateValue(e) {
							let selectedValue = e.target.value;
							for(let i=1;i<=5;i++) {
								let classes = document.getElementById('label-rate-'+ i).classList;
								if(i<=selectedValue) {
									if(!classes.contains("mo_ldap_dir_search_rating_star_checked")) {
										classes.add("mo_ldap_dir_search_rating_star_checked");
									}
								}
								else {
									if(classes.contains("mo_ldap_dir_search_rating_star_checked")) {
										classes.remove("mo_ldap_dir_search_rating_star_checked");
									}
								}
							}

							document.querySelector('#mo_ldap_ds_outer').style.visibility = "visible";
							var result = 'Thank you for appreciating our work';
							let emojis = document.querySelectorAll('.sm');
							emojis.forEach(emoji => {
								if(!emoji.classList.contains('d-none')) {
									emoji.classList.add('d-none');
								}
							});
							document.getElementById("emoji_"+selectedValue).classList.remove('d-none');
							switch (e.target.value) {
								case '1':
									result = 'Not happy with our plugin ? Let us know what went wrong';
									break;
								case '2':
									result = 'Found any issues? Let us know and we\'ll fix it ASAP';
									break;
								case '3':
									result = 'Let us know if you need any help';
									break;
								case '4':
									result = 'We\'re glad that you are happy with our plugin';
									break;
								case '5':
									result = 'Thank you for appreciating our work';
									break;
							}
							document.querySelector('#result').innerHTML = result;

						}
					</script>
				</form>
				<form name="mo_ldap_dir_search_feedback_form_close" method="post" action="" id="mo_ldap_dir_search_feedback_form_close">
					<?php wp_nonce_field( 'mo_ldap_dir_search_skip_feedback' ); ?>
					<input type="hidden" name="option" value="mo_ldap_dir_search_skip_feedback" />
				</form>

			</div>

		</div>



		<script>
			var active_plugins = document.getElementsByClassName('deactivate');
			for (i = 0; i < active_plugins.length; i++) {
				var plugin_deactivate_link = active_plugins.item(i).getElementsByTagName('a').item(0);
				var plugin_name = plugin_deactivate_link.href;
				if (plugin_name.includes('plugin=ldap-ad-staff-employee-directory-search')) {
					jQuery(plugin_deactivate_link).click(function() {

						var mo_ldap_modal = document.getElementById('ldapDirSearchModal');
						var span = document.getElementsByClassName("mo_ldap_close")[0];
						mo_ldap_modal.style.display = "block";
						window.onclick = function(event) {
							if (event.target == mo_ldap_modal) {
								mo_ldap_modal.style.display = "none";
							}
						}
						return false;
					});
					break;
				}
			}
		</script>
	</body>
	<?php
}
?>
