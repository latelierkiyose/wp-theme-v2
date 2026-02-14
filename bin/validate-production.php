#!/usr/bin/env php
<?php
/**
 * Pre-activation validation script for L'Atelier Kiyose theme
 *
 * Validates that the theme is ready for production deployment.
 * Run this script before activating the theme on production.
 *
 * Usage:
 *   php bin/validate-production.php
 *   php bin/validate-production.php --verbose
 *
 * @package Kiyose
 * @since 0.3.0
 */

// Parse command line arguments.
$verbose = in_array( '--verbose', $argv, true ) || in_array( '-v', $argv, true );

// ANSI color codes for terminal output.
define( 'COLOR_GREEN', "\033[32m" );
define( 'COLOR_RED', "\033[31m" );
define( 'COLOR_YELLOW', "\033[33m" );
define( 'COLOR_BLUE', "\033[34m" );
define( 'COLOR_RESET', "\033[0m" );
define( 'BOLD', "\033[1m" );

// Validation results.
$errors   = array();
$warnings = array();
$passed   = array();

/**
 * Print a success message
 *
 * @param string $message The message to print.
 */
function print_success( $message ) {
	echo COLOR_GREEN . '✓ ' . COLOR_RESET . $message . PHP_EOL;
}

/**
 * Print an error message
 *
 * @param string $message The message to print.
 */
function print_error( $message ) {
	echo COLOR_RED . '✗ ' . COLOR_RESET . $message . PHP_EOL;
}

/**
 * Print a warning message
 *
 * @param string $message The message to print.
 */
function print_warning( $message ) {
	echo COLOR_YELLOW . '⚠ ' . COLOR_RESET . $message . PHP_EOL;
}

/**
 * Print an info message
 *
 * @param string $message The message to print.
 */
function print_info( $message ) {
	echo COLOR_BLUE . 'ℹ ' . COLOR_RESET . $message . PHP_EOL;
}

/**
 * Print a section header
 *
 * @param string $title The section title.
 */
function print_section( $title ) {
	echo PHP_EOL . BOLD . COLOR_BLUE . '━━━ ' . $title . ' ━━━' . COLOR_RESET . PHP_EOL . PHP_EOL;
}

// Get theme path.
$theme_path = dirname( __DIR__ ) . '/latelierkiyose';

if ( ! is_dir( $theme_path ) ) {
	print_error( 'Theme directory not found: ' . $theme_path );
	exit( 1 );
}

echo BOLD . 'L\'Atelier Kiyose Theme — Production Validation' . COLOR_RESET . PHP_EOL;
echo 'Theme path: ' . $theme_path . PHP_EOL;

// ============================================================================
// 1. REQUIRED FILES VALIDATION
// ============================================================================

print_section( '1. Required Files' );

$required_files = array(
	'style.css'     => 'Theme stylesheet with metadata',
	'functions.php' => 'Theme functions file',
	'index.php'     => 'Fallback template',
	'header.php'    => 'Header template',
	'footer.php'    => 'Footer template',
	'404.php'       => '404 error page template',
	'page.php'      => 'Single page template',
	'single.php'    => 'Single post template',
	'archive.php'   => 'Archive template',
	'search.php'    => 'Search results template',
	'searchform.php' => 'Search form template',
);

foreach ( $required_files as $file => $description ) {
	$filepath = $theme_path . '/' . $file;
	if ( file_exists( $filepath ) ) {
		print_success( $file . ( $verbose ? ' — ' . $description : '' ) );
		$passed[] = $file;
	} else {
		print_error( $file . ' is missing' );
		$errors[] = $file . ' not found';
	}
}

// ============================================================================
// 2. TEMPLATE FILES VALIDATION
// ============================================================================

print_section( '2. Template Files' );

$template_files = array(
	'templates/page-home.php'     => 'Homepage template',
	'templates/page-services.php' => 'Services pages template',
	'templates/page-contact.php'  => 'Contact page template',
	'templates/page-about.php'    => 'About page template',
	'templates/page-calendar.php' => 'Calendar page template',
);

foreach ( $template_files as $file => $description ) {
	$filepath = $theme_path . '/' . $file;
	if ( file_exists( $filepath ) ) {
		// Check for Template Name comment.
		$content = file_get_contents( $filepath );
		if ( preg_match( '/Template Name:/', $content ) ) {
			print_success( $file . ( $verbose ? ' — ' . $description : '' ) );
			$passed[] = $file;
		} else {
			print_warning( $file . ' exists but missing "Template Name" header' );
			$warnings[] = $file . ' missing Template Name header';
		}
	} else {
		print_error( $file . ' is missing' );
		$errors[] = $file . ' not found';
	}
}

// ============================================================================
// 3. TEMPLATE PARTS VALIDATION
// ============================================================================

print_section( '3. Template Parts' );

$template_parts = array(
	'template-parts/content-blog.php'      => 'Blog card component',
	'template-parts/content-testimony.php' => 'Testimony card component',
	'template-parts/content-service.php'   => 'Service card component',
);

foreach ( $template_parts as $file => $description ) {
	$filepath = $theme_path . '/' . $file;
	if ( file_exists( $filepath ) ) {
		print_success( $file . ( $verbose ? ' — ' . $description : '' ) );
		$passed[] = $file;
	} else {
		print_error( $file . ' is missing' );
		$errors[] = $file . ' not found';
	}
}

// ============================================================================
// 4. ASSETS VALIDATION
// ============================================================================

print_section( '4. Assets' );

$asset_dirs = array(
	'assets/css'    => 'CSS files',
	'assets/js'     => 'JavaScript files',
	'assets/fonts'  => 'Font files',
	'assets/images' => 'Image files',
);

foreach ( $asset_dirs as $dir => $description ) {
	$dirpath = $theme_path . '/' . $dir;
	if ( is_dir( $dirpath ) ) {
		$files = glob( $dirpath . '/*' );
		if ( count( $files ) > 0 ) {
			print_success( $dir . ' — ' . count( $files ) . ' files' );
			$passed[] = $dir;
		} else {
			print_warning( $dir . ' exists but is empty' );
			$warnings[] = $dir . ' is empty';
		}
	} else {
		print_error( $dir . ' directory not found' );
		$errors[] = $dir . ' not found';
	}
}

// Check for critical CSS files.
$critical_css = array(
	'assets/css/reset.css'          => 'CSS reset',
	'assets/css/variables.css'      => 'CSS variables',
	'assets/css/base.css'           => 'Base styles',
	'assets/css/header.css'         => 'Header styles',
	'assets/css/footer.css'         => 'Footer styles',
	'assets/css/mobile-menu.css'    => 'Mobile menu styles',
	'assets/css/hero.css'           => 'Hero section styles',
	'assets/css/kintsugi.css'       => 'Kintsugi decorative styles',
);

foreach ( $critical_css as $file => $description ) {
	$filepath = $theme_path . '/' . $file;
	if ( file_exists( $filepath ) ) {
		if ( $verbose ) {
			print_success( $file . ' — ' . $description );
		}
		$passed[] = $file;
	} else {
		print_error( $file . ' is missing' );
		$errors[] = $file . ' not found';
	}
}

// Check for critical JS files.
$critical_js = array(
	'assets/js/mobile-menu.js' => 'Mobile menu functionality',
	'assets/js/carousel.js'    => 'Carousel functionality',
);

foreach ( $critical_js as $file => $description ) {
	$filepath = $theme_path . '/' . $file;
	if ( file_exists( $filepath ) ) {
		if ( $verbose ) {
			print_success( $file . ' — ' . $description );
		}
		$passed[] = $file;
	} else {
		print_error( $file . ' is missing' );
		$errors[] = $file . ' not found';
	}
}

// ============================================================================
// 5. INC MODULES VALIDATION
// ============================================================================

print_section( '5. PHP Modules (inc/)' );

$inc_modules = array(
	'inc/enqueue-assets.php'      => 'Asset enqueue functions',
	'inc/theme-setup.php'         => 'Theme setup and supports',
	'inc/custom-post-types.php'   => 'Custom post types registration',
	'inc/shortcodes.php'          => 'Shortcodes implementation',
	'inc/navigation.php'          => 'Navigation menus',
	'inc/template-functions.php'  => 'Template helper functions',
);

foreach ( $inc_modules as $file => $description ) {
	$filepath = $theme_path . '/' . $file;
	if ( file_exists( $filepath ) ) {
		// Check for PHP syntax errors.
		$lint_output = array();
		$lint_return = 0;
		exec( 'php -l ' . escapeshellarg( $filepath ) . ' 2>&1', $lint_output, $lint_return );

		if ( 0 === $lint_return ) {
			print_success( $file . ( $verbose ? ' — ' . $description : '' ) );
			$passed[] = $file;
		} else {
			print_error( $file . ' has PHP syntax errors' );
			$errors[] = $file . ' syntax error: ' . implode( ' ', $lint_output );
		}
	} else {
		print_error( $file . ' is missing' );
		$errors[] = $file . ' not found';
	}
}

// ============================================================================
// 6. STYLE.CSS METADATA VALIDATION
// ============================================================================

print_section( '6. Theme Metadata (style.css)' );

$style_file = $theme_path . '/style.css';
if ( file_exists( $style_file ) ) {
	$style_content = file_get_contents( $style_file );

	$required_headers = array(
		'Theme Name'     => 'L\'Atelier Kiyose',
		'Theme URI'      => 'https://www.latelierkiyose.fr/',
		'Author'         => '.+',
		'Description'    => '.+',
		'Version'        => '\d+\.\d+\.\d+',
		'Requires at least' => '\d+\.\d+',
		'Requires PHP'   => '\d+\.\d+',
		'Text Domain'    => 'kiyose',
		'License'        => 'GNU General Public License',
	);

	foreach ( $required_headers as $header => $pattern ) {
		if ( preg_match( '/' . preg_quote( $header, '/' ) . ':\s*(' . $pattern . ')/i', $style_content, $matches ) ) {
			print_success( $header . ': ' . trim( $matches[1] ) );
			$passed[] = 'style.css header: ' . $header;
		} else {
			print_error( $header . ' missing or invalid in style.css' );
			$errors[] = 'style.css missing header: ' . $header;
		}
	}
} else {
	print_error( 'style.css not found' );
	$errors[] = 'style.css not found';
}

// ============================================================================
// 7. SECURITY CHECKS
// ============================================================================

print_section( '7. Security Checks' );

// Check for common security issues in PHP files.
$php_files = glob( $theme_path . '/{*.php,inc/*.php,templates/*.php,template-parts/*.php}', GLOB_BRACE );

$security_patterns = array(
	'eval('              => 'Dangerous eval() usage',
	'base64_decode('     => 'Suspicious base64_decode() usage',
	'file_get_contents(' => 'Unvalidated file_get_contents() (check if user input is sanitized)',
	'$_GET'              => 'Direct $_GET usage (must be sanitized)',
	'$_POST'             => 'Direct $_POST usage (must be sanitized)',
	'$_REQUEST'          => 'Direct $_REQUEST usage (must be sanitized)',
);

$security_issues = array();
foreach ( $php_files as $filepath ) {
	$content = file_get_contents( $filepath );
	$filename = str_replace( $theme_path . '/', '', $filepath );

	foreach ( $security_patterns as $pattern => $description ) {
		// Skip sanitized usages (with sanitize_, wp_kses, esc_, etc.).
		$lines = explode( "\n", $content );
		foreach ( $lines as $line_num => $line ) {
			if ( false !== strpos( $line, $pattern ) ) {
				// Check if line contains sanitization functions.
				$is_sanitized = preg_match( '/(sanitize_|wp_kses|esc_|wp_verify_nonce|check_admin_referer)/', $line );
				if ( ! $is_sanitized && 'file_get_contents(' !== $pattern ) {
					$security_issues[] = $filename . ':' . ( $line_num + 1 ) . ' — ' . $description;
				}
			}
		}
	}
}

if ( empty( $security_issues ) ) {
	print_success( 'No obvious security issues detected' );
	$passed[] = 'Security basic checks';
} else {
	foreach ( $security_issues as $issue ) {
		print_warning( $issue );
		$warnings[] = 'Security: ' . $issue;
	}
}

// ============================================================================
// 8. ACCESSIBILITY CHECKS
// ============================================================================

print_section( '8. Accessibility (Basic Checks)' );

// Check for skip link in header.php.
$header_file = $theme_path . '/header.php';
if ( file_exists( $header_file ) ) {
	$header_content = file_get_contents( $header_file );
	if ( preg_match( '/class="skip-link"/', $header_content ) ) {
		print_success( 'Skip link found in header.php' );
		$passed[] = 'Skip link';
	} else {
		print_warning( 'Skip link not found in header.php' );
		$warnings[] = 'Missing skip link';
	}

	if ( preg_match( '/<nav[^>]*role="navigation"/', $header_content ) || preg_match( '/<nav[^>]*aria-label/', $header_content ) ) {
		print_success( 'Navigation has ARIA label/role' );
		$passed[] = 'Navigation ARIA';
	} else {
		print_warning( 'Navigation missing ARIA label/role' );
		$warnings[] = 'Navigation missing ARIA attributes';
	}
}

// Check footer for landmarks.
$footer_file = $theme_path . '/footer.php';
if ( file_exists( $footer_file ) ) {
	$footer_content = file_get_contents( $footer_file );
	if ( preg_match( '/<footer/', $footer_content ) ) {
		print_success( 'Footer landmark found' );
		$passed[] = 'Footer landmark';
	} else {
		print_warning( 'Footer landmark not found' );
		$warnings[] = 'Missing footer landmark';
	}
}

// Check for lang attribute in header.
if ( file_exists( $header_file ) ) {
	$header_content = file_get_contents( $header_file );
	if ( preg_match( '/language_attributes\(\)/', $header_content ) ) {
		print_success( 'language_attributes() found (sets lang attribute)' );
		$passed[] = 'Language attributes';
	} else {
		print_error( 'language_attributes() missing in <html> tag' );
		$errors[] = 'Missing language_attributes()';
	}
}

// ============================================================================
// FINAL SUMMARY
// ============================================================================

print_section( 'Validation Summary' );

$total_checks = count( $passed ) + count( $errors ) + count( $warnings );

echo BOLD . 'Results:' . COLOR_RESET . PHP_EOL;
echo COLOR_GREEN . '  ✓ Passed:  ' . count( $passed ) . COLOR_RESET . PHP_EOL;
echo COLOR_YELLOW . '  ⚠ Warnings: ' . count( $warnings ) . COLOR_RESET . PHP_EOL;
echo COLOR_RED . '  ✗ Errors:   ' . count( $errors ) . COLOR_RESET . PHP_EOL;
echo '  Total:    ' . $total_checks . PHP_EOL;

if ( ! empty( $errors ) ) {
	echo PHP_EOL . COLOR_RED . BOLD . '❌ VALIDATION FAILED' . COLOR_RESET . PHP_EOL;
	echo 'The theme is NOT ready for production. Please fix the errors above.' . PHP_EOL;
	exit( 1 );
} elseif ( ! empty( $warnings ) ) {
	echo PHP_EOL . COLOR_YELLOW . BOLD . '⚠️  VALIDATION PASSED WITH WARNINGS' . COLOR_RESET . PHP_EOL;
	echo 'The theme can be deployed but please review the warnings above.' . PHP_EOL;
	exit( 0 );
} else {
	echo PHP_EOL . COLOR_GREEN . BOLD . '✅ VALIDATION PASSED' . COLOR_RESET . PHP_EOL;
	echo 'The theme is ready for production deployment!' . PHP_EOL;
	exit( 0 );
}
