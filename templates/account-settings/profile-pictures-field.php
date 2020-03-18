<?php
/**
 * Profile pictures field UI
 *
 * @package Nabeel_Molham\WooCommerce\WC_Customer_Profile_Pictures
 * 
 * @since 1.0.0
 * 
 * @var $customer_id int
 * @var $maximum_allowed int
 * @var $profile_pictures array
 */

?>

<fieldset id="wc-customer-profile-pictures-fieldset" data-maximum="<?php echo esc_attr( $maximum_allowed ); ?>">
	<legend><?php esc_html_e( 'Profile Picture', 'woocommerce-customer-profile-pictures' ); ?>&nbsp;<span class="required">*</span></legend>

	<?php if ( empty( $profile_pictures ) ) : ?>
		<div class="woocommerce-notices-wrapper">
			<p class="woocommerce-info" role="alert"><?php esc_attr_e( 'You do not have any profile pictures uploaded yet.', 'woocommerce-customer-profile-pictures' ); ?></p>
		</div>
	<?php endif; ?>

	<div class="wc-customer-profile-pictures-list"></div>

	<button id="wc-customer-profile-pictures-add-new" type="button" class="button"><?php esc_attr_e( 'Add New', 'woocommerce-customer-profile-pictures' ); ?></button>

</fieldset>

<div class="clear"></div>
