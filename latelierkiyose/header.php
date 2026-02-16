<?php
/**
 * L'Atelier Kiyose - Header template.
 *
 * @package Kiyose
 * @since   0.1.0
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php echo kiyose_get_skip_link(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

<header class="site-header" role="banner">
	<div class="site-header__container">
	<?php if ( has_custom_logo() ) : ?>
		<div class="site-header__logo">
			<?php the_custom_logo(); ?>
		</div>
	<?php else : ?>
		<div class="site-header__logo-bubble">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-header__logo" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) . ' - ' . __( 'Accueil', 'kiyose' ) ); ?>">
				<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/logo-kiyose.png' ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" height="250" width="250">
			</a>
		</div>
	<?php endif; ?>

		<nav class="main-nav" role="navigation" aria-label="<?php esc_attr_e( 'Menu principal', 'kiyose' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'menu_class'     => 'main-nav__list',
					'container'      => false,
					'depth'          => 2,
					'fallback_cb'    => false,
				)
			);
			?>
		</nav>

		<button class="hamburger-button" aria-label="<?php esc_attr_e( 'Ouvrir le menu de navigation', 'kiyose' ); ?>" aria-expanded="false" aria-controls="mobile-menu">
			<span class="hamburger-button__line"></span>
			<span class="hamburger-button__line"></span>
			<span class="hamburger-button__line"></span>
		</button>
	</div>
</header>

<!-- Header divider (Kintsugi decoration) -->
<div class="section-divider section-divider--header" role="presentation" aria-hidden="true"></div>

<!-- Mobile Menu Overlay -->
<div class="mobile-menu-overlay" aria-hidden="true"></div>

<!-- Mobile Menu -->
<div id="mobile-menu" class="mobile-menu" aria-hidden="true" role="dialog" aria-label="<?php esc_attr_e( 'Menu de navigation', 'kiyose' ); ?>">
	<button class="mobile-menu__close" aria-label="<?php esc_attr_e( 'Fermer le menu de navigation', 'kiyose' ); ?>">
		<span class="mobile-menu__close-icon" aria-hidden="true">×</span>
	</button>

	<nav aria-label="<?php esc_attr_e( 'Menu principal', 'kiyose' ); ?>">
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'menu_class'     => 'mobile-menu__list',
				'container'      => false,
				'depth'          => 2,
				'fallback_cb'    => false,
			)
		);
		?>
	</nav>
</div>

<main id="main-content" role="main">
