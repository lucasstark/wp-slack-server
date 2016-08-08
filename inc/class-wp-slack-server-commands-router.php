<?php


class WP_Slack_Server_Commands_Router {

	public function __construct() {
	}

	public function route( WP_Slack_Server_Message_Incoming $message ) {

		$commands = slack_server()->get_commands();

		$handled = false;
		foreach ( $commands as $command ) {
			$handled = $command->maybe_handle_message( $message );
			if ( $handled ) {
				return $handled;
			}
		}

		if ( empty( $handled ) ) {
			return new WP_Error( '404', __( 'Command Not Found: ' . json_encode($message), 'wp-slack-server' ) );
		}

	}

	public function route_callback(WP_Slack_Server_Message_Incoming $message){
		$commands = slack_server()->get_commands();
		$handled = false;
		foreach ( $commands as $command ) {
			$handled = $command->maybe_handle_callback( $message );
			if ( $handled ) {
				return $handled;
			}
		}

		if ( empty( $handled ) ) {
			return new WP_Error( '404', __( 'Command Not Found: ' . json_encode($message), 'wp-slack-server' ) );
		}
	}

}