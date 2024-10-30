<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://webdeclic.com/
 * @since      1.0.0
 *
 * @package    Clean_My_Wordpress
 * @subpackage Clean_My_Wordpress/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Clean_My_Wordpress
 * @subpackage Clean_My_Wordpress/includes
 * @author     Webdeclic <contact@webdeclic.com>
 */
class Clean_My_Wordpress {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Clean_My_Wordpress_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'CLEAN_MY_WORDPRESS_VERSION' ) ) {
			$this->version = CLEAN_MY_WORDPRESS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'clean-my-wordpress';

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
	 * - Clean_My_Wordpress_Loader. Orchestrates the hooks of the plugin.
	 * - Clean_My_Wordpress_i18n. Defines internationalization functionality.
	 * - Clean_My_Wordpress_Admin. Defines all hooks for the admin area.
	 * - Clean_My_Wordpress_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for loading composer dependencies.
		 */
		require_once CLEAN_MY_WORDPRESS_PLUGIN_PATH . 'includes/vendor/autoload.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once CLEAN_MY_WORDPRESS_PLUGIN_PATH . 'includes/class-clean-my-wordpress-loader.php';

		/**
		 * This file is loaded only on local environement for test or debug.
		 */
		if( $_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['REMOTE_ADDR'] == '::1' ){
			require_once CLEAN_MY_WORDPRESS_PLUGIN_PATH. 'includes/dev-toolkits.php';
		}
		
		/**
		 * The global functions for this plugin
		 */
		require_once CLEAN_MY_WORDPRESS_PLUGIN_PATH . 'includes/global-functions.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once CLEAN_MY_WORDPRESS_PLUGIN_PATH . 'includes/class-clean-my-wordpress-i18n.php';
		
		/**
		 * The class responsible of cron job.
		 */
		require_once CLEAN_MY_WORDPRESS_PLUGIN_PATH . 'admin/class-cron-job.php';

		/**
		 * The class responsible of admin.
		 */
		require_once CLEAN_MY_WORDPRESS_PLUGIN_PATH . 'admin/class-tools.php';

		/**
		 * The class responsible of shortcodes.
		 */
		require_once CLEAN_MY_WORDPRESS_PLUGIN_PATH . 'public/class-shortcodes.php';

		$this->loader = new Clean_My_Wordpress_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Clean_My_Wordpress_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Clean_My_Wordpress_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$clean_my_wordpress_cron_job = new Clean_My_Wordpress_Cron_Job( $this->get_plugin_name(), $this->get_version() );

		$tools = new Clean_My_Wordpress_Tools( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_menu', $tools, 'add_submenu' );
		$this->loader->add_action( 'wp_ajax_cmwp_get_folder_data', $tools, 'ajax_get_folder_data' );
		$this->loader->add_action( 'wp_ajax_cmwp_delete_file', $tools, 'ajax_delete_file' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$clean_my_wordpress_shortcodes = new Clean_My_Wordpress_Shortcodes( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'init', $clean_my_wordpress_shortcodes, 'add_shortcodes' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Clean_My_Wordpress_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
