<?php
/**
 * Basic Call To Action module (title, content, and button) with NO builder support
 * This module appears as placeholder box on Visual Builder
 *
 * @since 1.0.0
 */
class DICM_BLOG_SLIDER extends ET_Builder_Module {
	// Module slug (also used as shortcode tag)
	public $slug       = 'dicm_blog_slider';
	public $vb_support = 'partial';
	

	/**
	 * Module properties initialization
	 *
	 * @since 1.0.0
	 */
	function init() {
		// Module name
		$this->name             = esc_html__( 'Applause Blog Slider', 'dicm-divi-custom-modules' );

		// Module Icon
		// This character will be rendered using etbuilder font-icon. For fully customized icon, create svg icon and
		// define its path on $this->icon_path property (see CustomCTAFull class)
		$this->icon             = 'j';

		$this->whitelisted_fields = array(
			'categories',
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

	public function get_blog_categories() {
		$categories = get_categories();

		$options = array('All');

		foreach ($categories as $category) {
			$options[$category->slug] = $category->name;
		}

		return $options;
	}	
	
	public function get_blog_tags() {
		$categories = get_tags();

		$options = array('All');

		foreach ($categories as $category) {
			$options[$category->slug] = $category->name;
		}

		return $options;
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
			'categories' => array(
				'label' => esc_html__('Categories', 'et_builder'),
				'type' => 'select',
				'option_category' => 'configuration',
				'options' => $this->get_blog_categories(),
				'description' => esc_html__('Select the categories you want to display.', 'et_builder'),
				'toggle_slug' => 'categories',
			),			
			'tags' => array(
				'label' => esc_html__('Tags', 'et_builder'),
				'type' => 'select',
				'option_category' => 'configuration',
				'options' => $this->get_blog_tags(),
				'description' => esc_html__('Select the tags you want to display.', 'et_builder'),
				'toggle_slug' => 'categories',
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
		$posts_button_text          = $this->props['view_button_text'];
		$posts_button_url           = $this->props['view_button_url'];
		$categories = isset($this->props['categories']) ? $this->props['categories'] : '';
		$tags = isset($this->props['tags']) ? $this->props['tags'] : '';

        // Render module content
        // Query posts based on module settings

		if( !empty($tags) ) {
			$args = array(
				'posts_per_page' => $posts_number,
				'post_type' => 'post',
				'tag' => $tags,
				'order' => 'ASC'
			);
		} else {
			$args = array(
				'posts_per_page' => $posts_number,
				'post_type' => 'post',
				'category_name' => $categories,
				'order' => 'ASC'
			);
		}

		$query = new WP_Query($args);

		// Output module content
		$output = '<div class="applause-blog-elements">';
			if( !empty($posts_title) && !empty($posts_button_url) ) {
				$output .= '<h2 class="title as-h4">'.$posts_title.'<a class="view-all"  role="button" href="'.$posts_button_url.'">'.$posts_button_text.' <img src="https://bucketeer-57bb6f5e-ac50-48d6-a1b4-a5559e3736dc.s3.amazonaws.com/public/wp-content/uploads/2023/06/thin-arrow-right.svg" /></a></h2>';
			}


			$output .= '<div class="swiper-container blog-slider category-swiper" id="swiper-container-'.$module_id.'">';
				$output .= '<div class="swiper-wrapper">';
			
				if ($query->have_posts()) {
					while ($query->have_posts()) {
						$query->the_post();
						if( !empty($tags) ) {
							$category_get = get_tags();
						} else {
							$category_get = get_the_category();
						}
						$link = get_category_link( $category_get[0]->term_id );

						$output .= '<div class="swiper-slide">';
							$output .= '<a href="" class="blog-image">';
								$output .= get_the_post_thumbnail( get_the_ID(), 'medium' );
							$output .= '</a>';
						
							$output .= '<div class="blog-meta">';
								$output .= '<a href="'.$link.'">'.$category_get[0]->cat_name.'</a>';
							$output .= '</div>';


							$output .= '<h2><a class="tw-font-semibold" href="'.get_the_permalink().'">' . get_the_title() . '</a></h2>';

							$output .= '<p class="tw-text-sm tw-leading-[1.3rem]">' . wp_trim_words(get_the_excerpt(), 20) . '</p>';
						$output .= '</div>';
					}
					wp_reset_postdata();
				}

				$output .= '</div>';
			
				$output .= '<div class="swiper-pagination swiper-pagination-'. esc_attr($module_id) .'"></div>';

			$output .= '</div>';

		$output .= '</div>';

        wp_reset_postdata();

		// Enqueue Swiper initialization script
        $output .= '<script>';
        $output .= 'jQuery(function($) {';
        $output .= 'var swiper' . esc_js($module_id) . ' = new Swiper(\'#swiper-container-' . esc_js($module_id) . '\', {';
        $output .= 'pagination: {';
        $output .= 'el: \'.swiper-pagination-' . esc_js($module_id) . '\',';
        $output .= 'clickable: true,';
        $output .= '},';
        $output .= ' slidesPerView: 4, slidesPerGroup: 4, spaceBetween: 30, calculateHeight:true,';
        $output .= 'navigation: {';
        $output .= 'nextEl: \'.swiper-button-next-' . esc_js($module_id) . '\',';
        $output .= 'prevEl: \'.swiper-button-prev-' . esc_js($module_id) . '\',';
        $output .= '},';
        $output .= '});';
        $output .= '});';
        $output .= '</script>';

		// Render wrapper
		// 3rd party module with no full VB support has to wrap its render output with $this->_render_module_wrapper().
		// This method will automatically add module attributes and proper structure for parallax image/video background
		return $this->_render_module_wrapper( $output, $render_slug );
	}
}

new DICM_BLOG_SLIDER;
