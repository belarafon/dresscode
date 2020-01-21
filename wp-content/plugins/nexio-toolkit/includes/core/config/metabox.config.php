<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.

$data_meta = new Nexio_ThemeOption();
// ===============================================================================================
// -----------------------------------------------------------------------------------------------
// META BOX OPTIONS
// -----------------------------------------------------------------------------------------------
// ===============================================================================================
$options = array();
// -----------------------------------------
// Page Meta box Options                   -
// -----------------------------------------
$options[] = array(
	'id'        => '_custom_metabox_theme_options',
	'title'     => esc_html__( 'Custom Options', 'nexio-toolkit' ),
	'post_type' => 'page',
	'context'   => 'normal',
	'priority'  => 'high',
	'sections'  => array(
		array(
			'name'   => 'header_footer_theme_options', // !??
			'title'  => esc_html__( 'Header Settings', 'nexio-toolkit' ),
			'icon'   => 'fa fa-cube',
			'fields' => array(
				array(
					'type'    => 'subheading',
					'content' => esc_html__( 'Header Settings', 'nexio-toolkit' ),
				),
				array(
					'id'      => 'enable_custom_header',
					'type'    => 'switcher',
					'title'   => esc_html__( 'Enable Custom Header', 'nexio-toolkit' ),
					'default' => false,
					'desc'    => esc_html__( 'The default is off. If you want to use separate custom page header, turn it on.', 'nexio-toolkit' ),
				),
				array(
					'id'         => 'enable_sticky_menu',
					'type'       => 'select',
					'title'      => esc_html__( 'Sticky Header', 'nexio-toolkit' ),
					'options'    => array(
						'none'  => esc_html__( 'Disable', 'nexio-toolkit' ),
						'smart' => esc_html__( 'Sticky Header', 'nexio-toolkit' ),
					),
					'default'    => 'none',
					'dependency' => array( 'enable_custom_header', '==', true ),
				),
				array(
					'id'         => 'enable_topbar',
					'type'       => 'switcher',
					'title'      => esc_html__( 'Enable topbar', 'nexio-toolkit' ),
					'default'    => false,
					'dependency' => array( 'enable_custom_header', '==', true ),
				),
				array(
					'id'         => 'topbar-text',
					'type'       => 'text',
					'title'      => esc_html__( 'Text Topbar', 'nexio-toolkit' ),
					'dependency' => array( 'enable_custom_header|enable_topbar', '==', 'true|true' ),
				),
				array(
					'id'         => 'metabox_nexio_logo',
					'type'       => 'image',
					'title'      => esc_html__( 'Custom Logo', 'nexio-toolkit' ),
					'dependency' => array( 'enable_custom_header', '==', true ),
				),
				array(
					'id'         => 'nexio_metabox_used_header',
					'type'       => 'select_preview',
					'title'      => esc_html__( 'Header Layout', 'nexio-toolkit' ),
					'desc'       => esc_html__( 'Select a header layout', 'nexio-toolkit' ),
					'options'    => $data_meta->header_options,
					'default'    => 'style-01',
					'dependency' => array( 'enable_custom_header', '==', true ),
				),
				array(
					'id'         => 'header_position',
					'type'       => 'select',
					'title'      => esc_html__( 'Header Type', 'nexio-toolkit' ),
					'options'    => array(
						'relative' => esc_html__( 'Header No Transparent', 'nexio-toolkit' ),
						'absolute' => esc_html__( 'Header Transparent', 'nexio-toolkit' ),
					),
					'default'    => 'relative',
					'dependency' => array( 'enable_custom_header|nexio_metabox_used_header', '==|!=', 'true|sidebar' ),
				),
                array(
                    'id'         => 'header_color',
                    'type'       => 'select',
                    'title'      => esc_html__( 'Header Color', 'nexio-toolkit' ),
                    'options'    => array(
                        'dark' => esc_html__( 'Header Text Dark', 'nexio-toolkit' ),
                        'light' => esc_html__( 'Header Text Light', 'nexio-toolkit' ),
                    ),
                    'default'    => 'dark',
                    'dependency' => array( 'enable_custom_header|nexio_metabox_used_header', '==|!=', 'true|sidebar' ),
                ),
                array(
                    'id'         => 'header_bg',
                    'type'       => 'image',
                    'title'      => esc_html__( 'Header Background', 'nexio-toolkit' ),
                    'default'    => '',
                    'dependency' => array( 'enable_custom_header|nexio_metabox_used_header', '==|!=', 'true|sidebar' ),
                ),
			)
		),
		array(
			'name'   => 'page_banner_settings',
			'title'  => esc_html__( 'Page Banner Settings', 'nexio-toolkit' ),
			'icon'   => 'fa fa-cube',
			'fields' => array(
				array(
					'id'      => 'enable_custom_banner',
					'type'    => 'switcher',
					'title'   => esc_html__( 'Enable Page Custom Banner', 'nexio-toolkit' ),
					'default' => false,
					'desc'    => esc_html__( 'The default is off. If you want to use separate custom page banner, turn it on.', 'nexio-toolkit' ),
				),
				array(
					'id'         => 'hero_section_type',
					'type'       => 'select',
					'title'      => esc_html__( 'Banner Type', 'nexio-toolkit' ),
					'options'    => array(
						'disable'        => esc_html__( 'Disable', 'nexio-toolkit' ),
						'has_background' => esc_html__( 'Has Background', 'nexio-toolkit' ),
						'no_background'  => esc_html__( 'No Background ', 'nexio-toolkit' ),
						'rev_background' => esc_html__( 'Revolution', 'nexio-toolkit' ),
					),
					'default'    => 'no_background',
					'dependency' => array( 'enable_custom_banner', '==', true ),
				),
				array(
					'id'         => 'bg_banner_page',
					'type'       => 'image',
					'title'      => esc_html__( 'Background Banner', 'nexio-toolkit' ),
					'dependency' => array( 'enable_custom_banner|hero_section_type', '==|==', 'true|has_background' ),
				),
				array(
					'id'         => 'nexio_metabox_header_rev_slide',
					'type'       => 'select',
					'options'    => nexio_rev_slide_options(),
					'title'      => esc_html__( 'Revolution', 'nexio-toolkit' ),
					'dependency' => array( 'enable_custom_banner|hero_section_type', '==|==', 'true|rev_background' ),
				),
				array(
					'id'         => 'page_banner_full_width',
					'type'       => 'switcher',
					'title'      => esc_html__( 'Banner Background Full Width', 'nexio-toolkit' ),
					'default'    => 1,
					'dependency' => array( 'enable_custom_banner|hero_section_type', '==|==', 'true|has_background' ),
				),
				array(
					'id'         => 'page_banner_breadcrumb',
					'type'       => 'switcher',
					'title'      => esc_html__( 'Enable Breadcrumb', 'nexio-toolkit' ),
					'default'    => 0,
					'dependency' => array(
						'enable_custom_banner|hero_section_type',
						'==|any',
						'true|no_background,has_background'
					),
					'desc'       => esc_html__( 'This option has no effect on front page and blog page', 'nexio-toolkit' )
				),
				array(
					'id'         => 'page_height_banner',
					'type'       => 'number',
					'title'      => esc_html__( 'Banner Height', 'nexio-toolkit' ),
					'default'    => 420,
					'dependency' => array(
						'enable_custom_banner|hero_section_type',
						'==|any',
						'true|no_background,has_background'
					),
				),
				array(
					'id'         => 'show_hero_section_on_header_mobile',
					'type'       => 'switcher',
					'title'      => esc_html__( 'Show Header Banner On Mobile', 'nexio-toolkit' ),
					'default'    => false,
					'desc'       => esc_html__( 'If enabled, the "Header Banner" is still displayed on the mobile. This option only works when the mobile header is enabled in Theme Options', 'nexio-toolkit' ),
					'dependency' => array( 'enable_custom_banner', '==', 'true' ),
				),
			),
		),
        array(
            'name'   => 'boxed_body_settings',
            'title'  => esc_html__( 'Boxed Body Settings', 'nexio-toolkit' ),
            'icon'   => 'fa fa-cube',
            'fields' => array(
                array(
                    'id'      => 'enable_boxed_body',
                    'type'    => 'switcher',
                    'title'   => esc_html__( 'Enable Boxed Body', 'nexio-toolkit' ),
                    'default' => false,
                    'desc'    => esc_html__( 'The default is off. If you want to use separate boxed body, turn it on.', 'nexio-toolkit' ),
                ),
                array(
                    'id'         => 'bg_body',
                    'type'       => 'image',
                    'title'      => esc_html__( 'Background body', 'nexio-toolkit' ),
                    'default'    => '',
                    'dependency' => array( 'enable_boxed_body', '==', 'true' ),
                ),
            ),
        ),
		array(
			'name'   => 'footer_settings',
			'title'  => esc_html__( 'Footer Settings', 'nexio-toolkit' ),
			'icon'   => 'fa fa-cube',
			'fields' => array(
				array(
					'id'      => 'enable_custom_footer',
					'type'    => 'switcher',
					'title'   => esc_html__( 'Enable Custom Footer', 'nexio-toolkit' ),
					'default' => false,
				),
				array(
					'id'      => 'disable_footer',
					'type'    => 'switcher',
					'title'   => esc_html__( 'Disable Footer', 'nexio-toolkit' ),
					'default' => false,
					'dependency' => array( 'enable_custom_footer', '==', true ),
				),
				array(
					'id'         => 'nexio_metabox_footer_options',
					'type'       => 'select',
					'title'      => esc_html__( 'Select Footer Builder', 'nexio-toolkit' ),
					'options'    => 'posts',
					'query_args' => array(
						'post_type'      => 'footer',
						'orderby'        => 'post_date',
						'order'          => 'ASC',
						'posts_per_page' => - 1
					),
					'dependency' => array( 'enable_custom_footer', '==', true ),
				),
			)
		),
	),
);

// -----------------------------------------
// Product Meta box Options
// -----------------------------------------
$global_product_style      = nexio_toolkit_get_option( 'nexio_woo_single_product_layout', 'default' );
$all_product_styles        = array(
	'default'           => esc_html__( 'Default', 'nexio-toolkit' ),
	'vertical_thumnail' => esc_html__( 'Thumbnail Vertical', 'nexio-toolkit' ),
	'sticky_detail'     => esc_html__( 'Sticky Detail', 'nexio-toolkit' ),
	'gallery_detail'    => esc_html__( 'Gallery Detail', 'nexio-toolkit' ),
    'with_background'   => esc_html__( 'With Background', 'nexio-toolkit' ),
    'slider_large'      => esc_html__( 'Slider large', 'nexio-toolkit' ),
	'center_slider'     => esc_html__( 'Center Slider', 'nexio-toolkit' ),
	'unique'            => esc_html__( 'Unique', 'nexio-toolkit' ),
	'modern'            => esc_html__( 'Modern', 'nexio-toolkit' ),
);
$global_product_style_text = isset( $all_product_styles[ $global_product_style ] ) ? $all_product_styles[ $global_product_style ] : $global_product_style;
$options[]                 = array(
	'id'        => '_custom_product_metabox_theme_options',
	'title'     => esc_html__( 'Custom Options', 'nexio-toolkit' ),
	'post_type' => 'product',
	'context'   => 'normal',
	'priority'  => 'high',
	'sections'  => array(
		array(
			'name'   => 'product_options',
			'title'  => esc_html__( 'Product Configure', 'nexio-toolkit' ),
			'icon'   => 'fa fa-cube',
			'fields' => array(
				array(
					'id'         => 'size_guide',
					'type'       => 'switcher',
					'title'      => esc_html__( 'Size guide', 'nexio-toolkit' ),
					'desc'       => esc_html__( 'On or Off Size guide', 'nexio-toolkit' ),
					'default'    => false,
				),
				array(
					'id'         => 'nexio_sizeguide_options',
					'type'       => 'select',
					'title'      => esc_html__( 'Select Size Guide Builder', 'nexio-toolkit' ),
					'options'    => 'posts',
					'dependency' => array( 'size_guide', '==', true ),
					'query_args' => array(
						'post_type'      => 'sizeguide',
						'orderby'        => 'post_date',
						'order'          => 'ASC',
						'posts_per_page' => - 1
					),
				),
				array(
					'id'      => 'product_style',
					'type'    => 'select',
					'title'   => esc_html__( 'Choose Style', 'nexio-toolkit' ),
					'desc'    => esc_html__( 'Choose Product Style', 'nexio-toolkit' ),
					'options' => array(
						'global'            => sprintf( esc_html__( 'Use Theme Options Style: %s', 'nexio-toolkit' ), $global_product_style_text ),
						'default'           => esc_html__( 'Default', 'nexio-toolkit' ),
						'vertical_thumnail' => esc_html__( 'Thumbnail Vertical', 'nexio-toolkit' ),
						'sticky_detail'     => esc_html__( 'Sticky Detail', 'nexio-toolkit' ),
						'gallery_detail'    => esc_html__( 'Gallery Detail', 'nexio-toolkit' ),
						'with_background'   => esc_html__( 'With Background', 'nexio-toolkit' ),
                        'slider_large'      => esc_html__( 'Slider large', 'nexio-toolkit' ),
                        'center_slider'     => esc_html__( 'Center Slider', 'nexio-toolkit' ),
						'unique'            => esc_html__( 'Unique', 'nexio-toolkit' ),
						'modern'            => esc_html__( 'Modern', 'nexio-toolkit' ),
					),
					'default' => 'global',
				),
				array(
					'id'         => 'product_img_bg_color',
					'type'       => 'color_picker',
					'title'      => esc_html__( 'Image Background Color', 'nexio-toolkit' ),
					'default'    => 'rgba(0,0,0,0)',
					'rgba'       => true,
					'dependency' => array(
						'product_style',
						'==',
						'with_background'
					),
					'desc'       => esc_html__( 'For "Big Images" style only. Default: transparent', 'nexio-toolkit' ),
				),
			)
		),
	)
);
$options[]                 = array(
	'id'        => '_offer_boxed_product_metabox_theme_options',
	'title'     => esc_html__( 'Offer Boxed', 'nexio-toolkit' ),
	'post_type' => 'product',
	'context'   => 'normal',
	'priority'  => 'high',
	'sections'  => array(
		array(
			'name'   => 'offer_boxed_product_options',
			'title'  => esc_html__( 'Offer Boxed Configure', 'nexio-toolkit' ),
			'icon'   => 'fa fa-cube',
			'fields' => array(
				array(
					'id'      => 'title_offer_boxed',
					'type'    => 'text',
					'title'   => esc_html__( 'Offer Boxed Title', 'nexio-toolkit' ),
					'default' => '',
				),
				array(
					'id'      => 'list_offer_boxed',
					'type'    => 'wysiwyg',
					'title'   => esc_html__( 'List Boxed Content', 'nexio-toolkit' ),
					'default' => '',
				),
			)
		),
	)
);
// -----------------------------------------
// Page Footer Meta box Options            -
// -----------------------------------------
$options[] = array(
	'id'        => '_custom_footer_options',
	'title'     => esc_html__( 'Custom Footer Options', 'nexio-toolkit' ),
	'post_type' => 'footer',
	'context'   => 'normal',
	'priority'  => 'high',
	'sections'  => array(
		array(
			'name'   => esc_html__( 'FOOTER STYLE', 'nexio-toolkit' ),
			'fields' => array(
				array(
					'id'       => 'nexio_footer_style',
					'type'     => 'select',
					'title'    => esc_html__( 'Footer Style', 'nexio-toolkit' ),
					'subtitle' => esc_html__( 'Select a Footer Style', 'nexio-toolkit' ),
					'options'  => $data_meta->footer_options,
					'default'  => 'default',
				),
			),
		),
	),
);
// -----------------------------------------
// Page Testimonials Meta box Options      -
// -----------------------------------------
if ( class_exists( 'WooCommerce' ) ) {
	$options[] = array(
		'id'        => '_custom_post_woo_options',
		'title'     => esc_html__( 'Post Meta Data', 'nexio-toolkit' ),
		'post_type' => 'post',
		'context'   => 'normal',
		'priority'  => 'high',
		'sections'  => array(
			array(
				'name'   => 'post-format-setting',
				'title'  => esc_html__( 'Post Format Settings', 'nexio-toolkit' ),
				'icon'   => 'fa fa-picture-o',
				'fields' => array(
					array(
						'id'    => 'audio-video-url',
						'type'  => 'text',
						'title' => esc_html__( 'Upload Video or Audio Url', 'nexio-toolkit' ),
						'desc'  => esc_html__( 'Using when you choose post format video or audio.' ),
					),
					array(
						'id'          => 'post-gallery',
						'type'        => 'gallery',
						'title'       => esc_html__( 'Gallery', 'nexio-toolkit' ),
						'desc'        => esc_html__( 'Using when you choose post format gallery.' ),
						'add_title'   => esc_html__( 'Add Images', 'nexio-toolkit' ),
						'edit_title'  => esc_html__( 'Edit Images', 'nexio-toolkit' ),
						'clear_title' => esc_html__( 'Remove Images', 'nexio-toolkit' ),
					),
					array(
						'id'    => 'page_extra_class',
						'type'  => 'text',
						'title' => esc_html__( 'Extra Class', 'nexio-toolkit' ),
					),
				),
			),
		),
	);
	$options[] = array(
		'id'        => '_custom_product_woo_options',
		'title'     => esc_html__( 'Product Options', 'nexio-toolkit' ),
		'post_type' => 'product',
		'context'   => 'side',
		'priority'  => 'high',
		'sections'  => array(
			array(
				'name'   => 'meta_product_option',
				'fields' => array(
					array(
						'id'          => '360gallery',
						'type'        => 'gallery',
						'title'       => esc_html__( 'Gallery 360', 'nexio-toolkit' ),
						'add_title'   => esc_html__( 'Add Images', 'nexio-toolkit' ),
						'edit_title'  => esc_html__( 'Edit Images', 'nexio-toolkit' ),
						'clear_title' => esc_html__( 'Remove Images', 'nexio-toolkit' ),
					),
					array(
						'id'    => 'youtube_url',
						'type'  => 'text',
						'title' => esc_html__( 'Product Video', 'nexio-toolkit' ),
						'desc'  => esc_html__( 'Supported video Youtube, Vimeo .' ),
					),
				),
			),
		
		
		),
	);
}
// -----------------------------------------
// Page Side Meta box Options              -
// -----------------------------------------
$options[] = array(
	'id'        => '_custom_page_side_options',
	'title'     => esc_html__( 'Custom Page Side Options', 'nexio-toolkit' ),
	'post_type' => 'page',
	'context'   => 'side',
	'priority'  => 'default',
	'sections'  => array(
		array(
			'name'   => 'page_option',
			'fields' => array(
				array(
					'id'      => 'sidebar_page_layout',
					'type'    => 'image_select',
					'title'   => esc_html__( 'Single Post Sidebar Position', 'nexio-toolkit' ),
					'desc'    => esc_html__( 'Select sidebar position on Page.', 'nexio-toolkit' ),
					'options' => array(
						'left'  => NEXIO_TOOLKIT_URL . '/includes/core/assets/images/left-sidebar.png',
						'right' => NEXIO_TOOLKIT_URL . '/includes/core/assets/images/right-sidebar.png',
						'full'  => NEXIO_TOOLKIT_URL . '/includes/core/assets/images/default-sidebar.png',
					),
					'default' => 'left',
				),
				array(
					'id'         => 'page_sidebar',
					'type'       => 'select',
					'title'      => esc_html__( 'Page Sidebar', 'nexio-toolkit' ),
					'options'    => $data_meta->sidebars,
					'default'    => 'primary_sidebar',
					'dependency' => array( 'sidebar_page_layout_full', '==', false ),
				),
				array(
					'id'    => 'page_extra_class',
					'type'  => 'text',
					'title' => esc_html__( 'Extra Class', 'nexio-toolkit' ),
				),
			),
		),
	
	),
);
// -----------------------------------------
// Post Side Meta box Options              -
// -----------------------------------------

CSFramework_Metabox::instance( $options );
