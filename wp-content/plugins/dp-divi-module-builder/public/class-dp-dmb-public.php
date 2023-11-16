<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    dp-divi-module-builder
 * @subpackage dp-divi-module-builder/public
 * @author     DiviPlugins <support@diviplugins.com>
 */
class DP_DMB_Public extends DP_DMB_Utils_Functions {

	/**
	 * The ID of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    2.0.0
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    2.0.0
	 */
	public function enqueue_styles() {
		$dependecies = get_option( 'dp_dmb_dependency_options', array() );
		if ( ! empty( $dependecies ) && array_key_exists( '_dp_dmb_external_cssjs_metabox_repeat_group_fields', $dependecies ) ) {
			foreach ( $dependecies['_dp_dmb_external_cssjs_metabox_repeat_group_fields'] as $dependency ) {
				if ( array_key_exists( 'name', $dependency ) && array_key_exists( 'url', $dependency ) && ! empty( $dependency['name'] ) && ! empty( $dependency['url'] ) ) {
					if ( $dependency['type'] === 'css' ) {
						wp_enqueue_style( $dependency['name'], $dependency['url'], array(), $this->version, 'all' );
					}
				}
			}
		}
		if ( function_exists( 'et_core_is_fb_enabled' ) && et_core_is_fb_enabled() ) {
			wp_enqueue_style( $this->plugin_name, DPDMB_PLUGIN_URL . '/public/css/dp-dmb-public.css', array(), $this->version );
		}
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    2.0.0
	 */
	public function enqueue_scripts() {
		$dependecies = get_option( 'dp_dmb_dependency_options', array() );
		if ( ! empty( $dependecies ) && array_key_exists( '_dp_dmb_external_cssjs_metabox_repeat_group_fields', $dependecies ) ) {
			foreach ( $dependecies['_dp_dmb_external_cssjs_metabox_repeat_group_fields'] as $dependency ) {
				if ( array_key_exists( 'name', $dependency ) && array_key_exists( 'url', $dependency ) && ! empty( $dependency['name'] ) && ! empty( $dependency['url'] ) ) {
					if ( $dependency['type'] === 'js' ) {
						wp_enqueue_script( $dependency['name'], $dependency['url'], array( 'jquery' ), $this->version, true );
					}
				}
			}
		}
	}

	/**
	 * Legacy way to load script and styles of modules
	 *
	 * @since 2.0.0
	 */
	public function legacy_way_to_register_styles_and_scripts() {
		foreach ( parent::get_modules() as $module ) {
			$id               = $module['id'];
			$file_module_name = DPDMB_MODULES_DIR . '/css/dp_custom_module_' . $id . '.css';
			if ( file_exists( $file_module_name ) ) {
				wp_enqueue_style( 'dmb-module-' . $id, DPDMB_MODULES_URL . '/css/dp_custom_module_' . $id . '.css' );
			}
			$file_module_name = DPDMB_MODULES_DIR . '/js/dp_custom_module_' . $id . '.js';
			if ( file_exists( $file_module_name ) ) {
				if ( function_exists( 'et_builder_bfb_enabled' ) && et_builder_bfb_enabled() ) {
					wp_enqueue_script( 'dmb-module-' . $id, DPDMB_MODULES_URL . '/js/dp_custom_module_' . $id . '.js', array( 'jquery' ), false, true );
				} else {
					wp_register_script( 'dmb-module-' . $id, DPDMB_MODULES_URL . '/js/dp_custom_module_' . $id . '.js', array( 'jquery' ), false, true );
				}
			}
		}
	}

}
