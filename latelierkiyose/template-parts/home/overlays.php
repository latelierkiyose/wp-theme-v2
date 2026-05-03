<?php
/**
 * Home desktop overlays.
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

<div class="about-overlay" id="about-overlay" role="complementary" aria-label="<?php esc_attr_e( 'À propos de L\'Atelier Kiyose', 'kiyose' ); ?>" hidden>
	<button
		class="about-overlay__close"
		id="about-overlay-close"
		type="button"
		aria-label="<?php esc_attr_e( 'Fermer le panneau À propos', 'kiyose' ); ?>"
	>
		<span aria-hidden="true">&times;</span>
	</button>
	<div class="about-overlay__body">
		<?php if ( $kiyose_has_about_image ) : ?>
			<div class="about-overlay__image">
				<?php echo wp_get_attachment_image( $kiyose_about_image_id, 'thumbnail', false, array( 'alt' => '' ) ); ?>
			</div>
		<?php endif; ?>
		<div class="about-overlay__content">
			<p class="about-overlay__text"><?php echo esc_html( $kiyose_about_content ); ?></p>
			<a href="<?php echo esc_url( $kiyose_about_cta_url ); ?>" class="about-overlay__link">
				<?php echo esc_html( $kiyose_about_cta_text ); ?>
			</a>
		</div>
	</div>
</div>

<div class="signup-panel" id="signup-panel" role="complementary" aria-label="<?php esc_attr_e( 'Inscription à la newsletter', 'kiyose' ); ?>" hidden>
	<button
		class="signup-panel__close"
		id="signup-panel-close"
		type="button"
		aria-label="<?php esc_attr_e( 'Fermer le panneau Newsletter', 'kiyose' ); ?>"
	>
		<span aria-hidden="true">&times;</span>
	</button>
	<div class="signup-panel__body">
		<h2 class="signup-panel__title"><?php esc_html_e( 'Restez informés', 'kiyose' ); ?></h2>
		<p class="signup-panel__description">
			<?php esc_html_e( 'Recevez les prochaines dates et actualités directement dans votre boîte mail.', 'kiyose' ); ?>
		</p>
		<div class="signup-panel__form" data-signup-panel-form></div>
	</div>
</div>

<div class="sr-only" id="overlay-announcement" aria-live="polite" aria-atomic="true"></div>
