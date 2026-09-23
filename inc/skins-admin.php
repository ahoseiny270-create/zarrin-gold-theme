<?php
/**
 * مدیریت دموها (پوسته‌های رنگ) در پیشخوان — قالب زرین
 *
 * - انتخاب سریع دمو از نوار بالای پیشخوان (Admin Bar)
 * - کارت‌های تصویری دموها در صفحه «نمایش ← راه‌اندازی دمو»
 * - نمایش نسخه قالب برای تشخیص نصب قدیمی
 *
 * @package Zarrin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* =========================================================
 * ۱) نشانی‌های تغییر دمو
 * ======================================================= */

/** نشانی امن فعال‌سازی یک دمو */
function zarrin_skin_switch_url( $skin ) {
	return wp_nonce_url(
		admin_url( 'admin-post.php?action=zarrin_set_skin&skin=' . rawurlencode( $skin ) ),
		'zarrin_set_skin'
	);
}

/** نشانی امن بازنشانی «راه‌اندازی دمو» تا بتوان دوباره اجرا کرد */
function zarrin_demo_reset_url() {
	return wp_nonce_url(
		admin_url( 'admin-post.php?action=zarrin_reset_demo' ),
		'zarrin_reset_demo'
	);
}

/* =========================================================
 * ۲) پردازشگرها
 * ======================================================= */

/** فعال‌سازی دموی انتخاب‌شده */
function zarrin_set_skin_handler() {

	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( 'برای تغییر دمو دسترسی ندارید.' );
	}
	check_admin_referer( 'zarrin_set_skin' );

	$skin  = isset( $_GET['skin'] ) ? sanitize_key( wp_unslash( $_GET['skin'] ) ) : '';
	$skins = zarrin_skins();

	if ( ! isset( $skins[ $skin ] ) ) {
		wp_die( 'دموی انتخاب‌شده معتبر نیست.' );
	}

	set_theme_mod( 'zarrin_demo_skin', $skin );

	$back = wp_get_referer();
	$back = $back ? $back : admin_url( 'themes.php?page=zarrin-demo' );

	wp_safe_redirect( add_query_arg( 'zarrin_skin_done', $skin, $back ) );
	exit;
}
add_action( 'admin_post_zarrin_set_skin', 'zarrin_set_skin_handler' );

/** بازنشانی پرچم راه‌اندازی دمو (اجازه اجرای دوباره) */
function zarrin_reset_demo_handler() {

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'برای این کار دسترسی ندارید.' );
	}
	check_admin_referer( 'zarrin_reset_demo' );

	delete_option( 'zarrin_demo_done' );

	wp_safe_redirect( admin_url( 'themes.php?page=zarrin-demo&zarrin_demo=reset' ) );
	exit;
}
add_action( 'admin_post_zarrin_reset_demo', 'zarrin_reset_demo_handler' );

/* =========================================================
 * ۳) اعلان‌ها
 * ======================================================= */

/** اعلان موفقیت تغییر دمو */
function zarrin_skin_admin_notice() {

	if ( ! isset( $_GET['zarrin_skin_done'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return;
	}

	$skin  = sanitize_key( wp_unslash( $_GET['zarrin_skin_done'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
	$skins = zarrin_skins();

	if ( ! isset( $skins[ $skin ] ) ) {
		return;
	}
	?>
	<div class="notice notice-success is-dismissible">
		<p>
			<strong>🎨 دمو فعال شد:</strong> <?php echo esc_html( $skins[ $skin ]['name'] ); ?> (<?php echo esc_html( $skins[ $skin ]['demo'] ); ?>).
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank">مشاهده سایت</a>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'zarrin_skin_admin_notice' );

/* =========================================================
 * ۴) انتخاب سریع دمو از نوار بالای پیشخوان
 * ======================================================= */

/** افزودن منوی «دموهای زرین» به Admin Bar */
function zarrin_admin_bar_demos( $bar ) {

	if ( ! current_user_can( 'edit_theme_options' ) || ! $bar ) {
		return;
	}

	$active = zarrin_skin();

	$bar->add_node(
		array(
			'id'    => 'zarrin-demos',
			'title' => '🎨 دموهای زرین',
			'href'  => admin_url( 'themes.php?page=zarrin-demo' ),
		)
	);

	foreach ( zarrin_skins() as $slug => $info ) {
		$is_active = ( $slug === $active );
		$bar->add_node(
			array(
				'id'     => 'zarrin-demo-' . $slug,
				'parent' => 'zarrin-demos',
				'title'  => ( $is_active ? '✓ ' : '' ) . $info['name'] . ' — ' . $info['demo'],
				'href'   => $is_active
					? admin_url( 'customize.php?autofocus[section]=zarrin_skin_section' )
					: zarrin_skin_switch_url( $slug ),
			)
		);
	}

	$bar->add_node(
		array(
			'id'     => 'zarrin-demos-sep',
			'parent' => 'zarrin-demos',
			'title'  => '——',
		)
	);

	$bar->add_node(
		array(
			'id'     => 'zarrin-demos-customize',
			'parent' => 'zarrin-demos',
			'title'  => '⚙ تنظیمات دمو و ظاهر سایت',
			'href'   => admin_url( 'customize.php?autofocus[section]=zarrin_skin_section' ),
		)
	);
}
add_action( 'admin_bar_menu', 'zarrin_admin_bar_demos', 80 );

/* =========================================================
 * ۵) کارت‌های تصویری دموها (برای صفحه راه‌اندازی دمو)
 * ======================================================= */

/**
 * نمایش کارت‌های تصویری همه دموها با دکمه فعال‌سازی.
 *
 * @return string
 */
function zarrin_skins_cards_html() {

	$active = zarrin_skin();
	$out    = '<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(190px,1fr));gap:14px;margin-top:8px;">';

	foreach ( zarrin_skins() as $slug => $info ) {

		$is_active = ( $slug === $active );
		$thumb     = zarrin_skin_img_for( $slug, 'hero.jpg' );

		$out .= '<div style="background:' . ( $is_active ? '#fffaf0' : '#fff' ) . ';border:'
			. ( $is_active ? '2px solid #c9a227' : '1px solid #dcdcde' )
			. ';border-radius:12px;padding:10px;text-align:center;">';

		$out .= '<img src="' . esc_url( $thumb ) . '" alt="" style="width:100%;height:96px;object-fit:cover;border-radius:8px;display:block;">';

		$out .= '<div style="font-weight:700;margin:10px 0 2px;font-size:13px;line-height:1.6;">'
			. esc_html( $info['name'] ) . '</div>';

		$out .= '<div style="color:#666;font-size:11px;margin-bottom:9px;">'
			. esc_html( $info['demo'] ) . '</div>';

		if ( $is_active ) {
			$out .= '<span style="display:inline-block;background:#c9a227;color:#2d2305;border-radius:999px;'
				. 'padding:4px 12px;font-size:11px;font-weight:700;">دموی فعال</span>';
		} else {
			$out .= '<a class="button button-small" href="' . esc_url( zarrin_skin_switch_url( $slug ) ) . '">'
				. 'فعال‌سازی این دمو</a>';
		}

		$out .= '</div>';
	}

	$out .= '</div>';

	return $out;
}
