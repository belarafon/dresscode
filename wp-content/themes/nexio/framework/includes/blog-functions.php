<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
/**
 *
 * HOOK AFTER BLOG CONTENT
 */
add_action('nexio_after_blog_content', 'nexio_paging_nav', 10);
/**
 *
 * HOOK TEMPLATE
 */
if (!function_exists('nexio_paging_nav')) {
    function nexio_paging_nav()
    {?>
        <div class="post-link">
            <?php
            if( $prev_post = get_previous_post() ):
                echo'<div class="single-post-nav previous-post-link"><span class="flaticon-left-arrow"></span><span class="nav-subtitle">' . esc_html__( 'Previous Post', 'nexio' ) . '</span><br>';
                previous_post_link( '%link',"<span>%title</span>", TRUE );
                echo'</div>';
            endif;

            if( $next_post = get_next_post() ):
                echo'<div class="single-post-nav next-post-link"><span class="nav-subtitle">' . esc_html__( 'Next Post', 'nexio' ) . '</span><span class="flaticon-right-arrow"></span><br>';
                next_post_link( '%link',"<span>%title</span>", TRUE );
                echo'</div>';
            endif;
            ?>
        </div>
    <?php 
    }
}
if (!function_exists('nexio_post_thumbnail')) {
    function nexio_post_thumbnail()
    {
        $blog_style = nexio_get_option('blog-style', 'standard');
        $thumb = nexio_resize_image(get_post_thumbnail_id(), null, 440, 333, true, true, false);
        if (has_post_thumbnail()) :
            ?>
            <div class="post-thumb">
                <?php
                if (is_single()) {
                    the_post_thumbnail('full');
                } else {
                    echo '<a href="' . get_the_permalink() . '">';
                    if ($blog_style == 'grid' || $blog_style == 'modern') {
                        echo nexio_img_output($thumb);
                    } else {
                        the_post_thumbnail('full');
                    }
                    echo '</a>';
                }
                ?>
            </div>
            <?php
        endif;
    }
}
if (!function_exists('nexio_post_format')) {
    function nexio_post_format()
    {
        $nexio_post_meta = get_post_meta(get_the_ID(), '_custom_post_woo_options', true);
        $gallery_post = isset($nexio_post_meta['post-gallery']) ? $nexio_post_meta['post-gallery'] : '';
        $video_post = isset($nexio_post_meta['post-gallery']) ? $nexio_post_meta['audio-video-url'] : '';
        $post_format = get_post_format();
        $width = 1400;
        $height = 933;
        if (has_post_thumbnail()) :
            ?>
            <div class="post-thumb">
                <?php
                    if ($post_format == 'gallery' && $gallery_post != '') {
                        $gallery_post = explode(',', $gallery_post);
                        $data_reponsive = array(
                            '0'    => array(
                                'items' => 1,
                            ),
                            '360'  => array(
                                'items' => 1,
                            ),
                            '768'  => array(
                                'items' => 1,
                            ),
                            '992'  => array(
                                'items' => 1,
                            ),
                            '1200' => array(
                                'items' => 1,
                            ),
                            '1500' => array(
                                'items' => 1,
                            ),
                        );

                        $data_reponsive    = json_encode( $data_reponsive );
                        $loop              = 'false';
                        $dots              = 'false';
                        $data_margin       = '0';
                        ?>
                        <div class="owl-carousel"
                             data-margin="<?php echo esc_attr( $data_margin ); ?>" data-nav="true"
                             data-dots="<?php echo esc_attr( $dots ); ?>" data-loop="<?php echo esc_attr( $loop ); ?>"
                             data-responsive='<?php echo esc_attr( $data_reponsive ); ?>'>
                            <figure>
                                <?php
                                $image_thumb = nexio_resize_image(get_post_thumbnail_id(), null, $width, $height, false, false, false);
                                echo nexio_img_output($image_thumb);
                                ?>
                            </figure>
                            <?php foreach ($gallery_post as $item) : ?>
                                <figure>
                                    <?php
                                    $image_gallery = nexio_resize_image($item, null, $width, $height, false, false, false);
                                     echo nexio_img_output($image_gallery);
                                    ?>
                                </figure>
                            <?php endforeach; ?>
                        </div>
                    <?php } elseif ( $post_format == 'video' && $video_post != '' ) {
                        the_widget( 'WP_Widget_Media_Video', 'url=' . $video_post . '' );
                    } else {
                        $image_thumb = nexio_resize_image(get_post_thumbnail_id(), null, $width, $height, false, false, false);
                        echo '<a href="' . get_permalink() . '">';
                        echo nexio_img_output($image_thumb);
                        echo '</a>';
                        }
                    ?>
            </div>
            <?php
        endif;
    }
}
if (!function_exists('nexio_post_author')) {
    function nexio_post_author()
    {
        ?>
        <div class="post-author">
            <figure class="author-avatar"><?php echo get_avatar( get_the_author_meta( 'ID' ), 83 ); ?></figure>
            <div class="author-name">
                <?php echo esc_html__('By:', 'nexio'); ?>
                <a href="<?php echo get_author_posts_url(get_the_author_meta('ID'), get_the_author_meta('user_nicename')); ?>">
                    <?php the_author(); ?>
                </a>
            </div>
        </div>
        <?php
    }
}
if (!function_exists('nexio_post_comment')) {
    function nexio_post_comment()
    {
        ?>
        <div class="post-comment">
            <a href="<?php the_permalink(); ?>">
                <?php
                comments_number(
                    esc_html__('0 ', 'nexio') . '<span>' . esc_html__('Comments', 'nexio') . '</span>',
                    esc_html__('1 ', 'nexio') . '<span>' . esc_html__('Comment', 'nexio') . '</span>',
                    esc_html__('% ', 'nexio') . '<span>' . esc_html__('Comments', 'nexio') . '</span>'
                );
                ?>
            </a>
        </div>
        <?php
    }
}
if (!function_exists('nexio_post_comment_icon')) {
    function nexio_post_comment_icon()
    {
        ?>
        <div class="post-comment-icon">
            <a href="<?php the_permalink(); ?>">
                <?php
                comments_number(
                    esc_html__('0', 'nexio'),
                    esc_html__('1', 'nexio'),
                    esc_html__('%', 'nexio')
                );
                ?>
            </a>
        </div>
        <?php
    }
}
if (!function_exists('nexio_callback_comment')) {
    /**
     * Azirspares comment template
     *
     * @param array $comment the comment array.
     * @param array $args the comment args.
     * @param int $depth the comment depth.
     *
     * @since 1.0.0
     */
    function nexio_callback_comment($comment, $args, $depth)
    {
        if ('div' == $args['style']) {
            $tag = 'div ';
            $add_below = 'comment';
        } else {
            $tag = 'li ';
            $add_below = 'div-comment';
        }
        ?>
        <<?php echo esc_attr($tag); ?><?php comment_class(empty($args['has_children']) ? '' : 'parent') ?> id="comment-<?php echo get_comment_ID(); ?>">
        <div class="comment_container">
            <div class="comment-avatar">
                <?php echo get_avatar($comment, 120); ?>
            </div>
            <div class="comment-text commentmetadata">
                <strong class="comment-author vcard">
                    <?php printf(wp_kses_post('%s', 'nexio'), get_comment_author_link()); ?>
                </strong>
                <?php if ('0' == $comment->comment_approved) : ?>
                    <em class="comment-awaiting-moderation"><?php esc_attr_e('Your comment is awaiting moderation.', 'nexio'); ?></em>
                    <br/>
                <?php endif; ?>
                <a href="<?php echo esc_url(htmlspecialchars(get_comment_link(get_comment_ID()))); ?>"
                   class="comment-date">
                    <?php echo '<time datetime="' . get_comment_date('c') . '">' . get_comment_date() . '</time>'; ?>
                </a>
                <?php edit_comment_link(__('Edit', 'nexio'), '  ', ''); ?>
                <?php comment_reply_link(array_merge($args, array(
                    'add_below' => $add_below,
                    'depth' => $depth,
                    'max_depth' => $args['max_depth']
                ))); ?>
                <?php echo ('div' != $args['style']) ? '<div id="div-comment-' . get_comment_ID() . '" class="comment-content">' : '' ?>
                <?php comment_text(); ?>
                <?php echo 'div' != $args['style'] ? '</div>' : ''; ?>
            </div>
        </div>
        <?php
    }
}
if (!function_exists('nexio_post_title')) {
    function nexio_post_title()
    {
        ?>
        <h2 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
        <?php
    }
}
if (!function_exists('nexio_post_readmore')) {
    function nexio_post_readmore()
    {
        ?>
        <div class="readmore-btn"><a href="<?php the_permalink(); ?>"
           class="readmore"><?php echo esc_html__('Read more', 'nexio'); ?></a></div>
        <?php
    }
}
if (!function_exists('nexio_post_excerpt')) {
    function nexio_post_excerpt()
    {
        ?>
        <div class="post-content">
            <?php echo wp_trim_words(apply_filters('the_excerpt', get_the_excerpt()), 50, esc_html__('...', 'nexio')); ?>
        </div>
        <?php
    }
}
if (!function_exists('nexio_post_full_content')) {
    function nexio_post_full_content()
    {
        ?>
        <div class="post-content">
            <?php
            /* translators: %s: Name of current post */
            the_content(sprintf(
                    esc_html__('Continue reading %s', 'nexio'),
                    the_title('<span class="screen-reader-text">', '</span>', false)
                )
            );
            wp_link_pages(array(
                    'before' => '<div class="post-pagination"><span class="title">' . esc_html__('Pages:', 'nexio') . '</span>',
                    'after' => '</div>',
                    'link_before' => '<span>',
                    'link_after' => '</span>',
                )
            );
            ?>
        </div>
        <?php
    }
}
if (!function_exists('nexio_post_date')) {
    function nexio_post_date()
    {
        ?>
        <div class="date">
            <a href="<?php the_permalink(); ?>">
                <?php echo get_the_date(); ?>
            </a>
        </div>
        <?php
    }
}
if (!function_exists('nexio_post_datebox')) {
    function nexio_post_datebox()
    {
        $archive_year = get_the_time('Y');
        $archive_month = get_the_time('m');
        $archive_day = get_the_time('d');
        ?>
        <div class="date">
            <a href="<?php echo get_day_link($archive_year, $archive_month, $archive_day); ?>">
                <?php echo get_the_date('d.M.Y'); ?>
            </a>
        </div>
        <?php
    }
}
if (!function_exists('nexio_share_button')) {
    function nexio_share_button()
    {
        $enable_share_post = nexio_get_option('enable_share_post', '');
        $share_image_url = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'full');
        $share_link_url = get_permalink(get_the_ID());
        $share_link_title = get_the_title();
        $share_twitter_summary = get_the_excerpt();
        $twitter = 'https://twitter.com/share?url=' . $share_link_url . '&text=' . $share_twitter_summary;
        $facebook = 'https://www.facebook.com/sharer.php?s=100&title=' . $share_link_title . '&url=' . $share_link_url;
        $google = 'https://plus.google.com/share?url=' . $share_link_url . '&title=' . $share_link_title;
        $pinterest = 'http://pinterest.com/pin/create/button/?url=' . $share_link_url . '&description=' . $share_twitter_summary . '&media=' . $share_image_url[0];
        if ($enable_share_post == 1):
            ?>
            <div class="nexio-share-socials">
                <h5 class="social-heading"><?php echo esc_html__('Share', 'nexio') ?></h5>
                <a target="_blank" class="facebook"
                   href="<?php echo esc_url($facebook); ?>"
                   title="<?php echo esc_attr('Facebook') ?>"
                   onclick='window.open(this.href, "", "menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600");return false;'>
                    <i class="fa fa-facebook-f"></i>
                </a>
                <a target="_blank" class="twitter"
                   href="<?php echo esc_url($twitter); ?>"
                   title="<?php echo esc_attr('Twitter') ?>"
                   onclick='window.open(this.href, "", "menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600");return false;'>
                    <i class="fa fa-twitter"></i>
                </a>
                <a target="_blank" class="pinterest"
                   href="<?php echo esc_url($pinterest); ?>"
                   title="<?php echo esc_attr('Pinterest') ?>"
                   onclick='window.open(this.href, "", "menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600");return false;'>
                    <i class="fa fa-pinterest"></i>
                </a>
                <a target="_blank" class="googleplus"
                   href="<?php echo esc_url($google); ?>"
                   title="<?php echo esc_attr('Google+') ?>"
                   onclick='window.open(this.href, "", "menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600");return false;'>
                    <i class="fa fa-google-plus"></i>
                </a>
            </div>
            <?php
        endif;
    }
}
if (!function_exists('nexio_post_tags')) {
    function nexio_post_tags()
    {
        if (!empty(get_the_terms(get_the_ID(), 'post_tag'))) : ?>
            <div class="tags"><?php $tags_list = get_the_tag_list('', ', ');
                if ($tags_list) {
                    printf(esc_html__('%1$s', 'nexio'), $tags_list);
                } ?></div>
        <?php endif;
    }
}
if (!function_exists('nexio_post_category')) {
    function nexio_post_category()
    {
        $items = array();
        $taxonomy_names = get_post_taxonomies();
        if (isset($taxonomy_names[0]) && !empty($taxonomy_names)) {
            $get_terms = get_the_terms(get_the_ID(), $taxonomy_names[0]);
        } else {
            $get_terms = array();
        }
        if (!is_wp_error($get_terms) && !empty($get_terms)) : ?>
            <div class="categories">
                <?php
                foreach ($get_terms as $term) {
                    $link = get_term_link($term->term_id, $taxonomy_names[0]);
                    $items[] = '<a href="' . esc_url($link) . '">' . esc_html($term->name) . '</a>';
                }
                echo join(', ', $items);
                ?>
            </div>
        <?php endif;
    }
}
