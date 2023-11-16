<?php

class ACM_HypeEmbed extends ET_Builder_Module
{
	public $slug       = 'acm_hype_embed';
	public $vb_support = 'on';

	protected $module_credits = array(
		'module_uri' => 'https://www.applause.com',
		'author'     => 'Tammy Shipps',
		'author_uri' => 'https://www.applause.com',
	);

	function init()
	{
		// Module name
		$this->name = esc_html__('Hype Embed (New)', 'acm-applause-custom-modules');
		$this->icon = 'n';
		$this->main_css_element = '%%order_class%%';
	}

	function get_fields()
	{
		return array(
			'hype_archive' => array(
				'label'              => esc_html__('Upload Hype Asset', 'acm-applause-custom-modules'),
				'description'        => esc_html__('Upload and embed a Hype4 asset', 'acm-applause-custom-modules'),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__('Upload a Hype', 'acm-applause-custom-modules'),
				'choose_text'        => esc_attr__('Choose a Hype', 'acm-applause-custom-modules'),
				'update_text'        => esc_attr__('Set As Hype Asset', 'acm-applause-custom-modules'),
			)
		);
	}

	function get_attachment_id_by_filename($filename)
	{
		global $wpdb;
		$mktID = "%" . explode("-", $filename)[0] . "%";

		$sql = $wpdb->prepare("SELECT * FROM `wp_posts` WHERE `guid` LIKE %s ORDER BY post_date, id DESC LIMIT 1", array($mktID));
		$attachments = $wpdb->get_results($sql, OBJECT);
		return $attachments[0]->ID ?? false;
	}

	function render($attrs, $content = null, $render_slug)
	{
		$output = "";

		$hype_archive 		= $this->props['hype_archive'] ?? null;
		$post_id 			= get_attachment_id_by_filename( basename($hype_archive) );
		$attachment			= get_post( $post_id ) ?? null;
		$mkt_id 			= explode("-", basename($hype_archive))[0];

		if ( $attachment != null ) {
			$s3Folder = "https://" . getenv('BUCKETEER_BUCKET_NAME') . ".s3.amazonaws.com/public/wp-content/uploads/hype4/" . $mkt_id . "/Default/extracted.html";
			$extracted = file_get_contents($s3Folder) ?? false;

			if ( $extracted != false ) {
				$hype_content = str_replace(array('><![CDATA[', ']]></script>'), array('>', '</script>'), $extracted);

				$baseUrl = "https://bucketeer-57bb6f5e-ac50-48d6-a1b4-a5559e3736dc.s3.amazonaws.com/public/wp-content/uploads/hype4/{$mkt_id}/Default/Default.hyperesources";
				$hype_content = str_replace( array('"Default.hyperesources', '&quot;Default.hyperesources'), '"' . $baseUrl, $hype_content);
				$output = $hype_content;
				
			} else {
				$output = "Nothing Here";
			}
			
		}

		return $this->_render_module_wrapper($output, $render_slug);
	}
}

new ACM_HypeEmbed;
