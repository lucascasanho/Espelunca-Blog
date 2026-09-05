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
