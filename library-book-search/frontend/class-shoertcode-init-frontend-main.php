<?php
if (!function_exists('book_search_form')):
    function book_search_form($filter) {
        if ($filter == 'yes') {
            ?>
            <div class="row lbs-form">
            <h3>Book Search</h3>            
            <form class="sorting">
                <div class="flex">
                    <div class="fx-col">
                        <label for="Book name">Book Name: </label>
                        <input type="text" name="bookname" class="input-field">        
                    </div>
                    <div class="fx-col">
                        <label for="Author">Author: </label>
                        <input type="text" name="authorname" class="input-field">
                    </div>
                </div>
                <div class="flex">
                    <div class="fx-col">
                        <label>Publisher: </label>
                        <?php 
                        $publisher_terms = get_terms( 'publisher', array('hide_empty' => 1));
                         if ( ! empty( $publisher_terms ) && ! is_wp_error( $publisher_terms ) ){
                             echo '<select name="publisher" class="selectmenu-dropdown">';
                             echo '<option value="" selected="selected">Select Publisher</option>';
                             foreach ( $publisher_terms as $term ) {
                               echo '<option value="'. $term->slug .'">' . $term->name . '</option>';                           
                             }
                             echo '</select>';
                         }
                        ?>                          
                    </div>
                    <div class="fx-col">
                        <label>Rating: </label>                        
                        <?php 
                        echo '<select name="rating" class="selectmenu-dropdown">';
                        for( $rating=0; $rating<=5; $rating++ )
                        {
                            echo '<option value="'. $rating .'">' .(($rating == 0) ? 'Search By Rating' : $rating). '</option>';              
                        }
                        echo '</select>';
                        ?>
                    </div>
                </div>
                <div class="flex">
                    <div class="fx-col">
                        <label for="amount">Price range:</label>  
                            <div class="input-field price-box">                     
                                <input type="text" name="price" class="amount" readonly style="border:0; color:#f6931f; font-weight:bold;">        
                                <div class="slider-range"></div>
                            </div>
                    </div>   
                    <div class="fx-col">
                    </div>                 
                </div>
                <div class="flex">
                    <div class="fx-col">
                        <input type="submit" name="search" value="Search">
                    </div>
                </div>
            </form>
            <div id="formparamiter"></div>
        </div>
            <?php
        }
    }
endif;

if (!function_exists('GetTermslistByID')):
    function GetTermslistByID($bookid,$texonomy_name,$link = false)
    {
        $terms = get_the_terms($bookid,$texonomy_name);                         
        if ( $terms && ! is_wp_error( $terms ) ) :          
            $terms_links = array();
            foreach ( $terms as $term ) {                            
                $terms_links[] = ($link == true) ? '<a href="'.get_term_link($term->slug, $texonomy_name).'">'.$term->name.'</a>' : $term->name;
            }                                 
            $terms_result = join( ", ", $terms_links );
            return $terms_result;
        endif; 
    }
endif;

if (!function_exists('title_filter')):
    function title_filter( $where, &$wp_query )
    {
        global $wpdb;
        if ( $search_title = $wp_query->get( 'search_book_title' ) ) {
            $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( like_escape( $search_title ) ) . '%\'';
        }
        return $where;
    }
endif;
