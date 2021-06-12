<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.fiverr.com/junaidzx90
 * @since             1.0.0
 * @package           Moresell
 *
 * @wordpress-plugin
 * Plugin Name:       Moresell
 * Plugin URI:        https://github.com/junaidzx90/moresell
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Md Junayed
 * Author URI:        https://www.fiverr.com/junaidzx90
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       moresell
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'MORESELL_VERSION', '1.0.0' );
define( 'MORESELL_PATH', plugin_dir_path( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-moresell-activator.php
 */
function activate_moresell() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-moresell-activator.php';
	Moresell_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-moresell-deactivator.php
 */
function deactivate_moresell() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-moresell-deactivator.php';
	Moresell_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_moresell' );
register_deactivation_hook( __FILE__, 'deactivate_moresell' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-moresell.php';

require __DIR__ . '/vendor/autoload.php';

use Automattic\WooCommerce\Client;

$moresell_woocommerce = new Client(
    get_home_url(  ), 
    get_option('moresell_consumar_key',''), 
    get_option('moresell_consumer_secret',''),
    [
        'version' => 'wc/v3',
    ]
);

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_moresell() {

	$plugin = new Moresell();
	$plugin->run();

}
run_moresell();
