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

use SkyVerge\WooCommerce\PluginFramework\v5_5_1\SV_WC_Plugin;

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

}
