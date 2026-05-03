<?php
/**
 * Tests for Customizer settings.
 *
 * @package Kiyose
 */

use PHPUnit\Framework\TestCase;

/**
 * Test class for theme Customizer registration.
 */
class Test_Customizer extends TestCase {

	public function test_kiyose_customize_register_registersServicePageDropdownControls() {
		// Given.
		$customizer = new Kiyose_Test_Customizer_Manager();

		// When.
		if ( function_exists( 'kiyose_customize_register' ) ) {
			kiyose_customize_register( $customizer );
		}

		// Then.
		$this->assertArrayHasKey( 'kiyose_service_cta', $customizer->sections );
		$this->assertSame( 'dropdown-pages', $customizer->controls['kiyose_service_contact_page_id']['type'] ?? '' );
		$this->assertSame( 'dropdown-pages', $customizer->controls['kiyose_service_calendar_page_id']['type'] ?? '' );
		$this->assertSame( 'absint', $customizer->settings['kiyose_service_contact_page_id']['sanitize_callback'] ?? '' );
		$this->assertSame( 'absint', $customizer->settings['kiyose_service_calendar_page_id']['sanitize_callback'] ?? '' );
	}

	public function test_kiyose_customize_register_registersFooterLegalDropdownControls() {
		// Given.
		$customizer = new Kiyose_Test_Customizer_Manager();

		// When.
		if ( function_exists( 'kiyose_customize_register' ) ) {
			kiyose_customize_register( $customizer );
		}

		// Then.
		$this->assertArrayHasKey( 'kiyose_footer_legal_links', $customizer->sections );
		$this->assertSame( 'dropdown-pages', $customizer->controls['kiyose_footer_legal_notice_page_id']['type'] ?? '' );
		$this->assertSame( 'dropdown-pages', $customizer->controls['kiyose_footer_privacy_policy_page_id']['type'] ?? '' );
		$this->assertSame( 'absint', $customizer->settings['kiyose_footer_legal_notice_page_id']['sanitize_callback'] ?? '' );
		$this->assertSame( 'absint', $customizer->settings['kiyose_footer_privacy_policy_page_id']['sanitize_callback'] ?? '' );
	}
}

/**
 * Minimal Customizer manager double.
 */
class Kiyose_Test_Customizer_Manager {
	/**
	 * @var array<string, array<string, mixed>>
	 */
	public $sections = array();

	/**
	 * @var array<string, array<string, mixed>>
	 */
	public $settings = array();

	/**
	 * @var array<string, array<string, mixed>>
	 */
	public $controls = array();

	/**
	 * @param string               $id   Section ID.
	 * @param array<string, mixed> $args Section args.
	 */
	public function add_section( string $id, array $args ): void {
		$this->sections[ $id ] = $args;
	}

	/**
	 * @param string               $id   Setting ID.
	 * @param array<string, mixed> $args Setting args.
	 */
	public function add_setting( string $id, array $args ): void {
		$this->settings[ $id ] = $args;
	}

	/**
	 * @param string               $id   Control ID.
	 * @param array<string, mixed> $args Control args.
	 */
	public function add_control( string $id, array $args ): void {
		$this->controls[ $id ] = $args;
	}
}
