<?php

/**
 * Basic Call To Action module (title, content, and button) with NO builder support
 * This module appears as placeholder box on Visual Builder
 *
 * @since 1.0.0
 */
class DICM_WISTIA_EMBED extends ET_Builder_Module
{
	// Module slug (also used as shortcode tag)
	public $slug       = 'dicm_wistia_embed';
	public $vb_support = 'partial';


	/**
	 * Module properties initialization
	 *
	 * @since 1.0.0
	 */
	function init()
	{
		// Module name
		$this->name             = esc_html__('Wistia Embed', 'dicm-divi-custom-modules');

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
			'wistia_id' => array(
				'label' => esc_html__('Wistia Video ID', 'et_builder'),
				'type' => 'text',
				'option_category' => 'basic_option',
				'description' => esc_html__('Enter the Wistia Video ID.', 'et_builder'),
			),
			'module_image' => array(
				'label'              => esc_html__('Video Placeholder Image', 'et_builder'),
				'description'        => esc_html__('Upload Video Placeholder Image', 'et_builder'),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__('Upload Video Placeholder Image', 'et_builder'),
				'choose_text'        => esc_attr__('Choose Video Placeholder Image', 'et_builder'),
				'update_text'        => esc_attr__('Set As Video Placeholder Image', 'et_builder'),
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
		$module_image          		= $this->props['module_image'];
        $wistia_id          		= $this->props['wistia_id'];

		$image_url = $module_image;
		$cdn_url = 'https://bucketeer-57bb6f5e-ac50-48d6-a1b4-a5559e3736dc.s3.amazonaws.com/public';

        wp_enqueue_script('wistia-jsonp', "https://fast.wistia.com/embed/medias/" . $wistia_id . ".jsonp" , array());
		wp_enqueue_script('wistia-script', "https://fast.wistia.com/assets/external/E-v1.js", array());


        $output = "<div class='wistia_embed wistia_async_{$wistia_id} popover=true window.wistiaDisableMux=true popoverContent=html wistia_embed_initialized' id='wistia-{$wistia_id}'>
			<a class='real-video-button video-play-button-careers' data-video='{$wistia_id}'>
				<svg class='play-button' width='100%' height='100%' viewBox='0 0 80 80' fill='none' xmlns='http://www.w3.org/2000/svg'><circle class='play-bg' cx='40' cy='40' r='40' fill='white' fill-opacity='0.8'/><path class='play-icon' d='M56 40L31.8712 53.8564L31.8712 26.1436L56 40Z' fill='#0272B4'/></svg>
				<div class='video-image tw-overflow-hidden tw-rounded-lg'>
					<img src='' data-src='{$image_url}' class='lazyload' />
				</div>
			</a>
			</div>";

		return $this->_render_module_wrapper($output, $render_slug);
	}
}

new DICM_WISTIA_EMBED;
