<?php
/**
 * Abstract Payment Gateway
 *
 * @package turinpay-plugin-for-woocommerce
 * @since 0.0.1
 */

namespace TPFW\Gateway;

use WC_Payment_Gateway;
use TPFW\Inc\Helper;

/**
 * Abstract Payment Gateway
 *
 * @since 0.0.1
 */
abstract class Abstract_Payment_Gateway extends WC_Payment_Gateway {

	/**
	 * Url of assets directory
	 *
	 * @var string
	 */
	private $assets_url = TPFW_URL . 'assets/';

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'woocommerce_payment_gateways', [ $this, 'add_gateway_class' ], 999 );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, [ $this, 'process_admin_options' ] );
	}

	/**
	 * Adds transaction url in order details page
	 *
	 * @param WC_Order $order current order.
	 * @return string
	 */
	public function get_transaction_url( $order ) {
		if ( 'test' === Helper::get_payment_mode() ) {
			$this->view_transaction_url = 'https://dashboard.turinpay.com/test/payments/%s';
		} else {
			$this->view_transaction_url = 'https://dashboard.turinpay.com/payments/%s';
		}

		return parent::get_transaction_url( $order );
	}

	/**
	 * Get Order description string
	 *
	 * @param WC_Order $order current order.
	 * @return string
	 */
	public function get_order_description( $order ) {
		return apply_filters( 'tpfw_get_order_description', get_bloginfo( 'name' ) . ' - ' . __( 'Order ', 'turinpay-plugin-for-woocommerce' ) . $order->get_id() );
	}

	/**
	 * Registering Gateway to WooCommerce
	 *
	 * @param array $methods List of registered gateways.
	 * @return array
	 */
	public function add_gateway_class( $methods ) {
		array_unshift( $methods, $this );
		return $methods;
	}

	/**
	 * Get WooCommerce currency
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_currency() {
		global $wp;

		if ( isset( $wp->query_vars['order-pay'] ) ) {
			$order = wc_get_order( absint( $wp->query_vars['order-pay'] ) );

			return $order->get_currency();
		}

		return get_woocommerce_currency();
	}

	/**
	 * Checks whether this gateway is available.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean
	 */
	public function is_available() {
		if ( 'yes' !== $this->enabled ) {
			return false;
		}

		if ( ! Helper::get_payment_mode() && is_checkout() ) {
			return false;
		}

		if ( 'test' === Helper::get_payment_mode() ) {
			if ( empty( Helper::get_setting( 'tpfw_test_api_key' ) ) ) {
				return false;
			}
		} else {
			if ( empty( Helper::get_setting( 'tpfw_live_api_key' ) ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Refunds amount from Turinpay and return true/false as result
	 *
	 * @param string $order_id order id.
	 * @param string $amount refund amount.
	 * @param string $reason reason of refund.
	 * @return bool
	 */
	public function process_refund( $order_id, $amount = null, $reason = '' ) {
		if ( 0 >= $amount ) {
			return false;
		}

		return true;
	}

	/**
	 * Add metadata to Turinpay
	 *
	 * @since 1.0.0
	 *
	 * @param int $order_id WooCommerce order Id.
	 *
	 * @return array
	 */
	public function get_metadata( $order_id ) {
		$order              = wc_get_order( $order_id );
		$details            = [];
		$billing_first_name = $order->get_billing_first_name();
		$billing_last_name  = $order->get_billing_last_name();
		$name               = $billing_first_name . ' ' . $billing_last_name;

		if ( ! empty( $name ) ) {
			$details['name'] = $name;
		}

		if ( ! empty( $order->get_billing_email() ) ) {
			$details['email'] = $order->get_billing_email();
		}

		if ( ! empty( $order->get_billing_phone() ) ) {
			$details['phone'] = $order->get_billing_phone();
		}

		if ( ! empty( $order->get_billing_address_1() ) ) {
			$details['address'] = $order->get_billing_address_1();
		}

		if ( ! empty( $order->get_billing_city() ) ) {
			$details['city'] = $order->get_billing_city();
		}

		if ( ! empty( $order->get_billing_country() ) ) {
			$details['country'] = $order->get_billing_country();
		}

		$details['site_url'] = get_site_url();

		return apply_filters( 'tpfw_metadata_details', $details, $order );
	}

	/**
	 * All payment icons that work with Turinpay
	 *
	 * @since 1.0.0
	 *
	 * @param string $gateway_id gateway id to fetch icon.
	 *
	 * @return array
	 */
	public function payment_icons( $gateway_id ) {
		$icons = [
			'tpfw_payment' => '<img src="' . $this->assets_url . 'icon/tpfw_payment.png" class="tpfw-payment-icon" alt="Alipay" width="100px" />',
		];

		return apply_filters(
			'tpfw_payment_icons',
			isset( $icons[ $gateway_id ] ) ? $icons[ $gateway_id ] : ''
		);
	}
}
