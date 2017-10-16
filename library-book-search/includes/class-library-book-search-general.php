<?php

/**
 * The file that defines the core plugin class
 * Front side of the site and the admin area. 
 */

class Library_Book_Search_General {
    /**
     * Define the core functionality of the plugin.   
     */
    public function __construct() {
        $this->load_core_files();        
    }

    /**
     * Load the required files for this plugin.    
     */
    private function load_core_files() {
        
        /**
         * Register all of the hooks related to the admin area functionality
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'backend/class-library-book-search-backend.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'backend/class-backend-core-main.php';

        /**
         * Register all of the hooks related to the front side functionality         
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'frontend/class-library-book-search-front.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'frontend/class-shoertcode-init-frontend-main.php';

        //$this->loader = new Get_Post_Custom_Taxonomy_Term_Shortcode_Loader();
    }

}
$plugin = new Library_Book_Search_General();