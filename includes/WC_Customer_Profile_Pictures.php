<?php
/**
 * WooCommerce Customer Profile Picture Plugin
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * @author    Nabeel Molham
 * @copyright Copyright (c) 2014-2019, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace Nabeel_Molham\WooCommerce\WC_Customer_Profile_Pictures;

defined( 'ABSPATH' ) or exit;

use Nabeel_Molham\WooCommerce\WC_Customer_Profile_Pictures\REST_API\WC_Customer_Profile_Pictures_REST_Controller;
use SkyVerge\WooCommerce\PluginFramework\v5_5_1\SV_WC_Plugin;
use WP_Comment;
use WP_User;

/**
 * @since 1.0.0
 */
class WC_Customer_Profile_Pictures extends SV_WC_Plugin {

	/**
	 * @var string
	 */
	public const VERSION = '1.0.0';

	/**
	 * @var string
	 */
	public const PLUGIN_ID = 'framework-plugin';

	/** @var static */
	protected static $instance;

	/**
	 * @var WC_Customer_Profile_Pictures_Account_Settings
	 */
	protected $_account_settings;

	/**
	 * @var WC_Customer_Profile_Pictures_Settings
	 */
	protected $_plugin_settings;

	/**
	 * @var WC_Customer_Profile_Pictures_User_Edit
	 */
	protected $_edit_user;

	/**
	 * @var WC_Customer_Profile_Pictures_Orders
	 */
	protected $_orders;

	/**
	 * @var WC_Customer_Profile_Pictures_REST_Controller
	 */
	protected $_rest_controller;

	/**
	 * Gets the main instance of Framework Plugin instance.
	 *
	 * Ensures only one instance is/can be loaded.
	 *
	 * @since 1.0.0
	 *
	 * @return static
	 */
	public static function instance(): WC_Customer_Profile_Pictures {

		if ( null === self::$instance ) {

			self::$instance = new self();

		}

		return self::$instance;

	}

	/**
	 * Constructs the plugin.
	 *
	 * @since 1.0
	 */
	public function __construct() {

		parent::__construct( self::PLUGIN_ID, self::VERSION, [
			'text_domain' => 'woocommerce-customer-profile-pictures',
		] );

		add_action( 'woocommerce_init', [ $this, 'woocommerce_init' ] );

	}

	/**
	 * Initialize plugin components when WooCommerce is ready
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function woocommerce_init(): void {

		$this->_plugin_settings  = new WC_Customer_Profile_Pictures_Settings();
		$this->_account_settings = new WC_Customer_Profile_Pictures_Account_Settings();
		$this->_edit_user        = new WC_Customer_Profile_Pictures_User_Edit();
		$this->_orders           = new WC_Customer_Profile_Pictures_Orders();

		add_filter( 'pre_get_avatar_data', [ $this, 'override_avatar_with_active_profile_picture' ], 10, 2 );

		add_action( 'rest_api_init', [ $this, 'register_rest_api_endpoint' ], 20 );

	}

	/**
	 * Register custom REST API endpoint
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_rest_api_endpoint(): void {

		$this->_rest_controller = new WC_Customer_Profile_Pictures_REST_Controller();
		$this->_rest_controller->register_routes();

	}

	/**
	 * Overriding User's Avatar URL with the current active profile picture for him/her
	 *
	 * @param array      $args
	 * @param int|string $id_or_email
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function override_avatar_with_active_profile_picture( $args, $id_or_email ): array {

		if ( is_numeric( $id_or_email ) ) {

			$user = get_user_by( 'ID', $id_or_email );

		} elseif ( $id_or_email instanceof WP_Comment ) {

			$user = get_user_by( 'email', $id_or_email->user_id );

		} elseif ( $id_or_email instanceof WP_User ) {

			$user = $id_or_email;

		} else {

			$user = get_user_by( 'email', $id_or_email );

		}

		if ( false === $user ) {

			return $args;

		}

		$active_profile_picture = $this->_account_settings->get_customer_active_profile_picture( 'all', $user->ID );

		if ( empty( $active_profile_picture ) ) {

			return $args;

		}

		$wanted_size_url  = $this->generate_size_file_path( $args['size'], $active_profile_picture['url'] );
		$wanted_size_path = $this->generate_size_file_path( $args['size'], $active_profile_picture['file'] );

		if ( file_exists( $wanted_size_path ) ) {

			return $this->return_avatar_args( $wanted_size_url, $args );

		}

		$image_editor = wp_get_image_editor( $active_profile_picture['file'] );

		// error getting the editor to serve the wanted size, fallback to full size
		if ( is_wp_error( $image_editor ) ) {

			return $this->return_avatar_args( $active_profile_picture['ur'], $args );

		}

		$resize = $image_editor->resize( $args['width'], $args['height'] );

		if ( is_wp_error( $resize ) ) {

			return $this->return_avatar_args( $active_profile_picture['ur'], $args );

		}

		$save_new_size = $image_editor->save( $wanted_size_path );

		if ( is_wp_error( $save_new_size ) ) {

			return $this->return_avatar_args( $active_profile_picture['ur'], $args );

		}

		return $this->return_avatar_args( $wanted_size_url, $args );

	}

	/**
	 * Get customer's profile picture URL based on given size, and fallback to full size if not found
	 *
	 * @param array  $profile_picture
	 * @param string $image_size
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_profile_picture_size_url( $profile_picture, $image_size = '' ): string {

		if ( '' === $image_size || empty( $image_size ) ) {

			return $profile_picture['url'];

		}

		$profile_picture_url = $profile_picture['url'];

		if ( file_exists( $this->generate_size_file_path( $image_size, $profile_picture['file'] ) ) ) {

			$profile_picture_url = $this->generate_size_file_path( $image_size, $profile_picture['url'] );

		}

		return $profile_picture_url;

	}

	/**
	 * Builds an output URL or Path based on given URl or Path, and adding proper suffix
	 *
	 * @param string $suffix
	 * @param string $url
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function generate_size_file_path( $suffix, $url ): string {

		$url_dir = pathinfo( $url, PATHINFO_DIRNAME );
		$url_ext = pathinfo( $url, PATHINFO_EXTENSION );
		$name    = wp_basename( $url, ".$url_ext" );

		return trailingslashit( $url_dir ) . "{$name}-{$suffix}.{$url_ext}";

	}

	/**
	 * @param string $url
	 * @param array  $args
	 *
	 * @return array
	 */
	protected function return_avatar_args( $url, $args ): array {

		$args['url'] = $url;

		return $args;

	}

	/**
	 * @since 1.0.0
	 *
	 * @return WC_Customer_Profile_Pictures_Orders
	 */
	public function get_orders_instance(): WC_Customer_Profile_Pictures_Orders {

		return $this->_orders;

	}

	/**
	 * Gets the full path and filename of the plugin file.
	 *
	 * @since 1.0.0
	 *
	 * @return string the full path and filename of the plugin file
	 */
	protected function get_file(): string {

		return str_replace( '/includes', '/woocommerce-customer-profile-pictures.php', __DIR__ );

	}

	/**
	 * Gets the plugin full name
	 *
	 * @since 1.0.0
	 *
	 * @return string plugin name
	 */
	public function get_plugin_name(): string {

		return __( 'WooCommerce Customer Profile Pictures', 'woocommerce-customer-profile-pictures' );

	}

	/**
	 * @return WC_Customer_Profile_Pictures_Settings
	 */
	public function get_plugin_settings_instance(): WC_Customer_Profile_Pictures_Settings {

		return $this->_plugin_settings;

	}

	/**
	 * @return WC_Customer_Profile_Pictures_Account_Settings
	 */
	public function get_account_settings_instance(): WC_Customer_Profile_Pictures_Account_Settings {

		return $this->_account_settings;

	}

	/**
	 * @return WC_Customer_Profile_Pictures_User_Edit
	 */
	public function get_user_edit_instance(): WC_Customer_Profile_Pictures_User_Edit {

		return $this->_edit_user;

	}

	/**
	 * @return WC_Customer_Profile_Pictures_REST_Controller
	 */
	public function get_rest_controller_instance(): WC_Customer_Profile_Pictures_REST_Controller {

		return $this->_rest_controller;

	}

}
