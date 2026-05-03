<?php
/**
 * Meta boxes for page templates.
 *
 * @package Kiyose
 * @since   0.2.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Sanitize a single welcome block keyword item.
 *
 * Pure function — usable and testable outside WordPress context.
 *
 * @param string $label Raw label text.
 * @param string $url   Raw URL string.
 * @return array|null Sanitized array with 'label' and 'url' keys, or null if invalid.
 */
function kiyose_sanitize_welcome_keyword_item( string $label, string $url ): ?array {
	$clean_label = sanitize_text_field( $label );
	$clean_url   = esc_url_raw( $url );

	if ( '' === $clean_label ) {
		return null;
	}

	return array(
		'label' => $clean_label,
		'url'   => $clean_url,
	);
}

/**
 * Sanitize a list of welcome block keyword items from JSON input.
 *
 * @param string $raw_json Raw JSON string from form input.
 * @return array<int, array{label:string,url:string}> Sanitized keyword items. Empty array if invalid.
 */
function kiyose_sanitize_welcome_keyword_items( string $raw_json ): array {
	$decoded = json_decode( $raw_json, true );

	if ( ! is_array( $decoded ) ) {
		return array();
	}

	$sanitized = array();

	foreach ( $decoded as $item ) {
		if ( ! isset( $item['label'], $item['url'] ) ) {
			continue;
		}

		$clean = kiyose_sanitize_welcome_keyword_item( (string) $item['label'], (string) $item['url'] );

		if ( null !== $clean ) {
			$sanitized[] = $clean;
		}
	}

	return $sanitized;
}

/**
 * Sanitize a list of Q&A items from JSON input.
 *
 * Pure function — usable and testable outside WordPress context.
 *
 * @param string $raw_json Raw JSON string from form input.
 * @return array Sanitized array of Q&A items with 'question' and 'answer' keys. Empty array if invalid.
 */
function kiyose_sanitize_qa_items( string $raw_json ): array {
	$decoded = json_decode( $raw_json, true );

	if ( ! is_array( $decoded ) ) {
		return array();
	}

	$sanitized = array();

	foreach ( $decoded as $item ) {
		if ( ! isset( $item['question'], $item['answer'] ) ) {
			continue;
		}

		$question = sanitize_text_field( $item['question'] );
		$answer   = sanitize_text_field( $item['answer'] );

		if ( '' === $question && '' === $answer ) {
			continue;
		}

		$sanitized[] = array(
			'question' => $question,
			'answer'   => $answer,
		);
	}

	return $sanitized;
}

/**
 * Check whether a page uses a specific template.
 *
 * @param object|null $post     Current post object.
 * @param string      $template Template path to match.
 * @return bool True when the post uses the given template.
 */
function kiyose_page_uses_template( $post, string $template ): bool {
	if ( ! is_object( $post ) || ! isset( $post->ID ) ) {
		return false;
	}

	return get_post_meta( (int) $post->ID, '_wp_page_template', true ) === $template;
}

/**
 * Decode a JSON meta value into an array for admin rendering.
 *
 * @param mixed $raw_value Raw meta value.
 * @return array<int, array<string, mixed>>
 */
function kiyose_decode_meta_box_json_items( $raw_value ): array {
	$decoded = json_decode( (string) $raw_value, true );

	return is_array( $decoded ) ? $decoded : array();
}

/**
 * Encode meta box repeater items for hidden input values.
 *
 * @param array<int, array<string, mixed>> $items Items to encode.
 * @return string JSON string.
 */
function kiyose_encode_meta_box_json_items( array $items ): string {
	$encoded = wp_json_encode( $items );

	return is_string( $encoded ) ? $encoded : '[]';
}

/**
 * Return an unslashed POST field only when it is a string.
 *
 * @param string $field_name POST field name.
 * @return string Empty string when the field is missing or not string-like.
 */
function kiyose_get_unslashed_string_post_field( string $field_name ): string {
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Called only from save handlers after their nonce verification.
	if ( ! isset( $_POST[ $field_name ] ) || ! is_string( $_POST[ $field_name ] ) ) {
		return '';
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Caller applies field-specific sanitization after unslashing.
	return wp_unslash( $_POST[ $field_name ] );
}

/**
 * Return an admin asset path with the current environment suffix.
 *
 * @param string $path Asset path from the theme directory.
 * @return string
 */
function kiyose_get_admin_meta_box_asset_path( string $path ): string {
	$suffix = function_exists( 'kiyose_get_asset_suffix' ) ? kiyose_get_asset_suffix() : '';

	if ( '' === $suffix ) {
		return $path;
	}

	$path_with_suffix = preg_replace( '/\.(css|js)$/', $suffix . '.$1', $path );

	return is_string( $path_with_suffix ) ? $path_with_suffix : $path;
}

/**
 * Return an admin asset version.
 *
 * @param string $path Asset path from the theme directory.
 * @return string|int
 */
function kiyose_get_admin_meta_box_asset_version( string $path ) {
	if ( function_exists( 'kiyose_get_asset_version' ) ) {
		return kiyose_get_asset_version( $path );
	}

	$full_path = get_template_directory() . $path;

	return file_exists( $full_path ) ? filemtime( $full_path ) : KIYOSE_VERSION;
}

/**
 * Enqueue admin assets needed by page template meta boxes.
 *
 * @param string $hook_suffix Current admin hook suffix.
 * @return void
 */
function kiyose_enqueue_admin_meta_box_assets( string $hook_suffix ): void {
	if ( ! in_array( $hook_suffix, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}

	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

	if ( ! is_object( $screen ) || ! isset( $screen->post_type ) || 'page' !== $screen->post_type ) {
		return;
	}

	$style_path  = kiyose_get_admin_meta_box_asset_path( '/assets/css/admin/meta-boxes.css' );
	$script_path = kiyose_get_admin_meta_box_asset_path( '/assets/js/admin/meta-boxes.js' );

	wp_enqueue_style(
		'kiyose-admin-meta-boxes',
		get_template_directory_uri() . $style_path,
		array(),
		kiyose_get_admin_meta_box_asset_version( $style_path )
	);

	wp_enqueue_script(
		'kiyose-admin-meta-boxes',
		get_template_directory_uri() . $script_path,
		array( 'jquery', 'media-editor' ),
		kiyose_get_admin_meta_box_asset_version( $script_path ),
		true
	);

	wp_enqueue_media();

	wp_localize_script(
		'kiyose-admin-meta-boxes',
		'kiyoseMetaBoxes',
		array(
			'keywordLabelPlaceholder' => __( 'Label', 'kiyose' ),
			'keywordUrlPlaceholder'   => __( 'URL (ex: /services/ ou #ancre)', 'kiyose' ),
			'removeLabel'             => __( 'Supprimer', 'kiyose' ),
			'qaQuestionLabel'         => __( 'Question', 'kiyose' ),
			'qaAnswerLabel'           => __( 'Réponse', 'kiyose' ),
			'heroMediaTitle'          => __( 'Choisir une image de fond', 'kiyose' ),
			'contactMediaTitle'       => __( 'Choisir une photo de contact', 'kiyose' ),
			'mediaButtonText'         => __( 'Utiliser cette image', 'kiyose' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'kiyose_enqueue_admin_meta_box_assets', 10, 1 );

/**
 * Render a notice for template-specific meta boxes.
 *
 * @param bool   $is_visible     Whether the meta fields are visible.
 * @param string $template_label Human-readable template label.
 * @return void
 */
function kiyose_render_template_meta_notice( bool $is_visible, string $template_label ): void {
	if ( $is_visible ) {
		return;
	}

	?>
	<div class="kiyose-meta-box__notice">
		<p>
			<?php
			printf(
				/* translators: %s: required page template label. */
				esc_html__( 'Ces champs sont disponibles uniquement avec le template « %s ».', 'kiyose' ),
				esc_html( $template_label )
			);
			?>
		</p>
	</div>
	<?php
}

/**
 * Render a meta box section title.
 *
 * @param string $title       Section title.
 * @param string $description Optional section description.
 * @return void
 */
function kiyose_render_meta_box_section_title( string $title, string $description = '' ): void {
	?>
	<h3 class="kiyose-meta-box__section-title">
		<?php echo esc_html( $title ); ?>
	</h3>
	<?php if ( '' !== $description ) : ?>
		<p class="kiyose-meta-box__description kiyose-meta-box__section-description">
			<?php echo esc_html( $description ); ?>
		</p>
	<?php endif; ?>
	<?php
}

/**
 * Render a text-like input field.
 *
 * @param array<string, mixed> $args Field arguments.
 * @return void
 */
function kiyose_render_text_field( array $args ): void {
	$id          = (string) ( $args['id'] ?? '' );
	$name        = (string) ( $args['name'] ?? $id );
	$label       = (string) ( $args['label'] ?? '' );
	$value       = (string) ( $args['value'] ?? '' );
	$description = (string) ( $args['description'] ?? '' );
	$placeholder = (string) ( $args['placeholder'] ?? '' );
	$type        = sanitize_key( (string) ( $args['type'] ?? 'text' ) );
	$type        = in_array( $type, array( 'text', 'url' ), true ) ? $type : 'text';
	?>
	<div class="kiyose-meta-box__field-group">
		<?php if ( '' !== $label ) : ?>
			<label class="kiyose-meta-box__label" for="<?php echo esc_attr( $id ); ?>">
				<?php echo esc_html( $label ); ?>
			</label>
		<?php endif; ?>
		<input
			class="kiyose-meta-box__input"
			type="<?php echo esc_attr( $type ); ?>"
			name="<?php echo esc_attr( $name ); ?>"
			id="<?php echo esc_attr( $id ); ?>"
			value="<?php echo 'url' === $type ? esc_url( $value ) : esc_attr( $value ); ?>"
			<?php if ( '' !== $placeholder ) : ?>
				placeholder="<?php echo esc_attr( $placeholder ); ?>"
			<?php endif; ?>
		>
		<?php if ( '' !== $description ) : ?>
			<p class="kiyose-meta-box__description">
				<?php echo esc_html( $description ); ?>
			</p>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Render a textarea field.
 *
 * @param array<string, mixed> $args Field arguments.
 * @return void
 */
function kiyose_render_textarea_field( array $args ): void {
	$id          = (string) ( $args['id'] ?? '' );
	$name        = (string) ( $args['name'] ?? $id );
	$label       = (string) ( $args['label'] ?? '' );
	$value       = (string) ( $args['value'] ?? '' );
	$description = (string) ( $args['description'] ?? '' );
	$rows        = absint( $args['rows'] ?? 4 );
	?>
	<div class="kiyose-meta-box__field-group">
		<?php if ( '' !== $label ) : ?>
			<label class="kiyose-meta-box__label" for="<?php echo esc_attr( $id ); ?>">
				<?php echo esc_html( $label ); ?>
			</label>
		<?php endif; ?>
		<textarea
			class="kiyose-meta-box__textarea"
			name="<?php echo esc_attr( $name ); ?>"
			id="<?php echo esc_attr( $id ); ?>"
			rows="<?php echo esc_attr( max( 1, $rows ) ); ?>"
		><?php echo esc_textarea( $value ); ?></textarea>
		<?php if ( '' !== $description ) : ?>
			<p class="kiyose-meta-box__description">
				<?php echo esc_html( $description ); ?>
			</p>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Render a media selection field.
 *
 * @param array<string, mixed> $args Field arguments.
 * @return void
 */
function kiyose_render_media_field( array $args ): void {
	$id           = (string) ( $args['id'] ?? '' );
	$name         = (string) ( $args['name'] ?? $id );
	$label        = (string) ( $args['label'] ?? '' );
	$value        = absint( $args['value'] ?? 0 );
	$button_id    = (string) ( $args['button_id'] ?? $id . '_button' );
	$remove_id    = (string) ( $args['remove_id'] ?? $id . '_remove' );
	$preview_id   = (string) ( $args['preview_id'] ?? $id . '_preview' );
	$button_label = (string) ( $args['button_label'] ?? __( 'Choisir une image', 'kiyose' ) );
	$remove_label = (string) ( $args['remove_label'] ?? __( 'Supprimer l\'image', 'kiyose' ) );
	$description  = (string) ( $args['description'] ?? '' );
	$image_size   = (string) ( $args['image_size'] ?? 'medium' );
	$preview_mod  = (string) ( $args['preview_modifier'] ?? '' );
	?>
	<div class="kiyose-meta-box__field-group">
		<?php if ( '' !== $label ) : ?>
			<label class="kiyose-meta-box__label" for="<?php echo esc_attr( $id ); ?>">
				<?php echo esc_html( $label ); ?>
			</label>
		<?php endif; ?>
		<input
			type="hidden"
			name="<?php echo esc_attr( $name ); ?>"
			id="<?php echo esc_attr( $id ); ?>"
			value="<?php echo esc_attr( $value ); ?>"
		>
		<div class="kiyose-meta-box__button-group">
			<button type="button" class="button" id="<?php echo esc_attr( $button_id ); ?>">
				<?php echo esc_html( $button_label ); ?>
			</button>
			<button
				type="button"
				class="button"
				id="<?php echo esc_attr( $remove_id ); ?>"
				<?php if ( 0 === $value ) : ?>
					hidden
				<?php endif; ?>
			>
				<?php echo esc_html( $remove_label ); ?>
			</button>
		</div>
		<div
			class="kiyose-meta-box__image-preview<?php echo '' !== $preview_mod ? ' ' . esc_attr( $preview_mod ) : ''; ?>"
			id="<?php echo esc_attr( $preview_id ); ?>"
		>
			<?php
			if ( $value > 0 ) {
				echo wp_get_attachment_image( $value, $image_size );
			}
			?>
		</div>
		<?php if ( '' !== $description ) : ?>
			<p class="kiyose-meta-box__description">
				<?php echo esc_html( $description ); ?>
			</p>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Render one welcome keyword repeater row.
 *
 * @param array<string, mixed> $keyword Keyword item.
 * @return void
 */
function kiyose_render_welcome_keyword_row( array $keyword ): void {
	$label = (string) ( $keyword['label'] ?? '' );
	$url   = (string) ( $keyword['url'] ?? '' );
	?>
	<div class="kiyose-repeater-row kiyose-repeater-row--keyword keyword-row">
		<input
			type="text"
			placeholder="<?php esc_attr_e( 'Label', 'kiyose' ); ?>"
			class="kiyose-repeater-row__input kiyose-repeater-row__input--keyword-label kw-label"
			value="<?php echo esc_attr( $label ); ?>"
		>
		<input
			type="text"
			placeholder="<?php esc_attr_e( 'URL (ex: /services/ ou #ancre)', 'kiyose' ); ?>"
			class="kiyose-repeater-row__input kiyose-repeater-row__input--keyword-url kw-url"
			value="<?php echo esc_attr( $url ); ?>"
		>
		<button type="button" class="button kw-remove">
			<?php esc_html_e( 'Supprimer', 'kiyose' ); ?>
		</button>
	</div>
	<?php
}

/**
 * Render one Q&A repeater row.
 *
 * @param array<string, mixed> $item Q&A item.
 * @return void
 */
function kiyose_render_qa_repeater_row( array $item ): void {
	$question = (string) ( $item['question'] ?? '' );
	$answer   = (string) ( $item['answer'] ?? '' );
	?>
	<div class="kiyose-repeater-row kiyose-repeater-row--qa qa-row">
		<div class="kiyose-repeater-row__field">
			<label class="kiyose-repeater-row__label">
				<?php esc_html_e( 'Question', 'kiyose' ); ?>
			</label>
			<input
				type="text"
				class="kiyose-repeater-row__input qa-question"
				value="<?php echo esc_attr( $question ); ?>"
			>
		</div>
		<div class="kiyose-repeater-row__field">
			<label class="kiyose-repeater-row__label">
				<?php esc_html_e( 'Réponse', 'kiyose' ); ?>
			</label>
			<textarea class="kiyose-repeater-row__textarea qa-answer" rows="3"><?php echo esc_textarea( $answer ); ?></textarea>
		</div>
		<button type="button" class="button qa-remove">
			<?php esc_html_e( 'Supprimer', 'kiyose' ); ?>
		</button>
	</div>
	<?php
}

/**
 * Add meta box for hero section on home page template.
 *
 * @param object|null $post Current post object.
 * @return void
 */
function kiyose_add_home_hero_meta_box( $post = null ) {
	if ( ! kiyose_page_uses_template( $post, 'templates/page-home.php' ) ) {
		return;
	}

	add_meta_box(
		'kiyose_home_hero',
		__( 'Page d\'accueil — Bienvenue &amp; Overlay À propos', 'kiyose' ),
		'kiyose_render_home_hero_meta_box',
		'page',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_page', 'kiyose_add_home_hero_meta_box', 10, 1 );

/**
 * Render the home hero meta box.
 *
 * @param WP_Post $post Current post object.
 * @return void
 */
function kiyose_render_home_hero_meta_box( $post ) {
	// Add nonce for security.
	wp_nonce_field( 'kiyose_home_hero_nonce_action', 'kiyose_home_hero_nonce' );

	// Get current values — Bloc Bienvenue.
	$welcome_title        = get_post_meta( $post->ID, 'kiyose_welcome_title', true );
	$welcome_subtitle     = get_post_meta( $post->ID, 'kiyose_welcome_subtitle', true );
	$welcome_text         = get_post_meta( $post->ID, 'kiyose_welcome_text', true );
	$welcome_keywords_raw = get_post_meta( $post->ID, 'kiyose_welcome_keywords', true );
	$welcome_keywords     = $welcome_keywords_raw ? json_decode( $welcome_keywords_raw, true ) : array();
	if ( ! is_array( $welcome_keywords ) ) {
		$welcome_keywords = array();
	}
	$welcome_slogan = get_post_meta( $post->ID, 'kiyose_welcome_slogan', true );

	// Get current values — Bloc Contenu 1 (Q&A).
	$content1_qa_raw = get_post_meta( $post->ID, 'kiyose_content1_qa', true );
	$content1_slogan = get_post_meta( $post->ID, 'kiyose_content1_slogan', true );

	// Get current values — Bloc Contenu 2.
	$content2_text         = get_post_meta( $post->ID, 'kiyose_content2_text', true );
	$content2_quote        = get_post_meta( $post->ID, 'kiyose_content2_slogan', true );
	$content2_quote_author = get_post_meta( $post->ID, 'kiyose_content2_quote_author', true );

	// Get current values — Overlay À propos (existing hero fields).
	$hero_content  = get_post_meta( $post->ID, 'kiyose_hero_content', true );
	$hero_cta_text = get_post_meta( $post->ID, 'kiyose_hero_cta_text', true );
	$hero_cta_url  = get_post_meta( $post->ID, 'kiyose_hero_cta_url', true );
	$hero_image_id = get_post_meta( $post->ID, 'kiyose_hero_image_id', true );

	// Default values for overlay fields.
	if ( empty( $hero_content ) ) {
		$hero_content = 'Au sein de L\'Atelier Kiyose, découvrez des pratiques de bien-être et de développement personnel qui vous accompagnent dans votre transformation : art-thérapie, rigologie, bols tibétains et ateliers philo.';
	}
	if ( empty( $hero_cta_text ) ) {
		$hero_cta_text = 'En savoir plus sur l\'atelier';
	}
	if ( empty( $hero_cta_url ) ) {
		$hero_cta_url = home_url( '/a-propos/' );
	}

	$content1_qa_items = kiyose_decode_meta_box_json_items( $content1_qa_raw );

	?>
	<div class="kiyose-meta-box" data-template-required="templates/page-home.php">
		<div class="kiyose-meta-box__fields">
			<?php
			kiyose_render_meta_box_section_title( __( 'Bloc Bienvenue', 'kiyose' ) );

			kiyose_render_text_field(
				array(
					'id'          => 'kiyose_welcome_title',
					'name'        => 'kiyose_welcome_title',
					'label'       => __( 'Titre (H1) du bloc bienvenue', 'kiyose' ),
					'value'       => $welcome_title,
					'description' => __( 'Titre principal affiché en haut de la page, en typographie Dancing Script.', 'kiyose' ),
				)
			);

			kiyose_render_text_field(
				array(
					'id'    => 'kiyose_welcome_subtitle',
					'name'  => 'kiyose_welcome_subtitle',
					'label' => __( 'Sous-titre', 'kiyose' ),
					'value' => $welcome_subtitle,
				)
			);

			kiyose_render_textarea_field(
				array(
					'id'    => 'kiyose_welcome_text',
					'name'  => 'kiyose_welcome_text',
					'label' => __( 'Texte descriptif', 'kiyose' ),
					'value' => $welcome_text,
					'rows'  => 4,
				)
			);
			?>

			<div class="kiyose-meta-box__field-group">
				<label class="kiyose-meta-box__label">
					<?php esc_html_e( 'Mots-clefs (chips cliquables)', 'kiyose' ); ?>
				</label>
				<input
					type="hidden"
					name="kiyose_welcome_keywords"
					id="kiyose_welcome_keywords"
					value="<?php echo esc_attr( kiyose_encode_meta_box_json_items( $welcome_keywords ) ); ?>"
				>
				<div class="kiyose-repeater" id="kiyose_welcome_keywords_list">
					<?php
					foreach ( $welcome_keywords as $kw ) {
						kiyose_render_welcome_keyword_row( $kw );
					}
					?>
				</div>
				<button type="button" class="button" id="kiyose_keyword_add">
					<?php esc_html_e( 'Ajouter un mot-clef', 'kiyose' ); ?>
				</button>
				<p class="kiyose-meta-box__description">
					<?php esc_html_e( 'Chaque mot-clef est un lien cliquable. L\'URL peut pointer vers une ancre (#section) ou une autre page.', 'kiyose' ); ?>
				</p>
			</div>

			<?php
			kiyose_render_text_field(
				array(
					'id'          => 'kiyose_welcome_slogan',
					'name'        => 'kiyose_welcome_slogan',
					'label'       => __( 'Slogan', 'kiyose' ),
					'value'       => $welcome_slogan,
					'description' => __( 'Affiché sous les mots-clefs, en typographie Dancing Script avec couleur accent.', 'kiyose' ),
				)
			);

			kiyose_render_meta_box_section_title(
				__( 'Overlay « À propos »', 'kiyose' ),
				__( 'Ces champs alimentent le panneau flottant qui s\'affiche au premier scroll (desktop) et la section À propos sur mobile.', 'kiyose' )
			);

			kiyose_render_textarea_field(
				array(
					'id'    => 'kiyose_hero_content',
					'name'  => 'kiyose_hero_content',
					'label' => __( 'Texte de description (overlay / mobile)', 'kiyose' ),
					'value' => $hero_content,
					'rows'  => 4,
				)
			);

			kiyose_render_text_field(
				array(
					'id'    => 'kiyose_hero_cta_text',
					'name'  => 'kiyose_hero_cta_text',
					'label' => __( 'Texte du lien « En savoir plus »', 'kiyose' ),
					'value' => $hero_cta_text,
				)
			);

			kiyose_render_text_field(
				array(
					'id'          => 'kiyose_hero_cta_url',
					'name'        => 'kiyose_hero_cta_url',
					'label'       => __( 'URL de la page « À propos »', 'kiyose' ),
					'value'       => $hero_cta_url,
					'type'        => 'url',
					'placeholder' => home_url( '/a-propos/' ),
				)
			);

			kiyose_render_media_field(
				array(
					'id'           => 'kiyose_hero_image_id',
					'name'         => 'kiyose_hero_image_id',
					'label'        => __( 'Photo (overlay / mobile)', 'kiyose' ),
					'value'        => $hero_image_id,
					'button_id'    => 'kiyose_hero_image_button',
					'remove_id'    => 'kiyose_hero_image_remove',
					'preview_id'   => 'kiyose_hero_image_preview',
					'button_label' => __( 'Choisir une image', 'kiyose' ),
					'remove_label' => __( 'Supprimer l\'image', 'kiyose' ),
					'image_size'   => 'medium_large',
				)
			);

			kiyose_render_meta_box_section_title(
				__( 'Bloc Contenu 1 — Questions & Réponses', 'kiyose' ),
				__( 'Liste de questions/réponses. Si vide, la section est masquée.', 'kiyose' )
			);
			?>

			<div class="kiyose-meta-box__field-group">
				<input
					type="hidden"
					name="kiyose_content1_qa"
					id="kiyose_content1_qa"
					value="<?php echo esc_attr( kiyose_encode_meta_box_json_items( $content1_qa_items ) ); ?>"
				>
				<div class="kiyose-repeater" id="kiyose_content1_qa_list">
					<?php
					foreach ( $content1_qa_items as $kiyose_qa_item ) {
						kiyose_render_qa_repeater_row( $kiyose_qa_item );
					}
					?>
				</div>
				<button type="button" class="button" id="kiyose_qa_add">
					<?php esc_html_e( 'Ajouter une question', 'kiyose' ); ?>
				</button>
			</div>

			<?php
			kiyose_render_text_field(
				array(
					'id'          => 'kiyose_content1_slogan',
					'name'        => 'kiyose_content1_slogan',
					'label'       => __( 'Citation / Slogan (optionnel)', 'kiyose' ),
					'value'       => $content1_slogan,
					'description' => __( 'Affiché sous les Q&R, en typographie Dancing Script.', 'kiyose' ),
				)
			);

			kiyose_render_meta_box_section_title( __( 'Bloc Contenu 2 — Texte libre', 'kiyose' ) );
			?>

			<div class="kiyose-meta-box__field-group">
				<label class="kiyose-meta-box__label" for="kiyose_content2_text">
					<?php esc_html_e( 'Texte (HTML limité : gras, italique, listes, liens)', 'kiyose' ); ?>
				</label>
				<?php
				wp_editor(
					wp_kses_post( $content2_text ),
					'kiyose_content2_text',
					array(
						'textarea_name' => 'kiyose_content2_text',
						'media_buttons' => false,
						'teeny'         => true,
						'tinymce'       => array(
							'toolbar1' => 'bold,italic,bullist,numlist,link,unlink',
						),
						'quicktags'     => false,
						'textarea_rows' => 8,
					)
				);
				?>
			</div>

			<?php
			kiyose_render_text_field(
				array(
					'id'          => 'kiyose_content2_slogan',
					'name'        => 'kiyose_content2_slogan',
					'label'       => __( 'Citation (sans guillemets)', 'kiyose' ),
					'value'       => $content2_quote,
					'description' => __( 'Saisis uniquement le texte cité, sans guillemets et sans nom d\'auteurice.', 'kiyose' ),
				)
			);

			kiyose_render_text_field(
				array(
					'id'          => 'kiyose_content2_quote_author',
					'name'        => 'kiyose_content2_quote_author',
					'label'       => __( 'Auteurice de la citation', 'kiyose' ),
					'value'       => $content2_quote_author,
					'description' => __( 'Exemple : Michel Audiard. Laisse vide si la citation n\'a pas d\'attribution.', 'kiyose' ),
				)
			);
			?>

		</div><!-- .kiyose-meta-box__fields -->
	</div><!-- .kiyose-meta-box -->
	<?php
}

/**
 * Save home hero meta box data.
 *
 * @param int $post_id Post ID.
 * @return void
 */
function kiyose_save_home_hero_meta( $post_id ) {
	// Check nonce.
	if ( ! isset( $_POST['kiyose_home_hero_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['kiyose_home_hero_nonce'] ) ), 'kiyose_home_hero_nonce_action' ) ) {
		return;
	}

	// Check autosave.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check permissions.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Only save for home page template.
	$template = get_post_meta( $post_id, '_wp_page_template', true );
	if ( 'templates/page-home.php' !== $template ) {
		return;
	}

	// Save welcome title.
	if ( isset( $_POST['kiyose_welcome_title'] ) ) {
		update_post_meta( $post_id, 'kiyose_welcome_title', sanitize_text_field( wp_unslash( $_POST['kiyose_welcome_title'] ) ) );
	}

	// Save welcome subtitle.
	if ( isset( $_POST['kiyose_welcome_subtitle'] ) ) {
		update_post_meta( $post_id, 'kiyose_welcome_subtitle', sanitize_text_field( wp_unslash( $_POST['kiyose_welcome_subtitle'] ) ) );
	}

	// Save welcome text.
	if ( isset( $_POST['kiyose_welcome_text'] ) ) {
		update_post_meta( $post_id, 'kiyose_welcome_text', sanitize_textarea_field( wp_unslash( $_POST['kiyose_welcome_text'] ) ) );
	}

	// Save welcome keywords.
	if ( isset( $_POST['kiyose_welcome_keywords'] ) ) {
		$raw_json = kiyose_get_unslashed_string_post_field( 'kiyose_welcome_keywords' );
		$keywords = kiyose_sanitize_welcome_keyword_items( $raw_json );

		update_post_meta( $post_id, 'kiyose_welcome_keywords', wp_slash( wp_json_encode( $keywords ) ) );
	}

	// Save welcome slogan.
	if ( isset( $_POST['kiyose_welcome_slogan'] ) ) {
		update_post_meta( $post_id, 'kiyose_welcome_slogan', sanitize_text_field( wp_unslash( $_POST['kiyose_welcome_slogan'] ) ) );
	}

	// Save hero content.
	if ( isset( $_POST['kiyose_hero_content'] ) ) {
		update_post_meta( $post_id, 'kiyose_hero_content', sanitize_textarea_field( wp_unslash( $_POST['kiyose_hero_content'] ) ) );
	}

	// Save hero CTA text.
	if ( isset( $_POST['kiyose_hero_cta_text'] ) ) {
		update_post_meta( $post_id, 'kiyose_hero_cta_text', sanitize_text_field( wp_unslash( $_POST['kiyose_hero_cta_text'] ) ) );
	}

	// Save hero CTA URL.
	if ( isset( $_POST['kiyose_hero_cta_url'] ) ) {
		update_post_meta( $post_id, 'kiyose_hero_cta_url', esc_url_raw( wp_unslash( $_POST['kiyose_hero_cta_url'] ) ) );
	}

	// Save hero image ID.
	if ( isset( $_POST['kiyose_hero_image_id'] ) ) {
		$image_id = absint( $_POST['kiyose_hero_image_id'] );
		if ( $image_id > 0 ) {
			update_post_meta( $post_id, 'kiyose_hero_image_id', $image_id );
		} else {
			delete_post_meta( $post_id, 'kiyose_hero_image_id' );
		}
	}

	// Save content1 Q&A items.
	if ( isset( $_POST['kiyose_content1_qa'] ) ) {
		$qa_items = kiyose_sanitize_qa_items( kiyose_get_unslashed_string_post_field( 'kiyose_content1_qa' ) );
		update_post_meta( $post_id, 'kiyose_content1_qa', wp_slash( wp_json_encode( $qa_items ) ) );
	}

	// Save content1 slogan.
	if ( isset( $_POST['kiyose_content1_slogan'] ) ) {
		update_post_meta( $post_id, 'kiyose_content1_slogan', sanitize_text_field( wp_unslash( $_POST['kiyose_content1_slogan'] ) ) );
	}

	// Save content2 text.
	if ( isset( $_POST['kiyose_content2_text'] ) ) {
		update_post_meta( $post_id, 'kiyose_content2_text', wp_kses_post( wp_unslash( $_POST['kiyose_content2_text'] ) ) );
	}

	// Save content2 slogan.
	if ( isset( $_POST['kiyose_content2_slogan'] ) ) {
		update_post_meta( $post_id, 'kiyose_content2_slogan', sanitize_text_field( wp_unslash( $_POST['kiyose_content2_slogan'] ) ) );
	}

	// Save content2 quote author.
	if ( isset( $_POST['kiyose_content2_quote_author'] ) ) {
		update_post_meta( $post_id, 'kiyose_content2_quote_author', sanitize_text_field( wp_unslash( $_POST['kiyose_content2_quote_author'] ) ) );
	}
}
add_action( 'save_post_page', 'kiyose_save_home_hero_meta' );

/**
 * Add meta box for event categories on service page template.
 *
 * @param object|null $post Current post object.
 * @return void
 */
function kiyose_add_service_event_categories_meta_box( $post = null ) {
	if ( ! kiyose_page_uses_template( $post, 'templates/page-services.php' ) ) {
		return;
	}

	add_meta_box(
		'kiyose_service_event_categories',
		__( 'Page de service — Calendrier', 'kiyose' ),
		'kiyose_render_service_event_categories_meta_box',
		'page',
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes_page', 'kiyose_add_service_event_categories_meta_box', 10, 1 );

/**
 * Render the service event categories meta box.
 *
 * @param WP_Post $post Current post object.
 * @return void
 */
function kiyose_render_service_event_categories_meta_box( $post ) {
	wp_nonce_field( 'kiyose_save_service_event_categories', 'kiyose_service_event_categories_nonce' );

	$selected_slugs = kiyose_get_service_event_category_slugs( (int) $post->ID );
	$terms          = kiyose_get_event_category_terms();
	?>
	<div class="kiyose-meta-box" data-template-required="templates/page-services.php">
		<div class="kiyose-meta-box__fields">
			<p class="kiyose-meta-box__description">
				<?php esc_html_e( 'Sélectionne les catégories à afficher quand le bouton « Voir le calendrier » est utilisé. Laisse vide pour afficher toutes les dates.', 'kiyose' ); ?>
			</p>

			<?php if ( empty( $terms ) ) : ?>
				<p class="kiyose-meta-box__description">
					<?php esc_html_e( 'Aucune catégorie Events Manager disponible pour le moment.', 'kiyose' ); ?>
				</p>
			<?php else : ?>
				<?php foreach ( $terms as $term ) : ?>
					<?php $slug = sanitize_title( (string) $term->slug ); ?>
					<label class="kiyose-meta-box__label">
						<input
							type="checkbox"
							name="kiyose_service_event_categories[]"
							value="<?php echo esc_attr( $slug ); ?>"
							<?php checked( in_array( $slug, $selected_slugs, true ) ); ?>
						>
						<?php echo esc_html( $term->name ?? $slug ); ?>
					</label>
				<?php endforeach; ?>
			<?php endif; ?>
		</div><!-- .kiyose-meta-box__fields -->
	</div><!-- .kiyose-meta-box -->
	<?php
}

/**
 * Save service event categories meta box data.
 *
 * @param int $post_id Post ID.
 * @return void
 */
function kiyose_save_service_event_categories_meta( $post_id ) {
	if ( ! isset( $_POST['kiyose_service_event_categories_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['kiyose_service_event_categories_nonce'] ) ), 'kiyose_save_service_event_categories' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$template = get_post_meta( $post_id, '_wp_page_template', true );
	if ( 'templates/page-services.php' !== $template ) {
		return;
	}

	$raw_categories = array();

	if ( isset( $_POST['kiyose_service_event_categories'] ) && is_array( $_POST['kiyose_service_event_categories'] ) ) {
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized by kiyose_sanitize_event_category_slugs().
		$raw_categories = wp_unslash( $_POST['kiyose_service_event_categories'] );
	}

	$categories = kiyose_sanitize_event_category_slugs( $raw_categories );

	if ( empty( $categories ) ) {
		delete_post_meta( $post_id, 'kiyose_service_event_categories' );
		return;
	}

	update_post_meta( $post_id, 'kiyose_service_event_categories', $categories );
}
add_action( 'save_post_page', 'kiyose_save_service_event_categories_meta' );

/**
 * Add meta box for contact photo on contact page template.
 *
 * @param object|null $post Current post object.
 * @return void
 */
function kiyose_add_contact_photo_meta_box( $post = null ) {
	if ( ! kiyose_page_uses_template( $post, 'templates/page-contact.php' ) ) {
		return;
	}

	add_meta_box(
		'kiyose_contact_photo',
		__( 'Page de contact — Photo', 'kiyose' ),
		'kiyose_render_contact_photo_meta_box',
		'page',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_page', 'kiyose_add_contact_photo_meta_box', 10, 1 );

/**
 * Render the contact photo meta box.
 *
 * @param WP_Post $post Current post object.
 * @return void
 */
function kiyose_render_contact_photo_meta_box( $post ) {
	wp_nonce_field( 'kiyose_save_contact_photo', 'kiyose_contact_photo_nonce' );

	$photo_id  = get_post_meta( $post->ID, 'kiyose_contact_photo_id', true );
	$photo_alt = get_post_meta( $post->ID, 'kiyose_contact_photo_alt', true );
	?>
	<div class="kiyose-meta-box" data-template-required="templates/page-contact.php">
		<div class="kiyose-meta-box__fields">
			<?php
			kiyose_render_meta_box_section_title( __( 'Photo de contact', 'kiyose' ) );

			kiyose_render_media_field(
				array(
					'id'               => 'kiyose_contact_photo_id',
					'name'             => 'kiyose_contact_photo_id',
					'label'            => __( 'Image', 'kiyose' ),
					'value'            => $photo_id,
					'button_id'        => 'kiyose_contact_photo_button',
					'remove_id'        => 'kiyose_contact_photo_remove',
					'preview_id'       => 'kiyose_contact_photo_preview',
					'button_label'     => __( 'Choisir une image', 'kiyose' ),
					'remove_label'     => __( 'Supprimer l\'image', 'kiyose' ),
					'image_size'       => 'medium',
					'preview_modifier' => 'kiyose-meta-box__image-preview--contact',
				)
			);

			kiyose_render_text_field(
				array(
					'id'          => 'kiyose_contact_photo_alt',
					'name'        => 'kiyose_contact_photo_alt',
					'label'       => __( 'Texte alternatif (accessibilité)', 'kiyose' ),
					'value'       => $photo_alt,
					'description' => __( 'Recommandé : renseigner le nom de la personne (ex : « Sandrine Delmas »). Laisser vide pour marquer l\'image comme décorative.', 'kiyose' ),
				)
			);
			?>
		</div><!-- .kiyose-meta-box__fields -->
	</div><!-- .kiyose-meta-box -->
	<?php
}

/**
 * Save contact photo meta box data.
 *
 * @param int $post_id Post ID.
 * @return void
 */
function kiyose_save_contact_photo_meta( $post_id ) {
	if ( ! isset( $_POST['kiyose_contact_photo_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['kiyose_contact_photo_nonce'] ) ), 'kiyose_save_contact_photo' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$template = get_post_meta( $post_id, '_wp_page_template', true );
	if ( 'templates/page-contact.php' !== $template ) {
		return;
	}

	if ( isset( $_POST['kiyose_contact_photo_id'] ) ) {
		$photo_id = absint( $_POST['kiyose_contact_photo_id'] );
		if ( $photo_id > 0 ) {
			update_post_meta( $post_id, 'kiyose_contact_photo_id', $photo_id );
		} else {
			delete_post_meta( $post_id, 'kiyose_contact_photo_id' );
		}
	}

	if ( isset( $_POST['kiyose_contact_photo_alt'] ) ) {
		update_post_meta( $post_id, 'kiyose_contact_photo_alt', sanitize_text_field( wp_unslash( $_POST['kiyose_contact_photo_alt'] ) ) );
	}
}
add_action( 'save_post_page', 'kiyose_save_contact_photo_meta' );
