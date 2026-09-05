<?php
/**
 * Espelunca Blog theme setup.
 *
 * @package Espelunca_Blog
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register theme supports and editor stylesheet.
 */
function espelunca_blog_setup() {
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'style.css' );
}
add_action( 'after_setup_theme', 'espelunca_blog_setup' );

/**
 * Load the small amount of theme CSS used in addition to theme.json.
 */
function espelunca_blog_enqueue_styles() {
	$theme = wp_get_theme();

	wp_enqueue_style(
		'espelunca-blog',
		get_stylesheet_uri(),
		array(),
		$theme->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', 'espelunca_blog_enqueue_styles' );

/**
 * Find the first image in a post and return safe image HTML.
 *
 * This keeps the publishing flow simple: when a post has no explicit featured
 * image, the first image inserted in its content can still represent the post
 * in cards and in the single-post hero.
 *
 * @param int    $post_id WordPress post ID.
 * @param string $size    Requested WordPress image size.
 * @return string Image HTML or an empty string when the post has no image.
 */
function espelunca_blog_get_first_content_image( $post_id, $size = 'large' ) {
	$post = get_post( $post_id );

	if ( ! $post || empty( $post->post_content ) ) {
		return '';
	}

	$content = $post->post_content;

	// Prefer a Media Library attachment so WordPress can provide srcset/sizes.
	if ( preg_match( '/\bwp-image-(\d+)\b/', $content, $matches ) ) {
		$attachment_id = absint( $matches[1] );

		if ( $attachment_id ) {
			$image = wp_get_attachment_image(
				$attachment_id,
				$size,
				false,
				array(
					'class'    => 'esp-featured-fallback-image',
					'loading'  => 'lazy',
					'decoding' => 'async',
				)
			);

			if ( $image ) {
				return $image;
			}
		}
	}

	// Also support externally hosted images and legacy/classic post markup.
	if ( preg_match( '/<img\b[^>]*\bsrc=["\']([^"\']+)["\'][^>]*>/i', $content, $matches ) ) {
		$src = esc_url( $matches[1] );

		if ( $src ) {
			$alt = '';
			if ( preg_match( '/<img\b[^>]*\balt=["\']([^"\']*)["\'][^>]*>/i', $matches[0], $alt_matches ) ) {
				$alt = sanitize_text_field( html_entity_decode( $alt_matches[1], ENT_QUOTES, get_bloginfo( 'charset' ) ) );
			}

			return sprintf(
				'<img class="esp-featured-fallback-image" src="%1$s" alt="%2$s" loading="lazy" decoding="async">',
				esc_url( $src ),
				esc_attr( $alt )
			);
		}
	}

	return '';
}

/**
 * Render the first content image when a Post Featured Image block has no image.
 *
 * Explicit featured images always win. This filter only fills an otherwise
 * empty core/post-featured-image block, so it works across home, archives,
 * search results and single-post templates without modifying post data.
 *
 * @param string $block_content Rendered block HTML.
 * @param array  $block         Parsed block data.
 * @return string
 */
function espelunca_blog_featured_image_fallback( $block_content, $block ) {
	if ( 'core/post-featured-image' !== ( $block['blockName'] ?? '' ) || '' !== trim( $block_content ) ) {
		return $block_content;
	}

	$post_id = get_the_ID();

	if ( ! $post_id || has_post_thumbnail( $post_id ) ) {
		return $block_content;
	}

	$size = 'large';
	if ( ! empty( $block['attrs']['sizeSlug'] ) ) {
		$size = sanitize_key( $block['attrs']['sizeSlug'] );
	}

	$image = espelunca_blog_get_first_content_image( $post_id, $size );

	if ( ! $image ) {
		return $block_content;
	}

	$classes = array( 'wp-block-post-featured-image', 'esp-featured-fallback' );
	if ( ! empty( $block['attrs']['className'] ) ) {
		$classes[] = sanitize_html_class( $block['attrs']['className'] );
	}

	if ( ! empty( $block['attrs']['isLink'] ) ) {
		$image = sprintf(
			'<a href="%1$s" aria-label="%2$s">%3$s</a>',
			esc_url( get_permalink( $post_id ) ),
			esc_attr( sprintf( __( 'Abrir %s', 'espelunca-blog' ), get_the_title( $post_id ) ) ),
			$image
		);
	}

	return sprintf(
		'<figure class="%1$s">%2$s</figure>',
		esc_attr( implode( ' ', array_unique( $classes ) ) ),
		$image
	);
}
add_filter( 'render_block', 'espelunca_blog_featured_image_fallback', 10, 2 );
