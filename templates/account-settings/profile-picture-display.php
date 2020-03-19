<?php
/**
 * Profile picture field template
 *
 * @since 1.0.0
 *
 * @var $customer_id int
 * @var $picture_index int
 * @var $profile_picture array
 * @var $is_active bool
 *
 * @package Nabeel_Molham\WooCommerce\WC_Customer_Profile_Pictures
 */
?>
<div class="wc-customer-profile-picture-field">
	<p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first wc-customer-profile-picture-field-file">
		<label for="account-profile-picture-<?php echo esc_attr( $picture_index ); ?>">
			<?php echo sprintf( __( 'Picture #%d', 'woocommerce-customer-profile-pictures' ), $picture_index + 1 ); ?>
		</label>
		<img src="<?php echo esc_url( $profile_picture['url'] ); ?>" alt="<?php echo esc_attr( pathinfo( $profile_picture['url'], PATHINFO_FILENAME ) ); ?>"
		     class="wc-customer-profile-picture-field-image" />
		<input type="hidden" name="account_keep_profile_pictures[]" value="<?php echo esc_attr( $picture_index ); ?>" />
	</p>

	<p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last wc-customer-profile-picture-field-active">
		<label class="wc-customer-profile-pictures-set-active">
			<input type="radio" name="account_profile_picture_active" value="<?php echo esc_attr( $picture_index ); ?>"
				<?php checked( true, $is_active ) ?>
				   class="woocommerce-Input woocommerce-Input--radio input-radio" />
			<?php esc_attr_e( 'Set as Primary', 'woocommerce-customer-profile-pictures' ); ?>
		</label>

		<button type="button" class="button wc-customer-profile-pictures-remove"><?php esc_attr_e( 'Remove', 'woocommerce-customer-profile-pictures' ); ?></button>
	</p>

	<div class="clear"></div>
</div>