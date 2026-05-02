<?php
/**
 * L'Atelier Kiyose - Footer template.
 *
 * @package Kiyose
 * @since   0.1.0
 */

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
			 * Le shortcode [brevo_form id="X"] sera fourni par le plugin Brevo après configuration.
			 * Remplacer le placeholder ci-dessous par le shortcode réel en production.
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

			<div class="site-footer__legal">
				<a href="<?php echo esc_url( home_url( '/mentions-legales/' ) ); ?>"><?php esc_html_e( 'Mentions légales', 'kiyose' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/politique-de-confidentialite/' ) ); ?>"><?php esc_html_e( 'Politique de confidentialité', 'kiyose' ); ?></a>
			</div>

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
