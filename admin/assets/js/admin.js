( function( $ ) {
	$( 'a[href="' + tpfw_ajax_object.site_url + '&tab=checkout&section=tpfw_api_settings"]' ).attr( 'href', tpfw_ajax_object.site_url + '&tab=tpfw_api_settings' );

	$( 'a[href="' + tpfw_ajax_object.site_url + '&tab=checkout&section="]' ).closest( 'li' ).remove();

	if ( $( 'a[href="' + tpfw_ajax_object.site_url + '&tab=tpfw_api_settings"]' ).hasClass( 'nav-tab-active' ) ) {
		$( 'a[href="' + tpfw_ajax_object.site_url + '&tab=checkout"]' ).addClass( 'nav-tab-active' );
	}
	
	$( document ).ready( function() {
		$( '.tpfw_select_woo' ).selectWoo();
	} );

	const tpfwAdminPaymentSettings = {
		init() {
			$( '[name^="woocommerce_' + tpfw_ajax_object.tpfw_admin_current_page + '_allowed_countries"]' ).on( 'change', this.toggle_select_country_sections );
			$( '[name^="tpfw_mode"]' ).on( 'change', this.show_hide_webhook_secret );

			this.toggle_select_country_sections();
			this.show_hide_webhook_secret();
		},

		/**
		 * Show hide webhook secret
		 */
		show_hide_webhook_secret() {
			const mode = $( '#tpfw_mode' ).val();

			if ( 'test' === mode ) {
				$( '#tpfw_test_webhook_secret' ).parents( 'tr' ).show();
				$( '#tpfw_live_webhook_secret' ).parents( 'tr' ).hide();
			} else if ( 'live' === mode ) {
				$( '#tpfw_test_webhook_secret' ).parents( 'tr' ).hide();
				$( '#tpfw_live_webhook_secret' ).parents( 'tr' ).show();
			}
		},

		/**
		 * Show hide country dorpdown
		 */
		toggle_select_country_sections() {
			const getOption = $( ' [name^="woocommerce_' + tpfw_ajax_object.tpfw_admin_current_page + '_allowed_countries"] ' ).val();
			const exceptCountries = $( '[name^="woocommerce_' + tpfw_ajax_object.tpfw_admin_current_page + '_except_countries[]"]' ).parents( 'tr' );
			const specificCountries = $( '[name^="woocommerce_' + tpfw_ajax_object.tpfw_admin_current_page + '_specific_countries[]"]' ).parents( 'tr' );

			if ( getOption === 'all_except' ) {
				exceptCountries.show();
				specificCountries.hide();
			} else if ( getOption === 'specific' ) {
				exceptCountries.hide();
				specificCountries.show();
			} else {
				exceptCountries.hide();
				specificCountries.hide();
			}
		},
	};

	$( function() {
		tpfwAdminPaymentSettings.init();
	} );
}( jQuery ) );