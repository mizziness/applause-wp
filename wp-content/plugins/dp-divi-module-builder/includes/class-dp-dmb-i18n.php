<?php

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin so that it is ready for translation.
 *
 * @since      2.0.0
 * @package    dp-divi-module-builder
 * @subpackage dp-divi-module-builder/includes
 * @author     DiviPlugins <support@diviplugins.com>
 */
class DP_Divi_Module_Builder_i18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    2.0.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'dp_divi_module_builder', false, 'dp-divi-module-builder/languages/' );
	}

}
