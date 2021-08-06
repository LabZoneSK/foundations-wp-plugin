<?php
/**
 * The settings page for the plugin.
 *
 * @link       http://labzone.tech/foundations
 * @since      1.0.0
 *
 * @package    Foundations
 * @subpackage Foundations/admin
 */

use Carbon_Fields\Container;
use Carbon_Fields\Field;

/**
 * The settings page for the plugin.
 *
 * @since      1.0.0
 * @package    Foundations
 * @subpackage Foundations/admin
 * @author     Martin Starosta <info@labzone.sk>
 */
class Foundations_Settings {

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
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Create settings page for the foundation plugin.
	 *
	 * @since    1.0.0
	 */
	public function add_settings_page() {
		Container::make( 'theme_options', __( 'Settings', 'foundations' ) )
		->set_page_parent( 'edit.php?post_type=foundations' )
		->add_fields(
			array(
				Field::make( 'checkbox', 'foundations_show_when_zero', __( 'Show empty messages when no contribution', 'foundations' ) )
					->set_option_value( 'yes' )
			)
		);
	}
}
