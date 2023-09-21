<?php
/**
 * Basic Call To Action module (title, content, and button) with NO builder support
 * This module appears as placeholder box on Visual Builder
 *
 * @since 1.0.0
 */
class DICM_RESOURCE_WEBINNERS extends ET_Builder_Module {
	// Module slug (also used as shortcode tag)
	public $slug       = 'dicm_resource_webinners';
	public $vb_support = 'partial';
	

	/**
	 * Module properties initialization
	 *
	 * @since 1.0.0
	 */
	function init() {
		// Module name
		$this->name             = esc_html__( 'Applause Webiners', 'dicm-divi-custom-modules' );

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
            'post_in' => array(
                'label' => esc_html__('Post In', 'et_builder'),
                'type' => 'text',
                'option_category' => 'basic_option',
                'description' => esc_html__('Enter post id with comma.', 'et_builder'),
            ),
			'active_posts' => array(
				'type'        => 'multiple_checkboxes',
				'label'       => __( 'Show Resource Type', 'et_builder' ),
				'description' => __( 'Select which features this post type should support', 'et_builder' ),
				'default'     => array( 'case_studies' ),
				'options'     => array(
					'case_studies'           => __( 'Case Studies', 'et_builder' ),
					'ebooks'          => __( 'Ebooks', 'et_builder' ),
					'webinars'          => __( 'Webinars', 'et_builder' ),
					'reports'       => __( 'Reports', 'et_builder' ),
				),
				'return_values' => true,
			),
			'select_columns' => array(
				'type'        => 'select',
				'label'       => __( 'Select Coloumns', 'et_builder' ),
				'description' => __( 'Select How Many columns post you want disaply', 'et_builder' ),
				'default'     => '4',
				'options'     => array(
					'2'          => __( '2 Coloumns', 'et_builder' ),
					'3'          => __( '3 Coloumns', 'et_builder' ),
					'4'       => __( '4 Coloumns', 'et_builder' ),
					'5'       => __( '5 Coloumns', 'et_builder' ),
				),
			),			
			'button_type' => array(
				'type'        => 'select',
				'label'       => __( 'Button Type', 'et_builder' ),
				'default'     => 'secondary',
				'options'     => array(
					'primary'           => __( 'Primary', 'et_builder' ),
					'secondary'          => __( 'Secondary', 'et_builder' ),
				),
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
		$post_in_from           	= $this->props['post_in'];
		$active_posts               = $this->props['active_posts'];
		$select_columns             = $this->props['select_columns'];
		$button_type             	= $this->props['button_type'];
		$button_type2             	= $this->props['multi_select_test'];

        // Render module content
        // Query posts based on module settings
		if( !empty( $post_in_from  ) ) {
			// Assuming you have retrieved the value from the text field as $postIdsText

			// Convert the comma-separated string of post IDs to an array
			$postIdsArray = explode(',', $post_in_from);

			// Remove any leading or trailing spaces from each post ID
			$postIdsArray = array_map('trim', $postIdsArray);

			// Convert the post IDs from strings to integers
			$postIdsArray = array_map('intval', $postIdsArray);

			$args = array(
				'posts_per_page' => $posts_number,
				'post_type' => 'webinars',
				'post__in' => $postIdsArray,
				'order' => 'DESC',
			);
		} else {
			$args = array(
				'posts_per_page' => $posts_number,
				'post_type' => 'webinars',
				'orderby'        => 'rand',
				'order' => 'DESC',
			);
		}

		$query = new WP_Query($args);

		$output = '';
		
		// Output module content
		$output = '<div class="applasue-resources-eleements">';
			if( !empty($posts_title) ) {
				$output .= '<div class="tw-pb-4 tw-mb-8 tw-border-b tw-border-gray-300"><h4 class="title as-h4 tw-mb-0 tw-text-gray-500">'.$posts_title.'</h4></div>';
			}

			$output .= '<div id="resources-'.$module_id.'">';
				$output .= '<div class="resources-container">';

				$output .= '<div class="resources-wrapper">';
				if ($query->have_posts()) {
					$output .= '<div class="medium:tw-grid-cols-'.$select_columns.' tw-grid tw-grid-cols-2 tw-gap-6 tw-mb-12">';
					while ($query->have_posts()) {
						$query->the_post();
						//button_json, resourceButtonUrl, resourceButtonText, resource_button_texts, resource_json, resource_button_json 
						$buton_url = get_post_meta( get_the_ID(), 'resourceButtonUrl', true );
						$buton_url1 = get_post_meta( get_the_ID(), 'resource_button_url', true );
						$buton_texts = get_post_meta( get_the_ID(), 'resource_button_texts', true );
						$buton_texts1 = get_post_meta( get_the_ID(), 'resource_button_text', true );
						$buton_text_json_1 = get_post_meta( get_the_ID(), 'resourceButtonText', true );
						$buton_text_json_3 = get_post_meta( get_the_ID(), 'resources_buttons_Texts', true );
						$buton_text_json_2 = get_post_meta( get_the_ID(), 'resource_button_json', true );
						$buton_json = get_post_meta( get_the_ID(), 'button_json', true );
						$post_type = get_post_type( get_the_ID() );
						$pt = get_post_type_object( $post_type );

						if( !empty( $buton_url ) ) {
							$resource_links = $buton_url;
							$resource_btn_text = $buton_texts;
						} elseif( !empty( $buton_text_json_1 ) ) {
							$get_json = json_decode($buton_text_json_1);
							if( !empty( $get_json ) ) {
								$resource_links = $get_json->resourceButtonUrl;
								$resource_btn_text = $get_json->resourceButtonText;
							} else {
								$resource_links = $buton_url;
								$resource_btn_text = $buton_text_json_1;
							}
						} elseif ( !empty( $buton_json )) {
							$get_json = json_decode($buton_json, true);
							$firstElement = array_values($get_json)[0];

							// Access the 'fields' object
							$fields = $firstElement['fields'];
							$resource_btn_text = $fields['resourceButtonText'];
							$resource_links = $fields['resourceButtonUrl'];
						} elseif ( !empty( $buton_text_json_2 )) {
							$get_json = json_decode($buton_text_json_2, true);
							$firstElement = array_values($get_json)[0];

							// Access the 'fields' object
							$fields = $firstElement['fields'];
							$resource_btn_text = $fields['resourceButtonText'];
							$resource_links = $fields['resourceButtonUrl'];
						}  elseif(!empty( $buton_texts1 )  ) {
							$resource_links = $buton_url1;
							$resource_btn_text = $buton_texts1;
						} else {
							$resource_links = $buton_url;
							$resource_btn_text = $buton_texts;
						}

						$output .= '<div class="column medium:tw-text-left tw-text-center">';
							$output .= '<a class="hover" href="'.$resource_links.'">';	
								$output .= get_the_post_thumbnail( get_the_ID(), 'medium' );
								$output .= '<div class="tag-label tw-inline-block tw-mb-4 tw-bg-white">'.$pt->label.'</div>';

								$output .= '<h2 class="title as-h5">' . get_the_title() . '</h2>';
								$output .= '<p class="tw-mb-4 tw-tracking-tight">'.get_the_excerpt().'</p>';
								if( !empty( $resource_btn_text ) ) {
									if( $button_type == 'secondary' ) {
										$output .= '<span class="button is-secondary">'. $resource_btn_text .'</span>';
									} else {
										$output .= '<span class="button is-primary">'. $resource_btn_text .'</span>';
									}				
								} else {
									if( $button_type == 'secondary' ) {
										$output .= '<span class="button is-secondary">'. __('Read Now', 'et_builder') .'</span>';
									} else {
										$output .= '<span class="button is-primary">'. __('Read Now', 'et_builder') .'</span>';
									}
								}
							$output .= '</a>';
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

new DICM_RESOURCE_WEBINNERS;
