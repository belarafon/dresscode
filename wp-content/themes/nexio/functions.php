<?php
if ( ! isset( $content_width ) ) {
	$content_width = 900;
}
if ( ! class_exists( 'Nexio_Functions' ) ) {
	class Nexio_Functions {
		
		/**
		 * Instance of the class.
		 *
		 * @since   1.0.0
		 *
		 * @var   object
		 */
		protected static $instance = null;
		
		/**
		 * Initialize the plugin by setting localization and loading public scripts
		 * and styles.
		 *
		 * @since    1.0.0
		 */
		public function __construct() {
			add_action( 'after_setup_theme', array( $this, 'setup' ) );
			add_action( 'widgets_init', array( $this, 'widgets_init' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
			add_action( 'upload_mimes', array( $this, 'add_svg_type_upload' ), 1 );
			add_filter( 'get_default_comment_status', array( $this, 'open_default_comments_for_page' ), 10, 3 );
			add_filter( 'comment_form_fields', array( &$this, 'nexio_move_comment_field_to_bottom' ), 10, 3 );
			
			$this->includes();
		}
		
		public function setup() {
			/*
			* Make theme available for translation.
			* Translations can be filed in the /languages/ directory.
			* If you're building a theme based on boutique, use a find and replace
			* to change 'nexio' to the name of your theme in all the template files
			*/
			load_theme_textdomain( 'nexio', get_template_directory() . '/languages' );
			add_theme_support( 'automatic-feed-links' );
			
			/*
			 * Let WordPress manage the document title.
			 * By adding theme support, we declare that this theme does not use a
			 * hard-coded <title> tag in the document head, and expect WordPress to
			 * provide it for us.
			 */
			add_theme_support( 'title-tag' );
			
			/*
			 * Enable support for Post Formats.
			 *
			 * See: https://codex.wordpress.org/Post_Formats
			 */
			add_theme_support(
				'post-formats',
				array(
					'video',
					'gallery',
					'audio',
				)
			);
			
			/*
			 * Enable support for Post Thumbnails on posts and pages.
			 *
			 * See: https://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
			 */
			add_theme_support( 'post-thumbnails' );
			add_theme_support( 'custom-header' );
			
			set_post_thumbnail_size( 825, 510, true );
			
			/*This theme uses wp_nav_menu() in two locations.*/
			register_nav_menus(
				array(
					'primary'     => esc_html__( 'Primary Menu', 'nexio' ),
					'double-menu' => esc_html__( 'Double Menu', 'nexio' ),
					'privacy'     => esc_html__( 'Privacy Vertical Menu', 'nexio' )
				)
			);
			
			/*
			 * Switch default core markup for search form, comment form, and comments
			 * to output valid HTML5.
			 */
			add_theme_support(
				'html5',
				array(
					'search-form',
					'comment-form',
					'comment-list',
					'gallery',
					'caption'
				)
			);
			
			if ( class_exists( 'WooCommerce' ) ) {
				/*Support woocommerce*/
				add_theme_support( 'woocommerce' );
				add_theme_support( 'wc-product-gallery-lightbox' );
				add_theme_support( 'wc-product-gallery-slider' );
				add_theme_support( 'wc-product-gallery-zoom' );
			}
			
			// Add image sizes
			$img_sizes = array(
				'79x79',
				'150x150', // thumb
				'393x420', // shop
				'180x200', // Compare page
				'440x333', // Blog
			);
			foreach ( $img_sizes as $img_size ) {
				$img_size_arg = explode( 'x', $img_size );
				$crop         = false;
				if ( $img_size = '320x250' ) {
					$crop = true;
				}
				add_image_size( "fami_img_size_{$img_size}", $img_size_arg[0], $img_size_arg[1], $crop );
			}
		}
		
		public function nexio_move_comment_field_to_bottom( $fields ) {
			$comment_field = $fields['comment'];
			unset( $fields['comment'] );
			$fields['comment'] = $comment_field;
			
			return $fields;
		}
		
		/**
		 * Register widget area.
		 *
		 * @since nexio 1.0
		 *
		 * @link  https://codex.wordpress.org/Function_Reference/register_sidebar
		 */
		function widgets_init() {
			register_sidebar(
				array(
					'name'          => esc_html__( 'Primary Sidebar', 'nexio' ),
					'id'            => 'primary_sidebar',
					'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'nexio' ),
					'before_widget' => '<div id="%1$s" class="widget %2$s">',
					'after_widget'  => '</div>',
					'before_title'  => '<h2 class="widgettitle">',
					'after_title'   => '<span class="arrow"></span></h2>',
				)
			);
			
			register_sidebar(
				array(
					'name'          => esc_html__( 'Shop Sidebar', 'nexio' ),
					'id'            => 'shop-widget-area',
					'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'nexio' ),
					'before_widget' => '<div id="%1$s" class="widget %2$s">',
					'after_widget'  => '</div>',
					'before_title'  => '<h2 class="widgettitle">',
					'after_title'   => '<span class="arrow"></span></h2>',
				)
			);
			
			register_sidebar(
				array(
					'name'          => esc_html__( 'Product Sidebar', 'nexio' ),
					'id'            => 'product-widget-area',
					'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'nexio' ),
					'before_widget' => '<div id="%1$s" class="widget %2$s">',
					'after_widget'  => '</div>',
					'before_title'  => '<h2 class="widgettitle">',
					'after_title'   => '<span class="arrow"></span></h2>',
				)
			);
			
			register_sidebar(
				array(
					'name'          => esc_html__( 'Product Description Sidebar', 'nexio' ),
					'id'            => 'product-desc-sidebar',
					'description'   => esc_html__( 'This sidebar is only displayed on default product page or product with vertical thumbnails', 'nexio' ),
					'before_widget' => '<div id="%1$s" class="widget %2$s">',
					'after_widget'  => '</div>',
					'before_title'  => '<h2 class="widgettitle">',
					'after_title'   => '<span class="arow"></span></h2>',
				)
			);
		}
		
		/**
		 * Enqueue scripts and styles.
		 *
		 * @since nexio 1.0
		 */
		function scripts() {
			if ( class_exists( 'WooCommerce' ) ) {
				if ( ! is_woocommerce() && ! is_cart() && ! is_checkout() ) {
					wp_dequeue_script( 'wc_price_slider' );
					wp_dequeue_script( 'wc-checkout' );
					wp_dequeue_script( 'wc-cart' );
					wp_dequeue_script( 'wc-chosen' );
					wp_dequeue_script( 'prettyPhoto' );
					wp_dequeue_script( 'prettyPhoto-init' );
					wp_dequeue_script( 'jquery-blockui' );
					wp_dequeue_script( 'jquery-placeholder' );
					wp_dequeue_script( 'fancybox' );
					wp_dequeue_script( 'jqueryui' );
				}
			}
			
			$animation_on_scroll = nexio_get_option( 'animation_on_scroll', '' );
			
			/*Load our main stylesheet.*/
			wp_enqueue_style( 'bootstrap', get_theme_file_uri( '/assets/css/bootstrap.min.css' ), array(), false );
			wp_enqueue_style( 'owl-carousel', get_theme_file_uri( '/assets/css/owl.carousel.min.css' ), array(), false );
			wp_enqueue_style( 'font-awesome', get_theme_file_uri( '/assets/css/font-awesome.min.css' ), array(), false );
			wp_enqueue_style( 'flat-icons', get_theme_file_uri( '/assets/fonts/flaticon.css' ), array(), false );
			wp_enqueue_style( 'fullpage', get_theme_file_uri( '/assets/css/fullpage.css' ), array(), false );
			wp_enqueue_style( 'scrollbar', get_theme_file_uri( '/assets/css/jquery.scrollbar.css' ), array(), false );
			
			if ( $animation_on_scroll ) {
				wp_enqueue_style( 'animation-on-scroll', get_theme_file_uri( '/assets/css/animation-on-scroll.css' ), array(), false );
			}
			wp_enqueue_style( 'nexio-main-style', get_template_directory_uri() . '/style.css', array(), false );
			
			if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
				wp_enqueue_script( 'comment-reply' );
			}
			
			/*Load lib js*/
			wp_enqueue_script( 'imagesloaded' );
			wp_enqueue_script( 'wc-add-to-cart-variation' );
			
			wp_enqueue_script( 'bootstrap', get_theme_file_uri( '/assets/js/bootstrap.min.js' ), array( 'jquery' ), false, true );
			wp_enqueue_script( 'owl-carousel', get_theme_file_uri( '/assets/js/owl.carousel.min.js' ), array( 'jquery' ), false, true );
			wp_enqueue_script( 'magnific-popup', get_theme_file_uri( '/assets/js/jquery.magnific-popup.min.js' ), array( 'jquery' ), false, true );
			wp_enqueue_script( 'scrollbar', get_theme_file_uri( '/assets/js/jquery.scrollbar.min.js' ), array( 'jquery' ), false, true );
			wp_enqueue_script( 'sticky', get_theme_file_uri( '/assets/js/jquery.sticky.js' ), array( 'jquery' ), false, true );
			wp_enqueue_script( 'jquery-countdown', get_theme_file_uri( '/assets/js/jquery.countdown.js' ), array( 'jquery' ), false, true );
			wp_enqueue_script( 'theia-sticky-sidebar', get_theme_file_uri( '/assets/js/theia-sticky-sidebar.min.js' ), array( 'jquery' ), false, true );
			wp_enqueue_script( 'threesixty', get_theme_file_uri( '/assets/js/threesixty.min.js' ), array( 'jquery' ), false, true );
			wp_enqueue_script( 'slick', get_theme_file_uri( '/assets/js/slick.js' ), array( 'jquery' ), false, true );
			wp_enqueue_script( 'isotope', get_theme_file_uri( '/assets/js/isotope.pkgd.min.js' ), array( 'jquery' ), false, true );
			wp_enqueue_script( 'fullPage', get_theme_file_uri( '/assets/js/jquery.fullPage.min.js' ), array( 'jquery' ), false, true );
			
			if ( $animation_on_scroll ) {
				wp_enqueue_script( 'wow', get_theme_file_uri( '/assets/js/wow.min.js' ), array(), false, true );
			}
			
			$gmap_api_key = nexio_get_option( 'gmap_api_key', '' );
			$gmap_api_key = trim( $gmap_api_key );
			if ( $gmap_api_key != '' ) {
				$load_gmap_js        = false;
				$load_gmap_js_target = nexio_get_option( 'load_gmap_js_target', 'all_pages' );
				if ( $load_gmap_js_target == 'selected_pages' ) {
					$load_gmap_js_on = nexio_get_option( 'load_gmap_js_on', array() );
					if ( ! is_array( $load_gmap_js_on ) ) {
						$load_gmap_js_on = array();
					}
					if ( is_singular( 'page' ) ) {
						if ( in_array( get_the_ID(), $load_gmap_js_on ) ) {
							$load_gmap_js = true;
						}
					}
				}
				if ( $load_gmap_js_target == 'all_pages' ) {
					$load_gmap_js = true;
				}
				if ( $load_gmap_js ) {
					wp_enqueue_script( 'maps', esc_url( 'https://maps.googleapis.com/maps/api/js?key=' . esc_attr( $gmap_api_key ) ), array( 'jquery' ), false, true );
				}
			}
			
			$enable_lazy = nexio_get_option( 'nexio_enable_lazy', false );
			if ( $enable_lazy ) {
				wp_enqueue_script( 'lazy', get_theme_file_uri( '/assets/js/jquery.lazy.min.js' ), array( 'jquery' ), false, true );
			}
			
			wp_enqueue_script( 'nexio-frontend', get_theme_file_uri( '/assets/js/frontend.js' ), array(), false, true );
			wp_localize_script(
				'nexio-frontend',
				'nexio_theme_frontend',
				array(
					'ajaxurl'                  => admin_url( 'admin-ajax.php' ),
					'security'                 => wp_create_nonce( 'nexio_ajax_frontend' ),
					'main_menu_break_point'    => 1024,
					'newsletter_popup_timeout' => nexio_get_option( 'newsletter_popup_timeout', 5000 ),
					'text'                     => array(
						'load_more'         => esc_html__( 'Load More', 'nexio' ),
						'view_all'          => esc_html__( 'View All', 'nexio' ),
						'no_more_product'   => esc_html__( 'No More Product', 'nexio' ),
						'no_products_found' => esc_html__( 'There are no suitable suggestions, try to hit Enter to see possible results', 'nexio' ),
						'more_detail'       => esc_html__( 'More Details', 'nexio' ),
						'less_detail'       => esc_html__( 'Less Details', 'nexio' ),
						'back_to_menu_text' => esc_html__( 'Back to "{{menu_name}}"', 'nexio' ),
					),
					'animation_on_scroll'      => $animation_on_scroll ? 'yes' : 'no'
				)
			);
		}
		
		/**
		 * Filter whether comments are open for a given post type.
		 *
		 * @param string $status       Default status for the given post type,
		 *                             either 'open' or 'closed'.
		 * @param string $post_type    Post type. Default is `post`.
		 * @param string $comment_type Type of comment. Default is `comment`.
		 *
		 * @return string (Maybe) filtered default status for the given post type.
		 */
		function open_default_comments_for_page( $status, $post_type, $comment_type ) {
			if ( 'page' == $post_type ) {
				return 'open';
			}
			
			return $status;
			/*You could be more specific here for different comment types if desired*/
		}
		
		
		public function includes() {
			include_once( get_template_directory() . '/framework/framework.php' );
			define( 'CS_ACTIVE_FRAMEWORK', true ); // default true
			define( 'CS_ACTIVE_METABOX', true ); // default true
			define( 'CS_ACTIVE_TAXONOMY', true ); // default true
			define( 'CS_ACTIVE_SHORTCODE', false ); // default true
			define( 'CS_ACTIVE_CUSTOMIZE', false ); // default true
			
			include_once( get_template_directory() . '/framework/import-data/import-demo-config.php' );
		}
		
		public function add_svg_type_upload( $file_types ) {
			$new_filetypes        = array();
			$new_filetypes['svg'] = 'image/svg+xml';
			$file_types           = array_merge( $file_types, $new_filetypes );
			
			return $file_types;
		}
		
	}
	
	new  Nexio_Functions();
}

if ( ! function_exists( 'nexio_html_output' ) ) {
	function nexio_html_output( $html ) {
		return apply_filters( 'nexio_html_output', $html );
	}
}
add_action( 'admin_head', 'vc_css_admin' );
if ( ! function_exists( 'vc_css_admin' ) ) {
    function vc_css_admin() {
        echo '<style>
	    .vc_license-activation-notice,
	    #customize-control-woocommerce_catalog_columns {
	      display:none !important; 
	    } 
	  </style>';
    }
}