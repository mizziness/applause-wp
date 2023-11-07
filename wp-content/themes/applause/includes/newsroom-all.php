<?php

/**
 * Customize the Newsroom query to spit out only the results we want.
 *
 * @param [array] $query
 * @param [array] $props
 * @return array
 */
function press_dfg_custom_query_function($query, $props)
{

    if (isset($props['admin_label']) && $props['admin_label'] === 'All Press Releases') {
            
            $query['post_type']         = 'press_releases';
            $query['post_number']       = -1;
            $query['posts_per_page']    = -1;
            $query['order']             = 'DESC';
            $query['orderby']           = 'date';

    }

    return $query;
}
add_filter('dpdfg_custom_query_args', 'press_dfg_custom_query_function', 10, 2);