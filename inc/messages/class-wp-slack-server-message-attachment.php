<?php


class WP_Slack_Server_Message_Attachment {
	public $fallback;
	public $color;
	public $pretext;
	public $author_name;
	public $author_link;
	public $title;
	public $title_link;
	public $text;
	public $image_url;
	public $thumb_url;
	public $footer;
	public $footer_icon;
	public $ts;
	public $fields;
	public function __construct($title, $text = '', $title_link = '') {
		$this->title = $title;
		$this->text = $text;
		$this->title_link = $title_link;
	}

	public function add_field($title, $value) {
		if (!is_array($this->fields)){
			$this->fields = array();
		}
		$field = new stdClass();
		$field->title = $title;
		$field->value = $value;
		$this->fields[] = $field;
	}


}