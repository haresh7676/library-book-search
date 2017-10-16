<?php 
/*
 * Plugin Name: Library​ ​Book​ ​Search
 * Plugin URI: http://www.xyz.com/
 * Description: Library Book Search can manage based on library book search which will be based on book name, author, publisher, price, book rating.
 * Author: Haresh Valiya
 * Version: 1.1
 * Author URI: http://www.xyz.com/
*/
// Protect their plugins from direct access.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The function runs during plugin activate. 
 */
function activate_library_book_search() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-library-book-search-activate.php';	
	class_Book_Search_Activate::activate();
}

/**
 * The function runs during plugin activate deactivate. 
 */
function deactivate_library_book_search() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-library-book-search-deactivate.php';
	class_Book_Search_Deactivate::deactivate();
}

/**
 * register hook for plugins activate and deactivate.
 */
register_activation_hook( __FILE__, 'activate_library_book_search' );
register_deactivation_hook( __FILE__, 'deactivate_library_book_search' );

/**
 * The core plugin class that is used to admin hooks, and front site hooks internationalization, 
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-library-book-search-general.php';