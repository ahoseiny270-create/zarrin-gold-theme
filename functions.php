<?php
/**
 * توابع قالب زرین — قالب فروشگاه طلا
 *
 * @package Zarrin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ZARRIN_VERSION', '1.0.0' );

/* =========================================================
 * ۱) راه‌اندازی قالب
 * ======================================================= */
function zarrin_setup() {

	load_theme_textdomain( 'zarrin', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 240,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	// پشتیبانی ووکامرس.
	add_theme_support(
		'woocommerce',
		array(
			'thumbnail_image_width' => 450,
			'single_image_width'    => 720,
			'product_grid'          => array(
				'default_rows'    => 2,
				'min_rows'        => 1,
				'default_columns' => 4,
				'min_columns'     => 2,
				'max_columns'     => 5,
			),
		)
	);
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	register_nav_menus(
		array(
			'primary' => 'منوی اصلی',
			'footer'  => 'منوی فوتر',
		)
	);

	add_image_size( 'zarrin-product', 480, 480, true );
	add_image_size( 'zarrin-post', 680, 430, true );
}
add_action( 'after_setup_theme', 'zarrin_setup' );

/* =========================================================
 * ۲) محتوا عرض پست و خلاصه
 * ======================================================= */
function zarrin_excerpt_length( $length ) {
	return 26;
}
add_filter( 'excerpt_length', 'zarrin_excerpt_length' );

function zarrin_excerpt_more( $more ) {
	return ' …';
}
add_filter( 'excerpt_more', 'zarrin_excerpt_more' );

/* =========================================================
 * ۳) ویجت‌ها
 * ======================================================= */
function zarrin_widgets_init() {

	register_sidebar(
		array(
			'name'          => 'ستون کناری',
			'id'            => 'sidebar-1',
			'description'   => 'ستون کناری در صفحه‌های وبلاگ و آرشیو',
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'zarrin_widgets_init' );

/* =========================================================
 * ۴) استایل و اسکریپت
 * ======================================================= */
function zarrin_assets() {

	wp_enqueue_style( 'zarrin-style', get_stylesheet_uri(), array(), ZARRIN_VERSION );

	// فونت وزیرمتن — لوکال (بدون نیاز به اینترنت/CDN).
	wp_add_inline_style(
		'zarrin-style',
		"@font-face{font-family:Vazirmatn;src:url('" . esc_url( get_template_directory_uri() . '/assets/fonts/Vazirmatn-var.woff2' ) . "') format('woff2-variations');font-weight:100 900;font-display:swap;font-style:normal;}"
	);

	wp_enqueue_script( 'zarrin-main', get_template_directory_uri() . '/assets/js/main.js', array(), ZARRIN_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'zarrin_assets' );

/* =========================================================
 * ۵) توابع کمکی
 * ======================================================= */

/** آیا ووکامرس فعال است؟ */
function zarrin_is_woo() {
	return class_exists( 'WooCommerce' );
}

/** تبدیل ارقام انگلیسی به فارسی */
function zarrin_fa_digits( $value ) {
	$en = array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' );
	$fa = array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' );
	return str_replace( $en, $fa, (string) $value );
}

/** قالب‌بندی مبلغ با جداکننده هزارگان و ارقام فارسی */
function zarrin_money( $value ) {
	if ( is_numeric( $value ) ) {
		return zarrin_fa_digits( str_replace( ',', '٬', number_format( (float) $value ) ) );
	}
	return zarrin_fa_digits( $value );
}

/** خواندن تنظیم سفارشی‌سازی */
function zarrin_get( $key, $default = '' ) {
	return get_theme_mod( $key, $default );
}

/** آیکون‌های SVG قالب */
function zarrin_icon( $name, $size = 20 ) {
	$common = 'width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"';

	$icons = array(
		'phone'    => '<svg ' . $common . '><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .7 2.9a2 2 0 0 1-.5 2.1L8.1 10a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.5c.9.3 1.9.6 2.9.7a2 2 0 0 1 1.6 2z"/></svg>',
		'clock'    => '<svg ' . $common . '><circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15.5 13.5"/></svg>',
		'pin'      => '<svg ' . $common . '><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/></svg>',
		'search'   => '<svg ' . $common . '><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.3" y2="16.3"/></svg>',
		'cart'     => '<svg ' . $common . '><circle cx="9" cy="20" r="1.6"/><circle cx="17" cy="20" r="1.6"/><path d="M3 3h2l2.6 12.4a1.5 1.5 0 0 0 1.5 1.1h7.6a1.5 1.5 0 0 0 1.5-1.2L20.5 8H6"/></svg>',
		'diamond'  => '<svg ' . $common . '><path d="M6 3h12l4 6-10 13L2 9z"/><path d="M11 3 8 9l4 13 4-13-3-6"/><path d="M2 9h20"/></svg>',
		'shield'   => '<svg ' . $common . '><path d="M12 22s8-3.6 8-10V5l-8-3-8 3v7c0 6.4 8 10 8 10z"/><path d="m9 11.5 2 2 4-4.5"/></svg>',
		'truck'    => '<svg ' . $common . '><rect x="1" y="5" width="13" height="10" rx="1"/><path d="M14 8h4l3 3v4h-7z"/><circle cx="6" cy="17.5" r="2"/><circle cx="17" cy="17.5" r="2"/></svg>',
		'exchange' => '<svg ' . $common . '><path d="m17 2 4 4-4 4"/><path d="M3 6h18"/><path d="m7 22-4-4 4-4"/><path d="M21 18H3"/></svg>',
		'award'    => '<svg ' . $common . '><circle cx="12" cy="9" r="6"/><path d="M8.5 14 7 22l5-3 5 3-1.5-8"/></svg>',
		'coin'     => '<svg ' . $common . '><circle cx="12" cy="12" r="9"/><path d="M12 7v10"/><path d="M14.8 9.2h-4.3a1.7 1.7 0 0 0 0 3.4h3a1.7 1.7 0 0 1 0 3.4H9.2"/></svg>',
		'mail'     => '<svg ' . $common . '><rect x="2" y="4" width="20" height="16" rx="3"/><path d="m2 7 10 6L22 7"/></svg>',
		'insta'    => '<svg ' . $common . '><rect x="2.5" y="2.5" width="19" height="19" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.3" cy="6.7" r=".8" fill="currentColor" stroke="none"/></svg>',
		'telegram' => '<svg ' . $common . '><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>',
		'whats'    => '<svg ' . $common . '><path d="M12 3a9 9 0 0 0-7.8 13.5L3 21l4.6-1.2A9 9 0 1 0 12 3z"/><path d="M9 9.3c.4 2.7 3 5.3 5.7 5.7l1.1-1.6-2.2-1-.8.8c-.9-.5-1.5-1.1-2-2l.8-.8-1-2.2z" fill="currentColor" stroke="none"/></svg>',
		'chevron'  => '<svg ' . $common . '><polyline points="14 6 8 12 14 18"/></svg>',
		'up'       => '<svg ' . $common . '><polyline points="6 14 12 8 18 14"/></svg>',
		'users'    => '<svg ' . $common . '><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.9"/><path d="M16 3.1a4 4 0 0 1 0 7.8"/></svg>',
		'check'    => '<svg ' . $common . '><circle cx="12" cy="12" r="9"/><path d="m8.5 12 2.5 2.5 4.5-5"/></svg>',
		'comment'  => '<svg ' . $common . '><path d="M21 11.5a8.4 8.4 0 0 1-8.5 8.3 8.9 8.9 0 0 1-3.7-.8L3 21l1.9-5.5a8 8 0 0 1-1-3.9A8.4 8.4 0 0 1 12.5 3.2 8.4 8.4 0 0 1 21 11.5z"/></svg>',
		'calendar' => '<svg ' . $common . '><rect x="3" y="4" width="18" height="18" rx="3"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
		'pen'      => '<svg ' . $common . '><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>',
	);

	return isset( $icons[ $name ] ) ? $icons[ $name ] : '';
}

/** چاپ آیکون */
function zarrin_icon_e( $name, $size = 20 ) {
	echo zarrin_icon( $name, $size ); // phpcs:ignore WordPress.Security.EscapeOutput
}

/** منوی پیش‌فرض وقتی هنوز منویی ساخته نشده */
function zarrin_menu_fallback() {
	echo '<ul class="nav-list">';
	echo '<li class="current-menu-item"><a href="' . esc_url( home_url( '/' ) ) . '">خانه</a></li>';
	if ( zarrin_is_woo() && get_option( 'woocommerce_shop_page_id' ) ) {
		echo '<li><a href="' . esc_url( get_permalink( get_option( 'woocommerce_shop_page_id' ) ) ) . '">فروشگاه</a></li>';
	}
	wp_list_pages(
		array(
			'title_li' => '',
			'depth'    => 1,
		)
	);
	echo '</ul>';
}

/** کارت نوشته (وبلاگ/آرشیو/جستجو) */
function zarrin_post_card() {
	?>
	<article class="post-card reveal">
		<a class="post-thumb" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
			<?php
			if ( has_post_thumbnail() ) {
				the_post_thumbnail( 'zarrin-post' );
			} else {
				echo '<span class="thumb-fallback">' . zarrin_icon( 'diamond', 40 ) . '</span>'; // phpcs:ignore
			}
			?>
		</a>
		<div class="post-body">
			<div class="post-meta">
				<span><?php zarrin_icon_e( 'calendar', 14 ); ?><?php echo esc_html( zarrin_fa_digits( get_the_date() ) ); ?></span>
				<span><?php zarrin_icon_e( 'pen', 14 ); ?><?php the_author(); ?></span>
			</div>
			<h3 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
			<p class="post-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18, ' …' ) ); ?></p>
			<a class="read-more" href="<?php the_permalink(); ?>">ادامه مطلب <?php zarrin_icon_e( 'chevron', 15 ); ?></a>
		</div>
	</article>
	<?php
}

/* =========================================================
 * ۶) ووکامرس — اضافات
 * ======================================================= */

/** شمارنده سبد خرید با آجاکس به‌روز شود */
function zarrin_cart_count_fragment( $fragments ) {
	if ( function_exists( 'WC' ) && WC()->cart ) {
		$fragments['.cart-count'] = '<span class="cart-count">' . esc_html( zarrin_fa_digits( WC()->cart->get_cart_contents_count() ) ) . '</span>';
	}
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'zarrin_cart_count_fragment' );

/** تعداد ستون‌های فروشگاه */
add_filter(
	'loop_shop_columns',
	function () {
		return 4;
	}
);

/* =========================================================
 * ۷) تنظیمات سفارشی‌سازی (نمایش ← سفارشی‌سازی)
 * ======================================================= */
function zarrin_customize_register( $wp_customize ) {

	$wp_customize->add_panel(
		'zarrin_panel',
		array(
			'title'    => 'تنظیمات قالب زرین',
			'priority' => 5,
		)
	);

	/* --- تابع کمکی برای فیلدهای متنی --- */
	function zarrin_add_field( $wp_customize, $id, $label, $section, $default = '', $type = 'text', $sanitize = 'sanitize_text_field' ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $default,
				'sanitize_callback' => $sanitize,
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'   => $label,
				'section' => $section,
				'type'    => $type,
			)
		);
	}

	/* --- تابع کمکی برای فیلد تصویر --- */
	function zarrin_add_image( $wp_customize, $id, $label, $section ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => '',
				'sanitize_callback' => 'esc_url_raw',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				$id,
				array(
					'label'   => $label,
					'section' => $section,
				)
			)
		);
	}

	/* ============ بخش: هیرو ============ */
	$wp_customize->add_section(
		'zarrin_hero',
		array(
			'title' => 'بنر اصلی (هیرو)',
			'panel' => 'zarrin_panel',
		)
	);
	zarrin_add_field( $wp_customize, 'zarrin_hero_eyebrow', 'متن کوچک بالای عنوان', 'zarrin_hero', 'طلا و جواهر زرین — از سال ۱۳۷۵' );
	zarrin_add_field( $wp_customize, 'zarrin_hero_title', 'عنوان اصلی بنر', 'zarrin_hero', 'درخشش طلای اصیل، زیبایی ماندگار شما' );
	zarrin_add_field( $wp_customize, 'zarrin_hero_sub', 'توضیح کوتاه بنر', 'zarrin_hero', 'مجموعه‌ای نفیس از طلای ۱۸ و ۲۴ عیار با ضمانت اصالت، قیمت روز و امکان معاوضه.', 'textarea' );
	zarrin_add_image( $wp_customize, 'zarrin_hero_img', 'تصویر بنر اصلی', 'zarrin_hero' );
	zarrin_add_field( $wp_customize, 'zarrin_hero_btn1_text', 'متن دکمه اول', 'zarrin_hero', 'مشاهده محصولات' );
	zarrin_add_field( $wp_customize, 'zarrin_hero_btn1_url', 'لینک دکمه اول', 'zarrin_hero', '' );
	zarrin_add_field( $wp_customize, 'zarrin_hero_btn2_text', 'متن دکمه دوم', 'zarrin_hero', 'تماس با ما' );
	zarrin_add_field( $wp_customize, 'zarrin_hero_btn2_url', 'لینک دکمه دوم', 'zarrin_hero', '' );

	/* ============ بخش: اطلاعات تماس ============ */
	$wp_customize->add_section(
		'zarrin_contact',
		array(
			'title' => 'اطلاعات تماس',
			'panel' => 'zarrin_panel',
		)
	);
	zarrin_add_field( $wp_customize, 'zarrin_phone', 'شماره تلفن فروشگاه', 'zarrin_contact', '۰۲۱-۳۳۴۴۵۵۶۶' );
	zarrin_add_field( $wp_customize, 'zarrin_mobile', 'شماره موبایل / واتساپ', 'zarrin_contact', '۰۹۱۲۳۴۵۶۷۸۹' );
	zarrin_add_field( $wp_customize, 'zarrin_email', 'ایمیل', 'zarrin_contact', 'info@example.com' );
	zarrin_add_field( $wp_customize, 'zarrin_address', 'آدرس', 'zarrin_contact', 'تهران، بازار بزرگ، سرای امیر، پلاک ۱۲', 'textarea' );
	zarrin_add_field( $wp_customize, 'zarrin_hours', 'ساعات کاری', 'zarrin_contact', 'شنبه تا پنجشنبه — ۱۰ صبح تا ۸ شب' );

	/* ============ بخش: شبکه‌های اجتماعی ============ */
	$wp_customize->add_section(
		'zarrin_social',
		array(
			'title' => 'شبکه‌های اجتماعی',
			'panel' => 'zarrin_panel',
		)
	);
	zarrin_add_field( $wp_customize, 'zarrin_instagram', 'آدرس اینستاگرام', 'zarrin_social', '', 'url', 'esc_url_raw' );
	zarrin_add_field( $wp_customize, 'zarrin_telegram', 'آدرس تلگرام', 'zarrin_social', '', 'url', 'esc_url_raw' );
	zarrin_add_field( $wp_customize, 'zarrin_whatsapp', 'آدرس واتساپ', 'zarrin_social', '', 'url', 'esc_url_raw' );

	/* ============ بخش: قیمت طلا ============ */
	$wp_customize->add_section(
		'zarrin_prices',
		array(
			'title'       => 'قیمت طلا و سکه',
			'panel'       => 'zarrin_panel',
			'description' => 'قیمت‌ها را عددی وارد کنید (مثلاً 4250000) تا با جداکننده هزارگان نمایش داده شوند. این قیمت‌ها دستی به‌روزرسانی می‌شوند.',
		)
	);
	zarrin_add_field( $wp_customize, 'zarrin_price_18', 'قیمت گرم طلای ۱۸ عیار (تومان)', 'zarrin_prices', '4250000' );
	zarrin_add_field( $wp_customize, 'zarrin_price_24', 'قیمت گرم طلای ۲۴ عیار (تومان)', 'zarrin_prices', '5660000' );
	zarrin_add_field( $wp_customize, 'zarrin_price_coin', 'قیمت سکه امامی (تومان)', 'zarrin_prices', '52000000' );
	zarrin_add_field( $wp_customize, 'zarrin_price_mesghal', 'قیمت مثقال طلا (تومان)', 'zarrin_prices', '18500000' );
	zarrin_add_field( $wp_customize, 'zarrin_price_updated', 'زمان آخرین به‌روزرسانی', 'zarrin_prices', 'امروز — ۱۲:۳۰' );

	/* ============ بخش: درباره ما ============ */
	$wp_customize->add_section(
		'zarrin_about',
		array(
			'title' => 'بخش درباره ما',
			'panel' => 'zarrin_panel',
		)
	);
	zarrin_add_field( $wp_customize, 'zarrin_about_title', 'عنوان بخش', 'zarrin_about', 'درباره طلافروشی زرین' );
	zarrin_add_field(
		$wp_customize,
		'zarrin_about_text',
		'متن معرفی',
		'zarrin_about',
		'زرین با بیش از دو دهه تجربه در عرضه طلا و جواهر، مجموعه‌ای منتخب از زیباترین طرح‌های طلای ۱۸ و ۲۴ عیار را با ضمانت کتبی اصالت و عیار ارائه می‌کند. تمام محصولات ما دارای مهر و شناسنامه معتبر هستند.',
		'textarea',
		'wp_kses_post'
	);
	zarrin_add_image( $wp_customize, 'zarrin_about_img', 'تصویر بخش درباره ما', 'zarrin_about' );
	zarrin_add_field( $wp_customize, 'zarrin_stat_years', 'آمار ۱ — عدد', 'zarrin_about', '۲۵' );
	zarrin_add_field( $wp_customize, 'zarrin_stat_years_lbl', 'آمار ۱ — برچسب', 'zarrin_about', 'سال تجربه' );
	zarrin_add_field( $wp_customize, 'zarrin_stat_customers', 'آمار ۲ — عدد', 'zarrin_about', '۱۲هزار+' );
	zarrin_add_field( $wp_customize, 'zarrin_stat_customers_lbl', 'آمار ۲ — برچسب', 'zarrin_about', 'مشتری وفادار' );
	zarrin_add_field( $wp_customize, 'zarrin_stat_designs', 'آمار ۳ — عدد', 'zarrin_about', '۸۰۰+' );
	zarrin_add_field( $wp_customize, 'zarrin_stat_designs_lbl', 'آمار ۳ — برچسب', 'zarrin_about', 'مدل اختصاصی' );

	/* ============ بخش: بنر تماس (CTA) ============ */
	$wp_customize->add_section(
		'zarrin_cta',
		array(
			'title' => 'بنر دعوت به تماس',
			'panel' => 'zarrin_panel',
		)
	);
	zarrin_add_field( $wp_customize, 'zarrin_cta_title', 'عنوان بنر', 'zarrin_cta', 'خرید طلا را به اعتماد بسپارید' );
	zarrin_add_field( $wp_customize, 'zarrin_cta_sub', 'زیرعنوان بنر', 'zarrin_cta', 'کارشناسان ما پاسخگوی سوالات شما درباره قیمت روز، معاوضه و ساخت سفارشی هستند.' );

	/* ============ بخش: فوتر ============ */
	$wp_customize->add_section(
		'zarrin_footer',
		array(
			'title' => 'فوتر',
			'panel' => 'zarrin_panel',
		)
	);
	zarrin_add_field(
		$wp_customize,
		'zarrin_footer_about',
		'متن معرفی کوتاه در فوتر',
		'zarrin_footer',
		'طلافروشی زرین؛ عرضه‌کننده انواع طلا و جواهر با ضمانت اصالت، قیمت روز و امکان معاوضه. خرید شما را با خیال راحت انجام دهید.',
		'textarea',
		'wp_kses_post'
	);
}
add_action( 'customize_register', 'zarrin_customize_register' );
