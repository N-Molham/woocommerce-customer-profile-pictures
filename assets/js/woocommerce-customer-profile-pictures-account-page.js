(function ( $ ) {

	$( function () {

		const $profile_pictures_fieldset     = $( '#wc-customer-profile-pictures-fieldset' ),
		      $profile_pictures_list         = $profile_pictures_fieldset.find( '.wc-customer-profile-pictures-list' ),
		      profile_picture_field_template = $( '#wc-customer-profile-picture-field-template' ).html();

		$( '#wc-customer-profile-pictures-add-new' ).on( 'click', function ( e ) {

			e.preventDefault();

			let current_pictures_count = $profile_pictures_list.find( 'p.wc-customer-profile-picture-field' ).length;

			let new_field = profile_picture_field_template.replace( /{index}/g, current_pictures_count ).replace( /{index_label}/g, current_pictures_count + 1 );

			$profile_pictures_list.append( new_field );

			current_pictures_count++;

			if ( current_pictures_count >= $profile_pictures_fieldset.data( 'maximum' ) ) {

				$( this ).remove();

			}

		} )

	} );

})( jQuery );