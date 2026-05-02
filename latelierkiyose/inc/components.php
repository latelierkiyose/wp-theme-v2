<?php
/**
 * Reusable theme components.
 *
 * @package Kiyose
 * @since   2.1.0
 */

/**
 * Returns testimonials carousel markup for an existing query.
 *
 * @param WP_Query $query      Testimonials query.
 * @param string   $aria_label Carousel accessible label.
 * @return string Carousel HTML.
 */
function kiyose_get_testimonials_carousel_html( WP_Query $query, string $aria_label ): string {
	if ( ! $query->have_posts() ) {
		return '';
	}

	$total_slides = (int) $query->post_count;

	ob_start();
	?>
	<div
		class="carousel"
		role="region"
		aria-roledescription="carousel"
		aria-label="<?php echo esc_attr( $aria_label ); ?>"
	>
		<div class="carousel__controls">
			<button type="button" class="carousel__prev" aria-label="<?php esc_attr_e( 'Témoignage précédent', 'kiyose' ); ?>">
				<?php echo kiyose_carousel_icon_svg( 'prev' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</button>
			<button type="button" class="carousel__pause" aria-label="<?php esc_attr_e( 'Mettre en pause le défilement', 'kiyose' ); ?>">
				<span class="carousel__icon carousel__icon--pause"><?php echo kiyose_carousel_icon_svg( 'pause' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<span class="carousel__icon carousel__icon--play" hidden><?php echo kiyose_carousel_icon_svg( 'play' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			</button>
			<button type="button" class="carousel__next" aria-label="<?php esc_attr_e( 'Témoignage suivant', 'kiyose' ); ?>">
				<?php echo kiyose_carousel_icon_svg( 'next' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</button>
		</div>
		<div class="carousel__track" aria-live="polite">
			<?php
			$slide_index = 0;
			while ( $query->have_posts() ) :
				$query->the_post();
				++$slide_index;

				$is_active = 1 === $slide_index;
				?>
				<div
					class="carousel__slide<?php echo $is_active ? ' carousel__slide--active' : ''; ?>"
					role="group"
					aria-roledescription="slide"
					aria-label="<?php echo esc_attr( sprintf( /* translators: 1: current slide number, 2: total number of slides */ __( '%1$d sur %2$d', 'kiyose' ), $slide_index, $total_slides ) ); ?>"
					aria-hidden="<?php echo $is_active ? 'false' : 'true'; ?>"
				>
					<?php get_template_part( 'template-parts/content', 'testimony' ); ?>
				</div>
				<?php
			endwhile;
			?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Returns configured social profiles.
 *
 * @return array<int, array{key:string,label:string,url:string,icon:string}> Social profiles.
 */
function kiyose_get_social_profiles(): array {
	$profiles = array(
		array(
			'key'   => 'facebook',
			'label' => __( 'Facebook', 'kiyose' ),
			'url'   => get_theme_mod( 'kiyose_social_facebook', 'https://www.facebook.com/latelierkiyose' ),
			'icon'  => kiyose_social_icon_svg( 'facebook' ),
		),
		array(
			'key'   => 'instagram',
			'label' => __( 'Instagram', 'kiyose' ),
			'url'   => get_theme_mod( 'kiyose_social_instagram', 'https://www.instagram.com/latelierkiyose' ),
			'icon'  => kiyose_social_icon_svg( 'instagram' ),
		),
		array(
			'key'   => 'linkedin',
			'label' => __( 'LinkedIn', 'kiyose' ),
			'url'   => get_theme_mod( 'kiyose_social_linkedin', 'https://www.linkedin.com/company/latelierkiyose' ),
			'icon'  => kiyose_social_icon_svg( 'linkedin' ),
		),
	);

	$configured_profiles = array();

	foreach ( $profiles as $profile ) {
		$url = esc_url_raw( trim( (string) $profile['url'] ) );

		if ( '' === $url ) {
			continue;
		}

		$profile['url']        = $url;
		$configured_profiles[] = $profile;
	}

	return $configured_profiles;
}

/**
 * Returns social profile links markup for a supported context.
 *
 * @param string $context Rendering context: footer or contact.
 * @return string Social links HTML.
 */
function kiyose_get_social_links_html( string $context ): string {
	$profiles = kiyose_get_social_profiles();

	if ( empty( $profiles ) ) {
		return '';
	}

	$context = sanitize_key( $context );

	if ( 'footer' === $context ) {
		return kiyose_get_footer_social_links_html( $profiles );
	}

	if ( 'contact' === $context ) {
		return kiyose_get_contact_social_links_html( $profiles );
	}

	return '';
}

/**
 * Returns footer social profile links markup.
 *
 * @param array<int, array{key:string,label:string,url:string,icon:string}> $profiles Social profiles.
 * @return string Footer social links HTML.
 */
function kiyose_get_footer_social_links_html( array $profiles ): string {
	ob_start();
	?>
	<div class="site-footer__social">
		<?php foreach ( $profiles as $profile ) : ?>
			<a href="<?php echo esc_url( $profile['url'] ); ?>" aria-label="<?php echo esc_attr( $profile['label'] ); ?>" target="_blank" rel="noopener noreferrer">
				<?php echo $profile['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>
		<?php endforeach; ?>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Returns contact page social profile links markup.
 *
 * @param array<int, array{key:string,label:string,url:string,icon:string}> $profiles Social profiles.
 * @return string Contact social links HTML.
 */
function kiyose_get_contact_social_links_html( array $profiles ): string {
	ob_start();
	?>
	<ul class="social-links">
		<?php foreach ( $profiles as $profile ) : ?>
			<li>
				<a href="<?php echo esc_url( $profile['url'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: social network name */ __( '%s (ouverture dans un nouvel onglet)', 'kiyose' ), $profile['label'] ) ); ?>">
					<?php echo $profile['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span><?php echo esc_html( $profile['label'] ); ?></span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php
	return ob_get_clean();
}

/**
 * Returns blog post share links markup.
 *
 * @param int $post_id Post ID.
 * @return string Share links HTML.
 */
function kiyose_get_post_share_links_html( int $post_id ): string {
	$post_id   = absint( $post_id );
	$permalink = get_permalink( $post_id );
	$title     = get_the_title( $post_id );

	if ( 0 === $post_id || empty( $permalink ) ) {
		return '';
	}

	ob_start();
	?>
	<ul class="blog-single__share-links">
		<li>
			<a
				href="<?php echo esc_url( 'https://www.facebook.com/sharer/sharer.php?u=' . $permalink ); ?>"
				target="_blank"
				rel="noopener noreferrer"
				aria-label="<?php echo esc_attr( sprintf( /* translators: %s: title of the blog post */ __( 'Partager %s sur Facebook', 'kiyose' ), $title ) ); ?>"
				class="blog-single__share-link blog-single__share-link--facebook"
			>
				<?php echo kiyose_social_icon_svg( 'facebook' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span class="screen-reader-text"><?php esc_html_e( 'Facebook', 'kiyose' ); ?></span>
			</a>
		</li>
		<li>
			<a
				href="<?php echo esc_url( 'https://www.linkedin.com/sharing/share-offsite/?url=' . $permalink ); ?>"
				target="_blank"
				rel="noopener noreferrer"
				aria-label="<?php echo esc_attr( sprintf( /* translators: %s: title of the blog post */ __( 'Partager %s sur LinkedIn', 'kiyose' ), $title ) ); ?>"
				class="blog-single__share-link blog-single__share-link--linkedin"
			>
				<?php echo kiyose_social_icon_svg( 'linkedin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span class="screen-reader-text"><?php esc_html_e( 'LinkedIn', 'kiyose' ); ?></span>
			</a>
		</li>
		<li>
			<button
				type="button"
				class="blog-single__share-link blog-single__share-link--instagram js-copy-link"
				data-url="<?php echo esc_url( $permalink ); ?>"
				aria-label="<?php echo esc_attr( sprintf( /* translators: %s: title of the blog post */ __( 'Copier le lien de %s pour Instagram', 'kiyose' ), $title ) ); ?>"
			>
				<?php echo kiyose_social_icon_svg( 'instagram' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span class="screen-reader-text"><?php esc_html_e( 'Instagram', 'kiyose' ); ?></span>
			</button>
		</li>
	</ul>
	<?php
	return ob_get_clean();
}
