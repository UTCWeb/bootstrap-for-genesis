<?php
/**
 * Pagination
 *
 * @package      B4Genesis
 * @since        1.0
 * @link         http://rotsenacob.com
 * @author       Rotsen Mark Acob <rotsenacob.com>
 * @copyright    Copyright (c) 2017, Rotsen Mark Acob
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 *
*/

// Pagination Numeric
// add_filter( 'genesis_prev_link_text', 'bfg_genesis_prev_link_text_numeric' );
// add_filter( 'genesis_next_link_text', 'bfg_genesis_next_link_text_numeric' );

function bfg_genesis_prev_link_text_numeric( $text ) {
    if ( 'numeric' === genesis_get_option( 'posts_nav' ) ) {
        return '<span aria-hidden="true">&laquo;</span>'
            . '<span class="sr-only">' . __( 'Previous Page', 'b4genesis' ) . '</span>';
    }
    return $text;
}

function bfg_genesis_next_link_text_numeric( $text ) {
    if ( 'numeric' === genesis_get_option( 'posts_nav' ) ) {
        return '<span class="sr-only">' . __( 'Next Page', 'b4genesis' ) . '</span>'
            . '<span aria-hidden="true">&raquo;</span>';
    }
    return $text;
}

/**
 * Change Post Navigation to use Bootstrap Markup
 */
remove_action( 'genesis_after_endwhile', 'genesis_posts_nav' );
add_action( 'genesis_after_endwhile', 'bfg_posts_nav' );
function bfg_posts_nav() {
    if ( 'numeric' === genesis_get_option( 'posts_nav' ) ) {
		bfg_numeric_posts_nav();
	} else {
		bfg_prev_next_posts_nav();
	}
}

function bfg_numeric_posts_nav() {
    if( is_singular() ) {
		return;
	}

	global $wp_query;

	// Stop execution if there's only one page.
	if( $wp_query->max_num_pages <= 1 ) {
		return;
	}

	$paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
	$max   = (int) $wp_query->max_num_pages;

	// Add current page to the array.
	if ( $paged >= 1 ) {
		$links[] = $paged;
	}

	// Add the pages around the current page to the array.
	if ( $paged >= 3 ) {
		$links[] = $paged - 1;
		$links[] = $paged - 2;
	}

	if ( ( $paged + 2 ) <= $max ) {
		$links[] = $paged + 2;
		$links[] = $paged + 1;
	}

	genesis_markup( array(
		'open'    => '<nav %s>',
		'context' => 'archive-pagination',
	) );

	$before_number = genesis_a11y( 'screen-reader-text' ) ? '<span class="screen-reader-text">' . __( 'Page ', 'genesis' ) .  '</span>' : '';

	echo '<ul class="pagination">';

	// Previous Post Link.
	if ( get_previous_posts_link() ) {
		printf( '<li class="page-item pagination-previous">%s</li>' . "\n", get_previous_posts_link( apply_filters( 'genesis_prev_link_text', '&#x000AB; ' . __( 'Previous Page', 'genesis' ) ) ) );
	}

	// Link to first page, plus ellipses if necessary.
	if ( ! in_array( 1, $links ) ) {

		$class = 1 == $paged ? ' class="page-item active"' : ' class="page-item"';

		printf( '<li%s><a class="page-link" href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( 1 ) ), $before_number . '1' );

		if ( ! in_array( 2, $links ) ) {
			echo '<li class="pagination-omission">&#x02026;</li>' . "\n";
		}

	}

	// Link to current page, plus 2 pages in either direction if necessary.
	sort( $links );
	foreach ( (array) $links as $link ) {
		$class = $paged == $link ? ' class="page-item active" ' : ' class="page-item"';
		$aria  = $paged == $link ? ' aria-label="' . esc_attr__( 'Current page', 'genesis' ) . '"' : '';
		printf( '<li%s><a class="page-link" href="%s"%s>%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $link ) ), $aria, $before_number . $link );
	}

	// Link to last page, plus ellipses if necessary.
	if ( ! in_array( $max, $links ) ) {

		if ( ! in_array( $max - 1, $links ) ) {
			echo '<li class="pagination-omission page-item disabled"><a class="page-link" href="">&#x02026;</a></li>' . "\n";
        }
        
		$class = $paged == $max ? ' class="active page-item"' : ' class="page-item"';
		printf( '<li%s><a class="page-link" href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $max ) ), $before_number . $max );

	}

	// Next Post Link.
	if ( get_next_posts_link() ) {
		printf( '<li class="page-item pagination-next">%s</li>' . "\n", get_next_posts_link( apply_filters( 'genesis_next_link_text', __( 'Next Page', 'genesis' ) . ' &#x000BB;' ) ) );
	}

	echo '</ul>';
	genesis_markup( array(
		'close'    => '</nav>',
		'context' => 'archive-pagination',
	) );

	echo "\n";
}

function bfg_prev_next_posts_nav() {
    $prev_link = get_previous_posts_link( apply_filters( 'genesis_prev_link_text', '&#x000AB; ' . __( 'Previous Page', 'genesis' ) ) );
	$next_link = get_next_posts_link( apply_filters( 'genesis_next_link_text', __( 'Next Page', 'genesis' ) . ' &#x000BB;' ) );

	if ( $prev_link || $next_link ) {

		$pagination = $prev_link ? sprintf( '<div class="pagination-previous alignleft">%s</div>', $prev_link ) : '';
		$pagination .= $next_link ? sprintf( '<div class="pagination-next alignright">%s</div>', $next_link ) : '';

		genesis_markup( array(
			'open'    => '<div %s>',
			'close'   => '</div>',
			'content' => $pagination,
			'context' => 'archive-pagination',
		) );

	}
}

add_filter( 'next_posts_link_attributes', function( $attr ) {
    $attr = 'class="page-link"';
    return $attr;
}, 10, 2 );

add_filter( 'previous_posts_link_attributes', function( $attr ) {
    $attr = 'class="page-link"';

    return $attr;
}, 10, 2 );