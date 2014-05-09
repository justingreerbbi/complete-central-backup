/**
 * WPBU Admin JS functions
 *
 * This file contains js functions for the admin area "as needed". We should use WP JS hooks when possible. Nobody likes hijackers
 *
 * @author wpbackupware
 */
(function($){
	
	/*
	|--------------------------------------------------------------------------
	| WBOU Admin namespace
	|--------------------------------------------------------------------------
	|
	| If there is a function that we need, it should be ran through this lib
	|
	*/
	wpbu = {};

	/*
	|--------------------------------------------------------------------------
	| WBOU Options url
	|--------------------------------------------------------------------------
	|
	| For now we will do it this way. We will have to add this is PHP to gether 
	| this value the correct way.
	|
	*/
	wpbu.optionsUrl = '/wp-content/plugins/complete-central-backup/options/';

	wpbu.doing_backup = false;
	wpbu.placeholder_id = 'backup_placeholder';

	/*
	|--------------------------------------------------------------------------
	| WPBU Admin Init functions
	|--------------------------------------------------------------------------
	|
	| Simply resets listeners to a fresh state. We would use something like 
	| "on" or "live" but they never are stable, so we will do it the old fashion
	| way.
	|
	*/
	wpbu.init = function(){

		/*
		|--------------------------------------------------------------------------
		| RESET
		|--------------------------------------------------------------------------
		|
		| RESETS eventlisteners so new elements can hook without 
		| adding to old events.
		|
		*/
		$('.wpbu-remove-backup').unbind();

		/*
		|--------------------------------------------------------------------------
		| WPBU backup options popup
		|--------------------------------------------------------------------------
		|
		| Options up the options for any given backup 
		|
		*/
		$('.options').click(function(e) {
			e.preventDefault();
			var backupID = $(this).children('a').attr('data-id');
	        tb_show('Backup Options', wpbu.optionsUrl + 'index.php?iframe=inline&backup=' + backupID+'&height=600&width=900	', false);
	        $('#TB_window').on("tb_unload", function(){
		       var data = {
					action: 'wpbu_options_window_close'
				};
				$.post(ajaxurl, data, function(response) {});
		    });
	        return false;
	    });

		/*
		|--------------------------------------------------------------------------
		| EVENTLISTENER - Removes a backup
		|--------------------------------------------------------------------------
		|
		| Removed a backup from teh system and the backup list
		|
		*/
		$('.wpbu-remove-backup').click(function(e){
			e.preventDefault();
			var backupID = $(this).attr('data-id');
			var hook = $('#wpbu-backup-'+backupID);
			if(confirm('Are you sure that you want to DELETE this backup?')){
				$(hook).animate({
					opacity: 0.5
				});
				var data = {
					action: 'wpbu_remove_backup',
					backupID: backupID
				};
				$.post(ajaxurl, data, function(response) {
					if(response === '1'){
						if($('#wpbu-backup-list tr').length < 2){
							$(hook).fadeOut(500, function(){
								$(this).remove();
								$('#wpbu-backup-list').append('<td id="no-backups-found"> There are no backups found. </td><td></td><td></td><td></td><td></td><td></td>');
							});
						}else{
							$(hook).fadeOut(500, function(){
								$(this).remove();
							});
						}
					}else{
						alert('There was an error removing the backup.');
					}
				});
			}
			wpbu.init();
		});
	}

	/*
	|--------------------------------------------------------------------------
	| AJAX Remove a backup
	|--------------------------------------------------------------------------
	|
	| AJAX call to remove backup
	|
	*/
	wpbu.remove_backup = function( backupID ){
		if( confirm('Are you sure you want to DELETE this backup? There is no turning back. ') ){
			var data = {
				action: 'wpbu_remove_backup',
				backupID: backupID
			};
			$.post(ajaxurl, data, function(response) {
				if(response === '1'){
					wpbu.removebacup_fromlist('#wpbu-backup-' + backupID);
				}else{
					alert('There was an error removing the backup.');
				}
			});
			wpbu.init();
		}
	};

	wpbu.init();

})(jQuery)