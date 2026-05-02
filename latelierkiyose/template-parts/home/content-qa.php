<?php
/**
 * Home questions and answers section.
 *
 * @package Kiyose
 * @since   0.2.7
 *
 * @param array $args Home page data.
 */

$kiyose_content1_qa     = $args['content1_qa'] ?? array();
$kiyose_content1_slogan = $args['content1_slogan'] ?? '';
?>

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
						<p class="home-content1__question"><?php echo kiyose_fr_nbsp( esc_html( $kiyose_qa_item['question'] ) ); ?></p>
					<?php endif; ?>
					<?php if ( ! empty( $kiyose_qa_item['answer'] ) ) : ?>
						<p class="home-content1__answer"><?php echo esc_html( $kiyose_qa_item['answer'] ); ?></p>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php if ( ! empty( $kiyose_content1_slogan ) ) : ?>
			<p class="home-content1__slogan home-emphase"><?php echo kiyose_fr_nbsp( esc_html( $kiyose_content1_slogan ) ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php endif; ?>
