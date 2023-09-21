<?php

/**
 * Basic Call To Action module (title, content, and button) with NO builder support
 * This module appears as placeholder box on Visual Builder
 *
 * @since 1.0.0
 */
class DICM_HYPE_EMBED extends ET_Builder_Module
{
	// Module slug (also used as shortcode tag)
	public $slug       = 'dicm_hype_embed';
	public $vb_support = 'off';


	/**
	 * Module properties initialization
	 *
	 * @since 1.0.0
	 */
	function init()
	{
		// Module name
		$this->name             = esc_html__('Hype Embed', 'dicm-divi-custom-modules');

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
				'label'              => esc_html__('Upload Hype Asset', 'et_builder'),
				'description'        => esc_html__('Upload and embed a Hype4 asset', 'et_builder'),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__('Upload a Hype', 'et_builder'),
				'choose_text'        => esc_attr__('Choose a Hype', 'et_builder'),
				'update_text'        => esc_attr__('Set As Hype Asset', 'et_builder'),
			)
			// 'description' => array(
			// 	'label' => esc_html__('Descriptions', 'et_builder'),
			// 	'type' => 'text',
			// 	'option_category' => 'basic_option',
			// 	'description' => esc_html__('Enter the description.', 'et_builder'),
			// ),
			// 'button_text' => array(
			// 	'label' => esc_html__('Button Text', 'et_builder'),
			// 	'type' => 'text',
			// 	'option_category' => 'basic_option',
			// 	'description' => esc_html__('Enter the Text.', 'et_builder'),
			// ),
			// 'button_url' => array(
			// 	'label' => esc_html__('Module URL', 'et_builder'),
			// 	'type' => 'text',
			// 	'option_category' => 'basic_option',
			// 	'description' => esc_html__('Enter the url.', 'et_builder'),
			// )
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

	
	function get_attachment_id_by_filename($filename)
	{
		global $wpdb;
		$mktID = "%" . explode("-", $filename)[0] . "%";

		$sql = $wpdb->prepare("SELECT * FROM `wp_posts` WHERE `guid` LIKE %s ORDER BY post_date, id DESC LIMIT 1", array($mktID));
		$attachments = $wpdb->get_results($sql, OBJECT);
		return $attachments[0]->ID ?? false;
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
		$module_image          		= $this->props['module_image'];

		$zip = basename( $module_image );
		$attachment_id = get_attachment_id_by_filename( $zip ) ?? false;

		if ( $attachment_id == false ) {
			return new \WP_Error('attachment_id-error', 'The attachment_id was not found.');
		}

		$attachment = get_post( $attachment_id );
		$mkt_id = explode("-", $attachment->post_name)[0];

		// Zip Uploads ->
		// https://bucketeer-57bb6f5e-ac50-48d6-a1b4-a5559e3736dc.s3.amazonaws.com/public/wp-content/uploads/mkt0675-dev-qa-business-benefits-en-v8.zip

		$s3Folder = "https://" . getenv('BUCKETEER_BUCKET_NAME') . ".s3.amazonaws.com/public/wp-content/uploads/hype4/" . $mkt_id . "/Default/extracted.html";
		$extracted = file_get_contents( $s3Folder ) ?? false;
		$output = false;

		if ( strlen($extracted) > 0 ) {
			$extracted = str_replace( array( '><![CDATA[', ']]></script>'), array( '>', '</script>' ), $extracted );
			$output = $extracted;
		}

		// $zip = basename( $module_image );
		// $attachment_id = get_attachment_id_by_filename( $zip ) ?? false;

		// if ( $attachment_id == false ) {
		// 	return new \WP_Error('attachment_id-error', 'The attachment_id was not found.');
		// }

		// https://bucketeer-57bb6f5e-ac50-48d6-a1b4-a5559e3736dc.s3.amazonaws.com/public/wp-content/uploads/mkt0675-dev-qa-business-benefits-en-v8-2.zip

		// $meta = get_post_meta($attachment_id, "hype_content")[0] ?? false;

		// if ( $meta == false || strlen($meta) <= 0 ) {
		// 	return new \WP_Error('attachment_id-error', 'The attachment_id was not found.');
		// }

		// $meta = str_replace( array( '<![CDATA[', ']]></script>'), array( '', '</script>' ), $meta );

		return $this->_render_module_wrapper($output, $render_slug);
	}
}

new DICM_HYPE_EMBED;
