<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current version of the plugin.
 *
 * @since      2.0.0
 * @package    dp-divi-module-builder
 * @subpackage dp-divi-module-builder/includes
 * @author     DiviPlugins <support@diviplugins.com>
 */
class DP_Divi_Module_Builder {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power the plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      Dp_Divi_Module_Builder_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin. Load the dependencies, define the locale, and set the hooks for the admin area and the public-facing side of the site.
	 *
	 * @since    2.0.0
	 */
	public function __construct() {
		if ( defined( 'DPDMB_PLUGIN_VERSION' ) ) {
			$this->version = DPDMB_PLUGIN_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'dp_divi_module_builder';
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - DP_Divi_Module_Builder_Loader. Orchestrates the hooks of the plugin.
	 * - DP_Divi_Module_Builder_i18n. Defines internationalization functionality.
	 * - DP_Divi_Module_Builder_Admin. Defines all hooks for the admin area.
	 * - DP_Divi_Module_Builder_Public. Defines all hooks for the public side of the site.
	 * - DP_DMB_Plugin_Updater. Defines the EDD Software License class used to provide automatic updates.
	 * - DP_DMB_Utils_Functions. Defines common functions for use across the plugin.
	 * - DP_DMB_CMB2. Defines use of cmb2 metaboxes on the plugin.
	 * - DP_DMB_Create_Modules. Defines the process of custom modules files creation.
	 *
	 * Create an instance of the loader which will be used to register the hooks with WordPress.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		/**
		 * The class responsible for orchestrating the actions and filters of the core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-dp-dmb-loader.php';
		/**
		 * The class responsible for orchestrating the actions and filters of the core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-dp-dmb-i18n.php';
		/**
		 * The class contains several common static function to use across the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-dp-dmb-utils-functions.php';
		/**
		 * The class handle the settings page related functions.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-dp-dmb-settings.php';
		/**
		 * The class handle the import page related functions.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-dp-dmb-import.php';
		/**
		 * The class contain cmb2 hooks related functions for declare metaboxes
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-dp-dmb-cmb2.php';
		/**
		 * The class responsible for the process of creation of custom modules files
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-dp-dmb-custom-module.php';
		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-dp-dmb-admin.php';
		/**
		 * The class responsible for defining all actions that occur in the public-facing side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-dp-dmb-public.php';
		/*
		 * Legacy load of global settings for custom modules
		 */
		foreach ( glob( DPDMB_MODULES_DIR . '/gs/*.php' ) as $filename ) {
			require_once $filename;
		}
		$this->loader = new DP_Divi_Module_Builder_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the DP_Divi_Module_Builder_i18n class in order to set the domain and to register the hook with WordPress.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function set_locale() {
		$plugin_i18n = new DP_Divi_Module_Builder_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality of the plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new DP_DMB_Admin( $this->get_plugin_name(), $this->get_version() );
		/*
		 * Custom modules post type related hooks
		 */
		$this->loader->add_action( 'init', $plugin_admin, 'add_custom_modules_post_type' );
		$this->loader->add_action( 'quick_edit_custom_box', $plugin_admin, 'add_php_checkbox_on_quick_edit', 10, 2 );
		$this->loader->add_action( 'save_post', $plugin_admin, 'update_post_meta_after_quick_edit', 99, 1 );
		$this->loader->add_action( 'manage_dp_custom_modules_posts_custom_column', $plugin_admin, 'add_custom_php_column', 10, 2 );
		$this->loader->add_action( 'do_meta_boxes', $plugin_admin, 'remove_unwanted_metabox', 9999 );
		$this->loader->add_action( 'admin_head', $plugin_admin, 'remove_unwanted_metabox_head', 9999 );
		$this->loader->add_action( 'admin_title', $plugin_admin, 'modify_admin_tab_titles', 10, 1 );
		/*
		 * Styles and scripts related hooks
		 */
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		/*
		 * CMB2 related hooks
		 */
		$cmb2 = new DP_DMB_CMB2();
		$this->loader->add_action( 'cmb2_admin_init', $cmb2, 'custom_modules_metaboxes' );
		$this->loader->add_action( 'cmb2_admin_init', $cmb2, 'external_dependency_metabox' );
		$this->loader->add_action( 'cmb2_admin_init', $cmb2, 'debugger_metabox' );
		/*
		 * Ajax related hooks
		 */
		$settings = new DP_DMB_Settings();
		$import   = new DP_DMB_Import();
		$this->loader->add_action( 'wp_ajax_dp_dmb_process_import_file', $import, 'ajax_process_import_file' );
		$this->loader->add_action( 'wp_ajax_dp_dmb_process_import_module_library', $import, 'ajax_process_import_library_module' );
		$this->loader->add_action( 'wp_ajax_dp_dmb_process_import_publish_module', $import, 'ajax_process_publish_module' );
		$this->loader->add_action( 'wp_ajax_dp_dmb_process_update_file', $settings, 'ajax_process_update_file' );
		$this->loader->add_action( 'wp_ajax_dp_dmb_clone_module', $settings, 'ajax_process_clone_module' );
		$this->loader->add_action( 'wp_ajax_dp_dmb_onoff_module', $settings, 'ajax_process_onoff_module' );
		$this->loader->add_action( 'wp_ajax_dp_dmb_off_all_modules', $settings, 'ajax_process_off_all_modules' );
		$this->loader->add_action( 'wp_ajax_dp_dmb_delete_module', $settings, 'ajax_process_delete_module' );
		$this->loader->add_action( 'wp_ajax_dp_dmb_publish_module', $settings, 'ajax_process_publish_module' );
		$this->loader->add_action( 'wp_ajax_dp_dmb_save_extra_settings', $settings, 'ajax_save_settings' );
		/*
		 * Import page related hooks
		 */
		$this->loader->add_action( 'admin_menu', $import, 'add_import_page' );
		/*
		 * Settings page related hooks
		 */
		$this->loader->add_action( 'admin_init', $settings, 'register_setting_page_options' );
		$this->loader->add_action( 'admin_menu', $settings, 'add_settings_page' );
		/*
		 * Multisite related hooks
		 */
		$this->loader->add_action( 'wpmu_new_blog', $plugin_admin, 'multisite_new_blog_recreate_dmb_folder', 10, 1 );
		/*
		 * Clear storage related hooks
		 */
		$this->loader->add_action( 'admin_head', $plugin_admin, 'remove_custom_modules_from_local_storage' );
		/*
		 * Update all modules
		 */
		$this->loader->add_action( 'admin_init', $plugin_admin, 'check_dmb_folder', 1000 );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'trigger_update_all_modules', 1001 );
		/*
		 * Create modules related hooks
		 */
		$this->loader->add_action( 'save_post', $plugin_admin, 'save_custom_module_post', 100, 1 );
		/*
		 * ET related hooks
		 */
		$this->loader->add_action( 'et_builder_ready', $plugin_admin, 'legacy_way_to_load_modules' );
		/*
		 * Debugger file
		 */
		$this->loader->add_action( 'update_option_dp_dmb_debug', $plugin_admin, 'create_delete_dmb_log_file', 10, 2 );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'remove_expired_log', 1002 );
		/*
		 * Head
		 */
		$this->loader->add_action( 'wp_head', $settings, 'add_code_to_head' );
		$this->loader->add_action( 'admin_head', $settings, 'add_code_to_head' );
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of WordPress and to define internationalization functionality.
	 *
	 * @return    string    The name of the plugin.
	 * @since     2.0.0
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 * @since     2.0.0
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Register all of the hooks related to the public-facing functionality of the plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new DP_DMB_Public( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		/*
		 * ET related hooks
		 */
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'legacy_way_to_register_styles_and_scripts' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    2.0.0
	 */
	public function run() {
		if ( file_exists( DPDMB_PLUGIN_DIR . '/vendor/cmb2/init.php' ) ) {
			require_once DPDMB_PLUGIN_DIR . '/vendor/cmb2/init.php';
			$this->loader->run();
		} else {
			add_action( 'admin_notices', array(
				$this,
				'notice_cmb2_dependency_missing'
			) );
		}
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return    DP_Divi_Module_Builder_Loader    Orchestrates the hooks of the plugin.
	 * @since     2.0.0
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Admin notice for CMB2 dependency missing
	 *
	 * @since     2.0.0
	 */
	public function notice_cmb2_dependency_missing() {
		echo et_core_intentionally_unescaped( sprintf( '<div class="notice notice-error is-dismissible"><p>%1$s</p></div>', __( 'CMB2 dependency is missing. Divi Module Builder can not work without this dependency. Contact with our support team at support@diviplugins.com.', 'dp_divi_module_builder' ) ), 'html' );
	}

}
