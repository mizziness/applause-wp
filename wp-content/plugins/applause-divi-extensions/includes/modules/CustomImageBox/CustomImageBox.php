<?php
/**
 * Basic Call To Action module (title, content, and button) with NO builder support
 * This module appears as placeholder box on Visual Builder
 *
 * @since 1.0.0
 */
class DICM_ICON_BOXS extends ET_Builder_Module {
	// Module slug (also used as shortcode tag)
	public $slug       = 'dicm_custom_icon_boxs';
	public $vb_support = 'partial';
	

	/**
	 * Module properties initialization
	 *
	 * @since 1.0.0
	 */
	function init() {
		// Module name
		$this->name             = esc_html__( 'Applause Icon Box', 'dicm-divi-custom-modules' );

		// Module Icon
		// This character will be rendered using etbuilder font-icon. For fully customized icon, create svg icon and
		// define its path on $this->icon_path property (see CustomCTAFull class)
		$this->icon             = 'j';
  
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
			'button_url' => array(
                'label' => esc_html__('Module URL', 'et_builder'),
                'type' => 'text',
                'option_category' => 'basic_option',
                'description' => esc_html__('Enter the url.', 'et_builder'),
            ),	
			'module_type' => array(
				'type'        => 'select',
				'label'       => __( 'Module Type', 'et_builder' ),
				'default'     => 'secondary',
				'options'     => array(
					'menu'           => __( 'For Menu', 'et_builder' ),
					'title'          => __( 'Title', 'et_builder' ),
				),
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
		$resource_btn_url          	= $this->props['button_url'];
		$description          		= $this->props['description'];
		$module_image          		= $this->props['module_image'];
		$module_type          		= $this->props['module_type'];

        // Render module content
        // Query posts based on module settings

		// Render module content
		// Query posts based on module settings
		$image_url = $module_image;
		$cdn_url = 'https://bucketeer-57bb6f5e-ac50-48d6-a1b4-a5559e3736dc.s3.amazonaws.com/public';
		
		if ( strpos($module_image, site_url()) !== false ) {
			$image_url = str_replace(site_url(), $cdn_url, $module_image);
		} else if ( strpos($module_image, 'https://wordpress-12factor.herokuapp.com') !== false) {
			$image_url = str_replace('https://wordpress-12factor.herokuapp.com', $cdn_url, $module_image);
		}

		if( $module_type == 'title' ) {
			if( empty($resource_btn_url ) ) {
				$output = '<div class="no-link medium:tw-mb-2 medium:tw-pb-2 medium:tw-pt-0 tw-flex tw-pt-1 tw-mr-4 tw-text-xs tw-text-gray-500 tw-uppercase tw-border-b tw-border-solid tw-border-gray-200">
				<span class="tw-flex"><img class="nav-icon tw-inline-block ls-is-cached lazyloaded" aria-hidden="true" alt="" src="'.$image_url.'"><div><div class="link-title tw-text-xs tw-uppercase">' . $posts_title . '</div></div>
				</span></div>';
			} else {
				$output = '<a class="mini-title click-track medium:tw-mb-1 medium:tw-pt-0 tw-flex tw-pt-1 tw-mr-4 tw-text-gray-500 tw-border-b tw-border-solid tw-border-gray-200" href="'.$resource_btn_url.'" target=""><span class="tw-flex"><img class="nav-icon tw-inline-block ls-is-cached lazyloaded" aria-hidden="true" alt="" src="'.$image_url.'"><div><div class="link-title carat tw-text-xs tw-uppercase">' . $posts_title . '</div></div>
				</span></a>';
			}
		} else {
			$output = '<a href="'.$resource_btn_url.'" class="inner-link hover click-track tw-flex" data-category="Header"><img alt="" src="'.$image_url.'"><div><div class="p-1 link-title tw-font-normal tw-text-gray-900">' . $posts_title . '</div><p class="p-0 tw-mb-0 link-description tw-text-gray-500">'.$description.'</p></div></a>';
		}

				
		// Render wrapper
		// 3rd party module with no full VB support has to wrap its render output with $this->_render_module_wrapper().
		// This method will automatically add module attributes and proper structure for parallax image/video background
		return $this->_render_module_wrapper( $output, $render_slug );
	}
}

new DICM_ICON_BOXS;
