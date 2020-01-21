<?php
/**
 * @version    1.0
 * @package    Nexio_Toolkit
 * @author     FamiThemes
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * Class Toolkit Post Type
 *
 * @since    1.0
 */
if ( !class_exists( 'Nexio_Toolkit_Posttype' ) ) {
	class Nexio_Toolkit_Posttype
	{

		public function __construct()
		{
			add_action( 'init', array( &$this, 'init' ), 9999 );
		}

		public static function init()
		{
			/*Mega menu */
			$args = array(
				'labels'              => array(
					'name'               => __( 'Mega Builder', 'nexio-toolkit' ),
					'singular_name'      => __( 'Mega menu item', 'nexio-toolkit' ),
					'add_new'            => __( 'Add new', 'nexio-toolkit' ),
					'add_new_item'       => __( 'Add new menu item', 'nexio-toolkit' ),
					'edit_item'          => __( 'Edit menu item', 'nexio-toolkit' ),
					'new_item'           => __( 'New menu item', 'nexio-toolkit' ),
					'view_item'          => __( 'View menu item', 'nexio-toolkit' ),
					'search_items'       => __( 'Search menu items', 'nexio-toolkit' ),
					'not_found'          => __( 'No menu items found', 'nexio-toolkit' ),
					'not_found_in_trash' => __( 'No menu items found in trash', 'nexio-toolkit' ),
					'parent_item_colon'  => __( 'Parent menu item:', 'nexio-toolkit' ),
					'menu_name'          => __( 'Menu Builder', 'nexio-toolkit' ),
				),
				'hierarchical'        => false,
				'description'         => __( 'Mega Menus.', 'nexio-toolkit' ),
				'supports'            => array( 'title', 'editor' ),
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => 'nexio_menu',
				'menu_position'       => 3,
				'show_in_nav_menus'   => true,
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'has_archive'         => false,
				'query_var'           => true,
				'can_export'          => true,
				'rewrite'             => false,
				'capability_type'     => 'page',
				'menu_icon'           => 'dashicons-welcome-widgets-menus',
			);
			register_post_type( 'megamenu', $args );

			/* Footer */
			$args = array(
				'labels'              => array(
					'name'               => __( 'Footers', 'nexio-toolkit' ),
					'singular_name'      => __( 'Footers', 'nexio-toolkit' ),
					'add_new'            => __( 'Add New', 'nexio-toolkit' ),
					'add_new_item'       => __( 'Add new footer', 'nexio-toolkit' ),
					'edit_item'          => __( 'Edit footer', 'nexio-toolkit' ),
					'new_item'           => __( 'New footer', 'nexio-toolkit' ),
					'view_item'          => __( 'View footer', 'nexio-toolkit' ),
					'search_items'       => __( 'Search template footer', 'nexio-toolkit' ),
					'not_found'          => __( 'No template items found', 'nexio-toolkit' ),
					'not_found_in_trash' => __( 'No template items found in trash', 'nexio-toolkit' ),
					'parent_item_colon'  => __( 'Parent template item:', 'nexio-toolkit' ),
					'menu_name'          => __( 'Footer Builder', 'nexio-toolkit' ),
				),
				'hierarchical'        => false,
				'description'         => __( 'To Build Template Footer.', 'nexio-toolkit' ),
				'supports'            => array( 'title', 'editor', 'page-attributes', 'thumbnail' ),
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => 'nexio_menu',
				'menu_position'       => 4,
				'show_in_nav_menus'   => true,
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'has_archive'         => false,
				'query_var'           => true,
				'can_export'          => true,
				'rewrite'             => false,
				'capability_type'     => 'page',
			);
			register_post_type( 'footer', $args );
			/*NewsLetter */
			$args = array(
				'labels'              => array(
					'name'               => __( 'NewsLetter', 'nexio-toolkit' ),
					'singular_name'      => __( 'NewsLetter item', 'nexio-toolkit' ),
					'add_new'            => __( 'Add new', 'nexio-toolkit' ),
					'add_new_item'       => __( 'Add new NewsLetter', 'nexio-toolkit' ),
					'edit_item'          => __( 'Edit NewsLetter', 'nexio-toolkit' ),
					'new_item'           => __( 'New NewsLetter', 'nexio-toolkit' ),
					'view_item'          => __( 'View NewsLetter', 'nexio-toolkit' ),
					'search_items'       => __( 'Search NewsLetter', 'nexio-toolkit' ),
					'not_found'          => __( 'No NewsLetter found', 'nexio-toolkit' ),
					'not_found_in_trash' => __( 'No NewsLetter found in trash', 'nexio-toolkit' ),
					'parent_item_colon'  => __( 'Parent NewsLetter:', 'nexio-toolkit' ),
					'menu_name'          => __( 'NewsLetter Builder', 'nexio-toolkit' ),
				),
				'hierarchical'        => false,
				'description'         => __( 'To Build Template NewsLetter.', 'nexio-toolkit' ),
				'supports'            => array( 'title', 'editor' ),
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => 'nexio_menu',
				'menu_position'       => 3,
				'show_in_nav_menus'   => true,
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'has_archive'         => false,
				'query_var'           => true,
				'can_export'          => true,
				'rewrite'             => false,
				'capability_type'     => 'page',
				'menu_icon'           => 'dashicons-welcome-widgets-menus',
			);
			register_post_type( 'newsletter', $args );

			/*Size Guide */
			$args = array(
				'labels'              => array(
					'name'               => __( 'Size Guide', 'nexio-toolkit' ),
					'singular_name'      => __( 'Size Guide item', 'nexio-toolkit' ),
					'add_new'            => __( 'Add new', 'nexio-toolkit' ),
					'add_new_item'       => __( 'Add new Size Guide', 'nexio-toolkit' ),
					'edit_item'          => __( 'Edit Size Guide', 'nexio-toolkit' ),
					'new_item'           => __( 'New Size Guide', 'nexio-toolkit' ),
					'view_item'          => __( 'View Size Guide', 'nexio-toolkit' ),
					'search_items'       => __( 'Search Size Guide', 'nexio-toolkit' ),
					'not_found'          => __( 'No Size Guide found', 'nexio-toolkit' ),
					'not_found_in_trash' => __( 'No Size Guide found in trash', 'nexio-toolkit' ),
					'parent_item_colon'  => __( 'Parent Size Guide:', 'nexio-toolkit' ),
					'menu_name'          => __( 'Size Guide Builder', 'nexio-toolkit' ),
				),
				'hierarchical'        => false,
				'description'         => __( 'To Build Template Size Guide.', 'nexio-toolkit' ),
				'supports'            => array( 'title', 'editor' ),
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => 'nexio_menu',
				'menu_position'       => 3,
				'show_in_nav_menus'   => true,
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'has_archive'         => false,
				'query_var'           => true,
				'can_export'          => true,
				'rewrite'             => false,
				'capability_type'     => 'page',
				'menu_icon'           => 'dashicons-welcome-widgets-menus',
			);
			register_post_type( 'sizeguide', $args ); 
            /* Project */

            $labels = array(
                'name'               => _x( 'Project', 'nexio-toolkit' ),
                'singular_name'      => _x( 'Project', 'nexio-toolkit' ),
                'add_new'            => __( 'Add New', 'nexio-toolkit' ),
                'all_items'          => __( 'Projects', 'nexio-toolkit' ),
                'add_new_item'       => __( 'Add New Project', 'nexio-toolkit' ),
                'edit_item'          => __( 'Edit Project', 'nexio-toolkit' ),
                'new_item'           => __( 'New Project', 'nexio-toolkit' ),
                'view_item'          => __( 'View Project', 'nexio-toolkit' ),
                'search_items'       => __( 'Search Project', 'nexio-toolkit' ),
                'not_found'          => __( 'No Project found', 'nexio-toolkit' ),
                'not_found_in_trash' => __( 'No Project found in Trash', 'nexio-toolkit' ),
                'parent_item_colon'  => __( 'Parent Project', 'nexio-toolkit' ),
                'menu_name'          => __( 'Projects', 'nexio-toolkit' ),
            );
            $args   = array(
                'labels'              => $labels,
                'description'         => 'Post type Project',
                'supports'            => array(
                    'title',
                    'editor',
                    'thumbnail',
                    'excerpt',
                ),
                'hierarchical'        => false,
                'rewrite'             => true,
                'public'              => true,
                'show_ui'             => true,
                'show_in_nav_menus'   => true,
                'show_in_admin_bar'   => true,
                'menu_position'       => 4,
                'can_export'          => true,
                'has_archive'         => true,
                'exclude_from_search' => false,
                'publicly_queryable'  => true,
                'capability_type'     => 'post',
                'menu_icon'           => 'dashicons-images-alt2',
            );

            //register_post_type( 'project', $args );
			
		}
	}

	new Nexio_Toolkit_Posttype();
}
