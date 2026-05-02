<?php
/**
 * Template Name: Page de contact
 *
 * Template pour la page de contact avec formulaire Contact Form 7
 * et informations de contact.
 *
 * @package Kiyose
 */

get_header();
?>

<main id="main" class="site-main" tabindex="-1">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'contact-page' ); ?>>
			<header class="contact-page__header">
				<h1 class="contact-page__title"><?php the_title(); ?></h1>
				<p class="contact-page__intro">
					Je serais ravie d'échanger avec vous sur vos besoins et de vous accompagner dans votre démarche de mieux-être.
				</p>
			</header>

			<div class="contact-page__layout">
				<!-- Colonne formulaire -->
				<div class="contact-page__form">
					<h2>Envoyez-moi un message</h2>
					<?php the_content(); ?>
				</div>

				<!-- Colonne informations -->
				<aside class="contact-page__info" aria-label="Informations de contact">
					<?php
					$kiyose_contact_photo_id  = get_post_meta( get_the_ID(), 'kiyose_contact_photo_id', true );
					$kiyose_contact_photo_alt = get_post_meta( get_the_ID(), 'kiyose_contact_photo_alt', true );
					if ( ! empty( $kiyose_contact_photo_id ) ) :
						?>
					<div class="contact-page__photo">
						<?php
						echo wp_get_attachment_image(
							$kiyose_contact_photo_id,
							'medium',
							false,
							array( 'alt' => esc_attr( $kiyose_contact_photo_alt ) )
						);
						?>
					</div>
					<?php endif; ?>
					<h2>Coordonnées</h2>

					<div class="contact-info">
						<div class="contact-info__item">
							<h3>Téléphone</h3>
							<a href="tel:+33658373205">06 58 37 32 05</a>
						</div>

						<div class="contact-info__item">
							<h3>Email</h3>
							<a href="mailto:contact@latelierkiyose.fr">contact@latelierkiyose.fr</a>
						</div>

						<div class="contact-info__item">
							<h3>Adresse</h3>
							<address>
								Le Grand Vron<br>
								86510 Brux<br>
								Nouvelle-Aquitaine
							</address>
						</div>

						<?php
						$kiyose_contact_social_links = kiyose_get_social_links_html( 'contact' );
						if ( '' !== $kiyose_contact_social_links ) :
							?>
							<div class="contact-info__social">
								<h3>Réseaux sociaux</h3>
								<?php echo $kiyose_contact_social_links; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</div>
						<?php endif; ?>
					</div>
				</aside>
			</div>
		</article>
		<?php
	endwhile;
	?>
	<?php get_template_part( 'template-parts/page-shapes', 'container' ); ?>
</main>

<?php
get_footer();
