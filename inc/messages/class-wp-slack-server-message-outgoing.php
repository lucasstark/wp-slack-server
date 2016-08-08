<?php


class WP_Slack_Server_Message_Outgoing {

	public $text;
	public $response_type;
	public $attachments;

	public function __construct($text, $in_channel = true, $original_message = null) {
		$this->text = $text;
		$this->response_type = $in_channel ? 'in_channel' : 'ephemeral';
	}

	public function add_attachment(WP_Slack_Server_Message_Attachment $attachment){
		if ($this->attachments == null) {
			$this->attachments = array();
		}

		$this->attachments[] = $attachment;
	}
}