<?php
/**
 * The admin function of the plugin.
 */

class Library_Book_Search_Backend_Hook {  
    /**
     * Initialize the class and set its properties.
     *    
     */
    public function __construct() {

        add_action( 'init', array(&$this,'book_post_type_with_taxonomy')); // Initialize post type and taxonomy
        add_action( 'add_meta_boxes', array(&$this,'book_price_and_star_rating_add_meta_box')); // Add meta box for custom fields.
        add_action( 'save_post', array(&$this,'book_price_and_star_rating_save')); // Sotre meta box custom fields data.
        add_action( 'admin_menu', array(&$this,'library_book_search_shortcode_page')); // Add page for listing shortcode

    }

    
    public function book_post_type_with_taxonomy() {
        /**
         * Register custom post type for book         
         */
        $post_labels = array(
            'name'                  => _x( 'Books', 'Post Type General Name', 'library_book' ),
            'singular_name'         => _x( 'Book', 'Post Type Singular Name', 'library_book' ),
            'menu_name'             => __( 'Book', 'library_book' ),
            'name_admin_bar'        => __( 'Book', 'library_book' ),
            'archives'              => __( 'Book Archives', 'library_book' ),
            'attributes'            => __( 'Book Attributes', 'library_book' ),
            'parent_item_colon'     => __( 'Parent Book:', 'library_book' ),
            'all_items'             => __( 'All Books', 'library_book' ),
            'add_new_item'          => __( 'Add New Book', 'library_book' ),
            'add_new'               => __( 'Add New Book', 'library_book' ),
            'new_item'              => __( 'New book', 'library_book' ),
            'edit_item'             => __( 'Edit book', 'library_book' ),
            'update_item'           => __( 'Update book', 'library_book' ),
            'view_item'             => __( 'View book', 'library_book' ),
            'view_items'            => __( 'View books', 'library_book' ),
            'search_items'          => __( 'Search book', 'library_book' ),
            'not_found'             => __( 'Not found', 'library_book' ),
            'not_found_in_trash'    => __( 'Not found in Trash', 'library_book' ),
            'featured_image'        => __( 'Featured Image', 'library_book' ),
            'set_featured_image'    => __( 'Set featured image', 'library_book' ),
            'remove_featured_image' => __( 'Remove featured image', 'library_book' ),
            'use_featured_image'    => __( 'Use as featured image', 'library_book' ),
            'insert_into_item'      => __( 'Insert into book', 'library_book' ),
            'uploaded_to_this_item' => __( 'Uploaded to this book', 'library_book' ),
            'items_list'            => __( 'books list', 'library_book' ),
            'items_list_navigation' => __( 'books list navigation', 'library_book' ),
            'filter_items_list'     => __( 'Filter books list', 'library_book' ),
        );
        $post_args = array(
            'label'                 => __( 'Book', 'library_book' ),
            'description'           => __( 'Book Description', 'library_book' ),
            'labels'                => $post_labels,
            'supports'              => array( 'title', 'editor', 'thumbnail', ),
            'taxonomies'            => array( 'author', 'publisher' ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'menu_icon'             => 'dashicons-book',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,        
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
        );
        register_post_type( 'book', $post_args );

        /**
         * Register Publisher Taxonomy
         */
        $publisher_labels = array(
            'name'                       => _x( 'Publishers', 'Taxonomy General Name', 'library_book' ),
            'singular_name'              => _x( 'Publisher', 'Taxonomy Singular Name', 'library_book' ),
            'menu_name'                  => __( 'Publisher', 'library_book' ),
            'all_items'                  => __( 'All Publishers', 'library_book' ),
            'parent_item'                => __( 'Parent Publisher', 'library_book' ),
            'parent_item_colon'          => __( 'Parent Publisher:', 'library_book' ),
            'new_item_name'              => __( 'New Publisher Name', 'library_book' ),
            'add_new_item'               => __( 'Add New Publisher', 'library_book' ),
            'edit_item'                  => __( 'Edit Publisher', 'library_book' ),
            'update_item'                => __( 'Update Publisher', 'library_book' ),
            'view_item'                  => __( 'View Item', 'library_book' ),
            'separate_items_with_commas' => __( 'Separate publishers with commas', 'library_book' ),
            'add_or_remove_items'        => __( 'Add or remove publishers', 'library_book' ),
            'choose_from_most_used'      => __( 'Choose from the most used publishers', 'library_book' ),
            'popular_items'              => __( 'Popular Items', 'library_book' ),
            'search_items'               => __( 'Search Publishers', 'library_book' ),
            'not_found'                  => __( 'Not Found', 'library_book' ),
            'no_terms'                   => __( 'No items', 'library_book' ),
            'items_list'                 => __( 'Items list', 'library_book' ),
            'items_list_navigation'      => __( 'Items list navigation', 'library_book' ),
        );
        $publisher_args = array(
            'labels'                     => $publisher_labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
        );
        register_taxonomy( 'publisher', array( 'book' ), $publisher_args );

        /**
         * Register Autor Taxonomy
         */
        $author_labels = array(
            'name'                       => _x( 'Authors', 'Taxonomy General Name', 'library_book' ),
            'singular_name'              => _x( 'Author', 'Taxonomy Singular Name', 'library_book' ),
            'menu_name'                  => __( 'Author', 'library_book' ),
            'all_items'                  => __( 'All Authors', 'library_book' ),
            'parent_item'                => __( 'Parent Author', 'library_book' ),
            'parent_item_colon'          => __( 'Parent Author:', 'library_book' ),
            'new_item_name'              => __( 'New Author Name', 'library_book' ),
            'add_new_item'               => __( 'Add New Author', 'library_book' ),
            'edit_item'                  => __( 'Edit Author', 'library_book' ),
            'update_item'                => __( 'Update Author', 'library_book' ),
            'view_item'                  => __( 'View Item', 'library_book' ),
            'separate_items_with_commas' => __( 'Separate Authors with commas', 'library_book' ),
            'add_or_remove_items'        => __( 'Add or remove Authors', 'library_book' ),
            'choose_from_most_used'      => __( 'Choose from the most used Authors', 'library_book' ),
            'popular_items'              => __( 'Popular Items', 'library_book' ),
            'search_items'               => __( 'Search Authors', 'library_book' ),
            'not_found'                  => __( 'Not Found', 'library_book' ),
            'no_terms'                   => __( 'No items', 'library_book' ),
            'items_list'                 => __( 'Items list', 'library_book' ),
            'items_list_navigation'      => __( 'Items list navigation', 'library_book' ),
        );
        $author_args = array(
            'labels'                     => $author_labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
        );
        register_taxonomy( 'book-author', array( 'book' ), $author_args );

    }    

    /**
    * Add meta box for custom fields.
    */
    public function book_price_and_star_rating_add_meta_box() {

        add_meta_box(
            'book_price_and_star_rating-book-price-and-star-rating',
            __( 'Book Price and Star Rating', 'book_price_and_star_rating' ),
            'book_price_and_star_rating_html',
            'book',
            'side',
            'default'
        );

    }
    
    /**
    * Store meta box custom fields data.
    */
    public function book_price_and_star_rating_save( $post_id ) {

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
        if ( ! isset( $_POST['book_price_and_star_rating_nonce'] ) || ! wp_verify_nonce( $_POST['book_price_and_star_rating_nonce'], '_book_price_and_star_rating_nonce' ) ) return;
        if ( ! current_user_can( 'edit_post', $post_id ) ) return;

        if ( isset( $_POST['book_price'] ) )
            update_post_meta( $post_id, 'book_price', esc_attr( $_POST['book_price'] ) );
        if ( isset( $_POST['book_star_rating'] ) )
            update_post_meta( $post_id, 'book_star_rating', esc_attr( $_POST['book_star_rating'] ) );
    }


    /**
     * Adds a submenu page under a book post type parent.
     */
    public function library_book_search_shortcode_page(){

        add_submenu_page(
        'edit.php?post_type=book',
        __( 'Books Shortcode Reference', 'library_book' ),
        __( 'Shortcode Reference', 'library_book' ),
        'manage_options',
        'books-shortcode-ref',
        array(&$this,'books_ref_page_callback')
    );


     /*   function mt_settings_page() {
        echo "<h2>" . __( 'Test Settings', 'menu-test' ) . "</h2>"; 

        }*/
    }

    /**
     * Display callback for the submenu page.
     */
    public function books_ref_page_callback() { 
        ?>
        <div class="wrap">
            <h1><?php _e( 'Books Shortcode Reference', 'library_book' ); ?></h1>
            <p><?php _e( 'Copy below shortcode and paste it into your post, page content', 'library_book' ); ?></p>
            <p>Shortcode: <code>[library_book_search]</code></p>
        </div>
        <?php
    }
    
}
new Library_Book_Search_Backend_Hook();