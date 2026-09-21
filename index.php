<?php
/**
 * قالب اصلی — لیست نوشته‌ها
 *
 * @package Zarrin
 */

get_header();
?>

<div class="page-head">
	<div class="container">
		<h1 class="page-title">وبلاگ طلا و جواهر</h1>
		<p class="page-desc">مقالات، راهنمای خرید و آخرین اخبار</p>
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
					<p>هنوز نوشته‌ای منتشر نشده است.</p>
					<?php get_search_form(); ?>
				</div>
			<?php endif; ?>
		</main>
		<?php get_sidebar(); ?>
	</div>
</div>

<?php
get_footer();
