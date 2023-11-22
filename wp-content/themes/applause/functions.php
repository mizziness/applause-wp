<?php

if (!defined('ABSPATH')) {
    die();
}

use Aws\S3\Exception\S3Exception;

require __DIR__ . "/includes/custom-posts.php";
require __DIR__ . "/includes/shortcodes.php";
require __DIR__ . "/includes/blog.php";
require __DIR__ . "/includes/resources.php";
require __DIR__ . "/includes/newsroom.php";
require __DIR__ . "/includes/biographies.php";

if (!function_exists('WP_Filesystem')) {
    include_once 'wp-admin/includes/file.php';
}

function get_inner_html($node)
{
    $innerHTML = '';
    $handleildren = $node->childNodes;
    foreach ($handleildren as $handleild) {
        $innerHTML .= $handleild->ownerDocument->saveHTML($handleild);
    }

    return $innerHTML;
}

function get_attachment_id_by_filename($filename)
{
    global $wpdb;
    $mktID = "%" . explode("-", $filename)[0] . "%";

    $sql = $wpdb->prepare("SELECT * FROM `wp_posts` WHERE `guid` LIKE %s ORDER BY post_date, id DESC LIMIT 1", array($mktID));
    $attachments = $wpdb->get_results($sql, OBJECT);
    return $attachments[0]->ID ?? false;
}

function divi_engine_body_class($classes)
{
    global $post;
    $slug = $post->post_name ?? false;

    if ($slug) {
        $classes[] = $slug;
    }

    return $classes;
}
add_filter('body_class', 'divi_engine_body_class', 99999);

function wpb_custom_new_menu()
{
    register_nav_menu('mobile-menu', __('Mobile Menu'));
}
add_action('init', 'wpb_custom_new_menu');


function enqueue_admin_script()
{
    wp_register_style('font-css', get_stylesheet_directory_uri() . '/public/webfonts/proximanova/font-proximanova.css');
    wp_register_style('admin-css', get_stylesheet_directory_uri() . '/dist/admin.css', array('font-css'));

    wp_register_script('admin-js', get_stylesheet_directory_uri() . '/src/js/admin.js', array());

    wp_enqueue_style('font-css');
    wp_enqueue_style('admin-css');
    wp_enqueue_script('admin-js');
}
add_action('admin_enqueue_scripts', 'enqueue_admin_script');


add_action('wp_enqueue_scripts', 'ds_ct_enqueue_parent');
function ds_ct_enqueue_parent()
{
    global $post;
    if (in_category('state-of-digital-quality-2022') && is_page()) {
        wp_enqueue_style('roc-grotesk', 'https://use.typekit.net/zlx5gpp.css');
    }
    if (in_category('state-of-digital-quality-2023') && is_page()) {
        wp_enqueue_style('transducer', 'https://use.typekit.net/otb7pmx.css');
    }

    wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css');
    wp_enqueue_style('font-css', get_stylesheet_directory_uri() . '/public/webfonts/proximanova/font-proximanova.css');
    wp_enqueue_style('app-css', get_stylesheet_directory_uri() . '/dist/app-build.min.css', array('font-css'));
    wp_enqueue_style('extras-css', get_stylesheet_directory_uri() . '/dist/extras-build.min.css', array('font-css'));

    if (is_admin()) {
        return;
    }
}

add_action('wp_enqueue_scripts', 'ds_ct_loadjs');
function ds_ct_loadjs()
{
    wp_enqueue_script('ds-theme-script', get_stylesheet_directory_uri() . '/ds-script.js', array('jquery'));
    wp_enqueue_script('swiper-script', 'https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js');

    if (is_admin()) {
        return;
    }

    wp_enqueue_script('wistia-script', 'https://fast.wistia.com/assets/external/E-v1.js', array(), '', array('strategy' => 'async'));
    wp_enqueue_script('lazysizes', get_stylesheet_directory_uri() . '/public/assets/lazysizes.min.js');

    // Localize the AJAX URL
    wp_localize_script('ds-theme-script', 'load_more_params', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'current_page' => get_query_var('paged') ? get_query_var('paged') : 1,
    )
    );
}

function dpdfg_custom_loader()
{
    ob_start();
    ?>
        <div class="dp-dfg-loader"><div class="applause-loader"></div></div>
        <style>
            .dp-dfg-loader {
                position: absolute;
                top: 50%;
                left: 50%;
                margin-top: -30px;
                margin-left: -30px;
            }
            .applause-loader {
                border: 8px solid #01446C;
                border-top: 8px solid #0D7DC1;
                border-radius: 50%;
                width: 60px;
                height: 60px;
                animation: spin 2s ease-in-out infinite;
                margin: 0 auto;
            }
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        </style>
    <?php
    return ob_get_clean();
}
add_filter('dpdfg_custom_loader', 'dpdfg_custom_loader');

add_filter('acf/settings/remove_wp_meta_box', '__return_false');

add_action('http_api_curl', function ($handle) {
    // curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, 0);
    // curl_setopt($handle, CURLOPT_VERBOSE, true);
    // curl_setopt($handle, CURLOPT_STDERR, fopen('php://stderr', 'w'));
});

function allow_cors()
{
    // Replace '*' with the specific origin you want to allow, or use a more advanced configuration.
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Authorization, Content-Type, X-WP-Nonce");
}
add_action('rest_api_init', 'allow_cors', 15);

// Load the modern JS bundles as modules, calling them out by handle name
add_filter('script_loader_tag', 'add_type_attribute', 10, 3);
function add_type_attribute($tag, $handle, $src)
{
    // if not your script, do nothing and return original $tag
    if ('app-js' !== $handle && 'vendors-js' !== $handle) {
        return $tag;
    }

    $tag = '<script type="module" src="' . esc_url($src) . '"></script>';

    return $tag;
}

include('login-editor.php');

function ds_ct_remove_srcset($sources)
{
    // Remove all sources from the srcset attribute
    return false;
}
add_filter('wp_calculate_image_srcset', 'ds_ct_remove_srcset');

//Fix WPML setup
function divi_custom_excerpt_length($length)
{
    return 20;
}
add_filter('excerpt_length', 'divi_custom_excerpt_length', 999);

add_filter('manage_biographies_posts_columns', 'smashing_filter_posts_columns');
function smashing_filter_posts_columns($columns)
{
    $columns['executive_sort_order'] = __('Sort Order', 'smashing');
    return $columns;
}

add_action('manage_biographies_posts_custom_column', 'biographies_column', 10, 2);
function biographies_column($column, $post_id)
{
    if ($column == 'executive_sort_order') {
        $order = get_post_meta($post_id, 'executiveSortOrder', true);
        if (!$order) {
            _e('--');
        } else {
            echo $order;
        }
    }
}

add_action('admin_head', 'admin_custom_css');
function admin_custom_css()
{
    echo '<style> 
        th#executive_sort_order { width: 7rem; } 
        td.executive_sort_order.column-executive_sort_order { text-align: center; }
        #banner-alerts,
        .banner-alerts { display: none !important; }
    </style>';
}

add_filter('manage_edit-biographies_sortable_columns', 'filter_posts_columns');
function filter_posts_columns($columns)
{
    $columns['executive_sort_order'] = array(
        'executiveSortOrder',
        false,
        'Sort Order',
        'Table ordered by Custom Sort Order',
        'asc'
    );
    return $columns;
}

add_action('pre_get_posts', 'posts_orderby');
function posts_orderby($query)
{
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    if ($query->get('orderby') === 'executiveSortOrder') {
        $query->set('orderby', 'meta_value');
        $query->set('meta_key', 'executiveSortOrder');
        $query->set('meta_type', 'numeric');
        $query->set('order', 'asc');
    }

    return $query;
}

function custom_attachment_filter($post_id)
{

    $file = get_post($post_id);

    // We only want to handle zip files that begin with mkt#### 
    if ($file === null || $file->post_mime_type != "application/zip" || strpos($file->guid, "mkt") < 0) {
        // Not a Hype File, skipsies
        return $post_id;
    }

    $filename = basename($file->guid);
    $fileParts = explode(".", $filename);
    $localFile = get_attached_file($file->ID);
    $hypeFolder = WP_CONTENT_DIR . "/uploads/hype4";
    $mkt_id = explode("-", $fileParts[0])[0] ?? null;

    // Make sure we have a marketo ID on the filename
    if ($mkt_id == null) {
        return $post_id;
    }

    require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
    require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
    $filesystem = new WP_Filesystem_Direct(false);

    // Make the hype4 local directory if it doesn't exist
    if (!is_dir($hypeFolder)) {
        $filesystem->mkdir($hypeFolder, 0755);
    }

    try {
        unzip_file($localFile, $hypeFolder);
    } catch (\Throwable $th) {
        error_log('Hype Uploads: Could not extract the archive / zip file to a folder.');
        return $file;
    }

    $getFolders = scandir($hypeFolder);
    $foundIt = false;

    foreach ($getFolders as $key => $value) {
        // Check for the unprocessed folder - the folder name will have the mktID in it
        // but also consists of other words.
        if ($value != $mkt_id && strpos($value, $mkt_id) >= 0) {
            $foundIt = $value;
        }
    }

    $foundFolder = WP_CONTENT_DIR . "/uploads/hype4/" . $foundIt;

    if (!is_dir($foundFolder)) {
        error_log('Hype Uploads: Extracted folder was not found.');
        return $file;
    }

    // Rename the directory to just the mktID prefix
    $targetFolder = $hypeFolder . "/" . $mkt_id;
    $filesystem->move($foundFolder, $targetFolder, true);

    $defaultFolder = $filesystem->find_folder($targetFolder . "/Default");

    // Grab the index.html file
    if (is_dir($defaultFolder) && file_exists($defaultFolder . "/index.html")) {
        $index = $defaultFolder . "/index.html";
        $pageHTML = mb_convert_encoding(file_get_contents($index), "UTF-8");
        $updatedHTML = str_replace("default_hype_container", "default_hype_container_" . $mkt_id, $pageHTML);
        $updatedHTML = str_replace(array('><![CDATA[', ']]></script>'), array('>', '</script>'), $updatedHTML);
        $updatedHTML = str_replace('\n', " ", $updatedHTML);
        $updatedHTML = str_replace('\t', "", $updatedHTML);

        $domme = new DomDocument();
        $domme->validateOnParse = true;
        $domme->preserveWhiteSpace = false;
        $domme->formatOutput = false;

        @$domme->loadHTML($updatedHTML);
        $xpath = new DOMXpath($domme);

        $html = $xpath->query('/html[1]//*');

        $newHTML = "";

        foreach ($html as $node) {
            if ($node->nodeName == "link") {
                $newHTML .= $domme->saveHTML($node) . PHP_EOL;
            }

            if ($node->nodeName == "body") {
                $newHTML .= get_inner_html($node) . PHP_EOL;
            }
        }

        $newHTML = str_replace(array('><![CDATA[', ']]></script>'), array('>', '</script>'), $newHTML);

        // update_post_meta($post_id, "hype_content", $newHTML);
        // $hype_content = get_post_meta($post_id, "hype_content", true);

        // if ( strlen($hype_content) <= 0 ) {
        //     error_log('Hype Uploads: Unable to save html to meta field.');
        //     return $file;
        // }

        // Create extracted.html for use later
        $filesystem->put_contents($newFolder . "/" . $defaultFolder . "extracted.html", $newHTML);

        // Upload folder to S3
        $sharedConfig = [
            'region' => getenv('AWS_REGION'),
            'version' => 'latest'
        ];

        $sdk = new Aws\Sdk($sharedConfig);
        $client = $sdk->createS3();
        $prefix = "public/wp-content/uploads/hype4/";

        try {

            // Delete and re-upload the s3 folder copy
            $client->deleteMatchingObjects(getenv('AWS_BUCKET'), $prefix . $mkt_id . "/");
            $client->uploadDirectory($targetFolder, getenv('AWS_BUCKET'), $prefix . $mkt_id);

        } catch (S3Exception $th) {
            error_log('Hype Uploads: Upload to the bucket failed.');
            return $file;
        }

        // Delete the local folder
        $filesystem->delete($targetFolder, true);

    }
}
add_action('add_attachment', 'custom_attachment_filter', 10, 1);
