<?php

namespace Nabeel_Molham\WooCommerce\WC_Customer_Profile_Pictures;

use WP_Error;

/**
 * Customer's account settings integration class
 *
 * @package Nabeel_Molham\WooCommerce\WC_Customer_Profile_Pictures
 */
class WC_Customer_Profile_Pictures_Account_Settings {

	/**
	 * Temporary holder for uploaded files
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $_saved_files = [];

	/**
	 * WC_Customer_Profile_Pictures_Settings constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		add_action( 'woocommerce_edit_account_form_tag', [ $this, 'add_multipart_to_edit_account_form' ] );

		add_action( 'woocommerce_edit_account_form_start', [ $this, 'render_profile_pictures_field' ] );

		add_action( 'template_redirect', [ $this, 'load_assets' ] );

		add_action( 'woocommerce_save_account_details_errors', [ $this, 'validate_submitted_profile_pictures' ] );

		add_action( 'woocommerce_save_account_details', [ $this, 'save_uploaded_profile_pictures' ] );

	}

	/**
	 * Associate the uploaded image(s) to the associated user/customer
	 *
	 * @param int $user_id
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function save_uploaded_profile_pictures( $user_id ): void {

		$active_profile_picture_index = filter_input( INPUT_POST, 'account_profile_picture_active', FILTER_SANITIZE_NUMBER_INT );

		update_user_meta( $user_id, 'wc_active_profile_picture', $active_profile_picture_index );

		if ( count( $this->_saved_files ) ) {

			update_user_meta( $user_id, 'wc_profile_pictures', $this->_saved_files );

		}

	}


	/**
	 * Validate submitted profile pictures files and store them if valid
	 *
	 * @param WP_Error $errors
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function validate_submitted_profile_pictures( $errors ): void {

		$uploaded_files = $_FILES['account_profile_picture'] ?? null;

		if ( empty( $uploaded_files ) ) {

			return;

		}

		$maximum_allowed_profile_pictures = wc_customer_profile_pictures_maximum_allowed();

		if ( count( $uploaded_files['tmp_name'] ) > $maximum_allowed_profile_pictures ) {

			$errors->add( 'wc_customer_profile_pictures_maximum_allowed',
				__( 'You exceeded the maximum number of profile pictures allowed: ', 'woocommerce-customer-profile-pictures' ) . $maximum_allowed_profile_pictures );

			return;

		}

		if ( ! function_exists( 'wp_handle_upload' ) ) {

			require_once( ABSPATH . 'wp-admin/includes/file.php' );

		}

		foreach ( $uploaded_files['tmp_name'] as $index => $temp_file_path ) {

			if ( false === wp_get_image_mime( $temp_file_path ) ) {

				$errors->add( 'wc_customer_profile_pictures_invalid_image', __( 'Invalid image file provided!', 'woocommerce-customer-profile-pictures' ) );

				return;

			}

			$posted_file = [
				'name'     => $uploaded_files['name'][ $index ],
				'type'     => $uploaded_files['type'][ $index ],
				'tmp_name' => $temp_file_path,
				'error'    => $uploaded_files['error'][ $index ],
				'size'     => $uploaded_files['size'][ $index ],
			];

			$saved_file = wp_handle_upload( $posted_file, [
				'action' => 'save_account_details',
			] );

			if ( isset( $saved_file['error'] ) ) {

				$errors->add( 'wc_customer_profile_pictures_upload_error', __( 'Error uploading image file: ', 'woocommerce-customer-profile-pictures' ), $saved_file['error'] );

				return;

			}

			$this->_saved_files[ $index ] = $saved_file;

		}

	}

	/**
	 * Load JS & CSS assets
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function load_assets(): void {

		if ( is_account_page() && is_wc_endpoint_url( 'edit-account' ) ) {

			wp_enqueue_style( 'woocommerce-customer-profile-pictures-account-page',
				wc_customer_profile_pictures()->get_plugin_url() . '/assets/css/woocommerce-customer-profile-pictures-account-page.css',
				null, wc_customer_profile_pictures()->get_version() );

			wp_enqueue_script( 'woocommerce-customer-profile-pictures-account-page',
				wc_customer_profile_pictures()->get_plugin_url() . '/assets/js/woocommerce-customer-profile-pictures-account-page.js',
				[ 'jquery' ], wc_customer_profile_pictures()->get_version(), true );

			wp_localize_script( 'woocommerce-customer-profile-pictures-account-page', 'wc_customer_profile_pictures', [
				'i18n' => [
					'confirm_remove' => esc_html__( 'Are you sure you want to remove this picture?', 'woocommerce-customer-profile-pictures' ),
				],
			] );

		}

	}

	/**
	 * Render profile pictures field
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function render_profile_pictures_field(): void {

		$customer_id = get_current_user_id();

		wc_get_template( 'account-settings/profile-pictures-field.php',
			[
				'customer_id'                  => $customer_id,
				'maximum_allowed'              => wc_customer_profile_pictures_maximum_allowed(),
				'profile_pictures'             => $this->get_customer_profile_pictures( $customer_id ),
				'active_profile_picture'       => $this->get_customer_active_profile_picture( 'all', $customer_id ),
				'active_profile_picture_index' => $this->get_customer_active_profile_picture_index( $customer_id ),
			],
			'',
			wc_customer_profile_pictures()->get_plugin_path() . '/templates/' );

		wc_get_template( 'account-settings/profile-picture-field-template.php',
			[], '',
			wc_customer_profile_pictures()->get_plugin_path() . '/templates/' );

	}

	/**
	 * Add multipart attribute to Edit Account form
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_multipart_to_edit_account_form(): void {

		echo 'enctype="multipart/form-data" ';

	}

	/**
	 * Get list of customer's profile pictures (ID & URLs)
	 *
	 * @param int $customer_id
	 *
	 * @return array
	 */
	public function get_customer_profile_pictures( $customer_id = null ): array {

		$customer_id = $customer_id ?? get_current_user_id();

		$profile_pictures = array_filter( (array) get_user_meta( $customer_id, 'wc_profile_pictures', true ) );

		/**
		 * Allow 3rd party plugins/theme to modify customer's profile pictures
		 *
		 * @param array $profile_pictures
		 * @param int   $customer_id
		 *
		 * @return array
		 */
		return (array) apply_filters( 'wc_customer_profile_pictures_of_a_customer', $profile_pictures, $customer_id );

	}

	/**
	 * Get customer's active profile picture
	 *
	 * @param string $return
	 * @param int    $customer_id
	 *
	 * @return string|array|bool
	 */
	public function get_customer_active_profile_picture( $return = 'url', $customer_id = null ) {

		$active_profile_picture_index = $this->get_customer_active_profile_picture_index( $customer_id );

		$profile_pictures = $this->get_customer_profile_pictures( $customer_id );

		if ( empty( $profile_pictures ) ) {

			/**
			 * Allow 3rd party plugins/theme to modify customer's active profile picture
			 *
			 * @param mixed  $not_found
			 * @param int    $customer_id
			 * @param string $return
			 *
			 * @return mixed
			 */
			return apply_filters( 'wc_customer_profile_pictures_active', false, $customer_id, $return );

		}

		// if index doesn't exists or not longer available, fallback to the first picture
		$active_profile_picture = $profile_pictures[ $active_profile_picture_index ] ?? array_shift( $profile_pictures );

		/**
		 * Allow 3rd party plugins/theme to modify customer's active profile picture
		 *
		 * @param mixed  $active_profile_picture
		 * @param int    $customer_id
		 * @param string $return
		 *
		 * @return string|array
		 */
		return apply_filters( 'wc_customer_profile_pictures_active', $active_profile_picture[ $return ] ?? $active_profile_picture, $customer_id, $return );

	}

	/**
	 * Get customer's active profile picture index
	 *
	 * @param int $customer_id
	 *
	 * @return int
	 */
	public function get_customer_active_profile_picture_index( $customer_id = null ): int {

		$customer_id = $customer_id ?? get_current_user_id();

		$active_profile_picture_index = (int) get_user_meta( $customer_id, 'wc_active_profile_picture', true );

		/**
		 * Allow 3rd party plugins/theme to modify customer's active profile picture index
		 *
		 * @param int $active_profile_picture_index
		 * @param int $customer_id
		 *
		 * @return int
		 */
		return (int) apply_filters( 'wc_customer_profile_picture_active_index', $active_profile_picture_index, $customer_id );

	}

}