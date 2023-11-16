<?php

class DP_DMB_CMB2 {

	public function custom_modules_metaboxes() {

		$box_field         = '_dp_dmb_fieldbox_';
		$box_html          = '_dp_dmb_htmlbox_';
		$box_css           = '_dp_dmb_cssbox_';
		$box_js            = '_dp_dmb_jsbox_';
		$box_html_child    = '_dp_dmb_htmlchildbox_';
		$box_functions     = '_dp_dmb_functionsbox_';
		$box_before_render = '_dp_dmb_beforerenderbox_';
		$box_global_assets = '_dp_dmb_globalassetsbox_';

		/**
		 * Initiate the fields metabox
		 */
		$cmb = new_cmb2_box(
			array(
				'id'           => 'dp_dmb_fields_metabox',
				'title'        => __( 'Module Fields', 'dp_divi_module_builder' ),
				'object_types' => array( 'dp_custom_modules', ),
				'context'      => 'normal',
				'priority'     => 'high',
				'show_names'   => true,
			)
		);

		/*
		 * Initiate the fields for fields metabox
		 */
		$cmb->add_field( array(
			'desc' => __( 'Begin building your custom module by adding fields below. Fields are ideal for any data that a user might want to change or customize. Fields will be displayed in the module in the same order they appear below. Use the up and down arrows within each field box to move that field up or down and change the order.<br/><br/> The Field Identifier is how you will reference each field in your HTML output. Here are some examples of valid identifiers:<br/><em><strong>title</strong></em><br/><em><strong>image1</strong></em><br/><em><strong>background_color</strong></em> ', 'dp_divi_module_builder' ),
			'type' => 'title',
			'id'   => $box_field . 'title',
		) );

		$cmb->add_field( array(
			'desc' => __( '<b>Make this module fullwidth. Fullwidth modules are only available within fullwidth sections.</b>', 'dp_divi_module_builder' ),
			'id'   => $box_field . 'checkbox_fullwidth',
			'type' => 'checkbox',
		) );

		$cmb->add_field( array(
			'desc'    => __( '<b>Activate partial VB support for this module.</b> This option will turn on live preview via ajax in the Visual Builder. Custom javascript will NOT be rendered in live preview.', 'dp_divi_module_builder' ),
			'id'      => $box_field . 'partial_support',
			'type'    => 'checkbox',
			'default' => $this->cmb2_set_checkbox_default_for_new_module( true ),
		) );

		$cmb->add_field( array(
			'desc' => __( '<b>Add a TinyMCE editor to this module.</b> You can use <b>%%tiny_mce%%</b> to output the content inside HTML or <b>$this->content</b> to output the content inside PHP. If the module has repeat fields, the tiny_MCE editor can only be outputted in the Repeat Field HTML Output.', 'dp_divi_module_builder' ),
			'id'   => $box_field . 'checkbox_tiny_mce',
			'type' => 'checkbox',
		) );

		$cmb->add_field( array(
			'desc' => __( '<b>Activate Dynamic Content.</b> This option will activate Dynamic Content on the field type that support it. Currently not compatible with the Visual Builder.', 'dp_divi_module_builder' ),
			'id'   => $box_field . 'checkbox_dynamic_content',
			'type' => 'checkbox',
		) );

		$cmb->add_field( array(
			'desc' => __( '<b>Remove repeat fields wrapper.</b> This option will remove the div around repeat field groups. Design and Advanced tab settings will no longer apply to these fields.', 'dp_divi_module_builder' ),
			'id'   => $box_field . 'checkbox_repeat_fields',
			'type' => 'checkbox',
		) );

		$cmb->add_field( array(
			'name' => __( 'Repeat Group Label', 'dp_divi_module_builder' ),
			'desc' => __( 'Enter the Field Identifier of any repeat field which has a Field Type of Text. The value of this field will be used for the repeat field group label. If left empty, the Admin Label will be used.', 'dp_divi_module_builder' ),
			'id'   => $box_field . 'child_label',
			'type' => 'text',
		) );

		$group_field_id = $cmb->add_field( array(
			'id'      => $box_field . 'repeat_group_fields',
			'type'    => 'group',
			'options' => array(
				'group_title'   => __( 'Field {#}', 'dp_divi_module_builder' ),
				'add_button'    => __( 'Add Another Field', 'dp_divi_module_builder' ),
				'remove_button' => __( 'Remove Field', 'dp_divi_module_builder' ),
				'sortable'      => true,
				'closed'        => true,
			),
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name'       => __( 'Field Label', 'dp_divi_module_builder' ),
			'desc'       => __( 'The Field Label is the label that the end user will see to the left of the input in the module.', 'dp_divi_module_builder' ),
			'id'         => 'label',
			'type'       => 'text',
			'default'    => '',
			'attributes' => array(
				'placeholder' => 'Label for the field',
			),
			'classes'    => 'dp_dmb_field_label dp_dmb_fields_group',
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name'       => __( 'Field Identifier', 'dp_divi_module_builder' ),
			'desc'       => __( 'The Field Identifier must be unique. It will not be visible anywhere in the module. This is how you will reference the field in the HTML output. You should only use lowercase letters, numbers, and underscores for field identifiers. No spaces.', 'dp_divi_module_builder' ),
			'id'         => 'field_id',
			'type'       => 'text',
			'attributes' => array(
				'placeholder' => 'Identifier for the field',
			),
			'classes'    => 'dp_dmb_field_identifier dp_dmb_fields_group',
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name'       => __( 'Field Description (optional)', 'dp_divi_module_builder' ),
			'desc'       => __( 'A Field Description can be added to help explain the purpose of the field and will display below the input in the module.', 'dp_divi_module_builder' ),
			'id'         => 'description',
			'type'       => 'textarea_small',
			'attributes' => array(
				'placeholder' => 'Your description for the field',
			),
			'classes'    => 'dp_dmb_field_description dp_dmb_fields_group'
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name'    => __( 'Repeat Field', 'dp_divi_module_builder' ),
			'desc'    => __( 'Check this box if you want this field to be part of a repeatable field group for this module. You can use repeat fields to create "Add New Item" to your module (sliders, tabs, etc.) Multiple fields can be added to a repeatable field group.', 'dp_divi_module_builder' ),
			'id'      => 'field_child',
			'type'    => 'checkbox',
			'classes' => 'dp_dmb_field_child dp_dmb_fields_group',
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name'    => __( 'Responsive Field', 'dp_divi_module_builder' ),
			'desc'    => __( 'Check this box if you want this field to be a responsive field. Responsive fields can hold different values per screen size. To access responsive field values use _tablet and _phone suffixes on the field identifier.', 'dp_divi_module_builder' ),
			'id'      => 'field_responsive',
			'type'    => 'checkbox',
			'classes' => 'dp_dmb_field_responsive dp_dmb_fields_group',
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name'    => __( 'Field Group (optional)', 'dp_divi_module_builder' ),
			'desc'    => __( 'Enter the field group label you would like this field to display under in the module. Toggling this field group in the module will show/hide all fields with the same label. Field groups will display in the same order they are added.', 'dp_divi_module_builder' ),
			'id'      => 'group',
			'type'    => 'text',
			'classes' => 'dp_dmb_field_fieldgroup dp_dmb_fields_group'
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name'             => __( 'Field Type', 'dp_divi_module_builder' ),
			'desc'             => __( 'The Field Type will determine what type of input the user sees: text box, text area, color picker, image selector, etc.', 'dp_divi_module_builder' ),
			'id'               => 'type',
			'type'             => 'select',
			'show_option_none' => false,
			'options'          => array(
				'text'        => __( 'Text', 'dp_divi_module_builder' ),
				'textarea'    => __( 'Textarea', 'dp_divi_module_builder' ),
				'link'        => __( 'Link', 'dp_divi_module_builder' ),
				'image'       => __( 'Image', 'dp_divi_module_builder' ),
				'gallery'     => __( 'Gallery', 'dp_divi_module_builder' ),
				'video'       => __( 'Video', 'dp_divi_module_builder' ),
				'audio'       => __( 'Audio', 'dp_divi_module_builder' ),
				'color'       => __( 'Color', 'dp_divi_module_builder' ),
				'icon'        => __( 'Icon', 'dp_divi_module_builder' ),
				'select'      => __( 'Select', 'dp_divi_module_builder' ),
				'checkbox'    => __( 'Checkbox List', 'dp_divi_module_builder' ),
				'button'      => __( 'Button', 'dp_divi_module_builder' ),
				'yesno'       => __( 'Yes/No Toggle', 'dp_divi_module_builder' ),
				'date_picker' => __( 'Date Time', 'dp_divi_module_builder' ),
				'upload'      => __( 'Upload', 'dp_divi_module_builder' ),
				// TODO ask on Slack about the wpautop on code field type
				//'code'        => __( 'Code', 'dp_divi_module_builder' ),
			),
			'classes'          => 'dp_dmb_field_fieldtype dp_dmb_fields_group'
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name'    => __( 'Default Text (optional)', 'dp_divi_module_builder' ),
			'desc'    => __( 'Enter default text for this field.', 'dp_divi_module_builder' ),
			'id'      => 'field_default_text',
			'type'    => 'text',
			'classes' => 'dp_dmb_field_default_text dp_dmb_fields_group',
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name'       => __( 'Default Text (optional)', 'dp_divi_module_builder' ),
			'desc'       => __( 'Enter default text for this field.', 'dp_divi_module_builder' ),
			'id'         => 'field_default_textarea',
			'type'       => 'textarea',
			'attributes' => array(
				'rows' => '2',
			),
			'classes'    => 'dp_dmb_field_default_textarea dp_dmb_fields_group',
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name'    => __( 'Default Text (optional)', 'dp_divi_module_builder' ),
			'desc'    => __( 'Enter default text for this button.', 'dp_divi_module_builder' ),
			'id'      => 'field_default_button',
			'type'    => 'text',
			'classes' => 'dp_dmb_field_default_button dp_dmb_fields_group',
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name'       => __( 'Default Color (optional)', 'dp_divi_module_builder' ),
			'desc'       => __( 'Select a default color.', 'dp_divi_module_builder' ),
			'id'         => 'field_default_color',
			'type'       => 'colorpicker',
			'options'    => array( 'alpha' => true ),
			'attributes' => array(
				'rows' => '2',
			),
			'classes'    => 'dp_dmb_field_default_color dp_dmb_fields_group',
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name'    => __( 'Default State', 'dp_divi_module_builder' ),
			'desc'    => __( 'Check if you want the toggle to be active by default', 'dp_divi_module_builder' ),
			'id'      => 'field_default_state',
			'type'    => 'checkbox',
			'classes' => 'dp_dmb_field_default_state dp_dmb_fields_group',
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name'    => __( 'Default Image', 'dp_divi_module_builder' ),
			'desc'    => __( 'Check this box if you want to enable a default image.', 'dp_divi_module_builder' ),
			'id'      => 'field_default_image',
			'type'    => 'checkbox',
			'classes' => 'dp_dmb_field_default_image dp_dmb_fields_group',
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name'    => __( 'Default Image URL', 'dp_divi_module_builder' ),
			'desc'    => __( 'Type in the URL to the image you would like to display. Leave this blank if you would like to display the default Divi placeholder image.', 'dp_divi_module_builder' ),
			'id'      => 'field_default_image_url',
			'type'    => 'text',
			'classes' => 'dp_dmb_field_default_image_url dp_dmb_fields_group',
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name'       => __( 'Select Options', 'dp_divi_module_builder' ),
			'desc'       => __( 'Enter a comma-separated list of select options labels.', 'dp_divi_module_builder' ),
			'id'         => 'field_select_options',
			'type'       => 'textarea',
			'attributes' => array(
				'rows' => '2',
			),
			'classes'    => 'dp_dmb_field_select_options dp_dmb_fields_group',
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name'       => __( 'Select Values', 'dp_divi_module_builder' ),
			'desc'       => __( 'Enter a comma-separated list of select options values. If left empty or if number of values does not match number of labels above, the value of the first field option will be 1 and the values for all other options will increase by 1 in the order you list them.', 'dp_divi_module_builder' ),
			'id'         => 'field_select_options_values',
			'type'       => 'textarea',
			'attributes' => array(
				'rows' => '2',
			),
			'classes'    => 'dp_dmb_field_select_options_values dp_dmb_fields_group',
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name'    => __( 'Get Options From Function', 'dp_divi_module_builder' ),
			'desc'    => __( 'Enter the name of a custom function that will return an array containing the option labels and values of the select. The key of each value on this array will be the value of each option.', 'dp_divi_module_builder' ),
			'id'      => 'field_select_options_function',
			'type'    => 'text',
			'classes' => 'dp_dmb_field_select_options_function dp_dmb_fields_group',
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name'    => __( 'Checkbox Labels', 'dp_divi_module_builder' ),
			'desc'    => __( 'Enter a comma-separated list of checkbox labels. The return value will be a string of (on|off) values that will indicate which labels are checked. Use ${field_identifier}_labels on PHP output to access array of selected labels.', 'dp_divi_module_builder' ),
			'id'      => 'field_checkbox_values',
			'type'    => 'text',
			'classes' => 'dp_dmb_field_checkbox_values dp_dmb_fields_group',
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name'    => __( 'Process Checkbox Values', 'dp_divi_module_builder' ),
			'desc'    => __( 'Return array of checked options values instead of the (on|off) string.', 'dp_divi_module_builder' ),
			'id'      => 'field_checkbox_process',
			'type'    => 'checkbox',
			'classes' => 'dp_dmb_field_checkbox_process dp_dmb_fields_group',
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name'    => __( 'Design Tab', 'dp_divi_module_builder' ),
			'desc'    => __( 'Check this box if you want to show fonts controls in the Design Tab of the module for this field.', 'dp_divi_module_builder' ),
			'id'      => 'field_show_design',
			'type'    => 'checkbox',
			'classes' => 'dp_dmb_field_show_design dp_dmb_fields_group',
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name'    => __( 'Button URL', 'dp_divi_module_builder' ),
			'desc'    => __( 'Check this box if you want the button URL to be the_permalink(). If left unchecked, an URL input will be added to the module.', 'dp_divi_module_builder' ),
			'id'      => 'field_buttom_permalink',
			'type'    => 'checkbox',
			'classes' => 'dp_dmb_field_buttom_permalink dp_dmb_fields_group',
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name'    => __( 'Advanced Tab', 'dp_divi_module_builder' ),
			'desc'    => __( 'Check this box if you want to show a custom css box in the Advanced Tab of the module for this field.', 'dp_divi_module_builder' ),
			'id'      => 'field_custom_css',
			'type'    => 'checkbox',
			'classes' => 'dp_dmb_field_custom_css dp_dmb_fields_group',
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name'    => __( 'Hide Field', 'dp_divi_module_builder' ),
			'desc'    => __( 'Check this box if you want to hide the input for this field in the Content tab.', 'dp_divi_module_builder' ),
			'id'      => 'field_hide_content',
			'type'    => 'checkbox',
			'classes' => 'dp_dmb_field_hide_content dp_dmb_fields_group',
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name'    => __( 'Allow Date Format', 'dp_divi_module_builder' ),
			'desc'    => __( 'Check this box if you want to give the user the option to change the date format.', 'dp_divi_module_builder' ),
			'id'      => 'field_allow_date_format',
			'type'    => 'checkbox',
			'classes' => 'dp_dmb_field_allow_date_format dp_dmb_fields_group',
		) );

		/**
		 * Initiate the html output metabox
		 */
		$cmb = new_cmb2_box(
			array(
				'id'           => 'dp_dmb_html_metabox',
				'title'        => __( 'HTML Output', 'dp_divi_module_builder' ),
				'object_types' => array( 'dp_custom_modules', ), // Post type
				'context'      => 'normal',
				'priority'     => 'high',
				'show_names'   => true,
			)
		);

		/*
		 * Initiate the fields for html output metabox
		 */
		$cmb->add_field( array(
			'desc' => __( 'The HTML Output is where you will add your custom HTML, PHP and available Field Identifiers created in the left panel. Each Field Identifier needs to be begin and end with <strong><em>%%</em></strong> if included inside HTML. If included inside the PHP tags, simply add a <strong><em>$</em></strong> in front of the identifier. This is how you would include a field with "title" set as the identifier:<br/><strong><em>Inside HTML</em></strong><br/>%%title%%<br/><strong><em>Inside PHP:</em></strong><br/>$title<br/><br/><strong>* IMPORTANT - </strong> If you add invalid PHP markup inside the PHP tags and get locked out of editing this module, simply disable PHP for this module from the Settings page. <br/>Use CTRL+spacebar to autocomplete PHP.', 'dp_divi_module_builder' ),
			'type' => 'title',
			'id'   => $box_html . 'title',
		) );

		$cmb->add_field( array(
			'desc'   => __( '<b>Activate PHP processing</b>', 'dp_divi_module_builder' ),
			'id'     => $box_html . 'checkbox_php_onoff',
			'type'   => 'checkbox',
			'column' => array(
				'position' => 2,
				'name'     => 'PHP',
			),
		) );

		$expand_html = '<span class="dmb-expand-panel dashicons dashicons-editor-expand"></span><span class="dmb-contract-panel dashicons dashicons-editor-contract"></span>';

		$cmb->add_field( array(
			'name'            => __( 'HTML Output', 'dp_divi_module_builder' ) . $expand_html,
			'id'              => $box_html . 'textarea_html_output',
			'type'            => 'textarea_code',
			'sanitization_cb' => false,
			'attributes'      => array(
				'rows' => '20',
			),
			'options'         => array( 'disable_codemirror' => true )
		) );

		$cmb->add_field( array(
			'name' => 'HTML',
			'type' => 'title',
			'desc' => ' ',
			'id'   => $box_html . 'keyboard',
		) );

		$cmb->add_field( array(
			'name' => 'PHP',
			'desc' => '<span class="dp_dmb_kb_btn" data-type="php">Post Loop</span><span class="dp_dmb_kb_btn" data-type="php">New Query</span><span class="dp_dmb_kb_btn" data-type="php">Divi Excerpt</span>',
			'type' => 'title',
			'id'   => $box_html . 'keyboard2',
		) );

		/**
		 * Initiate the css output metabox
		 */
		$cmb = new_cmb2_box(
			array(
				'id'           => 'dp_dmb_css_metabox',
				'title'        => __( 'CSS Output', 'dp_divi_module_builder' ),
				'object_types' => array( 'dp_custom_modules', ), // Post type
				'context'      => 'normal',
				'priority'     => 'high',
				'show_names'   => true,
			)
		);

		/*
		 * Initiate the fields for css output metabox
		 */
		$cmb->add_field( array(
			'desc' => __( 'If you have CSS rules you would like to add, place them in the textbox below. Add your classes to the HTML output above, and then target them in this section.', 'dp_divi_module_builder' ),
			'type' => 'title',
			'id'   => $box_css . 'title',
		) );

		$cmb->add_field( array(
			'name'            => __( 'CSS Output', 'dp_divi_module_builder' ) . $expand_html,
			'id'              => $box_css . 'textarea_css_output',
			'type'            => 'textarea_code',
			'sanitization_cb' => false,
			'attributes'      => array(
				'rows' => '20',
			),
			'options'         => array( 'disable_codemirror' => true )
		) );

		/**
		 * Initiate the js output metabox
		 */
		$cmb = new_cmb2_box(
			array(
				'id'           => 'dp_dmb_js_metabox',
				'title'        => __( 'Javascript Output', 'dp_divi_module_builder' ),
				'object_types' => array( 'dp_custom_modules', ), // Post type
				'context'      => 'normal',
				'priority'     => 'high',
				'show_names'   => true,
			)
		);

		/*
		 * Initiate the fields for js output metabox
		 */
		$cmb->add_field( array(
			'desc' => __( 'If you have javascript or jQuery functions you would like to add, place them in the textbox below. The code added this section is already wrapped in script tags so DO NOT include them. ', 'dp_divi_module_builder' ),
			'type' => 'title',
			'id'   => $box_js . 'title',
		) );

		$cmb->add_field( array(
			'name'            => __( 'Javascript Output', 'dp_divi_module_builder' ) . $expand_html,
			'id'              => $box_js . 'textarea_js_output',
			'type'            => 'textarea_code',
			'sanitization_cb' => false,
			'attributes'      => array(
				'rows' => '20',
			),
			'options'         => array( 'disable_codemirror' => true )
		) );

		/**
		 * Initiate the child html output metabox
		 */
		$cmb = new_cmb2_box(
			array(
				'id'           => 'dp_dmb_htmlchild_metabox',
				'title'        => __( 'Repeat Fields HTML Output', 'dp_divi_module_builder' ),
				'object_types' => array( 'dp_custom_modules', ),
				'context'      => 'normal',
				'priority'     => 'low',
				'show_names'   => true,
			)
		);
		/*
		 * Initiate the fields for child html output metabox
		 */
		$cmb->add_field( array(
			'desc' => __( 'The Repeat Fields HTML Output is where you will add your custom HTML, PHP and available Field Identifiers created in the left panel for the repeat field groups. This section is similar to a foreach loop. To echo the results of this loop, you then need to add <b>%%repeat_fields%%</b> inside HTML or <b>$this->content</b> inside PHP in the HTML Output section at the top of this panel.<br/><br />The code entered in this section will get wrapped in a div container with class <strong>dp_dmb_repeat_item</strong>.', 'dp_divi_module_builder' ),
			'type' => 'title',
			'id'   => $box_html_child . 'title',
		) );

		$cmb->add_field( array(
			'name'            => __( 'Repeat Fields HTML Output', 'dp_divi_module_builder' ) . $expand_html,
			'id'              => $box_html_child . 'textarea_htmlchild_output',
			'type'            => 'textarea_code',
			'sanitization_cb' => false,
			'options'         => array( 'disable_codemirror' => true )
		) );

		$cmb->add_field( array(
			'name' => 'HTML',
			'type' => 'title',
			'desc' => ' ',
			'id'   => $box_html_child . 'keyboard',
		) );

		$cmb->add_field( array(
			'name' => 'PHP',
			'desc' => '<span class="dp_dmb_kb_btn child" data-type="php">Post Loop</span><span class="dp_dmb_kb_btn child" data-type="php">New Query</span><span class="dp_dmb_kb_btn child" data-type="php">Divi Excerpt</span>',
			'type' => 'title',
			'id'   => $box_html_child . 'keyboard2',
		) );

		/**
		 * Initiate the custom functions metabox
		 */
		$cmb = new_cmb2_box(
			array(
				'id'           => 'dp_dmb_functions_metabox',
				'title'        => __( 'Add Custom Functions', 'dp_divi_module_builder' ),
				'object_types' => array( 'dp_custom_modules', ),
				'context'      => 'normal',
				'priority'     => 'low',
				'show_names'   => true,
			)
		);
		/*
		 * Initiate the fields for custom functions metabox
		 */
		$cmb->add_field( array(
			'desc' => __( "Add custom functions below, which can be accessed in the module's output boxes. You can use standard PHP tags &lt?php and ?&gt within your functions. Be sure to begin and end the box below with PHP tags and activate PHP processing in your module. If you add invalid PHP markup inside the PHP tags and get locked out of editing this module, simply disable PHP for this module from the Settings page.", 'dp_divi_module_builder' ),
			'type' => 'title',
			'id'   => $box_functions . 'title',
		) );

		$cmb->add_field( array(
			'name'            => __( 'Custom Functions Code', 'dp_divi_module_builder' ) . $expand_html,
			'id'              => $box_functions . 'textarea_functions_output',
			'type'            => 'textarea_code',
			'default'         => '<?php ?>',
			'sanitization_cb' => false,
			'options'         => array( 'disable_codemirror' => true )
		) );

		/**
		 * Initiate the before metabox
		 */
		$cmb = new_cmb2_box(
			array(
				'id'           => 'dp_dmb_before_render_metabox',
				'title'        => __( 'Before Render', 'dp_divi_module_builder' ),
				'object_types' => array( 'dp_custom_modules', ),
				'context'      => 'normal',
				'priority'     => 'low',
				'show_names'   => true,
			)
		);
		/*
		 * Initiate the fields for before render metabox
		 */
		$cmb->add_field( array(
			'desc' => __( "Code added here will be included in the module's before render function. This is useful for adding functionality needed before the module renders the output. EX: enqueue script/styles. Use \$this->props to access the module's field values. <b>Use only PHP code here without open/closing tabs.</b>", 'dp_divi_module_builder' ),
			'type' => 'title',
			'id'   => $box_before_render . 'title',
		) );

		$cmb->add_field( array(
			'name'            => __( 'Before Render Code', 'dp_divi_module_builder' ) . $expand_html,
			'id'              => $box_before_render . 'textarea_before_render_output',
			'type'            => 'textarea_code',
			'default'         => '',
			'sanitization_cb' => false,
			'options'         => array( 'disable_codemirror' => true )
		) );

		/**
		 * Initiate the global assets metabox
		 */
		$cmb = new_cmb2_box(
			array(
				'id'           => 'dp_dmb_global_assets_metabox',
				'title'        => __( 'Divi Global Assets', 'dp_divi_module_builder' ),
				'object_types' => array( 'dp_custom_modules', ),
				'context'      => 'normal',
				'priority'     => 'low',
				'show_names'   => true,
			)
		);
		/*
		 * Initiate the fields for global assets metabox
		 */
		$cmb->add_field( array(
			'desc' => __( "Select the global Divi dependencies needed for the module.", 'dp_divi_module_builder' ),
			'type' => 'title',
			'id'   => $box_global_assets . 'title',
		) );

		$cmb->add_field( array(
			'name'              => __( "Global", 'dp_divi_module_builder' ),
			'id'                => $box_global_assets . 'features',
			'type'              => 'multicheck_inline',
			'select_all_button' => false,
			'options'           => array(
				'fonts'          => __( "Divi Icons & Font Awesome", 'dp_divi_module_builder' ),
				'magnific_popup' => __( "Magnific Popup CSS/JS", 'dp_divi_module_builder' ),
				'overlay'        => __( "Overlay", 'dp_divi_module_builder' ),
				'gutters'        => __( "Column Gutters", 'dp_divi_module_builder' ),
				'grid_items'     => __( "Grid Items", 'dp_divi_module_builder' ),
				'animations'     => __( "Animations", 'dp_divi_module_builder' )
			),
		) );

		$cmb->add_field( array(
			'name'              => __( "Modules", 'dp_divi_module_builder' ),
			'id'                => $box_global_assets . 'modules',
			'type'              => 'multicheck_inline',
			'select_all_button' => false,
			'options'           => array(
				'accordion'   => __( "Accordion", 'dp_divi_module_builder' ),
				'audio'       => __( "Audio", 'dp_divi_module_builder' ),
				'blog'        => __( "Blog", 'dp_divi_module_builder' ),
				'blurb'       => __( "Blurb", 'dp_divi_module_builder' ),
				'button'      => __( "Button", 'dp_divi_module_builder' ),
				'circle'      => __( "Circle Counter", 'dp_divi_module_builder' ),
				'form'        => __( "Contact Form", 'dp_divi_module_builder' ),
				'countdown'   => __( "Countdown Timer", 'dp_divi_module_builder' ),
				'counter'     => __( "Counter", 'dp_divi_module_builder' ),
				'cta'         => __( "CTA", 'dp_divi_module_builder' ),
				'divider'     => __( "Divider", 'dp_divi_module_builder' ),
				'filterable'  => __( "Filterable Portfolio", 'dp_divi_module_builder' ),
				'gallery'     => __( "Gallery", 'dp_divi_module_builder' ),
				'image'       => __( "Image", 'dp_divi_module_builder' ),
				'map'         => __( "Map", 'dp_divi_module_builder' ),
				'menu'        => __( "Menu", 'dp_divi_module_builder' ),
				'number'      => __( "Number Counter", 'dp_divi_module_builder' ),
				'price'       => __( "Pricing Tables", 'dp_divi_module_builder' ),
				'search'      => __( "Search", 'dp_divi_module_builder' ),
				'shop'        => __( "Shop", 'dp_divi_module_builder' ),
				'slider'      => __( "Slider", 'dp_divi_module_builder' ),
				'social'      => __( "Social Media", 'dp_divi_module_builder' ),
				'tabs'        => __( "Tabs", 'dp_divi_module_builder' ),
				'team'        => __( "Team Member", 'dp_divi_module_builder' ),
				'testimonial' => __( "Testimonials", 'dp_divi_module_builder' ),
				'toggle'      => __( "Toggle", 'dp_divi_module_builder' ),
				'video'       => __( "Video", 'dp_divi_module_builder' ),
			),
		) );
	}

	public function cmb2_set_checkbox_default_for_new_module( $default ) {
		// phpcs:ignore
		return isset( $_GET['post'] ) ? '' : ( $default ? (string) $default : '' );
	}

	public function external_dependency_metabox() {

		$box_external = '_dp_dmb_external_cssjs_metabox_';

		$cmb = new_cmb2_box( array(
			'id'           => 'dp_dmb_external_cssjs_metabox',
			'title'        => esc_html__( 'Divi Module Builder - External CSS/JS', 'dp_divi_module_builder' ),
			'object_types' => array( 'options-page' ),
			'option_key'   => 'dp_dmb_dependency_options',
			'menu_title'   => esc_html__( 'Dependency', 'dp_divi_module_builder' ),
			'parent_slug'  => 'edit.php?post_type=dp_custom_modules',
			'capability'   => 'manage_options',
			'save_button'  => esc_html__( 'Save Options', 'dp_divi_module_builder' ),
			'show_names'   => true,
		) );

		$cmb->add_field( array(
			'desc' => __( 'Here you can add 3rd party CSS and Javascript dependencies, which can be added using external or internal URLs. Dependencies will be properly enqueued in WordPress and available on any page. For example, to include Font Awesome: <br><br><strong>Select Type:</strong> CSS<br><strong>Dependency Name:</strong> fontawesome<br><strong>Dependency URL:</strong> https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css<br>', 'dp_divi_module_builder' ),
			'type' => 'title',
			'id'   => $box_external . 'title',
		) );

		$group_field_id = $cmb->add_field( array(
			'id'      => $box_external . 'repeat_group_fields',
			'type'    => 'group',
			'options' => array(
				'group_title'   => __( 'Dependency {#}', 'dp_divi_module_builder' ),
				'add_button'    => __( 'Add Another Dependency', 'dp_divi_module_builder' ),
				'remove_button' => __( 'Remove Dependency', 'dp_divi_module_builder' ),
				'sortable'      => true,
			),
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name'    => __( 'Select Type', 'dp_divi_module_builder' ),
			'id'      => 'type',
			'type'    => 'select',
			'options' => array(
				'css' => 'Cascade Style Sheet',
				'js'  => 'Javascript'
			),
			'classes' => 'dp_dmb_external_type',
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name'       => __( 'Dependency Name', 'dp_divi_module_builder' ),
			'id'         => 'name',
			'type'       => 'text',
			'attributes' => array(
				'type' => 'text',
			),
			'classes'    => 'dp_dmb_external_name',
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name'       => __( 'Dependency Url', 'dp_divi_module_builder' ),
			'id'         => 'url',
			'type'       => 'text',
			'attributes' => array(
				'type' => 'url',
			),
			'classes'    => 'dp_dmb_external_url',
		) );
	}

	public function debugger_metabox() {

		$box_debug = '_dp_dmb_debugger_';

		$cmb = new_cmb2_box( array(
			'id'           => 'dp_dmb_debugger',
			'title'        => __( 'Divi Module Builder - Debugger', 'dp_divi_module_builder' ),
			'object_types' => array( 'options-page' ),
			'option_key'   => 'dp_dmb_debug',
			'menu_title'   => __( 'Debugger', 'dp_divi_module_builder' ),
			'parent_slug'  => 'edit.php?post_type=dp_custom_modules',
			'capability'   => 'manage_options',
			'save_button'  => __( 'Save Options', 'dp_divi_module_builder' ),
			'show_names'   => true,
		) );

		$cmb->add_field( array(
			'desc' => __( 'Click on the checkbox and button below to begin debugging. The debugger will run in the background while using the Divi Module Builder, keeping a log of all successful and failed processes in the output below. If you continue to have issues or see any errors in the output, please contact us and include the output in your message.', 'dp_divi_module_builder' ),
			'type' => 'title',
			'id'   => $box_debug . 'title',
		) );

		$cmb->add_field( array(
			'desc' => __( 'Activate DMB Debugger and start collect info.', 'dp_divi_module_builder' ),
			'id'   => $box_debug . 'active',
			'type' => 'checkbox',
		) );

		$cmb->add_field( array(
			'name'            => __( 'Debugger Output', 'dp_divi_module_builder' ),
			'id'              => $box_debug . 'debugger_output',
			'type'            => 'textarea',
			'sanitization_cb' => false,
			// phpcs:ignore
			'default'         => ( file_exists( DPDMB_MODULES_DIR . '/dmb-debug.log' ) ? file_get_contents( DPDMB_MODULES_DIR . '/dmb-debug.log' ) : "" ),
			'attributes'      => array(
				'rows'     => '10',
				'disabled' => 'true'
			),
		) );
	}

}
