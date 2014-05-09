<?php
/**
 * Plugin Name: Complete Central Backup
 * Plugin URI: http://blackbirdi.com/2012/10/25/complete-central-backup-wordpress-plugin/
 * Version: 2.1.0
 * Description: Complete Backup takes a complete backup of your wordpress site, even widgets and information. It then gives you the options to store locally, download, or push to any server using FTP.
 * Author: Justin Greer
 * Author URI: http://blackbirdi.com
 * License: GPL2
 * Text Domain: wpbu
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 *
 * @package  Complete Central Backup WordPress plugin
 * @author  Justin Greer <justin@justin-greer.com>
 * @copyright  Blackbird Inetractive 2014
*/

class WPBU_Backupware {

	// WPBU Version
	public $version = '2.1.0';

	public $pages = null;
	public $components = null;

	/**
	 * [__construct description]
	 * Loads the actions, filter and other needed companents
	 */
	public function __construct(){
		do_action('wpbu_pre');
		ignore_user_abort( true );
		
		add_action( 'plugins_loaded' , array( $this, 'init' ), 0 );
		add_filter( 'wpbu_components', array( $this,'register_wpbu_components'));
		add_filter( 'wpbu_pages', array( $this,'register_wpbu_pages'));
	}

	/*
	 |--------------------------------------------------------------------------
	 | WPBU Start Method
	 |--------------------------------------------------------------------------
	 |
	 | Setups up some basic infromation WPBU will use
	 |
	 */
	public function start(){
		define( 'WPBU_VERSION', $this->version);
		define( 'WPBU_ABSPATH' , dirname( __FILE__ ) );
		define( 'WPBU_ROOT_DIR', plugins_url('/', __FILE__ ) );
		define( 'WPBU_DOWNLOADER', WPBU_ROOT_DIR . 'components/downloader.php' );
		define( 'WPBU_REMOTE_HOME', 'http://blackbirdi.com');

		$wp_upload_basedir_wpbu = wp_upload_dir();
		define( 'WPBU_BACKUP_DIR', $wp_upload_basedir_wpbu['basedir'] . '/wpbu-backups' );
	}

	/*
	 |--------------------------------------------------------------------------
	 | WOBU Init Method
	 |--------------------------------------------------------------------------
	 |
	 | Simply does required things when WPBU is called
	 |
	 */
	public function init(){
		do_action('wpbu_pre_int'); 
		
		$this->start();
		$this->includes();
		$this->load();

		do_action('wpbu_post_init');
	}

	/*
	 |--------------------------------------------------------------------------
	 | WPBU's defualt components
	 |--------------------------------------------------------------------------
	 |
	 | Register components as an addon would to keep things uniform
	 |
	 */
	public function register_wpbu_components( $components ){

		$components['components']['wpbu_error'] = array(
			'title' 	=> 'Error Handler (Analytics)',
			'path'		=> WPBU_ABSPATH . '/components/error_handler.php',
			'version' 	=> '1.0.0',
			'auhtor'	=> 'Justin Greer',
			'description'	=> 'Reports core errors to the developers analaytics server.'
			);
		$components['components']['core_backup'] = array(
			'title' 	=> 'WPBU Core Backup',
			'path'		=> WPBU_ABSPATH . '/components/backup.php',
			'version' 	=> '2.0.2',
			'auhtor'	=> 'Justin Greer',
			'description'	=> 'WPBU core backup component'
			);
		$components['components']['wpbu_cron'] = array(
			'title' 	=> 'WP Cron Helper',
			'path'		=> WPBU_ABSPATH . '/components/cron.php',
			'version' 	=> '1.0.0',
			'auhtor'	=> 'Justin Greer',
			'description'	=> 'Extends native WP cron options.'
			);
		
		return $components;
	}

	/*
	 |--------------------------------------------------------------------------
	 | WPBU's default pages
	 |--------------------------------------------------------------------------
	 |
	 | Register pages as an addon would to keep things uniform. Keep in mind
	 | that the first page is dedicated as the main page. 
	 |
	 */
	public function register_wpbu_pages( $pages ){

		$pages['page']['wpbu_overvieww'] = array(
			'title' 	=> 'Overview',
			'path'		=> WPBU_ABSPATH . '/pages/overview.php',
			'class' 	=> '',
			'slug'		=> 'wpbu_overview',
			'permission'=> 'administrator',
			'call'		=> 'wpbu_overview_ui'
			);

		$pages['page']['wpbu_scheduler'] = array(
			'title' 	=> 'Schedule Options',
			'path'		=> WPBU_ABSPATH . '/pages/scheduler.php',
			'class' 	=> 'fa fa-calendar fa-1x',
			'slug'		=> 'wpbu_scheduler',
			'permission'=> 'administrator',
			'call'		=> 'wpbu_scheduler_ui'
			);

		$pages['page']['wpbu_general'] = array(
			'title' 	=> 'General Options',
			'path'		=> WPBU_ABSPATH . '/pages/general.php',
			'class' 	=> 'fa fa-gear fa-1x',
			'slug'		=> 'wpbu_general',
			'permission'=> 'administrator',
			'call'		=> 'wpbu_general_ui'
			);

		$pages['page']['wpbu_addons'] = array(
			'title' 	=> 'Addons',
			'path'		=> WPBU_ABSPATH . '/pages/addons.php',
			'class' 	=> 'fa fa-gear fa-1x',
			'slug'		=> 'wpbu_addons',
			'permission'=> 'administrator',
			'call'		=> 'wpbu_addons_ui'
			);

		return $pages;
	}

	/*
	 |--------------------------------------------------------------------------
	 | WPBU Includes Method
	 |--------------------------------------------------------------------------
	 |
	 | Grabs the basic files need right now
	 |
	 */
	public function includes(){

		// Require WPBU Functions
		require_once( WPBU_ABSPATH . '/lib/wpbu-functions.php');

		// Apply WPBU components Filter
		@$this->components = apply_filters( 'wpbu_components', $this->components );
		if(is_array($this->components)){
			foreach($this->components as $component){
				foreach($component as $include){
					require_once($include['path']);
				}
			}
		}

		// Apply WPBU Pages Filter
		@$this->pages = apply_filters( 'wpbu_pages', $this->pages );
		if(is_array($this->pages)){
			foreach($this->pages as $page){
				foreach($page as $include){
					require_once($include['path']);
				}
			}
		}
		do_action('wpbu_includes');
	}


	/*
	 |--------------------------------------------------------------------------
	 | WPBU Load Method
	 |--------------------------------------------------------------------------
	 |
	 | Loads all the things needed for WPBU
	 |
	 */
	public function load(){
		add_action( 'admin_menu', 'wpbu_include_pages');
		add_action( 'admin_init', array( $this, 'wpbu_register_settings') ); 
		add_action( 'plugins_loaded', array( $this, 'wpbu_register_languages') );
		add_action( 'plugins_loaded', 'wpbu_schedule_ensure' );
		add_action( 'wpbu_backup_event', 'wpbu_backup_event' );
		do_action('wpbu_load');
	}

	/**
	 * wpbu_register_settings Registers Complete Central Backups core settings (containers)
	 * @return 
	 */
	public function wpbu_register_settings(){
		register_setting( 'wpbu-global-group', 'wpbu_global_settings' );
		register_setting( 'wpbu-general-group', 'wpbu_general_settings' );
		register_setting( 'wpbu-schedule-group', 'wpbu_schedule_settings' );
	}

	/**
	 * wpbu_register_languages Loads Complete Central Backup's Language files directory
	 * @return 
	 *
	 * @todo Language files are not setup to work. Add lanaguagse eventully!
	 */
	 public function wpbu_register_languages(){
	 	load_plugin_textdomain('wpbu', false, basename( dirname( __FILE__ ) ) . '/languages');
	 }

	/**
	 * activate Adds basic options for Complete Central Backup
	 * @return 
	 */
	public function activate(){
		add_option('wpbu_account_settings');
		add_option('wpbu_general_settings');
		add_option('wpbu_schedule_settings');
		do_action('wpbu_activate');
	}

	/**
	 * [deactivate description]
	 * @return [type] [description]
	 */
	public function deactivate(){
		do_action('wpbu_deactivate');
	}

}

// Start WPBU
$wpbu = new WPBU_Backupware();
global $pagenow;
if ( 'plugins.php' === $pagenow ){
    $file   = basename( __FILE__ );
    $folder = basename( dirname( __FILE__ ) );
    $hook = "in_plugin_update_message-{$folder}/{$file}";
    add_action( $hook, 'wpbu_upgrade_notice_nagg', 20, 2 );
}