<?php
/**
 * قالب آرشیو (دسته‌بندی، برچسب، نویسنده، تاریخ)
 *
 * @package Zarrin
 */

get_header();
?>

<div class="page-head">
	<div class="container">
		<?php the_archive_title( '<h1 class="page-title">', '</h1>' ); ?>
		<?php the_archive_description( '<p class="page-desc">', '</p>' ); ?>
	</div>
</div>

<div class="container">
	<div class="layout <?php echo is_active_sidebar( 'sidebar-1' ) ? 'has-sidebar' : ''; ?>">
		<main class="site-main" id="content">
			<?php if ( have_posts() ) : ?>
				<div class="posts-grid">
					<?php
					while ( have_posts() ) :
						the_post();
						zarrin_post_card();
					endwhile;
					?>
				</div>
				<?php
				the_posts_pagination(
					array(
						'prev_text' => 'قبلی',
						'next_text' => 'بعدی',
					)
				);
				?>
			<?php else : ?>
				<div class="no-results">
					<h2>مطلبی پیدا نشد</h2>
					<p>در این بخش هنوز نوشته‌ای منتشر نشده است.</p>
					<?php get_search_form(); ?>
				</div>
			<?php endif; ?>
		</main>
		<?php get_sidebar(); ?>
	</div>
</div>

<?php
get_footer();
