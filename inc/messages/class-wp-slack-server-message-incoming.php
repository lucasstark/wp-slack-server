<?php


class WP_Slack_Server_Message_Incoming {

	public $token;
	public $team_id;
	public $team_domain;
	public $channel_id;
	public $channel_name;
	public $user_id;
	public $user_name;

	public $command;
	public $text;
	public $response_url;

	public $is_callback;

	public function __construct( array $data ) {

		$this->token        = isset( $data['token'] ) ? $data['token'] : false;
		$this->team_id      = isset( $data['team_id'] ) ? $data['team_id'] : false;
		$this->team_domain  = isset( $data['team_domain'] ) ? $data['team_domain'] : false;
		$this->channel_id   = isset( $data['channel_id'] ) ? $data['channel_id'] : false;
		$this->channel_name = isset( $data['channel_name'] ) ? $data['channel_name'] : false;
		$this->user_id      = isset( $data['user_id'] ) ? $data['user_id'] : false;
		$this->user_name    = isset( $data['user_name'] ) ? $data['user_name'] : false;
		$this->command      = isset( $data['command'] ) ? $data['command'] : false;

		$this->text         = isset( $data['text'] ) ? $data['text'] : false;
		$this->response_url = isset( $data['response_url'] ) ? $data['response_url'] : false;

		$this->is_callback = isset($data['is_callback']) ? $data['is_callback'] == 'true' : false;
	}


}