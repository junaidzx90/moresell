<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.fiverr.com/junaidzx90
 * @since      1.0.0
 *
 * @package    MobilityBuy
 * @subpackage MobilityBuy/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    MobilityBuy
 * @subpackage MobilityBuy/includes
 * @author     Md Junayed <admin@easeare.com>
 */
class MobilityBuy_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		$mobilitybuy_site_urls = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mobilitybuy_site_urls ( `ID` INT NOT NULL AUTO_INCREMENT ,
		`site_url` VARCHAR(555) NOT NULL,
		`create_at` DATE NOT NULL,
		PRIMARY KEY (`ID`)) ENGINE = InnoDB";

		dbDelta($mobilitybuy_site_urls);

		$mobilitybuy_published_urls_v1 = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mobilitybuy_published_urls_v1 ( `ID` INT NOT NULL AUTO_INCREMENT ,
		`site_url` VARCHAR(555) NOT NULL,
		`product_id` INT NOT NULL,
		`bonus_rate` INT NOT NULL,
		`create_at` DATE NOT NULL,
		PRIMARY KEY (`ID`)) ENGINE = InnoDB";

		dbDelta($mobilitybuy_published_urls_v1);
	}

}
