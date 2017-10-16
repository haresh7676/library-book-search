<?php
function book_price_and_star_rating_html( $post) {

    wp_nonce_field( '_book_price_and_star_rating_nonce', 'book_price_and_star_rating_nonce' ); ?>
    <p>
        <label for="book_price"><?php _e( 'Price', 'book_price_and_star_rating' ); ?></label><br>
        <input type="number" name="book_price" id="book_price" value="<?php echo (!empty(book_price_and_star_rating_get_meta( 'book_price' ))) ? book_price_and_star_rating_get_meta( 'book_price' ) : 0 ; ?>" min="0">
    </p>    <p>
        <label for="book_star_rating"><?php _e( 'Star Rating', 'book_price_and_star_rating' ); ?></label><br>
        <select name="book_star_rating" id="book_star_rating">
            <option <?php echo (book_price_and_star_rating_get_meta( 'book_star_rating' ) === '0' ) ? 'selected' : '' ?> value="0">Select Rating</option>
            <option <?php echo (book_price_and_star_rating_get_meta( 'book_star_rating' ) === '1' ) ? 'selected' : '' ?>>1</option>
            <option <?php echo (book_price_and_star_rating_get_meta( 'book_star_rating' ) === '2' ) ? 'selected' : '' ?>>2</option>
            <option <?php echo (book_price_and_star_rating_get_meta( 'book_star_rating' ) === '3' ) ? 'selected' : '' ?>>3</option>
            <option <?php echo (book_price_and_star_rating_get_meta( 'book_star_rating' ) === '4' ) ? 'selected' : '' ?>>4</option>
            <option <?php echo (book_price_and_star_rating_get_meta( 'book_star_rating' ) === '5' ) ? 'selected' : '' ?>>5</option>
        </select>
    </p><?php
}

/*
Usage: book_price_and_star_rating_get_meta( 'book_price' )
Usage: book_price_and_star_rating_get_meta( 'book_star_rating' )
*/
function book_price_and_star_rating_get_meta( $value ) {
    global $post;
    $field = get_post_meta( $post->ID, $value, true );
    if ( ! empty( $field ) ||  $field == 0) {
        return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
    } else {
        return false;
    }
}