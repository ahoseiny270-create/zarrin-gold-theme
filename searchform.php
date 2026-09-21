<?php
/**
 * فرم جستجو
 *
 * @package Zarrin
 */
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<input type="search" name="s" placeholder="جستجو کنید…" value="<?php echo esc_attr( get_search_query() ); ?>" aria-label="جستجو">
	<button type="submit" aria-label="جستجو"><?php zarrin_icon_e( 'search', 19 ); ?></button>
</form>
