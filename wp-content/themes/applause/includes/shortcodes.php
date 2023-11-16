<?php

/**
 * Convert curly/smart quotes to regular quotes
 *
 * This function will convert Windows-1252, CP-1252, and other UTF-8 single and double quotes to regular quotes,
 * otherwise known as Unicode character U+0022 quotion mark (") and U+0027 apostrophe (') which typically do not have
 * any sort of encoding issues that the others run into.
 *
 * @param string $text The text that contains curly quotes
 * @return string Normalized text using regular quotes
 */
function convertCurlyQuotes($text): string
{
    $quoteMapping = [
        // U+0082⇒U+201A single low-9 quotation mark
        "\xC2\x82"     => "'",

        // U+0084⇒U+201E double low-9 quotation mark
        "\xC2\x84"     => '"',

        // U+008B⇒U+2039 single left-pointing angle quotation mark
        "\xC2\x8B"     => "'",

        // U+0091⇒U+2018 left single quotation mark
        "\xC2\x91"     => "'",

        // U+0092⇒U+2019 right single quotation mark
        "\xC2\x92"     => "'",

        // U+0093⇒U+201C left double quotation mark
        "\xC2\x93"     => '"',

        // U+0094⇒U+201D right double quotation mark
        "\xC2\x94"     => '"',

        // U+009B⇒U+203A single right-pointing angle quotation mark
        "\xC2\x9B"     => "'",

        // U+00AB left-pointing double angle quotation mark
        "\xC2\xAB"     => '"',

        // U+00BB right-pointing double angle quotation mark
        "\xC2\xBB"     => '"',

        // U+2018 left single quotation mark
        "\xE2\x80\x98" => "'",

        // U+2019 right single quotation mark
        "\xE2\x80\x99" => "'",

        // U+201A single low-9 quotation mark
        "\xE2\x80\x9A" => "'",

        // U+201B single high-reversed-9 quotation mark
        "\xE2\x80\x9B" => "'",

        // U+201C left double quotation mark
        "\xE2\x80\x9C" => '"',

        // U+201D right double quotation mark
        "\xE2\x80\x9D" => '"',

        // U+201E double low-9 quotation mark
        "\xE2\x80\x9E" => '"',

        // U+201F double high-reversed-9 quotation mark
        "\xE2\x80\x9F" => '"',

        // U+2039 single left-pointing angle quotation mark
        "\xE2\x80\xB9" => "'",

        // U+203A single right-pointing angle quotation mark
        "\xE2\x80\xBA" => "'",

        // HTML left double quote
        "&ldquo;"      => '"',

        // HTML right double quote
        "&rdquo;"      => '"',

        // HTML left sinqle quote
        "&lsquo;"      => "'",

        // HTML right single quote
        "&rsquo;"      => "'",
    ];

    return strtr(html_entity_decode($text, ENT_QUOTES, "UTF-8"), $quoteMapping);
}

// Custom Shortcode to display the author name on Blog Authors 
// ===============================================================================
function posttitle_func($atts)
{
    // Use like: [posttitle]
    return get_the_title();
}
add_shortcode('posttitle', 'posttitle_func');



// Custom Shortcode to display the location meta info under the subheading of a PR
// ===============================================================================
function pressLocation_func($atts)
{
    $post_id = get_the_ID();
    $other_location = get_field("other_location", $post_id) ?? "";
    $location = get_field("location", $post_id) ?? "";

    $locationText = !empty($other_location) ? $other_location : $location;
    $html = "<div class='press-release-meta meta-data'>" . $locationText . " &#8212; " . get_the_date('F d, Y') . "</div>";

    // Use like: [pressLocation]
    return $html;
}
add_shortcode('pressLocation', 'pressLocation_func');



// Custom Shortcode to insert a Wistia Play Video, Button or Preview
// ===============================================================================
function wistiaEmbed_func($atts)
{
    $a = shortcode_atts(
        array(
            'type'              => 'button',
            'wistia_id'         => '',
            'category'          => 'Page Navigation',
            'action'            => 'Button',
            'label'             => 'Watch Now',
            'icon'              => '►',
            'button_class'      => 'button is-secondary',
            'video_image'       => ''
        ), $atts);

    if ( empty($a['wistia_id'] )) {
        return '<strong> Error! Missing Wistia ID in the shortcode! </strong>';
    }

    $html = "";
    $html .= "<!--  Wistia JS API Button for {$a["wistia_id"]}  -->";
    $html .= "<script src='https://fast.wistia.com/embed/medias/{$a["wistia_id"]}.jsonp' async></script>";

    if ( $a["type"] == "button" ) {
        
        $html .= "<span id='wistia-id-{$a["wistia_id"]}' class='applause-button-wistia wistia_embed wistia_async_{$a["wistia_id"]} popover=true popoverContent=link' style='display:inline;position:relative'>";
        $html .= "<div class='{$a["button_class"]} click-track wistia-trigger' role='button' aria-label='{$a["label"]}' target='_self' data-category='{$a["category"]}' data-action='{$a["action"]}' data-label='{$a["label"]}' data-href='{$a["wistia_id"]}'>";
        $html .= "<span>{$a["icon"]} {$a["label"]}</span>";
        $html .= "</div></span>";

    } else {
        
        $html .= "<div id='wistia-embed-{$a["wistia_id"]}' class='applause-inline-wistia wistia_embed wistia_async_{$a["wistia_id"]} popover=true popoverContent=html'>";
        $html .= "<a class='{$a["button_class"]} click-track wistia-trigger' data-video='{$a["wistia_id"]}' target='_self' data-category='{$a["category"]}' data-action='{$a["action"]}' data-label='{$a["label"]}' data-href='{$a["wistia_id"]}'>";
            $html .= "<svg class='play-button' width='100%' height='100%' viewBox='0 0 80 80' fill='none' xmlns='http://www.w3.org/2000/svg'><circle class='play-bg' cx='40' cy='40' r='40' fill='white' fill-opacity='0.8'/><path class='play-icon' d='M56 40L31.8712 53.8564L31.8712 26.1436L56 40Z' fill='#0272B4'/></svg>";
            $html .= "<div class='video-image tw-overflow-hidden tw-rounded-lg'><img src='' data-src='{$a["video_image"]}' class='lazyload' /></div>";
        $html .= "</a></div>";

    }
    
    // Use like: [wistia_button wistia_id="xxxxxxx" label="Watch Now"]
    return $html;
}
add_shortcode('wistia_button', 'wistiaEmbed_func');

// Custom Shortcode to get Lat/Long from Locations for use on the Culture Map
// ===============================================================================
function officeLocations_func($atts)
{
    $args = [
        'post_type'         => 'office-locations',
        'posts-per-page'    => -1,
        'status'            => 'publish',
        'orderby'           => 'menu_order',
        'order'             => 'ASC',
        'suppress_filters'  => false
    ];

    $custom_fields = [ 
        'office_location_label',
        'office_location_image',
        'office_location_address',
        'office_location_phone_number',
        'office_location_latitude',
        'office_location_longitude',
        'office_location_section_size'
    ];

    $posts = get_posts( $args );
    $newPosts = [];

    foreach ($posts as $post) {
        $newPostData = [
            'ID'                => $post->ID,
            'post_content'      => convertCurlyQuotes($post->post_content),
            'post_title'        => convertCurlyQuotes($post->post_title),
            'post_name'         => $post->post_name,
            'menu_order'        => $post->menu_order
        ];

        foreach ($custom_fields as $field) {
            $newPostData[$field] = convertCurlyQuotes(get_field($field, $post->ID, true ));
        }

        $newPosts[] = $newPostData;
    }

    $json = json_encode($newPosts, JSON_UNESCAPED_SLASHES);
    $json = str_replace( ['\n', '\r'], '', $json);

    $script          = "<script>";
    $script         .= "const hqJSON = $json;";
    $script         .= "</script>";
    
    return $script;
}
add_shortcode('offices', 'officeLocations_func');


