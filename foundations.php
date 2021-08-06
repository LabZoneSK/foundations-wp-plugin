<?php

/**
 * The plugin bootstrap file
 *
 * @link              https://labzone.tech/foundations
 * @since             1.0.0
 * @package           Foundations
 *
 * @wordpress-plugin
 * Plugin Name:       Foundations
 * Plugin URI:        https://labzone.tech/foundations
 * Description:       Plugins adds features to donate part of the product price to foundation (charity).
 * Version:           1.0.0
 * Author:            Martin Starosta <LabZone>
 * Author URI:        https://labzone.tech
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       foundations
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'FOUNDATIONS_VERSION', '1.0.0' );

/** Composer Autoload */
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function activate_foundations() {
	/*
	 require_once plugin_dir_path( __FILE__ ) . 'includes/class-foundation-activator.php';
	Plugin_Name_Activator::activate(); */
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function deactivate_foundations() {
	/*
	 require_once plugin_dir_path( __FILE__ ) . 'includes/class-foundation-deactivator.php';
	Plugin_Name_Deactivator::deactivate(); */
}

register_activation_hook( __FILE__, 'activate_foundations' );
register_deactivation_hook( __FILE__, 'deactivate_foundations' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-foundations.php';

if ( ! function_exists( 'foundations_run' ) ) {
	/**
	 * Begins execution of the plugin.
	 *
	 * Since everything within the plugin is registered via hooks,
	 * then kicking off the plugin from this point in the file does
	 * not affect the page life cycle.
	 *
	 * @since    1.0.0
	 */
	function foundations_run() {
		$plugin = new Foundations();
		$plugin->run();
	}
}
foundations_run();