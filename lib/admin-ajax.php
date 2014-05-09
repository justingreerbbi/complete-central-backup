<?php
// Admin ajax functions

// Do full backup
function wpbu_do_full_backup() {
	if(class_exists('WPBU_backup')){
		$backup = new WPBU_Backup();
		$backup = $backup->full_backup();
		//print json_encode($backup);
	}
	exit;
}

// Do database backup
function wpbu_do_database_backup() {
	if(class_exists('WPBU_backup')){
		$backup = new WPBU_Backup();
		$backup = $backup->Create_Database_Backup();
		//print json_encode($backup);
	}
	exit;
}

/**
 * Remove a backup from WordPress
 *
 * @updated @ 2.0.27 to remove backup post type. 
 * This will prevent the backup from stalling and never being removed.
 * Also make the action return true all the time since there is no handler for it.
 *
 * @todo  Add some feddback to tell what is going on.
 */
add_action('wp_ajax_wpbu_remove_backup', 'wpbu_remove_backup');
function wpbu_remove_backup(){
	$params = $_POST;
	if(empty($params['backupID']))
		return;

	$backup_location = get_post_meta($params['backupID'], 'backup_location', true);	
	@unlink( $backup_location ); 
	wp_delete_post( $params['backupID'] );

	print '1';
	exit;
}

/*
|--------------------------------------------------------------------------
| Unpack a backup
|--------------------------------------------------------------------------
|
| Returns a JSON object contain a tree structure of a given backup
|
*/
add_action('wp_ajax_wpbu_backup_browser', 'wpbu_backup_browser');
function wpbu_backup_browser(){

	$errors = array();
	$params = $_POST;

	// Make sure the backuID was given
	if(empty($params['backupID'])){
		array_push($errors, array( 'Missing backup parameter' ));
	}

	// Get the backup location for the backup CPT
	$backup_location = get_post_meta( $params['backupID'], 'backup_location', true );

	// make sure the backup exists
	if( ! file_exists( $backup_location ) ){
		array_push($errors, array( 'Backup does not exist' ));
	}

	// Report is there is any errors
	if( count( $errors ) > 0 ){
		print json_encode( array( 'errors' => $errors ) );
		exit;
	}

	$backup_clone_location = WPBU_ABSPATH . '/options/tmp';
	if( ! file_exists( $backup_clone_location )){
		mkdir( $backup_clone_location, 0755);
	}
	$backup_clone_name = 'tmp-browser-data.zip';

	// Try to copy the backup to a temp location
	if( ! @copy( $backup_location, $backup_clone_location. '/' . $backup_clone_name ) ){
		array_push($errors, array( 'Failed to make a copy of the backup' ));
		print json_encode( array( 'errors' => $errors ) );
		exit;
	}

	$zip = new ZipArchive;
	$zip->open( $backup_clone_location. '/' . $backup_clone_name );
	
	$zip->extractTo( WPBU_ABSPATH . '/options/tmp/' );
	$zip->close();

	// Everything should be unpacked and now we only want the level 1 files and directories
	$files = listDirectoryByFolder( WPBU_ABSPATH . '/options/tmp' );

	// print_r($files);
	
	$dir_content = array();

	array_push($dir_content, array(
								'directory' => '/'
								));

	// Loop through the files in the root directory
	foreach($files as $file){
		$file_data['filename'] 	= basename($file);
		$file_data['is_dir'] 	= is_dir( WPBU_ABSPATH . '/options/tmp/' . basename($file) ) ? '1':'0';
		$file_data['file_size']	= filesize_formatted( filesize( WPBU_ABSPATH . '/options/tmp/' . basename($file) ));
		$file_data['file_type']	= pathinfo( WPBU_ABSPATH . '/options/tmp/' . basename($file), PATHINFO_EXTENSION );
		array_push($dir_content, $file_data);
		
	}

	print json_encode( $dir_content );
	exit;
}

/*
|--------------------------------------------------------------------------
| Options Close - Function 
|--------------------------------------------------------------------------
|
| Cleans the the temp directory used for backup browser
*/
add_action('wp_ajax_wpbu_options_window_close', 'wpbu_options_window_close');
function wpbu_options_window_close(){
	delTree( WPBU_ABSPATH . '/options/tmp' );
}

/*
|--------------------------------------------------------------------------
| JS Analytics
|--------------------------------------------------------------------------
|
| This will send a call to the analytics server. We do not want to disturb
| the use so we will not listen for a response.
|
*/
add_action('wp_ajax_wpbu_report_analytics_to_server', 'wpbu_report_analytics');
function wpbu_report_analytics(){

	// Only send any information if the user has turned on error analytics
	$wpbu_settings = get_option('wpbu_general_settings');
	if( isset( $wpbu_settings['error_analytics'] ) && $wpbu_settings['error_analytics'] == 'ON' ){
		$data = $_POST['data'][0];
		$fields = '';
	   	foreach($data as $key => $value) { 
	    	$fields .= $key . '=' . $value . '&'; 
	   	}
	   	rtrim($fields, '&');
	  	$post = curl_init();
		curl_setopt($post, CURLOPT_URL, "http://analytics.blackbirdi.com/api/V1/track");
	   	curl_setopt($post, CURLOPT_POST, count($data));
	   	curl_setopt($post, CURLOPT_POSTFIELDS, $fields);
	   	curl_setopt($post, CURLOPT_RETURNTRANSFER, 1);
	   	$result = curl_exec($post);
	   	curl_close($post);
	  }
   	exit;
}