<?php
/*
  Register all actions and filters for the plugin
 */

require("class-morkvaup-plugin-callbacks.php");

class MUP_Plugin_Loader {

	public $tdb = MUP_TABLEDB;
	/**
	 * The array of pages for plugin menu
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var array $pages 	Pages for plugin menu
	 */
	protected $pages;

	/**
	 * The array of subpages for plugin menu
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var array $subpages 	Subpages for plugin menu
	 */
	protected $subpages;

	/**
	 * Array of settings groups fields for plugin
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var array $settings
	 */
	protected $settings;

	/**
	 * Array of sections for settings fields for plugin
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var array $sections
	 */
	protected $sections;

	/**
	 * Array of fields for settings fields for plugin
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var array $fields
	 */
	protected $fields;

	/**
	 * The array of actions registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $actions    The actions registered with WordPress to fire when the plugin loads.
	 */
	protected $actions;
	/**
	 * The array of filters registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $filters    The filters registered with WordPress to fire when the plugin loads.
	 */
	protected $filters;

	/**
	 * Object of callbacks class
	 *
	 * @since 	1.0.0
	 * @access  protected
	 * @var 	string $callbacks 		Class of callbacks
	 */
	protected $callbacks;

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		global $wp_settings_sections;
		$this->actions = array();
		$this->filters = array();
		$this->pages = array();
		$this->subpages = array();
		$this->settings = array();
		$this->sections = array();
		$this->fields = array();

		$this->callbacks = new MUP_Plugin_Callbacks();

		$this->add_settings_fields();
		$this->register_fields_sections();
		$this->register_settings_fields();

		$this->register_menu_pages();
		$this->register_menu_subpages();

		add_action( 'admin_menu', array( $this, 'register_plugin_menu' ) );
		add_action( 'add_meta_boxes', array( $this, 'mv_add_meta_boxes' ) );
		add_action( 'admin_init', array( $this, 'register_plugin_settings' ));
		add_filter( 'manage_edit-shop_order_columns', array( $this, 'woo_custom_column' ) );
		add_action( 'manage_shop_order_posts_custom_column', array( $this, 'woo_column_get_data' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_invoice_meta_box' ) );

		add_filter( 'wp_mail_from_name', array( $this, 'my_mail_from_name' ) );
		add_filter( 'plugins_api', array( $this, 'addmorkvaupApi' ), 20, 3 );
	}
	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string               $hook             The name of the WordPress action that is being registered.
	 * @param    object               $component        A reference to the instance of the object on which the action is defined.
	 * @param    string               $callback         The name of the function definition on the $component.
	 * @param    int                  $priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param    int                  $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 */

	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}
	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string               $hook             The name of the WordPress filter that is being registered.
	 * @param    object               $component        A reference to the instance of the object on which the filter is defined.
	 * @param    string               $callback         The name of the function definition on the $component.
	 * @param    int                  $priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param    int                  $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1
	 */
	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}
	/**
	 * A utility function that is used to register the actions and hooks into a single
	 * collection.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    array                $hooks            The collection of hooks that is being registered (that is, actions or filters).
	 * @param    string               $hook             The name of the WordPress filter that is being registered.
	 * @param    object               $component        A reference to the instance of the object on which the filter is defined.
	 * @param    string               $callback         The name of the function definition on the $component.
	 * @param    int                  $priority         The priority at which the function should be fired.
	 * @param    int                  $accepted_args    The number of arguments that should be passed to the $callback.
	 * @return   array                                  The collection of actions and filters registered with WordPress.
	 */
	private function add( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {
		$hooks[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args
		);
		return $hooks;
	}
	/**
	 * Register the filters and actions with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		foreach ( $this->filters as $hook ) {
			add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}
		foreach ( $this->actions as $hook ) {
			add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}
	}

	/**
	 * Registering plugin pages to menu
	 *
	 * @since 	1.0.0
	 */
	public function register_menu_pages()
	{
		$this->pages = array(
			array(
				'page_title' => MUP_PLUGIN_NAME,
				'menu_title' => 'UkrPoshta ',
				'capability' => 'manage_woocommerce',
				'menu_slug' => 'morkvaup_plugin',
				'callback' => array($this, 'add_settings_page'),
				'icon_url' => plugins_url("icon.svg", __FILE__),
				'position' => 60
			)
		);

		return $this;
	}

	/**
	 *	Add Plugin Settings page
	 *
	 *	@since 	1.0.0
	 */
	public function add_settings_page()
	{

		require_once( MUP_PLUGIN_PATH . '/public/partials/morkvaup-plugin-settings.php');
	}

	/**
	 * Registering subpages for menu of plugin
	 *
	 * @since 	1.0.0
	 */
	public function register_menu_subpages()
	{
		$title = "Налаштування";

		$this->subpages = array(
			array(
				'parent_slug' 	=> 'morkvaup_plugin',
				'page_title' 	=> 'Налаштування',
				'menu_title' 	=> 'Налаштування',
				'capability' 	=> 'manage_woocommerce',
				'menu_slug' 	=> 'morkvaup_plugin',
				'callback' 		=> array( $this, 'add_settings_page' )
			),
			array(
				'parent_slug' 	=> 'morkvaup_plugin',
				'page_title' 	=> 'Створити відправлення',
				'menu_title' 	=> 'Створити відправлення',
				'capability' 	=> 'manage_woocommerce',
				'menu_slug' 	=> 'morkvaup_invoice',
				'callback' 		=>  array( $this, 'add_invoice_page' )
			),
			array(
				'parent_slug' 	=> 'morkvaup_plugin',
				'page_title' 	=> 'Мої відправлення',
				'menu_title'	=> 'Мої відправлення',
				'capability'	=> 'manage_woocommerce',
				'menu_slug'		=> 'morkvaup_invoices',
				'callback'		=> array( $this, 'invoices_page' )
			),
			array(
				'parent_slug'	=> 'morkvaup_plugin',
				'page_title'	=> 'Про плагін',
				'menu_title'	=> 'Про плагін',
				'capability'	=> 'manage_woocommerce',
				'menu_slug'		=> 'morkvaup_about',
				'callback'		=> array( $this, 'about_page' )
			)
		);

		return $this;
	}

	/**
	 * Adding subpage of plugin
	 *
	 * @since 1.0.0
	 */
	public function add_invoice_page()
	{
		require_once( MUP_PLUGIN_PATH . 'public/partials/morkvaup-plugin-form.php');
	}

	/**
	 * Add invoices subpage of plugin
	 *
	 * @since 1.0.0
	 */
	public function invoices_page()
	{
		$path = MUP_PLUGIN_PATH . 'public/partials/morkvaup-plugin-invoices-page.php';
		if(file_exists($path)){
		require_once( $path );
		}
		else{
			$path = MUP_PLUGIN_PATH . 'public/partials/morkvaup-plugin-invoices-page-demo.php';
			require_once( $path );

		}
	}

	/**
	 * Add about page of plugin
	 *
	 * @since 1.0.0
	 */
	public function about_page()
	{
		echo file_get_contents( MUP_PLUGIN_PATH . 'public/partials/morkvaup-plugin-about-page.php');
		//require_once( get_home_path() . 'wp-content/plugins/nova-poshta-ttn-pro/public/partials/morkvaup-plugin-about-page.php' );
	}

	/**
	 * Register plugin menu
	 *
	 * @since 	1.0.0
	 */
	public function register_plugin_menu()
	{
		foreach ($this->pages as $page) {
			add_menu_page( $page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['callback'], $page['icon_url'], $page['position'] );
		}

		foreach ($this->subpages as $subpage ) {
			add_submenu_page( $subpage['parent_slug'], $subpage['page_title'], $subpage['menu_title'], $subpage['capability'], $subpage['menu_slug'], $subpage['callback'] );
		}
	}

	/**
	 * Add setting fields for plugin
	 *
	 * @since 	1.0.0
	 */
	public function add_settings_fields()
	{
		$args = array(
			array(
				'option_group' => 'morkvaup_options_group',
				'option_name' => 'production_bearer_ecom'
			),
			array(
				'option_group' => 'morkvaup_options_group',
				'option_name' => 'production_bearer_status_tracking'
			),
			array(
				'option_group' => 'morkvaup_options_group',
				'option_name' => 'production_cp_token'
			),
			array(
				'option_group' => 'morkvaup_options_group',
				'option_name' => 'proptype'
			),

			array(
				'option_group' => 'morkvaup_options_group',
				'option_name' => 'sendtype'
			),
			array(
				'option_group' => 'morkvaup_options_group',
				'option_name' => 'senduptype'
			),

			array(
				'option_group' => 'morkvaup_options_group',
				'option_name' => 'sendwtype'
			),

			array(
				'option_group' => 'morkvaup_options_group',
				'option_name' => 'names1'
			),

			array(
				'option_group' => 'morkvaup_options_group',
				'option_name' => 'nameslatin'
			),

			array(
				'option_group' => 'morkvaup_options_group',
				'option_name' => 'streetlatin'
			),

			array(
				'option_group' => 'morkvaup_options_group',
				'option_name' => 'numlatin'
			),

			array(
				'option_group' => 'morkvaup_options_group',
				'option_name' => 'citylatin'
			),

			array(
				'option_group' => 'morkvaup_options_group',
				'option_name' => 'names2'
			),
			array(
				'option_group' => 'morkvaup_options_group',
				'option_name' => 'names3'
			),

			array(
				'option_group' => 'morkvaup_options_group',
				'option_name' => 'activate_plugin'
			),
			array(
				'option_group' => 'morkvaup_options_group',
				'option_name' => 'phone'
			),
			array(
				'option_group' => 'morkvaup_options_group',
				'option_name' => 'flat'
			),
			array(
				'option_group' => 'morkvaup_options_group',
				'option_name' => 'warehouse'
			),
			array(
				'option_group' => 'morkvaup_options_group',
				'option_name' => 'invoice_description'
			),
		/*	array(
				'option_group' => 'morkvaup_options_group',
				'option_name' => 'morkvaup_email_template'
			),
			array(
				'option_group' => 'morkvaup_options_group',
				'option_name' => 'morkvaup_email_subject'
			)
			*/
		);

		$this->settings = $args;

		return $this;
	}

	/**
	 *	Register all sections for settings fields
	 *
	 *	@since 	 1.0.0
	 */
	public function register_fields_sections()
	{
		$args = array(
			array(
				'id' => 'morkvaup_admin_index',
				'title' => 'Налаштування',
				'callback' => function() { echo ""; },
				'page' => 'morkvaup_plugin'
			)
		);

		$this->sections = $args;

		return $this;
	}

	/**
	 * Register settings callbacks fields
	 *
	 * @since 	1.0.0
	 */
	public function register_settings_fields()
	{
		$args = array(
			array(
				'id' => 'activate_plugin',
				'title' => 'Активувати плагін?',
				'callback' => array( $this->callbacks, 'morkvaupActivate' ),
				'page' => 'morkvaup_plugin',
				'section' => 'morkvaup_admin_index',
				'args' => array(
					'label_for' => 'activate_plugin'
				)
			),
			array(
				'id' => 'production_bearer_ecom',
				'title' => 'PRODUCTION BEARER eCom - Authorization Bearer',
				'callback' => array( $this->callbacks, 'morkvaupAuthBearer' ),
				'page' => 'morkvaup_plugin',
				'section' => 'morkvaup_admin_index',
				'args' => array(
					'label_for' => 'production_bearer_ecom',
					'class' => 'example-class'
				)
			),
			array(
				'id' => 'production_bearer_status_tracking',
				'title' => 'PRODUCTION BEARER StatusTracking - Tracking Bearer ',
				'callback' => array( $this->callbacks, 'morkvaupProdBearer' ),
				'page' => 'morkvaup_plugin',
				'section' => 'morkvaup_admin_index',
				'args' => array(
					'label_for' => 'production_bearer_status_tracking',
					'class' => 'example-class'
				)
			),
			array(
				'id' => 'production_cp_token',
				'title' => 'PROD_COUNTERPARTY TOKEN - User token ',
				'callback' => array( $this->callbacks, 'morkvaupCpToken' ),
				'page' => 'morkvaup_plugin',
				'section' => 'morkvaup_admin_index',
				'args' => array(
					'label_for' => 'production_cp_token',
					'class' => 'example-class'
				)
			),
			array(
				'id' => 'production_cp_token',
				'title' => 'PROD_COUNTERPARTY TOKEN - User token ',
				'callback' => array( $this->callbacks, 'morkvaupCpToken' ),
				'page' => 'morkvaup_plugin',
				'section' => 'morkvaup_admin_index',
				'args' => array(
					'label_for' => 'production_cp_token',
					'class' => 'example-class'
				)
			),
			array(
				'id' => 'proptype',
				'title' => 'Формат друку наклейки',
				'callback' => array( $this->callbacks, 'morkvaupprinttype' ),
				'page' => 'morkvaup_plugin',
				'section' => 'morkvaup_admin_index',
				'args' => array(
					'label_for' => 'proptype',
					'class' => 'example-class'
				)
			),
			array(
				'id' => 'sendtype',
				'title' => 'Тип відправки по замовчуванню',
				'callback' => array( $this->callbacks, 'morkvaupsendtype' ),
				'page' => 'morkvaup_plugin',
				'section' => 'morkvaup_admin_index',
				'args' => array(
					'label_for' => 'sendtype',
					'class' => 'example-class'
				)
			),

			array(
				'id' => 'senduptype',
				'title' => 'Тип упаковки відправлення по замовчуванню',
				'callback' => array( $this->callbacks, 'morkvaupsenduptype' ),
				'page' => 'morkvaup_plugin',
				'section' => 'morkvaup_admin_index',
				'args' => array(
					'label_for' => 'senduptype',
					'class' => 'example-class'
				)
			),
			array(
				'id' => 'sendwtype',
				'title' => 'Тип доставки по замовчуванню',
				'callback' => array( $this->callbacks, 'morkvaupsendwtype' ),
				'page' => 'morkvaup_plugin',
				'section' => 'morkvaup_admin_index',
				'args' => array(
					'label_for' => 'sendwtype',
					'class' => 'example-class'
				)
			),
			array(
				'id' => 'names1',
				'title' => 'Прізвище Відправника',
				'callback' => array( $this->callbacks, 'morkvaupNames' ),
				'page' => 'morkvaup_plugin',
				'section' => 'morkvaup_admin_index',
				'args' => array(
					'label_for' => 'names1',
					'class' => 'names1'
				)
			),

			array(
				'id' => 'names2',
				'title' => 'Ім\'я Відправника',
				'callback' => array( $this->callbacks, 'morkvaupNames2' ),
				'page' => 'morkvaup_plugin',
				'section' => 'morkvaup_admin_index',
				'args' => array(
					'label_for' => 'names2',
					'class' => 'names2'
				)
			),

			array(
				'id' => 'names3',
				'title' => 'По-батькові Відправника',
				'callback' => array( $this->callbacks, 'morkvaupNames3' ),
				'page' => 'morkvaup_plugin',
				'section' => 'morkvaup_admin_index',
				'args' => array(
					'label_for' => 'names3',
					'class' => 'names3'
				)
			),
			array(
				'id' => 'nameslatin',
				'title' => 'Повне імя Відправника',
				'callback' => array( $this->callbacks, 'morkvaupNamesLatin' ),
				'page' => 'morkvaup_plugin',
				'section' => 'morkvaup_admin_index',
				'args' => array(
					'label_for' => 'nameslatin',
					'class' => 'nameslatin'
				)
			),

			array(
				'id' => 'streetlatin',
				'title' => 'Вулиця Відправника латиницею',
				'callback' => array( $this->callbacks, 'morkvaupStreetLatin' ),
				'page' => 'morkvaup_plugin',
				'section' => 'morkvaup_admin_index',
				'args' => array(
					'label_for' => 'nameslatin',
					'class' => 'nameslatin'
				)
			),

			array(
				'id' => 'numlatin',
				'title' => 'Номер будинку Відправника',
				'callback' => array( $this->callbacks, 'morkvaupNumLatin' ),
				'page' => 'morkvaup_plugin',
				'section' => 'morkvaup_admin_index',
				'args' => array(
					'label_for' => 'nameslatin',
					'class' => 'nameslatin'
				)
			),
			array(
				'id' => 'citylatin',
				'title' => 'Місто латиницею',
				'callback' => array( $this->callbacks, 'morkvaupCityLatin' ),
				'page' => 'morkvaup_plugin',
				'section' => 'morkvaup_admin_index',
				'args' => array(
					'label_for' => 'citylatin',
					'class' => 'citylatin'
				)
			),
			array(
				'id' => 'phone',
				'title' => 'Номер телефону',
				'callback' => array( $this->callbacks, 'morkvaupPhone' ),
				'page' => 'morkvaup_plugin',
				'section' => 'morkvaup_admin_index',
				'args' => array(
					'label_for' => 'phone',
					'class' => 'phone'
				)
			),


			array(
				'id' => 'warehouse',
				'title' => 'Індекс',
				'callback' => array( $this->callbacks, 'morkvaupWarehouseAddress'),
				'page' => 'morkvaup_plugin',
				'section' => 'morkvaup_admin_index',
				'args' => array(
					'label_for' => 'warehouse',
					'class' => 'warehouse'
				)
			),
			array(
				'id' => 'invoice_description',
				'title' => 'Опис відправлення (по замовчуванню)',
				'callback' => array( $this->callbacks, 'morkvaupInvoiceDescription' ),
				'page' => 'morkvaup_plugin',
				'section' => 'morkvaup_admin_index',
				'args' => array(
					'label_for' => 'invoice_description',
					'class' => 'invoice_description'
				)
			),
/*
			array(
				'id' => 'morkvaup_email_subject',
				'title' => 'Шаблон заголовку email повідомлення',
				'callback' => array( $this->callbacks, 'morkvaupEmailSubject' ),
				'page' => 'morkvaup_plugin',
				'section' => 'morkvaup_admin_index',
				'args' => array(
					'label_for' => 'morkvaup_email_subject',
					'class' => 'morkvaup_email_subject'
				)
			),
			array(
				'id' => 'invoice_email_template',
				'title' => 'Шаблон email',
				'callback' => array( $this->callbacks, 'morkvaupEmailTemplate' ),
				'page' => 'morkvaup_plugin',
				'section' => 'morkvaup_admin_index',
				'args' => array(
					'label_for' => 'invoice_date',
					'class' => 'invoice_date'
				)
			)
			*/
		);

		$this->fields = $args;

		return $this;
	}

	/**
	 *	Registering all settings fields for plugin
	 *
	 *	@since 	 1.0.0
	 */
	public function register_plugin_settings()
	{
		foreach ($this->settings as $setting) {
			register_setting( $setting["option_group"], $setting["option_name"], ( isset( $setting["callback"] ) ? $setting["callback"] : '' ) );
		}

		foreach ($this->sections as $section) {
			add_settings_section( $section["id"], $section["title"], ( isset( $section["callback"] ) ? $section["callback"] : '' ), $section["page"] );
		}

		foreach ($this->fields as $field) {
			add_settings_field( $field["id"], $field["title"], ( isset( $field["callback"] ) ? $field["callback"] : '' ), $field["page"], $field["section"], ( isset( $field["args"] ) ? $field["args"] : '' ) );
		}
	}

	/**
	 * Add meta box to WooCommerce order's page
	 *
	 * @since 1.0.0
	 */
	public function add_plugin_meta_box()
	{
		if(!isset($_SESSION)) 
	    { 
	        session_start(); 
	    } 

        if ( isset($_GET["post"]) ) { $order_id = $_GET["post"]; }
        if ( isset($order_id) ) {
            $order_data = wc_get_order( $order_id );
            $order = $order_data->get_data();
            $_SESSION['order_data'] = $order;
            $_SESSION['order_id'] = $order_id;
        }

        echo "<img src='". MUP_PLUGIN_URL ."/includes/icon.svg' style='width: 20px;margin-right: 20px;'/>";
        echo "<a class='button button-primary send' href='admin.php?page=morkvaup_invoice'>Нове відправлення</a>";
				echo "<script src=". MUP_PLUGIN_URL . 'public/js/script.js'."></script>";
				echo "<link href=". MUP_PLUGIN_URL . 'public/css/style.css'."/>";
	}

	/**
	 * Generating meta box
	 *
	 * @since 1.0.0
	 */
	public function mv_add_meta_boxes()
    {
        add_meta_box( 'mvup_other_fields', __('Відправлення укрпошта','woocommerce'), array( $this, 'add_plugin_meta_box' ), 'shop_order', 'side', 'core' );
    }

    /**
     * Creating custom column at woocommerce order page
     *
     * @since 1.1.0
     */
    public function woo_custom_column( $columns )
    {
    	$columns['created_invoice'] = 'Відправлення';
    	$columns['invoice_number'] = 'Номер Відправлення';
    	return $columns;
    }

    /**
     * Getting data of order column at order page
     *
     * @since 1.1.0
     */
    public function woo_column_get_data( $column ) {
    	global $post;
			$tdb = MUP_TABLEDB;
    	$data = get_post_meta( $post->ID );

    	if ( $column == 'created_invoice' ) {
    		global $wpdb;

    		$order_id = $post->ID;
    		$results = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}{$tdb} WHERE order_id = '$order_id'", ARRAY_A );

    		if ( !empty($results) ) {
    			$img = "/logo1.svg";
    			echo '<img height=25 src="' . site_url() . '/wp-content/plugins/' . plugin_basename( __DIR__ ) . $img . '" />';
    		} else {
    			$img = '/logo2.svg';
    			echo '<img height=25 src="' . site_url() . '/wp-content/plugins/' . plugin_basename( __DIR__ ) . $img . '" />';
    		}
    	}

    	if ( $column == 'invoice_number' ) {
    		global $wpdb;

    		$order_id = $post->ID;
				$query = "SELECT * FROM {$wpdb->prefix}".$tdb." WHERE order_id = '$order_id'";
    		$number_result = $wpdb->get_row( $query , ARRAY_A );

    		if ( $number_result ) {
    			echo $number_result["order_invoice"];
    		} else {
    			echo "-";
    		}
    	}
    }

    /**
     * Add meta box with invoice information
     *
     * @since 1.1.0
     */
    public function add_invoice_meta_box()
    {
    	if ( isset($_GET["post"]) ) {
    		add_meta_box( 'invoice_other_fields', __('Відправлення','woocommerce'), array( $this, 'invoice_meta_box_info' ), 'shop_order', 'side', 'core' );
    	}

    }

    /**
     * Add info of invoice meta box
     *
     * @since 1.1.0
     */
    public function invoice_meta_box_info()
    {
			$tdb = MUP_TABLEDB;

    	if ( isset($_GET["post"]) ) { $order_id = $_GET["post"]; }

    	global $wpdb;

    	$result = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}{$tdb} WHERE order_id = '$order_id'", ARRAY_A );
    	$invoice_number = null;
    	if(isset($result[0]['order_invoice'])){
    		$invoice_number = $result[0]['order_invoice'];
    	}



    	$selected_order = wc_get_order( $order_id );
		$order = $selected_order->get_data();
		$invoice_email = $order['billing']['email'];

    	if ( ! empty( $result ) ) {
    		echo 'Номер Відправлення: ' . $result[0]['order_invoice'];
    		//echo '<a style="margin: 5px;" href="https://my.novaposhta.ua/orders/printDocument/orders[]/' .  $invoice_number . '/type/pdf/apiKey/' .  $api_key . '" class="button" target="_blank">' . '<img src="' . plugins_url('img/004-printer.png', __FILE__) . '" height="15" width="15" />' . ' Друк Відправлення</a>';
    		//echo '<a style="margin: 5px;" href="https://my.novaposhta.ua/orders/printMarkings/orders[]/' . $invoice_number . '/type/pdf/apiKey/' . $api_key . '" class="button" target="_blank">' . '<img src="' . plugins_url('img/003-barcode.png', __FILE__) . '" height="15" width="15"  />' . ' Друк стікера</a>';




		$methodProperties = array(
			"Documents" => array(
				array(
					"DocumentNumber" => $invoice_number
					),
				)
		);


			// var_dump( $obj['Number'] );

			?>
			<?php

    	} else {
    		echo 'Номер відправлення не встановлено: -';
    	}

    }


	/**
	 * From name email
	 *
	 * @since 1.1.3
	 */
	public function my_mail_from_name( $name ) {
		$bloginfo = get_bloginfo();
		$title = $bloginfo->name;

    	return get_option('blogname');
	}

	/**
	 * Add morkvaup plugin API for updating plugin
	 *
	 * @since 1.2.2
	 * @return void
	 */
	public function addmorkvaupApi( $res, $action, $args )
	{
		if( $action !== 'plugin_information' ) {
			return false;
		}

		// do nothing if it is not our plugin
		if( 'morkvaup_PLUGIN' !== $args->slug ) {
			return $res;
		}

			// trying to get from cache first, to disable cache comment 18,28,29,30,32
		if( false == $remote = get_transient( 'morkva_upgrade_morkvaup_PLUGIN' ) ) {

				// info.json is the file with the actual plugin information on your server
				/*$remote = wp_remote_get( 'https://YOUR_WEBSITE/SOME_PATH/info.json', array(
				'timeout' => 10,
					'headers' => array(
						'Accept' => 'application/json'
					) )
				);*/
				$remote = json_encode( array(
					"name" => "Nova Poshta TTN Pro",
					"slug" => "nova-poshta-ttn-pro",
					"version" => "1.2.2",
					"requires" => "7.3",
					"author" => "<a href='https://morkva.co.ua'>MORKVA</a>",
					"author_profile" => "https://morkva.co.ua",
					"download_url"=> "https://wordpress.org/plugins/nova-poshta-ttn/",
					"sections" => array(
						"description" => "Плагін допомагає автоматизувати процес відправки ваших замовлень через Нову Пошту. На сторінці замовлення можна згенерувати відправлення із даних, які вносив покупець при оформленні. Ви просто приходите на відділення і кажете номер відправлення менеджеру."
					)
				) );

				if ( !is_wp_error( $remote ) && isset( $remote['response']['code'] ) && $remote['response']['code'] == 200 && !empty( $remote['body'] ) ) {
					set_transient( 'morkva_upgrade_morkvaup_PLUGIN', $remote, 43200 ); // 12 hours cache
				}

		}

		if( $remote ) {

		$remote = json_decode( $remote['body'] );
			$res = new stdClass();
			$res->name = $remote->name;
			$res->slug = 'morkvaup_PLUGIN';
			$res->version = $remote->version;
			// $res->tested = $remote->tested;
			$res->requires = $remote->requires;
			$res->author = '<a href="https//mokrva.co.ua">MORKVA</a>'; // I decided to write it directly in the plugin
			$res->author_profile = 'https://morkva.co.ua'; // WordPress.org profile
			$res->download_link = $remote->download_url;
			// $res->trunk = $remote->download_url;
			// $res->last_updated = $remote->last_updated;
			$res->sections = array(
				'description' => $remote->sections->description, // description tab
				// 'installation' => $remote->sections->installation, // installation tab
				// 'changelog' => $remote->sections->changelog, // changelog tab
				// you can add your custom sections (tabs) here
			);

			// in case you want the screenshots tab, use the following HTML format for its content:
			// <ol><li><a href="IMG_URL" target="_blank"><img src="IMG_URL" alt="CAPTION" /></a><p>CAPTION</p></li></ol>
			if( !empty( $remote->sections->screenshots ) ) {
				$res->sections['screenshots'] = $remote->sections->screenshots;
			}

			$res->banners = array(
				'low' => 'https://YOUR_WEBSITE/banner-772x250.jpg',
            	'high' => 'https://YOUR_WEBSITE/banner-1544x500.jpg'
			);

        	return $res;

		}

		return false;

	}

	/**
	 * Update plugin of MORKVA
	 *
	 * @return void
	 */
	public function morkvaup_update_plugin( $transient )
	{

		if ( empty($transient->checked ) ) {
            return $transient;
        }

	// trying to get from cache first, to disable cache comment 10,20,21,22,24
	if( false == $remote = get_transient( 'morkva_update_morkvaup-plugin' ) ) {

		// info.json is the file with the actual plugin information on your server
		/*$remote = wp_remote_get( 'https://YOUR_WEBSITE/SOME_PATH/info.json', array(
			'timeout' => 10,
			'headers' => array(
				'Accept' => 'application/json'
			) )
		);

		if ( !is_wp_error( $remote ) && isset( $remote['response']['code'] ) && $remote['response']['code'] == 200 && !empty( $remote['body'] ) ) {
			set_transient( 'morkva_upgrade_morkvaup-plugin', $remote, 43200 ); // 12 hours cache
		}*/

	}

	$remote = json_encode( array(
		"name" => "Nova Poshta TTN Pro",
		"slug" => "nova-poshta-ttn-pro",
		"version" => "1.2.2",
		"requires" => "7.3",
		"author" => "<a href='https://morkva.co.ua'>MORKVA</a>",
		"author_profile" => "https://morkva.co.ua",
		"download_url"=> "https://wordpress.org/plugins/nova-poshta-ttn/",
		"sections" => array(
			"description" => "Плагін допомагає автоматизувати процес відправки ваших замовлень через Нову Пошту. На сторінці замовлення можна згенерувати відправлення із даних, які вносив покупець при оформленні. Ви просто приходите на відділення і кажете номер відправлення менеджеру."
		)
	) );

	if( $remote ) {

		$remote = json_decode( $remote['body'] );

		// your installed plugin version should be on the line below! You can obtain it dynamically of course
			if( $remote && version_compare( '1.0', $remote->version, '<' ) && version_compare($remote->requires, get_bloginfo('version'), '<' ) ) {
				$res = new stdClass();
				$res->slug = 'morkvaup-plugin';
				$res->plugin = 'nova-poshta-ttn-pro/morkvaup-plugin.php'; // it could be just YOUR_PLUGIN_SLUG.php if your plugin doesn't have its own directory
				$res->new_version = $remote->version;
				// $res->tested = $remote->tested;
				$res->package = $remote->download_url;
				// $res->url = $remote->homepage;
           		$transient->response[$res->plugin] = $res;
           		//$transient->checked[$res->plugin] = $remote->version;
           	}

		}

        return $transient;

	}


}
