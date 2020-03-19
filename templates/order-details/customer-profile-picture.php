<?php
/**
 * Order's customer's profile picture
 *
 * @since 1.0.0
 *
 * @var $profile_picture array
 * @var $thumb_picture_url string
 *
 * @package Nabeel_Molham\WooCommerce\WC_Customer_Profile_Pictures
 */
?>
<p class="form-field form-field-wide wc-customer-profile-picture">
	<a href="#TB_inline?height=600&width=600&inlineId=wc-customer-profile-pictures-image-model" class="thickbox"
	   title="<?php esc_html_e( 'Customer Profile Picture at time of purchase', 'woocommerce-customer-profile-pictures' ); ?>">
		<img alt="" src="<?php echo esc_url( $thumb_picture_url ); ?>" class="wc-customer-profile-picture-image" width="96"></a></p>

<div id="wc-customer-profile-pictures-image-model" class="hidden">
	<div class="wc-customer-profile-pictures-image-model"><img src="<?php echo esc_url( $profile_picture['url'] ); ?>" alt="" /></div>
</div>