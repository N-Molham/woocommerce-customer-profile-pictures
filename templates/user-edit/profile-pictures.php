<?php
/**
 * Profile picture field template
 *
 * @since 1.0.0
 *
 * @var $user_profile_pictures array
 *
 * @package Nabeel_Molham\WooCommerce\WC_Customer_Profile_Pictures
 */
?>
<strong><?php esc_html_e( 'Other profile pictures:', 'woocommerce-customer-profile-pictures' ); ?></strong>

<span class="wc-customer-profile-picture-list">
	<?php foreach ( $user_profile_pictures as $index => $picture ) :

		$picture_full_size_model_id = 'wc-customer-profile-pictures-image-model-' . $index;
		$picture_small_size_url = wc_customer_profile_pictures()->get_profile_picture_size_url( $picture, '96' );

		?>
		<span class="wc-customer-profile-picture-item">
			<a href="#TB_inline?height=600&width=600&inlineId=<?php echo esc_attr( $picture_full_size_model_id ); ?>" class="thickbox"
			   title="<?php echo esc_attr__( 'Profile #', 'woocommerce-customer-profile-pictures' ), ( $index + 1 ); ?>">
				<img alt="" src="<?php echo esc_url( $picture_small_size_url ); ?>" class="wc-customer-profile-picture-image" width="96"></a>
		</span>

		<span id="<?php echo esc_attr( $picture_full_size_model_id ); ?>" class="hidden">
			<span class="wc-customer-profile-pictures-image-model"><img src="<?php echo esc_url( $picture['url'] ); ?>" alt="" /></span>
		</span>
	<?php endforeach; ?>
</span>