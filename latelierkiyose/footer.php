<?php
/**
 * L'Atelier Kiyose - Footer template.
 *
 * @package Kiyose
 * @since   0.1.0
 */

$kiyose_footer_legal_links = kiyose_get_footer_legal_links();

?>
	<footer class="site-footer" role="contentinfo">
		<div class="site-footer__container">
			
			<?php echo kiyose_get_social_links_html( 'footer' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

		<div class="site-footer__newsletter">
			<h3 class="site-footer__newsletter-title"><?php esc_html_e( 'Newsletter', 'kiyose' ); ?></h3>
			<?php
			/**
			 * Formulaire newsletter Brevo.
			 *
			 * Le plugin Brevo expose le shortcode [sibwp_form].
			 *
			 * @since 0.1.13
			 */
			if ( shortcode_exists( 'sibwp_form' ) ) {
				echo do_shortcode( '[sibwp_form id=1]' );
			} else {
				?>
				<div class="site-footer__newsletter-placeholder">
					<p><?php esc_html_e( 'Restez informé·e de nos actualités et prochains ateliers.', 'kiyose' ); ?></p>
					<p class="site-footer__newsletter-notice">
						<em><?php esc_html_e( 'Le formulaire d\'inscription à la newsletter sera disponible une fois le plugin Brevo configuré.', 'kiyose' ); ?></em>
					</p>
				</div>
				<?php
			}
			?>
		</div>

			<?php if ( ! empty( $kiyose_footer_legal_links ) ) : ?>
				<div class="site-footer__legal">
					<?php foreach ( $kiyose_footer_legal_links as $kiyose_footer_legal_link ) : ?>
						<a href="<?php echo esc_url( $kiyose_footer_legal_link['url'] ); ?>"><?php echo esc_html( $kiyose_footer_legal_link['label'] ); ?></a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<div class="site-footer__copyright">
				<p>
					<?php
					printf(
						/* translators: 1: year, 2: site name, 3: SIRET number */
						esc_html__( '&copy; %1$s %2$s — SIRET %3$s', 'kiyose' ),
						esc_html( gmdate( 'Y' ) ),
						esc_html( get_bloginfo( 'name' ) ),
						'49219620900026'
					);
					?>
				</p>
			</div>

		</div>
	</footer>

	<?php wp_footer(); ?>
</body>
</html>
