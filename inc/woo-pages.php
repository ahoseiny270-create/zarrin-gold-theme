<?php
/**
 * صفحات کاربری و ووکامرس — قالب زرین
 *
 * - فرم ورود/ثبت‌نام فارسی با تب‌بندی و اعتبارسنجی موبایل  ([zarrin_auth])
 * - نشان‌های اعتماد در سبد خرید و تسویه حساب
 * - فیلد کد ملی و الزامی‌بودن موبایل در تسویه حساب (لازم برای فاکتور طلا)
 * - بهبود صفحه پرداخت، ثبت نهایی خرید و فاکتور قابل چاپ و اشتراک‌گذاری
 *
 * @package Zarrin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* =========================================================
 * ۱) فرم ورود و ثبت‌نام
 * ======================================================= */

/** تبدیل ارقام فارسی به لاتین و پاک‌سازی شماره موبایل */
function zarrin_normalize_mobile( $value ) {
	$value = str_replace( array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' ), range( 0, 9 ), (string) $value );
	$value = str_replace( array( '٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩' ), range( 0, 9 ), $value );
	$value = preg_replace( '/[^0-9]/', '', $value );
	if ( strlen( $value ) === 10 && 0 === strpos( $value, '9' ) ) {
		$value = '0' . $value;
	}
	if ( 12 === strlen( $value ) && 0 === strpos( $value, '98' ) ) {
		$value = '0' . substr( $value, 2 );
	}
	return $value;
}

/** آیا موبایل ایرانی معتبر است؟ */
function zarrin_is_valid_mobile( $value ) {
	return (bool) preg_match( '/^09\d{9}$/', zarrin_normalize_mobile( $value ) );
}

/** آیا کد ملی معتبر است؟ (الگوریتم رسمی) */
function zarrin_is_valid_national_id( $code ) {

	$code = zarrin_normalize_mobile( $code ); // فقط ارقام را نگه می‌دارد.
	if ( strlen( $code ) !== 10 || '0000000000' === $code ) {
		return false;
	}
	$sum = 0;
	for ( $i = 0; $i < 9; $i++ ) {
		$sum += ( (int) $code[ $i ] ) * ( 10 - $i );
	}
	$r = $sum % 11;
	$check = (int) $code[9];
	return ( $r < 2 ) ? ( $check === $r ) : ( $check === 11 - $r );
}

/** اعتبارسنجی فرم ثبت‌نام */
function zarrin_validate_registration( $errors, $username, $email ) {

	if ( isset( $_POST['zarrin_mobile'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		$mobile = zarrin_normalize_mobile( wp_unslash( $_POST['zarrin_mobile'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
		if ( '' === $mobile ) {
			$errors->add( 'zarrin_mobile_empty', 'شماره موبایل الزامی است.' );
		} elseif ( ! zarrin_is_valid_mobile( $mobile ) ) {
			$errors->add( 'zarrin_mobile_invalid', 'شماره موبایل معتبر نیست (نمونه: ۰۹۱۲۳۴۵۶۷۸۹).' );
		}
	}

	if ( isset( $_POST['zarrin_national_id'] ) && '' !== trim( (string) wp_unslash( $_POST['zarrin_national_id'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		if ( ! zarrin_is_valid_national_id( wp_unslash( $_POST['zarrin_national_id'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$errors->add( 'zarrin_nid_invalid', 'کد ملی وارد‌شده معتبر نیست.' );
		}
	}

	return $errors;
}
add_filter( 'woocommerce_process_registration_errors', 'zarrin_validate_registration', 10, 3 );

/** ذخیره فیلدهای اضافی پس از ساخت کاربر */
function zarrin_save_extra_registration_fields( $customer_id ) {

	$first = isset( $_POST['zarrin_first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['zarrin_first_name'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$last  = isset( $_POST['zarrin_last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['zarrin_last_name'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$mob   = isset( $_POST['zarrin_mobile'] ) ? zarrin_normalize_mobile( wp_unslash( $_POST['zarrin_mobile'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$nid   = isset( $_POST['zarrin_national_id'] ) ? zarrin_normalize_mobile( wp_unslash( $_POST['zarrin_national_id'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification

	if ( $first ) {
		update_user_meta( $customer_id, 'first_name', $first );
		update_user_meta( $customer_id, 'billing_first_name', $first );
	}
	if ( $last ) {
		update_user_meta( $customer_id, 'last_name', $last );
		update_user_meta( $customer_id, 'billing_last_name', $last );
	}
	if ( $mob ) {
		update_user_meta( $customer_id, 'billing_phone', $mob );
		update_user_meta( $customer_id, 'zarrin_mobile', $mob );
	}
	if ( $nid ) {
		update_user_meta( $customer_id, 'zarrin_national_id', $nid );
		update_user_meta( $customer_id, 'billing_national_id', $nid );
	}
}
add_action( 'woocommerce_created_customer', 'zarrin_save_extra_registration_fields' );

/**
 * شورت‌کد فرم ورود و ثبت‌نام: [zarrin_auth]
 *
 * @param array $atts پارامترها.
 * @return string
 */
function zarrin_auth_shortcode( $atts ) {

	$atts = shortcode_atts(
		array(
			'redirect' => '',
			'tab'      => 'login',
		),
		$atts,
		'zarrin_auth'
	);

	if ( is_user_logged_in() ) {
		$user = wp_get_current_user();
		return '<div class="zauth-card" style="text-align:center">'
			. '<p style="font-weight:800;margin:0 0 6px">خوش آمدید، ' . esc_html( $user->display_name ) . '</p>'
			. '<p style="color:var(--muted);font-size:.85rem">شما وارد حساب خود شده‌اید.</p>'
			. '<p><a class="button" href="' . esc_url( wc_get_page_permalink( 'myaccount' ) ) . '">حساب کاربری من</a></p>'
			. '</div>';
	}

	$redirect = $atts['redirect'] ? $atts['redirect'] : ( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/' ) );
	$register = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : wp_registration_url();

	ob_start();
	?>
	<div class="zauth">
		<div class="zauth-card">
			<div class="zauth-tabs" role="tablist">
				<button type="button" class="zauth-tab <?php echo 'register' === $atts['tab'] ? '' : 'is-active'; ?>"
					data-zauth-tab="login" role="tab" aria-selected="<?php echo 'register' === $atts['tab'] ? 'false' : 'true'; ?>">
					ورود به حساب
				</button>
				<button type="button" class="zauth-tab <?php echo 'register' === $atts['tab'] ? 'is-active' : ''; ?>"
					data-zauth-tab="register" role="tab" aria-selected="<?php echo 'register' === $atts['tab'] ? 'true' : 'false'; ?>">
					ثبت‌نام
				</button>
			</div>

			<?php /* ---------- ورود ---------- */ ?>
			<div class="zauth-panel" data-zauth-panel="login" <?php echo 'register' === $atts['tab'] ? 'hidden' : ''; ?>>
				<?php
				wp_login_form(
					array(
						'echo'           => true,
						'redirect'       => $redirect,
						'label_username' => 'نام کاربری یا ایمیل',
						'label_password' => 'رمز عبور',
						'label_remember' => 'مرا به خاطر بسپار',
						'label_log_in'   => 'ورود',
						'remember'       => true,
					)
				);
				?>
				<div class="zauth-links">
					<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>">رمز عبور را فراموش کرده‌اید؟</a>
					<a href="<?php echo esc_url( $register ); ?>">حساب ندارید؟ ثبت‌نام کنید</a>
				</div>
			</div>

			<?php /* ---------- ثبت‌نام ---------- */ ?>
			<div class="zauth-panel" data-zauth-panel="register" <?php echo 'register' === $atts['tab'] ? '' : 'hidden'; ?>>
				<?php if ( ! function_exists( 'WC' ) ) : ?>
					<p class="zauth-note">برای ثبت‌نام، افزونه ووکامرس باید فعال باشد.</p>
				<?php elseif ( ! get_option( 'woocommerce_enable_myaccount_registration' ) ) : ?>
					<p class="zauth-note">
						ثبت‌نام در حال حاضر غیرفعال است. مدیر سایت می‌تواند آن را از
						«ووکامرس ← تنظیمات ← حساب‌ها و حریم خصوصی» فعال کند.
					</p>
				<?php else : ?>
					<ul class="zauth-perks">
						<li><?php zarrin_icon_e( 'shield', 17 ); ?> پیگیری سفارش و فاکتور طلا</li>
						<li><?php zarrin_icon_e( 'award', 17 ); ?> ذخیره ضمانت‌نامه و شناسنامه کالا</li>
						<li><?php zarrin_icon_e( 'exchange', 17 ); ?> معاوضه و بازخرید آسان‌تر</li>
					</ul>

					<form method="post" class="woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?>>

						<p class="form-row form-row-first">
							<label for="zarrin_first_name">نام</label>
							<input type="text" class="input-text" name="zarrin_first_name" id="zarrin_first_name"
								value="<?php echo esc_attr( isset( $_POST['zarrin_first_name'] ) ? wp_unslash( $_POST['zarrin_first_name'] ) : '' ); ?>" autocomplete="given-name"> <?php // phpcs:ignore WordPress.Security.NonceVerification ?>
						</p>
						<p class="form-row form-row-last">
							<label for="zarrin_last_name">نام خانوادگی</label>
							<input type="text" class="input-text" name="zarrin_last_name" id="zarrin_last_name"
								value="<?php echo esc_attr( isset( $_POST['zarrin_last_name'] ) ? wp_unslash( $_POST['zarrin_last_name'] ) : '' ); ?>" autocomplete="family-name"> <?php // phpcs:ignore WordPress.Security.NonceVerification ?>
						</p>
						<p class="form-row">
							<label for="zarrin_mobile">شماره موبایل <span style="color:#c0392b">*</span></label>
							<input type="tel" class="input-text" name="zarrin_mobile" id="zarrin_mobile"
								placeholder="۰۹۱۲۳۴۵۶۷۸۹" inputmode="tel" required autocomplete="tel"
								value="<?php echo esc_attr( isset( $_POST['zarrin_mobile'] ) ? wp_unslash( $_POST['zarrin_mobile'] ) : '' ); ?>"> <?php // phpcs:ignore WordPress.Security.NonceVerification ?>
						</p>
						<p class="form-row">
							<label for="zarrin_national_id">کد ملی <small style="color:var(--muted)">(اختیاری — برای فاکتور رسمی)</small></label>
							<input type="text" class="input-text" name="zarrin_national_id" id="zarrin_national_id"
								inputmode="numeric" maxlength="10"
								value="<?php echo esc_attr( isset( $_POST['zarrin_national_id'] ) ? wp_unslash( $_POST['zarrin_national_id'] ) : '' ); ?>"> <?php // phpcs:ignore WordPress.Security.NonceVerification ?>
						</p>
						<p class="form-row">
							<label for="reg_email">ایمیل <span style="color:#c0392b">*</span></label>
							<input type="email" class="input-text" name="email" id="reg_email" required autocomplete="email"
								value="<?php echo esc_attr( isset( $_POST['email'] ) ? wp_unslash( $_POST['email'] ) : '' ); ?>"> <?php // phpcs:ignore WordPress.Security.NonceVerification ?>
						</p>
						<p class="form-row">
							<label for="reg_password">رمز عبور <span style="color:#c0392b">*</span></label>
							<input type="password" class="input-text" name="password" id="reg_password" required
								autocomplete="new-password" data-zpass>
							<span class="password-strength" data-zstrength data-level="0"><span></span></span>
						</p>

						<?php do_action( 'woocommerce_register_form' ); ?>
						<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>

						<p class="form-row">
							<button type="submit" class="button" name="register" value="ثبت‌نام">ایجاد حساب کاربری</button>
						</p>

						<p class="zauth-note">
							با ثبت‌نام، <strong>قوانین و شرایط فروش</strong> فروشگاه (شامل شرایط معاوضه، بازخرید و بازگشت کالا)
							را می‌پذیرید. اطلاعات شما تنها برای صدور فاکتور و پیگیری سفارش استفاده می‌شود.
						</p>
					</form>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'zarrin_auth', 'zarrin_auth_shortcode' );

/* =========================================================
 * ۲) فیلدهای تسویه حساب مخصوص طلا
 * ======================================================= */

/** الزامی کردن موبایل و افزودن کد ملی/توضیح سفارش طلا */
function zarrin_checkout_fields( $fields ) {

	if ( isset( $fields['billing']['billing_phone'] ) ) {
		$fields['billing']['billing_phone']['required'] = true;
		$fields['billing']['billing_phone']['label']    = 'شماره موبایل (برای هماهنگی سفارش)';
		$fields['billing']['billing_phone']['priority'] = 25;
	}

	$fields['billing']['billing_national_id'] = array(
		'type'        => 'text',
		'label'       => 'کد ملی (برای فاکتور رسمی طلا)',
		'required'    => false,
		'class'       => array( 'form-row-wide' ),
		'input_class' => array( 'input-text' ),
		'priority'    => 26,
		'maxlength'   => 10,
	);

	if ( isset( $fields['order']['order_comments'] ) ) {
		$fields['order']['order_comments']['label']       = 'توضیحات سفارش (مثلاً اندازه، حکاکی یا ساعت تحویل)';
		$fields['order']['order_comments']['placeholder'] = 'اگر درخواست خاصی دارید بنویسید…';
	}

	return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'zarrin_checkout_fields', 20 );

/** اعتبارسنجی موبایل و کد ملی در تسویه حساب */
function zarrin_checkout_validate( $data, $errors ) {

	if ( ! empty( $data['billing_phone'] ) && ! zarrin_is_valid_mobile( $data['billing_phone'] ) ) {
		$errors->add( 'billing_phone', 'شماره موبایل معتبر نیست. نمونه صحیح: ۰۹۱۲۳۴۵۶۷۸۹' );
	}
	if ( ! empty( $data['billing_national_id'] ) && ! zarrin_is_valid_national_id( $data['billing_national_id'] ) ) {
		$errors->add( 'billing_national_id', 'کد ملی وارد‌شده معتبر نیست.' );
	}
	return $errors;
}
add_filter( 'woocommerce_after_checkout_validation', 'zarrin_checkout_validate', 10, 2 );

/** ذخیره کد ملی در سفارش و پروفایل کاربر */
function zarrin_checkout_save_nid( $order, $data ) {

	if ( ! empty( $data['billing_national_id'] ) ) {
		$nid = zarrin_normalize_mobile( $data['billing_national_id'] );
		$order->update_meta_data( '_billing_national_id', $nid );
		if ( $order->get_customer_id() ) {
			update_user_meta( $order->get_customer_id(), 'billing_national_id', $nid );
		}
	}
}
add_action( 'woocommerce_checkout_create_order', 'zarrin_checkout_save_nid', 10, 2 );

/** پیش‌پر کردن کد ملی برای کاربران قبلی */
function zarrin_checkout_prefill_nid( $value ) {
	if ( $value ) {
		return $value;
	}
	if ( is_user_logged_in() ) {
		return get_user_meta( get_current_user_id(), 'billing_national_id', true );
	}
	return $value;
}
add_filter( 'woocommerce_checkout_get_value', function ( $value, $input ) {
	if ( 'billing_national_id' === $input ) {
		return zarrin_checkout_prefill_nid( $value );
	}
	return $value;
}, 10, 2 );

/* =========================================================
 * ۳) نشان‌های اعتماد در سبد خرید، تسویه و پرداخت
 * ======================================================= */

/** خروجی نشان‌های اعتماد */
function zarrin_trust_badges_html() {

	ob_start();
	?>
	<ul class="ztrust">
		<li><?php zarrin_icon_e( 'shield', 22 ); ?><span>ضمانت اصالت و عیار<small>مهر و شناسنامه معتبر</small></span></li>
		<li><?php zarrin_icon_e( 'truck', 22 ); ?><span>ارسال بیمه‌شده<small>پست پیشتاز و تیپاکس</small></span></li>
		<li><?php zarrin_icon_e( 'exchange', 22 ); ?><span>معاوضه و بازخرید<small>تا ۷ روز پس از خرید</small></span></li>
		<li><?php zarrin_icon_e( 'check', 22 ); ?><span>پرداخت امن<small>درگاه بانکی شاپرک</small></span></li>
	</ul>
	<?php
	return ob_get_clean();
}

function zarrin_maybe_trust_badges() {
	if ( zarrin_get( 'zarrin_trust_badges', true ) ) {
		echo zarrin_trust_badges_html(); // phpcs:ignore WordPress.Security.EscapeOutput
	}
}
add_action( 'woocommerce_before_cart', 'zarrin_maybe_trust_badges', 5 );
add_action( 'woocommerce_before_checkout_form', 'zarrin_maybe_trust_badges', 5 );

/** یادداشت بیمه ارسال زیر جمع سبد */
function zarrin_cart_insurance_note() {
	?>
	<div class="z-insurance">
		<?php zarrin_icon_e( 'truck', 18 ); ?>
		<span>تمام مرسولات طلا با <b>بیمه کامل</b> و بسته‌بندی مخفی ارسال می‌شوند. کد رهگیری پس از ثبت سفارش پیامک می‌شود.</span>
	</div>
	<?php
}
add_action( 'woocommerce_after_cart_totals', 'zarrin_cart_insurance_note', 20 );

/** لوگوی روش‌های پرداخت و یادداشت امنیت در تسویه */
function zarrin_checkout_pay_info() {
	?>
	<div class="z-pay-logos">
		<span><?php zarrin_icon_e( 'shield', 16 ); ?> درگاه امن شاپرک</span>
		<span><?php zarrin_icon_e( 'check', 16 ); ?> پرداخت در محل (تهران)</span>
		<span><?php zarrin_icon_e( 'exchange', 16 ); ?> کارت‌به‌کارت</span>
	</div>
	<p class="z-checkout-trust">
		با ثبت سفارش، <b>قوانین و شرایط فروش</b> را می‌پذیرید. قیمت طلا بر پایه نرخ لحظه‌ای بازار محاسبه
		و در فاکتور نهایی قطعی می‌شود. در صورت تغییر نرخ پیش از پرداخت، کارشناسان ما با شما هماهنگ می‌کنند.
	</p>
	<?php
}
add_action( 'woocommerce_review_order_before_submit', 'zarrin_checkout_pay_info', 20 );

/** متن دکمه ثبت نهایی خرید */
function zarrin_place_order_text( $text ) {
	return 'ثبت نهایی خرید و پرداخت';
}
add_filter( 'woocommerce_order_button_text', 'zarrin_place_order_text' );

/* =========================================================
 * ۴) سبد خرید خالی
 * ======================================================= */

/** راهنمای سبد خالی با پیشنهاد محصولات */
function zarrin_empty_cart_content() {
	?>
	<div class="z-cart-empty">
		<?php zarrin_icon_e( 'cart', 60 ); ?>
		<h2>سبد خرید شما خالی است</h2>
		<p>محصولی انتخاب نکرده‌اید. از فروشگاه دیدن کنید یا با کارشناسان ما تماس بگیرید تا در انتخاب کمک کنند.</p>
		<p>
			<a class="button" href="<?php echo esc_url( zarrin_is_woo() && get_option( 'woocommerce_shop_page_id' ) ? get_permalink( get_option( 'woocommerce_shop_page_id' ) ) : home_url( '/' ) ); ?>">مشاهده فروشگاه</a>
			<a class="button" style="margin-inline-start:8px" href="<?php echo esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', zarrin_get( 'zarrin_phone', '' ) ) ); ?>">تماس با کارشناس</a>
		</p>
	</div>
	<?php
}
add_action( 'woocommerce_cart_is_empty', 'zarrin_empty_cart_content' );

/* =========================================================
 * ۵) ثبت نهایی خرید (صفحه تشکر) و فاکتور
 * ======================================================= */

/** کارت خلاصه سفارش پس از پرداخت */
function zarrin_thankyou_card( $order_id ) {

	$order = wc_get_order( $order_id );
	if ( ! $order ) {
		return;
	}

	$nid    = $order->get_meta( '_billing_national_id' );
	$phone  = $order->get_billing_phone();
	$items  = $order->get_items();
	$weight = 0;

	foreach ( $items as $item ) {
		$w = $item->get_meta( 'وزن (گرم)' );
		if ( $w ) {
			$weight += (float) zarrin_parse_fa_number( $w );
		}
	}

	$share_text = rawurlencode( 'سفارش من در ' . get_bloginfo( 'name' ) . ' با شماره ' . $order->get_order_number() . ' ثبت شد.' );
	?>
	<div class="zthanks-card">
		<div class="zthanks-head">
			<span class="zthanks-icon"><?php zarrin_icon_e( 'check', 28 ); ?></span>
			<div>
				<h2>سفارش شما با موفقیت ثبت شد ✅</h2>
				<p style="margin:4px 0 0;color:var(--muted);font-size:.85rem">
					همکاران ما به‌زودی برای تأیید نهایی و هماهنگی ارسال با شما تماس می‌گیرند.
				</p>
			</div>
		</div>

		<div class="zthanks-meta">
			<div><span>شماره سفارش</span><b>#<?php echo esc_html( zarrin_fa_digits( $order->get_order_number() ) ); ?></b></div>
			<div><span>تاریخ ثبت</span><b><?php echo esc_html( zarrin_fa_digits( $order->get_date_created() ? $order->get_date_created()->date_i18n( 'Y/m/d — H:i' ) : '' ) ); ?></b></div>
			<div><span>مبلغ کل</span><b><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></b></div>
			<div><span>روش پرداخت</span><b><?php echo esc_html( $order->get_payment_method_title() ); ?></b></div>
			<?php if ( $weight > 0 ) : ?>
				<div><span>مجموع وزن طلا</span><b><?php echo esc_html( zarrin_fa_digits( number_format( $weight, 2, '.', '' ) ) ); ?> گرم</b></div>
			<?php endif; ?>
			<?php if ( $phone ) : ?>
				<div><span>موبایل هماهنگی</span><b><?php echo esc_html( zarrin_fa_digits( $phone ) ); ?></b></div>
			<?php endif; ?>
			<?php if ( $nid ) : ?>
				<div><span>کد ملی فاکتور</span><b><?php echo esc_html( zarrin_fa_digits( $nid ) ); ?></b></div>
			<?php endif; ?>
		</div>

		<ol class="zthanks-steps">
			<li>پیامک تأیید سفارش برای شما ارسال می‌شود.</li>
			<li>کارشناس فروش برای تأیید نرخ نهایی طلا و هماهنگی ارسال تماس می‌گیرد.</li>
			<li>پس از تأیید، سفارش با <strong>بیمه کامل</strong> و بسته‌بندی مخفی ارسال می‌شود و کد رهگیری پیامک می‌گردد.</li>
			<li>شناسنامه، مهر عیار و فاکتور رسمی همراه مرسوله ارسال می‌شود.</li>
		</ol>

		<div class="zthanks-actions">
			<a class="button" href="<?php echo esc_url( $order->get_checkout_order_received_url() ); ?>" onclick="window.print();return false;">
				<?php zarrin_icon_e( 'pen', 18 ); ?> چاپ / ذخیره فاکتور
			</a>
			<a class="button" target="_blank" rel="noopener"
				href="https://wa.me/<?php echo esc_attr( preg_replace( '/[^0-9]/', '', zarrin_get( 'zarrin_whatsapp_num', '989123456789' ) ) ); ?>?text=<?php echo esc_attr( $share_text ); ?>">
				<?php zarrin_icon_e( 'whatsapp', 18 ); ?> ارسال خلاصه سفارش در واتساپ
			</a>
		</div>
	</div>
	<?php
}
add_action( 'woocommerce_thankyou', 'zarrin_thankyou_card', 5 );

/** نشان‌های اعتماد پایین صفحه تشکر */
function zarrin_thankyou_badges() {
	zarrin_maybe_trust_badges();
}
add_action( 'woocommerce_thankyou', 'zarrin_thankyou_badges', 40 );

/* =========================================================
 * ۶) چند بهبود تجربه کاربری ووکامرس
 * ======================================================= */

/** پیام «به سبد اضافه شد» با دکمه رفتن به تسویه */
function zarrin_add_to_cart_message( $message ) {
	if ( ! zarrin_is_woo() || ! function_exists( 'wc_get_cart_url' ) ) {
		return $message;
	}
	return $message . '<a href="' . esc_url( wc_get_checkout_url() ) . '" class="button" style="margin-inline-start:10px">ثبت نهایی خرید</a>';
}
add_filter( 'wc_add_to_cart_message_html', 'zarrin_add_to_cart_message', 20 );

/** حذف عبارت «مشاهده سبد خرید» تکراری در برخی قالب‌ها و افزودن راهنمای تماس */
function zarrin_after_shop_loop_note() {
	if ( ! is_shop() && ! is_product_category() ) {
		return;
	}
	?>
	<div class="z-insurance" style="margin-top:26px">
		<?php zarrin_icon_e( 'phone', 18 ); ?>
		<span>
			قیمت‌ها بر پایه نرخ روز محاسبه می‌شود؛ برای سفارش تلفنی یا مشاوره خرید
			<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', zarrin_get( 'zarrin_phone', '' ) ) ); ?>">
				<?php echo esc_html( zarrin_get( 'zarrin_phone', '' ) ); ?>
			</a>
			در تماس باشید.
		</span>
	</div>
	<?php
}
add_action( 'woocommerce_after_shop_loop', 'zarrin_after_shop_loop_note', 20 );

/** ثبت سفارش تلفنی/واتساپ: دکمه شناور در صفحه محصول */
function zarrin_product_contact_buttons() {
	global $product;
	if ( ! $product instanceof WC_Product ) {
		return;
	}
	$title = rawurlencode( 'سلام، درباره این محصول سؤال دارم: ' . get_the_title( $product->get_id() ) . ' — ' . get_permalink( $product->get_id() ) );
	?>
	<div class="z-share-row">
		<a target="_blank" rel="noopener"
			href="https://wa.me/<?php echo esc_attr( preg_replace( '/[^0-9]/', '', zarrin_get( 'zarrin_whatsapp_num', '989123456789' ) ) ); ?>?text=<?php echo esc_attr( $title ); ?>">
			<?php zarrin_icon_e( 'whatsapp', 17 ); ?> سفارش در واتساپ
		</a>
		<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', zarrin_get( 'zarrin_phone', '' ) ) ); ?>">
			<?php zarrin_icon_e( 'phone', 17 ); ?> مشاوره تلفنی
		</a>
		<a href="<?php echo esc_url( home_url( '/?p=' . $product->get_id() ) ); ?>" onclick="navigator.clipboard&&navigator.clipboard.writeText(location.href);this.textContent='لینک کپی شد';return false;">
			<?php zarrin_icon_e( 'check', 17 ); ?> کپی لینک محصول
		</a>
	</div>
	<?php
}
add_action( 'woocommerce_single_product_summary', 'zarrin_product_contact_buttons', 35 );

/** برچسب‌های اطمینان زیر دکمه افزودن به سبد */
function zarrin_single_trust_chips() {
	?>
	<div class="z-order-badges">
		<span><?php zarrin_icon_e( 'shield', 15 ); ?> ضمانت اصالت</span>
		<span><?php zarrin_icon_e( 'truck', 15 ); ?> ارسال بیمه‌شده</span>
		<span><?php zarrin_icon_e( 'exchange', 15 ); ?> معاوضه تا ۷ روز</span>
	</div>
	<?php
}
add_action( 'woocommerce_single_product_summary', 'zarrin_single_trust_chips', 36 );
