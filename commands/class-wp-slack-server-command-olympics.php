<?php


class WP_Slack_Server_Command_Olympics extends WP_Slack_Server_Command {

	public function __construct() {

		//The actions => commands are regular expressions.  Use any valid regular expression to match to the incomming message text.
		parent::__construct( array(
				'command_name'  => 'olympics',
				'command_token' => WP_SLACK_SERVER_TOKEN,
				'actions'       => array(
					array(
						'function' => array( $this, 'handle_show_medals' ),
						'commands' => array(
							'/medals for (.*)/',
						)
					),
				),
			)
		);
	}

	public function handle_show_medals( WP_Slack_Server_Message_Incoming $message, $args = '' ) {
		$data = wp_remote_get( 'http://olympics.atelerix.co/medals' );
		if ( ! is_wp_error( $data ) ) {
			$json = wp_remote_retrieve_body( $data );
			if ( ! is_wp_error( $json ) ) {
				$temp = json_decode( $json );
				$medals = wp_list_filter($temp->medals , array( 'name' => $args[1] ) );

				if ( $medals ) {
					$message = new WP_Slack_Server_Message_Outgoing( 'Current Medals for ' . $args[1], true );
					$medal = array_pop($medals);
					$attachment = new WP_Slack_Server_Message_Attachment( '' );
					$attachment->add_field( 'Gold', $medal->gold );
					$attachment->add_field( 'Silver', $medal->silver );
					$attachment->add_field( 'Bronze', $medal->bronze );

					$message->add_attachment( $attachment );

					return $message;
				}
			}
		}

		return new WP_Slack_Server_Message_Outgoing( 'There was a problem getting the data.' );
	}
}

add_action( 'wp_slack_server_init', function () {
	slack_server()->register_command( 'WP_Slack_Server_Command_Olympics' );
} );