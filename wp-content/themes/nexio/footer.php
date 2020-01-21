<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link       https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package    WordPress
 * @subpackage Nexio
 * @since      1.0
 * @version    1.0
 */

?>
<?php nexio_get_footer(); ?>
<?php nexio_get_popup_newsletter(); ?>
<a href="#" class="backtotop">
    <i class="fa fa-angle-up"></i>
</a>
</div>
</div> <!-- End .page-wrapper -->
<?php do_action( 'nexio_after_page_wrapper' ); ?>
<?php wp_footer(); ?>
</body>
</html>
