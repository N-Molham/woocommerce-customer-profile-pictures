<?php

namespace Nabeel_Molham\WooCommerce\WC_Customer_Profile_Pictures;

use WC_Order;

/**
 * Orders integration class
 *
 * @package Nabeel_Molham\WooCommerce\WC_Customer_Profile_Pictures
 */
class WC_Customer_Profile_Pictures_Orders {

	/**
	 * WC_Customer_Profile_Pictures_Orders constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		add_action( 'woocommerce_checkout_create_order', [ $this, 'order_include_user_current_active_profile_image' ] );

		add_action( 'manage_shop_order_posts_custom_column', [ $this, 'prepend_customer_profile_picture_to_order_number_column' ], 10, 2 );

		add_action( 'woocommerce_admin_order_data_after_order_details', [ $this, 'prepend_customer_profile_picture_after_order_details' ] );

		add_action( 'admin_enqueue_scripts', [ $this, 'load_assets' ] );

	}

	/**
	 * Load CSS & JS asset files
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function load_assets(): void {

		if ( in_array( get_current_screen()->id, [ 'shop_order', 'edit-shop_order' ], true ) ) {

			add_thickbox();

			wp_enqueue_style( 'woocommerce-customer-profile-pictures-admin' );

		}

	}

	/**
	 * Append customer's full size profile picture after order details section
	 *
	 * @param WC_Order $wc_order
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function prepend_customer_profile_picture_after_order_details( $wc_order ): void {

		$profile_picture = $wc_order->get_meta( '_wc_customer_profile_picture' );

		if ( empty( $profile_picture ) ) {

			return;

		}

		$thumb_picture_url = wc_customer_profile_pictures()->get_profile_picture_size_url( $profile_picture, '192' );

		wc_get_template( 'order-details/customer-profile-picture.php',
			compact( 'profile_picture', 'thumb_picture_url' ), '',
			wc_customer_profile_pictures()->get_plugin_path() . '/templates/' );

	}

	/**
	 * Prepend customer's profile picture to order number column on Orders Table
	 *
	 * @param string $column
	 * @param int    $order_id
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function prepend_customer_profile_picture_to_order_number_column( $column, $order_id ): void {

		if ( 'order_number' !== $column ) {

			return;

		}

		$wc_order = wc_get_order( $order_id );

		$profile_picture = $wc_order->get_meta( '_wc_customer_profile_picture' );

		if ( empty( $profile_picture ) ) {

			return;

		}

		wc_get_template( 'orders-table/customer-profile-picture.php', [
			'profile_picture_url' => wc_customer_profile_pictures()->get_profile_picture_size_url( $profile_picture, '52' ),
		], '', wc_customer_profile_pictures()->get_plugin_path() . '/templates/' );

	}

	/**
	 * Store the customer's current active profile picture into the order meta data
	 *
	 * @param WC_Order $order
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function order_include_user_current_active_profile_image( $order ): void {

		$customer_active_profile_picture = wc_customer_profile_pictures_get_user_active_picture( 'all', $order->get_customer_id() );

		if ( empty( $customer_active_profile_picture ) ) {

			return;

		}

		$order->add_meta_data( '_wc_customer_profile_picture', $customer_active_profile_picture );

	}

}