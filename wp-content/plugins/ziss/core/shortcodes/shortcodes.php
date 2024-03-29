<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'vc_before_init', 'zissShortcode' );
function zissShortcode() {
	global $wpdb;
	
	$allowed_tags = array(
		'em'     => array(),
		'i'      => array(),
		'b'      => array(),
		'strong' => array(),
		'a'      => array(
			'href'   => array(),
			'target' => array(),
			'class'  => array(),
			'id'     => array(),
			'title'  => array(),
		),
	);
	
	$sql  = "SELECT * FROM {$wpdb->prefix}posts WHERE post_type='ziss' AND post_status='publish'";
	$rows = $wpdb->get_results( $sql );
	
	$ziss_post_vc_select_args = array();
	if ( $rows ) {
		$ziss_post_vc_select_args = array(
			esc_html__( ' ----- Choose an Instagram shop ----- ', 'ziss' ) => 0
		);
		foreach ( $rows as $row ) {
			$ziss_post                                          = get_post( $row->ID );
			$ziss_post_vc_select_args[ $ziss_post->post_title ] = $ziss_post->ID;
		}
	} else {
		$ziss_post_vc_select_args = array(
			esc_html__( ' ----- You need create an Instagram shop first ----- ', 'ziss' ) => 0
		);
	}
	
	$cols_select = array(
		esc_html__( '6 Columns', 'ziss' ) => 6,
		esc_html__( '5 Columns', 'ziss' ) => 5,
		esc_html__( '4 Columns', 'ziss' ) => 4,
		esc_html__( '3 Columns', 'ziss' ) => 3,
		esc_html__( '2 Columns', 'ziss' ) => 2,
		esc_html__( '1 Columns', 'ziss' ) => 1,
	);
	
	global $kt_vc_anim_effects_in;
	vc_map(
		array(
			'name'     => esc_html__( 'Instagram Shop', 'ziss' ),
			'base'     => 'ziss', // shortcode
			'class'    => '',
			'category' => esc_html__( 'Ziss', 'ziss' ),
			'params'   => array(
				array(
					'type'       => 'dropdown',
					'class'      => '',
					'heading'    => esc_html__( 'Instagram Shop', 'ziss' ),
					'param_name' => 'id',
					'value'      => $ziss_post_vc_select_args
				),
				array(
					'type'        => 'textfield',
					'heading'     => esc_html__( 'Pin Text', 'ziss' ),
					'param_name'  => 'pin_text',
					'std'         => esc_html__( 'Shop It', 'ziss' ),
					'admin_label' => true,
					'description' => esc_html__( 'The text displayed if have pinned products', 'ziss' ),
				),
				array(
					"type"        => 'textfield',
					"heading"     => esc_html__( "Extra Class", "lebe" ),
					"param_name"  => 'extra_class',
					"description" => esc_html__( "If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'ziss' ),
				),
				array(
					'type'        => 'dropdown',
					'class'       => '',
					'heading'     => esc_html__( 'Large Screen', 'ziss' ),
					'description' => esc_html__( 'Number of columns on screen width >= 1200px', 'ziss' ),
					'param_name'  => 'lg_cols',
					'value'       => $cols_select,
					'std'         => 4,
					'group'       => esc_html__( 'Responsive Options', 'ziss' ),
				),
				array(
					'type'        => 'dropdown',
					'class'       => '',
					'heading'     => esc_html__( 'Medium Screen', 'ziss' ),
					'description' => esc_html__( 'Number of columns on screen width >= 992px and <= 1199px', 'ziss' ),
					'param_name'  => 'md_cols',
					'value'       => $cols_select,
					'std'         => 3,
					'group'       => esc_html__( 'Responsive Options', 'ziss' ),
				),
				array(
					'type'        => 'dropdown',
					'class'       => '',
					'heading'     => esc_html__( 'Small Screen', 'ziss' ),
					'description' => esc_html__( 'Number of columns on screen width >= 768px and <= 991px', 'ziss' ),
					'param_name'  => 'sm_cols',
					'value'       => $cols_select,
					'std'         => 2,
					'group'       => esc_html__( 'Responsive Options', 'ziss' ),
				),
				array(
					'type'        => 'dropdown',
					'class'       => '',
					'heading'     => esc_html__( 'Extra Small Screen', 'ziss' ),
					'description' => esc_html__( 'Number of columns on screen width >= 480px and <= 767px', 'ziss' ),
					'param_name'  => 'xs_cols',
					'value'       => $cols_select,
					'std'         => 2,
					'group'       => esc_html__( 'Responsive Options', 'ziss' ),
				),
				array(
					'type'        => 'dropdown',
					'class'       => '',
					'heading'     => esc_html__( 'Smallest Screen', 'ziss' ),
					'description' => esc_html__( 'Number of columns on screen width <= 479px', 'ziss' ),
					'param_name'  => 'xxs_cols',
					'value'       => $cols_select,
					'std'         => 1,
					'group'       => esc_html__( 'Responsive Options', 'ziss' ),
				),
				array(
					'type'       => 'css_editor',
					'heading'    => esc_html__( 'Css', 'ziss' ),
					'param_name' => 'css',
					'group'      => esc_html__( 'Design Options', 'ziss' ),
				),
			),
		)
	);
}

function ziss_shortcode( $atts ) {
	
	$single_img_src = isset( $atts['img_src'] ) ? $atts['img_src'] : '';
	$single_class   = isset( $atts['class'] ) ? $atts['class'] : '';
	$single_width   = isset( $atts['w'] ) ? $atts['w'] : '';
	$extra_class    = isset( $atts['extra_class'] ) ? $atts['extra_class'] : '';
	
	$atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'ziss', $atts ) : $atts;
	
	extract(
		shortcode_atts(
			array(
				'id'          => 0,
				'pin_text'    => '',
				'img_src'     => '',
				'lg_cols'     => 4,
				'md_cols'     => 3,
				'sm_cols'     => 2,
				'xs_cols'     => 2,
				'xxs_cols'    => 1,
				'extra_class' => '',
				'css'         => '',
			), $atts
		)
	);
	
	$pin_text = isset( $atts['pin_text'] ) ? $atts['pin_text'] : $pin_text;
	
	$css_class = 'ziss-shop-wrap instagram-shop-wrap social-shop-wrap ' . esc_attr( $extra_class );
	if ( function_exists( 'vc_shortcode_custom_css_class' ) ):
		$css_class .= ' ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), '', $atts );
	endif;
	
	$html            = '';
	$single_img_html = '';
	
	$pin_data_str  = get_post_meta( $id, 'ziss_pin_data', true );
	$pin_data_json = array();
	if ( trim( $pin_data_str ) != '' ) {
		$pin_data_json = json_decode( $pin_data_str );
	}
	
	if ( ! empty( $pin_data_json ) ) {
		global $ziss;
		$instagram_id        = isset( $ziss['instagram_id'] ) ? trim( $ziss['instagram_id'] ) : '';
		$instagram_token     = isset( $ziss['instagram_token'] ) ? $instagram_id . '.' . trim( $ziss['instagram_token'] ) : '';
		$instagram_api_url   = 'https://api.instagram.com/v1/users/' . esc_attr( $instagram_id ) . '/media/recent/?access_token=' . esc_attr( $instagram_token );
		$instagram_all_items = array();
		
		$response = wp_remote_get( $instagram_api_url );
		if ( ! is_wp_error( $response ) ) {
			$response_body = json_decode( $response['body'] );
			
			if (isset($response_body->meta->code)) {
				if ( $response_body->meta->code == 200 ) {
					$items_as_objects = $response_body->data;
					foreach ( $items_as_objects as $item_object ) {
						if ( isset( $item_object->images->standard_resolution ) ) {
							$item['id']            = $item_object->id;
							$item['link']          = $item_object->link;
							$item['src']           = $item_object->images->standard_resolution->url;
							$item['width']         = $item_object->images->standard_resolution->width;
							$item['height']        = $item_object->images->standard_resolution->height;
							$item['raw_data']      = $item_object;
							$instagram_all_items[] = $item;
						} else {
							$item['id']            = $item_object->id;
							$item['link']          = $item_object->link;
							$item['src']           = $item_object->images->low_resolution->url;
							$item['width']         = $item_object->images->standard_resolution->width;
							$item['height']        = $item_object->images->standard_resolution->height;
							$item['raw_data']      = $item_object;
							$instagram_all_items[] = $item;
						}
					}
				}
			}
		}
		
		$cols_num = array( 1, 2, 3, 4, 5, 6 );
		$lg_cols  = ! in_array( $lg_cols, $cols_num ) ? 4 : $lg_cols;
		$md_cols  = ! in_array( $md_cols, $cols_num ) ? 3 : $md_cols;
		$sm_cols  = ! in_array( $sm_cols, $cols_num ) ? 2 : $sm_cols;
		$xs_cols  = ! in_array( $xs_cols, $cols_num ) ? 2 : $xs_cols;
		$xxs_cols = ! in_array( $xxs_cols, $cols_num ) ? 1 : $xxs_cols;
		
		$item_html = '';
		$col_class = '';
		if ( $lg_cols == 5 ) {
			$col_class .= ' ziss-col5-lg';
		} else {
			$col_class .= ' ziss-col-lg-' . floor( 12 / $lg_cols );
		}
		if ( $md_cols == 5 ) {
			$col_class .= ' ziss-col5-md';
		} else {
			$col_class .= ' ziss-col-md-' . floor( 12 / $md_cols );
		}
		if ( $sm_cols == 5 ) {
			$col_class .= ' ziss-col5-sm';
		} else {
			$col_class .= ' ziss-col-sm-' . floor( 12 / $sm_cols );
		}
		if ( $xs_cols == 5 ) {
			$col_class .= ' ziss-col5-xs';
		} else {
			$col_class .= ' ziss-col-xs-' . floor( 12 / $xs_cols );
		}
		if ( $xxs_cols == 5 ) {
			$col_class .= ' ziss-col5-xxs';
		} else {
			$col_class .= ' ziss-col-xxs-' . floor( 12 / $xxs_cols );
		}
		// $col_class = 'ziss-col-lg-' . floor( 12 / $lg_cols ) . ' ziss-col-md-' . floor( 12 / $md_cols ) . ' ziss-col-sm-' . floor( 12 / $sm_cols ) . ' ziss-col-xs-' . floor( 12 / $xs_cols ) . ' ziss-col-xxs-' . floor( 12 / $xxs_cols );
		
		foreach ( $pin_data_json as $data ) {
			
			$pin_data_full_product_info = array();
			$instagram_data             = isset( $data->instagram_data ) ? json_encode( $data->instagram_data ) : '';
			$instagram_data_json        = json_decode( $instagram_data );
			foreach ( $instagram_all_items as $instagram_item ) {
				if ( isset( $instagram_data_json->id ) ) {
					if ( $instagram_item['id'] == $instagram_data_json->id ) {
						if ( isset( $instagram_item['raw_data'] ) ) {
							$instagram_data_json = $instagram_item['raw_data'];
							$instagram_data      = wp_json_encode( $instagram_data_json );
						}
					}
				}
			}
			
			$pin_data_html = '';;
			$cart_icon_html = '';
			
			if ( ! empty( $data->pin_data ) ) {
				$i = 0;
				foreach ( $data->pin_data as $pin_data ) {
					$i ++;
					$post_thumbnail_id = get_post_thumbnail_id( $pin_data->product_id );
					$product_thumb     = ziss_resize_image( $post_thumbnail_id, null, 90, 110, true, true, false );
					
					$hotspot_popup_html = '';
					if ( class_exists( 'WooCommerce' ) && ziss_post_exist_by_id( $pin_data->product_id ) ) {
						if ( is_null( get_post( $pin_data->product_id ) ) ) {
							continue;
						}
						
						$_product             = wc_get_product( $pin_data->product_id );
						$add_to_cart_btn_html = '';
						$product_type         = $_product->get_type();
						if ( $product_type == 'simple' ) {
							$btn_class            = $_product->is_purchasable() ? 'ajax_add_to_cart add_to_cart_button is-purchasable' : 'add_to_cart_button';
							$add_to_cart_btn_html .= sprintf( '<a href="%s" data-quantity="%s" data-product_id="%s" class="%s">%s</a>',
							                                  esc_url( $_product->add_to_cart_url() ),
							                                  1,
							                                  esc_attr( $_product->get_id() ),
							                                  'button product_type_' . esc_attr( $product_type ) . ' ' . $btn_class,
							                                  esc_html( $_product->add_to_cart_text() )
							);
						} else {
							$btn_class            = $_product->is_purchasable() ? 'add_to_cart_button is-purchasable' : 'add_to_cart_button';
							$add_to_cart_btn_html .= sprintf( '<a href="%s" data-quantity="%s" data-product_id="%s" class="%s">%s</a>',
							                                  esc_url( $_product->add_to_cart_url() ),
							                                  1,
							                                  esc_attr( $_product->get_id() ),
							                                  'button product_type_' . esc_attr( $product_type ) . ' ' . $btn_class,
							                                  esc_html( $_product->add_to_cart_text() )
							);
						}
						$short_desc_html = '';
						if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
							$short_desc_html .= '<p>' . wp_trim_words( $_product->post->post_excerpt, 10, '...' ) . '</p>';
						} else {
							$short_desc_html .= '<p>' . wp_trim_words( $_product->get_short_description(), 10, '...' ) . '</p>';
						}
						$average = $_product->get_average_rating();
						
						// $reviews_count_html = '<div class="review-count">' . sprintf( esc_html__( '%s Reviews', 'ziss' ), '<span>' . $_product->get_review_count() . '</span>' ) . '</div>';
						
						$product_data_args = array(
							'id'               => $pin_data->product_id,
							'title'            => esc_html( $_product->get_title() ),
							'price_html'       => $_product->get_price_html(),
							'permalink'        => esc_url( $_product->get_permalink() ),
							'thumb'            => array(
								'url'    => esc_url( $product_thumb['url'] ),
								'width'  => esc_attr( $product_thumb['width'] ),
								'height' => esc_attr( $product_thumb['height'] )
							),
							'add_to_cart_html' => $add_to_cart_btn_html,
							'rating_html'      => '<div class="star-rating" title="' . sprintf( __( 'Rated %s out of 5', 'ziss' ), $average ) . '"><span style="width:' . ( ( $average / 5 ) * 100 ) . '%"><strong class="rating">' . $average . '</strong> ' . __( 'out of 5', 'ziss' ) . '</span></div>'
						);
						// $product_data_json = json_encode( $product_data_args );
						$pin_data_full_product_info[] = array(
							'product'      => $product_data_args,
							'top_percent'  => $pin_data->top_percent,
							'left_percent' => $pin_data->left_percent,
						);
						
						$hotspot_popup_html = '<div class="ziss-hotspot-popup popup-right">
										        <a class="ziss-close-hotspot-popup" href="#"><i class="fa fa-times" aria-hidden="true"></i></a>
										        <div class="ziss-pin-popup-header">
										        	<h3 class="ziss-product-title"><a href="' . $product_data_args['permalink'] . '">' . $product_data_args['title'] . '</a></h3>
										            <div class="ziss-wc-info">
										                <div class="ziss-wc-price">' . $product_data_args['price_html'] . '</div>
										                <div class="woocommerce-product-rating">
										                    <div class="star-rating" title="' . sprintf( __( 'Rated %s out of 5', 'woocommerce' ), $average ) . '"><span style="width:' . ( ( $average / 5 ) * 100 ) . '%"><strong class="rating">' . $average . '</strong> ' . __( 'out of 5', 'ziss' ) . '</span></div>
										                </div>
										            </div>
									            </div>
										        <div class="ziss-popup-main">
										            <div class="col-left">
										            	<div class="ziss-product-thumbnail">
										            	<a href="' . $product_data_args['permalink'] . '"><img
										                            width="' . $product_data_args['thumb']['width'] . '" height="' . $product_data_args['thumb']['height'] . '"
										                            src="' . $product_data_args['thumb']['url'] . '"
										                            class="ziss-wc-thumbnail wp-post-image" alt=""></a>
														</div>
													</div>
										            <div class="col-right">
										            	<div class="ziss-product-sort-desc">
										            		' . $short_desc_html . '
										            	</div>
										            </div>
										        </div>
										        <div class="ziss-popup-footer">' . $add_to_cart_btn_html . '</div>
										    </div>';
					}
					
					$hotspot_top_percent  = $pin_data->top_percent;
					$hotspot_left_percent = $pin_data->left_percent;
					$hotspot_top_percent  = ( $pin_data->top_percent * $data->img_height + 50 * $data->img_width - 50 * $data->img_height ) / $data->img_width;
					
					if ( ziss_post_exist_by_id( $pin_data->product_id ) ) {
						$pin_data_html .= '<div class="ziss-hotspot-wrap" data-product_id="' . esc_attr( $pin_data->product_id ) . '" data-top_percent="' . esc_attr( $pin_data->top_percent ) . '" data-left_percent="' . esc_attr( $pin_data->left_percent ) . '" style="top: ' . esc_attr( $hotspot_top_percent ) . '%; left: ' . esc_attr( $hotspot_left_percent ) . '%;">
											<div data-hotspot_num="' . $i . '" class="hotspot-num hotspot-num-on-img hotspot-num-on-img-' . $i . '">
												<div class="ziss-hotspot-text">' . $i . '</div>
											</div>
											' . $hotspot_popup_html . '
										</div>';
					}
				}
				if ( $i > 0 ) {
					$cart_icon_html = '<i class="ziss-icon ziss-cart-icon fa fa-shopping-cart"></i>';
				}
			}
			
			$single_item_inner_class = $pin_data_html != '' ? 'has-products-pin' : 'no-products-pin';
			$pin_text_html           = $pin_data_html != '' && $pin_text != '' ? '<span class="pin-text">' . esc_html( $pin_text ) . '</span>' : '';
			
			$single_item_inner_html = '<div class="ziss-item-inner ' . esc_attr( $single_item_inner_class ) . '">
										<div class="ziss-figure-wrap">
											<div class="ziss-figure-inner hover-zoom-img">
												<figure data-instagram_data="' . htmlentities2( $instagram_data ) . '" data-pin_data="' . htmlentities2( json_encode( $pin_data_full_product_info ) ) . '" data-img_src="' . esc_url( $data->img_src ) . '" data-width="' . esc_attr( $data->img_width ) . '" data-height="' . esc_attr( $data->img_height ) . '" class="ziss-figure" style="background-image: url(' . esc_url( $data->img_src ) . ')">
												</figure>
											</div>
											' . $pin_data_html . '
											' . $cart_icon_html . '
											' . $pin_text_html . '
										</div>
									</div>';
			$single_item_html       = '<div class="ziss-item lebe-wow fadeInUp ' . esc_attr( $col_class ) . '">
										' . $single_item_inner_html . '
									</div>';
			
			$item_html .= $single_item_html;
			if ( trim( $single_img_src ) == trim( $data->img_src ) && trim( $single_img_src ) != '' ) {
				$single_item_inner_html_2 = '<div class="img-holder">' .
				                            '<figure data-instagram_data="' . htmlentities2( $instagram_data ) . '" data-pin_data="' . htmlentities2( json_encode( $pin_data_full_product_info ) ) . '" data-img_src="' . esc_url( $data->img_src ) . '" data-width="' . esc_attr( $data->img_width ) . '" data-height="' . esc_attr( $data->img_height ) . '" class="ziss-figure" >
												<img width="' . esc_attr( $data->img_width ) . '" height="' . esc_attr( $data->img_height ) . '" src="' . esc_url( $data->img_src ) . '" />
											</figure>' . $pin_data_html . $cart_icon_html .
				                            '</div>';
				
				
				$single_width = trim( $single_width ) != '' ? str_replace( 'px', '', strtolower( $single_width ) ) . 'px' : '';
				$single_style = '';
				if ( $single_width != '' ) {
					$single_style = 'style="width:' . $single_width . '; max-width: 100%;"';
				}
				$single_img_html .= '<div class="ziss-single-item-wrap ' . esc_attr( $single_class ) . '" ' . $single_style . '>' . $single_item_inner_html_2 . '</div>';
				$html            = $single_img_html;
				
				return $html;
			}
		}
		if ( $item_html != '' ) {
			$html = '<div class="ziss-row">' . $item_html . '</div>';
		}
	}
	
	if ( $html != '' ) {
		$html = '<div class="' . esc_attr( $css_class ) . '">' . $html . '</div>';
	}
	
	return $html;
	
}

add_shortcode( 'ziss', 'ziss_shortcode' );
