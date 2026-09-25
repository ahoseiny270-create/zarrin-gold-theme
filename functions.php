<?php
/**
 * توابع قالب زرین — قالب فروشگاه طلا
 *
 * @package Zarrin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ZARRIN_VERSION', '1.5.1' );

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
 * ۲) خلاصه نوشته
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

	// پوسته رنگ فعال (دموی ۱ تا ۵) — هر پوسته یک استایل کامل است.
	wp_enqueue_style( 'zarrin-style', zarrin_skin_css(), array(), ZARRIN_VERSION );

	// کامپوننت‌های پیشرفته (اسلایدر، نظرات، محاسبه‌گر، صفحات ووکامرس) — بعد از پوسته.
	wp_enqueue_style(
		'zarrin-parts',
		get_template_directory_uri() . '/assets/css/theme-parts.css',
		array( 'zarrin-style' ),
		ZARRIN_VERSION
	);

	// فونت وزیرمتن — لوکال (بدون نیاز به اینترنت/CDN).
	wp_add_inline_style(
		'zarrin-style',
		"@font-face{font-family:Vazirmatn;src:url('" . esc_url( get_template_directory_uri() . '/assets/fonts/Vazirmatn-var.woff2' ) . "') format('woff2-variations');font-weight:100 900;font-display:swap;font-style:normal;}"
	);

	wp_enqueue_script( 'zarrin-main', get_template_directory_uri() . '/assets/js/main.js', array(), ZARRIN_VERSION, true );

	// اطلاعات لازم برای به‌روزرسانی لحظه‌ای قیمت‌ها.
	wp_localize_script(
		'zarrin-main',
		'zarrinLive',
		array(
			'ajax'     => admin_url( 'admin-ajax.php' ),
			/* فاصله به‌روزرسانی مرورگر = همان فاصله تنظیم‌شده سرور (پیش‌فرض: هر ۱۰ دقیقه). */
			'interval' => max( 1, (int) zarrin_get( 'zarrin_live_interval', 10 ) ) * 60,
			'enabled'  => zarrin_live_enabled(),
			'isWoo'    => zarrin_is_woo(),
		)
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'zarrin_assets' );

/* =========================================================
 * ۵) توابع کمکی
 * ======================================================= */

/* =========================================================
 * ۵-۱) پوسته‌های رنگ — چهار دموی آماده در یک قالب
 * ======================================================= */

/** فهرست پوسته‌های رنگ قالب */
function zarrin_skins() {
	return array(
		'gold'    => array(
			'name'  => 'زرین — تیره طلایی',
			'demo'  => 'دموی ۱',
			'label' => 'زرین',
		),
		'light'   => array(
			'name'  => 'زرین روشن — سفید-عاجی',
			'demo'  => 'دموی ۲',
			'label' => 'زرین روشن',
		),
		'emerald' => array(
			'name'  => 'زرین زمرد — زمرد تیره',
			'demo'  => 'دموی ۳',
			'label' => 'زرین زمرد',
		),
		'choco'   => array(
			'name'  => 'زرین کاکائو — شکلاتی/کاراملی',
			'demo'  => 'دموی ۴',
			'label' => 'زرین کاکائو',
		),
		'rose'    => array(
			'name'  => 'زرین رزگلد — رزگلد پودری',
			'demo'  => 'دموی ۵',
			'label' => 'زرین رزگلد',
		),
	);
}

/** شناسه پوسته فعال */
function zarrin_skin() {
	$skin  = zarrin_get( 'zarrin_demo_skin', 'gold' );
	$skins = zarrin_skins();
	return isset( $skins[ $skin ] ) ? $skin : 'gold';
}

/** نام کوتاه پوسته/دموی فعال (برای متن‌های پیشخوان) */
function zarrin_demo_name() {
	$skins = zarrin_skins();
	$skin  = zarrin_skin();
	return $skins[ $skin ]['label'];
}

/**
 * پیش‌تنظیم‌های محتوایی هر دمو (نام فروشگاه، متن فوتر، بنر تماس و درباره ما).
 * هنگام «راه‌اندازی دمو» اعمال می‌شوند تا هر دمو هویت کامل خودش را داشته باشد.
 *
 * @return array
 */
function zarrin_skin_presets() {
	$skin    = zarrin_skin();
	$presets = array(
		'gold'    => array(
			'blogname'        => 'طلا و جواهر زرین',
			'blogdescription' => 'فروشگاه آنلاین طلا و جواهر با قیمت لحظه‌ای',
			'footer_about'    => 'طلافروشی زرین؛ عرضه‌کننده انواع طلا و جواهر با ضمانت اصالت، قیمت لحظه‌ای روز و امکان معاوضه. خرید شما را با خیال راحت انجام دهید.',
			'cta_title'       => 'خرید طلا را به اعتماد بسپارید',
			'cta_sub'         => 'کارشناسان ما پاسخگوی سوالات شما درباره قیمت روز، معاوضه و ساخت سفارشی هستند.',
			'about_text'      => 'زرین با بیش از دو دهه تجربه در عرضه طلا و جواهر، مجموعه‌ای منتخب از زیباترین طرح‌های طلای ۱۸ و ۲۴ عیار را با ضمانت کتبی اصالت و عیار ارائه می‌کند.',
		),
		'light'   => array(
			'blogname'        => 'طلا و جواهر زرین روشن',
			'blogdescription' => 'گالری طلا و جواهر با میزبانى روشن و آرام',
			'footer_about'    => 'گالری زرین روشن؛ مجموعه‌ای روشن و خوانا از طلا و جواهر با ضمانت اصالت و قیمت لحظه‌ای روز. تجربه‌ای آرام از خرید طلا.',
			'cta_title'       => 'انتخاب طلای خود را ساده کنید',
			'cta_sub'         => 'کارشناسان ما برای راهنمایی خرید، معاوضه و ساخت سفارشی در کنار شما هستند.',
			'about_text'      => 'زرین روشن با فضایی آرام و شفاف، زیباترین طرح‌های طلای ۱۸ عیار را با ضمانت کتبی اصالت و قیمت روز بازار ارائه می‌کند.',
		),
		'emerald' => array(
			'blogname'        => 'طلا و جواهر زرین زمرد',
			'blogdescription' => 'جواهرات نفیس با میزبانی زمرد و طلای شامپاینی',
			'footer_about'    => 'گالری زرین زمرد؛ گزیده‌ای نفیس از طلا و جواهر با ضمانت اصالت، قیمت لحظه‌ای و خدمات معاوضه — در فضایی آرام و گالری‌وار.',
			'cta_title'       => 'درخشش زمرد، اصالت طلا',
			'cta_sub'         => 'برای مشاوره خرید، استعلام قیمت روز و سفارش ساخت اختصاصی با ما گفتگو کنید.',
			'about_text'      => 'زرین زمرد گزیده‌ای از نفیس‌ترین طرح‌های طلا و جواهر را با ضمانت کتبی اصالت و عیار، همراه با قیمت لحظه‌ای بازار ارائه می‌کند.',
		),
		'choco'   => array(
			'blogname'        => 'طلا و جواهر زرین کاکائو',
			'blogdescription' => 'طلا و جواهر با حس گرم شکلات و کارامل',
			'footer_about'    => 'زرین کاکائو؛ گرمای طلا با حس دنج شکلات. عرضه انواع طلا و جواهر با ضمانت اصالت، قیمت لحظه‌ای روز و امکان معاوضه.',
			'cta_title'       => 'گرمای طلا را هدیه دهید',
			'cta_sub'         => 'برای انتخاب هدیه، مشاوره خرید و استعلام قیمت روز، کارشناسان ما پاسخگو هستند.',
			'about_text'      => 'زرین کاکائو با فضایی گرم و دنج، منتخبی از زیباترین طرح‌های طلا و جواهر را با ضمانت کتبی اصالت و قیمت روز بازار عرضه می‌کند.',
		),
		'rose'    => array(
			'blogname'        => 'طلا و جواهر زرین رزگلد',
			'blogdescription' => 'جواهرات رزگلد؛ لطیف، ماندگار و برای هدیه',
			'footer_about'    => 'گالری زرین رزگلد؛ مجموعه‌ای لطیف از جواهرات رزگلد و طلای ۱۸ عیار با ضمانت اصالت، قیمت لحظه‌ای روز و امکان معاوضه — برای لحظه‌های خاص.',
			'cta_title'       => 'لحظه‌های خاص را ماندگار کنید',
			'cta_sub'         => 'برای انتخاب هدیه، مشاوره خرید و سفارش ساخت سفارشی رزگلد در کنار شما هستیم.',
			'about_text'      => 'زرین رزگلد با تمرکز بر جواهرات رزگلد و طرح‌های لطیف، گزیده‌ای از زیباترین قطعات طلای ۱۸ عیار را با ضمانت کتبی اصالت و قیمت روز بازار ارائه می‌کند.',
		),
	);

	return isset( $presets[ $skin ] ) ? $presets[ $skin ] : $presets['gold'];
}

/** آدرس فایل استایل پوسته فعال */
function zarrin_skin_css() {
	$skin = zarrin_skin();
	if ( 'gold' === $skin ) {
		return get_stylesheet_uri();
	}
	return get_template_directory_uri() . '/assets/css/skin-' . $skin . '.css';
}

/** مسیر پوشه تصاویر نمونه پوسته فعال */
function zarrin_skin_img_dir() {
	$skin = zarrin_skin();
	if ( 'gold' !== $skin && file_exists( get_template_directory() . '/assets/img/skins/' . $skin . '/hero.jpg' ) ) {
		return get_template_directory() . '/assets/img/skins/' . $skin;
	}
	return get_template_directory() . '/assets/img';
}

/**
 * آدرس تصویر نمونه برای یک پوسته دلخواه.
 *
 * @param string $skin شناسه پوسته.
 * @param string $file نام فایل تصویر.
 * @return string
 */
function zarrin_skin_img_for( $skin, $file ) {
	$skins = zarrin_skins();
	if ( 'gold' !== $skin && isset( $skins[ $skin ] ) && file_exists( get_template_directory() . '/assets/img/skins/' . $skin . '/' . $file ) ) {
		return get_template_directory_uri() . '/assets/img/skins/' . $skin . '/' . $file;
	}
	return get_template_directory_uri() . '/assets/img/' . $file;
}

/** آدرس تصویر نمونه پوسته فعال */
function zarrin_skin_img( $file ) {
	return zarrin_skin_img_for( zarrin_skin(), $file );
}

/** افزودن کلاس پوسته به body */
function zarrin_body_class( $classes ) {
	$classes[] = 'zarrin-skin-' . zarrin_skin();

	if ( ! zarrin_get( 'zarrin_sticky_atc_enable', true ) ) {
		$classes[] = 'zarrin-no-sticky-atc';
	}

	return $classes;
}
add_filter( 'body_class', 'zarrin_body_class' );

/* =========================================================
 * ۵-۱) سئو، شبکه‌های اجتماعی و پیش‌بارگذاری فونت
 * ======================================================= */

/** پیش‌بارگذاری فونت وزیرمتن برای نمایش سریع‌تر متن فارسی */
function zarrin_preload_font() {
	printf(
		'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
		esc_url( get_template_directory_uri() . '/assets/fonts/Vazirmatn-var.woff2' )
	);
}
add_action( 'wp_head', 'zarrin_preload_font', 1 );

/** توضیح متا، Open Graph و Twitter Card برای اشتراک‌گذاری در واتساپ/تلگرام */
function zarrin_social_meta() {

	if ( is_admin() || is_feed() ) {
		return;
	}

	$title = wp_get_document_title();

	if ( is_singular() ) {
		$desc = get_the_excerpt();
		if ( ! $desc ) {
			$desc = wp_strip_all_tags( get_the_content() );
		}
		$desc = wp_trim_words( $desc, 30, '…' );
	} else {
		$desc = get_bloginfo( 'description' );
		if ( ! $desc ) {
			$desc = 'خرید طلا و جواهر با ضمانت اصالت، مهر عیار، قیمت روز و ارسال بیمه‌شده — ' . get_bloginfo( 'name' );
		}
	}

	$image = '';
	if ( is_singular() && has_post_thumbnail() ) {
		$image = get_the_post_thumbnail_url( null, 'large' );
	}
	if ( ! $image ) {
		$image = zarrin_skin_img( 'hero.jpg' );
	}

	echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
	echo '<meta property="og:locale" content="fa_IR">' . "\n";
	echo '<meta property="og:type" content="' . ( is_singular( 'product' ) ? 'product' : ( is_singular() ? 'article' : 'website' ) ) . '">' . "\n";
	echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
	echo '<meta property="og:description" content="' . esc_attr( $desc ) . '">' . "\n";
	echo '<meta property="og:url" content="' . esc_url( home_url( add_query_arg( array() ) ) ) . '">' . "\n";
	if ( $image ) {
		echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
	}
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
	echo '<meta name="twitter:description" content="' . esc_attr( $desc ) . '">' . "\n";
	if ( $image ) {
		echo '<meta name="twitter:image" content="' . esc_url( $image ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'zarrin_social_meta', 2 );

/** داده‌های ساختاریافته فروشگاه طلا (WebSite + Organization + نرخ روز) */
function zarrin_website_schema() {

	if ( is_admin() ) {
		return;
	}

	$prices = zarrin_get_prices();
	$phone  = zarrin_get( 'zarrin_phone', '' );

	$schema = array(
		'@context' => 'https://schema.org',
		'@graph'   => array(
			array(
				'@type'     => 'WebSite',
				'@id'       => home_url( '/#website' ),
				'url'       => home_url( '/' ),
				'name'      => get_bloginfo( 'name' ),
				'description' => get_bloginfo( 'description' ),
				'inLanguage' => 'fa-IR',
			),
			array(
				'@type'      => 'Store',
				'@id'        => home_url( '/#store' ),
				'name'       => get_bloginfo( 'name' ),
				'url'        => home_url( '/' ),
				'telephone'  => $phone,
				'currenciesAccepted' => 'IRR',
				'priceRange' => '$$',
			),
			array(
				'@type'       => 'ItemList',
				'name'        => 'نرخ لحظه‌ای طلا و سکه',
				'itemListElement' => array(
					array( '@type' => 'ListItem', 'position' => 1, 'name' => 'طلای ۱۸ عیار (هر گرم)', 'description' => zarrin_money( $prices['p18']['value'] ) . ' تومان' ),
					array( '@type' => 'ListItem', 'position' => 2, 'name' => 'طلای ۲۴ عیار (هر گرم)', 'description' => zarrin_money( $prices['p24']['value'] ) . ' تومان' ),
					array( '@type' => 'ListItem', 'position' => 3, 'name' => 'سکه امامی', 'description' => zarrin_money( $prices['coin']['value'] ) . ' تومان' ),
					array( '@type' => 'ListItem', 'position' => 4, 'name' => 'مثقال طلا', 'description' => zarrin_money( $prices['mesghal']['value'] ) . ' تومان' ),
				),
			),
		),
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}
add_action( 'wp_head', 'zarrin_website_schema', 22 );

/** دکمه‌های اشتراک‌گذاری سریع (فقط در نوشته‌های وبلاگ) */
function zarrin_share_buttons() {

	if ( ! is_singular( 'post' ) ) {
		return;
	}

	$url   = rawurlencode( get_permalink() );
	$title = rawurlencode( get_the_title() );
	?>
	<div class="zshare">
		<span class="zshare-title">اشتراک‌گذاری:</span>
		<a target="_blank" rel="noopener" href="https://wa.me/?text=<?php echo esc_attr( $title . '%20' . $url ); ?>"><?php zarrin_icon_e( 'whatsapp', 17 ); ?> واتساپ</a>
		<a target="_blank" rel="noopener" href="https://t.me/share/url?url=<?php echo esc_attr( $url ); ?>&text=<?php echo esc_attr( $title ); ?>"><?php zarrin_icon_e( 'telegram', 17 ); ?> تلگرام</a>
		<a href="#" onclick="navigator.clipboard&&navigator.clipboard.writeText(location.href);this.textContent='لینک کپی شد';return false;"><?php zarrin_icon_e( 'check', 17 ); ?> کپی لینک</a>
	</div>
	<?php
}
add_action( 'zarrin_after_post_content', 'zarrin_share_buttons' );

/* =========================================================
 * ۵-۲) بلوک‌های چنداسلایدی و محتوای صفحه اصلی
 * ======================================================= */

/**
 * اسلایدهای هیرو (سه اسلاید، قابل ویرایش در سفارشی‌سازی).
 *
 * @return array
 */
function zarrin_slider_slides() {

	$defaults = array(
		1 => array(
			'eyebrow'    => 'طلا و جواهر زرین — از سال ۱۳۷۵',
			'title'      => 'درخشش طلای اصیل، زیبایی ماندگار شما',
			'sub'        => 'مجموعه‌ای نفیس از طلای ۱۸ و ۲۴ عیار با ضمانت اصالت، قیمت روز و امکان معاوضه.',
			'img'        => 'hero.jpg',
			'btn1_text'  => 'مشاهده محصولات',
			'btn1_url'   => '',
			'btn2_text'  => 'تماس با ما',
			'btn2_url'   => '',
			'show_price' => true,
		),
		2 => array(
			'eyebrow'    => 'ضمانت اصالت و عیار',
			'title'      => 'هر قطعه با شناسنامه و مهر عیار معتبر',
			'sub'        => 'تمام محصولات دارای فاکتور رسمی، مهر عیار و ضمانت‌نامه کتبی اصالت هستند.',
			'img'        => 'product-3.jpg',
			'btn1_text'  => 'فروشگاه',
			'btn1_url'   => '',
			'btn2_text'  => 'درباره ما',
			'btn2_url'   => '',
			'show_price' => false,
		),
		3 => array(
			'eyebrow'    => 'معاوضه و بازخرید',
			'title'      => 'طلای قدیمی خود را با طرح نو معاوضه کنید',
			'sub'        => 'امکان معاوضه طلا با طلا بدون محاسبه اجرت قبلی و بازخرید تضمینی محصولات فروشگاه.',
			'img'        => 'about.jpg',
			'btn1_text'  => 'شرایط معاوضه',
			'btn1_url'   => '',
			'btn2_text'  => 'تماس با کارشناس',
			'btn2_url'   => '',
			'show_price' => false,
		),
	);

	$slides = array();
	for ( $i = 1; $i <= 3; $i++ ) {
		$d = $defaults[ $i ];

		/* اسلاید اول از تنظیمات قدیمی هیرو ارث‌بری می‌کند تا سایت‌های فعال نشکنند. */
		if ( 1 === $i ) {
			$d['eyebrow']   = zarrin_get( 'zarrin_hero_eyebrow', $d['eyebrow'] );
			$d['title']     = zarrin_get( 'zarrin_hero_title', $d['title'] );
			$d['sub']       = zarrin_get( 'zarrin_hero_sub', $d['sub'] );
			$d['img']       = zarrin_get( 'zarrin_hero_img', $d['img'] );
			$d['btn1_text'] = zarrin_get( 'zarrin_hero_btn1_text', $d['btn1_text'] );
			$d['btn1_url']  = zarrin_get( 'zarrin_hero_btn1_url', $d['btn1_url'] );
			$d['btn2_text'] = zarrin_get( 'zarrin_hero_btn2_text', $d['btn2_text'] );
			$d['btn2_url']  = zarrin_get( 'zarrin_hero_btn2_url', $d['btn2_url'] );
		}

		$img = zarrin_get( 'zarrin_slide' . $i . '_img', '' );
		$slides[ $i ] = array(
			'eyebrow'    => zarrin_get( 'zarrin_slide' . $i . '_eyebrow', $d['eyebrow'] ),
			'title'      => zarrin_get( 'zarrin_slide' . $i . '_title', $d['title'] ),
			'sub'        => zarrin_get( 'zarrin_slide' . $i . '_sub', $d['sub'] ),
			'img'        => $img ? $img : ( preg_match( '#^https?://#', $d['img'] ) ? $d['img'] : zarrin_skin_img( $d['img'] ) ),
			'btn1_text'  => zarrin_get( 'zarrin_slide' . $i . '_btn1_text', $d['btn1_text'] ),
			'btn1_url'   => zarrin_get( 'zarrin_slide' . $i . '_btn1_url', $d['btn1_url'] ),
			'btn2_text'  => zarrin_get( 'zarrin_slide' . $i . '_btn2_text', $d['btn2_text'] ),
			'btn2_url'   => zarrin_get( 'zarrin_slide' . $i . '_btn2_url', $d['btn2_url'] ),
			'show_price' => (bool) zarrin_get( 'zarrin_slide' . $i . '_price', $d['show_price'] ),
		);
	}

	return $slides;
}

/**
 * تبدیل متن چندخطی به آرایه سطرها.
 * هر سطر با «|» به ستون‌ها تقسیم می‌شود.
 *
 * @param string $text متن خام.
 * @param int    $cols تعداد ستون مورد انتظار.
 * @return array
 */
function zarrin_parse_lines( $text, $cols = 2 ) {

	$rows = array();
	$text = (string) $text;
	if ( '' === trim( $text ) ) {
		return $rows;
	}
	foreach ( preg_split( '/
|
|
/', $text ) as $line ) {
		$line = trim( $line );
		if ( '' === $line ) {
			continue;
		}
		$parts = array_map( 'trim', explode( '|', $line ) );
		if ( count( $parts ) < $cols ) {
			continue;
		}
		$rows[] = array_slice( $parts, 0, $cols );
	}
	return $rows;
}

/** نظرات مشتریان (پیش‌فرض + قابل ویرایش) */
function zarrin_testimonials() {

	$raw = zarrin_get( 'zarrin_testimonials_text', '' );
	$rows = zarrin_parse_lines( $raw, 4 );

	if ( empty( $rows ) ) {
		$rows = array(
			array( 'مریم رضایی', 'تهران', '۵', 'انگشتر رو با پست پیشتاز و بسته‌بندی خیلی تمیز گرفتم؛ مهر عیار و شناسنامه همراهش بود. قیمت هم دقیقاً بر اساس نرخ روز محاسبه شد.' ),
			array( 'علی محمدی', 'اصفهان', '۵', 'برای معاوضه رفتم و بدون هیچ دردسری طلای قدیمی‌ام را تعویض کردم. اجرت قبلی حساب نشد و همه‌چیز شفاف بود.' ),
			array( 'سمیرا ک.', 'شیراز', '۴', 'مشاوره تلفنی خیلی کمک کرد تا بین دو مدل انتخاب کنم. ارسال کمی دیر شد ولی ارزشش را داشت.' ),
			array( 'حسین ط.', 'مشهد', '۵', 'قیمت لحظه‌ای سایت را قبل از خرید چک کردم و فاکتور با همان نرخ صادر شد. اعتماد خوبی ایجاد می‌کند.' ),
		);
	}

	return $rows;
}

/** سوالات متداول */
function zarrin_faqs() {

	$raw  = zarrin_get( 'zarrin_faq_text', '' );
	$rows = zarrin_parse_lines( $raw, 2 );

	if ( empty( $rows ) ) {
		$rows = array(
			array( 'قیمت محصولات چگونه محاسبه می‌شود؟', 'قیمت هر قطعه بر پایه نرخ لحظه‌ای گرم طلا در بازار، ضرب در وزن، به‌علاوه اجرت ساخت و سود فروشگاه محاسبه می‌شود. از محاسبه‌گر قیمت در صفحه محصول می‌توانید برآورد دقیق‌تری بگیرید.' ),
			array( 'آیا محصولات شناسنامه و مهر عیار دارند؟', 'بله. تمام محصولات دارای مهر عیار، شناسنامه معتبر و فاکتور رسمی با ذکر وزن، عیار و اجرت هستند.' ),
			array( 'شرایط معاوضه و بازخرید چیست؟', 'معاوضه طلا با طلا تا ۷ روز پس از خرید امکان‌پذیر است و اجرت ساخت قبلی محاسبه نمی‌شود. بازخرید نیز با کسر کارمزد و بر اساس نرخ روز انجام می‌شود.' ),
			array( 'ارسال چگونه و با چه ضمانتی انجام می‌شود؟', 'ارسال با پست پیشتاز یا تیپاکس، کاملاً بیمه‌شده و با بسته‌بندی مخفی انجام می‌شود. کد رهگیری پس از ثبت سفارش پیامک می‌گردد.' ),
			array( 'امکان پرداخت اقساطی یا کارت‌به‌کارت وجود دارد؟', 'بله، پرداخت از طریق درگاه بانکی امن (شاپرک) انجام می‌شود و برای سفارش‌های خاص امکان هماهنگی کارت‌به‌کارت یا پرداخت در محل (تهران) نیز فراهم است.' ),
			array( 'اگر نرخ طلا پس از ثبت سفارش تغییر کند چه می‌شود؟', 'قیمت در زمان ثبت سفارش قطعی می‌شود. اگر پیش از پرداخت نرخ بازار تغییر کند، کارشناسان ما با شما تماس می‌گیرند و سفارش با نرخ جدید تأیید یا لغو می‌شود.' ),
		);
	}

	return $rows;
}

/**
 * نخستین حرف یک متن (سازگار با سرورهای بدون mbstring).
 *
 * @param string $text متن.
 * @return string
 */
function zarrin_first_char( $text ) {

	$text = trim( wp_strip_all_tags( (string) $text ) );
	if ( '' === $text ) {
		return '';
	}
	if ( function_exists( 'mb_substr' ) ) {
		return mb_substr( $text, 0, 1, 'UTF-8' );
	}
	if ( preg_match( '/^./u', $text, $m ) ) {
		return $m[0];
	}
	return substr( $text, 0, 1 );
}

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

/** بررسی صحتی چک‌باکس */
function zarrin_sanitize_checkbox( $checked ) {
	return ( isset( $checked ) && true == $checked ) ? true : false;
}

/** آیا دریافت خودکار قیمت فعال است؟ */
function zarrin_live_enabled() {
	return (bool) zarrin_get( 'zarrin_live_enable', true );
}

// راه‌اندازی دمو با یک کلیک.
require get_template_directory() . '/inc/demo-import.php';

// مدیریت دموها (انتخاب سریع از نوار بالای پیشخوان + کارت‌های تصویری).
require get_template_directory() . '/inc/skins-admin.php';

// مشخصات تخصصی طلا (عیار، وزن، اجرت، شناسنامه) + محاسبه‌گر قیمت.
require get_template_directory() . '/inc/gold-fields.php';

// صفحات ورود/ثبت‌نام، سبد خرید، تسویه و ثبت نهایی خرید ووکامرس.
require get_template_directory() . '/inc/woo-pages.php';

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

	// نام‌های جایگزین (هدر و فوتر از instagram/whatsapp استفاده می‌کنند).
	$aliases = array(
		'instagram' => 'insta',
		'whatsapp'  => 'whats',
	);
	if ( isset( $aliases[ $name ] ) ) {
		$name = $aliases[ $name ];
	}

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
 * ۶) قیمت لحظه‌ای طلا و سکه
 * ======================================================= */

/**
 * دریافت قیمت‌های لحظه‌ای از TGJU و ذخیره در کش موقت.
 * قیمت‌ها از API عمومی tgju.org به ریال دریافت و به تومان تبدیل می‌شوند.
 *
 * @return bool موفقیت دریافت.
 */
function zarrin_refresh_live_prices() {

	// قفل کوتاه برای جلوگیری از درخواست‌های همزمان.
	if ( get_transient( 'zarrin_live_prices_lock' ) ) {
		return false;
	}
	set_transient( 'zarrin_live_prices_lock', 1, 45 );

	$response = wp_remote_get(
		'https://call1.tgju.org/ajax.json',
		array(
			'timeout'    => 10,
			'user-agent' => 'Mozilla/5.0 (ZarrinTheme/' . ZARRIN_VERSION . ')',
		)
	);

	if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
		return false;
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( empty( $body['current'] ) || ! is_array( $body['current'] ) ) {
		return false;
	}

	$map  = array( 'geram18', 'geram24', 'sekee', 'mesghal' );
	$keys = array(
		'geram18' => 'p18',
		'geram24' => 'p24',
		'sekee'   => 'coin',
		'mesghal' => 'mesghal',
	);

	$items = array();
	foreach ( $map as $key ) {
		if ( isset( $body['current'][ $key ]['p'] ) ) {
			$rial = (float) str_replace( ',', '', (string) $body['current'][ $key ]['p'] );
			if ( $rial > 0 ) {
				$items[ $keys[ $key ] ] = array(
					'toman' => $rial / 10, // تبدیل ریال به تومان.
					'change'=> isset( $body['current'][ $key ]['dp'] ) ? (float) str_replace( ',', '', (string) $body['current'][ $key ]['dp'] ) : 0,
				);
			}
		}
	}

	if ( count( $items ) < 2 ) {
		return false;
	}

	$data = array(
		'items'   => $items,
		'updated' => zarrin_fa_digits( date_i18n( 'H:i' ) ),
		'time'    => time(),
	);

	$interval = max( 1, (int) zarrin_get( 'zarrin_live_interval', 10 ) );
	set_transient( 'zarrin_live_prices', $data, MINUTE_IN_SECONDS * $interval );
	set_transient( 'zarrin_live_prices_last', $data, DAY_IN_SECONDS ); // نسخه پشتیبان تا ۲۴ ساعت.

	return true;
}

/**
 * فاصله زمانی کرون به‌اندازه تنظیم «فاصله به‌روزرسانی خودکار قیمت».
 *
 * @param array $schedules زمان‌بندی‌های موجود.
 * @return array
 */
function zarrin_cron_schedules( $schedules ) {

	$minutes = max( 1, (int) zarrin_get( 'zarrin_live_interval', 10 ) );

	$schedules['zarrin_prices_interval'] = array(
		'interval' => MINUTE_IN_SECONDS * $minutes,
		'display'  => 'زرین — به‌روزرسانی قیمت طلا (هر ' . zarrin_fa_digits( $minutes ) . ' دقیقه)',
	);

	return $schedules;
}
add_filter( 'cron_schedules', 'zarrin_cron_schedules' ); // phpcs:ignore WordPress.WP.CronInterval.ChangeDetected

/** زمان‌بندی/حذف رویداد کرون بر اساس تنظیمات قالب */
function zarrin_sync_prices_cron() {

	if ( zarrin_live_enabled() ) {
		if ( ! wp_next_scheduled( 'zarrin_refresh_prices_event' ) ) {
			wp_schedule_event( time() + MINUTE_IN_SECONDS, 'zarrin_prices_interval', 'zarrin_refresh_prices_event' );
		}
	} elseif ( wp_next_scheduled( 'zarrin_refresh_prices_event' ) ) {
		wp_clear_scheduled_hook( 'zarrin_refresh_prices_event' );
	}
}
add_action( 'init', 'zarrin_sync_prices_cron' );
add_action( 'zarrin_refresh_prices_event', 'zarrin_refresh_live_prices' );

/** پاک‌سازی زمان‌بندی هنگام تغییر قالب */
function zarrin_clear_prices_cron() {
	wp_clear_scheduled_hook( 'zarrin_refresh_prices_event' );
}
add_action( 'switch_theme', 'zarrin_clear_prices_cron' );

/**
 * داده‌های قیمت برای نمایش (لحظه‌ای یا دستی).
 *
 * @return array
 */
function zarrin_get_prices() {

	$live = get_transient( 'zarrin_live_prices' );

	// اگر کش خالی و حالت لحظه‌ای فعال است، یک‌بار تلاش برای دریافت.
	if ( ! $live && zarrin_live_enabled() ) {
		zarrin_refresh_live_prices();
		$live = get_transient( 'zarrin_live_prices' );
	}

	// اگر باز هم خالی بود، از آخرین داده معتبر (تا ۲۴ ساعت) استفاده کن.
	if ( ! $live ) {
		$live = get_transient( 'zarrin_live_prices_last' );
	}

	$manual = array(
		'p18'     => zarrin_get( 'zarrin_price_18', '23966000' ),
		'p24'     => zarrin_get( 'zarrin_price_24', '31954000' ),
		'coin'    => zarrin_get( 'zarrin_price_coin', '237010000' ),
		'mesghal' => zarrin_get( 'zarrin_price_mesghal', '103822000' ),
	);

	$prices = array(
		'_live'    => (bool) $live,
		'_updated' => ( $live && ! empty( $live['updated'] ) ) ? $live['updated'] : zarrin_get( 'zarrin_price_updated', '۱۲:۳۰' ),
	);

	foreach ( $manual as $key => $fallback ) {
		$entry = array(
			'value'  => is_numeric( $fallback ) ? (float) $fallback : $fallback,
			'change' => null,
		);
		if ( $live && isset( $live['items'][ $key ] ) ) {
			$entry['value']  = $live['items'][ $key ]['toman'];
			$entry['change'] = isset( $live['items'][ $key ]['change'] ) ? $live['items'][ $key ]['change'] : 0;
		}
		$prices[ $key ] = $entry;
	}

	return $prices;
}

/** نشان درصد تغییر (سبز/قرمز) */
function zarrin_change_badge( $dp ) {
	if ( null === $dp || '' === $dp || ! is_numeric( $dp ) ) {
		return '';
	}
	$dp    = (float) $dp;
	$dir   = $dp > 0 ? 'up' : ( $dp < 0 ? 'down' : 'flat' );
	$arrow = 'up' === $dir ? '▲' : ( 'down' === $dir ? '▼' : '◆' );
	$num   = number_format( abs( $dp ), 2, '٫', '' );
	$num   = rtrim( rtrim( $num, '0' ), '٫' );
	return '<span class="p-change ' . esc_attr( $dir ) . '">' . esc_html( $arrow . ' ' . zarrin_fa_digits( $num ) . '٪' ) . '</span>';
}

/** خروجی AJAX برای به‌روزرسانی لحظه‌ای در مرورگر */
function zarrin_ajax_live_prices() {

	$data  = zarrin_get_prices();
	$items = array();
	foreach ( array( 'p18', 'p24', 'coin', 'mesghal' ) as $key ) {
		$items[ $key ] = array(
			'formatted' => zarrin_money( $data[ $key ]['value'] ),
			'badge'     => zarrin_change_badge( $data[ $key ]['change'] ),
			'raw'       => is_numeric( $data[ $key ]['value'] ) ? (float) $data[ $key ]['value'] : 0,
		);
	}

	wp_send_json_success(
		array(
			'items'   => $items,
			'updated' => $data['_updated'],
			'live'    => $data['_live'],
		)
	);
}
add_action( 'wp_ajax_zarrin_live_prices', 'zarrin_ajax_live_prices' );
add_action( 'wp_ajax_nopriv_zarrin_live_prices', 'zarrin_ajax_live_prices' );

/* =========================================================
 * ۷) ووکامرس — اضافات
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

/** افزودن واحد پول «تومان» به ووکامرس */
function zarrin_add_toman_currency( $currencies ) {
	$currencies['IRT'] = 'تومان ایران';
	return $currencies;
}
add_filter( 'woocommerce_currencies', 'zarrin_add_toman_currency' );

/** نماد تومان */
function zarrin_toman_symbol( $symbol, $currency ) {
	return ( 'IRT' === $currency ) ? 'تومان' : $symbol;
}
add_filter( 'woocommerce_currency_symbol', 'zarrin_toman_symbol', 10, 2 );

/* =========================================================
 * ۸) تنظیمات سفارشی‌سازی (نمایش ← سفارشی‌سازی)
 * ======================================================= */
function zarrin_customize_register( $wp_customize ) {

	$wp_customize->add_panel(
		'zarrin_panel',
		array(
			'title'    => 'تنظیمات قالب زرین',
			'priority' => 5,
		)
	);

	/* --- فیلدهای متنی --- */
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

	/* --- فیلد تصویر --- */
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

	/* ============ بخش: ظاهر سایت (انتخاب دمو) ============ */
	$wp_customize->add_section(
		'zarrin_skin_section',
		array(
			'title'       => 'ظاهر سایت — انتخاب دمو',
			'panel'       => 'zarrin_panel',
			'priority'    => 1,
			'description' => 'چهار دموی آماده زرین در یک قالب. با تغییر این گزینه، رنگ‌بندی، جزئیات ظاهری و تصاویر نمونه سایت عوض می‌شود؛ متن‌ها و تصاویری که خودتان تغییر داده باشید دست‌نخورده می‌ماند.',
		)
	);
	$wp_customize->add_setting(
		'zarrin_demo_skin',
		array(
			'default'           => 'gold',
			'sanitize_callback' => 'sanitize_key',
		)
	);
	$zarrin_skin_choices = array();
	foreach ( zarrin_skins() as $zarrin_slug => $zarrin_info ) {
		$zarrin_skin_choices[ $zarrin_slug ] = $zarrin_info['name'] . ' — ' . $zarrin_info['demo'];
	}
	$wp_customize->add_control(
		'zarrin_demo_skin',
		array(
			'label'       => 'پوسته رنگ (دمو)',
			'description' => 'دموی ۱: تیره طلایی (پیش‌فرض) — دموی ۲: روشن عاجی — دموی ۳: زمرد تیره — دموی ۴: شکلاتی/کاراملی',
			'section'     => 'zarrin_skin_section',
			'type'        => 'select',
			'choices'     => $zarrin_skin_choices,
		)
	);

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
			'description' => 'به‌صورت پیش‌فرض قیمت‌ها به‌صورت خودکار و لحظه‌ای از بازار طلا (tgju.org) دریافت و نمایش داده می‌شوند. مقادیر دستی فقط وقتی استفاده می‌شوند که دریافت خودکار غیرفعال باشد یا ارتباط برقرار نشود. اعداد را تومان و بدون جداکننده وارد کنید.',
		)
	);

	$wp_customize->add_setting(
		'zarrin_live_enable',
		array(
			'default'           => true,
			'sanitize_callback' => 'zarrin_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'zarrin_live_enable',
		array(
			'label'   => 'دریافت خودکار قیمت از بازار (لحظه‌ای)',
			'section' => 'zarrin_prices',
			'type'    => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'zarrin_live_interval',
		array(
			'default'           => 10,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		'zarrin_live_interval',
		array(
			'label'       => 'فاصله به‌روزرسانی خودکار قیمت (دقیقه)',
			'description' => 'پیش‌فرض ۱۰ دقیقه. سرور در این فاصله نرخ‌ها را از بازار می‌خواند، مرورگر بازدیدکننده هم در همان فاصله آخرین نرخ کش‌شده را به‌روز می‌کند و کرون وردپرس نیز به‌صورت زمان‌بندی‌شده قیمت‌ها را تازه می‌کند.',
			'section'     => 'zarrin_prices',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 1,
				'max'  => 120,
				'step' => 1,
			),
		)
	);

	zarrin_add_field( $wp_customize, 'zarrin_price_18', 'قیمت دستی گرم طلای ۱۸ عیار (تومان)', 'zarrin_prices', '23966000' );
	zarrin_add_field( $wp_customize, 'zarrin_price_24', 'قیمت دستی گرم طلای ۲۴ عیار (تومان)', 'zarrin_prices', '31954000' );
	zarrin_add_field( $wp_customize, 'zarrin_price_coin', 'قیمت دستی سکه امامی (تومان)', 'zarrin_prices', '237010000' );
	zarrin_add_field( $wp_customize, 'zarrin_price_mesghal', 'قیمت دستی مثقال طلا (تومان)', 'zarrin_prices', '103822000' );
	zarrin_add_field( $wp_customize, 'zarrin_price_updated', 'زمان دستی (فقط وقتی دریافت خودکار خاموش است)', 'zarrin_prices', '۱۲:۳۰' );

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

	/* ============ بخش: بلوک‌های صفحه اصلی (اسلایدر، نظرات، سوالات) ============ */
	$wp_customize->add_section(
		'zarrin_blocks',
		array(
			'title'       => 'بلوک‌های صفحه اصلی',
			'panel'       => 'zarrin_panel',
			'description' => 'اسلایدر چنداسلایدی، نوار متحرک نرخ، نظرات مشتریان و سوالات متداول را از اینجا مدیریت کنید.',
		)
	);

	$zarrin_toggles = array(
		'zarrin_slider_enable'     => array( 'اسلایدر چنداسلایدی بنر اصلی', true ),
		'zarrin_marquee_enable'    => array( 'نوار متحرک نرخ طلا و سکه', true ),
		'zarrin_testi_enable'      => array( 'بخش نظرات مشتریان', true ),
		'zarrin_faq_enable'        => array( 'بخش سوالات متداول', true ),
		'zarrin_float_enable'      => array( 'نوار اقدام شناور در موبایل (تماس، واتساپ، سبد)', true ),
		'zarrin_sticky_atc_enable' => array( 'نوار چسبان «افزودن به سبد» در موبایل', true ),
		'zarrin_calc_enable'       => array( 'محاسبه‌گر قیمت روز در صفحه محصول', true ),
		'zarrin_trust_badges'      => array( 'نمایش نشان‌های اعتماد در سبد و تسویه', true ),
	);

	foreach ( $zarrin_toggles as $zarrin_tid => $zarrin_tinfo ) {
		$wp_customize->add_setting(
			$zarrin_tid,
			array(
				'default'           => $zarrin_tinfo[1],
				'sanitize_callback' => 'zarrin_sanitize_checkbox',
			)
		);
		$wp_customize->add_control(
			$zarrin_tid,
			array(
				'label'   => $zarrin_tinfo[0],
				'section' => 'zarrin_blocks',
				'type'    => 'checkbox',
			)
		);
	}

	/* --- اسلایدهای هیرو --- */
	for ( $zarrin_si = 1; $zarrin_si <= 3; $zarrin_si++ ) {
		zarrin_add_field( $wp_customize, 'zarrin_slide' . $zarrin_si . '_eyebrow', 'اسلاید ' . zarrin_fa_digits( $zarrin_si ) . ' — متن کوچک', 'zarrin_blocks', '' );
		zarrin_add_field( $wp_customize, 'zarrin_slide' . $zarrin_si . '_title', 'اسلاید ' . zarrin_fa_digits( $zarrin_si ) . ' — عنوان', 'zarrin_blocks', '' );
		zarrin_add_field( $wp_customize, 'zarrin_slide' . $zarrin_si . '_sub', 'اسلاید ' . zarrin_fa_digits( $zarrin_si ) . ' — توضیح', 'zarrin_blocks', '', 'textarea' );
		zarrin_add_image( $wp_customize, 'zarrin_slide' . $zarrin_si . '_img', 'اسلاید ' . zarrin_fa_digits( $zarrin_si ) . ' — تصویر', 'zarrin_blocks' );
		zarrin_add_field( $wp_customize, 'zarrin_slide' . $zarrin_si . '_btn1_text', 'اسلاید ' . zarrin_fa_digits( $zarrin_si ) . ' — متن دکمه اول', 'zarrin_blocks', '' );
		zarrin_add_field( $wp_customize, 'zarrin_slide' . $zarrin_si . '_btn1_url', 'اسلاید ' . zarrin_fa_digits( $zarrin_si ) . ' — لینک دکمه اول', 'zarrin_blocks', '' );
		zarrin_add_field( $wp_customize, 'zarrin_slide' . $zarrin_si . '_btn2_text', 'اسلاید ' . zarrin_fa_digits( $zarrin_si ) . ' — متن دکمه دوم', 'zarrin_blocks', '' );
		zarrin_add_field( $wp_customize, 'zarrin_slide' . $zarrin_si . '_btn2_url', 'اسلاید ' . zarrin_fa_digits( $zarrin_si ) . ' — لینک دکمه دوم', 'zarrin_blocks', '' );
		$wp_customize->add_setting(
			'zarrin_slide' . $zarrin_si . '_price',
			array(
				'default'           => ( 1 === $zarrin_si ),
				'sanitize_callback' => 'zarrin_sanitize_checkbox',
			)
		);
		$wp_customize->add_control(
			'zarrin_slide' . $zarrin_si . '_price',
			array(
				'label'   => 'اسلاید ' . zarrin_fa_digits( $zarrin_si ) . ' — نمایش کارت قیمت لحظه‌ای',
				'section' => 'zarrin_blocks',
				'type'    => 'checkbox',
			)
		);
	}

	zarrin_add_field(
		$wp_customize,
		'zarrin_testimonials_text',
		'نظرات مشتریان — هر سطر: نام | شهر | امتیاز ۱ تا ۵ | متن',
		'zarrin_blocks',
		'',
		'textarea',
		'sanitize_textarea_field'
	);
	zarrin_add_field(
		$wp_customize,
		'zarrin_faq_text',
		'سوالات متداول — هر سطر: سوال | پاسخ',
		'zarrin_blocks',
		'',
		'textarea',
		'sanitize_textarea_field'
	);

	/* ============ بخش: تنظیمات طلا و ووکامرس ============ */
	$wp_customize->add_section(
		'zarrin_gold',
		array(
			'title'       => 'تنظیمات طلا و فروش',
			'panel'       => 'zarrin_panel',
			'description' => 'مقادیر پیش‌فرض محاسبه‌گر قیمت و راه‌های ارتباط سریع مشتری.',
		)
	);
	$wp_customize->add_setting(
		'zarrin_default_profit',
		array(
			'default'           => 7,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		'zarrin_default_profit',
		array(
			'label'       => 'سود پیش‌فرض فروشگاه (درصد)',
			'description' => 'در محاسبه‌گر قیمت روز استفاده می‌شود. برای هر محصول قابل تغییر است.',
			'section'     => 'zarrin_gold',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 0,
				'max'  => 100,
				'step' => 1,
			),
		)
	);
	zarrin_add_field( $wp_customize, 'zarrin_whatsapp_num', 'شماره واتساپ برای سفارش (با کد کشور، بدون +)', 'zarrin_gold', '989123456789' );
	zarrin_add_field( $wp_customize, 'zarrin_telegram_id', 'شناسه تلگرام برای پشتیبانی', 'zarrin_gold', '' );
}
add_action( 'customize_register', 'zarrin_customize_register' );
