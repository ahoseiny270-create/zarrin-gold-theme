<?php
/**
 * هدر قالب
 *
 * @package Zarrin
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#content">پرش به محتوا</a>

<?php $zarrin_phone = zarrin_get( 'zarrin_phone', '۰۲۱-۳۳۴۴۵۵۶۶' ); ?>

<!-- نوار بالا -->
<div class="topbar">
	<div class="container topbar-inner">
		<div class="topbar-items">
			<span class="topbar-item">
				<?php zarrin_icon_e( 'phone', 15 ); ?>
				<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $zarrin_phone ) ); ?>"><?php echo esc_html( $zarrin_phone ); ?></a>
			</span>
			<span class="topbar-item hide-sm">
				<?php zarrin_icon_e( 'clock', 15 ); ?>
				<?php echo esc_html( zarrin_get( 'zarrin_hours', 'شنبه تا پنجشنبه — ۱۰ صبح تا ۸ شب' ) ); ?>
			</span>
		</div>
		<div class="topbar-socials">
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
						<?php zarrin_icon_e( $zarrin_sname, 15 ); ?>
					</a>
					<?php
				endif;
			endforeach;
			?>
		</div>
	</div>
</div>

<!-- هدر اصلی -->
<header class="site-header" id="siteHeader">
	<div class="nav-overlay" id="navOverlay"></div>
	<div class="container header-inner">

		<div class="brand">
			<?php
			if ( has_custom_logo() ) {
				the_custom_logo();
			} else {
				?>
				<a class="brand-link" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
					<span class="brand-mark"><?php zarrin_icon_e( 'diamond', 24 ); ?></span>
					<span>
						<span class="brand-name"><?php bloginfo( 'name' ); ?></span>
						<span class="brand-tag">GOLD &amp; JEWELRY</span>
					</span>
				</a>
				<?php
			}
			?>
		</div>

		<nav class="main-nav" id="mainNav" aria-label="منوی اصلی">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'nav-list',
					'fallback_cb'    => 'zarrin_menu_fallback',
				)
			);
			?>
			<?php /* پاورقی کشوی همبرگری — فقط در موبایل نمایش داده می‌شود */ ?>
			<div class="drawer-foot">
				<a class="drawer-cta" href="<?php echo esc_url( zarrin_is_woo() && get_option( 'woocommerce_shop_page_id' ) ? get_permalink( get_option( 'woocommerce_shop_page_id' ) ) : home_url( '/' ) ); ?>">
					<?php zarrin_icon_e( 'cart', 18 ); ?> سفارش طلا و مشاهده فروشگاه
				</a>

				<ul class="drawer-contact">
					<li>
						<?php zarrin_icon_e( 'phone', 17 ); ?>
						<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $zarrin_phone ) ); ?>"><?php echo esc_html( $zarrin_phone ); ?></a>
					</li>
					<li>
						<?php zarrin_icon_e( 'whatsapp', 17 ); ?>
						<a target="_blank" rel="noopener" href="https://wa.me/<?php echo esc_attr( preg_replace( '/[^0-9]/', '', zarrin_get( 'zarrin_whatsapp_num', '989123456789' ) ) ); ?>">مشاوره خرید در واتساپ</a>
					</li>
					<li>
						<?php zarrin_icon_e( 'pin', 17 ); ?>
						<span><?php echo esc_html( zarrin_get( 'zarrin_address', 'تهران، بازار بزرگ، سرای امیر، پلاک ۱۲' ) ); ?></span>
					</li>
					<li>
						<?php zarrin_icon_e( 'clock', 17 ); ?>
						<span><?php echo esc_html( zarrin_get( 'zarrin_hours', 'شنبه تا پنجشنبه — ۱۰ صبح تا ۸ شب' ) ); ?></span>
					</li>
				</ul>

				<div class="drawer-socials">
					<?php
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
		</nav>

		<div class="header-actions">
			<button class="icon-btn search-toggle" aria-label="نمایش جستجو" aria-expanded="false">
				<?php zarrin_icon_e( 'search', 19 ); ?>
			</button>

			<?php if ( zarrin_is_woo() && function_exists( 'wc_get_cart_url' ) ) : ?>
				<a class="icon-btn" href="<?php echo esc_url( wc_get_cart_url() ); ?>" aria-label="سبد خرید">
					<?php zarrin_icon_e( 'cart', 19 ); ?>
					<span class="cart-count"><?php echo esc_html( function_exists( 'WC' ) && WC()->cart ? zarrin_fa_digits( WC()->cart->get_cart_contents_count() ) : '۰' ); ?></span>
				</a>
			<?php endif; ?>

			<button class="icon-btn nav-toggle" id="navToggle" aria-label="باز و بسته کردن منو" aria-controls="mainNav" aria-expanded="false">
				<span></span><span></span><span></span>
			</button>
		</div>
	</div>

	<!-- نوار جستجو -->
	<div class="search-bar" id="searchBar">
		<div class="container">
			<?php get_search_form(); ?>
		</div>
	</div>
</header>

<main id="content">
