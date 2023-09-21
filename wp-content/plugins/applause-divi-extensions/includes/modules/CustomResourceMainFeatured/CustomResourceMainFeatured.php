<?php
/**
 * Basic Call To Action module (title, content, and button) with NO builder support
 * This module appears as placeholder box on Visual Builder
 *
 * @since 1.0.0
 */
class DICM_RESOURCE_MAIN_FEATURED extends ET_Builder_Module {
	// Module slug (also used as shortcode tag)
	public $slug       = 'dicm_resource_main_featured';
	public $vb_support = 'partial';
	

	/**
	 * Module properties initialization
	 *
	 * @since 1.0.0
	 */
	function init() {
		// Module name
		$this->name             = esc_html__( 'Applause Resources Featured', 'dicm-divi-custom-modules' );

		// Module Icon
		// This character will be rendered using etbuilder font-icon. For fully customized icon, create svg icon and
		// define its path on $this->icon_path property (see CustomCTAFull class)
		$this->icon             = 'j';

		$this->whitelisted_fields = array(
			'posts_number',
			// Add any other fields you want to include in the module
		);

        $this->fields_defaults = array(
			'posts_number' => array( '6' ),
			// Set default values for the fields
		  );
  
		$this->main_css_element = '%%order_class%%';
		$this->advanced_fields = array(
			'fonts' => array(),
			'text'  => array(),
			'background' => array(),
		);
	}

	/**
	 * Module's specific fields
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	function get_fields() {
		return array(
            'module_image' => array(
				'label'              => esc_html__( 'Upload Image', 'et_builder' ),
				'description'        => esc_html__( 'Upload Image of module', 'et_builder' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an Image', 'et_builder' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Image', 'et_builder' ),
            ),	           
			'type_of_doc' => array(
                'label' => esc_html__('Doc Type', 'et_builder'),
                'type' => 'text',
                'option_category' => 'basic_option',
                'description' => esc_html__('Enter the Doc Type.', 'et_builder'),
            ),	 		
			'title' => array(
                'label' => esc_html__('Title', 'et_builder'),
                'type' => 'text',
                'option_category' => 'basic_option',
                'description' => esc_html__('Enter the title.', 'et_builder'),
            ),	            
			'description' => array(
                'label' => esc_html__('Descriptions', 'et_builder'),
                'type' => 'text',
                'option_category' => 'basic_option',
                'description' => esc_html__('Enter the description.', 'et_builder'),
            ),			
			'button_type' => array(
				'type'        => 'select',
				'label'       => __( 'Button Type', 'et_builder' ),
				'default'     => 'secondary',
				'options'     => array(
					'primary'           => __( 'Primary', 'et_builder' ),
					'secondary'          => __( 'Secondary', 'et_builder' ),
				),
			),
			'button_text' => array(
                'label' => esc_html__('Button text', 'et_builder'),
                'type' => 'text',
                'option_category' => 'basic_option',
                'description' => esc_html__('Enter the button text.', 'et_builder'),
            ),				
			'button_url' => array(
                'label' => esc_html__('Module URL', 'et_builder'),
                'type' => 'text',
                'option_category' => 'basic_option',
                'description' => esc_html__('Enter the url.', 'et_builder'),
            ),	
		);
	}

	/**
	 * Module's advanced options configuration
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	function get_advanced_fields_config() {
		return array();
	}


	/**
	 * Render module output
	 *
	 * @since 1.0.0
	 *
	 * @param array  $attrs       List of unprocessed attributes
	 * @param string $content     Content being processed
	 * @param string $render_slug Slug of module that is used for rendering output
	 *
	 * @return string module's rendered output
	 */
	function render( $attrs, $content = null, $render_slug ) {
		// Module specific props added on $this->get_fields()
		$module_id 					= $this->props['module_id'];
		$posts_title                = $this->props['title'];
		$type_of_doc                = $this->props['type_of_doc'];
		$button_type             	= $this->props['button_type'];
		$resource_btn_text          = $this->props['button_text'];
		$resource_btn_url          	= $this->props['button_url'];
		$description          		= $this->props['description'];
		$module_image          		= $this->props['module_image'];

        // Render module content
        // Query posts based on module settings

		$output = '';
		
		// Output module content
		$output = '<div class="applasue-resources-eleements">';
			$output .= '<div id="resources-'.$module_id.'">';
				$output .= '<div class="resources-wrapper">';
					$output .= '<div class="custom-grid-not-required">';
						$output .= '<div class="medium:tw-text-left tw-text-center">';
							$output .= '<a class="hover" href="'.$resource_btn_url.'">';	
								$output .= '<img src="'.$module_image.'" alt="' . $posts_title . '" />';
								$output .= '<div class="tag-label tw-inline-block tw-mb-4 tw-bg-white">'.$type_of_doc.'</div>';

								$output .= '<h2 class="title as-h5">' . $posts_title . '</h2>';
								$output .= '<p class="tw-mb-4 tw-tracking-tight">'.$description.'</p>';
								if( $button_type == 'secondary' ) {
									$output .= '<span class="button is-secondary">'. $resource_btn_text .'</span>';
								} else {
									$output .= '<span class="button is-primary">'. $resource_btn_text .'</span>';
								}
							$output .= '</a>';
						$output .= '</div>';
					$output .= '</div>';

				$output .= '</div>';
			$output .= '</div>';
		$output .= '</div>';
		
		// Render wrapper
		// 3rd party module with no full VB support has to wrap its render output with $this->_render_module_wrapper().
		// This method will automatically add module attributes and proper structure for parallax image/video background
		return $this->_render_module_wrapper( $output, $render_slug );
	}
}

new DICM_RESOURCE_MAIN_FEATURED;
