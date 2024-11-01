<?php
/**
 * Turinpay Webhook Class
 *
 * @package turinpay-plugin-for-woocommerce
 * @since 0.0.1
 */

namespace TPFW\Gateway\Turinpay;

use TPFW\Gateway\Abstract_Payment_Gateway;
use TPFW\Inc\Traits\Get_Instance;
use TPFW\Inc\Helper;
use TPFW\Inc\Logger;
use DateTime;

/**
 * Webhook endpoints
 */
class Webhook extends Abstract_Payment_Gateway {

	use Get_Instance;

	/**
	 * Constructor function
	 */
	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'register_endpoints' ] );
	}

	/**
	 * Registers endpoint for Turinpay webhook
	 *
	 * @return void
	 */
	public function register_endpoints() {
		register_rest_route(
			'tpfw',
			'/v1/webhook',
			array(
				'methods'             => 'POST',
				'callback'            => [ $this, 'webhook_listener' ],
				'permission_callback' => function() {
					return true;
				},
			)
		);
	}

	/**
	 * This function listens webhook events from Turinpay.
	 *
	 * @return void
	 */
	public function webhook_listener() {
		//update_option( 'tpfw_turinpay_response_webhook', $_POST );
	}

}
