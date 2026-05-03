<?php
/**
 * Home mobile about section.
 *
 * @package Kiyose
 * @since   0.2.7
 *
 * @param array $args Home page data.
 */

defined( 'ABSPATH' ) || exit;

$kiyose_about_content   = $args['about_content'] ?? '';
$kiyose_about_cta_text  = $args['about_cta_text'] ?? '';
$kiyose_about_cta_url   = $args['about_cta_url'] ?? '';
$kiyose_about_image_id  = $args['about_image_id'] ?? '';
$kiyose_has_about_image = ! empty( $kiyose_about_image_id );
?>

<div class="home-about-mobile">
	<section class="home-about" aria-labelledby="about-mobile-heading">
		<?php if ( $kiyose_has_about_image ) : ?>
			<div class="home-about__image">
				<?php echo wp_get_attachment_image( $kiyose_about_image_id, 'medium', false, array( 'alt' => '' ) ); ?>
			</div>
		<?php endif; ?>
		<h2 id="about-mobile-heading" class="home-about__title">
			<?php esc_html_e( 'À propos', 'kiyose' ); ?>
		</h2>
		<p class="home-about__text"><?php echo esc_html( $kiyose_about_content ); ?></p>
		<a href="<?php echo esc_url( $kiyose_about_cta_url ); ?>" class="home-about__link">
			<?php echo esc_html( $kiyose_about_cta_text ); ?>
		</a>
	</section>
</div>
