<?php
/**
 * LSX: Block Patterns
 *
 * @since LSX 1.0
 */

/**
 * Registers block patterns and categories.
 *
 * @since LSX 1.0
 *
 * @return void
 */
function lsx_register_block_patterns() {
	$block_pattern_categories = array(
		'featured' => array( 'label' => __( 'Featured', 'lsx' ) ),
		'footer'   => array( 'label' => __( 'Footers', 'lsx' ) ),
		'header'   => array( 'label' => __( 'Headers', 'lsx' ) ),
		'query'    => array( 'label' => __( 'Query', 'lsx' ) ),
		'pages'    => array( 'label' => __( 'Pages', 'lsx' ) ),
	);

	/**
	 * Filters the theme block pattern categories.
	 *
	 * @since LSX 1.0
	 *
	 * @param array[] $block_pattern_categories {
	 *     An associative array of block pattern categories, keyed by category name.
	 *
	 *     @type array[] $properties {
	 *         An array of block category properties.
	 *
	 *         @type string $label A human-readable label for the pattern category.
	 *     }
	 * }
	 */
	$block_pattern_categories = apply_filters( 'lsx_block_pattern_categories', $block_pattern_categories );

	foreach ( $block_pattern_categories as $name => $properties ) {
		if ( ! WP_Block_Pattern_Categories_Registry::get_instance()->is_registered( $name ) ) {
			register_block_pattern_category( $name, $properties );
		}
	}

	$block_patterns = array(
		'general-pricing-table',
	);

	/**
	 * Filters the theme block patterns.
	 *
	 * @since LSX 1.0
	 *
	 * @param array $block_patterns List of block patterns by name.
	 */
	$block_patterns = apply_filters( 'lsx_block_patterns', $block_patterns );

	foreach ( $block_patterns as $block_pattern ) {
		$pattern_file = get_theme_file_path( '/includes/patterns/' . $block_pattern . '.php' );

		register_block_pattern(
			'lsx/' . $block_pattern,
			require $pattern_file
		);
	}
}
add_action( 'init', 'lsx_register_block_patterns', 9 );


function lsx_blocks_init() {
	register_block_type( get_template_directory() . '/blocks/src/post-meta' );
}
add_action( 'init', 'lsx_blocks_init' );

/**
 * A function to replace the query post vars.
 *
 * @param array $query
 * @param WP_Block $block
 * @param int $page
 * @return array
 */
function lsx_related_posts_query_args( $query, $block, $page ) {
	if ( ! is_admin() && is_singular( 'post' ) && 'post' === $query['post_type'] && isset( $block->context['query']['related'] ) ) {
		$group     = array();
		$terms     = get_the_terms( get_the_ID(), 'category' );

		if ( is_array( $terms ) && ! empty( $terms ) ) {
			foreach( $terms as $term ) {
				$group[] = $term->term_id;
			}
		}
		$query['tax_query'] = array(
			array(
				'taxonomy' => 'category',
				'field'    => 'term_id',
				'terms'     => $group,
			)
		);
		$query['orderby']      = 'rand';
		$query['post__not_in'] = array( get_the_ID() );
	}
	return $query;
}
// Register our the content filters.
add_filter( 'query_loop_block_query_vars', 'lsx_related_posts_query_args', 10, 3 );


/**
 * Register the meta fields with REST.
 *
 * @return void
 */
function meta_fields_register_meta() {

    $metafields = [ 'price' ];

    foreach( $metafields as $metafield ){
        // Pass an empty string to register the meta key across all existing post types.
        register_post_meta( '', $metafield, array(
            'show_in_rest' => true,
            'type' => 'string',
            'single' => true,
            'sanitize_callback' => 'sanitize_text_field',
            'auth_callback' => function() { 
                return current_user_can( 'edit_posts' );
            }
        ));
    } 
}
add_action( 'init', 'meta_fields_register_meta', 100 );
