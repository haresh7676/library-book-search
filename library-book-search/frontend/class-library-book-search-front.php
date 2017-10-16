<?php

/**
 * The frontside functionality of the plugin.
 * 
 */

class Library_Book_Search_Forntside_Hook {

    public function __construct() {                
        add_action( 'wp_enqueue_scripts', array(&$this,'enqueue_styles'));
        add_action( 'wp_enqueue_scripts', array(&$this,'enqueue_scripts'));
        add_shortcode('library_book_search', array(&$this,'book_listing'));
        add_action( 'wp_ajax_load_my_books',array(&$this,'load_my_books'));
        add_action( 'wp_ajax_nopriv_load_my_books', array(&$this,'load_my_books' ));
        add_filter( 'the_content',  array(&$this,'output_book_content_before_content'));
    }

    /**
     * Register the stylesheets for the front side of the site.
     *     
     */
    public function enqueue_styles() {
        global $post;            
        if( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'library_book_search') ):
            wp_enqueue_style('lbs-style-jquery-ui', plugin_dir_url(__FILE__) . 'css/jquery-ui.css', array(),'1.0.0', 'all');            
        endif;
            wp_enqueue_style('lbs-style-custom', plugin_dir_url(__FILE__) . 'css/library-book-search-custom.css', array(),'1.0.0', 'all');

    }

    /**
     * Register the JavaScript for the front side of the site.
     *
     */
    public function enqueue_scripts() {  

        global $post;
        wp_deregister_script('jquery');
        wp_register_script('jquery', plugin_dir_url(__FILE__) . "js/jquery.js", false, null);
        wp_enqueue_script('jquery');

        if( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'library_book_search') ):
            wp_enqueue_script('lbs-jquery-ui', plugin_dir_url(__FILE__) . 'js/jquery-ui.js',array(),false,false);
            wp_enqueue_script('lb-script-custom', plugin_dir_url(__FILE__) . 'js/library-book-search-custom-sctipt.js', array('jquery'),'1.0.0', false);
            wp_localize_script( 'lb-script-custom', 'lbs_ajax_object', array('ajaxurl' => admin_url( 'admin-ajax.php' ), 'plugin_dirurl' => plugin_dir_url(__FILE__)));   
        endif;
       
    }

     /**
     * Add short code function here.
     *
     */
    public function book_listing($atts) {
        ob_start();
        book_search_form('yes');
        echo '<table class = "table table-striped table-book-list no-margin">';
        echo '<thead>
            <tr class="table-header">
            <th width="60" valign="middle" align="left">No</th>
            <th valign="middle" align="left">Book Name</th>
            <th valign="middle" align="left">Price</th>
            <th valign="middle" align="left">Author</th>
            <th valign="middle" align="left">Publisher</th>
            <th width="90" valign="middle" align="left">Rating</th>
            </tr>
            <input type="hidden" class="inputfilter">
            </thead>';   
        echo '</table>';
        echo '<div class = "lbs_pag_loading no-padding">
                <div class = "lbs_universal_container">
                    <div class="lbs-universal-content"></div>
                </div>
            </div>';

        $post_data = ob_get_clean();
        return $post_data;
    }

    /**
     * Ajax call for load books data.
     *
     */
    function load_my_books() {
           
        global $wpdb;       
        $msg = '';       
        if( isset( $_POST['data']['page'] ) ){           
            $post_type_name = 'book';
            $page = sanitize_text_field($_POST['data']['page']); // The page we are currently at            
            $cur_page = $page;            
            $per_page = get_option( 'posts_per_page' ); // Number of items to display per page
            $previous_btn = true;
            $next_btn = true;
            $first_btn = false;
            $last_btn = false;
            $start = $page * $per_page;

            $PostListArgs = array(
                'post_type' => $post_type_name,
                'orderby' => "title",            
                'posts_per_page' => $per_page,
                'paged' => $page
            );
            if( isset( $_POST['data']['sorting_data'] ) ){
                parse_str($_POST['data']['sorting_data'], $searcharray);
                if (!empty($searcharray)) {
                    $bookname = (isset($searcharray['bookname'])) ? $searcharray['bookname'] : '' ;
                    $taxonomy_author = (isset($searcharray['authorname'])) ? $searcharray['authorname'] : '' ;
                    $taxonomy_publisher = (isset($searcharray['publisher'])) ? $searcharray['publisher'] : '' ;
                    $rating = (isset($searcharray['rating']) && $searcharray['rating'] != 0) ? $searcharray['rating'] : '' ;
                    $price = (isset($searcharray['price'])) ? $searcharray['price'] : '' ;
                    if (!empty($price)) {
                        $book_price = explode('-', $price);
                        $minprice = preg_replace("/[^0-9,.]/", "", $book_price[0]);
                        $maxprice = preg_replace("/[^0-9,.]/", "", $book_price[1]);
                        $maxprice = ($maxprice == 3000) ? 99999999 : $maxprice;
                    }
                   
                    if (isset($bookname) && !empty($bookname)) {
                        $PostListArgs['search_book_title'] = $bookname;
                    }
                    if (!empty($taxonomy_author) && !empty($taxonomy_publisher)) {
                        $PostListArgs['tax_query'] = array(
                            'relation' => 'AND',
                            array(
                                'taxonomy' => 'author',
                                'field'    => 'name',
                                'terms'    => $taxonomy_author,
                            ),
                            array(
                                'taxonomy' => 'publisher',
                                'field'    => 'slug',
                                'terms'    => $taxonomy_publisher,
                            ));
                    }
                    if (!empty($taxonomy_author) && empty($taxonomy_publisher)) {
                        $PostListArgs['tax_query'] = array(
                            array(
                                'taxonomy' => 'author',
                                'field'    => 'name',
                                'terms'    => $taxonomy_author,
                            ));
                    }
                    if (empty($taxonomy_author) && !empty($taxonomy_publisher)) {
                        $PostListArgs['tax_query'] = array(
                            array(
                                'taxonomy' => 'publisher',
                                'field'    => 'slug',
                                'terms'    => $taxonomy_publisher,
                            ));
                    }
                    if (!empty($rating) && !empty($price)) {
                        $PostListArgs['meta_query'] = array(
                            'relation'      => 'AND',
                            array(
                                'key'       => 'book_price',
                                'value'     => array($minprice, $maxprice),
                                'type'    => 'numeric',
                                'compare'   => 'BETWEEN',
                            ),
                            array(
                                'key'       => 'book_star_rating',
                                'value'     => $rating,
                                'compare'   => '=',
                            )
                        );
                    }
                    if (!empty($rating) && empty($price)) {
                        $PostListArgs['meta_query'] = array(                            
                            array(
                                'key'       => 'book_star_rating',
                                'value'     => $rating,
                                'compare'   => '=',
                            )
                        );
                    }
                    if (empty($rating) && !empty($price)) {
                        $PostListArgs['meta_query'] = array(                            
                            array(
                                'key'       => 'book_price',
                                'value'     => array($minprice, $maxprice),
                                'type'    => 'numeric',
                                'compare'   => 'BETWEEN',
                            )
                        );
                    }                          
                }
            }

            add_filter( 'posts_where', 'title_filter', 10, 2 );
            $my_query = new WP_Query($PostListArgs);  
            remove_filter( 'posts_where', 'title_filter', 10, 2 );
            $no_of_paginations = $my_query->max_num_pages;                
            
            // Check if our query returns anything.
            if( $my_query->have_posts() ):                
                    $msg .='<table class ="table table-striped table-hover table-file-list no-margin">';      
                    $bookitem = ($page == 1) ? 0 : $per_page*($page-1);
                    while ($my_query->have_posts()) : $my_query->the_post();
                        $bookitem++;
                        $ratingvalue = book_price_and_star_rating_get_meta( 'book_star_rating' );
                        $msg .='<tr>';
                        $msg .='<td width="60" valign="middle" align="left">'. $bookitem .'</td>';
                        $msg .='<td valign="middle" align="left">' . '<a href="' . get_permalink() . '">' . get_the_title() . '</a></td>';
                        $msg .='<td valign="middle" align="left">$'.book_price_and_star_rating_get_meta( 'book_price' ).'</td>';
                        $msg .='<td valign="middle" align="left">'.GetTermslistByID(get_the_ID(), 'book-author').'</td>';
                        $msg .='<td valign="middle" align="left">'.GetTermslistByID(get_the_ID(), 'publisher').'</td>';
                        $msg .='<td width="90" valign="middle" align="left"><span class="rating-static star-'.$ratingvalue.'""></span></td>';
                        $msg .='</tr>';
                    endwhile;
                    $msg .='</table>';
            // If the query returns nothing, we throw an error message
            else:
                $msg .= '<p class = "bg-danger">No books matching your search criteria were found.</p>';               
            endif;

            $msg = "<div class='lbs-universal-content'>" . $msg . "</div><br class = 'clear' />"; 

            if ($cur_page >= 7) {
                $start_loop = $cur_page - 3;
                if ($no_of_paginations > $cur_page + 3)
                    $end_loop = $cur_page + 3;
                else if ($cur_page <= $no_of_paginations && $cur_page > $no_of_paginations - 6) {
                    $start_loop = $no_of_paginations - 6;
                    $end_loop = $no_of_paginations;
                } else {
                    $end_loop = $no_of_paginations;
                }
            } else {
                $start_loop = 1;
                if ($no_of_paginations > 7)
                    $end_loop = 7;
                else
                    $end_loop = $no_of_paginations;
            }
             
            $pag_container .= "
            <div class='lbs-universal-pagination'>
                <ul>";

            if ($first_btn && $cur_page > 1) {
                $pag_container .= "<li p='1' class='active'>First</li>";
            } else if ($first_btn) {
                $pag_container .= "<li p='1' class='inactive'>First</li>";
            }

            if ($previous_btn && $cur_page > 1) {
                $pre = $cur_page - 1;
                $pag_container .= "<li p='$pre' class='active'>Previous</li>";
            } else if ($previous_btn) {
                $pag_container .= "<li class='inactive'>Previous</li>";
            }
            for ($i = $start_loop; $i <= $end_loop; $i++) {

                if ($cur_page == $i)
                    $pag_container .= "<li p='$i' class = 'selected' >{$i}</li>";
                else
                    $pag_container .= "<li p='$i' class='active'>{$i}</li>";
            }
           
            if ($next_btn && $cur_page < $no_of_paginations) {
                $nex = $cur_page + 1;
                $pag_container .= "<li p='$nex' class='active'>Next</li>";
            } else if ($next_btn) {
                $pag_container .= "<li class='inactive'>Next</li>";
            }

            if ($last_btn && $cur_page < $no_of_paginations) {
                $pag_container .= "<li p='$no_of_paginations' class='active'>Last</li>";
            } else if ($last_btn) {
                $pag_container .= "<li p='$no_of_paginations' class='inactive'>Last</li>";
            }

            $pag_container = $pag_container . "
                </ul>
            </div>";
           
            echo '<div class = "lbs-pagination-content">' . $msg . '</div>' .
            '<div class = "lbs-pagination-nav">' . $pag_container . '</div>';
             wp_reset_postdata(); 
           
        }
       
        exit();
       
    }

    /**
     * Add book data in single post file
     *
     */
    public function output_book_content_before_content( $content ) {
            $output_content = '';
        if ( is_singular( 'book' ) ) {
            $bookid = get_the_ID();
            $ratingvalue = book_price_and_star_rating_get_meta( 'book_star_rating' );
            $output_content .= '<div class="book-meta">';
            $output_content .= '<div class="authorname">by '.GetTermslistByID($bookid,'book-author',true).'</div>';
            $output_content .= '<div class="publishername">Published by '.GetTermslistByID($bookid,'publisher',true).'</div>';
            $output_content .= '<div class="bookprice">$'.book_price_and_star_rating_get_meta( 'book_price' ).'<span class="rating-static star-'.$ratingvalue.'""></span></div>';
            $output_content .= '</div>';            
        }
        $output_content .= $content;
        return $output_content;
    }        
}
new Library_Book_Search_Forntside_Hook();