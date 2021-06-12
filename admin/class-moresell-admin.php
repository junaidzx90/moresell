<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.fiverr.com/junaidzx90
 * @since      1.0.0
 *
 * @package    Moresell
 * @subpackage Moresell/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Moresell
 * @subpackage Moresell/admin
 * @author     Md Junayed <admin@easeare.com>
 */
class Moresell_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		if(isset($_GET['page']) && $_GET['page'] == 'moresell'){
			wp_enqueue_style('dataTable', plugin_dir_url( __FILE__ ) . 'css/dataTable.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/moresell-admin.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		if(isset($_GET['page']) && $_GET['page'] == 'moresell'){
			wp_enqueue_script( 'dataTable', plugin_dir_url( __FILE__ ) . 'js/dataTable.js', array( 'jquery' ), '', false );
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/moresell-admin.js', array( 'jquery' ), $this->version, false );
			wp_localize_script($this->plugin_name, "moresell_ajaxurl", array(
				'ajaxurl' => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce('ajax-nonce'),
			));
		}
	}

	/**
	 * Column lists for order table
	 */
	function wp_shop_orders_list_table_columnname($defaults){
		unset($defaults['order_date']);
		unset($defaults['order_status']);
		unset($defaults['order_total']);

		$defaults['sold_by'] = 'Sold by';

		$defaults['order_date'] = "Date";
		$defaults['order_status'] = "Status";
		$defaults['order_total'] = "Total";
		return $defaults;
	}


	// Order table column data
	function wp_wc_order_column_view($column_name){
		if ($column_name == 'sold_by') {
			global $woocommerce, $post;
			$order = new WC_Order($post->ID);
			$order_id = $order->get_order_number();
			
			if(get_post_meta( $order_id, 'product_sold_by', true )[0] === $order_id){
				echo '<a href="'.esc_url(get_post_meta( $order_id, 'product_sold_by', true )[1]).'">'.get_post_meta( $order_id, 'product_sold_by', true )[1].'</a>';
			}
		}
	}
	
	// Order create when child site product sold
	function moresell_custom_api_update_product(){
		register_rest_route( 'ms/v1','infocreate',[
			'methods' => 'POST',
			'callback' => [$this,'moresell_requests_infocreate'],
			'permission_callback' => '__return_true'
		]);
	}

	function moresell_requests_infocreate($param){
		add_post_meta( $param['order_id'], 'product_sold_by', [$param['order_id'],$param['sold_by']] );
	}

	// Url add
	function junu_add_url_callback($url){
		if($url !== ""){
			$url = esc_url_raw( $url );
			global $wpdb;
			$junuurltbl = $wpdb->prefix.'moresell_site_urls';

			$output = '';
			$old = $wpdb->get_var("SELECT ID FROM $junuurltbl WHERE site_url = '$url'");
			if($old){
				$output = 'This url is already exist.';
			}else{
				$date = date('d-m-y');
				$insert = $wpdb->insert($junuurltbl, array('site_url' => $url, 'create_at' => $date), array('%s','%s'));
				$lastid = $wpdb->insert_id;
				if($insert){
					$output = 'Added Successfull.';
				}
			}
		}
		return $output;
	}

	// Moresell urls for table
	function moresell_urls_table(){
		global $wpdb;
		$res = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}moresell_site_urls");

		if($res){
			return $res;
		}
	}

	// Moresell get published product count
	function moresell_published_urls($url){
		global $wpdb;
		if(!empty($url)){
			$url = esc_url_raw( $url );
			$res = $wpdb->query("SELECT ID FROM {$wpdb->prefix}moresell_published_urls WHERE site_url = '$url'");

			if($res){
				return $res;
			}else{
				return 0;
			}
		}
	}

	// SEND POST REQUEST
    function send_post_POST_to_json($url, $data){
		if(substr($url , -1)=='/'){
			$url = rtrim($url,"/");
		}
		$url = $url.'/wp-json/ms/v1/create';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$result = curl_exec($ch);
		$response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$res = 'Response code: ' . $response_code;
		curl_close($ch);
        return $result;
    }
	
	// Delete url
	function moresell_delete_url(){
		if(!wp_verify_nonce( $_POST['nonce'], 'ajax-nonce' )){
			die();
		}

		if(isset($_POST['urlid'])){
			$url_id = intval($_POST['urlid']);
			global $wpdb;
			if($wpdb->query("DELETE FROM {$wpdb->prefix}moresell_site_urls WHERE ID = $url_id")){
				echo "Deleted!";
				die;
			}
		}
		die;
	}

	// export_products_to_child
	function export_products_to_child(){
		if(!wp_verify_nonce( $_POST['nonce'], 'ajax-nonce' )){
			die();
		}

		if(!get_option('moresell_consumar_key','') && !get_option('moresell_consumer_secret','')){
			die("Setup your api keys!");
		}
		
		if(isset($_POST['products']) && isset($_POST['url']) && isset($_POST['cat']) && isset($_POST['status'])){
			
			$products = $_POST['products'];
			$urlId = intval($_POST['url']);
			$cat = $_POST['cat'];
			$status = $_POST['status'];
			global $wpdb,$moresell_woocommerce;
			$url = $wpdb->get_var("SELECT site_url FROM {$wpdb->prefix}moresell_site_urls WHERE ID = $urlId");
			
			if(!empty($products)){
				foreach($products as $product){
					$details = $moresell_woocommerce->get("products/$product");
					$imgIDs = new WC_product($product);
					$attachment_ids = $imgIDs->get_gallery_image_ids();
					$images = [];
					foreach($attachment_ids as $id){
						$images[] = wp_get_attachment_url($id);
					}

					$terms = wp_get_post_terms( $product, 'product_tag');
					$product_tags = array();
					if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
						foreach ( $terms as $term ) {
							$product_tags[] = $term->slug;
						}
					}

					$data = [
						'name' => (!empty($details->name)?$details->name:''),
						'type' => (!empty($details->type)?$details->type:''),
						'status' => (!empty($status)?$status:''),
						'attached' => get_the_post_thumbnail_url($product),
						'images' => $images,
						'the_tags' => $product_tags,
						'original_pid' => $product,
						'featured' => (!empty($details->featured)?$details->featured:''),
						'description' => (!empty($details->description)?$details->description:''),
						'short_description' => (!empty($details->short_description)?$details->short_description:''),
						'price' => (!empty($details->price)?$details->price:''),
						'regular_price' => (!empty($details->regular_price)?$details->regular_price:''),
						'sale_price' => (!empty($details->sale_price)?$details->sale_price:''),
						'date_on_sale_from' => (!empty($details->date_on_sale_from)?$details->date_on_sale_from:''),
						'date_on_sale_to' => (!empty($details->date_on_sale_to)?$details->date_on_sale_to:''),
						'purchasable' => (!empty($details->purchasable)?$details->purchasable:''),
						'total_sales' => (!empty($details->total_sales)?$details->total_sales:''),
						'virtual' => (!empty($details->virtual)?$details->virtual:''),
						'downloadable' => (!empty($details->downloadable)?$details->downloadable:''),
						'downloads' => (!empty($details->downloads)?$details->downloads:''),
						'download_limit' => (!empty($details->download_limit)?$details->download_limit:''),
						'download_expiry' => (!empty($details->download_expiry)?$details->download_expiry:''),
						'manage_stock' => (!empty($details->manage_stock)?$details->manage_stock:''),
						'_stock' => get_post_meta( $product, '_stock', true ),
						'dimensions' => (!empty($details->dimensions)?$details->dimensions:''),
						'purchase_note' => (!empty($details->purchase_note)?$details->purchase_note:''),
						'categories' => (!empty($cat)?$cat:''),
						'attributes' => (!empty($details->attributes)?$details->attributes:''),
						'_sku' => get_post_meta($product,'_sku', true),
						'gallery_img' => get_post_meta($product,'_product_image_gallery', true),
						'downloadable_files' => get_post_meta($product,'_downloadable_files', true),
						'product_attributes' => get_post_meta($product,'_product_attributes', true),
						'download_limit' => get_post_meta($product,'_download_limit', true),
					];

					
					$result = $this->send_post_POST_to_json($url,http_build_query($data));
					$published = $wpdb->get_var("SELECT ID FROM {$wpdb->prefix}moresell_published_urls WHERE product_id = $product AND site_url = '$url'");
					if(!$published){
						$wpdb->insert($wpdb->prefix.'moresell_published_urls', array('product_id' => $product, 'site_url' => $url),array('%d','%s'));
					}
				}

				echo json_encode(array('success'=>'Finished'));
				// echo json_encode(array('success'=>$result));
				die;
			}
			
			die;
		}
	}

	public function moresell_menu_register(){
		add_menu_page( 'moresell', 'Moresell', 'manage_options', 'moresell', [$this,'moresell_menupage_callback'], 'dashicons-update-alt', 45 );
	}

	public function moresell_menupage_callback(){
		require_once plugin_dir_path( __FILE__ ).'partials/moresell-admin-display.php';
	}

}
