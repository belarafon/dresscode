<?php

/*
 * Header banner (Hero section)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( is_single() || is_404() ) {
	return;
}

$single_id            = nexio_get_single_page_id();
$enable_custom_banner = false;
$enable_categories    = nexio_get_option( 'shop_panel', false );
$style_categories     = nexio_get_option( 'style-categories', 'cate-image' );
$list_categories      = nexio_get_option( 'panel-categories', array() );
$queried_obj          = '';
if ( ! is_home() && ! is_date() ) {
	$queried_obj = get_queried_object();
}

$meta_data = get_post_meta( $single_id, '_custom_metabox_theme_options', true );
if ( isset( $meta_data['enable_custom_banner'] ) ) {
	$enable_custom_banner = $meta_data['enable_custom_banner'];
	// Check request hero_section_type for custom banner
	if ( isset( $_GET['hero_section_type'] ) ) {
		$meta_data['hero_section_type'] = $_GET['hero_section_type'];
	}
}
if ( $enable_custom_banner ) {
	$enable_header_mobile                 = nexio_get_option( 'enable_header_mobile', false );
	$show_hero_section                    = true;
	
	// Check hero section on mobile is enabled or disabled
	if ( $enable_header_mobile && nexio_is_mobile() ) {
		$enable_hero_section_mobile = isset( $meta_data['show_hero_section_on_header_mobile'] ) ? $meta_data['show_hero_section_on_header_mobile'] : false;
		if ( ! $enable_hero_section_mobile ) {
			$show_hero_section = false;
		}
	}
	
	if ( ! $show_hero_section ) {
		return;
	}
	
	switch ( $meta_data['hero_section_type'] ) {
		case 'rev_background':
			if ( $meta_data['nexio_metabox_header_rev_slide'] != '' && shortcode_exists( 'rev_slider' ) ) {
				?>
                <div class="slider-rev-wrap">
					<?php
					echo do_shortcode( '[rev_slider alias="' . esc_attr( $meta_data['nexio_metabox_header_rev_slide'] ) . '"][/rev_slider]' );
					?>
                </div>
				<?php
			}
			break;
		case 'has_background':
		case 'no_background' :
			$page_banner_type = $meta_data['hero_section_type'];
			$page_banner_image            = $meta_data['bg_banner_page'];
			$nexio_page_heading_height    = $meta_data['page_height_banner'];
			$nexio_page_banner_breadcrumb = $meta_data['page_banner_breadcrumb'];
			$is_banner_full_width         = $meta_data['page_banner_full_width'];
			$css                          = '';
			if ( $page_banner_type == 'has_background' ) {
				if ( $page_banner_image && $page_banner_image !== '' ) {
					$css .= 'background-image:url(' . wp_get_attachment_image_url( $page_banner_image, 'full' ) . ');';
				} else {
					$css .= 'background: linear-gradient(90deg, #fff3ea, #fff);';
				}
			}
			$css .= 'min-height:' . esc_attr( $nexio_page_heading_height ) . 'px;';
			
			if ( ! $is_banner_full_width ) { ?>
                <div class="container">
                <div class="row">
			<?php } ?>
            <div class="rev_slider banner-page <?php echo esc_attr( $page_banner_type ); ?>"
                 style='<?php echo esc_attr( $css ); ?>'>
                <div class="content-banner">
                    <div class="container">
						<?php if ( ! is_front_page() ) { ?>
                            <h2 class="title-page page-title">
								<?php if ( is_home() ) :
									if ( is_front_page() ):
										echo esc_html__( 'Latest Posts', 'nexio' );
									else:
										single_post_title();
									endif;
                                elseif ( is_page() ):
									single_post_title();
                                elseif ( is_search() ):
									echo printf( esc_html__( 'Search Results for: %s', 'nexio' ), '<span>' . get_search_query() . '</span>' );
								else:
									the_archive_title();
								endif; ?>
                            </h2>
						<?php } ?>
						<?php if ( ! is_front_page() && $nexio_page_banner_breadcrumb ) {
							get_template_part( 'template-parts/part', 'breadcrumb' );
						}; ?>
                    </div>
                </div>
            </div>
			<?php
			if ( ! $is_banner_full_width ) { ?>
                </div>
                </div>
			<?php }
			break;
		case 'disable':
			break;
		default:
			break;
	}
} else {
	$default_page_banner_height = 0;
	if ( is_front_page() && is_home() ) {
		$default_page_banner_height = 0;
	}
	
	$page_banner_type     = nexio_get_option( 'page_banner_type', 'no_background' );
	$page_banner_image    = nexio_get_option( 'page_banner_image' );
	$is_banner_full_width = nexio_get_option( 'page_banner_full_width', true );
	$page_banner_height   = nexio_get_option( 'page_height_banner', $default_page_banner_height );
	
	if ( class_exists( 'WooCommerce' ) ) {
		if ( is_shop() || is_product_category() || is_product_tag() || ( isset( $queried_obj->taxonomy ) && $queried_obj->taxonomy == 'product_brand' ) ) {
			$page_banner_type     = nexio_get_option( 'shop_banner_type', 'no_background' );
			$page_banner_image    = nexio_get_option( 'shop_banner_image' );
			$is_banner_full_width = true;
			$page_banner_height   = nexio_get_option( 'shop_banner_height', 0 );
		}
	}
	
	$css = '';
	if ( $page_banner_type == 'has_background' ) {
		if ( $page_banner_image && $page_banner_image !== '' ) {
			$css .= 'background-image:url(' . wp_get_attachment_image_url( $page_banner_image, 'full' ) . ');';
		} else {
			$css .= 'background-image: linear-gradient(90deg, #fff3ea, #fff);';
		}
	}
	$css .= 'min-height:' . intval( $page_banner_height ) . 'px;';
	if ( ! $is_banner_full_width ) { ?>
        <div class="container">
        <div class="row">
	<?php } ?>
    <div class="banner-page hero-banner-page <?php echo esc_attr( $page_banner_type ); ?>"
         style='<?php echo esc_attr( $css ); ?>'>
        <div class="content-banner">
            <div class="container">
				<?php if ( class_exists( 'WooCommerce' ) ) { ?>
					<?php if ( ( is_shop() || is_product_category() || is_product_tag() || ( isset( $queried_obj->taxonomy ) && $queried_obj->taxonomy == 'product_brand' ) ) && $list_categories && $enable_categories && $style_categories == 'cate-image' ) : ?>
						<?php
						$cur_term_id    = isset( $queried_obj->term_id ) ? $queried_obj->term_id : 0;
						$data_reponsive = array(
							'0'    => array(
								'items'  => 2,
								'margin' => 10,
							),
							'360'  => array(
								'items'  => 2,
								'margin' => 10,
							),
							'768'  => array(
								'items'  => 3,
								'margin' => 20,
							),
							'992'  => array(
								'items'  => 4,
								'margin' => 20,
							),
							'1200' => array(
								'items'  => 4,
								'margin' => 30,
							),
							'1500' => array(
								'items'  => 5,
								'margin' => 30,
							),
						);
						
						$data_reponsive = json_encode( $data_reponsive );
						$data_margin    = '30';
						?>
                        <div class="panel-categories <?php echo esc_attr( $style_categories ); ?>">
                            <div class="panel-categories-inner owl-carousel"
                                 data-margin="<?php echo esc_attr( $data_margin ); ?>" data-nav="true"
                                 data-dots="false" data-loop="false"
                                 data-responsive='<?php echo esc_attr( $data_reponsive ); ?>'>
								<?php foreach ( $list_categories as $list_category ) {
									$product_term = get_term( $list_category, 'product_cat' );
									if ( ! is_wp_error( $product_term ) ) {
										$cat_link      = get_term_link( $product_term->term_id, 'product_cat' );
										$term_class    = $cur_term_id == $product_term->term_id ? 'product-cat-link current-product-cat' : 'product-cat-link';
										$cat_thumb_id  = get_term_meta( $product_term->term_id, 'thumbnail_id', true );
										$width         = 138;
										$height        = 138;
										$cat_thumb_url = nexio_resize_image( $cat_thumb_id, null, $width, $height, true, false, false );
										?>
                                        <div class="category-wrap <?php echo esc_attr( $term_class ); ?>"
                                             data-slug="<?php echo esc_attr( $product_term->slug ); ?>">
                                            <div class="category-thumb">
                                                <a href="<?php echo esc_url( $cat_link ); ?>">
                                                    <img src="<?php echo esc_url( $cat_thumb_url['url'] ) ?>"
                                                         alt="<?php esc_attr( $product_term->name ); ?>"/>
                                                </a>
                                            </div>
                                            <h3 class="category-title"><a
                                                        href="<?php echo esc_url( $cat_link ); ?>"><?php echo esc_attr( $product_term->name ); ?></a>
                                            </h3>
                                        </div>
										<?php
									}
								}; ?>
                            </div>
                        </div>
					<?php endif; ?>
				<?php } ?>
				<?php if ( class_exists( 'WooCommerce' ) ) { ?>
					<?php if ( ( ! is_shop() && ! is_product_category() && ! is_product_tag() && ( ! isset( $queried_obj->taxonomy ) || ( isset( $queried_obj->taxonomy ) && $queried_obj->taxonomy != 'product_brand' ) ) ) || ! $list_categories || ! $enable_categories || $style_categories != 'cate-image' ): ?>
                        <h2 class="title-page page-title">
							<?php
							if ( class_exists( 'WooCommerce' ) ) {
								if ( is_woocommerce() ) {
									echo woocommerce_page_title( false );
								} else {
									if ( is_home() ) :
										if ( is_front_page() ):
											echo esc_html__( 'Latest Posts', 'nexio' );
										else:
											single_post_title();
										endif;
                                    elseif ( is_page() ):
										single_post_title();
                                    elseif ( is_search() ):
										echo sprintf( __( 'Search Results for: %s', 'nexio' ), '<span>' . get_search_query() . '</span>' );
									else:
										the_archive_title();
									endif;
								}
							} else {
								if ( is_home() ) :
									if ( is_front_page() ):
										echo esc_html__( 'Latest Posts', 'nexio' );
									else:
										single_post_title();
									endif;
                                elseif ( is_page() ):
									single_post_title();
                                elseif ( is_search() ):
									echo sprintf( __( 'Search Results for: %s', 'nexio' ), '<span>' . get_search_query() . '</span>' );
								else:
									the_archive_title();
								endif;
							} ?>
                        </h2>
						<?php get_template_part( 'template-parts/part', 'breadcrumb' ); ?>
					<?php endif; ?>
				<?php } else { ?>
                    <h2 class="title-page page-title">
						<?php if ( is_home() ) :
							if ( is_front_page() ):
								echo esc_html__( 'Latest Posts', 'nexio' );
							else:
								single_post_title();
							endif;
                        elseif ( is_page() ):
							single_post_title();
                        elseif ( is_search() ):
							echo sprintf( __( 'Search Results for: %s', 'nexio' ), '<span>' . get_search_query() . '</span>' );
						else:
							the_archive_title();
						endif; ?>
                    </h2>
				<?php } ?>
            </div>
        </div>
    </div>
	<?php if ( class_exists( 'WooCommerce' ) ) { ?>
		<?php if ( ( is_shop() || is_product_category() || is_product_tag() || ( isset( $queried_obj->taxonomy ) && $queried_obj->taxonomy == 'product_brand' ) ) && $list_categories && $enable_categories ): ?>
			<?php if ( $style_categories == 'cate-icon' ): ?>
				<?php
				$cur_term_id    = isset( $queried_obj->term_id ) ? $queried_obj->term_id : 0;
				$data_reponsive = array(
					'0'    => array(
						'items'  => 1,
						'margin' => 0,
					),
					'360'  => array(
						'items'  => 1,
						'margin' => 0,
					),
					'768'  => array(
						'items'  => 2,
						'margin' => 0,
					),
					'992'  => array(
						'items'  => 3,
						'margin' => 0,
					),
					'1200' => array(
						'items'  => 4,
						'margin' => 0,
					),
					'1500' => array(
						'items'  => 4,
						'margin' => 0,
					),
				);
				
				$data_reponsive = json_encode( $data_reponsive );
				$data_margin    = '0';
				?>
                <div class="panel-categories <?php echo esc_attr( $style_categories ); ?>">
                    <div class="panel-categories-inner owl-carousel"
                         data-margin="<?php echo esc_attr( $data_margin ); ?>" data-nav="true"
                         data-dots="false" data-loop="false"
                         data-responsive='<?php echo esc_attr( $data_reponsive ); ?>'>
						<?php foreach ( $list_categories as $list_category ) {
							$product_term   = get_term( $list_category, 'product_cat' );
							$term_meta      = get_term_meta( $product_term->term_id, 'nexio_category_product' );
							$term_meta_icon = $term_meta[0]['cate_icon'];
							if ( ! is_wp_error( $product_term ) ) {
								$cat_link   = get_term_link( $product_term->term_id, 'product_cat' );
								$term_class = $cur_term_id == $product_term->term_id ? 'product-cat-link current-product-cat' : 'product-cat-link';
								?>
                                <div class="category-wrap <?php echo esc_attr( $term_class ); ?>"
                                     data-slug="<?php echo esc_attr( $product_term->slug ); ?>">
                                    <h3 class="category-title">
                                        <span class="<?php echo esc_attr( $term_meta_icon ); ?>"></span>
                                        <a href="<?php echo esc_url( $cat_link ); ?>"><?php echo esc_attr( $product_term->name ); ?></a>
                                    </h3>
                                </div>
								<?php
							}
						}; ?>
                    </div>
                </div>
			<?php endif; ?>
			<?php if ( $style_categories == 'cate-count' ): ?>
				<?php
				$cur_term_id = isset( $queried_obj->term_id ) ? $queried_obj->term_id : 0;
				?>
                <div class="panel-categories <?php echo esc_attr( $style_categories ); ?>">
                    <div class="panel-categories-inner">
						<?php foreach ( $list_categories as $list_category ) {
							$product_term = get_term( $list_category, 'product_cat' );
							if ( ! is_wp_error( $product_term ) ) {
								$cat_link   = get_term_link( $product_term->term_id, 'product_cat' );
								$term_class = $cur_term_id == $product_term->term_id ? 'product-cat-link current-product-cat' : 'product-cat-link';
								?>
                                <div class="category-wrap <?php echo esc_attr( $term_class ); ?>"
                                     data-slug="<?php echo esc_attr( $product_term->slug ); ?>">
                                    <h3 class="category-title">
                                        <a href="<?php echo esc_url( $cat_link ); ?>"><?php echo esc_attr( $product_term->name ); ?>
                                            <span class="count-pro"><?php echo esc_html( $product_term->count ) ?></span>
                                        </a>
                                    </h3>
                                </div>
								<?php
							}
						}; ?>
                    </div>
                </div>
			<?php endif; ?>
		<?php endif; ?>
		<?php if ( ( is_shop() || is_product_category() || is_product_tag() || ( isset( $queried_obj->taxonomy ) && $queried_obj->taxonomy == 'product_brand' ) ) && $list_categories && $enable_categories && $style_categories == 'cate-image' ): ?>
			<?php if ( ! is_front_page() ) { ?>
                <h2 class="title-page page-title">
					<?php
					if ( class_exists( 'WooCommerce' ) ) {
						if ( is_woocommerce() ) {
							echo woocommerce_page_title( false );
						} else {
							if ( is_home() ) :
								if ( is_front_page() ):
									echo esc_html__( 'Latest Posts', 'nexio' );
								else:
									single_post_title();
								endif;
                            elseif ( is_page() ):
								single_post_title();
                            elseif ( is_search() ):
								echo sprintf( __( 'Search Results for: %s', 'nexio' ), '<span>' . get_search_query() . '</span>' );
							else:
								the_archive_title();
							endif;
						}
					} else {
						if ( is_home() ) :
							if ( is_front_page() ):
								echo esc_html__( 'Latest Posts', 'nexio' );
							else:
								single_post_title();
							endif;
                        elseif ( is_page() ):
							single_post_title();
                        elseif ( is_search() ):
							echo sprintf( __( 'Search Results for: %s', 'nexio' ), '<span>' . get_search_query() . '</span>' );
						else:
							the_archive_title();
						endif;
					} ?>
                </h2>
			<?php } ?>
			<?php get_template_part( 'template-parts/part', 'breadcrumb' ); ?>
		<?php endif; ?>
	<?php } ?>
	<?php
	if ( ! $is_banner_full_width ) { ?>
        </div>
        </div>
	<?php }
}

