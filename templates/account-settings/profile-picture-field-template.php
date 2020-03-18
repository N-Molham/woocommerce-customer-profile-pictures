<?php
/**
 * Profile picture field template
 *
 * @package Nabeel_Molham\WooCommerce\WC_Customer_Profile_Pictures
 * 
 * @since 1.0.0
 */

?>
<script type="html/template" id="wc-customer-profile-picture-field-template">
	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide wc-customer-profile-picture-field">
		<label for="account-profile-picture-{index}"><?php esc_html_e( 'Picture #{index_label}', 'woocommerce-customer-profile-pictures' ); ?></label>
		<input type="file" name="account_profile_picture[{index}]" id="account-profile-picture-{index}" class="woocommerce-Input woocommerce-Input--file input-file" />
	</p>
</script>