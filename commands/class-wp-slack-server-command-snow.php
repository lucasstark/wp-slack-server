<?php


class WP_Slack_Server_Command_Snow extends WP_Slack_Server_Command {


	public function __construct() {
		parent::__construct( array(
			'command_name'  => 'snow',
			'command_token' => 'PKxB1PfoW2YCGPDhzXim2NR2',
			'actions'       => array(
				array(
					'function'          => array( $this, 'handle_ticket_list' ),
					'callback_function' => array( $this, 'handle_ticket_list_callback' ),
					'commands'          => array(
						'/tickets/',
						'/ticket summary/',
						'/ticket list/',
						'/show my tickets/',
						'/list my tickets/'
					)
				),
				array(
					'function'          => array( $this, 'handle_ticket_details' ),
					'callback_function' => array( $this, 'handle_ticket_details_callback' ),
					'commands'          => array(
						'/ticket details (REQ\d+)/',
						'/(REQ\d+) details/'
					)
				)
			),
		) );
	}

	public function handle_message( WP_Slack_Server_Message_Incoming $message ) {
		return new WP_Slack_Server_Message_Outgoing( 'Unknown SNOW command', false );
	}


	public function handle_ticket_list( WP_Slack_Server_Message_Incoming $message, $args ) {
		return new WP_Slack_Server_Message_Outgoing( 'Looking up your tickets...', false );
	}


	public function handle_ticket_details( WP_Slack_Server_Message_Incoming $message, $args ) {
		return new WP_Slack_Server_Message_Outgoing( 'Looking up ticket details for: ' . $args[1], false );
	}


	/**
	 * Execute a delayed response back to the response_url on the slack message.
	 *
	 * @param WP_Slack_Server_Message_Incoming $slack_message
	 *
	 * @return bool
	 */
	public function handle_ticket_list_callback( WP_Slack_Server_Message_Incoming $slack_message ) {
		$message = new WP_Slack_Server_Message_Outgoing( 'Your Snow Tickets', false );

		$sd = array(
			'these are just',
			'placeholders until, Adam',
			'can get the',
			'SNOW API Hooked Up',
			'Hopefully, things will work out.',
			'Green might mean ticket has been updated recently',
			'Red might mean they havent been updated',
			'Or maybe they are past their due dates',
			'Short Description',
			'Short Description',
			'Short Description'
		);

		for ( $i = 0; $i < 10; $i ++ ) {
			$attachment = new WP_Slack_Server_Message_Attachment( 'REQ000000' . $i, $sd[ $i ], 'http://www.google.com' );
			if ( $i % 3 === 0 ) {
				$attachment->color = 'warning';
			} else {
				$attachment->color = 'good';
			}

			$message->add_attachment( $attachment );
		}

		$args = array(
			'headers' => array( 'Content-Type' => 'application/json' ),
			'body'    => json_encode( $message )
		);

		$result = wp_remote_post( $slack_message->response_url, $args );

		return true;
	}


	/**
	 * Execute a delayed response back to the response_url on the slack message.
	 *
	 * @param WP_Slack_Server_Message_Incoming $slack_message
	 *
	 * @return bool
	 */
	public function handle_ticket_details_callback( WP_Slack_Server_Message_Incoming $slack_message, $args ) {
		$message = new WP_Slack_Server_Message_Outgoing( 'Ticket Details for ' . $args[1], false );
		$args    = array(
			'headers' => array( 'Content-Type' => 'application/json' ),
			'body'    => json_encode( $message )
		);
		$result  = wp_remote_post( $slack_message->response_url, $args );

		return true;
	}


}


add_action( 'wp_slack_server_init', function () {
	slack_server()->register_command( 'WP_Slack_Server_Command_Snow' );
} );