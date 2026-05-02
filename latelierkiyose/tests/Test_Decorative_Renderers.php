<?php
/**
 * Tests for decorative SVG renderers.
 *
 * @package Kiyose
 * @since   2.6.0
 */

use PHPUnit\Framework\TestCase;

if ( file_exists( __DIR__ . '/../inc/decorative-renderers.php' ) ) {
	require_once __DIR__ . '/../inc/decorative-renderers.php';
}

/**
 * Test class for decorative SVG renderers.
 */
class Test_Decorative_Renderers extends TestCase {

	protected function setUp(): void {
		parent::setUp();

		kiyose_reset_test_state();
	}

	public function test_kiyose_render_decorative_shape_svg_whenTypeIsUnknown_returnsEmptyString() {
		// Given
		$args = array(
			'type' => 'unknown',
		);

		// When
		$html = $this->call_shape_renderer( $args );

		// Then
		$this->assertSame( '', $html );
	}

	public function test_kiyose_render_decorative_shape_svg_whenOpacityIsOutsideBounds_clampsOpacity() {
		// Given
		$negative_args = array(
			'type'    => 'blob',
			'opacity' => -0.5,
		);
		$high_args     = array(
			'type'    => 'blob',
			'opacity' => 1.8,
		);

		// When
		$negative_html = $this->call_shape_renderer( $negative_args );
		$high_html     = $this->call_shape_renderer( $high_args );

		// Then
		$this->assertStringContainsString( 'opacity="0"', $negative_html );
		$this->assertStringContainsString( 'opacity="1"', $high_html );
	}

	public function test_kiyose_render_decorative_shape_svg_whenSizeIsUnknown_returnsMediumShape() {
		// Given
		$args = array(
			'type' => 'blob',
			'size' => 'xl',
		);

		// When
		$html = $this->call_shape_renderer( $args );

		// Then
		$this->assertStringContainsString( 'deco-shape--md', $html );
		$this->assertStringContainsString( 'width="350"', $html );
		$this->assertStringContainsString( 'height="350"', $html );
	}

	public function test_kiyose_render_decorative_shape_svg_whenPositionIsUnknown_returnsSafePosition() {
		// Given
		$args = array(
			'type'     => 'blob',
			'position' => 'offscreen-danger',
		);

		// When
		$html = $this->call_shape_renderer( $args );

		// Then
		$this->assertStringContainsString( 'deco-shape--top-left', $html );
		$this->assertStringNotContainsString( 'offscreen-danger', $html );
	}

	public function test_kiyose_render_decorative_shape_svg_containsPresentationAttributes() {
		// Given
		$args = array(
			'type' => 'brushstroke',
		);

		// When
		$html = $this->call_shape_renderer( $args );

		// Then
		$this->assertStringContainsString( 'aria-hidden="true"', $html );
		$this->assertStringContainsString( 'role="presentation"', $html );
	}

	public function test_kiyose_render_decorative_scribble_svg_whenTypeIsUnknown_returnsEmptyString() {
		// Given
		$args = array(
			'type' => 'unknown',
		);

		// When
		$html = $this->call_scribble_renderer( $args );

		// Then
		$this->assertSame( '', $html );
	}

	public function test_kiyose_render_decorative_scribble_svg_whenSizeIsInvalid_returnsDefaultSize() {
		// Given
		$args = array(
			'type' => 'squiggle',
			'size' => -20,
		);

		// When
		$html = $this->call_scribble_renderer( $args );

		// Then
		$this->assertStringContainsString( 'width="80"', $html );
		$this->assertStringContainsString( 'height="80"', $html );
	}

	public function test_kiyose_render_decorative_scribble_svg_containsPresentationAttributes() {
		// Given
		$args = array(
			'type' => 'cross',
		);

		// When
		$html = $this->call_scribble_renderer( $args );

		// Then
		$this->assertStringContainsString( 'aria-hidden="true"', $html );
		$this->assertStringContainsString( 'role="presentation"', $html );
		$this->assertStringContainsString( 'preserveAspectRatio="xMidYMid meet"', $html );
	}

	public function test_kiyose_render_wave_separator_svg_whenTypeIsUnknown_returnsEmptyString() {
		// Given
		$args = array(
			'type' => 'unknown',
		);

		// When
		$html = $this->call_wave_renderer( $args );

		// Then
		$this->assertSame( '', $html );
	}

	public function test_kiyose_render_wave_separator_svg_containsPresentationAttributes() {
		// Given
		$args = array(
			'type'    => 'wave',
			'color'   => '--kiyose-color-primary',
			'from_bg' => '#ffffff',
		);

		// When
		$html = $this->call_wave_renderer( $args );

		// Then
		$this->assertStringContainsString( 'class="wave-separator"', $html );
		$this->assertStringContainsString( 'role="presentation"', $html );
		$this->assertStringContainsString( 'aria-hidden="true"', $html );
		$this->assertStringContainsString( 'focusable="false"', $html );
		$this->assertStringContainsString( 'background-color: #ffffff', $html );
		$this->assertStringContainsString( 'fill: var(--kiyose-color-primary)', $html );
	}

	private function call_shape_renderer( array $args ): string {
		if ( ! function_exists( 'kiyose_render_decorative_shape_svg' ) ) {
			$this->fail( 'Expected kiyose_render_decorative_shape_svg() to exist.' );
		}

		return kiyose_render_decorative_shape_svg( $args );
	}

	private function call_scribble_renderer( array $args ): string {
		if ( ! function_exists( 'kiyose_render_decorative_scribble_svg' ) ) {
			$this->fail( 'Expected kiyose_render_decorative_scribble_svg() to exist.' );
		}

		return kiyose_render_decorative_scribble_svg( $args );
	}

	private function call_wave_renderer( array $args ): string {
		if ( ! function_exists( 'kiyose_render_wave_separator_svg' ) ) {
			$this->fail( 'Expected kiyose_render_wave_separator_svg() to exist.' );
		}

		return kiyose_render_wave_separator_svg( $args );
	}
}
