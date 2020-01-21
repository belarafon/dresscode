<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.
// ===============================================================================================
// -----------------------------------------------------------------------------------------------
// TAXONOMY OPTIONS
// -----------------------------------------------------------------------------------------------
// ===============================================================================================

$options     = array();

// -----------------------------------------
// Taxonomy Options                        -
// -----------------------------------------
$options[]   = array(
  'id'       => 'nexio_category_product',
  'taxonomy' => 'product_cat', // category, post_tag or your custom taxonomy name
  'fields'   => array(

    array(
      'id'    => 'cate_icon',
      'type'  => 'icon',
      'title' => 'Category Icon',
    ),
  ),
);

CSFramework_Taxonomy::instance( $options );
