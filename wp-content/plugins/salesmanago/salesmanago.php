<?php
/**
 * Plugin Name: SALESmanago
 * Plugin URI:  https://www.salesmanago.pl
 * Description: SALESmanago Marketing Automation integration for Wordpress, WooCommerce, Contact Form 7, Gravity Forms
 * Version:     2.6.0
 * Author:      SALESmanago
 * Author URI:  https://www.salesmanago.pl
 * License:     License: GPL2
 */

//avoid direct calls to this file, because now WP core and framework has been used
if (!function_exists('add_filter')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

const
    SM_TEST_MODE = false,
    SM_DEBUG_MODE = false;

require_once 'vendor/autoload.php';

use bhr\Admin\Admin;
use bhr\Helper\HooksFiltersManager as HFM;

new Admin(
    plugin_dir_path(__FILE__) . 'src/',
    plugin_dir_url(__FILE__) . 'src/'
);

HFM::doAction('salesmanago_init');
