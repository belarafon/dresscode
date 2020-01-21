<?php
/**
 *
 * Get option
 *
 * @since   1.0.0
 * @version 1.0.0
 *
 */

if ( ! function_exists( 'nexio_get_single_page_id' ) ) {
	
	/**
	 * Get single post, page, post type, shop page, my account... id
	 */
	function nexio_get_single_page_id() {
		$single_id = get_the_ID();
		if ( class_exists( 'WooCommerce' ) && is_woocommerce() && ! is_product() ) {
			$single_id = get_option( 'woocommerce_shop_page_id' );
		}
		
		return $single_id;
	}
}

if ( ! function_exists( 'nexio_get_option' ) ) {
	function nexio_get_option( $option_name = '', $default = '' ) {
		$get_value = isset( $_GET[ $option_name ] ) ? $_GET[ $option_name ] : '';
		
		$cs_option = null;
		
		if ( defined( 'CS_VERSION' ) ) {
			$cs_option = get_option( CS_OPTION );
		}
		if ( isset( $_GET[ $option_name ] ) ) {
			$cs_option = $get_value;
			$default   = $get_value;
		}
		
		$options = apply_filters( 'cs_get_option', $cs_option, $option_name, $default );
		
		if ( ! empty( $option_name ) && ! empty( $options[ $option_name ] ) ) {
			return $options[ $option_name ];
		} else {
			return ( ! empty( $default ) ) ? $default : null;
		}
		
	}
}

add_filter( 'body_class', 'nexio_body_class' );
if ( ! function_exists( 'nexio_body_class' ) ) {
	
	function nexio_body_class( $classes ) {
		
		$animation_on_scroll = nexio_get_option( 'animation_on_scroll', '' );
		$the_theme           = wp_get_theme();
		$classes[]           = $the_theme->get( 'template' ) . "-" . $the_theme->get( 'Version' );
		
		if ( nexio_is_mobile() ) {
			$classes[]            = 'nexio-is-real-mobile';
			$enable_header_mobile = nexio_get_option( 'enable_header_mobile', true );
			if ( $enable_header_mobile ) {
				$classes[] = 'enable-header-mobile';
			}
			if ( class_exists( 'WooCommerce' ) ) {
				$enable_shop_mobile = nexio_get_option( 'enable_shop_mobile', true );
				$classes[]          = 'enable-shop-page-mobile';
				if ( is_shop() || is_product_category() || is_product_taxonomy() ) {
					if ( $enable_shop_mobile ) {
						$classes[] = 'shop-page-mobile';
					}
				}
				if ( is_product() ) {
					$enable_single_product_mobile = nexio_get_option( 'enable_single_product_mobile', true );
					if ( $enable_single_product_mobile ) {
						$classes[] = 'single-product-mobile';
					}
				}
			}
		} else {
			$classes[] = 'nexio-none-mobile-device';
		}
		
		if ( $animation_on_scroll ) {
			$classes[] = 'enable-animation-on-scroll';
		}
		
		return $classes;
	}
}

add_action( 'nexio_before_page_wrapper', 'nexio_preloader' );
if ( ! function_exists( 'nexio_preloader' ) ) {
	function nexio_preloader() {
		$enable_preloader = nexio_get_option( 'enable_preloader', false );
		if ( $enable_preloader ) {
			$preloader_style = nexio_get_option( 'preloader_style', 'default' );
			$preloader_text  = nexio_get_option( 'preloader_text', esc_html__( 'Nexio', 'nexio' ) );
			if ( $preloader_style == 'default' ) {
				?>
                <div class="nexio-default-preloader">
                    <div class="nexio-ripple">
                        <div></div>
                        <div></div>
                    </div>
                </div>
				<?php
			}
			if ( $preloader_style == 'block_rotate' ) {
				?>
                <div class="nexio-preloader wppu-trendy-frontend-body">
                    <div class="nexio-preloader-inner wppu-trendy-frontend-inner">
                        <div class="item-inner">
                            <div class="item-inner">
                                <div class="item-loader-container">
                                    <div class="nexio-preloader-block nexio-preloader-block-1 wppu_trendy_block trendy_wrapper1_loader">
                                        <div class="first-wrapper">
                                            <div class="square-loader">
                                                <div class="wrapper-square first_square"></div>
                                                <div class="wrapper-square second_square"></div>
                                                <div class="wrapper-square third_square"></div>
                                            </div>
                                        </div>
                                        <span class="nexio-preloader-text trendy_loading_text"><?php echo sanitize_text_field( $preloader_text ); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
				<?php
			}
			if ( $preloader_style == 'segment_blocks' ) {
				?>
                <div class="nexio-preloader wppu-trendy-frontend-body">
                    <div class="nexio-preloader-inner wppu-trendy-frontend-inner">
                        <div class="item-inner">
                            <div class="item-inner">
                                <div class="item-loader-container">
                                    <div class="nexio-preloader-block nexio-segment-loader-block wppu_trendy_block trendy_segment_loader">
                                        <div class="nexio-segment-loader">
                                            <div class="nexio-segment-loader-holder">
                                                <div class="nexio-segment nexio-segment-one"></div>
                                            </div>
                                            <div class="nexio-segment-loader-holder">
                                                <div class="nexio-segment nexio-segment-two"></div>
                                            </div>
                                            <div class="nexio-segment-loader-holder">
                                                <div class="nexio-segment nexio-segment-three"></div>
                                            </div>
                                            <div class="nexio-segment-loader-holder">
                                                <div class="nexio-segment nexio-segment-four"></div>
                                            </div>
                                        </div>
                                        <span class="nexio-preloader-text"><?php echo sanitize_text_field( $preloader_text ); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
				<?php
			}
			if ( $preloader_style == 'text_fill' ) {
				?>
                <section class="nexio-preloader-section loading not-loaded">

                    <div class="nexio-preloader-progress-bar nexio-progress-pos-top"
                         style="background-color:#ed4e6e;height:3px;"></div>
                    <div class="nexio-preloader-text-loader wppu-logo-loader">
                        <div class="nexio-frame">
                            <div class="nexio-preloader">
                                <div id="nexio-fill-loader">

                                    <div id="nexio-preloader-text">
                                    <span style="font-size: 99px;"
                                          class="nexio-preloader-text-1"><?php echo sanitize_text_field( $preloader_text ); ?></span>
                                        <span style="font-size: 99px;color: #ffffff;"
                                              class="nexio-preloader-text-2"><?php echo sanitize_text_field( $preloader_text ); ?></span>
                                    </div>
                                    <div class="nexio-preloader-counter"
                                         style="color:#000;font-size:20px;"><span>0</span>%
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </section>
				<?php
			}
			
		}
	}
}

function nexio_set_post_views( $postID ) {
	$count_key = 'nexio_post_views_count';
	$count     = get_post_meta( $postID, $count_key, true );
	if ( $count == '' ) {
		$count = 0;
		delete_post_meta( $postID, $count_key );
		add_post_meta( $postID, $count_key, '0' );
	} else {
		$count ++;
		update_post_meta( $postID, $count_key, $count );
	}
}

remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );

if ( ! function_exists( 'nexio_paging_nav' ) ) {
	
	/**
	 * Display navigation to next/previous set of posts when applicable.
	 *
	 * @since Nexio 1.0
	 *
	 */
	function nexio_paging_nav() {
		global $wp_query;
		
		// Don't print empty markup if there's only one page.
		if ( $wp_query->max_num_pages < 2 ) {
			return;
		}
		echo get_the_posts_pagination();
	}
}

if ( ! function_exists( 'nexio_get_logo' ) ) {
	
	/**
	 * Function get the site logo
	 *
	 * @since  Nexio 1.0
	 * @author FamiThemes
	 **/
	function nexio_get_logo() {
		
		$id_page          = nexio_get_single_page_id();
		$width            = nexio_get_option( 'width_logo', '70' );
		$width            .= 'px';
		$logo_url_default = get_template_directory_uri() . '/assets/images/logo-dark.svg';
		$logo_url         = $logo_url_default;
		$data_meta        = get_post_meta( $id_page, '_custom_metabox_theme_options', true );
		$logo             = nexio_get_option( 'nexio_logo' );
		if ( isset( $data_meta['metabox_nexio_logo'] ) && $data_meta['metabox_nexio_logo'] != '' ) {
			$logo = $data_meta['metabox_nexio_logo'];
		}
		if ( ( ! is_front_page() && is_home() ) || is_category() || is_tag() ) {
			$logo = nexio_get_option( 'blog_logo' );
		}
		$queried_obj = '';
		if ( ! is_home() && ! is_date() ) {
			$queried_obj = get_queried_object();
		}
		if ( class_exists( 'WooCommerce' ) ) {
			if ( is_shop() || is_product_category() || is_product_tag() || ( isset( $queried_obj->taxonomy ) && $queried_obj->taxonomy == 'product_brand' ) ) {
				$logo = nexio_get_option( 'shop_logo' );
			}
		}
		if ( $logo ) {
			$logo_url_default = get_template_directory_uri() . '/assets/images/logo-light.svg';
			$logo_url         = wp_get_attachment_image_url( $logo, 'full' );
		}
		
		if ( ! $logo_url ) {
			$logo_url = $logo_url_default;
		}
		
		$html = '<a href="' . esc_url( home_url( '/' ) ) . '"><img style="width:' . esc_attr( $width ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" src="' . esc_url( $logo_url ) . '" class="_rw" /></a>';
		echo apply_filters( 'nexio_site_logo', $html );
	}
}
if ( ! function_exists( 'nexio_get_logo_mobile' ) ) {
	
	/**
	 * Function get the site logo
	 *
	 * @since  Nexio 1.0
	 * @author FamiThemes
	 **/
	function nexio_get_logo_mobile() {
		$width    = nexio_get_option( 'width_logo_mobile', '70' );
		$width    .= 'px';
		$logo_url = get_template_directory_uri() . '/assets/images/logo-dark.svg';
		$logo     = nexio_get_option( 'nexio_logo_mobile' );
		if ( $logo != '' ) {
			$logo_url = wp_get_attachment_image_url( $logo, 'full' );
		}
		$html = '<a href="' . esc_url( home_url( '/' ) ) . '"><img style="width:' . esc_attr( $width ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" src="' . esc_url( $logo_url ) . '" class="_rw" /></a>';
		echo apply_filters( 'nexio_site_logo', $html );
	}
}
/* GET SEARCH FORM */
if ( ! function_exists( 'nexio_search_form' ) ) {
	/**
	 * Function get the search form template
	 *
	 * @since  Nexio 1.0
	 * @author FamiThemes
	 **/
	function nexio_search_form( $suffix = '' ) {
		get_template_part( 'template-parts/search', 'form' . $suffix );
	}
}

if ( ! function_exists( 'nexio_placehold_image' ) ) {
	
	/**
	 * No image generator
	 *
	 * @since 1.0
	 *
	 * @param $size : array, image size
	 * @param $echo : bool, echo or return no image url
	 **/
	function nexio_placehold_image(
		$size = array(
			'width'  => 500,
			'height' => 500
		), $echo = false, $transparent = false
	) {
		$noimage_dir = get_template_directory() . '/assets';
		$noimage_uri = get_template_directory_uri() . '/assets';
		$suffix      = ( $transparent ) ? '_transparent' : '';
		if ( ! is_array( $size ) || empty( $size ) ):
			$size = array( 'width' => 500, 'height' => 500 );
		endif;
		if ( ! is_numeric( $size['width'] ) && $size['width'] == '' || $size['width'] == null ):
			$size['width'] = 'auto';
		endif;
		if ( ! is_numeric( $size['height'] ) && $size['height'] == '' || $size['height'] == null ):
			$size['height'] = 'auto';
		endif;
		
		if ( file_exists( $noimage_dir . '/images/placehold/placehold_img' . $suffix . '-' . $size['width'] . 'x' . $size['height'] . '.png' ) ) {
			if ( $echo ) {
				echo esc_url( $noimage_uri . '/images/placehold/placehold_img' . $suffix . '-' . $size['width'] . 'x' . $size['height'] . '.png' );
			}
			
			return esc_url( $noimage_uri . '/images/placehold/placehold_img' . $suffix . '-' . $size['width'] . 'x' . $size['height'] . '.png' );
		}
		
		// Base image must be exist
		$img_base_fullpath = $noimage_dir . '/images/placehold/placehold_img' . $suffix . '.png';
		$no_image_src      = $noimage_uri . '/images/placehold/placehold_img' . $suffix . '.png';
		
		// Check no image exist or not
		if ( ! file_exists( $noimage_dir . '/images/placehold/placehold_img' . $suffix . '-' . $size['width'] . 'x' . $size['height'] . '.png' ) && is_writable( $noimage_dir . '/images/placehold/' ) ):
			$no_image = wp_get_image_editor( $img_base_fullpath );
			if ( ! is_wp_error( $no_image ) ):
				$no_image->resize( $size['width'], $size['height'], true );
				$no_image_name = $no_image->generate_filename( $size['width'] . 'x' . $size['height'], $noimage_dir . '/images/placehold/', null );
				$no_image->save( $no_image_name );
			endif;
		endif;
		
		// Check no image exist after resize
		$noimage_path_exist_after_resize = $noimage_dir . '/images/placehold/placehold_img' . $suffix . '-' . $size['width'] . 'x' . $size['height'] . '.png';
		if ( file_exists( $noimage_path_exist_after_resize ) ):
			$no_image_src = $noimage_uri . '/images/placehold/placehold_img' . $suffix . '-' . $size['width'] . 'x' . $size['height'] . '.png';
		endif;
		
		if ( $echo ) {
			echo esc_url( $no_image_src );
		}
		
		return esc_url( $no_image_src );
	}
}

if ( ! function_exists( 'nexio_resize_image' ) ) {
	
	/**
	 * @param int    $attach_id
	 * @param string $img_url
	 * @param int    $width
	 * @param int    $height
	 * @param bool   $crop
	 * @param bool   $place_hold        Using place hold image if the image does not exist
	 * @param bool   $use_real_img_hold Using real image for holder if the image does not exist
	 * @param string $solid_img_color   Solid placehold image color (not text color). Random color if null
	 *
	 * @since Nexio 1.0
	 * @return array
	 */
	function nexio_resize_image( $attach_id = null, $img_url = null, $width, $height, $crop = false, $place_hold = true, $use_real_img_hold = true, $solid_img_color = null ) {
		$img_on_curent_host = true;
		$remote_img_url     = '';
		
		/*If is singular and has post thumbnail and $attach_id is null, so we get post thumbnail id automatic*/
		if ( is_singular() && ! $attach_id ) {
			if ( has_post_thumbnail() && ! post_password_required() ) {
				$attach_id = get_post_thumbnail_id();
			}
		}
		/*this is an attachment, so we have the ID*/
		$image_src = array();
		if ( $attach_id ) {
			if ( has_image_size( "fami_img_size_{$width}x{$height}" ) ) {
				$image_src = wp_get_attachment_image_src( $attach_id, "fami_img_size_{$width}x{$height}" );
				if ( ! empty( $image_src ) ) {
					
					return array(
						'url'    => $image_src[0],
						'width'  => $image_src[1],
						'height' => $image_src[2]
					);
				}
			} else {
				$image_src = wp_get_attachment_image_src( $attach_id, 'full' );
			}
			$actual_file_path = get_attached_file( $attach_id );
			
			if ( isset( $image_src[0] ) ) {
				$remote_img_url      = $image_src[0];
				$img_parse           = parse_url( $image_src[0] );
				$current_host_domain = sanitize_text_field( $_SERVER['SERVER_NAME'] );
				if ( $current_host_domain != $img_parse['host'] ) {
					$img_on_curent_host = false;
				}
			}
			
			/*this is not an attachment, let's use the image url*/
		} else if ( $img_url ) {
			$file_path        = str_replace( get_site_url(), get_home_path(), $img_url );
			$actual_file_path = rtrim( $file_path, '/' );
			if ( ! file_exists( $actual_file_path ) ) {
				$file_path        = parse_url( $img_url );
				$actual_file_path = rtrim( ABSPATH, '/' ) . $file_path['path'];
			}
			if ( file_exists( $actual_file_path ) ) {
				$orig_size    = getimagesize( $actual_file_path );
				$image_src[0] = $img_url;
				$image_src[1] = $orig_size[0];
				$image_src[2] = $orig_size[1];
			} else {
				$image_src[0] = '';
				$image_src[1] = 0;
				$image_src[2] = 0;
			}
		}
		if ( ! empty( $actual_file_path ) && file_exists( $actual_file_path ) ) {
			$file_info = pathinfo( $actual_file_path );
			$extension = '.' . $file_info['extension'];
			/*the image path without the extension*/
			$no_ext_path      = $file_info['dirname'] . '/' . $file_info['filename'];
			$cropped_img_path = $no_ext_path . '-' . $width . 'x' . $height . $extension;
			/*checking if the file size is larger than the target size*/
			/*if it is smaller or the same size, stop right here and return*/
			if ( $image_src[1] > $width || $image_src[2] > $height ) {
				/*the file is larger, check if the resized version already exists (for $crop = true but will also work for $crop = false if the sizes match)*/
				if ( file_exists( $cropped_img_path ) ) {
					$cropped_img_url = str_replace( basename( $image_src[0] ), basename( $cropped_img_path ), $image_src[0] );
					$vt_image        = array(
						'url'    => $cropped_img_url,
						'width'  => $width,
						'height' => $height,
					);
					
					return $vt_image;
				}
				
				if ( $crop == false ) {
					/*calculate the size proportionaly*/
					$proportional_size = wp_constrain_dimensions( $image_src[1], $image_src[2], $width, $height );
					$resized_img_path  = $no_ext_path . '-' . $proportional_size[0] . 'x' . $proportional_size[1] . $extension;
					/*checking if the file already exists*/
					if ( file_exists( $resized_img_path ) ) {
						$resized_img_url = str_replace( basename( $image_src[0] ), basename( $resized_img_path ), $image_src[0] );
						$vt_image        = array(
							'url'    => $resized_img_url,
							'width'  => $proportional_size[0],
							'height' => $proportional_size[1],
						);
						
						return $vt_image;
					}
				}
				/*no cache files - let's finally resize it*/
				$img_editor = wp_get_image_editor( $actual_file_path );
				if ( is_wp_error( $img_editor ) || is_wp_error( $img_editor->resize( $width, $height, $crop ) ) ) {
					return array(
						'url'    => '',
						'width'  => '',
						'height' => '',
					);
				}
				$new_img_path = $img_editor->generate_filename();
				if ( is_wp_error( $img_editor->save( $new_img_path ) ) ) {
					return array(
						'url'    => '',
						'width'  => '',
						'height' => '',
					);
				}
				if ( ! is_string( $new_img_path ) ) {
					return array(
						'url'    => '',
						'width'  => '',
						'height' => '',
					);
				}
				$new_img_size = getimagesize( $new_img_path );
				$new_img      = str_replace( basename( $image_src[0] ), basename( $new_img_path ), $image_src[0] );
				/*resized output*/
				$vt_image = array(
					'url'    => $new_img,
					'width'  => $new_img_size[0],
					'height' => $new_img_size[1],
				);
				
				return $vt_image;
			}
			/*default output - without resizing*/
			$vt_image = array(
				'url'    => $image_src[0],
				'width'  => $image_src[1],
				'height' => $image_src[2],
			);
			
			return $vt_image;
		} else {
			if ( ! $img_on_curent_host && $remote_img_url != '' ) {
				$vt_image = array(
					'url'    => $remote_img_url,
					'width'  => $width,
					'height' => $height,
				);
				
				return $vt_image;
				
			} else {
				if ( $place_hold ) {
					$width  = intval( $width );
					$height = intval( $height );
					/*Real image place hold (https://unsplash.it/)*/
					if ( $use_real_img_hold ) {
						$random_time = time() + rand( 1, 100000 );
						$vt_image    = array(
							'url'    => 'https://unsplash.it/' . $width . '/' . $height . '?random&time=' . $random_time,
							'width'  => $width,
							'height' => $height,
						);
					} else {
						$vt_image = array(
							'url'    => 'https://placehold.it/' . $width . 'x' . $height,
							'width'  => $width,
							'height' => $height,
						);
					}
					
					return $vt_image;
				}
			}
		}
		
		return false;
	}
}

if ( ! function_exists( 'nexio_img_lazy' ) ) {
	function nexio_img_lazy( $width = 1, $height = 1 ) {
		$img_lazy = nexio_placehold_image(
			array(
				'width'  => $width,
				'height' => $height
			), false, true );
		
		return $img_lazy;
	}
}

if ( ! function_exists( 'nexio_img_output' ) ) {
	/**
	 * @param array  $img
	 * @param string $class
	 * @param string $alt
	 * @param string $title
	 *
	 * @return string
	 */
	function nexio_img_output( $img, $class = '', $alt = '', $title = '' ) {
		
		$img_default = array(
			'width'  => '',
			'height' => '',
			'url'    => ''
		);
		$img         = wp_parse_args( $img, $img_default );
		$enable_lazy = nexio_get_option( 'nexio_enable_lazy', false );
		
		if ( $enable_lazy ) {
			$img_lazy = nexio_img_lazy( $img['width'], $img['height'] );
			$img_html = '<img class="fami-img fami-lazy lazy ' . esc_attr( $class ) . '" width="' . esc_attr( $img['width'] ) . '" height="' . esc_attr( $img['height'] ) . '" src="' . $img_lazy . '" data-src="' . esc_url( $img['url'] ) . '" alt="' . esc_attr( $alt ) . '" title="' . esc_attr( $title ) . '" />';
		} else {
			$img_html = '<img class="fami-img ' . esc_attr( $class ) . '" width="' . esc_attr( $img['width'] ) . '" height="' . esc_attr( $img['height'] ) . '" src="' . esc_url( $img['url'] ) . '" alt="' . esc_attr( $alt ) . '" title="' . esc_attr( $title ) . '" />';
		}
		
		return $img_html;
	}
}

/* GET HEADER */

if ( ! function_exists( 'nexio_get_header' ) ) {
	/**
	 * Function get the header form template
	 *
	 * @since  Nexio 1.0
	 * @author FamiThemes
	 **/
	function nexio_get_header() {
		/* Data MetaBox */
		$default_header_used = 'style-03';
		
		$nexio_used_header    = nexio_get_option( 'nexio_used_header', $default_header_used );
		$single_id            = nexio_get_single_page_id();
		$data_meta            = get_post_meta( $single_id, '_custom_metabox_theme_options', true );
		$enable_custom_header = false;
		
		if ( $single_id > 0 ) {
			
			// Override custom header (if request from url)
			if ( isset( $_GET['enable_custom_header'] ) ) {
				$data_meta['enable_custom_header'] = $_GET['enable_custom_header'] == 'yes';
			}
			
			if ( isset( $data_meta['enable_custom_header'] ) ) {
				$enable_custom_header = $data_meta['enable_custom_header'];
			}
		}
		
		if ( ! empty( $data_meta ) && $enable_custom_header ) {
			$nexio_used_header = isset( $data_meta['nexio_metabox_used_header'] ) ? $data_meta['nexio_metabox_used_header'] : $nexio_used_header;
		}
		
		if ( trim( $nexio_used_header ) == '' ) {
			$nexio_used_header = $default_header_used;
		}
		
		$enable_header_mobile = nexio_get_option( 'enable_header_mobile', false );
		if ( $enable_header_mobile && nexio_is_mobile() ) {
			get_template_part( 'templates/header', 'mobile' );
		} else {
			get_template_part( 'templates/headers/header', $nexio_used_header );
		}
		
		do_action( 'nexio_after_header' );
	}
}
/*NEWSLETTER*/
if ( ! function_exists( 'nexio_get_popup_newsletter' ) ) {
	function nexio_get_popup_newsletter() {
		$nexio_newsletter_id = nexio_get_option( 'nexio_newsletter_popup', '' );
		
		/* Data MetaBox */
		$enable_newsletter = nexio_get_option( 'enable_newsletter', false );
		
		$disable_on_mobile = nexio_get_option( 'disable_newsletter_popup_on_mobile', false );
		
		if ( $disable_on_mobile && nexio_is_mobile() ) {
			return;
		}
		
		$class = 'disable-on-mobile';
		if ( ! $disable_on_mobile ) {
			$class = 'enable-on-mobile';
		}
		
		$query = new WP_Query(
			array(
				'p'              => $nexio_newsletter_id,
				'post_type'      => 'newsletter',
				'posts_per_page' => 1
			)
		);
		
		if ( $enable_newsletter ) {
			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post(); ?>
                    <div class="modal fade <?php echo esc_attr( $class ); ?>" id="popup-newsletter" tabindex="-1"
                         role="dialog" data-on-mobile="<?php echo esc_attr( $disable_on_mobile ); ?>">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <button type="button" class="close" data-dismiss="modal"
                                        aria-label="<?php echo esc_attr__( 'Close', 'nexio' ); ?>">
									<?php echo esc_html__( 'close', 'nexio' ); ?>
                                </button>
                                <div class="modal-inner">
                                    <div class="newsletter-content">
										<?php the_content(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
					<?php
				};
			};
		};
		wp_reset_postdata();
		
	}
}

if ( ! function_exists( 'nexio_get_footer' ) ) {
	function nexio_get_footer() {
		$nexio_footer_id = nexio_get_option( 'nexio_footer_options', '' );
		
		/* Data MetaBox */
		$page_id              = nexio_get_single_page_id();
		$enable_custom_footer = false;
		$disable_footer       = false;
		
		$data_option_meta = get_post_meta( $page_id, '_custom_metabox_theme_options', true );
		if ( isset( $data_option_meta['enable_custom_footer'] ) ) {
			$enable_custom_footer = $data_option_meta['enable_custom_footer'] === true;
			if ( isset( $data_option_meta['disable_footer'] ) ) {
				$disable_footer = $data_option_meta['disable_footer'] === true;
			}
		}
		
		if ( $page_id > 0 && $enable_custom_footer ) {
			$nexio_footer_id = $data_option_meta['nexio_metabox_footer_options'];
			$data_meta       = get_post_meta( $data_option_meta['nexio_metabox_footer_options'], '_custom_footer_options', true );;
		}
		if ( empty( $data_meta ) ) {
			$nexio_template_style = 'default';
		} else {
			$nexio_template_style = $data_meta['nexio_footer_style'];
		}
		$allowed_html = array(
			'a' => array(
				'href' => array(),
			),
		);
		
		$query = new WP_Query( array( 'p' => $nexio_footer_id, 'post_type' => 'footer', 'posts_per_page' => 1 ) );
		if ( $enable_custom_footer == '1' && $disable_footer == '1' ) :
        elseif ( $query->have_posts() ):
			while ( $query->have_posts() ): $query->the_post(); ?>
				<?php if ( $nexio_template_style == 'default' ): ?>
                    <footer class="footer nexio-footer-builder footer-id-<?php echo esc_attr( get_the_ID() ); ?>">
                        <div class="nexio-container">
							<?php the_content(); ?>
                        </div>
                    </footer>
				<?php else: ?>
					<?php get_template_part( 'templates/footers/footer', $nexio_template_style ); ?>
				<?php endif; ?>
			<?php endwhile;
		else: ?>
            <footer class="footer wp-default">
                <div class="container">
					<?php printf( wp_kses( __( '&copy; 2019 <a href="%1$s">Famithemes</a>. All Rights Reserved.', 'nexio' ), $allowed_html ), esc_url( 'https://famithemes.com' ) ); ?>
                </div>
            </footer>
			<?php
		endif;
		wp_reset_postdata();
	}
}

/* GET FOOTER */

if ( ! function_exists( 'nexio_get_title' ) ) {
	function nexio_get_title() {
		$output      = '';
		$output_html = '';
		if ( ! is_front_page() && ! is_home() ) {
			if ( is_page() ) {
				$output = get_the_title();
			} elseif ( is_single() ) {
			
			} elseif ( is_search() ) {
			
			} elseif ( is_404() ) {
			
			} else {
				$output = get_the_archive_title();
				if ( class_exists( 'WooCommerce' ) ) {
					if ( is_shop() ) {
						$shop_page_id = wc_get_page_id( 'shop' );
						$output       = get_the_title( $shop_page_id );
					}
				}
			}
		}
		
		if ( $output != '' ) {
			$output_html = '<div class="title-page">' . $output . '</div>';
		}
		
		return $output_html;
	}
}

if ( ! function_exists( 'nexio_comments_list' ) ) {
	function nexio_comments_list( $comment, $args, $depth ) {
		
		// Globalize comment object
		$GLOBALS['comment'] = $comment;
		
		switch ( $comment->comment_type ) :
			
			case 'pingback'  :
			case 'trackback' :
				?>
                <li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
                <p>
					<?php
					echo esc_html__( 'Pingback:', 'nexio' );
					comment_author_link();
					edit_comment_link( esc_html__( 'Edit', 'nexio' ), '<span class="edit-link">', '</span>' );
					?>
                </p>
				<?php
				break;
			
			default :
				global $post;
				?>
                <li <?php comment_class( 'mt__30' ); ?> id="li-comment-<?php comment_ID(); ?>">
                <article id="comment-<?php comment_ID(); ?>"
                         class="comment_container" <?php nexio_schema_metadata( array( 'context' => 'comment' ) ); ?>>
                    <div class="nexio-avatar"><?php echo get_avatar( $comment, 120 ); ?></div>
                    <div class="comment-text">
						<?php if ( '0' == $comment->comment_approved ) : ?>
                            <p class="comment-awaiting-moderation"><?php echo esc_html__( 'Your comment is awaiting moderation.', 'nexio' ); ?></p>
						<?php endif; ?>
                        <div class="comment-top">
                            <div class="comment-meta">
								<?php
								printf(
									'<h5 class="comment-author" ' .
									nexio_schema_metadata(
										array(
											'context' => 'comment_author',
											'echo'    => false
										) ) . '><span ' .
									nexio_schema_metadata(
										array(
											'context' => 'author_name',
											'echo'    => false
										) ) . '>%1$s</span></h5>',
									
									get_comment_author_link(),
									( $comment->user_id == $post->post_author ) ? '<span class="author-post">' . esc_html__( 'Post author', 'nexio' ) . '</span>' : ''
								);
								printf(
									'<time class="grow" ' . nexio_schema_metadata( array(
										                                               'context' => 'entry_time',
										                                               'echo'    => false
									                                               ) ) . '>%3$s</time>',
									esc_url( get_comment_link( $comment->comment_ID ) ),
									get_comment_time( 'c' ),
									sprintf( wp_kses_post( '%1$s' ), get_comment_date() )
								);
								?>
                            </div>
                            <div class="flex">
								<?php
								edit_comment_link( esc_html__( 'Edit', 'nexio' ) );
								comment_reply_link(
									array_merge(
										$args,
										array(
											'reply_text' => esc_html__( 'Reply', 'nexio' ),
											'depth'      => $depth,
											'max_depth'  => $args['max_depth'],
										)
									)
								);
								?>
                            </div><!-- .action-link -->
                        </div>
                        <div <?php nexio_schema_metadata( array( 'context' => 'entry_content' ) ); ?>>
							<?php comment_text(); ?>
                        </div>
                    </div><!-- .comment-content -->
                </article><!-- #comment- -->
				<?php
				break;
		
		endswitch;
	}
}

if ( ! function_exists( 'nexio_schema_metadata' ) ) {
	function nexio_schema_metadata( $args ) {
		
		// Set default arguments
		$default_args = array(
			'post_type' => '',
			'context'   => '',
			'echo'      => true,
		);
		
		$args = apply_filters( 'nexio_schema_metadata_args', wp_parse_args( $args, $default_args ) );
		
		if ( empty( $args['context'] ) ) {
			return;
		}
		
		// Markup string - stores markup output
		$markup     = ' ';
		$attributes = array();
		
		// Try to fetch the right markup
		switch ( $args['context'] ) {
			case 'body':
				$attributes['itemscope'] = 'itemscope';
				$attributes['itemtype']  = 'http://schema.org/WebPage';
				break;
			
			case 'header':
				$attributes['itemscope'] = 'itemscope';
				$attributes['itemtype']  = 'http://schema.org/WPHeader';
				break;
			
			case 'nav':
				$attributes['role']      = 'navigation';
				$attributes['itemscope'] = 'itemscope';
				$attributes['itemtype']  = 'http://schema.org/SiteNavigationElement';
				break;
			
			case 'content':
				$attributes['role']     = 'main';
				$attributes['itemprop'] = 'mainContentOfPage';
				
				// Frontpage, Blog, Archive & Single Post
				if ( is_singular( 'post' ) || is_archive() || is_home() ) {
					$attributes['itemscope'] = 'itemscope';
					$attributes['itemtype']  = 'http://schema.org/Blog';
				}
				
				// Search Results Pages
				if ( is_search() ) {
					$attributes['itemscope'] = 'itemscope';
					$attributes['itemtype']  = 'http://schema.org/SearchResultsPage';
				}
				break;
			
			case 'entry':
				$attributes['itemscope'] = 'itemscope';
				$attributes['itemtype']  = 'http://schema.org/CreativeWork';
				break;
			
			case 'image':
				$attributes['itemscope'] = 'itemscope';
				$attributes['itemtype']  = 'http://schema.org/ImageObject';
				break;
			
			case 'image_url':
				$attributes['itemprop'] = 'contentURL';
				break;
			
			case 'name':
				$attributes['itemprop'] = 'name';
				break;
			
			case 'email':
				$attributes['itemprop'] = 'email';
				break;
			
			case 'url':
				$attributes['itemprop'] = 'url';
				break;
			
			case 'author':
				$attributes['itemprop']  = 'author';
				$attributes['itemscope'] = 'itemscope';
				$attributes['itemtype']  = 'http://schema.org/Person';
				break;
			
			case 'author_link':
				$attributes['itemprop'] = 'url';
				break;
			
			case 'author_name':
				$attributes['itemprop'] = 'name';
				break;
			
			case 'author_description':
				$attributes['itemprop'] = 'description';
				break;
			
			case 'entry_time':
				$attributes['itemprop'] = 'datePublished';
				$attributes['datetime'] = get_the_time( 'c' );
				break;
			
			case 'entry_title':
				$attributes['itemprop'] = 'headline';
				break;
			
			case 'entry_content':
				$attributes['itemprop'] = 'text';
				break;
			
			case 'comment':
				$attributes['itemprop']  = 'comment';
				$attributes['itemscope'] = 'itemscope';
				$attributes['itemtype']  = 'http://schema.org/Comment';
				break;
			
			case 'comment_author':
				$attributes['itemprop']  = 'creator';
				$attributes['itemscope'] = 'itemscope';
				$attributes['itemtype']  = 'http://schema.org/Person';
				break;
			
			case 'comment_author_link':
				$attributes['itemprop']  = 'creator';
				$attributes['itemscope'] = 'itemscope';
				$attributes['itemtype']  = 'http://schema.org/Person';
				$attributes['rel']       = 'external nofollow';
				break;
			
			case 'comment_time':
				$attributes['itemprop']  = 'commentTime';
				$attributes['itemscope'] = 'itemscope';
				$attributes['datetime']  = get_the_time( 'c' );
				break;
			
			case 'comment_text':
				$attributes['itemprop'] = 'commentText';
				break;
			
			case 'sidebar':
				$attributes['role']      = 'complementary';
				$attributes['itemscope'] = 'itemscope';
				$attributes['itemtype']  = 'http://schema.org/WPSideBar';
				break;
			
			case 'search_form':
				$attributes['itemprop']  = 'potentialAction';
				$attributes['itemscope'] = 'itemscope';
				$attributes['itemtype']  = 'http://schema.org/SearchAction';
				break;
			
			case 'footer':
				$attributes['itemscope'] = 'itemscope';
				$attributes['itemtype']  = 'http://schema.org/WPFooter';
				break;
		}
		
		$attributes = apply_filters( 'nexio_schema_metadata_attributes', $attributes, $args );
		
		// If failed to fetch the attributes - let's stop
		if ( empty( $attributes ) ) {
			return;
		}
		
		// Cycle through attributes, build tag attribute string
		foreach ( $attributes as $key => $value ) {
			$markup .= $key . '="' . $value . '" ';
		}
		
		$markup = apply_filters( 'nexio_schema_metadata_output', $markup, $args );
		
		if ( $args['echo'] ) {
			echo '' . $markup;
		} else {
			return $markup;
		}
	}
}

if ( ! function_exists( 'nexio_rev_slide_options' ) ) {
	function nexio_rev_slide_options() {
		$nexio_rev_slide_options = array( '' => esc_html__( '--- Choose Revolution Slider ---', 'nexio' ) );
		if ( class_exists( 'RevSlider' ) ) {
			global $wpdb;
			if ( shortcode_exists( 'rev_slider' ) ) {
				$rev_sql  = $wpdb->prepare(
					"SELECT *
                FROM {$wpdb->prefix}revslider_sliders
                WHERE %d", 1
				);
				$rev_rows = $wpdb->get_results( $rev_sql );
				if ( count( $rev_rows ) > 0 ) {
					foreach ( $rev_rows as $rev_row ):
						$nexio_rev_slide_options[ $rev_row->alias ] = $rev_row->title;
					endforeach;
				}
			}
		}
		
		return $nexio_rev_slide_options;
	}
}

// Get search form
function nexio_get_search_form( $form ) {
	
	$form = '<form role="search" method="get" id="searchform" class="searchform" action="' . home_url( '/' ) . '" >
    <div class="nexio-searchform"><label class="screen-reader-text" for="s">' . esc_html__( 'Search for:', 'nexio' ) . '</label>
    <input type="text" value="' . get_search_query() . '" placeholder="' . esc_attr__( 'Enter your keywords...', 'nexio' ) . '" name="s" id="s" />
    <button type="submit"><span class="flaticon-magnifying-glass"></span></button>    
    </div>
    </form>';
	
	return $form;
}

add_filter( 'get_search_form', 'nexio_get_search_form', 100 );


/**
 * Instant search data
 */
function nexio_instant_search_data() {
	
	$response = array(
		'array'   => '',
		'message' => '',
		'success' => 'no',
	);
	
	$args  = array(
		'post_type'      => 'product',
		'posts_per_page' => - 1,
	);
	$posts = new WP_Query( $args );
	
	if ( $posts->have_posts() ) { ?>
		<?php while ( $posts->have_posts() ) { ?>
			<?php
			$posts->the_post();
			ob_start(); ?>
            <div <?php post_class( 'product-item-search col-bg-3 col-lg-3 col-md-3 col-sm-4 col-xs-6 col-ts-6 ' ); ?>>
                <div class="product-inner">
                    <div class="post-thumb">
						<?php
						$image   = nexio_resize_image( get_post_thumbnail_id(), null, 320, 387, true, true, false );
						$product = new WC_Product( get_the_ID() );
						?>
                        <a href="<?php the_permalink() ?>">
                            <img width="<?php echo esc_attr( $image['width'] ); ?>"
                                 height="<?php echo esc_attr( $image['height'] ); ?>"
                                 class="attachment-post-thumbnail wp-post-image"
                                 src="<?php echo esc_url( $image['url'] ); ?>"
                                 alt="<?php echo esc_attr( get_the_title() ); ?>"/>
                        </a>
                    </div>
                    <div class="product-info">
						<?php printf( '<div class="rating"> %s </div>', wc_get_rating_html( $product->get_average_rating() ) ); ?>
                        <h3 class="product-title product-name"><a
                                    href="<?php echo esc_url( get_permalink() ); ?>"><?php the_title(); ?></a></h3>
						<?php
						printf( '<div class="price">' . esc_html__( 'Price', 'nexio' ) . ' : %s </div>', $product->get_price_html() );
						$term_list = wp_get_post_terms( $product->get_id(), 'product_cat' );
						$arg_term  = array();
						if ( is_wp_error( $term_list ) ) {
							return $term_list;
						}
						foreach ( $term_list as $term ) {
							$arg_term[] = $term->slug;
						}
						$arg_term = implode( ',', $arg_term );
						?>
                    </div>
                </div>
            </div>
			<?php
			
			$post_html   = ob_get_clean();
			$cat_slugs   = $arg_term;
			$post_data[] = array(
				'post_title' => esc_html( get_the_title() ),
				'post_link'  => esc_url( get_permalink() ),
				'thumb'      => $image,
				'post_html'  => $post_html,
				'cat_slugs'  => $cat_slugs,
			);
			?>
		<?php } ?>
	<?php }
	wp_reset_postdata();
	
	$response['array']   = $post_data;
	$response['success'] = 'yes';
	wp_send_json( $response );
	
	die();
}

add_action( 'wp_ajax_nexio_instant_search_data', 'nexio_instant_search_data' );
add_action( 'wp_ajax_nopriv_nexio_instant_search_data', 'nexio_instant_search_data' );

if ( ! function_exists( 'nexio_change_buy_together_thumb_width' ) ) {
	function nexio_change_buy_together_thumb_width( $thumb_w ) {
		$thumb_w = 180;
		
		return $thumb_w;
	}
	
	add_filter( 'famibt_thumb_w', 'nexio_change_buy_together_thumb_width', 10, 1 );
}

if ( ! function_exists( 'nexio_change_buy_together_thumb_height' ) ) {
	function nexio_change_buy_together_thumb_height( $thumb_h ) {
		$thumb_h = 220;
		
		return $thumb_h;
	}
	
	add_filter( 'famibt_thumb_h', 'nexio_change_buy_together_thumb_height', 10, 1 );
}

if ( ! function_exists( 'nexio_is_mobile' ) ) {
	function nexio_is_mobile() {
		$is_mobile = false;
		if ( function_exists( 'nexio_toolkit_is_mobile' ) ) {
			$is_mobile = nexio_toolkit_is_mobile();
		}
		
		$force_mobile = isset( $_REQUEST['force_mobile'] ) ? $_REQUEST['force_mobile'] == 'yes' || $_REQUEST['force_mobile'] == 'true' : false;
		if ( $force_mobile ) {
			$is_mobile = true;
		}
		
		$is_mobile = apply_filters( 'nexio_is_mobile', $is_mobile );
		
		return $is_mobile;
	}
}