<?php
/**
 * Custom Post Types registration.
 *
 * @package Kiyose
 * @since   0.1.6
 */

/**
 * Register kiyose_testimony custom post type.
 *
 * @return void
 */
function kiyose_register_testimony_cpt() {
	$labels = array(
		'name'               => __( 'Témoignages', 'kiyose' ),
		'singular_name'      => __( 'Témoignage', 'kiyose' ),
		'add_new'            => __( 'Ajouter un témoignage', 'kiyose' ),
		'add_new_item'       => __( 'Ajouter un nouveau témoignage', 'kiyose' ),
		'edit_item'          => __( 'Modifier le témoignage', 'kiyose' ),
		'new_item'           => __( 'Nouveau témoignage', 'kiyose' ),
		'view_item'          => __( 'Voir le témoignage', 'kiyose' ),
		'search_items'       => __( 'Rechercher des témoignages', 'kiyose' ),
		'not_found'          => __( 'Aucun témoignage trouvé', 'kiyose' ),
		'not_found_in_trash' => __( 'Aucun témoignage dans la corbeille', 'kiyose' ),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => false,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'menu_icon'          => 'dashicons-format-quote',
		'supports'           => array( 'title', 'editor' ),
		'has_archive'        => false,
		'publicly_queryable' => false,
	);

	register_post_type( 'kiyose_testimony', $args );
}
add_action( 'init', 'kiyose_register_testimony_cpt' );

/**
 * Add meta box for testimony context.
 *
 * @return void
 */
function kiyose_add_testimony_meta_box() {
	add_meta_box(
		'kiyose_testimony_context',
		__( 'Contexte du témoignage', 'kiyose' ),
		'kiyose_render_testimony_context_meta_box',
		'kiyose_testimony',
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'kiyose_add_testimony_meta_box' );

/**
 * Render the testimony context meta box.
 *
 * @param WP_Post $post Current post object.
 * @return void
 */
function kiyose_render_testimony_context_meta_box( $post ) {
	// Add nonce for security.
	wp_nonce_field( 'kiyose_testimony_context_nonce_action', 'kiyose_testimony_context_nonce' );

	// Get current value.
	$context = get_post_meta( $post->ID, 'kiyose_testimony_context', true );
	if ( empty( $context ) ) {
		$context = 'individual';
	}

	// Render select field.
	?>
	<label for="kiyose_testimony_context_field">
		<?php esc_html_e( 'Type de contexte :', 'kiyose' ); ?>
	</label>
	<select name="kiyose_testimony_context" id="kiyose_testimony_context_field" class="widefat">
		<option value="individual" <?php selected( $context, 'individual' ); ?>>
			<?php esc_html_e( 'Individu', 'kiyose' ); ?>
		</option>
		<option value="enterprise" <?php selected( $context, 'enterprise' ); ?>>
			<?php esc_html_e( 'Entreprise', 'kiyose' ); ?>
		</option>
		<option value="structure" <?php selected( $context, 'structure' ); ?>>
			<?php esc_html_e( 'Structure / Association', 'kiyose' ); ?>
		</option>
	</select>
	<?php
}

/**
 * Save testimony context meta box data.
 *
 * @param int $post_id Post ID.
 * @return void
 */
function kiyose_save_testimony_context_meta( $post_id ) {
	// Check nonce.
	if ( ! isset( $_POST['kiyose_testimony_context_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['kiyose_testimony_context_nonce'] ) ), 'kiyose_testimony_context_nonce_action' ) ) {
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

	// Save the value.
	if ( isset( $_POST['kiyose_testimony_context'] ) ) {
		$context = sanitize_key( $_POST['kiyose_testimony_context'] );

		// Validate value is one of allowed options.
		$allowed_values = array( 'individual', 'enterprise', 'structure' );
		if ( in_array( $context, $allowed_values, true ) ) {
			update_post_meta( $post_id, 'kiyose_testimony_context', $context );
		}
	}
}
add_action( 'save_post_kiyose_testimony', 'kiyose_save_testimony_context_meta' );

/**
 * Get testimony context label.
 *
 * @param string $context_key Context key (individual, enterprise, structure).
 * @return string Translated context label or empty string for individual.
 */
function kiyose_get_testimony_context_label( $context_key ) {
	$labels = array(
		'individual' => '',
		'enterprise' => __( 'Entreprise', 'kiyose' ),
		'structure'  => __( 'Association / Structure', 'kiyose' ),
	);

	return isset( $labels[ $context_key ] ) ? $labels[ $context_key ] : '';
}
