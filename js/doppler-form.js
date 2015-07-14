
jQuery(document).ready(function(){

	jQuery("#dplr_apikey_options label input[type='text']").focusin(function(e) {
		jQuery("#dplr_apikey_options").addClass('notempty');
		jQuery(this).addClass("notempty");
		
	});
	jQuery("#dplr_apikey_options label input[type='text']").focusout(function(e) {
		if( jQuery(this).val() == ""){
			jQuery("#dplr_apikey_options").removeClass('notempty');
			jQuery(this).removeClass("notempty");
		}
		
	});

	jQuery("#dplr_apikey_options").submit(function(event) {
		jQuery(".loader").show();
	});

	jQuery("#dplr_apikey_options.error label input[type='text']").keyup(function(event) {
		jQuery(".error").each(function(index, el) {
			jQuery(this).removeClass('error');
		}); 
	});

	jQuery(".multiple-selec").each(function(){
		var elem = jQuery(this);
		var elemID = elem.attr('id');
		if(elemID != 'widget-dplr_subscription_widget-__i__-selected_lists'){
			elem.chosen({
				width: "100%",

			});
			elem.addClass('selecAdded');
		}
	});
});

jQuery(document).on('widget-updated',  function(e, elem){
		select = elem.find("form select.multiple-selec");
		
		select.chosen({
			width: "100%"

		});
	});

jQuery(document).on('widget-added', function(e, elem){
		select = elem.find("form select.multiple-selec");
		
		select.chosen({
			width: "100%",
			
		});
	});



