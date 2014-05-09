(function($){

})(jQuery);

// Main File Browser 
wpbu.file_browser = {};

wpbu.file_browser.current_directory = '/';

wpbu.file_browser.reinit = function(){

	$('tr').on('dblclick', function(){
		alert('You selected something');
	});
}



// Get browser content
wpbu.file_browser.get_content = function( backupID ){

	$('a.upack-backup').remove();

	$('#backup-browser-status').html('Gathering Backup Information');

	var data = {
		action: 'wpbu_backup_browser',
		backupID: backupID
	};
	$.post(ajaxurl, data, function(response) {
		
		var response = $.parseJSON(response);
		
		//console.log( response );

		// 0 = Current Directory
		// 1 = .
		// 2 = ..

		var browserContent;
		for(var i=1;i< response.length;i++){

			var file_class = ' class="fa fa-file-text-o" ';
			if( response[i].is_dir == 1 ){
				file_class = ' class="fa fa-folder" ';
			}

			var file_type = ' directory ';
			if( response[i].file_type != '' || ! response[i].is_dir ){
				file_type = response[i].file_type;
			}

			browserContent += '<tr data-file="'+response[i].filename+'">\
							     <td><span '+file_class+'></span></td>\
							     <td><b>'+response[i].filename+'</b>\
							    	<div class="row-actions">\
					     				<span class="View"><a href="">Open</a> | </span>\
					     				<span class="Retore"><a  href="" data-id="">Restore</a>\
					     			</div>\
							     </td>\
							     <td>'+file_type+'</td>\
							     <td>'+response[i].file_size+'</td>\
							    </tr>';

		}	
		$('#wpbu-browser-table-content').html(browserContent);
		$('#backup-browser-status').html('<b> Current Directory: </b>  ' + wpbu.file_browser.current_directory );

		wpbu.file_browser.reinit();

	});
}