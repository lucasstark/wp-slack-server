<?php


class WP_Slack_Server_Command_Builder {


	public function build_command( $post_id ) {

		$options = get_post_meta( $post_id, 'slack_command_options', true );

		$settings = array(
			'command_name'  => $options['command_name'],
			'command_token' => $options['command_token'],
			'actions'       => array()
		);

		foreach ( $options['actions'] as $action ) {


			$command_action =
				array(
					'filter'   => $action['response_function'],
				);

			if ( $action['delayed_response'] == 'yes' ) {
				$command_action['callback_filter'] = $action['response_function'] . '_delayed';
			}

			if (!empty($action['utterances'])) {
				$utterances = array_values( array_filter( explode( PHP_EOL, $action['utterances'] ) ) );
				foreach ( $utterances as $utterance ) {
					$command_action['commands'][] = '/' . $utterance . '/';
				}
			}

			$settings['actions'][] = $command_action;

		}

		return new WP_Slack_Server_Command( $settings );

	}

}