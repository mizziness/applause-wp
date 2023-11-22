<?php

global $postTypes;
$postTypes = [ 'case_studies', 'ebooks', 'reports', 'webinars', 'whitepapers' ];

global $clockSVG;
$clockSVG = '<svg width="12px" height="12px" viewBox="0 0 12 12" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
        <g id="Responsive" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
            <g id="DL/Blog/Index" transform="translate(-830.000000, -407.000000)" fill="#606060">
                <g id="Featured" transform="translate(250.000000, 113.000000)">
                    <g id="blog/featured/large" transform="translate(0.000000, 42.000000)">
                        <g id="Time" transform="translate(580.000000, 250.000000)">
                            <g id="Group" transform="translate(0.000000, 2.000000)">
                                <path d="M6,12 C2.6862915,12 0,9.3137085 0,6 C0,2.6862915 2.6862915,0 6,0 C9.3137085,0 12,2.6862915 12,6 C11.9960327,9.31206378 9.31206378,11.9960327 6,12 Z M6,1.8 C3.68040405,1.8 1.8,3.68040405 1.8,6 C1.8,8.31959595 3.68040405,10.2 6,10.2 C8.31959595,10.2 10.2,8.31959595 10.2,6 C10.2,4.88609074 9.75750127,3.81780431 8.96984848,3.03015152 C8.18219569,2.24249873 7.11390926,1.8 6,1.8 Z" id="Shape"></path>
                                <path d="M7.5679173,8.25 C7.43319548,8.25013455 7.30146739,8.20628345 7.18941511,8.1239997 L5.55323851,6.92399977 C5.36356986,6.78497092 5.24974048,6.55073406 5.25,6.29999981 L5.25,4.49999993 C5.25,4.0857864 5.55522596,3.75 5.9317407,3.75 C6.30825543,3.75 6.61348094,4.0857864 6.61348095,4.49999993 L6.61348095,5.89919984 L7.94641949,6.87599978 C8.19640445,7.05937898 8.30778733,7.40118381 8.22064397,7.71752274 C8.1335006,8.03386167 7.86837994,8.25014001 7.5679173,8.25 L7.5679173,8.25 Z" id="Path"></path>
                            </g>
                        </g>
                    </g>
                </g>
            </g>
        </g>
    </svg>';

function defaultButtonText($post_type) 
{
    $text = "";
    switch ($post_type) {
        case 'case_studies' || 'ebooks' || 'reports' || 'whitepapers':
            $text = __("Read Now", 'applause');
            break;

        case 'videos' || 'podcasts' || 'webinars' || 'events':
            $text = __("Watch Now", 'applause');
            break;
    }

    return $text;
}

function dp_dfg_custom_resources($query, $props)
{
    if ( !isset($props['admin_label'])) {
        return $query;
    }

    $admin_label = $props['admin_label'];

    // All Resources
    // ===================================================
    if ( $admin_label === 'Featured Resources' || $admin_label === 'Recent Resources' || $admin_label === 'Popular Resources' ) {
        // Queries to support the Resources main page.
        $query['cache_results']         = true;
        $query['post_type']             = ['case_studies', 'ebooks', 'reports', 'webinars', 'whitepapers'];
        $query['posts_per_page']        = 2;
        $query['order']                 = 'DESC';
        $query['orderby']               = 'post_date';
        $query['meta_query']            = array(
            'relation'  => 'AND',
            array(
                'key' => 'visibility',
                'value' => 1,
                'compare' => '='
            ),
            array(
                'key' => 'is_featured',
                'value' => 1,
                'compare' => '='
            )  
        );
    }

    if ($admin_label === 'Recent Resources') {
        $query['posts_per_page']        = 4;
        $query['offset']                = 2; 
    }

    if ($admin_label === 'Popular Resources') {
        $query['posts_per_page']        = 4;
        $query['offset']                = 6;
    }

    if ($admin_label === 'All Resources') {
        $active = $props['active_filter'] ?? '';

        $query['cache_results']         = true;
        $query['posts_per_page']        = 12;
        $query['post_type']             = ['case_studies', 'ebooks', 'reports', 'webinars', 'whitepapers'];
        $query['order']                 = 'DESC';
        $query['orderby']               = 'post_date';
        $query['tax_query']             = false;
        $query['meta_query']            = array(
            array(
                'key' => 'visibility',
                'value' => 1,
                'compare' => '='
            ), 
        );

        if ( $active == '' || $active == 'all') {
            $query['post_type']         = ['case_studies', 'ebooks', 'reports', 'webinars', 'whitepapers'];
        } else {
            $query['post_type']         = [ $active ];
        }
    }

    // Webinars
    // ===================================================
    if ( $admin_label === 'Featured Webinars' || $admin_label === 'Recent Webinars' || $admin_label === 'Popular Webinars' ) {
        // Queries to support the Resources main page.
        $query['cache_results']         = true;
        $query['post_type']             = ['webinars'];
        $query['posts_per_page']        = 2;
        $query['order']                 = 'DESC';
        $query['orderby']               = 'post_date';
        $query['meta_query']            = array(
            'relation'  => 'AND',
            array(
                'key' => 'visibility',
                'value' => 1,
                'compare' => '='
            ),
            array(
                'key' => 'is_featured',
                'value' => 1,
                'compare' => '='
            )  
        );
    }

    if ($admin_label === 'Recent Webinars') {
        $query['posts_per_page']        = 4;
        $query['offset']                = 2; 
    }

    if ($admin_label === 'Popular Webinars') {
        $query['posts_per_page']        = 4;
        $query['offset']                = 6;
    }

    // Events 
    // =====================================================
    if ( $admin_label === 'Product Sessions Live' ) {
        $query['cache_results']         = true;
        $query['post_type']             = ['events'];
        $query['posts_per_page']        = -1;
        $query['order']                 = 'DESC';
        $query['orderby']               = 'post_date';
        $query['category_name']         = 'product-sessions-live';
    }

    return $query;
}
add_filter('dpdfg_custom_query_args', 'dp_dfg_custom_resources', 10, 2);

function resources_custom_headers($title_output, $props, $post_id) 
{
    // Resource Pages 
    // ===============================================================
    if (
        (
            $props['admin_label'] === 'Featured Resources' ||
            $props['admin_label'] === 'Recent Resources' ||
            $props['admin_label'] === 'Popular Resources' ||
            $props['admin_label'] === 'All Resources'
        )
    ) {
        $post = get_post($post_id);
        $imageSrc = get_field('resource_image_collage', $post_id) ?? "placeholder.jpg";

        $featuredText = $props['admin_label'] === 'Featured Resources' ? 'Featured ' : '';
        $label = $featuredText . (get_post_type_object($post->post_type)->labels->singular_name);

        $imageTag = '<img src="' . $imageSrc . '" class="lazyload" alt="' . get_the_title() . '">';
        $labelTag = '<div class="tag-label"><span>' . $label . '</span></div>';
        $title_output = $imageTag . $labelTag . $title_output;
    }
    
    // Webinars Pages 
    // ===============================================================
    if (
        (
            $props['admin_label'] === 'Featured Webinars' ||
            $props['admin_label'] === 'Recent Webinars' ||
            $props['admin_label'] === 'Popular Webinars'
        )
    ) {
        $post = get_post($post_id);
        $imageSrc = get_field('resource_image_collage', $post_id) ?? "placeholder.jpg";

        $featuredText = $props['admin_label'] === 'Featured Webinars' ? 'Featured ' : '';
        $label = $featuredText . (get_post_type_object($post->post_type)->labels->singular_name);

        $imageTag = '<img src="' . $imageSrc . '" class="lazyload" alt="' . get_the_title() . '">';
        $labelTag = '<div class="tag-label"><span>' . $label . '</span></div>';
        $title_output = $imageTag . $labelTag . $title_output;
    }

    // Events 
    // =====================================================
    if ( $props['admin_label'] === 'Product Sessions Live' ) {
        $post = get_post($post_id);
        $attachmentID = get_field('event_image', $post_id) ?? null;
        $url = wp_get_attachment_image_url($attachmentID);
        
        $label = get_field("event_type", $post_id);
        $imageTag = '<img src="' . $url . '" class="lazyload" alt="' . get_the_title() . '">';
        $labelTag = '<div class="tag-label"><span>' . $label . '</span></div>';
        $title_output = $imageTag . $labelTag . $title_output;
    }
    
    return $title_output;
}
add_filter('dpdfg_custom_header', 'resources_custom_headers', 10, 3);

function featured_resources_custom_content($excerpt, $props)
{
    if (
        (
            $props['admin_label'] === 'Featured Resources' ||
            $props['admin_label'] === 'Recent Resources' ||
            $props['admin_label'] === 'Popular Resources' ||
            $props['admin_label'] === 'All Resources' ||
            $props['admin_label'] === 'Featured Webinars' ||
            $props['admin_label'] === 'Recent Webinars' ||
            $props['admin_label'] === 'Popular Webinars'
        )
    ) {
        $post = get_post(get_the_ID());
        $newContent = "<div class='inner-custom-content'>";
        if (has_excerpt()) {
            $newContent .= get_the_excerpt();
        } else {
            $newContent .= get_the_content();
        }
        $newContent .= '</div>';
        $buttonText = get_field('resourceButtonText', get_the_ID()) ?? defaultButtonText($post->post_type);
        $newContent .= '<div class="button-holder"><span class="button is-secondary" role="button">' . $buttonText . '</span></div>';
        $excerpt = $newContent;
    }

    // Events 
    // =====================================================
    if ($props['admin_label'] === 'Product Sessions Live') {
        $newContent = "<div class='inner-custom-content'>";
        $newContent .= get_field('event_location', get_the_ID());
        $newContent .= '</div>';
        $buttonText = get_field('event_button_text', get_the_ID()) ?? defaultButtonText($post->post_type);
        $newContent .= '<div class="button-holder"><span class="button is-primary" role="button">' . $buttonText . '</span></div>';
        $excerpt = $newContent;
    }

    return $excerpt;
}
add_filter('dpdfg_after_read_more', 'featured_resources_custom_content', 10, 2);

function dpdfg_default_post_types($default_post_type) 
{
    global $postTypes;
    
    $results = [];
    foreach ($postTypes as $type) {
        $results[$type] = get_post_type_object($type);
    }

    return $results;
}
add_filter('dpdfg_default_post_types', 'dpdfg_default_post_types');

function dpdfg_custom_filters($filters, $props) 
{
    if ( $props['admin_label'] === 'All Resources'  ) {
        $custom_filters = array(
            array(
                'id' => 'all',
                'name' => 'All'
            ),
            array(
                'id' => 'case_studies',
                'name' => 'Case Studies'
            ),
            array(
                'id' => 'ebooks',
                'name' => 'eBooks'
            ),
            array(
                'id' => 'reports',
                'name' => 'Reports'
            ),
            array(
                'id' => 'webinars',
                'name' => 'Webinars'
            ),
            array(
                'id' => 'whitepapers',
                'name' => 'Whitepapers'
            ),
        );
    }
    return array($custom_filters);
}
add_filter('dpdfg_custom_filters', 'dpdfg_custom_filters', 10, 2);

function applause_custom_meta($meta_output, $props, $post_id) {

    // Events 
    // =====================================================
    if ( $props['admin_label'] === 'All Podcasts' ) {
        global $clockSVG;

        $post = get_post($post_id);
        $newDate = date('M d, Y', strtotime($post->post_date));

        $newContent = "";
        $newContent .= '<div class="extended-meta datetime="' . $newDate. '">';
        $newContent .= 'Ep. ' . get_field("episode_number", $post_id) . ' // ' . $newDate;
        $newContent .= '</div>';

        $newContent .= '<div class="read-time"><span class="icon icon-read-time">' . $clockSVG . '</span>';
        $newContent .= get_field("episode_length", $post_id) . __("min", 'applause');
        $newContent .= '</div>';
    }
    return $newContent;
}
add_filter('dpdfg_custom_meta', 'applause_custom_meta', 10, 3);
