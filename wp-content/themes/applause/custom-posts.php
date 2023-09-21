<?php
	
function cptui_register_my_cpts() {

	/**
	 * Post Type: Blog Authors.
	 */

	$labels = [
		"name" => esc_html__( "Blog Authors", "custom-post-type-ui" ),
		"singular_name" => esc_html__( "Blog Author", "custom-post-type-ui" ),
	];

	$args = [
		"label" => esc_html__( "Blog Authors", "custom-post-type-ui" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => false,
		"rewrite" => [ "slug" => "author", "with_front" => true ],
		"query_var" => true,
		"menu_icon" => "dashicons-admin-users",
		"supports" => [ "title", "editor", "thumbnail", "custom-fields", "page-attributes", "author" ],
		"show_in_graphql" => false,
	];

	register_post_type( "blog-author", $args );

	/**
	 * Post Type: Press Releases.
	 */

	$labels = [
		"name" => esc_html__( "Press Releases", "custom-post-type-ui" ),
		"singular_name" => esc_html__( "Press Release", "custom-post-type-ui" ),
	];

	$args = [
		"label" => esc_html__( "Press Releases", "custom-post-type-ui" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => true,
		"can_export" => true,
		"rewrite" => false,
		"query_var" => true,
		"supports" => [ "title", "editor", "thumbnail", "custom-fields", "page-attributes", "author" ],
		"show_in_graphql" => false,
	];

	register_post_type( "press_releases", $args );

	/**
	 * Post Type: Biographies.
	 */

	$labels = [
		"name" => esc_html__( "Biographies", "custom-post-type-ui" ),
		"singular_name" => esc_html__( "Biography", "custom-post-type-ui" ),
	];

	$args = [
		"label" => esc_html__( "Biographies", "custom-post-type-ui" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => false,
		"rewrite" => [ "slug" => "biographies", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "editor", "thumbnail", "custom-fields", "page-attributes", "author" ],
		"show_in_graphql" => false,
	];

	register_post_type( "biographies", $args );

	/**
	 * Post Type: Showcases.
	 */

	$labels = [
		"name" => esc_html__( "Showcases", "custom-post-type-ui" ),
		"singular_name" => esc_html__( "Showcase", "custom-post-type-ui" ),
	];

	$args = [
		"label" => esc_html__( "Showcases", "custom-post-type-ui" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => false,
		"rewrite" => [ "slug" => "showcases", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "editor", "thumbnail", "custom-fields", "page-attributes", "author" ],
		"show_in_graphql" => false,
	];

	register_post_type( "showcases", $args );

	/**
	 * Post Type: Events.
	 */

	$labels = [
		"name" => esc_html__( "Events", "custom-post-type-ui" ),
		"singular_name" => esc_html__( "Event", "custom-post-type-ui" ),
	];

	$args = [
		"label" => esc_html__( "Events", "custom-post-type-ui" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => false,
		"rewrite" => [ "slug" => "events", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "editor", "thumbnail", "custom-fields", "page-attributes", "author" ],
		"show_in_graphql" => false,
	];

	register_post_type( "events", $args );

	/**
	 * Post Type: News Mentions.
	 */

	$labels = [
		"name" => esc_html__( "News Mentions", "custom-post-type-ui" ),
		"singular_name" => esc_html__( "News Mention", "custom-post-type-ui" ),
	];

	$args = [
		"label" => esc_html__( "News Mentions", "custom-post-type-ui" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => false,
		"rewrite" => [ "slug" => "news_mentions", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "editor", "thumbnail", "custom-fields", "page-attributes", "author" ],
		"show_in_graphql" => false,
	];

	register_post_type( "news_mentions", $args );

	/**
	 * Post Type: Case Studies.
	 */

	$labels = [
		"name" => esc_html__( "Case Studies", "custom-post-type-ui" ),
		"singular_name" => esc_html__( "Case Study", "custom-post-type-ui" ),
	];

	$args = [
		"label" => esc_html__( "Case Studies", "custom-post-type-ui" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => false,
		"rewrite" => [ "slug" => "case_studies", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "editor", "thumbnail", "custom-fields", "page-attributes", "author", "excerpt" ],
		"show_in_graphql" => false,
		"taxonomies" => [ "post_tag", "category" ]
	];

	register_post_type( "case_studies", $args );

	/**
	 * Post Type: Ebooks.
	 */

	$labels = [
		"name" => esc_html__( "Ebooks", "custom-post-type-ui" ),
		"singular_name" => esc_html__( "Ebook", "custom-post-type-ui" ),
	];

	$args = [
		"label" => esc_html__( "Ebooks", "custom-post-type-ui" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => false,
		"rewrite" => [ "slug" => "ebooks", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "editor", "thumbnail", "custom-fields", "page-attributes", "author" ],
		"show_in_graphql" => false,
		"taxonomies" => [ "post_tag", "category" ]
	];

	register_post_type( "ebooks", $args );

	/**
	 * Post Type: Podcasts.
	 */

	$labels = [
		"name" => esc_html__( "Podcasts", "custom-post-type-ui" ),
		"singular_name" => esc_html__( "Podcast", "custom-post-type-ui" ),
	];

	$args = [
		"label" => esc_html__( "Podcasts", "custom-post-type-ui" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => false,
		"rewrite" => [ "slug" => "resources/podcasts", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "editor", "thumbnail", "custom-fields", "page-attributes", "author" ],
		"show_in_graphql" => false,
		"taxonomies" => [ "post_tag", "category" ]
	];

	register_post_type( "podcasts", $args );

	/**
	 * Post Type: Reports.
	 */

	$labels = [
		"name" => esc_html__( "Reports", "custom-post-type-ui" ),
		"singular_name" => esc_html__( "Report", "custom-post-type-ui" ),
	];

	$args = [
		"label" => esc_html__( "Reports", "custom-post-type-ui" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => false,
		"rewrite" => [ "slug" => "reports", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "editor", "thumbnail", "custom-fields", "page-attributes", "author", "excerpt" ],
		"show_in_graphql" => false,
		"taxonomies" => [ "post_tag", "category" ]
	];

	register_post_type( "reports", $args );

	/**
	 * Post Type: Webinars.
	 */

	$labels = [
		"name" => esc_html__( "Webinars", "custom-post-type-ui" ),
		"singular_name" => esc_html__( "Webinar", "custom-post-type-ui" ),
	];

	$args = [
		"label" => esc_html__( "Webinars", "custom-post-type-ui" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => false,
		"rewrite" => [ "slug" => "webinars", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "editor", "thumbnail", "custom-fields", "page-attributes", "author" ],
		"show_in_graphql" => false,
		"taxonomies" => [ "post_tag", "category" ]
	];

	register_post_type( "webinars", $args );

	/**
	 * Post Type: Whitepapers.
	 */

	$labels = [
		"name" => esc_html__( "Whitepapers", "custom-post-type-ui" ),
		"singular_name" => esc_html__( "Whitepaper", "custom-post-type-ui" ),
	];

	$args = [
		"label" => esc_html__( "Whitepapers", "custom-post-type-ui" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => false,
		"rewrite" => [ "slug" => "whitepapers", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "editor", "thumbnail", "custom-fields", "page-attributes", "author" ],
		"show_in_graphql" => false,
		"taxonomies" => [ "post_tag", "category" ]
	];

	register_post_type( "whitepapers", $args );

	/**
	 * Post Type: Videos.
	 */

	$labels = [
		"name" => esc_html__( "Videos", "custom-post-type-ui" ),
		"singular_name" => esc_html__( "Videos", "custom-post-type-ui" ),
	];

	$args = [
		"label" => esc_html__( "Videos", "custom-post-type-ui" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => false,
		"rewrite" => [ "slug" => "videos", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "editor", "thumbnail", "custom-fields", "page-attributes", "author" ],
		"show_in_graphql" => false,
		"taxonomies" => [ "post_tag", "category" ]
	];

	register_post_type( "videos", $args );
}

add_action( 'init', 'cptui_register_my_cpts' );
