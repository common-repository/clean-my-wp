<?php
class Clean_My_Wordpress_Tools {

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
     * add_submenu
     *
     * @return void
     */
    public function add_submenu() {
        add_submenu_page(
            'tools.php',
            __( '🛠️ Clean My WP', 'clean-my-wordpress' ),
            __( '🛠️ Clean My WP', 'clean-my-wordpress' ),
            'manage_options',
            'clean-my-wordpress',
            array( $this, 'display_submenu' )
        );
    }
    		
	/**
	 * get_icons_urls
	 *
	 * @return void
	 */
	public function get_icons_urls(){
		$file_icons_relative_path = 'public/assets/images/folders-explorer/file-icons/';
		$files_icons_urls = array();
		// scan the folder
		$files = scandir( CLEAN_MY_WORDPRESS_PLUGIN_PATH . $file_icons_relative_path );
		$files = array_diff( $files, array( '.', '..' ) );
		foreach( $files as $key => $file ){
			// exemple css.png php.png
			$type = explode( '.', $file );
			$type = $type[0];
			$files_icons_urls[$type] = CLEAN_MY_WORDPRESS_PLUGIN_URL . $file_icons_relative_path . $file;
		}

		$folder_icons_relative_path = 'public/assets/images/folders-explorer/folder-icons/';
		$folders_icons_urls = array();
		// scan the folder
		$files = scandir( CLEAN_MY_WORDPRESS_PLUGIN_PATH . $folder_icons_relative_path );
		$files = array_diff( $files, array( '.', '..' ) );
		foreach( $files as $key => $file ){
			$type = explode( '.', $file );
			$type = $type[0];
			$folders_icons_urls[$type] = CLEAN_MY_WORDPRESS_PLUGIN_URL . $folder_icons_relative_path . $file;
		}

		return array(
			'files'   => $files_icons_urls,
			'folders' => $folders_icons_urls,
		);		
	}

    /**
     * display_submenu
     *
     * @return void
     */
    public function display_submenu() {	

        $assets_data = include( CLEAN_MY_WORDPRESS_PLUGIN_PATH . 'public/assets/build/page-tools.asset.php' );
        $version = $assets_data['version'] ?? $this->version;
        
        $vuejs = CLEAN_MY_WORDPRESS_DEV_MOD || ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? 'vue.js' : 'vue.min.js';
        wp_enqueue_script( 'clean-my-wordpress-d3-v4', CLEAN_MY_WORDPRESS_PLUGIN_URL . 'public/assets/d3/d3.v4.min.js', array(), $version, false );
        wp_enqueue_script( 'clean-my-wordpress-vuejs', CLEAN_MY_WORDPRESS_PLUGIN_URL . 'public/assets/vuejs/' . $vuejs, array(), $version, false );
		wp_enqueue_script( 'clean-my-wordpress-page-tools-js', CLEAN_MY_WORDPRESS_PLUGIN_URL . 'public/assets/build/page-tools.js', array(), $version, true );
		wp_localize_script( 'clean-my-wordpress-page-tools-js', 'php', array(
			'ajax_url' 		 => admin_url( 'admin-ajax.php' ),
			'nonce'    		 => wp_create_nonce( 'clean_my_wordpress_page_tools' ),
			'wp_root'  		 => rtrim( ABSPATH, '/' ),
			'parent_wp_root' => rtrim( dirname( ABSPATH ), '/' ),
			'icons' 		 => $this->get_icons_urls(),
		) );
		wp_localize_script( 'clean-my-wordpress-page-tools-js', 'i18n', array(
			'Home' 			=> __( 'Home', 'clean-my-wordpress' ),
			'Disk Explorer' => __( 'Disk Explorer', 'clean-my-wordpress' ),
			'Are you sure you want to delete this file ?' => __( 'Are you sure you want to delete this file ?', 'clean-my-wordpress' ),
			'Are you sure you want to delete this folder and all its content ?' => __( 'Are you sure you want to delete this folder and all its content ?', 'clean-my-wordpress' ),
		) );
		wp_enqueue_style( 'clean-my-wordpress-page-tools-css', CLEAN_MY_WORDPRESS_PLUGIN_URL . 'public/assets/build/page-tools.css', array(), $version );
		
        require_once CLEAN_MY_WORDPRESS_PLUGIN_PATH . 'admin/templates/page-tools.php';
    }
	
	/**
	 * ajax_get_folder_data
	 *
	 * @return void
	 */
	public function ajax_get_folder_data(){
		if( ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'clean_my_wordpress_page_tools' ) ){
			wp_send_json_error();
		}
		
		if( ! isset( $_POST['path'] ) ){
			wp_send_json_error( array(
					'message' => __( 'No path provided', 'clean-my-wordpress' ),
			));
		}

		$wp_root = rtrim( ABSPATH, '/' );
		
		$path = sanitize_text_field( $_POST['path'] );

		// prevent access to parent folders of root
		if( strpos( $path, $wp_root ) === false ){
			wp_send_json_error( array(
					'message' => __( 'Invalid path', 'clean-my-wordpress' ),
			));
		}

		$files = scandir( $path );
		$files = array_diff( $files, array( '.', '..' ) );

		$output_files = array();

		foreach( $files as $key => $file ){
			$file_path = $path . '/' . $file;
			if( is_dir( $file_path ) ){
				$file_size = 0;
				// TODO: Voir si on peut remettre cette fonction:
				// if( function_exists( 'shell_exec' ) ){
				// 	$result = shell_exec('du -sh "'. $file_path .'"');
				// 	// get with regex number and letter two groups (ex: 1,2M or 1,2G)
				// 	$result = preg_match( '/([0-9,\.]+)([A-Z]+)/', $result, $matches );
				// 	$file_size = floatval( str_replace( ',', '.', $matches[1] ) );
				// 	switch($matches[2]){
				// 		case 'K':
				// 			$file_size = $file_size * 1024;
				// 			break;
				// 		case 'M':
				// 			$file_size = $file_size * 1024 * 1024;
				// 			break;
				// 		case 'G':
				// 			$file_size = $file_size * 1024 * 1024 * 1024;
				// 			break;
				// 		case 'T':
				// 			$file_size = $file_size * 1024 * 1024 * 1024 * 1024;
				// 			break;
				// 		default:
				// 			break;
				// 	}
				// } else { 
					$iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $file_path ) );
					foreach( $iterator as $entry ){
						$file_size += $entry->getSize();
					}
				// }
			} else {
				$file_size = filesize( $file_path );
			}
			$output_files[] = array(
				'name' 	 => $file,
				'path' 	 => $file_path,
				'size' 	 => $file_size,
				'is_dir' => is_dir( $file_path ),
				'matches' => $matches ?? array(),
			);
		}

		wp_send_json_success( array(
			'name'  		=> basename( $path ),
			'path'  		=> $path,
			'parent_folder' => dirname( $path ),
			'files' 		=> $output_files,
		) );
	}
	
	/**
	 * ajax_delete_file
	 *
	 * @return void
	 */
	public function ajax_delete_file(){
		if( ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'clean_my_wordpress_page_tools' ) ){
			wp_send_json_error();
		}
		
		if( ! isset( $_POST['path'] ) ){
			wp_send_json_error( array(
					'message' => __( 'No path provided', 'clean-my-wordpress' ),
			));
		}

		$path = sanitize_text_field( $_POST['path'] );

		if( strpos( $path, ABSPATH ) === false ){
			wp_send_json_error( array(
					'message' => __( 'Invalid path', 'clean-my-wordpress' ),
			));
		}

		if( ! file_exists( $path ) ){
			wp_send_json_error( array(
					'message' => __( 'File does not exist', 'clean-my-wordpress' ),
			));
		}

		// delete file or folder
		if( is_dir( $path ) ){
			$success = self::delete_directory( $path );
		} else {
			$success = unlink( $path );
		}

		if( ! $success ){
			wp_send_json_error( array(
					'message' => __( 'Error while deleting file', 'clean-my-wordpress' ),
			));
		}

		wp_send_json_success( array(
			'message' => __( 'File deleted', 'clean-my-wordpress' ),
		) );
	}
	
	/**
	 * delete_directory
	 *
	 * @param  mixed $path
	 * @return void
	 */
	public function delete_directory($path) {
		if (is_dir($path)) {
			$directory_contents = scandir($path);
			foreach ($directory_contents as $item) {
				if ($item != '.' && $item != '..') {
					$item_path = $path . '/' . $item;
					if (is_dir($item_path)) {
						self::delete_directory($item_path);
					} else {
						unlink($item_path);
					}
				}
			}
			rmdir($path);
			return true;
		} else {
			return false;
		}
	}
	
}