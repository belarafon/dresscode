<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Register Custom Taxonomy
function nexio_toolkit_custom_taxonomies() {
	if ( class_exists( 'WooCommerce' ) ) {
		// Product brand
		$labels = array(
			'name'                       => _x( 'Brands', 'Taxonomy General Name', 'nexio-toolkit' ),
			'singular_name'              => _x( 'Brand', 'Taxonomy Singular Name', 'nexio-toolkit' ),
			'menu_name'                  => __( 'Brands', 'nexio-toolkit' ),
			'all_items'                  => __( 'All Brands', 'nexio-toolkit' ),
			'parent_item'                => __( 'Parent Brand', 'nexio-toolkit' ),
			'parent_item_colon'          => __( 'Parent Brand:', 'nexio-toolkit' ),
			'new_item_name'              => __( 'New Brand Name', 'nexio-toolkit' ),
			'add_new_item'               => __( 'Add New Brand', 'nexio-toolkit' ),
			'edit_item'                  => __( 'Edit Brand', 'nexio-toolkit' ),
			'update_item'                => __( 'Update Brand', 'nexio-toolkit' ),
			'view_item'                  => __( 'View Brand', 'nexio-toolkit' ),
			'separate_items_with_commas' => __( 'Separate brands with commas', 'nexio-toolkit' ),
			'add_or_remove_items'        => __( 'Add or remove brands', 'nexio-toolkit' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'nexio-toolkit' ),
			'popular_items'              => __( 'Popular Brands', 'nexio-toolkit' ),
			'search_items'               => __( 'Search Brands', 'nexio-toolkit' ),
			'not_found'                  => __( 'Not Found', 'nexio-toolkit' ),
			'no_terms'                   => __( 'No brands', 'nexio-toolkit' ),
			'items_list'                 => __( 'Brands list', 'nexio-toolkit' ),
			'items_list_navigation'      => __( 'Brands list navigation', 'nexio-toolkit' ),
		);
		$args   = array(
			'labels'            => $labels,
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => true,
		);
		register_taxonomy( 'product_brand', array( 'product' ), $args );
	}
}

add_action( 'init', 'nexio_toolkit_custom_taxonomies', 0 );