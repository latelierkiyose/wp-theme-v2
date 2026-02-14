<?php
/**
 * Template Name: Page d'accueil
 *
 * @package Kiyose
 * @since   0.1.9
 */

get_header();
?>

<main id="main" class="site-main home-page">
	<!-- Hero Section -->
	<?php
	// Récupérer les données du hero depuis les meta fields.
	$kiyose_hero_title    = get_post_meta( get_the_ID(), 'kiyose_hero_title', true );
	$kiyose_hero_content  = get_post_meta( get_the_ID(), 'kiyose_hero_content', true );
	$kiyose_hero_cta_text = get_post_meta( get_the_ID(), 'kiyose_hero_cta_text', true );
	$kiyose_hero_cta_url  = get_post_meta( get_the_ID(), 'kiyose_hero_cta_url', true );
	$kiyose_hero_image_id = get_post_meta( get_the_ID(), 'kiyose_hero_image_id', true );

	// Valeurs par défaut.
	if ( empty( $kiyose_hero_title ) ) {
		$kiyose_hero_title = 'L\'être humain est un vitrail, révélons ensemble sa lumière';
	}
	if ( empty( $kiyose_hero_content ) ) {
		$kiyose_hero_content = 'Au sein de L\'Atelier Kiyose, découvrez des pratiques de bien-être et de développement personnel qui vous accompagnent dans votre transformation : art-thérapie, rigologie, bols tibétains et ateliers philo.';
	}
	if ( empty( $kiyose_hero_cta_text ) ) {
		$kiyose_hero_cta_text = 'Découvrir les prochains ateliers';
	}
	if ( empty( $kiyose_hero_cta_url ) ) {
		$kiyose_hero_cta_url = home_url( '/calendrier-tarifs/' );
	}

	$kiyose_has_image = ! empty( $kiyose_hero_image_id );
	?>
	<section class="hero-section<?php echo $kiyose_has_image ? ' hero-section--with-image' : ''; ?>" aria-label="Présentation de l'atelier">
		<div class="hero-section__inner">
			<?php if ( $kiyose_has_image ) : ?>
				<div class="hero-section__image">
					<?php echo wp_get_attachment_image( $kiyose_hero_image_id, 'medium_large', false, array( 'alt' => esc_attr( $kiyose_hero_title ) ) ); ?>
				</div>
			<?php endif; ?>
			<div class="hero-section__content">
				<h1 class="hero-section__title"><?php echo esc_html( $kiyose_hero_title ); ?></h1>
				<p class="hero-section__subtitle">
					<?php echo esc_html( $kiyose_hero_content ); ?>
				</p>
				<a href="<?php echo esc_url( $kiyose_hero_cta_url ); ?>" class="button button--primary hero-section__cta">
					<?php echo esc_html( $kiyose_hero_cta_text ); ?>
				</a>
			</div>
		</div>
	</section>

	<!-- Section 4 Piliers -->
	<section class="home-services" aria-labelledby="services-title">
		<h2 id="services-title" class="home-services__title">Nos activités</h2>
		<div class="home-services__grid">
			<?php
			// Récupérer les pages des 4 piliers via leur slug.
			$kiyose_service_slugs = array( 'art-therapie', 'rigologie', 'bols-tibetains', 'ateliers-philosophie' );

			foreach ( $kiyose_service_slugs as $kiyose_slug ) {
				$kiyose_service_page = get_page_by_path( $kiyose_slug );

				if ( $kiyose_service_page ) {
					// Setup post data pour get_template_part.
					global $post;
					$post = $kiyose_service_page; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					setup_postdata( $post );

					get_template_part( 'template-parts/content', 'service' );

					wp_reset_postdata();
				}
			}
			?>
		</div>
	</section>

	<!-- Section Prochaines Dates -->
	<section class="home-events" aria-labelledby="events-title">
		<h2 id="events-title" class="home-events__title">Prochaines dates</h2>
		<div class="home-events__list">
			<?php
			// Utilisation du shortcode Events Manager.
			// Le shortcode sera affiché une fois Events Manager activé.
			// Pour l'instant, affichage d'un placeholder.
			if ( shortcode_exists( 'events_list' ) ) {
				echo do_shortcode( '[events_list limit="4" scope="future" orderby="event_start_date" order="ASC"]' );
			} else {
				?>
				<p class="home-events__placeholder">
					Les prochains événements seront affichés ici une fois le plugin Events Manager activé.
				</p>
				<?php
			}
			?>
		</div>
		<a href="<?php echo esc_url( home_url( '/calendrier-tarifs/' ) ); ?>" class="button button--secondary home-events__cta">
			Voir tout le calendrier
		</a>
	</section>

	<!-- Section Témoignages (Carousel) -->
	<section class="home-testimonials" aria-labelledby="testimonials-title">
		<h2 id="testimonials-title" class="home-testimonials__title">Témoignages</h2>
		<?php
		// Query testimonials.
		$kiyose_testimonials_query = new WP_Query(
			array(
				'post_type'      => 'kiyose_testimony',
				'posts_per_page' => 5,
				'orderby'        => 'rand',
			)
		);

		if ( $kiyose_testimonials_query->have_posts() ) :
			?>
			<div
				class="carousel"
				role="region"
				aria-roledescription="carousel"
				aria-label="Témoignages de participants"
			>
				<div class="carousel__controls">
					<button class="carousel__prev" aria-label="Témoignage précédent">←</button>
					<button class="carousel__pause" aria-label="Mettre en pause le défilement">⏸</button>
					<button class="carousel__next" aria-label="Témoignage suivant">→</button>
				</div>
				<div class="carousel__track" aria-live="polite">
					<?php
					while ( $kiyose_testimonials_query->have_posts() ) :
						$kiyose_testimonials_query->the_post();
						?>
						<div class="carousel__slide" role="group" aria-roledescription="slide" aria-label="">
							<?php get_template_part( 'template-parts/content', 'testimony' ); ?>
						</div>
						<?php
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			</div>
			<?php
		else :
			?>
			<p class="home-testimonials__empty">Aucun témoignage disponible pour le moment.</p>
			<?php
		endif;
		?>
	</section>

	<!-- Section Newsletter -->
	<section class="home-newsletter" aria-labelledby="newsletter-title">
		<div class="home-newsletter__content">
			<h2 id="newsletter-title" class="home-newsletter__title">Restez informé·e</h2>
			<p class="home-newsletter__description">
				Recevez les prochaines dates et actualités de l'atelier directement dans votre boîte mail.
			</p>
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
				<div class="home-newsletter__form-placeholder">
					<p><?php esc_html_e( 'Le formulaire d\'inscription à la newsletter sera intégré ici une fois le plugin Brevo configuré.', 'kiyose' ); ?></p>
				</div>
				<?php
			}
			?>
		</div>
	</section>

	<!-- Section Actualités (Blog) -->
	<section class="home-blog" aria-labelledby="blog-title">
		<h2 id="blog-title" class="home-blog__title">Actualités</h2>
		<?php
		// Query 3 derniers articles de blog.
		$kiyose_blog_query = new WP_Query(
			array(
				'post_type'      => 'post',
				'posts_per_page' => 3,
				'orderby'        => 'date',
				'order'          => 'DESC',
			)
		);

		if ( $kiyose_blog_query->have_posts() ) :
			?>
			<div class="home-blog__grid">
				<?php
				while ( $kiyose_blog_query->have_posts() ) :
					$kiyose_blog_query->the_post();
					get_template_part( 'template-parts/content', 'blog' );
				endwhile;
				wp_reset_postdata();
				?>
			</div>
			<a href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ); ?>" class="button button--secondary home-blog__cta">
				Toutes les actualités
			</a>
			<?php
		else :
			?>
			<p class="home-blog__empty">Aucune actualité disponible pour le moment.</p>
			<?php
		endif;
		?>
	</section>
</main>

<?php
get_footer();
