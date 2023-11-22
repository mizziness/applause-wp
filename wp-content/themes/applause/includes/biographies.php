<?php 

    function applause_biography_query($query, $props)
    {
        if ( !isset($props['admin_label'])) {
            return $query;
        }

        $admin_label = $props['admin_label'];

        // Executives
        // ===================================================
        if ( $admin_label === 'Executives' || $admin_label === 'Board Members' ) {
            // Queries to support the Resources main page.
            $query['cache_results']         = true;
            $query['post_type']             = ['biographies'];
            $query['posts_per_page']        = -1;
            $query['order']                 = 'ASC';
            $query['orderby']               = 'menu_order';
            
            if ( $admin_label == "Executives" ) {
                $query['meta_query']            = array(
                    'relation'  => 'AND',
                    array(
                        'key' => 'executive_type',
                        'value' => 17,
                        'compare' => '='
                    ), 
                );
            }

            if ( $admin_label == "Board Members" ) {
                $query['meta_query']            = array(
                    'relation'  => 'AND',
                    array(
                        'key' => 'executive_type',
                        'value' => 89,
                        'compare' => '='
                    ), 
                );
            }
        }

        return $query;
    }
    add_filter('dpdfg_custom_query_args', 'applause_biography_query', 10, 2);