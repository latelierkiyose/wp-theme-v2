<?php
/**
 * L'Atelier Kiyose - 404 error page template.
 *
 * @package Kiyose
 * @since   0.1.0
 */

get_header();
?>

<main id="main" class="site-main">
	<section class="error-404">
		<h1 class="error-404__title"><?php esc_html_e( 'Page introuvable', 'kiyose' ); ?></h1>
		<p class="error-404__message">
			<?php esc_html_e( 'La page que vous recherchez n\'existe pas ou a été déplacée.', 'kiyose' ); ?>
		</p>

		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="button button--primary">
			<?php esc_html_e( 'Retour à l\'accueil', 'kiyose' ); ?>
		</a>

		<div class="error-404__search">
			<h2><?php esc_html_e( 'Rechercher sur le site', 'kiyose' ); ?></h2>
			<?php get_search_form(); ?>
		</div>

		<div class="error-404__suggestions">
			<h2><?php esc_html_e( 'Pages populaires', 'kiyose' ); ?></h2>
			<ul>
				<li><a href="<?php echo esc_url( home_url( '/art-therapie/' ) ); ?>"><?php esc_html_e( 'Art-thérapie', 'kiyose' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/rigologie/' ) ); ?>"><?php esc_html_e( 'Rigologie / Yoga du rire', 'kiyose' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/bols-tibetains/' ) ); ?>"><?php esc_html_e( 'Bols tibétains', 'kiyose' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/ateliers-philosophie/' ) ); ?>"><?php esc_html_e( 'Ateliers philosophie', 'kiyose' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/calendrier/' ) ); ?>"><?php esc_html_e( 'Calendrier des événements', 'kiyose' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'kiyose' ); ?></a></li>
			</ul>
		</div>
	</section>
</main>

<?php
get_footer();
