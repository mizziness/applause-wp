<?php

/**
 * This class handle the import page related functions
 *
 * @since      2.1.0
 * @package    dp-divi-module-builder
 * @subpackage dp-divi-module-builder/includes
 * @author     DiviPlugins <support@diviplugins.com>
 */
class DP_DMB_Import extends DP_DMB_Utils_Functions {

	/**
	 * Add settings page to WP menu
	 *
	 * @since 2.1.0
	 */
	public function add_import_page() {
		add_submenu_page( 'edit.php?post_type=dp_custom_modules', __( 'Import', 'dp_divi_module_builder' ), __( 'Import', 'dp_divi_module_builder' ), 'manage_options', 'dp_dmb_import', array(
			$this,
			'import_page_html'
		) );
	}

	/**
	 * Html code of the import page
	 *
	 * @since 2.1.0
	 */
	public function import_page_html() {
		$import_from_library = false;
		$library_html        = "";
		$modules_html        = "";
		if ( get_option( 'dp_dmb_license_status' ) === 'valid' ) {
			if ( get_transient( 'dmb_license_transient' ) ) {
				$import_from_library = true;
			} else {
				if ( parent::check_license() ) {
					set_transient( 'dmb_license_transient', true, 2592000 );
					$import_from_library = true;
				}
			}
		}
		if ( $import_from_library ) {
			if ( get_transient( 'dmb_import_modules_cache' ) ) {
				$modules_data = get_transient( 'dmb_import_modules_cache' );
			} else {
				$modules_data = $this->get_divi_plugins_modules();
				if ( $modules_data ) {
					set_transient( 'dmb_import_modules_cache', $modules_data, 10800 );
				}
			}
			if ( $modules_data ) {
				if ( $modules_data->api_query_errors !== "OK" ) {
					$modules_html = sprintf( '<h2 class="dp_dmb_error_message">Error Message: %1$s</h2>', $modules_data->api_query_errors );
				} else {
					wp_localize_script( 'dp_dmb_page_import', 'dpDmbModules', (array) $modules_data );
					$user_modules     = "";
					$official_modules = "";
					foreach ( $modules_data->modules as $module ) {
						ob_start();
						if ( property_exists( $module, "category" ) ) {
							if ( $module->category === 'user-module' ) {
								$user_modules = $user_modules . $this->get_module_html( $module );
							} else {
								$official_modules = $official_modules . $this->get_module_html( $module );
							}
						}
					}
					$categories = sprintf( '<option value="%1$s">%2$s</option>', "all", __( "All Categories", 'dp_divi_module_builder' ) );
					foreach ( $modules_data->terms as $term ) {
						$categories = $categories . sprintf( '<option value="%1$s">%2$s</option>', $term->slug, $term->name );
					}
					echo et_core_intentionally_unescaped( sprintf( '<div id="dp_dmb_library_filters">'
					                                               . '<select id="dp_dmb_library_filter">%1$s</select>'
					                                               . '<input id="dp_dmb_library_search" placeholder="%2$s" type="text">'
					                                               . '</div>',
						$categories,
						__( "Search", 'dp_divi_module_builder' ) ), 'html' );
					echo et_core_intentionally_unescaped( sprintf( '<div id="dp_dmb_library_modules_tabs">'
					                                               . '<ul>'
					                                               . '<li><a href="#tabs-1">%1$s</a></li>'
					                                               /* . '<li><a href="#tabs-2">%2$s</a></li>' */
					                                               . '</ul>'
					                                               . '<div id="tabs-1">'
					                                               . '<div class="modules_grid">%2$s</div>'
					                                               . '<div class="pagination"></div>'
					                                               . '</div>'
					                                               /* . '<div id="tabs-2">'
																	 . '<div class="modules_disclamer">%5$s</div>'
																	 . '<div class="modules_grid">%4$s</div>'
																	 . '<div class="pagination"></div>'
																	 . '</div>' */
					                                               . '</div>',
						__( 'Official Modules', 'dp_divi_module_builder' ), /*
                              __('User Modules', 'dp_divi_module_builder'), */
						$official_modules/* ,
                              $user_modules,
                              __('<h1 style="text-align: center; font-weight: bold;">COMING SOON</h1>', 'dp_divi_module_builder') */
					), 'html' );
					$modules_html = ob_get_clean();
				}
			} else {
				$modules_html = sprintf( '<h2 class="dp_dmb_error_message">%1$s</h2>', __( 'Unable to connect to server. Please try again later or <a href="https://diviplugins.com/modules/" target="_blank">manually download modules from our website.</a>', 'dp_divi_module_builder' ) );
			}
			$library_html = et_core_intentionally_unescaped( sprintf( '<div id="dp_dmb_admin_page_modules_container">'
			                                                          . '<h3 class="dp_dmb_admin_page_section">%1$s</h3>'
			                                                          . '<div class="dp_dmb_library">'
			                                                          . '%2$s'
			                                                          . '</div>'
			                                                          . '</div>',
				__( 'Import Modules from DiviPlugins library', 'dp_divi_module_builder' ),
				$modules_html
			), 'html' );
		} else {
			$library_html = et_core_intentionally_unescaped( sprintf( '<div id="dp_dmb_admin_page_modules_container">'
			                                                          . '<h3 class="dp_dmb_admin_page_section">%1$s</h3>'
			                                                          . '<p>%2$s</p>'
			                                                          . '<p><a class="button" href="https:\\www.diviplugins.com" target="_blank">%3$s</a> '
			                                                          . '<a class="button" href="%4$s">%5$s</a></p>'
			                                                          . '</div>',
				__( 'Import Modules from DiviPlugins library', 'dp_divi_module_builder' ),
				__( 'It appears your license is not active. Please purchase a new license or enter an active license to continue.', 'dp_divi_module_builder' ),
				__( 'Purchase a new license', 'dp_divi_module_builder' ),
				admin_url( 'edit.php?post_type=dp_custom_modules&page=' . DPDMB_PLUGIN_LICENSE_PAGE ),
				__( 'Enter an active license', 'dp_divi_module_builder' )
			), 'html' );
		}

		echo et_core_intentionally_unescaped( sprintf( '<div class="wrap cmb2-options-page dp_dmb_options dp_dmb_wrapper" >'
		                                               . '<div id="dp_dmb_admin_page_header">'
		                                               . '<h2 class="dp_dmb_admin_page_title">%1$s</h2>'
		                                               . '<p class="dp_dmb_admin_page_description">%2$s</p>'
		                                               . '</div>'
		                                               . '%5$s'
		                                               . '<hr>'
		                                               . '<div id="dp_dmb_admin_page_import_form_container">'
		                                               . '<h3 class="dp_dmb_admin_page_section">%3$s</h3>'
		                                               . '<form id="dp_dmb_form_import" method="get" enctype="multipart/form-data">'
		                                               . '<input type="file" name="dp_dmb_form_import_file" id="dp_dmb_form_import_file" required="" accept="application/json,.json">'
		                                               . '<br>'
		                                               . '<input id="dp_dmb_form_import_button" type="submit" value="%4$s">'
		                                               . '</form>'
		                                               . '</div>'
		                                               . '</div>'
		                                               . '<div id="dp_dmb_loader_wrapper">'
		                                               . '<div id="dp_dmb_form_import_msg"><p></p>'
		                                               . '<div id="dp_dmb_form_import_actions">'
		                                               . '<a class="dp_dmb_admin_page_publish_link" href="#" data-module="">%6$s</a>'
		                                               . '<a class="dp_dmb_admin_page_edit_link" href="#">%7$s</a>'
		                                               . '<a class="dp_dmb_admin_page_close_link" href="#">%8$s</a>'
		                                               . '</div></div>'
		                                               . '<div class="dp_dmb_loader"></div>'
		                                               . '</div>',
			__( 'Divi Module Builder - Import', 'dp_divi_module_builder' ),
			__( 'Import a custom module from the diviplugins.com library or from a JSON file.', 'dp_divi_module_builder' ),
			__( 'Import Modules from JSON File', 'dp_divi_module_builder' ),
			__( 'Import Module File', 'dp_divi_module_builder' ),
			$library_html,
			__( 'Publish', 'dp_divi_module_builder' ),
			__( 'Edit', 'dp_divi_module_builder' ),
			__( 'Close', 'dp_divi_module_builder' )
		), 'html' );
	}

	/**
	 * Get the modules data
	 *
	 * @return boolean|Array
	 */
	public function get_divi_plugins_modules() {
		$response = wp_remote_get( DPDMB_MODULES_API_URL, array(
			'timeout'   => 30,
			'sslverify' => false
		) );
		if ( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) {
			$modules_data = json_decode( wp_remote_retrieve_body( $response ) );

			return $modules_data;
		} else {
			return false;
		}
	}

	public function get_module_html( $module ) {
		return et_core_intentionally_unescaped( sprintf( ''
		                                                 . '<div class="dp_dmb_module" data-cat="%8$s">'
		                                                 . '<img src="%5$s">'
		                                                 . '<h3 class="dp_dmb_library_title">%1$s</h3>'
		                                                 . '<p class="dp_dmb_library_description">%4$s</p>'
		                                                 . '<div class="dp_dmb_module_actions"><a class="button dp_dmb_library_import" data-module-id="%2$s">%3$s</a>'
		                                                 . '<a class="button dp_dmb_library_read_more" href="%6$s" target="_blank">%7$s</a></div>'
		                                                 . '</div>',
			$module->name,
			$module->id,
			__( "Import", 'dp_divi_module_builder' ),
			$module->description,
			$module->thumbnail,
			$module->link,
			__( "More Info", 'dp_divi_module_builder' ),
			implode( "|", $module->terms )
		), 'html' );
	}

	/**
	 * Ajax function that handle the process of import a custom module json file
	 *
	 * @since 2.0.0
	 */
	public function ajax_process_import_file() {
		// phpcs:ignore
		$file_data = isset( $_FILES['dp_dmb_form_import_file'] ) ? $_FILES['dp_dmb_form_import_file'] : array();
		if ( $file_data['error'] === 0 ) {
			// phpcs:ignore
			$json_file_data = file_get_contents( $file_data['tmp_name'] );
			if ( $json_file_data ) {
				$module_array = json_decode( $json_file_data, true );
				$id           = $this->insert_module_post( $module_array );
				if ( ! is_wp_error( $id ) ) {
					parent::send_json_response( 1, __( 'The module was successfully imported', 'dp_divi_module_builder' ), array( 'id' => $id ) );
				} else {
					parent::send_json_response( 0, __( 'Fail to insert the module post ' . $id, 'dp_divi_module_builder' ) );
				}
			} else {
				parent::send_json_response( 0, __( 'Fail to retrieve the file content', 'dp_divi_module_builder' ) );
			}
		} else {
			parent::send_json_response( 0, $file_data['error'] );
		}
	}

	/**
	 * Insert the module post type
	 *
	 * @param $module_array
	 *
	 * @return int|WP_Error
	 */
	public function insert_module_post( $module_array ) {
		return wp_insert_post(
			array(
				'post_title' => $module_array['name'],
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
					'_dp_dmb_fieldbox_checkbox_repeat_fields'                  => ( array_key_exists( 'unwrap_repeat_fields', $module_array ) ) ? $module_array['unwrap_repeat_fields'] : "",
					'_dp_dmb_fieldbox_child_label'                          => ( array_key_exists( 'child_label', $module_array ) ) ? $module_array['child_label'] : "",
					'_dp_dmb_htmlchildbox_textarea_htmlchild_output'        => ( array_key_exists( 'html_child', $module_array ) ) ? parent::add_backslashes( $module_array['html_child'] ) : "",
					'_dp_dmb_functionsbox_textarea_functions_output'        => ( array_key_exists( 'custom_functions', $module_array ) ) ? parent::add_backslashes( $module_array['custom_functions'] ) : "",
					'_dp_dmb_beforerenderbox_textarea_before_render_output' => ( array_key_exists( 'before_render', $module_array ) ) ? parent::add_backslashes( $module_array['before_render'] ) : "",
					'_dp_dmb_globalassetsbox_features'                      => ( array_key_exists( 'global_assets', $module_array ) ) ? $module_array['global_assets'] : array(),
					'_dp_dmb_globalassetsbox_modules'                       => ( array_key_exists( 'module_assets', $module_array ) ) ? $module_array['module_assets'] : array(),
				),
			)
		);
	}

	/**
	 * Ajax function that handle the process of import a custom module from library
	 *
	 * @since 2.1.0
	 */
	public function ajax_process_import_library_module() {
		// phpcs:ignore
		$module_array = isset( $_POST['module_data'] ) ? $_POST['module_data'] : array();
		array_walk_recursive( $module_array, function ( &$element, $index ) {
			$element = str_replace( array( "\\\\", "\\'", '\\"' ), array( "\\", "'", '"' ), $element );
		} );
		if ( isset( $module_array['fields'] ) && is_array( $module_array['fields'] ) ) {
			foreach ( $module_array['fields'] as $key => $all_fields ) {
				foreach ( $all_fields as $field_key => $field ) {
					if ( $field === "false" ) {
						$module_array['fields'][ $key ][ $field_key ] = 0;
					}
				}
			}
		}
		$id = $this->insert_module_post( $module_array );
		if ( ! is_wp_error( $id ) ) {
			parent::send_json_response( 1, __( 'The module was successfully imported', 'dp_divi_module_builder' ), array( 'id' => $id ) );
		} else {
			parent::send_json_response( 0, __( 'Fail to insert the module post ' . $id, 'dp_divi_module_builder' ) );
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
			$id = $_POST['id'];
			$id = wp_publish_post( $id );
			if ( ! is_wp_error( $id ) ) {
				parent::send_json_response( 1, __( 'The module was successfully published', 'dp_divi_module_builder' ) );
			} else {
				parent::send_json_response( 0, __( 'Fail to publish the module ' . $id, 'dp_divi_module_builder' ) );
			}
		} else {
			parent::send_json_response( 0, __( 'Import publish action missing the module ID', 'dp_divi_module_builder' ) );
		}
	}

}
