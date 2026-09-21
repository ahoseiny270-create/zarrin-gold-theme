<?php
/**
 * قالب صفحه ۴۰۴
 *
 * @package Zarrin
 */

get_header();
?>

<div class="container">
	<div class="error-404">
		<div class="error-code" data-fa-num>404</div>
		<h1>صفحه پیدا نشد!</h1>
		<p>صفحه‌ای که دنبال آن هستید وجود ندارد یا جابه‌جا شده است. می‌توانید از جستجو استفاده کنید یا به صفحه اصلی برگردید.</p>
		<?php get_search_form(); ?>
		<p style="margin-top:24px;">
			<a class="btn btn-gold" href="<?php echo esc_url( home_url( '/' ) ); ?>">بازگشت به صفحه اصلی</a>
		</p>
	</div>
</div>

<?php
get_footer();
