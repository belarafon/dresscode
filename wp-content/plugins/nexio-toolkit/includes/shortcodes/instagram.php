<?php

if ( ! class_exists( 'Nexio_Shortcode_Instagram' ) ) {

	class Nexio_Shortcode_Instagram extends Nexio_Shortcode {
		/**
		 * Shortcode name.
		 *
		 * @var  string
		 */
		public $shortcode = 'instagram';

		/**
		 * Default $atts .
		 *
		 * @var  array
		 */
		public $default_atts = array();


		public static function generate_css( $atts ) {
			$atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'nexio_instagram', $atts ) : $atts;
			// Extract shortcode parameters.
			extract( $atts );
			$css = '';

			return $css;
		}


		public function output_html( $atts, $content = null ) {
			$limit = $id = $token = '';
			$atts  = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'nexio_instagram', $atts ) : $atts;
			// Extract shortcode parameters.
			extract( $atts );
			$css_class   = array( 'nexio-instagram-sc' );
			$css_class[] = $atts['el_class'];
			$css_class[] = $atts['style'];
			$css_class[] = $atts['instagram_custom_id'];
			$css_class[] = $atts['animate_on_scroll'];
			if ( function_exists( 'vc_shortcode_custom_css_class' ) ) {
				$css_class[] = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $atts['css'], ' ' ), '', $atts );
			}
			$type_icon = isset( $atts['i_type'] ) ? $atts['i_type'] : '';
			if ( $type_icon == 'fontflaticon' ) {
				$class_icon = isset( $atts['icon_nexiocustomfonts'] ) ? $atts['icon_nexiocustomfonts'] : '';
			} else {
				$class_icon = isset( $atts['icon_fontawesome'] ) ? $atts['icon_fontawesome'] : '';
			}
			ob_start();
			?>
            <div class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>">
                <div class="instagram-inner">
					<?php if ( $atts['iconimage'] != '' && ($atts['style'] == 'style-01' || $atts['style'] == 'style-02' || $atts['style'] == 'style-03' || $atts['style'] == 'style-04')): ?>
                        <div class="icon">
							<?php if ( $atts['iconimage'] == 'imagetype' && $atts['image'] ): ?>
								<?php echo wp_get_attachment_image( $atts['image'], 'full' ); ?>
							<?php elseif ( $atts['iconimage'] == 'icontype' ): ?>
                                <span class="<?php echo esc_attr( $class_icon ); ?>"></span>
							<?php endif; ?>
                        </div>
					<?php endif; ?>
					<?php if ( $atts['title'] && ($atts['style'] == 'style-01' || $atts['style'] == 'style-03') || $atts['style'] == 'style-04'): ?>
                        <h3 class="title">
							<span><?php echo esc_html( $atts['title'] ) ?></span>
                        </h3>
					<?php endif; ?>
					<?php if ( $atts['desc'] && ($atts['style'] == 'style-03' || $atts['style'] == 'style-04') ): ?>
                        <div class="desc"><?php echo esc_html($atts['desc']); ?></div>
					<?php endif; ?>
					<?php
					if ( intval( $id ) === 0 ) {
						esc_html_e( 'No user ID specified.', 'nexio' );
					}
					$transient_var = $id . '_' . $limit;
					if ( $atts['style'] == 'style-04' ) {
						$transient_var = $id . '_8';
					}
					if ($atts['style'] == 'style-05') {
						$transient_var = $id . '_6';
					}
					$items = get_transient( $transient_var );
					if ( $id && $token ) {
						$response = wp_remote_get( 'https://api.instagram.com/v1/users/' . esc_attr( $id ) . '/media/recent/?access_token=' . esc_attr( $token ) . '&count=' . esc_attr( $limit ) );
						if ( $atts['style'] == 'style-04' ) {
							$response = wp_remote_get( 'https://api.instagram.com/v1/users/' . esc_attr( $id ) . '/media/recent/?access_token=' . esc_attr( $token ) . '&count=8' );
						}
						if ($atts['style'] == 'style-05') {
							$response = wp_remote_get('https://api.instagram.com/v1/users/' . esc_attr($id) . '/media/recent/?access_token=' . esc_attr($token) . '&count=6');
						}
						if ( ! is_wp_error( $response ) ) {
							$response_body = json_decode( $response['body'] );
							if ( $response_body->meta->code !== 200 ) {
								echo '<p>' . esc_html__( 'User ID and access token do not match. Please check again.', 'nexio' ) . '</p>';
							} else {
								$items_as_objects = $response_body->data;
								$items            = array();
								foreach ( $items_as_objects as $item_object ) {
									$item['link']     = $item_object->link;
									$item['url']      = $item_object->images->standard_resolution->url;
									$item['width']    = $item_object->images->standard_resolution->width;
									$item['height']   = $item_object->images->standard_resolution->height;
									$item['likes']    = $item_object->likes->count;
									$item['comments'] = $item_object->comments->count;
									$items[]          = $item;
								}
								set_transient( $transient_var, $items, 60 * 60 );
							}

						}
					}
					?>
					<?php if ( isset( $items ) && $items ): ?>
						<?php if ( $atts['style'] == 'style-01' || $atts['style'] == 'style-02' || $atts['style'] == 'style-03' ):
							$owl_settings = $this->generate_carousel_data_attributes( '', $atts );
							$owl_class[] = 'owl-carousel';
							$i = 1; ?>
                            <div class="<?php echo esc_attr( implode( ' ', $owl_class ) ); ?> " <?php echo force_balance_tags( $owl_settings ); ?>>
                                <div class="owl-one-row">
									<?php foreach ( $items as $item ): ?>
                                        <div class="item <?php echo $owl_rows_space;; ?>">
                                            <a target="_blank" href="<?php echo esc_url( $item['link'] ) ?>">
												<?php echo nexio_toolkit_img_output( $item ); ?>
                                            </a>
                                            <div class="info-img">
                                                <span class="social-info"><i
                                                            class="flaticon-comment"></i><?php echo esc_attr( $item['comments'] ); ?></span>
                                                <span class="social-info"><i
                                                            class="flaticon-heart"></i><?php echo esc_attr( $item['likes'] ); ?></span>
                                            </div>
                                        </div>
										<?php
										if ( $i % $owl_number_row == 0 && $i < $limit ) {
											echo '</div><div class="owl-one-row">';
										}
										$i ++;
										?>
									<?php endforeach; ?>
                                </div>
                            </div>
						<?php elseif ( $atts['style'] == 'style-04' ) : ?>
                            <div class="item-table">
                                <div class="item-cell">
                                    <div class="item">
                                        <a target="_blank" href="<?php echo esc_url( $items[0]['link'] ) ?>">
											<?php echo nexio_toolkit_img_output( $items[0] ); ?>
                                        </a>
                                        <div class="info-img">
                                            <span class="social-info"><i
                                                        class="flaticon-comment"></i><?php echo esc_attr( $items[0]['comments'] ); ?></span>
                                            <span class="social-info"><i
                                                        class="flaticon-heart"></i><?php echo esc_attr( $items[0]['likes'] ); ?>
                                                </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="item-cell">
                                    <div class="item">
                                        <a target="_blank" href="<?php echo esc_url( $items[1]['link'] ) ?>">
											<?php echo nexio_toolkit_img_output( $items[1] ); ?>
                                        </a>
                                        <div class="info-img">
                                                 <span class="social-info"><i
                                                             class="flaticon-comment"></i><?php echo esc_attr( $items[1]['comments'] ); ?>
                                                     </span>
                                            <span class="social-info"><i
                                                        class="flaticon-heart"></i><?php echo esc_attr( $items[1]['likes'] ); ?>
                                                </span>
                                        </div>
                                    </div>
                                    <div class="item">
                                        <a target="_blank" href="<?php echo esc_url( $items[2]['link'] ) ?>">
											<?php echo nexio_toolkit_img_output( $items[2] ); ?>
                                        </a>
                                        <div class="info-img">
                                             <span class="social-info"><i
                                                         class="flaticon-comment"></i><?php echo esc_attr( $items[2]['comments'] ); ?></span>
                                            <span class="social-info"><i
                                                        class="flaticon-heart"></i><?php echo esc_attr( $items[2]['likes'] ); ?>
                                                </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="item-cell">
                                    <div class="item">
                                        <a target="_blank" href="<?php echo esc_url( $items[3]['link'] ) ?>">
											<?php echo nexio_toolkit_img_output( $items[3] ); ?>
                                        </a>
                                        <div class="info-img">
                                            <span class="social-info"><i
                                                        class="flaticon-comment"></i><?php echo esc_attr( $items[3]['comments'] ); ?></span>
                                            <span class="social-info"><i
                                                        class="flaticon-heart"></i><?php echo esc_attr( $items[3]['likes'] ); ?>
                                                </span>
                                        </div>
                                    </div>
                                    <div class="item">
                                        <a target="_blank" href="<?php echo esc_url( $items[4]['link'] ) ?>">
											<?php echo nexio_toolkit_img_output( $items[4] ); ?>
                                        </a>
                                        <div class="info-img">
                                            <span class="social-info"><i
                                                        class="flaticon-comment"></i><?php echo esc_attr( $items[4]['comments'] ); ?></span>
                                            <span class="social-info"><i
                                                        class="flaticon-heart"></i><?php echo esc_attr( $items[4]['likes'] ); ?>
                                                </span>
                                        </div>
                                    </div>
                                    <div class="item">
                                        <a target="_blank" href="<?php echo esc_url( $items[5]['link'] ) ?>">
											<?php echo nexio_toolkit_img_output( $items[5] ); ?>
                                        </a>
                                        <div class="info-img">
                                             <span class="social-info"><i
                                                         class="flaticon-comment"></i><?php echo esc_attr( $items[5]['comments'] ); ?>
                                                 </span>
                                            <span class="social-info"><i
                                                        class="flaticon-heart"></i><?php echo esc_attr( $items[5]['likes'] ); ?>
                                                </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="item-cell">
                                    <div class="item">
                                        <a target="_blank" href="<?php echo esc_url( $items[6]['link'] ) ?>">
											<?php echo nexio_toolkit_img_output( $items[6] ); ?>
                                        </a>
                                        <div class="info-img">
                                            <span class="social-info"><i
                                                        class="flaticon-comment"></i><?php echo esc_attr( $items[6]['comments'] ); ?></span>
                                            <span class="social-info"><i
                                                        class="flaticon-heart"></i><?php echo esc_attr( $items[6]['likes'] ); ?>
                                                </span>
                                        </div>
                                    </div>
                                    <div class="item">
                                        <a target="_blank" href="<?php echo esc_url( $items[7]['link'] ) ?>">
											<?php echo nexio_toolkit_img_output( $items[7] ); ?>
                                        </a>
                                        <div class="info-img">
                                            <span class="social-info"><i
                                                        class="flaticon-comment"></i><?php echo esc_attr( $items[7]['comments'] ); ?></span>
                                            <span class="social-info"><i
                                                        class="flaticon-heart"></i><?php echo esc_attr( $items[7]['likes'] ); ?>
                                                </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
						<?php elseif ($atts['style'] == 'style-05') : ?>
                            <div class="item-wrap">
								<?php if ($atts['iconimage'] != ''): ?>
                                    <div class="icon">
										<?php if ($atts['iconimage'] == 'imagetype' && $atts['image']): ?>
											<?php echo wp_get_attachment_image($atts['image'], 'full'); ?>
										<?php elseif ($atts['iconimage'] == 'icontype'): ?>
                                            <span class="<?php echo esc_attr($class_icon); ?>"></span>
										<?php endif; ?>
                                    </div>
								<?php endif; ?>
								<?php if ($atts['title']): ?>
                                    <h3 class="title">
										<?php echo esc_html($atts['title']) ?>
                                    </h3>
								<?php endif; ?>
								<?php if ($atts['desc']): ?>
                                    <div class="desc"><?php echo esc_html($atts['desc']) ?></div>
								<?php endif; ?>
                            </div>
                            <div class="item-wrap">
                                <div class="item">
                                    <a target="_blank" href="<?php echo esc_url($items[0]['link']) ?>">
										<?php echo nexio_toolkit_img_output($items[0]); ?>
                                    </a>
                                    <div class="info-img">
                                            <span class="social-info"><i
                                                        class="flaticon-comment"></i><?php echo esc_attr($items[0]['comments']); ?></span>
                                        <span class="social-info"><i class="flaticon-heart"></i><?php echo esc_attr($items[0]['likes']); ?>
                                            </span>
                                    </div>
                                </div>
                            </div>
                            <div class="item-wrap">
                                <div class="item">
                                    <a target="_blank" href="<?php echo esc_url($items[1]['link']) ?>">
										<?php echo nexio_toolkit_img_output($items[1]); ?>
                                    </a>
                                    <div class="info-img">
                                             <span class="social-info"><i class="flaticon-comment"></i><?php echo esc_attr($items[1]['comments']); ?>
                                                 </span>
                                        <span class="social-info"><i class="flaticon-heart"></i><?php echo esc_attr($items[1]['likes']); ?>
                                            </span>
                                    </div>
                                </div>
                            </div>
                            <div class="item-wrap">
                                <div class="item">
                                    <a target="_blank" href="<?php echo esc_url($items[2]['link']) ?>">
										<?php echo nexio_toolkit_img_output($items[2]); ?>
                                    </a>
                                    <div class="info-img">
                                             <span class="social-info"><i
                                                         class="flaticon-comment"></i><?php echo esc_attr($items[2]['comments']); ?></span>
                                        <span class="social-info"><i class="flaticon-heart"></i><?php echo esc_attr($items[2]['likes']); ?>
                                            </span>
                                    </div>
                                </div>
                            </div>
                            <div class="item-wrap">
                                <div class="item">
                                    <a target="_blank" href="<?php echo esc_url($items[3]['link']) ?>">
										<?php echo nexio_toolkit_img_output($items[3]); ?>
                                    </a>
                                    <div class="info-img">
                                            <span class="social-info"><i
                                                        class="flaticon-comment"></i><?php echo esc_attr($items[3]['comments']); ?></span>
                                        <span class="social-info"><i class="flaticon-heart"></i><?php echo esc_attr($items[3]['likes']); ?>
                                            </span>
                                    </div>
                                </div>
                            </div>
                            <div class="item-wrap">
                                <div class="item">
                                    <a target="_blank" href="<?php echo esc_url($items[4]['link']) ?>">
										<?php echo nexio_toolkit_img_output($items[4]); ?>
                                    </a>
                                    <div class="info-img">
                                        <span class="social-info"><i
                                                    class="flaticon-comment"></i><?php echo esc_attr($items[4]['comments']); ?></span>
                                        <span class="social-info"><i
                                                    class="flaticon-heart"></i><?php echo esc_attr($items[4]['likes']); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="item-wrap">
                                <div class="item">
                                    <a target="_blank" href="<?php echo esc_url($items[5]['link']) ?>">
										<?php echo nexio_toolkit_img_output($items[5]); ?>
                                    </a>
                                    <div class="info-img">
                                             <span class="social-info"><i
                                                         class="flaticon-comment"></i><?php echo esc_attr($items[5]['comments']); ?>
                                                 </span>
                                        <span class="social-info"><i
                                                    class="flaticon-heart"></i><?php echo esc_attr($items[5]['likes']); ?>
                                            </span>
                                    </div>
                                </div>
                            </div>
						<?php endif; ?>
					<?php endif; ?>
                </div>
            </div>
			<?php
			$html = ob_get_clean();

			return apply_filters( 'nexio_toolkit_shortcode_instagram', force_balance_tags( $html ), $atts, $content );
		}
	}
}
