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

			let $widgetSettings = $scope.data( 'settings' );

			if ( $widgetSettings && $widgetSettings.enable_load_more ) {
				let loadMoreType = $widgetSettings.load_more_type;

				switch ( loadMoreType ) {
					case 'click':
						let triggerId = '#' + $widgetSettings.load_more_trigger_id;

						$( document ).on( 'click', triggerId, function ( event ) {

							event.preventDefault();

							let wrapper = $scope.find( '.jet-woo-products' ),
								$loadMoreSettings = $( wrapper ).data( 'load-more-settings' ),
								$loadMoreQuery = $( wrapper ).data( 'load-more-query' ),
								$productsPerPage = $( wrapper ).data( 'product-per-page' );

							JetWooBuilderPGLM.ajaxRequest( wrapper, $loadMoreSettings, $loadMoreQuery, $productsPerPage );

						} );
						break;
					case 'scroll':
						if ( ! window.elementorFrontend || ! window.elementorFrontend.isEditMode() ) {
							let widgetID = $scope.data( 'id' );

							$( window )
								.off( 'scroll.JetWooBuilderInfinityScroll/' + widgetID )
								.on( 'scroll.JetWooBuilderInfinityScroll/' + widgetID, JetWooBuilderPGLM.debounce( 250, function () {

									let wrapper = $scope.find( '.jet-woo-products' ),
										$loadMoreSettings = $( wrapper ).data( 'load-more-settings' ),
										$loadMoreQuery = $( wrapper ).data( 'load-more-query' ),
										$productsPerPage = $( wrapper ).data( 'product-per-page' );

									if ( ! $( wrapper ).outerHeight() ) {
										return;
									}

									if ( $( window ).scrollTop() + $( window ).outerHeight() < $( wrapper ).offset().top + $( wrapper ).outerHeight() ) {
										return;
									}

									JetWooBuilderPGLM.ajaxRequest( wrapper, $loadMoreSettings, $loadMoreQuery, $productsPerPage );

								} ) );
						}
						break;
					default:
						break;
				}
			}

		},

		debounce: function( threshold, callback ) {

			let timeout;

			return function debounced( $event ) {
				function delayed() {
					callback.call( this, $event );
					timeout = null;
				}

				if ( timeout ) {
					clearTimeout( timeout );
				}

				timeout = setTimeout( delayed, threshold );
			};

		},

		ajaxRequest: function( wrapper, settings, query, number ) {
			$.ajax( {
				type: 'POST',
				url: window.jetWooBuilderData.ajax_url,
				dataType: 'json',
				data: {
					action: 'jet_woo_builder_load_more',
					settings: settings,
					query: query,
					productsPerPage: number
				},
			} ).done( function( response ) {
				let $html = $( response.data.html );

				$( wrapper ).parent().html( $html );
			} );
		}
	};

	$( window ).on( 'elementor/frontend/init', JetWooBuilderPGLM.init );

}( jQuery, window.elementorFrontend ) );