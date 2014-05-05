<?php
/**
 * Plugin Name: WP Job Manager - Registration Use Email
 * Plugin URI:  https://github.com/tripflex/wp-job-manager-registration-use-email
 * Description: Use email address as username when a new user registers
 * Author:      Myles McNamara
 * Contributors: Chris McCoy
 * Author URI:  http://smyl.es
 * Version:     1.1.2
 * Text Domain: job_manager_registration_use_email
 * GitHub Plugin URI: tripflex/wp-job-manager-registration-use-email
 * GitHub Branch:   master
 * @Last Modified by:   Myles McNamara
 * @Last Modified time: 2014-05-05 19:15:29
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


// Set the version of this plugin
if( ! defined( 'JOB_MANAGER_REGISTRATION_USE_EMAIL' ) ) {
	define( 'JOB_MANAGER_REGISTRATION_USE_EMAIL', '1.1.1' );
} // end if

class WP_Job_Manager_Registration_Use_Email {
	private static $instance;

	/**
	 * @var      string
	 */
	protected $plugin_slug = 'wp-job-manager-registration-use-email';

	public static function instance() {
		if ( ! isset ( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function __construct() {
		add_action( 'admin_notices', array( $this, 'plugin_activate' ) ) ;
		add_filter( 'job_manager_settings', array( $this, 'job_manager_settings' ) );
		add_filter( 'job_manager_create_account_data', array( $this, 'job_manager_change_username' ) );
		add_filter( 'gettext', array( $this, 'change_username_label' ));
		add_filter( 'plugin_row_meta', array( $this, 'add_plugin_row_meta'), 10, 4 );
	}

	public static function plugin_activate() {
		if( JOB_MANAGER_REGISTRATION_USE_EMAIL != get_option( 'Job_Manager_Registration_Use_Email' ) ) {
			update_option('Job_Manager_Registration_Use_Email', JOB_MANAGER_REGISTRATION_USE_EMAIL );
			$html = '<div class="updated">';
			$html .= '<p>';
			$html .= __( '<b>Hooray!</b> Using email as username is ready to go, but you have to enable it <a href="edit.php?post_type=job_listing&page=job-manager-settings#settings-job_submission">on this page</a> under "Job Submission".', 'job_manager_reg_use_email' );
			$html .= '</p>';
			$html .= '</div>';

			echo $html;
		}
	}

	public static function plugin_deactivate(){
		delete_option( 'Job_Manager_Registration_Use_Email' );
	}

	public function change_username_label($text){
		if(job_manager_enable_registration_use_email()) {
			switch ( $text ) {
				case 'Username' :
					$text = __( 'Username or Email', 'textdomain' );
					break;
			}
		}
		return $text;
	}

	public function add_plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status ) {
		if ( $this->plugin_slug . '/' . $this->plugin_slug . '.php' == $plugin_file ) {
			$plugin_meta[] = sprintf( '<a href="%s">%s</a>', __( 'http://github.com/tripflex/' . $this->plugin_slug, $this->plugin_slug), __( 'GitHub', $this->plugin_slug ) );
			$plugin_meta[] = sprintf( '<a href="%s">%s</a>', __( 'https://www.transifex.com/projects/p/' . $this->plugin_slug . '/resource/' . $this->plugin_slug  . '/', $this->plugin_slug ), __( 'Translate', $this->plugin_slug ) );
		}
		return $plugin_meta;
	}

	public function job_manager_settings( $settings ) {

		$use_email = array(
			'name'       => 'job_manager_enable_registration_use_email',
			'std'        => '0',
			'label'      => __( 'Registration Username', 'wp-job-manager' ),
			'cb_label'   => __( 'Use email as username', 'wp-job-manager' ),
			'desc'       => __( 'Choose whether to use the email address as the username when a new user registers.  Will also change <code>Username</code> on login forms to <code>Username or Email</code>', 'wp-job-manager' ),
			'type'       => 'checkbox',
			'attributes' => array()
		);
		// Get all settings before index 1 in array
		$settings_before = array_slice($settings['job_submission'][1], 0, 1);
		// Get all settings after index 1 in array
		$settings_after = array_slice($settings['job_submission'][1], 1);
		// Add new settings
		$settings_before[] = $use_email;
		// Merge everything back together
		$settings['job_submission'][1] = array_merge($settings_before, $settings_after);

		return $settings;
	}

	public function job_manager_change_username ( $fields ) {
		if(job_manager_enable_registration_use_email()){
//			Store username in tmp variable so we can change it and still have the original value
			$username_tmp = $fields['user_login'];
			$fields['user_login'] = $fields['user_email'];
			$fields['display_name'] = $username_tmp;
			$fields['nickname'] = $username_tmp;
		}

		return $fields;
	}

}

function job_manager_enable_registration_use_email() {
	return apply_filters( 'job_manager_enable_registration_use_email', get_option( 'job_manager_enable_registration_use_email' ) == 1 ? true : false );
}

//register_activation_hook( __FILE__, array( 'WP_Job_Manager_Registration_Use_Email', 'plugin_activate' ) );
register_deactivation_hook( __FILE__, array( 'WP_Job_Manager_Registration_Use_Email', 'plugin_deactivate' ) );

add_action( 'init', array( 'WP_Job_Manager_Registration_Use_Email', 'instance' ) );
