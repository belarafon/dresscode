<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$animation_on_scroll = nexio_get_option('animation_on_scroll', false);
$classes = array('post-item', 'post-classic');
if ($animation_on_scroll) {
    $classes[] = 'nexio-wow fadeInUp';
}
?>
<?php if (have_posts()) :?>
    <div class="blog-classic content-post row auto-clear">
        <?php while (have_posts()) : the_post();?>
            <article <?php post_class($classes); ?>>
                <div class="post-inner">
                    <?php nexio_post_thumbnail();?>
                    <div class="post-content equal-elem">
                        <div class="post-meta">
                            <?php
                            nexio_post_date();
                            nexio_post_comment();
                            ?>
                        </div>
                        <div class="post-info">
                            <?php
                            nexio_post_title();
                            ?>
                            <div class="post-excerpt-content">
                                <?php
                                echo wp_trim_words(apply_filters('the_excerpt', get_the_excerpt()), 35, '');
                                ?>
                            </div>
                        </div>
                        <div class="post-foot">
                            <?php
                            nexio_post_readmore();
                            ?>
                        </div>
                    </div>
                </div>
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
    do_action('nexio_after_blog_content'); ?>
<?php else :
    get_template_part('content', 'none');
endif;