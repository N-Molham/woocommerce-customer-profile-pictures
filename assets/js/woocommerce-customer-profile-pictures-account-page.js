(function ( $ ) {

	$( function () {

		const $profile_pictures_fieldset     = $( '#wc-customer-profile-pictures-fieldset' ),
		      $profile_pictures_list         = $profile_pictures_fieldset.find( '.wc-customer-profile-pictures-list' ),
		      $profile_pictures_add_new      = $( '#wc-customer-profile-pictures-add-new' ),
		      profile_picture_field_template = $( '#wc-customer-profile-picture-field-template' ).html();

		$profile_pictures_list.on( 'click', 'button.wc-customer-profile-pictures-remove', function ( e ) {

			e.preventDefault();

			if ( confirm( wc_customer_profile_pictures.i18n.confirm_remove ) ) {

				$( this ).closest( 'div.wc-customer-profile-picture-field' ).remove();

				$profile_pictures_add_new.removeClass( 'hidden' );

			}

		} );

		$profile_pictures_add_new.on( 'click', function ( e ) {

			e.preventDefault();

			let last_added_index = parseInt( $profile_pictures_fieldset.attr( 'data-last' ) );

			if ( isNaN( last_added_index ) ) {

				last_added_index = 0;

			} else {

				last_added_index++;

			}

			let new_field = profile_picture_field_template.replace( /{index}/g, last_added_index ).replace( /{index_label}/g, last_added_index + 1 );

			$profile_pictures_list.append( new_field );

			if ( 0 === last_added_index ) {

				$profile_pictures_fieldset.find( '.woocommerce-notices-wrapper' ).remove();

			}

			$profile_pictures_fieldset.attr( 'data-last', last_added_index );

			console.log( $profile_pictures_list.find( '.wc-customer-profile-picture-field' ).length );
			
			if ( $profile_pictures_list.find( '.wc-customer-profile-picture-field' ).length >= $profile_pictures_fieldset.data( 'maximum' ) ) {

				$( this ).addClass( 'hidden' );

			}

		} )

	} );

})( jQuery );