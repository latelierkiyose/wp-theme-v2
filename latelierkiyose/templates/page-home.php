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
	<section class="hero-section hero-section--with-overlay<?php echo $kiyose_has_image ? ' hero-section--with-image' : ''; ?>" aria-label="Présentation de l'atelier">
		<?php
		get_template_part(
			'template-parts/decorative',
			'scribbles',
			array(
				'type'     => 'squiggle',
				'color'    => '--kiyose-color-warm-yellow',
				'size'     => 80,
				'rotation' => -20,
				'float'    => true,
				'bottom'   => '12%',
				'right'    => '5%',
			)
		);
		get_template_part(
			'template-parts/decorative',
			'scribbles',
			array(
				'type'     => 'cross',
				'color'    => '--kiyose-color-accent',
				'size'     => 35,
				'rotation' => 30,
				'float'    => false,
				'top'      => '18%',
				'left'     => '3%',
			)
		);
		?>
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

	<?php
	// Wave separator — Hero (beige) → Services (white).
	get_template_part(
		'template-parts/decorative',
		'shapes',
		array(
			'mode'    => 'separator',
			'color'   => '#ffffff',
			'from_bg' => '--kiyose-color-background',
		)
	);
	?>

	<!-- Section 4 Piliers -->
	<section class="home-services" aria-labelledby="services-title">
		<?php
		get_template_part(
			'template-parts/decorative',
			'scribbles',
			array(
				'type'     => 'spiral',
				'color'    => '--kiyose-color-primary',
				'size'     => 100,
				'rotation' => -8,
				'float'    => true,
				'top'      => '5%',
				'left'     => '2%',
			)
		);
		get_template_part(
			'template-parts/decorative',
			'scribbles',
			array(
				'type'     => 'cross',
				'color'    => '--kiyose-color-warm-yellow',
				'size'     => 45,
				'rotation' => 18,
				'float'    => false,
				'top'      => '12%',
				'right'    => '4%',
			)
		);
		get_template_part(
			'template-parts/decorative',
			'scribbles',
			array(
				'type'     => 'virgules',
				'color'    => '--kiyose-color-accent',
				'size'     => 55,
				'rotation' => -14,
				'float'    => false,
				'bottom'   => '8%',
				'right'    => '6%',
			)
		);
		?>
		<h2 id="services-title" class="home-services__title">Mes activités</h2>
		<div class="home-services__grid">
			<?php
			// Récupérer les pages de services marquées pour affichage sur la page d'accueil.
			$kiyose_service_pages = get_posts(
				array(
					'post_type'      => 'page',
					'meta_key'       => 'kiyose_show_on_homepage', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Query intentionnelle.
					'meta_value'     => '1', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- Query intentionnelle.
					'orderby'        => 'menu_order',
					'order'          => 'ASC',
					'posts_per_page' => -1,
				)
			);

			if ( ! empty( $kiyose_service_pages ) ) {
				foreach ( $kiyose_service_pages as $kiyose_service_page ) {
					// Setup post data pour get_template_part.
					global $post;
					$post = $kiyose_service_page; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					setup_postdata( $post );

					get_template_part( 'template-parts/content', 'service' );

					wp_reset_postdata();
				}
			} else {
				?>
				<p class="home-services__empty">
					<?php esc_html_e( 'Aucun service n\'est actuellement configuré pour l\'affichage sur la page d\'accueil.', 'kiyose' ); ?>
				</p>
				<?php
			}
			?>
		</div>
	</section>

	<?php
	// Wave separator — Services (white) → Events (tinted).
	get_template_part(
		'template-parts/decorative',
		'shapes',
		array(
			'mode'    => 'separator',
			'color'   => 'rgb(201 171 167 / 10%)',
			'from_bg' => '#ffffff',
		)
	);
	?>

	<!-- Section Prochaines Dates -->
	<section class="home-events section--tinted" aria-labelledby="events-title">
		<?php
		get_template_part(
			'template-parts/decorative',
			'scribbles',
			array(
				'type'     => 'squiggle',
				'color'    => '--kiyose-color-warm-yellow',
				'size'     => 120,
				'rotation' => 15,
				'float'    => true,
				'top'      => '8%',
				'right'    => '3%',
			)
		);
		get_template_part(
			'template-parts/decorative',
			'scribbles',
			array(
				'type'     => 'spiral',
				'color'    => '--kiyose-color-primary',
				'size'     => 65,
				'rotation' => -22,
				'float'    => false,
				'bottom'   => '12%',
				'left'     => '3%',
			)
		);
		get_template_part(
			'template-parts/decorative',
			'scribbles',
			array(
				'type'     => 'mini-splash',
				'color'    => '--kiyose-color-accent',
				'size'     => 50,
				'rotation' => 8,
				'float'    => false,
				'top'      => '45%',
				'left'     => '1%',
			)
		);
		?>
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

	<?php
	// Wave separator — Events (tinted) → Testimonials (white).
	get_template_part(
		'template-parts/decorative',
		'shapes',
		array(
			'mode'    => 'separator',
			'color'   => '#ffffff',
			'from_bg' => 'rgb(201 171 167 / 10%)',
		)
	);
	?>

	<!-- Section Témoignages (Carousel) -->
	<section class="home-testimonials" aria-labelledby="testimonials-title">
		<?php
		get_template_part(
			'template-parts/decorative',
			'scribbles',
			array(
				'type'     => 'mini-splash',
				'color'    => '--kiyose-color-warm-yellow',
				'size'     => 70,
				'rotation' => 20,
				'float'    => false,
				'top'      => '10%',
				'left'     => '5%',
			)
		);
		get_template_part(
			'template-parts/decorative',
			'scribbles',
			array(
				'type'     => 'squiggle',
				'color'    => '--kiyose-color-primary',
				'size'     => 90,
				'rotation' => -12,
				'float'    => true,
				'bottom'   => '15%',
				'right'    => '2%',
			)
		);
		get_template_part(
			'template-parts/decorative',
			'scribbles',
			array(
				'type'     => 'cross',
				'color'    => '--kiyose-color-accent',
				'size'     => 40,
				'rotation' => 35,
				'float'    => false,
				'top'      => '55%',
				'right'    => '8%',
			)
		);
		?>
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

	<?php
	// Wave separator — Testimonials (white) → Newsletter (tinted).
	get_template_part(
		'template-parts/decorative',
		'shapes',
		array(
			'mode'    => 'separator',
			'color'   => 'rgb(201 171 167 / 10%)',
			'from_bg' => '#ffffff',
		)
	);
	?>

	<!-- Section Newsletter -->
	<section class="home-newsletter section--tinted" aria-labelledby="newsletter-title">
		<?php
		get_template_part(
			'template-parts/decorative',
			'scribbles',
			array(
				'type'     => 'virgules',
				'color'    => '--kiyose-color-burgundy',
				'size'     => 60,
				'rotation' => -10,
				'float'    => false,
				'top'      => '15%',
				'right'    => '5%',
			)
		);
		get_template_part(
			'template-parts/decorative',
			'scribbles',
			array(
				'type'     => 'squiggle',
				'color'    => '--kiyose-color-warm-yellow',
				'size'     => 80,
				'rotation' => 25,
				'float'    => false,
				'bottom'   => '10%',
				'left'     => '4%',
			)
		);
		?>
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

	<?php
	// Wave separator — Newsletter (tinted) → Blog (beige).
	get_template_part(
		'template-parts/decorative',
		'shapes',
		array(
			'mode'    => 'separator',
			'color'   => '--kiyose-color-background',
			'from_bg' => 'rgb(201 171 167 / 10%)',
		)
	);
	?>

	<!-- Section Actualités (Blog) -->
	<section class="home-blog" aria-labelledby="blog-title">
		<?php
		get_template_part(
			'template-parts/decorative',
			'scribbles',
			array(
				'type'     => 'cross',
				'color'    => '--kiyose-color-accent',
				'size'     => 50,
				'rotation' => 22,
				'float'    => true,
				'top'      => '6%',
				'right'    => '7%',
			)
		);
		get_template_part(
			'template-parts/decorative',
			'scribbles',
			array(
				'type'     => 'virgules',
				'color'    => '--kiyose-color-primary',
				'size'     => 50,
				'rotation' => -18,
				'float'    => false,
				'bottom'   => '10%',
				'left'     => '2%',
			)
		);
		get_template_part(
			'template-parts/decorative',
			'scribbles',
			array(
				'type'     => 'mini-splash',
				'color'    => '--kiyose-color-warm-yellow',
				'size'     => 55,
				'rotation' => 12,
				'float'    => false,
				'top'      => '40%',
				'left'     => '6%',
			)
		);
		?>
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

	<!-- Decorative shapes — distributed along page margins -->
	<div class="deco-page-shapes" aria-hidden="true">
		<?php
		get_template_part(
			'template-parts/decorative',
			'shapes',
			array(
				'type'     => 'blob',
				'color'    => '--kiyose-color-accent',
				'opacity'  => 0.3,
				'size'     => 'sm',
				'position' => 'margin-left',
				'top'      => '1%',
				'rotation' => 15,
			)
		);
		get_template_part(
			'template-parts/decorative',
			'shapes',
			array(
				'type'     => 'splash',
				'color'    => '--kiyose-color-accent',
				'opacity'  => 0.4,
				'size'     => 'md',
				'position' => 'margin-right',
				'top'      => '3%',
				'rotation' => -6,
			)
		);
		get_template_part(
			'template-parts/decorative',
			'shapes',
			array(
				'type'     => 'brushstroke',
				'color'    => '--kiyose-color-primary',
				'opacity'  => 0.25,
				'size'     => 'sm',
				'position' => 'margin-left',
				'top'      => '9%',
				'rotation' => 20,
			)
		);
		get_template_part(
			'template-parts/decorative',
			'shapes',
			array(
				'type'     => 'blob',
				'color'    => '--kiyose-color-primary',
				'opacity'  => 0.3,
				'size'     => 'sm',
				'position' => 'margin-left',
				'top'      => '15%',
				'rotation' => 12,
			)
		);
		get_template_part(
			'template-parts/decorative',
			'shapes',
			array(
				'type'     => 'splash',
				'color'    => '--kiyose-color-accent',
				'opacity'  => 0.3,
				'size'     => 'sm',
				'position' => 'margin-right',
				'top'      => '21%',
				'rotation' => -10,
			)
		);
		get_template_part(
			'template-parts/decorative',
			'shapes',
			array(
				'type'     => 'blob',
				'color'    => '--kiyose-color-accent',
				'opacity'  => 0.25,
				'size'     => 'sm',
				'position' => 'margin-right',
				'top'      => '28%',
				'rotation' => 5,
			)
		);
		get_template_part(
			'template-parts/decorative',
			'shapes',
			array(
				'type'     => 'splash',
				'color'    => '--kiyose-color-primary',
				'opacity'  => 0.35,
				'size'     => 'md',
				'position' => 'margin-left',
				'top'      => '42%',
				'rotation' => 4,
			)
		);
		get_template_part(
			'template-parts/decorative',
			'shapes',
			array(
				'type'     => 'brushstroke',
				'color'    => '--kiyose-color-accent',
				'opacity'  => 0.4,
				'size'     => 'sm',
				'position' => 'margin-right',
				'top'      => '56%',
				'rotation' => -12,
			)
		);
		get_template_part(
			'template-parts/decorative',
			'shapes',
			array(
				'type'     => 'spiral',
				'color'    => '--kiyose-color-accent',
				'opacity'  => 0.25,
				'size'     => 'sm',
				'position' => 'margin-left',
				'top'      => '71%',
				'rotation' => -15,
			)
		);
		get_template_part(
			'template-parts/decorative',
			'shapes',
			array(
				'type'     => 'blob',
				'color'    => '--kiyose-color-primary',
				'opacity'  => 0.3,
				'size'     => 'md',
				'position' => 'margin-right',
				'top'      => '86%',
				'rotation' => 7,
			)
		);
		?>
	</div>
</main>

<?php
get_footer();
