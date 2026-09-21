<?php
/**
 * قالب ووکامرس — فروشگاه، محصول تکی، دسته‌بندی محصولات
 *
 * @package Zarrin
 */

get_header();
?>

<div class="container woocommerce-wrap">
	<main id="content" class="site-main">
		<?php woocommerce_content(); ?>
	</main>
</div>

<?php
get_footer();
