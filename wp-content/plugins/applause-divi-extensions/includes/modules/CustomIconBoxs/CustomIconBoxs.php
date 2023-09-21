<?php

/**
 * Basic Call To Action module (title, content, and button) with NO builder support
 * This module appears as placeholder box on Visual Builder
 *
 * @since 1.0.0
 */
class DICM_IMAGES_BOXS extends ET_Builder_Module
{
	// Module slug (also used as shortcode tag)
	public $slug       = 'dicm_custom_image_boxs';
	public $vb_support = 'partial';


	/**
	 * Module properties initialization
	 *
	 * @since 1.0.0
	 */
	function init()
	{
		// Module name
		$this->name             = esc_html__('Applause Image Box', 'dicm-divi-custom-modules');

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
	function get_fields()
	{
		return array(
			'module_image' => array(
				'label'              => esc_html__('Upload Image', 'et_builder'),
				'description'        => esc_html__('Upload Image of module', 'et_builder'),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__('Upload an Image', 'et_builder'),
				'choose_text'        => esc_attr__('Choose an Image', 'et_builder'),
				'update_text'        => esc_attr__('Set As Image', 'et_builder'),
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
			'button_text' => array(
				'label' => esc_html__('Button Text', 'et_builder'),
				'type' => 'text',
				'option_category' => 'basic_option',
				'description' => esc_html__('Enter the Text.', 'et_builder'),
			),
			'button_url' => array(
				'label' => esc_html__('Module URL', 'et_builder'),
				'type' => 'text',
				'option_category' => 'basic_option',
				'description' => esc_html__('Enter the url.', 'et_builder'),
			)
		);
	}

	/**
	 * Module's advanced options configuration
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	function get_advanced_fields_config()
	{
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
	function render($attrs, $content = null, $render_slug)
	{
		global $pagenow;
		
		// Module specific props added on $this->get_fields()
		$module_id 					= $this->props['module_id'];
		$posts_title                = $this->props['title'];
		$resource_btn_url          	= $this->props['button_url'];
		$description          		= $this->props['description'];
		$module_image          		= $this->props['module_image'];
		$module_text          		= $this->props['button_text'];

		// Render module content
		// Query posts based on module settings
		$image_url = $module_image;
		$cdn_url = 'https://bucketeer-57bb6f5e-ac50-48d6-a1b4-a5559e3736dc.s3.amazonaws.com/public';
		
		if ( strpos($module_image, site_url()) !== false ) {
			$image_url = str_replace(site_url(), $cdn_url, $module_image);
		} else if ( strpos($module_image, 'https://wordpress-12factor.herokuapp.com') !== false) {
			$image_url = str_replace('https://wordpress-12factor.herokuapp.com', $cdn_url, $module_image);
		}

		$output = '<a class="p-0 cta-link hover click-track tw-block" href="' . 
			$resource_btn_url . '" target="_blank"><img src="' . 
			$image_url . '" alt="" aria-hidden="true" /><div class="cta-title tw-mt-4 tw-mb-2 tw-text-base tw-text-gray-900">' . 
			$posts_title . '</div><p class="p-0 cta-description tw-text-gray-500">' . 
			$description . '</p><div class="tw-mt-2 tw-text-blue-500 tw-underline">' . 
			$module_text . '</div></a>'; 

		// Render wrapper
		// 3rd party module with no full VB support has to wrap its render output with $this->_render_module_wrapper().
		// This method will automatically add module attributes and proper structure for parallax image/video background
		return $this->_render_module_wrapper($output, $render_slug);
	}
}

new DICM_IMAGES_BOXS;
