<?php


class WP_Slack_Server_Command_Example extends WP_Slack_Server_Command {

	public function __construct() {

		//The actions => commands are regular expressions.  Use any valid regular expression to match to the incomming message text.
		parent::__construct( array(
				'command_name'  => 'example',
				'command_token' => 'PKxB1PfoW2YCGPDhzXim2NR2',
				'actions'       => array(
					array(
						'function' => array( $this, 'handle_example_one' ),
						'commands' => array(
							'/example/',
							'/show example/',
							'/demo/',
							'/examples/',
						)
					),
					array(
						'function'          => array( $this, 'handle_example_two' ),
						'callback_function' => array( $this, 'handle_example_two_callback' ),
						'commands'          => array(
							'/callback example/',
							'/callback examples/',
							'/show callback examples/',
							'/respond (\d+) times/'
						)
					)
				),
			)
		);
	}

	public function handle_example_one( WP_Slack_Server_Message_Incoming $message, $args = '' ) {
		return new WP_Slack_Server_Message_Outgoing( 'Welcome to the examples' );
	}

	public function handle_example_two( WP_Slack_Server_Message_Incoming $message, $args = '' ) {
		return new WP_Slack_Server_Message_Outgoing( 'Since we defined a callback you will get another message in a sec' );
	}

	public function handle_example_two_callback( WP_Slack_Server_Message_Incoming $message, $args = '' ) {
		sleep(2);

		if ( count( $args ) > 1 ) {
			for ( $i = 0; $i < $args[1]; $i++ ) {
				//For some reason this will only send one message back to slack, not sure if they throttle it or what.
				//Figure out later..
				$response_message = new WP_Slack_Server_Message_Outgoing( 'Delayed Response ' . $i . ' of ' . $args[1] );
				$args             = array(
					'headers' => array( 'Content-Type' => 'application/json' ),
					'body'    => json_encode( $response_message )
				);

				$result = wp_remote_post( $message->response_url, $args );
				sleep(4);
			}
		} else {
			$response_message = new WP_Slack_Server_Message_Outgoing( 'Delayed Response from Callback' );
			$args             = array(
				'headers' => array( 'Content-Type' => 'application/json' ),
				'body'    => json_encode( $response_message )
			);

			$result = wp_remote_post( $message->response_url, $args );
		}

		return $result;
	}

}


add_action( 'wp_slack_server_init', function () {
	slack_server()->register_command( 'WP_Slack_Server_Command_Example' );
} );