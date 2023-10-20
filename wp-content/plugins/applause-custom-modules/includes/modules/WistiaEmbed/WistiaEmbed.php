<?php

class ACM_WistiaEmbed extends ET_Builder_Module
{
	public $slug       = 'acm_wistia_embed';
	public $vb_support = 'on';

	protected $module_credits = array(
		'module_uri' => 'https://www.applause.com',
		'author'     => 'Tammy Shipps',
		'author_uri' => 'https://www.applause.com',
	);

	public function init()
	{
		$this->name = esc_html__('Wistia Embed (New)', 'acm-applause-custom-modules');
		$this->icon = 'I';
		$this->main_css_element = '%%order_class%%';
	}

	public function get_fields()
	{
		return array(
			'wistia_id' => array(
				'label' => esc_html__('Wistia Video ID', 'acm-applause-custom-modules'),
				'type' => 'text',
				'option_category' => 'basic_option',
				'description' => esc_html__('Enter the Wistia Video ID.', 'acm-applause-custom-modules'),
			),
			'module_image' => array(
				'label'              => esc_html__('Video Placeholder Image', 'acm-applause-custom-modules'),
				'description'        => esc_html__('Upload Video Placeholder Image', 'acm-applause-custom-modules'),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__('Upload Video Placeholder Image', 'acm-applause-custom-modules'),
				'choose_text'        => esc_attr__('Choose Video Placeholder Image', 'acm-applause-custom-modules'),
				'update_text'        => esc_attr__('Set As Video Placeholder Image', 'acm-applause-custom-modules'),
			)
		);
	}

	public function render($attrs, $content = null, $render_slug)
	{
		global $pagenow;

		// Module specific props added on $this->get_fields()
		$module_image          		= $this->props['module_image'];
		$wistia_id          		= $this->props['wistia_id'];

		$image_url = $module_image;
		$cdn_url = 'https://bucketeer-57bb6f5e-ac50-48d6-a1b4-a5559e3736dc.s3.amazonaws.com/public';

		wp_enqueue_script('wistia-jsonp', "https://fast.wistia.com/embed/medias/" . $wistia_id . ".jsonp", array());
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

new ACM_WistiaEmbed;
