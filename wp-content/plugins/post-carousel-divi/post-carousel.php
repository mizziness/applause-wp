<?php
/*
Plugin Name: Post Carousel Divi
Plugin URI:  https://www.learnhowwp.com/divi-post-carousel
Description: Adds a Post Carousle module to the Divi builder.
Version:     1.2
Author:      Learnhowwp.com
Author URI:  https://www.learnhowwp.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: lwp-divi-module
Domain Path: /languages

Post Carousel is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Post Carousel is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Post Carousel. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/

//==================================ET MARKETPLACE======================================
//======================================================================================

if ( ! function_exists( 'lwp_pcdivi_fs' ) ) {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-lwp-pc-freemius-alt.php';
    function lwp_pcdivi_fs() {
        return new Lwp_Pc_Freemius_Alt;
    }
}

//======================================================================================
//======================================================================================

if ( ! function_exists( 'lwp_initialize_post_carousel_extension' ) ):
/**
 * Creates the extension's main class instance.
 *
 * @since 1.0.0
 */
function lwp_initialize_post_carousel_extension() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/PostCarousel.php';
}
add_action( 'divi_extensions_init', 'lwp_initialize_post_carousel_extension' );
endif;

if ( ! function_exists( 'lwp_post_carousel_style' ) ):
function lwp_post_carousel_style($carousel_style,$post_title_output,$post_meta_output,$post_excerpt_output,$button_output,$featured_image_src,$post_permalink,$has_featured_image,$carousel_image_position,$featured_image_url){

	$output='';
	$post_thumbnail_output ='';

	if($has_featured_image){

		$post_thumbnail_output = 
		'<div class="lwp_post_carousel_image">
			<a class="lwp_carousel_featured_image" href="'.$post_permalink.'">
			'.$featured_image_src.'
			</a>
		</div>';

	}	

	if($carousel_style=='default'){

		$output = 
		'<div class="lwp_post_carousel_item">
			<div class="lwp_post_carousel_item_inner lwp_carousel_default">'.
				$post_thumbnail_output
				.$post_title_output
				.$post_meta_output
				.$post_excerpt_output
				.'<div class="lwp_post_carousel_read_more">
					'.$button_output.'
				</div>
			</div>
		</div>';
	}

	else if($carousel_style=='side'){

		$position_class = 'lwp_image_position_'.$carousel_image_position;
		$post_thumbnail_output_side ='';

		if($has_featured_image)
			$post_thumbnail_output_side = '
				<div class="lwp_image_side">'.
					$post_thumbnail_output
				.'</div>';

		$output = 
		'<div class="lwp_post_carousel_item">
			<div class="lwp_post_carousel_item_inner lwp_carousel_side '.$position_class.'">'.
				$post_thumbnail_output_side.
				'<div class="lwp_content_side">'	
					.$post_title_output
					.$post_meta_output
					.$post_excerpt_output
					.'<div class="lwp_post_carousel_read_more">
						'.$button_output.'
					</div>
				</div>	
			</div>
		</div>';		

	}

	else if($carousel_style=='overlay' || $carousel_style=='hover'){

		$featured_image_style ='';
		$hover_class='';
		$featured_image_class ='';

		if($has_featured_image){
			$featured_image_style = 'style="background-image:url('.$featured_image_url.');"';
			$featured_image_class = 'lwp_has_featured_image';
		}
		if($carousel_style == 'hover')
			$hover_class = 'lwp_carousel_hover';

		$output = 
		'<div class="lwp_post_carousel_item">
			<div class="lwp_post_carousel_item_inner lwp_carousel_overlay '.$hover_class.' '.$featured_image_class.'" '.$featured_image_style.'>
				<div class="lwp_overlay_container"></div>'.
				'<div class="lwp_content_overlay">'	
					.$post_title_output
					.$post_meta_output
					.$post_excerpt_output
					.'<div class="lwp_post_carousel_read_more">
						'.$button_output.'
					</div>
				</div>	
			</div>
		</div>';

	}

	else if($carousel_style=='overlay_box'){

		$featured_image_style ='';
		$featured_image_class ='';

		if($has_featured_image){
			$featured_image_style = 'style="background-image:url('.$featured_image_url.');"';
			$featured_image_class = 'lwp_has_featured_image';
		}

		$output = 
		'<div class="lwp_post_carousel_item">
			<div class="lwp_post_carousel_item_inner lwp_carousel_overlay_box '.$featured_image_class.'" '.$featured_image_style.'>'.
				'<div class="lwp_content_overlay">'	
					.$post_title_output
					.$post_meta_output
					.$post_excerpt_output
					.'<div class="lwp_post_carousel_read_more">
						'.$button_output.'
					</div>
				</div>	
			</div>
		</div>';

	}
	
	else if($carousel_style=='overlap_content'){

		$post_thumbnail_output_overlap ='';
		$featured_image_class ='';

		if($has_featured_image){

			$featured_image_class='lwp_has_featured_image';

			$post_thumbnail_output_overlap = '
				<div class="lwp_image_overlap">'.
					$post_thumbnail_output
				.'</div>';
		}				

		$output = 
		'<div class="lwp_post_carousel_item">
			<div class="lwp_post_carousel_item_inner lwp_carousel_overlap '.$featured_image_class.'">'
				.$post_thumbnail_output_overlap
				.'<div class="lwp_overlap_content_outer">
					<div class="lwp_overlap_content">'
						.$post_title_output
						.$post_meta_output
						.$post_excerpt_output
						.'
					</div>
					<div class="lwp_overlap_button">
						<div class="lwp_post_carousel_read_more">
						'.$button_output.'
						</div>				
					</div>
				</div>
			</div>
		</div>';

	}	


	return $output;

}
endif;


if ( ! function_exists( 'lwp_get_carousel_posts' ) ):

add_action( 'wp_ajax_lwp_get_carousel_posts', 'lwp_get_carousel_posts' );

function lwp_get_carousel_posts(){

	if ( isset( $_POST['et_admin_load_nonce_'] ) && ! wp_verify_nonce( sanitize_key( $_POST['et_admin_load_nonce_'] ), 'et_admin_load_nonce' ) ) {
		die( 'Nonce verification failed.' ); 
	}	
	
	/*Post settings*/
	$post_title_level 			= 'h4';
	$post_count 				= 9;
	$featured_image_size		= '';
	$post_categories			= array();
	$use_manual_excerpt			= 'off';


	if(isset($_POST['post_count']) && !empty($_POST['post_count']))
		$post_count = sanitize_option('posts_per_page',$_POST['post_count']);

	if(isset($_POST['featured_image_size']) && !empty($_POST['featured_image_size']))	
		$featured_image_size = sanitize_text_field($_POST['featured_image_size']);

	if(isset($_POST['post_categories']) && !empty($_POST['post_categories']))	
		$post_categories = wp_parse_id_list($_POST['post_categories']);

	if(isset($_POST['use_manual_excerpt']) && !empty($_POST['use_manual_excerpt']))	
		$use_manual_excerpt = sanitize_text_field($_POST['use_manual_excerpt']);		
	
	$order 							= 'DESC';
	$orderby 						= 'date';
	$post_offset					= 0;
	$show_title						= 'on';
	$show_featured_image			= 'on';
	$show_excerpt					= 'on';
	$show_author					= 'on';
	$show_date						= 'on';
	$show_categories				= 'on';		
	$show_comments					= 'on';		
	$show_button					= 'on';
	$post_meta_separator			= '|';
	$excerpt_length					= 170;
	$button_text					= 'Read More';
	$date_format					='M j, Y';
	$carousel_style					= 'default';
	$carousel_image_position 		= 'left';	

	if(lwp_pcdivi_fs()->is__premium_only()){
		/*Setting paid plan*/
		if(isset($_POST['order']) && !empty($_POST['order']))	
			$order = sanitize_text_field($_POST['order']);
		
		if(isset($_POST['orderby']) && !empty($_POST['orderby']))	
			$orderby = sanitize_text_field($_POST['orderby']);
		
		if(isset($_POST['post_categories']) && !empty($_POST['post_categories']))	
			$post_categories = wp_parse_id_list($_POST['post_categories']);
		
		if(isset($_POST['post_offset']) && !empty($_POST['post_offset']))		
			$post_offset = absint($_POST['post_offset']);
		
		if(isset($_POST['show_title']) && !empty($_POST['show_title']))	
			$show_title = sanitize_text_field($_POST['show_title']);

		if(isset($_POST['show_featured_image']) && !empty($_POST['show_featured_image']))		
			$show_featured_image = sanitize_text_field($_POST['show_featured_image']);

		if(isset($_POST['show_excerpt']) && !empty($_POST['show_excerpt']))	
			$show_excerpt = sanitize_text_field($_POST['show_excerpt']);

		if(isset($_POST['show_author']) && !empty($_POST['show_author']))	
			$show_author = sanitize_text_field($_POST['show_author']);

		if(isset($_POST['show_date']) && !empty($_POST['show_date']))		
			$show_date = sanitize_text_field($_POST['show_date']);
		
		if(isset($_POST['show_categories']) && !empty($_POST['show_categories']))		
			$show_categories = sanitize_text_field($_POST['show_categories']);		

		if(isset($_POST['show_comments']) && !empty($_POST['show_comments']))		
			$show_comments = sanitize_text_field($_POST['show_comments']);		

		if(isset($_POST['show_button']) && !empty($_POST['show_button']))		
			$show_button = sanitize_text_field($_POST['show_button']);
		
		$post_meta_separator			= '|';
		
		if(isset($_POST['excerpt_length']) && !empty($_POST['excerpt_length']))	
			$excerpt_length = absint($_POST['excerpt_length']);

		if(isset($_POST['button_text']) && !empty($_POST['button_text']))		
			$button_text = sanitize_text_field($_POST['button_text']);		

		if(isset($_POST['date_format']) && !empty($_POST['date_format']))		
			$date_format = sanitize_text_field($_POST['date_format']);		

		if(isset($_POST['carousel_style']) && !empty($_POST['carousel_style']))		
			$carousel_style = sanitize_text_field($_POST['carousel_style']);		

		if(isset($_POST['carousel_image_position']) && !empty($_POST['carousel_image_position']))		
			$carousel_image_position = sanitize_text_field($_POST['carousel_image_position']);		
	}
	

	$post_query = new WP_Query( 
		array (
			'post_type' 		=>	'post',
			'posts_per_page' 	=>	$post_count,
			'offset'			=> 	$post_offset,
			'cat'				=>	$post_categories,
			'post_status' 		=> 'publish',
			'order'   			=> $order,				
			'orderby' 			=> $orderby			
		) 
	);
	$post_output='';

	while ( $post_query->have_posts() ) {
		$post_query->the_post();

		$button_output	= '';

		if($show_button =='on'){
			$button_output=
			'<div class="et_pb_button_wrapper">
				<a class="et_pb_button" href="#">'.$button_text.'</a>
			</div>';
		}		

		$post_thumbnail_output ='';

		$has_featured_image 	= false;
		$post_permalink			= get_permalink();
		$featured_image_src		= get_the_post_thumbnail(get_the_ID(), $featured_image_size );
		$featured_image_url 	= wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), $featured_image_size );			

		if( has_post_thumbnail() && $show_featured_image=='on'){
			$has_featured_image =true;
		}

		$post_title_output='';

		if($show_title=='on'){
			$post_title_output 
			='<div class="lwp_post_carousel_title">
				<'.$post_title_level.' class="lwp_post_carousel_heading">
					<a class="lwp_post_title" href="'.get_permalink().'">' . 
					get_the_title()
					.'</a>
				</'.$post_title_level.'>
			</div>';
		}
		
		$post_excerpt_output ='';

			if($show_excerpt == 'on'){

				$excerpt_text='';

				if(has_excerpt() && $use_manual_excerpt =='on'){
					$excerpt_text = get_the_excerpt();
				}
				else{
					$excerpt_text = substr(wp_strip_all_tags(preg_replace("~(?:\[/?)[^/\]]+/?\]~s", '', get_the_content())),0,$excerpt_length);					
				}


				$post_excerpt_output =
				'<div class="lwp_post_carousel_excerpt">'.
					$excerpt_text
				.'</div>';
			}
		
		$post_meta_output 		= '';
		$post_meta_array		= array();
		$post_author_output 	= '';
		$post_date_output	 	= '';
		$post_category_output 	= '';
		$post_comment_output	= '';

		if($show_author == 'on'){
			$post_author_output = '<span class="lwp_meta_by">'.esc_html__("by","lwp-divi-module").'</span> '.get_the_author_posts_link();
			array_push($post_meta_array,$post_author_output);
		}

		if($show_date == 'on'){
			$post_date_output = '<span class="lwp_meta_date">'.get_the_time($date_format).'</span>';
			array_push($post_meta_array,$post_date_output);
		}

		if($show_categories == 'on'){
			$post_category_output = '<span class="lwp_meta_categories">'.get_the_category_list(',').'</span>';
			array_push($post_meta_array,$post_category_output);
		}			

		if($show_comments == 'on'){
			$post_comment_output = '<span class="lwp_meta_comments">'.get_comments_number_text(__("0 Comments","lwp-divi-module")).'</span>';
			array_push($post_meta_array,$post_comment_output);
		}			

		$post_meta_output	= $post_meta_output. '<p class="lwp_post_carousel_meta">';
		
		$meta_count = count($post_meta_array);	
		for ($i = 0; $i < $meta_count; $i++) {
			
			$post_meta_output = $post_meta_output.$post_meta_array[$i];
			
			if($meta_count == 1 || $i == $meta_count-1)
				continue;
			else
				$post_meta_output = $post_meta_output.' <span class="lwp_meta_separator">'.$post_meta_separator.'</span> ';
		}				

		$post_meta_output	= $post_meta_output.'</p>';		

		$post_output = $post_output. lwp_post_carousel_style($carousel_style,$post_title_output,$post_meta_output,$post_excerpt_output,$button_output,$featured_image_src,$post_permalink,$has_featured_image,$carousel_image_position,$featured_image_url[0]);
	}	

	wp_reset_postdata();

	$result = [
		'html'	=> $post_output
	];
	echo json_encode( $result );
	wp_die();
}

endif;