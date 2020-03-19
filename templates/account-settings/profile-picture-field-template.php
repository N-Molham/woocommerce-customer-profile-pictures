<?php
/**
 * Profile picture field template
 *
 * @since 1.0.0
 * 
 * @package Nabeel_Molham\WooCommerce\WC_Customer_Profile_Pictures
 */

?>
<script type="html/template" id="wc-customer-profile-picture-field-template">
	<div class="wc-customer-profile-picture-field">
		<p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first wc-customer-profile-picture-field-file">
			<label for="account-profile-picture-{index}"><?php esc_html_e( 'Picture #{index_label}', 'woocommerce-customer-profile-pictures' ); ?></label>
			<input type="file" name="account_profile_picture[{index}]" id="account-profile-picture-{index}" class="woocommerce-Input woocommerce-Input--file input-file" />
		</p>

		<p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last wc-customer-profile-picture-field-active">
			<button type="button" class="button wc-customer-profile-pictures-remove"><?php esc_attr_e( 'Remove', 'woocommerce-customer-profile-pictures' ); ?></button>
		</p>

		<div class="clear"></div>
	</div>
</script>