<?php

if ( ! class_exists( 'Nexio_Shortcode_Dealproduct' ) ) {
	class Nexio_Shortcode_Dealproduct extends Nexio_Shortcode {
		/**
		 * Shortcode name.
		 *
		 * @var  string
		 */
		public $shortcode = 'dealproduct';
		
		/**
		 * Default $atts .
		 *
		 * @var  array
		 */
		public $default_atts = array();
		
		public $product_thumb_width  = 700;
		public $product_thumb_height = 700;
		
		
		public static function generate_css( $atts ) {
			extract( $atts );
			$css = '';
			
			return $css;
		}
		
		public function output_html( $atts, $content = null ) {
			$atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'nexio_dealproduct', $atts ) : $atts;
			
			extract( $atts );
			$css_class   = array( 'nexio-dealproduct' );
			$css_class[] = $atts['el_class'];
			$css_class[] = $atts['dealproduct_custom_id'];
			$css_class[] = $atts['animate_on_scroll'];
			$css_class[] = 'style-' . $atts['style'];
			
			if ( function_exists( 'vc_shortcode_custom_css_class' ) ) {
				$css_class[] = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $atts['css'], ' ' ), '', $atts );
			}
			$product_size_args = array(
				'width'  => 320,
				'height' => 320
			);
			
			/* Product Size */
			if ( $atts['product_image_size'] ) {
				if ( $atts['product_image_size'] == 'custom' ) {
					$this->product_thumb_width  = $atts['product_custom_thumb_width'];
					$this->product_thumb_height = $atts['product_custom_thumb_height'];
				} else {
					$product_image_size         = explode( "x", $atts['product_image_size'] );
					$this->product_thumb_width  = $product_image_size[0];
					$this->product_thumb_height = $product_image_size[1];
				}
			}
			
			$product_size_args['width']  = $this->product_thumb_width;
			$product_size_args['height'] = $this->product_thumb_height;
			
			$products      = $this->getProducts( $atts );
			$total_product = $products->post_count;
			
			$product_item_class   = array( 'product-item', $atts['target'], 'famiau-wow-continuous nexio-wow fadeInUp' );
			$product_item_class[] = 'style-' . $atts['style'];
			
			$product_list_class = array();
			$owl_settings       = '';
			if ( $atts['style'] == '02' ) {
				if ( $total_product < $lg_items ) {
					$atts['owl_loop'] = 'false';
				}
				$product_list_class[] = 'product-grid product-list-owl owl-carousel equal-container better-height ' . $atts['nav_position'] . ' ' . $atts['nav_color'] . ' ' . $atts['nav_type'] . ' ' . $atts['dots_color'];
				$product_item_class[] = $owl_rows_space;
				$owl_settings         = $this->generate_carousel_data_attributes( '', $atts );
			}
			ob_start();
			?>
            <div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
				<?php if ( $products->have_posts() ): ?>
					<?php if ( $atts['style'] == '01' ): ?>
                        <div class="<?php echo esc_attr( implode( ' ', $product_list_class ) ); ?>">
							<?php while ( $products->have_posts() ) : $products->the_post(); ?>
                                <div id="post-<?php echo get_the_ID(); ?>" <?php post_class( $product_item_class ); ?>>
									<?php wc_get_template( 'product-deal/content-product-style-' . $atts['style'] . '.php', $product_size_args ); ?>
                                </div>
							<?php endwhile; ?>
                        </div>
					<?php elseif ( $atts['style'] == '02' ) : ?>
                        <!-- OWL Products -->
						<?php $i = 1; ?>
                        <div class="<?php echo esc_attr( implode( ' ', $product_list_class ) ); ?>" <?php echo force_balance_tags( $owl_settings ); ?>>
                            <div class="owl-one-row">
								<?php while ( $products->have_posts() ) : $products->the_post(); ?>
                                    <div <?php post_class( $product_item_class ); ?>>
										<?php wc_get_template( 'product-deal/content-product-style-' . $atts['style'] . '.php', $product_size_args ); ?>
                                    </div>
									<?php
									if ( $i % $owl_number_row == 0 && $i < $total_product ) {
										echo '</div><div class="owl-one-row">';
									}
									$i ++;
									?>
								<?php endwhile; ?>
                            </div>
                        </div>
					<?php endif; ?>
				<?php else: ?>
                    <p>
                        <strong><?php esc_html_e( 'No Product', 'nexio-toolkit' ); ?></strong>
                    </p>
				<?php endif; ?>
            </div>
			<?php
			wp_reset_postdata();
			$html = ob_get_clean();
			
			return apply_filters( 'Nexio_Shortcode_dealproduct', force_balance_tags( $html ), $atts, $content );
		}
	}
}