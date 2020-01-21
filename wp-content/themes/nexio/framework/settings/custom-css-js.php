<?php

add_action( 'wp_head', 'nexio_preloader_css' );
if ( ! function_exists( 'nexio_preloader_css' ) ) {
	function nexio_preloader_css() {
		/* Main color */
		$main_color      = nexio_get_option( 'nexio_main_color', '#ff4040' );
		$body_text_color = trim( nexio_get_option( 'nexio_body_text_color', '' ) );
		$css             = '';
		
		// Preloader CSS
		$enable_preloader = nexio_get_option( 'enable_preloader', false );
		if ( $enable_preloader ) {
			$preloader_style = nexio_get_option( 'preloader_style', 'default' );
			if ( $preloader_style == 'default' ) {
				$css = '.nexio-default-preloader {
						position: fixed;
						background-color: #fff;
						top: 0;
						left: 0;
						right: 0;
						bottom: 0;
						z-index: 9999999;
					}
					.nexio-ripple {
						display: inline-block;
						position: fixed;
						top: 50%;
						left: 50vw;
						margin-left: -32px;
						margin-top: -32px;
						width: 64px;
						height: 64px;
					}
					.nexio-ripple div {
						position: absolute;
						border: 4px solid ' . esc_attr( $main_color ) . ';
						opacity: 1;
						border-radius: 50%;
						animation: nexio-ripple 1.5s cubic-bezier(0, 0.2, 0.8, 1) infinite;
					}
					.nexio-ripple div:nth-child(2) {
						animation-delay: -0.5s;
					}
					@keyframes nexio-ripple {
						0% {
							top: 28px;
							left: 28px;
							width: 0;
							height: 0;
							opacity: 1;
						}
						100% {
							top: -1px;
							left: -1px;
							width: 58px;
							height: 58px;
							opacity: 0;
						}
					}';
			}
			if ( $preloader_style == 'block_rotate' ) {
				$css = '.nexio-preloader,
						.finger-loading .last-finger-loader-item i::after {
							background-color: ' . esc_attr( $main_color ) . ';
						}
						span.nexio-preloader-text {
							font-size: 13px;
							color: #ffffff;
							display: block;
						}
						.nexio-preloader {
						  bottom: 0;
						  left: 0;
						  position: fixed;
						  right: 0;
						  top: 0;
						  z-index: 9999999;
						  text-align: center;
						}
						.nexio-preloader-inner {
							height: 100%;
						}
						.nexio-preloader .item-inner {
						  align-items: center;
						  display: flex;
						  height: 100%;
						  justify-content: center;
						  position: relative;
						  width: 100%;
						}
						.nexio-preloader-block {
						  width: 100%;
						}
						.square-loader,
						.circle-loader {
						  position: relative;
						  width: 200px;
						  height: 200px;
						}
						.wrapper-square {
						  width: 50px;
						  height: 50px;
						  background-color: rgba(255,255,255,0);
						  margin-right: auto;
						  margin-left: auto;
						  border: 2px solid #fff;
						  left: 73px;
						  top: 73px;
						  position: absolute;
						}
				
						.square-loader {
						  transform: rotate(45deg);
						}
				
						.first_square {
						  animation: first_square_animate 1s infinite ease-in-out;
						}
						.second_square {
						  animation: second_square 1s forwards,
						             second_square_animate 1s infinite ease-in-out;
						}
						.third_square {
						  animation: third_square 1s forwards,
						             third_square_animate 1s infinite ease-in-out;
						}
						
						@keyframes second_square {
						  100% { width: 100px; height:100px; left: 48px; top: 48px; }
						}
				
						@keyframes third_square {
						  100% { width: 150px; height:150px; left: 23px; top: 23px;}
						}
				
						@keyframes first_square_animate {
						  0%   { transform: perspective(100px) rotateX(0deg) rotateY(0deg);}
						  50%  { transform: perspective(100px) rotateX(-180deg) rotateY(0deg); }
						  100% { transform: perspective(100px) rotateX(-180deg) rotateY(-180deg); }
						}
				
						@keyframes second_square_animate {
						  0%   { transform: perspective(200px) rotateX(0deg) rotateY(0deg); }
						  50%  { transform: perspective(200px) rotateX(180deg) rotateY(0deg); }
						  100% { transform: perspective(200px) rotateX(180deg) rotateY(180deg); }
						}
				
						@keyframes third_square_animate {
						  0%   { transform: perspective(300px) rotateX(0deg) rotateY(0deg); }
						  50%  { transform: perspective(300px) rotateX(-180deg) rotateY(0deg); }
						  100% { transform: perspective(300px) rotateX(-180deg) rotateY(-180deg); }
						}
						.third-wrapper .square-loader {
						  -webkit-transform: rotate(0);
						  transform: rotate(0);
						}';
			}
			
			if ( $preloader_style == 'segment_blocks' ) {
				$css = '.nexio-preloader,
						.finger-loading .last-finger-loader-item i::after {
							background-color: #322b3b;
						}
						span.nexio-preloader-text {
							font-size: 13px;
							color: #ffffff;
							display: block;
						}
						.nexio-preloader {
						  bottom: 0;
						  left: 0;
						  position: fixed;
						  right: 0;
						  top: 0;
						  z-index: 9999999;
						  text-align: center;
						}
						.nexio-preloader-inner {
							height: 100%;
						}
						.nexio-preloader .item-inner {
						  align-items: center;
						  display: flex;
						  height: 100%;
						  justify-content: center;
						  position: relative;
						  width: 100%;
						}
						.nexio-preloader-block {
						  width: 100%;
						}
						
						.nexio-segment-loader {
						  height: 120px;
						  width: 120px;
						  margin: 0 auto;
						  transform: rotate(-45deg);
						  font-size: 0;
						  line-height: 0;
						  animation: rotate-loader 5s infinite;
						  padding: 25px;
						  border: 1px solid ' . esc_attr( $main_color ) . ';
						}
						.nexio-segment-loader-holder {
						  position: relative;
						  display: inline-block;
						  width: 50%;
						  height: 50%;
						}
				
						.nexio-segment {
						  position: absolute;
						  background: ' . esc_attr( $main_color ) . ';
						}
				
						.nexio-segment-one {
						  bottom: 0;
						  height: 0;
						  width: 100%;
						  animation: slide-one 1s infinite;
						}
				
						.nexio-segment-two {
						  left: 0;
						  height: 100%;
						  width: 0;
						  animation: slide-two 1s infinite;
						  animation-delay: 0.25s;
						}
				
						.nexio-segment-three {
						  right: 0;
						  height: 100%;
						  width: 0;
						  animation: slide-two 1s infinite;
						  animation-delay: 0.75s;
						}
				
						.nexio-segment-four {
						  top: 0;
						  height: 0;
						  width: 100%;
						  animation: slide-one 1s infinite;
						  animation-delay: 0.5s;
						}
						.nexio-segment-loader-block span.nexio-preloader-text {
						  margin-top: 45px;
						}
				
						@keyframes slide-one {
						  0%    { height: 0;    opacity: 1; }
						  12.5% { height: 100%; opacity: 1; }
						  50%   { opacity: 1; }
						  100%  { height: 100%; opacity: 0;}
						}
				
						@keyframes slide-two {
						  0%    { width: 0;    opacity: 1; }
						  12.5% { width: 100%; opacity: 1; }
						  50%   { opacity: 1; }
						  100%  { width: 100%; opacity: 0;}
						}
				
						@keyframes rotate-loader {
						  0%   { transform: rotate(-45deg); }
						  20%  { transform: rotate(-45deg); }
						  25%  { transform: rotate(-135deg); }
						  45%  { transform: rotate(-135deg); }
						  50%  { transform: rotate(-225deg); }
						  70%  { transform: rotate(-225deg); }
						  75%  { transform: rotate(-315deg); }
						  95%  { transform: rotate(-315deg); }
						  100% { transform: rotate(-405deg); }
						}';
			}
			
			if ( $preloader_style == 'text_fill' ) {
				$css = '.nexio-preloader-section {
							position: fixed;
						    top: 0;
						    left: 0;
						    right: 0;
						    bottom: 0;
						    background-color: ' . esc_attr( $main_color ) . ';
						    z-index: 99999999;
						    overflow: hidden;
						}
						.nexio-preloader {
							font-size: 50%;
						    margin: 0 auto;
						    text-align: center;
						    width: 100%;
						}
						.nexio-preloader-text-loader {
							display: table;
						    height: 100%;
						    left: 0;
						    margin: 0;
						    padding: 0;
						    position: absolute;
						    top: 0;
						    width: 100%;
						}
						.nexio-frame {
						    display: table-cell;
						    margin: 0;
						    padding: 0;
						    vertical-align: middle;
						}
						#nexio-preloader-text {
							  display: inline-block;
							  margin: auto;
							  position: relative;
							  text-align: center;
							  width: auto;
							}
							#nexio-preloader-text span {
							  color: rgba(0, 0, 0, 0.1);
							  display: inline-block;
							  font-size: 5.75em;
							  letter-spacing: 0;
							  line-height: 1;
							  overflow: hidden;
							  position: relative;
							  text-transform: none;
							}
							#nexio-preloader-text span.nexio-preloader-text-2 {
							  color: #000;
							  display: block;
							  left: 0;
							  position: absolute;
							  top: 0;
							  width: 0%;
							}
							.wppu-fill-loader-thumb {
							  width: 100px;
							  position: relative;
							}
							.wppu-fill-loader-thumb img {
							  display: block;
							  height: auto;
							  opacity: 0.3;
							  position: relative;
							  width: 100%;
							  z-index: 1;
							}
					
							.wppu-fill-thumbnail-fill {
							  background-position: center bottom;
							  background-repeat: no-repeat;
							  background-size: cover;
							  bottom: 0;
							  display: block;
							  height: 0;
							  left: 0;
							  position: absolute;
							  transition: all 0s ease 0s;
							  width: 100%;
							  z-index: 3;
							}
							#run_animation_fill {
							  background-color: #000;
							  color: #fff;
							  cursor: pointer;
							  line-height: 1em;
							  padding: 6px 10px 8px;
							  position: absolute;
							  right: 0;
							  top: 0;
							  z-index: 991;
							}
							.nexio-preloader-progress-bar {
							  height: 2px;
							  left: 0;
							  position: absolute;
							  top: 0;
							  display: none;
							  width: 0%;
							  z-index: 140;
							  background-color: #ed4e6e;
							}
							.nexio-progress-pos-top.nexio-preloader-progress-bar {
							  display: block;
							  top: 0;
							  left: 0;
							}
							.wppu_fill_progress_pos-bottom.nexio-preloader-progress-bar {
							  display: block;
							  top: inherit;
							  bottom: 0;
							  left: 0;
							}
							.nexio-preloader-counter {
							  line-height: 1;
							  margin-top: 20px;
							}
							#wppu-fill-loader-thunb {
							  width: 100px;
							  position: relative;
							  margin: auto;
							}
							#wppu-fill-loader-thunb img {
							  display: block;
							  height: auto;
							  opacity: 0.3;
							  position: relative;
							  width: 100%;
							  z-index: 1;
							}
							.wppu-fill-thumbnail-fill {
							  background-position: center bottom;
							  background-repeat: no-repeat;
							  background-size: cover;
							  bottom: 0;
							  display: block;
							  height: 0;
							  left: 0;
							  position: absolute;
							  transition: all 0s ease 0s;
							  width: 100%;
							  z-index: 3;
							}
					
							.nexio-preloader-section-loaded .nexio-preloader-progress-bar,
							.nexio-preloader-section-loaded span.nexio-preloader-text-2 {
							    transition: width 0.5s ease 0s;
							    -webkit-transition: width 0.5s ease 0s;
							    -moz-transition: width 0.5s ease 0s;
							    -o-transition: width 0.5s ease 0s;
							    -ms-transition: width 0.5s ease 0s;
							    width: 100% !important;
							}
							.nexio-preloader-section-loaded .wppu-fill-thumbnail-fill {
								transition: height 0.5s ease 0s;
							    -webkit-transition: height 0.5s ease 0s;
							    -moz-transition: height 0.5s ease 0s;
							    -o-transition: height 0.5s ease 0s;
							    -ms-transition: height 0.5s ease 0s;
							    height: 100% !important;
							}';
			}
			
			if ( $css != '' ) {
				echo "<style type='text/css' class='nexio-preloader-css'>{$css}</style>";
			}
		}
	}
}

if ( ! function_exists( 'nexio_custom_css' ) ) {
	function nexio_custom_css() {
		$css = '';
		
		$css .= nexio_theme_color();
		$css .= nexio_vc_custom_css_footer();
		
		wp_enqueue_style( 'nexio-custom-css', get_theme_file_uri( '/assets/css/customs.css' ), array(), '1.0' );
		wp_add_inline_style( 'nexio-custom-css', $css );
		
		$enable_preloader = nexio_get_option( 'enable_preloader', false );
		$preloader_style  = nexio_get_option( 'preloader_style', 'block_rotate' );
		if ( $enable_preloader ) {
			$js_script = '';
			
			// Inline script
			if ( $preloader_style == 'default' ) {
				$js_script = '//<![CDATA[
								jQuery( function( $ ) {
									window.onbeforeunload = function (e) {
									  $(".nexio-default-preloader").fadeIn(400);
									};
									$( window ).load(function() {
										$(".nexio-default-preloader").delay(500).fadeOut(600);
									});
								});
								//]]>';
			}
			if ( $preloader_style == 'block_rotate' || $preloader_style == 'segment_blocks' ) {
				$js_script = '//<![CDATA[
								jQuery( function( $ ) {
									window.onbeforeunload = function (e) {
									  $(".nexio-preloader").fadeIn(400);
									};
									$( window ).load(function() {
										$(".nexio-preloader").delay(500).fadeOut(600);
									});
								});
								//]]>';
			}
			if ( $preloader_style == 'text_fill' ) {
				$js_script = '//<![CDATA[
						jQuery( function( $ ) {
							var counter_duration = 1200;
							$( ".nexio-preloader-progress-bar" ).animate({ width: "100%"}, {
								duration: counter_duration,
								complete: function(){
									$(".nexio-preloader-section").addClass("nexio-loading");
								}
							});
							$(\'.nexio-preloader-counter span\').each(function () {
							var $this = $(this);
							$({ Counter: 0 }).animate({ Counter: 100 }, {
							    duration: counter_duration,
							    easing: \'swing\',
							    step: function () {
							      $this.text(Math.ceil(this.Counter));
							    },
								complete: function() {
								  $(\'.nexio-preloader-counter span\').text(this.Counter);
								}
							  });
							});
							
							window.onbeforeunload = function (e) {
							  $(".nexio-preloader-section").removeClass("nexio-preloader-section-loaded nexio-loading").fadeIn(400);
							  $(".nexio-preloader-section .nexio-preloader").css({display: "block"});
							  $(".nexio-preloader-counter span").text("0");
							};
							
							$(window).load(function() {
								if ($(".nexio-preloader-section").hasClass("nexio-loading") ) {
								    $(".nexio-preloader-section").removeClass("nexio-loading").addClass("nexio-preloader-section-loaded").delay(500).fadeOut(600);
								    $({countNum: $(\'.nexio-preloader-counter span\').text()}).animate({countNum: 100}, {
									  duration: 500,
									  easing:\'linear\',
									  step: function() {
									    $(\'.nexio-preloader-counter span\').text(Math.floor(this.countNum));
									  },
									  complete: function() {
									    $(\'.nexio-preloader-counter span\').text(this.countNum);
									  }
									});
								} else {
								    $(".nexio-preloader-section").addClass("nexio-preloader-section-loaded");
								    $(".nexio-preloader-section").delay(900+500).fadeOut(600);
									
									$({countNum: $(\'.nexio-preloader-counter span\').text()}).animate({countNum: 100}, {
									  duration: 850+500,
									  easing:\'linear\',
									  step: function() {
									    $(\'.nexio-preloader-counter span\').text(Math.floor(this.countNum));
									  },
									  complete: function() {
									    $(\'.nexio-preloader-counter span\').text(this.countNum);
									  }
									});
								}
							});
							
						});
					//]]>';
			}
			
			if ( $js_script != '' ) {
				wp_add_inline_script( 'nexio-frontend', $js_script );
			}
		}
		
	}
}
add_action( 'wp_enqueue_scripts', 'nexio_custom_css', 999 );

if ( ! function_exists( 'nexio_theme_color' ) ) {
	function nexio_theme_color() {
		$css = '';
		
		/* Main color */
		$main_color      = nexio_get_option( 'nexio_main_color', '#ff4040' );
		$body_text_color = trim( nexio_get_option( 'nexio_body_text_color', '' ) );
		
		// Typography
		$enable_google_font = nexio_get_option( 'enable_google_font', false );
		if ( $enable_google_font ) {
			$body_font = nexio_get_option( 'typography_themes' );
			if ( ! empty( $body_font ) ) {
				$typography_themes['family']  = 'Open Sans';
				$typography_themes['variant'] = '400';
				$body_fontsize                = nexio_get_option( 'fontsize-body', '15' );
				
				$css .= 'body{';
				$css .= 'font-family: "' . $body_font['family'] . '";';
				if ( '100italic' == $body_font['variant'] ) {
					$css .= '
					font-weight: 100;
					font-style: italic;
				';
				} elseif ( '300italic' == $body_font['variant'] ) {
					$css .= '
					font-weight: 300;
					font-style: italic;
				';
				} elseif ( '400italic' == $body_font['variant'] ) {
					$css .= '
					font-weight: 400;
					font-style: italic;
				';
				} elseif ( '700italic' == $body_font['variant'] ) {
					$css .= '
					font-weight: 700;
					font-style: italic;
				';
				} elseif ( '800italic' == $body_font['variant'] ) {
					$css .= '
					font-weight: 700;
					font-style: italic;
				';
				} elseif ( '900italic' == $body_font['variant'] ) {
					$css .= '
					font-weight: 900;
					font-style: italic;
				';
				} elseif ( 'regular' == $body_font['variant'] ) {
					$css .= 'font-weight: 400;';
				} elseif ( 'italic' == $body_font['variant'] ) {
					$css .= 'font-style: italic;';
				} else {
					$css .= 'font-weight:' . $body_font['variant'] . ';';
				}
				// Body font size
				if ( $body_fontsize ) {
					$css .= 'font-size:' . esc_attr( $body_fontsize ) . 'px;';
				}
				$css .= '}';
				$css .= 'body{
						font-family: "' . $body_font['family'] . '";
					}
					.heading-light {
						font-family: "' . $body_font['family'] . '" !important;
						font-weight: 300;
					}
					.heading-medium,.page-links > a,
					.page-links > span:not(.page-links-title),
					.widget_shopping_cart .product_list_widget li a:nth-child(2),
					.header-search-box > .icons,
					.instant-search-modal .product-cats label span,
					.block-account,
					.header .nexio-minicart .mini-cart-icon,
					.filter-button-group .filter-list .blog-filter,
					.nexio-blog.style-01 .post-item .post-info .post-title,
					.comment-text .woocommerce-review__author,
					.comment-text .comment-author,
					.comment-form .form-submit #submit,
					h4.title-variable,
					.summary .cart .single_add_to_cart_button,
					.sticky_info_single_product button.nexio-single-add-to-cart-btn.btn.button ,
					h3.famibt-title,
					div.famibt-wrap .famibt-products-wrap .famibt-product .famibt-price,
					#widget-area .prdctfltr-widget div[data-filter="product_cat"] label,
					#widget-area .prdctfltr_wc.prdctfltr_round .prdctfltr_filter[data-filter="product_cat"] label > i::before,
					body .prdctfltr_hierarchy_circle .prdctfltr_checkboxes i.prdctfltr-plus::before,
					.box-mobile-menu .close-menu,
					#widget-area .widgettitle,
					.nexio_newsletter_widget button,
					.woocommerce-pagination a.page-numbers,
					.woocommerce-pagination span.page-numbers,
					.woocommerce-pagination li .page-numbers,
					.comments-pagination .page-numbers,
					.post-pagination > span:not(.title),
					.post-pagination a span,
					.pagination .page-numbers,
					nav.woocommerce-breadcrumb,
					.toolbar-products .category-filter li a,
					span.prdctfltr_title_selected,
					.reset-filter-product,
					div.prdctfltr_filter .prdctfltr_regular_title ,
					.prdctfltr_sc.hide-cat-thumbs .product-category h2.woocommerce-loop-category__title,
					.prdctfltr_wc.prdctfltr_wc_regular .prdctfltr_buttons label,
					.toolbar-products-mobile .cat-item, .real-mobile-toolbar.toolbar-products-shortcode .cat-item,
					.return-to-shop .button,
					body .shop_table tr td.product-stock-status > span,
					body .woocommerce table.shop_table .product-add-to-cart .add_to_cart,
					.cart-collaterals .cart_totals .shop_table .shipping-calculator-button,
					.wc-proceed-to-checkout .checkout-button,
					.nav-tabs > li > a,
					p.des-res,
					.customer-form .form-row-wide label,
					.login .form-row-wide label,
					.customer-form input[type="submit"],
					.login input[type="submit"],
					.woocommerce-MyAccount-content fieldset legend,
					body.error404 .error-404 .title,
					body.error404 .error-404 .button,
					.product-mobile-layout .summary .yith-wcwl-add-to-wishlist a,
					.product-mobile-layout .product-toggle-more-detail,
					.add-to-cart-fixed-btn,
					.shop_table_mobile .product-info > a,
					.enable-shop-page-mobile .product-remove .remove span,
					.enable-shop-page-mobile .woocommerce .wishlist_table tfoot .yith-wcwl-share h4.yith-wcwl-share-title,
					.bestseller-cat-products .block-grid-title,
					.wpcf7-form .wpcf7-submit,
					.nexio-tabs .tab-head .tab-link > li,
					.nexio-title.style-03 .small-title,
					.nexio-title.style-10 .button,
					.nexio-title.style-13 .title-inner .block-title,
					.nexio-title.style-19 .block-title,
					.nexio-title.style-21 .block-desc,
					.nexio-custommenu.style-06 .title span,
					.nexio-custommenu.style-07 .title span,
					.nexio-button.style-02 .button,
					.nexio-banner.style-02 .banner-info .title,
					.nexio-banner.style-06 .banner-info .desc,
					.nexio-banner.style-25 .banner-info .button,
					.nexio-banner.style-26 .banner-info .button,
					.nexio-banner.style-27 .banner-info .button,
					.nexio-banner.style-25 .banner-thumb .title,
					.nexio-banner.style-26 .banner-thumb .title,
					.nexio-banner.style-27 .banner-thumb .title,
					.nexio-banner.style-34 .banner-thumb .title,
					.nexio-banner.style-35 .banner-thumb .title,
					.nexio-banner.style-37 .banner-info .title,
					.nexio-banner.style-39 .banner-info .button,
					.nexio-banner.style-40 h6.title,
					.nexio-instagram-sc.style-01 .title,
					.nexio-instagram-sc.style-04 .title,
					.nexio-categories.style-01 .category-info .category-name,
					.nexio-slider > .title,
					.nexio-video.style-02 .video-inner .nexio-bt-video a .video-text,
					.nexio-video.style-03 .video-inner .nexio-bt-video a .video-text,
					.nexio-video.style-04 .video-inner .nexio-bt-video a .video-text  {
						font-family: "' . $body_font['family'] . '" !important;
						font-weight: 500;
					}
					.horizon-menu .main-menu > .menu-item > a,
					.heading-semibold,
					.widget_shopping_cart .woocommerce-mini-cart__total,
					.widget_shopping_cart .woocommerce-mini-cart__buttons .button,
					.search-view,
					.currency-language .wcml-dropdown-click a,
					.currency-language .dropdown a,
					header .wcml_currency_switcher li a,
					header .wcml_currency_switcher li li a,
					.header .minicart-content-inner .minicart-title,
					.header .minicart-items .product-cart .product-detail .product-detail-info .product-quantity,
					.header .minicart-content-inner .actions .button,
					.header .to-cart,
					.post-item .readmore,
					.blog-grid .post-meta .categories,
					.nexio-blog.style-01 .post-item .post-info .date-up,
					.summary ins .woocommerce-Price-amount.amount,
					.summary .nexio-countdown .timers .box .time-title,
					button.nexio-single-add-to-cart-deal,
					.woocommerce-product-details__short-description ul,
					.woocommerce-product-details__short-description li,
					.summary a.change-value:not(.color),
					.content-tab-sticky h2,
					a.product-sticky-toggle-tab-content,
					.single-product .nexio-bt-video a, .product-360-button a,
					.wc-tabs li a,
					button.famibt-add-all-to-cart,
					.has-extend h4.title,
					.part-filter-wrap .filter-toggle,
					.part-filter-wrap .filter-toggle-button,
					.WOOF_Widget .woof_container h4,
					.action-mini a.filter-toggle-button,
					.action-mini .fami-woocommerce-ordering select.orderby,
					.enable-shop-page-mobile .prdctfltr_wc .prdctfltr_filter_title > span.prdctfltr_woocommerce_filter_title,
					.enable-shop-page-mobile .woocommerce-page-header ul .line-hover a,
					body .woocommerce table.shop_table thead th,
					.actions-btn .button,
					.actions-btn .shopping,
					.actions .coupon label,
					.cart-collaterals .cart_totals h2,
					.checkout-before-top .woocommerce-info,
					.woocommerce-billing-fields h3,
					.woocommerce-shipping-fields h2,
					.woocommerce-checkout-review-order-wrap #order_review_heading,
					.divider,
					#popup-newsletter .title,
					.page-404 a.button,
					.box-tabs-nav-wrap .box-tabs-nav .box-tab-nav .nav-text,
					.product-mobile-layout .variable_mobile.cart .quantity .btn-number.qtyplus,
					.variable_mobile.cart .quantity .input-qty ,
					.product-mobile-layout a.reset_variations,
					button.single_add_to_cart_button-clone.button,
					.variable_mobile tr.variation label,
					.toggle-variations-select-mobile,
					.tabs-mobile-content a.button-togole,
					.content-des .comment-respond .comment-reply-title,
					.woocommerce-cart-form-mobile .actions .actions-btn .shopping ,
					.woocommerce-cart-form-mobile .actions .coupon label,
					.woocommerce-cart-form-mobile ~ .cart-collaterals .cart_totals .shop_table tr th,
					.woocommerce-cart-form-mobile ~ .cart-collaterals .cart_totals .shop_table tr td,
					.nexio-tabs .tab-container .tab-panel .button-link,
					.nexio-title.style-01 .button,
					.nexio-title.style-03 .button,
					.nexio-title.style-06 .block-title,
					.nexio-title.style-07 .button,
					.nexio-title.style-10 .block-title ,
					.nexio-title.style-19 .button ,
					.style-24 h3.block-title,
					.nexio-newsletter.style-01 .newsletter-form-wrap button,
					.nexio-newsletter.style-02 .newsletter-form-wrap button,
					.nexio-newsletter.style-06 .newsletter-form-wrap button,
					.nexio-newsletter.style-12 .newsletter-form-wrap button,
					.nexio-newsletter.style-13 .newsletter-form-wrap button,
					.nexio-newsletter.style-03 .newsletter-title,
					.nexio-newsletter.style-04 .newsletter-title,
					.nexio-newsletter.style-05 .newsletter-form-wrap .submit-newsletter,
					.nexio-newsletter.style-06 .newsletter-title ,
					.nexio-newsletter.style-08 .newsletter-form-wrap .submit-newsletter,
					.nexio-newsletter.style-09 .newsletter-form-wrap .submit-newsletter,
					.nexio-newsletter.style-10 .newsletter-top .newsletter-heading,
					.nexio-newsletter.style-11 .newsletter-form-wrap .submit-newsletter,
					.nexio-custommenu.style-01 .title,
					.nexio-custommenu.style-02 .title ,
					.nexio-custommenu.style-03 .menu .menu-item > a,
					.nexio-custommenu.style-05 .menu .menu-item ,
					.nexio-button.style-01 .button,
					.nexio-banner.style-01 .banner-info .button,
					.nexio-banner.style-02 .banner-info .button,
					.nexio-banner.style-03 .banner-info .button,
					.nexio-banner.style-04 .banner-info .button,
					.nexio-banner.style-05 .banner-info .button,
					.nexio-banner.style-06 .banner-info .button,
					.nexio-banner.style-07 .banner-info .button,
					.nexio-banner.style-08 .banner-info .button,
					.nexio-banner.style-09 .banner-info .title,
					.nexio-banner.style-09 .banner-info .button ,
					.nexio-banner.style-11 .banner-info .button,
					.nexio-banner.style-12 .banner-info .button,
					.nexio-banner.style-14 .banner-info .button,
					.nexio-banner.style-16 .banner-info .button,
					.nexio-banner.style-16 .banner-info .button,
					.nexio-banner.style-17 .banner-info .title,
					.nexio-banner.style-17 .banner-info .button,
					.nexio-banner.style-18 .banner-info .button,
					.nexio-banner.style-20 .banner-info .button,
					.nexio-banner.style-23 .banner-info .button,
					.nexio-banner.style-24 .banner-info .button,
					.nexio-banner.style-18 .banner-info .title,
					.nexio-banner.style-20 .banner-info .title,
					.nexio-banner.style-28 .banner-info .button,
					.nexio-banner.style-33 .banner-info .button,
					.nexio-banner.style-34 .banner-info .button,
					.nexio-banner.style-35 .banner-info .button,
					.nexio-banner.style-38 .banner-thumb .button ,
					.nexio-iconbox.style-01 .content .title,
					.nexio-iconbox.style-03 .content .title,
					.nexio-iconbox.style-04 .button,
					.nexio-iconbox.style-05 .title,
					.nexio-iconbox.style-06 .title,
					.nexio-products.text-light .button-link,
					.product-item.style-3 .add-to-cart a::before,
					.nexio-pinmap.style-02 .mapper-short-info-wrap,
					.prdctfltr_filter.prdctfltr_attributes.prdctfltr_pa_size.prdctfltr_single label > span,
					.prdctfltr-pagination-load-more .button,
					.onnew, .onsale {
						font-family: "' . $body_font['family'] . '" !important;
						font-weight: 600;
					}
					.heading-bold {
						font-family: "' . $body_font['family'] . '" !important;
						font-weight: 700;
					}
					.instant-search-modal .search-fields .search-field,
					span.text-search,
					.post-content .dropcap,
					.nexio-blog.style-02 .blog-heading.
					.nexio-blog.style-03 .blog-heading ,
					.comments-area .title-comment,
					div#yith-wcwl-popup-message,
					div.mfp-wrap div .mfp-close,
					.famibt-messages-wrap a.button.wc-forward,
					.page-title,
					body.wpb-js-composer .vc_tta.vc_general.style-02 .vc_tta-panel-title > a ,
					.nexio-title.style-01 .block-title,
					.nexio-title.style-02 .block-title,
					.nexio-title.style-03 .block-title,
					.nexio-title.style-12 .title-inner .block-title,
					.nexio-title.style-16 .block-title,
					.nexio-title.style-17 .block-title,
					.nexio-title.style-18 .block-title,
					.nexio-title.style-20 .block-title,
					.nexio-title.style-21 .block-title,
					.nexio-newsletter.style-01 .newsletter-title,
					.nexio-newsletter.style-02 .newsletter-title,
					.nexio-instagramshopwrap.style-07 .block-title,
					.nexio-banner.style-07 .banner-info .bigtitle,
					.nexio-banner.style-08 .banner-info .bigtitle,
					.nexio-banner.style-10 .banner-info .bigtitle,
					.nexio-banner.style-11 .banner-info .bigtitle,
					.nexio-banner.style-12 .banner-info .bigtitle,
					.nexio-banner.style-13 .banner-info .bigtitle,
					.nexio-banner.style-15 .banner-info .bigtitle,
					.nexio-banner.style-16 .banner-info .bigtitle,
					.nexio-banner.style-17 .banner-info .bigtitle,
					.nexio-banner.style-18 .banner-info .bigtitle,
					.nexio-banner.style-20 .banner-info .bigtitle,
					.nexio-banner.style-21 .banner-info .bigtitle,
					.nexio-banner.style-22 .banner-info .bigtitle a,
					.nexio-banner.style-23 .banner-info .title,
					.nexio-banner.style-23 .banner-info .bigtitle,
					.nexio-banner.style-24 .banner-info .bigtitle,
					.nexio-banner.style-28 .banner-info .bigtitle,
					.nexio-banner.style-29 .banner-info .title,
					.nexio-banner.style-31 .banner-info .title,
					.nexio-banner.style-29 .banner-info .bigtitle,
					.nexio-banner.style-31 .banner-info .bigtitle,
					.nexio-banner.style-33 .banner-info .bigtitle,
					.nexio-banner.style-34 .banner-info .bigtitle,
					.nexio-banner.style-35 .banner-info .bigtitle,
					.nexio-banner.style-37 .banner-info .bigtitle,
					.nexio-banner.style-38 .banner-info .bigtitle,
					.nexio-banner.style-39 .banner-info .bigtitle,
					.nexio-banner.style-40 h3.bigtitle,
					.nexio-banner.style-41 h6.title,
					.nexio-iconbox.style-07 .content .title,
					#demo-popup .mfp-close,
					.nexio-products.style-1 .product-title-wrap > h3 ,
					.nexio-video.style-03 .video-info > h3,
					.nexio-video.style-04 .video-info > h3,
					.nexio-pinmap.style-02 .mapper-short-info-wrap .short-desc {
						font-family: "' . $body_font['family'] . '" !important;
						font-weight: 300;
					}
					.horizon-menu .main-header-content .main-menu > .menu-item > a,
					div .row.mobile-shop-real .page-title {
						font-family: "' . $body_font['family'] . '" !important;
					}
					.style-23 h3.block-title {
						font-family: "' . $body_font['family'] . '" !important;
						font-weight: 100;
					}
				';
			}
		}
		
		$css .= '
		        .bestseller-cat-products .block-title > a,
                .post-password-form input[type="submit"]:hover,
				.woocommerce-error .button:hover, .woocommerce-info .button:hover, .woocommerce-message .button:hover,
				.widget_shopping_cart .woocommerce-mini-cart__buttons .button.checkout,
				.widget_shopping_cart .woocommerce-mini-cart__buttons .button:not(.checkout):hover,
				#widget-area .widget .select2-container--default .select2-selection--multiple .select2-selection__choice,
				.woocommerce-widget-layered-nav-dropdown .woocommerce-widget-layered-nav-dropdown__submit:hover,
				.fami-btn:hover,
				.owl-carousel .owl-dots .owl-dot.active,
				.owl-carousel .owl-dots .owl-dot:hover,
				.search-view,
				.header .minicart-content-inner .minicart-number-items,
				.product-grid-title::before,
				.panel-categories.cate-image .owl-carousel .owl-nav > *:hover,
				.part-filter-wrap .filter-toggle,
				.part-filter-wrap .filter-toggle-button,
				.widget_categories ul li a:hover::before,
				.widget_search .searchform button:hover,
				.nexio_socials_list_widget .social::before,
				span.prdctfltr_reset-clone:hover,
				.onsale,
				#yith-wcwl-popup-message,
				.return-to-shop .button:hover,
				.comment-form .form-submit #submit:hover,
				.offer-boxed-product li::before,
				.reset_variations:hover,
				.summary .cart .single_add_to_cart_button:hover,
				.actions-btn .shopping:hover,
				.actions .coupon .button:hover,
				.wc-proceed-to-checkout .checkout-button:hover,
				.track_order .form-tracking .button:hover,
				body.error404 .error-404 .button:hover,
				#popup-newsletter .newsletter-form-wrap .submit-newsletter:hover,
				.page-404 a.button,
				.nexio-content-single-product-mobile .product-mobile-layout .woocommerce-product-gallery .flex-control-nav.flex-control-thumbs li img.flex-active,
				.bestseller-cat-products .block-title > a,
				.wpcf7-form .wpcf7-submit:hover,
				.nexio-tabs .tab-container .tab-panel .button-link:hover,
				.nexio-newsletter.style-03 .newsletter-form-wrap .submit-newsletter,
				.nexio-newsletter.style-04 .newsletter-form-wrap .submit-newsletter,
				.nexio-newsletter.style-04 .newsletter-form-wrap .submit-newsletter:hover,
				.nexio-newsletter.style-07 .newsletter-form-wrap .submit-newsletter,
				.nexio-newsletter.style-08 .newsletter-form-wrap .submit-newsletter:hover,
				.nexio-newsletter.style-10 .newsletter-form-wrap .submit-newsletter:hover,
				.nexio-newsletter.style-11 .newsletter-form-wrap .submit-newsletter:hover,
				.nexio-instagramshopwrap.style-03 .title-insshop,
				.nexio-socials.style-03 .social-item::before,
				.nexio-button.style-01 .button:hover,
				.nexio-button.style-02 .button:hover,
				.nexio-button.style-02 .button:hover,
				.nexio-banner.style-02 .banner-info .button:hover,
				.nexio-banner.style-03 .banner-info .button::before,
				.nexio-banner.style-04 .banner-info .button::before,
				.nexio-banner.style-05 .banner-info .button::before,
				.nexio-banner.style-06 .banner-info .button:hover,
				.nexio-banner.style-11 .banner-info .button:hover,
				.nexio-banner.style-22 .banner-info .bigtitle a:hover,
				.nexio-banner.style-39 .banner-info .button:hover,
				.nexio-iconbox.style-03 .icon,
				.nexio-iconbox.style-04 .button,
				.nexio-iconbox.style-05 .icon,
				.nexio-products.style-1 .button-link:hover,
				.nexio-products.style-2 .button-link:hover,
				.product-item.style-1 .button-loop-action .add-to-cart:hover,
				.product-item.style-1 .button-loop-action .yith-wcqv-button:hover,
				.product-item.style-1 .button-loop-action .yith-wcwl-add-to-wishlist:hover,
				.product-item.style-1 .button-loop-action .compare-button:hover,
				.product-item.style-1 .button-loop-action .fami-wccp-button:hover,
				.product-item.style-2 .button-loop-action .add-to-cart:hover,
				.product-item.style-2 .button-loop-action .yith-wcqv-button:hover,
				.product-item.style-2 .button-loop-action .yith-wcwl-add-to-wishlist:hover,
				.product-item.style-2 .button-loop-action .compare-button:hover,
				.product-item.style-2 .button-loop-action .fami-wccp-button:hover,
				.product-item.style-1 .button-loop-action .yith-wcqv-button:hover,
				.product-item.style-2 .button-loop-action .yith-wcqv-button:hover,
				.nexio-instagram-sc.style-02 .icon,
				a.backtotop,
				.product-item.style-2 .button-loop-action .add-to-cart:hover,
				.product-item.style-2 .button-loop-action .yith-wcqv-button:hover,
				.product-item.style-2 .button-loop-action .yith-wcwl-add-to-wishlist:hover,
				.product-item.style-2 .button-loop-action .compare-button:hover,
				.product-item.style-2 .button-loop-action .fami-wccp-button:hover,
				.nexio-title.style-03 .button:hover {
					background-color: ' . esc_attr( $main_color ) . ';
				}
				.widget_tag_cloud .tagcloud a:hover,
				.nexio-title.style-19 .button:hover,
				.nexio-socials.style-05 .social-item:hover,
				.nexio-banner.style-17 .banner-info .button:hover,
				.nexio-banner.style-18 .banner-info .button:hover,
				.nexio-banner.style-20 .banner-info .button:hover,
				.nexio-banner.style-23 .banner-info .button:hover,
				.nexio-banner.style-24 .banner-info .button:hover,
				.nexio-banner.style-33 .banner-info .button:hover,
				.nexio-banner.style-34 .banner-info .button:hover,
				.nexio-banner.style-35 .banner-info .button:hover,
				.nexio-products.text-light .button-link:hover,
				.post-item .tags a:hover,
				.nexio-share-socials a:hover,
				.woocommerce-cart-form-mobile .actions .actions-btn .shopping:hover,
				.summary .yith-wcwl-add-to-wishlist:hover,
				.main-product .with_background .summary .yith-wcwl-add-to-wishlist:hover {
					background-color: ' . esc_attr( $main_color ) . ';
					border-color: ' . esc_attr( $main_color ) . ';
				}
				a:hover, a:focus, a:active,
				.wcml-dropdown .wcml-cs-submenu li:hover > a,
				.horizon-menu .main-menu .menu-item .submenu .menu-item:hover > a,
				.horizon-menu .main-menu .menu-item:hover > .toggle-submenu,
				.close-vertical-menu:hover,
				.vertical-menu .main-navigation .main-menu > .menu-item:hover > a,
				.header-search-box .search-icon:hover,
				.header-search-box > .icons:hover,
				.instant-search-close:hover,
				.instant-search-modal .product-cats label span:hover,
				.instant-search-modal .product-cats label.selected span,
				.post-content .dropcap,
				.single-post-info .categories a:hover,
				.blog-grid .post-meta .categories,
				.filter-button-group .filter-list .blog-filter.active,
				.nexio-blog.style-01 .post-item .readmore,
				.woocommerce-product-gallery .woocommerce-product-gallery__trigger:hover,
				.woocommerce-product-gallery .flex-control-nav.flex-control-thumbs .slick-arrow,
				.summary .woocommerce-product-rating .woocommerce-review-link:hover,
				.detail-content .summary .price,
				.summary .stock.out-of-stock,
				div button.close,
				.social-share-product .share-product-title:hover,
				.nexio-social-product a:hover,
				.product_meta a:hover,
				.close-tab:hover,
				p.stars:hover a:before,
				p.stars.selected:not(:hover) a:before,
				.total-price-html,
				div.famibt-wrap .famibt-item .famibt-price,
				.famibt-wrap ins,
				.WOOF_Widget .woof_container .icheckbox_flat-purple.checked ~ label,
				.WOOF_Widget .woof_container .iradio_flat-purple.checked ~ label,
				.WOOF_Widget .woof_container li label.hover,
				.WOOF_Widget .woof_container li label.hover,
				.box-mobile-menu .back-menu:hover,
				.box-mobile-menu .close-menu:hover,
				.box-mobile-menu .main-menu .menu-item.active > a,
				.box-mobile-menu .main-menu .menu-item:hover > a,
				.box-mobile-menu .main-menu .menu-item:hover > .toggle-submenu::before,
				nav.woocommerce-breadcrumb a:hover,
				.toolbar-products .category-filter li.active a,
				.toolbar-products .category-filter li a:hover,
				div.prdctfltr_wc.prdctfltr_round .prdctfltr_filter label.prdctfltr_active > span,
				div.prdctfltr_wc.prdctfltr_round .prdctfltr_filter label:hover > span,
				.validate-required label::after,
				.woocommerce-MyAccount-navigation > ul li.is-active a,
				#popup-newsletter button.close:hover,
				.single-product-mobile .product-grid .product-info .price,
				.nexio-tabs .tab-head .tab-link > li.active,
				.nexio-tabs .tab-head .tab-link > li:hover,
				body .vc_toggle_default.vc_toggle_active .vc_toggle_title > h4,
				div.prdctfltr_wc.prdctfltr_round .prdctfltr_filter label:hover,
				.prdctfltr_sc.hide-cat-thumbs .product-category h2.woocommerce-loop-category__title:hover,
				.toolbar-products-mobile .cat-item.active, .toolbar-products-mobile .cat-item.active a,
				.real-mobile-toolbar.toolbar-products-shortcode .cat-item.active, .real-mobile-toolbar.toolbar-products-shortcode .cat-item.active a,
				.enable-shop-page-mobile .shop-page a.products-size.products-list.active,
				.enable-shop-page-mobile .shop-page .product-inner .price,
				.enable-shop-page-mobile .woocommerce-page-header ul .line-hover a:hover,
				.enable-shop-page-mobile .woocommerce-page-header ul .line-hover.active a,
				.price ins,
				body .woocommerce table.shop_table tr td.product-remove a:hover,
				.nexio-newsletter.style-01 .newsletter-form-wrap button:hover,
				.nexio-newsletter.style-02 .newsletter-form-wrap button:hover,
				.nexio-newsletter.style-06 .newsletter-form-wrap button:hover,
				.nexio-newsletter.style-12 .newsletter-form-wrap button:hover,
				.nexio-newsletter.style-13 .newsletter-form-wrap button:hover,
				.nexio-newsletter.style-05 .newsletter-form-wrap .submit-newsletter:hover,
				.nexio-newsletter.style-09 .newsletter-form-wrap .submit-newsletter:hover,
				.nexio-iconbox.style-01 .icon,
				.product-item.style-3 .button-loop-action .yith-wcqv-button:hover,
				.product-item.style-4 .button-loop-action .yith-wcqv-button::before:hover {
					color: ' . esc_attr( $main_color ) . ';
				}
				blockquote, q {
					border-left: 3px solid ' . esc_attr( $main_color ) . ';
				}
				.owl-carousel.circle-dark .owl-nav > *:hover {
					background-color: ' . esc_attr( $main_color ) . ' !important;
				}
				.banner-page .content-banner .page-title::before {
					border: 1px solid ' . esc_attr( $main_color ) . ';
				}
				.instant-search-modal .product-cats label span::before {
					border-bottom: 1px solid ' . esc_attr( $main_color ) . ';
				}
				.currency-language .wcml-dropdown-click a.wcml-cs-item-toggle:hover::before {
					border-color: ' . esc_attr( $main_color ) . ';
				}
				.currency-language .wcml-dropdown-click a.wcml-cs-item-toggle:hover::after,
				.currency-language .dropdown > a:hover::after {
					border-color: ' . esc_attr( $main_color ) . ' transparent transparent transparent;
				}
				.currency-language .dropdown > a:hover::before {
					border-color: ' . esc_attr( $main_color ) . ';
				}
				.currency-language .dropdown .active a,
				header .wcml_currency_switcher li li a:hover,
				.block-account a:hover,
				.header .nexio-minicart:hover .mini-cart-icon,
				.header .minicart-content-inner .close-minicart:hover,
				.header .minicart-items .product-cart .product-remove .remove:hover,
				.header .minicart-content-inner .actions .button:hover,
				.header .to-cart:hover {
					color: ' . esc_attr( $main_color ) . ';
				}
				.header .nexio-minicart .mini-cart-icon .minicart-number {
					background: ' . esc_attr( $main_color ) . ';
				}
				.header .to-cart::before,
				.blog-grid .title span::before,
				.filter-button-group .filter-list .blog-filter::before,
				.offer-boxed-product .title-offer::before,
				.panel-categories.cate-image .panel-categories-inner .category-title::before {
					border-bottom: 2px solid ' . esc_attr( $main_color ) . ';
				}
				.nexio-blog.style-03 .blog-heading::before,
				.panel-categories.cate-count .panel-categories-inner .category-title > a::before,
				.panel-categories.cate-icon .panel-categories-inner .category-title::before {
					border-bottom: 3px solid ' . esc_attr( $main_color ) . ';
				}
				.summary .compare:hover,
				.summary .fami-wccp-button:hover {
					color: ' . esc_attr( $main_color ) . ' !important;
				}
				@media (min-width: 1200px) {
					.unique-wrap .summary .woocommerce-variation-add-to-cart .yith-wcwl-add-to-wishlist:hover,
					.unique-wrap .summary .cart .woocommerce-variation-add-to-cart .single_add_to_cart_button:hover {
						background-color: transparent;
						color: ' . esc_attr( $main_color ) . ';
					}
				}
				.sticky_info_single_product button.nexio-single-add-to-cart-btn.btn.button,
				.famibt-messages-wrap a.button.wc-forward:hover {
					background: ' . esc_attr( $main_color ) . ';
				}
				a.product-sticky-toggle-tab-content::before,
				.wc-tabs li a::before {
					border-bottom: 2px solid ' . esc_attr( $main_color ) . ';
				}
				.products-size.active svg, .products-size:hover svg {
					stroke: ' . esc_attr( $main_color ) . ';
					fill: ' . esc_attr( $main_color ) . ';
				}
				.price_slider_amount .button:hover, .price_slider_amount .button:focus {
					background-color: ' . esc_attr( $main_color ) . ';
					border: 2px solid ' . esc_attr( $main_color ) . ';
				}
				.WOOF_Widget .woof_container li .icheckbox_flat-purple.hover,
				.WOOF_Widget .woof_container li .iradio_flat-purple.hover,
				.icheckbox_flat-purple.checked,
				.iradio_flat-purple.checked {
					background: ' . esc_attr( $main_color ) . ' 0 0 !important;
					border: 1px solid ' . esc_attr( $main_color ) . ' !important;
				}
				.toolbar-products .category-filter li a::before {
					border-bottom: 1px solid ' . esc_attr( $main_color ) . ';
				}
				div.prdctfltr_wc.prdctfltr_round .prdctfltr_filter label.prdctfltr_active > span::before,
				div.prdctfltr_wc.prdctfltr_round .prdctfltr_filter label:hover > span::before {
					background: ' . esc_attr( $main_color ) . ';
					border: 1px double ' . esc_attr( $main_color ) . ';
					color: ' . esc_attr( $main_color ) . ';
				}
				.prdctfltr_filter .prdctfltr_regular_title::before {
					border-top: 1px solid ' . esc_attr( $main_color ) . ';
				}
				.prdctfltr_sc.hide-cat-thumbs .product-category h2.woocommerce-loop-category__title::before {
					border-bottom: 1px solid ' . esc_attr( $main_color ) . ';
				}
				div.pf_rngstyle_flat .irs-from::after, div.pf_rngstyle_flat .irs-to::after, div.pf_rngstyle_flat .irs-single::after {
					border-top-color: ' . esc_attr( $main_color ) . ';
				}
				.prdctfltr_woocommerce_filter_submit:hover, .prdctfltr_wc .prdctfltr_buttons .prdctfltr_reset span:hover, .prdctfltr_sale:hover,
				.prdctfltr_instock:hover,
				.prdctfltr-pagination-load-more .button:hover,
				div.pf_rngstyle_flat .irs-bar,
				.enable-shop-page-mobile span.prdctfltr_title_selected,
				body .woocommerce table.shop_table .product-add-to-cart .add_to_cart:hover,
				.yith-wcqv-button .blockOverlay,
				.compare .blockOverlay,
				.woocommerce-MyAccount-content input.button:hover,
				.error404 .nexio-searchform button:hover {
					background: ' . esc_attr( $main_color ) . ';
				}
				.nexio-tabs .tab-head .tab-link > li::before {
					border-bottom: 2px solid ' . esc_attr( $main_color ) . ';
				}
				body.wpb-js-composer .vc_tta-style-classic .vc_tta-panel.vc_active .vc_tta-panel-title > a {
					color: ' . esc_attr( $main_color ) . ' !important;
				}
				.nexio-mapper .nexio-pin .nexio-popup-footer a:hover {
					background: ' . esc_attr( $main_color ) . ' !important;
					border-color: ' . esc_attr( $main_color ) . ' !important;
				}
				@media (min-width: 992px) {
					.ziss-popup-wrap .ziss-popup-inner .ziss-popup-body.ziss-right-no-content ~ .ziss-popup-nav:hover,
					.ziss-popup-wrap .ziss-popup-inner .ziss-popup-body:not(.ziss-right-no-content) ~ .ziss-popup-nav:hover {
						color: ' . esc_attr( $main_color ) . ';
					}
				}
				.nexio-title.style-12 .title-inner .block-title::before {
					border-bottom: 3px solid ' . esc_attr( $main_color ) . ';
				}
			
				.nexio-banner.style-01 .banner-info .button::before,
				.nexio-banner.style-09 .banner-info .button::before {
					border-bottom: 2px solid ' . esc_attr( $main_color ) . ';
				}
				.nexio-banner.style-03 .banner-info .button:hover,
				.nexio-banner.style-04 .banner-info .button:hover,
				.nexio-banner.style-05 .banner-info .button:hover,
				.nexio-banner.style-08 .banner-info .button::before {
					border-color: ' . esc_attr( $main_color ) . ';
				}
		';
		
		if ( $body_text_color && $body_text_color != '' ) {
			$css .= 'body {color: ' . esc_attr( $body_text_color ) . '}';
		}
		
		return $css;
	}
}

if ( ! function_exists( 'nexio_vc_custom_css_footer' ) ) {
	function nexio_vc_custom_css_footer() {
		
		$nexio_footer_options = nexio_get_option( 'nexio_footer_options', '' );
		$page_id              = nexio_get_single_page_id();
		
		$data_option_meta = get_post_meta( $page_id, '_custom_metabox_theme_options', true );
		if ( $page_id > 0 ) {
			$enable_custom_footer = false;
			if ( isset( $data_option_meta['enable_custom_footer'] ) ) {
				$enable_custom_footer = $data_option_meta['enable_custom_footer'];
			}
			if ( $enable_custom_footer ) {
				$nexio_footer_options = $data_option_meta['nexio_metabox_footer_options'];
			}
		}
		
		$shortcodes_custom_css = get_post_meta( $nexio_footer_options, '_wpb_post_custom_css', true );
		$shortcodes_custom_css .= get_post_meta( $nexio_footer_options, '_wpb_shortcodes_custom_css', true );
		$shortcodes_custom_css .= get_post_meta( $nexio_footer_options, '_nexio_shortcode_custom_css', true );
		$shortcodes_custom_css .= get_post_meta( $nexio_footer_options, '_responsive_js_composer_shortcode_custom_css', true );
		
		return $shortcodes_custom_css;
	}
}