(function($){


	// JS Error Tracker Object
	jsErrorTracker = {};
	jsErrorTracker.errors = [];
	jsErrorTracker.calling = false;

	var gOldOnError = window.onerror;
	window.onerror = function myErrorHandler(errorMsg, url, lineNumber) {

		errorData = {
			errorMsg: errorMsg,
			url: url,
			lineNumber: lineNumber,
			browser: navigator.appName,
			browserVersion: navigator.appVersion,
			platform: navigator.platform
		}

		try{
			
			jsErrorTracker.errors.push( errorData );
		
		}catch(e){}

		/*
		|--------------------------------------------------------------------------
		| Start reporting
		|--------------------------------------------------------------------------
		|
		| WStart reporting when the page is compelte
		|
		*/
		var loadCheck = setInterval( function(){
			if ( document.readyState == "complete" ) {

				//console.log( 'Running Error Check! ' + jsErrorTracker.errors.length + ' Errors Found.' );

				// If there is an error, send it and reset
				if( jsErrorTracker.errors.length > 0 && !jsErrorTracker.calling ){
					jsErrorTracker.sendErrorReport();
					jsErrorTracker.errors.length = 0;
				}
				//clearInterval( loadCheck );
			}
		}, 3000);
		
	  	if (gOldOnError)
	    	return gOldOnError(errorMsg, url, lineNumber);

		return false;
	}


	// Function to send error to server
	jsErrorTracker.sendErrorReport = function(){

		try{
			jsErrorTracker.calling = true;
			var data = {
				action: 'wpbu_report_analytics_to_server',
				data: jsErrorTracker.errors
			}
			$.post(ajaxurl, data, function( response ){
				jsErrorTracker.calling = false;
				//console.log( response );
			});
		}catch(e){}

	};

	$(window).load(function(){
		$('#tab-link-wpbu-about-documentation a').addClass('fa fa-info-circle');
		$('#tab-link-wpbu-developer-documentation a').addClass('fa fa-bullhorn');
	});

})(jQuery);