( function( $, elementorFrontend ) {

	"use strict";

	let pageHolder = 1;

	let JetWooBuilderPGLM = {

		init: function () {

			elementorFrontend.hooks.addAction( 'frontend/element_ready/jet-woo-products.default', JetWooBuilderPGLM.productsGridLoadMore );

			$( document ).on( 'jet-filter-content-rendered', function( event, $scope ) {
				JetWooBuilderPGLM.productsGridLoadMore( $scope.closest( '.elementor-element' ) );

				pageHolder = 1;
			} )

		},

		productsGridLoadMore: function ( $scope ) {

			let $widgetSettings = $scope.data( 'settings' );

			if ( $widgetSettings && $widgetSettings.enable_load_more ) {
				let loadMoreType = $widgetSettings.load_more_type;

				switch ( loadMoreType ) {
					case 'click':
						let triggerId = '#' + $widgetSettings.load_more_trigger_id,
							grid = $scope.find( '.jet-woo-products' ),
							page = $( grid ).data( 'products-page' ),
							pages = $( grid ).data( 'products-pages' );

						if ( page === pages ) {
							$( triggerId ).css( 'display', 'none' );
						} else {
							$( triggerId ).removeAttr( 'style' );
						}

						$( document ).on( 'click', triggerId, function ( event ) {

							event.preventDefault();

							let wrapper = $scope.find( '.jet-woo-products' ),
								$loadMoreSettings = $( wrapper ).data( 'load-more-settings' ),
								$loadMoreQuery = $( wrapper ).data( 'load-more-query' ),
								productsPerPage = $( wrapper ).data( 'product-per-page' ),
								productsPage = $( wrapper ).data( 'products-page' ),
								productsPages = $( wrapper ).data( 'products-pages' );

							if ( productsPage === productsPages ) {
								return;
							}

							$( 'html, body' ).animate( {
								scrollTop: $( wrapper ).outerHeight() + $( wrapper ).offset().top
							}, 1000 );

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

									let wrapper = $scope.find( '.jet-woo-products' ),
										$loadMoreSettings = $( wrapper ).data( 'load-more-settings' ),
										$loadMoreQuery = $( wrapper ).data( 'load-more-query' ),
										productsPerPage = $( wrapper ).data( 'product-per-page' ),
										productsPage = $( wrapper ).data( 'products-page' ),
										productsPages = $( wrapper ).data( 'products-pages' );

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
									<div class="skeleton-content-wrapper">
										<div class="skeleton skeleton-title"></div>
										<div class="skeleton skeleton-price"></div>
										<div class="skeleton-text-wrapper">
											<div class="skeleton skeleton-text"></div>
											<div class="skeleton skeleton-text"></div>
											<div class="skeleton skeleton-text"></div>
										</div>
										<div class="skeleton skeleton-btn"></div>
									</div>
								</div>
							</div>`;

			if ( pageHolder < page ){
				for ( let i = 0; i < productsNumber; i++ ) {
					$( wrapper ).append( skeleton );
				}
			}

			pageHolder = page;

			let $widget = wrapper.closest( '.elementor-element' ),
				pagination = $.find( '.jet-smart-filters-pagination[data-query-id="' + $widget.attr('id') + '"]' );

			if ( pagination ) {
				$( pagination ).find( '.jet-filters-pagination__item' ).each( function() {

					if ( $( this ).hasClass( 'jet-filters-pagination__current' ) ) {
						$( this ).removeClass( 'jet-filters-pagination__current' );
					}

					if ( page === $( this ).data( 'value' ) ) {
						$( this ).addClass( 'jet-filters-pagination__current' );
					}

				} );
			}

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