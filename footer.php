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

<button class="to-top" id="toTop" aria-label="بازگشت به بالای صفحه">
	<?php zarrin_icon_e( 'up', 20 ); ?>
</button>

<?php wp_footer(); ?>
</body>
</html>
