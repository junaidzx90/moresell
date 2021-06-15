<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.fiverr.com/junaidzx90
 * @since      1.0.0
 *
 * @package    MobilityBuy
 * @subpackage MobilityBuy/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    MobilityBuy
 * @subpackage MobilityBuy/includes
 * @author     Md Junayed <admin@easeare.com>
 */
class MobilityBuy {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      MobilityBuy_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'MOBILITYBUY_VERSION' ) ) {
			$this->version = MOBILITYBUY_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'mobilitybuy';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - MobilityBuy_Loader. Orchestrates the hooks of the plugin.
	 * - MobilityBuy_i18n. Defines internationalization functionality.
	 * - MobilityBuy_Admin. Defines all hooks for the admin area.
	 * - MobilityBuy_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mobilitybuy-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mobilitybuy-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-mobilitybuy-admin.php';

		$this->loader = new MobilityBuy_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the MobilityBuy_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new MobilityBuy_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new MobilityBuy_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		// Menupage register
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'mobilitybuy_menu_register' );
		
		// Add column in order table
		$this->loader->add_action('manage_edit-shop_order_columns',$plugin_admin, 'wp_shop_orders_list_table_columnname');
		$this->loader->add_action('manage_shop_order_posts_custom_column',$plugin_admin,'wp_wc_order_column_view');

		// Delete url
		$this->loader->add_action("wp_ajax_mobilitybuy_delete_url", $plugin_admin, "mobilitybuy_delete_url");
		$this->loader->add_action("wp_ajax_nopriv_mobilitybuy_delete_url", $plugin_admin, "mobilitybuy_delete_url");

		// Export products
		$this->loader->add_action("wp_ajax_export_products_to_child", $plugin_admin, "export_products_to_child");
		$this->loader->add_action("wp_ajax_nopriv_export_products_to_child", $plugin_admin, "export_products_to_child");

		$this->loader->add_action("rest_api_init", $plugin_admin, "mobilitybuy_custom_api_update_product");
		// Update post
		$this->loader->add_action("save_post_product", $plugin_admin, "update_products_to_child");

		// Bonus rate
		// Display Fields
		$this->loader->add_action('woocommerce_product_options_pricing', $plugin_admin, 'woocommerce_mobilitybuy_bocus_rate');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    MobilityBuy_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
