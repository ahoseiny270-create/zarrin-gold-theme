<?php
/**
 * مشخصات تخصصی طلا و محاسبه‌گر قیمت — قالب زرین
 *
 * - فیلدهای عیار، وزن، اجرت، شناسنامه، کد رهگیری و ساخت برای محصولات ووکامرس
 * - نمایش در صفحه محصول، کارت محصول، سبد خرید، تسویه، فاکتور و ایمیل
 * - محاسبه‌گر قیمت روز طلا بر پایه نرخ لحظه‌ای قالب
 * - داده ساخت‌یافته (Schema.org) مخصوص محصول طلا
 *
 * @package Zarrin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* =========================================================
 * ۱) مقادیر و برچسب‌ها
 * ======================================================= */

/** گزینه‌های عیار */
function zarrin_karat_options() {
	return array(
		'18'  => '۱۸ عیار (۷۵۰)',
		'21'  => '۲۱ عیار',
		'22'  => '۲۲ عیار (۹۱۶)',
		'24'  => '۲۴ عیار (۹۹۹)',
		'other' => 'سایر / مخراجی',
	);
}

/** گزینه‌های ساخت */
function zarrin_made_in_options() {
	return array(
		'iran'    => 'ایران',
		'italy'   => 'ایتالیا',
		'turkey'  => 'ترکیه',
		'other'   => 'سایر',
	);
}

/** خواندن مشخصات طلای یک محصول */
function zarrin_gold_meta( $product_id ) {
	return array(
		'karat'    => (string) get_post_meta( $product_id, '_zarrin_karat', true ),
		'weight'   => (string) get_post_meta( $product_id, '_zarrin_weight', true ),
		'wage'     => (string) get_post_meta( $product_id, '_zarrin_wage', true ),
		'profit'   => (string) get_post_meta( $product_id, '_zarrin_profit', true ),
		'cert'     => (bool) get_post_meta( $product_id, '_zarrin_cert', true ),
		'tracking' => (string) get_post_meta( $product_id, '_zarrin_tracking', true ),
		'made_in'  => (string) get_post_meta( $product_id, '_zarrin_made_in', true ),
	);
}

/** آیا محصول مشخصات طلا دارد؟ */
function zarrin_has_gold_meta( $product_id ) {
	$m = zarrin_gold_meta( $product_id );
	return ( '' !== $m['karat'] || '' !== $m['weight'] );
}

/** برچسب کوتاه عیار برای کارت محصول */
function zarrin_gold_badge_text( $product_id ) {
	$m     = zarrin_gold_meta( $product_id );
	$parts = array();
	if ( '' !== $m['weight'] ) {
		$parts[] = zarrin_fa_digits( $m['weight'] ) . ' گرم';
	}
	if ( '' !== $m['karat'] && 'other' !== $m['karat'] ) {
		$parts[] = 'عیار ' . zarrin_fa_digits( $m['karat'] );
	}
	return implode( ' • ', $parts );
}

/* =========================================================
 * ۲) فیلدهای پنل مدیریت محصول
 * ======================================================= */

/** افزودن جعبه «مشخصات طلا» */
function zarrin_gold_metabox() {
	add_meta_box(
		'zarrin_gold_box',
		'🏅 مشخصات طلا (قالب زرین)',
		'zarrin_gold_metabox_render',
		'product',
		'side',
		'high'
	);
}
add_action( 'add_meta_boxes', 'zarrin_gold_metabox' );

/** نمایش فرم جعبه */
function zarrin_gold_metabox_render( $post ) {

	$m = array(
		'karat'    => get_post_meta( $post->ID, '_zarrin_karat', true ),
		'weight'   => get_post_meta( $post->ID, '_zarrin_weight', true ),
		'wage'     => get_post_meta( $post->ID, '_zarrin_wage', true ),
		'profit'   => get_post_meta( $post->ID, '_zarrin_profit', true ),
		'cert'     => get_post_meta( $post->ID, '_zarrin_cert', true ),
		'tracking' => get_post_meta( $post->ID, '_zarrin_tracking', true ),
		'made_in'  => get_post_meta( $post->ID, '_zarrin_made_in', true ),
	);

	wp_nonce_field( 'zarrin_gold_save', 'zarrin_gold_nonce' );
	?>
	<p>
		<label for="zarrin_karat"><strong>عیار</strong></label><br>
		<select name="zarrin_karat" id="zarrin_karat" style="width:100%">
			<option value="">— انتخاب کنید —</option>
			<?php foreach ( zarrin_karat_options() as $val => $label ) : ?>
				<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $m['karat'], $val ); ?>>
					<?php echo esc_html( $label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
	</p>
	<p>
		<label for="zarrin_weight"><strong>وزن (گرم)</strong></label><br>
		<input type="text" inputmode="decimal" name="zarrin_weight" id="zarrin_weight"
			value="<?php echo esc_attr( $m['weight'] ); ?>" placeholder="مثلاً 3.45" style="width:100%">
	</p>
	<p>
		<label for="zarrin_wage"><strong>اجرت ساخت (درصد)</strong></label><br>
		<input type="text" inputmode="decimal" name="zarrin_wage" id="zarrin_wage"
			value="<?php echo esc_attr( $m['wage'] ); ?>" placeholder="مثلاً 12" style="width:100%">
	</p>
	<p>
		<label for="zarrin_profit"><strong>سود فروشگاه (درصد)</strong></label><br>
		<input type="text" inputmode="decimal" name="zarrin_profit" id="zarrin_profit"
			value="<?php echo esc_attr( $m['profit'] ); ?>" placeholder="خالی = پیش‌فرض تنظیمات" style="width:100%">
	</p>
	<p>
		<label for="zarrin_made_in"><strong>ساخت</strong></label><br>
		<select name="zarrin_made_in" id="zarrin_made_in" style="width:100%">
			<option value="">— انتخاب کنید —</option>
			<?php foreach ( zarrin_made_in_options() as $val => $label ) : ?>
				<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $m['made_in'], $val ); ?>>
					<?php echo esc_html( $label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
	</p>
	<p>
		<label for="zarrin_tracking"><strong>کد رهگیری / بارکد</strong></label><br>
		<input type="text" name="zarrin_tracking" id="zarrin_tracking"
			value="<?php echo esc_attr( $m['tracking'] ); ?>" style="width:100%">
	</p>
	<p>
		<label>
			<input type="checkbox" name="zarrin_cert" value="1" <?php checked( $m['cert'] ); ?>>
			<strong>دارای شناسنامه و مهر عیار</strong>
		</label>
	</p>
	<p style="color:#666;font-size:11px;line-height:1.9">
		این مشخصات در صفحه محصول، سبد خرید، تسویه حساب، فاکتور و ایمیل سفارش نمایش داده می‌شود
		و مبنای محاسبه‌گر قیمت روز طلا است.
	</p>
	<?php
}

/** ذخیره فیلدها */
function zarrin_gold_metabox_save( $post_id ) {

	if ( ! isset( $_POST['zarrin_gold_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['zarrin_gold_nonce'] ) ), 'zarrin_gold_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$karat   = isset( $_POST['zarrin_karat'] ) ? sanitize_key( wp_unslash( $_POST['zarrin_karat'] ) ) : '';
	$karats  = array_keys( zarrin_karat_options() );
	$karat   = in_array( $karat, $karats, true ) ? $karat : '';

	$made    = isset( $_POST['zarrin_made_in'] ) ? sanitize_key( wp_unslash( $_POST['zarrin_made_in'] ) ) : '';
	$mades   = array_keys( zarrin_made_in_options() );
	$made    = in_array( $made, $mades, true ) ? $made : '';

	$weight  = isset( $_POST['zarrin_weight'] ) ? zarrin_parse_fa_number( wp_unslash( $_POST['zarrin_weight'] ) ) : '';
	$wage    = isset( $_POST['zarrin_wage'] ) ? zarrin_parse_fa_number( wp_unslash( $_POST['zarrin_wage'] ) ) : '';
	$profit  = isset( $_POST['zarrin_profit'] ) ? zarrin_parse_fa_number( wp_unslash( $_POST['zarrin_profit'] ) ) : '';
	$track   = isset( $_POST['zarrin_tracking'] ) ? sanitize_text_field( wp_unslash( $_POST['zarrin_tracking'] ) ) : '';
	$cert    = isset( $_POST['zarrin_cert'] ) ? '1' : '';

	update_post_meta( $post_id, '_zarrin_karat', $karat );
	update_post_meta( $post_id, '_zarrin_made_in', $made );
	update_post_meta( $post_id, '_zarrin_tracking', $track );
	update_post_meta( $post_id, '_zarrin_cert', $cert );

	// اعداد: ۰ تا ۱۰۰۰۰۰۰ (وزن) و ۰ تا ۲۰۰ (درصدها)
	update_post_meta( $post_id, '_zarrin_weight', zarrin_clamp_number( $weight, 0, 1000000 ) );
	update_post_meta( $post_id, '_zarrin_wage', zarrin_clamp_number( $wage, 0, 200 ) );
	update_post_meta( $post_id, '_zarrin_profit', zarrin_clamp_number( $profit, 0, 200 ) );
}
add_action( 'save_post_product', 'zarrin_gold_metabox_save' );

/**
 * تبدیل عدد فارسی/عربی به عدد لاتین.
 *
 * @param string $value ورودی.
 * @return string
 */
function zarrin_parse_fa_number( $value ) {
	$value = str_replace( array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' ), range( 0, 9 ), (string) $value );
	$value = str_replace( array( '٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩' ), range( 0, 9 ), $value );
	$value = str_replace( array( '٫', '،', ',' ), '.', $value );
	$value = preg_replace( '/[^0-9.\-]/', '', $value );
	return ( '' === $value ) ? '' : $value;
}

/**
 * محدودکردن عدد به بازه مجاز.
 *
 * @param mixed $value مقدار.
 * @param float $min   کمینه.
 * @param float $max   بیشینه.
 * @return string
 */
function zarrin_clamp_number( $value, $min = 0, $max = PHP_INT_MAX ) {
	if ( '' === $value || ! is_numeric( $value ) ) {
		return '';
	}
	$num = (float) $value;
	$num = max( (float) $min, min( (float) $max, $num ) );
	return rtrim( rtrim( number_format( $num, 3, '.', '' ), '0' ), '.' );
}

/* =========================================================
 * ۳) نمایش در فروشگاه و صفحه محصول
 * ======================================================= */

/** جدول مشخصات در صفحه محصول */
function zarrin_gold_specs_html( $product_id ) {

	if ( ! zarrin_has_gold_meta( $product_id ) ) {
		return '';
	}
	$m     = zarrin_gold_meta( $product_id );
	$karats = zarrin_karat_options();
	$mades  = zarrin_made_in_options();
	$rows   = array();

	if ( '' !== $m['karat'] ) {
		$rows['عیار'] = isset( $karats[ $m['karat'] ] ) ? $karats[ $m['karat'] ] : $m['karat'];
	}
	if ( '' !== $m['weight'] ) {
		$rows['وزن'] = zarrin_fa_digits( $m['weight'] ) . ' گرم';
	}
	if ( '' !== $m['wage'] ) {
		$rows['اجرت ساخت'] = zarrin_fa_digits( $m['wage'] ) . '٪';
	}
	if ( '' !== $m['made_in'] && isset( $mades[ $m['made_in'] ] ) ) {
		$rows['ساخت'] = $mades[ $m['made_in'] ];
	}
	if ( '' !== $m['tracking'] ) {
		$rows['کد رهگیری'] = $m['tracking'];
	}

	if ( empty( $rows ) && ! $m['cert'] ) {
		return '';
	}

	ob_start();
	?>
	<div class="zgold-specs">
		<h3><?php zarrin_icon_e( 'shield', 20 ); ?> مشخصات فنی طلا</h3>
		<?php if ( $rows ) : ?>
			<table>
				<tbody>
					<?php foreach ( $rows as $label => $value ) : ?>
						<tr>
							<th><?php echo esc_html( $label ); ?></th>
							<td><?php echo esc_html( $value ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
		<?php if ( $m['cert'] ) : ?>
			<span class="zgold-cert">
				<?php zarrin_icon_e( 'check', 16 ); ?>
				دارای شناسنامه و مهر عیار معتبر
			</span>
		<?php endif; ?>
	</div>
	<?php
	return ob_get_clean();
}

/** افزودن جدول مشخصات به صفحه محصول */
function zarrin_gold_single_specs() {
	global $product;
	if ( ! $product instanceof WC_Product ) {
		return;
	}
	echo zarrin_gold_specs_html( $product->get_id() ); // phpcs:ignore WordPress.Security.EscapeOutput
}
add_action( 'woocommerce_single_product_summary', 'zarrin_gold_single_specs', 24 );

/** یادداشت قیمت روز در صفحه محصول */
function zarrin_gold_live_note() {
	global $product;
	if ( ! $product instanceof WC_Product ) {
		return;
	}
	$m = zarrin_gold_meta( $product->get_id() );
	if ( '' === $m['weight'] ) {
		return;
	}
	$prices = zarrin_get_prices();
	$key    = ( '24' === $m['karat'] ) ? 'p24' : 'p18';
	$unit   = $prices[ $key ]['value'];

	if ( ! is_numeric( $unit ) || $unit <= 0 ) {
		return;
	}
	?>
	<div class="zgold-live-note">
		<?php zarrin_icon_e( 'coin', 18 ); ?>
		<span>
			ارزش طلای این کالا بر اساس نرخ روز: <b><?php echo esc_html( zarrin_money( round( (float) $unit * (float) $m['weight'] ) ) ); ?> تومان</b>
			(<?php echo esc_html( zarrin_fa_digits( $m['weight'] ) ); ?> گرم × نرخ گرم)
		</span>
	</div>
	<?php
}
add_action( 'woocommerce_single_product_summary', 'zarrin_gold_live_note', 26 );

/** برچسب وزن/عیار روی تصویر محصولات در آرشیو */
function zarrin_gold_loop_badge() {
	global $product;
	if ( ! $product instanceof WC_Product ) {
		return;
	}
	$text = zarrin_gold_badge_text( $product->get_id() );
	if ( $text ) {
		echo '<span class="zgold-badge">' . esc_html( $text ) . '</span>';
	}
}
add_action( 'woocommerce_before_shop_loop_item_title', 'zarrin_gold_loop_badge', 15 );

/* =========================================================
 * ۴) سبد خرید، تسویه، سفارش و ایمیل
 * ======================================================= */

/** نمایش مشخصات طلا در سبد خرید و تسویه */
function zarrin_gold_cart_item_meta( $item_data, $cart_item ) {

	$pid = isset( $cart_item['product_id'] ) ? (int) $cart_item['product_id'] : 0;
	if ( ! $pid ) {
		return $item_data;
	}
	$m = zarrin_gold_meta( $pid );
	if ( '' !== $m['weight'] ) {
		$item_data[] = array(
			'key'   => 'وزن',
			'value' => zarrin_fa_digits( $m['weight'] ) . ' گرم',
		);
	}
	if ( '' !== $m['karat'] && 'other' !== $m['karat'] ) {
		$item_data[] = array(
			'key'   => 'عیار',
			'value' => zarrin_fa_digits( $m['karat'] ),
		);
	}
	if ( $m['cert'] ) {
		$item_data[] = array(
			'key'   => 'شناسنامه',
			'value' => 'دارد',
		);
	}
	return $item_data;
}
add_filter( 'woocommerce_get_item_data', 'zarrin_gold_cart_item_meta', 10, 2 );

/** ذخیره مشخصات طلا در آیتم سفارش */
function zarrin_gold_order_item_meta( $item, $cart_item_key, $values, $order ) {

	$pid = isset( $values['product_id'] ) ? (int) $values['product_id'] : 0;
	if ( ! $pid ) {
		return;
	}
	$m = zarrin_gold_meta( $pid );
	if ( '' !== $m['weight'] ) {
		$item->add_meta_data( 'وزن (گرم)', zarrin_fa_digits( $m['weight'] ) );
	}
	if ( '' !== $m['karat'] && 'other' !== $m['karat'] ) {
		$item->add_meta_data( 'عیار', zarrin_fa_digits( $m['karat'] ) );
	}
	if ( '' !== $m['wage'] ) {
		$item->add_meta_data( 'اجرت ساخت', zarrin_fa_digits( $m['wage'] ) . '٪' );
	}
	if ( '' !== $m['tracking'] ) {
		$item->add_meta_data( 'کد رهگیری', $m['tracking'] );
	}
	if ( $m['cert'] ) {
		$item->add_meta_data( 'شناسنامه و مهر عیار', 'دارد' );
	}
}
add_action( 'woocommerce_checkout_create_order_line_item', 'zarrin_gold_order_item_meta', 10, 4 );

/** نمایش در پیشخوان سفارش و ایمیل‌ها */
function zarrin_gold_order_item_display( $item_id, $item, $order, $plain_text = false ) {

	foreach ( $item->get_formatted_meta_data( '_' ) as $meta ) {
		if ( $plain_text ) {
			echo "\n" . wp_strip_all_tags( $meta->display_key ) . ': ' . wp_strip_all_tags( $meta->display_value );
		} else {
			echo '<div style="font-size:12px;color:#666">' . esc_html( $meta->display_key ) . ': ' . esc_html( wp_strip_all_tags( $meta->display_value ) ) . '</div>';
		}
	}
}
add_action( 'woocommerce_order_item_meta_end', 'zarrin_gold_order_item_display', 10, 4 );

/* =========================================================
 * ۵) محاسبه‌گر قیمت روز طلا
 * ======================================================= */

/**
 * خروجی محاسبه‌گر.
 *
 * @param array $args پارامترها (weight, karat, wage).
 * @return string
 */
function zarrin_gold_calc_html( $args = array() ) {

	$args = wp_parse_args(
		$args,
		array(
			'weight'  => '',
			'karat'   => '18',
			'wage'    => '',
			'product' => 0,
		)
	);

	$prices = zarrin_get_prices();
	$profit = zarrin_get( 'zarrin_default_profit', 7 );
	$profit = is_numeric( $profit ) ? (float) $profit : 7;

	ob_start();
	?>
	<div class="zcalc" data-zcalc
		data-p18="<?php echo esc_attr( is_numeric( $prices['p18']['value'] ) ? (float) $prices['p18']['value'] : 0 ); ?>"
		data-p24="<?php echo esc_attr( is_numeric( $prices['p24']['value'] ) ? (float) $prices['p24']['value'] : 0 ); ?>"
		data-profit="<?php echo esc_attr( $profit ); ?>"
		data-updated="<?php echo esc_attr( $prices['_updated'] ); ?>">

		<h3 class="zcalc-title"><?php zarrin_icon_e( 'coin', 20 ); ?> محاسبه‌گر قیمت روز طلا</h3>
		<p class="zcalc-sub">قیمت تقریبی را بر اساس نرخ لحظه‌ای بازار، وزن، اجرت و سود فروشگاه محاسبه کنید.</p>

		<div class="zcalc-grid">
			<div class="zcalc-field">
				<label for="zcalc-weight">وزن (گرم)</label>
				<input type="text" inputmode="decimal" id="zcalc-weight" data-zcalc-weight
					value="<?php echo esc_attr( zarrin_fa_digits( $args['weight'] ) ); ?>" placeholder="۳٫۵">
			</div>
			<div class="zcalc-field">
				<label for="zcalc-karat">عیار</label>
				<select id="zcalc-karat" data-zcalc-karat>
					<option value="18" <?php selected( '18', $args['karat'] ); ?>>۱۸ عیار</option>
					<option value="21" <?php selected( '21', $args['karat'] ); ?>>۲۱ عیار</option>
					<option value="22" <?php selected( '22', $args['karat'] ); ?>>۲۲ عیار</option>
					<option value="24" <?php selected( '24', $args['karat'] ); ?>>۲۴ عیار</option>
				</select>
			</div>
			<div class="zcalc-field">
				<label for="zcalc-wage">اجرت ساخت (٪)</label>
				<input type="text" inputmode="decimal" id="zcalc-wage" data-zcalc-wage
					value="<?php echo esc_attr( zarrin_fa_digits( $args['wage'] ) ); ?>" placeholder="۱۲">
			</div>
			<div class="zcalc-field">
				<label for="zcalc-profit">سود فروشگاه (٪)</label>
				<input type="text" inputmode="decimal" id="zcalc-profit" data-zcalc-profit
					value="<?php echo esc_attr( zarrin_fa_digits( $profit ) ); ?>">
			</div>
		</div>

		<div class="zcalc-out">
			<span class="zcalc-label">
				قیمت تقریبی — <span data-zcalc-updated><?php echo esc_html( $prices['_updated'] ); ?></span>
			</span>
			<span class="zcalc-value"><span data-zcalc-result>—</span> <small>تومان</small></span>
		</div>

		<p class="zcalc-steps">
			فرمول: <b>(وزن × نرخ گرم روز) × (۱ + اجرت ٪ + سود ٪)</b> — نرخ گرم بر پایه
			<?php echo esc_html( $prices['_live'] ? 'قیمت لحظه‌ای بازار' : 'آخرین نرخ ثبت‌شده' ); ?> محاسبه می‌شود.
			این عدد تقریبی است؛ قیمت نهایی در فاکتور فروشگاه قطعی می‌شود.
		</p>
	</div>
	<?php
	return ob_get_clean();
}

/** شورت‌کد محاسبه‌گر */
function zarrin_gold_calc_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'weight' => '',
			'karat'  => '18',
			'wage'   => '',
		),
		$atts,
		'zarrin_gold_calc'
	);
	return zarrin_gold_calc_html( $atts );
}
add_shortcode( 'zarrin_gold_calc', 'zarrin_gold_calc_shortcode' );

/** افزودن خودکار محاسبه‌گر به صفحه محصول */
function zarrin_gold_auto_calc() {
	if ( ! zarrin_get( 'zarrin_calc_enable', true ) ) {
		return;
	}
	global $product;
	if ( ! $product instanceof WC_Product ) {
		return;
	}
	$m = zarrin_gold_meta( $product->get_id() );
	echo zarrin_gold_calc_html( // phpcs:ignore WordPress.Security.EscapeOutput
		array(
			'weight' => $m['weight'],
			'karat'  => ( '' !== $m['karat'] && 'other' !== $m['karat'] ) ? $m['karat'] : '18',
			'wage'   => $m['wage'],
		)
	);
}
add_action( 'woocommerce_after_single_product_summary', 'zarrin_gold_auto_calc', 12 );

/* =========================================================
 * ۶) داده ساخت‌یافته (Schema.org)
 * ======================================================= */

/** داده ساخت‌یافته محصول طلا */
function zarrin_gold_schema() {

	if ( ! function_exists( 'is_product' ) || ! is_product() ) {
		return;
	}
	global $product;
	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$m    = zarrin_gold_meta( $product->get_id() );
	$data = array(
		'@context'    => 'https://schema.org',
		'@type'       => 'Product',
		'name'        => get_the_title( $product->get_id() ),
		'sku'         => $product->get_sku() ? $product->get_sku() : (string) $product->get_id(),
		'description' => wp_strip_all_tags( $product->get_short_description() ? $product->get_short_description() : $product->get_description() ),
		'brand'       => array(
			'@type' => 'Brand',
			'name'  => get_bloginfo( 'name' ),
		),
		'offers'      => array(
			'@type'         => 'Offer',
			'price'         => $product->get_price() ? $product->get_price() : '0',
			'priceCurrency' => 'IRR',
			'availability'  => $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
			'url'           => get_permalink( $product->get_id() ),
		),
	);

	if ( has_post_thumbnail( $product->get_id() ) ) {
		$data['image'] = get_the_post_thumbnail_url( $product->get_id(), 'large' );
	}

	$props = array();
	if ( '' !== $m['karat'] && 'other' !== $m['karat'] ) {
		$props[] = array(
			'@type' => 'PropertyValue',
			'name'  => 'عیار',
			'value' => zarrin_fa_digits( $m['karat'] ),
		);
		$data['material'] = 'طلا ' . zarrin_fa_digits( $m['karat'] ) . ' عیار';
	}
	if ( '' !== $m['weight'] ) {
		$props[] = array(
			'@type' => 'PropertyValue',
			'name'  => 'وزن',
			'value' => zarrin_fa_digits( $m['weight'] ) . ' گرم',
		);
		$data['weight'] = array(
			'@type'    => 'QuantitativeValue',
			'value'    => (float) $m['weight'],
			'unitCode' => 'GRM',
		);
	}
	if ( '' !== $m['wage'] ) {
		$props[] = array(
			'@type' => 'PropertyValue',
			'name'  => 'اجرت ساخت',
			'value' => zarrin_fa_digits( $m['wage'] ) . ' درصد',
		);
	}
	if ( $props ) {
		$data['additionalProperty'] = $props;
	}

	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}
add_action( 'wp_head', 'zarrin_gold_schema', 20 );

/** داده ساخت‌یافته کسب‌وکار محلی (فروشگاه طلا) */
function zarrin_localbusiness_schema() {

	$data = array(
		'@context'    => 'https://schema.org',
		'@type'       => 'JewelryStore',
		'name'        => get_bloginfo( 'name' ),
		'description' => get_bloginfo( 'description' ),
		'url'         => home_url( '/' ),
		'telephone'   => zarrin_get( 'zarrin_phone', '' ),
		'openingHours' => zarrin_get( 'zarrin_hours', '' ),
		'address'     => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => zarrin_get( 'zarrin_address', '' ),
			'addressCountry'  => 'IR',
		),
	);

	$socials = array_filter(
		array(
			zarrin_get( 'zarrin_instagram' ),
			zarrin_get( 'zarrin_telegram' ),
		)
	);
	if ( $socials ) {
		$data['sameAs'] = array_values( $socials );
	}

	$logo = get_theme_mod( 'custom_logo' );
	if ( $logo ) {
		$src = wp_get_attachment_image_url( $logo, 'full' );
		if ( $src ) {
			$data['logo'] = $src;
		}
	}

	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}
add_action( 'wp_head', 'zarrin_localbusiness_schema', 21 );
