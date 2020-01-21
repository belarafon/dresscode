<?php
$nexio_blog_used_sidebar = nexio_get_option( 'blog_sidebar', 'primary_sidebar' );
if ( is_single() ) {
    $nexio_blog_used_sidebar = nexio_get_option( 'single_post_sidebar', 'primary_sidebar' );
}
?>
<?php if ( is_active_sidebar( $nexio_blog_used_sidebar ) ) : ?>
    <div id="widget-area" class="widget-area">
        <?php dynamic_sidebar( $nexio_blog_used_sidebar ); ?>
    </div><!-- .widget-area -->
<?php endif; ?>