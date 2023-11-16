<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    dp-divi-module-builder
 * @subpackage dp-divi-module-builder/admin
 * @author     DiviPlugins <support@diviplugins.com>
 */
class DP_DMB_Admin extends DP_DMB_Utils_Functions {

	/**
	 * The ID of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    2.0.0
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @param $hook
	 *
	 * @since    2.0.0
	 */
	public function enqueue_styles( $hook ) {
		/*
		 * Enqueue styles of the dependency page
		 */
		if ( $hook === 'dp_custom_modules_page_dp_dmb_dependency_options' ) {
			wp_enqueue_style( 'dp_dmb_page_dependency', DPDMB_PLUGIN_URL . '/admin/css/page-dependency.css', false, DPDMB_PLUGIN_VERSION );
		}
		/*
		 * Enqueue styles of the post new page or post page for the dp_custom_modules post type
		 */
		// phpcs:ignore
		if ( ( 'post-new.php' === $hook && isset( $_GET['post_type'] ) && 'dp_custom_modules' === $_GET['post_type'] ) || ( 'post.php' === $hook && isset( $_GET['post'] ) && 'dp_custom_modules' === get_post_type( $_GET['post'] ) ) ) {
			wp_enqueue_style( 'dp_dmb_page_custom_modules', DPDMB_PLUGIN_URL . '/admin/css/page-custom-module.css', false, DPDMB_PLUGIN_VERSION );
		}
		/*
		 * Enqueue styles of the settings page
		 */
		if ( $hook === 'dp_custom_modules_page_dp_dmb_options' ) {
			wp_enqueue_style( 'dp_dmb_page_settings', DPDMB_PLUGIN_URL . '/admin/css/page-settings.css', false, DPDMB_PLUGIN_VERSION );
		}
		/*
		 * Enqueue styles of the import page
		 */
		if ( $hook === 'dp_custom_modules_page_dp_dmb_import' ) {
			wp_enqueue_style( 'dp_dmb_page_import', DPDMB_PLUGIN_URL . '/admin/css/page-import.css', false, DPDMB_PLUGIN_VERSION );
		}
		/*
		 * Enqueue styles of the license page
		 */
		if ( $hook === 'dp_custom_modules_page_dp_dmb_license' ) {
			wp_enqueue_style( 'dp_dmb_page_license', DPDMB_PLUGIN_URL . '/admin/css/page-license.css', false, DPDMB_PLUGIN_VERSION );
		}
		/*
		 * Enqueue styles of the debugger page
		 */
		if ( $hook === 'dp_custom_modules_page_dp_dmb_debug' ) {
			wp_enqueue_style( 'dp_dmb_page_debugger', DPDMB_PLUGIN_URL . '/admin/css/page-debugger.css', false, DPDMB_PLUGIN_VERSION );
		}
		/*
		 * Enqueue module block styles
		 */
		if ( function_exists( 'et_builder_bfb_enabled' ) && et_builder_bfb_enabled() ) {
			wp_enqueue_style( $this->plugin_name, DPDMB_PLUGIN_URL . '/public/css/dp-dmb-public.css', array(), $this->version );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @param $hook
	 *
	 * @since    2.0.0
	 */
	public function enqueue_scripts( $hook ) {
		/*
		 * Enqueue script of the dependency page
		 */
		if ( 'dp_custom_modules_page_dp_dmb_dependency_options' === $hook ) {
			wp_enqueue_script( 'dp_dmb_page_dependency', DPDMB_PLUGIN_URL . '/admin/js/page-dependency.js', array( 'jquery' ), DPDMB_PLUGIN_VERSION, true );
		}
		/*
		 * Enqueue script of the post new page or post page for the dp_custom_modules post type
		 */
		// phpcs:ignore
		if ( ( 'post-new.php' === $hook && isset( $_GET['post_type'] ) && 'dp_custom_modules' === $_GET['post_type'] ) || ( 'post.php' === $hook && isset( $_GET['post'] ) && 'dp_custom_modules' === get_post_type( $_GET['post'] ) ) ) {
			wp_enqueue_script( 'dp_dmb_page_custom_modules', DPDMB_PLUGIN_URL . '/admin/js/page-custom-module.js', array( 'jquery' ), DPDMB_PLUGIN_VERSION, true );
			$codemirror_options   = array( 'indentUnit' => 2 );
			$css_editor_args      = wp_enqueue_code_editor(
				array(
					'type'       => 'text/css',
					'codemirror' => $codemirror_options
				)
			);
			$js_editor_args       = wp_enqueue_code_editor(
				array(
					'type'       => 'text/javascript',
					'codemirror' => $codemirror_options
				)
			);
			$php_html_editor_args = wp_enqueue_code_editor(
				array(
					'type'       => 'application/x-httpd-php',
					'codemirror' => $codemirror_options
				)
			);
			$php_only_editor_args = wp_enqueue_code_editor(
				array(
					'type'       => 'text/x-php',
					'codemirror' => $codemirror_options
				)
			);
			wp_add_inline_script( 'dp_dmb_page_custom_modules', sprintf( 'jQuery( function() { wp.codeEditor.initialize( "_dp_dmb_cssbox_textarea_css_output", %1$s ); wp.codeEditor.initialize( "_dp_dmb_jsbox_textarea_js_output", %2$s ); wp.codeEditor.initialize( "_dp_dmb_functionsbox_textarea_functions_output", %3$s ); wp.codeEditor.initialize( "_dp_dmb_htmlbox_textarea_html_output", %3$s ); wp.codeEditor.initialize( "_dp_dmb_htmlchildbox_textarea_htmlchild_output", %3$s ); wp.codeEditor.initialize( "_dp_dmb_beforerenderbox_textarea_before_render_output", %4$s )} );', wp_json_encode( $css_editor_args ), wp_json_encode( $js_editor_args ), wp_json_encode( $php_html_editor_args ), wp_json_encode( $php_only_editor_args ) ) );
		}
		/*
		 * Enqueue script of the settings page
		 */
		if ( $hook === 'dp_custom_modules_page_dp_dmb_options' ) {
			wp_enqueue_script( 'dp_dmb_page_settings', DPDMB_PLUGIN_URL . '/admin/js/page-settings.js', array( 'jquery' ), DPDMB_PLUGIN_VERSION, true );
			wp_localize_script( 'dp_dmb_page_settings', 'dpDmb', array(
				'ajaxurl'    => admin_url( 'admin-ajax.php' ),
				'delete_msg' => __( 'The following module will be moved to Trash: ', 'dp_divi_module_builder' )
			) );
		}
		/*
		 * Enqueue script of the import page
		 */
		if ( $hook === 'dp_custom_modules_page_dp_dmb_import' ) {
			wp_enqueue_script( 'dp_dmb_page_import', DPDMB_PLUGIN_URL . '/admin/js/page-import.js', array(
				'jquery',
				'jquery-ui-tabs'
			), DPDMB_PLUGIN_VERSION, true );
			wp_localize_script( 'dp_dmb_page_import', 'dpDmbImport', array(
				'ajaxurl'     => admin_url( 'admin-ajax.php' ),
				'settingsurl' => admin_url( 'edit.php' ) . '?post_type=dp_custom_modules&page=dp_dmb_options'
			) );
		}
		/*
		 * Enqueue the quick edit custom script on the edit admin page and only if the post type is dp_custom_modules
		 */
		// phpcs:ignore
		if ( 'edit.php' === $hook && isset( $_GET['post_type'] ) && 'dp_custom_modules' === $_GET['post_type'] ) {
			wp_enqueue_script( 'dp_dmb_quick_edit', DPDMB_PLUGIN_URL . '/admin/js/quick-edit-custom-modules.js', array( 'jquery' ), DPDMB_PLUGIN_VERSION, true );
		}
	}

	/**
	 * Modify the admin tab titles for each of the DMB pages.
	 *
	 * @param $admin_title
	 *
	 * @return string|void
	 * @since 2.1.0
	 */
	public function modify_admin_tab_titles( $admin_title ) {
		global $current_screen;
		$hook = $current_screen->base;
		if ( $hook === "dp_custom_modules_page_dp_dmb_dependency_options" ) {
			return __( 'DMB Dependency', 'dp_divi_module_builder' );
		} elseif ( $hook === "dp_custom_modules_page_dp_dmb_debug" ) {
			return __( 'DMB Debugger', 'dp_divi_module_builder' );
		} elseif ( $hook === "dp_custom_modules_page_dp_dmb_import" ) {
			return __( 'DMB Import', 'dp_divi_module_builder' );
		} elseif ( $hook === "dp_custom_modules_page_dp_dmb_options" ) {
			return __( 'DMB Settings', 'dp_divi_module_builder' );
		} elseif ( $hook === "dp_custom_modules_page_dp_dmb_license" ) {
			return __( 'DMB License', 'dp_divi_module_builder' );
		} elseif ( $hook === "edit" && $current_screen->post_type === "dp_custom_modules" ) {
			return __( 'DMB Modules', 'dp_divi_module_builder' );
		} elseif ( $hook === "post" && $current_screen->action === "add" && $current_screen->post_type === "dp_custom_modules" ) {
			return __( 'DMB New Module', 'dp_divi_module_builder' );
		} elseif ( $hook === "post" && $current_screen->action === "" && $current_screen->post_type === "dp_custom_modules" ) {
			return __( 'DMB Edit Module', 'dp_divi_module_builder' );
		} else {
			return $admin_title;
		}
	}

	/**
	 * Register custom modules post type. Slug for this custom post type dp_custom_modules
	 *
	 * @since 2.0.0
	 */
	public function add_custom_modules_post_type() {
		$labels = array(
			'name'               => __( 'Custom Modules', 'dp_divi_module_builder' ),
			'singular_name'      => __( 'Module', 'dp_divi_module_builder' ),
			'add_new'            => __( 'Add Module', 'dp_divi_module_builder' ),
			'add_new_item'       => __( 'Add Module', 'dp_divi_module_builder' ),
			'edit_item'          => __( 'Edit Module', 'dp_divi_module_builder' ),
			'new item'           => __( 'New Module', 'dp_divi_module_builder' ),
			'all_items'          => __( 'Modules', 'dp_divi_module_builder' ),
			'view_item'          => __( 'View Modules', 'dp_divi_module_builder' ),
			'search_items'       => __( 'Search Modules', 'dp_divi_module_builder' ),
			'not_found'          => __( 'No modules found', 'dp_divi_module_builder' ),
			'not_found_in_trash' => __( 'No modules found in Trash', 'dp_divi_module_builder' ),
			'menu_name'          => __( 'Custom Modules', 'dp_divi_module_builder' )
		);

		$args = array(
			'labels'        => $labels,
			'public'        => false,
			'supports'      => array( 'title', ),
			'menu_position' => 100,
			'menu_icon'     => DPDMB_PLUGIN_URL . "/admin/img/dp.png",
			'show_ui'       => true,
			'has_archive'   => false,
		);

		register_post_type( 'dp_custom_modules', $args );
	}

	/**
	 * Add a checkbox on quick edit for dp_custom_modules post type to allow the user enable/disabled php processing for any dp_custom_modules post type from the post list.
	 *
	 * @param $column_name
	 * @param $post_type
	 *
	 * @since 2.0.0
	 */
	public function add_php_checkbox_on_quick_edit( $column_name, $post_type ) {
		if ( $post_type === 'dp_custom_modules' ) {
			echo et_core_intentionally_unescaped( sprintf( '<fieldset class="inline-edit-col-right inline-edit-php"><div class="inline-edit-col column-%1$s"><label class="inline-edit-group">%2$s</label></div></fieldset>', $column_name, ( $column_name === "_dp_dmb_htmlbox_checkbox_php_onoff" ) ? sprintf( '<span class="title">%1$s</span><input name="dp_dmb_quick_edit_checkbox_php_onoff" type="checkbox" />', __( 'PHP processing', 'dp_divi_module_builder' ) ) : "" ), 'html' );
		}
	}

	/**
	 * Update post meta php checkbox after change on quick edit
	 *
	 * @param $post_id
	 *
	 * @since 2.0.0
	 */
	public function update_post_meta_after_quick_edit( $post_id ) {
		if ( get_post_type( $post_id ) === 'dp_custom_modules' && get_post_status( $post_id ) !== 'draft' ) {
			global $pagenow;
			// phpcs:ignore
			if ( $pagenow === 'admin-ajax.php' && $_REQUEST['action'] !== 'dp_dmb_process_update_file' && $_REQUEST['action'] !== 'dp_dmb_publish_module' && $_REQUEST['action'] !== 'dp_dmb_process_import_publish_module' ) {
				// phpcs:ignore
				if ( isset( $_REQUEST['dp_dmb_quick_edit_checkbox_php_onoff'] ) ) {
					update_post_meta( $post_id, '_dp_dmb_htmlbox_checkbox_php_onoff', 'on' );
				} else {
					update_post_meta( $post_id, '_dp_dmb_htmlbox_checkbox_php_onoff', '' );
				}
			}
		}
	}

	/**
	 * Add custom column content for the php processing activate/deactivate feature.
	 *
	 * @param $column
	 * @param $post_id
	 *
	 * @since 2.0.0
	 */
	public function add_custom_php_column( $column, $post_id ) {
		switch ( $column ) {
			case '_dp_dmb_htmlbox_checkbox_php_onoff':
				echo et_core_intentionally_unescaped( sprintf( '<div id="module-%1$s" data-value="%2$s"></div>', $post_id, get_post_meta( $post_id, '_dp_dmb_htmlbox_checkbox_php_onoff', true ) ), 'html' );
				break;
		}
	}

	/**
	 * Remove some defaults and unused metabox from the dp-custom-modules post type edit page
	 *
	 * @param $post_type
	 *
	 * @since 2.0.0
	 */
	public function remove_unwanted_metabox( $post_type ) {
		if ( $post_type === "dp_custom_modules" ) {
			global $wp_meta_boxes;
			$exceptions = apply_filters( 'dpdmb_allowed_metaboxes', array(
				'submitdiv',
				'postcustom',
				'dp_dmb_fields_metabox',
				'dp_dmb_html_metabox',
				'dp_dmb_css_metabox',
				'dp_dmb_js_metabox',
				'dp_dmb_htmlchild_metabox',
				'dp_dmb_functions_metabox',
				'dp_dmb_before_render_metabox',
				'dp_dmb_global_assets_metabox'
			) );
			if ( ! empty( $wp_meta_boxes ) ) {
				foreach ( $wp_meta_boxes as $page => $page_boxes ) {
					if ( ! empty( $page_boxes ) ) {
						foreach ( $page_boxes as $context => $box_context ) {
							if ( ! empty( $box_context ) ) {
								foreach ( $box_context as $box_type ) {
									if ( ! empty( $box_type ) ) {
										foreach ( $box_type as $id => $box ) {
											if ( ! in_array( $id, $exceptions ) ) {
												remove_meta_box( $id, $page, $context );
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}

	public function remove_unwanted_metabox_head() {
		remove_meta_box( 'icl_div_config', "dp_custom_modules", 'normal' );
	}

	/**
	 * Recreate dmb folder when new blog is create on WP multisite
	 *
	 * @param $blog_id
	 *
	 * @since 2.0.0
	 */
	public function multisite_new_blog_recreate_dmb_folder( $blog_id ) {
		if ( is_plugin_active_for_network( 'dp-divi-module-builder/dp-divi-module-builder.php' ) ) {
			switch_to_blog( $blog_id );
			parent::set_module_files_dir();
			restore_current_blog();
		}
	}

	/**
	 * Add script to the admin head to clear local storage data related with custom modules
	 *
	 * @since 2.0.0
	 */
	public function remove_custom_modules_from_local_storage() {
		global $post;
		if ( isset( $post->ID ) && get_post_type( $post->ID ) === 'dp_custom_modules' ) {
			echo "<script>localStorage.removeItem('et_pb_templates_et_pb_dp_dmb_module_" . esc_attr( $post->ID ) . "');</script>";
		}
	}

	/**
	 * Trigger updates of all modules when version change
	 *
	 * @since 2.0.0
	 */
	public function trigger_update_all_modules() {
		if ( get_option( 'dp_dmb_last_update', '0' ) !== DPDMB_PLUGIN_VERSION ) {
			parent::dmb_write_log( 'Trigger and update of all module files after version change from ' . get_option( 'dp_dmb_last_update', '0' ) . ' to ' . DPDMB_PLUGIN_VERSION );
			$this->update_all_modules();
			update_option( 'dp_dmb_last_update', DPDMB_PLUGIN_VERSION );
		}
	}

	/**
	 * Updates of all modules when version change
	 *
	 * @since 2.0.0
	 */
	public function update_all_modules() {
		parent::dmb_write_log( 'Updating all modules files' );
		foreach ( parent::get_modules() as $module ) {
			new DP_DMB_Custom_Module( array(
				'id'    => $module['id'],
				'title' => $module['name']
			) );
		}
	}

	/**
	 * Write module files when post publish is save or update or remove modules file is post is not publish
	 *
	 * @param $post_id
	 *
	 * @since 2.0.0
	 */
	public function save_custom_module_post( $post_id ) {
		if ( get_post_type( $post_id ) === 'dp_custom_modules' ) {
			if ( get_post_status( $post_id ) === "publish" ) {
				$this->check_dmb_folder();
				parent::dmb_write_log( 'Creating modules files of module ' . $post_id );
				new DP_DMB_Custom_Module( array( 'id' => $post_id ) );
			} else {
				parent::dmb_write_log( 'Removing modules files of module ' . $post_id );
				$this->remove_module_files( $post_id );
			}
		}
	}

	/**
	 * Remove modules files
	 *
	 * @param $id
	 *
	 * @since 2.0.0
	 */
	public function remove_module_files( $id ) {
		parent::remove_file( DPDMB_MODULES_DIR . '/modules/dp_custom_module_' . $id . '.php' );
		parent::remove_file( DPDMB_MODULES_DIR . '/export/dp_custom_module_' . $id . '.json' );
		parent::remove_file( DPDMB_MODULES_DIR . '/css/dp_custom_module_' . $id . '.css' );
		parent::remove_file( DPDMB_MODULES_DIR . '/js/dp_custom_module_' . $id . '.js' );
	}

	/**
	 * Legacy way to load the custom modules
	 *
	 * @since 2.0.0
	 */
	public function legacy_way_to_load_modules() {
		foreach ( glob( DPDMB_MODULES_DIR . '/modules/*.php' ) as $filename ) {
			include_once $filename;
		}
	}

	/**
	 * Check for the existence of the dmb folder. If not recreate it and recreate all published modules files.
	 *
	 * @since 2.0.0
	 */
	public function check_dmb_folder() {
		if ( ! parent::check_dmb_folder_existence() ) {
			parent::set_module_files_dir();
			$this->update_all_modules();
		}
	}

	public function create_delete_dmb_log_file( $old_value, $new_value ) {
		$debug_file    = DPDMB_MODULES_DIR . '/dmb-debug.log';
		$debug_options = $new_value;
		if ( is_array( $debug_options ) && array_key_exists( '_dp_dmb_debugger_active', $debug_options ) && $debug_options['_dp_dmb_debugger_active'] === 'on' ) {
			set_transient( 'dmb_transient_debug_expired', 'OK', 86400 );
			if ( ! file_exists( $debug_file ) ) {
				// phpcs:ignore
				$file = fopen( $debug_file, 'w' );
				// phpcs:ignore
				fclose( $file );
			}
		} else {
			if ( file_exists( $debug_file ) ) {
				unlink( $debug_file );
			}
		}
	}

	public function remove_expired_log() {
		if ( get_transient( 'dmb_transient_debug_expired' ) !== 'OK' ) {
			$debug_options = get_option( 'dp_dmb_debug', '' );
			if ( is_array( $debug_options ) && array_key_exists( '_dp_dmb_debugger_active', $debug_options ) && $debug_options['_dp_dmb_debugger_active'] === 'on' ) {
				update_option( 'dp_dmb_debug', '' );
			}
		}
	}

}
