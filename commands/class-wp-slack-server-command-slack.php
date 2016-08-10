<?php

/*
 * This command will re-route anything with the slash command /slack to it's own command.
 * Developed to help with teams using the free version of slack who have reached their integration limit.
 * Just type /slack <command-name> <command-argument> and this command will re-route from /slack to /<command-name>
 */
class WP_Slack_Server_Command_Slack extends WP_Slack_Server_Command {


	public function __construct() {
		parent::__construct( array(
			'command_name'  => 'slack',
			'command_token' => WP_SLACK_SERVER_TOKEN,
		) );
	}

	/**
	 * Generic Handler - Reroutes commands to the actual command.
	 * @param WP_Slack_Server_Message_Incoming $message
	 *
	 * @return bool|mixed|null|void|WP_Error|WP_Slack_Server_Message_Outgoing
	 */
	public function handle_message( WP_Slack_Server_Message_Incoming $message ) {

		//We are using a single slack command to route different message.
		//Let's get the text command, the first word in the text.
		if ( strpos( $message->text, ' ' ) > 0 ) {
			$text_command = substr( $message->text, 0, strpos( $message->text, ' ' ) );
		} else {
			$text_command = $message->text;
		}

		$response = null;
		if ( ! empty( $text_command ) ) {
			$message->command = '/' . trim( $text_command, ' ' );
			//Reroute the request.
			$response = slack_server()->router->route( $message );
		} else {
			$response = new WP_Slack_Server_Message_Outgoing( 'Unknown Text Command: ' . $message->text, false );
		}

		return $response;
	}

	public function handle_callback( WP_Slack_Server_Message_Incoming $message ) {

		//We are using a single slack command to route different message.
		//Let's get the text command, the first word in the text.
		if ( strpos( $message->text, ' ' ) > 0 ) {
			$text_command = substr( $message->text, 0, strpos( $message->text, ' ' ) );
		} else {
			$text_command = $message->text;
		}

		$response = null;
		if ( ! empty( $text_command ) ) {
			$message->command = '/' . trim( $text_command, ' ' );
			//Reroute the request.
			$response = slack_server()->router->route_callback( $message );
		} else {
			$response = new WP_Slack_Server_Message_Outgoing( 'Unknown Text Command: ' . $message->text, false );
		}

		return $response;
	}

}


add_action( 'wp_slack_server_init', function () {
	slack_server()->register_command( 'WP_Slack_Server_Command_Slack' );
} );