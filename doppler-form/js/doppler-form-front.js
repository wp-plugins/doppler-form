
jQuery(document).ready(function(){
	jQuery('form.dplr_wdg_form').on('submit', function(event){
		event.preventDefault();
		var form = jQuery(this);
		var args = {
			action: 'add_subscribers',
			args: {
				list_ids: form.find('input[name=lists]').val(),
				api_key: dplr_plugin_settings.api_key,
				email: form.find('input[name=email]').val()
			}
		}

		jQuery.ajax({
			url: dplr_plugin_settings.url+'lib/pluginRequests.php',
			type: 'POST',
			data: args,
		})
		.done(function(res) {
			jQuery("form.dplr_wdg_form").fadeOut('slow', function() {
				jQuery("div.thanksMessage").fadeIn('slow', function() {
					setTimeout(function(){
						jQuery("div.thanksMessage").fadeOut('slow', function(){
							jQuery("form.dplr_wdg_form input[type='email']").val("")
							jQuery("form.dplr_wdg_form").fadeIn('slow');
						});
					}, 3000);
					
				});
			});
		})
		.fail(function(res) {
			console.log(res);
		})
		.always(function(res) {

		});
		
	});	
}); 