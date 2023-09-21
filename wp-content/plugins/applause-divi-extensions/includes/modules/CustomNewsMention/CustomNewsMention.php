<?php
/**
 * Basic Call To Action module (title, content, and button) with NO builder support
 * This module appears as placeholder box on Visual Builder
 *
 * @since 1.0.0
 */
class DICM_NEWS_MENTIONS extends ET_Builder_Module {
	// Module slug (also used as shortcode tag)
	public $slug       = 'dicm_news_mention';
	public $vb_support = 'partial';
	

	/**
	 * Module properties initialization
	 *
	 * @since 1.0.0
	 */
	function init() {
		// Module name
		$this->name             = esc_html__( 'Applause News Mention', 'dicm-divi-custom-modules' );

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
            'posts_number' => array(
                'label' => esc_html__('Number of Posts', 'et_builder'),
                'type' => 'text',
                'option_category' => 'basic_option',
                'description' => esc_html__('Enter the number of posts to display.', 'et_builder'),
            ),            
            'view_article_text' => array(
                'label' => esc_html__('View Button Text', 'et_builder'),
                'type' => 'text',
                'option_category' => 'basic_option',
                'description' => esc_html__('Enter the button text.', 'et_builder'),
            ),         
            'load_more_text' => array(
                'label' => esc_html__('Load More Button Text', 'et_builder'),
                'type' => 'text',
                'option_category' => 'basic_option',
                'description' => esc_html__('Enter the button text.', 'et_builder'),
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
		$article_text               = $this->props['view_article_text'];
		$load_more_text               = $this->props['load_more_text'];

        // Render module content
        // Query posts based on module settings

        $args = array(
            'posts_per_page' => $posts_number,
            'post_type' => 'news_mentions',
        );

        $query = new WP_Query($args);

        // Output module content
        $output = '<div id="news-grid">';
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $output .= '<div class="news-mention item"><a href="'.get_post_meta( get_the_ID(), 'news_mention_article_url', true ).'">';
                $output .= get_the_post_thumbnail( get_the_ID(), 'full' );
                $output .= '<p>' . get_the_title() . '</p><p><span class="date">'.get_the_date( 'M d Y' ).'</span><span class="company">'.get_post_meta( get_the_ID(), 'news_mention_company_name', true ).'</span></p><span class="button is-primary" role="button">'.$article_text .'</span>';
                $output .= '</a></div>';
            }
            wp_reset_postdata();
        }

        $output .= '</div>';
		

        wp_reset_postdata();

        $output .= '<div class="load-more-elements"><button>'.$load_more_text.'</button></div>';

		// Render wrapper
		// 3rd party module with no full VB support has to wrap its render output with $this->_render_module_wrapper().
		// This method will automatically add module attributes and proper structure for parallax image/video background
		return $this->_render_module_wrapper( $output, $render_slug );
	}
}

new DICM_NEWS_MENTIONS;
