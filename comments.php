<?php
/**
 * ناحیه دیدگاه‌ها
 *
 * @package Zarrin
 */

if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments">

	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php zarrin_icon_e( 'comment', 20 ); ?>
			دیدگاه کاربران (<?php echo esc_html( zarrin_fa_digits( get_comments_number() ) ); ?>)
		</h2>

		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 48,
				)
			);
			?>
		</ol>

		<?php
		the_comments_pagination(
			array(
				'prev_text' => 'قبلی',
				'next_text' => 'بعدی',
			)
		);
		?>
	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() ) : ?>
		<p>دیدگاه‌ها بسته شده‌اند.</p>
	<?php endif; ?>

	<?php
	comment_form(
		array(
			'title_reply'         => 'دیدگاه خود را بنویسید',
			'title_reply_before'  => '<h3 class="comment-reply-title">',
			'title_reply_after'   => '</h3>',
			'label_submit'        => 'ارسال دیدگاه',
			'comment_notes_before'=> '<p class="comment-notes">نشانی ایمیل شما منتشر نخواهد شد.</p>',
			'comment_field'       => '<p class="comment-form-comment"><label for="comment">دیدگاه *</label><textarea id="comment" name="comment" rows="5" required></textarea></p>',
		)
	);
	?>
</div>
