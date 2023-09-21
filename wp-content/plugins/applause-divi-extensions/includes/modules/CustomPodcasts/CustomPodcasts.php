<?php
/**
 * Basic Call To Action module (title, content, and button) with NO builder support
 * This module appears as placeholder box on Visual Builder
 *
 * @since 1.0.0
 */
class DICM_PODCASTS extends ET_Builder_Module {
	// Module slug (also used as shortcode tag)
	public $slug       = 'dicm_podcasts';
	public $vb_support = 'partial';
	

	/**
	 * Module properties initialization
	 *
	 * @since 1.0.0
	 */
	function init() {
		// Module name
		$this->name             = esc_html__( 'Applause Posdcasts', 'dicm-divi-custom-modules' );

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
            'posts_number' => array(
                'label' => esc_html__('Number of Posts', 'et_builder'),
                'type' => 'text',
                'option_category' => 'basic_option',
                'description' => esc_html__('Enter the number of posts to display.', 'et_builder'),
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
		$posts_number               = $this->props['posts_number'];

        // Render module content
        // Query posts based on module settings

		$args = array(
			'posts_per_page' => $posts_number,
			'post_type' => 'podcasts',
			'orderby' => 'date',
			'order'   => 'DESC',
		);

		$query = new WP_Query($args);

		// Output module content
		$output = '<div class="applasue-podcasts-eleements">';
			$count = 0;

			$output .= '<div id="podcasts-container-'.$module_id.'">';
				$output .= '<div class="podcasts-wrapper">';

				
				$output .= '<div class="small:tw-grid-cols-2 medium:tw-grid-cols-3 tw-grid tw-gap-6">';
				if ($query->have_posts()) {
					while ($query->have_posts()) {
						$query->the_post();
						$category_get = get_the_category();
						$link = get_category_link( $category_get[0]->term_id );

						$output .= '<a href="'.get_the_permalink().'" class="podcast-link">';
		
							$output .= '<div class="podcast-image">';
								$output .= get_the_post_thumbnail( get_the_ID(), 'large' );
							$output .= '</div>';	
							
							$output .= '<div class="extended-meta tw-justify-items-stretch tw-inline-flex tw-justify-between tw-w-full tw-mb-2">';
								$output .= '<div class="date tw-text-xs tw-text-gray-500">'.get_the_date( 'M d Y' ).'</div>';
								$output .= '<div class="durations tw-text-xs tw-text-gray-500">'.get_post_meta(get_the_ID(), 'episode_length', true).'n</div>';
		
							$output .= '</div>';
		
							$output .= '<div class="podcasts-content">';
								$output .= '<div class="title">' . get_the_title() . '</div>';
								$output .= '<div class="content">'.get_the_excerpt().'</div>';
							$output .= '</div>';
		
						$output .= '</a>';

					}
					$output .= '</div>';	
					wp_reset_postdata();
				}

				$output .= '</div>';
			$output .= '</div>';

		$output .= '</div>';

        wp_reset_postdata();

		// Render wrapper
		// 3rd party module with no full VB support has to wrap its render output with $this->_render_module_wrapper().
		// This method will automatically add module attributes and proper structure for parallax image/video background
		return $this->_render_module_wrapper( $output, $render_slug );
	}
}

new DICM_PODCASTS;
