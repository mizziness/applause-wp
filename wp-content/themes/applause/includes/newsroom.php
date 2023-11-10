<?php

// Customize the Newsroom query to spit out only the results we want.
// ===============================================================================
function press_dfg_custom_query_function($query, $props)
{

  if (isset($props['admin_label']) && $props['admin_label'] === 'All Press Releases') {

    $query['post_type'] = 'press_releases';
    $query['post_number'] = -1;
    $query['posts_per_page'] = -1;
    $query['order'] = 'DESC';
    $query['orderby'] = 'date';

  }

  return $query;
}
add_filter('dpdfg_custom_query_args', 'press_dfg_custom_query_function', 10, 2);

// Update the press urls to include the /press/ slug 
// ===============================================================================
function press_update_post_link($post_link, $id = 0)
{
  $post = get_post($id);
  if (is_object($post) && $post->post_type == 'press_releases') {
    return home_url('/press/' . $post->post_name);
  }
  return $post_link;
}
add_filter('post_link', 'press_update_post_link', 1, 3);