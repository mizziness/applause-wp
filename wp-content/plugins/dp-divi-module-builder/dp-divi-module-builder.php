<?php

/**
 * Plugin Name: Divi Module Builder
 * Plugin URI: https://www.diviplugins.com/divi-module-builder/
 * Description: Create, import and export custom modules for Divi. Once a module is published, it becomes available as a new module inside the Divi Page Builder.
 * Version: 2.4.3
 * Author: DiviPlugins
 * Author URI: http://www.diviplugins.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: dp_divi_module_builder
 * Domain Path: /languages
 * Update URI: https://elegantthemes.com/
 * */
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'DPDMB_PLUGIN_VERSION', '2.4.3' );
define( 'DPDMB_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'DPDMB_PLUGIN_URL', plugins_url( '', __FILE__ ) );
define( 'DPDMB_PLUGIN_LICENSE_PAGE', 'dp_dmb_license' );
define( 'DPDMB_STORE_URL', 'https://diviplugins.com/' );
define( 'DPDMB_ITEM_NAME', 'Divi Module Builder' );
define( 'DPDMB_ITEM_ID', '11837' );
define( 'DPDMB_MODULES_DIR', wp_upload_dir()['basedir'] . '/dmb' );
define( 'DPDMB_MODULES_API_URL', "https://modules.diviplugins.com/wp-json/divi_plugins/v1/modules" );
if ( is_ssl() ) {
	define( 'DPDMB_MODULES_URL', str_replace( 'http://', 'https://', wp_upload_dir()['baseurl'] . '/dmb' ) );
} else {
	define( 'DPDMB_MODULES_URL', wp_upload_dir()['baseurl'] . '/dmb' );
}

/**
 * The code that runs during plugin activation. This action is documented in includes/class-dp-dmb-activator.php
 *
 * @param $network_wide
 *
 * @since 2.0.0
 */
function activate_dp_dmb( $network_wide ) {
	require_once DPDMB_PLUGIN_DIR . 'includes/class-dp-dmb-activator.php';
	DP_DMB_Activator::activate( $network_wide );
}

register_activation_hook( __FILE__, 'activate_dp_dmb' );

/**
 * The code that runs during plugin deactivation. This action is documented in includes/class-dp-dmb-deactivator.php
 *
 * @since 2.0.0
 */
function deactivate_dp_dmb() {
	require_once DPDMB_PLUGIN_DIR . 'includes/class-dp-dmb-deactivator.php';
	DP_DMB_Deactivator::deactivate();
}

register_deactivation_hook( __FILE__, 'deactivate_dp_dmb' );

/**
 * Add new modules as main plugin links to the plugins lists
 *
 * @param $links
 *
 * @return array
 * @since 2.0.0
 */
function dp_dmb_add_plugin_main_links( $links ) {
	$links['new_module'] = sprintf( '<a href="%1$s"><b>%2$s</b></a>', admin_url( 'post-new.php?post_type=dp_custom_modules' ), __( 'New module', 'dp_divi_module_builder' ) );

	return array_reverse( $links );
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'dp_dmb_add_plugin_main_links', 10, 1 );

/**
 * Add settings, license and get support links to the plugins lists in the plugin meta row.
 *
 * @param $links
 * @param $file
 *
 * @return mixed
 * @since 2.0.0
 */
function dp_dmb_add_plugin_row_meta( $links, $file ) {
	if ( $file === plugin_basename( __FILE__ ) ) {
		$links['settings'] = sprintf( '<a href="%1$s">%2$s</a>', admin_url( 'edit.php?post_type=dp_custom_modules&page=' ) . DPDMB_PLUGIN_LICENSE_PAGE, __( 'Settings', 'dp_divi_module_builder' ) );
		$links['support']  = sprintf( '<a href="%1$s" target="_blank">%2$s</a>', 'http://www.diviplugins.com/contact', __( 'Get support', 'dp_divi_module_builder' ) );
	}

	return $links;
}

add_filter( 'plugin_row_meta', 'dp_dmb_add_plugin_row_meta', 10, 2 );


/**
 * The core plugin class that is used to define internationalization, admin-specific hooks, and public-facing site hooks.
 */
require DPDMB_PLUGIN_DIR . 'includes/class-dp-divi-module-builder.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks, then kicking off the plugin from this point in the file does not affect the page life cycle.
 *
 * @since    2.0.0
 */
function run_dp_dmb() {
	$template = get_option( 'template' );
	if ( 'Extra' === $template || 'Divi' === $template || defined( 'ET_BUILDER_PLUGIN_VERSION' ) ) {
		$plugin = new DP_Divi_Module_Builder();
		$plugin->run();
	}
}

run_dp_dmb();
