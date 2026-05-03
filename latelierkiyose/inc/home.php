<?php
/**
 * Home page helpers.
 *
 * @package Kiyose
 * @since   0.2.7
 */

defined( 'ABSPATH' ) || exit;

/**
 * Decode a JSON meta value expected to contain an array.
 *
 * @param string $raw_value Raw JSON meta value.
 * @return array<int|string, mixed> Decoded array, or an empty array when invalid.
 */
function kiyose_decode_json_meta_array( string $raw_value ): array {
	if ( '' === $raw_value ) {
		return array();
	}

	$decoded = json_decode( $raw_value, true );

	return is_array( $decoded ) ? $decoded : array();
}

/**
 * Return all editable data used by the home page template.
 *
 * @param int $post_id Home page post ID.
 * @return array<string, mixed> Home page data.
 */
function kiyose_get_home_page_data( int $post_id ): array {
	$welcome_title  = get_post_meta( $post_id, 'kiyose_welcome_title', true );
	$about_content  = get_post_meta( $post_id, 'kiyose_hero_content', true );
	$about_cta_text = get_post_meta( $post_id, 'kiyose_hero_cta_text', true );
	$about_cta_url  = get_post_meta( $post_id, 'kiyose_hero_cta_url', true );

	if ( empty( $welcome_title ) ) {
		$welcome_title = 'L\'être humain est un vitrail, révélons ensemble sa lumière';
	}

	if ( empty( $about_content ) ) {
		$about_content = 'Au sein de L\'Atelier Kiyose, découvrez des pratiques de bien-être et de développement personnel qui vous accompagnent dans votre transformation : art-thérapie, rigologie, bols tibétains et ateliers philo.';
	}

	if ( empty( $about_cta_text ) ) {
		$about_cta_text = 'En savoir plus sur l\'atelier';
	}

	if ( empty( $about_cta_url ) ) {
		$about_cta_url = home_url( '/a-propos/' );
	}

	return array(
		'welcome_title'         => $welcome_title,
		'welcome_subtitle'      => get_post_meta( $post_id, 'kiyose_welcome_subtitle', true ),
		'welcome_text'          => get_post_meta( $post_id, 'kiyose_welcome_text', true ),
		'welcome_keywords'      => kiyose_decode_json_meta_array( (string) get_post_meta( $post_id, 'kiyose_welcome_keywords', true ) ),
		'welcome_slogan'        => get_post_meta( $post_id, 'kiyose_welcome_slogan', true ),
		'about_content'         => $about_content,
		'about_cta_text'        => $about_cta_text,
		'about_cta_url'         => $about_cta_url,
		'about_image_id'        => get_post_meta( $post_id, 'kiyose_hero_image_id', true ),
		'content1_qa'           => kiyose_decode_json_meta_array( (string) get_post_meta( $post_id, 'kiyose_content1_qa', true ) ),
		'content1_slogan'       => get_post_meta( $post_id, 'kiyose_content1_slogan', true ),
		'content2_text'         => get_post_meta( $post_id, 'kiyose_content2_text', true ),
		'content2_quote'        => get_post_meta( $post_id, 'kiyose_content2_slogan', true ),
		'content2_quote_author' => get_post_meta( $post_id, 'kiyose_content2_quote_author', true ),
	);
}

/**
 * Return the decorative shapes curated for the home page.
 *
 * @return array<int, array<string, mixed>> Home page decorative shape configs.
 */
function kiyose_get_home_page_shapes(): array {
	return array(
		array(
			'type'     => 'blob',
			'color'    => '--kiyose-color-accent',
			'opacity'  => 0.3,
			'size'     => 'sm',
			'position' => 'margin-left',
			'top'      => '1%',
			'rotation' => 15,
		),
		array(
			'type'     => 'splash',
			'color'    => '--kiyose-color-accent',
			'opacity'  => 0.4,
			'size'     => 'md',
			'position' => 'margin-right',
			'top'      => '3%',
			'rotation' => -6,
		),
		array(
			'type'     => 'brushstroke',
			'color'    => '--kiyose-color-primary',
			'opacity'  => 0.25,
			'size'     => 'sm',
			'position' => 'margin-left',
			'top'      => '9%',
			'rotation' => 20,
		),
		array(
			'type'     => 'blob',
			'color'    => '--kiyose-color-primary',
			'opacity'  => 0.3,
			'size'     => 'sm',
			'position' => 'margin-left',
			'top'      => '15%',
			'rotation' => 12,
		),
		array(
			'type'     => 'splash',
			'color'    => '--kiyose-color-accent',
			'opacity'  => 0.3,
			'size'     => 'sm',
			'position' => 'margin-right',
			'top'      => '21%',
			'rotation' => -10,
		),
		array(
			'type'     => 'blob',
			'color'    => '--kiyose-color-accent',
			'opacity'  => 0.25,
			'size'     => 'sm',
			'position' => 'margin-right',
			'top'      => '28%',
			'rotation' => 5,
		),
		array(
			'type'     => 'splash',
			'color'    => '--kiyose-color-primary',
			'opacity'  => 0.35,
			'size'     => 'md',
			'position' => 'margin-left',
			'top'      => '42%',
			'rotation' => 4,
		),
		array(
			'type'     => 'brushstroke',
			'color'    => '--kiyose-color-accent',
			'opacity'  => 0.4,
			'size'     => 'sm',
			'position' => 'margin-right',
			'top'      => '56%',
			'rotation' => -12,
		),
		array(
			'type'     => 'spiral',
			'color'    => '--kiyose-color-accent',
			'opacity'  => 0.25,
			'size'     => 'sm',
			'position' => 'margin-left',
			'top'      => '71%',
			'rotation' => -15,
		),
		array(
			'type'     => 'blob',
			'color'    => '--kiyose-color-primary',
			'opacity'  => 0.3,
			'size'     => 'md',
			'position' => 'margin-right',
			'top'      => '86%',
			'rotation' => 7,
		),
		array(
			'type'     => 'blob',
			'color'    => '--kiyose-color-accent',
			'opacity'  => 0.12,
			'size'     => 'md',
			'position' => 'content-right',
			'top'      => '4%',
			'rotation' => 8,
		),
		array(
			'type'     => 'blob',
			'color'    => '--kiyose-color-accent',
			'opacity'  => 0.10,
			'size'     => 'sm',
			'position' => 'content-left',
			'top'      => '12%',
			'rotation' => -6,
		),
		array(
			'type'     => 'brushstroke',
			'color'    => '--kiyose-color-primary',
			'opacity'  => 0.10,
			'size'     => 'lg',
			'position' => 'content-left',
			'top'      => '20%',
			'rotation' => -5,
		),
		array(
			'type'     => 'blob',
			'color'    => '--kiyose-color-accent',
			'opacity'  => 0.10,
			'size'     => 'sm',
			'position' => 'content-right',
			'top'      => '28%',
			'rotation' => 4,
		),
		array(
			'type'     => 'brushstroke',
			'color'    => '--kiyose-color-accent',
			'opacity'  => 0.12,
			'size'     => 'md',
			'position' => 'content-right',
			'top'      => '36%',
			'rotation' => 10,
		),
		array(
			'type'     => 'blob',
			'color'    => '--kiyose-color-primary',
			'opacity'  => 0.08,
			'size'     => 'sm',
			'position' => 'content-right',
			'top'      => '44%',
			'rotation' => 7,
		),
		array(
			'type'     => 'blob',
			'color'    => '--kiyose-color-primary',
			'opacity'  => 0.15,
			'size'     => 'md',
			'position' => 'content-left',
			'top'      => '52%',
			'rotation' => -12,
		),
		array(
			'type'     => 'brushstroke',
			'color'    => '--kiyose-color-accent',
			'opacity'  => 0.09,
			'size'     => 'sm',
			'position' => 'content-left',
			'top'      => '60%',
			'rotation' => -8,
		),
		array(
			'type'     => 'brushstroke',
			'color'    => '--kiyose-color-accent',
			'opacity'  => 0.10,
			'size'     => 'lg',
			'position' => 'content-right',
			'top'      => '68%',
			'rotation' => 6,
		),
		array(
			'type'     => 'brushstroke',
			'color'    => '--kiyose-color-accent',
			'opacity'  => 0.09,
			'size'     => 'sm',
			'position' => 'content-left',
			'top'      => '76%',
			'rotation' => -5,
		),
		array(
			'type'     => 'blob',
			'color'    => '--kiyose-color-primary',
			'opacity'  => 0.12,
			'size'     => 'md',
			'position' => 'content-left',
			'top'      => '84%',
			'rotation' => -8,
		),
		array(
			'type'     => 'brushstroke',
			'color'    => '--kiyose-color-primary',
			'opacity'  => 0.10,
			'size'     => 'sm',
			'position' => 'content-right',
			'top'      => '92%',
			'rotation' => 5,
		),
	);
}
