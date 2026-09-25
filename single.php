<?php
/**
 * قالب نوشته تکی
 *
 * @package Zarrin
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article class="entry">
		<header class="entry-header">
			<h1 class="entry-title"><?php the_title(); ?></h1>
			<div class="entry-meta">
				<span><?php zarrin_icon_e( 'calendar', 15 ); ?><?php echo esc_html( zarrin_fa_digits( get_the_date() ) ); ?></span>
				<span><?php zarrin_icon_e( 'pen', 15 ); ?><?php the_author(); ?></span>
				<?php if ( comments_open() ) : ?>
					<span><?php zarrin_icon_e( 'comment', 15 ); ?><?php comments_number( 'بدون دیدگاه', '۱ دیدگاه', '%' . ' دیدگاه' ); ?></span>
				<?php endif; ?>
			</div>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<div class="entry-thumb"><?php the_post_thumbnail( 'large' ); ?></div>
		<?php endif; ?>

		<div class="entry-content">
			<?php
			the_content();
			wp_link_pages(
				array(
					'before' => '<div class="page-links">صفحات: ',
					'after'  => '</div>',
				)
			);
			?>
		</div>

		<?php the_tags( '<div class="tags">', '', '</div>' ); ?>

		<?php do_action( 'zarrin_after_post_content' ); ?>

		<nav class="post-nav" aria-label="نوشته بعدی و قبلی">
			<?php
			previous_post_link( '<span class="nav-previous">%link</span>', '<span class="nav-label">نوشته قبلی</span> %title' );
			next_post_link( '<span class="nav-next">%link</span>', '<span class="nav-label">نوشته بعدی</span> %title' );
			?>
		</nav>

		<?php
		$zarrin_author_id = get_the_author_meta( 'ID' );
		if ( get_the_author_meta( 'description' ) ) :
			?>
			<div class="author-box">
				<?php echo get_avatar( $zarrin_author_id, 70 ); ?>
				<div>
					<div class="author-name"><?php the_author(); ?></div>
					<p class="author-desc"><?php echo esc_html( get_the_author_meta( 'description' ) ); ?></p>
				</div>
			</div>
			<?php
		endif;

		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
		?>
	</article>
	<?php
endwhile;
?>

<?php
get_footer();
