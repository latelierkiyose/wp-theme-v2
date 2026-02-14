<?php
/**
 * L'Atelier Kiyose - Search form template.
 *
 * @package Kiyose
 * @since   0.1.0
 */

?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="search-field" class="sr-only"><?php esc_html_e( 'Rechercher', 'kiyose' ); ?></label>
	<input
		type="search"
		id="search-field"
		class="search-form__input"
		placeholder="<?php esc_attr_e( 'Rechercher...', 'kiyose' ); ?>"
		value="<?php echo esc_attr( get_search_query() ); ?>"
		name="s"
	>
	<button type="submit" class="search-form__submit button button--primary">
		<?php esc_html_e( 'Rechercher', 'kiyose' ); ?>
	</button>
</form>
