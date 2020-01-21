<?php
if ( ! class_exists( 'Responsive_Js_Composer_Shortcode' ) ) {
	class Responsive_Js_Composer_Shortcode {
		/**
		 * Shortcode name.
		 *
		 * @var  string
		 */
		public $shortcode = '';

		/**
		 * Meta key.
		 *
		 * @var  string
		 */
		protected $metakey = RESPONSIVE_JS_COMPOSER_METAKEY;

		public function __construct() {
			// Hook into post saving.
			add_action( 'save_post', array( &$this, 'update_post' ) );
			add_action( 'vc_after_mapping', array( &$this, 'add_param_all_shortcode' ) );
		}


		public function add_param_all_shortcode() {
			global $shortcode_tags;
			WPBMap::addAllMappedShortcodes();
			if ( count( $shortcode_tags ) > 0 ) {
				if ( isset( $shortcode_tags['vc_row'] ) ) {
					$attributes = array(
						array(
							'type'        => 'checkbox',
							'heading'     => esc_html__( 'Disable on real mobile', 'fami-responsive-js-composer' ),
							'param_name'  => 'disable_row_on_real_mobile',
							// Inner param name.
							'description' => esc_html__( 'If checked the row will be removed on real mobile devices. You can switch it back any time.', 'fami-responsive-js-composer' ),
							'value'       => array( esc_html__( 'Yes', 'fami-responsive-js-composer' ) => 'yes' ),
						),
						array(
							'type'       => 'checkbox',
							'heading'    => esc_html__( 'Overflow hidden', 'fami-responsive-js-composer' ),
							'param_name' => 'overflow_hidden',
							'value'      => array( esc_html__( 'Yes', 'fami-responsive-js-composer' ) => 'yes' ),
						),
					);
					vc_add_params( 'vc_row', $attributes );
				}
				foreach ( $shortcode_tags as $code => $function ) {
					if($code != 'woocommerce_order_tracking' && $code !='woocommerce_cart' && $code != 'woocommerce_checkout') {
						$attributes = array(
							array(
								'type'        => 'checkbox',
								'heading'     => esc_html__('Enbale Design Options', 'azora'),
								'param_name'  => 'enbale_extend_design_options',
								'value'       => array(esc_html__('Yes', 'responsive-js-composer') => 'yes'),
								'std'         => '',
								'group'       => esc_html__( 'Design Options', 'responsive-js-composer' ),
							),
							array(
								'type' => 'param_group',
								'value' => '',
								'param_name' => 'vc_custom_design_options_reponsive',
								"heading"     => esc_html__("Extend Design Options", 'responsive-js-composer'),
								'params' => array(
									array(
										'type'        => 'dropdown',
										'heading'     => esc_html__( 'Screen Device', 'responsive-js-composer' ),
										'param_name'  => 'screen',
										'value'       => array(
											esc_html__( '1366px', 'responsive-js-composer' ) => '1366',
											esc_html__( '1280px', 'responsive-js-composer' ) => '1280',
											esc_html__('991px', 'responsive-js-composer')    => '991',
											esc_html__('767px ', 'responsive-js-composer')   => '767',
											esc_html__('480px ', 'responsive-js-composer')   => '480',
											esc_html__('320px ', 'responsive-js-composer')   => '320',
											esc_html__('Custom ', 'responsive-js-composer')  => 'custom',
										),
										'std'=>'1366',
										'admin_label' => true,
									),
									array(
										"type"        => "textfield",
										"heading"     => esc_html__("Screen Custom", 'responsive-js-composer'),
										"param_name"  => "screen_custom",
										"suffix"      => esc_html__("px", 'responsive-js-composer'),
										"dependency"  => array("element" => "screen", "value" => array( 'custom' )),
									),
									array(
										"type"       => "textfield",
										"heading"    => esc_html__("Padding Top", 'responsive-js-composer'),
										"param_name" => "padding_top",
										'edit_field_class' => 'vc_col-sm-3',
									),
									array(
										"type"       => "textfield",
										"heading"    => esc_html__("Padding Right", 'responsive-js-composer'),
										"param_name" => "padding_right",
										'edit_field_class' => 'vc_col-sm-3',
									),
									array(
										"type"       => "textfield",
										"heading"    => esc_html__("Padding Bottom", 'responsive-js-composer'),
										"param_name" => "padding_bottom",
										'edit_field_class' => 'vc_col-sm-3',
									),
									array(
										"type"       => "textfield",
										"heading"    => esc_html__("Padding Left", 'responsive-js-composer'),
										"param_name" => "padding_left",
										'edit_field_class' => 'vc_col-sm-3',
									),

									// margin
									array(
										"type"       => "textfield",
										"heading"    => esc_html__("Margin Top", 'responsive-js-composer'),
										"param_name" => "margin_top",
										'edit_field_class' => 'vc_col-sm-3',
									),
									array(
										"type"       => "textfield",
										"heading"    => esc_html__("Margin Right", 'responsive-js-composer'),
										"param_name" => "margin_right",
										'edit_field_class' => 'vc_col-sm-3',
									),
									array(
										"type"       => "textfield",
										"heading"    => esc_html__("Margin Bottom", 'responsive-js-composer'),
										"param_name" => "margin_bottom",
										'edit_field_class' => 'vc_col-sm-3',
									),
									array(
										"type"       => "textfield",
										"heading"    => esc_html__("Margin Left", 'responsive-js-composer'),
										"param_name" => "margin_left",
										'edit_field_class' => 'vc_col-sm-3',
									),
									// Border
									array(
										"type"       => "textfield",
										"heading"    => esc_html__("Border Top", 'responsive-js-composer'),
										"param_name" => "border_top",
										'edit_field_class' => 'vc_col-sm-3',
									),
									array(
										"type"       => "textfield",
										"heading"    => esc_html__("Border Right", 'responsive-js-composer'),
										"param_name" => "border_right",
										'edit_field_class' => 'vc_col-sm-3',
									),
									array(
										"type"       => "textfield",
										"heading"    => esc_html__("Border Bottom", 'responsive-js-composer'),
										"param_name" => "border_bottom",
										'edit_field_class' => 'vc_col-sm-3',
									),
									array(
										"type"       => "textfield",
										"heading"    => esc_html__("Border Left", 'responsive-js-composer'),
										"param_name" => "border_left",
										'edit_field_class' => 'vc_col-sm-3',
									),
									array(
										"type" => "colorpicker",
										"heading"    => esc_html__("Border Color", 'responsive-js-composer'),
										"param_name" => "border_color",
										'edit_field_class' => 'vc_col-sm-3',
									),
									array(
										"type" => "dropdown",
										"heading"    => esc_html__("Border style", 'responsive-js-composer'),
										"param_name" => "border_style",
										'edit_field_class' => 'vc_col-sm-3',
										'value'=> array(
											'Theme Default' => '',
											'Solid'         => 'solid',
											'Dotted'        => 'dotted',
											'Dashed'        => 'dashed',
											'None'          => 'none',
											'Hidden'        => 'hidden',
											'Double'        => 'double',
											'Groove'        => 'groove',
											'Ridge'         => 'ridge',
											'Inset'         => 'inset',
											'Outset'        => 'outset',
											'Initial'       => 'initial',
											'Inherit'       => 'inherit',
										)
									),
									array(
										"type"       => "textfield",
										"heading"    => esc_html__("Border Radius", 'responsive-js-composer'),
										"param_name" => "border_radius",
										'edit_field_class' => 'vc_col-sm-6',
									),

									// Background
									array(
										"type" => "colorpicker",
										"heading"    => esc_html__("Background Color", 'responsive-js-composer'),
										"param_name" => "background_color",
										'edit_field_class' => 'vc_col-sm-6',
									),
									array(
										"type"        => "attach_image",
										"heading"     => __( "Background Image", "responsive-js-composer" ),
										"param_name"  => "background_image",
									),
									array(
										'type'        => 'checkbox',
										'heading'     => esc_html__('Background Image None', 'azora'),
										'param_name'  => 'background_image_none',
										'value'       => array(esc_html__('Yes', 'responsive-js-composer') => 'yes'),
										'std'         => '',
									),
									array(
										"type" => "dropdown",
										"heading"    => esc_html__("Background Style", 'responsive-js-composer'),
										"param_name" => "background_style",
										'value'=> array(
											__('Theme Default','responsive-js-composer') => '',
											__('Cover', 'responsive-js-composer')        => 'cover',
											__('Contain', 'responsive-js-composer')      => 'contain',
											__('No Repeat', 'responsive-js-composer')    => 'no-repeat',
											__('Repeat', 'responsive-js-composer')       => 'repeat',
										)
									),

									array(
										'type'        => 'checkbox',
										'heading'     => esc_html__('Box Shadow', 'azora'),
										'param_name'  => 'box_shadow',
										'value'       => array(esc_html__('Yes', 'responsive-js-composer') => 'yes'),
										'std'         => '',
									),

									array(
										"type"       => "textfield",
										"heading"    => esc_html__("Horizontal Length", 'responsive-js-composer'),
										"param_name" => "horizontal_length",
										'edit_field_class' => 'vc_col-sm-3',
										"dependency"  => array(
											"element" => "box_shadow", "value" => array( 'yes' ),
										),
									),
									array(
										"type"       => "textfield",
										"heading"    => esc_html__("Vertical Length", 'responsive-js-composer'),
										"param_name" => "vertical_length",
										'edit_field_class' => 'vc_col-sm-3',
										"dependency"  => array(
											"element" => "box_shadow", "value" => array( 'yes' ),
										),
									),
									array(
										"type"       => "textfield",
										"heading"    => esc_html__("Blur Radius", 'responsive-js-composer'),
										"param_name" => "blur_radius",
										'edit_field_class' => 'vc_col-sm-3',
										"dependency"  => array(
											"element" => "box_shadow", "value" => array( 'yes' ),
										),
									),
									array(
										"type"       => "textfield",
										"heading"    => esc_html__("Spread Radius", 'responsive-js-composer'),
										"param_name" => "spread_radius",
										'edit_field_class' => 'vc_col-sm-3',
										"dependency"  => array(
											"element" => "box_shadow", "value" => array( 'yes' ),
										),
									),
									array(
										"type" => "colorpicker",
										"heading"    => esc_html__("Shadow Color", 'responsive-js-composer'),
										"param_name" => "shadow_color",
										'edit_field_class' => 'vc_col-sm-3',
										"dependency"  => array(
											"element" => "box_shadow", "value" => array( 'yes' ),
										),
									),
								),
								'group'       => esc_html__( 'Design Options', 'responsive-js-composer' ),
								"dependency"  => array(
									"element" => "enbale_extend_design_options", "value" => array( 'yes' ),
								),
							),
							array(
								'param_name'       => 'responsive_js_composer_custom_id',
								'heading'          => esc_html__('Hidden ID', 'responsive-js-composer'),
								'type'             => 'responsive_js_composer_uniqid',
								'edit_field_class' => 'hidden',
							),
						);
						vc_add_params( $code, $attributes );
					}
				}
			}

		}

		/**
		 * Replace and save custom css to post meta.
		 *
		 * @param   int $post_id
		 *
		 * @return  void
		 */
		public function update_post( $post_id ) {
			if ( ! isset( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
				return;
			}

			// Set and replace content.
			$post = $this->replace_post( $post_id );
			if ( $post ) {
				// Generate custom CSS.
				$css = $this->get_css( $post->post_content );
				// Update post and save CSS to post meta.
				$this->save_post( $post );
				$this->save_postmeta( $post_id, $css );
			} else {
				$this->save_postmeta( $post_id, '' );
			}
		}

		/**
		 * Replace shortcode used in a post with real content.
		 *
		 * @param   int $post_id Post ID.
		 *
		 * @return  WP_Post object or null.
		 */
		public function replace_post( $post_id ) {
			// Get post.
			$post = get_post( $post_id );
			if ( $post ) {
				$post->post_content = preg_replace_callback(
					'/(responsive_js_composer_custom_id)="[^"]+"/',
					'responsive_js_composer_shortcode_replace_post_callback',
					$post->post_content
				);
			}

			return $post;
		}

		/**
		 * Parse shortcode custom css string.
		 *
		 * @param   string $content
		 * @param   string $shortcode
		 *
		 * @return  string
		 */
		public function get_css( $content ) {
			$css = '';
			WPBMap::addAllMappedShortcodes();
			if ( preg_match_all( '/' . get_shortcode_regex() . '/', $content, $shortcodes ) ) {
				foreach ( $shortcodes[2] as $index => $tag ) {
					$atts      = shortcode_parse_atts( trim( $shortcodes[3][ $index ] ) );
					$shortcode = $tag;
					$class     = 'Responsive_Js_Composer_' . implode( '_', array_map( 'ucfirst', explode( '-', $shortcode ) ) );
					if ( class_exists( $class ) ) {
						$css .= $class::generate_css( $atts );
					}

					$css .= self::generate_css_designs_option( $atts, $shortcode );
				}

				foreach ( $shortcodes[5] as $shortcode_content ) {
					$css .= $this->get_css( $shortcode_content );
				}
			}

			return $css;
		}

		/**
		 * Update post data content.
		 *
		 * @param   int $post WP_Post object.
		 *
		 * @return  void
		 */
		public function save_post( $post ) {
			// Sanitize post data for inserting into database.
			$data = sanitize_post( $post, 'db' );

			// Update post content.
			global $wpdb;

			$wpdb->query( "UPDATE {$wpdb->posts} SET post_content = '" . esc_sql( $data->post_content ) . "' WHERE ID = {$data->ID};" );

			// Update post cache.
			$data = sanitize_post( $post, 'raw' );

			wp_cache_replace( $data->ID, $data, 'posts' );
		}

		/**
		 * Update extra post meta.
		 *
		 * @param   int    $post_id Post ID.
		 * @param   string $css     Custom CSS.
		 *
		 * @return  void
		 */
		public function save_postmeta( $post_id, $css ) {
			if ( $post_id && $this->metakey ) {
				if ( empty( $css ) ) {
					delete_post_meta( $post_id, $this->metakey );
				} else {
					update_post_meta( $post_id, $this->metakey, preg_replace( '/[\t\r\n]/', '', $css ) );
				}
			}
		}

		/**
		 * Generate custom CSS.
		 *
		 * @param   array $atts Shortcode parameters.
		 *
		 * @return  string
		 */
		public static function generate_css( $atts ) {
			return '';
		}

		public function generate_design_option_css( $atts ) {
			$css = '';


			// padding
			if ( isset( $atts['padding_top'] ) && $atts['padding_top'] != "" ) {
				$unit = 'px';
				if ( ! is_numeric( $atts['padding_top'] ) ) {
					$unit = '';
				}
				$css .= 'padding-top: ' . $atts['padding_top'] . '' . $unit . '!important;';
			}
			if ( isset( $atts['padding_right'] ) && $atts['padding_right'] != "" ) {
				$unit = 'px';
				if ( ! is_numeric( $atts['padding_right'] ) ) {
					$unit = '';
				}
				$css .= 'padding-right: ' . $atts['padding_right'] . '' . $unit . '!important;';
			}
			if ( isset( $atts['padding_bottom'] ) && $atts['padding_bottom'] != "" ) {
				$unit = 'px';
				if ( ! is_numeric( $atts['padding_bottom'] ) ) {
					$unit = '';
				}
				$css .= 'padding-bottom: ' . $atts['padding_bottom'] . '' . $unit . '!important;';
			}
			if ( isset( $atts['padding_left'] ) && $atts['padding_left'] != "" ) {
				$unit = 'px';
				if ( ! is_numeric( $atts['padding_left'] ) ) {
					$unit = '';
				}
				$css .= 'padding-left: ' . $atts['padding_left'] . '' . $unit . '!important;';
			}
			// Margin
			if ( isset( $atts['margin_top'] ) && $atts['margin_top'] != "" ) {
				$unit = 'px';
				if ( ! is_numeric( $atts['margin_top'] ) ) {
					$unit = '';
				}
				$css .= 'margin-top: ' . $atts['margin_top'] . '' . $unit . '!important;';
			}
			if ( isset( $atts['margin_right'] ) && $atts['margin_right'] != "" ) {
				$unit = 'px';
				if ( ! is_numeric( $atts['margin_right'] ) ) {
					$unit = '';
				}
				$css .= 'margin-right: ' . $atts['margin_right'] . '' . $unit . '!important;';
			}
			if ( isset( $atts['margin_bottom'] ) && $atts['margin_bottom'] != "" ) {
				$unit = 'px';
				if ( ! is_numeric( $atts['margin_bottom'] ) ) {
					$unit = '';
				}
				$css .= 'margin-bottom: ' . $atts['margin_bottom'] . '' . $unit . '!important;';
			}
			if ( isset( $atts['margin_left'] ) && $atts['margin_left'] != "" ) {
				$unit = 'px';
				if ( ! is_numeric( $atts['margin_left'] ) ) {
					$unit = '';
				}
				$css .= 'margin-left: ' . $atts['margin_left'] . '' . $unit . '!important;';
			}

			// Border
			if ( isset( $atts['border_top'] ) && $atts['border_top'] != "" ) {
				$unit = 'px';
				if ( ! is_numeric( $atts['border_top'] ) ) {
					$unit = '';
				}
				$css .= 'border-top-width: ' . $atts['border_top'] . '' . $unit . '!important;';
			}
			if ( isset( $atts['border_right'] ) && $atts['border_right'] != "" ) {
				$unit = 'px';
				if ( ! is_numeric( $atts['border_right'] ) ) {
					$unit = '';
				}
				$css .= 'border-right-width: ' . $atts['border_right'] . '' . $unit . '!important;';
			}
			if ( isset( $atts['border_bottom'] ) && $atts['border_bottom'] != "" ) {
				$unit = 'px';
				if ( ! is_numeric( $atts['border_bottom'] ) ) {
					$unit = '';
				}
				$css .= 'border-bottom-width: ' . $atts['border_bottom'] . '' . $unit . '!important;';
			}
			if ( isset( $atts['border_left'] ) && is_numeric( $atts['border_left'] ) && $atts['border_left'] != "" ) {
				$unit = 'px';
				if ( ! is_numeric( $atts['border_bottom'] ) ) {
					$unit = '';
				}
				$css .= 'border-left-width: ' . $atts['border_left'] . '' . $unit . '!important;';
			}
			if ( isset( $atts['border_style'] ) && $atts['border_style'] != "" ) {
				$css .= 'border-style: ' . $atts['border_style'] . '!important;';
			}
			if ( isset( $atts['border_radius'] ) && $atts['border_radius'] != "" ) {
				$unit = 'px';
				if ( ! is_numeric( $atts['border_radius'] ) ) {
					$unit = '';
				}
				$css .= 'border-radius: ' . $atts['border_radius'] . '' . $unit . '!important;';
			}
			if ( isset( $atts['border_color'] ) && $atts['border_color'] != "" ) {
				$css .= 'border-color: ' . $atts['border_color'] . '!important;';
			}

			// Background
			if ( isset( $atts['background_color'] ) && $atts['background_color'] != "" ) {
				$css .= 'background-color:' . $atts['background_color'] . '!important;';
			}
			if ( isset( $atts['background_image'] ) && $atts['background_image'] != "" && is_numeric( $atts['background_image'] ) && $atts['background_image'] > 0 ) {
				$image = wp_get_attachment_image_src( $atts['background_image'], 'full' );

				if ( $image && isset( $image['0'] ) ) {
					$css .= 'background-image: url("' . $image['0'] . '")!important;';
				}

			}
			if ( isset( $atts['background_style'] ) && $atts['background_style'] != "" ) {
				$css .= 'background-size:' . $atts['background_style'] . '!important;';
			}

			if ( isset( $atts['background_image_none'] ) && $atts['background_image_none'] == 'yes' ) {
				$css .= 'background-image:none!important;';
			}

			// box_shadow
			if ( isset( $atts['box_shadow'] ) && $atts['box_shadow'] == 'yes' ) {

				$horizontal_length = isset( $atts['horizontal_length'] ) ? $atts['horizontal_length'] : 0;
				$vertical_length   = isset( $atts['vertical_length'] ) ? $atts['vertical_length'] : 0;
				$blur_radius       = isset( $atts['blur_radius'] ) ? $atts['blur_radius'] : 0;
				$spread_radius     = isset( $atts['spread_radius'] ) ? $atts['spread_radius'] : 0;
				$shadow_color      = isset( $atts['shadow_color'] ) ? $atts['shadow_color'] : 'rgba(0,0,0,1)';

				$css .= '-webkit-box-shadow: ' . $horizontal_length . 'px ' . $vertical_length . 'px ' . $blur_radius . 'px ' . $spread_radius . 'px ' . $shadow_color . ';
                -moz-box-shadow: ' . $horizontal_length . 'px ' . $vertical_length . 'px ' . $blur_radius . 'px ' . $spread_radius . 'px ' . $shadow_color . ';
                box-shadow: ' . $horizontal_length . 'px ' . $vertical_length . 'px ' . $blur_radius . 'px ' . $spread_radius . 'px ' . $shadow_color . '';
			}

			return $css;
		}

		public function generate_css_designs_option( $atts, $shortcode ) {

			$css = '';
			if ( isset( $atts['enbale_extend_design_options'] ) && $atts['enbale_extend_design_options'] == 'yes' ) {
				$vc_custom_design_options_reponsive = array();
				if ( isset( $atts['vc_custom_design_options_reponsive'] ) ) {
					$vc_custom_design_options_reponsive = vc_param_group_parse_atts( $atts['vc_custom_design_options_reponsive'] );
				}

				if ( $vc_custom_design_options_reponsive && count( $vc_custom_design_options_reponsive ) > 0 ) {
					foreach ( $vc_custom_design_options_reponsive as $item ) {
						$css_item = $this->generate_design_option_css( $item );
						$screen   = '';
						if ( isset( $item['screen'] ) && is_numeric( $item['screen'] ) && $item['screen'] > 0 ) {
							$screen = $item['screen'];
						} elseif ( isset( $item['screen'] ) && $item['screen'] == 'custom' ) {
							if ( isset( $item['screen_custom'] ) && is_numeric( $item['screen_custom'] ) && $item['screen_custom'] > 0 ) {
								$screen = $item['screen_custom'];
							}
						}
						if ( $screen != '' && is_numeric( $screen ) && $screen > 0 && $css_item != "" ) {
							if ( $shortcode == 'vc_column' || $shortcode == 'vc_column_inner' ) {
								$css .= '@media (max-width: ' . $screen . 'px){ .' . $atts['responsive_js_composer_custom_id'] . ' .vc_column-inner{' . $css_item . '}}';
							} else {
								$css .= '@media (max-width: ' . $screen . 'px){ .' . $atts['responsive_js_composer_custom_id'] . ' { ' . $css_item . ' } }';
							}

						}
					}
				}
			}


			return $css;
		}
	}
}