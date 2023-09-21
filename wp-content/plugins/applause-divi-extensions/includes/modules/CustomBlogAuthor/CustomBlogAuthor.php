<?php
/**
 * Basic Call To Action module (title, content, and button) with NO builder support
 * This module appears as placeholder box on Visual Builder
 *
 * @since 1.0.0
 */
class DICM_AUTHOR_BLOG extends ET_Builder_Module {
	// Module slug (also used as shortcode tag)
	public $slug       = 'dicm_blog_authors';
	public $vb_support = 'partial';
	

	/**
	 * Module properties initialization
	 *
	 * @since 1.0.0
	 */
	function init() {
		// Module name
		$this->name             = esc_html__( 'Applause Blog Author', 'dicm-divi-custom-modules' );

		// Module Icon
		// This character will be rendered using etbuilder font-icon. For fully customized icon, create svg icon and
		// define its path on $this->icon_path property (see CustomCTAFull class)
		$this->icon             = 'j';

		// Toggle settings
		$this->settings_modal_toggles  = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'dicm-divi-custom-modules' ),
					'button'       => esc_html__( 'Button', 'dicm-divi-custom-modules' ),
				),
			),
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
			'title' => array(
				'label'           => esc_html__( 'Title', 'dicm-divi-custom-modules' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Text entered here will appear as title.', 'dicm-divi-custom-modules' ),
				'toggle_slug'     => 'main_content',
			),
		);
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
		$title                 = $this->props['title'];

		$author_id = get_post_field( 'post_author', get_the_ID() );
		$author_name = get_the_author_meta( 'display_name', $author_id );
		$author_description = get_the_author_meta( 'user_description', $author_id );
		$author_thumb = get_avatar(  $author_id, 50 );

		$blog_autor = get_post_meta(get_the_ID(), 'blog_authors', true);

		
		// Render module content
		$output = '';
		if( $blog_autor ) {
			$output .= '<section id="author-bio">';
			$output .= '<a href="'.get_permalink($blog_autor).'" class="tw-group tw-inline-flex tw-no-underline">';
			$output .= '<div class="author-image tw-mr-4">';
			
			$output .= '<img src="' .get_the_post_thumbnail_url($blog_autor, 'thumbnail' )  .'" />';
			
			$output .= '</div><div class="author-info"><div class="group-hover:tw-text-blue-700 tw-text-sm tw-font-semibold tw-text-blue-500">'.get_the_title($blog_autor).'</div><div class="group-hover:tw-text-gray-700 tw-text-xs tw-leading-3">'.get_post_meta($blog_autor, 'authortitle', true).'</div></div></a></section>';		
		} else {
			$output .= '<section id="author-bio">';
			$output .= '<a href="#" class="tw-group tw-inline-flex tw-no-underline">';
			$output .= '<div class="author-image tw-mr-4">';
			
			$output .= $author_thumb;
			
			$output .= '</div><div class="author-info"><div class="group-hover:tw-text-blue-700 tw-text-sm tw-font-semibold tw-text-blue-500">'.$author_name.'</div><div class="group-hover:tw-text-gray-700 tw-text-xs tw-leading-3">'.$author_description.'</div></div></a></section>';
		}
		

		// Render wrapper
		// 3rd party module with no full VB support has to wrap its render output with $this->_render_module_wrapper().
		// This method will automatically add module attributes and proper structure for parallax image/video background
		return $this->_render_module_wrapper( $output, $render_slug );
	}
}

new DICM_AUTHOR_BLOG;
