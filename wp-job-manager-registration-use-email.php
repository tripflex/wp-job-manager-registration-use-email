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
 * GitHub Branch:   master
 * @Last Modified by:   Myles McNamara
 * @Last Modified time: 2014-05-07 14:10:52
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Set the version of this plugin
if ( ! defined( 'JOB_MANAGER_REGISTRATION_USE_EMAIL' ) ) {
	define( 'JOB_MANAGER_REGISTRATION_USE_EMAIL', '1.2.0' );
} // end if

class WP_Job_Manager_Registration_Use_Email {
	/**
	 * @var      string
	 */
	public static $plugin_slug = 'wp-job-manager-registration-use-email';
	private static $instance;

	public function __construct() {
		add_action( 'admin_notices', array( $this, 'plugin_activate' ) );
		add_filter( 'job_manager_settings', array(
			$this,
			'job_manager_settings'
		) );
		add_filter( 'job_manager_create_account_data', array(
			$this,
			'job_manager_change_username'
		) );
		add_filter( 'gettext', array( $this, 'change_username_label' ) );
		add_filter( 'plugin_row_meta', array(
			$this,
			'add_plugin_row_meta'
		), 10, 4 );

	}

	public static function instance() {
		if ( ! isset ( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public static function plugin_activate() {
		if ( JOB_MANAGER_REGISTRATION_USE_EMAIL != get_option( 'Job_Manager_Registration_Use_Email' ) ) {
			update_option( 'Job_Manager_Registration_Use_Email', JOB_MANAGER_REGISTRATION_USE_EMAIL );
			$html = '<div class="updated">';
			$html .= '<p>';
			$html .= __( '<b>Hooray!</b> Using email as username is ready to go, but you have to enable it <a href="edit.php?post_type=job_listing&page=job-manager-settings#settings-job_submission">on this page</a> under "Job Submission".', self::$plugin_slug );
			$html .= '</p>';
			$html .= '</div>';

			echo $html;
		}
	}

	public static function plugin_deactivate() {
		delete_option( 'Job_Manager_Registration_Use_Email' );
	}

	public function change_username_label( $text ) {
		if ( job_manager_enable_registration_use_email() ) {
			$custom_label = get_option( 'job_manager_registration_use_email_custom_username_label' );
			if ( ! $custom_label ) {
				$custom_label = "Username or Email";
			}
			switch ( $text ) {
				case 'Username' :
					$text = __( $custom_label, self::$plugin_slug );
					break;
			}
		}

		return $text;
	}

	public function add_plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status ) {
		if ( self::$plugin_slug . '/' . self::$plugin_slug . '.php' == $plugin_file ) {
			$plugin_meta[] = sprintf( '<a href="%s" target="_blank">%s</a>', __( 'http://github.com/tripflex/' . self::$plugin_slug, self::$plugin_slug ), __( 'GitHub', self::$plugin_slug ) );
			$plugin_meta[] = sprintf( '<a href="%s" target="_blank">%s</a>', __( 'http://wordpress.org/plugins/' . self::$plugin_slug, self::$plugin_slug ), __( 'Wordpress', self::$plugin_slug ) );
			$plugin_meta[] = sprintf( '<a href="%s" target="_blank">%s</a>', __( 'https://www.transifex.com/projects/p/' . self::$plugin_slug . '/resource/' . self::$plugin_slug . '/', self::$plugin_slug ), __( 'Translate', self::$plugin_slug ) );
		}

		return $plugin_meta;
	}

	public function job_manager_settings( $settings ) {

		wp_enqueue_script( 'job_manager_registration_use_email', plugins_url( '/wpjm-use-email.min.js', __FILE__ ), array( 'jquery' ) );

		$use_email             = array(
			'name'       => 'job_manager_enable_registration_use_email',
			'std'        => '0',
			'label'      => __( 'Registration Username', self::$plugin_slug ),
			'cb_label'   => __( 'Use email as username', self::$plugin_slug ),
			'desc'       => __( 'Choose whether to use the email address as the username when a new user registers.  Will also change <code>Username</code> on login forms to <code>Username or Email</code>', self::$plugin_slug ),
			'type'       => 'checkbox',
			'attributes' => array()
		);
		$custom_username_label = array(
			'name'       => 'job_manager_registration_use_email_custom_username_label',
			'std'        => 'Username or Email',
			'label'      => __( 'Login Username Label', self::$plugin_slug ),
			'desc'       => __( 'By default when Use Email As Username is enabled, it will change every instance of <code>Username</code> to <code>Username or Email</code>, if you want to use a custom label, enter it here.', self::$plugin_slug ),
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

	public function job_manager_change_username( $fields ) {
		if ( job_manager_enable_registration_use_email() ) {
//			Store username in tmp variable so we can change it and still have the original value
			$username_tmp           = $fields['user_login'];
			$fields['user_login']   = $fields['user_email'];
			$fields['display_name'] = $username_tmp;
			$fields['nickname']     = $username_tmp;
		}

		return $fields;
	}

}

function job_manager_enable_registration_use_email() {
	return apply_filters( 'job_manager_enable_registration_use_email', get_option( 'job_manager_enable_registration_use_email' ) == 1 ? true : false );
}

//register_activation_hook( __FILE__, array( 'WP_Job_Manager_Registration_Use_Email', 'plugin_activate' ) );
register_deactivation_hook( __FILE__, array(
	'WP_Job_Manager_Registration_Use_Email',
	'plugin_deactivate'
) );

add_action( 'init', array(
	'WP_Job_Manager_Registration_Use_Email',
	'instance'
) );
