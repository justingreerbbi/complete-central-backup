<?php
function wpbu_overview_ui(){ 

	/*@update_option('wpbu-allow-error-analytics-nag', '');
	if( isset($_GET['wpbu-error-reporting']) && $_GET['wpbu-error-reporting'] == "on"){
		add_option('wpbu-allow-error-analytics-nag', 'ON');
		// Update the general settings
	}else{
		add_option('wpbu-allow-error-analytics-nag', 'OFF');
	}

	$allow_analytics = get_option('wpbu-allow-error-analytics-nag');
	if( empty( $allow_analytics ) ):?>
		<div class="wrap">
			<h2>Complete Central Backup</h2>
			
			<div class="wpbu-analytics-nag" style="padding: 20px; background: white;">
				<h3>Help make the plugin better!</h3>
				<p>
					We have added an error reporting component to the plugin and would like you to allow any errors caused by 
					Complete Central Backup to be reported to our analytics server. Errors are collected anonymously
					and not sensitive data is used. We simply want to make the plugin better but using the information provided by
					it user base.
				</p>

				<p>
					<strong> What do the developers hope to gain by collecting errors? </strong><br>
					For some users the plugin is failing and we are unable to see why. By reporting the errors,
					we are able to see what is happening and why in different enviroments.
				</p>

				<p>
					We have added a setting in the <b>"General Options"</b> tab that allows you to turn error reporting on and off.
				</p>

				<p>
					<hr>
					<h4> Would you like to help by allowing errors to be recorded? </h4>
					<a class="button button-primary" href="<?= admin_url(); ?>admin.php?page=wpbu_general">Yes, I would like to help out.</a> 
					<a class="button" href="<?= admin_url(); ?>?page=wpbu_overview&wpbu-error-reporting=off">No Thanks, I will pass.</a>
				</p>
			</div>

		</div>
	
	<?php endif;
	*/
	
	// Check for Backup and type
	// Instead of AJAX we will now use a stanadard approach. Which seems to work better

	if( isset( $_GET['wpbu-task'] ) && $_GET['wpbu-task'] == 'backup' ){

		if( ! empty($_GET['backup_type'] ) ){

			print '<h2>Complete Central Backup</h2>';

			switch ( $_GET['backup_type'] ){
				
				case 'full_backup':
					print "<p>Starting Full Backup...</p>";
					flush(); sleep(1);
					wpbu_do_full_backup();
					exit;
					break;

				case 'database_backup':
					print "<p>Starting Database Backup...</p>";
					flush(); sleep(1);
					wpbu_do_database_backup();
					exit;
					break;

					// do nothing if nothing is found
			}
		}


	}
	
	wpbu_admin_styles();
	wpbu_admin_scripts();
	wpbu_top_ui_callout();
	
	?>



	<div class="wrap">

		<div id="poststuff">

			<?php if(isset($_GET['settings-updated'])): ?>
					<div id="setting-error-settings_updated" class="updated settings-error"> 
						<p>
							<strong>Settings saved.</strong>
						</p>
					</div>
			<?php endif; ?>
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content">

					<!-- Start of tabs -->	

					<div class="wpbu-tabs">
					  <?php wpbu_admin_page_tabs(); ?>
					  <div class="clearboth"></div>
					</div>		

					<div class="wpbu-wrapper">

						<!--<div class="tab-description">
							<h3> Overview </h3>
						  	<p>
						  		Below are your current backups.
						  	</p>
						</div>-->
												  	
						<table class="widefat">
							<thead>
							    <tr>
							        <th>ID</th>
							        <th>Date</th> 
							        <th>Type</th>
							        <th>Status</th>       
							        <th>Size</th>
							    </tr>
							</thead>
							<tfoot>
							    <tr>
							    	<th>ID</th>
							    	<th>Date</th>
							    	<th>Type</th>
							    	<th>Status</th>
							    	<th>Size</th>
							    </tr>
							</tfoot>
							<tbody id="wpbu-backup-list">
								<?php
									$args = array( 'post_type' => 'wpbu_backups', 'posts_per_page' => 10 );
									$loop = new WP_Query( $args );
									if($loop->have_posts()): while ( $loop->have_posts() ) : $loop->the_post();
									$backup_status =  get_post_meta( $loop->post->ID, 'backup_status', true);
								?>

								<tr id="wpbu-backup-<?= $loop->post->ID; ?>">
							     <td><?php print $loop->post->ID; ?></td>
							     <td><?php the_time('F jS, Y  @ H: i: s'); ?>
							     	<b></b>

							     	<?php if(1==1): ?>
							     	<div class="row-actions">
							     		<span class="download"><a class="download-backup" title="Download this backup to your local computer" href="<?= WPBU_DOWNLOADER . '?download=' . $loop->post->ID; ?>">Download</a> | </span>
							     		<span class="delete"><a class="wpbu-remove-backup" href="javascript:void(0);" title="Delete the backup from the the server" data-id="<?= $loop->post->ID; ?>">Remove</a> </span>
							     		<span class="options"> |  <a href="javascript:void(0);" title="Backup Options" data-id="<?= $loop->post->ID; ?>">Options</a></span>
							     	</div>
									<?php endif; ?>

							     </td>
							     <td><?php print get_post_meta($loop->post->ID, 'backup_type', true); ?></td>
							     <td><b><?php print $backup_status; if($backup_status == 'In Progress') print ' <img class="ajax-loading-backup-browser" src="'.WPBU_ROOT_DIR .'/assets/loading.gif" width="20" align="top"><br /> <!--<small> 19% Complete</small>-->'; ?></b></td>
							     <td><b><?php print get_post_meta($loop->post->ID, 'backup_size', true); ?></b></td>
							    </tr>
								<?php endwhile; else: ?>
								<tr id="no-backups-found">
									<td id="no-backups-found"> There are no backups found. </td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
								<?php endif; ?>
							</tbody>
							</table>

							<br>
							<div class="doing-backup" style="margin-bottom: 20px; line-height: 30px; height: 30px; position: relative;">
								<span class="spinner" style="width: 40px; height: 40px; display: inline; position: relative; top: 3px;"></span> <b>Creating Backup...</b>
							</div>
							<a class="button button-primary" id="create-full-system-backup" href="<?= admin_url(); ?>?page=wpbu_overview&wpbu-task=backup&backup_type=full_backup"> Create Full Backup </a>
							<a class="button button-primary" id="create-database-backup" href="<?= admin_url(); ?>?page=wpbu_overview&wpbu-task=backup&backup_type=database_backup"> Create Database Backup </a>
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