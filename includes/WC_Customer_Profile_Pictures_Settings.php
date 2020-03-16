<?php

namespace Nabeel_Molham\WooCommerce\WC_Customer_Profile_Pictures;

/**
 * Plugin settings class
 *
 * @package Nabeel_Molham\WooCommerce\WC_Customer_Profile_Pictures
 */
class WC_Customer_Profile_Pictures_Settings {

	/**
	 * Default maximum pictures value
	 *
	 * @var int
	 */
	protected $_default_maximum_value = 2;

	/**
	 * WC_Customer_Profile_Pictures_Settings constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		add_filter( 'woocommerce_account_settings', [ $this, 'add_settings' ] );

	}

	/**
	 * @param array $account_settings
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function add_settings( $account_settings ): array {
		
		$account_settings[] = [
			'title' => __( 'Customer Profile Pictures', 'woocommerce-customer-profile-pictures' ),
			'type'  => 'title',
			'id'    => 'wc_customer_profile_pictures',
		];

		$account_settings[] = [
			'title'             => __( 'Maximum number of pictures', 'woocommerce-customer-profile-pictures' ),
			'desc'              => __( 'The maximum number of profile pictures the customer can have.', 'woocommerce-customer-profile-pictures' ),
			'id'                => 'wc_customer_profile_pictures_max',
			'css'               => 'width:80px;',
			'default'           => $this->_default_maximum_value,
			'desc_tip'          => true,
			'type'              => 'number',
			'custom_attributes' => [
				'min'  => 1,
				'step' => 1,
			],
		];

		$account_settings[] = [
			'type' => 'sectionend',
			'id'   => 'wc_customer_profile_pictures',
		];

		return $account_settings;

	}

	/**
	 * Get maximum number of profile pictures allowed
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public function get_profile_pictures_maximum(): int {

		$maximum_allowed = get_option( 'wc_customer_profile_pictures_max', $this->_default_maximum_value );

		/**
		 * Allow 3rd party plugins/theme to modify the value
		 *
		 * @param int $maximum_allowed
		 *
		 * @return int
		 */
		return (int) apply_filters( 'wc_customer_profile_pictures_maximum_allowed', $maximum_allowed );

	}

}