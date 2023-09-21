<?php
/**
 * Basic Call To Action module (title, content, and button) with NO builder support
 * This module appears as placeholder box on Visual Builder
 *
 * @since 1.0.0
 */
class DICM_BLOG extends ET_Builder_Module {
	// Module slug (also used as shortcode tag)
	public $slug       = 'dicm_blog';
	public $vb_support = 'partial';
	

	/**
	 * Module properties initialization
	 *
	 * @since 1.0.0
	 */
	function init() {
		// Module name
		$this->name             = esc_html__( 'Applause Press Releases', 'dicm-divi-custom-modules' );

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
                'label' => esc_html__('Title', 'et_builder'),
                'type' => 'text',
                'option_category' => 'basic_option',
                'description' => esc_html__('Enter the title.', 'et_builder'),
            ),
            'posts_number' => array(
                'label' => esc_html__('Number of Posts', 'et_builder'),
                'type' => 'text',
                'option_category' => 'basic_option',
                'description' => esc_html__('Enter the number of posts to display.', 'et_builder'),
            ),
            'view_button_text' => array(
                'label' => esc_html__('View All Button Text', 'et_builder'),
                'type' => 'text',
                'option_category' => 'basic_option',
                'description' => esc_html__('Enter Button Text', 'et_builder'),
            ),
			'view_button_url' => array(
                'label' => esc_html__('View All Button URL', 'et_builder'),
                'type' => 'text',
                'option_category' => 'basic_option',
                'description' => esc_html__('Enter Button URL', 'et_builder'),
            ),			
			'select_if_archive' => array(
                'label' => esc_html__('Select If module used for archive', 'et_builder'),
                'type' => 'select',
                'option_category' => 'configuration',
				'options'         => array(
					'regular' => esc_html__('Regular Query', 'et_builder'),
					'has_archive' => esc_html__('Archive Query', 'et_builder')
				),
				'default'         => 'regular',
                'description' => esc_html__('Select which way you want to show press release?', 'et_builder'),
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
		$posts_number               = $this->props['posts_number'];
		$posts_title                = $this->props['title'];
		$posts_button_text          = $this->props['view_button_text'];
		$posts_button_url           = $this->props['view_button_url'];
		$query_type           		= $this->props['select_if_archive'];

        // Render module content
        // Query posts based on module settings

		if( $query_type == 'has_archive' ) {
			$args = array(
				'posts_per_page' => $posts_number,
				'post_type' => 'press_releases',
				'orderby'        => 'date',
				'order'          => 'DESC',
			);

			// Initialize the year variable
			$year = '';
	
			$query = new WP_Query($args);

			$posts_by_year = array();
	
			// Output module content
			$output = '<div id="newsroom-contact">';
			if( !empty($posts_title) && !empty($posts_button_url) ) {
				$output .= '<h2 class="title as-h4">'.$posts_title.'<a class="view-all" role="button" href="'.$posts_button_url.'">'.$posts_button_text.'</a></h2>';
			}
	
			
			if ($query->have_posts()) {
				while ($query->have_posts()) {
					$query->the_post();
					// Get the post's year
					$post_year = get_the_date('Y');
					
					// Display the year as a heading if it's different from the previous post
					if ($post_year !== $year) {
						$output .= '<h2 class="title as-h4 top-space">' . $post_year . '</h2>';
						$year = $post_year;
					}
					
					$output .= '<div class="entry grid-elements-2">';
					$posts_by_year[$post_year][] = $post;

					$output .= '<div class="date">'.get_the_date( 'M d Y' ).'</div><div class="link"><a href="'.get_the_permalink().'">' . get_the_title() . '</a></div>';

					$output .= '</div>';
				}
			}
	
			$output .= '</div>';
		} else {
			$args = array(
				'posts_per_page' => $posts_number,
				'post_type' => 'press_releases',
			);
	
			$query = new WP_Query($args);
	
			// Output module content
			$output = '<div id="newsroom-contact">';
			if( !empty($posts_title) && !empty($posts_button_url) ) {
				$output .= '<h2 class="title as-h4">'.$posts_title.'<a class="view-all button is-secondary"  role="button" href="'.$posts_button_url.'">'.$posts_button_text.'</a></h2>';
			}
	
			$output .= '<div class="entry grid-elements">';
		
			if ($query->have_posts()) {
				while ($query->have_posts()) {
					$query->the_post();
					$output .= '<div class="date">'.get_the_date( 'M d Y' ).'</div><div class="link"><a href="'.get_the_permalink().'">' . get_the_title() . '</a></div>';
				}
				wp_reset_postdata();
			}
	
			$output .= '</div>';
			$output .= '</div>';
		}

        wp_reset_postdata();

		// Render wrapper
		// 3rd party module with no full VB support has to wrap its render output with $this->_render_module_wrapper().
		// This method will automatically add module attributes and proper structure for parallax image/video background
		return $this->_render_module_wrapper( $output, $render_slug );
	}
}

new DICM_BLOG;
