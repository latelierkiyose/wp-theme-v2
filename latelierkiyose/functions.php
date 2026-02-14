<?php
/**
 * L'Atelier Kiyose - Functions and definitions.
 *
 * @package Kiyose
 * @since   0.1.0
 */

// Theme version.
define( 'KIYOSE_VERSION', '0.1.6' );

// Setup du thème.
require_once get_template_directory() . '/inc/setup.php';

// Chargement des assets.
require_once get_template_directory() . '/inc/enqueue.php';

// Accessibilité.
require_once get_template_directory() . '/inc/accessibility.php';

// Custom Post Types.
require_once get_template_directory() . '/inc/custom-post-types.php';
