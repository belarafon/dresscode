<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link       https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package    WordPress
 * @subpackage Nexio
 * @since      1.0
 * @version    1.0
 */

get_header(); ?>
    <div class="container">
        <div class="text-center page-404">
            <h1 class="heading">
				<?php echo esc_html__( '404', 'nexio' ); ?>
            </h1>
            <h2 class="title"><?php echo esc_html__( 'We are sorry, the page you\'ve requested is not available', 'nexio' ); ?></h2>
			<?php get_search_form(); ?>
            <a class="button"
               href="<?php echo esc_url( get_home_url() ); ?>"><?php echo esc_html__( 'Back To Home Page', 'nexio' ); ?></a>
        </div>
    </div>
<?php get_footer();
