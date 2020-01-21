<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
$quickview_style = nexio_get_option( 'quickview_style', '' );
$classes_inner = '';
if($quickview_style != 'quickdrawer') {
	$classes_inner = 'scrollbar-macosx';
}
while ( have_posts() ) : the_post(); ?>

 <div class="product">

	<div id="product-<?php the_ID(); ?>" <?php post_class('product'); ?>>

        <?php wc_get_template_part('single-product/content','quickview');?>
		<div class="summary entry-summary">
			<div class="summary-content <?php echo esc_attr($classes_inner)?>">
				<?php do_action( 'yith_wcqv_product_summary' ); ?>
			</div>
		</div>

	</div>

</div>

<?php endwhile; // end of the loop.