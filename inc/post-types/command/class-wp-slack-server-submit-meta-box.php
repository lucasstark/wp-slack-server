<?php
/**
 * Replacement for builtin submitdiv meta box
 * for our custom post type.
 */
class WP_Slack_Server_Submit_Div {

	/**
	 * @var WP_Slack_Server_Submit_Div
	 */
	private $post_type;

	public function __construct( WP_Slack_Server_Post_Type_Command $post_type ) {
		$this->post_type = $post_type;

		add_action( 'add_meta_boxes', array( $this, 'register_meta_box' ) );
	}

	/**
	 * Register submit meta box.
	 *
	 * @param
	 */
	public function register_meta_box( $post_type ) {
		if ( $this->post_type->name === $post_type ) {
			add_meta_box( 'slack_submitdiv', __( 'Save Setting', 'slack' ), array( $this, 'slack_submitdiv' ), null, 'side', 'core' );
		}
	}

	/**
	 * Display post submit form fields.
	 *
	 * @param object $post
	 */
	public function slack_submitdiv( $post ) {
		require_once untrailingslashit(plugin_dir_path(__FILE__)) . '/views/submit-meta-box.php';
	}
}
