<?php
/**
 * L'Atelier Kiyose - Functions and definitions.
 *
 * @package Kiyose
 * @since   0.1.0
 */

// Theme version.
define( 'KIYOSE_VERSION', '0.2.4' );

// URL de l'appel découverte.
define( 'KIYOSE_DISCOVERY_CALL_URL', '/contact/' );

// Setup du thème.
require_once get_template_directory() . '/inc/setup.php';

// Chargement des assets.
require_once get_template_directory() . '/inc/enqueue.php';

// Accessibilité.
require_once get_template_directory() . '/inc/accessibility.php';

// Composants réutilisables.
require_once get_template_directory() . '/inc/components.php';

// Customizer.
require_once get_template_directory() . '/inc/customizer.php';

// Custom Post Types.
require_once get_template_directory() . '/inc/custom-post-types.php';

// CTAs des pages de service.
require_once get_template_directory() . '/inc/service-ctas.php';

// Meta boxes.
require_once get_template_directory() . '/inc/meta-boxes.php';

// Shortcodes.
require_once get_template_directory() . '/inc/shortcodes.php';

// Content filters.
require_once get_template_directory() . '/inc/content-filters.php';

// Home page helpers.
require_once get_template_directory() . '/inc/home.php';

// Blog helpers.
require_once get_template_directory() . '/inc/blog.php';

// Decorative SVG renderers.
require_once get_template_directory() . '/inc/decorative-renderers.php';

// Decorative shapes configuration.
require_once get_template_directory() . '/inc/decorative-config.php';
