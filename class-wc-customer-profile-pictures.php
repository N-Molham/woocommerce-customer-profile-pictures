<?php
/**
 * WooCommerce Framework Plugin
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2019, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_1 as Framework;

/**
 * @since 1.0.0
 */
class WC_Customer_Profile_Pictures extends Framework\SV_WC_Plugin {

	/**
	 * @var string
	 */
	public const VERSION = '1.0.0';

	/**
	 * @var string
	 */
	public const PLUGIN_ID = 'framework-plugin';

	/** @var \WC_Customer_Profile_Pictures */
	protected static $instance;


	/**
	 * Gets the main instance of Framework Plugin instance.
	 *
	 * Ensures only one instance is/can be loaded.
	 *
	 * @since 1.0.0
	 *
	 * @return WC_Customer_Profile_Pictures
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

	}

	/**
	 * Gets the full path and filename of the plugin file.
	 *
	 * @since 1.0.0
	 *
	 * @return string the full path and filename of the plugin file
	 */
	protected function get_file(): string {

		return __FILE__;

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

}


/**
 * @since 1.0.0
 */
function wc_customer_profile_pictures() {

	return WC_Customer_Profile_Pictures::instance();

}
