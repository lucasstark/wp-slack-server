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
			self::$instance->init();
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

	/**
	 * @var string The base file system path to the plugin.
	 */
	public $dir;

	/**
	 * @var string The base url to the plugin.
	 */
	public $url;

	/**
	 * @var WP_Slack_Server_Commands_Router
	 */
	public $router;

	/**
	 * @var WP_Slack_Server_Command_Builder
	 */
	public $command_builder;


	public function __construct() {
		$this->dir = untrailingslashit( plugin_dir_path( __FILE__ ) );
		$this->url = untrailingslashit( plugin_dir_url( __FILE__ ) );

		require $this->dir . '/inc/class-wp-slack-server-commands-router.php';
		require $this->dir . '/inc/class-wp-slack-server-commands-controller.php';
		require $this->dir . '/inc/messages/class-wp-slack-server-message-incoming.php';
		require $this->dir . '/inc/messages/class-wp-slack-server-message-attachment.php';
		require $this->dir . '/inc/messages/class-wp-slack-server-message-outgoing.php';

		require $this->dir . '/inc/post-types/command/class-wp-slack-server-post-type-command.php';

		//Require Built In Commands
		require $this->dir . '/inc/class-wp-slack-server-command-builder.php';
		require $this->dir . '/inc/class-wp-slack-server-command.php';
		foreach ( glob( $this->dir . "/commands/*.php" ) as $filename ) {
			include $filename;
		}

		add_action( 'rest_api_init', array( $this, 'on_rest_api_init' ) );
	}

	private function init() {
		//Setup Internal Properties.
		$this->_commands       = array();
		$this->router          = new WP_Slack_Server_Commands_Router();
		$this->command_builder = new WP_Slack_Server_Command_Builder();

		//Boot up some controllers.
		WP_Slack_Server_Commands_Controller::register();
		WP_Slack_Server_Post_Type_Command::register();
	}

	public function on_rest_api_init() {
		$posts = get_posts(
			array(
				'post_type'   => 'slack_command',
				'post_status' => 'publish',
				'nopaging'    => true,
				'fields'      => 'ids'
			)
		);

		if ( $posts ) {
			foreach ( $posts as $post ) {
				$command = $this->command_builder->build_command( $post );
				$this->register_command( $command );
			}
		}

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


/* Example Filters for Slack Commands built using the interface in the dashboard.
add_filter('respond_with_joke', 'my_respond_with_a_joke', 10, 3);
function my_respond_with_a_joke($response, $message, $args){
	$response = 'Here is your joke';
	return $response;
}


add_action('respond_with_joke_delayed', 'my_respond_with_a_joke_delayed', 10, 2);
function my_respond_with_a_joke_delayed($message, $matches){
	$fp = fopen( untrailingslashit( plugin_dir_path(__FILE__) ) .  '/jokes.txt', 'a');
	fwrite($fp, 'Another Joke Here');
	fwrite($fp, PHP_EOL);
	fclose($fp);
}
*/
