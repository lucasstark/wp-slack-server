<?php


abstract class WP_Slack_Server_Command {

	public $command_name;
	public $command_token;
	public $use_callback;
	public $actions;

	public $callback_commands;

	public function __construct( $settings ) {

		$this->command_name         = '/' . $settings['command_name'];
		$this->command_token        = $settings['command_token'];
		$this->use_callback         = isset( $settings['use_callback'] ) ? (bool) $settings['use_callback'] : false;
		$this->actions = isset( $settings['actions'] ) ? $settings['actions'] : false;
	}

	public function maybe_handle_message( WP_Slack_Server_Message_Incoming $message ) {
		if ( $message->command == $this->command_name && $message->token == $this->command_token ) {

			$response = null;
			$action   = null;
			if ( ! empty( $this->actions ) ) {
				foreach ( $this->actions as $action ) {
					foreach ( $action['commands'] as $command ) {
						if ( preg_match( $command, $message->text, $matches ) ) {
							$response = call_user_func( $action['function'], $message, $matches );
						}
					}
				}
			}

			//If we have not defined any specific actions, or nothing matched our input.
			if ( empty( $response ) ) {
				$response = $this->handle_message( $message );
			}

			//If the matched action requires a delayed response, fire the socket back to ourselves to trigger async processing of the callback.
			if ( ! empty( $action ) && isset( $action['callback_function'] ) ) {
				//Post back to ourselves, triggering the callback function.
				//Using a socket so that it's a non-blocking request.
				$this->curl_post_async( get_rest_url( 1, '/slack/v1/slash-callback/' ), array(
					'token'        => $message->token,
					'team_id'      => $message->team_id,
					'team_domain'  => $message->team_domain,
					'channel_id'   => $message->channel_id,
					'channel_name' => $message->channel_name,
					'user_id'      => $message->user_id,
					'user_name'    => $message->user_name,
					'command'      => $message->command,
					'text'         => $message->text,
					'response_url' => $message->response_url
				) );
			}

			//Finally return any response that was generated.
			return $response;

		}
	}

	public function maybe_handle_callback( WP_Slack_Server_Message_Incoming $message ) {
		if ( $message->command == $this->command_name && $message->token == $this->command_token ) {
			$handled = false;
			if ( ! empty( $this->actions ) ) {
				foreach ( $this->actions as $action ) {
					foreach ( $action['commands'] as $command ) {
						if ( preg_match( $command, $message->text, $matches ) ) {
							$handled = true;
							call_user_func( $action['callback_function'], $message, $matches );
						}
					}
				}
			}

			//If we have not defined any specific actions, or nothing matched our input.
			if ( ! $handled ) {
				$this->handle_callback( $message );
			}

		}
	}

	public function handle_message( WP_Slack_Server_Message_Incoming $message ) {
		throw new Exception( 'Not Implemented' );
	}


	public function handle_callback( WP_Slack_Server_Message_Incoming $message ) {
		throw new Exception( 'Not Implemented' );
	}

	/*
	 * Open an HTTP socket back to ourselves to trigger processing of the callback.
	 */
	protected function curl_post_async( $url, $params = array() ) {
		$post_params = array();
		foreach ( $params as $key => &$val ) {
			if ( is_array( $val ) ) {
				$val = implode( ',', $val );
			}
			$post_params[] = $key . '=' . urlencode( $val );
		}
		$post_string = implode( '&', $post_params );

		$parts = parse_url( $url );

		$fp = fsockopen( $parts['host'],
			isset( $parts['port'] ) ? $parts['port'] : 80,
			$errno, $errstr, 30 );

		$out = "POST " . $parts['path'] . " HTTP/1.1\r\n";
		$out .= "Host: " . $parts['host'] . "\r\n";
		$out .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$out .= "Content-Length: " . strlen( $post_string ) . "\r\n";
		$out .= "Connection: Close\r\n\r\n";
		if ( isset( $post_string ) ) {
			$out .= $post_string;
		}

		fwrite( $fp, $out );
		fclose( $fp );
	}

}