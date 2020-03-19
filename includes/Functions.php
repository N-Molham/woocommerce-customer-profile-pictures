<?php

use Nabeel_Molham\WooCommerce\WC_Customer_Profile_Pictures\WC_Customer_Profile_Pictures;
use Nabeel_Molham\WooCommerce\WC_Customer_Profile_Pictures\WC_Customer_Profile_Pictures_Account_Settings;

/**
 * @since 1.0.0
 *
 * @return WC_Customer_Profile_Pictures
 */
function wc_customer_profile_pictures(): WC_Customer_Profile_Pictures {

	return WC_Customer_Profile_Pictures::instance();

}

/**
 * @since 1.0.0
 *
 * @return WC_Customer_Profile_Pictures_Account_Settings
 */
function wc_customer_profile_pictures_account_settings(): WC_Customer_Profile_Pictures_Account_Settings {

	return wc_customer_profile_pictures()->get_account_settings_instance();

}

/**
 * Get maximum number of profile pictures allowed
 *
 * @since 1.0.0
 *
 * @return int
 */
function wc_customer_profile_pictures_maximum_allowed(): int {

	return wc_customer_profile_pictures()->get_plugin_settings_instance()->get_profile_pictures_maximum();

}

/**
 * Get customer's active profile picture index
 *
 * @param int $customer_id
 *
 * @return int
 */
function wc_customer_profile_pictures_get_user_active_picture_index( $customer_id = null ): int {

	return wc_customer_profile_pictures_account_settings()->get_customer_active_profile_picture_index( $customer_id );

}

/**
 * Get customer's active profile picture
 *
 * @param string $return
 * @param int    $customer_id
 *
 * @return string|array|bool
 */
function wc_customer_profile_pictures_get_user_active_picture( $return = 'url', $customer_id = null ) {

	return wc_customer_profile_pictures_account_settings()->get_customer_active_profile_picture( $return, $customer_id );

}

/**
 * Get list of customer's profile pictures (ID & URLs)
 *
 * @param int $customer_id
 *
 * @return array
 */
function wc_customer_profile_pictures_get_user_pictures( $customer_id = null ): array {

	return wc_customer_profile_pictures_account_settings()->get_customer_profile_pictures( $customer_id );

}