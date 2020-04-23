<?php
$post_format = get_post_format();
do_action('nexio_before_single_blog_content');
?>
    <article <?php post_class('post-item post-single'); ?>>
        <div class="single-post-thumb">
            <?php nexio_post_format(); ?>
        </div>
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
        nexio_post_full_content();
        ?>
        <?php
        nexio_post_tags();
        nexio_paging_nav();
        nexio_share_button();
        ?>
    </article>
<?php
do_action('nexio_after_single_blog_content');