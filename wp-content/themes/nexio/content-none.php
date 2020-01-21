<div class="no-results not-found">
	<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>
        <p><?php printf( '%2$s <a href="%1$s">%3$s</a>.', esc_url( admin_url( 'post-new.php' ) ), esc_html__( 'Ready to publish your first post?', 'nexio' ), esc_html__( 'Get started here', 'nexio' ) ); ?></p>
	<?php elseif ( is_search() ) : ?>
        <p><?php echo esc_html__( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'nexio' ); ?></p>
		<?php get_search_form(); ?>
	<?php else : ?>
        <p><?php echo esc_html__( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'nexio' ); ?></p>
		<?php get_search_form(); ?>
	<?php endif; ?>
</div><!-- .no-results -->