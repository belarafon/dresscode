<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link    https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Nexio
 */

get_header();
$term_id       = get_queried_object_id();
$sidebar_isset = wp_get_sidebars_widgets();

/* Blog Layout */
$nexio_blog_layout       = nexio_get_option( 'nexio_blog_layout', 'left' );
$nexio_blog_style        = nexio_get_option( 'blog-style', 'standard' );
$nexio_blog_used_sidebar = nexio_get_option( 'blog_sidebar', 'primary_sidebar' );
$nexio_container_class   = array( 'main-container' );

if ( is_single() ) {
	
	/*Single post layout*/
	$nexio_blog_layout       = nexio_get_option( 'sidebar_single_post_position', 'left' );
	$nexio_blog_used_sidebar = nexio_get_option( 'single_post_sidebar', 'primary_sidebar' );
}

if ( isset( $sidebar_isset[ $nexio_blog_used_sidebar ] ) && empty( $sidebar_isset[ $nexio_blog_used_sidebar ] ) ) {
	$nexio_blog_layout = 'full';
}

if ( $nexio_blog_layout == 'full' ) {
	$nexio_container_class[] = 'no-sidebar';
} else {
	$nexio_container_class[] = $nexio_blog_layout . '-sidebar has-sidebar';
}

if ( $nexio_blog_style == 'modern' ) {
	$nexio_container_class[] = 'blog-bg';
}

$nexio_content_class   = array();
$nexio_content_class[] = 'main-content';

if ( $nexio_blog_layout == 'full' ) {
	$nexio_content_class[] = 'col-sm-12 col-xs-12';
} else {
	$nexio_content_class[] = 'col-lg-9 col-md-8 col-sm-12 col-xs-12';
}

$nexio_sidebar_class   = array();
$nexio_sidebar_class[] = 'sidebar';

if ( $nexio_blog_layout != 'full' ) {
	$nexio_sidebar_class[] = 'col-lg-3 col-md-4 col-sm-12 col-xs-12';
}

?>
<div class="<?php echo esc_attr( implode( ' ', $nexio_container_class ) ); ?>">
    <!-- POST LAYOUT -->
	<?php if ( is_single() ) { ?>
        <div class="nexio-breadcrumb container">
			<?php get_template_part( 'template-parts/part', 'breadcrumb' ); ?>
        </div>
	<?php } ?>
    <div class="container">
        <div class="row">
            <div class="<?php echo esc_attr( implode( ' ', $nexio_content_class ) ); ?>">
				<?php
				if ( is_single() ) {
					while ( have_posts() ): the_post();
						get_template_part( 'templates/blog/blog', 'single' );
						/*If comments are open or we have at least one comment, load up the comment template.*/
						if ( comments_open() || get_comments_number() ) :
							comments_template();
						endif;
					endwhile;
					wp_reset_postdata();
				} else {
					get_template_part( 'templates/blog/blog', $nexio_blog_style );
				} ?>
            </div>
			<?php if ( $nexio_blog_layout != 'full' ): ?>
                <div class="<?php echo esc_attr( implode( ' ', $nexio_sidebar_class ) ); ?>">
					<?php get_sidebar(); ?>
                </div>
			<?php endif; ?>
        </div>
    </div>
</div>
<?php get_footer(); ?>

