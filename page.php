<?php
/**
 * قالب برگه
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
	</article>
	<?php
	if ( comments_open() || get_comments_number() ) {
		comments_template();
	}
endwhile;
?>

<?php
get_footer();
