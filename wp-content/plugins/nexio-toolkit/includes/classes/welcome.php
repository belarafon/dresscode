<?php
if ( ! class_exists( 'Nexio_Welcome' ) ) {
	class Nexio_Welcome {
		
		public $tabs = array();
		public $theme_name;
		
		public function __construct() {
			$this->set_tabs();
			$this->theme_name = wp_get_theme()->get( 'Name' );
			// Add action to enqueue scripts.
			add_action( 'admin_menu', array( $this, 'admin_menu' ), 9 );
		}
		
		public function admin_menu() {
			if ( current_user_can( 'edit_theme_options' ) ) {
				add_menu_page( 'nexio-toolkit', 'Nexio', 'manage_options', 'nexio_menu', array(
					$this,
					'welcome'
				), NEXIO_TOOLKIT_URL . '/assets/images/menu-icon.png', 2 );
				add_submenu_page( 'nexio_menu', 'Nexio Dashboard', 'Dashboard', 'manage_options', 'nexio_menu', array(
					$this,
					'welcome'
				) );
			}
		}
		
		public function set_tabs() {
			$this->tabs = array(
				'dashboard' => esc_html__( 'Welcome', 'nexio-toolkit' ),
				//'demos'     => esc_html__( 'Demo Import', 'nexio-toolkit' ),
				// 'plugins'   => esc_html__( 'Plugins', 'nexio-toolkit' ),
				'support'   => esc_html__( 'Support', 'nexio-toolkit' ),
			);
			
		}
		
		public function active_plugin() {
			if ( empty( $_GET['magic_token'] ) || wp_verify_nonce( $_GET['magic_token'], 'panel-plugins' ) === false ) {
				esc_html_e( 'Permission denied', 'nexio-toolkit' );
				die;
			}
			
			if ( isset( $_GET['plugin_slug'] ) && $_GET['plugin_slug'] != "" ) {
				$plugin_slug = $_GET['plugin_slug'];
				$plugins     = TGM_Plugin_Activation::$instance->plugins;
				foreach ( $plugins as $plugin ) {
					if ( $plugin['slug'] == $plugin_slug ) {
						activate_plugins( $plugin['file_path'] );
						?>
                        <script type="text/javascript">
                            window.location = "admin.php?page=nexio_menu&tab=plugins";
                        </script>
						<?php
						break;
					}
				}
			}
			
		}
		
		public function deactivate_plugin() {
			if ( empty( $_GET['magic_token'] ) || wp_verify_nonce( $_GET['magic_token'], 'panel-plugins' ) === false ) {
				esc_html_e( 'Permission denied', 'nexio-toolkit' );
				die;
			}
			
			if ( isset( $_GET['plugin_slug'] ) && $_GET['plugin_slug'] != "" ) {
				$plugin_slug = $_GET['plugin_slug'];
				$plugins     = TGM_Plugin_Activation::$instance->plugins;
				foreach ( $plugins as $plugin ) {
					if ( $plugin['slug'] == $plugin_slug ) {
						deactivate_plugins( $plugin['file_path'] );
						?>
                        <script type="text/javascript">
                            window.location = "admin.php?page=nexio_menu&tab=plugins";
                        </script>
						<?php
						break;
					}
				}
			}
			
		}
		
		public function intall_plugin() {
		
		}
		
		public function dashboard() {
			// here
			?>
            <div class="dashboard">
                <h1>Welcome to <?php echo ucfirst( esc_html( $this->theme_name ) ); ?></h1>
                <p class="about-text">Thank you for choosing Nexio. This is a great theme for any e-commerce purpose or
                    simply blogging, news. You can easily customize your website without the knowledge of code.</p>
                <div class="dashboard-intro">
                    <div class="image">
                        <img src="<?php echo esc_url( get_theme_file_uri( '/screenshot.jpg' ) ); ?>" alt="nexio">
                    </div>
                    <div class="intro">
                        <p><strong><?php echo ucfirst( esc_html( $this->theme_name ) ); ?></strong> is a modern, clean
                            and professional WooCommerce Wordpress Theme, It
                            is fully responsive, it looks stunning on all types of screens and devices.</p>
                        <h2>Quick Setings</h2>
                        <ul>
                            <li><a href="admin.php?page=sample-data">Install Demos</a></li>
                            <li><a href="admin.php?page=nexio_menu&tab=plugins">Install Plugins</a></li>
                            <li><a href="admin.php?page=nexio-toolkit">Theme Options</a></li>
                        </ul>
                    </div>
                </div>
            </div>
			<?php
			$this->support();
		}
		
		/**
		 * Render HTML of intro tab.
		 *
		 * @return  string
		 */
		
		public function welcome() {
			
			/* deactivate_plugin */
			if ( isset( $_GET['action'] ) && $_GET['action'] == 'deactivate_plugin' ) {
				$this->deactivate_plugin();
			}
			/* deactivate_plugin */
			if ( isset( $_GET['action'] ) && $_GET['action'] == 'active_plugin' ) {
				$this->active_plugin();
			}
			$tab = 'dashboard';
			if ( isset( $_GET['tab'] ) ) {
				$tab = $_GET['tab'];
			}
			?>
            <div class="fami-wrap">
                <div id="tabs-container" role="tabpanel">
                    <div class="nav-tab-wrapper">
						<?php foreach ( $this->tabs as $key => $value ): ?>
                            <a class="nav-tab nexio-nav <?php if ( $tab == $key ): ?> active<?php endif; ?>"
                               href="admin.php?page=nexio_menu&tab=<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value ); ?></a>
						<?php endforeach; ?>
                    </div>
                    <div class="tab-content">
						<?php $this->$tab(); ?>
                    </div>
                </div>
            </div>
			<?php
		}
		
		public static function demos() {
//			if ( class_exists( 'NEXIO_IMPORTER' ) ) {
//				$nexio_importer = new NEXIO_IMPORTER();
//				$nexio_importer->importer_page_content();
//			}
			do_action( 'importer_page_content' );
		}
		
		public static function plugins() {
			$nexio_tgm_theme_plugins = TGM_Plugin_Activation::$instance->plugins;
			$tgm                     = TGM_Plugin_Activation::$instance;
			
			$status_class = "";
			?>
            <div class="plugins rp-row">
				<?php
				$wp_plugin_list = get_plugins();
				foreach ( $nexio_tgm_theme_plugins as $nexio_tgm_theme_plugin ) {
					if ( $tgm->is_plugin_active( $nexio_tgm_theme_plugin['slug'] ) ) {
						$status_class = 'is-active';
						if ( $tgm->does_plugin_have_update( $nexio_tgm_theme_plugin['slug'] ) ) {
							$status_class = 'plugin-update';
						}
					} else if ( isset( $wp_plugin_list[ $nexio_tgm_theme_plugin['file_path'] ] ) ) {
						$status_class = 'plugin-inactive';
					} else {
						$status_class = 'no-intall';
					}
					?>
                    <div class="rp-col">
                        <div class="plugin <?php echo esc_attr( $status_class ); ?>">
                            <div class="preview">
								<?php if ( isset( $nexio_tgm_theme_plugin['image'] ) && $nexio_tgm_theme_plugin['image'] != "" ): ?>
                                    <img src="<?php echo esc_url( $nexio_tgm_theme_plugin['image'] ); ?>" alt="">
								<?php else: ?>
                                    <img src="<?php echo esc_url( get_template_directory_uri() . '/framework/assets/images/no-image.jpg' ); ?>"
                                         alt="">
								<?php endif; ?>
                            </div>
                            <div class="plugin-name">
                                <h3 class="theme-name"><?php echo $nexio_tgm_theme_plugin['name'] ?></h3>
                            </div>
                            <div class="actions">
                                <a class="button button-primary button-install-plugin" href="<?php
								echo esc_url( wp_nonce_url(
									              add_query_arg(
										              array(
											              'page'          => urlencode( TGM_Plugin_Activation::$instance->menu ),
											              'plugin'        => urlencode( $nexio_tgm_theme_plugin['slug'] ),
											              'tgmpa-install' => 'install-plugin',
										              ),
										              admin_url( 'themes.php' )
									              ),
									              'tgmpa-install',
									              'tgmpa-nonce'
								              )
								);
								?>"><?php esc_html_e( 'Install', 'nexio-toolkit' ); ?></a>

                                <a class="button button-primary button-update-plugin" href="<?php
								echo esc_url( wp_nonce_url(
									              add_query_arg(
										              array(
											              'page'         => urlencode( TGM_Plugin_Activation::$instance->menu ),
											              'plugin'       => urlencode( $nexio_tgm_theme_plugin['slug'] ),
											              'tgmpa-update' => 'update-plugin',
										              ),
										              admin_url( 'themes.php' )
									              ),
									              'tgmpa-install',
									              'tgmpa-nonce'
								              )
								);
								?>"><?php esc_html_e( 'Update', 'nexio-toolkit' ); ?></a>

                                <a class="button button-primary button-activate-plugin" href="<?php
								echo esc_url(
									add_query_arg(
										array(
											'page'        => urlencode( 'nexio-toolkit' ),
											'plugin_slug' => urlencode( $nexio_tgm_theme_plugin['slug'] ),
											'action'      => 'active_plugin',
											'magic_token' => wp_create_nonce( 'panel-plugins' ),
										),
										admin_url( 'admin.php' )
									)
								);
								?>"><?php esc_html_e( 'Activate', 'nexio-toolkit' ); ?></a>
                                <a class="button button-secondary button-uninstall-plugin" href="<?php
								echo esc_url(
									add_query_arg(
										array(
											'page'        => urlencode( 'nexio-toolkit' ),
											'plugin_slug' => urlencode( $nexio_tgm_theme_plugin['slug'] ),
											'action'      => 'deactivate_plugin',
											'magic_token' => wp_create_nonce( 'panel-plugins' ),
										),
										admin_url( 'admin.php' )
									)
								);
								?>"><?php esc_html_e( 'Deactivate', 'nexio-toolkit' ); ?></a>
                            </div>
                        </div>
                    </div>
					<?php
				}
				?>
            </div>
			<?php
		}
		
		public static function support() {
			?>
            <div class="rp-row support-tabs">
                <div class="rp-col">
                    <div class="suport-item">
                        <h3><?php esc_html_e( 'Documentation', 'nexio-toolkit' ); ?></h3>
                        <p><?php esc_html_e( 'Here is our user guide for nexio, including basic setup steps, as well as nexio features and elements for your reference.', 'nexio-toolkit' ); ?></p>
                        <a target="_blank" href="http://docs.famithemes.com/docs/nexio/"
                           class="button button-primary"><?php esc_html_e( 'Read Documentation', 'nexio-toolkit' ); ?></a>
                    </div>
                </div>
                <div class="rp-col closed">
                    <div class="suport-item">
                        <h3><?php esc_html_e( 'Video Tutorials', 'nexio-toolkit' ); ?></h3>
                        <p class="coming-soon"><?php esc_html_e( 'Video tutorials is the great way to show you how to setup nexio theme, make sure that the feature works as it\'s designed.', 'nexio-toolkit' ); ?></p>
                        <a href="#"
                           class="button button-primary disabled"><?php esc_html_e( 'See Video', 'nexio-toolkit' ); ?></a>
                    </div>
                </div>
                <div class="rp-col">
                    <div class="suport-item">
                        <h3><?php esc_html_e( 'Support', 'nexio-toolkit' ); ?></h3>
                        <p><?php esc_html_e( 'Can\'t find the solution on documentation? We\'re here to help, even on weekend. Just click here to email us!', 'nexio-toolkit' ); ?></p>
                        <a target="_blank" href="mailto:support@famithemes.com"
                           class="button button-primary"><?php esc_html_e( 'Request Support', 'nexio-toolkit' ); ?></a>
                    </div>
                </div>
            </div>
			
			<?php
		}
	}
	
	new Nexio_Welcome();
}
