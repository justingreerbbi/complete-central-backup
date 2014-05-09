<?php
function wpbu_scheduler_ui(){
	wpbu_admin_styles();
	wpbu_top_ui_callout();
	?>

	<div class="wrap">

		<?php if(isset($_GET['settings-updated'])): ?>
			<div id="setting-error-settings_updated" class="updated settings-error"> 
				<p>
					<strong>Backup schedule saved</strong>
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
				<h3> Schedule Settings </h3>
			  	<p>
			  		Control how often your WordPress website is backed up.
			  	</p>
			</div>
			-->
			  	
			<form name="wpbu_general" method="post" action="options.php"/>
				<?php
					$wpbu_settings = get_option('wpbu_schedule_settings');
					settings_fields( 'wpbu-schedule-group' );
				?>
				<table class="form-table">

					<!-- Backup Frequency -->
					<tr>
						<th>
							<label for="backup-notifications">Backup Frequency</label><br>
							<!--
							<small class="description">
								The schedule uses WP Cron as a means to trigger backups.<br>
								<a href="http://wp.tutsplus.com/articles/insights-into-wp-cron-an-introduction-to-scheduling-tasks-in-wordpress/" title="Read more about WP Cron" target="_blank">Read more about WP Cron</a>
							</small>
							-->
						</th>
						<td>
							<select id="backup-notifications" name="wpbu_schedule_settings[backup_frequency]">
								<?php wpbu_get_admin_schedule_options(); ?>
							</select>
						</td>
					</tr>
					<!-- / backup Frequency -->

					<!-- backup Type -->
					<tr>
						<th>
							<label for="backup-type">Backup Type</label><br>
							<!--
							<small class="description">
								We recommend only doing database backups while using the automated backup feature.
								Full backups may slow your site down while backing up.
							</small>
							-->
						</th>
						<td>
							<select id="backup-type" name="wpbu_schedule_settings[backup_type]">
								<option value="database" <?php if($wpbu_settings['backup_type'] == 'database') print 'selected="selected";'?>> Database (recommended) </option>
								<option value="full" <?php if($wpbu_settings['backup_type'] == 'full') print 'selected="selected";'?>> Full </option>
							</select>
						</td>
					</tr>
					<!-- / backup Type -->

				</table>

				<?php submit_button('Update Backup Schedule Settings'); ?>
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