<?php


class WP_Slack_Server_Commands_Controller {
	private static $instance;

	public static function register() {
		if ( self::$instance == null ) {
			self::$instance = new WP_Slack_Server_Commands_Controller();
		}
	}

	public function __construct() {

		add_action( 'rest_api_init', function () {

			register_rest_route( 'slack/v1', '/slash', array(
				'methods'  => 'GET',
				'callback' => array( $this, 'handle_command' ),
			) );

			register_rest_route( 'slack/v1', '/slash-callback', array(
				'methods'  => 'POST',
				'callback' => array( $this, 'handle_callback' ),
			) );

		} );

	}

	public function handle_command( WP_REST_Request $request ) {
		$slack_message = new WP_Slack_Server_Message_Incoming( $request->get_query_params() );
		return slack_server()->router->route( $slack_message );
	}

	public function handle_callback( WP_REST_Request $request ) {
		ignore_user_abort(true);
		$slack_message = new WP_Slack_Server_Message_Incoming( $request->get_params() );
		$slack_message->is_callback = true;
		$processed = slack_server()->router->route_callback( $slack_message );
		return $processed ? 'Callback Processed' : 'Callback Not Processed';
	}

}