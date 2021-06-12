<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.fiverr.com/junaidzx90
 * @since      1.0.0
 *
 * @package    Moresell
 * @subpackage Moresell/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Moresell
 * @subpackage Moresell/includes
 * @author     Md Junayed <admin@easeare.com>
 */
class Moresell_Activator {

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

		$moresell_site_urls = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}moresell_site_urls ( `ID` INT NOT NULL AUTO_INCREMENT ,
		`site_url` VARCHAR(555) NOT NULL,
		`create_at` DATE NOT NULL,
		PRIMARY KEY (`ID`)) ENGINE = InnoDB";

		dbDelta($moresell_site_urls);

		$moresell_published_urls = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}moresell_published_urls ( `ID` INT NOT NULL AUTO_INCREMENT ,
		`site_url` VARCHAR(555) NOT NULL,
		`product_id` INT NOT NULL,
		`create_at` DATE NOT NULL,
		PRIMARY KEY (`ID`)) ENGINE = InnoDB";

		dbDelta($moresell_published_urls);
	}

}
