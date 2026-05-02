<?php
/**
 * Theme Customizer settings.
 *
 * @package Kiyose
 * @since   0.2.5
 */

/**
 * Register theme Customizer settings.
 *
 * @param object $wp_customize Customizer manager.
 * @return void
 */
function kiyose_customize_register( $wp_customize ): void {
	$wp_customize->add_section(
		'kiyose_service_cta',
		array(
			'title'       => __( 'Pages de service — boutons', 'kiyose' ),
			'description' => __( 'Choisis les pages utilisées par les boutons automatiques en bas des pages de service.', 'kiyose' ),
			'priority'    => 130,
		)
	);

	$wp_customize->add_setting(
		'kiyose_service_contact_page_id',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'kiyose_service_contact_page_id',
		array(
			'label'          => __( 'Page du bouton « Me contacter »', 'kiyose' ),
			'section'        => 'kiyose_service_cta',
			'type'           => 'dropdown-pages',
			'allow_addition' => true,
		)
	);

	$wp_customize->add_setting(
		'kiyose_service_calendar_page_id',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'kiyose_service_calendar_page_id',
		array(
			'label'          => __( 'Page du bouton « Voir le calendrier »', 'kiyose' ),
			'section'        => 'kiyose_service_cta',
			'type'           => 'dropdown-pages',
			'allow_addition' => true,
		)
	);
}
add_action( 'customize_register', 'kiyose_customize_register' );
