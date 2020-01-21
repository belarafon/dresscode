<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'NexioToolkitTaxonomyImages' ) ) {
	class NexioToolkitTaxonomyOptions {
		public function __construct() {
			
			$support_taxs = self::support_taxonomies();
			if ( ! empty( $support_taxs ) ) {
				foreach ( $support_taxs as $support_tax ) {
					add_action( "{$support_tax}_add_form_fields", array(
						$this,
						'add_cat_img_call_back_function'
					) );
					add_action( "{$support_tax}_edit_form_fields", array(
						$this,
						'edit_cat_img_call_back_function'
					) );
					add_action( "edited_{$support_tax}", array( $this, 'save_cat_img_call_back_function' ), 10, 2 );
					add_action( "created_{$support_tax}", array( $this, 'save_cat_img_call_back_function' ), 10, 2 );
					add_filter( "manage_edit-{$support_tax}_columns", array( $this, 'taxonomy_columns' ) );
					add_filter( "manage_{$support_tax}_custom_column", array(
						$this,
						'taxonomy_columns_content'
					), 10, 3 );
				}
			}
			
			add_shortcode( 'nexio_toolkit_upload_media', array( $this, 'upload_shortcode' ) );
		}
		
		public static function support_taxonomies() {
			$support_taxs = array(
				'product_brand',
			);
			
			return apply_filters( 'nexio_toolkit_support_taxs', $support_taxs );
		}
		
		public static function add_cat_img_call_back_function() {
			?>
            <div class="form-field">
                <label><?php _e( 'Image', 'nexio-toolkit' ); ?></label>
                <input type="hidden" name="tax_image" id="tax_image" value="">
				<?php echo do_shortcode( '[nexio_toolkit_upload_media results_selector="#tax_image"]' ); ?>
                <p class="description"><?php echo sprintf( __( 'FOR DEVELOPER - Use the following function to get the image id: %s' ), '<pre>$tax_img_id = get_term_meta( $term_id, \'tax_image\', true );</pre>' ); ?></p>
            </div>
			<?php
		}
		
		public static function edit_cat_img_call_back_function( $term ) {
			$term_id    = $term->term_id;
			$tax_img_id = get_term_meta( $term_id, 'tax_image', true );
			?>
            <tr class="form-field">
                <th><label for="tax_image"><?php _e( 'Image', 'nexio-toolkit' ); ?></label></th>
                <td>
                    <input style="display: none;" type="hidden" name="tax_image" id="tax_image"
                           value="<?php echo esc_attr( $tax_img_id ) ? esc_attr( $tax_img_id ) : ''; ?>">
					<?php echo do_shortcode( '[nexio_toolkit_upload_media img_ids="' . esc_attr( $tax_img_id ) . '" results_selector="#tax_image"]' ); ?>
                </td>
            </tr>
			<?php
		}
		
		public static function save_cat_img_call_back_function( $term_id ) {
			if ( isset( $_POST['tax_image'] ) ) {
				$term_image = $_POST['tax_image'];
				if ( $term_image ) {
					update_term_meta( $term_id, 'tax_image', $term_image );
				}
			}
		}
		
		public static function taxonomy_columns( $columns ) {
			$new_columns = array();
			$i           = 0;
			foreach ( $columns as $key => $column ) {
				if ( $i == 1 ) {
					$new_columns['nexio_term_img_cols'] = esc_html__( 'Image', 'nexio-toolkit' );
				}
				$new_columns[ $key ] = $column;
				$i ++;
			}
			
			return $new_columns;
		}
		
		public static function taxonomy_columns_content( $content, $column_name, $term_id ) {
			$tax_img_id = get_term_meta( $term_id, 'tax_image', true );
			$tax_image  = nexio_toolkit_resize_image( $tax_img_id, null, 60, 60, false, true, false );
			?>
            <img width="<?php echo esc_attr( $tax_image['width'] ); ?>"
                 height="<?php echo esc_attr( $tax_image['height'] ); ?>"
                 src="<?php echo esc_url( $tax_image['url'] ); ?>"/>
			<?php
			
			return $content;
		}
		
		/**
		 * @param $atts
		 *
		 * @return string
		 */
		public static function upload_shortcode( $atts ) {
			
			extract(
				shortcode_atts(
					array(
						'wrap_class'       => '',
						'btn_class'        => '',
						'img_ids'          => '',
						'multi'            => 'no', // yes, no
						'results_selector' => ''    // Html input selector
					), $atts
				)
			);
			
			$html       = '';
			$wrap_class .= ' ' . uniqid( 'nexio_toolkit-upload-wrap-' );
			$btn_class  .= ' ' . uniqid( 'nexio_toolkit-upload-btn-' );
			$img_ids    = trim( $img_ids );
			
			$btn_text             = esc_html__( 'Select Images', 'nexio-toolkit' );
			$uploader_title       = esc_html__( 'Select Images', 'nexio-toolkit' );
			$uploader_button_text = esc_html__( 'Select', 'nexio-toolkit' );
			
			// check if user can upload files
			if ( current_user_can( 'upload_files' ) ) {
				$imgs_preview_html = '';
				if ( $img_ids != '' ) {
					$img_ids = explode( ',', $img_ids );
					foreach ( $img_ids as $img_id ) {
						$img_full          = nexio_toolkit_resize_image( $img_id, null, 4000, 4000, true, true, false );
						$img               = nexio_toolkit_resize_image( $img_id, null, 150, 150, true, true, false );
						$imgs_preview_html .= '<div class="nexio_toolkit-img-preview-wrap"><img width="' . esc_attr( $img['width'] ) . '" height="' . esc_attr( $img['height'] ) . '" data-attachment_id="' . esc_attr( $img_id ) . '" data-img_full="' . htmlentities2( json_encode( $img_full ) ) . '" class="nexio_toolkit-img-preview nexio_toolkit-img-preview-' . esc_attr( $img_id ) . '" src="' . esc_url( $img['url'] ) . '"> <a href="#" class="remove-img-btn remove-btn">x</a></div>';
					}
				}
				
				$btn_html = '<button data-uploader_title="' . esc_attr( $uploader_title ) . '" data-uploader_button_text="' . esc_attr( $uploader_button_text ) . '" data-results_selector="' . esc_attr( $results_selector ) . '" type="button" class="button btn btn-default nexio_toolkit-upload-btn ' . esc_attr( $btn_class ) . '">' . $btn_text . '</button>';
				$html     = '<div data-multi="' . esc_attr( $multi ) . '" class="nexio_toolkit-upload-wrap ' . esc_attr( $wrap_class ) . '">' .
				            '<div class="nexio_toolkit-main-img-wrap"></div>' .
				            '<div class="nexio_toolkit-imgs-preview-wrap nexio_toolkit-sortable">' . $imgs_preview_html . '</div>'
				            . $btn_html .
				            '</div>';
			}
			
			$html = apply_filters( 'nexio_toolkit_media_frontend_upload', $html, $atts );
			
			return $html;
		}
		
	}
	
	new NexioToolkitTaxonomyOptions();
}