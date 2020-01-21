<?php

if ( ! class_exists( 'FamiImportSampleSettings' ) ) {
	class FamiImportSampleSettings {
		
		public function __construct() {
			
			// Filter Sample Data Menu
			add_filter( 'import_sample_data_menu_args', array( $this, 'import_sample_data_menu_args' ) );
			add_filter( 'import_sample_data_packages', array( $this, 'import_sample_data_packages' ) );
			add_filter( 'import_sample_data_required_plugins', array( $this, 'import_sample_data_required_plugins' ) );
			add_filter( 'import_sample_data_demo_site_pattern', array(
				$this,
				'import_sample_data_demo_site_pattern'
			) );
			add_filter( 'import_sample_data_theme_option_key', array( $this, 'import_sample_data_theme_option_key' ) );
			
			add_action( 'import_sample_data_after_install_sample_data', array(
				$this,
				'import_sample_data_after_install_sample_data'
			), 10, 1 );
		}
		
		public function import_sample_data_demo_site_pattern( $demo_site_pattern ) {
			
			$demo_site_pattern = 'https?(%3A|:)[%2F\\\\/]+(rc|demo|theme_slug)\.famithemes\.com';
			
			return $demo_site_pattern;
		}
		
		public function import_sample_data_theme_option_key( $theme_option_key ) {
			$theme_option_key = 'envy';
			
			return $theme_option_key;
		}
		
		public function import_sample_data_required_plugins( $plugins ) {
			$theme_plugins_uri = get_template_directory_uri() . '/framework/plugins/';
			$plugins           = array(
				array(
					'name'        => 'WPBakery Visual Composer', // The plugin name
					'slug'        => 'js_composer', // The plugin slug (typically the folder name)
					'source'      => $theme_plugins_uri . 'js_composer.zip',
					'source_type' => 'external',
					'file_path'   => 'js_composer/js_composer.php',
				),
				array(
					'name'        => 'Redux Framework',
					'slug'        => 'redux-framework',
					'required'    => true,
					'file_path'   => 'redux-framework/redux-framework.php',
					'source_type' => 'repo', // Plugins On wordpress.org
				),
			);
			
			return $plugins;
		}
		
		/**
		 * Change Menu Sample dataß.
		 *
		 * @param   array $uri Remote URI for fetching content.
		 *
		 * @return  array
		 */
		public function import_sample_data_menu_args( $args ) {
			
			$args = array(
				'parent_slug' => 'cendigi_menu',
				'page_title'  => esc_html__( 'Sample Data', 'fami-import-sample-data' ),
				'menu_title'  => esc_html__( 'Sample Data', 'fami-import-sample-data' ),
				'capability'  => 'manage_options',
				'menu_slug'   => 'sample-data',
				'function'    => 'FamiImport_Sample_Data_Dashboard::dashboard'
			);
			
			return $args;
		}
		
		public function import_sample_data_packages( $packages ) {
			return array(
				'main' => array(
					'id'          => 'main',
					'name'        => 'Theme Name',
					'thumbnail'    => 'https://via.placeholder.com/400x200',
					'demo'        => 'https://theme_slug.famithemes.com',
					'download'    => 'https://localhost/theme_slug/framework/import-data/data/theme_slug-data.zip',
					'tags'        => array( 'all', 'simple' ),
					'main'        => true,
					'sample-page' => array(
						array(
							'name'     => 'Home I',
							'slug'     => 'home-1',
							'thumbnail' => 'https://via.placeholder.com/180x130',
							'settings' => array(
								'used_header' => 'style-1',
								'footer_used' => 2934,
							)
						),
						array(
							'name'     => 'Home II',
							'slug'     => 'home-2',
							'thumbnail' => 'https://via.placeholder.com/180x130',
							'settings' => array(
								'used_header' => 'default',
								'footer_used' => 541,
							)
						),
					)
				),
				//and more...
			);
		}
		
		public function import_sample_data_after_install_sample_data( $package ) {
			// Do something here!
		}
	}
}

new FamiImportSampleSettings();