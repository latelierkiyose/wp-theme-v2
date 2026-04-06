<?php
/**
 * Decorative shapes configuration.
 *
 * Provides the pool of decorative shapes for non-homepage templates.
 * The shapes container is injected via template-parts/page-shapes-container.php
 * and rendered from footer (called before </main> in each template).
 *
 * @package Kiyose
 * @since   2.5.0
 */

/**
 * Returns the pool of decorative shapes for the current page.
 *
 * Returns null for the homepage (shapes are hardcoded in page-home.php)
 * and for pages that should not have shapes.
 *
 * @return array<int, array<string, mixed>>|null Array of shape configs, or null.
 */
function kiyose_get_page_shapes() {
	// Homepage : shapes hardcodées dans page-home.php, ne pas dupliquer.
	if ( is_page_template( 'templates/page-home.php' ) ) {
		return null;
	}

	return kiyose_build_shapes_pool();
}

/**
 * Builds the universal pool of decorative shapes.
 *
 * Returns ~16 shapes covering 2% to 95% of the page height. The types and
 * colors are fixed for visual consistency across visits. The SVG variant
 * selection (e.g. among the 3 splash variants) remains random in
 * decorative-shapes.php.
 *
 * @return array<int, array<string, mixed>> Array of shape config arrays.
 */
function kiyose_build_shapes_pool() {
	return array(
		// --- Margin shapes (gauche/droite alternées) ---
		array(
			'type'     => 'splash',
			'color'    => '--kiyose-color-accent',
			'opacity'  => 0.30,
			'size'     => 'md',
			'position' => 'margin-left',
			'top'      => '4%',
			'rotation' => 12,
		),
		array(
			'type'     => 'blob',
			'color'    => '--kiyose-color-warm-yellow',
			'opacity'  => 0.35,
			'size'     => 'lg',
			'position' => 'margin-right',
			'top'      => '10%',
			'rotation' => -8,
		),
		array(
			'type'     => 'brushstroke',
			'color'    => '--kiyose-color-burgundy',
			'opacity'  => 0.25,
			'size'     => 'sm',
			'position' => 'margin-left',
			'top'      => '20%',
			'rotation' => 5,
		),
		array(
			'type'     => 'spiral',
			'color'    => '--kiyose-color-accent',
			'opacity'  => 0.30,
			'size'     => 'md',
			'position' => 'margin-right',
			'top'      => '30%',
			'rotation' => -15,
		),
		array(
			'type'     => 'blob',
			'color'    => '--kiyose-color-warm-yellow',
			'opacity'  => 0.35,
			'size'     => 'sm',
			'position' => 'margin-left',
			'top'      => '42%',
			'rotation' => 10,
		),
		array(
			'type'     => 'splash',
			'color'    => '--kiyose-color-accent',
			'opacity'  => 0.28,
			'size'     => 'lg',
			'position' => 'margin-right',
			'top'      => '55%',
			'rotation' => 6,
		),
		array(
			'type'     => 'brushstroke',
			'color'    => '--kiyose-color-burgundy',
			'opacity'  => 0.30,
			'size'     => 'md',
			'position' => 'margin-left',
			'top'      => '68%',
			'rotation' => -10,
		),
		array(
			'type'     => 'blob',
			'color'    => '--kiyose-color-warm-yellow',
			'opacity'  => 0.32,
			'size'     => 'sm',
			'position' => 'margin-right',
			'top'      => '80%',
			'rotation' => 18,
		),
		array(
			'type'     => 'spiral',
			'color'    => '--kiyose-color-accent',
			'opacity'  => 0.25,
			'size'     => 'md',
			'position' => 'margin-left',
			'top'      => '91%',
			'rotation' => -5,
		),

		// --- Content shapes (sous le contenu, desktop uniquement) ---
		array(
			'type'     => 'blob',
			'color'    => '--kiyose-color-warm-yellow',
			'opacity'  => 0.10,
			'size'     => 'lg',
			'position' => 'content-left',
			'top'      => '7%',
			'rotation' => 8,
		),
		array(
			'type'     => 'splash',
			'color'    => '--kiyose-color-accent',
			'opacity'  => 0.08,
			'size'     => 'md',
			'position' => 'content-right',
			'top'      => '18%',
			'rotation' => -12,
		),
		array(
			'type'     => 'brushstroke',
			'color'    => '--kiyose-color-burgundy',
			'opacity'  => 0.12,
			'size'     => 'sm',
			'position' => 'content-left',
			'top'      => '35%',
			'rotation' => 15,
		),
		array(
			'type'     => 'blob',
			'color'    => '--kiyose-color-warm-yellow',
			'opacity'  => 0.09,
			'size'     => 'lg',
			'position' => 'content-right',
			'top'      => '50%',
			'rotation' => -7,
		),
		array(
			'type'     => 'spiral',
			'color'    => '--kiyose-color-accent',
			'opacity'  => 0.11,
			'size'     => 'md',
			'position' => 'content-left',
			'top'      => '65%',
			'rotation' => 20,
		),
		array(
			'type'     => 'splash',
			'color'    => '--kiyose-color-burgundy',
			'opacity'  => 0.08,
			'size'     => 'sm',
			'position' => 'content-right',
			'top'      => '78%',
			'rotation' => -4,
		),
		array(
			'type'     => 'blob',
			'color'    => '--kiyose-color-warm-yellow',
			'opacity'  => 0.10,
			'size'     => 'md',
			'position' => 'content-left',
			'top'      => '88%',
			'rotation' => 11,
		),
	);
}
