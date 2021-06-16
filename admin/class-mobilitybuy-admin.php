<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.fiverr.com/junaidzx90
 * @since      1.0.0
 *
 * @package    MobilityBuy
 * @subpackage MobilityBuy/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    MobilityBuy
 * @subpackage MobilityBuy/admin
 * @author     Md Junayed <admin@easeare.com>
 */
class MobilityBuy_Admin {

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
		add_filter( 'wp_dropdown_cats', [$this,'wp_dropdown_cats_multiple'], 10, 2 );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		if(isset($_GET['page']) && $_GET['page'] == 'mobilitybuy'){
			wp_enqueue_style('dataTable', plugin_dir_url( __FILE__ ) . 'css/dataTable.css', array(), microtime(), 'all' );
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mobilitybuy-admin.css', array(), microtime(), 'all' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		if(isset($_GET['page']) && $_GET['page'] == 'mobilitybuy'){
			wp_enqueue_script( 'dataTable', plugin_dir_url( __FILE__ ) . 'js/dataTable.js', array( 'jquery' ), '', false );
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/mobilitybuy-admin.js', array( 'jquery' ), $this->version, false );
			wp_localize_script($this->plugin_name, "mobilitybuy_ajaxurl", array(
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
	function mobilitybuy_custom_api_update_product(){
		register_rest_route( 'ms/v1','infocreate',[
			'methods' => 'POST',
			'callback' => [$this,'mobilitybuy_requests_infocreate'],
			'permission_callback' => '__return_true'
		]);
	}

	function mobilitybuy_requests_infocreate($param){
		add_post_meta( $param['order_id'], 'product_sold_by', [$param['order_id'],$param['sold_by']] );
	}

	function wp_dropdown_cats_multiple( $output, $r ) {
                        
		if( isset( $r['multiple'] ) && $r['multiple'] ) {
	
			 $output = preg_replace( '/^<select/i', '<select multiple', $output );
	
			$output = str_replace( "name='{$r['name']}'", "name='{$r['name']}[]'", $output );
	
			foreach ( array_map( 'trim', explode( ",", $r['selected'] ) ) as $value )
				$output = str_replace( "value=\"{$value}\"", "value=\"{$value}\" selected", $output );
	
		}
	
		return $output;
	}
	// Url add
	function junu_add_url_callback($url){
		if($url !== ""){
			$url = esc_url_raw( $url );
			global $wpdb;
			$junuurltbl = $wpdb->prefix.'mobilitybuy_site_urls';

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

	// MobilityBuy urls for table
	function mobilitybuy_urls_table(){
		global $wpdb;
		$res = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}mobilitybuy_site_urls");

		if($res){
			return $res;
		}
	}

	// MobilityBuy get published product count
	function mobilitybuy_published_urls_v1($url){
		global $wpdb;
		if(!empty($url)){
			$url = esc_url_raw( $url );
			$res = $wpdb->query("SELECT ID FROM {$wpdb->prefix}mobilitybuy_published_urls_v1 WHERE site_url = '$url'");

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
	function mobilitybuy_delete_url(){
		if(!wp_verify_nonce( $_POST['nonce'], 'ajax-nonce' )){
			die();
		}

		if(isset($_POST['urlid'])){
			$url_id = intval($_POST['urlid']);
			global $wpdb;
			if($wpdb->query("DELETE FROM {$wpdb->prefix}mobilitybuy_site_urls WHERE ID = $url_id")){
				echo "Deleted!";
				die;
			}
		}
		die;
	}

	
	function woocommerce_mobilitybuy_bocus_rate(){
		global $woocommerce, $post;
		echo '<div class="_bocus_rate_field">';
		echo '<p>If you want to change <strong>Bonus rate</strong> for child site, you need to update manually one by one.</p>';
		echo '</div>';
	}

	// Update post
	function update_products_to_child($meta_id, $post_id, $meta_key='', $meta_value=''){
		if(!get_option('mobilitybuy_consumar_key','') && !get_option('mobilitybuy_consumer_secret','')){
			die("Setup your api keys!");
		}

		global $wpdb,$mobilitybuy_woocommerce;
		
		$published_sites = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}mobilitybuy_published_urls_v1 WHERE `product_id` = {$post_id}");

		if($published_sites){
			foreach($published_sites as $site){
				$taxonomy = 'product_cat';
				
				$terms = get_the_terms( $post_id, $taxonomy);
				$cats_list = $terms;

				$status = get_post_status($post_id);
				$url = $site->site_url;
				$bonus_rate = $site->bonus_rate;

				$details = $mobilitybuy_woocommerce->get("products/$post_id");
				$imgIDs = new WC_product($post_id);
				$attachment_ids = $imgIDs->get_gallery_image_ids();
				$images = [];
				foreach($attachment_ids as $id){
					$images[] = wp_get_attachment_url($id);
				}

				$terms = wp_get_post_terms( $post_id, 'product_tag');
				$product_tags = array();
				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
					foreach ( $terms as $term ) {
						$product_tags[] = $term->slug;
					}
				}

				$price = (!empty($details->price)?$details->price:'');
				$price = intval($price) + (intval($price) / 100) * intval($bonus_rate);

				$regular_price = (!empty($details->regular_price)?$details->regular_price:'');
				$regular_price = intval($regular_price) + (intval($regular_price) / 100) * intval($bonus_rate);

				$sale_price = (!empty($details->sale_price)?$details->sale_price:'');
				$sale_price = intval($sale_price) + (intval($sale_price) / 100) * intval($bonus_rate);

				$data = [
					'name' => (!empty($details->name)?$details->name:''),
					'type' => (!empty($details->type)?$details->type:''),
					'status' => (!empty($status)?$status:''),
					'attached' => get_the_post_thumbnail_url($post_id),
					'images' => $images,
					'the_tags' => $product_tags,
					'original_pid' => $post_id,
					'featured' => (!empty($details->featured)?$details->featured:''),
					'description' => (!empty($details->description)?$details->description:''),
					'short_description' => (!empty($details->short_description)?$details->short_description:''),
					'price' => $price,
					'regular_price' => $regular_price,
					'sale_price' => $sale_price,
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
					'_stock' => get_post_meta( $post_id, '_stock', true ),
					'_height' => get_post_meta($post_id,'_height', true),
					'_width' => get_post_meta($post_id,'_width', true),
					'_weight' => get_post_meta($post_id,'_weight', true),
					'_length' => get_post_meta($post_id,'_length', true),
					'purchase_note' => (!empty($details->purchase_note)?$details->purchase_note:''),
					'categories' => (!empty($cats_list)?$cats_list:''),
					'attributes' => (!empty($details->attributes)?$details->attributes:''),
					'_sku' => get_post_meta($post_id,'_sku', true),
					'gallery_img' => get_post_meta($post_id,'_product_image_gallery', true),
					'downloadable_files' => get_post_meta($post_id,'_downloadable_files', true),
					'product_attributes' => get_post_meta($post_id,'_product_attributes', true),
					'download_limit' => get_post_meta($post_id,'_download_limit', true),
				];
				
				$results = $this->send_post_POST_to_json($url,http_build_query($data));
				$publishedurls = $wpdb->get_var("SELECT ID FROM {$wpdb->prefix}mobilitybuy_published_urls_v1 WHERE `product_id` = $post_id AND `site_url` = '$url'");
				if(!$publishedurls){
					$wpdb->insert($wpdb->prefix.'mobilitybuy_published_urls_v1', array('product_id' => $post_id, 'site_url' => $url,'bonus_rate'=> $bonus_rate,'create_at' => date('Y-m-d')),array('%d','%s','%d','%s'));
				}else{
					$wpdb->update($wpdb->prefix.'mobilitybuy_published_urls_v1', array('product_id' => $post_id, 'site_url' => $url,'bonus_rate'=> $bonus_rate,'create_at' => date('Y-m-d')),array('product_id' => $post_id,'site_url' => $url),array('%d','%s','%d','%s'),array('%d','%s'));
				}
			}
			
			return true;
		}
	}

	// export_products_to_child
	function export_products_to_child(){
		if(!wp_verify_nonce( $_POST['nonce'], 'ajax-nonce' )){
			die();
		}

		if(!get_option('mobilitybuy_consumar_key','') && !get_option('mobilitybuy_consumer_secret','')){
			die("Setup your api keys!");
		}
		
		if(isset($_POST['products']) && isset($_POST['url']) && isset($_POST['status'])){
			
			$products = $_POST['products'];
			$urlId = intval($_POST['url']);
			$status = $_POST['status'];
			global $wpdb,$mobilitybuy_woocommerce;
			$url = $wpdb->get_var("SELECT site_url FROM {$wpdb->prefix}mobilitybuy_site_urls WHERE ID = $urlId");
			
			if(!empty($products)){
				foreach($products as $product){
					$bonusratee = $wpdb->get_var("SELECT bonus_rate FROM {$wpdb->prefix}mobilitybuy_published_urls_v1 WHERE product_id = {$product}");
					
					$bonus_rate = '';
					if(!empty($_POST['bonus_rate'])){
						$bonus_rate = $_POST['bonus_rate'];
					}else{
						if($bonusratee){
							$bonus_rate = $bonusratee;
						}
					}


					$details = $mobilitybuy_woocommerce->get("products/$product");
					$imgIDs = new WC_product($product);
					$attachment_ids = $imgIDs->get_gallery_image_ids();
					$images = [];
					foreach($attachment_ids as $id){
						$images[] = wp_get_attachment_url($id);
					}
					$taxonomy = 'product_cat';
					$terms = get_the_terms( $product, $taxonomy);
					$cats_list = $terms;

					$terms = wp_get_post_terms( $product, 'product_tag');
					$product_tags = array();
					if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
						foreach ( $terms as $term ) {
							$product_tags[] = $term->slug;
						}
					}

					$price = (!empty($details->price)?$details->price:'');
    				$price = $price + (intval($price) / 100) * intval($bonus_rate);

					$regular_price = (!empty($details->regular_price)?$details->regular_price:'');
    				$regular_price = $regular_price + (intval($regular_price) / 100) * intval($bonus_rate);

					$sale_price = (!empty($details->sale_price)?$details->sale_price:'');
    				$sale_price = $sale_price + (intval($sale_price) / 100) * intval($bonus_rate);

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
						'price' => $price,
						'regular_price' => $regular_price,
						'sale_price' => $sale_price,
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
						'_height' => get_post_meta($product,'_height', true),
						'_width' => get_post_meta($product,'_width', true),
						'_weight' => get_post_meta($product,'_weight', true),
						'_length' => get_post_meta($product,'_length', true),
						'purchase_note' => (!empty($details->purchase_note)?$details->purchase_note:''),
						'categories' => (!empty($cats_list)?$cats_list:''),
						'attributes' => (!empty($details->attributes)?$details->attributes:''),
						'_sku' => get_post_meta($product,'_sku', true),
						'gallery_img' => get_post_meta($product,'_product_image_gallery', true),
						'downloadable_files' => get_post_meta($product,'_downloadable_files', true),
						'product_attributes' => get_post_meta($product,'_product_attributes', true),
						'download_limit' => get_post_meta($product,'_download_limit', true),
					];
					
					$result = $this->send_post_POST_to_json($url,http_build_query($data));
					$published = $wpdb->get_var("SELECT ID FROM {$wpdb->prefix}mobilitybuy_published_urls_v1 WHERE `product_id` = $product AND `site_url` = '$url'");
					if(!$published){
						$wpdb->insert($wpdb->prefix.'mobilitybuy_published_urls_v1', array('product_id' => $product, 'site_url' => $url,'bonus_rate'=> $bonus_rate,'create_at' => date('Y-m-d')),array('%d','%s','%d','%s'));
					}else{
						$wpdb->update($wpdb->prefix.'mobilitybuy_published_urls_v1', array('product_id' => $product, 'site_url' => $url,'bonus_rate'=> $bonus_rate,'create_at' => date('Y-m-d')),array('product_id' => $product,'site_url' => $url),array('%d','%s','%d','%s'),array('%d','%s'));
					}
				}

				echo json_encode(array('success'=>'Finished'));
				die;
			}
			
			die;
		}
	}

	public function mobilitybuy_menu_register(){
		add_menu_page( 'mobilitybuy', 'MobilityBuy', 'manage_options', 'mobilitybuy', [$this,'mobilitybuy_menupage_callback'], 'dashicons-update-alt', 45 );
	}

	public function mobilitybuy_menupage_callback(){
		require_once plugin_dir_path( __FILE__ ).'partials/mobilitybuy-admin-display.php';
	}

}
