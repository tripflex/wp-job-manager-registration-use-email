/**
 * Created by Myles McNamara on 5/6/14.
 */

jQuery(function($){

	var index = 3;

	metaValue = $('#setting-job_manager_registration_use_email_custom_username_label').data('meta');
	newRow = '<tr valign="top"><th scope="row"><label for="setting-job_manager_registration_use_email_custom_username_label">Login Username Label</label></th><td><input id="setting-job_manager_registration_use_email_custom_username_label" class="regular-text" type="text" name="job_manager_registration_use_email_custom_username_label" value="Username or Email"> <p class="description">By default when Use Email As Username is enabled, it will change every instance of <code>Username</code> to <code>Username or Email</code>, if you want to use a custom label, enter it here.</p></td></tr>';


	$('#setting-job_manager_enable_registration_use_email').change(function(){
		if(this.checked){
			$('#settings-job_submission .form-table > tbody > tr').eq(index-1).before(newRow);
		} else {
			$('td > #setting-job_manager_registration_use_email_custom_username_label').parents('tr').hide();
		}
	});
});
