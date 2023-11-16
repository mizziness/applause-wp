<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      2.0.0
 * @package    dp-divi-odule-builder
 * @subpackage dp-divi-odule-builder/includes
 * @author     DiviPlugins <support@diviplugins.com>
 */
class DP_DMB_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @param $network_wide
	 *
	 * @since    2.0.0
	 */
	public static function activate( $network_wide ) {
		require_once DPDMB_PLUGIN_DIR . 'includes/class-dp-dmb-utils-functions.php';
		$utils = new DP_DMB_Utils_Functions();
		$utils->dmb_write_log( 'Activating DMB' );
		if ( is_multisite() && $network_wide ) {
			global $wpdb;
			// phpcs:ignore
			foreach ( $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" ) as $blog_id ) {
				switch_to_blog( $blog_id );
				$utils->set_module_files_dir();
				restore_current_blog();
			}
		} else {
			$utils->set_module_files_dir();
		}
	}

}
