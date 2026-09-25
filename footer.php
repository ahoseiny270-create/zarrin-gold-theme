<?php
/**
 * فوتر قالب
 *
 * @package Zarrin
 */
?>
</main>

<footer class="site-footer">
	<div class="container footer-grid">

		<!-- ستون معرفی -->
		<div class="footer-col">
			<div class="footer-brand">
				<span class="brand-mark"><?php zarrin_icon_e( 'diamond', 22 ); ?></span>
				<span class="brand-name"><?php bloginfo( 'name' ); ?></span>
			</div>
			<p class="footer-about"><?php echo wp_kses_post( zarrin_get( 'zarrin_footer_about', 'طلافروشی زرین؛ عرضه‌کننده انواع طلا و جواهر با ضمانت اصالت، قیمت روز و امکان معاوضه. خرید شما را با خیال راحت انجام دهید.' ) ); ?></p>
			<div class="footer-socials">
				<?php
				$zarrin_socials = array(
					'instagram' => zarrin_get( 'zarrin_instagram' ),
					'telegram'  => zarrin_get( 'zarrin_telegram' ),
					'whatsapp'  => zarrin_get( 'zarrin_whatsapp' ),
				);
				foreach ( $zarrin_socials as $zarrin_sname => $zarrin_surl ) :
					if ( $zarrin_surl ) :
						?>
						<a href="<?php echo esc_url( $zarrin_surl ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $zarrin_sname ); ?>">
							<?php zarrin_icon_e( $zarrin_sname, 18 ); ?>
						</a>
						<?php
					endif;
				endforeach;
				?>
			</div>
		</div>

		<!-- دسترسی سریع -->
		<div class="footer-col">
			<h3 class="footer-title">دسترسی سریع</h3>
			<ul class="footer-nav">
				<?php
				if ( has_nav_menu( 'footer' ) ) {
					wp_nav_menu(
						array(
							'theme_location' => 'footer',
							'container'      => false,
							'menu_class'     => 'footer-nav',
							'depth'          => 1,
						)
					);
				} else {
					?>
					<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">صفحه اصلی</a></li>
					<?php
					if ( zarrin_is_woo() && get_option( 'woocommerce_shop_page_id' ) ) {
						echo '<li><a href="' . esc_url( get_permalink( get_option( 'woocommerce_shop_page_id' ) ) ) . '">فروشگاه</a></li>';
					}
					$zarrin_blog_page = get_option( 'page_for_posts' );
					if ( $zarrin_blog_page ) {
						echo '<li><a href="' . esc_url( get_permalink( $zarrin_blog_page ) ) . '">وبلاگ</a></li>';
					}
					?>
					<li><a href="<?php echo esc_url( home_url( '/#about' ) ); ?>">درباره ما</a></li>
					<li><a href="<?php echo esc_url( home_url( '/#contact' ) ); ?>">تماس با ما</a></li>
					<?php
				}
				?>
			</ul>
		</div>

		<!-- خدمات مشتریان و قوانین -->
		<div class="footer-col">
			<h3 class="footer-title">خدمات مشتریان</h3>
			<ul class="footer-nav">
				<li><a href="<?php echo esc_url( home_url( '/#faq' ) ); ?>">سوالات متداول</a></li>
				<li><a href="<?php echo esc_url( home_url( '/#faq' ) ); ?>">شرایط معاوضه و بازخرید</a></li>
				<?php
				/* صفحات قانونی ووکامرس — در صورت تعریف‌شدن نمایش داده می‌شوند. */
				if ( zarrin_is_woo() ) {
					$zarrin_terms  = (int) get_option( 'woocommerce_terms_page_id' );
					$zarrin_refund = (int) get_option( 'woocommerce_refund_returns_page_id' );

					if ( $zarrin_terms && 'publish' === get_post_status( $zarrin_terms ) ) {
						echo '<li><a href="' . esc_url( get_permalink( $zarrin_terms ) ) . '">قوانین و شرایط فروش</a></li>';
					}
					if ( $zarrin_refund && 'publish' === get_post_status( $zarrin_refund ) ) {
						echo '<li><a href="' . esc_url( get_permalink( $zarrin_refund ) ) . '">رویه بازگشت کالا</a></li>';
					}
				}
				$zarrin_privacy = get_privacy_policy_url();
				if ( $zarrin_privacy ) {
					echo '<li><a href="' . esc_url( $zarrin_privacy ) . '">حریم خصوصی</a></li>';
				}
				if ( function_exists( 'wc_get_page_permalink' ) ) {
					echo '<li><a href="' . esc_url( wc_get_page_permalink( 'myaccount' ) ) . '">پیگیری سفارش</a></li>';
				}
				?>
				<li><a href="https://wa.me/<?php echo esc_attr( preg_replace( '/[^0-9]/', '', zarrin_get( 'zarrin_whatsapp_num', '989123456789' ) ) ); ?>" target="_blank" rel="noopener">پشتیبانی واتساپ</a></li>
			</ul>
		</div>

		<!-- تماس با ما -->
		<div class="footer-col" id="contact">
			<h3 class="footer-title">تماس با ما</h3>
			<ul class="footer-contact">
				<li>
					<?php zarrin_icon_e( 'pin', 17 ); ?>
					<span><?php echo esc_html( zarrin_get( 'zarrin_address', 'تهران، بازار بزرگ، سرای امیر، پلاک ۱۲' ) ); ?></span>
				</li>
				<li>
					<?php zarrin_icon_e( 'phone', 17 ); ?>
					<span>
						<?php echo esc_html( zarrin_get( 'zarrin_phone', '۰۲۱-۳۳۴۴۵۵۶۶' ) ); ?>
						&nbsp;|&nbsp;
						<?php echo esc_html( zarrin_get( 'zarrin_mobile', '۰۹۱۲۳۴۵۶۷۸۹' ) ); ?>
					</span>
				</li>
				<li>
					<?php zarrin_icon_e( 'mail', 17 ); ?>
					<span><?php echo esc_html( zarrin_get( 'zarrin_email', 'info@example.com' ) ); ?></span>
				</li>
				<li>
					<?php zarrin_icon_e( 'clock', 17 ); ?>
					<span><?php echo esc_html( zarrin_get( 'zarrin_hours', 'شنبه تا پنجشنبه — ۱۰ صبح تا ۸ شب' ) ); ?></span>
				</li>
			</ul>
		</div>
	</div>

	<?php if ( function_exists( 'zarrin_trust_badges_html' ) ) : ?>
		<div class="container footer-trust"><?php echo zarrin_trust_badges_html(); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
	<?php endif; ?>

	<div class="footer-bottom">
		<div class="container">
			<?php
			printf(
				'© %1$s — تمامی حقوق برای %2$s محفوظ است.',
				esc_html( zarrin_fa_digits( date_i18n( 'Y' ) ) ),
				esc_html( get_bloginfo( 'name' ) )
			);
			?>
		</div>
	</div>
</footer>

<?php if ( zarrin_get( 'zarrin_float_enable', true ) ) : ?>
<!-- نوار اقدام شناور موبایل -->
<nav class="zfloat" aria-label="دسترسی سریع">
	<div class="zfloat-inner">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php zarrin_icon_e( 'diamond', 20 ); ?>
			<span>صفحه اصلی</span>
		</a>
		<a href="<?php echo esc_url( zarrin_is_woo() && get_option( 'woocommerce_shop_page_id' ) ? get_permalink( get_option( 'woocommerce_shop_page_id' ) ) : home_url( '/' ) ); ?>">
			<?php zarrin_icon_e( 'cart', 20 ); ?>
			<span>فروشگاه</span>
		</a>
		<?php if ( zarrin_is_woo() && function_exists( 'wc_get_cart_url' ) ) : ?>
			<a href="<?php echo esc_url( wc_get_cart_url() ); ?>">
				<?php zarrin_icon_e( 'coin', 20 ); ?>
				<span>سبد خرید</span>
				<?php $zarrin_fc_count = ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0; ?>
				<?php if ( $zarrin_fc_count ) : ?>
					<span class="zfloat-badge"><?php echo esc_html( zarrin_fa_digits( $zarrin_fc_count ) ); ?></span>
				<?php endif; ?>
			</a>
		<?php endif; ?>
		<a href="<?php echo esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', zarrin_get( 'zarrin_phone', '۰۲۱-۳۳۴۴۵۵۶۶' ) ) ); ?>">
			<?php zarrin_icon_e( 'phone', 20 ); ?>
			<span>تماس با ما</span>
		</a>
	</div>
</nav>
<?php endif; ?>

<button class="to-top" id="toTop" aria-label="بازگشت به بالای صفحه">
	<?php zarrin_icon_e( 'up', 20 ); ?>
</button>

<?php wp_footer(); ?>
</body>
</html>
