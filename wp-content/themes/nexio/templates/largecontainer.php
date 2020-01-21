<?php
/**
 * Template Name: Large Container Page
 *
 * @package WordPress
 * @subpackage Nexio
 * @since Nexio 1.0
 */
get_header();

?>
	<div class="largecontainer-template">
		<div class="nexio-container">
			<?php

			// Start the loop.
			while ( have_posts() ) : the_post();
				?>
				<?php the_content(); ?>
				<?php
				// End the loop.
			endwhile;
			?>
		</div>
	</div>
<?php
get_footer();