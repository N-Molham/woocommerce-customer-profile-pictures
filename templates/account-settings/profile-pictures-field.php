<?php
/**
 * Profile pictures field UI
 *
 * @var $customer_id int
 * @var $maximum_allowed int
 * @var $profile_pictures array
 */

?>

<fieldset id="wc-customer-profile-pictures-fieldset" data-maximum-allowed="<?php echo esc_attr( $maximum_allowed ); ?>">
	<legend><?php esc_html_e( 'Profile Picture', 'woocommerce-customer-profile-pictures' ); ?>&nbsp;<span class="required">*</span></legend>

	<?php var_dump( $profile_pictures ); ?>

</fieldset>

<script type="html/template" id="wc-customer-profile-picture-field-template">
	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
		<label for="account-profile-picture-{index}"><?php esc_html_e( 'Picture #{index}', 'woocommerce-customer-profile-pictures' ); ?></label>
	</p>
</script>

<div class="clear"></div>
