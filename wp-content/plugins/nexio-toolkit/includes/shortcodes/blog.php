<?php

if ( ! class_exists( 'Nexio_Shortcode_blog' ) ) {
	class Nexio_Shortcode_blog extends Nexio_Shortcode {
		/**
		 * Shortcode name.
		 *
		 * @var  string
		 */
		public $shortcode = 'blog';


		/**
		 * Default $atts .
		 *
		 * @var  array
		 */
		public $default_atts = array();


		public static function generate_css( $atts ) {
			// Extract shortcode parameters.
			extract( $atts );
			$css = '';

			return $css;
		}


		public function output_html( $atts, $content = null ) {
			$atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'nexio_blog', $atts ) : $atts;

			// Extract shortcode parameters.
			extract( $atts );

			$css_class   = array( 'nexio-blog' );
			$css_class[] = $atts['style'];
			$css_class[] = $atts['el_class'];
			$css_class[] = $atts['blog_custom_id'];
			$css_class[] = $atts['animate_on_scroll'];
			if ( function_exists( 'vc_shortcode_custom_css_class' ) ) {
				$css_class[] = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $atts['css'], ' ' ), '', $atts );
			}
			$loop_posts = vc_build_loop_query( $atts['loop_query'] )[1];
			ob_start();
			?>
			<?php if ( $loop_posts->have_posts() ) : ?>
                <div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
					<?php if ( $atts['style'] == 'style-01' ): ?>
						<?php while ( $loop_posts->have_posts() ) : $loop_posts->the_post() ?>
                            <div <?php post_class( 'post-item equal-container equal-container better-height' ); ?>>
								<?php
								if ( has_post_thumbnail() ) :
									$nexio_post_meta = get_post_meta( get_the_ID(), '_custom_post_woo_options', true );
									$gallery_post = isset( $nexio_post_meta['post-gallery'] ) ? $nexio_post_meta['post-gallery'] : '';
									$post_format = get_post_format();
									$width = 462;
									$height = 480;
									?>
                                    <div class="post-thumb equal-elem">
										<?php
										if ( $post_format == 'gallery' && $gallery_post != '' ) {
											$gallery_post   = explode( ',', $gallery_post );
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

											$data_reponsive = json_encode( $data_reponsive );
											$loop           = 'false';
											$dots           = 'false';
											$data_margin    = '0';
											?>
                                            <div class="owl-carousel"
                                                 data-margin="<?php echo esc_attr( $data_margin ); ?>" data-nav="true"
                                                 data-dots="<?php echo esc_attr( $dots ); ?>"
                                                 data-loop="<?php echo esc_attr( $loop ); ?>"
                                                 data-responsive='<?php echo esc_attr( $data_reponsive ); ?>'>
                                                <figure>
													<?php
													$image_thumb = nexio_toolkit_resize_image( get_post_thumbnail_id(), null, $width, $height, true, false, false );
													echo nexio_toolkit_img_output( $image_thumb );
													?>
                                                </figure>
												<?php foreach ( $gallery_post as $item ) : ?>
                                                    <figure>
														<?php
														$image_gallery = nexio_toolkit_resize_image( $item, null, $width, $height, true, false, false );
														echo nexio_toolkit_img_output( $image_gallery );
														?>
                                                    </figure>
												<?php endforeach; ?>
                                            </div>
										<?php } else {
											$image_thumb = nexio_toolkit_resize_image( get_post_thumbnail_id(), null, $width, $height, true, false, false );
											echo '<a href="' . get_permalink() . '">';
											echo nexio_toolkit_img_output( $image_thumb );
											echo '</a>';
										}
										?>
                                    </div>
								<?php endif; ?>
                                <div class="post-info equal-elem">
                                    <div class="post-info-inner">
                                        <div class="post-info-bg">
                                            <div class="date-up">
                                                <a href="<?php the_permalink(); ?>">
													<?php echo get_the_date(); ?>
                                                </a>
                                            </div>
                                            <h2 class="post-title"><a
                                                        href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                            </h2>
                                            <div class="post-content">
												<?php echo wp_trim_words( apply_filters( 'the_excerpt', get_the_excerpt() ), 15, esc_html__( '...', 'nexio-toolkit' ) ); ?>
                                            </div>
                                            <a class="readmore"
                                               href="<?php the_permalink(); ?>"><?php echo esc_html__( 'Read more', 'nexio-toolkit' ); ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
						<?php endwhile; ?>
					<?php elseif( $atts['style'] == 'style-04' ):?>
						<?php while ( $loop_posts->have_posts() ) : $loop_posts->the_post() ?>
                            <div <?php post_class('post-item'); ?>>
                                <div class="post-thumb">
									<?php if ( has_post_thumbnail() ) {
										$image_thumb = nexio_toolkit_resize_image( get_post_thumbnail_id(), null, 79, 79, true, false, false ); ?>
                                        <a href="<?php the_permalink(); ?>">
											<?php echo nexio_toolkit_img_output( $image_thumb, 'attachment-post-thumbnail wp-post-image', get_the_title() ); ?>
                                        </a>
									<?php } ?>
                                </div>
                                <div class="post-content">
                                    <div class="post-meta">
                                        <div class="date">
                                            <a href="<?php the_permalink(); ?>">
	                                            <?php echo get_the_date('d.m.Y'); ?>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="post-info">
                                        <h2 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                                    </div>
                                </div>
                            </div>
						<?php endwhile; ?>
					<?php else:
						$owl_class[] = 'owl-carousel' .$atts['nav_color'].' '.$atts['dots_color'];
						$owl_settings = $this->generate_carousel_data_attributes( '', $atts );
						?>
						<?php if ( $atts['title'] ): ?>
                            <h3 class="blog-heading"><?php echo esc_html( $atts['title'] ) ?></h3>
                        <?php endif; ?>
                        <div class="owl-carousel equal-container better-height <?php echo esc_attr( implode( ' ', $owl_class ) ); ?>" <?php echo force_balance_tags( $owl_settings ); ?>>
							<?php while ( $loop_posts->have_posts() ) : $loop_posts->the_post() ?>
                                <?php
                                $classes = array('post-item');
					            if ( $atts['style'] == 'style-02' ) {
						            $classes[] = 'blog-grid';
                                } ?>
                                <div <?php post_class($classes); ?>>
                                    <div class="post-thumb">
										<?php if ( has_post_thumbnail() ) {
											$image_thumb = nexio_toolkit_resize_image( get_post_thumbnail_id(), null, 440, 333, true, false, false ); ?>
                                            <a href="<?php the_permalink(); ?>">
												<?php echo nexio_toolkit_img_output( $image_thumb, 'attachment-post-thumbnail wp-post-image', get_the_title() ); ?>
                                            </a>
										<?php } ?>
                                    </div>
                                    <div class="post-content">
								        <?php if ( $atts['style'] == 'style-02' ): ?>
                                            <div class="post-meta">
                                                <?php nexio_post_category(); ?>
                                                <div class="date">
                                                    <a href="<?php the_permalink(); ?>">
                                                        <?php echo get_the_date(); ?>
                                                    </a>
                                                </div>
                                            </div>
								        <?php endif; ?>
                                        <div class="post-info">
                                            <h2 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                                            <div class="post-excerpt-content">
												<?php echo wp_trim_words( apply_filters( 'the_excerpt', get_the_excerpt() ), 14, esc_html__( '...', 'nexio-toolkit' ) ); ?>
                                            </div>
                                        </div>
                                        <div class="post-foot">
                                            <a class="readmore"
                                               href="<?php the_permalink(); ?>"><?php echo esc_html__( 'Read more', 'nexio-toolkit' ); ?></a>
                                        </div>
	                                    <?php if ( $atts['style'] == 'style-03' ): ?>
                                            <div class="post-meta">
	                                            <?php
                                                nexio_post_comment();
	                                            nexio_post_datebox();
	                                            ?>
                                            </div>
	                                    <?php endif; ?>
                                    </div>
                                </div>
							<?php endwhile; ?>
                        </div>
					<?php endif; ?>
                </div>
			<?php else : ?>
				<?php get_template_part( 'content', 'none' ); ?>
			<?php endif; ?>
			<?php
			$array_filter = array(
				'query' => $loop_posts,
			);
			wp_reset_postdata();
			$html = ob_get_clean();

			return apply_filters( 'Nexio_Shortcode_blog', $html, $atts, $content, $array_filter );
		}
	}
}