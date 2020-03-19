<?php

namespace Nabeel_Molham\WooCommerce\WC_Customer_Profile_Pictures;

use WP_User;

/**
 * User's edit page integration class
 *
 * @package Nabeel_Molham\WooCommerce\WC_Customer_Profile_Pictures
 */
class WC_Customer_Profile_Pictures_User_Edit {

	/**
	 * WC_Customer_Profile_Pictures_User_Edit constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		add_filter( 'user_profile_picture_description', [ $this, 'append_users_uploaded_profile_pictures' ], 10, 2 );

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

		$current_screen_id = get_current_screen()->id;

		if ( 'profile' === $current_screen_id || 'user-edit' === $current_screen_id ) {

			add_thickbox();

			wp_enqueue_style( 'woocommerce-customer-profile-pictures-user-edit',
				wc_customer_profile_pictures()->get_plugin_url() . '/assets/css/woocommerce-customer-profile-pictures-user-edit.css',
				null, wc_customer_profile_pictures()->get_version() );

		}

	}

	/**
	 * @param string  $description
	 * @param WP_User $user
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function append_users_uploaded_profile_pictures( $description, $user ): string {


		$user_profile_pictures = wc_customer_profile_pictures_get_user_pictures( $user->ID );

		if ( empty( $user_profile_pictures ) ) {

			return $description;

		}

		$user_active_profile_picture_index = wc_customer_profile_pictures_get_user_active_picture_index( $user->ID );

		unset( $user_profile_pictures[ $user_active_profile_picture_index ] );

		if ( empty( $user_profile_pictures ) ) {

			return $description;

		}

		$description .= '</p><p><strong>' . esc_html__( 'Other profile pictures:', 'woocommerce-customer-profile-pictures' ) . '</strong><span class="wc-customer-profile-picture-list">';

		foreach ( $user_profile_pictures as $index => $picture ) {

			$model_id = 'wc-customer-profile-pictures-image-model-' . $index;

			$description .= '<span class="wc-customer-profile-picture-item">' .
			                '<a href="#TB_inline?height=600&width=600&inlineId=' . esc_attr( $model_id ) . '" class="thickbox"' .
			                ' title="' . esc_attr__( 'Profile #', 'woocommerce-customer-profile-pictures' ) . ( $index + 1 ) . '">' .
			                '<img alt="" src="' . esc_url( $picture['url'] ) . '" class="wc-customer-profile-picture-image" width="96"></a></span>';

			$description .= '<div id="' . esc_attr( $model_id ) . '" class="hidden">' .
			                '<div class="wc-customer-profile-pictures-image-model"><img src="' . esc_url( $picture['url'] ) . '" alt=""/></div></div>';

		}

		$description .= '</span>';

		return $description;

	}

}