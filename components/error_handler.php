<?php
// Simple error analytic class for reporting to our analytics API
class wpbu_ErrorHandler{
	function reportError( $error ){
		$this->sendError( $error );
		return;
	}
	function sendError( $error ){
		if(empty($error))
			return; // silent return

    $fields = '';
    foreach($error as $key => $value) { 
        $fields .= $key . '=' . $value . '&'; 
    }
    rtrim($fields, '&');
		
    $errors = $fields .'&php_version='.phpversion();
		$post = curl_init();
		curl_setopt($post, CURLOPT_URL, "http://analytics.blackbirdi.com/api/V1/track");
	   	curl_setopt($post, CURLOPT_POST, count($errors));
	   	curl_setopt($post, CURLOPT_POSTFIELDS, $errors);
	   	curl_setopt($post, CURLOPT_RETURNTRANSFER, 1);
	   	$result = curl_exec($post);
	   	curl_close($post);
	}
}

// ONLY set this trigger if the user allows it
$wpbu_settings = get_option('wpbu_general_settings');
if( $wpbu_settings['error_analytics'] == 'ON')
  register_shutdown_function( "wpbu_fatal_handler" );

function wpbu_fatal_handler() {
  $errfile = "unknown file";
  $errstr  = "shutdown";
  $errno   = E_CORE_ERROR;
  $errline = 0;
  $error = error_get_last();
  if( $error !== NULL) {
    $errno   = $error["type"];
    $errfile = $error["file"];
    $errline = $error["line"];
    $errstr  = $error["message"];
  }

  // we only wan to track errror from the plugin
  if( ! strpos( $errfile, '/plugins/complete-central-backup/') )
       return; // We do not want to know about it

  $wpbuError = new wpbu_ErrorHandler;
  $wpbuError->reportError( $error );
}
