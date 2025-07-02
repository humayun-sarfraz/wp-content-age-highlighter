<?php
/**
 * Plugin Name: WP Content Age Highlighter
 * Plugin URI:  https://github.com/humayun-sarfraz/wp-content-age-highlighter
 * Description: Flags old or stale posts/pages/products with a customizable badge or warning message.
 * Version:     1.0.0
 * Author:      Humayun Sarfraz
 * Author URI:  https://github.com/humayun-sarfraz
 * Text Domain: wpah
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'WPAH_VERSION', '1.0.0' );
define( 'WPAH_DIR', plugin_dir_path( __FILE__ ) );
define( 'WPAH_URL', plugin_dir_url( __FILE__ ) );

if ( ! function_exists( 'wpah_load_main' ) ) {
    function wpah_load_main() {
        require_once WPAH_DIR . 'includes/class-wpah-main.php';
        require_once WPAH_DIR . 'includes/class-wpah-admin.php';
    }
    add_action( 'plugins_loaded', 'wpah_load_main' );
}
