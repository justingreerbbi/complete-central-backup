<?php
/**
 * Manage backup option s
 *
 * This file is modeled after WP core media-upload.php 
 *
 * @author Justin Greer
 */

if ( ! isset( $_GET['iframe'] ) )
	define( 'IFRAME_REQUEST' , false );

/** instead we will load the front end until something better is written */
require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-admin/admin.php' );

// Check to make sure a backup ID is given
if ( ! isset( $_GET['backup'] ) ){
	wp_die('Something went wrong! Please reload the window and try again');
}

// Additional check to make sure the backup exists
// Add prepare statment for SQL injection prevention
global $wpdb;
$backupID = $_GET['backup'];
$backup_exists = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE id = '" . $backupID . "'", 'ARRAY_A');
if( ! $backup_exists ){
	wp_die('Whoops.. Looks like you are getting ahead of yourself!');
}

// Secury check - This should be sufficent enough. After all this is how WP restricts access
if (!current_user_can('manage_options') || IFRAME_REQUEST == false )
	wp_die(__('You do not have permission to access this file.'));

@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
?>
<html>
	<head>
		<script src="<?= WPBU_ROOT_DIR . 'assets/js/option.ajax.js?version='. time(); ?>"></script>
		<script>
		// Look into moving the script to an external source
		var $ = jQuery;
		$(document).ready(function(){
			$('#tabs').tabs();
		});
		</script>
		<style>
			#TB_ajaxContent {
				background: #f1f1f1;
			}
		</style>
	</head>
	<body>
		<section id="backup-options">
			<h2><span class="fa fa-gears"></span> Backup Options </h2>

			<div id="tabs">
			  <ul>
			    <li><a href="#tabs-1"><span class="fa fa-pen"</span> Backup Explorer </a></li>
			    <li><a href="#tabs-2"> Transfer </a></li>
			    <li><a href="#tabs-3"> Migrate </a></li>
			    <li><a href="#tabs-4"> Full Restore </a></li>
			  </ul>
			  <div id="tabs-1" class="tabContent backup-explorer-container">
			  	<div class="inner">
			  		<p>
			  			Browser your backup files. You can also restore individual files and directories.
			  		</p>

			  		<a class="button button-primary upack-backup" href="#" onclick="wpbu.file_browser.get_content(<?= $backupID; ?>); return false;"> Unpack the Backup </a>
			  		
			  		<!--
			  		<span class="ajax-loading-message"> 
			  			<img class="ajax-loading-backup-browser" src="<?= WPBU_ROOT_DIR; ?>/assets/loading.gif" width="40" align="middle">
			  			<span>Upnacking Backup</span>
			  		</span>
			  		-->

			  		<!-- Holder for the file browser content -->
			  		<div id="backup-browser-status"></div>
			  		
			  		<div id="wpbu-browser-content">
			  			
			  			<table class="widefat">
							<thead>
							    <tr>
							        <th></th>
							        <th>Name</th> 
							        <th>Type</th> 
							        <th>Size</th>
							    </tr>
							</thead>
							<tfoot>
							    <tr>
							    	<th></th>
									<th>Name</th> 
							        <th>Type</th> 
							        <th>Size</th>	
							    </tr>
							</tfoot>
							<tbody id="wpbu-browser-table-content">
								
							</tbody>
						</table>

			  		</div>
			  	</div>
			
			  </div>
			  <div id="tabs-2" class="tabContent">
			  	<div class="inner">
			  		<p>
			  			<b>Coming Soon</b><br>
			  			Push your backup to another server or service like Amozon, Dropbox and Google Drive.
			  		</p>
			  	</div> 
			  </div>
			  <div id="tabs-3" class="tabContent">
			    <div class="inner">
			  		<p>
			  			<b>Coming Soon</b><br>
			  			Package your backup for a move to another server.
			  		</p>
			  	</div> 
			  </div>
			  <div id="tabs-4" class="tabContent">
			    <div class="inner">
			  		<p>
			  			<b>Coming Soon</b><br>
			  			Restore your entire WordPress install right from the dashboard.
			  		</p>
			  	</div> 
			  </div>
			</div>
		</section>
	</body>
</html>