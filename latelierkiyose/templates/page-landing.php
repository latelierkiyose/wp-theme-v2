<?php
/**
 * Template Name: Landing page
 *
 * Template pour les pages d'acquisition (diffusion d'une ressource gratuite).
 * Rendu volontairement détaché du reste du site : ni en-tête, ni navigation,
 * ni pied de page institutionnel, ni décorations kintsugi.
 *
 * @package Kiyose
 * @since   2.4.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'landing' );
?>

<main id="main" class="landing" tabindex="-1">
	<div class="landing__container">
		<?php
		while ( have_posts() ) :
			the_post();
			the_content();
		endwhile;
		?>
	</div>
</main>

<?php
get_footer( 'landing' );
