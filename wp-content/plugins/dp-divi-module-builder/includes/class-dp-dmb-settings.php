<?php

/**
 * This class handle the settings page related functions
 *
 * @since      2.1.0
 * @package    dp-divi-module-builder
 * @subpackage dp-divi-module-builder/includes
 * @author     DiviPlugins <support@diviplugins.com>
 */
class DP_DMB_Settings extends DP_DMB_Utils_Functions {

	public $dp_dmb_global_default_settings;

	public function __construct() {
		$this->dp_dmb_global_default_settings = array(
			"show_partial_message" => false
		);
	}

	/**
	 * Register settings page options dp_dmb_options
	 *
	 * @since 2.0.0
	 */
	public function register_setting_page_options() {
		register_setting( 'dp_dmb_options', 'dp_dmb_options' );
	}

	/**
	 * Add settings page to WP menu
	 *
	 * @since 2.0.0
	 */
	public function add_settings_page() {
		add_submenu_page( 'edit.php?post_type=dp_custom_modules', __( 'Settings', 'dp_divi_module_builder' ), __( 'Settings', 'dp_divi_module_builder' ), 'manage_options', 'dp_dmb_options', array(
			$this,
			'settings_page_html'
		) );
	}

	/**
	 * Html code of the settings page
	 *
	 * @since 2.0.0
	 */
	public function settings_page_html() {
		$dp_dmb_global_settings = get_option( 'dp_dmb_global_settings', $this->dp_dmb_global_default_settings );
		?>
        <div class="wrap cmb2-options-page dp_dmb_options dp_dmb_wrapper">
            <div id="dp_dmb_admin_page_header">
                <h2 class="dp_dmb_admin_page_title"><?php echo esc_html( __( 'Divi Module Builder - Settings', 'dp_divi_module_builder' ) ); ?></h2>
                <p class="dp_dmb_admin_page_description"><?php echo esc_html( __( 'Manage, Import, and Export your modules. If you have entered invalid PHP markup in a module and have become locked out of the module editor, you can temporarily Disable PHP for that module. Once disabled, you can once again edit the module, correct your mistake and Enable PHP. Modules will not become available in the Divi Page Builder until they are published.', 'dp_divi_module_builder' ) ); ?></p>
                <form id="dp_dmb_admin_page_settings_form">
                    <input type="hidden" name="action"
                           value="dp_dmb_save_extra_settings">
                    <input type="hidden" name="dp_dmb_global_settings[reload]"
                           value="1">
                    <label>
                        <input type="hidden"
                               name="dp_dmb_global_settings[show_partial_message]">
                        <input type="checkbox"
                               name="dp_dmb_global_settings[show_partial_message]"
                               value="hide_partial_warning" <?php echo esc_html( ( $dp_dmb_global_settings['show_partial_message'] ) ? "checked" : "" ); ?>>
						<?php echo esc_html( __( 'Show Divi\'s third party, partial compatibility warning for all modules', 'dp_divi_module_builder' ) ); ?>
                    </label>
                    <input type="submit"
                           value="<?php echo esc_html( __( 'Save Settings', 'dp_divi_module_builder' ) ); ?>">
					<?php wp_nonce_field( "dp_dmb_save_extra_settings" ); ?>
                </form>
            </div>
            <hr>
            <div id="dp_dmb_admin_page_modules_container">
                <h3 class="dp_dmb_admin_page_section"><?php echo esc_html( __( 'Published Modules', 'dp_divi_module_builder' ) ); ?></h3>
				<?php
				$modules_count = 0;
				foreach ( parent::get_modules() as $module ) :
					?>
                    <div class="dp_dmb_admin_page_module">
                        <div class="dp_dmb_admin_page_module_name_container">
                            <span><?php echo esc_html( $module['name'] ); ?></span>
                        </div>
                        <div class="dp_dmb_admin_page_module_actions_container">
                            <a class="dp_dmb_admin_page_delete_link"
                               data-module="<?php echo esc_html( $module['id'] ); ?>"
                               data-module-name="<?php echo esc_html( $module['name'] ); ?>"
                               href="#"><?php echo esc_html( __( 'Delete', 'dp_divi_module_builder' ) ); ?></a>
                            <a class="dp_dmb_admin_page_clone_link " href="#"
                               data-module="<?php echo esc_html( $module['id'] ); ?>"><?php echo esc_html( __( 'Clone', 'dp_divi_module_builder' ) ); ?></a>
                            <a class="dp_dmb_admin_page_edit_link"
                               href="post.php?post=<?php echo esc_html( $module['id'] ); ?>&action=edit"><?php echo esc_html( __( 'Edit', 'dp_divi_module_builder' ) ); ?></a>
							<?php if ( $module['php'] === 'on' ): ?>
                                <a class="dp_dmb_admin_page_php_on_link" href="#"
                                   data-module="<?php echo esc_html( $module['id'] ); ?>"><?php echo esc_html( __( 'Disable PHP', 'dp_divi_module_builder' ) ); ?></a>
							<?php else: ?>
                                <a class="dp_dmb_admin_page_php_off_link" href="#"
                                   data-module="<?php echo esc_html( $module['id'] ); ?>"><?php echo esc_html( __( 'Enable PHP', 'dp_divi_module_builder' ) ); ?></a>
							<?php endif; ?>
                            <a class="dp_dmb_admin_page_update_link" href="#"
                               data-module="<?php echo esc_html( $module['id'] ); ?>"
                               data-module-name="<?php echo esc_html( $module['name'] ); ?>"><?php echo esc_html( __( 'Update', 'dp_divi_module_builder' ) ); ?></a>
                            <a class="dp_dmb_admin_page_export_link"
                               href="<?php echo esc_html( DPDMB_MODULES_URL . '/export/dp_custom_module_' . $module['id'] . '.json' ); ?>"
                               download=""><?php echo esc_html( __( 'Export', 'dp_divi_module_builder' ) ); ?></a>
                        </div>
                    </div>
					<?php
					$modules_count ++;
				endforeach;
				if ( $modules_count > 0 ):
					?>
                    <a id="dp_dmb_deactivate_all_php"
                       href="#"><?php echo esc_html( __( 'Disable PHP on all modules', 'dp_divi_module_builder' ) ); ?></a>
				<?php endif; ?>
                <hr>
                <h3 class="dp_dmb_admin_page_section"><?php echo esc_html( __( 'Draft Modules', 'dp_divi_module_builder' ) ); ?></h3>
				<?php
				foreach ( parent::get_draft_modules() as $module ) :
					?>
                    <div class="dp_dmb_admin_page_module">
                        <div class="dp_dmb_admin_page_module_name_container">
                            <span><?php echo esc_html( $module['name'] ); ?></span>
                        </div>
                        <div class="dp_dmb_admin_page_module_actions_container">
                            <a class="dp_dmb_admin_page_delete_link"
                               data-module="<?php echo esc_html( $module['id'] ); ?>"
                               data-module-name="<?php echo esc_html( $module['name'] ); ?>"
                               href="#"><?php echo esc_html( __( 'Delete', 'dp_divi_module_builder' ) ); ?></a>
                            <a class="dp_dmb_admin_page_publish_link"
                               data-module="<?php echo esc_html( $module['id'] ); ?>"
                               href="#"><?php echo esc_html( __( 'Publish', 'dp_divi_module_builder' ) ); ?></a>
                            <a class="dp_dmb_admin_page_edit_link"
                               href="post.php?post=<?php echo esc_html( $module['id'] ); ?>&action=edit"><?php echo esc_html( __( 'Edit', 'dp_divi_module_builder' ) ); ?></a>
                        </div>
                    </div>
				<?php
				endforeach;
				?>
            </div>
            <div id="dp_dmb_admin_page_update_modal_container">
                <div id="dp_dmb_admin_page_update_form_container">
                    <span class="dp_dmb_close_btn">X</span>
                    <p><?php echo esc_html( __( 'Select the json file of a module previously exported to replace this module with. The module ID will stay the same, everything else will get replaced. You are about to replace: ', 'dp_divi_module_builder' ) ); ?>
                        <strong class="dp_dmb_module_name"></strong></p>
                    <form id="dp_dmb_form_update" method="get"
                          enctype="multipart/form-data">
                        <input type="file" name="dp_dmb_form_update_file"
                               id="dp_dmb_form_update_file" required=""
                               accept="application/json,.json"><br>
                        <label style="display: block;"><input type="checkbox"
                                                              name="dp_dmb_form_maintain_name"
                                                              id="dp_dmb_form_maintain_name"><?php echo esc_html( __( 'Check this box if you want to replace the current module name with the imported module name.', 'dp_divi_module_builder' ) ); ?>
                        </label><br>
                        <input id="dp_dmb_form_update_button" type="submit"
                               value="<?php echo esc_html( __( 'Update Module', 'dp_divi_module_builder' ) ); ?>">
                        <div class="dp_dmb_loader_wrapper">
                            <div id="dp_dmb_form_update_msg"></div>
                            <div class="dp_dmb_loader"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
		<?php
	}

	/**
	 * Ajax function that handle the process of update a custom modules
	 *
	 * @since 2.0.0
	 */
	public function ajax_process_update_file() {
		// phpcs:ignore
		$posted_data = isset( $_POST ) ? $_POST : array();
		// phpcs:ignore
		$file_data = isset( $_FILES ) ? $_FILES : array();
		$data      = array_merge( $posted_data, $file_data );
		if ( $data['file']['error'] === 0 ) {
			// phpcs:ignore
			$json_file_data = file_get_contents( $data['file']['tmp_name'] );
			if ( $json_file_data ) {
				$module_array = json_decode( $json_file_data, true );
				$args         = array(
					'ID'         => $data['module_id'],
					'post_type'  => 'dp_custom_modules',
					'meta_input' => array(
						'_dp_dmb_builder_version'                               => ( array_key_exists( 'builder_version', $module_array ) ) ? $module_array['builder_version'] : "",
						'_dp_dmb_fieldbox_checkbox_fullwidth'                   => ( array_key_exists( 'fullwidth', $module_array ) ) ? $module_array['fullwidth'] : "",
						'_dp_dmb_fieldbox_partial_support'                      => ( array_key_exists( 'partial_support', $module_array ) ) ? $module_array['partial_support'] : "",
						'_dp_dmb_fieldbox_repeat_group_fields'                  => ( array_key_exists( 'fields', $module_array ) ) ? $module_array['fields'] : "",
						'_dp_dmb_htmlbox_textarea_html_output'                  => ( array_key_exists( 'html', $module_array ) ) ? parent::add_backslashes( $module_array['html'] ) : "",
						'_dp_dmb_htmlbox_checkbox_php_onoff'                    => ( array_key_exists( 'php', $module_array ) ) ? $module_array['php'] : "",
						'_dp_dmb_cssbox_textarea_css_output'                    => ( array_key_exists( 'css', $module_array ) ) ? parent::add_backslashes( $module_array['css'] ) : "",
						'_dp_dmb_jsbox_textarea_js_output'                      => ( array_key_exists( 'js', $module_array ) ) ? parent::add_backslashes( $module_array['js'] ) : "",
						'_dp_dmb_fieldbox_checkbox_tiny_mce'                    => ( array_key_exists( 'tiny_mce', $module_array ) ) ? $module_array['tiny_mce'] : "",
						'_dp_dmb_fieldbox_checkbox_dynamic_content'             => ( array_key_exists( 'dynamic_content', $module_array ) ) ? $module_array['dynamic_content'] : "",
						'_dp_dmb_fieldbox_checkbox_repeat_fields'               => ( array_key_exists( 'unwrap_repeat_fields', $module_array ) ) ? $module_array['unwrap_repeat_fields'] : "",
						'_dp_dmb_fieldbox_child_label'                          => ( array_key_exists( 'child_label', $module_array ) ) ? $module_array['child_label'] : "",
						'_dp_dmb_htmlchildbox_textarea_htmlchild_output'        => ( array_key_exists( 'html_child', $module_array ) ) ? parent::add_backslashes( $module_array['html_child'] ) : "",
						'_dp_dmb_functionsbox_textarea_functions_output'        => ( array_key_exists( 'custom_functions', $module_array ) ) ? parent::add_backslashes( $module_array['custom_functions'] ) : "",
						'_dp_dmb_beforerenderbox_textarea_before_render_output' => ( array_key_exists( 'before_render', $module_array ) ) ? parent::add_backslashes( $module_array['before_render'] ) : "",
						'_dp_dmb_globalassetsbox_features'                      => ( array_key_exists( 'global_assets', $module_array ) ) ? $module_array['global_assets'] : array(),
						'_dp_dmb_globalassetsbox_modules'                       => ( array_key_exists( 'module_assets', $module_array ) ) ? $module_array['module_assets'] : array(),
					),
				);
				if ( $data['replace_name'] === 'true' ) {
					$args['post_title'] = $module_array['name'];
				}
				$id = wp_update_post( $args );
				if ( ! is_wp_error( $id ) ) {
					parent::dmb_write_log( 'Successfully updated module ' . $id );
					echo 'OK';
				} else {
					parent::dmb_write_log( 'Error when try to update module' );
					echo esc_html( __( 'Error on update the post' . $id, 'dp_divi_module_builder' ) );
				}
			} else {
				parent::dmb_write_log( 'Error at retrieve the file content' );
				echo esc_html( __( 'Fail to retrieve the file content', 'dp_divi_module_builder' ) );
			}
		} else {
			parent::dmb_write_log( $data['file']['error'] );
			echo esc_html( $data['file']['error'] );
		}
		wp_die();
	}

	/**
	 * Ajax function that handle the process of clone a custom modules
	 *
	 * @since 2.0.0
	 */
	public function ajax_process_clone_module() {
		// phpcs:ignore
		if ( isset( $_POST['id'] ) && ! empty( $_POST['id'] ) ) {
			// phpcs:ignore
			$id = intval( $_POST['id'] );
			$id = wp_insert_post(
				array(
					'post_title' => get_post_field( 'post_title', $id ) . " " . __( '(copy)', 'dp_divi_module_builder' ),
					'post_type'  => 'dp_custom_modules',
					'meta_input' => array(
						'_dp_dmb_builder_version'                               => get_post_meta( $id, '_dp_dmb_builder_version', true ),
						'_dp_dmb_fieldbox_checkbox_fullwidth'                   => get_post_meta( $id, '_dp_dmb_fieldbox_checkbox_fullwidth', true ),
						'_dp_dmb_fieldbox_partial_support'                      => get_post_meta( $id, '_dp_dmb_fieldbox_partial_support', true ),
						'_dp_dmb_fieldbox_checkbox_tiny_mce'                    => get_post_meta( $id, '_dp_dmb_fieldbox_checkbox_tiny_mce', true ),
						'_dp_dmb_fieldbox_checkbox_dynamic_content'             => get_post_meta( $id, '_dp_dmb_fieldbox_checkbox_dynamic_content', true ),
						'_dp_dmb_fieldbox_checkbox_repeat_fields'               => get_post_meta( $id, '_dp_dmb_fieldbox_checkbox_repeat_fields', true ),
						'_dp_dmb_fieldbox_child_label'                          => get_post_meta( $id, '_dp_dmb_fieldbox_child_label', true ),
						'_dp_dmb_fieldbox_repeat_group_fields'                  => get_post_meta( $id, '_dp_dmb_fieldbox_repeat_group_fields', true ),
						'_dp_dmb_htmlbox_textarea_html_output'                  => parent::add_backslashes( get_post_meta( $id, '_dp_dmb_htmlbox_textarea_html_output', true ) ),
						'_dp_dmb_htmlbox_checkbox_php_onoff'                    => get_post_meta( $id, '_dp_dmb_htmlbox_checkbox_php_onoff', true ),
						'_dp_dmb_cssbox_textarea_css_output'                    => parent::add_backslashes( get_post_meta( $id, '_dp_dmb_cssbox_textarea_css_output', true ) ),
						'_dp_dmb_jsbox_textarea_js_output'                      => parent::add_backslashes( get_post_meta( $id, '_dp_dmb_jsbox_textarea_js_output', true ) ),
						'_dp_dmb_htmlchildbox_textarea_htmlchild_output'        => parent::add_backslashes( get_post_meta( $id, '_dp_dmb_htmlchildbox_textarea_htmlchild_output', true ) ),
						'_dp_dmb_functionsbox_textarea_functions_output'        => parent::add_backslashes( get_post_meta( $id, '_dp_dmb_functionsbox_textarea_functions_output', true ) ),
						'_dp_dmb_beforerenderbox_textarea_before_render_output' => parent::add_backslashes( get_post_meta( $id, '_dp_dmb_beforerenderbox_textarea_before_render_output', true ) )
					),
				)
			);
			if ( ! is_wp_error( $id ) ) {
				parent::dmb_write_log( 'Successfully cloned module ' . $id );
				echo 'OK';
			} else {
				parent::dmb_write_log( 'Error at insert the post' );
				echo esc_html( __( 'Error on insert the post' . $id, 'dp_divi_module_builder' ) );
			}
		} else {
			parent::dmb_write_log( 'Error missing the module id' );
			echo esc_html( __( 'Missing the module id', 'dp_divi_module_builder' ) );
		}
		wp_die();
	}

	/**
	 * Ajax function that handle the process of delete a custom modules
	 *
	 * @since 2.1
	 */
	public function ajax_process_delete_module() {
		// phpcs:ignore
		if ( isset( $_POST['id'] ) && ! empty( $_POST['id'] ) ) {
			// phpcs:ignore
			$id = intval( $_POST['id'] );
			if ( ! is_wp_error( wp_trash_post( $id ) ) ) {
				parent::dmb_write_log( 'Successfully delete module ' . $id );
				echo 'OK';
			} else {
				parent::dmb_write_log( 'Error at delete the post' );
				echo esc_html( __( 'Error on delete the post' . $id, 'dp_divi_module_builder' ) );
			}
		} else {
			parent::dmb_write_log( 'Error missing the module id' );
			echo esc_html( __( 'Missing the module id', 'dp_divi_module_builder' ) );
		}
		wp_die();
	}

	/**
	 * Ajax function that handle the process of publish a custom modules
	 *
	 * @since 2.1
	 */
	public function ajax_process_publish_module() {
		// phpcs:ignore
		if ( isset( $_POST['id'] ) && ! empty( $_POST['id'] ) ) {
			// phpcs:ignore
			$id = intval( $_POST['id'] );
			$id = wp_publish_post( $id );
			if ( ! is_wp_error( $id ) ) {
				parent::dmb_write_log( 'Successfully publish module ' . $id );
				echo 'OK';
			} else {
				parent::dmb_write_log( 'Error at publish the post' );
				echo esc_html( __( 'Error on publish the post' . $id, 'dp_divi_module_builder' ) );
			}
		} else {
			parent::dmb_write_log( 'Error missing the module id' );
			echo esc_html( __( 'Missing the module id', 'dp_divi_module_builder' ) );
		}
		wp_die();
	}

	/**
	 * Ajax function that handle the process of php processing activation/deactivation of one module
	 *
	 * @since 2.0.0
	 */
	public function ajax_process_onoff_module() {
		// phpcs:ignore
		if ( isset( $_POST['id'] ) && ! empty( $_POST['id'] ) && isset( $_POST['php'] ) ) {
			// phpcs:ignore
			$id = intval( $_POST['id'] );
			// phpcs:ignore
			$php = sanitize_text_field( $_POST['php'] );
			( update_post_meta( $id, '_dp_dmb_htmlbox_checkbox_php_onoff', $php ) ) ? parent::dmb_write_log( 'Success at change php processing for module ' . $id ) : parent::dmb_write_log( 'Error at change php processing for module ' . $id );
			new DP_DMB_Custom_Module( array( 'id' => $id, 'php' => $php ) );
		} else {
			parent::dmb_write_log( 'Missing the module id or php state' );
			echo esc_html( __( 'Missing the module id or php state', 'dp_divi_module_builder' ) );
		}
		wp_die();
	}

	/**
	 * Ajax function that handle the process of php processing deactivation for all modules
	 *
	 * @since 2.0.0
	 */
	public function ajax_process_off_all_modules() {
		foreach ( parent::get_modules() as $module ) {
			if ( $module['php'] === 'on' ) {
				update_post_meta( $module['id'], '_dp_dmb_htmlbox_checkbox_php_onoff', '' );
				new DP_DMB_Custom_Module( array(
					'id'    => $module['id'],
					'title' => $module['name']
				) );
			}
		}
		wp_die();
	}

	/**
	 * Ajax function that handle savings the extra settings.
	 *
	 * @since 2.0.0
	 */
	public function ajax_save_settings() {
		if ( check_ajax_referer( "dp_dmb_save_extra_settings" ) && isset( $_POST['dp_dmb_global_settings'] ) ) {
			// phpcs:ignore
			$options                = $_POST['dp_dmb_global_settings'];
			$dp_dmb_global_settings = array(
				"show_partial_message" => $options['show_partial_message'] !== "" ? true : false,
			);
			update_option( 'dp_dmb_global_settings', $dp_dmb_global_settings );
			parent::send_json_response( 1, __( "Settings Saved", 'dp_divi_module_builder' ) );
		}
	}

	public function add_code_to_head() {
		if ( is_user_logged_in() ) {
			$dp_dmb_global_settings = get_option( 'dp_dmb_global_settings', $this->dp_dmb_global_default_settings );
			if ( isset( $dp_dmb_global_settings['show_partial_message'] ) && ! $dp_dmb_global_settings['show_partial_message'] ) {
				?>
                <!-- DMB hide partial compatibility support message -->
                <style>
                    .et-fb-modal__support-notice {
                        display: none !important;
                    }
                </style>
				<?php
			}
		}
	}

}
