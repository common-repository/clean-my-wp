<?php

/**
 * Add shortcodes for Plugin Name.
 *
 * @link       https://webdeclic.com/
 * @since      1.0.0
 *
 * @package    Clean_My_Wordpress
 * @subpackage Clean_My_Wordpress/public
 */
class Clean_My_Wordpress_Shortcodes {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}
	
	/**
	 * add_shrortcodes
	 *
	 * @since    1.0.0
	 * @return void
	 */
	public function add_shortcodes() {
		add_shortcode( 'clean_my_wordpress_informations', array( $this, 'shortcode_clean_my_wordpress_informations' ) );
	}
	
	/**
	 * shortcode_clean_my_wordpress_informations
	 * 
	 * @since    1.0.0
	 * @param  mixed $atts
	 * @return void
	 */
	public function shortcode_clean_my_wordpress_informations() {
		return 'Clean My WP v' . CLEAN_MY_WORDPRESS_VERSION;
	}
}
