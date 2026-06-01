<?php
/**
 * SEO — garde-fous sur le schema JSON-LD généré par Yoast SEO.
 *
 * Empêche la publication de nœuds BreadcrumbList invalides (itemListElement
 * absent ou vide) et nettoie les références « breadcrumb » orphelines dans
 * le graphe schema.org produit par le plugin Yoast SEO.
 *
 * @package Kiyose
 * @since   2.2.1
 */

defined( 'ABSPATH' ) || exit;

/**
 * Retire du graphe Yoast les fils d'Ariane invalides.
 *
 * Supprime tout nœud BreadcrumbList dont `itemListElement` est absent ou
 * vide, puis nettoie les clés `breadcrumb` dont le `@id` ne correspond
 * plus à un nœud présent dans le graphe (référence orpheline).
 *
 * @param array $graph Pièces du graphe schema.org (filtre wpseo_schema_graph).
 * @return array Graphe nettoyé, ré-indexé.
 */
function kiyose_remove_invalid_breadcrumbs( $graph ) {
	if ( ! is_array( $graph ) ) {
		return $graph;
	}

	// 1. Retirer les BreadcrumbList sans itemListElement exploitable.
	foreach ( $graph as $index => $piece ) {
		if ( ! kiyose_schema_piece_has_type( $piece, 'BreadcrumbList' ) ) {
			continue;
		}

		$items = ( is_array( $piece ) && isset( $piece['itemListElement'] ) )
			? $piece['itemListElement']
			: null;

		if ( ! is_array( $items ) || array() === $items ) {
			unset( $graph[ $index ] );
		}
	}

	// 2. Recenser les @id encore présents dans le graphe nettoyé.
	$present_ids = array();
	foreach ( $graph as $piece ) {
		if ( is_array( $piece ) && isset( $piece['@id'] ) ) {
			$present_ids[] = $piece['@id'];
		}
	}

	// 3. Supprimer les références breadcrumb orphelines des nœuds restants.
	foreach ( $graph as $index => $piece ) {
		if (
			is_array( $piece )
			&& isset( $piece['breadcrumb']['@id'] )
			&& ! in_array( $piece['breadcrumb']['@id'], $present_ids, true )
		) {
			unset( $graph[ $index ]['breadcrumb'] );
		}
	}

	return array_values( $graph );
}

/**
 * Indique si une pièce de graphe porte le type schema.org donné.
 *
 * Le champ `@type` peut être une chaîne simple ou un tableau de chaînes
 * (types multiples schema.org). Les deux formes sont gérées.
 *
 * @param mixed  $piece Pièce du graphe à inspecter.
 * @param string $type  Type recherché (ex. 'BreadcrumbList').
 * @return bool True si la pièce déclare ce type.
 */
function kiyose_schema_piece_has_type( $piece, $type ) {
	if ( ! is_array( $piece ) || ! isset( $piece['@type'] ) ) {
		return false;
	}

	$types = (array) $piece['@type'];

	return in_array( $type, $types, true );
}

add_filter( 'wpseo_schema_graph', 'kiyose_remove_invalid_breadcrumbs', 20 );
