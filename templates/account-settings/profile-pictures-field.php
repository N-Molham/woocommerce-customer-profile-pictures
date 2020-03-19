<?php
/**
 * Profile pictures field UI
 *
 * @since 1.0.0
 *
 * @package Nabeel_Molham\WooCommerce\WC_Customer_Profile_Pictures
 *
 * @var $customer_id int
 * @var $maximum_allowed int
 * @var $profile_pictures array
 * @var $active_profile_picture array
 * @var $active_profile_picture_index int
 */

$profile_pictures_indexes   = empty( $profile_pictures ) ? [] : array_keys( $profile_pictures );
$last_profile_picture_index = empty( $profile_pictures_indexes ) ? '' : array_pop( $profile_pictures_indexes );

?>

<fieldset id="wc-customer-profile-pictures-fieldset" data-maximum="<?php echo esc_attr( $maximum_allowed ); ?>" data-last="<?php echo esc_attr( $last_profile_picture_index ); ?>">
	<legend><?php esc_html_e( 'Profile Picture', 'woocommerce-customer-profile-pictures' ); ?>&nbsp;<span class="required">*</span></legend>

	<?php if ( empty( $profile_pictures ) ) : ?>
		<div class="woocommerce-notices-wrapper">
			<p class="woocommerce-info" role="alert"><?php esc_attr_e( 'You do not have any profile pictures uploaded yet.', 'woocommerce-customer-profile-pictures' ); ?></p>
		</div>
	<?php endif; ?>

	<div class="wc-customer-profile-pictures-list">
		<?php if ( $profile_pictures ) : ?>
			<?php foreach ( $profile_pictures as $picture_index => $profile_picture ) : $is_active = $active_profile_picture_index === $picture_index; ?>

				<?php wc_get_template( 'account-settings/profile-picture-display.php',
					compact( 'customer_id', 'picture_index', 'profile_picture', 'is_active' ),
					'',
					wc_customer_profile_pictures()->get_plugin_path() . '/templates/' ); ?>

			<?php endforeach; ?>
		<?php endif; ?>
	</div>

	<button id="wc-customer-profile-pictures-add-new" type="button" class="button"><?php esc_attr_e( 'Add New', 'woocommerce-customer-profile-pictures' ); ?></button>

</fieldset>

