<?php
/**
 * صفحه اصلی — صفحه ویژه قالب زرین
 *
 * @package Zarrin
 */

get_header();

$zarrin_hero_img   = zarrin_get( 'zarrin_hero_img', get_template_directory_uri() . '/assets/img/hero.jpg' );
$zarrin_btn1_text  = zarrin_get( 'zarrin_hero_btn1_text', 'مشاهده محصولات' );
$zarrin_btn1_url   = zarrin_get( 'zarrin_hero_btn1_url' );
$zarrin_btn2_text  = zarrin_get( 'zarrin_hero_btn2_text', 'تماس با ما' );
$zarrin_btn2_url   = zarrin_get( 'zarrin_hero_btn2_url' );
$zarrin_phone_link = preg_replace( '/[^0-9+]/', '', zarrin_get( 'zarrin_phone', '02133445566' ) );

/* لینک پیش‌فرض دکمه اول: فروشگاه یا لنگر محصولات */
if ( ! $zarrin_btn1_url ) {
	$zarrin_btn1_url = ( zarrin_is_woo() && get_option( 'woocommerce_shop_page_id' ) )
		? get_permalink( get_option( 'woocommerce_shop_page_id' ) )
		: home_url( '/#products' );
}
if ( ! $zarrin_btn2_url ) {
	$zarrin_btn2_url = 'tel:' . $zarrin_phone_link;
}
?>

<!-- ================= هیرو ================= -->
<section class="hero">
	<div class="container hero-inner">
		<div class="hero-content reveal in-view">
			<span class="hero-eyebrow"><?php zarrin_icon_e( 'diamond', 15 ); ?><?php echo esc_html( zarrin_get( 'zarrin_hero_eyebrow', 'طلا و جواهر زرین — از سال ۱۳۷۵' ) ); ?></span>
			<h1 class="hero-title"><?php echo wp_kses_post( zarrin_get( 'zarrin_hero_title', 'درخشش طلای اصیل، زیبایی ماندگار شما' ) ); ?></h1>
			<p class="hero-sub"><?php echo esc_html( zarrin_get( 'zarrin_hero_sub', 'مجموعه‌ای نفیس از طلای ۱۸ و ۲۴ عیار با ضمانت اصالت، قیمت روز و امکان معاوضه.' ) ); ?></p>
			<div class="hero-actions">
				<a class="btn btn-gold" href="<?php echo esc_url( $zarrin_btn1_url ); ?>"><?php echo esc_html( $zarrin_btn1_text ); ?></a>
				<a class="btn btn-ghost" href="<?php echo esc_url( $zarrin_btn2_url ); ?>"><?php echo esc_html( $zarrin_btn2_text ); ?></a>
			</div>
		</div>
		<div class="hero-media reveal in-view">
			<div class="frame">
				<img src="<?php echo esc_url( $zarrin_hero_img ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" loading="eager">
			</div>
			<?php $zarrin_hero_p18 = zarrin_get_prices(); ?>
			<div class="float-card" data-live-item="p18">
				<span class="fc-label"><?php zarrin_icon_e( 'coin', 15 ); ?>قیمت امروز گرم طلای ۱۸<?php if ( $zarrin_hero_p18['_live'] ) : ?><span class="live-dot" title="لحظه‌ای"></span><?php endif; ?></span>
				<span class="fc-value live-value"><?php echo esc_html( zarrin_money( $zarrin_hero_p18['p18']['value'] ) ); ?> <small>تومان</small></span>
				<span class="fc-badge">به‌روزرسانی: <span class="live-updated"><?php echo esc_html( $zarrin_hero_p18['_updated'] ); ?></span></span>
				<span class="live-change"><?php echo zarrin_change_badge( $zarrin_hero_p18['p18']['change'] ); // phpcs:ignore ?></span>
			</div>
		</div>
	</div>
</section>

<!-- ================= نوار قیمت لحظه‌ای ================= -->
<section class="prices-strip" aria-label="قیمت لحظه‌ای طلا و سکه">
	<?php $zarrin_prices_data = zarrin_get_prices(); ?>
	<div class="container">
		<div class="prices-grid">
			<?php
			$zarrin_prices_map = array(
				'p18'     => array( 'label' => 'طلای ۱۸ عیار', 'unit' => 'هر گرم', 'icon' => 'coin' ),
				'p24'     => array( 'label' => 'طلای ۲۴ عیار', 'unit' => 'هر گرم', 'icon' => 'coin' ),
				'coin'    => array( 'label' => 'سکه امامی', 'unit' => 'هر عدد', 'icon' => 'award' ),
				'mesghal' => array( 'label' => 'مثقال طلا', 'unit' => 'هر مثقال', 'icon' => 'diamond' ),
			);
			foreach ( $zarrin_prices_map as $zarrin_pkey => $zarrin_price ) :
				$zarrin_pitem = $zarrin_prices_data[ $zarrin_pkey ];
				?>
				<div class="price-card reveal" data-live-item="<?php echo esc_attr( $zarrin_pkey ); ?>">
					<span class="p-icon"><?php zarrin_icon_e( $zarrin_price['icon'], 22 ); ?></span>
					<span>
						<span class="p-label"><?php echo esc_html( $zarrin_price['label'] ); ?><?php if ( $zarrin_prices_data['_live'] ) : ?><span class="live-dot" title="لحظه‌ای"></span><?php endif; ?></span>
						<span class="p-value live-value" data-fa-num><?php echo esc_html( zarrin_money( $zarrin_pitem['value'] ) ); ?></span>
						<span class="p-unit">تومان | <?php echo esc_html( $zarrin_price['unit'] ); ?></span>
						<span class="live-change"><?php echo zarrin_change_badge( $zarrin_pitem['change'] ); // phpcs:ignore ?></span>
					</span>
				</div>
				<?php
			endforeach;
			?>
		</div>
		<p class="prices-note">* قیمت‌ها به‌صورت خودکار از بازار طلا به‌روزرسانی می‌شوند — آخرین به‌روزرسانی: <span class="live-updated"><?php echo esc_html( $zarrin_prices_data['_updated'] ); ?></span> — برای خرید نهایی با فروشگاه هماهنگ کنید.</p>
	</div>
</section>

<!-- ================= ویژگی‌ها / خدمات ================= -->
<section class="features section">
	<div class="container">
		<div class="section-head reveal">
			<span class="eyebrow">چرا زرین؟</span>
			<h2 class="section-title">مزیت‌های خرید از ما</h2>
			<p class="section-sub">با خیال راحت طلا بخرید؛ اصالت و کیفیت، تعهد ماست.</p>
			<div class="ornament"><?php zarrin_icon_e( 'diamond', 16 ); ?></div>
		</div>
		<div class="features-grid">
			<?php
			$zarrin_features = array(
				array( 'icon' => 'shield', 'title' => 'ضمانت اصالت و عیار', 'text' => 'تمام محصولات دارای مهر معتبر و ضمانت‌نامه کتبی اصالت هستند.' ),
				array( 'icon' => 'coin', 'title' => 'قیمت لحظه‌ای روز', 'text' => 'قیمت‌گذاری شفاف بر اساس نرخ روز بازار طلا و ارقام واقعی.' ),
				array( 'icon' => 'exchange', 'title' => 'معاوضه و بازخرید', 'text' => 'امکان معاوضه طلا با طلا و بازخرید تضمینی محصولات فروشگاه.' ),
				array( 'icon' => 'truck', 'title' => 'ارسال بیمه‌شده', 'text' => 'ارسال سریع و کاملاً بیمه‌شده سفارش‌ها به سراسر کشور.' ),
			);
			foreach ( $zarrin_features as $zarrin_feature ) :
				?>
				<div class="feature reveal">
					<span class="feature-icon"><?php zarrin_icon_e( $zarrin_feature['icon'], 26 ); ?></span>
					<h3><?php echo esc_html( $zarrin_feature['title'] ); ?></h3>
					<p><?php echo esc_html( $zarrin_feature['text'] ); ?></p>
				</div>
				<?php
			endforeach;
			?>
		</div>
	</div>
</section>

<?php
/* ================= محصولات ویژه (نیازمند ووکامرس) ================= */
$zarrin_show_products = false;
if ( zarrin_is_woo() ) {
	$zarrin_featured_ids = wc_get_featured_product_ids();
	$zarrin_prod_args    = array(
		'post_type'      => 'product',
		'posts_per_page' => 8,
		'post_status'    => 'publish',
	);
	if ( ! empty( $zarrin_featured_ids ) ) {
		$zarrin_prod_args['post__in'] = $zarrin_featured_ids;
	}
	$zarrin_products_q = new WP_Query( $zarrin_prod_args );
	$zarrin_show_products = $zarrin_products_q->have_posts();
}

if ( $zarrin_show_products ) :
	?>
	<section class="section products-section" id="products">
		<div class="container">
			<div class="section-head reveal">
				<span class="eyebrow">فروشگاه زرین</span>
				<h2 class="section-title">محصولات ویژه</h2>
				<p class="section-sub">منتخبی از جدیدترین و پرفروش‌ترین طرح‌های طلا و جواهر</p>
				<div class="ornament"><?php zarrin_icon_e( 'diamond', 16 ); ?></div>
			</div>
			<div class="products-grid">
				<?php
				while ( $zarrin_products_q->have_posts() ) :
					$zarrin_products_q->the_post();
					$zarrin_wc_product = wc_get_product( get_the_ID() );
					?>
					<article class="product-card reveal">
						<a class="product-thumb" href="<?php the_permalink(); ?>">
							<?php if ( $zarrin_wc_product && $zarrin_wc_product->is_on_sale() ) : ?>
								<span class="product-badge">تخفیف ویژه</span>
							<?php elseif ( $zarrin_wc_product && $zarrin_wc_product->is_featured() ) : ?>
								<span class="product-badge off">ویژه</span>
							<?php endif; ?>
							<?php if ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'zarrin-product' ); ?>
							<?php else : ?>
								<span class="thumb-fallback"><?php zarrin_icon_e( 'diamond', 44 ); ?></span>
							<?php endif; ?>
						</a>
						<div class="product-body">
							<?php if ( function_exists( 'wc_get_product_category_list' ) ) : ?>
								<span class="product-cat"><?php echo wp_kses_post( $zarrin_wc_product ? $zarrin_wc_product->get_categories( '، ' ) : '' ); ?></span>
							<?php endif; ?>
							<h3 class="product-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<span class="product-price"><?php echo wp_kses_post( $zarrin_wc_product ? $zarrin_wc_product->get_price_html() : '' ); ?></span>
							<a class="product-add" href="<?php echo esc_url( $zarrin_wc_product ? $zarrin_wc_product->add_to_cart_url() : get_the_permalink() ); ?>">
								<?php zarrin_icon_e( 'cart', 16 ); ?>افزودن به سبد خرید
							</a>
						</div>
					</article>
					<?php
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</div>
	</section>
	<?php
endif;
?>

<!-- ================= درباره ما ================= -->
<section class="about" id="about">
	<div class="container about-inner">
		<div class="about-media reveal">
			<img src="<?php echo esc_url( zarrin_get( 'zarrin_about_img', get_template_directory_uri() . '/assets/img/about.jpg' ) ); ?>" alt="<?php echo esc_attr( zarrin_get( 'zarrin_about_title', 'درباره ما' ) ); ?>" loading="lazy">
			<div class="exp-badge">
				<span class="num"><?php echo esc_html( zarrin_get( 'zarrin_stat_years', '۲۵' ) ); ?></span>
				<span class="lbl"><?php echo esc_html( zarrin_get( 'zarrin_stat_years_lbl', 'سال تجربه' ) ); ?></span>
			</div>
		</div>
		<div class="about-content reveal">
			<span class="eyebrow">داستان زرین</span>
			<h2 class="section-title"><?php echo esc_html( zarrin_get( 'zarrin_about_title', 'درباره طلافروشی زرین' ) ); ?></h2>
			<p class="about-text"><?php echo wp_kses_post( zarrin_get( 'zarrin_about_text', 'زرین با بیش از دو دهه تجربه در عرضه طلا و جواهر، مجموعه‌ای منتخب از زیباترین طرح‌های طلای ۱۸ و ۲۴ عیار را با ضمانت کتبی اصالت و عیار ارائه می‌کند.' ) ); ?></p>
			<ul class="about-points">
				<li><?php zarrin_icon_e( 'check', 18 ); ?>شناسنامه و مهر معتبر برای تمام محصولات</li>
				<li><?php zarrin_icon_e( 'check', 18 ); ?>ساخت سفارشی طرح دلخواه شما توسط استادکاران مجرب</li>
				<li><?php zarrin_icon_e( 'check', 18 ); ?>پشتیبانی کامل پس از خرید و خدمات نگهداری طلا</li>
			</ul>
			<div class="stats-row">
				<div class="stat">
					<span class="num"><?php echo esc_html( zarrin_get( 'zarrin_stat_years', '۲۵' ) ); ?></span>
					<span class="lbl"><?php echo esc_html( zarrin_get( 'zarrin_stat_years_lbl', 'سال تجربه' ) ); ?></span>
				</div>
				<div class="stat">
					<span class="num"><?php echo esc_html( zarrin_get( 'zarrin_stat_customers', '۱۲هزار+' ) ); ?></span>
					<span class="lbl"><?php echo esc_html( zarrin_get( 'zarrin_stat_customers_lbl', 'مشتری وفادار' ) ); ?></span>
				</div>
				<div class="stat">
					<span class="num"><?php echo esc_html( zarrin_get( 'zarrin_stat_designs', '۸۰۰+' ) ); ?></span>
					<span class="lbl"><?php echo esc_html( zarrin_get( 'zarrin_stat_designs_lbl', 'مدل اختصاصی' ) ); ?></span>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- ================= بنر تماس ================= -->
<section class="cta">
	<div class="container">
		<div class="cta-banner reveal">
			<h2><?php echo esc_html( zarrin_get( 'zarrin_cta_title', 'خرید طلا را به اعتماد بسپارید' ) ); ?></h2>
			<p><?php echo esc_html( zarrin_get( 'zarrin_cta_sub', 'کارشناسان ما پاسخگوی سوالات شما درباره قیمت روز، معاوضه و ساخت سفارشی هستند.' ) ); ?></p>
			<div class="cta-actions">
				<a class="btn btn-dark cta-phone" href="tel:<?php echo esc_attr( $zarrin_phone_link ); ?>"><?php zarrin_icon_e( 'phone', 20 ); ?><?php echo esc_html( zarrin_get( 'zarrin_phone', '۰۲۱-۳۳۴۴۵۵۶۶' ) ); ?></a>
				<?php if ( zarrin_is_woo() && get_option( 'woocommerce_shop_page_id' ) ) : ?>
					<a class="btn btn-dark" href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_shop_page_id' ) ) ); ?>">مشاهده فروشگاه</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>

<!-- ================= آخرین مطالب وبلاگ ================= -->
<?php
$zarrin_blog_q = new WP_Query(
	array(
		'post_type'           => 'post',
		'posts_per_page'      => 3,
		'ignore_sticky_posts' => true,
	)
);
if ( $zarrin_blog_q->have_posts() ) :
	?>
	<section class="section blog-section">
		<div class="container">
			<div class="section-head reveal">
				<span class="eyebrow">مجله طلا</span>
				<h2 class="section-title">راهنمای خرید و دانش طلا</h2>
				<p class="section-sub">آموزش‌ها و مقالات کاربردی برای خرید آگاهانه</p>
				<div class="ornament"><?php zarrin_icon_e( 'diamond', 16 ); ?></div>
			</div>
			<div class="posts-grid">
				<?php
				while ( $zarrin_blog_q->have_posts() ) :
					$zarrin_blog_q->the_post();
					zarrin_post_card();
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</div>
	</section>
	<?php
endif;
?>

<?php
get_footer();
