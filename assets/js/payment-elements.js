( function( $ ) {
	if ( '' === tpfw_global_settings.api_key ) {
		return;
	}

	function initializePaymentButton() {
		fetch('https://backend.turinlabs.com/graphql', {
		method: 'POST',
		headers: {
			'apiKey': tpfw_global_settings.api_key,
			"Content-Type": "application/json",
		},
		body: JSON.stringify({
			query: `
			mutation CREATE_PAYMENT_INTENT_MUTATION($input: CreatePaymentIntentInput!) {
				createTPayPaymentIntent(input: $input) {
				__typename
				... on TPayPaymentIntent {
					id
				}
				... on Error {
					code
					message
					description
				}
				}
			}
			`,
			variables: {
				"input": {
					"notificationUrl": tpfw_global_settings.webhook_url,
					"orderId": tpfw_global_settings.webhook_url,
					"currency": tpfw_global_settings.currency_code,
					"price": parseFloat(tpfw_global_settings.cart_total),
					"description": "Payment making with TurinPay, powered by WooCommerce.",
					"initDate": tpfw_global_settings.initDate,
					"endDate": tpfw_global_settings.endDate,
					"active": true,
					"multipleInvoicesAllowed": true,
					"invoiceExpirationTimeMin": 30,
					"onlyLN": false
				}
			},
		}),
		})
		.then((res) => res.json())
		.then((result) => {
			//console.log(result);
			if ( result.data.createTPayPaymentIntent.id ) {
				$("#tpfw-payment-button-container").html('<div class="tpfw-payment-button-popup-content"><span class="tpfw-payment-button-popup-close">'+tpfw_global_settings.close_msg+'</span><h3 class="tpfw-payment-button-popup-pay-now">'+tpfw_global_settings.pay_now_text+'</h3><turinpay-button size="big" id="tpfw-pay-button-id" paymentIntent="'+result.data.createTPayPaymentIntent.id+'" paidText="Paid 🚀"" /></turinpay-button></div><div class="tpfw-payment-button-popup-shadow"></div>');
				
				let elem = document.querySelector('#tpfw-pay-button-id');
				let paymentForm = $( 'form.woocommerce-checkout' );

				$(".tpfw-payment-button-popup-close").on( 'click', function(e) {
					$("#tpfw-payment-button-container").removeClass('tpfw-payment-button-popup-enabled');
					$("#place_order").show();
				});

				if ( elem.addEventListener ) {
					elem.addEventListener('paid', function (elem) {
						$("#tpfw-payment-button-container").hide();
						//console.log( elem );
						let intentID = result.data.createTPayPaymentIntent.id;
						
						paymentForm.append(
							"<input type='hidden' class='tpfw_payment_intent_id' name='tpfw_payment_intent_id' value='" +
							intentID +
							"'/>",
						);
						paymentForm.trigger( 'submit' );
					}, false);
				}
			} else {
				$("#tpfw-payment-button-container").html('<span class="wc-error">'+tpfw_global_settings.error_msg+'</span>');
			}
		});
	}

	function cppwPlaceOrderButtonHide() {
		const selectedPaymentMethod = $( '.wc_payment_method input[name="payment_method"]:checked' ).val();

		if ( 'tpfw_payment' === selectedPaymentMethod ) {
			$("#tpfw-payment-button-container").addClass('tpfw-payment-button-popup-enabled');
			$("#tpfw-payment-button-container").show();
			$("#place_order").hide();
		} else {
			$("#place_order").show();
			$("#tpfw-payment-button-container").hide();
		}
	}

	function onHashChange() {
		let selectedPaymentMethod = $( '.wc_payment_method input[name="payment_method"]:checked' ).val();
		let wcCheckoutForm = $( 'form.woocommerce-checkout' );

		if ( 'tpfw_payment' !== selectedPaymentMethod ) {
			return;
		}
		const partials = window.location.hash.match(
			/^#?confirm-(pi|si)-([^:]+):(.+):(.+)$/,
		);

		if ( ! partials || 4 > partials.length ) {
			return;
		}

		// Cleanup the URL
		history.pushState( {}, '', window.location.pathname );
		cppwPlaceOrderButtonHide();
		$( '.woocommerce-error' ).remove();
		wcCheckoutForm.unblock();
		wcCheckoutForm.removeClass( 'processing' );
	}

	window.addEventListener( 'hashchange', onHashChange );

	$( document.body ).on( 'updated_checkout', function() {
		initializePaymentButton();
		$("#place_order").show();
		$("#tpfw-payment-button-container").hide();
		$("#tpfw-payment-button-container").removeClass('tpfw-payment-button-popup-enabled');
	} );
}( jQuery ) );
