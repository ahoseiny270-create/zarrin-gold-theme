/**
 * زرین — تنظیمات ظاهری سبد و تسویه بلوکی ووکامرس (نسخه ۱.۵.۱)
 *
 * متن دکمه ثبت سفارش و چند برچسب را در «تسویه حساب بلوکی» فارسی و متناسب
 * با خرید طلا می‌کند. اگر ووکامرس بلوکی فعال نباشد، این فایل بی‌اثر است.
 */
(function () {
	'use strict';

	if (!window.wc || !window.wc.blocksCheckout) {
		return;
	}

	var registerCheckoutFilters = window.wc.blocksCheckout.registerCheckoutFilters;
	if (typeof registerCheckoutFilters !== 'function') {
		return;
	}

	registerCheckoutFilters('zarrin-gold', {
		placeOrderButtonLabel: function (defaultValue) {
			return 'ثبت نهایی خرید و پرداخت';
		},
		placeOrderButtonDescription: function (defaultValue) {
			return 'با ثبت سفارش، قوانین و شرایط فروش فروشگاه را می‌پذیرید.';
		},
		orderSummaryCartItemsLabel: function (defaultValue) {
			return defaultValue;
		}
	});
})();
