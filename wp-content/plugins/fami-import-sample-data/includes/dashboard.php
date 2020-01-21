<?php
if ( ! class_exists( 'FamiImport_Sample_Data_Dashboard' ) ) {
	class FamiImport_Sample_Data_Dashboard {
		
		/**
		 * Variable to hold the initialization state.
		 *
		 * @var  boolean
		 */
		protected static $initialized = false;
		
		public static function initialize() {
			// Do nothing if pluggable functions already initialized.
			if ( self::$initialized ) {
				return;
			}
			add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) );
			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts' ) );
			
			// State that initialization completed.
			self::$initialized = true;
		}
		
		public static function admin_enqueue_scripts() {
			
			wp_enqueue_style( 'magnific-popup', FAMI_IMPORT_SAMPLE_DATA_PLUGIN_URL . 'assets/3rd-party/magnific-popup/magnific-popup.css', array(), '1.8.0' );
			wp_enqueue_script( 'magnific-popup', FAMI_IMPORT_SAMPLE_DATA_PLUGIN_URL . '/assets/3rd-party/magnific-popup/jquery.magnific-popup.min.js', array( 'jquery' ), '1.8.0', true );
			
			wp_enqueue_style( 'import-sampe-data', FAMI_IMPORT_SAMPLE_DATA_PLUGIN_URL . '/assets/css/style.css' );
			wp_enqueue_script( 'import-sampe-data', FAMI_IMPORT_SAMPLE_DATA_PLUGIN_URL . '/assets/js/functions.js', array( 'jquery' ), '1.0.0', true );
			wp_localize_script( 'import-sampe-data', 'import_sample_data_ajax_admin', array(
				                                       'ajaxurl'               => admin_url( 'admin-ajax.php' ),
				                                       'security'              => wp_create_nonce( 'import_sample_data_ajax_admin' ),
				                                       'install_popup_title'   => esc_html__( 'Install Sample Data', 'fami-import-sample-data' ),
				                                       'uninstall_popup_title' => esc_html__( 'Uninstall Sample Data', 'fami-import-sample-data' ),
				                                       'required_plugins'      => FamiImport_Sample_Data_Settings::plugins(),
			                                       )
			);
		}
		
		public static function admin_menu() {
			$args = array(
				'parent_slug' => 'tools.php',
				'page_title'  => esc_html__( 'Sample Data', 'fami-import-sample-data' ),
				'menu_title'  => esc_html__( 'Sample Data', 'fami-import-sample-data' ),
				'capability'  => 'manage_options',
				'menu_slug'   => 'sample-data',
				'function'    => 'FamiImport_Sample_Data_Dashboard::dashboard'
			);
			$args = apply_filters( 'import_sample_data_menu_args', $args );
			add_submenu_page( $args['parent_slug'], $args['page_title'], $args['menu_title'], $args['capability'], $args['menu_slug'], $args['function'] );
			
		}
		
		public static function dashboard() {
			?>
            <div class="wrap import-sample-data-wrap">
                <h1 class="intro-title">
					<?php esc_html_e( 'Intall Sample Data', 'fami-import-sample-data' ); ?>
                </h1>
				<?php
				$packages                              = FamiImport_Sample_Data_Sample_Data::get_sample_packages();
				$import_sample_data_curent_sample_data = get_option( 'import_sample_data_curent_sample_data' );
				if ( ! empty( $packages ) ) {
					?>
                    <div class="box-wrap three-col">
						<?php foreach ( $packages as $package ): ?>
							<?php
							if ( $package['id'] == $import_sample_data_curent_sample_data ) {
								$install_class   = 'hidden';
								$uninstall_class = '';
							} else {
								$uninstall_class = 'hidden';
								$install_class   = '';
							}
							
							?>
                            <div class="col">
                                <div class="box" id="sample-data-<?php echo esc_attr( $package['id'] ); ?>">
                                    <a target="_blank" href="<?php echo esc_url( $package['demo'] ); ?> ">
                                        <img src="<?php echo esc_url( $package['thumbnail'] ) ?>"
                                             alt="<?php echo esc_attr( $package['name'] ); ?>">
                                    </a>
                                    <div class="box-info">
                                        <h5><?php echo esc_html( $package['name'] ); ?></h5>

                                        <div class="bootom">
                                            <a data-package="<?php echo esc_attr( $package['id'] ); ?>" href="#"
                                               class="button button-primary uninstall-sample <?php echo esc_attr( $uninstall_class ); ?>"><?php esc_html_e( 'Uninstall', 'fami-import-sample-data' ); ?></a>

                                            <a data-package="<?php echo esc_attr( $package['id'] ); ?>" href="#"
                                               class="button button-primary install-sample <?php echo esc_attr( $install_class ); ?>"><?php esc_html_e( 'Install', 'fami-import-sample-data' ); ?></a>

                                        </div>

                                    </div>
                                </div>
                            </div>
						<?php endforeach; ?>
                    </div>
					
					<?php
					
				} else {
					?>
                    <div class="error">
                        <p><?php esc_html_e( 'Failed to get available sample data packages.', 'fami-import-sample-data' ); ?></p>
                    </div>
					<?php
				}
				?>

                <div class="welcome-panel">
                    <h3 style="margin-top: 0;"><?php esc_html_e( 'Export Sample Data', 'fami-import-sample-data' ); ?></h3>
                    <form action="" method="post">
                        <div class="input-text-wrap">
                            <p>
                                <label for="backup_filename"> <?php esc_attr_e( 'Name', 'fami-import-sample-data' ); ?></label>
                                <input type="text" name="backup_filename" id="backup_filename">
                            </p>
                        </div>
                        <input type="submit" class="button"
                               value="<?php esc_attr_e( 'Export', 'fami-import-sample-data' ); ?>">
						<?php echo wp_nonce_field( 'export-sameple-data-form', '_wpnonce', true, false ); ?>
                        <input type="hidden" name="import_sample_data_action" value="export-sample-data">
                    </form>
                </div>
            </div>
			<?php
		}
	}
}