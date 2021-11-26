( function( $, elementorFrontend ) {

	"use strict";

	let pageHolder = 1;

	let JetWooBuilderPGLM = {

		init: function () {
			let widgets = {
				'jet-woo-products.default' : JetWooBuilderPGLM.productsGridLoadMore
			};

			$.each( widgets, function( widget, callback ) {
				elementorFrontend.hooks.addAction( 'frontend/element_ready/' + widget, callback );
			});

			$( document ).on( 'jet-filter-content-rendered', function() {
				pageHolder = 1;
			} )
		},

		productsGridLoadMore: function ( $scope ) {

			let $widgetSettings = $scope.data( 'settings' );

			if ( $widgetSettings && $widgetSettings.enable_load_more ) {
				let loadMoreType = $widgetSettings.load_more_type,
					wrapper = $scope.find( '.jet-woo-products' ),
					$loadMoreSettings = $( wrapper ).data( 'load-more-settings' ),
					$loadMoreQuery = $( wrapper ).data( 'load-more-query' ),
					productsPerPage = $( wrapper ).data( 'product-per-page' ),
					productsPage = parseInt( $( wrapper ).data( 'products-page' ), 10 ) || 0,
					productsPages = parseInt( $( wrapper ).data( 'products-pages' ), 10 ) || 0;

				switch ( loadMoreType ) {
					case 'click':
						let triggerId = '#' + $widgetSettings.load_more_trigger_id;

						$( document ).on( 'click', triggerId, function ( event ) {

							event.preventDefault();

							if ( productsPage === productsPages ) {
								return;
							}

							$("html, body").animate({
								scrollTop: $( wrapper ).outerHeight()
							}, 1000);

							productsPage++;

							JetWooBuilderPGLM.ajaxRequest( wrapper, $loadMoreSettings, $loadMoreQuery, productsPerPage, productsPage, productsPages, triggerId );

						} );
						break;
					case 'scroll':
						if ( ! window.elementorFrontend || ! window.elementorFrontend.isEditMode() ) {
							let widgetID = $scope.data( 'id' );

							$( window )
								.off( 'scroll.JetWooBuilderInfinityScroll/' + widgetID )
								.on( 'scroll.JetWooBuilderInfinityScroll/' + widgetID, JetWooBuilderPGLM.debounce( 250, function () {

									if ( productsPage === productsPages ) {
										return;
									}

									if ( ! $( wrapper ).outerHeight() ) {
										return;
									}

									if ( $( window ).scrollTop() + $( window ).outerHeight() < $( wrapper ).offset().top + $( wrapper ).outerHeight() ) {
										return;
									}

									productsPage++;

									JetWooBuilderPGLM.ajaxRequest( wrapper, $loadMoreSettings, $loadMoreQuery, productsPerPage, productsPage, productsPages );

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

		ajaxRequest: function( wrapper, settings, query, productsNumber, page, pages, triggerButton ) {

			if ( $( triggerButton ).length ) {
				if ( page === pages && ! window.elementor ) {
					$( triggerButton ).css( 'display', 'none' );
				} else {
					$( triggerButton ).removeAttr( 'style' );
				}
			}

			let skeleton = `<div class="jet-woo-products__item jet-woo-builder-product">
								<div class="jet-woo-products__inner-box">
									<div class="skeleton skeleton-image"></div>
									<div class="skeleton skeleton-title"></div>
									<div class="skeleton skeleton-price"></div>
									<div class="skeleton-text-wrapper">
										<div class="skeleton skeleton-text"></div>
										<div class="skeleton skeleton-text"></div>
										<div class="skeleton skeleton-text"></div>
									</div>
									<div class="skeleton skeleton-btn"></div>
								</div>
							</div>`;

			if ( pageHolder < page ){
				for ( let i = 0; i < productsNumber; i++ ) {
					$( wrapper ).append( skeleton );
				}
			}

			pageHolder = page;

			$.ajax( {
				type: 'POST',
				url: window.jetWooBuilderData.ajax_url,
				dataType: 'json',
				data: {
					action: 'jet_woo_builder_load_more',
					settings: settings,
					query: query,
					productsPerPage: productsNumber,
					page: page,
					pages: pages
				},
			} ).done( function( response ) {
				let $html = $( response.data.html );

				$( wrapper ).parent().html( $html );

				$( document ).trigger('jet-load-more-content-rendered', [ $html ] );
			} );

		}

	};

	$( window ).on( 'elementor/frontend/init', JetWooBuilderPGLM.init );

}( jQuery, window.elementorFrontend ) );