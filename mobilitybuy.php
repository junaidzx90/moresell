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
 * @package           MobilityBuy
 *
 * @wordpress-plugin
 * Plugin Name:       MobilityBuy
 * Plugin URI:        https://github.com/junaidzx90/mobilitybuy
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Md Junayed
 * Author URI:        https://www.fiverr.com/junaidzx90
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       mobilitybuy
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
define( 'MOBILITYBUY_VERSION', '1.0.0' );
define( 'MOBILITYBUY_PATH', plugin_dir_path( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-mobilitybuy-activator.php
 */
function activate_mobilitybuy() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mobilitybuy-activator.php';
	MobilityBuy_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-mobilitybuy-deactivator.php
 */
function deactivate_mobilitybuy() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mobilitybuy-deactivator.php';
	MobilityBuy_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_mobilitybuy' );
register_deactivation_hook( __FILE__, 'deactivate_mobilitybuy' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-mobilitybuy.php';

require __DIR__ . '/vendor/autoload.php';

use Automattic\WooCommerce\Client;

$mobilitybuy_woocommerce = new Client(
    get_home_url(  ), 
    get_option('mobilitybuy_consumar_key',''), 
    get_option('mobilitybuy_consumer_secret',''),
    [
        'version' => 'wc/v3',
    ]
);
/** Walker_Category_Checklist class */
require_once ABSPATH . 'wp-admin/includes/class-walker-category-checklist.php';
class Junu_Category_Checklist extends Walker_Category_Checklist{

    function start_el(&$output, $category, $depth = 0, $args = array(), $id = 0) {

        if ( empty( $args['taxonomy'] ) ) {
            $taxonomy = 'category';
        } else {
            $taxonomy = $args['taxonomy'];
        }
     
        if ( 'category' === $taxonomy ) {
            $name = 'post_category';
        } else {
            $name = 'tax_input[' . $taxonomy . ']';
        }
     
        $args['popular_cats'] = ! empty( $args['popular_cats'] ) ? array_map( 'intval', $args['popular_cats'] ) : array();
     
        $class = in_array( $category->term_id, $args['popular_cats'], true ) ? ' class="popular-category"' : '';
     
        $args['selected_cats'] = ! empty( $args['selected_cats'] ) ? array_map( 'intval', $args['selected_cats'] ) : array();
     
        if ( ! empty( $args['list_only'] ) ) {
            $aria_checked = 'false';
            $inner_class  = 'category';
     
            if ( in_array( $category->term_id, $args['selected_cats'], true ) ) {
                $inner_class .= ' selected';
                $aria_checked = 'true';
            }
     
            $output .= "\n" . '<li' . $class . '>' .
                '<div class="' . $inner_class . '" data-term-id=' . $category->term_id .
                ' tabindex="0" role="checkbox" aria-checked="' . $aria_checked . '">' .
                /** This filter is documented in wp-includes/category-template.php */
                esc_html( apply_filters( 'the_category', $category->name, '', '' ) ) . '</div>';
        } else {
            $is_selected = in_array( $category->term_id, $args['selected_cats'], true );
            $is_disabled = ! empty( $args['disabled'] );
            $output .= '<ul class="customcatshow">';
            $output .= "\n<li id='{$taxonomy}-{$category->term_id}'$class>" .
                '<label class="selectit"><input value="' . $category->term_id . '" type="checkbox" name="' . $name . '[]" catname="'.$category->name.'" parent="'.$category->parent.'" id="in-' . $taxonomy . '-' . $category->term_id . '"' .
                checked( $is_selected, true, false ) .
                disabled( $is_disabled, true, false ) . ' /> ' .
                /** This filter is documented in wp-includes/category-template.php */
                esc_html( apply_filters( 'the_category', $category->name, '', '' ) ) . '</label>';
            $output .= '</ul>';
        }
	}
}
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_mobilitybuy() {

	$plugin = new MobilityBuy();
	$plugin->run();

}
run_mobilitybuy();
