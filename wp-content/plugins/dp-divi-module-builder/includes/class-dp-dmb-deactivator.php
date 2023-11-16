<?php

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      2.0.0
 * @package    dp-divi-module-builder
 * @subpackage dp-divi-module-builder/includes
 * @author     DiviPlugins <support@diviplugins.com>
 */
class DP_DMB_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    2.0.0
	 */
	public static function deactivate() {
		require_once DPDMB_PLUGIN_DIR . 'includes/class-dp-dmb-utils-functions.php';
		$utils = new DP_DMB_Utils_Functions();
		$utils->dmb_write_log( 'Deactivating DMB' );
		unregister_post_type( 'dp_custom_modules' );
	}

}
