<?php
/**
 * L'Atelier Kiyose - Functions and definitions.
 *
 * @package Kiyose
 * @since   0.1.0
 */

// Theme version.
define( 'KIYOSE_VERSION', '0.2.4' );

// Setup du thème.
require_once get_template_directory() . '/inc/setup.php';

// Chargement des assets.
require_once get_template_directory() . '/inc/enqueue.php';

// Accessibilité.
require_once get_template_directory() . '/inc/accessibility.php';

// Custom Post Types.
require_once get_template_directory() . '/inc/custom-post-types.php';

// Meta boxes.
require_once get_template_directory() . '/inc/meta-boxes.php';

// Shortcodes.
require_once get_template_directory() . '/inc/shortcodes.php';

// Content filters.
require_once get_template_directory() . '/inc/content-filters.php';
