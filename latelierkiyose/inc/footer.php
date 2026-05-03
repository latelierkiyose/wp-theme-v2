<?php
/**
 * Footer configuration helpers.
 *
 * @package Kiyose
 * @since   0.2.5
 */

defined( 'ABSPATH' ) || exit;

/**
 * Return valid legal links configured for the footer.
 *
 * @return array<int, array{label:string,url:string}> Legal links.
 */
function kiyose_get_footer_legal_links(): array {
	$configured_links = array(
		array(
			'theme_mod' => 'kiyose_footer_legal_notice_page_id',
			'label'     => __( 'Mentions légales', 'kiyose' ),
		),
		array(
			'theme_mod' => 'kiyose_footer_privacy_policy_page_id',
			'label'     => __( 'Politique de confidentialité', 'kiyose' ),
		),
	);
	$links            = array();

	foreach ( $configured_links as $configured_link ) {
		$url = kiyose_get_configured_page_permalink( get_theme_mod( $configured_link['theme_mod'], 0 ) );

		if ( '' === $url ) {
			continue;
		}

		$links[] = array(
			'label' => $configured_link['label'],
			'url'   => $url,
		);
	}

	return $links;
}
