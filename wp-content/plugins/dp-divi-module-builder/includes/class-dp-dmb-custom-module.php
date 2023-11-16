<?php

class DP_DMB_Custom_Module extends DP_DMB_Utils_Functions {

	public $id, $builder_version, $builder_constructor_version, $title, $fields, $html, $php, $css, $js, $fullwidth, $tiny_mce, $dynamic_content, $child_label, $html_output_child, $functions, $before_render, $global_assets, $module_assets, $partial_support, $have_childs, $advanced_fields, $child_advanced_fields, $custom_css, $child_custom_css, $fields_groups, $unwrap_repeat_fields;

	public function __construct( $args ) {
		$this->builder_constructor_version = '2';
		$this->id                          = ( array_key_exists( 'id', $args ) ) ? $args['id'] : die( 'Missing custom module post id when try to initiate the module maker class' );
		$this->builder_version             = ( array_key_exists( 'builder_version', $args ) ) ? $args['builder_version'] : get_post_meta( $this->id, '_dp_dmb_builder_version', true );
		$this->title                       = ( array_key_exists( 'title', $args ) ) ? esc_html( $args['title'] ) : esc_html( get_post_field( 'post_title', $this->id ) );
		$this->fullwidth                   = ( array_key_exists( 'fullwidth', $args ) ) ? $args['fullwidth'] : get_post_meta( $this->id, '_dp_dmb_fieldbox_checkbox_fullwidth', true );
		$this->tiny_mce                    = ( array_key_exists( 'tiny_mce', $args ) ) ? $args['tiny_mce'] : get_post_meta( $this->id, '_dp_dmb_fieldbox_checkbox_tiny_mce', true );
		$this->dynamic_content             = ( array_key_exists( 'dynamic_content', $args ) ) ? $args['dynamic_content'] : get_post_meta( $this->id, '_dp_dmb_fieldbox_checkbox_dynamic_content', true );
		$this->unwrap_repeat_fields        = ( array_key_exists( 'unwrap_repeat_fields', $args ) ) ? $args['unwrap_repeat_fields'] : get_post_meta( $this->id, '_dp_dmb_fieldbox_checkbox_repeat_fields', true );
		$this->child_label                 = ( array_key_exists( 'child_label', $args ) ) ? $args['child_label'] : get_post_meta( $this->id, '_dp_dmb_fieldbox_child_label', true );
		$this->html                        = ( array_key_exists( 'html', $args ) ) ? $args['html'] : get_post_meta( $this->id, '_dp_dmb_htmlbox_textarea_html_output', true );
		$this->fields                      = ( array_key_exists( 'fields', $args ) ) ? $args['fields'] : get_post_meta( $this->id, '_dp_dmb_fieldbox_repeat_group_fields', true );
		$this->fields_groups               = $this->process_fields_groups();
		$this->php                         = ( array_key_exists( 'php', $args ) ) ? $args['php'] : get_post_meta( $this->id, '_dp_dmb_htmlbox_checkbox_php_onoff', true );
		$this->css                         = ( array_key_exists( 'css', $args ) ) ? $args['css'] : get_post_meta( $this->id, '_dp_dmb_cssbox_textarea_css_output', true );
		$this->js                          = ( array_key_exists( 'js', $args ) ) ? $args['js'] : get_post_meta( $this->id, '_dp_dmb_jsbox_textarea_js_output', true );
		$this->html_output_child           = ( array_key_exists( 'html_output_child', $args ) ) ? $args['html_output_child'] : get_post_meta( $this->id, '_dp_dmb_htmlchildbox_textarea_htmlchild_output', true );
		$this->functions                   = ( array_key_exists( 'functions', $args ) ) ? $args['functions'] : get_post_meta( $this->id, '_dp_dmb_functionsbox_textarea_functions_output', true );
		$this->before_render               = ( array_key_exists( 'before_render', $args ) ) ? $args['before_render'] : get_post_meta( $this->id, '_dp_dmb_beforerenderbox_textarea_before_render_output', true );
		$this->global_assets               = ( array_key_exists( 'global_assets', $args ) ) ? $args['global_assets'] : get_post_meta( $this->id, '_dp_dmb_globalassetsbox_features', true );
		$this->module_assets               = ( array_key_exists( 'module_assets', $args ) ) ? $args['module_assets'] : get_post_meta( $this->id, '_dp_dmb_globalassetsbox_modules', true );
		$this->have_childs                 = false;
		$this->partial_support             = ( array_key_exists( 'partial_support', $args ) ) ? $args['partial_support'] : get_post_meta( $this->id, '_dp_dmb_fieldbox_partial_support', true );
		$this->advanced_fields             = array(
			'background'     => array(
				'use_background_image' => 'true',
				'use_background_video' => 'true',
			),
			'text'           => 'array("use_background_layout" => true, "options" => array("background_layout" => array("default" => "light",),), "css" => array("text_orientation"=> "%%order_class%%", "background_layout"=> "%%order_class%%"))',
			'borders'        => 'array("default" => array("css" => array("main" => "%%order_class%%",)))',
			'box_shadow'     => 'array("default" => array("css" => array("main" => "%%order_class%%",)))',
			'button'         => 'array()',
			'filters'        => 'array()',
			'fonts'          => 'array()',
			'margin_padding' => 'array()',
			'max_width'      => 'array()',
			'animation'      => 'array()'
		);
		$this->child_advanced_fields       = array(
			'background'     => array(
				'use_background_image' => 'true',
				'use_background_video' => 'true',
			),
			'text'           => 'array("use_background_layout" => false , "css" => array("text_orientation"=> "%%order_class%%"))',
			'borders'        => 'array("default" => array("css" => array("main" => "%%order_class%%",)))',
			'box_shadow'     => 'array("default" => array("css" => array("main" => "%%order_class%%",)))',
			'button'         => 'array()',
			'filters'        => 'array()',
			'fonts'          => 'array()',
			'margin_padding' => 'array()',
			'max_width'      => 'array()',
			'animation'      => 'array()'
		);
		$this->write_module_files();
	}

	public function process_fields_groups() {
		$custom_groups = array();
		$counter       = 0;
		if ( ! empty( $this->fields ) ) {
			foreach ( $this->fields as $field ) {
				if ( array_key_exists( 'group', $field ) && $field['group'] !== "" && ! in_array( $field['group'], $custom_groups ) ) {
					$counter ++;
					$custom_groups[ 'group_' . $counter ] = htmlentities( $field['group'] );
				}
			}
		}

		return $custom_groups;
	}

	public function write_module_files() {
		/*
		 * Write js and css file too. Get the enqueue string of the js and css files for the modules class string
		 */
		$module_css = $this->write_css_file();
		$module_js  = $this->write_js_file();
		/*
		 * Retrieve properties of parent and child modules fields
		 */
		$fields_data        = $this->create_fields_data();
		$fields_data_parent = array();
		$fields_data_child  = array();
		if ( $this->have_childs ) {
			foreach ( $fields_data as $value ) {
				if ( $value['is_child'] === 'on' ) {
					$fields_data_child[] = $value;
				} else {
					$fields_data_parent[] = $value;
				}
			}
		} else {
			foreach ( $fields_data as $value ) {
				$fields_data_parent[] = $value;
			}
		}
		/*
		 * Create fields strings
		 */
		$fields_strings = $this->create_fields_string( $fields_data_parent, false );
		if ( $this->have_childs ) {
			$fields_strings_childs = $this->create_fields_string( $fields_data_child, true );
		} else {
			$fields_strings_childs = '';
		}
		/*
		 * Write JSON export file and run backward compatibility tasks
		 */
		$this->backward_compatibility_tasks();
		$this->write_json_export_file();
		/*
		 * Activate PHP processing ([divi_php] will still work for backward compatibility)
		 */
		if ( $this->php === 'on' ) {
			$this->html = str_replace( array(
				'[divi_php]',
				'[/divi_php]'
			), array( '<?php ', ' ?>' ), $this->html );
			if ( $this->have_childs && ! empty( $this->html_output_child ) ) {
				$this->html_output_child = str_replace( array(
					'[divi_php]',
					'[/divi_php]'
				), array( '<?php ', ' ?>' ), $this->html_output_child );
			}
		} else {
			$this->html = str_replace( array( '<?php', '?>' ), array(
				'[divi_php]',
				'[/divi_php]'
			), $this->html );
			if ( $this->have_childs && ! empty( $this->html_output_child ) ) {
				$this->html_output_child = str_replace( array(
					'<?php',
					'?>'
				), array( '[divi_php]', '[/divi_php]' ), $this->html_output_child );
			}
			$this->functions = '';
		}
		/*
		 * Activate childs modules
		 */
		if ( $this->have_childs ) {
			$this->html       = str_replace( array(
				'%%repeat_fields%%',
				'$repeat_fields'
			), array(
				' <?php echo $this->content; ?> ',
				' echo $this->content; '
			), $this->html );
			$child_properties = '$this->child_slug = "et_pb_dp_dmb_module_' . $this->id . '_item";
            $this->child_item_text = __("Item");';
		} else {
			$child_properties = '';
		}
		/*
		 * Activate full width module
		 */
		if ( $this->fullwidth === 'on' ) {
			$this->fullwidth = '$this->fullwidth = true;';
		} else {
			$this->fullwidth = '';
		}
		/*
		 * Activate partial support
		 */
		if ( $this->partial_support === 'on' ) {
			$this->partial_support = 'public $vb_support = "partial";';
		} else {
			$this->partial_support = 'public $vb_support = "off";';
		}
		/*
		 * Add tiny_mce field to the html output. If the module have child to the child output.
		 */
		if ( $this->tiny_mce === 'on' ) {
			$have_tiny_mce = '"content" => array(
	"label" => esc_html__( "Content", "dp_dmb" ),
	"type" => "tiny_mce",
	"description" => esc_html__( "Here you can create the content that will be used within the module.", "dp_dmb" ),
	"toggle_slug" => "content",
	),';
			$tiny_mce_code = 'echo "<div class=\'dp_field_tinymce\'>".$this->content."</div>";';
			if ( $this->have_childs ) {
				$have_tiny_mce_child     = $have_tiny_mce;
				$this->html_output_child = str_replace( array(
					'%%tiny_mce%%',
					'$tiny_mce'
				), array(
					' <?php ' . $tiny_mce_code . ' ?> ',
					$tiny_mce_code
				), $this->html_output_child );
				$have_tiny_mce           = '';
			} else {
				$this->html = str_replace( array(
					'%%tiny_mce%%',
					'$tiny_mce'
				), array(
					' <?php ' . $tiny_mce_code . ' ?> ',
					$tiny_mce_code
				), $this->html );
			}
		} else {
			$have_tiny_mce       = '';
			$have_tiny_mce_child = '';
		}
		/*
		 * Replacing fields strings on the html output
		 */
		foreach ( $fields_data as $this_field ) {
			$this_field_responsive = $this_field['is_responsive'] === 'on';
			switch ( $this_field['type'] ) {
				case 'button':
					if ( $this_field['btn_link'] === 'on' ) {
						$button_code = $this_field['php'] . '_output = str_replace("button_permalink", get_the_permalink(), ' . $this_field['php'] . ');
                         echo ' . $this_field['php'] . '_output; ';
					} else {
						$button_code = 'echo ' . $this_field['php'] . '; ';
					}
					if ( $this_field['is_child'] === 'on' ) {
						$this->html_output_child = str_replace( $this_field['html'], ' <?php ' . $button_code . ' ?> ', $this->html_output_child );
					} else {
						$this->html = str_replace( $this_field['html'], ' <?php ' . $button_code . ' ?> ', $this->html );
					}
					break;
				case 'gallery':
				case 'video':
				case 'audio':
					if ( $this_field['is_child'] === 'on' ) {
						$this->html_output_child = str_replace( $this_field['html'], "<?php echo " . $this_field['php'] . "_html_output; ?>", $this->html_output_child );
					} else {
						$this->html = str_replace( $this_field['html'], "<?php echo " . $this_field['php'] . "_html_output; ?>", $this->html );
					}
					break;
				default:
					if ( $this_field['is_child'] === 'on' ) {
						$this->html_output_child = str_replace( $this_field['html'], "<?php echo " . $this_field['php'] . "; ?>", $this->html_output_child );
						if ( $this_field_responsive ) {
							$this->html_output_child = str_replace( '%%' . $this_field['id'] . '_tablet%%', "<?php echo " . $this_field['php'] . "_tablet; ?>", $this->html_output_child );
							$this->html_output_child = str_replace( '%%' . $this_field['id'] . '_phone%%', "<?php echo " . $this_field['php'] . "_phone; ?>", $this->html_output_child );
						}
					} else {
						$this->html = str_replace( $this_field['html'], "<?php echo " . $this_field['php'] . "; ?>", $this->html );
						if ( $this_field_responsive ) {
							$this->html = str_replace( '%%' . $this_field['id'] . '_tablet%%', "<?php echo " . $this_field['php'] . "_tablet; ?>", $this->html );
							$this->html = str_replace( '%%' . $this_field['id'] . '_phone%%', "<?php echo " . $this_field['php'] . "_phone; ?>", $this->html );
						}
					}
					break;
			}
		}
		/*
		 * Creating php file for include
		 */
		$module_string = $this->create_parent_module_class( $child_properties, $fields_strings, $have_tiny_mce, $module_css, $module_js );
		if ( $this->have_childs ) {
			$module_string .= $this->create_child_module_class( $fields_strings_childs, $have_tiny_mce_child );
		}
		parent::write_file( DPDMB_MODULES_DIR . '/modules/dp_custom_module_' . $this->id . '.php', $module_string );
	}

	public function write_json_export_file() {
		parent::write_file( DPDMB_MODULES_DIR . '/export/dp_custom_module_' . $this->id . '.json', wp_json_encode( array(
			'id'                   => $this->id,
			'builder_version'      => $this->builder_constructor_version,
			'name'                 => $this->title,
			'php'                  => $this->php,
			'html'                 => $this->html,
			'fields'               => $this->fields,
			'js'                   => $this->js,
			'css'                  => $this->css,
			'fullwidth'            => $this->fullwidth,
			'tiny_mce'             => $this->tiny_mce,
			'dynamic_content'      => $this->dynamic_content,
			'unwrap_repeat_fields' => $this->unwrap_repeat_fields,
			'child_label'          => $this->child_label,
			'html_child'           => $this->html_output_child,
			'custom_functions'     => $this->functions,
			'partial_support'      => $this->partial_support,
			'before_render'        => $this->before_render,
			'global_assets'        => $this->global_assets,
			'module_assets'        => $this->module_assets
		) ) );
	}

	public function write_css_file() {
		$module_css = '';
		$file_path  = DPDMB_MODULES_DIR . '/css/dp_custom_module_' . $this->id . '.css';
		if ( ! empty( $this->css ) ) {
			parent::write_file( $file_path, $this->css );
			$module_css = ' wp_enqueue_style("dmb-module-' . $this->id . '"); ';
		} else {
			parent::remove_file( $file_path );
		}

		return $module_css;
	}

	public function write_js_file() {
		$module_js = '';
		$file_path = DPDMB_MODULES_DIR . '/js/dp_custom_module_' . $this->id . '.js';
		if ( ! empty( $this->js ) ) {
			parent::write_file( $file_path, $this->js );
			$module_js = ' wp_enqueue_script("dmb-module-' . $this->id . '"); ';
		} else {
			parent::remove_file( $file_path );
		}

		return $module_js;
	}

	public function create_fields_data() {
		$fields_data = array();
		if ( ! empty( $this->fields ) ) {
			foreach ( $this->fields as $field ) {
				if ( array_key_exists( 'field_id', $field ) && ! empty( $field['field_id'] ) && ! preg_match( '/[^a-z0-9_]/', $field['field_id'] ) ) {
					$description       = array_key_exists( 'description', $field ) ? $field['description'] : "";
					$label             = array_key_exists( 'label', $field ) ? $field['label'] : "";
					$design            = array_key_exists( 'field_show_design', $field ) ? $field['field_show_design'] : "";
					$custom_css        = array_key_exists( 'field_custom_css', $field ) ? $field['field_custom_css'] : "";
					$btn_permalink     = array_key_exists( 'field_buttom_permalink', $field ) ? $field['field_buttom_permalink'] : "";
					$hide              = array_key_exists( 'field_hide_content', $field ) ? $field['field_hide_content'] : "";
					$select_options    = array_key_exists( 'field_select_options', $field ) ? $field['field_select_options'] : "";
					$select_values     = array_key_exists( 'field_select_options_values', $field ) ? $field['field_select_options_values'] : "";
					$select_function   = array_key_exists( 'field_select_options_function', $field ) ? $field['field_select_options_function'] : "";
					$allow_date_format = array_key_exists( 'field_allow_date_format', $field ) ? $field['field_allow_date_format'] : "";
					$is_child          = array_key_exists( 'field_child', $field ) ? $field['field_child'] : "";
					$is_responsive     = array_key_exists( 'field_responsive', $field ) ? $field['field_responsive'] : "";
					$default_text      = array_key_exists( 'field_default_text', $field ) ? $field['field_default_text'] : "";
					$default_textarea  = array_key_exists( 'field_default_textarea', $field ) ? $field['field_default_textarea'] : "";
					$default_button    = array_key_exists( 'field_default_button', $field ) ? $field['field_default_button'] : "";
					$default_color     = array_key_exists( 'field_default_color', $field ) ? $field['field_default_color'] : "";
					$default_state     = array_key_exists( 'field_default_state', $field ) ? $field['field_default_state'] : "";
					$default_image     = array_key_exists( 'field_default_image', $field ) ? $field['field_default_image'] : "";
					$default_image_url = array_key_exists( 'field_default_image_url', $field ) ? $field['field_default_image_url'] : "";
					$checkbox_values   = array_key_exists( 'field_checkbox_values', $field ) ? $field['field_checkbox_values'] : "";
					$process_checkbox  = array_key_exists( 'field_checkbox_process', $field ) ? $field['field_checkbox_process'] : "";
					$field_group       = ( array_key_exists( 'group', $field ) && $field['group'] !== "" ) ? array_search( $field['group'], $this->fields_groups ) : "content";
					array_push( $fields_data, array(
							'html'              => '%%' . $field['field_id'] . '%%',
							'php'               => '$' . $field['field_id'],
							'type'              => $field['type'],
							'group'             => $field_group,
							'id'                => $field['field_id'],
							'label'             => ( empty( $label ) ) ? strtoupper( $field['field_id'] ) : $label,
							'description'       => ( empty( $description ) ) ? "" : $description,
							'design'            => ( ! $design ) ? 'off' : $design,
							'custom_css'        => ( ! $custom_css ) ? 'off' : $custom_css,
							'btn_link'          => ( ! $btn_permalink ) ? 'off' : $btn_permalink,
							'hide'              => ( ! $hide ) ? 'off' : $hide,
							'select_options'    => ( empty( $select_options ) ) ? "" : $select_options,
							'select_values'     => ( empty( $select_values ) ) ? "" : $select_values,
							'checkbox_values'   => ( empty( $checkbox_values ) ) ? "" : $checkbox_values,
							'process_checkbox'  => ( ! $process_checkbox ) ? 'off' : $process_checkbox,
							'select_function'   => ( empty( $select_function ) ) ? "" : $select_function,
							'allow_date_format' => ( ! $allow_date_format ) ? 'off' : $allow_date_format,
							'is_child'          => ( ! $is_child ) ? 'off' : $is_child,
							'is_responsive'     => ( ! $is_responsive ) ? 'off' : $is_responsive,
							'default_text'      => ( empty( $default_text ) ) ? "" : esc_html( $default_text ),
							'default_textarea'  => ( empty( $default_textarea ) ) ? "" : esc_html( $default_textarea ),
							'default_button'    => ( empty( $default_button ) ) ? "" : esc_html( $default_button ),
							'default_color'     => $default_color,
							'default_state'     => ( ! $default_state ) ? 'off' : $default_state,
							'default_image'     => ( ! $default_image ) ? 'off' : $default_image,
							'default_image_url' => ( ! $default_image_url ) ? '' : $default_image_url,
						)
					);
					if ( $is_child === 'on' ) {
						$this->have_childs = true;
					}
				}
			}
		}

		return $fields_data;
	}

	public function create_responsive_vars( $field_identifier, $responsive ) {
		if ( $responsive === 'true' ) {
			return '$' . $field_identifier . '_tablet = $this->props["' . $field_identifier . '_tablet"]; $' . $field_identifier . '_phone = $this->props["' . $field_identifier . '_phone"];';
		} else {
			return '';
		}
	}

	public function create_fields_string( $fields_data, $is_for_child ) {
		$fields_string = array(
			'get_fields'      => '',
			'shortcode_attrs' => '',
			'add_code'        => ''
		);
		if ( $is_for_child ) {
			$this->child_advanced_fields['button'] = 'array(';
			$this->child_advanced_fields['fonts']  = 'array(';
			$this->child_custom_css                = 'array(';
		} else {
			$this->advanced_fields['button'] = 'array(';
			$this->advanced_fields['fonts']  = 'array(';
			$this->custom_css                = 'array(';
		}
		/*
		 * Dynamic Content Support
		 */
		$dynamic_content_text  = '';
		$dynamic_content_image = '';
		$dynamic_content_url   = '';
		if ( $this->dynamic_content === 'on' ) {
			$dynamic_content_text  = '"dynamic_content" => "text",';
			$dynamic_content_image = '"dynamic_content" => "image",';
			$dynamic_content_url   = '"dynamic_content" => "url",';
		}
		foreach ( $fields_data as $field ) {
			$field['label']       = htmlentities( $field['label'] );
			$field['description'] = htmlentities( $field['description'] );
			$responsive           = $field['is_responsive'] === 'on' ? 'true' : 'false';
			/* Custom CSS on Advanced Tab */
			if ( ( $field['type'] === 'text' || $field['type'] === 'textarea' || $field['type'] === 'image' || $field['type'] === 'icon' || $field['type'] === 'button' ) && $field['custom_css'] === 'on' ) {
				$custom_css_string = ' "' . $field['id'] . '" => array("label"    => __("' . $field['label'] . '", "dp_dmb" ), "selector" => ".dp_field_' . $field['id'] . '",),';
				if ( $field['is_child'] !== 'on' ) {
					$this->custom_css .= $custom_css_string;
				} else {
					$this->child_custom_css .= $custom_css_string;
				}
			}
			/*
			 * Fields string declarations
			 */
			switch ( $field['type'] ) {
				case 'text':
					if ( $field['hide'] !== 'on' ) {
						$fields_string['get_fields']      .= '
                        "' . $field['id'] . '" => array(
                            "label" => __("' . $field['label'] . '", "dp_dmb"),
                            "type" => "' . $field['type'] . '",                         
                            "mobile_options" => ' . $responsive . ',   
                            "default" => __( "' . $field['default_text'] . '", "dp_dmb" ),
                            ' . $dynamic_content_text . '
                            "tab_slug" => "general",
                            "toggle_slug" => "' . $field['group'] . '",
                            "description" => __("' . $field['description'] . '", "dp_dmb"),
                        ),';
						$fields_string['shortcode_attrs'] .= '$' . $field['id'] . ' = $this->props["' . $field['id'] . '"]; ' . $this->create_responsive_vars( $field['id'], $responsive );
					}
					if ( $field['design'] === 'on' ) {
						$fonts_syles_string = '
                            "' . $field['id'] . '" => array(
                                "label" =>  __("' . $field['label'] . '", "dp_dmb"), 
                                "css" => array("main" => "{$this->main_css_element} .dp_field_' . $field['id'] . '", "important" => "all",), 
                                "line_height" => array("default" => floatval( et_get_option( "body_font_height", "1.7" ) ) . "em",), 
                                "font_size" => array("default" => absint( et_get_option( "body_font_size", "14" ) ) . "px",),                                
                            ),';
						if ( $field['is_child'] === 'on' ) {
							$this->child_advanced_fields['fonts'] .= $fonts_syles_string;
						} else {
							$this->advanced_fields['fonts'] .= $fonts_syles_string;
						}
					}
					break;
				case 'textarea':
					if ( $field['hide'] !== 'on' ) {
						$fields_string['get_fields']      .= '
                        "' . $field['id'] . '" => array(
                            "label" => __("' . $field['label'] . '", "dp_dmb"),
                            "type" => "' . $field['type'] . '",     
                            "mobile_options" => ' . $responsive . ',                       
                            "default" => __( "' . $field['default_textarea'] . '", "dp_dmb" ),
                            ' . $dynamic_content_text . '
                            "tab_slug" => "general",
                            "toggle_slug" => "' . $field['group'] . '",
                            "description" => __("' . $field['description'] . '", "dp_dmb"),
                        ),';
						$fields_string['shortcode_attrs'] .= '$' . $field['id'] . ' = $this->props["' . $field['id'] . '"]; ' . $this->create_responsive_vars( $field['id'], $responsive );
					}
					if ( $field['design'] === 'on' ) {
						$fonts_syles_string = '
                            "' . $field['id'] . '" => array(
                                "label" =>  __("' . $field['label'] . '", "dp_dmb"), 
                                "css" => array("main" => "{$this->main_css_element} .dp_field_' . $field['id'] . '", "important" => "all",), 
                                "line_height" => array("default" => floatval( et_get_option( "body_font_height", "1.7" ) ) . "em",), 
                                "font_size" => array("default" => absint( et_get_option( "body_font_size", "14" ) ) . "px",),                                
                            ),';
						if ( $field['is_child'] === 'on' ) {
							$this->child_advanced_fields['fonts'] .= $fonts_syles_string;
						} else {
							$this->advanced_fields['fonts'] .= $fonts_syles_string;
						}
					}
					break;
				case 'link':
					if ( $field['hide'] !== 'on' ) {
						$fields_string['get_fields']      .= '
                        "' . $field['id'] . '" => array(
                            "label" => __("' . $field['label'] . '", "dp_dmb"),
                            "type" => "text",                            
                            "default" => __( "' . $field['default_text'] . '", "dp_dmb" ),
                            ' . $dynamic_content_url . '
                            "tab_slug" => "general",
                            "toggle_slug" => "' . $field['group'] . '",
                            "description" => __("' . $field['description'] . '", "dp_dmb"),
                        ),';
						$fields_string['shortcode_attrs'] .= '$' . $field['id'] . ' = $this->props["' . $field['id'] . '"]; ';
					}
					if ( $field['design'] === 'on' ) {
						$fonts_syles_string = '
                            "' . $field['id'] . '" => array(
                                "label" =>  __("' . $field['label'] . '", "dp_dmb"), 
                                "css" => array("main" => "{$this->main_css_element} .dp_field_' . $field['id'] . '", "important" => "all",), 
                                "line_height" => array("default" => floatval( et_get_option( "body_font_height", "1.7" ) ) . "em",), 
                                "font_size" => array("default" => absint( et_get_option( "body_font_size", "14" ) ) . "px",),                                
                            ),';
						if ( $field['is_child'] === 'on' ) {
							$this->child_advanced_fields['fonts'] .= $fonts_syles_string;
						} else {
							$this->advanced_fields['fonts'] .= $fonts_syles_string;
						}
					}
					break;
				case 'color':
					$fields_string['get_fields']      .= '
                    "' . $field['id'] . '" => array(
                        "label" => __("' . $field['label'] . '", "dp_dmb"),
                        "type" => "color-alpha",                         
                        "mobile_options" => ' . $responsive . ',   
                        "custom_color" => true,
                        "default" => "' . $field['default_color'] . '",
                        "tab_slug" => "general",
                         "toggle_slug" => "' . $field['group'] . '",
                        "description" => __("' . $field['description'] . '", "dp_dmb"),
                        ),';
					$fields_string['shortcode_attrs'] .= '$' . $field['id'] . ' = $this->props["' . $field['id'] . '"]; ' . $this->create_responsive_vars( $field['id'], $responsive );
					break;
				case 'image':
					$default_image = '';
					if ( $field['default_image'] !== 'off' ) {
						if ( $field['default_image_url'] === '' ) {
							$default_image = "data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTA4MCIgaGVpZ2h0PSI1NDAiIHZpZXdCb3g9IjAgMCAxMDgwIDU0MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KICAgIDxnIGZpbGw9Im5vbmUiIGZpbGwtcnVsZT0iZXZlbm9kZCI+CiAgICAgICAgPHBhdGggZmlsbD0iI0VCRUJFQiIgZD0iTTAgMGgxMDgwdjU0MEgweiIvPgogICAgICAgIDxwYXRoIGQ9Ik00NDUuNjQ5IDU0MGgtOTguOTk1TDE0NC42NDkgMzM3Ljk5NSAwIDQ4Mi42NDR2LTk4Ljk5NWwxMTYuMzY1LTExNi4zNjVjMTUuNjItMTUuNjIgNDAuOTQ3LTE1LjYyIDU2LjU2OCAwTDQ0NS42NSA1NDB6IiBmaWxsLW9wYWNpdHk9Ii4xIiBmaWxsPSIjMDAwIiBmaWxsLXJ1bGU9Im5vbnplcm8iLz4KICAgICAgICA8Y2lyY2xlIGZpbGwtb3BhY2l0eT0iLjA1IiBmaWxsPSIjMDAwIiBjeD0iMzMxIiBjeT0iMTQ4IiByPSI3MCIvPgogICAgICAgIDxwYXRoIGQ9Ik0xMDgwIDM3OXYxMTMuMTM3TDcyOC4xNjIgMTQwLjMgMzI4LjQ2MiA1NDBIMjE1LjMyNEw2OTkuODc4IDU1LjQ0NmMxNS42Mi0xNS42MiA0MC45NDgtMTUuNjIgNTYuNTY4IDBMMTA4MCAzNzl6IiBmaWxsLW9wYWNpdHk9Ii4yIiBmaWxsPSIjMDAwIiBmaWxsLXJ1bGU9Im5vbnplcm8iLz4KICAgIDwvZz4KPC9zdmc+Cg==";
						} else {
							$default_image = esc_url( $field['default_image_url'] );
						}
					}
					$fields_string['get_fields']      .= '
                    "' . $field['id'] . '" => array(
                        "label" => __("' . $field['label'] . '", "dp_dmb"), 
                        "type" => "upload",                         
                        "mobile_options" => ' . $responsive . ',   
                        "upload_button_text" => __( "Upload an image", "dp_dmb" ),
                        "choose_text" => __( "Choose an Image", "dp_dmb" ),
                        "update_text" => __( "Set As Image", "dp_dmb" ),
                        "default" =>"' . $default_image . '",
                        ' . $dynamic_content_image . '
                        "tab_slug" => "general",
                        "toggle_slug" => "' . $field['group'] . '",
                        "description" => __("' . $field['description'] . '", "dp_dmb"),
                        ),';
					$fields_string['shortcode_attrs'] .= '$' . $field['id'] . ' = $this->props["' . $field['id'] . '"]; ' . $this->create_responsive_vars( $field['id'], $responsive );
					break;
				case 'gallery':
					$fields_string['get_fields']      .= '
                    "' . $field['id'] . '" => array(
                        "label" => __("' . $field['label'] . '", "dp_dmb"), 
                        "type" => "upload-gallery",
                        "tab_slug" => "general",
                        "toggle_slug" => "' . $field['group'] . '",
                        "description" => __("' . $field['description'] . '", "dp_dmb"),
                        ),';
					$fields_string['add_code']        .= '     
                        /* Processing Gallery: ' . $field['id'] . ' */
                        $' . $field['id'] . '_html_output = "";
						$gallery_images = explode( ",", $' . $field['id'] . ' );
						foreach ( $gallery_images as $image_id ) {
							$' . $field['id'] . '_html_output .= wp_get_attachment_image( $image_id, "full" );
						}
                       /* End Processing Gallery: ' . $field['id'] . ' */
                      ';
					$fields_string['shortcode_attrs'] .= '$' . $field['id'] . ' = $this->props["' . $field['id'] . '"]; ';
					break;
				case 'yesno':
					$fields_string['get_fields']      .= '
                    "' . $field['id'] . '" => array(
                        "label" => __("' . $field['label'] . '", "dp_dmb"),
                        "type" => "yes_no_button",                         
                        "mobile_options" => ' . $responsive . ',   
                        "options" => array(
                            "off" => __("No", "dp_dmb"),
                            "on" => __("Yes", "dp_dmb"),
                        ),
                        "default" => "' . $field['default_state'] . '",
                        "tab_slug" => "general",
                        "toggle_slug" => "' . $field['group'] . '",
                        "description" => __("' . $field['description'] . '", "dp_dmb"),
                        ),';
					$fields_string['shortcode_attrs'] .= '$' . $field['id'] . ' = $this->props["' . $field['id'] . '"]; ' . $this->create_responsive_vars( $field['id'], $responsive );
					break;
				case 'icon':
					$fields_string['get_fields']      .= '
                    "' . $field['id'] . '" => array(
                        "label" => __("' . $field['label'] . '", "dp_dmb"),
                        "type" => "select_icon",    
                        "class" => array( "et-pb-font-icon" ),                        
                        "default" => "%%297%%",
                        "tab_slug" => "general",
                        "toggle_slug" => "' . $field['group'] . '",
                        "description" => __("' . $field['description'] . '", "dp_dmb"),       
                    ),
                    "' . $field['id'] . '_icon_color" => array(
                        "label" => __("' . $field['label'] . ' Color", "dp_dmb"),
                        "type" => "color-alpha",
                        "custom_color" => true,
                        "tab_slug" => "advanced",
                        "toggle_slug" => "icon",
                        "description" => __("Here you can define a custom color for your icon.", "dp_dmb"),
                    ),
                    "' . $field['id'] . '_use_circle" => array(
                        "label" => __("' . $field['label'] . ' Circle", "dp_dmb"),
                        "type" => "yes_no_button",
                        "options" => array(
                            "off" => __("No", "dp_dmb"),
                            "on" => __("Yes", "dp_dmb"),
                        ),
                        "default" => "off",
                        "affects" => array(
                            "' . $field['id'] . '_use_circle_border",
                            "' . $field['id'] . '_circle_color",
                        ),
                        "tab_slug" => "advanced",
                        "toggle_slug" => "icon",
                        "description" => __("Here you can choose whether icon should display within a circle.", "dp_dmb"),
                    ),
                    "' . $field['id'] . '_circle_color" => array(
                        "label" => __("' . $field['label'] . ' Circle Color", "dp_dmb"),
                        "type" => "color-alpha",
                        "custom_color" => true,
                        "tab_slug" => "advanced",
                        "toggle_slug" => "icon",
                        "description" => __("Here you can define a custom color for the icon circle.", "dp_dmb"),
                    ),
                    "' . $field['id'] . '_use_circle_border" => array(
                        "label" => __("' . $field['label'] . ' Show Circle Border", "dp_dmb"),
                        "type" => "yes_no_button",
                        "options" => array(
                            "off" => __("No", "dp_dmb"),
                            "on" => __("Yes", "dp_dmb"),
                        ),
                        "default" => "off",
                        "affects" => array(
                            "' . $field['id'] . '_circle_border_color",
                        ),
                        "tab_slug" => "advanced",
                        "toggle_slug" => "icon",
                        "description" => __("Here you can choose whether the icon circle border should display.", "dp_dmb"),
                    ),
                    "' . $field['id'] . '_circle_border_color" => array(
                        "label" => __("' . $field['label'] . ' Border Color", "dp_dmb"),
                        "type" => "color-alpha",
                        "custom_color" => true,
                        "tab_slug" => "advanced",
                        "toggle_slug" => "icon",
                        "description" => __("Here you can define a custom color for the icon circle border.", "dp_dmb"),
                    ),                        
                    "' . $field['id'] . '_use_icon_font_size" => array(
                        "label" => __("' . $field['label'] . ' Use Font Size", "dp_dmb"),
                        "type" => "yes_no_button",
                        "options" => array(
                            "off" => __("No", "dp_dmb"),
                            "on" => __("Yes", "dp_dmb"),
                        ),
                        "default" => "off",
                        "affects" => array(
                            "' . $field['id'] . '_icon_font_size",
                        ),
                        "tab_slug" => "advanced",
                        "toggle_slug" => "icon",
                    ),                       
                    "' . $field['id'] . '_icon_font_size" => array(
                        "label" => __("' . $field['label'] . ' Font Size", "dp_dmb"),
                        "type" => "range",
                        "option_category" => "font_option",
                        "default" => "96px",
                        "range_settings" => array(
                            "min" => "1",
                            "max" => "120",
                            "step" => "1",
                        ),
                        "mobile_options" => true,
                        "tab_slug" => "advanced",
                        "toggle_slug" => "icon",
                    ),                                               
                    "' . $field['id'] . '_icon_font_size_last_edited" => array(     
                        "type" => "skip",                            
                        "tab_slug" => "advanced",
                        "toggle_slug" => "icon",
                    ),                                              
                    "' . $field['id'] . '_icon_font_size_tablet" => array(     
                        "type" => "skip",                            
                        "tab_slug" => "advanced",
                        "toggle_slug" => "icon",
                    ),                                              
                    "' . $field['id'] . '_icon_font_size_phone" => array(   
                        "type" => "skip",
                        "tab_slug" => "advanced",
                        "toggle_slug" => "icon",
                    ),';
					$fields_string['add_code']        .= '   
                        /* Processing Icon: ' . $field['id'] . ' */
                        if ("off" !== $' . $field['id'] . '_use_icon_font_size) {
                                $' . $field['id'] . '_font_size_responsive_active = et_pb_get_responsive_status($' . $field['id'] . '_icon_font_size_last_edited);
                                $' . $field['id'] . '_font_size_values = array(
                                    "desktop" => $' . $field['id'] . '_icon_font_size,
                                    "tablet" => $' . $field['id'] . '_font_size_responsive_active ? $' . $field['id'] . '_icon_font_size_tablet : "",
                                    "phone" => $' . $field['id'] . '_font_size_responsive_active ? $' . $field['id'] . '_icon_font_size_phone : "",
                                );
                                et_pb_responsive_options()->generate_responsive_css($' . $field['id'] . '_font_size_values, "%%order_class%% .et-pb-icon.dp-icon-' . $field['id'] . '", "font-size", $render_slug);
                        }
                        $' . $field['id'] . '_icon_style = sprintf(\'color: %1$s;\', esc_attr($' . $field['id'] . '_icon_color));
                        if ("on" === $' . $field['id'] . '_use_circle) {
                            $' . $field['id'] . '_icon_style .= sprintf(\' background-color: %1$s;\', esc_attr($' . $field['id'] . '_circle_color));
                            if ("on" === $' . $field['id'] . '_use_circle_border) {
                                $' . $field['id'] . '_icon_style .= sprintf(\' border-color: %1$s;\', esc_attr($' . $field['id'] . '_circle_border_color));
                            }
                        }
                       $' . $field['id'] . ' = sprintf(' . '\' ' . '<span class="dp_field_' . $field['id'] . ' et-pb-icon dp-icon-' . $field['id'] . ' %2$s%3$s" style="%4$s">%1$s</span>' . '\' ' . ', esc_attr( et_pb_process_font_icon( ' . $field['php'] . ' )), ( \'on\' === ' . $field['php'] . '_use_circle ? \' et-pb-icon-circle\' : \'\'), ( \'on\' === ' . $field['php'] . '_use_circle && \'on\' === ' . $field['php'] . '_use_circle_border ? \' et-pb-icon-circle-border\' : \'\'), ' . $field['php'] . '_icon_style); 
                       if ( version_compare( ET_BUILDER_PRODUCT_VERSION, "4.12.1", ">" ) ) {
							$this->generate_styles(
								array(
									"utility_arg"    => "icon_font_family",
									"render_slug"    => $render_slug,
									"base_attr_name" => "' . $field['id'] . '",
									"important"      => true,
									"selector"       => "%%order_class%% .dp-icon-' . $field['id'] . '",
									"processor"      => array(
										"ET_Builder_Module_Helper_Style_Processor",
										"process_extended_icon",
									),
								)
							);
						}                       
                        /* End Processing Icon: ' . $field['id'] . ' */
                     ';
					$fields_string['shortcode_attrs'] .= '$' . $field['id'] . ' = $this->props["' . $field['id'] . '"]; ';
					$fields_string['shortcode_attrs'] .= '$' . $field['id'] . '_icon_color = $this->props["' . $field['id'] . '_icon_color"]; ';
					$fields_string['shortcode_attrs'] .= '$' . $field['id'] . '_use_circle = $this->props["' . $field['id'] . '_use_circle"]; ';
					$fields_string['shortcode_attrs'] .= '$' . $field['id'] . '_circle_color = $this->props["' . $field['id'] . '_circle_color"]; ';
					$fields_string['shortcode_attrs'] .= '$' . $field['id'] . '_use_circle_border = $this->props["' . $field['id'] . '_use_circle_border"]; ';
					$fields_string['shortcode_attrs'] .= '$' . $field['id'] . '_circle_border_color = $this->props["' . $field['id'] . '_circle_border_color"]; ';
					$fields_string['shortcode_attrs'] .= '$' . $field['id'] . '_use_icon_font_size = $this->props["' . $field['id'] . '_use_icon_font_size"]; ';
					$fields_string['shortcode_attrs'] .= '$' . $field['id'] . '_icon_font_size = $this->props["' . $field['id'] . '_icon_font_size"]; ';
					$fields_string['shortcode_attrs'] .= '$' . $field['id'] . '_icon_font_size_tablet = $this->props["' . $field['id'] . '_icon_font_size_tablet"]; ';
					$fields_string['shortcode_attrs'] .= '$' . $field['id'] . '_icon_font_size_phone = $this->props["' . $field['id'] . '_icon_font_size_phone"]; ';
					$fields_string['shortcode_attrs'] .= '$' . $field['id'] . '_icon_font_size_last_edited = $this->props["' . $field['id'] . '_icon_font_size_last_edited"]; ';
					break;
				case 'select':
					$field['select_options'] = str_replace( '"', '', $field['select_options'] );
					$field['select_values']  = str_replace( '"', '', $field['select_values'] );
					if ( ! empty( $field['select_options'] ) && empty( $field['select_function'] ) ) {
						$options_array        = explode( ',', $field['select_options'] );
						$options_values_array = array();
						if ( ! empty( $field['select_values'] ) ) {
							$options_values_array = explode( ',', $field['select_values'] );
						}
						$string_array_options = "";
						$index                = 1;
						if ( count( $options_array ) === count( $options_values_array ) ) {
							foreach ( $options_array as $key => $option_value ) {
								if ( ! empty( $option_value ) ) {
									$string_array_options .= '"' . trim( $options_values_array[ $key ] ) . '"=>"' . trim( $option_value ) . '",';
								}
							}
						} else {
							foreach ( $options_array as $option_value ) {
								if ( ! empty( $option_value ) ) {
									$string_array_options .= '"' . $index . '"=>"' . trim( $option_value ) . '",';
									$index ++;
								}
							}
						}
						$default_select_value             = isset( $options_values_array[0] ) ? $options_values_array[0] : '1';
						$fields_string['get_fields']      .= '
                            "' . $field['id'] . '" => array(
                                "label" => __("' . $field['label'] . '", "dp_dmb"),
                                "type" => "select",
                                "options" => array(' . $string_array_options . '),
                                "default" => "' . $default_select_value . '",
                                "tab_slug" => "general",
                                "toggle_slug" => "' . $field['group'] . '",
                                "description" => __("' . $field['description'] . '", "dp_dmb"),
                            ),';
						$fields_string['shortcode_attrs'] .= '$' . $field['id'] . ' = $this->props["' . $field['id'] . '"]; ';
					} elseif ( ! empty( $field['select_function'] ) ) {
						$fields_string['get_fields']      .= '
                        "' . $field['id'] . '" => array(
                            "label" => __("' . $field['label'] . '", "dp_dmb"),
                            "type" => "select",
                            "options" => function_exists("' . $field['select_function'] . '") ? ' . $field['select_function'] . '() : array("Your selected function do not exists"),
                            "default" => function_exists("' . $field['select_function'] . '") ? array_keys(' . $field['select_function'] . '())[0] : "",
                            "tab_slug" => "general",
                            "toggle_slug" => "' . $field['group'] . '",
                            "description" => __("' . $field['description'] . '", "dp_dmb"),
                        ),';
						$fields_string['shortcode_attrs'] .= '$' . $field['id'] . ' = $this->props["' . $field['id'] . '"]; ';
					}
					break;
				case 'button':
					$button_styles_string = ' 
                    "' . $field['id'] . '" => array(
	   "label" => __("' . $field['label'] . '", "dp_dmb"),
	   "css" => array("main" => "{$this->main_css_element} .dp_field_' . $field['id'] . '.et_pb_button", "important" => "all",),
                    ), ';
					if ( $field['is_child'] !== 'on' ) {
						$this->advanced_fields['button'] .= $button_styles_string;
					} else {
						$this->child_advanced_fields['button'] .= $button_styles_string;
					}
					$fields_string['get_fields'] .= '                    
	"' . $field['id'] . '_text" => array(
                        "label" => __( "' . $field['label'] . ' Text", "dp_dmb" ),
                        "type" => "text",                               
                        "default" => __( "' . $field['default_button'] . '", "dp_dmb" ),
                        ' . $dynamic_content_text . '
                        "description" => __( "Enter the button text.", "dp_dmb" ),
                        "tab_slug" => "general",
                        "toggle_slug" => "' . $field['group'] . '",
	),                
	"' . $field['id'] . '_new_tab" => array(
                        "label" => __( "' . $field['label'] . ' Open URL in New Tab", "dp_dmb" ),
                        "type" => "yes_no_button",
                        "description" => __( "Here you can choose whether or not your link opens in a new window", "dp_dmb" ),
                        "options" => array(
                            "off" => __("No", "dp_dmb"),
                            "on" => __("Yes", "dp_dmb"),
                        ),
                        "default" => "off",
                        "tab_slug" => "general",
                        "toggle_slug" => "' . $field['group'] . '",
	),';
					if ( $field['btn_link'] !== 'on' ) {
						$fields_string['shortcode_attrs'] .= '$' . $field['id'] . '_url = $this->props["' . $field['id'] . '_url"]; ';
						$fields_string['get_fields']      .= '      
	"' . $field['id'] . '_url" => array(
                        "label" => __( "' . $field['label'] . ' URL", "dp_dmb" ),
                        "type" => "text",
                        ' . $dynamic_content_url . ' 
                        "description" => __( "Input the destination URL for your button.", "dp_dmb" ),
                        "tab_slug" => "general",
                        "toggle_slug" => "' . $field['group'] . '",
	), ';
					} else {
						$fields_string['shortcode_attrs'] .= '$' . $field['id'] . '_url = "button_permalink"; ';
					}
					$fields_string['add_code']        .= '     
                        /* Processing Button: ' . $field['id'] . ' */
                        if ("" !== $' . $field['id'] . '_text) {
                          $' . $field['id'] . '= sprintf(' . '\' ' . '<a href="%2$s" target="%6$s" class="dp_field_' . $field['id'] . ' et_pb_button %4$s"%3$s%5$s>%1$s</a>' . '\' ' . ', ( "" !== $' . $field['id'] . '_text ? esc_attr($' . $field['id'] . '_text) : ""), ( "" !== $' . $field['id'] . '_url ? $' . $field['id'] . '_url : "#"), "" !== $' . $field['id'] . '_icon && "on" === $custom_' . $field['id'] . ' ? sprintf(' . '\' ' . 'data-icon="%1$s"' . '\' ' . ', esc_attr(et_pb_process_font_icon($' . $field['id'] . '_icon))) : "", "" !== $' . $field['id'] . '_icon && "on" === $custom_' . $field['id'] . ' ? " et_pb_custom_button_icon" : "", $this->get_rel_attributes($' . $field['id'] . '_rel), "on" === $' . $field['id'] . '_new_tab ? "_blank" : "");
                       }else{ $' . $field['id'] . '= ""; }
                       /* End Processing Button: ' . $field['id'] . ' */
                      ';
					$fields_string['shortcode_attrs'] .= '$' . $field['id'] . '_text = $this->props["' . $field['id'] . '_text"]; ';
					$fields_string['shortcode_attrs'] .= '$' . $field['id'] . '_new_tab = $this->props["' . $field['id'] . '_new_tab"]; ';
					$fields_string['shortcode_attrs'] .= '$' . $field['id'] . '_rel = $this->props["' . $field['id'] . '_rel"]; ';
					$fields_string['shortcode_attrs'] .= '$' . $field['id'] . '_icon = $this->props["' . $field['id'] . '_icon"]; ';
					$fields_string['shortcode_attrs'] .= '$custom_' . $field['id'] . ' = $this->props["custom_' . $field['id'] . '"]; ';
					break;
				case 'date_picker':
					$fields_string['get_fields'] .= '
                    "' . $field['id'] . '" => array(
                        "label" => __("' . $field['label'] . '", "dp_dmb"),
                        "type" => "date_picker",
                        "tab_slug" => "general",
                        "toggle_slug" => "' . $field['group'] . '",
                        "description" => __("' . $field['description'] . '", "dp_dmb"),
                    ),';
					if ( $field['allow_date_format'] === 'on' ) {
						$fields_string['get_fields']      .= '
                            "' . $field['id'] . '_custom_format" => array(
                            "label" => __("' . $field['label'] . ' Format", "dp_dmb"),
                            "type" => "text",
                            "tab_slug" => "general",
                            "toggle_slug" => "' . $field['group'] . '",
                            "description" => __("Use this field to change the default format. <a href=\"https://codex.wordpress.org/Formatting_Date_and_Time\" target=\"_blank\">Documentation on date and time formatting.</a>", "dp_dmb"),
                        ),';
						$fields_string['add_code']        .= '  
                            if ("" !== $' . $field['id'] . '_custom_format) {
                                $' . $field['id'] . ' = date_format(date_create($' . $field['id'] . '), $' . $field['id'] . '_custom_format);
                            }
                        ';
						$fields_string['shortcode_attrs'] .= '$' . $field['id'] . '_custom_format = $this->props["' . $field['id'] . '_custom_format"]; ';
					}
					$fields_string['shortcode_attrs'] .= '$' . $field['id'] . ' = $this->props["' . $field['id'] . '"]; ';
					break;
				case 'upload':
					$fields_string['get_fields']      .= '
                    "' . $field['id'] . '" => array(
                        "label" => __("' . $field['label'] . '", "dp_dmb"), 
                        "type" => "upload",                         
                        "mobile_options" => ' . $responsive . ',   
                        "upload_button_text" => __( "Upload File", "dp_dmb" ),
                        "choose_text" => __( "Choose File", "dp_dmb" ),
                        "data_type" =>"",
                        "tab_slug" => "general",
                        "toggle_slug" => "' . $field['group'] . '",
                        "description" => __("' . $field['description'] . '", "dp_dmb"),
                        ),';
					$fields_string['shortcode_attrs'] .= '$' . $field['id'] . ' = $this->props["' . $field['id'] . '"]; ' . $this->create_responsive_vars( $field['id'], $responsive );
					break;
				case 'video':
					$fields_string['get_fields']      .= '
                    "' . $field['id'] . '" => array(
                        "label" => __("' . $field['label'] . '", "dp_dmb"), 
                        "type" => "upload",         
                        "upload_button_text" => __( "Upload Video", "dp_dmb" ),
                        "choose_text" => __( "Choose Video", "dp_dmb" ),
                        "data_type" =>"video",
                        "tab_slug" => "general",
                        "toggle_slug" => "' . $field['group'] . '",
                        "description" => __("' . $field['description'] . '", "dp_dmb"),
                        ),';
					$fields_string['add_code']        .= '     
                        /* Processing Video: ' . $field['id'] . ' */
                        $' . $field['id'] . '_html_output = sprintf(\'<video controls preload="metadata"><source src="%1$s">Your browser does not support the video tag.</video>\', $' . $field['id'] . ');					
                       /* End Processing Video: ' . $field['id'] . ' */
                      ';
					$fields_string['shortcode_attrs'] .= '$' . $field['id'] . ' = $this->props["' . $field['id'] . '"]; ';
					break;
				case 'audio':
					$fields_string['get_fields']      .= '
                    "' . $field['id'] . '" => array(
                        "label" => __("' . $field['label'] . '", "dp_dmb"), 
                        "type" => "upload", 
                        "upload_button_text" => __( "Upload Audio", "dp_dmb" ),
                        "choose_text" => __( "Choose Audio", "dp_dmb" ),
                        "data_type" =>"audio",
                        "tab_slug" => "general",
                        "toggle_slug" => "' . $field['group'] . '",
                        "description" => __("' . $field['description'] . '", "dp_dmb"),
                        ),';
					$fields_string['add_code']        .= '     
                        /* Processing Audio: ' . $field['id'] . ' */
                        $' . $field['id'] . '_html_output = sprintf(\'<audio controls preload="metadata"><source src="%1$s">Your browser does not support the audio tag.</audio>\', $' . $field['id'] . ');					
                       /* End Processing Audio: ' . $field['id'] . ' */
                      ';
					$fields_string['shortcode_attrs'] .= '$' . $field['id'] . ' = $this->props["' . $field['id'] . '"]; ';
					break;
				case 'checkbox':
					$field['checkbox_values'] = str_replace( '"', '', $field['checkbox_values'] );
					if ( ! empty( $field['checkbox_values'] ) ) {
						$options_array = explode( ',', $field['checkbox_values'] );
					} else {
						$options_array = array();
					}
					$string_array_options = "";
					$string_array_labels  = "";
					foreach ( $options_array as $key => $option_value ) {
						if ( ! empty( $option_value ) ) {
							$string_array_options .= '"' . $key . '"=>"' . trim( $option_value ) . '",';
							$string_array_labels  .= '"' . trim( $option_value ) . '",';
						}
					}
					$fields_string['add_code']   .= '     
                        /* Processing Checkbox: ' . $field['id'] . ' */
                        $' . $field['id'] . '_labels = explode("|", $this->process_multiple_checkboxes_field_value( array(' . $string_array_labels . '), $' . $field['id'] . ' ));
                       /* End Processing Checkbox: ' . $field['id'] . ' */
                      ';
					$fields_string['get_fields'] .= '
                    "' . $field['id'] . '" => array(
                        "label" => __("' . $field['label'] . '", "dp_dmb"), 
                        "type" => "multiple_checkboxes",
                        "options" => array(' . $string_array_options . '),
                        "tab_slug" => "general",
                        "toggle_slug" => "' . $field['group'] . '",
                        "description" => __("' . $field['description'] . '", "dp_dmb"),
                        ),';
					if ( $field['process_checkbox'] === 'on' ) {
						$fields_string['add_code'] .= '  
                             /* Processing Checkbox List: ' . $field['id'] . ' */
                                 $options = explode(",", "' . $field['checkbox_values'] . '");
                                 $checked_options = array();
                                 $' . $field['id'] . ' = explode("|", $' . $field['id'] . ');
                                 foreach ($options as $key => $option) {
                                    if (isset($' . $field['id'] . '[$key]) && $' . $field['id'] . '[$key] === "on") {
                                       $checked_options[] = $option;
                                   }
                               }
                               $' . $field['id'] . ' = $checked_options;
                            /* End Processing Checkbox List: ' . $field['id'] . ' */
                        ';
					}
					$fields_string['shortcode_attrs'] .= '$' . $field['id'] . ' = $this->props["' . $field['id'] . '"]; ';
					break;
				case 'code':
					$fields_string['get_fields']      .= '
                    "' . $field['id'] . '" => array(
                        "label" => __("' . $field['label'] . '", "dp_dmb"), 
                        "type" => "codemirror",
                        "mode" => "html",
                        "tab_slug" => "general",
                        "toggle_slug" => "' . $field['group'] . '",
                        "description" => __("' . $field['description'] . '", "dp_dmb"),
                        ),';
					$fields_string['shortcode_attrs'] .= '$' . $field['id'] . ' = $this->props["' . $field['id'] . '"]; ';
					break;
				default:
					break;
			}
		}
		/* Tiny MCE Custom CSS */
		if ( $this->tiny_mce === 'on' ) {
			$tinymce_custom_css_string = '"dp_tiny_mce" => array("label" => __("Body", "dp_dmb" ), "selector" => ".dp_field_tinymce",),';
			/* Tiny MCE Design Tab */
			$tinymce_design_tab_string = '
                "dp_tiny_mce" => array(
                    "label" => __("Body", "dp_dmb" ),
                    "css" => array("main" => "{$this->main_css_element} .dp_field_tinymce",),
                    "line_height" => array("default" => floatval( et_get_option( "body_font_height", "1.7" ) ) . "em",), 
                    "font_size" => array("default" => absint( et_get_option( "body_font_size", "14" ) ) . "px",),
                ),';
			if ( $this->have_childs ) {
				$this->child_advanced_fields['fonts'] .= $tinymce_design_tab_string;
				$this->child_custom_css               .= $tinymce_custom_css_string;
			} else {
				$this->advanced_fields['fonts'] .= $tinymce_design_tab_string;
				$this->custom_css               .= $tinymce_custom_css_string;
			}
		}
		if ( $is_for_child ) {
			$this->child_advanced_fields['fonts']  .= ')';
			$this->child_advanced_fields['button'] .= ')';
			$this->child_custom_css                .= ')';
		} else {
			$this->advanced_fields['fonts']  .= ')';
			$this->advanced_fields['button'] .= ')';
			$this->custom_css                .= ')';
		}

		return $fields_string;
	}

	public function create_parent_module_class( $child_properties, $fields_strings, $have_tiny_mce, $module_css, $module_js ) {
		$this->before_render = $this->php === 'on' ? $this->before_render : "";
		$module_string       = '<?php
if(!class_exists("ET_Builder_Module_DP_DMB_Module_' . $this->id . '")){

    class ET_Builder_Module_DP_DMB_Module_' . $this->id . ' extends ET_Builder_Module {        
        
        public $slug = "et_pb_dp_dmb_module_' . $this->id . '";
         ' . $this->partial_support . '    
        
        public function init() {
            $this->name = "' . $this->title . '" ;
            ' . $this->fullwidth . '
            ' . $child_properties . '
            $this->main_css_element = "%%order_class%%";
        }

        public function get_fields() {
            $fields = array(
                ' . $fields_strings['get_fields'] . '
                ' . $have_tiny_mce . ' 
            );
            return $fields;
        }
        
        public function get_settings_modal_toggles() {
            ' . $this->get_toggles_string() . '
        }
        
        public function get_advanced_fields_config() {
            return array(
                "background" => array("use_background_image" => ' . $this->advanced_fields['background']['use_background_image'] . ', "use_background_video" => ' . $this->advanced_fields['background']['use_background_video'] . ',),
                "text" => ' . $this->advanced_fields['text'] . ',
                "borders" => ' . $this->advanced_fields['borders'] . ',
                "box_shadow" => ' . $this->advanced_fields['box_shadow'] . ',
                "button" => ' . $this->advanced_fields['button'] . ',
                "filters" => ' . $this->advanced_fields['filters'] . ',
                "fonts" => ' . $this->advanced_fields['fonts'] . ',
                "margin_padding" => ' . $this->advanced_fields['margin_padding'] . ',
                "max_width" => ' . $this->advanced_fields['max_width'] . ',
                "animation" => ' . $this->advanced_fields['animation'] . '
            );
        }
        
        public function get_custom_css_fields_config() {
            return ' . $this->custom_css . ';
        }
        
        public function before_render(){
            /*' . $module_css . '*/
            ' . $module_js . ' 
			add_filter( "et_late_global_assets_list", array( $this, "require_divi_global_assets" ) );
            ' . str_replace( array( "<?php", "?>" ), "", $this->before_render ) . ' 
       }

        public function render( $attrs, $content, $render_slug ) {
            $background_layout    = $this->props["background_layout"];
            ' . $fields_strings['shortcode_attrs'] . '
                
            $this->add_classname( array(
                "et_pb_bg_layout_{$background_layout}",
                $this->get_text_orientation_classname(),
            ) );
                
            ' . $fields_strings['add_code'] . '     

            ob_start();
            ?>
            ' . $this->html . '
            <?php

            $output = ob_get_clean();
            return $this->_render_module_wrapper( $output, $render_slug );
        }
        
        ' . $this->get_global_assets_code() . '  
    }
            
    ?>' . $this->functions . '<?php

    new ET_Builder_Module_DP_DMB_Module_' . $this->id . '; 
}';

		return $module_string;
	}

	public function get_toggles_string() {
		$custom_groups = "";
		foreach ( $this->fields_groups as $key => $group ) {
			$custom_groups .= sprintf( '"%1$s"  => esc_html__("%2$s", "dp_dmb" ),', $key, $group );
		}

		return '
        return array(
            "general" => array(
                "toggles" => array(
                    ' . $custom_groups . '
                    "content" => esc_html__("Content", "dp_dmb"),
                    "background" => esc_html__("Background", "dp_dmb"),
                    "admin_label"  => esc_html__("Admin Label", "dp_dmb" ),
                    ),
                ),
                "advanced" => array(
                    "toggles" => array(
                        "icon" => esc_html__("Icons", "dp_dmb"),
                    ),
                ),
        );';
	}

	public function create_child_module_class( $fields_strings_childs, $have_tiny_mce_child ) {
		if ( $this->child_label === '' ) {
			$this->child_label = 'admin_title';
		}
		$return_string_for_wrappers = $this->unwrap_repeat_fields === 'on' ? '$output;' : '$this->_render_module_wrapper( $output, $render_slug );';
		$module_string              = '            
if(!class_exists("ET_Builder_Module_DP_DMB_Module_Item_' . $this->id . '")){
                
    class ET_Builder_Module_DP_DMB_Module_Item_' . $this->id . ' extends ET_Builder_Module {
        
         public $slug = "et_pb_dp_dmb_module_' . $this->id . '_item";
         ' . $this->partial_support . '    
        
        public function init() {
            $this->name = esc_html__("Field", "et_builder");
            $this->type = "child";
            $this->child_title_var = "' . $this->child_label . '";
            $this->main_css_element = "%%order_class%%";
        }
        
        public function get_fields() {
            $fields = array(                
                ' . $fields_strings_childs['get_fields'] . ' 
                ' . $have_tiny_mce_child . '                    
                "admin_title" => array(
                    "label"       => esc_html__( "Admin Label", "dp_dmb" ),
                    "type"        => "text",
                    "description" => esc_html__( "This will change the label of the item in the builder for easy identification.", "dp_dmb" ),
                    "toggle_slug" => "admin_label",
                ),
            );
            return $fields;
        }
        
        public function get_settings_modal_toggles() {
            ' . $this->get_toggles_string() . '
        }
        
        public function get_advanced_fields_config() {
            return array(
                "background" => array("use_background_image" => ' . $this->child_advanced_fields['background']['use_background_image'] . ', "use_background_video" => ' . $this->child_advanced_fields['background']['use_background_video'] . ',),
                "text" => ' . $this->child_advanced_fields['text'] . ',
                "borders" => ' . $this->child_advanced_fields['borders'] . ',
                "box_shadow" => ' . $this->child_advanced_fields['box_shadow'] . ',
                "button" => ' . $this->child_advanced_fields['button'] . ',
                "filters" => ' . $this->child_advanced_fields['filters'] . ',
                "fonts" => ' . $this->child_advanced_fields['fonts'] . ',
                "margin_padding" => ' . $this->child_advanced_fields['margin_padding'] . ',
                "max_width" => ' . $this->child_advanced_fields['max_width'] . ',
                "animation" => ' . $this->child_advanced_fields['animation'] . '
            );
        }
        
        public function get_custom_css_fields_config() {
            return ' . $this->child_custom_css . ';
        }

        public function render( $attrs, $content, $render_slug ) { 
            ' . $fields_strings_childs['shortcode_attrs'] . '   

             ' . $fields_strings_childs['add_code'] . ' 
                 
            $this->add_classname( array(
                $this->get_text_orientation_classname(), 
                "dp_dmb_repeat_item",
                "dp_dmb_module_' . $this->id . '_item"
            ) );
            
            $this->remove_classname( array("et_pb_module",) );
                
            ob_start();
            ?>
            ' . $this->html_output_child . ' 
            <?php
            
            $output = ob_get_clean();
            
            return ' . $return_string_for_wrappers . '
        }

    }

    new ET_Builder_Module_DP_DMB_Module_Item_' . $this->id . '; 
}            ';

		return $module_string;
	}

	public function backward_compatibility_tasks() {
		update_post_meta( $this->id, '_dp_dmb_builder_version', $this->builder_constructor_version );
	}

	public function get_global_assets_code() {
		$code         = '';
		$dependencies = '';
		if ( is_array( $this->global_assets ) && ! empty( $this->global_assets ) ) {
			foreach ( $this->global_assets as $asset_name ) {
				switch ( $asset_name ) {
					case 'fonts':
						$dependencies .= '
			if ( ! ( isset( $assets["et_icons_all"] ) && isset( $assets["et_icons_fa"] ) ) ) {
				$assets_list["et_icons_all"] = array(
					"css" => "{$assets_prefix}/css/icons_all.css",
				);
				$assets_list["et_icons_fa"]  = array(
					"css" => "{$assets_prefix}/css/icons_fa_all.css",
				);
			}
						';
						break;
					case 'magnific_popup':
						$dependencies .= '
			if ( ! isset( $assets["et_jquery_magnific_popup"] ) ) {
				$assets_list["et_jquery_magnific_popup"] = array(
					"css" => "{$assets_prefix}/css/magnific_popup{$cpt_suffix}.css"
				);
				wp_enqueue_script( "magnific-popup", ET_BUILDER_URI . "/feature/dynamic-assets/assets/js/magnific-popup.js", array( "jquery" ), ET_CORE_VERSION, true );			
			}
						';
						break;
					case 'overlay':
						$dependencies .= '
			if ( ! isset( $assets["overlay"] ) ) {
				$assets_list["overlay"] = array(
					"css" => "{$assets_prefix}/css/overlay{$cpt_suffix}.css",
				);
			}
						';
						break;
					case 'gutters':
						$dependencies .= '
			$gutter_length = 4;
			for ( $i = 1; $i <= $gutter_length; $i ++ ) {
				if ( ! isset( $assets_list[ "et_divi_gutters" . $i ] ) ) {
					$assets_list[ "et_divi_gutters" . $i ] = array(
						"css" => "{$assets_prefix}/css/gutters" . $i . "{$cpt_suffix}.css",
					);
				}
				if ( ! isset( $assets_list[ "et_divi_gutters" . $i . "_grid_items" ] ) ) {
					$assets_list[ "et_divi_gutters" . $i . "_grid_items" ] = array(
						"css" => "{$assets_prefix}/css/gutters" . $i . "_grid_items{$cpt_suffix}.css",
					);
				}
				if ( ! isset( $assets_list[ "et_divi_gutters" . $i . "_specialty" ] ) ) {
					$assets_list[ "et_divi_gutters" . $i . "_specialty" ] = array(
						"css" => "{$assets_prefix}/css/gutters" . $i . "_specialty{$cpt_suffix}.css",
					);
				}
				if ( ! isset( $assets_list[ "et_divi_gutters" . $i . "_specialty_grid_items" ] ) ) {
					$assets_list[ "et_divi_gutters" . $i . "_specialty_grid_items" ] = array(
						"css" => "{$assets_prefix}/css/gutters" . $i . "_specialty_grid_items{$cpt_suffix}.css",
					);
				}
			}			
						';
						break;
					case 'grid_items':
						$dependencies .= '
			if ( ! isset( $assets["grid_items"] ) ) {
				$assets_list["grid_items"] = array(
					"css" => "{$assets_prefix}/css/grid_items{$cpt_suffix}.css",
				);
			}
						';
						break;
					case 'animations':
						$dependencies .= '
			if ( ! isset( $assets["animations"] ) ) {
				$assets_list["animations"] = array(
					"css" => "{$assets_prefix}/css/animations{$cpt_suffix}.css",
				);
			}
						';
					case 'sticky':
						$dependencies .= '
			if ( ! isset( $assets["animations"] ) ) {
				$assets_list["animations"] = array(
					"css" => "{$assets_prefix}/css/animations{$cpt_suffix}.css",
				);
			}
						';
						break;
				}
			}
		}

		if ( is_array( $this->module_assets ) && ! empty( $this->module_assets ) ) {
			foreach ( $this->module_assets as $asset_name ) {
				switch ( $asset_name ) {
					case 'accordion':
						$dependencies .= '
			if ( ! isset( $assets["et_pb_accordion"] ) ) {
				$assets_list["et_pb_accordion"] = array(
					"css" => array(
						"{$assets_prefix}/css/accordion{$cpt_suffix}.css",
						"{$assets_prefix}/css/toggle{$cpt_suffix}.css",
					),
				);
			}
						';
						break;
					case 'audio':
						$dependencies .= '
			if ( ! isset( $assets["et_pb_audio"] ) ) {
				$assets_list["et_pb_audio"] = array(
					"css" => array(
						"{$assets_prefix}/css/audio{$cpt_suffix}.css",
						"{$assets_prefix}/css/audio_player{$cpt_suffix}.css",
					),
				);
			}
						';
						break;
					case 'blog':
						$dependencies .= '
			if ( ! isset( $assets["et_pb_blog"] ) ) {
				$assets_list["et_pb_blog"] = array(
					"css" => array(
						"{$assets_prefix}/css/blog{$cpt_suffix}.css",
						"{$assets_prefix}/css/posts{$cpt_suffix}.css",
						"{$assets_prefix}/css/post_formats{$cpt_suffix}.css",    
						"{$assets_prefix}/css/overlay{$cpt_suffix}.css",
						"{$assets_prefix}/css/audio_player{$cpt_suffix}.css",
						"{$assets_prefix}/css/video_player{$cpt_suffix}.css",
						"{$assets_prefix}/css/slider_base{$cpt_suffix}.css",
						"{$assets_prefix}/css/slider_controls{$cpt_suffix}.css",
						"{$assets_prefix}/css/wp_gallery{$cpt_suffix}.css",
					),
				);
			}
						';
						break;
					case 'blurb':
						$dependencies .= '
			if ( ! isset( $assets["et_pb_blurb"] ) ) {
				$assets_list["et_pb_blurb"] = array(
					"css" => array(
						"{$assets_prefix}/css/blurb{$cpt_suffix}.css",
						"{$assets_prefix}/css/legacy_animations{$cpt_suffix}.css",
					),
				);
			}
						';
						break;
					case 'button':
						$dependencies .= '
			if ( ! isset( $assets["et_pb_button"] ) ) {
				$assets_list["et_pb_button"] = array(
					"css" => array(
						"{$assets_prefix}/css/button{$cpt_suffix}.css",
						"{$assets_prefix}/css/buttons{$cpt_suffix}.css",
					),
				);
			}
						';
						break;
					case 'circle':
						$dependencies .= '
			if ( ! isset( $assets["et_pb_circle_counter"] ) ) {
				$assets_list["et_pb_circle_counter"] = array(
					"css" => "{$assets_prefix}/css/circle_counter{$cpt_suffix}.css",
				);
			}
						';
						break;
					case 'form':
						$dependencies .= '
			if ( ! isset( $assets["et_pb_contact_form"] ) ) {
				$assets_list["et_pb_contact_form"] = array(
					"css" => array(
						"{$assets_prefix}/css/contact_form{$cpt_suffix}.css",
						"{$assets_prefix}/css/forms{$cpt_suffix}.css",
						"{$assets_prefix}/css/fields{$cpt_suffix}.css",
						"{$assets_prefix}/css/buttons{$cpt_suffix}.css",
					),
				);
			}
						';
						break;
					case 'countdown':
						$dependencies .= '
			if ( ! isset( $assets["et_pb_countdown_timer"] ) ) {
				$assets_list["et_pb_countdown_timer"] = array(
					"css" => "{$assets_prefix}/css/countdown_timer{$cpt_suffix}.css"
				);
			}
						';
						break;
					case 'counter':
						$dependencies .= '
			if ( ! isset( $assets["et_pb_counter"] ) ) {
				$assets_list["et_pb_counter"] = array(
					"css" => "{$assets_prefix}/css/counter{$cpt_suffix}.css",
				);
			}
						';
						break;
					case 'cta':
						$dependencies .= '
			if ( ! isset( $assets["et_pb_cta"] ) ) {
				$assets_list["et_pb_cta"] = array(
					"css" => array(
						"{$assets_prefix}/css/cta{$cpt_suffix}.css",
						"{$assets_prefix}/css/buttons{$cpt_suffix}.css",
					)
				);
			}
						';
						break;
					case 'divider':
						$dependencies .= '
			if ( ! isset( $assets["et_pb_divider"] ) ) {
				$assets_list["et_pb_divider"] = array(
					"css" => "{$assets_prefix}/css/divider{$cpt_suffix}.css",
				);
			}
						';
						break;
					case 'filterable':
						$dependencies .= '
			if ( ! isset( $assets["et_pb_filterable_portfolio"] ) ) {
				$assets_list["et_pb_filterable_portfolio"] = array(
					"css" => array(
						"{$assets_prefix}/css/filterable_portfolio{$cpt_suffix}.css",
						"{$assets_prefix}/css/portfolio{$cpt_suffix}.css",
						"{$assets_prefix}/css/grid_items{$cpt_suffix}.css",
						"{$assets_prefix}/css/overlay{$cpt_suffix}.css",
					),
				);
			}
						';
						break;
					case 'gallery':
						$dependencies .= '
			if ( ! isset( $assets["et_pb_gallery"] ) ) {
				$assets_list["et_pb_gallery"] = array(
					"css" => array(
						"{$assets_prefix}/css/gallery{$cpt_suffix}.css",
						"{$assets_prefix}/css/overlay{$cpt_suffix}.css",
						"{$assets_prefix}/css/grid_items{$cpt_suffix}.css",
						"{$assets_prefix}/css/slider_base{$cpt_suffix}.css",
						"{$assets_prefix}/css/slider_controls{$cpt_suffix}.css",
						"{$assets_prefix}/css/magnific_popup.css",
					),
				);
			}
						';
						break;
					case 'image':
						$dependencies .= '
			if ( ! isset( $assets["et_pb_image"] ) ) {
				$assets_list["et_pb_image"] = array(
					"css" => array(
						"{$assets_prefix}/css/image{$cpt_suffix}.css",
						"{$assets_prefix}/css/overlay{$cpt_suffix}.css",
					),
				);
			}
						';
						break;
					case 'map':
						$dependencies .= '
			if ( ! isset( $assets["et_pb_map"] ) ) {
				$assets_list["et_pb_map"] = array(
					"css" => "{$assets_prefix}/css/map{$cpt_suffix}.css",
				);
			}
						';
						break;
					case 'menu':
						$dependencies .= '
			if ( ! isset( $assets["et_pb_menu"] ) ) {
				$assets_list["et_pb_menu"] = array(
					"css" => array(
						"{$assets_prefix}/css/menus{$cpt_suffix}.css",
						"{$assets_prefix}/css/menu{$cpt_suffix}.css",
						"{$assets_prefix}/css/header_animations.css",
						"{$assets_prefix}/css/header_shared{$cpt_suffix}.css",
					),
				);
			}
						';
						break;
					case 'number':
						$dependencies .= '
			if ( ! isset( $assets["et_pb_number_counter"] ) ) {
				$assets_list["et_pb_number_counter"] = array(
					"css" => "{$assets_prefix}/css/number_counter{$cpt_suffix}.css",
				);
			}
						';
						break;
					case 'price':
						$dependencies .= '
			if ( ! isset( $assets["et_pb_pricing_tables"] ) ) {
				$assets_list["et_pb_pricing_tables"] = array(
					"css"  => array(
						"{$assets_prefix}/css/pricing_tables{$cpt_suffix}.css",
						"{$assets_prefix}/css/buttons{$cpt_suffix}.css",
					),
				);
			}
						';
						break;
					case 'search':
						$dependencies .= '
			if ( ! isset( $assets["et_pb_search"] ) ) {
				$assets_list["et_pb_search"] = array(
					"css" => "{$assets_prefix}/css/search{$cpt_suffix}.css",
				);
			}
						';
						break;
					case 'shop':
						$dependencies .= '
			if ( ! isset( $assets["et_pb_shop"] ) ) {
				$assets_list["et_pb_shop"] = array(
					"css" => array(
						"{$assets_prefix}/css/shop{$cpt_suffix}.css",
						"{$assets_prefix}/css/overlay{$cpt_suffix}.css",
					),
				);
			}
						';
						break;
					case 'slider':
						$dependencies .= '
			if ( ! isset( $assets["et_pb_slider"] ) ) {
				$assets_list["et_pb_slider"] = array(
					"css" => array(
						"{$assets_prefix}/css/slider{$cpt_suffix}.css",
						"{$assets_prefix}/css/slider_modules{$cpt_suffix}.css",
						"{$assets_prefix}/css/slider_base{$cpt_suffix}.css",
						"{$assets_prefix}/css/slider_controls{$cpt_suffix}.css",
						"{$assets_prefix}/css/buttons{$cpt_suffix}.css",
					),
				);
			}
						';
						break;
					case 'social':
						$dependencies .= '
			if ( ! isset( $assets["et_pb_social_media_follow"] ) ) {
				$assets_list["et_pb_social_media_follow"] = array(
					"css" => "{$assets_prefix}/css/social_media_follow{$cpt_suffix}.css",
				);
			}
						';
						break;
					case 'tabs':
						$dependencies .= '
			if ( ! isset( $assets["et_pb_tabs"] ) ) {
				$assets_list["et_pb_tabs"] = array(
					"css" => "{$assets_prefix}/css/tabs{$cpt_suffix}.css",
				);
			}
						';
						break;
					case 'team':
						$dependencies .= '
			if ( ! isset( $assets["et_pb_team_member"] ) ) {
				$assets_list["et_pb_team_member"] = array(
					"css" => array(
						"{$assets_prefix}/css/team_member{$cpt_suffix}.css",
						"{$assets_prefix}/css/legacy_animations{$cpt_suffix}.css",
					),
				);
			}
						';
						break;
					case 'testimonial':
						$dependencies .= '
			if ( ! isset( $assets["et_pb_testimonial"] ) ) {
				$assets_list["et_pb_testimonial"] = array(
					"css" => "{$assets_prefix}/css/testimonial{$cpt_suffix}.css",
				);
			}
						';
						break;
					case 'toggle':
						$dependencies .= '
			if ( ! isset( $assets["et_pb_toggle"] ) ) {
				$assets_list["et_pb_toggle"] = array(
					"css" => "{$assets_prefix}/css/toggle{$cpt_suffix}.css",
				);
			}
						';
						break;
					case 'video':
						$dependencies .= '
			if ( ! isset( $assets["et_pb_video"] ) ) {
				$assets_list["et_pb_video"] = array(
					"css"  => array(
						"{$assets_prefix}/css/video{$cpt_suffix}.css",
						"{$assets_prefix}/css/video_player{$cpt_suffix}.css",
					),
				);
			}
						';
						break;
				}
			}
		}

		if ( ! empty( $dependencies ) ) {
			$code = '
		public function require_divi_global_assets($assets_list){				
			$cpt_suffix    = et_is_cpt() ? "_cpt" : "";
			$assets_prefix = et_get_dynamic_assets_path();
			' . $dependencies . '	
			return $assets_list;
		}';
		} else {
			$code = '
		public function require_divi_global_assets($assets_list){
			return $assets_list;
		}';
		}

		return $code;
	}

}
