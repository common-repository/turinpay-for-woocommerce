<?php
/**
 * Turinpay Gateway
 *
 * @package turinpay-plugin-for-woocommerce
 * @since 0.0.1
 */

namespace TPFW\Gateway\Turinpay;

use TPFW\Inc\Helper;
use TPFW\Inc\Logger;
use TPFW\Inc\Traits\Get_Instance;
use TPFW\Gateway\Abstract_Payment_Gateway;
use WC_AJAX;
/**
 * Turinpay_Payments
 *
 * @since 0.0.1
 */
class Turinpay_Payments extends Abstract_Payment_Gateway {

	use Get_Instance;

	/**
	 * Gateway id
	 *
	 * @var string
	 */
	public $id = 'tpfw_payment';

	/**
	 * Constructor
	 *
	 * @since 0.0.1
	 */
	public function __construct() {
		parent::__construct();

		$this->method_title       = __( 'Turinpay Processing', 'turinpay-plugin-for-woocommerce' );
		$this->method_description = __( 'Accepts payments via TurinPay', 'turinpay-plugin-for-woocommerce' );
		$this->has_fields         = true;
		$this->init_supports();

		$this->init_form_fields();
		$this->init_settings();
		// get_option should be called after init_form_fields().
		$this->title                = $this->get_option( 'title' );
		$this->description          = $this->get_option( 'description' );
		$this->order_button_text    = $this->get_option( 'order_button_text' );

		add_filter( 'woocommerce_payment_successful_result', [ $this, 'modify_successful_payment_result' ], 999, 2 );
		add_filter( 'woocommerce_payment_complete_order_status', [ $this, 'tpfw_payment_complete_order_status' ], 10, 3 );
	}

	/**
	 * Registers supported filters for payment gateway
	 *
	 * @return void
	 */
	public function init_supports() {
		$this->supports = apply_filters(
			'tpfw_card_payment_supports',
			[
				'products',
				'refunds',
			]
		);
	}

	/**
	 * Gateway form fields
	 *
	 * @return void
	 */
	public function init_form_fields() {
		$this->form_fields = apply_filters(
			'tpfw_card_payment_form_fields',
			[
				'enabled'              => [
					'label'   => '',
					'type'    => 'checkbox',
					'title'   => __( 'Enable Turinpay Gateway', 'turinpay-plugin-for-woocommerce' ),
					'default' => 'no',
				],
				'title'                => [
					'title'       => __( 'Title', 'turinpay-plugin-for-woocommerce' ),
					'type'        => 'text',
					'description' => __( 'Title of Card Element', 'turinpay-plugin-for-woocommerce' ),
					'default'     => __( 'Turinpay', 'turinpay-plugin-for-woocommerce' ),
					'desc_tip'    => true,
				],
				'description'          => [
					'title'       => __( 'Description', 'turinpay-plugin-for-woocommerce' ),
					'type'        => 'textarea',
					'css'         => 'width:25em',
					'description' => __( 'Description on Card Elements for Live mode', 'turinpay-plugin-for-woocommerce' ),
					'default'     => __( 'Pay with your TurinWallet Apps', 'turinpay-plugin-for-woocommerce' ),
					'desc_tip'    => true,
				],
				'order_button_text'    => [
					'title'       => __( 'Order Button Label', 'turinpay-plugin-for-woocommerce' ),
					'type'        => 'text',
					'description' => __( 'Customize label for Order button', 'turinpay-plugin-for-woocommerce' ),
					'default'     => __( 'Pay via Turinpay', 'turinpay-plugin-for-woocommerce' ),
					'desc_tip'    => true,
				],
			]
		);
	}

	/**
	 * Process woocommerce orders after payment is done
	 *
	 * @param int $order_id wooCommerce order id.
	 * @return array data to redirect after payment processing.
	 */
	public function process_payment( $order_id ) {
		$order = wc_get_order( $order_id );

		if( ! isset( $_POST['tpfw_payment_intent_id'] ) ) {
			return [
				'result'        => 'success',
				'redirect'      => false,
				'intent_secret' => time(),
			];
		}

		$intent_id = sanitize_text_field( $_POST['tpfw_payment_intent_id'] );

		update_post_meta( $order_id, 'tpfw_payment_intent_id', $intent_id );

		$order->payment_complete( $intent_id );
		/* translators: order id */
		Logger::info( sprintf( __( 'Payment successful Order id - %1s', 'turinpay-plugin-for-woocommerce' ), $order->get_id() ), true );
		$order->add_order_note( __( 'Payment Status: ', 'checkout-plugins-paypal-woo' ) . ucfirst( 'paid' ) . ', ' . __( 'Source: Payment is Completed via TurinPay', 'turinpay-plugin-for-woocommerce' ) );

		WC()->cart->empty_cart();

		return [
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		];
	}

	/**
	 * Modify redirect url
	 *
	 * @param array $result redirect url array.
	 * @param int   $order_id woocommerce order id.
	 * @return array modified redirect url array.
	 */
	public function modify_successful_payment_result( $result, $order_id ) {
		if ( empty( $order_id ) ) {
			return $result;
		}

		$order = wc_get_order( $order_id );

		if ( $this->id !== $order->get_payment_method() ) {
			return $result;
		}

		if ( ! isset( $result['intent_secret'] ) ) {
			return $result;
		}

		// Put the final thank you page redirect into the verification URL.
		$verification_url = add_query_arg(
			[
				'order'                 => $order_id,
				'confirm_payment_nonce' => wp_create_nonce( 'tpfw_confirm_payment_intent' ),
				'redirect_to'           => rawurlencode( $result['redirect'] ),
			],
			WC_AJAX::get_endpoint( 'tpfw_payment_verify_payment_intent' )
		);

		// Combine into a hash.
		$redirect = sprintf( '#confirm-pi-%s:%s:%s', $result['intent_secret'], rawurlencode( $verification_url ), $this->id );

		return [
			'result'   => 'success',
			'redirect' => $redirect,
		];
	}

	/**
	 * Get Turinpay activated payment cards icon.
	 */
	public function get_icon() {
		return apply_filters( 'woocommerce_gateway_icon', '<span class="tpfw_payment_icons">' . $this->payment_icons( $this->id ) . '</span>', $this->id );
	}

	/**
	 * Creates markup for payment form for card payments
	 *
	 * @return void
	 */
	public function payment_fields() {
		/**
		 * Action before payment field.
		 *
		 * @since x.x.x
		 */
		do_action( $this->id . '_before_payment_field_checkout' );

		echo '<div class="status-box"></div>';
		echo '<div class="tpfw-turinpay-pay-data">';
		echo '<div class="tpfw-turinpay-info">';
		echo wp_kses_post( wpautop( $this->description ) );
		echo '</div>';
		if ( 'test' === Helper::get_payment_mode() ) {
			echo '<div class="tpfw-test-description">';
			/* translators: %1$1s - %6$6s: HTML Markup */
			printf( esc_html__( '%1$1s Test Mode Enabled:%2$2s Use demo info for payment.', 'turinpay-plugin-for-woocommerce' ), '<b>', '</b>' );
			echo '</div>';
		}
		echo '</div>';
		/**
		 * Action after payment field.
		 *
		 * @since 1.0.0
		 */
		do_action( $this->id . '_after_payment_field_checkout' );
	}

	/**
	 * Updates order status as per option 'order_status' set in card payment settings
	 *
	 * @param string   $order_status default order status.
	 * @param id       $order_id current order id.
	 * @param WC_Order $order current order.
	 * @return string
	 */
	public function tpfw_payment_complete_order_status( $order_status, $order_id, $order = null ) {
		if ( $order && $order->get_payment_method() ) {
			$gateway = $order->get_payment_method();
			if ( $this->id === $gateway && ! empty( $this->get_option( 'order_status' ) ) ) {
				$order_status = $this->get_option( 'order_status' );
			}
		}

		return apply_filters( 'tpfw_payment_complete_order_status', $order_status );
	}
}
