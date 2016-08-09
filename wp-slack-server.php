<?php


/**
 * Plugin Name: WP Slack Server
 * Description: Endpoints for the WordPress REST API as a backend for Slack Slash Commands.  Requires WP Rest API
 * Author: Lucas Stark
 * Author URI: http://lucasstark.com
 * Version: 1.0.0
 * Plugin URI:
 * License: GPLv3
 */

class WP_Slack_Server {
	/**
	 * @var WP_Slack_Server
	 */
	private static $instance;

	public static function register() {
		if ( self::$instance == null ) {
			self::$instance = new WP_Slack_Server();
		}
	}

	/**
	 * @return WP_Slack_Server
	 */
	public static function instance() {
		return self::$instance;
	}

	private $_commands;

	public $assets_version = '1.0.0';
	public $db_version = '1.0.0';

	public $dir;

	/**
	 * @var WP_Slack_Server_Commands_Router
	 */
	public $router;


	public function __construct() {
		$this->dir = untrailingslashit( plugin_dir_path( __FILE__ ) );


		require $this->dir . '/inc/class-wp-slack-server-commands-router.php';
		require $this->dir . '/inc/class-wp-slack-server-commands-controller.php';
		require $this->dir . '/inc/messages/class-wp-slack-server-message-incoming.php';
		require $this->dir . '/inc/messages/class-wp-slack-server-message-attachment.php';
		require $this->dir . '/inc/messages/class-wp-slack-server-message-outgoing.php';

		//Require Built In Commands
		require $this->dir . '/inc/class-wp-slack-server-command.php';
		foreach ( glob( $this->dir . "/commands/*.php" ) as $filename ) {
			include $filename;
		}

		//Setup Internal Properties.
		$this->_commands = array();
		$this->router    = new WP_Slack_Server_Commands_Router();


		//Boot up some controllers.
		WP_Slack_Server_Commands_Controller::register();

		add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ) );
	}

	public function on_plugins_loaded() {
		do_action( 'wp_slack_server_init' );
	}

	/**
	 * @param $command string|WP_Slack_Server_Command
	 */
	public function register_command( $command ) {
		if ( is_object( $command ) ) {
			$this->_commands[ $command->command_name ] = $command;
		} elseif ( class_exists( $command ) ) {
			$c                                   = new $command;
			$this->_commands[ $c->command_name ] = $c;
		}
	}

	/**
	 * @return WP_Slack_Server_Command[]
	 */
	public function get_commands() {
		return apply_filters( 'wp_slack_server_get_commands', $this->_commands );
	}
}

/**
 * @return WP_Slack_Server
 */
function slack_server() {
	return WP_Slack_Server::instance();
}

WP_Slack_Server::register();