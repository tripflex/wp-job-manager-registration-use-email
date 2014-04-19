<?php
/**
 * Plugin Name: WP Job Manager - Register Use Email
 * Plugin URI:  https://github.com/tripflex/wp-job-manager-reg-use-email
 * Description: Use email address as username when a new user registers
 * Author:      Myles McNamara
 * Author URI:  http://smyl.es
 * Version:     1.0
 * Text Domain: job_manager_reg_use_email
 * GitHub Plugin URI: tripflex/wp-job-manager-reg-use-email
 * GitHub Branch:   master
 * @Last Modified by:   Myles McNamara
 * @Last Modified time: 2014-04-19 16:52:09
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WP_Job_Manager_Register_Use_Email {
	private static $instance;

	public static function instance() {
		if ( ! isset ( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function __construct() {
		$this->setup_actions();
	}

	private function setup_actions() {
		add_filter( 'job_manager_settings', array( $this, 'job_manager_settings' ) );
		add_filter( 'job_manager_create_account_data', array( $this, 'job_manager_change_username' ) );
	}

	public function job_manager_settings( $settings ) {

		$use_email = array(
			'name'       => 'job_manager_enable_registration_use_email',
			'std'        => '0',
			'label'      => __( 'Registration Username', 'wp-job-manager' ),
			'cb_label'   => __( 'Use email as username', 'wp-job-manager' ),
			'desc'       => __( 'Choose whether to use the email address as the username when a new user registers.', 'wp-job-manager' ),
			'type'       => 'checkbox',
			'attributes' => array()
		);

		array_splice($settings['job_submission'][1], 1, 0, array());

		$settings['job_submission'][1][1] = $use_email;

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

add_action( 'init', array( 'WP_Job_Manager_Register_Use_Email', 'instance' ) );