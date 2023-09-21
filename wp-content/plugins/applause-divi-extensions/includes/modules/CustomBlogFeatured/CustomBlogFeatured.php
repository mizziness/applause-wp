<?php
/**
 * Basic Call To Action module (title, content, and button) with NO builder support
 * This module appears as placeholder box on Visual Builder
 *
 * @since 1.0.0
 */
class DICM_BLOG_FEATURED extends ET_Builder_Module {
	// Module slug (also used as shortcode tag)
	public $slug       = 'dicm_blog_featured';
	public $vb_support = 'partial';
	

	/**
	 * Module properties initialization
	 *
	 * @since 1.0.0
	 */
	function init() {
		// Module name
		$this->name             = esc_html__( 'Applause Blog Featured', 'dicm-divi-custom-modules' );

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
		$posts_title                = $this->props['title'];

        // Render module content
        // Query posts based on module settings

		$args = array(
			'posts_per_page' => $posts_number,
			'post_type' => 'post',
			'meta_query'	=> array(
				array(
					'key'	 	=> 'is_featured',
					'value'	  	=> true,
					'compare' 	=> '==',
				)
			)
		);		
		
		$args2 = array(
			'posts_per_page' => $posts_number,
			'post_type' => 'post',
			'offset'	=> 1,
			'meta_query'	=> array(
				array(
					'key'	 	=> 'is_featured',
					'value'	  	=> true,
					'compare' 	=> '==',
				)
			)
		);

		$query = new WP_Query($args);
		$query2 = new WP_Query($args2);

		// Output module content
		$output = '<div class="applause-blog-elements">';
			if( !empty($posts_title) ) {
				$output .= '<h2 class="title as-h4">'.$posts_title.' <a  href="#blog-search-trigger" class="button is-icon">
				<svg style="height: 1rem; width: 1rem;" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 6.83333H0.5H1ZM0.5 6.83333C0.5 10.3311 3.33553 13.1667 6.83333 13.1667V12.1667C3.88781 12.1667 1.5 9.77885 1.5 6.83333H0.5ZM6.83333 13.1667C10.3311 13.1667 13.1667 10.3311 13.1667 6.83333H12.1667C12.1667 9.77885 9.77885 12.1667 6.83333 12.1667V13.1667ZM13.1667 6.83333C13.1667 3.33553 10.3311 0.5 6.83333 0.5V1.5C9.77885 1.5 12.1667 3.88781 12.1667 6.83333H13.1667ZM6.83333 0.5C3.33553 0.5 0.5 3.33553 0.5 6.83333H1.5C1.5 3.88781 3.88781 1.5 6.83333 1.5V0.5ZM15.3535 14.6465L11.4646 10.7576L10.7575 11.4647L14.6464 15.3536L15.3535 14.6465Z" fill="currentColor"></path></svg>
				</a></h2>';
			}

			$count = 0;

			$output .= '<div id="featured-container-{$module_id}">';
				$output .= '<div class="featured-wrapper">';

				$output .= '<div class="blog-left-coloumns">';
				if ($query->have_posts()) {
					while ($query->have_posts()) {
						$query->the_post();
						$count++;
						$category_get = get_the_category();
						$link = get_category_link( $category_get[0]->term_id );
						if ($count === 1) {
							$output .= '<div class="blog-featured-large applause-blog-elements">';
								$output .= '<a href="'.get_the_permalink().'" class="blog-image">';
									$output .= get_the_post_thumbnail( get_the_ID(), 'large' );
								$output .= '</a>';
							
								$output .= '<div class="blog-meta">';
									$output .= '<a class="link" href="'.$link.'">'.$category_get[0]->cat_name.'</a>';
								$output .= '</div>';
		
								$output .= '<h2 class="title"><a href="'.get_the_permalink().'">' . get_the_title() . '</a></h2>';
		
								$output .= '<p>' . wp_trim_words(get_the_excerpt(), 30) . '</p>';
							$output .= '</div>';
						}
					}
					$output .= '</div>';	
					wp_reset_postdata();

					$output .= '<div class="blog-right-coloumns">';
					while ($query2->have_posts()) {
						$query2->the_post();
				
						$category_get = get_the_category();
						$link = get_category_link( $category_get[0]->term_id );

						$output .= '<div class="blog-featured-small applause-blog-elements">';
							$output .= '<div class="blog-content">';
								$output .= '<div class="blog-meta">';
									$output .= '<a class="link" href="'.$link.'">'.$category_get[0]->cat_name.'</a>';
								$output .= '</div>';
		
		
								$output .= '<h2 class="title"><a class="" href="'.get_the_permalink().'">' . get_the_title() . '</a></h2>';
		
								$output .= '<p class="tw-text-sm tw-leading-[1.3rem]">' . wp_trim_words(get_the_excerpt(), 30) . '</p>';
							$output .= '</div>';

							$output .= '<div class="blog-image">';
								$output .= '<a href="'.get_the_permalink().'" class="blog-image">';
									$output .= get_the_post_thumbnail( get_the_ID(), 'medium' );
								$output .= '</a>';
							$output .= '</div>';
						$output .= '</div>';
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

new DICM_BLOG_FEATURED;
