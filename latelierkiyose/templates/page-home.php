<?php
/**
 * Template Name: Page d'accueil
 *
 * @package Kiyose
 * @since   0.1.9
 */

get_header();

// Récupérer les données du bloc bienvenue.
$kiyose_welcome_title        = get_post_meta( get_the_ID(), 'kiyose_welcome_title', true );
$kiyose_welcome_subtitle     = get_post_meta( get_the_ID(), 'kiyose_welcome_subtitle', true );
$kiyose_welcome_text         = get_post_meta( get_the_ID(), 'kiyose_welcome_text', true );
$kiyose_welcome_keywords_raw = get_post_meta( get_the_ID(), 'kiyose_welcome_keywords', true );
$kiyose_welcome_keywords     = $kiyose_welcome_keywords_raw ? json_decode( $kiyose_welcome_keywords_raw, true ) : array();
if ( ! is_array( $kiyose_welcome_keywords ) ) {
	$kiyose_welcome_keywords = array();
}
$kiyose_welcome_slogan = get_post_meta( get_the_ID(), 'kiyose_welcome_slogan', true );

// Récupérer les données de l'overlay / section À propos (champs hero existants).
$kiyose_hero_content  = get_post_meta( get_the_ID(), 'kiyose_hero_content', true );
$kiyose_hero_cta_text = get_post_meta( get_the_ID(), 'kiyose_hero_cta_text', true );
$kiyose_hero_cta_url  = get_post_meta( get_the_ID(), 'kiyose_hero_cta_url', true );
$kiyose_hero_image_id = get_post_meta( get_the_ID(), 'kiyose_hero_image_id', true );

// Récupérer les données Contenu 1 (Q&A).
$kiyose_content1_qa_raw = get_post_meta( get_the_ID(), 'kiyose_content1_qa', true );
$kiyose_content1_qa     = $kiyose_content1_qa_raw ? json_decode( $kiyose_content1_qa_raw, true ) : array();
if ( ! is_array( $kiyose_content1_qa ) ) {
	$kiyose_content1_qa = array();
}
$kiyose_content1_slogan = get_post_meta( get_the_ID(), 'kiyose_content1_slogan', true );

// Récupérer les données Contenu 2.
$kiyose_content2_text   = get_post_meta( get_the_ID(), 'kiyose_content2_text', true );
$kiyose_content2_slogan = get_post_meta( get_the_ID(), 'kiyose_content2_slogan', true );

// Valeurs par défaut.
if ( empty( $kiyose_welcome_title ) ) {
	$kiyose_welcome_title = "L'être humain est un vitrail, révélons ensemble sa lumière";
}
if ( empty( $kiyose_hero_content ) ) {
	$kiyose_hero_content = "Au sein de L'Atelier Kiyose, découvrez des pratiques de bien-être et de développement personnel qui vous accompagnent dans votre transformation : art-thérapie, rigologie, bols tibétains et ateliers philo.";
}
if ( empty( $kiyose_hero_cta_text ) ) {
	$kiyose_hero_cta_text = "En savoir plus sur l'atelier";
}
if ( empty( $kiyose_hero_cta_url ) ) {
	$kiyose_hero_cta_url = home_url( '/a-propos/' );
}

$kiyose_has_about_image = ! empty( $kiyose_hero_image_id );
?>

<main id="main" class="site-main home-page">
	<!-- Bloc Bienvenue -->
	<section class="welcome-block" aria-label="<?php esc_attr_e( 'Bienvenue à L\'Atelier Kiyose', 'kiyose' ); ?>">
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
		<div class="welcome-block__inner">
			<h1 class="welcome-block__title"><?php echo esc_html( $kiyose_welcome_title ); ?></h1>
			<?php if ( ! empty( $kiyose_welcome_subtitle ) ) : ?>
				<p class="welcome-block__subtitle"><?php echo esc_html( $kiyose_welcome_subtitle ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $kiyose_welcome_text ) ) : ?>
				<p class="welcome-block__text"><?php echo esc_html( $kiyose_welcome_text ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $kiyose_welcome_keywords ) ) : ?>
				<ul class="welcome-block__keywords" aria-label="<?php esc_attr_e( 'Thèmes', 'kiyose' ); ?>">
					<?php foreach ( $kiyose_welcome_keywords as $kiyose_keyword ) : ?>
						<?php
						if ( empty( $kiyose_keyword['label'] ) ) {
							continue;
						}
						?>
						<li>
							<?php if ( ! empty( $kiyose_keyword['url'] ) ) : ?>
								<a href="<?php echo esc_url( $kiyose_keyword['url'] ); ?>" class="welcome-block__keyword">
									<?php echo esc_html( $kiyose_keyword['label'] ); ?>
								</a>
							<?php else : ?>
								<span class="welcome-block__keyword">
									<?php echo esc_html( $kiyose_keyword['label'] ); ?>
								</span>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<?php if ( ! empty( $kiyose_welcome_slogan ) ) : ?>
				<p class="welcome-block__slogan"><?php echo esc_html( $kiyose_welcome_slogan ); ?></p>
			<?php endif; ?>
			<div class="welcome-block__cta-wrapper" style="margin-top: var(--kiyose-spacing-xl);">
				<a href="<?php echo esc_url( KIYOSE_DISCOVERY_CALL_URL ); ?>" class="welcome-block__cta">
					<?php esc_html_e( 'Réserver votre appel découverte', 'kiyose' ); ?>
				</a>
			</div>
		</div>
	</section>

	<!-- Section À propos — Mobile uniquement (masquée sur desktop via CSS) -->
	<div class="home-about-mobile">
		<section class="home-about" aria-labelledby="about-mobile-heading">
			<?php if ( $kiyose_has_about_image ) : ?>
				<div class="home-about__image">
					<?php echo wp_get_attachment_image( $kiyose_hero_image_id, 'medium', false, array( 'alt' => '' ) ); ?>
				</div>
			<?php endif; ?>
			<h2 id="about-mobile-heading" class="home-about__title">
				<?php esc_html_e( 'À propos', 'kiyose' ); ?>
			</h2>
			<p class="home-about__text"><?php echo esc_html( $kiyose_hero_content ); ?></p>
			<a href="<?php echo esc_url( $kiyose_hero_cta_url ); ?>" class="home-about__link">
				<?php echo esc_html( $kiyose_hero_cta_text ); ?>
			</a>
		</section>
	</div>

	<?php
	// Wave separator — Bienvenue (beige) → Contenu 1 (blanc).
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

	<!-- Section Contenu 1 — Questions & Réponses -->
	<?php if ( ! empty( $kiyose_content1_qa ) ) : ?>
	<section class="home-content1" aria-labelledby="content1-heading">
		<h2 id="content1-heading" class="sr-only">
			<?php esc_html_e( 'Questions fréquentes', 'kiyose' ); ?>
		</h2>
		<div class="home-content1__inner">
			<ul class="home-content1__qa-list" role="list">
				<?php foreach ( $kiyose_content1_qa as $kiyose_qa_item ) : ?>
					<?php
					if ( empty( $kiyose_qa_item['question'] ) && empty( $kiyose_qa_item['answer'] ) ) {
						continue;
					}
					?>
					<li class="home-content1__qa-item">
						<?php if ( ! empty( $kiyose_qa_item['question'] ) ) : ?>
							<p class="home-content1__question"><?php echo esc_html( $kiyose_qa_item['question'] ); ?></p>
						<?php endif; ?>
						<?php if ( ! empty( $kiyose_qa_item['answer'] ) ) : ?>
							<p class="home-content1__answer"><?php echo esc_html( $kiyose_qa_item['answer'] ); ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php if ( ! empty( $kiyose_content1_slogan ) ) : ?>
				<p class="home-content1__slogan"><?php echo esc_html( $kiyose_content1_slogan ); ?></p>
			<?php endif; ?>
		</div>
	</section>
	<?php endif; ?>

	<?php
	// Wave separator — Contenu 1 (blanc) → Contenu 2 (rose).
	get_template_part(
		'template-parts/decorative',
		'shapes',
		array(
			'mode'    => 'separator',
			'color'   => '--kiyose-color-background',
			'from_bg' => '#ffffff',
		)
	);
	?>

	<!-- Section Contenu 2 — Texte libre -->
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
				<p class="home-content2__slogan"><?php echo esc_html( $kiyose_content2_slogan ); ?></p>
			<?php endif; ?>
			<div class="home-content2__cta-wrapper">
				<a href="<?php echo esc_url( KIYOSE_DISCOVERY_CALL_URL ); ?>" class="home-content2__cta">
					<?php esc_html_e( 'Réserver votre appel découverte', 'kiyose' ); ?>
				</a>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php
	// Wave separator — Contenu 2 (rose) → Events (blanc).
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

	<!-- Section Prochaines Dates -->
	<section class="home-events" aria-labelledby="events-title">
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
	// Wave separator — Events (blanc) → Témoignages (rose).
	get_template_part(
		'template-parts/decorative',
		'shapes',
		array(
			'mode'    => 'separator',
			'color'   => '--kiyose-color-background',
			'from_bg' => '#ffffff',
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
	// Wave separator — Témoignages (rose) → Blog (blanc).
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
		get_template_part(
			'template-parts/decorative',
			'shapes',
			array(
				'type'     => 'blob',
				'color'    => '--kiyose-color-accent',
				'opacity'  => 0.12,
				'size'     => 'md',
				'position' => 'content-right',
				'top'      => '4%',
				'rotation' => 8,
			)
		);
		get_template_part(
			'template-parts/decorative',
			'shapes',
			array(
				'type'     => 'blob',
				'color'    => '--kiyose-color-accent',
				'opacity'  => 0.10,
				'size'     => 'sm',
				'position' => 'content-left',
				'top'      => '12%',
				'rotation' => -6,
			)
		);
		get_template_part(
			'template-parts/decorative',
			'shapes',
			array(
				'type'     => 'brushstroke',
				'color'    => '--kiyose-color-primary',
				'opacity'  => 0.10,
				'size'     => 'lg',
				'position' => 'content-left',
				'top'      => '20%',
				'rotation' => -5,
			)
		);
		get_template_part(
			'template-parts/decorative',
			'shapes',
			array(
				'type'     => 'blob',
				'color'    => '--kiyose-color-accent',
				'opacity'  => 0.10,
				'size'     => 'sm',
				'position' => 'content-right',
				'top'      => '28%',
				'rotation' => 4,
			)
		);
		get_template_part(
			'template-parts/decorative',
			'shapes',
			array(
				'type'     => 'brushstroke',
				'color'    => '--kiyose-color-accent',
				'opacity'  => 0.12,
				'size'     => 'md',
				'position' => 'content-right',
				'top'      => '36%',
				'rotation' => 10,
			)
		);
		get_template_part(
			'template-parts/decorative',
			'shapes',
			array(
				'type'     => 'blob',
				'color'    => '--kiyose-color-primary',
				'opacity'  => 0.08,
				'size'     => 'sm',
				'position' => 'content-right',
				'top'      => '44%',
				'rotation' => 7,
			)
		);
		get_template_part(
			'template-parts/decorative',
			'shapes',
			array(
				'type'     => 'blob',
				'color'    => '--kiyose-color-primary',
				'opacity'  => 0.15,
				'size'     => 'md',
				'position' => 'content-left',
				'top'      => '52%',
				'rotation' => -12,
			)
		);
		get_template_part(
			'template-parts/decorative',
			'shapes',
			array(
				'type'     => 'brushstroke',
				'color'    => '--kiyose-color-accent',
				'opacity'  => 0.09,
				'size'     => 'sm',
				'position' => 'content-left',
				'top'      => '60%',
				'rotation' => -8,
			)
		);
		get_template_part(
			'template-parts/decorative',
			'shapes',
			array(
				'type'     => 'brushstroke',
				'color'    => '--kiyose-color-accent',
				'opacity'  => 0.10,
				'size'     => 'lg',
				'position' => 'content-right',
				'top'      => '68%',
				'rotation' => 6,
			)
		);
		get_template_part(
			'template-parts/decorative',
			'shapes',
			array(
				'type'     => 'brushstroke',
				'color'    => '--kiyose-color-accent',
				'opacity'  => 0.09,
				'size'     => 'sm',
				'position' => 'content-left',
				'top'      => '76%',
				'rotation' => -5,
			)
		);
		get_template_part(
			'template-parts/decorative',
			'shapes',
			array(
				'type'     => 'blob',
				'color'    => '--kiyose-color-primary',
				'opacity'  => 0.12,
				'size'     => 'md',
				'position' => 'content-left',
				'top'      => '84%',
				'rotation' => -8,
			)
		);
		get_template_part(
			'template-parts/decorative',
			'shapes',
			array(
				'type'     => 'brushstroke',
				'color'    => '--kiyose-color-primary',
				'opacity'  => 0.10,
				'size'     => 'sm',
				'position' => 'content-right',
				'top'      => '92%',
				'rotation' => 5,
			)
		);
		?>
	</div>
</main>

<?php
// Overlay À propos — desktop uniquement (position: fixed, déclenché par JS au scroll).
$kiyose_has_about_image = ! empty( $kiyose_hero_image_id );
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
				<?php echo wp_get_attachment_image( $kiyose_hero_image_id, 'thumbnail', false, array( 'alt' => '' ) ); ?>
			</div>
		<?php endif; ?>
		<div class="about-overlay__content">
			<p class="about-overlay__text"><?php echo esc_html( $kiyose_hero_content ); ?></p>
			<a href="<?php echo esc_url( $kiyose_hero_cta_url ); ?>" class="about-overlay__link">
				<?php echo esc_html( $kiyose_hero_cta_text ); ?>
			</a>
		</div>
	</div>
</div>

<?php // Overlay Newsletter — desktop uniquement (position: fixed, côté gauche, déclenché par JS au scroll). ?>
<div class="newsletter-overlay" id="newsletter-overlay" role="complementary" aria-label="<?php esc_attr_e( 'Inscription à la newsletter', 'kiyose' ); ?>" hidden>
	<button
		class="newsletter-overlay__close"
		id="newsletter-overlay-close"
		type="button"
		aria-label="<?php esc_attr_e( 'Fermer le panneau Newsletter', 'kiyose' ); ?>"
	>
		<span aria-hidden="true">&times;</span>
	</button>
	<div class="newsletter-overlay__body">
		<h2 class="newsletter-overlay__title"><?php esc_html_e( 'Restez informés', 'kiyose' ); ?></h2>
		<p class="newsletter-overlay__description">
			<?php esc_html_e( 'Recevez les prochaines dates et actualités directement dans votre boîte mail.', 'kiyose' ); ?>
		</p>
		<div class="newsletter-overlay__form">
			<?php
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
	</div>
</div>

<!-- Annonce screen reader partagée pour les overlays (aria-live="polite") -->
<div class="sr-only" id="overlay-announcement" aria-live="polite" aria-atomic="true"></div>

<?php
get_footer();
