<?php
/**
 * Component Name: WPBU Core backup
 * Version: 2.1.0
 * Description: Adds backup functonality for WPBU.
 * Author: Justin Greer
 * Author URI: http://justin-greer.com
 *
 * @todo Add encrytion when backuping up whether the user wants to or not. This will imporve security overall.
 * @todo Test the crap out of the PclZip library. This needs to work acrossed the board and support more users in order
 * to be considered for stable release.
 *
 * @since 2.1.0 Converted zipArchive over to PclZip fir native support with PHP.
*/
class WPBU_Backup {

	public $version = "2.1.0";
	public $mysql_version;
	public $time;
	public $database_backup;
	public $backup_zip;
	public $start_time;
	public $end_time;

	public $file_structure = array();
	public $backup_exceptions = array('wpbu-backups');

	public $errors = false;
	public $silent = true;

	/*
	 |--------------------------------------------------------------------------
	 | Construct Method
	 |--------------------------------------------------------------------------
	 |
	 | Sets the class up for success
	 |
	 */
	public function __construct(){
		
		if( empty($silent) )
			$silent = false;


		// Attempt to stop the server from killing process once the browser has moved on
		ignore_user_abort( true );

		// Added in attempt to prevent timeouts
		set_time_limit( 0 );

		// Ensure that the backup directory exists
		if( $silent == false ){
			print '<p>Checking backup directory permissions</p>';
			flush(); sleep(1);
		}

		$this->wpbu_premission_check();
		
		$this->wpbu_backupdir_check();
		if( $this->errors == false){
			print '<script>setTimeout(function(){ window.location.href = "'.admin_url() .'?page=wpbu_overview"}, 3000);</script> <p>The backup has been started. The window should refresh in a few moments. If not please click <a href="'.admin_url() .'?page=wpbu_overview"> Here </a>. </p>';
			flush(); sleep(1);
		}
	
		// The backup time
		$this->time = date('M-d-Y-Gis').'-bak';

		// Gets the backup version
		$this->mysql_version = $this->get_mysql_version();

		// Designate the database backup .SQL file
		$this->database_backup = WPBU_BACKUP_DIR . '/'. $this->time.'.sql';

		// Designate the backup ZIP file
		$this->backup_zip = WPBU_BACKUP_DIR .'/'. $this->time.'.zip';

		// Since the backup can be triggered by anyone (wp cron) we need to make
		// the user id something. We will use something big just in case.
		$this->user_id = 999999999;

	}

	/*
	 |--------------------------------------------------------------------------
	 | Check and create backup directory
	 |--------------------------------------------------------------------------
	 |
	 | This should already be done but this is just in case
	 |
	 */
	public function wpbu_backupdir_check(){

		// Check the permissions of the directory as well
		if( ! file_exists( WPBU_BACKUP_DIR ) ){

			if( ! mkdir( WPBU_BACKUP_DIR, 0777 , true ) ){
				print "Failed to create the backup directory. Please correct you server permissions";
				flush(); sleep(1);
				$this->errors = true;
				exit;
			}
		
		}

		if( ! file_exists( WPBU_BACKUP_DIR . '/.htaccess' ) ){
			$htaccess = fopen( WPBU_BACKUP_DIR . '/.htaccess', 'w' );
			$data = 'deny from all';
			if( ! fwrite( $htaccess, $data) ){
				print "Could not secure the backup directory. Please check your server permissions. ";
				flush(); sleep(1);
				$this->errors = true;
				exit;
			}
			fclose( $htaccess );
		}

		if( ! is_readable( WPBU_BACKUP_DIR ) ){
			print "Backup directory is not readable. Please fix your server permissions.";
			flush(); sleep(1);
			$this->errors = true;
			exit;
		}
	
		return $this->errors;
	}

	/**
	 * full_backup Initiates a full backup of the WordPress install
	 * @return null 
	 *
	 * @todo Change from zipArchive to PclZip to support accrossed the board
	 */
	public function full_backup(){

		// Insert a record of the backup first
		$page['post_type']    = 'wpbu_backups';
		$page['post_content'] = 'WP Backupware - ' . $this->time;
		$page['post_parent']  = 0;
		$page['post_author']  = $this->user_id;
		$page['post_status']  = 'private';
		$page['post_title']   = 'WP Backupware - ' . $this->time;
		$pageid = wp_insert_post ($page);
		if ($pageid != 0) {
			add_post_meta( $pageid, 'backup_status', 'In Progress');
			add_post_meta( $pageid, 'backup_size', '---');
			add_post_meta( $pageid, 'backup_location', $this->backup_zip);
			add_post_meta( $pageid, 'backup_type', 'Full');
		}

		$zip = new ZipArchive();
		$zip->open( $this->backup_zip, ZipArchive::CREATE);
	
		/**
		 * Start the export of the database
		 * @since 1.0.1
		 */
		global $wpdb;
		$this->write_mysql_bak_header_information();
    	$tables = $wpdb->get_results( "SHOW TABLES" );
		$table_call = 'Tables_in_'.DB_NAME;
		$data_buffer = null;
		foreach( $tables as $table ){
		  	$table_name = $table->$table_call;
		  	$data_buffer .= "-- \n";
		  	$data_buffer .= "-- Table structure for table `".$table_name."` \n";
		  	$data_buffer .=  "-- \n";
		  	$data_buffer .= "\n";
		  	$data_buffer .= "DROP TABLE IF EXISTS `".$table_name."`; \n";

		  	$create_table = $this->show_create_table( $table_name );
		  	$data_buffer .= $create_table . "; \n";
		  	$data_buffer .= "\n";
		  	$data_buffer .= "-- \n";
		  	$data_buffer .= "-- Dumping data for table `".$table_name."` \n";
			$data_buffer .= "-- \n";
			$data_buffer .= "\n";
			$data_buffer .= "LOCK TABLES `".$table_name."` WRITE; \n";
			$data_buffer .=  $this->show_create_insert( $table_name ) ."\n";
		  	$data_buffer .= "UNLOCK TABLES; \n";
			$data_buffer .= "\n";
		}

		$this->write( $data_buffer );	

		// Add the Database backup to the backupZIP
		$zip->addFile( $this->database_backup, '/db_source.sql');
  
		// Now we need to create the file system backup
		$this->dir_tree( ABSPATH );
		foreach( $this->file_structure as $file ){
			$zip->addFile( ABSPATH . $file, $file );
		}

		// Make sure the file is closed. if not the filesize will not be able to read it
		$zip->close();
	  	
	  	// Get the backup size
		$backup_size = $this->get_file_size( $this->backup_zip, 'MB', $pageid );
		update_post_meta( $pageid, 'backup_size', $backup_size);
	  	  
		// Remove database SQL (clean up)
		@unlink( $this->database_backup );    
				  
		// No need for a response
		return;

	}

	function wpbu_premission_check(){
		$this->dir_tree( ABSPATH );
		foreach( $this->file_structure as $file ){
			//chmod(ABSPATH . $file, 0755);
			if( !is_readable( ABSPATH . $file )){
				print $file . ' is not readable. Please fix the file or directory permision.';
				flush(); sleep(1);
				$this->errors = true;
				exit;
			}
		}
	}

	function Create_Database_Backup(){
		
		// Insert a record of the backup first
		$page['post_type']    = 'wpbu_backups';
		$page['post_content'] = 'WP Backupware - ' . $this->time;
		$page['post_parent']  = 0;
		$page['post_author']  = $this->user_id;
		$page['post_status']  = 'private';
		$page['post_title']   = 'WP Backupware - ' . $this->time;
		$pageid = wp_insert_post ($page);
		if ($pageid != 0) {
			add_post_meta( $pageid, 'backup_status', 'In Progress');
			add_post_meta( $pageid, 'backup_size', '---');
			add_post_meta( $pageid, 'backup_location', $this->backup_zip);
			add_post_meta( $pageid, 'backup_type', 'Database');
		}
	 	
		$zip = new ZipArchive();
		$zip->open( $this->backup_zip, ZipArchive::CREATE);

	 	global $wpdb;
		$this->write_mysql_bak_header_information();
    	$tables = $wpdb->get_results( "SHOW TABLES" );
		$table_call = 'Tables_in_'.DB_NAME;

		$data_buffer = null;
		foreach( $tables as $table ){
		  	$table_name = $table->$table_call;
		  	$data_buffer .= "-- \n";
		  	$data_buffer .= "-- Table structure for table `".$table_name."` \n";
		  	$data_buffer .=  "-- \n";
		  	$data_buffer .= "\n";
		  	$data_buffer .= "DROP TABLE IF EXISTS `".$table_name."`; \n";

		  	$create_table = $this->show_create_table( $table_name );
		  	$data_buffer .= $create_table . "; \n";
		  	$data_buffer .= "\n";
		  	$data_buffer .= "-- \n";
		  	$data_buffer .= "-- Dumping data for table `".$table_name."` \n";
			$data_buffer .= "-- \n";
			$data_buffer .= "\n";
			$data_buffer .= "LOCK TABLES `".$table_name."` WRITE; \n";
			$data_buffer .=  $this->show_create_insert( $table_name ) ."\n";
		  	$data_buffer .= "UNLOCK TABLES; \n";
			$data_buffer .= "\n";
		}
		$this->write( $data_buffer );
		$zip->addFile( $this->database_backup, '/database_backup.sql');
		$zip->close();

		// Get the backup size
		$backup_size = $this->get_file_size( $this->backup_zip, 'MB', $pageid );
		update_post_meta( $pageid, 'backup_size', $backup_size);
	  	  
		// Remove database SQL (clean up)
		@unlink( $this->database_backup );    
				  
		// No need for a response
		return;  
	 }

	/*
	 |--------------------------------------------------------------------------
	 | Get file size
	 |--------------------------------------------------------------------------
	 |
	 */
    public function get_file_size($file, $type, $ID){
       switch($type){
          case "KB":
            $filesize = filesize($file) * .0009765625; // bytes to KB
          	break;
          case "MB":
            $filesize = (filesize($file) * .0009765625) * .0009765625; // bytes to MB
          	break;
          case "GB":
            $filesize = ((filesize($file) * .0009765625) * .0009765625) * .0009765625; // bytes to GB
          	break;
       }
       if($filesize <= 0){
       	@update_post_meta( $ID, 'backup_status', 'Failed');
        return $filesize = 'Unknown';
       }else{
        @update_post_meta( $ID, 'backup_status', 'Completed');
        return round($filesize, 2).' '.$type;
        }
    }

	/*
	 |--------------------------------------------------------------------------
	 | Show create table syntax
	 |--------------------------------------------------------------------------
	 |
	 | Syntax straight form the MySQL server
	 |
	 */
    protected function show_create_table($table = null){
    	if(empty($table))
    		return;
    	global $wpdb;
    	$status = $wpdb->get_results("SHOW CREATE TABLE {$table}");
    	return $status[0]->{'Create Table'};
    }

    /*
	 |--------------------------------------------------------------------------
	 | Show create table syntax
	 |--------------------------------------------------------------------------
	 |
	 | Collect data and returns a formatted dump of any given table contents
	 |
	 */
    protected function show_create_insert($table = null){
    	if(empty($table))
    		return;
    	global $wpdb;
    	$data = $wpdb->get_results("SELECT * FROM {$table}");

    	$return ='';
    	if(count($data)>0){

    		foreach($data as $insert){
    			$value_array = array();
    			foreach($insert as $key=>$value){
    				if ( is_int($value) ){
    					array_push($value_array, $value);
    				}else{
    					array_push($value_array, "'".str_replace("'", "\'", $value)."'");
    				}
    			}
    			$row_values = implode(", ", $value_array);

    			// Build the first half of the insert
    			$return .= "INSERT INTO `".$table."` VALUES (".$row_values."); \n";
    			
    		}

    	}
    	return $return;
    }

	/*
	 |--------------------------------------------------------------------------
	 | Return MySQL Version
	 |--------------------------------------------------------------------------
	 |
	 | Retuns a formatted version number of current MySQL
	 |
	 */
	public function get_mysql_version(){
		global $wpdb;
		$version = $wpdb->get_results("SELECT VERSION()");
       	return $version[0]->{'VERSION()'};
	}

	/*
	 |--------------------------------------------------------------------------
	 | Write header information 
	 |--------------------------------------------------------------------------
	 |
	 | Write header information to the database backup dump
	 |
	 */
	protected function write_mysql_bak_header_information(){
		// Header Information
        $this->write( "-- PHP MySQL Dump Beta, Complete Central Backup (".$this->version.")" );
        $this->write( "--" );
        $this->write( "-- Host: ".DB_HOST."    Database: ".DB_NAME."" );
        $this->write( "-- ------------------------------------------------------" );
        $this->write( "-- Server version  ".$this->mysql_version."" );
        $this->write( "" );
	}

	/*
	 |--------------------------------------------------------------------------
	 | Gets the file system tree
	 |--------------------------------------------------------------------------
	 |
	 | Returns the entire filesystems absolute paths
	 |
	 */
	public function dir_tree($path){
	    if($dh = opendir($path)){
			while(false !== ($file = readdir($dh))){
				if(($file !== '.') && ($file !== '..')){

					if(in_array($file,$this->backup_exceptions))
						continue;

					if(!is_dir($path.$file)){
						array_push($this->file_structure, str_replace(ABSPATH, '', $path.$file) );
					}else{
						$this->dir_tree($path.$file.'/');
					}
				}
			}
		}
	}

	/*
	 |--------------------------------------------------------------------------
	 | Write to the file current backup file
	 |--------------------------------------------------------------------------
	 |
	 | Retuns a formatted version number of current MySQL
	 |
	 */
	protected function write( $content ){

    	if(empty($content))
    		return;

		$handler = fopen( $this->database_backup, 'a' );
	    fwrite( $handler,  $content . "\n");
	    fclose( $handler );
    
    }
}