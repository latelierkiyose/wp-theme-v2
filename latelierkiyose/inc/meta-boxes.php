<?php
/**
 * Meta boxes for page templates.
 *
 * @package Kiyose
 * @since   0.2.0
 */

/**
 * Add meta box for hero section on home page template.
 *
 * @return void
 */
function kiyose_add_home_hero_meta_box() {
	add_meta_box(
		'kiyose_home_hero',
		__( 'Section Hero (En-tête)', 'kiyose' ),
		'kiyose_render_home_hero_meta_box',
		'page',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'kiyose_add_home_hero_meta_box' );

/**
 * Render the home hero meta box.
 *
 * @param WP_Post $post Current post object.
 * @return void
 */
function kiyose_render_home_hero_meta_box( $post ) {
	// Check current template.
	$template   = get_post_meta( $post->ID, '_wp_page_template', true );
	$is_visible = ( 'templates/page-home.php' === $template );

	// Add nonce for security.
	wp_nonce_field( 'kiyose_home_hero_nonce_action', 'kiyose_home_hero_nonce' );

	// Get current values.
	$hero_title    = get_post_meta( $post->ID, 'kiyose_hero_title', true );
	$hero_content  = get_post_meta( $post->ID, 'kiyose_hero_content', true );
	$hero_cta_text = get_post_meta( $post->ID, 'kiyose_hero_cta_text', true );
	$hero_cta_url  = get_post_meta( $post->ID, 'kiyose_hero_cta_url', true );
	$hero_image_id = get_post_meta( $post->ID, 'kiyose_hero_image_id', true );

	// Default values.
	if ( empty( $hero_title ) ) {
		$hero_title = 'L\'être humain est un vitrail, révélons ensemble sa lumière';
	}
	if ( empty( $hero_content ) ) {
		$hero_content = 'Au sein de L\'Atelier Kiyose, découvrez des pratiques de bien-être et de développement personnel qui vous accompagnent dans votre transformation : art-thérapie, rigologie, bols tibétains et ateliers philo.';
	}
	if ( empty( $hero_cta_text ) ) {
		$hero_cta_text = 'Découvrir les prochains ateliers';
	}
	if ( empty( $hero_cta_url ) ) {
		$hero_cta_url = home_url( '/calendrier-tarifs/' );
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
			.kiyose-meta-box__notice { 
				background: #fff8e5; 
				border-left: 4px solid #f0b849; 
				padding: 12px; 
				margin-bottom: 15px;
			}
			.kiyose-meta-box__notice p { margin: 0; }
			.kiyose-meta-box__fields { display: <?php echo $is_visible ? 'block' : 'none'; ?>; }
		</style>

		<!-- Notice -->
		<div class="kiyose-meta-box__notice" style="display: <?php echo $is_visible ? 'none' : 'block'; ?>;">
			<p>
				<strong><?php esc_html_e( 'Information :', 'kiyose' ); ?></strong>
				<?php esc_html_e( 'Cette section sera active une fois que vous aurez sélectionné le template "Page d\'accueil" dans l\'encadré "Attributs de page" ci-dessous.', 'kiyose' ); ?>
			</p>
		</div>

		<!-- Champs (masqués si template pas sélectionné) -->
		<div class="kiyose-meta-box__fields">
		<!-- Titre -->
		<div class="field-group">
			<label for="kiyose_hero_title">
				<?php esc_html_e( 'Titre principal (H1)', 'kiyose' ); ?>
			</label>
			<input 
				type="text" 
				name="kiyose_hero_title" 
				id="kiyose_hero_title" 
				value="<?php echo esc_attr( $hero_title ); ?>"
			>
			<p class="description">
				<?php esc_html_e( 'Le titre principal affiché en haut de la page d\'accueil.', 'kiyose' ); ?>
			</p>
		</div>

		<!-- Contenu / Sous-titre -->
		<div class="field-group">
			<label for="kiyose_hero_content">
				<?php esc_html_e( 'Texte descriptif', 'kiyose' ); ?>
			</label>
			<textarea 
				name="kiyose_hero_content" 
				id="kiyose_hero_content" 
				rows="4"
			><?php echo esc_textarea( $hero_content ); ?></textarea>
			<p class="description">
				<?php esc_html_e( 'Le texte descriptif affiché sous le titre.', 'kiyose' ); ?>
			</p>
		</div>

		<!-- CTA Texte -->
		<div class="field-group">
			<label for="kiyose_hero_cta_text">
				<?php esc_html_e( 'Texte du bouton', 'kiyose' ); ?>
			</label>
			<input 
				type="text" 
				name="kiyose_hero_cta_text" 
				id="kiyose_hero_cta_text" 
				value="<?php echo esc_attr( $hero_cta_text ); ?>"
			>
			<p class="description">
				<?php esc_html_e( 'Le texte affiché sur le bouton d\'appel à l\'action.', 'kiyose' ); ?>
			</p>
		</div>

		<!-- CTA URL -->
		<div class="field-group">
			<label for="kiyose_hero_cta_url">
				<?php esc_html_e( 'Lien du bouton', 'kiyose' ); ?>
			</label>
			<input 
				type="url" 
				name="kiyose_hero_cta_url" 
				id="kiyose_hero_cta_url" 
				value="<?php echo esc_url( $hero_cta_url ); ?>"
				placeholder="<?php echo esc_attr( home_url( '/' ) ); ?>"
			>
			<p class="description">
				<?php esc_html_e( 'L\'URL vers laquelle le bouton redirige (ex: /calendrier-tarifs/).', 'kiyose' ); ?>
			</p>
		</div>

		<!-- Image de fond -->
		<div class="field-group">
			<label for="kiyose_hero_image">
				<?php esc_html_e( 'Image de fond', 'kiyose' ); ?>
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
			<p class="description">
				<?php esc_html_e( 'Image de fond pour la section hero. Si aucune image n\'est définie, la couleur de fond par défaut sera utilisée.', 'kiyose' ); ?>
			</p>
		</div>
		</div><!-- .kiyose-meta-box__fields -->
	</div><!-- .kiyose-meta-box -->

	<script>
	jQuery(document).ready(function($) {
		var mediaUploader;

		// Show/hide hero fields based on template selection
		function toggleHeroFields() {
			var selectedTemplate = $('#page_template').val();
			var requiredTemplate = 'templates/page-home.php';
			
			if (selectedTemplate === requiredTemplate) {
				$('.kiyose-meta-box__notice').hide();
				$('.kiyose-meta-box__fields').show();
			} else {
				$('.kiyose-meta-box__notice').show();
				$('.kiyose-meta-box__fields').hide();
			}
		}

		// Run on page load
		toggleHeroFields();

		// Run when template changes
		$('#page_template').on('change', function() {
			toggleHeroFields();
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

	// Save hero title.
	if ( isset( $_POST['kiyose_hero_title'] ) ) {
		update_post_meta( $post_id, 'kiyose_hero_title', sanitize_text_field( wp_unslash( $_POST['kiyose_hero_title'] ) ) );
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
}
add_action( 'save_post_page', 'kiyose_save_home_hero_meta' );

/**
 * Enregistrer la meta box pour l'affichage sur la page d'accueil.
 *
 * Cette meta box est visible uniquement sur les pages utilisant le template "Page de service".
 *
 * @since 0.1.17
 */
function kiyose_register_homepage_display_meta_box() {
	add_meta_box(
		'kiyose_homepage_display',
		__( 'Affichage page d\'accueil', 'kiyose' ),
		'kiyose_homepage_display_meta_box_callback',
		'page',
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'kiyose_register_homepage_display_meta_box' );

/**
 * Callback pour afficher le contenu de la meta box.
 *
 * @since 0.1.17
 *
 * @param WP_Post $post L'objet post actuel.
 */
function kiyose_homepage_display_meta_box_callback( $post ) {
	// Vérifier le template de la page.
	$page_template = get_post_meta( $post->ID, '_wp_page_template', true );

	// N'afficher la meta box que pour les pages avec template "Page de service".
	if ( 'templates/page-services.php' !== $page_template ) {
		?>
		<p class="description" style="background: #fff8e5; border-left: 4px solid #f0b849; padding: 12px; margin: 0;">
			<strong><?php esc_html_e( 'Information :', 'kiyose' ); ?></strong><br>
			<?php esc_html_e( 'Cette option est disponible uniquement pour les pages utilisant le template "Page de service".', 'kiyose' ); ?>
		</p>
		<?php
		return;
	}

	// Récupérer la valeur actuelle.
	$show_on_homepage = get_post_meta( $post->ID, 'kiyose_show_on_homepage', true );

	// Nonce pour la sécurité.
	wp_nonce_field( 'kiyose_save_homepage_display', 'kiyose_homepage_display_nonce' );

	?>
	<p>
		<label for="kiyose_show_on_homepage">
			<input
				type="checkbox"
				id="kiyose_show_on_homepage"
				name="kiyose_show_on_homepage"
				value="1"
				<?php checked( $show_on_homepage, '1' ); ?>
			/>
			<?php esc_html_e( 'Afficher dans la section "Mes activités"', 'kiyose' ); ?>
		</label>
	</p>
	<p class="description">
		<?php esc_html_e( 'Pour contrôler l\'ordre d\'affichage, utilisez le champ "Ordre" dans la section "Attributs de page" ci-dessous.', 'kiyose' ); ?>
	</p>
	<?php
}

/**
 * Sauvegarder la meta box lors de la sauvegarde de la page.
 *
 * @since 0.1.17
 *
 * @param int $post_id L'ID du post.
 */
function kiyose_save_homepage_display_meta_box( $post_id ) {
	// Vérifier le nonce.
	if ( ! isset( $_POST['kiyose_homepage_display_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['kiyose_homepage_display_nonce'] ) ), 'kiyose_save_homepage_display' ) ) {
		return;
	}

	// Vérifier l'autosave.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Vérifier les permissions.
	if ( ! current_user_can( 'edit_page', $post_id ) ) {
		return;
	}

	// Vérifier le template de la page.
	$page_template = get_post_meta( $post_id, '_wp_page_template', true );
	if ( 'templates/page-services.php' !== $page_template ) {
		// Si ce n'est pas une page de service, supprimer la meta pour éviter les incohérences.
		delete_post_meta( $post_id, 'kiyose_show_on_homepage' );
		return;
	}

	// Sauvegarder la valeur.
	if ( isset( $_POST['kiyose_show_on_homepage'] ) && '1' === $_POST['kiyose_show_on_homepage'] ) {
		update_post_meta( $post_id, 'kiyose_show_on_homepage', '1' );
	} else {
		delete_post_meta( $post_id, 'kiyose_show_on_homepage' );
	}
}
add_action( 'save_post_page', 'kiyose_save_homepage_display_meta_box' );
