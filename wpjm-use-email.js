/**
 * Created by Myles McNamara on 5/6/14.
 */

jQuery(function($){

	function isEmailEnabled(){
		if($('#setting-job_manager_enable_registration_use_email').prop("checked")){
			$('td > #setting-job_manager_registration_use_email_custom_username_label').parents('tr').show();
		} else {
			$('td > #setting-job_manager_registration_use_email_custom_username_label').parents('tr').hide();
		}
	}

	$('#setting-job_manager_enable_registration_use_email').change(function(){
		isEmailEnabled();
	});

	isEmailEnabled();
});