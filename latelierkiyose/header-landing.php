<?php
/**
 * L'Atelier Kiyose - Header template for landing pages.
 *
 * Volontairement dépouillé : une landing page n'affiche ni en-tête de site,
 * ni navigation, ni menu mobile. Seuls le skip link et les hooks WordPress
 * indispensables (wp_head, wp_body_open) sont conservés.
 *
 * @package Kiyose
 * @since   2.4.0
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'landing-page' ); ?>>
<?php wp_body_open(); ?>

<?php echo kiyose_get_skip_link(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
