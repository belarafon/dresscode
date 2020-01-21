<?php

if ( ! class_exists( 'Nexio_Shortcode_pinmap' ) ) {
	class Nexio_Shortcode_pinmap extends Nexio_Shortcode {
		/**
		 * Shortcode name.
		 *
		 * @var  string
		 */
		public $shortcode = 'pinmap';
		
		
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
			$atts = function_exists( 'vc_map_get_attributes' ) ? vc_map_get_attributes( 'nexio_pinmap', $atts ) : $atts;
			
			$html = '';
			// Extract shortcode parameters.
			extract( $atts );
			
			$css_class   = array( 'nexio-pinmap' );
			$css_class[] = $atts['style'];
			$css_class[] = $atts['el_class'];
			$css_class[] = $atts['custom_id'];
			$css_class[] = $atts['animate_on_scroll'];
			if ( function_exists( 'vc_shortcode_custom_css_class' ) ) {
				$css_class[] = ' ' . apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), '', $atts );
			}
			$mapper_id = intval( $ids );
			if ( $mapper_id > 0 ) {
				$html .= do_shortcode( '[nexio_mapper id="' . esc_attr( $mapper_id ) . '"]' );

				$title_html      = $atts['title'] ? '<span class="title">' . esc_html($atts['title']) . '</span>' : '';
				$short_desc_html =  $atts['short_desc'] ? '<span class="short-desc">' . esc_html($atts['short_desc']) . '</span>' : '';
				$btn_link = vc_build_link($atts['link']);
				if ($btn_link['url']) {
					$link_url = $btn_link['url'];
				} else {
					$link_url = '#';
				}
				if ($btn_link['target']) {
					$link_target = $btn_link['target'];
				} else {
					$link_target = '_self';
				}

				$btn_link_html = '';
				if ( $btn_link['title'] != '' ) {
					$btn_link_html = '<div class="nexio-button style-02"><a class="button" href="' . esc_url($link_url) . '" target="' . esc_attr( $link_target ) . '">' . esc_html( $btn_link['title'] ) . '<span class="icon"><span class="flaticon-right-arrow"></span></span></a></div>';
				}
				$info_content_html = '<div class="mapper-short-content-wrap"><div class="mapper-short-info-wrap">' . $title_html . $short_desc_html  .'</div>'. $btn_link_html . '</div>';
				$html              .= $info_content_html;

			}
			
			if ( $html != '' ) {
				$html = '<div class="' . esc_attr( implode( ' ', $css_class ) ) . '">' . $html . '</div>';
			}
			
			return apply_filters( 'Nexio_Shortcode_pinmap', force_balance_tags( $html ), $atts, $content );
		}
	}
}