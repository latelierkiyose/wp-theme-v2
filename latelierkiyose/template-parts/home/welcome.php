<?php
/**
 * Home welcome section.
 *
 * @package Kiyose
 * @since   0.2.7
 *
 * @param array $args Home page data.
 */

$kiyose_welcome_title    = $args['welcome_title'] ?? '';
$kiyose_welcome_subtitle = $args['welcome_subtitle'] ?? '';
$kiyose_welcome_text     = $args['welcome_text'] ?? '';
$kiyose_welcome_keywords = $args['welcome_keywords'] ?? array();
$kiyose_welcome_slogan   = $args['welcome_slogan'] ?? '';
?>

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
		<h1 class="welcome-block__title"><?php echo kiyose_fr_nbsp( esc_html( $kiyose_welcome_title ) ); ?></h1>
		<?php if ( ! empty( $kiyose_welcome_subtitle ) ) : ?>
			<p class="welcome-block__subtitle"><?php echo kiyose_fr_nbsp( esc_html( $kiyose_welcome_subtitle ) ); ?></p>
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
			<p class="welcome-block__slogan home-emphase"><?php echo kiyose_fr_nbsp( esc_html( $kiyose_welcome_slogan ) ); ?></p>
		<?php endif; ?>
		<div class="welcome-block__cta-wrapper" style="margin-top: var(--kiyose-spacing-xl);">
			<a href="<?php echo esc_url( KIYOSE_DISCOVERY_CALL_URL ); ?>" class="welcome-block__cta">
				<?php esc_html_e( 'Réserver votre appel découverte', 'kiyose' ); ?>
			</a>
		</div>
	</div>
</section>
