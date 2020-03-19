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

		add_action( 'admin_enqueue_scripts', [ wc_customer_profile_pictures()->get_user_edit_instance(), 'load_assets' ] );

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

		echo '<p class="form-field form-field-wide wc-customer-profile-picture">',
		'<a href="#TB_inline?height=600&width=600&inlineId=wc-customer-profile-pictures-image-model" class="thickbox" title="',
		esc_html__( 'Customer Profile Picture at time of purchase', 'woocommerce-customer-profile-pictures' ), '">',
			'<img alt="" src="' . esc_url( $thumb_picture_url ) . '" class="wc-customer-profile-picture-image" width="96"></a>',
		'</p>';

		echo '<div id="wc-customer-profile-pictures-image-model" class="hidden">',
			'<div class="wc-customer-profile-pictures-image-model"><img src="' . esc_url( $profile_picture['url'] ) . '" alt=""/></div></div>';

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

		/* @var $the_order WC_Order */
		global $the_order;

		if ( 'order_number' !== $column ) {

			return;

		}

		$wc_order = $the_order ?? wc_get_order( $order_id );

		$profile_picture = $wc_order->get_meta( '_wc_customer_profile_picture' );

		if ( empty( $profile_picture ) ) {

			return;

		}

		echo '<img src="', esc_url( wc_customer_profile_pictures()->get_profile_picture_size_url( $profile_picture, '52' ) ), '" alt="" /> ';

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