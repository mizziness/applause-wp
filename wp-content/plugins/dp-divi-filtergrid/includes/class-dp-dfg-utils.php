<?php

/**
 * @since      1.0.0
 * @package    Dp_Cpt_Filterable_Module
 * @subpackage Dp_Cpt_Filterable_Module/includes
 * @author     DiviPlugins <support@diviplugins.com>
 */
class Dp_Dfg_Utils {

	/**
	 * Ajax get post data
	 *
	 * @since    1.0.0
	 */
	public static function ajax_get_posts_data() {
		$post       = wp_unslash( $_POST );
		$posts_data = array();
		if ( isset( $post['module_data'] ) ) {
			$visual_builder_request = isset( $post['vb'] ) && 'on' === sanitize_text_field( $post['vb'] );
			/*
			 * Sanitize all module data
			 */
			$sanitized_properties = array();
			foreach ( $post['module_data'] as $name => $value ) {
				switch ( $name ) {
					case 'no_results':
					case 'conditional_tags':
					case 'query_var':
					case 'hover_icon':
					case 'video_icon':
					case 'read_more_button_icon':
					case 'action_button_icon':
					case 'custom_fields':
					case 'multilevel_tax_data':
					case 'multilevel_hierarchy_tax':
					case 'meta_separator':
						$sanitized_properties[ $name ] = $value;
						break;
					default:
						$sanitized_properties[ $name ] = sanitize_text_field( $value );
						break;
				}
			}
			if ( $visual_builder_request ) {
				$sanitized_properties['the_ID']           = $post['postId'];
				$sanitized_properties['conditional_tags'] = array(
					'is_user_logged_in' => 'true' === $post['conditionalTags']['is_user_logged_in'] ? 'on' : 'off',
					'is_front_page'     => 'true' === $post['conditionalTags']['is_front_page'] ? 'on' : 'off',
					'is_singular'       => 'true' === $post['conditionalTags']['is_singular'] ? 'on' : 'off',
					'is_archive'        => 'off',
					'is_search'         => 'true' === $post['conditionalTags']['is_search'] ? 'on' : 'off',
					'is_tax'            => 'off',
					'is_author'         => 'off',
					'is_date'           => 'off',
					'is_post_type'      => 'off',
				);
			}
			/*
			 * Get posts data
			 */
			if ( isset( $post['page'] ) ) {
				$posts_data = self::get_posts_data( $sanitized_properties, $post['page'], 'ajax' );
			} else {
				$posts_data = self::get_posts_data( $sanitized_properties, 1, 'ajax' );
			}
			/*
			 * Get data needed for the frontend or the visual builder
			 */
			if ( $visual_builder_request && isset( $posts_data['posts'] ) ) {
				$posts_data['filter_terms'] = self::get_filter_terms( $sanitized_properties, $posts_data['posts'] );
			} else if ( isset( $posts_data['posts'] ) ) {
				$posts_data['posts'] = self::get_items_html( $sanitized_properties, $posts_data['posts'] );
			}
		} else {
			$posts_data['error'] = __( 'Missing module data.', 'dpdfg-dp-divi-filtergrid' );
		}
		wp_send_json( $posts_data );
	}

	/**
	 * Process post data for the VB and the front end
	 *
	 * @param array $props Module properties
	 * @param int $page Page number
	 * @param string $from
	 *
	 * @return array $posts_data
	 * @since 1.0.0
	 */
	public static function get_posts_data( $props, $page, $from ) {
		/*
		 * Fix incorrect page number
		 */
		$page       = ( intval( $page ) <= 0 ) ? 1 : intval( $page );
		$posts_data = array();
		/*
		 * Image size according layout
		 */
		$wxh_size               = $props['thumbnail_size'];
		$registered_image_sizes = self::get_registered_thumbnail_sizes();
		if ( isset( $registered_image_sizes[ $wxh_size ] ) ) {
			$size_label = explode( ' - ', $registered_image_sizes[ $wxh_size ] );
			array_pop( $size_label );
			$registered_size_name = implode( ' - ', $size_label );
		} else {
			$registered_size_name = 'full';
		}
		if ( '400x284' === $wxh_size ) {
			//For backward compatibility
			$width  = ( 'dp-dfg-layout-fullwidth' === $props['items_layout'] || 'dp-dfg-layout-none' === $props['items_layout'] ) ? 1080 : 400;
			$height = ( 'dp-dfg-layout-fullwidth' === $props['items_layout'] || 'dp-dfg-layout-none' === $props['items_layout'] ) ? 9999 : 284;
		} elseif ( 'dfg_full' === $wxh_size ) {
			$width  = 9999;
			$height = 9999;
		} else {
			$wxh_size = explode( 'x', $wxh_size );
			$width    = $wxh_size[0];
			$height   = $wxh_size[1];
		}
		$height               = (int) apply_filters( 'dpdfg_image_thumb_height', $height, $props );
		$width                = (int) apply_filters( 'dpdfg_image_thumb_width', $width, $props );
		$posts_data['ratio']  = ( intval( $height ) && intval( $width ) ) ? $height / $width : 0;
		$posts_data['width']  = $width;
		$posts_data['height'] = $height;
		/*
		 * Taxonomies to filter by
		 */
		$tax_to_filter = array( 'category' );
		if ( 'on' === $props['multilevel'] ) {
			$tax_to_filter = self::get_multilevel_tax_data( $props );
		} else if ( '' !== $props['filter_taxonomies'] ) {
			$tax_to_filter = self::process_comma_separate_list( $props['filter_taxonomies'] );
		}
		/*
		 * Taxonomies to show terms of
		 */
		$tax_to_show = array( 'category' );
		if ( '' !== $props['show_terms_taxonomy'] ) {
			$tax_to_show = self::process_comma_separate_list( $props['show_terms_taxonomy'] );
		}
		/*
		 * Custom fields data
		 */
		$clean         = html_entity_decode( stripslashes( $props['custom_fields'] ) );
		$custom_fields = json_decode( $clean, true );
		/*
		 * Init the query
		 */
		$query_args    = self::set_query_arguments( $props, $page, $from );
		$query         = new WP_Query( $query_args );
		$item_position = 0;
		if ( $query->have_posts() ) {
			/*
			 * Query loop
			 */
			while ( $query->have_posts() ) {
				$post = array();
				$query->the_post();
				$post['position']   = $props['position'] = $item_position ++;
				$post['id']         = get_the_ID();
				$post['title']      = get_the_title();
				$post['format']     = get_post_format();
				$post['post_type']  = get_post_type();
				$post['custom_url'] = '';
				if ( 'on' === $props['custom_url'] && isset( $props['custom_url_field_name'] ) && ! empty( $props['custom_url_field_name'] ) ) {
					$post['custom_url'] = get_post_meta( $post['id'], $props['custom_url_field_name'], true );
					if ( class_exists( 'ACF' ) ) {
						$post['custom_url'] = get_field( $props['custom_url_field_name'], $post['id'], true );
						if ( is_array( $post['custom_url'] ) && isset( $post['custom_url']['url'] ) ) {
							$post['custom_url'] = $post['custom_url']['url'];
						}
					}
				}
				$post['permalink'] = $post['custom_url'] ?: get_the_permalink();
				$post['permalink'] = apply_filters( 'dpdfg_permalink', $post['permalink'], $props, $post['id'] );
				if ( 'on' == $props['show_post_meta'] && 'on' === $props['show_author'] ) {
					$post['author']      = get_the_author();
					$post['author_link'] = get_author_posts_url( get_post_field( 'post_author', $post['id'] ) );
				} else {
					$post['author']      = '';
					$post['author_link'] = '';
				}
				if ( 'on' == $props['show_post_meta'] && 'on' === $props['show_date'] ) {
					$post['date'] = get_the_date( $props['date_format'] );
				} else {
					$post['date'] = '';
				}
				if ( 'on' === $props['show_post_meta'] && 'on' === $props['show_comments'] ) {
					$comments_number = intval( get_post_field( 'comment_count', $post['id'] ) );
				} else {
					$comments_number = 0;
				}
				$post['comments'] = sprintf( _nx( '%s Comment', '%s Comments', $comments_number, 'number of comments', 'dpdfg-dp-divi-filtergrid' ), number_format_i18n( $comments_number ) );
				/*
				 * Ajax url
				 */
				$post['ajax_url'] = esc_url(
					wp_nonce_url(
						add_query_arg( array(
							'dp_action'         => 'dfg_popup_fetch',
							'popup_template'    => $props['popup_template'],
							'popup_link_target' => $props['popup_link_target']
						), $post['permalink'] ),
						'dfg_popup_fetch'
					)
				);
				/*
				 * Thumbnail
				 */
				$post_thumbnail_id          = get_post_thumbnail_id( $post['id'] );
				$post['thumbnail_id']       = $post_thumbnail_id;
				$post['thumbnail']          = false;
				$post['thumbnail_width']    = 0;
				$post['thumbnail_height']   = 0;
				$post['thumbnail_ratio']    = 0;
				$post['thumbnail_original'] = false;
				if ( $post_thumbnail_id ) {
					$image_data = wp_get_attachment_image_src( $post_thumbnail_id, $registered_size_name );
					if ( $image_data ) {
						$post['thumbnail']          = $image_data[0];
						$post['thumbnail_width']    = $image_data[1];
						$post['thumbnail_height']   = $image_data[2];
						$post['thumbnail_ratio']    = ( intval( $image_data[1] ) ) ? $image_data[2] / $image_data[1] : 1;
						$post['thumbnail_original'] = get_the_post_thumbnail_url( $post['id'], 'full' );
					}
				}
				$post['thumbnail']          = apply_filters( 'dpdfg_thumbnail_url', $post['thumbnail'], $props, $width, $height, $post['id'] );
				$post['thumbnail_original'] = apply_filters( 'dpdfg_thumbnail_original_url', $post['thumbnail_original'], $props, $post['id'] );
				//$post['thumbnail_content']=self::get_the_thumbnail_content
				/*
				 * Custom lightbox images
				 */
				$custom_field_images = array();
				if ( '' !== $props['gallery_cf_name'] ) {
					if ( class_exists( 'ACF' ) ) {
						$acf_gallery_images = get_field( $props['gallery_cf_name'], $post['id'], false );
						if ( is_array( $acf_gallery_images ) && ! empty( $acf_gallery_images ) ) {
							foreach ( $acf_gallery_images as $image_id ) {
								$custom_field_images[] = wp_get_attachment_url( $image_id );
							}
						} else {
							$custom_field_images = get_post_meta( $post['id'], $props['gallery_cf_name'] );
						}
					} else {
						$custom_field_images = get_post_meta( $post['id'], $props['gallery_cf_name'] );
					}
				}
				if ( count( $custom_field_images ) === 1 && is_array( $custom_field_images[0] ) ) {
					$custom_field_images = $custom_field_images[0];
				}
				$post['post_images'] = apply_filters( 'dp_dfg_custom_lightbox', $custom_field_images, $props );
				/*
				 * Video Preview
				 */
				$post['video_output'] = false;
				$post['video_url']    = false;
				if ( isset( $props['show_video_preview'] ) && 'on' === $props['show_video_preview'] ) {
					$first_video_data     = apply_filters( 'dpdfg_first_video_data', array(
						'video_url'    => '',
						'video_output' => ''
					), $post['id'], $props );
					$post['video_url']    = $first_video_data['video_url'];
					$post['video_output'] = $first_video_data['video_output'];
					if ( empty( $post['video_url'] ) && empty( $post['video_output'] ) ) {
						$first_video          = self::get_first_video( $post['id'], $props );
						$post['video_output'] = ( $first_video ) ? $first_video['output'] : false;
						$post['video_url']    = ( $first_video ) ? $first_video['url'] : false;
					}
				}
				/*
				 * Content / Excerpt
				 */
				$post['content'] = apply_filters( 'dpdfg_custom_content', self::get_content( $props ), $props );
				/*
				 * Post/Filter Terms
				 */
				$post['terms_classes'] = implode( ' ', get_post_class() ) . ' ';
				if ( $post['video_output'] ) {
					$post['terms_classes'] = $post['terms_classes'] . ' has-post-video ';
				}
				$post['terms_links'] = array();
				/*
				 * Terms to show
				 */
				if ( 'on' == $props['show_post_meta'] && 'on' == $props['show_terms'] ) {
					$post['terms'] = array();
					foreach ( $tax_to_show as $tax ) {
						$the_terms = get_the_terms( $post['id'], $tax );
						if ( ! is_wp_error( $the_terms ) && $the_terms ) {
							$post['terms'] = array_merge( $post['terms'], $the_terms );
						}
					}
					$non_allowed_terms = apply_filters( 'dfg_remove_meta_terms', array(), $props );
					foreach ( $post['terms'] as $term ) {
						if ( ! in_array( $term->term_id, $non_allowed_terms ) ) {
							$post['terms_links'][] = array(
								'name' => $term->name,
								'id'   => $term->term_id,
								'slug' => $term->slug,
								'link' => get_term_link( $term->term_id ),
								'tax'  => $term->taxonomy
							);
						}
					}
				}
				/*
				 * Terms to filter (terms classes for jQuery filtering)
				 */
				if ( 'on' === $props['show_filters'] ) {
					$post['filter_terms'] = array();
					foreach ( $tax_to_filter as $tax ) {
						$the_terms = get_the_terms( $post['id'], $tax );
						if ( ! is_wp_error( $the_terms ) && $the_terms ) {
							$post['filter_terms'] = array_merge( $post['filter_terms'], $the_terms );
						}
					}
					foreach ( $post['filter_terms'] as $term ) {
						$post['terms_classes'] .= 'dp-dfg-term-id-' . $term->term_id . ' ';
					}

					// Check term parents classes
					foreach ( $post['filter_terms'] as $term ) {
						if ( 'on' === $props['filter_children_terms'] && $term->parent ) {
							$parents_terms = get_ancestors( $term->term_id, $term->taxonomy );
							foreach ( array_reverse( $parents_terms ) as $parent_term ) {
								if ( ! str_contains( $post['terms_classes'], 'dp-dfg-term-id-' . $parent_term ) ) {
									$post['terms_classes'] .= 'dp-dfg-term-id-' . $parent_term . ' ';
								}
							}
						}
					}
				}
				/*
				 * Custom Content
				 */
				$post['after_read_more'] = apply_filters( 'dpdfg_after_read_more', '', $props );
				/*
				 * Custom fields
				 */
				$post['custom_fields'] = array();
				if ( ! empty( $props['custom_fields'] ) ) {
					if ( is_array( $custom_fields ) && ! empty( $custom_fields ) ) {
						foreach ( $custom_fields as $cf ) {
							$cf_data          = array();
							$cf_data['name']  = $cf['name'];
							$cf_data['label'] = $cf['label'];
							if ( class_exists( 'ACF' ) ) {
								$field_object = get_field_object( $cf['name'] );
								$field_value  = get_field( $cf['name'], $post['id'], true );
								if ( $field_object && $field_value ) {
									$type     = $field_object['type'];
									$r_format = isset( $field_object['return_format'] ) ? $field_object['return_format'] : false;
									switch ( $type ) {
										case 'select':
										case 'checkbox':
											if ( is_array( $field_value ) ) {
												if ( $r_format === 'array' && ! empty( $field_value ) ) {
													$options = array();
													foreach ( $field_value as $value ) {
														$options[] = $value['label'];
													}
													$field_value = implode( ', ', $options );
												} else {
													$field_value = implode( ', ', $field_value );
												}
											}
											break;
										case 'image':
											if ( $r_format === 'array' ) {
												$field_value = sprintf( '<img src="%1$s">', $field_value['url'] );
											} elseif ( $field_object['return_format'] === 'id' ) {
												$field_value = sprintf( '<img src="%1$s">', wp_get_attachment_url( $field_value ) );
											} else {
												$field_value = sprintf( '<img src="%1$s">', $field_value );
											}
											break;
										case 'email':
											$field_value = sprintf( '<a href="mailto:%1$s">%1$s</a>', $field_value );
											break;
										case 'url':
											$field_value = sprintf( '<a href="%1$s">%1$s</a>', $field_value );
											break;
										case 'link':
											if ( $r_format === 'array' ) {
												$field_value = sprintf( '<a href="%1$s" target="%3$s">%2$s</a>', $field_value['url'], $field_value['title'], $field_value['target'] );
											} else {
												$field_value = sprintf( '<a href="%1$s">%1$s</a>', $field_value );
											}
											break;
										case 'taxonomy':
											$terms       = array();
											$terms_names = array();
											if ( is_array( $field_value ) ) {
												foreach ( $field_value as $value ) {
													$terms[] = $value;
												}
											} else {
												$terms[] = $field_value;
											}
											foreach ( $terms as $value ) {
												if ( $r_format === 'id' ) {
													$term_object = get_term( $value );
													if ( ! ( is_wp_error( $term_object ) || is_null( $term_object ) ) ) {
														$terms_names[] = $term_object->name;
													}
												} else {
													$terms_names[] = $value->name;
												}
											}
											$field_value = implode( ', ', $terms_names );
											break;
										default:
											if ( ! is_string( $field_value ) ) {
												$field_value = 'ACF field type unsupported';
											}
											break;
									}
								}
								$cf_data['value'] = $field_value;
							} else {
								$cf_data['value'] = get_post_meta( $post['id'], $cf['name'], true );
							}
							if ( ! empty( $cf_data['value'] ) && $cf_data['value'] ) {
								$post['custom_fields'][] = apply_filters( 'dpdfg_custom_field_output', $cf_data, $cf['name'], $props );
							}
						}
					}
				}
				/*
				 * Include post data
				 */
				do_action_ref_array( 'dpdfg_ext_get_posts_data_in_loop', array( &$post, $props, $posts_data ) );
				$posts_data['posts'][] = $post;
			}
			/*
			 * Found posts
			 */
			$posts_data['found_posts'] = $query->found_posts - intval( $props['offset_number'] );
			if ( 'custom' === $props['custom_query'] || 'third_party' === $props['custom_query'] ) {
				$posts_data['post_number'] = intval( $query->query_vars['posts_per_page'] );
			} else {
				$posts_data['post_number'] = intval( $props['post_number'] );
			}
			if ( 'on' === $props['show_pagination'] && $posts_data['post_number'] > 0 ) {
				$max_pages = $posts_data['found_posts'] / $posts_data['post_number'];
			} else {
				$max_pages = 1;
			}
			$posts_data['max_pages'] = ceil( $max_pages );
		} else {
			$posts_data['no_results'] = apply_filters( 'dpdfg_no_results_html', sprintf( '<div class="dp-dfg-no-results">%1$s</div>', et_core_intentionally_unescaped( $props['no_results'], 'html' ) ), $props );
		}
		wp_reset_postdata();

		return $posts_data;
	}

	/**
	 * Process html of each item for the ajax request
	 *     *
	 *
	 * @param $props
	 * @param $posts
	 *
	 * @return false|string
	 * @since    1.0.0
	 */
	public static function get_items_html( $props, $posts ) {
		ob_start();
		$data_icon_overlay = '';
		if ( 'on' === $props['use_overlay_icon'] ) {
			$data_icon_overlay = sprintf( ' data-icon="%1$s"', esc_attr( et_pb_process_font_icon( $props['hover_icon'] ) ) );
		}
		$video_data_icon_overlay = '';
		if ( 'on' === $props['video_overlay_icon'] ) {
			$video_data_icon_overlay = sprintf( ' data-icon="%1$s"', esc_attr( et_pb_process_font_icon( $props['video_icon'] ) ) );
		}
		/*
		 * Extra Data
		 */
		$additional_data = apply_filters( 'dpdfg_ext_get_items_html_additional_data', array() );
		foreach ( $posts as $post ) {
			/*
			 * Escape all data coming from $posts
			 */
			$props['position']  = $post['position'];
			$id                 = intval( $post['id'] );
			$permalink          = esc_url( $post['permalink'] );
			$date               = esc_html( $post['date'] );
			$format             = esc_html( $post['format'] );
			$post_type          = esc_html( $post['post_type'] );
			$classes            = esc_attr( $post['terms_classes'] );
			$title              = et_core_esc_previously( $post['title'] );
			$thumbnail          = esc_attr( $post['thumbnail'] );
			$thumbnail_original = esc_attr( $post['thumbnail_original'] );
			$ajax_url           = esc_url( $post['ajax_url'] );
			$header_level       = esc_html( $props['dpdfg_entry_title_level'] );
			$author_prefix      = esc_attr( $props['author_prefix_text'] );
			$author             = esc_attr( $post['author'] );
			$author_link        = esc_url( $post['author_link'] );
			$video_url          = esc_url( $post['video_url'] );
			$custom_url         = esc_url( $post['custom_url'] );
			$video_output       = et_core_esc_previously( $post['video_output'] );
			/*
			 * Post Data
			 */
			if ( ! empty( $custom_url ) && 'same' !== $props['custom_url_target'] ) {
				$new_tab = ( 'on' === $props['custom_url_target'] ) ? 'target="_blank"' : '';
			} else {
				$new_tab = ( 'on' === $props['read_more_window'] ) ? 'target="_blank"' : '';
			}
			$thumbnail_action               = apply_filters( 'dpdfg_custom_action', $props['thumbnail_action'], $props, $id );
			$custom_lightbox_images         = '';
			$custom_lightbox_images_content = '';
			if ( is_array( $post['post_images'] ) && ! empty( $post['post_images'] ) ) {
				if ( isset( $post['post_images']['images'] ) && ! empty( $post['post_images']['images'] ) ) {
					$custom_lightbox_images = implode( '||', $post['post_images']['images'] );
				} else {
					$custom_lightbox_images = implode( '||', $post['post_images'] );
				}
				if ( isset( $post['post_images']['content'] ) ) {
					$custom_lightbox_images_content = implode( '||', $post['post_images']['content'] );
				}
			}
			$lightbox_content = [];
			if ( isset( $props['lightbox_elements'] ) ) {
				$lightbox_elements = explode( '|', $props['lightbox_elements'] );
				if ( $lightbox_elements[0] === 'on' ) {
					$lightbox_content[] = $title;
				}
				if ( $post['thumbnail_id'] ) {
					if ( $lightbox_elements[1] === 'on' ) {
						$lightbox_content[] = get_the_title( $post['thumbnail_id'] );
					}
					if ( $lightbox_elements[2] === 'on' ) {
						$lightbox_content[] = wp_get_attachment_caption( $post['thumbnail_id'] );
					}
					if ( $lightbox_elements[3] === 'on' ) {
						$lightbox_content[] = get_the_content( null, false, $post['thumbnail_id'] );
					}
				}
			}
			$lightbox_content = htmlentities2( json_encode( $lightbox_content ) );
			/*
			 * Item Init
			 */
			$show_item_action_data    = $video_output || ! $thumbnail || 'off' === $props['show_thumbnail'];
			$action_priority          = $video_output && $props['video_action_priority'] === 'on' ? 'video' : 'item';
			$sections_outputs['init'] = sprintf(
				'%12$s<article id="%1$s" class="%2$s" %3$s %4$s %5$s %6$s %7$s %8$s %9$s %10$s %11$s %13$s %14$s>',
				'post-' . $id,
				'dp-dfg-item ' . $classes,
				$show_item_action_data && 'link' === $thumbnail_action ? ' data-link="' . $permalink . '"' : '',
				$show_item_action_data && 'popup' === $thumbnail_action ? ' data-ajax-url="' . $ajax_url . '"' : '',
				$show_item_action_data && $thumbnail && ( 'lightbox' === $thumbnail_action || 'lightbox_gallery' === $thumbnail_action ) ? ' data-img="' . $thumbnail_original . '" data-title="' . $lightbox_content . '"' : '',
				$show_item_action_data && 'gallery_cf' === $thumbnail_action ? ' data-images="' . $custom_lightbox_images . '" data-content="' . $custom_lightbox_images_content . '"' : '',
				'data-position="' . $post['position'] . '"',
				$video_output ? 'data-video-action="' . $props['video_action'] . '"' : '',
				$video_url ? 'data-video-url="' . $video_url . '"' : '',
				! empty( $new_tab ) ? 'data-new-tab="on"' : 'data-new-tab="off"',
				$thumbnail_action !== $props['thumbnail_action'] ? 'data-action="' . $thumbnail_action . '"' : '',
				( 'dp-dfg-layout-masonry-standard' === $props['items_layout'] ) ? '<div class="dp-dfg-masonry-item">' : '',
				( 'attachment' === $post_type && ( 'media_download' === $thumbnail_action || 'media_link' === $thumbnail_action ) && isset( $post['the_attachment_data'] ) ) ? ' data-file="' . $post['the_attachment_data']['url'] . '" data-title="' . $title . '"' : '',
				'data-action-priority="' . $action_priority . '"'
			);
			/*
			 * Thumbnail
			 */
			$sections_outputs['thumbnail'] = '';
			if ( 'on' === $props['show_thumbnail'] || $video_output ) {
				$thumbnail_output = '';
				if ( $video_output ) {
					$thumbnail_output = $video_output;
				} else if ( 'on' === $props['show_thumbnail'] && $thumbnail ) {
					$ratio        = ( intval( $post['thumbnail_width'] ) ) ? $post['thumbnail_height'] / $post['thumbnail_width'] : 1;
					$image_output = sprintf( '<img class="dp-dfg-featured-image" src="%1$s" alt="%2$s" width="%3$s" height="%4$s" data-ratio="%5$s"/>', $thumbnail, $title, $post['thumbnail_width'], $post['thumbnail_height'], $ratio );
					if ( in_array( $thumbnail_action, array(
						'none',
						'custom',
						'woo_gallery',
						'media_download',
						'media_link'
					) ) ) {
						$thumbnail_output = $image_output;
					} elseif ( 'link' === $thumbnail_action ) {
						$thumbnail_output = sprintf( '<a href="%2$s" class="dp-dfg-image-link" %3$s>%1$s</a>', $image_output, $permalink, $new_tab );
					} elseif ( 'popup' === $thumbnail_action ) {
						$thumbnail_output = sprintf( '<a href="%2$s" data-ajax-url="%3$s" class="dp-dfg-image-link entry-thumb-popup">%1$s</a>', $image_output, $permalink, $ajax_url );
					} elseif ( 'lightbox' === $thumbnail_action || 'lightbox_gallery' === $thumbnail_action ) {
						$thumbnail_output = sprintf( '<a href="%3$s" data-img="%4$s" data-title="%2$s" class="dp-dfg-image-link entry-thumb-lightbox">%1$s</a>', $image_output, $lightbox_content, $permalink, $thumbnail_original );
					} elseif ( 'gallery_cf' === $thumbnail_action ) {
						if ( count( $post['post_images'] ) > 0 ) {
							$thumbnail_output = sprintf( '<a href="#" data-images="%2$s" data-content="%3$s" class="dp-dfg-image-link entry-thumb-gallery-cf">%1$s</a>', $image_output, $custom_lightbox_images, $custom_lightbox_images_content );
						} else {
							$thumbnail_output = $image_output;
						}
					}
				}
				$video_overlay = '';
				if ( $video_output ) {
					$style   = ( 'on' === $props['show_thumbnail'] && $thumbnail ) ? sprintf( ' style="background-image: url(%1$s);"', $thumbnail ) : '';
					$overlay = sprintf( '<span class="dfg_et_overlay" %1$s></span>', $video_data_icon_overlay );
					if ( 'on' === $props['video_overlay'] ) {
						$video_overlay = et_core_intentionally_unescaped( sprintf( '<div class="dp-dfg-video-overlay" %2$s>%1$s</div>', $overlay, ( $thumbnail ) ? $style : '' ), 'html' );
					} else if ( 'popup' === $props['video_action'] ) {
						$video_overlay = et_core_intentionally_unescaped( sprintf( '<div class="dp-dfg-video-overlay dp-dfg-popup-no-overlay">%1$s</div>', $overlay ), 'html' );
					}
				}
				$custom_thumbnail              = apply_filters( 'dpdfg_custom_thumbnail', $thumbnail_output, $props, $id );
				$sections_outputs['thumbnail'] = et_core_intentionally_unescaped( sprintf( '<figure class="dp-dfg-image %3$s">%1$s%2$s</figure>', $custom_thumbnail, $video_overlay, ( $video_output ) ? 'entry-video' : 'entry-thumb' ), 'html' );
			}
			/*
			 * Overlay
			 */
			$sections_outputs['overlay'] = '';
			if ( ! $video_output ) {
				$overlay        = sprintf( '<span class="dfg_et_overlay" %1$s></span>', $data_icon_overlay );
				$custom_overlay = apply_filters( 'dpdfg_custom_overlay', $overlay, $props, $id );
				if ( 'on' === $props['use_overlay'] ) {
					$sections_outputs['overlay'] = et_core_intentionally_unescaped( sprintf( '<div class="dp-dfg-overlay">%1$s</div>', $custom_overlay ), 'html' );
				} else {
					if ( $props['items_layout'] === 'dp-dfg-layout-list' ) {
						$sections_outputs['overlay'] = et_core_intentionally_unescaped( sprintf( '<div class="dp-dfg-overlay dp-dfg-hide">%1$s</div>', $custom_overlay ), 'html' );
					}
				}
			}
			/*
			 * Title
			 */
			$sections_outputs['title'] = '';
			if ( 'on' === $props['show_title'] ) {
				if ( 'on' === $props['title_link'] ) {
					$title_output = sprintf( '<%2$s class="entry-title"><a href="%3$s" %4$s>%1$s</a></%2$s>', $title, $header_level, $permalink, $new_tab );
				} else {
					$title_output = sprintf( '<%2$s class="entry-title">%1$s</%2$s>', $title, $header_level );
				}
				$custom_title              = apply_filters( 'dpdfg_custom_header', $title_output, $props, $id );
				$sections_outputs['title'] = et_core_intentionally_unescaped( sprintf( '<div class="dp-dfg-header entry-header">%1$s</div>', $custom_title ), 'html' );
			}
			/*
			 * Post Meta
			 */
			$sections_outputs['meta'] = '';
			if ( 'on' === $props['show_post_meta'] ) {
				$meta_output = '';
				/*
				 * Author
				 */
				if ( 'on' === $props['show_author'] ) {
					$meta_output .= sprintf( '<span class="author vcard"><a href="%3$s">%1$s</a>%2$s</span>', $author_prefix . ' ' . $author, ( 'on' === $props['show_date'] || 'on' === $props['show_terms'] || 'on' === $props['show_comments'] ) ? $props['meta_separator'] : '', $author_link );
				}
				/*
				 * Date
				 */
				if ( 'on' === $props['show_date'] ) {
					$meta_output .= sprintf( '<span class="published">%1$s%2$s</span>', $date, ( 'on' === $props['show_terms'] || 'on' === $props['show_comments'] ) ? $props['meta_separator'] : '' );
				}
				/*
				 * Terms
				 */
				if ( 'on' === $props['show_terms'] ) {
					if ( isset( $post['terms_links'] ) && ! empty( $post['terms_links'] ) ) {
						$links = array();
						foreach ( $post['terms_links'] as $term ) {
							if ( 'on' === $props['terms_links'] ) {
								$links[] = sprintf( '<a href="%1$s" class="term-link %3$s taxonomy-%5$s" data-term-id="%4$s">%2$s</a>', esc_url( $term['link'] ), esc_html( $term['name'] ), esc_html( $term['slug'] ), intval( $term['id'] ), esc_html( $term['tax'] ) );
							} else {
								$links[] = sprintf( '<span class="%2$s taxonomy-%4$s" data-term-id="%3$s">%1$s</span>', esc_html( $term['name'] ), esc_html( $term['slug'] ), intval( $term['id'] ), esc_html( $term['tax'] ) );
							}
						}
						$separator = ' ';
						if ( ! empty( $props['terms_separator'] ) ) {
							$separator = sprintf( '<span class="term-separator">%1$s</span> ', $props['terms_separator'] );
						}
						$meta_output .= sprintf( '<span class="terms">%1$s%2$s</span>', implode( $separator, $links ), ( 'on' === $props['show_comments'] ) ? $props['meta_separator'] : '' );
					}
				}
				/*
				 * Comments
				 */
				if ( 'on' === $props['show_comments'] ) {
					$meta_output .= sprintf( '<span class="comments">%1$s</span>', esc_html( $post['comments'] ) );
				}
				$custom_meta              = apply_filters( 'dpdfg_custom_meta', $meta_output, $props, $id );
				$sections_outputs['meta'] = et_core_intentionally_unescaped( sprintf( '<div class="dp-dfg-meta entry-meta">%1$s</div>', $custom_meta ), 'html' );
			}
			/*
			 * Content / Excerpt
			 */
			$sections_outputs['content'] = '';
			if ( 'off' !== $props['show_content'] ) {
				$sections_outputs['content'] = et_core_intentionally_unescaped( sprintf( '<div class="dp-dfg-content entry-summary">%1$s</div>', $post['content'] ), 'html' );
			}
			/*
			 * Action button
			 */
			$sections_outputs['action_button'] = '';
			if ( '' === $props['action_button_icon'] ) {
				$props['action_button_icon'] = '%%20%%';
			}
			if ( 'on' === $props['action_button'] && '' !== $props['action_button_text'] ) {
				$sections_outputs['action_button'] = et_core_intentionally_unescaped( sprintf( '<div class="et_pb_button_wrapper action-button-wrapper"><a class="et_pb_button dp-dfg-action-button" href="%1$s" data-icon="%3$s">%2$s</a></div>', '', esc_html( $props['action_button_text'] ), esc_attr( et_pb_process_font_icon( $props['action_button_icon'] ) ) ), 'html' );
			}
			/*
			 * Read more
			 */
			$sections_outputs['read_more_button'] = '';
			if ( '' === $props['read_more_button_icon'] ) {
				$props['read_more_button_icon'] = '%%20%%';
			}
			if ( 'on' === $props['read_more'] && '' !== $props['read_more_text'] ) {
				$sections_outputs['read_more_button'] = et_core_intentionally_unescaped( sprintf( '<div class="et_pb_button_wrapper read-more-wrapper"><a class="et_pb_button dp-dfg-more-button" href="%1$s" data-icon="%3$s" %4$s>%2$s</a></div>', $permalink, esc_html( $props['read_more_text'] ), esc_attr( et_pb_process_font_icon( $props['read_more_button_icon'] ) ), $new_tab ), 'html' );

			}
			/*
			 * Custom Content
			 */
			$sections_outputs['custom_content'] = '';
			if ( 'on' === $props['show_custom_content'] || 'on' === $props['show_custom_fields'] ) {
				$custom_fields = '';
				if ( 'on' === $props['show_custom_fields'] && ! empty( $post['custom_fields'] ) ) {
					foreach ( $post['custom_fields'] as $cf ) {
						if ( ! empty( $cf['value'] ) ) {
							$custom_fields .= sprintf( '<p class="dp-dfg-custom-field dp-dfg-cf-%3$s">%1$s<span class="dp-dfg-custom-field-value">%2$s</span></p>', ( ! empty( $cf['label'] ) ) ? '<span class="dp-dfg-custom-field-label">' . et_core_intentionally_unescaped( $cf['label'], 'html' ) . '</span>' : '', et_core_intentionally_unescaped( $cf['value'], 'html' ), str_replace( ' ', '_', $cf['name'] ) );
						}
					}
				}
				$custom_content = '';
				if ( 'on' === $props['show_custom_content'] ) {
					$custom_content = $post['after_read_more'];
				}
				if ( 'on' === $props['custom_content_container'] ) {
					$sections_outputs['custom_content'] = et_core_intentionally_unescaped( sprintf( '<div class="dp-dfg-custom-content">%1$s</div>', $custom_fields . $custom_content ), 'html' );
				} else {
					if ( '' !== $custom_fields ) {
						$sections_outputs['custom_content'] = et_core_intentionally_unescaped( sprintf( '%1$s%2$s', $custom_fields, $custom_content ), 'html' );
					} else {
						$sections_outputs['custom_content'] = et_core_intentionally_unescaped( $custom_content, 'html' );
					}
				}
			}
			/*
			 * Item Output
			 */
			$sections_outputs['end'] = sprintf( '</article><!-- DPDFG End Post Item Container -->%1$s',
				( 'dp-dfg-layout-masonry-standard' === $props['items_layout'] ) ? '</div>' : '' );
			$final_output            = '';
			foreach ( $sections_outputs as $section => $output ) {
				$final_output .= apply_filters( 'dpdfg_ext_get_items_html', $output, $section, $post, $props, $additional_data );
			}
			echo et_core_intentionally_unescaped( $final_output, 'html' );
		}

		return ob_get_clean();
	}

	/**
	 * Set the query arguments
	 *
	 * @param array $props Module properties
	 * @param int $page Page number
	 *
	 * @return array $query_args Query arguments
	 * @since 1.0.0
	 */
	public static function set_query_arguments( $props, $page, $from ) {
		$conditional_tags = isset( $props['conditional_tags'] ) ? $props['conditional_tags'] : array(
			'is_user_logged_in' => 'on',
			'is_front_page'     => 'off',
			'is_singular'       => 'on',
			'is_archive'        => 'off',
			'is_search'         => 'off',
			'is_tax'            => 'off',
			'is_author'         => 'off',
			'is_date'           => 'off',
			'is_post_type'      => 'off'
		);
		$query_vars       = isset( $props['query_var'] ) ? $props['query_var'] : 0;
		$the_id           = isset( $props['the_ID'] ) ? $props['the_ID'] : 0;
		/*
		 * Defaults arguments
		 */
		$query_args = array(
			'post_type'       => array( 'post' ),
			'post_status'     => 'publish',
			'posts_per_page'  => 12,
			'dfg_query_label' => 'dp_divi_filtergrid',
			'dfg_the_ID'      => $props['the_ID'],
			'dfg_module_id'   => isset( $props['module_id'] ) ? $props['module_id'] : 'DFG',
			'dfg_context'     => isset( $props['query_context'] ) ? $props['query_context'] : 'initial_query'
		);
		/*
		 * Set arguments according query types
		 */
		switch ( $props['custom_query'] ) {
			case 'custom':
				$query_args = apply_filters( 'dpdfg_custom_query_args', $query_args, $props );
				if ( 'on' === $props['show_filters'] && isset( $props['active_filter'] ) && ! empty( $props['active_filter'] ) && ! empty( $props['filter_taxonomies'] ) ) {
					$tax_query_with_filters[] = self::get_filter_tax_query_arguments( $props );
					if ( isset( $query_args['tax_query'] ) ) {
						$tax_query_with_filters['relation'] = 'AND';
						$tax_query_with_filters[]           = array( $query_args['tax_query'] );
					}
					$query_args['tax_query'] = $tax_query_with_filters;
				}
				break;
			case 'advanced':
				if ( isset( $props['multiple_cpt'] ) && '' !== $props['multiple_cpt'] ) {
					$query_args['post_type'] = self::process_comma_separate_list( $props['multiple_cpt'] );
				}
				if ( 'on' === $props['use_taxonomy_terms'] ) {
					$include_taxonomies_array = array();
					if ( isset( $props['multiple_taxonomies'] ) && '' !== $props['multiple_taxonomies'] ) {
						$taxonomies = self::process_comma_separate_list( $props['multiple_taxonomies'] );
						foreach ( $taxonomies as $tax ) {
							if ( taxonomy_exists( $tax ) ) {
								$include_taxonomies_array[] = $tax;
							}
						}
					}
					if ( empty( $include_taxonomies_array ) ) {
						$include_taxonomies_array = array( 'category' );
					}
					$exclude_taxonomies_array = array();
					if ( isset( $props['exclude_taxonomies'] ) && '' !== $props['exclude_taxonomies'] ) {
						$taxonomies = self::process_comma_separate_list( $props['exclude_taxonomies'] );
						foreach ( $taxonomies as $tax ) {
							if ( taxonomy_exists( $tax ) ) {
								$exclude_taxonomies_array[] = $tax;
							}
						}
					}
					if ( empty( $exclude_taxonomies_array ) ) {
						$exclude_taxonomies_array = array( 'post_tag' );
					}
					$query_args['tax_query'] = self::process_tax_query( $props, $include_taxonomies_array, $exclude_taxonomies_array );
				} else {
					$all_tax = array();
					foreach ( $query_args['post_type'] as $pt ) {
						$all_tax = array_merge( $all_tax, get_object_taxonomies( $pt, 'names' ) );
					}
				}
				break;
			case 'basic':
				if ( ! empty( $props['include_categories'] ) ) {
					$query_args['cat'] = $props['include_categories'];
				}
				if ( 'on' === $props['show_filters'] && isset( $props['active_filter'] ) && ! empty( $props['active_filter'] ) ) {
					$query_args['tax_query'][] = self::get_filter_tax_query_arguments( $props );
				}
				break;
			case 'archive':
				if ( isset( $props['multiple_cpt'] ) && '' !== $props['multiple_cpt'] ) {
					$query_args['post_type'] = self::process_comma_separate_list( $props['multiple_cpt'] );
				}
				$query_args['tax_query'] = array();
				if ( 'on' === $conditional_tags['is_archive'] ) {
					if ( 'on' === $conditional_tags['is_author'] ) {
						$query_args['author'] = $the_id;
					}
					if ( 'on' === $conditional_tags['is_date'] ) {
						$query_args['year']     = $query_vars['year'];
						$query_args['monthnum'] = $query_vars['monthnum'];
						$query_args['day']      = $query_vars['day'];
					}
					if ( 'on' === $conditional_tags['is_tax'] ) {
						$term_object = get_term( $the_id );
						if ( ! ( is_wp_error( $term_object ) || is_null( $term_object ) ) ) {
							$taxonomy = $term_object->taxonomy;
							if ( 'on' === $props['current_post_type'] ) {
								$query_args['post_type'] = get_taxonomy( $taxonomy )->object_type;
							}
							$query_args['tax_query'] = array(
								array(
									'taxonomy' => $taxonomy,
									'field'    => 'term_id',
									'terms'    => array( $the_id )
								)
							);
						}
					}
					if ( 'on' === $conditional_tags['is_post_type'] && 'on' === $props['current_post_type'] ) {
						$query_args['post_type'] = $query_vars['post_type'];
					}
				} else if ( 'on' === $conditional_tags['is_search'] ) {
					$query_args['s'] = $query_vars['s'];
				}
				if ( 'on' === $props['show_filters'] && isset( $props['active_filter'] ) && ! empty( $props['active_filter'] ) ) {
					$tax_query_with_filters[] = self::get_filter_tax_query_arguments( $props );
					if ( isset( $query_args['tax_query'] ) ) {
						$tax_query_with_filters['relation'] = 'AND';
					}
					$query_args['tax_query'] = array_merge( $query_args['tax_query'], $tax_query_with_filters );
				}
				break;
			case 'posts_ids':
				$query_args['post_type'] = array( 'any' );
				if ( ! empty( $props['posts_ids'] ) ) {
					$ids = array();
					if ( is_array( $props['posts_ids'] ) ) {
						$posts_ids = $props['posts_ids'];
					} else {
						$posts_ids = explode( ',', $props['posts_ids'] );
					}
					if ( is_array( $posts_ids ) ) {
						foreach ( $posts_ids as $id ) {
							if ( intval( $id ) ) {
								$ids[] = intval( $id );
							}
						}
					}
					$query_args['post__in'] = $ids;
				}
				break;
			case 'related':
				if ( 'on' === $conditional_tags['is_singular'] ) {
					$current_post_type        = get_post_type( $the_id );
					$query_args['post_type']  = array( $current_post_type );
					$current_taxonomies_terms = array();
					if ( isset( $props['related_taxonomies'] ) && ! empty( $props['related_taxonomies'] ) ) {
						$current_taxonomies = self::process_comma_separate_list( $props['related_taxonomies'] );
					} else {
						$current_taxonomies = array_diff( get_object_taxonomies( $current_post_type ), apply_filters( 'dpdfg_blacklisted_related_taxonomies', array(
								'layout_category',
								'layout_pack',
								'layout_type',
								'scope',
								'module_width',
								'post_format',
								'translation_priority'
							)
						) );
					}
					if ( ! empty( $current_taxonomies ) ) {
						foreach ( $current_taxonomies as $taxonomy ) {
							$current_terms_of_taxonomy = get_the_terms( $the_id, $taxonomy );
							if ( ! empty( $current_terms_of_taxonomy ) ) {
								$terms = array();
								foreach ( $current_terms_of_taxonomy as $term ) {
									$terms[] = $term->term_id;
								}
								$current_taxonomies_terms[ $taxonomy ] = $terms;
							}
						}
					}
					$query_args['tax_query'] = array();
					if ( ! empty( $current_taxonomies_terms ) ) {
						$criteria                  = $props['related_criteria'];
						$related_query             = array();
						$related_query['relation'] = ( 'one_in_one' === $criteria || 'all_in_one' === $criteria ) ? 'OR' : 'AND';
						foreach ( $current_taxonomies_terms as $tax_name => $terms_array ) {
							$related_query[] = array(
								'taxonomy'         => $tax_name,
								'terms'            => $terms_array,
								'field'            => 'term_id',
								'operator'         => ( 'all_in_all' === $criteria || 'all_in_one' === $criteria ) ? 'AND' : 'IN',
								'include_children' => false
							);
						}
						$query_args['tax_query'][] = $related_query;
					}
					if ( 'on' === $props['show_filters'] && isset( $props['active_filter'] ) && ! empty( $props['active_filter'] ) ) {
						$tax_query_with_filters[] = self::get_filter_tax_query_arguments( $props );
						if ( isset( $query_args['tax_query'] ) ) {
							$tax_query_with_filters['relation'] = 'AND';
						}
						$query_args['tax_query'] = array_merge( $query_args['tax_query'], $tax_query_with_filters );
					}
				}
				break;
		}
		/*
		 * Common arguments for all query types except custom.
		 */
		if ( 'custom' !== $props['custom_query'] ) {
			$query_args['posts_per_page'] = intval( $props['post_number'] );
			if ( intval( $props['offset_number'] ) ) {
				$query_args['offset'] = ( ( $page - 1 ) * $props['post_number'] ) + intval( $props['offset_number'] );
			}
			$query_args['order'] = $props['order'];
			if ( 'rand' === $props['orderby'] && isset( $props['seed'] ) ) {
				$query_args['orderby'] = 'RAND(' . intval( $props['seed'] ) . ')';
			} else {
				$query_args['orderby'] = $props['orderby'];
			}
			if ( 'meta_value' === $props['orderby'] && '' !== $props['meta_key'] ) {
				$query_args['meta_key']  = $props['meta_key'];
				$query_args['meta_type'] = $props['meta_type'];
			}
		}
		/*
		 * Pagination
		 */
		$query_args['paged'] = $page;
		/*
		 * Sticky posts, remove current post, post status for logged users, current author
		 */
		if ( 'on' === $props['show_private'] && 'on' === $conditional_tags['is_user_logged_in'] ) {
			$query_args['post_status'] = array( 'publish', 'private' );
		}
		if ( 'on' === $props['current_author'] && 'on' === $conditional_tags['is_singular'] && isset( $props['the_author'] ) ) {
			$query_args['author'] = $props['the_author'];
		}
		if ( 'on' === $props['sticky_posts'] ) {
			$query_args['ignore_sticky_posts'] = 1;
		}
		if ( ( 'related' === $props['custom_query'] || 'on' === $props['remove_current_post'] ) && 'on' === $conditional_tags['is_singular'] ) {
			$query_args['post__not_in'] = array( $the_id );
		}
		/*
		 * Search for Ajax
		 */
		if ( 'on' === $props['show_search'] && 'ajax' === $from && $query_vars && '' !== $query_vars['s'] ) {
			$query_args['s'] = $query_vars['s'];
			if ( 'on' === $props['orderby_search'] ) {
				$query_args['orderby'] = 'relevance';
			}
		}
		/*
		 * Third party support
		 */
		if ( 'third_party' === $props['custom_query'] ) {
			switch ( $props['support_for'] ) {
				case 'sfp':
					$query_args = array();
					if ( ! empty( $props['sfp_id'] ) ) {
						$query_args['search_filter_id'] = $props['sfp_id'];
					}
					break;
			}
		}
		/*
		 * Relevanssi support
		 */
		if ( isset( $props['relevanssi'] ) && $props['relevanssi'] === 'on' ) {
			$query_args['relevanssi'] = true;
		}

		return apply_filters( 'dpdfg_ext_set_query_arguments', $query_args, $props );
	}

	/**
	 * Build the tax_query part of the query arguments
	 *
	 * @param array $props Module properties
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public static function process_tax_query( $props, $include_taxonomies_array, $exclude_taxonomies_array ) {
		$tax_query              = array();
		$tax_query_with_filters = array();
		if ( ! empty( $props['include_terms'] ) ) {
			$terms_ids_array = self::process_comma_separate_list( $props['include_terms'] );
			if ( ! empty( $terms_ids_array ) && is_array( $terms_ids_array ) ) {
				$include_tax_query = array();
				foreach ( $include_taxonomies_array as $taxonomy_name ) {
					$terms_of_taxonomy = array();
					foreach ( $terms_ids_array as $term_id ) {
						$term_object = get_term( $term_id );
						if ( ! ( is_wp_error( $term_object ) || is_null( $term_object ) ) ) {
							if ( $term_object->taxonomy === $taxonomy_name ) {
								$terms_of_taxonomy[] = $term_id;
							}
						}
					}
					if ( ! empty( $terms_of_taxonomy ) ) {
						$include_tax_query[] = array(
							'taxonomy'         => $taxonomy_name,
							'terms'            => $terms_of_taxonomy,
							'field'            => 'term_id',
							'operator'         => $props['terms_relation'],
							'include_children' => 'on' === $props['include_children_terms'],
						);
					}
				}
				if ( is_array( $include_tax_query ) && count( $include_tax_query ) > 1 ) {
					$include_tax_query['relation'] = $props['taxonomies_relation'];
				}
			}
		}
		if ( ! empty( $props['exclude_terms'] ) ) {
			$terms_ids_array = self::process_comma_separate_list( $props['exclude_terms'] );
			if ( ! empty( $terms_ids_array ) && is_array( $terms_ids_array ) ) {
				$exclude_tax_query = array();
				foreach ( $exclude_taxonomies_array as $taxonomy_name ) {
					$terms_of_taxonomy = array();
					foreach ( $terms_ids_array as $term_id ) {
						$term_object = get_term( $term_id );
						if ( ! ( is_wp_error( $term_object ) || is_null( $term_object ) ) ) {
							if ( $term_object->taxonomy === $taxonomy_name ) {
								$terms_of_taxonomy[] = $term_id;
							}
						}
					}
					if ( ! empty( $terms_of_taxonomy ) ) {
						$exclude_tax_query[] = array(
							'taxonomy' => $taxonomy_name,
							'terms'    => $terms_of_taxonomy,
							'field'    => 'term_id',
							'operator' => 'NOT IN',
						);
					}
				}
				if ( is_array( $exclude_tax_query ) && count( $exclude_tax_query ) > 1 ) {
					$exclude_tax_query['relation'] = $props['exclude_taxonomies_relation'];
				}
			}
		}
		/*
		 * Arrange taxonomy query for the cases when is use both or one of the include or exclude taxonomies
		 */
		if ( ! empty( $include_tax_query ) && ! empty( $exclude_tax_query ) ) {
			$tax_query[]           = $include_tax_query;
			$tax_query[]           = $exclude_tax_query;
			$tax_query['relation'] = 'AND';
		} elseif ( ! empty( $include_tax_query ) && empty( $exclude_tax_query ) ) {
			$tax_query = $include_tax_query;
		} elseif ( empty( $include_tax_query ) && ! empty( $exclude_tax_query ) ) {
			$tax_query = $exclude_tax_query;
		}
		/*
		 * Add filters to the taxonomy query is set
		 */
		if ( 'on' === $props['show_filters'] && isset( $props['active_filter'] ) && ! empty( $props['active_filter'] ) && ! empty( $props['filter_taxonomies'] ) ) {
			$tax_query_with_filters[] = self::get_filter_tax_query_arguments( $props );
			if ( ! empty( $tax_query ) ) {
				$tax_query_with_filters['relation'] = 'AND';
				$tax_query_with_filters[]           = array( $tax_query );
			}

			return $tax_query_with_filters;
		} else {
			return $tax_query;
		}
	}

	/**
	 * @param $props
	 *
	 * @return array|string[]
	 */
	public static function get_multilevel_tax_data( $props ) {
		$taxonomies          = array();
		$multilevel_tax_data = self::clean_multilevel_tax_data( $props );
		if ( is_array( $multilevel_tax_data ) && ! empty( $multilevel_tax_data ) ) {
			foreach ( $multilevel_tax_data as $tax ) {
				$taxonomies[] = $tax['name'];
			}
		} else {
			$taxonomies = array( 'category' );
		}

		return $taxonomies;
	}

	/**
	 * @param $props
	 *
	 * @return mixed
	 */
	public static function clean_multilevel_tax_data( $props ) {
		if ( 'on' === $props['multilevel_hierarchy'] ) {
			$clean = html_entity_decode( stripslashes( $props['multilevel_hierarchy_tax'] ) );
		} else {
			$clean = html_entity_decode( stripslashes( $props['multilevel_tax_data'] ) );
		}

		return json_decode( $clean, true );
	}

	/**
	 * Get the taxonomy filter terms
	 *
	 * @param $props
	 * @param $posts_data
	 *
	 * @return array|mixed|void
	 */
	public static function get_filter_terms( $props, $posts_data ) {
		/*
		* Define taxonomies and filters terms based on module selection
		*/
		$filter_terms_args   = array();
		$filter_terms        = array();
		$terms               = array();
		$filter_levels       = array();
		$multilevel_tax_data = array();
		if ( 'basic' !== $props['custom_query'] ) {
			if ( 'on' === $props['multilevel'] ) {
				$multilevel_tax_data = self::clean_multilevel_tax_data( $props );
				$filter_taxonomies   = self::get_multilevel_tax_data( $props );
				if ( '' !== $props['filter_terms'] ) {
					$filter_terms = self::process_comma_separate_list( $props['filter_terms'] );
				}
			} else {
				$filter_taxonomies = self::process_comma_separate_list( $props['filter_taxonomies'] );
				if ( '' !== $props['filter_terms'] ) {
					$filter_terms = self::process_comma_separate_list( $props['filter_terms'] );
				}
			}
		} else {
			$filter_taxonomies = array( 'category' );
			if ( ! empty( $props['include_categories'] ) ) {
				$filter_terms = self::process_comma_separate_list( $props['include_categories'] );
			}
		}
		/*
		 * Define the terms to use for filter
		 */
		if ( is_array( $filter_taxonomies ) && ! empty( $filter_taxonomies ) ) {
			$filter_terms_args['taxonomy'] = $filter_taxonomies;
			if ( 'custom' !== $props['filters_sort'] && 'hierarchy' !== $props['filters_sort'] ) {
				$filter_terms_args['orderby'] = $props['filters_sort'];
				if ( 'term_group' !== $props['filters_sort'] ) {
					/*
					 * Ignore order define by plugin Category Order and Taxonomy Terms Order
					 * https://wordpress.org/plugins/taxonomy-terms-order/
					 */
					add_filter( 'to/get_terms_orderby/ignore', function () {
						return true;
					} );
				}
			}
			$filter_terms_args['order'] = $props['filters_order'];
			if ( is_array( $filter_terms ) && ! empty( $filter_terms ) ) {
				$filter_terms_args['include'] = $filter_terms;
			}
			$filter_terms_args = apply_filters( 'dpdfg_filters_query_args', $filter_terms_args, $props );
			$terms             = get_terms( $filter_terms_args );
		}
		if ( is_wp_error( $terms ) ) {
			$terms = array();
		}
		/*
		 * Custom order filters terms
		 */
		if ( 'custom' === $props['filters_sort'] && ! empty( $props['filters_custom'] ) ) {
			$terms_names          = self::process_comma_separate_list( $props['filters_custom'] );
			$terms_custom_ordered = array();
			foreach ( $terms_names as $term_name ) {
				foreach ( $terms as $term ) {
					if ( htmlentities2( $term->name ) === htmlentities2( $term_name ) || $term->term_id === intval( $term_name ) ) {
						$terms_custom_ordered[] = $term;
					}
				}
			}
			if ( ! empty( $terms_custom_ordered ) ) {
				$terms = $terms_custom_ordered;
			}
		}
		/*
		 * Hierarchy order filters terms
		 */
		if ( 'hierarchy' === $props['filters_sort'] ) {
			$ordered_terms = array();
			$sorted_terms  = array();
			self::sort_terms_hierarchically( $terms, $sorted_terms );
			self::get_terms_in_hierarchical_order( $sorted_terms, $ordered_terms, array() );
			$terms = $ordered_terms;
		}
		/*
		* Define filter levels
		*/
		if ( 'off' === $props['multilevel'] && 'off' === $props['hide_all'] ) {
			$filter_levels['all'][] = array(
				'id'    => 'all',
				'name'  => ( isset( $props['all_text'] ) && 'All' !== $props['all_text'] ) ? $props['all_text'] : __( 'All', 'dpdfg-dp-divi-filtergrid' ),
				'slug'  => 'all',
				'level' => 0
			);
		}
		foreach ( $terms as $term ) {
			$tax    = 'all';
			$parent = $term->parent;
			$level  = $term->parent && ( $props['multilevel_hierarchy'] === 'on' || $props['filters_sort'] === 'hierarchy' ) ? count( get_ancestors( $term->term_id, $term->taxonomy ) ) : 0;
			if ( 'on' === $props['multilevel'] ) {
				$tax = $term->taxonomy;
				if ( 'on' === $props['multilevel_hierarchy'] && $parent ) {
					$tax = $tax . "%%" . $parent;
				}
				if ( ! isset( $filter_levels[ $tax ] ) && taxonomy_exists( $tax ) ) {
					$filter_levels[ $tax ] = array();
					if ( 'off' === $props['hide_all'] && is_array( $multilevel_tax_data ) ) {
						$all_text = '';
						foreach ( $multilevel_tax_data as $tax_data ) {
							if ( $tax_data['name'] === $tax && isset( $tax_data['all'] ) ) {
								$all_text = $tax_data['all'];
							}
						}
						$filter_levels[ $tax ][] = array(
							'id'     => 'all',
							'name'   => ( '' === $all_text ) ? ( 'All ' . get_taxonomy( $tax )->labels->name ) : $all_text,
							'slug'   => 'all',
							'parent' => 'all',
							'level'  => 0
						);
					}
				}
			}
			$filter_levels[ $tax ][] = array(
				'id'     => $term->term_id,
				'name'   => $term->name,
				'slug'   => $term->slug,
				'parent' => $parent,
				'level'  => $level
			);
		}
		/*
		 * Order taxonomies
		 */
		if ( 'on' === $props['multilevel'] && ! empty( $multilevel_tax_data ) ) {
			$levels_order = array();
			$order        = 0;
			foreach ( $multilevel_tax_data as $tax ) {
				if ( isset( $filter_levels[ $tax['name'] ] ) ) {
					$key                  = $order . '%%' . $tax['name'];
					$levels_order[ $key ] = $filter_levels[ $tax['name'] ];
					$order ++;
					if ( 'on' === $props['multilevel_hierarchy'] ) {
						foreach ( $filter_levels as $tax_name => $level ) {
							if ( $tax_name !== $tax['name'] && false !== strpos( $tax_name, $tax['name'] ) ) {
								$key                  = $order . '%%' . $tax_name;
								$levels_order[ $key ] = $level;
								$order ++;
							}
						}
					}
				}
			}
			$filter_levels = $levels_order;
		}
		/*
		 * Custom filters filter
		 */
		if ( 'on' === $props['use_custom_terms_filters'] ) {
			$filter_levels = apply_filters( 'dpdfg_custom_filters', $filter_levels, $props );
		}

		return array(
			'levels'      => $filter_levels,
			'levels_data' => $multilevel_tax_data
		);
	}

	/**
	 *
	 * @param $filter
	 * @param $active_filter
	 * @param $props
	 *
	 * @return mixed|void
	 */
	public static function filter_button_output( $filter, $active_filter, $props ) {
		$filter_output = sprintf( '<li class="dp-dfg-filter" data-level="%5$s" data-parent="%6$s"><a class="dp-dfg-filter-link%2$s %4$s" href="#" data-term-id="%1$s" >%3$s</a></li>',
			esc_attr( $filter['id'] ),
			$filter['id'] === $active_filter ? ' active' : '',
			esc_attr( $filter['name'] ),
			isset( $filter['slug'] ) ? esc_attr( $filter['slug'] ) : '',
			isset( $filter['level'] ) ? esc_attr( $filter['level'] ) : 0,
			isset( $filter['parent'] ) ? esc_attr( $filter['parent'] ) : 0
		);

		return apply_filters( 'dpdfg_filter_button_output', $filter_output, $filter, $active_filter, $props );
	}

	/**
	 * Get tax query array arguments for the active filter
	 *
	 * @param array $props Module properties
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public static function get_filter_tax_query_arguments( $props ) {
		$filters_query = array();
		if ( ( isset( $props['multifilter'] ) && 'on' === $props['multifilter'] ) || ( isset( $props['multilevel'] ) && 'on' === $props['multilevel'] ) ) {
			$terms      = explode( '|', $props['active_filter'] );
			$taxonomies = array();
			foreach ( $terms as $term_id ) {
				$term_object = get_term( $term_id );
				if ( ! ( is_wp_error( $term_object ) || is_null( $term_object ) ) ) {
					if ( 'on' === $props['multilevel_hierarchy'] ) {
						$tax = $term_object->taxonomy . "%%" . $term_object->parent;
					} else {
						$tax = $term_object->taxonomy;
					}
					if ( ! isset( $taxonomies[ $tax ] ) ) {
						$taxonomies[ $tax ] = array();
					}
					$taxonomies[ $tax ][] = $term_id;
				}
			}
			foreach ( $taxonomies as $tax_name => $terms_array ) {
				if ( 'on' === $props['multilevel_hierarchy'] ) {
					$tax_name = explode( '%%', $tax_name )[0];
				}
				$filters_query[] = array(
					'taxonomy'         => $tax_name,
					'terms'            => $terms_array,
					'field'            => 'term_id',
					'operator'         => ( $props['multifilter_relation'] === 'AND' ) ? 'AND' : 'IN',
					'include_children' => 'on' === $props['filter_children_terms']
				);
			}
			if ( ! empty( $taxonomies ) ) {
				if ( count( $taxonomies ) > 1 ) {
					$filters_query['relation'] = ( isset( $props['multilevel_relation'] ) && 'on' === $props['multilevel'] ) ? $props['multilevel_relation'] : $props['multifilter_relation'];
				} else {
					$filters_query = $filters_query[0];
				}
			}
		} else {
			$active_filter_term = get_term( $props['active_filter'] );
			if ( ! ( is_wp_error( $active_filter_term ) || is_null( $active_filter_term ) ) ) {
				$active_filter_taxonomy = $active_filter_term->taxonomy;
				$filters_query          = array(
					'taxonomy'         => $active_filter_taxonomy,
					'terms'            => $props['active_filter'],
					'field'            => 'term_id',
					'operator'         => 'IN',
					'include_children' => 'on' === $props['filter_children_terms']
				);
			}

			// TS CUSTOM CODE START
			if ( is_null( $active_filter_term )  ) {
				$active_filter_term = get_post_type_object( $props['active_filter'] );
				if ( get_class($active_filter_term) == "WP_Post_Type" ) {
					$filters_query = false;
				}
			}
			// TS CUSTOM CODE END
		}

		return $filters_query;
	}

	/**
	 * Process comma separate list to obtain an array.
	 *
	 * @param string $list List options
	 *
	 * @return array List options
	 */
	public static function process_comma_separate_list( $list ) {
		if ( is_string( $list ) ) {
			$array = explode( ',', $list );
			if ( is_array( $array ) ) {
				foreach ( $array as $key => $value ) {
					$array[ $key ] = trim( $value );
				}
			}

			return $array;
		} else {
			return array();
		}
	}

	/**
	 * Get content or excerpt.
	 * Truncate content if excerpt is not set but is selected.
	 * Strip HTML is active.
	 *
	 * @param array $props Module props
	 *
	 * @return string excerpt
	 */
	public static function get_content( $props ) {
		if ( 'off' === $props['show_content'] ) {
			return '';
		} else {
			if ( 'excerpt' === $props['content_length'] ) {
				if ( has_excerpt() ) {
					if ( 'on' === $props['strip_html'] ) {
						$post_content = wp_strip_all_tags( get_the_excerpt() );
					} else {
						$post_content = get_the_excerpt();
					}
					if ( 'on' === $props['truncate_excerpt'] && intval( $props['truncate_content'] ) ) {
						return self::truncate_content( $post_content, $props['truncate_content'] );
					} else {
						return $post_content;
					}
				} else {
					if ( 'on' === $props['strip_html'] ) {
						$post_content = wp_strip_all_tags( get_the_content() );
					} else {
						$post_content = get_the_content();
					}
					if ( function_exists( 'et_builder_strip_dynamic_content' ) ) {
						$post_content = strip_shortcodes( et_builder_strip_dynamic_content( et_strip_shortcodes( $post_content ) ) );
					} else {
						$post_content = strip_shortcodes( et_strip_shortcodes( $post_content ) );
					}
					if ( isset( $props['truncate_content'] ) && intval( $props['truncate_content'] ) ) {
						return self::truncate_content( $post_content, $props['truncate_content'] );
					} else {
						return $post_content;
					}
				}
			} else {
				global $et_pb_rendering_column_content;
				// Set it as true to tell builder if we're going to process inner module.
				$et_pb_rendering_column_content = true;
				// Clean up internal module style.
				ET_Builder_Element::clean_internal_modules_styles();
				if ( et_pb_is_pagebuilder_used( get_the_ID() ) ) {
					$content = do_shortcode( get_the_content() );
				} else {
					// phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited
					$content = et_core_intentionally_unescaped( apply_filters( 'the_content', get_the_content() ), 'html' );
				}
				// Collect style for the previous content processing.
				$internal_style = ET_Builder_Element::get_style( true );
				// Reset the internal style.
				ET_Builder_Element::clean_internal_modules_styles( false );
				// Set it as false to reset.
				$et_pb_rendering_column_content = false;
				// Print internal styles for the builder preview.
				$style = '';
				if ( $internal_style ) {
					$style = sprintf( '<style type="text/css" class="et_fb_blog_inner_content_styles">%1$s</style>',
						et_core_esc_previously( $internal_style )
					);
				}

				return $content . $style;
			}
		}
	}

	/**
	 * Limit the content to a number of characters.
	 *
	 * @param string $excerpt Post Excerpt
	 * @param int $excerpt_limit Number of character to truncate the content
	 *
	 * @return string
	 */
	public static function truncate_content( $excerpt, $excerpt_limit ) {
		$charlength = $excerpt_limit;
		$charlength ++;
		if ( mb_strlen( $excerpt ) > $charlength ) {
			$subex   = trim( mb_substr( $excerpt, 0, $charlength - 5 ) );
			$exwords = explode( ' ', $subex );
			$excut   = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );
			if ( $excut < 0 ) {
				return mb_substr( $subex, 0, $excut ) . '...';
			} else {
				return $subex;
			}
		} else {
			return $excerpt;
		}
	}

	/**
	 * Ajax action to return the form to select the multiple post types on the VB
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	public static function ajax_get_cpt() {
		check_ajax_referer( 'dpdfg_get_cpt_action' );
		$html_output = '<form id="dp-dfg-cpt-form">';
		$html_output .= sprintf( '<p class="dp-dfg-modal-description" data-singular="%2$s">%1$s</p>', __( 'Select one or more post types below. Use CTRL or SHIFT to select multiple.', 'dpdfg-dp-divi-filtergrid' ), __( 'Select one post type below.', 'dpdfg-dp-divi-filtergrid' ) );
		$html_output .= '<select class="dp-dfg-vb-select" name="dp-dfg-vb-select" multiple size="6">';
		foreach ( self::get_cpt() as $key => $cpt ) {
			$html_output .= sprintf( '<option value="%1$s">%2$s</option>', esc_html( $key ), esc_html( $cpt ) );
		}
		$html_output .= '</select>';
		$html_output .= self::vb_modal_actions();
		$html_output .= '</form>';
		echo et_core_intentionally_unescaped( $html_output, 'html' );
		wp_die();
	}

	/**
	 * Get custom post types. Use the filter dpcptfm_default_post_types to modified the default post types.
	 *
	 * @return array $options Array with post types name as key and post type label as value.
	 * @since 1.0.0
	 */
	public static function get_cpt() {
		$options           = array();
		$default_post_type = apply_filters( 'dpdfg_default_post_types', array(
			'post' => get_post_type_object( 'post' ),
			'page' => get_post_type_object( 'page' )
		) );
		$post_types        = array_merge(
			$default_post_type,
			get_post_types(
				array(
					'_builtin' => false,
					'public'   => true,
				),
				'objects'
			)
		);
		foreach ( $post_types as $pt ) {
			$options[ $pt->name ] = $pt->label;
		}

		return $options;
	}

	/**
	 * VB selection modal actions.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public static function vb_modal_actions() {
		$html_output = '<div class="dp-dfg-vb-actions">';
		$html_output .= sprintf( '<input class="dp-dfg-vb-submit" type="button" value="%1$s" />', __( 'Set Values', 'dpdfg-dp-divi-filtergrid' ) );
		$html_output .= sprintf( '<input class="dp-dfg-vb-clean" type="button" value="%1$s" />', __( 'Clean Values', 'dpdfg-dp-divi-filtergrid' ) );
		$html_output .= sprintf( '<input class="dp-dfg-vb-finish" type="button" value="%1$s" />', __( 'Exit', 'dpdfg-dp-divi-filtergrid' ) );
		$html_output .= '</div>';

		return $html_output;
	}

	/**
	 * Ajax action to return the form to select the multiple taxonomies on the VB
	 *
	 * @since 1.0.0
	 */
	public static function ajax_get_taxonomies() {
		check_ajax_referer( 'dpdfg_get_taxonomies_action' );
		$cpt_array = array( 'post' );
		if ( isset( $_POST['cpt'] ) ) {
			$cpts = sanitize_text_field( wp_unslash( $_POST['cpt'] ) );
			if ( substr_count( $cpts, ',' ) > 0 ) {
				$cpt_array = self::process_comma_separate_list( $cpts );
			} else {
				$cpt_array = array( $cpts );
			}
		}
		$html_output = '<form id="dp-dfg-tax-form">';
		$html_output .= sprintf( '<p class="dp-dfg-modal-description" data-singular="%2$s">%1$s</p>', __( 'Select one or more taxonomies below. Use CTRL or SHIFT to select multiple.', 'dpdfg-dp-divi-filtergrid' ), __( 'Select one taxonomy below.', 'dpdfg-dp-divi-filtergrid' ) );
		$html_output .= '<select class="dp-dfg-vb-select" name="dp-dfg-vb-select" multiple size="6">';
		foreach ( self::get_taxonomies( $cpt_array ) as $key => $tax ) {
			$html_output .= sprintf( '<option value="%1$s">%2$s</option>', esc_html( $key ), esc_html( $tax ) );
		}
		$html_output .= '</select>';
		$html_output .= self::vb_modal_actions();
		$html_output .= '</form>';
		echo et_core_intentionally_unescaped( $html_output, 'html' );
		wp_die();
	}

	/**
	 * Get public taxonomies. Use the filter dpcptfm_blacklisted_taxonomies to remove unwanted taxonomies.
	 *
	 * @param array $cpt Array with post types names
	 *
	 * @return array $options Array with taxonomy name as key and taxonomy label as value.
	 * @since 1.0.0
	 */
	public static function get_taxonomies( $cpt ) {
		$options                = array();
		$blacklisted_taxonomies = apply_filters( 'dpdfg_blacklisted_taxonomies', array(
				'layout_category',
				'layout_pack',
				'layout_type',
				'scope',
				'module_width',
				'post_format',
			)
		);
		$taxonomies             = array_diff(
			get_taxonomies(
				array(
					'public'    => true,
					'query_var' => true,
				)
			),
			$blacklisted_taxonomies
		);
		if ( 'all' === $cpt[0] ) {
			foreach ( $taxonomies as $tax ) {
				$tax_obj         = get_taxonomy( $tax );
				$options[ $tax ] = $tax_obj->label;
			}
		} else {
			foreach ( $taxonomies as $tax ) {
				$tax_obj  = get_taxonomy( $tax );
				$is_there = array_intersect( $cpt, $tax_obj->object_type );
				if ( ! empty( $is_there ) ) {
					$options[ $tax ] = $tax_obj->label;
				}
			}
		}

		return apply_filters( 'dpdfg_allowed_taxonomies', $options );
	}

	/**
	 * Ajax action to return the form to select the multiple taxonomies on the VB
	 *
	 * @since 1.0.0
	 */
	public static function ajax_get_taxonomies_terms() {
		check_ajax_referer( 'dpdfg_get_taxonomies_terms_action' );
		$tax_array = array( 'category' );
		if ( isset( $_POST['tax'] ) ) {
			$taxs = sanitize_text_field( wp_unslash( $_POST['tax'] ) );
			if ( substr_count( $taxs, ',' ) > 0 ) {
				$tax_array = self::process_comma_separate_list( $taxs );
			} else {
				$tax_array = array( $taxs );
			}
		}
		$cpt_array = array( 'post' );
		if ( isset( $_POST['cpt'] ) ) {
			$cpts = sanitize_text_field( wp_unslash( $_POST['cpt'] ) );
			if ( substr_count( $cpts, ',' ) > 0 ) {
				$cpt_array = self::process_comma_separate_list( $cpts );
			} else {
				$cpt_array = array( $cpts );
			}
		}
		$html_output = '<form id="dp-dfg-terms-form">';
		$html_output .= sprintf( '<p class="dp-dfg-modal-description" data-singular="%2$s">%1$s</p>', __( 'Select one or more terms below. Use CTRL or SHIFT to select multiple.', 'dpdfg-dp-divi-filtergrid' ), __( 'Select one term below.', 'dpdfg-dp-divi-filtergrid' ) );
		$html_output .= '<select class="dp-dfg-vb-select" name="dp-dfg-vb-select" multiple size="12">';
		foreach ( self::get_taxonomies_terms( $tax_array, $cpt_array ) as $tax => $terms ) {
			$html_output .= '<optgroup label="' . esc_html( $tax ) . '">';
			$html_output .= self::show_taxonomy_hierarchy( '', $terms );
			$html_output .= '</optgroup>';
		}
		$html_output .= '</select>';
		$html_output .= self::vb_modal_actions();
		$html_output .= '</form>';
		echo et_core_intentionally_unescaped( $html_output, 'html' );
		wp_die();
	}

	/**
	 * @param $html_output
	 * @param $terms
	 * @param int $level
	 *
	 * @return string
	 */
	public static function show_taxonomy_hierarchy( $html_output, $terms, $level = 0 ) {
		foreach ( $terms as $term ) {
			$html_output .= sprintf( '<option value="%1$s">%3$s %2$s</option>', esc_html( $term->term_id ), esc_html( $term->name ) . ' (' . $term->count . ')', str_repeat( '-', $level ) );
			if ( ! empty( $term->children ) ) {
				$html_output .= self::show_taxonomy_hierarchy( '', $term->children, ( $level + 1 ) );
			}
		}

		return $html_output;
	}

	/**
	 * @param $taxonomy
	 * @param int $parent
	 *
	 * @return array
	 */
	public static function get_taxonomy_hierarchy( $taxonomy, $parent = 0 ) {
		$terms    = get_terms( $taxonomy, array(
			'parent'     => $parent,
			'hide_empty' => false
		) );
		$children = array();
		foreach ( $terms as $term ) {
			$term->children             = self::get_taxonomy_hierarchy( $taxonomy, $term->term_id );
			$children[ $term->term_id ] = $term;
		}

		return $children;
	}

	/**
	 * Get taxonomy terms
	 *
	 * @param array $tax Array with taxonomy names
	 * @param array $cpt Array with post types names
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public static function get_taxonomies_terms( $tax, $cpt ) {
		$options            = array();
		$all_cpt_taxonomies = self::get_taxonomies( $cpt );
		foreach ( $all_cpt_taxonomies as $tax_name => $tax_label ) {
			if ( 'all' === $tax[0] || in_array( $tax_name, $tax, true ) ) {
				$terms = self::get_taxonomy_hierarchy( $tax_name );
				if ( ! empty( $terms ) ) {
					$options[ $tax_label . ' (' . $tax_name . ')' ] = $terms;
				}
			}
		}

		return $options;
	}

	/**
	 * Ajax action to return custom fields UI
	 *
	 * @since 1.0.7
	 */
	public static function ajax_get_custom_fields() {
		check_ajax_referer( 'dpdfg_get_custom_fields_action' );
		$html_output = sprintf( '<form id="dp-dfg-cf-form"><p>%1$s</p><div id="dp-dfg-cf-container"><input id="dp-dfg-cf-select" type="text"/><span class="dp-dfg-cf-plus dashicons dashicons-plus-alt"></span></div><div class="dp-dfg-cf-selection"></div>%2$s</form><script>window.dpdfg_admin.customFields=%3$s</script>',
			__( 'Begin typing the custom field you want to add and click the + to add it. Once added, you can optionally add a label before each custom field by typing in the input for that field. Leave input empty to display the custom field value without a label. Drag and drop custom fields to change their order.', 'dpdfg-dp-divi-filtergrid' ),
			self::vb_modal_actions(),
			wp_json_encode( self::get_custom_fields() )
		);
		echo et_core_intentionally_unescaped( $html_output, 'html' );
		wp_die();
	}

	/**
	 * Get library items data
	 *
	 * @return array[]
	 */
	public static function get_library_items() {
		$layouts = array(
			array(
				'value' => 'default',
				'label' => __( 'Default template', 'dpdfg-dp-divi-filtergrid' )
			)
		);
		$args    = apply_filters( 'dpdfg_popup_layout_args', array(
			'post_type'      => 'et_pb_layout',
			'posts_per_page' => - 1,
			'tax_query'      => array(
				array(
					'taxonomy' => 'layout_category',
					'field'    => 'name',
					'terms'    => 'DFG Popup'
				)
			)
		) );
		$query   = new WP_Query( $args );
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$layouts[] = array(
					'value' => get_the_ID(),
					'label' => get_the_title()
				);
			}
		}
		wp_reset_postdata();

		return $layouts;
	}

	/**
	 * Ajax get library items data
	 */
	public static function ajax_get_library_items() {
		check_ajax_referer( 'dpdfg_get_layouts_action', 'nonce_layouts' );
		wp_send_json( self::get_library_items() );
	}

	/**
	 * Get custom fields
	 *
	 * @return array
	 * @since 1.0.7
	 */
	public static function get_custom_fields() {
		$options = array();
		global $wpdb;
		$table_name = $wpdb->base_prefix . 'postmeta';
		$result     = $wpdb->get_results( $wpdb->prepare( 'SELECT DISTINCT meta_key FROM %1s;', $table_name ), ARRAY_A );
		if ( is_array( $result ) ) {
			foreach ( $result as $cf ) {
				$options[] = $cf['meta_key'];
			}
		}

		return $options;
	}

	/**
	 * Ajax action to return multilevel tax UI
	 *
	 * @since 1.4
	 */
	public static function ajax_get_multilevel_tax() {
		check_ajax_referer( 'dpdfg_get_multilevel_tax_action' );
		$options = '';
		foreach ( self::get_taxonomies( array( 'all' ) ) as $tax => $name ) {
			$options .= sprintf( '<option value="%2$s">%1$s</option>', esc_html( $name ), esc_html( $tax ) );
		}
		$html_output = sprintf( '<form id="dp-dfg-cf-form"><p class="dp-dfg-modal-description" data-text-alt="%4$s">%1$s</p><div id="dp-dfg-cf-container"><select id="dp-dfg-cf-select">%3$s</select><span class="dp-dfg-cf-plus dashicons dashicons-plus-alt"></span></div><div class="dp-dfg-cf-selection"></div>%2$s</form>',
			__( 'Select the taxonomy you want to add for each level and click the + to add it. Once added, you can optionally add a label before each level by typing in the first input for that level. Leave the first input empty to display levels without a label. The ALL text can be changed by typing in the second input for each level. Drag and drop levels to change their order.', 'dpdfg-dp-divi-filtergrid' ),
			self::vb_modal_actions(),
			$options,
			__( 'Select the taxonomy you want to use and click the + to add it. Only one taxonomy can be used with the Parent/Child Taxonomy option. Once added, you can optionally add a label by typing in the first input. Leave the first input empty if you do not want to display a label. The ALL text can be changed by typing in the second input.', 'dpdfg-dp-divi-filtergrid' )
		);
		echo et_core_intentionally_unescaped( $html_output, 'html' );
		wp_die();
	}

	/**
	 * Return an array with the skins options. Use the key for the skin class and the value for the skin name.
	 * Use the filter dpdfg_custom_skins to modified the default options.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public static function get_skins() {
		$defaults = array(
			'dp-dfg-skin-none'                                      => __( 'None', 'dpdfg-dp-divi-filtergrid' ),
			'dp-dfg-skin-default'                                   => __( 'Default', 'dpdfg-dp-divi-filtergrid' ),
			'dp-dfg-skin-default dp-dfg-skin-midnight'              => __( 'Midnight', 'dpdfg-dp-divi-filtergrid' ),
			'dp-dfg-skin-default dp-dfg-skin-left-vertical-filters' => __( 'Left Vertical Filters', 'dpdfg-dp-divi-filtergrid' ),
			'dp-dfg-skin-default dp-dfg-skin-itemsinoverlay'        => __( 'Items in Overlay', 'dpdfg-dp-divi-filtergrid' ),
			'dp-dfg-skin-default dp-dfg-skin-zoomimage'             => __( 'Zoom Image', 'dpdfg-dp-divi-filtergrid' ),
			'dp-dfg-skin-default dp-dfg-skin-library'               => __( 'Library', 'dpdfg-dp-divi-filtergrid' ),
		);

		return array_merge( $defaults, apply_filters( 'dpdfg_custom_skins', $defaults ) );
	}

	/**
	 * Get all registered thumbnail sizes
	 *
	 * @return array
	 * @global array $_wp_additional_image_sizes
	 * @since 1.1.2
	 */
	public static function get_registered_thumbnail_sizes() {
		$options['dfg_full'] = __( 'Original Image Uploaded', 'dpdfg-dp-divi-filtergrid' );
		global $_wp_additional_image_sizes;
		foreach ( get_intermediate_image_sizes() as $_size ) {
			$size_width  = '';
			$size_height = '';
			if ( in_array(
				$_size,
				array(
					'thumbnail',
					'medium',
					'medium_large',
					'large',
				),
				true
			) ) {
				$size_width  = get_option( "{$_size}_size_w" );
				$size_height = get_option( "{$_size}_size_h" );
			} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
				$size_width  = $_wp_additional_image_sizes[ $_size ]['width'];
				$size_height = $_wp_additional_image_sizes[ $_size ]['height'];
			}
			$size_key             = $size_width . 'x' . $size_height;
			$size_label           = $_size . ' - (' . $size_width . 'x' . $size_height . ')';
			$options[ $size_key ] = $size_label;
		}

		return $options;
	}

	/**
	 * Enqueue styles and scripts for the VB custom interface
	 */
	public static function enqueue_and_localize_cpt_modal_script() {
		wp_enqueue_style( 'dp-dfg-admin-cpt-modal', DPDFG_URL . 'admin/css/dp-dfg-admin.min.css', array(), DPDFG_VERSION, 'all' );
		wp_enqueue_script( 'dp-dfg-admin-cpt-modal', DPDFG_URL . 'admin/js/dp-dfg-admin.min.js', array(
			'jquery',
			'jquery-ui-sortable',
			'jquery-ui-autocomplete'
		), DPDFG_VERSION, true );
		wp_localize_script(
			'dp-dfg-admin-cpt-modal',
			'dpdfg_admin',
			array(
				'ajaxurl'      => admin_url( 'admin-ajax.php' ),
				'nonce_cpt'    => wp_create_nonce( 'dpdfg_get_cpt_action' ),
				'nonce_tax'    => wp_create_nonce( 'dpdfg_get_taxonomies_action' ),
				'nonce_terms'  => wp_create_nonce( 'dpdfg_get_taxonomies_terms_action' ),
				'nonce_cf'     => wp_create_nonce( 'dpdfg_get_custom_fields_action' ),
				'nonce_ml_tax' => wp_create_nonce( 'dpdfg_get_multilevel_tax_action' ),
				'i18n'         => array(
					'ml_tax_level_label' => __( 'Label text for', 'dpdfg-dp-divi-filtergrid' ),
					'ml_tax_level_all'   => __( 'All filter text for', 'dpdfg-dp-divi-filtergrid' ),
				)
			)
		);
	}

	/**
	 * Register our custom action to request the builder function on ajax.
	 *
	 * @param $actions
	 *
	 * @return array
	 * @since    1.0.0
	 */
	public function add_our_custom_action( $actions ) {
		return array_merge( $actions, array( 'dpdfg_get_posts_data_action' ) );
	}

	/**
	 * Get first video
	 *
	 * @param $id
	 *
	 * @return false|mixed|string
	 */
	public static function get_first_video( $id, $props ) {
		$content   = get_the_content( null, false, $id );
		$video     = '';
		$video_url = '';
		preg_match_all( '|^\s*https?://[^\s"]+\s*$|im', $content, $urls );
		foreach ( $urls[0] as $url ) {
			$oembed = wp_oembed_get( esc_url( $url ) );
			if ( ! $oembed ) {
				continue;
			}
			$video     = $oembed;
			$video_url = $url;
			break;
		}
		// Look for video modules
		if ( isset( $props['video_module'] ) && 'on' === $props['video_module'] && empty( $video ) ) {
			$video_module = '';
			preg_match( '/et_pb_video.*?]/', $content, $video_module );
			if ( ! empty( $video_module[0] ) ) {
				preg_match( '/http.*?"/', $video_module[0], $src );
				if ( ! empty( $src[0] ) ) {
					$url    = esc_url( $src[0] );
					$oembed = wp_oembed_get( $url );
					if ( $oembed ) {
						$video = $oembed;
					} else {
						$video = do_shortcode( sprintf( '[video src="%1$s"]', $url ) );
					}
					$video_url = $url;
				}
			}
			// Look for gutenberg video
			if ( empty( $video_url ) ) {
				preg_match( '/<!-- wp:video[^\]]+?class="wp-block-video"><video[^\]]+?src="([^\]]+?)"[^\]]+?<!-- \/wp:video -->/', $content, $gb_video );
				if ( isset( $gb_video[1] ) ) {
					$video_url = $gb_video[1];
					$video     = do_shortcode( sprintf( '[video src="%1$s"]', $video_url ) );
				}
			}
		}
		// Look for video shortcode
		if ( empty( $video_url ) && has_shortcode( $content, 'video' ) ) {
			preg_match( "/\[video.*\[\/video]/s", $content, $match );
			$content = preg_replace( '/width="[0-9]*"/', '', $match[0] );
			$video   = preg_replace( '/height="[0-9]*"/', '', $content );
			preg_match( '/".*"/s', $video, $match );
			if ( isset( $match[0] ) ) {
				$video_url = str_replace( '"', '', $match[0] );
			}
			$video = do_shortcode( $video );
		}

		return ( ! empty( $video ) ) ? array(
			'output' => $video,
			'url'    => $video_url
		) : false;
	}

	/**
	 * @param array $terms
	 * @param array $into
	 * @param int $parent_id
	 */
	public static function sort_terms_hierarchically( array &$terms, array &$into, $parent_id = 0 ) {
		foreach ( $terms as $i => $term ) {
			if ( $term->parent == $parent_id ) {
				$into[ $term->term_id ] = $term;
				unset( $terms[ $i ] );
			}
		}
		foreach ( $into as $top_term ) {
			$top_term->children = array();
			self::sort_terms_hierarchically( $terms, $top_term->children, $top_term->term_id );
		}
	}

	/**
	 * @param $terms
	 * @param $ordered_terms
	 * @param array $indexed_terms
	 */
	public static function get_terms_in_hierarchical_order( $terms, &$ordered_terms, $indexed_terms = array() ) {
		foreach ( $terms as $term ) {
			if ( $term->parent ) {
				$term->level = $indexed_terms[ $term->parent ]->level + 1;
			} else {
				$term->level = 0;
			}
			$ordered_terms[]                 = $term;
			$indexed_terms[ $term->term_id ] = $term;
			if ( ! empty( $term->children ) ) {
				self::get_terms_in_hierarchical_order( $term->children, $ordered_terms, $indexed_terms );
			}
		}
	}

	/**
	 * Ajax add products to cart
	 *
	 * @since    1.0.0
	 */
	public static function ajax_add_to_cart() {
		$post              = wp_unslash( $_POST );
		$product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $post['product_id'] ) );
		$quantity          = empty( $post['qty'] ) ? 1 : wc_stock_amount( $post['qty'] );
		$variation_id      = empty( $post['variation_id'] ) ? 0 : intval( $post['variation_id'] );
		$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
		$product_status    = get_post_status( $product_id );
		if ( $passed_validation && WC()->cart->add_to_cart( $product_id, $quantity, $variation_id ) && 'publish' === $product_status ) {
			do_action( 'woocommerce_ajax_added_to_cart', $product_id );
			$data = array(
				'error'   => false,
				'message' => sprintf( '<div class="dp-dfg-cart-ajax-message dfg-success">%1$s</div>', wc_add_to_cart_message( array( $product_id => $quantity ), true, true ) )
			);
		} else {
			$data = array(
				'error'   => true,
				'message' => sprintf( '<div class="dp-dfg-cart-ajax-message dfg-error">%1$s</div>', __( 'And error happen when trying to add this product to the cart.', 'dpdfg-dp-divi-filtergrid' ) )
			);
		}
		wp_send_json( $data );
	}
}
