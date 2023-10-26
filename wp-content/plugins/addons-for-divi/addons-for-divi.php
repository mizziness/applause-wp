<?php
/*
 * Plugin Name: Torque Modules Lite
 * Plugin URI:  https://divitorque.com
 * Description: Powerful divi modules to create powerful websites.
 * Version:     3.6.5
 * Author:      WPPaw
 * Author URI:  https://wppaw.com
 * License: GPL3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: addons-for-divi
 * Domain Path: /languages
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

define('DIVI_TORQUE_PLUGIN_VERSION', '3.6.5');
define('DIVI_TORQUE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('DIVI_TORQUE_PLUGIN_URL', plugin_dir_url(__FILE__));
define('DIVI_TORQUE_PLUGIN_ASSETS', trailingslashit(DIVI_TORQUE_PLUGIN_URL . 'assets'));
define('DIVI_TORQUE_PLUGIN_FILE', __FILE__);
define('DIVI_TORQUE_PLUGIN_BASE', plugin_basename(__FILE__));

do_action('divitorque_loaded');

if (!class_exists('DIVI_TORQUE_PLUGIN')) :
    final class DIVI_TORQUE_PLUGIN
    {

        private static $instance;

        private function __construct()
        {
            register_activation_hook(__FILE__, array($this, 'activate'));
            add_action('plugins_loaded', array($this, 'init_plugin'));
        }

        public static function instance()
        {
            if (!isset(self::$instance) && !(self::$instance instanceof DIVI_TORQUE_PLUGIN)) {
                self::$instance = new DIVI_TORQUE_PLUGIN();
                self::$instance->init();
                self::$instance->includes();
            }

            return self::$instance;
        }

        private function init()
        {
            add_action('divi_extensions_init', array($this, 'initialize_extension'));
        }

        public function init_plugin()
        {
            new DiviTorque\Includes\AssetsManager();
        }


        public function activate()
        {
            update_option('dt-version', DIVI_TORQUE_PLUGIN_VERSION);

            $version = get_option('divitorque_version');

            if ($version) {
                if (version_compare($version, '3.5.7', '<=')) {
                    $deprecated = new DiviTorque_Deprecated();
                    $deprecated->run();
                }
            }
        }

        public function includes()
        {
            require_once DIVI_TORQUE_PLUGIN_DIR . 'includes/functions.php';
            require_once DIVI_TORQUE_PLUGIN_DIR . 'includes/deprecated.php';
            require_once DIVI_TORQUE_PLUGIN_DIR . 'includes/extensions/extensions.php';
            require_once DIVI_TORQUE_PLUGIN_DIR . 'includes/assets-manager.php';
            require_once DIVI_TORQUE_PLUGIN_DIR . 'includes/functions-forms.php';
        }

        public function initialize_extension()
        {
            require_once DIVI_TORQUE_PLUGIN_DIR . 'includes/divi-extension.php';
        }
    }

endif;

function divi_torque_lite_plugin()
{
    return DIVI_TORQUE_PLUGIN::instance();
}

divi_torque_lite_plugin();
