<?php

/* Blog Author Functions
 * ==============================================================
 * These functions control aspects of the Blog Authors 
 * custom post type
 */

/**
 * Customize the Blogs on Author pages to only show posts by that author.
 *
 * @param [array] $query
 * @param [array] $props
 * @return array
 */
function dp_dfg_custom_query_function($query, $props)
{

    if (isset($props['admin_label']) && $props['admin_label'] === 'Blog Author Query') {
        $currentAuthorID = get_the_ID();
        $query['post_type']             = 'post';
        $query['post_number']           = 8;
        $query['posts_per_page']        = 8;
        $query['meta_key']              = 'blog_author_ids';
        $query['meta_value']            = strval($currentAuthorID);
        $query['meta_compare']          = 'LIKE';
    }

    if (isset($props['admin_label']) && $props['admin_label'] === 'Featured Blog') {
        $query['post_type']             = 'post';
        $query['post_number']           = 1;
        $query['posts_per_page']        = 1;
        $query['meta_key']              = 'is_featured';
        $query['meta_value']            = 1;
        $query['meta_compare']          = '=';
    }

    if (isset($props['admin_label']) && $props['admin_label'] === 'Featured Blogs Query') {
        $query['post_type']             = 'post';
        $query['post_number']           = 3;
        $query['posts_per_page']        = 3;
        $query['offset']                = 1;
        $query['meta_key']              = 'is_featured';
        $query['meta_value']            = 1;
        $query['meta_compare']          = '=';
    }

    return $query;
}
add_filter('dpdfg_custom_query_args', 'dp_dfg_custom_query_function', 10, 2);

/**
 * Customize the Meta section
 *
 * @param [type] $meta_output
 * @param [type] $props
 * @param [type] $post_id
 * @return void
 */
function dpdfg_custom_meta_function($meta_output, $props, $post_id)
{
    $categories = get_the_category($post_id);
    $filteredCategories = array();

    foreach ($categories as $category) {

        if ($category->slug != "applause" && $category->slug != "blog-categories" && $category->slug != "blog-topics-new") {
            $filteredCategories[] = $category;
        }
    }

    $firstCategory = $filteredCategories[0];
    if (isset($firstCategory)) {
        $meta_output = '<a href="' . get_category_link($firstCategory->term_id) . '" title="' . esc_attr(sprintf(__("View all posts in %s"), $firstCategory->name)) . '">' . $firstCategory->cat_name . '</a>';
    }

    return $meta_output;
}
add_filter('dpdfg_custom_meta', 'dpdfg_custom_meta_function', 10, 3);

/**
 * Fix Permaink Structure
 *
 * @param [type] $post_link
 * @param [type] $post
 * @return void
 */
function blog_author_permalinks($post_link, $post = null)
{
    if ($post->post_type === 'blog-author') {
        $post_link = home_url('blog/author/' . $post->post_name . '/');
    }

    if ($post->post_type == 'post' && (in_category('blog-topics-new') || in_category('blog-categories')) ) {
      $post_link = home_url('blog/' . $post->post_name . '/');
    }

    return $post_link;
}
add_filter('post_type_link', 'blog_author_permalinks', 1, 3);

/**
 * Add New Rewrite Rule
 */
function blog_author_rewrites_init()
{
    add_rewrite_rule('blog/([^/]*)/?$', 'index.php?post_type=post&name=$matches[1]', 'top');
    add_rewrite_rule('blog/author/([^/]*)/?$', 'index.php?post_type=blog-author&name=$matches[1]', 'top');
    
}
add_action('init', 'blog_author_rewrites_init');
