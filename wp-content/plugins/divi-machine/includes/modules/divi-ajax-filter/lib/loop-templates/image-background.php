<?php

if ( ! defined( 'ABSPATH' ) ) exit;

// get the post ID
$post_id = get_the_ID();
// get the post link
$post_link = get_permalink( $post_id );
// get the post title
$post_title = get_the_title( $post_id );
// get the post author
$post_author = get_the_author();
// get the post author link
$post_author_link = get_author_posts_url( get_the_author_meta( 'ID' ) );
// get the post date
$post_date = get_the_date();
// get the post categories
$post_categories = get_the_category_list( ', ' );
// get the post tags
$post_tags = get_the_tag_list( '', ', ' );
// get the post thumbnail
$post_thumbnail = get_the_post_thumbnail( $post_id, 'full' );
// get the post thumbnail URL
$post_thumbnail_url = get_the_post_thumbnail_url( $post_id, 'full' );
// get the post thumbnail src
$post_thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' );

// if the post thumbnail src is not empty
if ( ! empty( $post_thumbnail_src ) ) {
    // get the post thumbnail src
    $post_thumbnail_src = $post_thumbnail_src[0];
} else {
    // set the post thumbnail src to empty
    $post_thumbnail_src = '';
}

// get the post thumbnail srcset
$post_thumbnail_srcset = wp_get_attachment_image_srcset( get_post_thumbnail_id( $post_id ), 'full' );
// get post thumbnail sizes
$post_thumbnail_sizes = wp_get_attachment_image_sizes( get_post_thumbnail_id( $post_id ), 'full' );
// get thumbnail alt
$thumbnail_alt = get_post_meta( get_post_thumbnail_id( $post_id ), '_wp_attachment_image_alt', true );

// if the post has a thumbnail set a variable to use as a background image on the article
if ( has_post_thumbnail( $post_id ) ) {
    $background_image = ' style="background-image: url(' . $post_thumbnail_src . ');background-size: cover;background-position: center center;background-repeat: no-repeat;"';
} else {
    $background_image = '';
}

?>
<article id="<?php echo $post_id; ?>" <?php post_class( 'et_pb_post clearfix grid-item'); ?>>
<div class="grid-item-cont">

    <div class="image_bg_content" <?php echo $background_image ?>>
    </div>
    <div class="post-content_cont">
        <div class="post-content-inner">
            <h2 class="entry-title">
                <a href="<?php echo esc_attr($post_link); ?>"><?php echo esc_html($post_title);?></a>
            </h2>
            
            <?php 
            if ($show_author == 'on') {
                $author = et_get_safe_localization( sprintf( __( 'by %s', 'et_builder' ), '<span class="author vcard">' . et_pb_get_the_author_posts_link() . '</span>' ) );
                $author_separator =' | ';
            } else {
                $author = '';
                $author_separator = '';
            }
            
            if ($show_date == 'on') {
                // phpcs:disable WordPress.WP.I18n.NoEmptyStrings -- intentionally used.
                $date = et_get_safe_localization( sprintf( __( '%s', 'et_builder' ), '<span class="published">' . esc_html( get_the_date( str_replace( '\\\\', '\\', $date_format ) ) ) . '</span>' ) );
                // phpcs:enable
                $date_separator = ' | ';
            } else {
                $date = '';
                $date_separator = '';
            }
            
            if ($show_categories == 'on') {
                $categories = et_builder_get_the_term_list( ', ' );
                $categories_separator = ' | ';
            } else {
                $categories = '';
                $categories_separator = '';
            }
            
            if ($show_comments == 'on') {
                $comments = et_core_maybe_convert_to_utf_8( sprintf( esc_html( _nx( '%s Comment', '%s Comments', get_comments_number(), 'number of comments', 'et_builder' ) ), number_format_i18n( get_comments_number() ) ) );
            } else {
                $comments = '';
            }
            
            printf(
                '<p class="post-meta">%1$s %2$s %3$s %4$s %5$s %6$s %7$s</p>',
                et_core_esc_previously( $author ),
                et_core_intentionally_unescaped( $author_separator, 'fixed_string' ),
                et_core_esc_previously( $date ),
                et_core_intentionally_unescaped( $date_separator, 'fixed_string' ),
                et_core_esc_wp( $categories ),
                et_core_intentionally_unescaped( $categories_separator, 'fixed_string' ),
                et_core_esc_previously( $comments )
            );
            ?>
            
            <?php if ($show_content !== 'none') { ?>
                <div class="post-content">
                    <div class="post-content-inner">
                        <?php 
                        if ($show_content == 'off') {
                            // get excerpt_length
                            $excerpt_length = $excerpt_length ? $excerpt_length : 55;
                            // get excerpt_more
                            $excerpt_more = $excerpt_more ? $excerpt_more : '...';
                            // get excerpt
                            $excerpt = get_the_excerpt();
                            // get excerpt
                            $excerpt = wp_trim_words( $excerpt, $excerpt_length, $excerpt_more );
                            // echo excerpt
                            echo $excerpt;
                        } else if ($show_content == 'on') {
                            echo the_content();
                        } else {
                            echo the_excerpt();
                        }
                        ?>
                    </div>
                    <?php
                    if ( $show_read_more == 'on' ) {
                        $more = sprintf( ' <a href="%1$s" class="more-link" >%2$s</a>', esc_url( get_permalink() ), esc_html__( 'read more', 'et_builder' ) ); //phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited
                        echo et_core_esc_previously( $more );
                    }
                    ?>
                </div>	
            <?php } ?>	
        </div>
    </div>
</div>
</article>
<?php 

