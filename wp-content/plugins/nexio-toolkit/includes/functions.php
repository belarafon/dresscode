<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! function_exists( 'nexio_toolkit_resize_image' ) ) {
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
	 * @since 1.0
	 * @return array
	 */
	function nexio_toolkit_resize_image( $attach_id = null, $img_url = null, $width, $height, $crop = false, $place_hold = true, $use_real_img_hold = true, $solid_img_color = null ) {
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

if ( ! function_exists( 'nexio_toolkit_get_option' ) ) {
	function nexio_toolkit_get_option( $option_name = '', $default = '' ) {
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

// Share Single
function nexio_toolkit_product_share() {
	$enable_single_product_sharing = nexio_toolkit_get_option( 'enable_single_product_sharing', false );
	if ( $enable_single_product_sharing ) {
		
		$facecbook_url  = add_query_arg( array( 'u' => rawurlencode( get_permalink() ) ), 'https://www.facebook.com/sharer/sharer.php' );
		$twitter_url    = add_query_arg( array(
			                                 'url'  => rawurlencode( get_permalink() ),
			                                 'text' => rawurlencode( get_the_title() ),
		                                 ), 'https://twitter.com/intent/tweet' );
		$pinterest_url  = add_query_arg( array(
			                                 'url'         => rawurlencode( get_permalink() ),
			                                 'media'       => get_the_post_thumbnail_url(),
			                                 'description' => rawurlencode( get_the_title() ),
		                                 ), 'http://pinterest.com/pin/create/button' );
		$googleplus_url = add_query_arg( array(
			                                 'url'  => rawurlencode( get_permalink() ),
			                                 'text' => rawurlencode( get_the_title() ),
		                                 ), 'https://plus.google.com/share' );
		
		$enable_fb_sharing    = nexio_toolkit_get_option( 'enable_single_product_sharing_fb', false );
		$enable_tw_sharing    = nexio_toolkit_get_option( 'enable_single_product_sharing_tw', false );
		$enable_pin_sharing   = nexio_toolkit_get_option( 'enable_single_product_sharing_pinterest', false );
		$enable_gplus_sharing = nexio_toolkit_get_option( 'enable_single_product_sharing_gplus', false );
		
		if ( $enable_fb_sharing || $enable_tw_sharing || $enable_pin_sharing || $enable_gplus_sharing ) {
			?>
            <div class="social-share-product">
                <div class="share-product-title"><?php echo esc_html__('Share','nexio-toolkit');?></div>
                <div class="nexio-social-product">
					<?php if ( $enable_tw_sharing ) { ?>
                        <a href="<?php echo esc_url( $twitter_url ) ?>" target="_blank" class="twitter-share-link"
                           title="<?php esc_html_e( 'Twitter', 'nexio-toolkit' ) ?>">
                            <i class="fa fa-twitter"></i>
                        </a>
					<?php } ?>
					<?php if ( $enable_fb_sharing ) { ?>
                        <a href="<?php echo esc_url( $facecbook_url ) ?>" target="_blank" class="facebook-share-link"
                           title="<?php esc_html_e( 'Facebook', 'nexio-toolkit' ) ?>">
                            <i class="fa fa-facebook"></i>
                        </a>
					<?php } ?>
					<?php if ( $enable_gplus_sharing ) { ?>
                        <a href="<?php echo esc_url( $googleplus_url ) ?>" target="_blank" class="twitter-share-link"
                           title="<?php esc_html_e( 'Google Plus', 'nexio-toolkit' ) ?>">
                            <i class="fa fa-google-plus"></i>
                        </a>
					<?php } ?>
					<?php if ( $enable_pin_sharing ) { ?>
                        <a href="<?php echo esc_url( $pinterest_url ) ?>" target="_blank" class="pinterest-share-link"
                           title="<?php esc_html_e( 'Pinterest', 'nexio-toolkit' ) ?>">
                            <i class="fa fa-pinterest-p"></i>
                        </a>
					<?php } ?>
                </div>
            </div>
			<?php
		}
	}
	
}

if ( ! function_exists( 'nexio_toolkit_img_output' ) ) {
	/**
	 * @param array  $img
	 * @param string $class
	 * @param string $alt
	 * @param string $title
	 *
	 * @return string
	 */
	function nexio_toolkit_img_output( $img, $class = '', $alt = '', $title = '', $owl_lazy = false ) {
		
		$img_html = '';
		if ( $owl_lazy ) {
			$img_html = '<img class="fami-img fami-owl-lazy ' . esc_attr( $class ) . '" width="' . esc_attr( $img['width'] ) . '" height="' . esc_attr( $img['height'] ) . '" data-src="' . esc_url( $img['url'] ) . '" src="https://via.placeholder.com/1x1" alt="' . esc_attr( $alt ) . '" title="' . esc_attr( $title ) . '" />';
			
			return $img_html;
		}
		if ( function_exists( 'nexio_img_output' ) ) {
			$img_html = nexio_img_output( $img, $class = '', $alt = '', $title = '' );
		} else {
			$img_html = '<img class="fami-img ' . esc_attr( $class ) . '" width="' . esc_attr( $img['width'] ) . '" height="' . esc_attr( $img['height'] ) . '" src="' . esc_url( $img['url'] ) . '" alt="' . esc_attr( $alt ) . '" title="' . esc_attr( $title ) . '" />';
		}
		
		return $img_html;
	}
}

/* User extra fields */
function nexio_toolkit_user_extra_fields_config() {
	$user_extra_fields = array(
		array(
			'name'   => 'user_socials_networks',
			'title'  => esc_html__( 'Personal Socials Networks', 'nexio-toolkit' ),
			'fields' => array(
				array(
					'id'    => 'nexio_user_facebook',
					'title' => esc_html__( 'Facebook', 'nexio-toolkit' ),
					'desc'  => esc_html__( 'User Facebook URL', 'nexio-toolkit' )
				),
				array(
					'id'    => 'nexio_user_twitter',
					'title' => esc_html__( 'Twitter', 'nexio-toolkit' ),
					'desc'  => esc_html__( 'User Twitter URL', 'nexio-toolkit' )
				),
				array(
					'id'    => 'nexio_user_gplus',
					'title' => esc_html__( 'Google Plus', 'nexio-toolkit' ),
					'desc'  => esc_html__( 'User Google Plus URL', 'nexio-toolkit' )
				),
				array(
					'id'    => 'nexio_user_youtube',
					'title' => esc_html__( 'Youtube', 'nexio-toolkit' ),
					'desc'  => esc_html__( 'User Youtube URL', 'nexio-toolkit' )
				),
				array(
					'id'    => 'nexio_user_vimeo',
					'title' => esc_html__( 'Vimeo', 'nexio-toolkit' ),
					'desc'  => esc_html__( 'User Vimeo URL', 'nexio-toolkit' )
				),
				array(
					'id'    => 'nexio_user_behance',
					'title' => esc_html__( 'Behance', 'nexio-toolkit' ),
					'desc'  => esc_html__( 'User Behance URL', 'nexio-toolkit' )
				),
				array(
					'id'    => 'nexio_user_pinterest',
					'title' => esc_html__( 'Pinterest', 'nexio-toolkit' ),
					'desc'  => esc_html__( 'User Pinterest URL', 'nexio-toolkit' )
				),
				array(
					'id'    => 'nexio_user_dribbble',
					'title' => esc_html__( 'Dribbble', 'nexio-toolkit' ),
					'desc'  => esc_html__( 'User Dribbble URL', 'nexio-toolkit' )
				)
			)
		)
	);
	
	return apply_filters( 'nexio_toolkit_user_extra_fields', $user_extra_fields );
}

function nexio_toolkit_extra_profile_fields( $user ) {
	$user_extra_fields = nexio_toolkit_user_extra_fields_config();
	
	if ( ! empty( $user_extra_fields ) ) {
		echo '<div class="nexio-user-extra-fields-wrap">';
		foreach ( $user_extra_fields as $user_extra_field ) {
			$group_id    = isset( $user_extra_field['name'] ) ? $user_extra_field['name'] : '';
			$group_title = isset( $user_extra_field['title'] ) ? $user_extra_field['title'] : '';
			?>
            <div <?php echo $group_id != '' ? 'id="' . esc_attr( $group_id ) . '"' : ''; ?>
                    class="nexio-user-extra-fields-group nexio-user-extra-fields-group-<?php echo esc_attr( $group_id ); ?>">
                <h3><?php echo esc_html( $group_title ); ?></h3>
				<?php
				if ( isset( $user_extra_field['fields'] ) ) {
					if ( ! empty( $user_extra_field['fields'] ) ) {
						?>
                        <table class="form-table">
                            <tbody>
							<?php
							foreach ( $user_extra_field['fields'] as $field ) {
								if ( ! isset( $field['id'] ) ) {
									continue;
								}
								?>
                                <tr>
                                    <th>
                                        <label for="twitter"><?php echo isset( $field['title'] ) ? $field['title'] : ''; ?></label>
                                    </th>
                                    <td>
                                        <input type="text" name="<?php echo esc_attr( $field['id'] ); ?>"
                                               id="<?php echo esc_attr( $field['id'] ); ?>"
                                               value="<?php echo esc_attr( get_the_author_meta( esc_attr( $field['id'] ), $user->ID ) ); ?>"
                                               class="regular-text"/><br/>
                                        <span class="description"><?php echo isset( $field['desc'] ) ? $field['desc'] : ''; ?></span>
                                    </td>
                                </tr>
								<?php
							}
							?>
                            </tbody>
                        </table>
						<?php
					}
				}
				?>
            </div>
			<?php
		}
		echo '</div>';
	}
}

add_action( 'show_user_profile', 'nexio_toolkit_extra_profile_fields' );
add_action( 'edit_user_profile', 'nexio_toolkit_extra_profile_fields' );

function nexio_toolkit_save_extra_profile_fields( $user_id ) {
	
	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return false;
	}
	
	$user_extra_fields = nexio_toolkit_user_extra_fields_config();
	if ( ! empty( $user_extra_fields ) ) {
		foreach ( $user_extra_fields as $user_extra_field ) {
			if ( isset( $user_extra_field['fields'] ) ) {
				if ( ! empty( $user_extra_field['fields'] ) ) {
					foreach ( $user_extra_field['fields'] as $field ) {
						if ( isset( $field['id'] ) ) {
							if ( isset( $_POST[ $field['id'] ] ) ) {
								update_user_meta( $user_id, $field['id'], $_POST[ $field['id'] ] );
							}
						}
					}
				}
			}
		}
	}
}

add_action( 'personal_options_update', 'nexio_toolkit_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'nexio_toolkit_save_extra_profile_fields' );

if ( ! function_exists( 'nexio_toolkit_add_svg_type_upload' ) ) {
	function nexio_toolkit_add_svg_type_upload( $file_types ) {
		$new_filetypes        = array();
		$new_filetypes['svg'] = 'image/svg+xml';
		$file_types           = array_merge( $file_types, $new_filetypes );
		
		return $file_types;
	}
	
	add_action( 'upload_mimes', 'nexio_toolkit_add_svg_type_upload' );
}

if ( ! function_exists( 'nexio_toolkit_is_mobile' ) ) {
	function nexio_toolkit_is_mobile() {
		if ( function_exists( 'wp_is_mobile' ) ) {
			return wp_is_mobile();
		}
		if ( empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
			$is_mobile = false;
		} elseif ( strpos( $_SERVER['HTTP_USER_AGENT'], 'Mobile' ) !== false // many mobile devices (all iPhone, iPad, etc.)
		           || strpos( $_SERVER['HTTP_USER_AGENT'], 'Android' ) !== false
		           || strpos( $_SERVER['HTTP_USER_AGENT'], 'Silk/' ) !== false
		           || strpos( $_SERVER['HTTP_USER_AGENT'], 'Kindle' ) !== false
		           || strpos( $_SERVER['HTTP_USER_AGENT'], 'BlackBerry' ) !== false
		           || strpos( $_SERVER['HTTP_USER_AGENT'], 'Opera Mini' ) !== false
		           || strpos( $_SERVER['HTTP_USER_AGENT'], 'Opera Mobi' ) !== false ) {
			$is_mobile = true;
		} else {
			$is_mobile = false;
		}
		
		return apply_filters( 'wp_is_mobile', $is_mobile );
	}
}