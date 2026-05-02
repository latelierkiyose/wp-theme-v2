<?php
/**
 * Meta boxes for page templates.
 *
 * @package Kiyose
 * @since   0.2.0
 */

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
	$content2_text   = get_post_meta( $post->ID, 'kiyose_content2_text', true );
	$content2_slogan = get_post_meta( $post->ID, 'kiyose_content2_slogan', true );

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

	?>
	<div class="kiyose-meta-box" data-template-required="templates/page-home.php">
		<style>
			.kiyose-meta-box { padding: 10px 0; }
			.kiyose-meta-box .field-group { margin-bottom: 20px; }
			.kiyose-meta-box label { display: block; font-weight: 600; margin-bottom: 5px; }
			.kiyose-meta-box input[type="text"],
			.kiyose-meta-box input[type="url"],
			.kiyose-meta-box textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
			.kiyose-meta-box textarea { min-height: 100px; resize: vertical; }
			.kiyose-meta-box .description { font-size: 13px; color: #666; margin-top: 5px; font-style: italic; }
			.kiyose-meta-box .image-preview { margin-top: 10px; max-width: 400px; }
			.kiyose-meta-box .image-preview img { max-width: 100%; height: auto; border: 1px solid #ddd; border-radius: 4px; }
			.kiyose-meta-box .button-group { margin-top: 10px; }
		</style>

		<div class="kiyose-meta-box__fields">

		<h3 style="margin: 0 0 15px; padding-bottom: 8px; border-bottom: 1px solid #ddd;">
			<?php esc_html_e( 'Bloc Bienvenue', 'kiyose' ); ?>
		</h3>

		<!-- Titre bienvenue -->
		<div class="field-group">
			<label for="kiyose_welcome_title">
				<?php esc_html_e( 'Titre (H1) du bloc bienvenue', 'kiyose' ); ?>
			</label>
			<input
				type="text"
				name="kiyose_welcome_title"
				id="kiyose_welcome_title"
				value="<?php echo esc_attr( $welcome_title ); ?>"
			>
			<p class="description">
				<?php esc_html_e( 'Titre principal affiché en haut de la page, en typographie Dancing Script.', 'kiyose' ); ?>
			</p>
		</div>

		<!-- Sous-titre -->
		<div class="field-group">
			<label for="kiyose_welcome_subtitle">
				<?php esc_html_e( 'Sous-titre', 'kiyose' ); ?>
			</label>
			<input
				type="text"
				name="kiyose_welcome_subtitle"
				id="kiyose_welcome_subtitle"
				value="<?php echo esc_attr( $welcome_subtitle ); ?>"
			>
		</div>

		<!-- Texte descriptif -->
		<div class="field-group">
			<label for="kiyose_welcome_text">
				<?php esc_html_e( 'Texte descriptif', 'kiyose' ); ?>
			</label>
			<textarea
				name="kiyose_welcome_text"
				id="kiyose_welcome_text"
				rows="4"
			><?php echo esc_textarea( $welcome_text ); ?></textarea>
		</div>

		<!-- Mots-clefs (repeater) -->
		<div class="field-group">
			<label>
				<?php esc_html_e( 'Mots-clefs (chips cliquables)', 'kiyose' ); ?>
			</label>
			<input
				type="hidden"
				name="kiyose_welcome_keywords"
				id="kiyose_welcome_keywords"
				value="<?php echo esc_attr( $welcome_keywords_raw ? $welcome_keywords_raw : '[]' ); ?>"
			>
			<div id="kiyose_welcome_keywords_list">
				<?php foreach ( $welcome_keywords as $kw ) : ?>
				<div class="keyword-row" style="display:flex;gap:8px;margin-bottom:8px;align-items:center;">
					<input
						type="text"
						placeholder="<?php esc_attr_e( 'Label', 'kiyose' ); ?>"
						class="kw-label"
						value="<?php echo esc_attr( $kw['label'] ); ?>"
						style="flex:1;"
					>
					<input
						type="text"
						placeholder="<?php esc_attr_e( 'URL (ex: /services/ ou #ancre)', 'kiyose' ); ?>"
						class="kw-url"
						value="<?php echo esc_attr( $kw['url'] ); ?>"
						style="flex:2;"
					>
					<button type="button" class="button kw-remove">
						<?php esc_html_e( 'Supprimer', 'kiyose' ); ?>
					</button>
				</div>
				<?php endforeach; ?>
			</div>
			<button type="button" class="button" id="kiyose_keyword_add">
				<?php esc_html_e( 'Ajouter un mot-clef', 'kiyose' ); ?>
			</button>
			<p class="description">
				<?php esc_html_e( 'Chaque mot-clef est un lien cliquable. L\'URL peut pointer vers une ancre (#section) ou une autre page.', 'kiyose' ); ?>
			</p>
		</div>

		<!-- Slogan -->
		<div class="field-group">
			<label for="kiyose_welcome_slogan">
				<?php esc_html_e( 'Slogan', 'kiyose' ); ?>
			</label>
			<input
				type="text"
				name="kiyose_welcome_slogan"
				id="kiyose_welcome_slogan"
				value="<?php echo esc_attr( $welcome_slogan ); ?>"
			>
			<p class="description">
				<?php esc_html_e( 'Affiché sous les mots-clefs, en typographie Dancing Script avec couleur accent.', 'kiyose' ); ?>
			</p>
		</div>

		<h3 style="margin: 25px 0 15px; padding-bottom: 8px; border-bottom: 1px solid #ddd;">
			<?php esc_html_e( 'Overlay « À propos »', 'kiyose' ); ?>
		</h3>
		<p class="description" style="margin-bottom:15px;">
			<?php esc_html_e( 'Ces champs alimentent le panneau flottant qui s\'affiche au premier scroll (desktop) et la section À propos sur mobile.', 'kiyose' ); ?>
		</p>

		<!-- Texte de description (overlay / mobile) -->
		<div class="field-group">
			<label for="kiyose_hero_content">
				<?php esc_html_e( 'Texte de description (overlay / mobile)', 'kiyose' ); ?>
			</label>
			<textarea
				name="kiyose_hero_content"
				id="kiyose_hero_content"
				rows="4"
			><?php echo esc_textarea( $hero_content ); ?></textarea>
		</div>

		<!-- Texte du lien En savoir plus -->
		<div class="field-group">
			<label for="kiyose_hero_cta_text">
				<?php esc_html_e( 'Texte du lien « En savoir plus »', 'kiyose' ); ?>
			</label>
			<input
				type="text"
				name="kiyose_hero_cta_text"
				id="kiyose_hero_cta_text"
				value="<?php echo esc_attr( $hero_cta_text ); ?>"
			>
		</div>

		<!-- URL de la page À propos -->
		<div class="field-group">
			<label for="kiyose_hero_cta_url">
				<?php esc_html_e( 'URL de la page « À propos »', 'kiyose' ); ?>
			</label>
			<input
				type="url"
				name="kiyose_hero_cta_url"
				id="kiyose_hero_cta_url"
				value="<?php echo esc_url( $hero_cta_url ); ?>"
				placeholder="<?php echo esc_attr( home_url( '/a-propos/' ) ); ?>"
			>
		</div>

		<!-- Photo (overlay / mobile) -->
		<div class="field-group">
			<label for="kiyose_hero_image">
				<?php esc_html_e( 'Photo (overlay / mobile)', 'kiyose' ); ?>
			</label>
			<input
				type="hidden"
				name="kiyose_hero_image_id"
				id="kiyose_hero_image_id"
				value="<?php echo esc_attr( $hero_image_id ); ?>"
			>
			<div class="button-group">
				<button type="button" class="button" id="kiyose_hero_image_button">
					<?php esc_html_e( 'Choisir une image', 'kiyose' ); ?>
				</button>
				<button type="button" class="button" id="kiyose_hero_image_remove" style="<?php echo empty( $hero_image_id ) ? 'display:none;' : ''; ?>">
					<?php esc_html_e( 'Supprimer l\'image', 'kiyose' ); ?>
				</button>
			</div>
			<div class="image-preview" id="kiyose_hero_image_preview">
				<?php
				if ( ! empty( $hero_image_id ) ) {
					echo wp_get_attachment_image( $hero_image_id, 'medium_large' );
				}
				?>
			</div>
		</div>

		<h3 style="margin: 25px 0 15px; padding-bottom: 8px; border-bottom: 1px solid #ddd;">
			<?php esc_html_e( 'Bloc Contenu 1 — Questions & Réponses', 'kiyose' ); ?>
		</h3>
		<p class="description" style="margin-bottom:15px;">
			<?php esc_html_e( 'Liste de questions/réponses. Si vide, la section est masquée.', 'kiyose' ); ?>
		</p>

		<!-- Repeater Q&A -->
		<div class="field-group">
			<input
				type="hidden"
				name="kiyose_content1_qa"
				id="kiyose_content1_qa"
				value="<?php echo esc_attr( $content1_qa_raw ? $content1_qa_raw : '[]' ); ?>"
			>
			<div id="kiyose_content1_qa_list">
				<?php
				$content1_qa_items = $content1_qa_raw ? json_decode( $content1_qa_raw, true ) : array();
				if ( ! is_array( $content1_qa_items ) ) {
					$content1_qa_items = array();
				}
				foreach ( $content1_qa_items as $kiyose_qa_item ) :
					?>
				<div class="qa-row" style="margin-bottom:16px;padding:12px;background:#f9f9f9;border:1px solid #ddd;border-radius:4px;">
					<div style="margin-bottom:8px;">
						<label style="display:block;font-weight:600;margin-bottom:4px;"><?php esc_html_e( 'Question', 'kiyose' ); ?></label>
						<input
							type="text"
							class="qa-question"
							value="<?php echo esc_attr( $kiyose_qa_item['question'] ); ?>"
							style="width:100%;"
						>
					</div>
					<div style="margin-bottom:8px;">
						<label style="display:block;font-weight:600;margin-bottom:4px;"><?php esc_html_e( 'Réponse', 'kiyose' ); ?></label>
						<textarea class="qa-answer" rows="3" style="width:100%;resize:vertical;"><?php echo esc_textarea( $kiyose_qa_item['answer'] ); ?></textarea>
					</div>
					<button type="button" class="button qa-remove"><?php esc_html_e( 'Supprimer', 'kiyose' ); ?></button>
				</div>
				<?php endforeach; ?>
			</div>
			<button type="button" class="button" id="kiyose_qa_add">
				<?php esc_html_e( 'Ajouter une question', 'kiyose' ); ?>
			</button>
		</div>

		<!-- Slogan Contenu 1 -->
		<div class="field-group">
			<label for="kiyose_content1_slogan">
				<?php esc_html_e( 'Citation / Slogan (optionnel)', 'kiyose' ); ?>
			</label>
			<input
				type="text"
				name="kiyose_content1_slogan"
				id="kiyose_content1_slogan"
				value="<?php echo esc_attr( $content1_slogan ); ?>"
			>
			<p class="description">
				<?php esc_html_e( 'Affiché sous les Q&R, en typographie Dancing Script.', 'kiyose' ); ?>
			</p>
		</div>

		<h3 style="margin: 25px 0 15px; padding-bottom: 8px; border-bottom: 1px solid #ddd;">
			<?php esc_html_e( 'Bloc Contenu 2 — Texte libre', 'kiyose' ); ?>
		</h3>

		<!-- Texte riche Contenu 2 -->
		<div class="field-group">
			<label for="kiyose_content2_text">
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

		<!-- Slogan Contenu 2 -->
		<div class="field-group">
			<label for="kiyose_content2_slogan">
				<?php esc_html_e( 'Citation / Slogan (optionnel)', 'kiyose' ); ?>
			</label>
			<input
				type="text"
				name="kiyose_content2_slogan"
				id="kiyose_content2_slogan"
				value="<?php echo esc_attr( $content2_slogan ); ?>"
			>
			<p class="description">
				<?php esc_html_e( 'Affiché sous le texte, en typographie Dancing Script.', 'kiyose' ); ?>
			</p>
		</div>

		</div><!-- .kiyose-meta-box__fields -->
	</div><!-- .kiyose-meta-box -->

	<script>
	jQuery(document).ready(function($) {
		var mediaUploader;

		// Keywords repeater
		function serializeKeywords() {
			var keywords = [];
			$('#kiyose_welcome_keywords_list .keyword-row').each(function() {
				var label = $(this).find('.kw-label').val().trim();
				var url = $(this).find('.kw-url').val().trim();
				if (label) {
					keywords.push({ label: label, url: url });
				}
			});
			$('#kiyose_welcome_keywords').val(JSON.stringify(keywords));
		}

		$('#kiyose_keyword_add').on('click', function() {
			var row = '<div class="keyword-row" style="display:flex;gap:8px;margin-bottom:8px;align-items:center;">' +
				'<input type="text" placeholder="Label" class="kw-label" style="flex:1;">' +
				'<input type="text" placeholder="URL (ex: /services/ ou #ancre)" class="kw-url" style="flex:2;">' +
				'<button type="button" class="button kw-remove">Supprimer</button>' +
				'</div>';
			$('#kiyose_welcome_keywords_list').append(row);
		});

		$(document).on('click', '.kw-remove', function() {
			$(this).closest('.keyword-row').remove();
			serializeKeywords();
		});

		// Serialize on any input change in keywords list
		$(document).on('change input', '#kiyose_welcome_keywords_list input', function() {
			serializeKeywords();
		});

		// Serialize before form submit
		$('form#post').on('submit', function() {
			serializeKeywords();
		});

		// Q&A repeater
		function serializeQA() {
			var items = [];
			$('#kiyose_content1_qa_list .qa-row').each(function() {
				var question = $(this).find('.qa-question').val().trim();
				var answer = $(this).find('.qa-answer').val().trim();
				if (question || answer) {
					items.push({ question: question, answer: answer });
				}
			});
			$('#kiyose_content1_qa').val(JSON.stringify(items));
		}

		$('#kiyose_qa_add').on('click', function() {
			var row = '<div class="qa-row" style="margin-bottom:16px;padding:12px;background:#f9f9f9;border:1px solid #ddd;border-radius:4px;">' +
				'<div style="margin-bottom:8px;"><label style="display:block;font-weight:600;margin-bottom:4px;"><?php echo esc_html__( 'Question', 'kiyose' ); ?></label>' +
				'<input type="text" class="qa-question" style="width:100%;"></div>' +
				'<div style="margin-bottom:8px;"><label style="display:block;font-weight:600;margin-bottom:4px;"><?php echo esc_html__( 'Réponse', 'kiyose' ); ?></label>' +
				'<textarea class="qa-answer" rows="3" style="width:100%;resize:vertical;"></textarea></div>' +
				'<button type="button" class="button qa-remove"><?php echo esc_html__( 'Supprimer', 'kiyose' ); ?></button>' +
				'</div>';
			$('#kiyose_content1_qa_list').append(row);
		});

		$(document).on('click', '.qa-remove', function() {
			$(this).closest('.qa-row').remove();
			serializeQA();
		});

		$(document).on('change input', '#kiyose_content1_qa_list input, #kiyose_content1_qa_list textarea', function() {
			serializeQA();
		});

		// Serialize Q&A before form submit
		$('form#post').on('submit', function() {
			serializeQA();
		});

		// Media uploader
		$('#kiyose_hero_image_button').on('click', function(e) {
			e.preventDefault();

			if (mediaUploader) {
				mediaUploader.open();
				return;
			}

			mediaUploader = wp.media({
				title: '<?php esc_html_e( 'Choisir une image de fond', 'kiyose' ); ?>',
				button: {
					text: '<?php esc_html_e( 'Utiliser cette image', 'kiyose' ); ?>'
				},
				multiple: false
			});

			mediaUploader.on('select', function() {
				var attachment = mediaUploader.state().get('selection').first().toJSON();
				$('#kiyose_hero_image_id').val(attachment.id);
				$('#kiyose_hero_image_preview').html('<img src="' + attachment.url + '" style="max-width:100%;height:auto;">');
				$('#kiyose_hero_image_remove').show();
			});

			mediaUploader.open();
		});

		$('#kiyose_hero_image_remove').on('click', function(e) {
			e.preventDefault();
			$('#kiyose_hero_image_id').val('');
			$('#kiyose_hero_image_preview').html('');
			$(this).hide();
		});
	});
	</script>
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
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitization happens inside kiyose_sanitize_welcome_keyword_item() after json_decode(); applying sanitize_text_field() here would corrupt \uXXXX Unicode escapes.
		$raw_json = wp_unslash( $_POST['kiyose_welcome_keywords'] );
		$decoded  = json_decode( $raw_json, true );

		if ( is_array( $decoded ) ) {
			$sanitized = array();
			foreach ( $decoded as $item ) {
				if ( isset( $item['label'], $item['url'] ) ) {
					$clean = kiyose_sanitize_welcome_keyword_item( $item['label'], $item['url'] );
					if ( null !== $clean ) {
						$sanitized[] = $clean;
					}
				}
			}
			update_post_meta( $post_id, 'kiyose_welcome_keywords', wp_slash( wp_json_encode( $sanitized ) ) );
		}
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
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitization happens inside kiyose_sanitize_qa_items() after json_decode(); applying sanitize_text_field() here would corrupt \uXXXX Unicode escapes.
		$qa_items = kiyose_sanitize_qa_items( wp_unslash( $_POST['kiyose_content1_qa'] ) );
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
}
add_action( 'save_post_page', 'kiyose_save_home_hero_meta' );

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
			<fieldset style="border: none; margin: 0; padding: 0;">
				<legend style="font-weight: 600; margin-bottom: 15px; font-size: 14px;">
					<?php esc_html_e( 'Photo de contact', 'kiyose' ); ?>
				</legend>

				<!-- Champ image -->
				<div class="field-group">
					<input
						type="hidden"
						name="kiyose_contact_photo_id"
						id="kiyose_contact_photo_id"
						value="<?php echo esc_attr( $photo_id ); ?>"
					>
					<div class="button-group">
						<button type="button" class="button" id="kiyose_contact_photo_button">
							<?php esc_html_e( 'Choisir une image', 'kiyose' ); ?>
						</button>
						<button type="button" class="button" id="kiyose_contact_photo_remove" style="<?php echo empty( $photo_id ) ? 'display:none;' : ''; ?>">
							<?php esc_html_e( 'Supprimer l\'image', 'kiyose' ); ?>
						</button>
					</div>
					<div class="image-preview" id="kiyose_contact_photo_preview">
						<?php
						if ( ! empty( $photo_id ) ) {
							echo wp_get_attachment_image( $photo_id, 'medium' );
						}
						?>
					</div>
				</div>

				<!-- Champ alt text -->
				<div class="field-group">
					<label for="kiyose_contact_photo_alt">
						<?php esc_html_e( 'Texte alternatif (accessibilité)', 'kiyose' ); ?>
					</label>
					<input
						type="text"
						name="kiyose_contact_photo_alt"
						id="kiyose_contact_photo_alt"
						value="<?php echo esc_attr( $photo_alt ); ?>"
						style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; margin-top: 5px;"
					>
					<p class="description" style="font-size: 13px; color: #666; margin-top: 5px; font-style: italic;">
						<?php esc_html_e( 'Recommandé : renseigner le nom de la personne (ex : « Sandrine Delmas »). Laisser vide pour marquer l\'image comme décorative.', 'kiyose' ); ?>
					</p>
				</div>
			</fieldset>
		</div><!-- .kiyose-meta-box__fields -->
	</div><!-- .kiyose-meta-box -->

	<script>
	jQuery(document).ready(function($) {
		var contactPhotoUploader;

		$('#kiyose_contact_photo_button').on('click', function(e) {
			e.preventDefault();

			if (contactPhotoUploader) {
				contactPhotoUploader.open();
				return;
			}

			contactPhotoUploader = wp.media({
				title: '<?php esc_html_e( 'Choisir une photo de contact', 'kiyose' ); ?>',
				button: {
					text: '<?php esc_html_e( 'Utiliser cette image', 'kiyose' ); ?>'
				},
				multiple: false
			});

			contactPhotoUploader.on('select', function() {
				var attachment = contactPhotoUploader.state().get('selection').first().toJSON();
				$('#kiyose_contact_photo_id').val(attachment.id);
				$('#kiyose_contact_photo_preview').html('<img src="' + attachment.url + '" style="max-width:200px;height:auto;border-radius:50%;margin-top:10px;">');
				$('#kiyose_contact_photo_remove').show();
			});

			contactPhotoUploader.open();
		});

		$('#kiyose_contact_photo_remove').on('click', function(e) {
			e.preventDefault();
			$('#kiyose_contact_photo_id').val('');
			$('#kiyose_contact_photo_preview').html('');
			$(this).hide();
		});
	});
	</script>
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
