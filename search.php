<?php
/**
 * قالب نتایج جستجو
 *
 * @package Zarrin
 */

get_header();
?>

<div class="page-head">
	<div class="container">
		<h1 class="page-title">نتایج جستجو</h1>
		<p class="page-desc">نتایج برای عبارت: «<?php echo esc_html( get_search_query() ); ?>»</p>
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
						if ( 'product' === get_post_type() && zarrin_is_woo() ) {
							// نمایش خلاصه محصول در نتایج جستجو.
							$zarrin_wc_product = wc_get_product( get_the_ID() );
							?>
							<article class="post-card reveal in-view">
								<a class="post-thumb" href="<?php the_permalink(); ?>">
									<?php
									if ( has_post_thumbnail() ) {
										the_post_thumbnail( 'zarrin-post' );
									} else {
										echo '<span class="thumb-fallback">' . zarrin_icon( 'diamond', 40 ) . '</span>'; // phpcs:ignore
									}
									?>
								</a>
								<div class="post-body">
									<span class="product-cat">محصول</span>
									<h3 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
									<span class="product-price"><?php echo wp_kses_post( $zarrin_wc_product ? $zarrin_wc_product->get_price_html() : '' ); ?></span>
									<a class="read-more" href="<?php the_permalink(); ?>">مشاهده محصول <?php zarrin_icon_e( 'chevron', 15 ); ?></a>
								</div>
							</article>
							<?php
						} else {
							zarrin_post_card();
						}
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
					<h2>چیزی پیدا نشد</h2>
					<p>متأسفانه نتیجه‌ای برای عبارت جستجوی شما یافت نشد. عبارت دیگری را امتحان کنید.</p>
					<?php get_search_form(); ?>
				</div>
			<?php endif; ?>
		</main>
		<?php get_sidebar(); ?>
	</div>
</div>

<?php
get_footer();
