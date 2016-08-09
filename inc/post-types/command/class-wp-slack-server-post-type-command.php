<?php

/**
 * Custom post type where each post stores Slack command settings.
 * Credits for the structure of this class, http://gedex.web.id/wp-slack/
 */

class WP_Slack_Server_Post_Type_Command {

	private static $instance;

	public static function register(){
		if (self::$instance == null){
			self::$instance = new WP_Slack_Server_Post_Type_Command();
		}
	}


	/**
	 * Post type name.
	 *
	 * @var string
	 */
	public $name = 'slack_command';



	public function __construct() {
		require slack_server()->dir . '/inc/post-types/command/class-wp-slack-server-post-meta-box.php';
		require slack_server()->dir . '/inc/post-types/command/class-wp-slack-server-submit-meta-box.php';

		// Register custom post type to store Slack command records.
		add_action( 'init', array( $this, 'register_post_type' ) );

		// Removes builtin submitdiv meta box.
		add_action( 'admin_menu', array( $this, 'remove_submitdiv' ) );

		// Enqueue scripts/styles and disables autosave for this post type.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Alters message when post is updated.
		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );

		// Alters messages when bulk updating.
		add_filter( 'bulk_post_updated_messages', array( $this, 'bulk_post_updated_messages' ), 10, 2 );

		/*
		// Custom bulk actions.
		add_filter( 'bulk_actions-edit-slack_command', array( $this, 'custom_bulk_actions' ) );

		// Custom row actions.
		add_filter( 'post_row_actions', array( $this, 'custom_row_actions' ), 10, 2 );

		// Activate and deactivate actions.
		add_action( 'admin_action_activate', array( $this, 'activate' ) );
		add_action( 'admin_action_deactivate', array( $this, 'deactivate' ) );

		// Add notices for activate/deactivate actions.
		add_action( 'all_admin_notices', array( $this, 'admin_notices' ) );

		// Custom columns
		add_filter( sprintf( 'manage_%s_posts_columns', $this->name ), array( $this, 'columns_header' ) );
		add_action( sprintf( 'manage_%s_posts_custom_column', $this->name ), array(
			$this,
			'custom_column_row'
		), 10, 2 );
		*/

		// Hides subsub top navigation.
		add_filter( 'views_edit-' . $this->name, array( $this, 'hide_subsubsub' ) );

		// Alters title placeholder.
		add_filter( 'enter_title_here', array( $this, 'title_placeholder' ) );
	}

	public function register_post_type() {
		$args = array(
			'description'         => '',
			'public'              => false,
			'publicly_queryable'  => false,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'exclude_from_search' => true,

			'show_ui'      => true,
			'show_in_menu' => true,

			'menu_position'    => 75, // Below tools
			'menu_icon'        => slack_server()->url . '/assets/img/logo.png',
			'can_export'       => true,
			'delete_with_user' => true,
			'hierarchical'     => false,
			'has_archive'      => false,
			'query_var'        => false,

			'map_meta_cap' => false,
			'capabilities' => array(

				// meta caps (don't assign these to roles)
				'edit_post'              => 'manage_options',
				'read_post'              => 'manage_options',
				'delete_post'            => 'manage_options',

				// primitive/meta caps
				'create_posts'           => 'manage_options',

				// primitive caps used outside of map_meta_cap()
				'edit_posts'             => 'manage_options',
				'edit_others_posts'      => 'manage_options',
				'publish_posts'          => 'manage_options',
				'read_private_posts'     => 'manage_options',

				// primitive caps used inside of map_meta_cap()
				'read'                   => 'manage_options',
				'delete_posts'           => 'manage_options',
				'delete_private_posts'   => 'manage_options',
				'delete_published_posts' => 'manage_options',
				'delete_others_posts'    => 'manage_options',
				'edit_private_posts'     => 'manage_options',
				'edit_published_posts'   => 'manage_options',
			),

			'rewrite'  => false,

			// What features the post type supports.
			'supports' => array(
				'title',
			),

			'labels' => array(
				'name'               => __( 'Slack Command', 'slack' ),
				'singular_name'      => __( 'Slack Command', 'slack' ),
				'menu_name'          => __( 'Slack', 'slack' ),
				'name_admin_bar'     => __( 'Slack', 'slack' ),
				'add_new'            => __( 'Add New', 'slack' ),
				'add_new_item'       => __( 'Add New Slack Command', 'slack' ),
				'edit_item'          => __( 'Edit Slack Command', 'slack' ),
				'new_item'           => __( 'New Slack Command', 'slack' ),
				'view_item'          => __( 'View Slack Command', 'slack' ),
				'search_items'       => __( 'Search Slack Command', 'slack' ),
				'not_found'          => __( 'No slack commands found', 'slack' ),
				'not_found_in_trash' => __( 'No slack commands in trash', 'slack' ),
				'all_items'          => __( 'Slack Commands', 'slack' ),
			),

		);

		// Register the post type.
		register_post_type( $this->name, $args );
		$this->meta_box = new WP_Slack_Server_Post_Meta_Box($this);
		$this->submit_meta_box = new WP_Slack_Server_Submit_Div($this);
	}

	public function remove_submitdiv() {
		remove_meta_box( 'submitdiv', $this->name, 'side' );
	}

	public function enqueue_scripts() {
		if ( $this->name === get_post_type() ) {
			wp_dequeue_script( 'autosave' );

			wp_enqueue_style(
			// Handle.
				'slack-admin',

				// Src.
				slack_server()->url . '/assets/css/admin.css',

				// Deps
				array(),

				// Version.
				slack_server()->assets_version,

				// Media.
				'all'
			);

			wp_enqueue_script(
			// Handle.
				'slack-admin-js',

				// Src.
				slack_server()->url . '/assets/js/admin.js',

				// Deps
				array( 'jquery' ),

				// Ver.
				slack_server()->assets_version
			);
		}
	}

	public function post_updated_messages( $messages ) {
		$messages[ $this->name ] = array_fill( 0, 11, __( 'Setting updated.', 'slack' ) );

		return $messages;
	}

	/**
	 * @param array $bulk_messages Arrays of messages, each keyed by the corresponding post type. Messages are
	 *                             keyed with 'updated', 'locked', 'deleted', 'trashed', and 'untrashed'.
	 * @param array $bulk_counts Array of item counts for each message, used to build internationalized strings.
	 */
	public function bulk_post_updated_messages( $bulk_messages, $bulk_counts ) {
		$screen = get_current_screen();

		if ( $this->name === $screen->post_type ) {
			$bulk_messages['post'] = array(
				'updated'   => _n( '%s command updated.', '%s commands updated.', $bulk_counts['updated'] ),
				'locked'    => _n( '%s command not updated, somebody is editing it.', '%s commands not updated, somebody is editing them.', $bulk_counts['locked'] ),
				'deleted'   => _n( '%s command permanently deleted.', '%s commands permanently deleted.', $bulk_counts['deleted'] ),
				'trashed'   => _n( '%s command moved to the Trash.', '%s commands moved to the Trash.', $bulk_counts['trashed'] ),
				'untrashed' => _n( '%s command restored from the Trash.', '%s commands restored from the Trash.', $bulk_counts['untrashed'] ),
			);
		}

		return $bulk_messages;
	}

	/**
	 * Custom bulk actions.
	 *
	 * @param  array $actions
	 *
	 * @return array
	 *
	 * @filter bulk_actions-edit-slack_command
	 */
	public function custom_bulk_actions( $actions ) {
		unset( $actions['edit'] );

		// Unfortunately adding bulk actions won't work here.
		// @see https://core.trac.wordpress.org/ticket/16031
		//
		// $actions['activate']   = __( 'Activate', 'slack' );
		// $actions['deactivate'] = __( 'Deactivate', 'slack' );

		return $actions;
	}

	/**
	 * Custom row actions for this post type.
	 *
	 * @param  array $actions
	 *
	 * @return array
	 *
	 * @filter post_row_actions
	 */
	public function custom_row_actions( $actions ) {
		$post = get_post();

		if ( $this->name === get_post_type( $post ) ) {
			unset( $actions['inline hide-if-no-js'] );
			unset( $actions['view'] );

			$setting          = get_post_meta( $post->ID, 'slack_command_setting', true );
			$post_type_object = get_post_type_object( $post->post_type );

			if ( $setting['active'] ) {
				$actions['deactivate'] = "<a title='" . esc_attr( __( 'Deactivate this command setting' ) ) . "' href='" . wp_nonce_url( admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=deactivate', $post->ID ) ), 'deactivate-post_' . $post->ID ) . "'>" . __( 'Deactivate' ) . "</a>";
			} else {
				$actions['activate'] = "<a title='" . esc_attr( __( 'Activate this command setting' ) ) . "' href='" . wp_nonce_url( admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=activate', $post->ID ) ), 'activate-post_' . $post->ID ) . "'>" . __( 'Activate' ) . "</a>";
			}
		}

		return $actions;
	}

	/**
	 * Activates the command.
	 *
	 * @action admin_action_{action}
	 */
	public function activate() {
		$this->_set_active_setting();
	}

	/**
	 * Deactivates the command.
	 *
	 * @action admin_action_{action}
	 */
	public function deactivate() {
		$this->_set_active_setting( false );
	}

	/**
	 * Action handler for activating/deactivating command setting(s).
	 *
	 * @param bool $activate
	 */
	private function _set_active_setting( $activate = true ) {
		$screen = get_current_screen();
		if ( $screen->id !== $this->name ) {
			return;
		}

		$post = ! empty( $_REQUEST['post'] ) ? get_post( $_REQUEST['post'] ) : null;
		if ( ! $post ) {
			wp_die(
				sprintf( __( 'The command you are trying to %s is no longer exists.', 'slack' ), $activate ? 'activate' : 'deactivate' )
			);
		}

		check_admin_referer( sprintf( '%s-post_%d', $activate ? 'activate' : 'deactivate', $post->ID ) );

		$sendback          = admin_url( 'edit.php?post_type=' . $this->name );
		$setting           = get_post_meta( $post->ID, 'slack_command_setting', true );
		$setting['active'] = $activate;

		update_post_meta( $post->ID, 'slack_command_setting', $setting );

		$key_arg = $activate ? 'activated' : 'deactivated';

		wp_redirect( add_query_arg( array( "$key_arg" => 1, 'ids' => $post->ID ), $sendback ) );
		exit;
	}

	/**
	 *
	 * @action all_admin_notices
	 */
	public function admin_notices() {
		$screen = get_current_screen();
		if ( $screen->id !== 'edit-' . $this->name ) {
			return;
		}

		$bulk_counts = array(
			'activated'   => isset( $_REQUEST['activated'] ) ? absint( $_REQUEST['activated'] ) : 0,
			'deactivated' => isset( $_REQUEST['deactivated'] ) ? absint( $_REQUEST['deactivated'] ) : 0,
		);

		$bulk_messages = array(
			'activated'   => _n( '%s command activated.', '%s commands activated.', $bulk_counts['activated'] ),
			'deactivated' => _n( '%s command deactivated.', '%s commands deactivated.', $bulk_counts['deactivated'] ),
		);

		$bulk_counts = array_filter( $bulk_counts );

		// If we have a bulk message to issue:
		$messages = array();
		foreach ( $bulk_counts as $message => $count ) {
			if ( isset( $bulk_messages[ $message ] ) ) {
				$messages[] = sprintf( $bulk_messages[ $message ], number_format_i18n( $count ) );
			}
		}

		if ( $messages ) {
			echo '<div id="message" class="updated"><p>' . join( ' ', $messages ) . '</p></div>';
		}
	}

	/**
	 * Custom columns for this post type.
	 *
	 * @param  array $columns
	 *
	 * @return array
	 *
	 * @filter manage_{post_type}_posts_columns
	 */
	public function columns_header( $columns ) {
		unset( $columns['date'] );

		$columns['service_url'] = __( 'Service URL', 'slack' );
		$columns['channel']     = __( 'Channel', 'slack' );
		$columns['bot_name']    = __( 'Bot Name', 'slack' );
		$columns['events']      = __( 'Notified Events', 'slack' );

		return $columns;
	}

	/**
	 * Custom column appears in each row.
	 *
	 * @param string $column Column name
	 * @param int $post_id Post ID
	 *
	 * @action manage_{post_type}_posts_custom_column
	 */
	public function custom_column_row( $column, $post_id ) {
		$setting = get_post_meta( $post_id, 'slack_command_setting', true );
		switch ( $column ) {
			case 'service_url':
				echo ! empty( $setting['service_url'] ) ? sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( $setting['service_url'] ), esc_html( $setting['service_url'] ) ) : '';
				break;
			case 'channel':
				echo ! empty( $setting['channel'] ) ? $setting['channel'] : '';
				break;
			case 'bot_name':
				echo ! empty( $setting['username'] ) ? $setting['username'] : '';
				break;
		}
	}



	/**
	 * Hides subsubsub top nav.
	 *
	 * @return array
	 */
	public function hide_subsubsub() {
		return array();
	}

	public function title_placeholder( $title ) {
		$screen = get_current_screen();

		if ( $this->name === $screen->post_type ) {
			$title = __( 'command Name', 'slack' );
		}

		return $title;
	}
}
