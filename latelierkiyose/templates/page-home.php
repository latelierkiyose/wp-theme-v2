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
		<?php
		// Decorative collage elements.
		get_template_part(
			'template-parts/decorative',
			'collage',
			array(
				'papers' => array(
					array(
						'class' => 'collage-paper--hero-main',
						'color' => '--kiyose-color-primary',
						'size'  => array( 400, 300 ),
						'path'  => 'M5 8 L18 3 L32 10 L47 5 L62 12 L78 6 L93 14 L108 7 L123 11 L138 4 L153 9 L168 13 L183 6 L198 10 L213 5 L228 12 L243 8 L258 14 L273 6 L288 11 L303 7 L318 13 L333 5 L348 10 L363 8 L378 12 L393 6 L400 9 L397 25 L400 42 L395 58 L398 75 L393 92 L397 108 L394 125 L399 142 L395 158 L398 175 L393 192 L397 208 L394 225 L399 242 L395 258 L398 275 L393 292 L396 300 L382 295 L368 300 L353 293 L338 298 L323 291 L308 297 L293 290 L278 296 L263 289 L248 295 L233 291 L218 297 L203 292 L188 298 L173 290 L158 296 L143 291 L128 297 L113 292 L98 298 L83 290 L68 295 L53 289 L38 296 L23 291 L8 297 L3 293 L6 278 L2 263 L7 248 L3 233 L8 218 L4 203 L7 188 L2 173 L6 158 L3 143 L7 128 L4 113 L8 98 L3 83 L6 68 L2 53 L7 38 L3 23 Z',
					),
					array(
						'class' => 'collage-paper--hero-accent',
						'color' => '--kiyose-color-turquoise',
						'size'  => array( 180, 150 ),
						'path'  => 'M3 6 L15 2 L28 8 L42 4 L55 10 L68 5 L82 11 L95 3 L108 9 L122 6 L135 12 L148 4 L162 8 L175 5 L180 10 L178 22 L180 35 L176 48 L180 62 L177 75 L180 88 L175 102 L179 115 L176 128 L180 142 L177 150 L163 146 L148 150 L133 144 L118 149 L103 143 L88 148 L73 142 L58 147 L43 141 L28 146 L13 140 L3 145 L6 132 L2 118 L7 105 L3 92 L8 78 L4 65 L7 52 L2 38 L5 25 Z',
					),
				),
				'micros' => array(
					array(
						'type'    => 'spiral',
						'style'   => 'top: 15%; left: 8%; width: 96px; height: 96px',
						'viewbox' => '40 40',
						'svg'     => '<path d="M20 22 C18 20 17 17 19 15 C21 13 24 14 25 16 C27 19 25 23 22 24 C18 26 14 23 13 19 C12 14 15 10 20 9" stroke="var(--kiyose-color-accent)" stroke-width="2" fill="none" stroke-linecap="round"/>',
					),
					array(
						'type'    => 'stroke',
						'style'   => 'bottom: 20%; right: 15%; width: 120px; height: 48px',
						'viewbox' => '50 20',
						'svg'     => '<path d="M3 12 L12 8 L22 14 L33 7 L45 11" stroke="var(--kiyose-color-coral)" stroke-width="2.5" fill="none" stroke-linecap="round"/>',
					),
					array(
						'type'    => 'dot',
						'style'   => 'top: 60%; left: 3%; width: 30px; height: 30px',
						'viewbox' => '30 30',
						'svg'     => '<path d="M15 5 C20 4 25 8 24 14 C23 20 18 23 13 21 C8 19 6 13 9 8 C10 6 12 5 15 5 Z" fill="var(--kiyose-color-turquoise)"/>',
					),
				),
			)
		);
		?>
	</section>

	<div class="section-divider section-divider--beige-to-white" role="presentation" aria-hidden="true"></div>

	<!-- Section 4 Piliers -->
	<section class="home-services section--with-overlay" aria-labelledby="services-title">
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
		<?php
		// Decorative collage elements.
		get_template_part(
			'template-parts/decorative',
			'collage',
			array(
				'papers' => array(
					array(
						'class' => 'collage-paper--services',
						'color' => '--kiyose-color-coral',
						'size'  => array( 300, 220 ),
						'path'  => 'M4 7 L17 3 L30 9 L44 5 L57 11 L71 4 L84 8 L97 3 L111 10 L124 6 L137 12 L151 5 L164 9 L177 4 L191 11 L204 7 L217 13 L231 5 L244 9 L257 4 L271 10 L284 6 L297 8 L300 12 L298 28 L300 44 L296 60 L300 76 L297 92 L300 108 L295 124 L299 140 L296 156 L300 172 L297 188 L300 204 L296 220 L282 216 L268 220 L253 214 L238 219 L223 213 L208 218 L193 212 L178 217 L163 211 L148 216 L133 210 L118 215 L103 209 L88 214 L73 208 L58 213 L43 207 L28 212 L13 208 L3 211 L6 196 L2 180 L7 164 L3 148 L8 132 L4 116 L7 100 L2 84 L5 68 L3 52 L7 36 L4 20 Z',
					),
				),
				'micros' => array(
					array(
						'type'    => 'circle',
						'style'   => 'top: 8%; right: 5%; width: 84px; height: 84px',
						'viewbox' => '35 35',
						'svg'     => '<path d="M17 3 C24 2 31 8 30 16 C29 24 22 30 14 28 C7 26 2 19 4 12 C5 7 10 3 17 3" stroke="var(--kiyose-color-accent)" stroke-width="2" fill="none" stroke-linecap="round"/>',
					),
					array(
						'type'    => 'cross',
						'style'   => 'bottom: 12%; left: 6%; width: 72px; height: 72px',
						'viewbox' => '30 30',
						'svg'     => '<path d="M6 6 L24 24 M24 6 L6 24" stroke="var(--kiyose-color-turquoise)" stroke-width="2.5" fill="none" stroke-linecap="round"/>',
					),
				),
			)
		);
		?>
	</section>

	<div class="section-divider section-divider--white-to-tinted" role="presentation" aria-hidden="true"></div>

	<!-- Section Prochaines Dates -->
	<section class="home-events section--tinted section--with-overlay" aria-labelledby="events-title">
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
		<?php
		// Decorative collage elements.
		get_template_part(
			'template-parts/decorative',
			'collage',
			array(
				'papers' => array(
					array(
						'class' => 'collage-paper--events',
						'color' => '--kiyose-color-turquoise',
						'size'  => array( 250, 280 ),
						'path'  => 'M3 8 L16 4 L29 10 L43 5 L56 12 L69 6 L83 11 L96 3 L109 9 L123 5 L136 13 L149 7 L163 10 L176 4 L189 8 L203 12 L216 5 L229 9 L243 6 L250 10 L248 26 L250 43 L246 59 L250 76 L247 92 L250 109 L245 125 L249 142 L246 158 L250 175 L247 191 L250 208 L245 224 L249 241 L246 257 L250 274 L247 280 L233 276 L218 280 L203 274 L188 279 L173 273 L158 278 L143 272 L128 277 L113 271 L98 276 L83 270 L68 275 L53 269 L38 274 L23 270 L8 275 L3 271 L6 255 L2 239 L7 223 L3 207 L8 191 L4 175 L7 159 L2 143 L5 127 L3 111 L7 95 L4 79 L8 63 L3 47 L6 31 Z',
					),
				),
				'micros' => array(
					array(
						'type'    => 'star',
						'style'   => 'top: 10%; left: 4%; width: 35px; height: 35px',
						'viewbox' => '35 35',
						'svg'     => '<path d="M17 2 L20 11 L29 9 L23 16 L30 23 L21 21 L17 30 L14 21 L5 23 L11 16 L4 9 L13 11 Z" fill="var(--kiyose-color-coral)"/>',
					),
					array(
						'type'    => 'stroke',
						'style'   => 'bottom: 15%; right: 8%; width: 108px; height: 42px',
						'viewbox' => '45 18',
						'svg'     => '<path d="M3 10 L14 6 L25 12 L36 5 L43 9" stroke="var(--kiyose-color-primary)" stroke-width="2" fill="none" stroke-linecap="round"/>',
					),
				),
			)
		);
		?>
	</section>

	<div class="section-divider section-divider--tinted-to-white" role="presentation" aria-hidden="true"></div>

	<!-- Section Témoignages (Carousel) -->
	<section class="home-testimonials section--with-overlay" aria-labelledby="testimonials-title">
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
		<?php
		// Decorative collage elements.
		get_template_part(
			'template-parts/decorative',
			'collage',
			array(
				'papers' => array(
					array(
						'class' => 'collage-paper--testimonials',
						'color' => '--kiyose-color-primary',
						'size'  => array( 200, 180 ),
						'path'  => 'M4 6 L17 2 L30 8 L44 4 L57 10 L70 5 L84 11 L97 3 L110 9 L124 6 L137 12 L150 4 L163 8 L176 3 L189 10 L200 7 L198 22 L200 38 L196 54 L200 70 L197 86 L200 102 L195 118 L199 134 L196 150 L200 166 L197 180 L183 176 L168 180 L153 174 L138 179 L123 173 L108 178 L93 172 L78 177 L63 171 L48 176 L33 172 L18 177 L4 173 L7 158 L3 142 L8 126 L4 110 L7 94 L2 78 L6 62 L3 46 L7 30 L4 14 Z',
					),
				),
				'micros' => array(
					array(
						'type'    => 'spiral',
						'style'   => 'bottom: 10%; left: 5%; width: 90px; height: 90px',
						'viewbox' => '38 38',
						'svg'     => '<path d="M19 21 C17 19 16 16 18 14 C20 12 23 13 24 15 C26 18 24 22 21 23 C17 25 13 22 12 18 C11 13 14 9 19 8" stroke="var(--kiyose-color-turquoise)" stroke-width="2" fill="none" stroke-linecap="round"/>',
					),
					array(
						'type'    => 'dot',
						'style'   => 'top: 12%; right: 10%; width: 25px; height: 25px',
						'viewbox' => '25 25',
						'svg'     => '<path d="M12 3 C17 2 22 6 21 12 C20 17 15 20 10 18 C6 16 4 11 6 7 C7 5 9 3 12 3 Z" fill="var(--kiyose-color-accent)"/>',
					),
				),
			)
		);
		?>
	</section>

	<div class="section-divider section-divider--white-to-tinted" role="presentation" aria-hidden="true"></div>

	<!-- Section Newsletter -->
	<section class="home-newsletter section--tinted" aria-labelledby="newsletter-title">
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

	<div class="section-divider section-divider--tinted-to-beige" role="presentation" aria-hidden="true"></div>

	<!-- Section Actualités (Blog) -->
	<section class="home-blog section--with-overlay" aria-labelledby="blog-title">
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
		<?php
		// Decorative collage elements.
		get_template_part(
			'template-parts/decorative',
			'collage',
			array(
				'papers' => array(
					array(
						'class' => 'collage-paper--blog',
						'color' => '--kiyose-color-coral',
						'size'  => array( 230, 200 ),
						'path'  => 'M5 7 L18 3 L31 9 L45 5 L58 11 L71 4 L85 8 L98 3 L111 10 L125 6 L138 12 L151 5 L165 9 L178 4 L191 11 L205 7 L218 13 L230 8 L228 24 L230 40 L226 56 L230 72 L227 88 L230 104 L225 120 L229 136 L226 152 L230 168 L227 184 L230 200 L216 196 L201 200 L186 194 L171 199 L156 193 L141 198 L126 192 L111 197 L96 191 L81 196 L66 190 L51 195 L36 191 L21 196 L5 192 L8 176 L3 160 L7 144 L3 128 L8 112 L4 96 L7 80 L2 64 L5 48 L3 32 L7 16 Z',
					),
				),
				'micros' => array(
					array(
						'type'    => 'circle',
						'style'   => 'top: 15%; right: 4%; width: 78px; height: 78px',
						'viewbox' => '32 32',
						'svg'     => '<path d="M16 3 C22 2 28 7 27 14 C26 21 20 27 13 25 C7 23 3 17 5 11 C6 7 10 3 16 3" stroke="var(--kiyose-color-turquoise)" stroke-width="2" fill="none" stroke-linecap="round"/>',
					),
					array(
						'type'    => 'cross',
						'style'   => 'bottom: 8%; left: 8%; width: 68px; height: 68px',
						'viewbox' => '28 28',
						'svg'     => '<path d="M5 5 L23 23 M23 5 L5 23" stroke="var(--kiyose-color-accent)" stroke-width="2.5" fill="none" stroke-linecap="round"/>',
					),
				),
			)
		);
		?>
	</section>
</main>

<?php
get_footer();
