<?php /** @noinspection ReturnTypeCanBeDeclaredInspection */

namespace Nabeel_Molham\WooCommerce\WC_Customer_Profile_Pictures\REST_API;

use WC_REST_Controller;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use WP_User_Query;

/**
 * Customers profile pictures REST API endpoint
 *
 * @package Nabeel_Molham\WooCommerce\WC_Customer_Profile_Pictures
 */
class WC_Customer_Profile_Pictures_REST_Controller extends WC_REST_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc/v3';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'customers_profile_pictures';

	/**
	 * Register the routes for customers' profile pictures
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_routes() {

		register_rest_route( $this->namespace, '/' . $this->rest_base, [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_items' ],
				'permission_callback' => [ $this, 'get_items_permissions_check' ],
				'args'                => $this->get_collection_params(),
			],
			'schema' => [ $this, 'get_public_item_schema' ],
		] );

	}

	/**
	 * Check whether a given request has permission to read resource.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Error|boolean
	 */
	public function get_items_permissions_check( $request ) {

		if ( false === wc_rest_check_user_permissions( 'read' ) ) {

			return new WP_Error( 'wc_customer_profile_pictures_rest_cannot_view', __( 'Sorry, you cannot list resources.', 'woocommerce-customer-profile-pictures' ), [
				'status' => rest_authorization_required_code(),
			] );

		}

		return true;
	}

	/**
	 * Get all profile pictures
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items( $request ) {

		$query_args = [
			'fields'     => 'ID',
			'number'     => $request->get_param( 'per_page' ),
			'paged'      => $request->get_param( 'page' ),
			'meta_query' => [
				[ 'key' => wc_customer_profile_pictures_account_settings()->get_profile_pictures_meta_key(), 'compare' => 'EXISTS' ],
			],
		];

		$customer_id = $request->get_param( 'customer_id' );

		if ( ! empty( $customer_id ) ) {

			$query_args['search']         = $customer_id;
			$query_args['search_columns'] = [ 'ID' ];

		}

		/**
		 * Filter arguments, before passing to WP_User_Query, when querying users via the REST API.
		 *
		 * @see https://developer.wordpress.org/reference/classes/wp_user_query/
		 *
		 * @param array           $query_args Array of arguments for WP_User_Query.
		 * @param WP_REST_Request $request The current request.
		 *
		 * @since 1.0.0
		 *
		 * @return array
		 */
		$query_args = apply_filters( 'woocommerce_rest_customer_query', $query_args, $request );

		$user_query = new WP_User_Query( $query_args );

		$profile_pictures = [];

		foreach ( $user_query->get_results() as $user ) {

			$profile_pictures_data = $this->prepare_item_for_response( $user, $request );

			foreach ( $profile_pictures_data as $picture_data ) {

				$profile_pictures[] = $picture_data;

			}

		}

		wp_send_json( $profile_pictures );

		$response = rest_ensure_response( $profile_pictures );

		// Store pagination values for headers then unset for count query.
		$per_page = $query_args['number'];
		$page     = $query_args['paged'];

		$total_users = $user_query->get_total();
		if ( $total_users < 1 ) {

			// Out-of-bounds, run the query again without LIMIT for total count.
			unset( $query_args['number'], $query_args['paged'] );

			$count_query = new WP_User_Query( $query_args );
			$total_users = $count_query->get_total();

		}

		$response->header( 'X-WP-Total', (int) $total_users );

		$max_pages = ceil( $total_users / $per_page );
		$response->header( 'X-WP-TotalPages', (int) $max_pages );

		$base = add_query_arg( $request->get_query_params(), rest_url( sprintf( '/%s/%s', $this->namespace, $this->rest_base ) ) );
		if ( $page > 1 ) {

			$prev_page = $page - 1;

			if ( $prev_page > $max_pages ) {

				$prev_page = $max_pages;

			}

			$prev_link = add_query_arg( 'page', $prev_page, $base );

			$response->link_header( 'prev', $prev_link );

		}

		if ( $max_pages > $page ) {

			$next_page = $page + 1;
			$next_link = add_query_arg( 'page', $next_page, $base );
			$response->link_header( 'next', $next_link );

		}

		return $response;

	}

	/**
	 * @param int             $user_id
	 * @param WP_REST_Request $request
	 *
	 * @return WP_Error|array
	 */
	public function prepare_item_for_response( $user_id, $request ) {

		$profile_pictures_data     = [];
		$customer_profile_pictures = wc_customer_profile_pictures_get_user_pictures( $user_id );

		foreach ( $customer_profile_pictures as $picture_index => $profile_picture ) {

			$profile_pictures_data[] = [
				'index'       => $picture_index,
				'name'        => pathinfo( $profile_picture['file'], PATHINFO_BASENAME ),
				'file_type'   => wp_get_image_mime( $profile_picture['file'] ),
				'url'         => $profile_picture['url'],
				'customer_id' => $user_id,
				// In case full customer object needed
				// 'customer'    => wc()->api->get_endpoint_data( '/wc/v3/customers/' . $user_id ),
			];

		}

		/**
		 * Filter customer data returned from the REST API.
		 *
		 * @param array           $profile_pictures_data The response object.
		 * @param WP_REST_Request $request Request object.
		 *
		 * @since 1.0.0
		 *
		 * @return WP_Error|array
		 */
		return apply_filters( 'woocommerce_rest_prepare_customer_profile_picture', $profile_pictures_data, $request );

	}

	/**
	 * Get the Profile picture's schema, conforming to JSON Schema.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_item_schema() {

		$schema = [
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'customer',
			'type'       => 'object',
			'properties' => [
				'index'       => [
					'description' => __( 'Index for the profile picture.', 'woocommerce-customer-profile-pictures' ),
					'type'        => 'integer',
					'context'     => [ 'view' ],
					'readonly'    => true,
				],
				'name'        => [
					'description' => __( 'Profile picture file name.', 'woocommerce-customer-profile-pictures' ),
					'type'        => 'string',
					'context'     => [ 'view' ],
					'readonly'    => true,
				],
				'file_type'   => [
					'description' => __( 'Profile picture file type.', 'woocommerce-customer-profile-pictures' ),
					'type'        => 'string',
					'context'     => [ 'view' ],
					'readonly'    => true,
				],
				'url'         => [
					'description' => __( 'Profile picture public URL.', 'woocommerce-customer-profile-pictures' ),
					'type'        => 'string',
					'context'     => [ 'view' ],
					'readonly'    => true,
				],
				'customer_id' => [
					'description' => __( 'The associated Customer ID for the Profile picture.', 'woocommerce-customer-profile-pictures' ),
					'type'        => 'integer',
					'context'     => [ 'view' ],
					'readonly'    => true,
				],
			],
		];

		return $this->add_additional_fields_schema( $schema );

	}

	/**
	 * Retrieves the query params for the collections.
	 *
	 * @since 1.0.0
	 *
	 * @return array Query parameters for the collection.
	 */
	public function get_collection_params() {

		return [
			'context'     => $this->get_context_param(),
			'page'        => [
				'description'       => __( 'Current page of the collection.', 'woocommerce-customer-profile-pictures' ),
				'type'              => 'integer',
				'default'           => 1,
				'sanitize_callback' => 'absint',
				'validate_callback' => 'rest_validate_request_arg',
				'minimum'           => 1,
			],
			'per_page'    => [
				'description'       => __( 'Maximum number of items to be returned in result set.', 'woocommerce-customer-profile-pictures' ),
				'type'              => 'integer',
				'default'           => 10,
				'minimum'           => 1,
				'maximum'           => 100,
				'sanitize_callback' => 'absint',
				'validate_callback' => 'rest_validate_request_arg',
			],
			'customer_id' => [
				'description'       => __( 'Limit results to those matching a string.', 'woocommerce-customer-profile-pictures' ),
				'type'              => 'integer',
				'default'           => 0,
				'sanitize_callback' => 'absint',
				'validate_callback' => [ $this, 'validate_request_customer_id_args' ],
			],
		];

	}

	/**
	 * Validate a request argument based on details registered to the route.
	 *
	 * @param mixed           $value
	 * @param WP_REST_Request $request
	 * @param string          $param
	 *
	 * @since 4.7.0
	 *
	 * @return WP_Error|boolean
	 */
	public function validate_request_customer_id_args( $value, $request, $param ) {

		$normal_validation = rest_validate_request_arg( $value, $request, $param );

		if ( is_wp_error( $normal_validation ) ) {

			return $normal_validation;

		}

		$customer_id = absint( $value );

		if ( empty( $customer_id ) ) {

			return true;

		}

		if ( false === get_user_by( 'id', $customer_id ) ) {

			return new WP_Error( 'wc_customer_profile_pictures_invalid_customer_id', __( 'Invalid customer ID.', 'woocommerce-customer-profile-pictures' ) );

		}

		return true;

	}

}