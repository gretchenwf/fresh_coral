<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Fresh Coral
 */

if ( ! function_exists( 'fresh_coral_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function fresh_coral_posted_on() {
	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
	}
	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() )
	);

	$posted_on = sprintf(
		esc_html_x( '%s', 'post date', 'fresh_coral' ),
		'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
	);

	echo '<span class="posted-on">' . $posted_on . '</span>'; // WPCS: XSS OK.

}
endif;

if ( ! function_exists( 'fresh_coral_entry_footer' ) ) :
/**
 * Prints HTML with meta information for the categories, tags and comments.
 */
function fresh_coral_entry_footer() {
	// Hide category and tag text for pages.
	if ( 'post' === get_post_type() ) {
		/* translators: used between list items, there is a space after the comma */
		$categories_list = get_the_category_list( esc_html__( ', ', 'fresh_coral' ) );
		if ( $categories_list && fresh_coral_categorized_blog() ) {
			printf( '<span class="cat-links">' . esc_html__( 'Posted in %1$s', 'fresh_coral' ) . '</span>', $categories_list ); // WPCS: XSS OK.
		}

		/* translators: used between list items, there is a space after the comma */
		$tags_list = get_the_tag_list( '', esc_html__( ', ', 'fresh_coral' ) );
		if ( $tags_list ) {
			printf( '<span class="tags-links">' . esc_html__( 'Tagged %1$s', 'fresh_coral' ) . '</span>', $tags_list ); // WPCS: XSS OK.
		}
	}

	if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
		echo '<span class="comments-link">';
		comments_popup_link( esc_html__( 'Leave a comment', 'fresh_coral' ), esc_html__( '1 Comment', 'fresh_coral' ), esc_html__( '% Comments', 'fresh_coral' ) );
		echo '</span>';
	}

	edit_post_link(
		sprintf(
			/* translators: %s: Name of current post */
			esc_html__( 'Edit %s', 'fresh_coral' ),
			the_title( '<span class="screen-reader-text">"', '"</span>', false )
		),
		'<span class="edit-link">',
		'</span>'
	);
}
endif;

/**
 * Customize Read More link
 */
function fresh_coral_modify_read_more_link() {
	$read_more_link = sprintf(
		/* translators: %s: Name of current post. */
		wp_kses( __( 'Read more%s', 'fresh_coral' ), array( 'span' => array( 'class' => array() ) ) ),
		the_title( ' <span class="screen-reader-text">"', '"</span>', false )
	);
	$read_more_string =
	'<div class="continue-reading">
		<a href="' . get_permalink() . '" rel="bookmark" class="btn btn-danger" role="button">' . $read_more_link . '<i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i></a>
	</div>';
	return $read_more_string;
}
add_filter( 'the_content_more_link', 'fresh_coral_modify_read_more_link' );
/**
 * Return nothing on excerpt_more
 */
function fresh_coral_excerpt_more( $more ) {
	return '';
}
add_filter('excerpt_more', 'fresh_coral_excerpt_more');

function fresh_coral_the_excerpt( $excerpt ) {
    global $post;

    // Save the link in a variable
		$read_more_link = sprintf(
			/* translators: %s: Name of current post. */
			wp_kses( __( 'Read more%s', 'fresh_coral' ), array( 'span' => array( 'class' => array() ) ) ),
			the_title( ' <span class="screen-reader-text">"', '"</span>', false )
		);
    $link = 	'<div class="continue-reading">
				<a href="' . get_permalink() . '" rel="bookmark" class="btn btn-danger" role="button">' . $read_more_link . '<i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i></a>
			</div>';
    // Concatenate the link to the excerpt
    return $excerpt . $link;

    }

add_filter( 'get_the_excerpt', 'fresh_coral_the_excerpt' );

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function fresh_coral_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'fresh_coral_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,
			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'fresh_coral_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so fresh_coral_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so fresh_coral_categorized_blog should return false.
		return false;
	}
}

/**
 * Flush out the transients used in fresh_coral_categorized_blog.
 */
function fresh_coral_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( 'fresh_coral_categories' );
}
add_action( 'edit_category', 'fresh_coral_category_transient_flusher' );
add_action( 'save_post',     'fresh_coral_category_transient_flusher' );

if ( ! function_exists( 'popper_paging_nav' ) ) :
/**
 * Display navigation to next/previous set of posts when applicable.
 * Based on paging nav function from Twenty Fourteen
 */
function fresh_coral_paging_nav() {
	// Don't print empty markup if there's only one page.
	if ( $GLOBALS['wp_query']->max_num_pages < 2 ) {
		return;
	}
	$paged        = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
	$pagenum_link = html_entity_decode( get_pagenum_link() );
	$query_args   = array();
	$url_parts    = explode( '?', $pagenum_link );
	if ( isset( $url_parts[1] ) ) {
		wp_parse_str( $url_parts[1], $query_args );
	}
	$pagenum_link = remove_query_arg( array_keys( $query_args ), $pagenum_link );
	$pagenum_link = trailingslashit( $pagenum_link ) . '%_%';
	$format  = $GLOBALS['wp_rewrite']->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
	$format .= $GLOBALS['wp_rewrite']->using_permalinks() ? user_trailingslashit( 'page/%#%', 'paged' ) : '?paged=%#%';
	// Set up paginated links.
	$links = paginate_links( array(
		'base'     => $pagenum_link,
		'format'   => $format,
		'total'    => $GLOBALS['wp_query']->max_num_pages,
		'current'  => $paged,
		'mid_size' => 1,
		'add_args' => array_map( 'urlencode', $query_args ),
		'prev_text' => __( 'Previous', 'fresh_coral' ),
		'next_text' => __( 'Next', 'fresh_coral' ),
		'type'      => 'list',
	) );
	if ( $links ) :
		?>
		<nav class="navigation paging-navigation text-center" role="navigation">
			<h1 class="screen-reader-text"><?php _e( 'Posts navigation', 'fresh_coral' ); ?></h1>
				<?php echo str_replace( "<ul class='page-numbers'>", '<ul class="pagination">', $links ); ?>
		</nav><!-- .navigation -->
		<?php
		endif;
	}
endif;
