<?php
/**
 * L'Atelier Kiyose - Theme setup.
 *
 * @package Kiyose
 * @since   0.1.0
 */

if ( ! isset( $content_width ) ) {
	$content_width = 1200;
}

/**
 * Setup theme features and configuration.
 */
function kiyose_setup() {
	// Gestion automatique de la balise <title>.
	add_theme_support( 'title-tag' );

	// Support des images à la une.
	add_theme_support( 'post-thumbnails' );

	// Markup HTML5 pour les formulaires et éléments natifs WordPress.
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		)
	);

	// Logo personnalisable dans le customizer.
	add_theme_support( 'custom-logo' );

	// Enregistrement du menu principal.
	register_nav_menus(
		array(
			'primary' => __( 'Menu principal', 'kiyose' ),
		)
	);

	// Tailles d'images personnalisées.
	add_image_size( 'kiyose-hero', 1920, 800, true );
	add_image_size( 'kiyose-service-card', 600, 400, true );
	add_image_size( 'kiyose-testimony-thumb', 150, 150, true );

	// Désactiver les commentaires sur les articles.
	remove_post_type_support( 'post', 'comments' );
	remove_post_type_support( 'post', 'trackbacks' );
}
add_action( 'after_setup_theme', 'kiyose_setup' );
