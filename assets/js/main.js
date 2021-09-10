( function( $, elementorFrontend ) {

	"use strict";

	let JetWooBuilderPGLM = {
		
		init: function () {
			let widgets = {
				'jet-woo-products.default' : JetWooBuilderPGLM.productsGridLoadMore
			};

			$.each( widgets, function( widget, callback ) {
				elementorFrontend.hooks.addAction( 'frontend/element_ready/' + widget, callback );
			});
		},

		productsGridLoadMore: function ( $scope ) {
			let $settings = $scope.data( 'settings' );

			if ( $settings && $settings.enable_load_more ) {
				let triggerId = '#' + $settings.load_more_trigger_id

				$( document ).on( 'click', triggerId, function ( event ) {
					event.preventDefault();

					let grid = $scope.find( '.pglm-settings-holder' );

					let $settings = $scope.find( '.pglm-settings-holder' ).data( 'load-more-settings' );

					$.ajax( {
						type: 'POST',
						url: window.jetWooBuilderData.ajax_url,
						dataType: 'json',
						data: {
							action: 'jet_woo_builder_load_more',
							settings: $settings
						},
					} ).done( function( response ) {
						let $html = $( response.data.html );

						$(grid).parent().html( $html );
					} );
				} );
			}
		}
	};

	$( window ).on( 'elementor/frontend/init', JetWooBuilderPGLM.init );

}( jQuery, window.elementorFrontend ) );