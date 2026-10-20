<?php
/**
 * Gestion du consentement aux cookies (tarteaucitron).
 *
 * @package Kiyose
 */

add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_script( 'tarteaucitron', get_template_directory_uri() . '/assets/js/tarteaucitron/tarteaucitron.min.js', array(), KIYOSE_VERSION, false );
		wp_enqueue_script( 'tarteaucitron-config', get_template_directory_uri() . '/assets/js/tarteaucitron-config.js', array( 'tarteaucitron' ), KIYOSE_VERSION, false );
	}
);
