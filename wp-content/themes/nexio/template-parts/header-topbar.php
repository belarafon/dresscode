<?php
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$single_id            = nexio_get_single_page_id();
$meta_data            = get_post_meta( $single_id, '_custom_metabox_theme_options', true );
$enable_custom_header = false;
if ( $single_id > 0 && isset( $meta_data['enable_custom_header'] ) ) {
	$enable_custom_header = $meta_data['enable_custom_header'];
}
$enable_topbar = false;
$topbar_text   = '';
if ( $enable_custom_header ) {
	$enable_topbar = isset( $meta_data['enable_topbar'] ) ? $meta_data['enable_topbar'] : '';
	$topbar_text   = isset( $meta_data['topbar-text'] ) ? $meta_data['topbar-text'] : '';
} else {
	$enable_topbar = nexio_get_option( 'enable_topbar', false );
	$topbar_text   = nexio_get_option( 'topbar-text', '' );
}

?>

<?php if ( $enable_topbar && trim( $topbar_text ) != '' ) { ?>
    <div class="header-topbar">
        <div class="header-container container">
            <?php echo esc_attr( $topbar_text ); ?>
            <span class="close-notice flaticon-close"></span>
        </div>
    </div>
	<?php
};