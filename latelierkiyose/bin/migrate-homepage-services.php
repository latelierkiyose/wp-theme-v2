#!/usr/bin/env php
<?php
/**
 * Script de migration one-time pour marquer les 4 pages de services existantes
 * comme affichables sur la page d'accueil.
 *
 * Ce script doit être exécuté UNE SEULE FOIS après le déploiement de la v0.1.17.
 *
 * Usage:
 *   wp eval-file bin/migrate-homepage-services.php
 *
 * Ou via PHP CLI (depuis la racine WordPress):
 *   php wp-content/themes/latelierkiyose/bin/migrate-homepage-services.php
 *
 * @package Kiyose
 * @since   0.1.17
 */

// Si exécuté via WP-CLI, charger WordPress.
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	// Déjà dans le contexte WordPress.
	$is_wp_cli = true;
} else {
	// Chargement manuel de WordPress.
	$is_wp_cli = false;

	// Remonter jusqu'à la racine WordPress.
	$wp_load_path = __DIR__ . '/../../../../wp-load.php';
	if ( ! file_exists( $wp_load_path ) ) {
		echo "Erreur: Impossible de trouver wp-load.php\n";
		echo "Ce script doit être exécuté depuis la racine WordPress ou via WP-CLI.\n";
		exit( 1 );
	}
	require_once $wp_load_path;
}

echo "=== Migration des pages de services pour affichage sur la page d'accueil ===\n\n";

// Liste des slugs des 4 pages de services.
$service_slugs = array(
	'art-therapie',
	'rigologie',
	'bols-tibetains',
	'ateliers-philosophie',
);

$updated_count = 0;
$errors        = array();

foreach ( $service_slugs as $slug ) {
	echo "Traitement de la page: {$slug}... ";

	$page = get_page_by_path( $slug );

	if ( ! $page ) {
		echo "❌ ERREUR: Page non trouvée\n";
		$errors[] = "Page '{$slug}' non trouvée dans la base de données.";
		continue;
	}

	// Vérifier que la page utilise bien le template "Page de service".
	$template = get_post_meta( $page->ID, '_wp_page_template', true );

	if ( 'templates/page-services.php' !== $template ) {
		echo "⚠️  AVERTISSEMENT: La page n'utilise pas le template 'Page de service' (template actuel: {$template})\n";
		$errors[] = "Page '{$slug}' (ID: {$page->ID}) n'utilise pas le template attendu.";
		continue;
	}

	// Marquer la page pour affichage sur la page d'accueil.
	update_post_meta( $page->ID, 'kiyose_show_on_homepage', '1' );

	// Assigner un menu_order si pas déjà défini.
	$current_order = (int) $page->menu_order;
	if ( 0 === $current_order ) {
		// Ordre par défaut basé sur l'index dans le tableau.
		$default_order = array_search( $slug, $service_slugs, true ) + 1;
		wp_update_post(
			array(
				'ID'         => $page->ID,
				'menu_order' => $default_order,
			)
		);
		echo "✅ OK (meta ajoutée, menu_order défini à {$default_order})\n";
	} else {
		echo "✅ OK (meta ajoutée, menu_order existant: {$current_order})\n";
	}

	++$updated_count;
}

echo "\n=== Résumé ===\n";
echo "Pages mises à jour: {$updated_count} / " . count( $service_slugs ) . "\n";

if ( ! empty( $errors ) ) {
	echo "\n⚠️  Erreurs/avertissements rencontrés:\n";
	foreach ( $errors as $error ) {
		echo "  - {$error}\n";
	}
	echo "\nVeuillez vérifier ces pages manuellement dans l'administration WordPress.\n";
} else {
	echo "\n✅ Migration terminée avec succès!\n";
}

echo "\nNote: Vous pouvez maintenant modifier l'ordre d'affichage des services en éditant\n";
echo "le champ 'Ordre' dans 'Attributs de page' de chaque page de service.\n";
