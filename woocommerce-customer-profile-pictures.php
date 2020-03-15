<?php
/**
 * Plugin Name: WooCommerce Customer Profile Pictures
 * Description: Allow WooCommerce customers to have multiple profile pictures
 * Author: Nabeel Molham
 * Author URI: https://nabeel.molham.me
 * Version: 1.0.0
 * Text Domain: woocommerce-customer-profile-pictures
 * Domain Path: /i18n/languages/
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package   WC-Customer-Profile-Pictures
 * @author    Nabeel Molham
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

// Required functions
if ( ! function_exists( 'woothemes_queue_update' ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'woo-includes/woo-functions.php' );

}

// WC active check
if ( ! is_woocommerce_active() ) {

	return;

}

/**
 * WooCommerce Customer Profile Pictures loader class.
 *
 * @since 1.0.0
 */
class WC_Customer_Profile_Pictures_Loader {

	/** minimum PHP version required by this plugin */
	public const MINIMUM_PHP_VERSION = '7.2.0';

	/** minimum WordPress version required by this plugin */
	public const MINIMUM_WP_VERSION = '5.3';

	/** minimum WooCommerce version required by this plugin */
	public const MINIMUM_WC_VERSION = '4.0.0';

	/** SkyVerge plugin framework version used by this plugin */
	public const FRAMEWORK_VERSION = '5.5.1';

	/** the plugin name, for displaying notices */
	public const PLUGIN_NAME = 'WooCommerce Customer Profile Pictures';

	/**
	 * @var WC_Customer_Profile_Pictures_Loader single instance of this class
	 */
	private static $instance;

	/** @var array the admin notices to add */
	private $notices = [];

	/**
	 * Constructs the class.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {

		register_activation_hook( __FILE__, [ $this, 'activation_check' ] );

		add_action( 'admin_init', [ $this, 'check_environment' ] );
		add_action( 'admin_init', [ $this, 'add_plugin_notices' ] );

		add_action( 'admin_notices', [ $this, 'admin_notices' ], 15 );

		// if the environment check fails, initialize the plugin
		if ( $this->is_environment_compatible() ) {
			add_action( 'plugins_loaded', [ $this, 'init_plugin' ] );
		}
	}


	/**
	 * Cloning instances is forbidden due to singleton pattern.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {

		_doing_it_wrong( __FUNCTION__, sprintf( 'You cannot clone instances of %s.', get_class( $this ) ), '1.0.0' );
	}


	/**
	 * Unserializing instances is forbidden due to singleton pattern.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {

		_doing_it_wrong( __FUNCTION__, sprintf( 'You cannot unserialize instances of %s.', get_class( $this ) ), '1.0.0' );
	}


	/**
	 * Initializes the plugin.
	 *
	 * @since 1.0.0
	 */
	public function init_plugin(): void {

		if ( ! $this->plugins_compatible() ) {
			return;
		}

		$this->load_framework();

		// load the main plugin class
		require_once( plugin_dir_path( __FILE__ ) . 'class-wc-customer-profile-pictures.php' );

		// fire it up!
		wc_customer_profile_pictures();

	}


	/**
	 * Loads the base framework classes.
	 *
	 * @since 1.0.0
	 */
	private function load_framework(): void {

		if ( ! class_exists( '\\SkyVerge\\WooCommerce\\PluginFramework\\' . $this->get_framework_version_namespace() . '\\SV_WC_Plugin' ) ) {
			require_once( plugin_dir_path( __FILE__ ) . 'vendor/skyverge/wc-plugin-framework/woocommerce/class-sv-wc-plugin.php' );
		}

		// TODO: remove this if not a payment gateway
		if ( ! class_exists( '\\SkyVerge\\WooCommerce\\PluginFramework\\' . $this->get_framework_version_namespace() . '\\SV_WC_Payment_Gateway_Plugin' ) ) {
			require_once( plugin_dir_path( __FILE__ ) . 'vendor/skyverge/wc-plugin-framework/woocommerce/payment-gateway/class-sv-wc-payment-gateway-plugin.php' );
		}
	}


	/**
	 * Gets the framework version in namespace form.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_framework_version_namespace(): string {

		return 'v' . str_replace( '.', '_', $this->get_framework_version() );

	}


	/**
	 * Gets the framework version used by this plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_framework_version(): string {

		return self::FRAMEWORK_VERSION;

	}


	/**
	 * Checks the server environment and other factors and deactivates plugins as necessary.
	 *
	 * Based on http://wptavern.com/how-to-prevent-wordpress-plugins-from-activating-on-sites-with-incompatible-hosting-environments
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function activation_check(): void {

		if ( ! $this->is_environment_compatible() ) {

			$this->deactivate_plugin();

			wp_die( self::PLUGIN_NAME . ' could not be activated. ' . $this->get_environment_message() );

		}

	}


	/**
	 * Checks the environment on loading WordPress, just in case the environment changes after activation.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function check_environment(): void {

		if ( ! $this->is_environment_compatible() && is_plugin_active( plugin_basename( __FILE__ ) ) ) {

			$this->deactivate_plugin();

			$this->add_admin_notice( 'bad_environment', 'error', self::PLUGIN_NAME . ' has been deactivated. ' . $this->get_environment_message() );

		}

	}


	/**
	 * Adds notices for out-of-date WordPress and/or WooCommerce versions.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 * @noinspection DuplicatedCode
	 */
	public function add_plugin_notices(): void {

		if ( ! $this->is_wp_compatible() ) {

			$this->add_admin_notice( 'update_wordpress', 'error', sprintf(
				'%s requires WordPress version %s or higher. Please %supdate WordPress &raquo;%s',
				'<strong>' . self::PLUGIN_NAME . '</strong>',
				self::MINIMUM_WP_VERSION,
				'<a href="' . esc_url( admin_url( 'update-core.php' ) ) . '">', '</a>'
			) );

		}

		if ( ! $this->is_wc_compatible() ) {

			$this->add_admin_notice( 'update_woocommerce', 'error', sprintf(
				'%1$s requires WooCommerce version %2$s or higher. Please %3$supdate WooCommerce%4$s to the latest version, or %5$sdownload the minimum required version &raquo;%6$s',
				'<strong>' . self::PLUGIN_NAME . '</strong>',
				self::MINIMUM_WC_VERSION,
				'<a href="' . esc_url( admin_url( 'update-core.php' ) ) . '">', '</a>',
				'<a href="' . esc_url( 'https://downloads.wordpress.org/plugin/woocommerce.' . self::MINIMUM_WC_VERSION . '.zip' ) . '">', '</a>'

			) );

		}
	}


	/**
	 * Determines if the required plugins are compatible.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function plugins_compatible(): bool {

		return $this->is_wp_compatible() && $this->is_wc_compatible();

	}


	/**
	 * Determines if the WordPress compatible.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function is_wp_compatible(): bool {

		if ( ! self::MINIMUM_WP_VERSION ) {

			return true;

		}

		return version_compare( get_bloginfo( 'version' ), self::MINIMUM_WP_VERSION, '>=' );

	}


	/**
	 * Determines if the WooCommerce compatible.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function is_wc_compatible(): bool {

		if ( ! self::MINIMUM_WC_VERSION ) {

			return true;

		}

		return defined( 'WC_VERSION' ) && version_compare( WC_VERSION, self::MINIMUM_WC_VERSION, '>=' );

	}


	/**
	 * Deactivates the plugin.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	protected function deactivate_plugin(): void {

		deactivate_plugins( plugin_basename( __FILE__ ) );

		if ( isset( $_GET['activate'] ) ) {

			unset( $_GET['activate'] );

		}

	}


	/**
	 * Adds an admin notice to be displayed.
	 *
	 * @param string $slug the slug for the notice
	 * @param string $class the css class for the notice
	 * @param string $message the notice message
	 *
	 * @since 1.0.0
	 */
	private function add_admin_notice( $slug, $class, $message ): void {

		$this->notices[ $slug ] = [
			'class'   => $class,
			'message' => $message,
		];

	}


	/**
	 * Displays any admin notices added with \WC_Customer_Profile_Pictures_Loader::add_admin_notice()
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function admin_notices(): void {

		foreach ( $this->notices as $notice_key => $notice ) {

			?>
			<div class="<?php echo esc_attr( $notice['class'] ); ?>">
				<p><?php echo wp_kses( $notice['message'], [ 'a' => [ 'href' => [] ] ] ); ?></p>
			</div>
			<?php

		}

	}


	/**
	 * Determines if the server environment is compatible with this plugin.
	 *
	 * Override this method to add checks for more than just the PHP version.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function is_environment_compatible(): bool {

		return version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '>=' );

	}


	/**
	 * Gets the message for display when the environment is incompatible with this plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	private function get_environment_message(): string {

		return sprintf( 'The minimum PHP version required for this plugin is %1$s. You are running %2$s.', self::MINIMUM_PHP_VERSION, PHP_VERSION );

	}


	/**
	 * Gets the main \WC_Customer_Profile_Pictures_Loader instance.
	 *
	 * Ensures only one instance can be loaded.
	 *
	 * @since 1.0.0
	 *
	 * @return \WC_Customer_Profile_Pictures_Loader
	 */
	public static function instance(): WC_Customer_Profile_Pictures_Loader {

		if ( null === self::$instance ) {

			self::$instance = new self();

		}

		return self::$instance;

	}

}

// fire it up!
WC_Customer_Profile_Pictures_Loader::instance();
