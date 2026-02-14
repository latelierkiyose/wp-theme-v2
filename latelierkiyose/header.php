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
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-header__logo" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) . ' - ' . __( 'Accueil', 'kiyose' ) ); ?>">
				<span class="site-header__logo-text"><?php bloginfo( 'name' ); ?></span>
			</a>
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

		<button class="hamburger-button" aria-label="<?php esc_attr_e( 'Menu', 'kiyose' ); ?>" aria-expanded="false" aria-controls="mobile-menu">
			<span class="hamburger-button__line"></span>
			<span class="hamburger-button__line"></span>
			<span class="hamburger-button__line"></span>
		</button>
	</div>
</header>

<main id="main-content" role="main">
