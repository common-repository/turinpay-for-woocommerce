<?php
/**
 * Turinpay Api Wrapper
 *
 * @package turinpay-plugin-for-woocommerce
 * @since 0.0.1
 */

namespace TPFW\Gateway\Turinpay;

use CPSW\Inc\Logger;

/**
 * Turinpay Api Class
 */
class Turinpay_Api {

	/**
	 * Instance of Turinpay
	 *
	 * @var Turinpay
	 */
	public $client;

	/**
	 * Constructor
	 *
	 * @since 0.0.1
	 */
	public function __construct() {
		$client_id  = apply_filters( 'tpfw_get_secret_key', ( 'live' === get_option( 'tpfw_mode' ) ) ? get_option( 'tpfw_client_id' ) : get_option( 'tpfw_test_client_id' ) );
		$secret_key = apply_filters( 'tpfw_get_secret_key', ( 'live' === get_option( 'tpfw_mode' ) ) ? get_option( 'tpfw_secret_key' ) : get_option( 'tpfw_test_secret_key' ) );

		if ( ! empty( $client_id ) && ! empty( $secret_key ) ) {
			// will add logics
		}
	}
}
