<?php

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



// Custom Shortcode to insert a Wistia Play Video button, only
// ===============================================================================
function wistiaEmbed_func($atts)
{
    $a = shortcode_atts(
        array(
            'wistia_id' => '',
            'category' => 'Page Navigation',
            'action' => 'Button',
            'button_text' => 'Watch Now',
            'icon' => '►',
            'button_class' => 'is-secondary'
        ), $atts);

    if ( empty($a['wistia_id'] )) {
        return '<strong> Error! Missing Wistia ID in the shortcode! </strong>';
    }

    $html = "";
    $html .= "<!--  Wistia JS API Button for {$a["wistia_id"]}  -->";
    $html .= "<script src='https://fast.wistia.com/embed/medias/{$a["wistia_id"]}.jsonp' async></script>";
    $html .= "<span id='wistia-id-{$a["wistia_id"]}' class='wistia_embed wistia_async_{$a["wistia_id"]} popover=true popoverContent=link' style='display:inline;position:relative'>";
    $html .= "<div class='button {$a["button_class"]} click-track wistia-trigger' role='button' aria-label='{$a["button_text"]}' target='_self' data-category='{$a["category"]}' data-action='{$a["action"]}' data-label='{$a["button_text"]}' data-href='{$a["wistia_id"]}'>";
    $html .= "<span> {$a["icon"]} {$a["button_text"]} </span>";
    $html .= "</div></span>";

    // Use like: [wistia_button wistia_id="xxxxxxx" button_text="Watch Now"]
    return $html;
}
add_shortcode('wistia_button', 'wistiaEmbed_func');

