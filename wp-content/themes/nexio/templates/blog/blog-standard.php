<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$animation_on_scroll = nexio_get_option( 'animation_on_scroll', false );
$classes[]           = 'post-item post-standard';
if ( $animation_on_scroll ) {
	$classes[] = 'nexio-wow fadeInUp';
}
?>
<?php
if ( have_posts() ) : ?>
	<?php do_action( 'nexio_before_blog_content' ); ?>
    <div class="blog-standard content-post">
		<?php while ( have_posts() ) : the_post(); ?>
            <article <?php post_class( $classes ); ?>>
				<?php nexio_post_format(); ?>
                <div class="single-post-info">
                    <div class="post-meta-wrap">
						<?php nexio_post_author() ?>
                        <div class="post-meta">
							<?php
							nexio_post_date();
							nexio_post_comment();
							?>
                        </div>
                    </div>
					<?php
					nexio_post_title();
					nexio_post_category();
					?>
                </div>
				<?php
				$enable_except_post = nexio_get_option( 'enable_except_post', '' );
				if ( $enable_except_post == 1 ) {
					nexio_post_excerpt();
				} else {
					nexio_post_full_content();
				}
				?>
				<?php
				if ( $enable_except_post == 1 ) {
					nexio_post_readmore();
				} else {
					nexio_post_tags();
				}
				?>
            </article>
		<?php endwhile;
		wp_reset_postdata(); ?>
    </div>
	<?php
	/**
	 * Functions hooked into nexio_after_blog_content action
	 *
	 * @hooked nexio_paging_nav               - 10
	 */
	do_action( 'nexio_after_blog_content' ); ?>
<?php else :
	get_template_part( 'content', 'none' );
endif; ?>