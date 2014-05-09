<?php
require_once( dirname( __FILE__ ) . '/helper.php' );
/**
 * Return filesize in MB, GB and TB if needed
 * @param  Integer $size Raw system size
 * @param  String $type MB, GB, TB
 * 
 * @return {Formatted Size}
 * @since 2.1.0
 */
function wpbu_convert_size( $size, $type ){
    switch($type){
    	case "KB":
            $filesize = $size * .0009765625; // bytes to KB
          	break;
        case "MB":
            $filesize = ( $size * .0009765625) * .0009765625; // bytes to MB
          	break;
        case "GB":
            $filesize = (( $size * .0009765625) * .0009765625) * .0009765625; // bytes to GB
          	break;
       }
    return round($filesize, 2).' '.$type;
}

/**
 * Return the cuurent system memeory limit
 * @return Current system memeory in MB
 */
function wpbu_get_memory_limit(){
	return ini_get('memory_limit');
}

/**
 * Attempts to chnage the memory limit 
 * If attempt was succefull, then we know that we are able to adjust the memory limit.
 * If we can not adjust the memory limit we need to trigger a admin notice about the issue
 * @return Boolean
 */
function wpbu_init_memory_check(){
	$stable = ini_get('memory_limit');
	ini_set( "memory_limit", "64M" );
	return $stable == ini_get("memory_limit") ? false : true;
}

/*
|--------------------------------------------------------------------------
| WPBU Register PostType
|--------------------------------------------------------------------------
|
| Registers WPBU Post Type - DO NOT MODIFY THIS
|
*/
add_action('init', 'register_wpbu_posttype');
function register_wpbu_posttype(){
	$labels = array(
	    'name'               => 'Backups',
	    'singular_name'      => 'Backup',
	    'add_new'            => 'Backup',
	    'add_new_item'       => 'Add New Backup',
 		'edit_item'          => 'Edit Backup',
	   	'new_item'           => 'New Backup',
	   	'all_items'          => 'All Backup',
	   	'view_item'          => 'View Backup',
	   	'search_items'       => 'Search Backups',
	   	'not_found'          => 'No Backups found',
	   	'not_found_in_trash' => 'No Backups found in Trash',
	   	'parent_item_colon'  => '',
	   	'menu_name'          => 'Backups'
	);
	$args = array(
	   	'labels'             => $labels,
	   	'public'             => false,
	   	'publicly_queryable' => false,
	   	'show_ui'            => true,
	   	'show_in_menu'       => true,
	   	'query_var'          => false,
	   	'rewrite'            => array( 'slug' => 'wpbu_backups' ),
	   	'capability_type'    => 'post',
	   	'has_archive'        => false,
	   	'hierarchical'       => false,
	   	'menu_position'      => null,
	   	'supports'           => array( 'title', 'editor', 'custom-fields' )
	);
  	register_post_type( 'wpbu_backups', $args );
}

/*
|--------------------------------------------------------------------------
| WPBU add main menu
|--------------------------------------------------------------------------
|
| Hardcode mmain page and all other pages will hook under this one
|
*/
add_action('admin_menu', 'wpbu_add_main_page');
function wpbu_add_main_page(){
	require_once( WPBU_ABSPATH . '/pages/overview.php' );
	$wpbu_main = add_menu_page( 'Complete Central Backup', 'Backup', 'manage_options', 'wpbu_overview', 'wpbu_overview_ui', '', null ); 
	add_action('load-'.$wpbu_main, 'wpbu_help_documentaton');
}

/*
|--------------------------------------------------------------------------
| WPBU include pages
|--------------------------------------------------------------------------
|
| Uses wpbu_pages filter - DO NOT MODIFY THIS
|
*/
function wpbu_include_pages(){
	global $wpbu;
	$pages = apply_filters('wpbu_pages', $wpbu->pages);
	if(is_array($pages)){
		foreach($pages as $page_info){
			foreach($page_info as $page){
				$wpbu_sub = add_submenu_page( 'wpbu_overview', $page['title'], $page['title'], $page['permission'], $page['slug'], $page['call'] );
				add_action('load-'.$wpbu_sub, 'wpbu_help_documentaton');
			}
		}
	}
}

/*
|--------------------------------------------------------------------------
| WPBU Register and Load Admin Styles
|--------------------------------------------------------------------------
|
| This should not be modified
|
*/
function wpbu_admin_styles(){
	do_action('wpbu_admin_styles');
	wp_register_style( 'wpbu-admin-styles', WPBU_ROOT_DIR . '/assets/css/wpbu-admin-styles.css'  );
	wp_register_style( 'wpbu-admin-fonts', WPBU_ROOT_DIR . '/assets/css/font-awesome.min.css'  );
	wp_enqueue_style( 'wpbu-admin-styles' );
	wp_enqueue_style( 'wpbu-admin-fonts' );
	wp_enqueue_style( 'jquery-color' );
	wp_enqueue_script('jquery-ui-tabs');
}

/*
|--------------------------------------------------------------------------
| WPBU Register and Load Admin Scripts
|--------------------------------------------------------------------------
|
| This should not be modified
|
*/
function wpbu_admin_scripts(){
	do_action('wpbu_admin_scripts');
	wp_enqueue_script( 'wpbu-admin-js', WPBU_ROOT_DIR . 'assets/js/admin.ajax.js?version=' . time() );
	wp_enqueue_script( 'wpbu-admin-functions-js', WPBU_ROOT_DIR . 'assets/js/functions.js?version=' . time() );
	wp_enqueue_script('thickbox');
    wp_enqueue_style('thickbox');
}

/*
|--------------------------------------------------------------------------
| WPBU Documentation Script
|--------------------------------------------------------------------------
|
| This should not be modified
|
*/
function wpbu_documentation_scripts(){
	wp_enqueue_script( 'wpbu-admin-js', WPBU_ROOT_DIR . '/assets/js/documentation.js?version=' . time() );
}
/*
|--------------------------------------------------------------------------
| WPBU admin page tabs
|--------------------------------------------------------------------------
|
| Displays formatted data based on the wpbu_pages filter
|
*/
function wpbu_admin_page_tabs(){
	global $wpbu;
	if(isset($wp_query->query_vars['page'])){ 
        $current_page = get_query_var('page');
    }
	$pages = $wpbu->pages['page'];
	print '<ul>';
	foreach($pages as $page){
		$walker_class = '';
		if($page['slug'] === $_GET['page']){
			$walker_class = 'class="active"';
		}

		print '<li><a '.$walker_class.' href="'.admin_url().'admin.php?page='.$page['slug'].'"><span class="'.$page['class'].'"></span> '.$page['title'].' </a></li>';
	}
	print '</ul>';
}

/*
|--------------------------------------------------------------------------
| WPBU admin top content
|--------------------------------------------------------------------------
|
| HTML content displayed at the top of each plugin page
|
*/
function wpbu_top_ui_callout(){ 

	// General Setting (notifications)
	$wpbu_general_settings 	= get_option('wpbu_general_settings');
	$wpbu_schedule_settings = get_option('wpbu_schedule_settings');
	$wpbu_account_settings 	= get_option('wpbu_account_settings');

	// Include the help tab for the plugin
	do_action('wpbu_load_help_tab');

	global $wpbu;
	?>
	<div class="wrap">
		<h2><span class="fa fa-cloud"></span> Complete Central Backup 
		<!-- <a href="http://development.dev/wp-admin/plugin-install.php" class="add-new-h2">Add New</a> </h2> -->
	</div>

	<!--
	<div id="welcome-panel" class="welcome-panel">

			<div class="welcome-panel-content">
				<h3>Complete Central Backup</h3>
				<p class="about-description">The easiest way to backup WordPress</p>
				<div class="welcome-panel-column-container">

					<div class="welcome-panel-column">

						<p class="promo-message">
							
							Having Troubles?<br><br>
							<a  class="button button-primary" href="<?= WPBU_REMOTE_HOME; ?>/register" target="_blank">Visit the Support Forums</a> 
			
						</p>

					</div>
					
					<div class="welcome-panel-column">
						<h4>Quick Links</h4>
						<ul>
							<li><span class="fa fa-fire fa-1x"></span> <a href="<?= WPBU_REMOTE_HOME; ?>/2012/10/25/complete-central-backup-wordpress-plugin/" target="_blank"> Whats New? </a></li>
							<li><span class="fa fa-comments fa-1x"></span> <a href="<?= WPBU_REMOTE_HOME; ?>/2012/10/25/complete-central-backup-wordpress-plugin/" target="_blank"> Support Forums</a></li>
							<li><span class="fa fa-thumbs-up fa-1x"></span> <a href="http://wordpress.org/support/view/plugin-reviews/complete-central-backup" target="_blank">Rate this plugin!</a></li>
						</ul>
					</div>

					<div class="welcome-panel-column welcome-panel-last">
						<h4>Plugin Information</h4>
						<ul>
							<li>Plugin Version: <b><?= $wpbu->version; ?></b> </li>
						</ul>
					</div>

				</div>
			</div>

		</div>
		-->
<?php
}

/*
|--------------------------------------------------------------------------
| Filters WP schedules
|--------------------------------------------------------------------------
|
| Filters the installed schedules but does it in a way that won't
| interferre with other schedule hooks that might be being used elsewhere.
| Reomves hourly and twice daily only for WPBU
|
*/
function wpbu_filtered_schedules(){
	$installed_schedules = wp_get_schedules();

	// Remove Hourly and twice daily
	unset($installed_schedules['hourly']);
	unset($installed_schedules['twicedaily']);

	return $installed_schedules;
}

/*
|--------------------------------------------------------------------------
| Schedule Output / WPBU Admin
|--------------------------------------------------------------------------
|
| Gathhers the availiable options and present a form select based on 
| settings.
|
*/
function wpbu_get_admin_schedule_options(){
	$schedule_options = wpbu_filtered_schedules();
	$schedule_keys = array_keys($schedule_options);
	$schedule_options_selected = get_option('wpbu_schedule_settings');

	print '<option> OFF </option>';
	foreach($schedule_keys as $key){
		if($schedule_options_selected['backup_frequency'] == $key){
			print '<option value="'.$key.'" selected="selected"> '.$schedule_options[$key]['display'].' </option>';
		}else{
			print '<option value="'.$key.'"> '.$schedule_options[$key]['display'].' </option>';
		}
	}
}

/*
|--------------------------------------------------------------------------
| WPBU Schedule Event Updater 
|--------------------------------------------------------------------------
|
| Checks the current state for  "OFF" and clear scheduled event else 
| it updates the scheduled event to what ever WPBU is set to.
|
*/
function wpbu_schedule_ensure(){
	$schedule_options = get_option('wpbu_schedule_settings');
	$schedule_setting = $schedule_options['backup_frequency'];
	if($schedule_setting == 'OFF'){
		if(wp_next_scheduled( 'wpbu_backup_event' )){
			wp_clear_scheduled_hook('wpbu_backup_event');
		}
	}else{
		if($schedule_setting != wp_get_schedule('wpbu_backup_event')){
			wp_clear_scheduled_hook('wpbu_backup_event');
			wp_schedule_event( current_time( 'timestamp' ), $schedule_setting, 'wpbu_backup_event');
		}

	}
}

/*
|--------------------------------------------------------------------------
| WPBU backup schedule
|--------------------------------------------------------------------------
|
| Trigger backup
|
*/
function wpbu_backup_event(){
	if(class_exists('WPBU_Backup')){
		$schedule_options = get_option('wpbu_schedule_settings');
		$general_settings = get_option('wpbu_general_settings');

		$wpbu_backup = new WPBU_Backup();
		if( $schedule_options['backup_type'] == 'database' ){
			$wpbu_backup->Create_Database_Backup();
		}elseif( $schedule_options['backup_type'] == 'full' ){
			$wpbu_backup->full_backup();
		}

		if( $general_settings['send_email_notification'] == 'ON' ){
			if($general_settings['email_notification_contact'] == ''){
				$email = get_bloginfo('admin_email');
			}else{
				$email = $general_settings['email_notification_contact'];
			}
			wp_mail( $email, 'Backup Notification', 'A '. $schedule_options['backup_type'] . ' backup task has run for '. site_url() );
		}
	}
}

/*
|--------------------------------------------------------------------------
| WPBU help Tab - Documentation
|--------------------------------------------------------------------------
|
*/
function wpbu_help_documentaton (){
	$screen = get_current_screen();

	// Welcome Tab
	$screen->add_help_tab( 
		array( 
		   'id' => 'wpbu-about-documentation',           
		   'title' => 'About',
		   'content' => file_get_contents( WPBU_ABSPATH . '/documentation/about.html' )
		)
	);

	// Whats New
	$screen->add_help_tab(
		array(
			'id' => 'wpbu-developer-documentation',
			'title'	=> 'Whats New',
			'content' => file_get_contents( WPBU_ABSPATH . '/documentation/whats-new.html' )
		)
	);
	$screen->set_help_sidebar( '<br><b>Plugin Version</b>: ' . WPBU_VERSION );
}

/*
|--------------------------------------------------------------------------
| WPBU Browser Notice
|--------------------------------------------------------------------------
|
| Displays a notification notice to people not using modern browsers.
| This is called on pages only
|
| @changelog 2.0.24 Added isset to $_GET var page
|
*/
function wpbu_admin_notice() {
	$show = array('wpbu_overview', 'wpbu_scheduler', 'wpbu_general', 'wpbu_addons');
	if( isset($_GET['page']) && ! in_array(@$_GET['page'], $show) )
		return;
	
	if( preg_match('/(?i)msie [2-9]/', $_SERVER['HTTP_USER_AGENT'] ) ) {
    	print '	<div class="update-nag">
       				<p class="fa fa-warning"> It looks like you are using a browser that is known to have issues with Complete Central Backup. Please download a reliable modern browser such as Chrome or Firefox.</p>
    			</div>';
	}
}
add_action( 'admin_notices', 'wpbu_admin_notice' );

/*
|--------------------------------------------------------------------------
| WPBU ZipArchive class check
|--------------------------------------------------------------------------
|
| Check for ZipArchive class and returns an error if it is not found
|
*/
function wpbu_zipArchive_notice() {
	$show = array('wpbu_overview', 'wpbu_scheduler', 'wpbu_general', 'wpbu_addons');
	if( isset($_GET['page']) && ! in_array(@$_GET['page'], $show) )
		return;
	
	if( !class_exists('ZipArchive') ) {
    	print '	<div class="update-nag error">
       				<p class="fa fa-warning"> Complete Central Backup depends on the PHP <b>ZipArchive</b> class. 
       				Your hosting does not have ZipArchive installed or enabled. If you would like to use
       				Complete Central Backup please enable <b>ZipArchive</ class. </p>
    			</div>';
	}
}
add_action( 'admin_notices', 'wpbu_zipArchive_notice' );


/*
|--------------------------------------------------------------------------
| WPBU Support Form Widget
|--------------------------------------------------------------------------
|
| Static form for sidebar in overview
|
*/
function wpbu_support_form_widget(){
	?>
	<div id="postbox-container-1" class="postbox-container">
		
		<!-- Error Analytics -->
		<div class="">
			<div id="submitdiv" class="postbox ">
				<h3 class=""><span> Help Improve the Plugin </span></h3>
				<div class="inside">
					<div class="submitbox" id="submitpost">
						<?php if(isset($_GET['wpbu-support-form-sent'])): ?>
							<h4 style="color: green; padding: 0 10px;"> Message Sent! </h4>
						<?php endif; ?>
						<p class="misc-pub-section">
							Allow the plugin to report issues to our analytics server.
							<form style="padding: 0px 10px;" method="post" action="options.php">
								<?php
									$wpbu_settings = get_option('wpbu_general_settings');
									settings_fields( 'wpbu-general-group' );
								?>
								<select id="wpbu-error-analytics" name="wpbu_general_settings[error_analytics]">
									<option value="OFF" <?= @$wpbu_settings['error_analytics']== 'OFF' ? 'selected="selected"' : ''; ?>>No Thanks</option>
									<option value="ON" <?= @$wpbu_settings['error_analytics']=='ON' ? 'selected="selected"' : ''; ?> >Allow Reporting</option>
								</select>
								<input type="hidden" name="wpbu_general_settings[email_notification_contact]" value="<?= $wpbu_settings['email_notification_contact'] ?>"/>
								<input type="hidden" name="wpbu_general_settings[send_email_notification]" value="<?= $wpbu_settings['send_email_notification'] ?>"/>
								<input class="button button-primary" type="submit" name="submit" value="Save" />
							</form>
						</p>

					</div>

				</div>
			</div>
		</div>

		<div class="">
			<div id="submitdiv" class="postbox ">
				<h3 class=""><span> Get Support </span></h3>
				<div class="inside">
					<div class="submitbox" id="submitpost">
						<?php if(isset($_GET['wpbu-support-form-sent'])): ?>
							<h4 style="color: green; padding: 0 10px;"> Message Sent! </h4>
						<?php endif; ?>
						<p class="misc-pub-section">
							In order to provide better support, this form provides us with your PHP version, 
							WP Version and Plugin Version.
							<form style="padding: 0px 10px;" method="post" action="http://blackbirdi.com/remote-support-contact.php">
								<label><strong> Your Email: </strong></label><br>
									<input type="email" name="admin_email" value="<?php bloginfo('admin_email'); ?>" style="width: 100%;" required/><br><br>
									<textarea style="resize: none; width: 100%; height: 100px" name="issue" placeholder="Explain your issue...." required></textarea><br><br>

									<input type="hidden" name="key" value="kjaghsdhaksdhhasjdh">

									<!-- Values that help out with trouble shooting -->
									<input type="hidden" name="php_version" value="<?= phpversion(); ?>"/>
									<input type="hidden" name="wp_version" value="<?php global $wp_version; print $wp_version; ?>"/>
									<input type="hidden" name="plugin_version" value="<?= WPBU_VERSION; ?>"/>
									<input type="hidden" name="redirect_back" value="<?= site_url() . $_SERVER['REQUEST_URI'];?>"/>
									<input type="submit" name="submit" class="button button-primary button-large" value="Send Request" />
							</form>
						</p>

					</div>

				</div>
			</div>
		</div>

	</div>



	<?php
}


/*
|--------------------------------------------------------------------------
| WPBU upgrade notice Nagg
|--------------------------------------------------------------------------
|
| Gets the latest version's reame upgrade notice and displays in under the 
| plugin when a update is avaliable.
|
*/
function wpbu_upgrade_notice_nagg( $plugin_data, $r ){
    $ch = curl_init ( 'http://plugins.svn.wordpress.org/complete-central-backup/trunk/readme.txt' );
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
	$data = curl_exec ($ch);
	curl_close($ch);
   	$upgrade_notice  = stristr( $data, '== Upgrade Notice ==' );
   	$upgrade_notice  = stristr( $upgrade_notice, '== Screenshots ==', true );
   	$upgrade_notice = str_replace('== Upgrade Notice ==', '', $upgrade_notice);
   	print '<p>'. $upgrade_notice .'</p>';
}

/*
|--------------------------------------------------------------------------
| Extend WPBU with ajax functionality
|--------------------------------------------------------------------------
|
| Other inludes
|
*/
require_once( dirname(__FILE__) . '/helper.php');
require_once( dirname(__FILE__) . '/admin-ajax.php');