<?php
/**
 * راه‌اندازی دمو با یک کلیک — قالب زرین
 *
 * با یک کلیک، محتوای نمونه فروشگاه (برگه‌ها، منوها، نوشته‌ها، محصولات،
 * تصاویر و تنظیمات) ساخته می‌شود تا سایت دقیقاً مثل دمو شود.
 *
 * @package Zarrin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* =========================================================
 * صفحه مدیریت و اعلان‌ها
 * ======================================================= */

/** افزودن صفحه «راه‌اندازی دمو» زیر منوی نمایش */
function zarrin_demo_menu() {
	add_theme_page(
		'راه‌اندازی دمو زرین',
		'راه‌اندازی دمو زرین',
		'manage_options',
		'zarrin-demo',
		'zarrin_demo_page'
	);
}
add_action( 'admin_menu', 'zarrin_demo_menu' );

/** محتوای صفحه راه‌اندازی دمو */
function zarrin_demo_page() {
	$done    = (bool) get_option( 'zarrin_demo_done' );
	$success = isset( $_GET['zarrin_demo'] ) && 'done' === $_GET['zarrin_demo']; // phpcs:ignore WordPress.Security.NonceVerification
	$error   = isset( $_GET['zarrin_demo'] ) && 'error' === $_GET['zarrin_demo']; // phpcs:ignore WordPress.Security.NonceVerification
	$url     = wp_nonce_url( admin_url( 'admin-post.php?action=zarrin_demo_import' ), 'zarrin_demo_import' );
	?>
	<div class="wrap">
		<h1>🚀 راه‌اندازی دمو — قالب زرین</h1>

		<?php if ( $success ) : ?>
			<div class="notice notice-success is-dismissible"><p><strong>دمو با موفقیت راه‌اندازی شد!</strong> حالا <a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank">صفحه اصلی سایت</a> را ببینید.</p></div>
		<?php elseif ( $error ) : ?>
			<div class="notice notice-error is-dismissible"><p><strong>خطا:</strong> دمو قبلاً راه‌اندازی شده و نمی‌توان دوباره اجرا کرد.</p></div>
		<?php endif; ?>

		<div class="card" style="max-width:760px;padding:8px 26px 20px;">
			<h2>با یک کلیک همه این‌ها ساخته می‌شود:</h2>
			<ul style="list-style:disc;padding-inline-start:22px;">
				<li>برگه‌های <strong>خانه، وبلاگ، درباره ما و تماس با ما</strong> + تنظیم صفحه اصلی</li>
				<li><strong>منوی اصلی و منوی فوتر</strong> با لینک‌های آماده</li>
				<li><strong>۳ نوشته نمونه</strong> برای وبلاگ با تصویر شاخص</li>
				<?php if ( zarrin_is_woo() ) : ?>
					<li><strong>۴ محصول نمونه</strong> (انگشتر، گردن‌آویز، دستبند، گوشواره) با دسته‌بندی و تصویر</li>
				<?php else : ?>
					<li>محصولات نمونه (نیازمند فعال بودن افزونه <strong>ووکامرس</strong>)</li>
				<?php endif; ?>
				<li>تصاویر نمونه، اطلاعات تماس و تنظیمات پیشنهادی قالب</li>
			</ul>
			<p><em>⚠️ توجه: این عملیات محتوای نمونه اضافه می‌کند؛ برگه‌ها و نوشته‌های فعلی شما حذف نمی‌شوند. این دکمه فقط یک‌بار قابل اجراست.</em></p>
			<p>
				<?php if ( ! $done ) : ?>
					<a href="<?php echo esc_url( $url ); ?>" class="button button-primary button-hero" style="background:#c9a227;border-color:#a8841c;">🚀 راه‌اندازی دمو با یک کلیک</a>
				<?php else : ?>
					<p style="color:green;"><strong>✅ دمو قبلاً در این سایت راه‌اندازی شده است (<?php echo esc_html( zarrin_fa_digits( date_i18n( 'Y/m/d — H:i', (int) get_option( 'zarrin_demo_done' ) ) ) ); ?>)</strong></p>
				<?php endif; ?>
			</p>
		</div>
	</div>
	<?php
}

/** اعلان پیشنهاد راه‌اندازی دمو بعد از فعال‌سازی قالب */
function zarrin_demo_notice() {
	if ( get_option( 'zarrin_demo_done' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( ! $screen || ! in_array( $screen->id, array( 'dashboard', 'themes' ), true ) ) {
		return;
	}
	$url = wp_nonce_url( admin_url( 'admin-post.php?action=zarrin_demo_import' ), 'zarrin_demo_import' );
	?>
	<div class="notice notice-info is-dismissible" style="border-inline-start-color:#c9a227;">
		<p>
			<strong>🎉 قالب زرین فعال شد!</strong>
			برای اینکه سایت دقیقاً مثل دمو شود، یک‌بار دکمه زیر را بزنید:
			<a href="<?php echo esc_url( $url ); ?>" class="button button-primary" style="margin:0 8px;background:#c9a227;border-color:#a8841c;">راه‌اندازی دمو با یک کلیک</a>
			یا از منوی <a href="<?php echo esc_url( admin_url( 'themes.php?page=zarrin-demo' ) ); ?>">نمایش ← راه‌اندازی دمو زرین</a>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'zarrin_demo_notice' );

/** هندلر کلیک دکمه */
function zarrin_demo_handle() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'دسترسی ندارید.' );
	}
	check_admin_referer( 'zarrin_demo_import' );

	$result = zarrin_run_demo_import();
	$status = is_wp_error( $result ) ? 'error' : 'done';

	wp_safe_redirect( add_query_arg( 'zarrin_demo', $status, admin_url( 'themes.php?page=zarrin-demo' ) ) );
	exit;
}
add_action( 'admin_post_zarrin_demo_import', 'zarrin_demo_handle' );

/* =========================================================
 * موتور ایمپورت
 * ======================================================= */

/**
 * ساخت پیوست از تصاویر همراه قالب.
 *
 * @param string $filename نام فایل در assets/img قالب.
 * @param string $title    عنوان تصویر.
 * @return int شناسه پیوست.
 */
function zarrin_demo_attach( $filename, $title ) {
	$upload = wp_upload_dir();
	$dest   = $upload['basedir'] . '/' . $filename;
	if ( ! file_exists( $dest ) ) {
		// phpcs:ignore WordPress.PHP.NoSilencedErrors
		@copy( get_template_directory() . '/assets/img/' . $filename, $dest );
	}
	if ( ! file_exists( $dest ) ) {
		return 0;
	}
	$attach_id = wp_insert_attachment(
		array(
			'post_mime_type' => 'image/jpeg',
			'post_title'     => $title,
			'post_status'    => 'inherit',
		),
		$dest,
		0
	);
	if ( is_wp_error( $attach_id ) || ! $attach_id ) {
		return 0;
	}
	if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
		require_once ABSPATH . 'wp-admin/includes/image.php';
	}
	wp_update_attachment_metadata( $attach_id, wp_generate_attachment_metadata( $attach_id, $dest ) );
	return (int) $attach_id;
}

/** ساخت برگه در صورت نبود و بازگرداندن شناسه */
function zarrin_demo_page_id( $title, $content = '' ) {
	$existing = get_page_by_path( sanitize_title( $title ) );
	if ( $existing ) {
		return (int) $existing->ID;
	}
	return (int) wp_insert_post(
		array(
			'post_title'   => $title,
			'post_content' => $content,
			'post_status'  => 'publish',
			'post_type'    => 'page',
		)
	);
}

/**
 * اجرای راه‌اندازی دمو.
 *
 * @return true|WP_Error
 */
function zarrin_run_demo_import() {

	if ( get_option( 'zarrin_demo_done' ) ) {
		return new WP_Error( 'zarrin_demo_done', 'دمو قبلاً راه‌اندازی شده است.' );
	}

	/* --- ۱) تصاویر نمونه --- */
	$imgs = array(
		'hero.jpg'     => 'گردنبند طلای زرین',
		'about.jpg'    => 'کارگاه طلاسازی',
		'product-1.jpg'=> 'انگشتر طلا',
		'product-2.jpg'=> 'گردن‌آویز طلا',
		'product-3.jpg'=> 'دستبند طلا',
		'product-4.jpg'=> 'گوشواره طلا',
	);
	$atts = array();
	foreach ( $imgs as $file => $title ) {
		$atts[ $file ] = zarrin_demo_attach( $file, $title );
	}

	/* --- ۲) برگه‌ها --- */
	$home_id    = zarrin_demo_page_id( 'خانه' );
	$blog_id    = zarrin_demo_page_id( 'وبلاگ' );
	$about_id   = zarrin_demo_page_id(
		'درباره ما',
		"طلافروشی زرین با بیش از دو دهه تجربه، عرضه‌کننده انواع طلا و جواهر با ضمانت اصالت و عیار است.\n\nتمام محصولات ما دارای مهر و شناسنامه معتبر هستند و امکان معاوضه و بازخرید تضمینی نیز فراهم است. برای مشاوره خرید با کارشناسان ما تماس بگیرید."
	);
	$contact_id = zarrin_demo_page_id(
		'تماس با ما',
		"برای دریافت مشاوره خرید، استعلام قیمت روز یا سفارش ساخت اختصاصی با ما در تماس باشید.\n\nتلفن: ۰۲۱-۳۳۴۴۵۵۶۶\nموبایل: ۰۹۱۲۳۴۵۶۷۸۹\nآدرس: تهران، بازار بزرگ، سرای امیر، پلاک ۱۲\nساعات کاری: شنبه تا پنجشنبه — ۱۰ صبح تا ۸ شب"
	);

	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $home_id );
	update_option( 'page_for_posts', $blog_id );

	/* --- ۳) نوشته‌های وبلاگ --- */
	$cat_id = 0;
	$term   = term_exists( 'مجله طلا', 'category' );
	if ( $term ) {
		$cat_id = (int) ( is_array( $term ) ? $term['term_id'] : $term );
	} else {
		$new    = wp_insert_term( 'مجله طلا', 'category', array( 'description' => 'راهنمای خرید و دانش طلا' ) );
		$cat_id = is_wp_error( $new ) ? 0 : (int) $new['term_id'];
	}

	$posts = array(
		array(
			'title'   => 'راهنمای کامل خرید طلای ۱۸ عیار برای اولین‌بارها',
			'content' => "قبل از خرید طلا باید به عیار، وزن، اجرت و قیمت روز توجه کرد.\n\nعیار ۱۸ یعنی ۷۵ درصد خالص طلا و مابقی آلیاژ است که استحکام زیورآلات را تضمین می‌کند. همیشه برگه شناسنامه و مهر عیار روی محصول را بررسی کنید و قیمت روز گرم طلا را قبل از خرید از فروشگاه بپرسید.",
			'img'     => 'about.jpg',
		),
		array(
			'title'   => 'چگونه اصالت طلا را تشخیص دهیم؟ ۷ نشانه مهم',
			'content' => "از مهر عیار تا تست مغناطیس و برگه شناسنامه؛ نشانه‌هایی که خرید طلا را برای شما امن می‌کند.\n\nتوصیه می‌کنیم فقط از فروشگاه‌های معتبر خرید کنید و فاکتور رسمی با ذکر وزن، عیار و اجرت دریافت نمایید.",
			'img'     => 'product-2.jpg',
		),
		array(
			'title'   => 'معاوضه طلا؛ نکاتی که قبل از معامله باید بدانید',
			'content' => "معاوضه طلا با طلا یکی از مزایای خرید طلاست، اما شرایطی دارد که بهتر است از قبل بدانید.\n\nدر معاوضه، اجرت ساخت قبلی محاسبه نمی‌شود و اجرت مدل جدید جداگانه محاسبه می‌گردد؛ پس قبل از معامله، قیمت روز و اجرت را استعلام کنید.",
			'img'     => 'hero.jpg',
		),
	);

	foreach ( $posts as $post ) {
		$pid = wp_insert_post(
			array(
				'post_title'    => $post['title'],
				'post_content'  => $post['content'],
				'post_status'   => 'publish',
				'post_type'     => 'post',
				'comment_status'=> 'open',
			)
		);
		if ( $pid && ! is_wp_error( $pid ) ) {
			if ( $cat_id ) {
				wp_set_post_categories( $pid, array( $cat_id ) );
			}
			if ( ! empty( $atts[ $post['img'] ] ) ) {
				set_post_thumbnail( $pid, $atts[ $post['img'] ] );
			}
		}
	}

	/* --- ۴) محصولات (در صورت فعال بودن ووکامرس) --- */
	if ( zarrin_is_woo() && post_type_exists( 'product' ) ) {

		// واحد پول تومان و قالب‌بندی مناسب فروشگاه‌های ایرانی.
		update_option( 'woocommerce_currency', 'IRT' );
		update_option( 'woocommerce_currency_pos', 'right_space' );
		update_option( 'woocommerce_price_num_decimals', '0' );
		update_option( 'woocommerce_price_thousand_sep', '٬' );
		update_option( 'woocommerce_price_decimal_sep', '.' );

		$cats = array( 'انگشتر', 'گردنبند و آویز', 'دستبند', 'گوشواره' );
		$cat_ids = array();
		foreach ( $cats as $cat ) {
			$term = term_exists( $cat, 'product_cat' );
			if ( $term ) {
				$cat_ids[ $cat ] = (int) ( is_array( $term ) ? $term['term_id'] : $term );
			} else {
				$new = wp_insert_term( $cat, 'product_cat' );
				$cat_ids[ $cat ] = is_wp_error( $new ) ? 0 : (int) $new['term_id'];
			}
		}

		$products = array(
			array(
				'title' => 'انگشتر الماس‌نشان ظریف ۱۸ عیار',
				'desc'  => 'انگشتر ظریف زنانه طلای ۱۸ عیار با نگین الماس درخشان؛ مناسب هدیه و استفاده روزمره.',
				'cat'   => 'انگشتر',
				'reg'   => 62400000,
				'sale'  => 58900000,
				'img'   => 'product-1.jpg',
			),
			array(
				'title' => 'گردن‌آویز قلب نگین‌دار ۱۸ عیار',
				'desc'  => 'گردن‌آویز قلب طلای ۱۸ عیار با نگین‌های ریز کریستال؛ ظرافتی ماندگار برای هر لحظه.',
				'cat'   => 'گردنبند و آویز',
				'reg'   => 81500000,
				'sale'  => 0,
				'img'   => 'product-2.jpg',
			),
			array(
				'title' => 'دستبند ایتالیایی طرح سنتی',
				'desc'  => 'دستبند طلای ۱۸ عیار با حکاکی سنتی و پرداخت ایتالیایی؛ سنگین و باشکوه.',
				'cat'   => 'دستبند',
				'reg'   => 112000000,
				'sale'  => 0,
				'img'   => 'product-3.jpg',
			),
			array(
				'title' => 'گوشواره آویز نگین‌دار ظریف',
				'desc'  => 'گوشواره آویز طلای ۱۸ عیار با نگین‌های براق؛ سبک، شیک و راحت.',
				'cat'   => 'گوشواره',
				'reg'   => 49800000,
				'sale'  => 0,
				'img'   => 'product-4.jpg',
			),
		);

		foreach ( $products as $product ) {
			$pid = wp_insert_post(
				array(
					'post_title'   => $product['title'],
					'post_content' => $product['desc'],
					'post_excerpt' => $product['desc'],
					'post_status'  => 'publish',
					'post_type'    => 'product',
				)
			);
			if ( ! $pid || is_wp_error( $pid ) ) {
				continue;
			}
			if ( ! empty( $cat_ids[ $product['cat'] ] ) ) {
				wp_set_object_terms( $pid, array( $cat_ids[ $product['cat'] ] ), 'product_cat' );
			}
			update_post_meta( $pid, '_regular_price', $product['reg'] );
			update_post_meta( $pid, '_price', $product['sale'] > 0 ? $product['sale'] : $product['reg'] );
			update_post_meta( $pid, '_sale_price', $product['sale'] > 0 ? $product['sale'] : '' );
			update_post_meta( $pid, '_stock_status', 'instock' );
			update_post_meta( $pid, '_manage_stock', 'no' );
			update_post_meta( $pid, '_virtual', 'no' );
			update_post_meta( $pid, '_downloadable', 'no' );
			// علامت‌گذاری به‌عنوان ویژه.
			wp_set_object_terms( $pid, 'featured', 'product_visibility' );
			if ( ! empty( $atts[ $product['img'] ] ) ) {
				set_post_thumbnail( $pid, $atts[ $product['img'] ] );
			}
		}
	}

	/* --- ۵) منوها --- */
	$menu_name = 'منوی اصلی زرین';
	$existing  = term_exists( $menu_name, 'nav_menu' );
	if ( $existing ) {
		$primary_id = (int) ( is_array( $existing ) ? $existing['term_id'] : $existing );
	} else {
		$primary_id = (int) wp_create_nav_menu( $menu_name );
	}
	if ( $primary_id && ! is_wp_error( $primary_id ) ) {
		$items = array(
			array( 'خانه', $home_id ),
			array( 'وبلاگ', $blog_id ),
			array( 'درباره ما', $about_id ),
			array( 'تماس با ما', $contact_id ),
		);
		if ( zarrin_is_woo() && get_option( 'woocommerce_shop_page_id' ) ) {
			array_splice( $items, 1, 0, array( array( 'فروشگاه', (int) get_option( 'woocommerce_shop_page_id' ) ) ) );
		}
		$has_items = wp_get_nav_menu_items( $primary_id );
		if ( empty( $has_items ) ) {
			$pos = 1;
			foreach ( $items as $item ) {
				wp_update_nav_menu_item(
					$primary_id,
					0,
					array(
						'menu-item-title'     => $item[0],
						'menu-item-object'    => 'page',
						'menu-item-object-id' => $item[1],
						'menu-item-type'      => 'post_type',
						'menu-item-status'    => 'publish',
						'menu-item-position'  => $pos,
					)
				);
				$pos++;
			}
		}
		$locations           = get_theme_mod( 'nav_menu_locations', array() );
		$locations['primary'] = $primary_id;
		$locations['footer']  = $primary_id;
		set_theme_mod( 'nav_menu_locations', $locations );
	}

	/* --- ۶) تنظیمات قالب (پیش‌فرض‌های دمو) --- */
	set_theme_mod( 'zarrin_phone', '۰۲۱-۳۳۴۴۵۵۶۶' );
	set_theme_mod( 'zarrin_mobile', '۰۹۱۲۳۴۵۶۷۸۹' );
	set_theme_mod( 'zarrin_email', 'info@zarrin-shop.ir' );
	set_theme_mod( 'zarrin_address', 'تهران، بازار بزرگ، سرای امیر، پلاک ۱۲' );
	set_theme_mod( 'zarrin_hours', 'شنبه تا پنجشنبه — ۱۰ صبح تا ۸ شب' );
	set_theme_mod( 'zarrin_instagram', 'https://instagram.com/' );
	set_theme_mod( 'zarrin_telegram', 'https://t.me/' );
	set_theme_mod( 'zarrin_whatsapp', 'https://wa.me/989123456789' );
	set_theme_mod( 'zarrin_footer_about', 'طلافروشی زرین؛ عرضه‌کننده انواع طلا و جواهر با ضمانت اصالت، قیمت لحظه‌ای روز و امکان معاوضه. خرید شما را با خیال راحت انجام دهید.' );

	/* --- ۷) تنظیمات کلی وردپرس --- */
	update_option( 'blogname', 'طلا و جواهر زرین' );
	update_option( 'blogdescription', 'فروشگاه آنلاین طلا و جواهر با قیمت لحظه‌ای' );
	update_option( 'permalink_structure', '/%postname%/' );
	flush_rewrite_rules();

	update_option( 'zarrin_demo_done', time() );

	return true;
}
