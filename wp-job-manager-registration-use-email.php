<?php
/**
 * Plugin Name: WP Job Manager - Registration Use Email
 * Plugin URI:  https://github.com/tripflex/wp-job-manager-registration-use-email
 * Description: Use email address as username when a new user registers
 * Author:      Myles McNamara
 * Contributors: Chris McCoy
 * Author URI:  http://smyl.es
 * Version:     1.2.0
 * Text Domain: job_manager_registration_use_email
 * GitHub Plugin URI: tripflex/wp-job-manager-registration-use-email
 * GitHub Branch: master
 * @Last Modified by:   Myles McNamara
 * @Last Modified time: 2014-05-07 14:10:52
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Set the version of this plugin
if ( ! defined( 'JOB_MANAGER_REGISTRATION_USE_EMAIL' ) ) define( 'JOB_MANAGER_REGISTRATION_USE_EMAIL', '1.2.0' );

class WP_Job_Manager_Registration_Use_Email {
	/**
	 * @var      string
	 */
	public static $plugin_slug = 'wp-job-manager-registration-use-email';
	private static $instance;

	public function __construct() {
		add_action( 'admin_notices', array( $this, 'activate' ) );
		add_filter( 'job_manager_settings', array( $this, 'settings' ) );
		add_filter( 'job_manager_create_account_data', array( $this,  'change_username') );
		add_filter( 'register_form_fields', array($this, 'register_form_fields'), 9999 );
		add_filter( 'gettext', array( $this, 'change_username_label' ) );
		add_filter( 'plugin_row_meta', array( $this, 'add_plugin_row_meta'), 10, 4 );
	}

	/**
	 * Change Username Label
	 *
	 * Using native WordPress gettext translation handling, this will replace Username labels
	 * throughout the entire site.
	 *
	 *
	 * @since @@version
	 *
	 * @param $text
	 *
	 * @return string|void
	 */
	public function change_username_label( $text ) {
		if ( job_manager_enable_registration_use_email() ) {
			$custom_label = get_option( 'job_manager_registration_use_email_custom_username_label' );
			if ( ! $custom_label ) {
				$custom_label = "Username or Email";
			}
			switch ( $text ) {
				case 'Username' :
					$text = __( $custom_label, 'wp-job-manager-registration-use-email' );
					break;
			}
		}

		return $text;
	}

	/**
	 * Plugin Page Row Meta Links
	 *
	 *
	 * @since @@version
	 *
	 * @param $plugin_meta
	 * @param $plugin_file
	 * @param $plugin_data
	 * @param $status
	 *
	 * @return array
	 */
	public function add_plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status ) {
		if ( self::$plugin_slug . '/' . self::$plugin_slug . '.php' == $plugin_file ) {
			$plugin_meta[] = sprintf( '<a href="%s" target="_blank">%s</a>', __( 'http://github.com/tripflex/' . self::$plugin_slug, 'wp-job-manager-registration-use-email' ), __( 'GitHub', 'wp-job-manager-registration-use-email' ) );
			$plugin_meta[] = sprintf( '<a href="%s" target="_blank">%s</a>', __( 'http://wordpress.org/plugins/' . self::$plugin_slug, 'wp-job-manager-registration-use-email' ), __( 'Wordpress', 'wp-job-manager-registration-use-email' ) );
			$plugin_meta[] = sprintf( '<a href="%s" target="_blank">%s</a>', __( 'https://www.transifex.com/projects/p/' . self::$plugin_slug . '/resource/' . self::$plugin_slug . '/', 'wp-job-manager-registration-use-email' ), __( 'Translate', 'wp-job-manager-registration-use-email' ) );
		}

		return $plugin_meta;
	}

	/**
	 * Insert Settings into core WP Job Manager
	 *
	 *
	 * @since @@version
	 *
	 * @param $settings
	 *
	 * @return mixed
	 */
	public function settings( $settings ) {

		wp_enqueue_script( 'job_manager_registration_use_email', plugins_url( '/wpjm-use-email.min.js', __FILE__ ), array( 'jquery' ) );

		$use_email             = array(
			'name'       => 'job_manager_enable_registration_use_email',
			'std'        => '0',
			'label'      => __( 'Registration Username', 'wp-job-manager-registration-use-email' ),
			'cb_label'   => __( 'Use email as username', 'wp-job-manager-registration-use-email' ),
			'desc'       => __( 'Choose whether to use the email address as the username when a new user registers.  Will also change <code>Username</code> on login forms to <code>Username or Email</code>', 'wp-job-manager-registration-use-email' ),
			'type'       => 'checkbox',
			'attributes' => array()
		);
		$custom_username_label = array(
			'name'       => 'job_manager_registration_use_email_custom_username_label',
			'std'        => 'Username or Email',
			'label'      => __( 'Login Username Label', 'wp-job-manager-registration-use-email' ),
			'desc'       => __( 'By default when Use Email As Username is enabled, it will change every instance of <code>Username</code> to <code>Username or Email</code>, if you want to use a custom label, enter it here.', 'wp-job-manager-registration-use-email' ),
			'type'       => 'input',
			'attributes' => array(
				'class' => 'job_manager_registration_use_email_custom_username_label'
			)
		);

		// Get all settings before index 1 in array
		$settings_before = array_slice( $settings['job_submission'][1], 0, 1 );

		// Get all settings after index 1 in array
		$settings_after = array_slice( $settings['job_submission'][1], 1 );

		// Add new setting option to enable using email
		array_push( $settings_before, $use_email );

		array_push( $settings_before, $custom_username_label );

		// Merge everything back together
		$settings['job_submission'][1] = array_merge( $settings_before, $settings_after );

		return $settings;
	}

	/**
	 * Change Username Value
	 *
	 * This method is ran when the filter for creating a new account is run from the core
	 * WP Job Manager plugin.  This method sets the username value to the email address.
	 *
	 *
	 * @since @@version
	 *
	 * @param $fields
	 *
	 * @return mixed
	 */
	public function change_username( $fields ) {
		if ( job_manager_enable_registration_use_email() ) {
//			Store username in tmp variable so we can change it and still have the original value
			$username_tmp           = $fields['user_login'];
			$fields['user_login']   = $fields['user_email'];
			$fields['display_name'] = $username_tmp;
			$fields['nickname']     = $username_tmp;
		}

		return $fields;
	}

	/**
	 * Remove Username Field from Register Form
	 *
	 * If use email as username is enabled, this will remove the username field from any register forms
	 * that use the register_form_fields filter with the username field key set as 'nicename'
	 *
	 *
	 * @since @@version
	 *
	 * @param $fields
	 *
	 * @return mixed
	 */
	public function register_form_fields( $fields ) {
		// Return fields as they were passed if use email is not enabled
		if( ! job_manager_enable_registration_use_email() ) return $fields;
		if( ! isset( $fields['creds'] ) || ! isset( $fields['creds']['nicename'] ) ) return $fields;

		// Set required to false to prevent WP_Error on field being empty
		$fields['creds']['nicename']['required'] = FALSE;

		if( ! $this->is_register_POST() || $this->has_validation_errors( $fields ) ) {
			unset($fields['creds']['nicename']);
		}

		return $fields;
	}

	/**
	 * Check for Validation Errors in $_POST
	 *
	 *
	 * @since @@version
	 *
	 * @param $field_groups
	 *
	 * @return bool
	 */
	function has_validation_errors( $field_groups ){

		foreach( $field_groups as $group_key => $fields ) {
			foreach( $fields as $key => $field ) {
				// Handle role field exceptions as nothing selected has a value of "none"
				if( $key == 'role' && $field['required'] && isset($_POST['role']) && $_POST['role'] == 'none' ) return true;
				if( $field['required'] && ( ! isset($_POST) || ! isset($_POST[ $key ]) || empty($_POST[ $key ]) ) ) return true;
			}
		}

		return false;
	}

	/**
	 * Check if current $_POST is for register form
	 *
	 *
	 * @since @@version
	 *
	 * @return bool
	 */
	function is_register_POST(){

		if( ! isset( $_POST ) ) return false;
		if( isset( $_POST['job_manager_form'] ) && $_POST['job_manager_form'] == 'register' ) return true;
		if( isset( $_POST['submit_register'] ) && $_POST['submit_register'] == __( 'Register' ) ) return true;

		return false;
	}

	/**
	 * Plugin Activation
	 *
	 *
	 * @since @@version
	 *
	 */
	public static function activate() {

		if( JOB_MANAGER_REGISTRATION_USE_EMAIL != get_option( 'Job_Manager_Registration_Use_Email' ) ) {
			update_option( 'Job_Manager_Registration_Use_Email', JOB_MANAGER_REGISTRATION_USE_EMAIL );
			$html = '<div class="updated">';
			$html .= '<p>';
			$html .= __( '<b>Hooray!</b> Using email as username is ready to go, but you have to enable it <a href="edit.php?post_type=job_listing&page=job-manager-settings#settings-job_submission">on this page</a> under "Job Submission".', 'wp-job-manager-registration-use-email' );
			$html .= '</p>';
			$html .= '</div>';

			echo $html;
		}
	}

	/**
	 * Plugin Deactivation
	 *
	 *
	 * @since @@version
	 *
	 */
	public static function plugin_deactivate() { delete_option( 'Job_Manager_Registration_Use_Email' ); }

	/**
	 * Singleton Instance
	 *
	 *
	 * @since @@version
	 *
	 * @return \WP_Job_Manager_Registration_Use_Email
	 */
	public static function instance() {
		if( ! isset (self::$instance) ) self::$instance = new self;
		return self::$instance;
	}
}

/**
 * Check if Registration Use Email is Enabled
 *
 *
 * @since @@version
 *
 * @return mixed|void
 */
function job_manager_enable_registration_use_email() {
	return apply_filters( 'job_manager_enable_registration_use_email', get_option( 'job_manager_enable_registration_use_email' ) == 1 ? true : false );
}

//register_activation_hook( __FILE__, array( 'WP_Job_Manager_Registration_Use_Email', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'WP_Job_Manager_Registration_Use_Email', 'plugin_deactivate' ) );
add_action( 'init', array( 'WP_Job_Manager_Registration_Use_Email', 'instance' ) );
