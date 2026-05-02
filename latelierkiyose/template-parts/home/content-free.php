<?php
/**
 * Home free text section.
 *
 * @package Kiyose
 * @since   0.2.7
 *
 * @param array $args Home page data.
 */

$kiyose_content2_text   = $args['content2_text'] ?? '';
$kiyose_content2_slogan = $args['content2_slogan'] ?? '';
?>

<?php if ( ! empty( $kiyose_content2_text ) || ! empty( $kiyose_content2_slogan ) ) : ?>
<section class="home-content2" aria-labelledby="content2-heading">
	<h2 id="content2-heading" class="sr-only">
		<?php esc_html_e( 'À propos de ma pratique', 'kiyose' ); ?>
	</h2>
	<div class="home-content2__inner">
		<?php if ( ! empty( $kiyose_content2_text ) ) : ?>
			<div class="home-content2__text">
				<?php echo wp_kses_post( $kiyose_content2_text ); ?>
			</div>
		<?php endif; ?>
		<?php if ( ! empty( $kiyose_content2_slogan ) ) : ?>
			<p class="home-content2__slogan home-emphase"><?php echo kiyose_fr_nbsp( esc_html( $kiyose_content2_slogan ) ); ?></p>
		<?php endif; ?>
		<div class="home-content2__cta-wrapper">
			<a href="<?php echo esc_url( KIYOSE_DISCOVERY_CALL_URL ); ?>" class="home-content2__cta">
				<?php esc_html_e( 'Réserver votre appel découverte', 'kiyose' ); ?>
			</a>
		</div>
	</div>
</section>
<?php endif; ?>
