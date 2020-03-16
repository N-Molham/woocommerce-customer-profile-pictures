<?php

use Nabeel_Molham\WooCommerce\WC_Customer_Profile_Pictures\WC_Customer_Profile_Pictures;

/**
 * @since 1.0.0
 */
function wc_customer_profile_pictures() {

	return WC_Customer_Profile_Pictures::instance();

}

/**
 * Get maximum number of profile pictures allowed
 *
 * @since 1.0.0
 *
 * @return int
 */
function wc_customer_profile_pictures_maximum_allowed() {

	return wc_customer_profile_pictures()->get_settings_instance()->get_profile_pictures_maximum();

}