<?php
/**
 * Tests for theme setup helpers.
 *
 * @package Kiyose
 * @since   2.1.0
 */

use PHPUnit\Framework\TestCase;

/**
 * Test class for setup helpers.
 */
class Test_Setup extends TestCase {

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function test_kiyose_register_block_styles_whenRegisterBlockStyleExists_registersRoundImageStyle() {
		// Given
		if ( ! function_exists( 'register_block_style' ) ) {
			function register_block_style( $block_name, $style_properties ) {
				$GLOBALS['kiyose_test_registered_block_styles'][] = array(
					'block_name'        => $block_name,
					'style_properties'  => $style_properties,
				);

				return true;
			}
		}

		require_once __DIR__ . '/../inc/setup.php';
		$GLOBALS['kiyose_test_registered_block_styles'] = array();

		// When
		$this->assertTrue( function_exists( 'kiyose_register_block_styles' ) );
		kiyose_register_block_styles();

		// Then
		$this->assertSame(
			array(
				array(
					'block_name'       => 'core/image',
					'style_properties' => array(
						'name'  => 'kiyose-round',
						'label' => 'Ronde',
					),
				),
			),
			$GLOBALS['kiyose_test_registered_block_styles']
		);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function test_kiyose_register_block_styles_whenRegisterBlockStyleDoesNotExist_doesNothing() {
		// Given
		require_once __DIR__ . '/../inc/setup.php';

		// When
		$this->assertFalse( function_exists( 'register_block_style' ) );
		$this->assertTrue( function_exists( 'kiyose_register_block_styles' ) );
		kiyose_register_block_styles();

		// Then
		$this->addToAssertionCount( 1 );
	}
}
