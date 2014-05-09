<?php 
function wpbu_general_ui(){
	wpbu_admin_styles();
	wpbu_top_ui_callout();
	?>

	<div class="wrap">

		<?php if(isset($_GET['settings-updated'])): ?>
				<div id="setting-error-settings_updated" class="updated settings-error"> 
					<p>
						<strong>General settings saved.</strong>
					</p>
				</div>
		<?php endif; ?>

		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content">

					<!-- Start of tabs -->	

					<div class="wpbu-tabs">
					  <?php wpbu_admin_page_tabs(); ?>
					  <div class="clearboth"></div>
					</div>		

					<div class="wpbu-wrapper">

						<!--
						<div class="tab-description">
							<h3> General Settings </h3>
						  	<p>
						  		Here you will be able to control backup notifications, and the recipient of the notification.<br><br>
						  		Notifications only trigger when automated backups are ran.
						  	</p>
						</div>
						-->
						  	
						<form name="wpbu_general" method="post" action="options.php"/>
							<?php
								$wpbu_settings = get_option('wpbu_general_settings');
								settings_fields( 'wpbu-general-group' );
							?>
							<table class="form-table">
								<tr>
									<th><label for="backup-notifications">Backup Notifications</label></th>
									<td>
										<select id="backup-notifications" name="wpbu_general_settings[send_email_notification]">
											<option <?= $wpbu_settings['send_email_notification']=='OFF' ? 'selected="selected"' : ''; ?>>OFF</option>
											<option <?= $wpbu_settings['send_email_notification']=='ON' ? 'selected="selected"' : ''; ?>>ON</option>		
										</select>
									</td>
								</tr>

								<tr>
									<th>
										<label for="backup-notifications-contact">Notification Recipient</label><br>
										<!--<small class="description">
										 	This email will recieve backup notices when automatic backups run. Leave this field blank and the site admin will
										 	recieve the notifications.
										</small>-->
									</th>
									<td>
										<input class="regular-text" id="backup-notifications-contact" type="text" name="wpbu_general_settings[email_notification_contact]" size="32" placeholder="<?= get_bloginfo('admin_email'); ?>" value="<?= $wpbu_settings['email_notification_contact'] ?>" />
									</td>
								</tr>

								<!--<tr>
									<th>
										<label for="wpbu-error-tracking">Error Analytics</label><br>
										<small class="description">
											Allow the plugin to push errors to Blackbird Interactive. 
											No sensitive information is sent, just the stuff we need to know.
										</small>
									</th>
									<td>
										<select hidden id="wpbu-error-analytics" name="wpbu_general_settings[error_analytics]">
											<option value="OFF" <?= @$wpbu_settings['error_analytics']== 'OFF' ? 'selected="selected"' : ''; ?>>Deny</option>
											<option value="ON" <?= @$wpbu_settings['error_analytics']=='ON' ? 'selected="selected"' : ''; ?> >Allow</option>
										</select>	
									</td>
								</tr>-->
							</table>
							<select hidden id="wpbu-error-analytics" name="wpbu_general_settings[error_analytics]">
								<option value="OFF" <?= @$wpbu_settings['error_analytics']== 'OFF' ? 'selected="selected"' : ''; ?>>Deny</option>
								<option value="ON" <?= @$wpbu_settings['error_analytics']=='ON' ? 'selected="selected"' : ''; ?> >Allow</option>
							</select>	

							<?php submit_button('Update General Settings'); ?>
						</form>
					</div>		
					<!-- / End of tabs -->

				</div>

				<?php wpbu_support_form_widget(); ?>
				
			</div>
		<br class="clear">
		</div>
		
		

	</div>
	<!-- /wrap -->


<?php }