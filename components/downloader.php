<?php
// Check tp make sure that the file is installed in WP
// @TODO add adition checks to make sure incase of mutiple installs
if( ! file_exists( '../../../../wp-load.php' ) ){
	print 'No WP hook found';
	exit;
}

// Hook into WP
require_once( '../../../../wp-load.php' );

// Make sure the person is logged in and can manage options
if( !current_user_can('manage_options') )
	exit;

// Make sure that there is a backup provided
if( !isset($_REQUEST['download'] ) ){
	print 'No backup id provided';
	exit;
}

$backupID = intval( $_REQUEST['download'] );
$backup_file = get_post_meta($backupID, 'backup_location', true );

$backup_check = WPBU_BACKUP_DIR . '/' . basename( $backup_file );
if( empty( $backup_file ) ){
	print 'No backup found';
	exit;
}

$backup_check = WPBU_BACKUP_DIR . '/' . basename( $backup_file );
if ( !is_readable( $backup_check ) ){
	print 'No read rights to backup. Check your server permissions.' ;
	exit;
}

$file_name = basename( $backup_file );
header("Content-Type: application/zip");
header("Content-Disposition: attachment; filename=$file_name");
header("Content-Length: " . filesize( $backup_file ));
readfile($backup_file);

exit;