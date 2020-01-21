<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.
if ( ! class_exists( 'Nexio_ThemeOption' ) ) {
	class Nexio_ThemeOption {
		public $sidebars        = array();
		public $header_options  = array();
		public $product_options = array();
		
		public function __construct() {
			$this->get_sidebars();
			$this->get_footer_options();
			$this->get_header_options();
			$this->nexio_rev_slide_options_for_redux();
			$this->get_product_options();
			$this->init_settings();
			add_action( 'admin_bar_menu', array( $this, 'nexio_custom_menu' ), 1000 );
		}
		
		public function get_header_options() {
			$layoutDir      = get_template_directory() . '/templates/headers/';
			$header_options = array();
			
			if ( is_dir( $layoutDir ) ) {
				$files = scandir( $layoutDir );
				if ( $files && is_array( $files ) ) {
					$option = '';
					foreach ( $files as $file ) {
						if ( $file != '.' && $file != '..' ) {
							$fileInfo = pathinfo( $file );
							if ( $fileInfo['extension'] == 'php' && $fileInfo['basename'] != 'index.php' ) {
								$file_data                    = get_file_data( $layoutDir . $file, array( 'Name' => 'Name' ) );
								$file_name                    = str_replace( 'header-', '', $fileInfo['filename'] );
								$header_options[ $file_name ] = array(
									'title'   => $file_data['Name'],
									'preview' => get_template_directory_uri() . '/templates/headers/header-' . $file_name . '.jpg',
								);
							}
						}
					}
				}
			}
			$this->header_options = $header_options;
		}
		
		public function get_social_options() {
			$socials     = array();
			$all_socials = cs_get_option( 'user_all_social' );
			if ( $all_socials ) {
				foreach ( $all_socials as $key => $social ) {
					$socials[ $key ] = $social['title_social'];
				}
			}
			
			return $socials;
		}
		
		public function get_footer_options() {
			$footer_options = array(
				'default' => esc_html__( 'Default', 'nexio-toolkit' ),
			);
			$layoutDir      = get_template_directory() . '/templates/footers/';
			if ( is_dir( $layoutDir ) ) {
				$files = scandir( $layoutDir );
				if ( $files && is_array( $files ) ) {
					$option = '';
					foreach ( $files as $file ) {
						if ( $file != '.' && $file != '..' ) {
							$fileInfo = pathinfo( $file );
							if ( $fileInfo['extension'] == 'php' && $fileInfo['basename'] != 'index.php' ) {
								$file_data                    = get_file_data( $layoutDir . $file, array( 'Name' => 'Name' ) );
								$file_name                    = str_replace( 'footer-', '', $fileInfo['filename'] );
								$footer_options[ $file_name ] = $file_data['Name'];
							}
						}
					}
				}
			}
			$this->footer_options = $footer_options;
		}
		
		/* GET REVOLOTION */
		public function nexio_rev_slide_options_for_redux() {
			$nexio_herosection_revolutions = array( '' => esc_html__( '--- Choose Revolution Slider ---', 'nexio-toolkit' ) );
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
							$nexio_herosection_revolutions[ $rev_row->alias ] = $rev_row->title;
						endforeach;
					}
				}
			}
			
			$this->herosection_options = $nexio_herosection_revolutions;
		}
		
		public function get_product_options() {
			$layoutDir       = get_template_directory() . '/woocommerce/product-styles/';
			$product_options = array();
			
			if ( is_dir( $layoutDir ) ) {
				$files = scandir( $layoutDir );
				if ( $files && is_array( $files ) ) {
					$option = '';
					foreach ( $files as $file ) {
						if ( $file != '.' && $file != '..' ) {
							$fileInfo = pathinfo( $file );
							if ( $fileInfo['extension'] == 'php' && $fileInfo['basename'] != 'index.php' ) {
								$file_data                     = get_file_data( $layoutDir . $file, array( 'Name' => 'Name' ) );
								$file_name                     = str_replace( 'content-product-style-', '', $fileInfo['filename'] );
								$product_options[ $file_name ] = array(
									'title'   => $file_data['Name'],
									'preview' => get_template_directory_uri() . '/woocommerce/product-styles/content-product-style-' . $file_name . '.jpg',
								);
							}
						}
					}
				}
			}
			$this->product_options = $product_options;
		}
		
		public function nexio_attributes_options() {
			$attributes     = array();
			$attributes_tax = array();
			if ( function_exists( 'wc_get_attribute_taxonomies' ) ) {
				$attributes_tax = wc_get_attribute_taxonomies();
			}
			if ( is_array( $attributes_tax ) && count( $attributes_tax ) > 0 ) {
				foreach ( $attributes_tax as $attribute ) {
					$attribute_name                = 'pa_' . $attribute->attribute_name;
					$attributes[ $attribute_name ] = $attribute->attribute_label;
				}
			}
			
			return $attributes;
		}
		
		public function get_sidebars() {
			global $wp_registered_sidebars;
			foreach ( $wp_registered_sidebars as $sidebar ) {
				$sidebars[ $sidebar['id'] ] = $sidebar['name'];
			}
			$this->sidebars = $sidebars;
		}
		
		public function nexio_custom_menu() {
			global $wp_admin_bar;
			if ( ! is_super_admin() || ! is_admin_bar_showing() ) {
				return;
			}
			// Add Parent Menu
			$argsParent = array(
				'id'    => 'theme_option',
				'title' => esc_html__( 'Theme Options', 'nexio-toolkit' ),
				'href'  => admin_url( 'admin.php?page=nexio-toolkit' ),
			);
			$wp_admin_bar->add_menu( $argsParent );
		}
		
		public function init_settings() {
			// ===============================================================================================
			// -----------------------------------------------------------------------------------------------
			// FRAMEWORK SETTINGS
			// -----------------------------------------------------------------------------------------------
			// ===============================================================================================
			$settings = array(
				'menu_title'      => 'Theme Options',
				'menu_type'       => 'submenu', // menu, submenu, options, theme, etc.
				'menu_slug'       => 'nexio-toolkit',
				'ajax_save'       => true,
				'menu_parent'     => 'nexio_menu',
				'show_reset_all'  => true,
				'menu_position'   => 2,
				'framework_title' => '<a href="http://nexio.famithemes.com/" target="_blank"><img src="' . esc_url( NEXIO_TOOLKIT_URL . 'assets/images/logo-backend.png' ) . '" alt=""></a> <small>by <a href="https://famithemes.com" target="_blank">FamiThemes</a></small>',
			);
			
			// ===============================================================================================
			// -----------------------------------------------------------------------------------------------
			// FRAMEWORK OPTIONS
			// -----------------------------------------------------------------------------------------------
			// ===============================================================================================
			$options = array();
			
			// ----------------------------------------
			// a option section for options overview  -
			// ----------------------------------------
			$options[] = array(
				'name'     => 'general',
				'title'    => esc_html__( 'General', 'nexio-toolkit' ),
				'icon'     => 'fa fa-wordpress',
				'sections' => array(
					array(
						'name'   => 'main_settings',
						'title'  => esc_html__( 'Main Settings', 'nexio-toolkit' ),
						'fields' => array(
							array(
								'id'        => 'nexio_logo',
								'type'      => 'image',
								'title'     => esc_html__( 'Logo', 'nexio-toolkit' ),
								'add_title' => esc_html__( 'Add Logo', 'nexio-toolkit' ),
								'desc'      => esc_html__( 'Add custom logo for your website.', 'nexio-toolkit' ),
							),
							array(
								'id'      => 'width_logo',
								'type'    => 'number',
								'default' => '70',
								'title'   => esc_html__( 'Width Logo', 'nexio-toolkit' ),
								'desc'    => esc_html__( 'Unit PX', 'nexio-toolkit' )
							),
							array(
								'id'      => 'nexio_main_color',
								'type'    => 'color_picker',
								'title'   => esc_html__( 'Main Color', 'nexio-toolkit' ),
								'default' => '#ff4040',
								'rgba'    => true,
							),
							array(
								'id'      => 'nexio_body_text_color',
								'type'    => 'color_picker',
								'title'   => esc_html__( 'Body Text Color', 'nexio-toolkit' ),
								'default' => '#868686',
								'rgba'    => true,
							),
							array(
								'id'    => 'gmap_api_key',
								'type'  => 'text',
								'title' => esc_html__( 'Google Map API Key', 'nexio-toolkit' ),
								'desc'  => wp_kses( sprintf( __( 'Enter your Google Map API key. <a href="%s" target="_blank">How to get?</a>', 'nexio-toolkit' ), 'https://developers.google.com/maps/documentation/javascript/get-api-key' ), array(
									'a' => array(
										'href'   => array(),
										'target' => array()
									)
								) ),
							),
							array(
								'id'         => 'load_gmap_js_target',
								'type'       => 'select',
								'title'      => esc_html__( 'Load GMap JS On', 'nexio-toolkit' ),
								'options'    => array(
									'all_pages'      => esc_html__( 'All Pages', 'nexio-toolkit' ),
									'selected_pages' => esc_html__( 'Selected Pages', 'nexio-toolkit' ),
									'disabled'       => esc_html__( 'Don\'t Load Gmap JS', 'nexio-toolkit' ),
								),
								'default'    => 'all_pages',
								'dependency' => array( 'gmap_api_key', '!=', '' ),
							),
							array(
								'id'         => 'load_gmap_js_on',
								'type'       => 'select',
								'title'      => esc_html__( 'Select Pages To Load GMap JS', 'nexio-toolkit' ),
								'options'    => 'pages',
								'query_args' => array(
									'post_type'      => 'page',
									'orderby'        => 'post_date',
									'order'          => 'ASC',
									'posts_per_page' => - 1
								),
								'attributes' => array(
									'multiple' => 'multiple',
									'style'    => 'width: 500px; height: 125px;',
								),
								'class'      => 'chosen',
								'desc'       => esc_html__( 'Load Google Map JS on selected pages', 'nexio-toolkit' ),
								'dependency' => array(
									'gmap_api_key|load_gmap_js_target',
									'!=|==',
									'|selected_pages'
								),
							),
							array(
								'id'      => 'nexio_enable_lazy',
								'type'    => 'switcher',
								'title'   => esc_html__( 'Lazy Load Images', 'nexio-toolkit' ),
								'default' => true,
								'desc'    => esc_html__( 'Enables lazy load to reduce page requests.', 'nexio-toolkit' ),
							),
							array(
								'id'      => 'animation_on_scroll',
								'type'    => 'switcher',
								'title'   => esc_html__( 'Animation On Scroll', 'nexio-toolkit' ),
								'default' => false,
								'desc'    => esc_html__( 'If enabled, will active the animation of elements when scrolling. You also need to select the animation when scrolling the mouse for each element when editing the article', 'nexio-toolkit' ),
							),
							array(
								'id'      => 'enable_preloader',
								'type'    => 'switcher',
								'title'   => esc_html__( 'Preloader', 'nexio-toolkit' ),
								'default' => false,
								'desc'    => esc_html__( 'Turn on if you want to show preloader', 'nexio-toolkit' ),
							),
							array(
								'id'         => 'preloader_style',
								'type'       => 'select',
								'title'      => esc_html__( 'Preloader Style', 'nexio-toolkit' ),
								'options'    => array(
									'default'        => esc_html__( 'Default', 'nexio-toolkit' ),
									'block_rotate'   => esc_html__( 'Block Rotate', 'nexio-toolkit' ),
									'segment_blocks' => esc_html__( 'Segment Blocks', 'nexio-toolkit' ),
									'text_fill'      => esc_html__( 'Text Fill', 'nexio-toolkit' ),
								),
								'default'    => 'default',
								'dependency' => array( 'enable_preloader', '==', true ),
							),
							array(
								'id'         => 'preloader_text',
								'type'       => 'text',
								'default'    => esc_html__( 'Nexio', 'nexio-toolkit' ),
								'title'      => esc_html__( 'Preloader Text', 'nexio-toolkit' ),
								'dependency' => array( 'enable_preloader', '==', true ),
							),
						),
					),
				),
			);
			$options[] = array(
				'name'   => 'newsletter',
				'title'  => esc_html__( 'Newsletter Popup', 'nexio-toolkit' ),
				'icon'   => 'fa fa-envelope-o',
				'fields' => array(
					array(
						'id'      => 'enable_newsletter',
						'type'    => 'switcher',
						'title'   => esc_html__( 'Enable Newsletter', 'nexio-toolkit' ),
						'default' => true,
					),
					array(
						'id'         => 'nexio_newsletter_popup',
						'type'       => 'select',
						'title'      => esc_html__( 'Select Newsletter Popup', 'nexio-toolkit' ),
						'options'    => 'posts',
						'dependency' => array( 'enable_newsletter', '==', true ),
						'query_args' => array(
							'post_type'      => 'newsletter',
							'orderby'        => 'post_date',
							'order'          => 'ASC',
							'posts_per_page' => - 1
						),
					),
					array(
						'id'      => 'newsletter_popup_timeout',
						'type'    => 'number',
						'title'   => esc_html__( 'Timeout', 'nexio-toolkit' ),
						'desc'    => esc_html__( 'Time to wait after page load to display the popup, in milliseconds. Default is 5000 (equivalent to 5 seconds)', 'nexio-toolkit' ),
						'default' => 5000
					),
					array(
						'id'         => 'disable_newsletter_popup_on_mobile',
						'type'       => 'switcher',
						'title'      => esc_html__( 'Disable On Mobile', 'nexio-toolkit' ),
						'default'    => true,
						'dependency' => array( 'enable_newsletter', '==', true ),
					),
				
				),
			);
			$options[] = array(
				'name'     => 'header',
				'title'    => esc_html__( 'Header Settings', 'nexio-toolkit' ),
				'icon'     => 'fa fa-folder-open-o',
				'sections' => array(
					array(
						'name'   => 'header_general_settings',
						'title'  => esc_html__( 'General Header Settings', 'nexio-toolkit' ),
						'fields' => array(
							array(
								'id'      => 'enable_sticky_menu',
								'type'    => 'select',
								'title'   => esc_html__( 'Sticky Header', 'nexio-toolkit' ),
								'options' => array(
									'none'  => esc_html__( 'Disable', 'nexio-toolkit' ),
									'smart' => esc_html__( 'Sticky Header', 'nexio-toolkit' ),
								),
								'default' => 'none',
							),
							array(
								'id'      => 'enable_topbar',
								'type'    => 'switcher',
								'title'   => esc_html__( 'Enable Topbar', 'nexio-toolkit' ),
								'default' => false,
							),
							array(
								'id'         => 'topbar-text',
								'type'       => 'text',
								'title'      => esc_html__( 'Text Topbar', 'nexio-toolkit' ),
								'dependency' => array( 'enable_topbar', '==', true ),
							),
							array(
								'id'         => 'enable_header_wishlist',
								'type'       => 'switcher',
								'title'      => esc_html__( 'Enable Header Wishlist Icon', 'nexio-toolkit' ),
								'desc'       => esc_html__( 'Show/Hide wish list icon on menu', 'nexio-toolkit' ),
								'default'    => false,
								'on'         => esc_html__( 'Show', 'nexio-toolkit' ),
								'off'        => esc_html__( 'Hide', 'nexio-toolkit' ),
							),
							array(
								'id'      => 'nexio_used_header',
								'type'    => 'select_preview',
								'title'   => esc_html__( 'Header Layout', 'nexio-toolkit' ),
								'desc'    => esc_html__( 'Select a header layout', 'nexio-toolkit' ),
								'options' => $this->header_options,
								'default' => 'style-01',
							),
							array(
								'id'         => 'nexio_header_social',
								'title'      => esc_html__( 'Header Social', 'nexio-toolkit' ),
								'type'       => 'select',
								'options'    => $this->get_social_options(),
								'attributes' => array(
									'multiple' => 'multiple',
								),
								'class'      => 'chosen',
							),
							array(
								'id'      => 'header_position',
								'type'    => 'select',
								'title'   => esc_html__( 'Header Type', 'nexio-toolkit' ),
								'options' => array(
									'relative' => esc_html__( 'Header No Transparent', 'nexio-toolkit' ),
									'absolute' => esc_html__( 'Header Transparent', 'nexio-toolkit' ),
								),
								'default' => 'relative',
							),
							array(
								'id'      => 'header_color',
								'type'    => 'select',
								'title'   => esc_html__( 'Header Color', 'nexio-toolkit' ),
								'options' => array(
									'dark'  => esc_html__( 'Header Text Dark', 'nexio-toolkit' ),
									'light' => esc_html__( 'Header Text Light', 'nexio-toolkit' ),
								),
								'default' => 'dark',
							),
						),
					),
					array(
						'name'   => 'page_banner_settings',
						'title'  => esc_html__( 'Page Banner Settings', 'nexio-toolkit' ),
						'fields' => array(
							array(
								'id'      => 'page_banner_type',
								'type'    => 'select',
								'title'   => esc_html__( 'Banner Type', 'nexio-toolkit' ),
								'options' => array(
									'has_background' => esc_html__( 'Has Background', 'nexio-toolkit' ),
									'no_background'  => esc_html__( 'No Background ', 'nexio-toolkit' ),
								),
								'default' => 'has_background'
							),
							array(
								'id'         => 'page_banner_image',
								'type'       => 'image',
								'title'      => esc_html__( 'Banner Image', 'nexio-toolkit' ),
								'add_title'  => esc_html__( 'Upload', 'nexio-toolkit' ),
								'dependency' => array( 'page_banner_type', '==', 'has_background' ),
							),
							array(
								'id'         => 'page_banner_full_width',
								'type'       => 'switcher',
								'title'      => esc_html__( 'Banner Full Width', 'nexio-toolkit' ),
								'default'    => true,
								'dependency' => array( 'page_banner_type', '==', 'has_background' ),
							),
							array(
								'id'      => 'page_height_banner',
								'type'    => 'number',
								'title'   => esc_html__( 'Banner Height', 'nexio-toolkit' ),
								'default' => '420'
							),
							array(
								'id'      => 'page_title',
								'type'    => 'select',
								'title'   => esc_html__( 'Page Title', 'nexio-toolkit' ),
								'options' => array(
									'' => esc_html__( 'Show Title', 'nexio-toolkit' ),
									'hidden-title'  => esc_html__( 'Hidden Title ', 'nexio-toolkit' ),
								),
								'default' => ''
							),
							array(
								'id'      => 'page_breadcrumb',
								'type'    => 'select',
								'title'   => esc_html__( 'Page Breadcrumb', 'nexio-toolkit' ),
								'options' => array(
									'' => esc_html__( 'Show Breadcrumb', 'nexio-toolkit' ),
									'hidden-breadcrumb'  => esc_html__( 'Hidden Breadcrumb ', 'nexio-toolkit' ),
								),
								'default' => ''
							),
						)
					),
					array(
						'name'   => 'header_mobile',
						'title'  => esc_html__( 'Header Mobile', 'nexio-toolkit' ),
						'fields' => array(
							array(
								'id'      => 'enable_header_mobile',
								'type'    => 'switcher',
								'title'   => esc_html__( 'Enable Header Mobile', 'nexio-toolkit' ),
								'default' => false,
							),
							array(
								'id'         => 'nexio_logo_mobile',
								'type'       => 'image',
								'title'      => esc_html__( 'Mobile Logo', 'nexio-toolkit' ),
								'add_title'  => esc_html__( 'Add Mobile Logo', 'nexio-toolkit' ),
								'desc'       => esc_html__( 'Add custom logo for mobile. If no mobile logo is selected, the default logo will be used or custom logo if placed in the page', 'nexio-toolkit' ),
								'dependency' => array( 'enable_header_mobile', '==', true )
							),
							array(
								'id'         => 'width_logo_mobile',
								'type'       => 'number',
								'default'    => '70',
								'title'      => esc_html__( 'Width Logo', 'nexio-toolkit' ),
								'desc'       => esc_html__( 'Unit PX', 'nexio-toolkit' ),
								'dependency' => array( 'enable_header_mobile', '==', true )
							),
							array(
								'id'         => 'enable_header_mini_cart_mobile',
								'type'       => 'switcher',
								'title'      => esc_html__( 'Show Mini Cart Icon', 'nexio-toolkit' ),
								'desc'       => esc_html__( 'Show/Hide header mini cart icon on mobile', 'nexio-toolkit' ),
								'default'    => true,
								'on'         => esc_html__( 'On', 'nexio-toolkit' ),
								'off'        => esc_html__( 'Off', 'nexio-toolkit' ),
								'dependency' => array( 'enable_header_mobile', '==', true )
							),
							array(
								'id'         => 'enable_header_product_search_mobile',
								'type'       => 'switcher',
								'title'      => esc_html__( 'Show Products Search Icon', 'nexio-toolkit' ),
								'desc'       => esc_html__( 'Show/Hide header product search icon on mobile', 'nexio-toolkit' ),
								'default'    => true,
								'on'         => esc_html__( 'On', 'nexio-toolkit' ),
								'off'        => esc_html__( 'Off', 'nexio-toolkit' ),
								'dependency' => array( 'enable_header_mobile', '==', true )
							),
							array(
								'id'         => 'enable_wishlist_mobile',
								'type'       => 'switcher',
								'title'      => esc_html__( 'Show Wish List Icon', 'nexio-toolkit' ),
								'desc'       => esc_html__( 'Show/Hide wish list icon on siding menu mobile', 'nexio-toolkit' ),
								'default'    => false,
								'on'         => esc_html__( 'Show', 'nexio-toolkit' ),
								'off'        => esc_html__( 'Hide', 'nexio-toolkit' ),
								'dependency' => array( 'enable_header_mobile', '==', true )
							),
							array(
								'id'         => 'enable_lang_mobile',
								'type'       => 'switcher',
								'title'      => esc_html__( 'Show Languges and Currency', 'nexio-toolkit' ),
								'desc'       => esc_html__( 'Show/Hide Languges and Currency on siding menu mobile', 'nexio-toolkit' ),
								'default'    => false,
								'on'         => esc_html__( 'Show', 'nexio-toolkit' ),
								'off'        => esc_html__( 'Hide', 'nexio-toolkit' ),
								'dependency' => array( 'enable_header_mobile', '==', true )
							),
							array(
								'id'         => 'enable_sticky_mobile',
								'type'       => 'switcher',
								'title'      => esc_html__( 'Show Sticky Menu', 'nexio-toolkit' ),
								'default'    => false,
								'on'         => esc_html__( 'Turn On', 'nexio-toolkit' ),
								'off'        => esc_html__( 'Turn Off', 'nexio-toolkit' ),
								'dependency' => array( 'enable_header_mobile', '==', true )
							),
						),
					),
				)
			);
			$options[] = array(
				'name'   => 'footer',
				'title'  => esc_html__( 'Footer Settings', 'nexio-toolkit' ),
				'icon'   => 'fa fa-folder-open-o',
				'fields' => array(
					array(
						'id'         => 'nexio_footer_options',
						'type'       => 'select',
						'title'      => esc_html__( 'Select Footer Builder', 'nexio-toolkit' ),
						'options'    => 'posts',
						'query_args' => array(
							'post_type'      => 'footer',
							'orderby'        => 'post_date',
							'order'          => 'ASC',
							'posts_per_page' => - 1
						),
					),
				),
			);
			
			$options[] = array(
				'name'     => 'blog',
				'title'    => esc_html__( 'Blog Settings', 'nexio-toolkit' ),
				'icon'     => 'fa fa-rss',
				'sections' => array(
					array(
						'name'   => 'blog_page',
						'title'  => esc_html__( 'Blog Page', 'nexio-toolkit' ),
						'fields' => array(
							array(
								'type'    => 'subheading',
								'content' => esc_html__( 'General Settings', 'nexio-toolkit' ),
							),
							array(
								'id'    => 'blog_logo',
								'type'  => 'image',
								'title' => esc_html__( 'Blog Logo', 'nexio-toolkit' ),
							),
							array(
								'id'      => 'blog_header_color',
								'type'    => 'select',
								'options' => array(
									'dark'  => esc_html__( 'Header Text Dark', 'nexio-toolkit' ),
									'light' => esc_html__( 'Header Text Light', 'nexio-toolkit' ),
								),
								'default' => 'dark',
								'title'   => esc_html__( 'Blog Header Color', 'nexio-toolkit' ),
							),
							array(
								'id'      => 'blog_header_position',
								'type'    => 'select',
								'options' => array(
									'relative' => esc_html__( 'No Transparent', 'nexio-toolkit' ),
									'absolute' => esc_html__( 'Transparent', 'nexio-toolkit' ),
								),
								'default' => 'relative',
								'title'   => esc_html__( 'Blog Header Position', 'nexio-toolkit' ),
							),
							array(
								'id'         => 'blog-style',
								'type'       => 'image_select',
								'title'      => esc_html__( 'Style', 'nexio-toolkit' ),
								'radio'      => true,
								'options'    => array(
									'standard' => CS_URI . '/assets/images/layout/standard.png',
									'classic'  => CS_URI . '/assets/images/layout/classic.png',
									'grid'     => CS_URI . '/assets/images/layout/grid.png',
									'modern'   => CS_URI . '/assets/images/layout/modern.png',
								),
								'default'    => 'standard',
								'attributes' => array(
									'data-depend-id' => 'blog-style',
								),
							),
							array(
								'id'      => 'nexio_blog_layout',
								'type'    => 'image_select',
								'title'   => esc_html__( 'Blog Sidebar Position', 'nexio-toolkit' ),
								'desc'    => esc_html__( 'Select sidebar position on Blog.', 'nexio-toolkit' ),
								'options' => array(
									'left'  => NEXIO_TOOLKIT_URL . '/includes/core/assets/images/left-sidebar.png',
									'right' => NEXIO_TOOLKIT_URL . '/includes/core/assets/images/right-sidebar.png',
									'full'  => NEXIO_TOOLKIT_URL . '/includes/core/assets/images/default-sidebar.png',
								),
								'default' => 'full',
							),
							array(
								'id'         => 'blog_sidebar',
								'type'       => 'select',
								'title'      => esc_html__( 'Blog Sidebar', 'nexio-toolkit' ),
								'options'    => $this->sidebars,
								'default'    => 'primary_sidebar',
								'dependency' => array( 'nexio_blog_layout_full', '==', false ),
							),
							array(
								'id'         => 'enable_except_post',
								'type'       => 'switcher',
								'title'      => esc_html__( 'Enable Except Post', 'nexio' ),
								'dependency' => array( 'blog-style', '==', 'standard' ),
							),
						),
					),
					array(
						'name'   => 'single_post',
						'title'  => 'Single Post',
						'fields' => array(
							array(
								'id'      => 'sidebar_single_post_position',
								'type'    => 'image_select',
								'title'   => 'Single Post Sidebar Position',
								'desc'    => 'Select sidebar position on Single Post.',
								'options' => array(
									'left'  => NEXIO_TOOLKIT_URL . '/includes/core/assets/images/left-sidebar.png',
									'right' => NEXIO_TOOLKIT_URL . '/includes/core/assets/images/right-sidebar.png',
									'full'  => NEXIO_TOOLKIT_URL . '/includes/core/assets/images/default-sidebar.png',
								),
								'default' => 'left',
							),
							array(
								'id'         => 'single_post_sidebar',
								'type'       => 'select',
								'title'      => 'Single Post Sidebar',
								'options'    => $this->sidebars,
								'default'    => 'primary_sidebar',
								'dependency' => array( 'sidebar_single_post_position_full', '==', false ),
							),
							array(
								'id'    => 'enable_share_post',
								'type'  => 'switcher',
								'title' => esc_html__( 'Enable Share Button', 'nexio' ),
							),
						),
					),
				),
			);
			if ( class_exists( 'WooCommerce' ) ) {
				$options[] = array(
					'name'     => 'wooCommerce',
					'title'    => esc_html__( 'WooCommerce', 'nexio-toolkit' ),
					'icon'     => 'fa fa-shopping-cart',
					'sections' => array(
						array(
							'name'   => 'shop_product',
							'title'  => esc_html__( 'General Settings', 'nexio-toolkit' ),
							'fields' => array(
								array(
									'type'    => 'subheading',
									'content' => esc_html__( 'Shop Settings', 'nexio-toolkit' ),
								),
								array(
									'id'    => 'shop_logo',
									'type'  => 'image',
									'title' => esc_html__( 'Shop Logo', 'nexio-toolkit' ),
								),
								array(
									'id'      => 'shop_header_position',
									'type'    => 'select',
									'title'   => esc_html__( 'Shop Header Type', 'nexio-toolkit' ),
									'options' => array(
										'relative' => esc_html__( 'Header No Transparent', 'nexio-toolkit' ),
										'absolute' => esc_html__( 'Header Transparent', 'nexio-toolkit' ),
									),
									'default' => 'relative',
								),
								array(
									'id'      => 'shop_header_color',
									'type'    => 'select',
									'title'   => esc_html__( 'Shop Header Color', 'nexio-toolkit' ),
									'options' => array(
										'dark'  => esc_html__( 'Header Text Dark', 'nexio-toolkit' ),
										'light' => esc_html__( 'Header Text Light', 'nexio-toolkit' ),
									),
									'default' => 'dark',
								),
								array(
									'id'      => 'shop_banner_type',
									'type'    => 'select',
									'title'   => esc_html__( 'Shop Banner Type', 'nexio-toolkit' ),
									'options' => array(
										'has_background' => esc_html__( 'Has Background', 'nexio-toolkit' ),
										'no_background'  => esc_html__( 'No Background ', 'nexio-toolkit' ),
									),
									'default' => 'no_background',
									'desc'    => esc_html__( 'Banner for Shop page, archive, search results page...', 'nexio-toolkit' ),
								),
								array(
									'id'         => 'shop_banner_image',
									'type'       => 'image',
									'title'      => esc_html__( 'Banner Image', 'nexio-toolkit' ),
									'add_title'  => esc_html__( 'Upload', 'nexio-toolkit' ),
									'dependency' => array( 'shop_banner_type', '==', 'has_background' ),
								),
								array(
									'id'      => 'shop_banner_height',
									'type'    => 'number',
									'title'   => esc_html__( 'Banner Height', 'nexio-toolkit' ),
									'default' => 467,
								),
								array(
									'id'      => 'shop_panel',
									'type'    => 'switcher',
									'title'   => esc_html__( 'Shop Top Categories', 'nexio-toolkit' ),
									'default' => false,
								),
								array(
									'id'         => 'style-categories',
									'type'       => 'select',
									'title'      => esc_html__( 'Shop Categories Style', 'nexio-toolkit' ),
									'options'    => array(
										'cate-image' => esc_html__( 'Categories Image', 'nexio-toolkit' ),
										'cate-icon'  => esc_html__( 'Categories Icon', 'nexio-toolkit' ),
										'cate-count' => esc_html__( 'Categories Count', 'nexio-toolkit' ),
									),
									'default'    => 'cate-image',
									'dependency' => array( 'shop_panel', '==', true ),
								),
								array(
									'id'             => 'panel-categories',
									'type'           => 'select',
									'title'          => esc_html__( 'Select Categories', 'nexio-toolkit' ),
									'options'        => 'categories',
									'query_args'     => array(
										'type'           => 'product',
										'taxonomy'       => 'product_cat',
										'orderby'        => 'post_date',
										'order'          => 'DESC',
										'posts_per_page' => - 1
									),
									'attributes'     => array(
										'multiple' => 'multiple',
										'style'    => 'width: 500px; height: 125px;',
									),
									'class'          => 'chosen',
									'default_option' => esc_html__( 'Select Categories', 'nexio-toolkit' ),
									'desc'           => esc_html__( 'Product categories displayed on the shop page', 'nexio-toolkit' ),
									'dependency'     => array( 'shop_panel', '==', true ),
								),
								array(
									'id'      => 'enable_shop_mobile',
									'type'    => 'switcher',
									'title'   => esc_html__( 'Shop Mobile Layout', 'nexio-toolkit' ),
									'default' => true,
									'desc'    => esc_html__( 'Use the dedicated mobile interface on a real device instead of responsive. Note, this option is not available for desktop browsing and uses resize the screen.', 'nexio-toolkit' ),
								),
								array(
									'id'      => 'enable_instant_product_search',
									'type'    => 'switcher',
									'title'   => esc_html__( 'Instant Products Search', 'nexio-toolkit' ),
									'default' => false,
									'desc'    => esc_html__( 'Enabling "Instant Products Search" will display search results instantly as soon as you type', 'nexio-toolkit' ),
								),
								array(
									'id'      => 'sidebar_shop_page_position',
									'type'    => 'image_select',
									'title'   => esc_html__( 'Shop Page Layout', 'nexio-toolkit' ),
									'desc'    => esc_html__( 'Select layout for Shop Page.', 'nexio-toolkit' ),
									'options' => array(
										'left'  => NEXIO_TOOLKIT_URL . '/includes/core/assets/images/left-sidebar.png',
										'right' => NEXIO_TOOLKIT_URL . '/includes/core/assets/images/right-sidebar.png',
										'full'  => NEXIO_TOOLKIT_URL . '/includes/core/assets/images/default-sidebar.png',
									),
									'default' => 'full',
								),
								array(
									'id'         => 'filter_shop_page',
									'type'       => 'select',
									'title'      => esc_html__( 'Sidebar Filter Shop Page', 'nexio-toolkit' ),
									'options'    => array(
										'top_sidebar'       => esc_html__( 'Default', 'nexio-toolkit' ),
										'drawer_sidebar'    => esc_html__( 'Drawer Sidebar Filter', 'nexio-toolkit' ),
										'offcanvas_sidebar' => esc_html__( 'Off Canvas Sidebar Filter', 'nexio-toolkit' ),
									),
									'default'    => 'top_sidebar',
									'dependency' => array(
										'sidebar_shop_page_position_full',
										'==',
										true
									),
								),
								array(
									'id'         => 'shop_page_sidebar',
									'type'       => 'select',
									'title'      => esc_html__( 'Shop Sidebar', 'nexio-toolkit' ),
									'options'    => $this->sidebars,
									'dependency' => array(
										'sidebar_shop_page_position_full',
										'==',
										false
									),
								),
								array(
									'id'      => 'product_per_page',
									'type'    => 'number',
									'title'   => esc_html__( 'Products perpage', 'nexio-toolkit' ),
									'desc'    => 'Number of products on shop page.',
									'default' => '12',
								),
								array(
									'id'      => 'product_newness',
									'default' => '10',
									'type'    => 'number',
									'title'   => esc_html__( 'Products Newness', 'nexio-toolkit' ),
									'desc'    => esc_html__( 'The number of days the product is still considered new', 'nexio-toolkit' ),
								),
								array(
									'id'      => 'nexio_enable_loadmore',
									'type'    => 'select',
									'options' => array(
										'default'  => esc_html__( 'Default', 'nexio-toolkit' ),
										'loadmore' => esc_html__( 'Load More', 'nexio-toolkit' ),
										'infinity' => esc_html__( 'Infinity', 'nexio-toolkit' ),
									
									),
									'title'   => esc_html__( 'Choose Pagination', 'nexio-toolkit' ),
									'desc'    => esc_html__( 'Choose pagination type for shop page.', 'nexio-toolkit' ),
									'default' => 'default',
								),
								array(
									'id'      => 'nexio_shop_product_style',
									'type'    => 'select_preview',
									'title'   => esc_html__( 'Product Shop Layout', 'nexio-toolkit' ),
									'desc'    => esc_html__( 'Select a Product layout in shop page', 'nexio-toolkit' ),
									'options' => $this->product_options,
									'default' => '1',
								),
								array(
									'id'      => 'product_star_rating',
									'type'    => 'select',
									'options' => array(
										''       => esc_html__( 'Turn On', 'nexio-toolkit' ),
										'nostar' => esc_html__( 'Turn Off', 'nexio-toolkit' ),
									),
									'title'   => esc_html__( 'Product Star Rating', 'nexio-toolkit' ),
									'default' => '',
								),
								array(
									'id'      => 'product_label_new_sale',
									'type'    => 'select',
									'options' => array(
										'on'       => esc_html__( 'Turn On', 'nexio-toolkit' ),
										'off' => esc_html__( 'Turn Off', 'nexio-toolkit' ),
									),
									'title'   => esc_html__( 'Product Label New Sale', 'nexio-toolkit' ),
									'default' => '',
								),
								array(
									'id'         => 'products_loop_attributes_display',
									'type'       => 'select',
									'title'      => esc_html__( 'Products Attribute Display On Loop', 'nexio-toolkit' ),
									'options'    => $this->nexio_attributes_options(),
									'attributes' => array(
										'multiple' => 'multiple',
										'style'    => 'width: 500px; height: 125px;',
									),
									'class'      => 'chosen',
									'default'    => array( 'pa_color' )
								),
								array(
									'id'      => 'mini_cart_style',
									'type'    => 'select',
									'options' => array(
										''             => esc_html__( 'Default', 'nexio-toolkit' ),
										'cartdropdown' => esc_html__( 'Dropdown', 'nexio-toolkit' ),
									),
									'title'   => esc_html__( 'Mini Cart Style', 'nexio-toolkit' ),
									'default' => '',
								),
								array(
									'id'      => 'quickview_style',
									'type'    => 'select',
									'options' => array(
										''            => esc_html__( 'Default', 'nexio-toolkit' ),
										'quickdrawer' => esc_html__( 'Drawer', 'nexio-toolkit' ),
									),
									'title'   => esc_html__( 'Quickview Style', 'nexio-toolkit' ),
									'default' => '',
								),
								array(
									'type'    => 'subheading',
									'content' => 'Grid Column Settings',
								),
								array(
									'id'      => 'enable_products_sizes',
									'type'    => 'switcher',
									'title'   => esc_html__( 'Show Products Size', 'nexio-toolkit' ),
									'default' => true,
								),
								array(
									'title'      => esc_html__( 'Items per row on Desktop( For grid mode )', 'nexio-toolkit' ),
									'desc'       => esc_html__( '(Screen resolution of device >= 1500px )', 'nexio-toolkit' ),
									'id'         => 'nexio_woo_bg_items',
									'type'       => 'select',
									'default'    => '3',
									'options'    => array(
										'12' => '1 item',
										'6'  => '2 items',
										'4'  => '3 items',
										'3'  => '4 items',
										'15' => '5 items',
										'2'  => '6 items',
									),
									'dependency' => array( 'enable_products_sizes', '==', false ),
								),
								array(
									'title'      => esc_html__( 'Items per row on Desktop( For grid mode )', 'nexio-toolkit' ),
									'desc'       => esc_html__( '(Screen resolution of device >= 1200px < 1500px )', 'nexio-toolkit' ),
									'id'         => 'nexio_woo_lg_items',
									'type'       => 'select',
									'default'    => '4',
									'options'    => array(
										'12' => '1 item',
										'6'  => '2 items',
										'4'  => '3 items',
										'3'  => '4 items',
										'15' => '5 items',
										'2'  => '6 items',
									),
									'dependency' => array( 'enable_products_sizes', '==', false ),
								),
								array(
									'title'      => esc_html__( 'Items per row on landscape tablet( For grid mode )', 'nexio-toolkit' ),
									'desc'       => esc_html__( '(Screen resolution of device >=992px and < 1200px )', 'nexio-toolkit' ),
									'id'         => 'nexio_woo_md_items',
									'type'       => 'select',
									'default'    => '4',
									'options'    => array(
										'12' => '1 item',
										'6'  => '2 items',
										'4'  => '3 items',
										'3'  => '4 items',
										'15' => '5 items',
										'2'  => '6 items',
									),
									'dependency' => array( 'enable_products_sizes', '==', false ),
								),
								array(
									'title'      => esc_html__( 'Items per row on portrait tablet( For grid mode )', 'nexio-toolkit' ),
									'desc'       => esc_html__( '(Screen resolution of device >=768px and < 992px )', 'nexio-toolkit' ),
									'id'         => 'nexio_woo_sm_items',
									'type'       => 'select',
									'default'    => '4',
									'options'    => array(
										'12' => '1 item',
										'6'  => '2 items',
										'4'  => '3 items',
										'3'  => '4 items',
										'15' => '5 items',
										'2'  => '6 items',
									),
									'dependency' => array( 'enable_products_sizes', '==', false ),
								),
								array(
									'title'      => esc_html__( 'Items per row on Mobile( For grid mode )', 'nexio-toolkit' ),
									'desc'       => esc_html__( '(Screen resolution of device >=480  add < 768px)', 'nexio-toolkit' ),
									'id'         => 'nexio_woo_xs_items',
									'type'       => 'select',
									'default'    => '6',
									'options'    => array(
										'12' => '1 item',
										'6'  => '2 items',
										'4'  => '3 items',
										'3'  => '4 items',
										'15' => '5 items',
										'2'  => '6 items',
									),
									'dependency' => array( 'enable_products_sizes', '==', false ),
								),
								array(
									'title'      => esc_html__( 'Items per row on Mobile( For grid mode )', 'nexio-toolkit' ),
									'desc'       => esc_html__( '(Screen resolution of device < 480px)', 'nexio-toolkit' ),
									'id'         => 'nexio_woo_ts_items',
									'type'       => 'select',
									'default'    => '12',
									'options'    => array(
										'12' => '1 item',
										'6'  => '2 items',
										'4'  => '3 items',
										'3'  => '4 items',
										'15' => '5 items',
										'2'  => '6 items',
									),
									'dependency' => array( 'enable_products_sizes', '==', false ),
								),
							),
						),
						array(
							'name'   => 'categories',
							'title'  => esc_html__( 'Categories', 'nexio-toolkit' ),
							'fields' => array(
								array(
									'id'    => 'enable_best_seller_on_product_cat',
									'type'  => 'switcher',
									'title' => esc_html__( 'Enable Bestseller Products', 'nexio-toolkit' ),
								),
								array(
									'id'         => 'category_banner',
									'type'       => 'image',
									'title'      => esc_html__( 'Categories banner', 'nexio-toolkit' ),
									'desc'       => esc_html__( 'Banner in category page WooCommerce.', 'nexio-toolkit' ),
									'dependency' => array( 'enable_best_seller_on_product_cat', '==', true ),
								),
								array(
									'id'         => 'category_banner_url',
									'type'       => 'text',
									'default'    => '#',
									'title'      => esc_html__( 'Banner Url', 'nexio-toolkit' ),
									'dependency' => array( 'enable_best_seller_on_product_cat', '==', true ),
								),
							),
						),
						array(
							'name'   => 'single_product',
							'title'  => esc_html__( 'Single Product', 'nexio-toolkit' ),
							'fields' => array(
								array(
									'id'      => 'sidebar_product_position',
									'type'    => 'image_select',
									'title'   => esc_html__( 'Single Product Sidebar Position', 'nexio-toolkit' ),
									'desc'    => esc_html__( 'Select sidebar position on single product page.', 'nexio-toolkit' ),
									'options' => array(
										'left'  => NEXIO_TOOLKIT_URL . '/includes/core/assets/images/left-sidebar.png',
										'right' => NEXIO_TOOLKIT_URL . '/includes/core/assets/images/right-sidebar.png',
										'full'  => NEXIO_TOOLKIT_URL . '/includes/core/assets/images/default-sidebar.png',
									),
									'default' => 'left',
								),
								array(
									'id'      => 'enable_single_product_mobile',
									'type'    => 'switcher',
									'title'   => esc_html__( 'Product Mobile Layout', 'nexio-toolkit' ),
									'default' => true,
									'desc'    => esc_html__( 'Use the dedicated mobile interface on a real device instead of responsive. Note, this option is not available for desktop browsing and uses resize the screen.', 'nexio-toolkit' ),
								),
								array(
									'id'      => 'nexio_product_variation_layout',
									'type'    => 'select',
									'title'   => esc_html__( 'Product Mobile Variation Layout', 'nexio-toolkit' ),
									'desc'    => esc_html__( 'Choose Single Product Mobile Variation Layout', 'nexio-toolkit' ),
									'options' => array(
										'default'           => esc_html__( 'Default', 'nexio-toolkit' ),
										'variation_popup'   => esc_html__( 'Variation Popup', 'nexio-toolkit' ),
									),
									'default' => 'variation_popup',
								),
								array(
									'id'      => 'enable_info_product_single',
									'type'    => 'switcher',
									'title'   => esc_html__( 'Sticky Info Product Single', 'nexio-toolkit' ),
									'default' => true,
									'desc'    => esc_html__( 'On or Off Sticky Info Product Single.', 'nexio-toolkit' ),
								),
								array(
									'id'         => 'single_product_sidebar',
									'type'       => 'select',
									'title'      => esc_html__( 'Single Product Sidebar', 'nexio-toolkit' ),
									'options'    => $this->sidebars,
									'dependency' => array( 'sidebar_product_position_full', '==', false ),
								),
								array(
									'id'      => 'nexio_woo_single_product_layout',
									'type'    => 'select',
									'title'   => esc_html__( 'Choose Single Style', 'nexio-toolkit' ),
									'desc'    => esc_html__( 'Choose Single Product Style', 'nexio-toolkit' ),
									'options' => array(
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
									'default' => 'vertical_thumnail',
								),
								array(
									'id'         => 'single_product_img_bg_color',
									'type'       => 'color_picker',
									'title'      => esc_html__( 'Image Background Color', 'nexio-toolkit' ),
									'default'    => 'rgba(0,0,0,0)',
									'rgba'       => true,
									'dependency' => array(
										'nexio_woo_single_product_layout',
										'==',
										'with_background'
									),
									'desc'       => esc_html__( 'For "Big Images" style only. Default: transparent', 'nexio-toolkit' ),
								),
								array(
									'id'      => 'enable_single_product_sharing',
									'type'    => 'switcher',
									'title'   => esc_html__( 'Enable Product Sharing', 'nexio-toolkit' ),
									'default' => false,
								),
								array(
									'id'         => 'enable_single_product_sharing_fb',
									'type'       => 'switcher',
									'title'      => esc_html__( 'Facebook Sharing', 'nexio-toolkit' ),
									'default'    => true,
									'dependency' => array( 'enable_single_product_sharing', '==', true ),
								),
								array(
									'id'         => 'enable_single_product_sharing_tw',
									'type'       => 'switcher',
									'title'      => esc_html__( 'Twitter Sharing', 'nexio-toolkit' ),
									'default'    => true,
									'dependency' => array( 'enable_single_product_sharing', '==', true ),
								),
								array(
									'id'         => 'enable_single_product_sharing_pinterest',
									'type'       => 'switcher',
									'title'      => esc_html__( 'Pinterest Sharing', 'nexio-toolkit' ),
									'default'    => true,
									'dependency' => array( 'enable_single_product_sharing', '==', true ),
								),
								array(
									'id'         => 'enable_single_product_sharing_gplus',
									'type'       => 'switcher',
									'title'      => esc_html__( 'Google Plus Sharing', 'nexio-toolkit' ),
									'default'    => true,
									'dependency' => array( 'enable_single_product_sharing', '==', true ),
								),
							),
						),
						array(
							'name'   => 'extend_single_product',
							'title'  => esc_html__( 'Extend Single Products', 'nexio-toolkit' ),
							'fields' => array(
								array(
									'id'      => 'enable_extend_single_product',
									'type'    => 'switcher',
									'title'   => esc_html__( 'Enable Extend Single Products', 'nexio-toolkit' ),
									'default' => false,
								),
							),
						),
						array(
							'name'   => 'cross_sell',
							'title'  => esc_html__( 'Cross Sell', 'nexio-toolkit' ),
							'fields' => array(
								array(
									'id'      => 'enable_cross_sell',
									'type'    => 'select',
									'options' => array(
										'yes' => esc_html__( 'Yes', 'nexio-toolkit' ),
										'no'  => esc_html__( 'No', 'nexio-toolkit' ),
									),
									'title'   => esc_html__( 'Enable Cross Sell', 'nexio-toolkit' ),
									'default' => 'yes',
								),
								array(
									'title'      => esc_html__( 'Cross sell title', 'nexio-toolkit' ),
									'id'         => 'nexio_cross_sells_products_title',
									'type'       => 'text',
									'default'    => esc_html__( 'You may be interested in...', 'nexio-toolkit' ),
									'desc'       => esc_html__( 'Cross sell title', 'nexio-toolkit' ),
									'dependency' => array( 'enable_cross_sell', '==', 'yes' ),
								),
								
								array(
									'title'      => esc_html__( 'Cross sell items per row on Desktop', 'nexio-toolkit' ),
									'desc'       => esc_html__( '(Screen resolution of device >= 1500px )', 'nexio-toolkit' ),
									'id'         => 'nexio_woo_crosssell_ls_items',
									'type'       => 'select',
									'default'    => '4',
									'options'    => array(
										'1' => '1 item',
										'2' => '2 items',
										'3' => '3 items',
										'4' => '4 items',
										'5' => '5 items',
										'6' => '6 items',
									),
									'dependency' => array( 'enable_cross_sell', '==', 'yes' ),
								),
								array(
									'title'      => esc_html__( 'Cross sell items per row on Desktop', 'nexio-toolkit' ),
									'desc'       => esc_html__( '(Screen resolution of device >= 1200px < 1500px )', 'nexio-toolkit' ),
									'id'         => 'nexio_woo_crosssell_lg_items',
									'type'       => 'select',
									'default'    => '4',
									'options'    => array(
										'1' => '1 item',
										'2' => '2 items',
										'3' => '3 items',
										'4' => '4 items',
										'5' => '5 items',
										'6' => '6 items',
									),
									'dependency' => array( 'enable_cross_sell', '==', 'yes' ),
								),
								array(
									'title'      => esc_html__( 'Cross sell items per row on landscape tablet', 'nexio-toolkit' ),
									'desc'       => esc_html__( '(Screen resolution of device >=992px and < 1200px )', 'nexio-toolkit' ),
									'id'         => 'nexio_woo_crosssell_md_items',
									'type'       => 'select',
									'default'    => '3',
									'options'    => array(
										'1' => '1 item',
										'2' => '2 items',
										'3' => '3 items',
										'4' => '4 items',
										'5' => '5 items',
										'6' => '6 items',
									),
									'dependency' => array( 'enable_cross_sell', '==', 'yes' ),
								),
								array(
									'title'      => esc_html__( 'Cross sell items per row on portrait tablet', 'nexio-toolkit' ),
									'desc'       => esc_html__( '(Screen resolution of device >=768px and < 992px )', 'nexio-toolkit' ),
									'id'         => 'nexio_woo_crosssell_sm_items',
									'type'       => 'select',
									'default'    => '2',
									'options'    => array(
										'1' => '1 item',
										'2' => '2 items',
										'3' => '3 items',
										'4' => '4 items',
										'5' => '5 items',
										'6' => '6 items',
									),
									'dependency' => array( 'enable_cross_sell', '==', 'yes' ),
								),
								array(
									'title'      => esc_html__( 'Cross sell items per row on Mobile', 'nexio-toolkit' ),
									'desc'       => esc_html__( '(Screen resolution of device >=480  add < 768px)', 'nexio-toolkit' ),
									'id'         => 'nexio_woo_crosssell_xs_items',
									'type'       => 'select',
									'default'    => '2',
									'options'    => array(
										'1' => '1 item',
										'2' => '2 items',
										'3' => '3 items',
										'4' => '4 items',
										'5' => '5 items',
										'6' => '6 items',
									),
									'dependency' => array( 'enable_cross_sell', '==', 'yes' ),
								),
								array(
									'title'      => esc_html__( 'Cross sell items per row on Mobile', 'nexio-toolkit' ),
									'desc'       => esc_html__( '(Screen resolution of device < 480px)', 'nexio-toolkit' ),
									'id'         => 'nexio_woo_crosssell_ts_items',
									'type'       => 'select',
									'default'    => '1',
									'options'    => array(
										'1' => '1 item',
										'2' => '2 items',
										'3' => '3 items',
										'4' => '4 items',
										'5' => '5 items',
										'6' => '6 items',
									),
									'dependency' => array( 'enable_cross_sell', '==', 'yes' ),
								),
							),
						),
						array(
							'name'   => 'related_product',
							'title'  => 'Related Products',
							'fields' => array(
								array(
									'id'      => 'enable_relate_products',
									'type'    => 'select',
									'options' => array(
										'yes' => esc_html__( 'Yes', 'nexio-toolkit' ),
										'no'  => esc_html__( 'No', 'nexio-toolkit' ),
									),
									'title'   => esc_html__( 'Enable Related Products', 'nexio-toolkit' ),
									'default' => 'yes',
								),
								array(
									'title'      => esc_html__( 'Related products title', 'nexio-toolkit' ),
									'id'         => 'nexio_related_products_title',
									'type'       => 'text',
									'default'    => 'Related Products',
									'desc'       => esc_html__( 'Related products title', 'nexio-toolkit' ),
									'dependency' => array( 'enable_relate_products', '==', 'yes' ),
								),
								array(
									'title'    => esc_html__( 'Limit Number Of Products', 'nexio' ),
									'id'       => 'nexio_related_products_perpage',
									'type'     => 'text',
									'default'  => '8',
									'validate' => 'numeric',
									'subtitle' => esc_html__( 'Number of products on shop page', 'nexio' ),
								),
								array(
									'title'      => esc_html__( 'Related products items per row on Desktop', 'nexio-toolkit' ),
									'desc'       => esc_html__( '(Screen resolution of device >= 1500px )', 'nexio-toolkit' ),
									'id'         => 'nexio_woo_related_ls_items',
									'type'       => 'select',
									'default'    => '4',
									'options'    => array(
										'1' => '1 item',
										'2' => '2 items',
										'3' => '3 items',
										'4' => '4 items',
										'5' => '5 items',
										'6' => '6 items',
									),
									'dependency' => array( 'enable_relate_products', '==', 'yes' ),
								),
								array(
									'title'      => esc_html__( 'Related products items per row on Desktop', 'nexio-toolkit' ),
									'desc'       => esc_html__( '(Screen resolution of device >= 1200px < 1500px )', 'nexio-toolkit' ),
									'id'         => 'nexio_woo_related_lg_items',
									'type'       => 'select',
									'default'    => '4',
									'options'    => array(
										'1' => '1 item',
										'2' => '2 items',
										'3' => '3 items',
										'4' => '4 items',
										'5' => '5 items',
										'6' => '6 items',
									),
									'dependency' => array( 'enable_relate_products', '==', 'yes' ),
								),
								array(
									'title'      => esc_html__( 'Related products items per row on landscape tablet', 'nexio-toolkit' ),
									'desc'       => esc_html__( '(Screen resolution of device >=992px and < 1200px )', 'nexio-toolkit' ),
									'id'         => 'nexio_woo_related_md_items',
									'type'       => 'select',
									'default'    => '3',
									'options'    => array(
										'1' => '1 item',
										'2' => '2 items',
										'3' => '3 items',
										'4' => '4 items',
										'5' => '5 items',
										'6' => '6 items',
									),
									'dependency' => array( 'enable_relate_products', '==', 'yes' ),
								),
								array(
									'title'      => esc_html__( 'Related product items per row on portrait tablet', 'nexio-toolkit' ),
									'desc'       => esc_html__( '(Screen resolution of device >=768px and < 992px )', 'nexio-toolkit' ),
									'id'         => 'nexio_woo_related_sm_items',
									'type'       => 'select',
									'default'    => '2',
									'options'    => array(
										'1' => '1 item',
										'2' => '2 items',
										'3' => '3 items',
										'4' => '4 items',
										'5' => '5 items',
										'6' => '6 items',
									),
									'dependency' => array( 'enable_relate_products', '==', 'yes' ),
								),
								array(
									'title'      => esc_html__( 'Related products items per row on Mobile', 'nexio-toolkit' ),
									'desc'       => esc_html__( '(Screen resolution of device >=480  add < 768px)', 'nexio-toolkit' ),
									'id'         => 'nexio_woo_related_xs_items',
									'type'       => 'select',
									'default'    => '2',
									'options'    => array(
										'1' => '1 item',
										'2' => '2 items',
										'3' => '3 items',
										'4' => '4 items',
										'5' => '5 items',
										'6' => '6 items',
									),
									'dependency' => array( 'enable_relate_products', '==', 'yes' ),
								),
								array(
									'title'      => esc_html__( 'Related products items per row on Mobile', 'nexio-toolkit' ),
									'desc'       => esc_html__( '(Screen resolution of device < 480px)', 'nexio-toolkit' ),
									'id'         => 'nexio_woo_related_ts_items',
									'type'       => 'select',
									'default'    => '1',
									'options'    => array(
										'1' => '1 item',
										'2' => '2 items',
										'3' => '3 items',
										'4' => '4 items',
										'5' => '5 items',
										'6' => '6 items',
									),
									'dependency' => array( 'enable_relate_products', '==', 'yes' ),
								),
							),
						),
						array(
							'name'   => 'upsells_product',
							'title'  => esc_html__( 'Up sells Products', 'nexio-toolkit' ),
							'fields' => array(
								array(
									'id'      => 'enable_up_sell',
									'type'    => 'select',
									'options' => array(
										'yes' => esc_html__( 'Yes', 'nexio-toolkit' ),
										'no'  => esc_html__( 'No', 'nexio-toolkit' ),
									),
									'title'   => esc_html__( 'Enable Up Sell', 'nexio-toolkit' ),
									'default' => 'yes',
								),
								array(
									'title'      => esc_html__( 'Up sells title', 'nexio-toolkit' ),
									'id'         => 'nexio_upsell_products_title',
									'type'       => 'text',
									'default'    => esc_html__( 'You may also like...', 'nexio-toolkit' ),
									'desc'       => esc_html__( 'Up sells products title', 'nexio-toolkit' ),
									'dependency' => array( 'enable_up_sell', '==', 'yes' ),
								),
								
								array(
									'title'      => esc_html__( 'Up sells items per row on Desktop', 'nexio-toolkit' ),
									'desc'       => esc_html__( '(Screen resolution of device >= 1500px )', 'nexio-toolkit' ),
									'id'         => 'nexio_woo_upsell_ls_items',
									'type'       => 'select',
									'default'    => '3',
									'options'    => array(
										'1' => '1 item',
										'2' => '2 items',
										'3' => '3 items',
										'4' => '4 items',
										'5' => '5 items',
										'6' => '6 items',
									),
									'dependency' => array( 'enable_up_sell', '==', 'yes' ),
								),
								array(
									'title'      => esc_html__( 'Up sells items per row on Desktop', 'nexio-toolkit' ),
									'desc'       => esc_html__( '(Screen resolution of device >= 1200px < 1500px )', 'nexio-toolkit' ),
									'id'         => 'nexio_woo_upsell_lg_items',
									'type'       => 'select',
									'default'    => '3',
									'options'    => array(
										'1' => '1 item',
										'2' => '2 items',
										'3' => '3 items',
										'4' => '4 items',
										'5' => '5 items',
										'6' => '6 items',
									),
									'dependency' => array( 'enable_up_sell', '==', 'yes' ),
								),
								array(
									'title'      => esc_html__( 'Up sells items per row on landscape tablet', 'nexio-toolkit' ),
									'desc'       => esc_html__( '(Screen resolution of device >=992px and < 1200px )', 'nexio-toolkit' ),
									'id'         => 'nexio_woo_upsell_md_items',
									'type'       => 'select',
									'default'    => '3',
									'options'    => array(
										'1' => '1 item',
										'2' => '2 items',
										'3' => '3 items',
										'4' => '4 items',
										'5' => '5 items',
										'6' => '6 items',
									),
									'dependency' => array( 'enable_up_sell', '==', 'yes' ),
								),
								array(
									'title'      => esc_html__( 'Up sells items per row on portrait tablet', 'nexio-toolkit' ),
									'desc'       => esc_html__( '(Screen resolution of device >=768px and < 992px )', 'nexio-toolkit' ),
									'id'         => 'nexio_woo_upsell_sm_items',
									'type'       => 'select',
									'default'    => '2',
									'options'    => array(
										'1' => '1 item',
										'2' => '2 items',
										'3' => '3 items',
										'4' => '4 items',
										'5' => '5 items',
										'6' => '6 items',
									),
									'dependency' => array( 'enable_up_sell', '==', 'yes' ),
								),
								array(
									'title'      => esc_html__( 'Up sells items per row on Mobile', 'nexio-toolkit' ),
									'desc'       => esc_html__( '(Screen resolution of device >=480  add < 768px)', 'nexio-toolkit' ),
									'id'         => 'nexio_woo_upsell_xs_items',
									'type'       => 'select',
									'default'    => '2',
									'options'    => array(
										'1' => '1 item',
										'2' => '2 items',
										'3' => '3 items',
										'4' => '4 items',
										'5' => '5 items',
										'6' => '6 items',
									),
									'dependency' => array( 'enable_up_sell', '==', 'yes' ),
								),
								array(
									'title'      => esc_html__( 'Up sells items per row on Mobile', 'nexio-toolkit' ),
									'desc'       => esc_html__( '(Screen resolution of device < 480px)', 'nexio-toolkit' ),
									'id'         => 'nexio_woo_upsell_ts_items',
									'type'       => 'select',
									'default'    => '1',
									'options'    => array(
										'1' => '1 item',
										'2' => '2 items',
										'3' => '3 items',
										'4' => '4 items',
										'5' => '5 items',
										'6' => '6 items',
									),
									'dependency' => array( 'enable_up_sell', '==', 'yes' ),
								),
							),
						),
					),
				);
			}
			
			$options[] = array(
				'name'   => 'social_settings',
				'title'  => esc_html__( 'Social Settings', 'nexio-toolkit' ),
				'icon'   => 'fa fa-users',
				'fields' => array(
					array(
						'type'    => 'subheading',
						'content' => esc_html__( 'Socials Networks', 'nexio-toolkit' ),
					),
					array(
						'id'              => 'user_all_social',
						'type'            => 'group',
						'title'           => esc_html__( 'Socials', 'nexio-toolkit' ),
						'button_title'    => esc_html__( 'Add New Social', 'nexio-toolkit' ),
						'accordion_title' => esc_html__( 'Social Settings', 'nexio-toolkit' ),
						'fields'          => array(
							array(
								'id'      => 'title_social',
								'type'    => 'text',
								'title'   => esc_html__( 'Social Title', 'nexio-toolkit' ),
								'default' => esc_html__( 'Facebook', 'nexio-toolkit' ),
							),
							array(
								'id'      => 'link_social',
								'type'    => 'text',
								'title'   => esc_html__( 'Social Link', 'nexio-toolkit' ),
								'default' => 'https://facebook.com',
							),
							array(
								'id'      => 'icon_social',
								'type'    => 'icon',
								'title'   => esc_html__( 'Social Icon', 'nexio-toolkit' ),
								'default' => 'fa fa-facebook',
							),
						),
					),
				),
			);
			
			$options[] = array(
				'name'   => 'typography',
				'title'  => esc_html__( 'Typography Options', 'nexio-toolkit' ),
				'icon'   => 'fa fa-font',
				'fields' => array(
					array(
						'id'      => 'enable_google_font',
						'type'    => 'switcher',
						'title'   => esc_html__( 'Enable Google Font', 'nexio-toolkit' ),
						'default' => false,
						'on'      => esc_html__( 'Enable', 'nexio-toolkit' ),
						'off'     => esc_html__( 'Disable', 'nexio-toolkit' )
					),
					array(
						'id'         => 'typography_themes',
						'type'       => 'typography',
						'title'      => esc_html__( 'Body Typography', 'nexio-toolkit' ),
						'default'    => array(
							'family'  => 'Open Sans',
							'variant' => '400',
							'font'    => 'google',
						),
						'dependency' => array( 'enable_google_font', '==', true )
					),
					array(
						'id'         => 'fontsize-body',
						'type'       => 'number',
						'title'      => esc_html__( 'Body Font Size', 'nexio-toolkit' ),
						'default'    => '15',
						'after'      => ' <i class="cs-text-muted">px</i>',
						'dependency' => array( 'enable_google_font', '==', true )
					)
				),
			);
			
			$options[] = array(
				'name'   => 'backup_option',
				'title'  => esc_html__( 'Backup Options', 'nexio-toolkit' ),
				'icon'   => 'fa fa-font',
				'fields' => array(
					array(
						'type'  => 'backup',
						'title' => esc_html__( 'Backup Field', 'nexio-toolkit' ),
					),
				),
			);
			
			
			CSFramework::instance( $settings, $options );
		}
	}
	
	new Nexio_ThemeOption();
}
