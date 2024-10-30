<?php

/**
 *
 * @link              https://webdeclic.com/
 * @since             1.0.0
 * @package           Clean_My_Wordpress
 *
 * @wordpress-plugin
 * Plugin Name:       Clean My WP
 * Plugin URI:        https://webdeclic.com/clean-my-wordpress
 * Description:       This plugin allows you to better monitor and maintain the space used by your WordPress.
 * Version:           1.0.0
 * Author:            Webdeclic
 * Author URI:        https://webdeclic.com/
 * License:           GPL-2.0+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       clean-my-wordpress
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Check if your are in local or production environment
 */
$is_local = $_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['REMOTE_ADDR'] == '::1';

/**
 * If you are in local environment, you can use the version number as a timestamp for better cache management in your browser
 */
$version  = $is_local ? time() : '1.0.0';

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'CLEAN_MY_WORDPRESS_VERSION', $version );

/**
 * You can use this const for check if you are in local environment
 */
define( 'CLEAN_MY_WORDPRESS_DEV_MOD', $is_local );

/**
 * Plugin Name text domain for internationalization.
 */
define( 'CLEAN_MY_WORDPRESS_TEXT_DOMAIN', 'clean-my-wordpress' );

/**
 * Plugin Name Path for plugin includes.
 */
define( 'CLEAN_MY_WORDPRESS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Plugin Name URL for plugin sources (css, js, images etc...).
 */
define( 'CLEAN_MY_WORDPRESS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-clean-my-wordpress-activator.php
 */
register_activation_hook( __FILE__, function(){
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-clean-my-wordpress-activator.php';
	Clean_My_Wordpress_Activator::activate();
} );

register_deactivation_hook( __FILE__, function(){
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-clean-my-wordpress-deactivator.php';
	Clean_My_Wordpress_Deactivator::deactivate();
} );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-clean-my-wordpress.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_clean_my_wordpress() {

	$plugin = new Clean_My_Wordpress();
	$plugin->run();

}
run_clean_my_wordpress();
