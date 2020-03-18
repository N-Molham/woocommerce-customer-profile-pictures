<?php

namespace Nabeel_Molham\WooCommerce\WC_Customer_Profile_Pictures;

use WC_Customer;

/**
 * Customer's account settings integration class
 *
 * @package Nabeel_Molham\WooCommerce\WC_Customer_Profile_Pictures
 */
class WC_Customer_Profile_Pictures_Account_Settings {

	/**
	 * WC_Customer_Profile_Pictures_Settings constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		add_action( 'woocommerce_edit_account_form_start', [ $this, 'render_profile_pictures_field' ] );

		add_action( 'template_redirect', [ $this, 'load_assets' ] );

	}

	/**
	 * Load JS & CSS assets
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function load_assets(): void {

		if ( is_account_page() && is_wc_endpoint_url( 'edit-account' ) ) {

			wp_enqueue_style( 'woocommerce-customer-profile-pictures-account-page',
				wc_customer_profile_pictures()->get_plugin_url() . '/assets/css/woocommerce-customer-profile-pictures-account-page.css',
				null, wc_customer_profile_pictures()->get_version() );

			wp_enqueue_script( 'woocommerce-customer-profile-pictures-account-page',
				wc_customer_profile_pictures()->get_plugin_url() . '/assets/js/woocommerce-customer-profile-pictures-account-page.js',
				[ 'jquery' ], wc_customer_profile_pictures()->get_version(), true );

		}

	}

	/**
	 * Render profile pictures field
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function render_profile_pictures_field(): void {

		$customer_id = get_current_user_id();

		wc_get_template( 'account-settings/profile-pictures-field.php',
			[
				'customer_id'      => $customer_id,
				'maximum_allowed'  => wc_customer_profile_pictures_maximum_allowed(),
				'profile_pictures' => $this->get_customer_profile_pictures( $customer_id ),
			],
			'',
			wc_customer_profile_pictures()->get_plugin_path() . '/templates/' );

		wc_get_template( 'account-settings/profile-picture-field-template.php',
			[], '',
			wc_customer_profile_pictures()->get_plugin_path() . '/templates/' );

	}

	/**
	 * Get list of customer's profile pictures (ID & URLs)
	 *
	 * @param int $customer_id
	 *
	 * @return array
	 */
	public function get_customer_profile_pictures( $customer_id = null ): array {

		$customer_id = $customer_id ? : get_current_user_id();

		$profile_pictures_ids = array_filter( get_user_meta( $customer_id, '_profile_pictures' ) );

		$profile_pictures = [];

		if ( count( $profile_pictures_ids ) ) {

			$profile_pictures = array_map( static function ( $picture_id ) {

				$picture_url = wp_get_attachment_image_url( $picture_id, 'thumbnail' );

				if ( empty( $picture_url ) ) {

					return false;

				}

				return [
					'id'  => $picture_id,
					'url' => $picture_url,
				];

			}, $profile_pictures_ids );

		}

		/**
		 * Allow 3rd party plugins/theme to modify customer's profile pictures
		 *
		 * @param array $profile_pictures
		 * @param int   $customer_id
		 *
		 * @return array
		 */
		return (array) apply_filters( 'wc_customer_profile_pictures_of_a_customer', $profile_pictures, $customer_id );

	}

}