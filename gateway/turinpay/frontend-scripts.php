<?php
/**
 * Turinpay Frontend Scripts
 *
 * @package turinpay-plugin-for-woocommerce
 * @since 0.0.1
 */

namespace TPFW\Gateway\Turinpay;

use TPFW\Inc\Traits\Get_Instance;
use TPFW\Inc\Helper;

/**
 * Consists frontend scripts for payment gateways
 */
class Frontend_Scripts {

	use Get_Instance;

	/**
	 * Prefix
	 *
	 * @var string
	 */
	private $prefix = 'tpfw-';

	/**
	 * Version
	 *
	 * @var string
	 */
	private $version = '';

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
		$this->version = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? time() : TPFW_VERSION;
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'wp_footer', [ $this, 'render_payment_buttons' ] );
	}

	/**
	 * Enqueue scripts
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		$api_key = apply_filters( 'tpfw_get_secret_key', ( 'live' === get_option( 'tpfw_mode' ) ) ? get_option( 'tpfw_live_api_key' ) : get_option( 'tpfw_test_api_key' ) );

		if (
			'yes' === Helper::get_setting( 'enabled', 'tpfw_payment' )
		) {
			$this->enqueue_card_payments_scripts( $api_key );
		}
	}

	/**
	 * Render payment buttons
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function render_payment_buttons() {
		if (
			'yes' === Helper::get_setting( 'enabled', 'tpfw_payment' )
		) {
			echo '<div id="tpfw-payment-button-container" class=""></div>';
		}
	}

	/**
	 * Enqueue card payments scripts
	 *
	 * @since 1.0.0
	 *
	 * @param string $api_key payment public key.
	 *
	 * @return void
	 */
	private function enqueue_card_payments_scripts( $api_key ) {
		wp_register_script( $this->prefix . 'payment-elements-js', $this->assets_url . 'js/payment-elements.js', [ 'jquery' ], $this->version, true );
		wp_enqueue_script( $this->prefix . 'payment-elements-js' );

		wp_register_script( $this->prefix . 'turinpay-elements-js', $this->assets_url . 'js/turinpay/build/index.js', [], $this->version, true );
		wp_enqueue_script( $this->prefix . 'turinpay-elements-js' );

		wp_register_style( $this->prefix . 'payment-elements', $this->assets_url . 'css/payment-elements.css', [], $this->version );
		wp_enqueue_style( $this->prefix . 'payment-elements' );

		$tracking_id  = $this->random_string( 32 );

		// Save tracking id
		update_option( 'tpfw_turinpay_tracking_id', $tracking_id );

		wp_localize_script(
			$this->prefix . 'payment-elements-js',
			'tpfw_global_settings',
			[
				'api_key'              => $api_key,
				'tpfw_version'         => TPFW_VERSION,
				'is_ssl'               => is_ssl(),
				'mode'                 => Helper::get_payment_mode(),
				'ajax_url'             => admin_url( 'admin-ajax.php' ),
				'currency_code'        => get_woocommerce_currency(),
				'js_nonce'             => wp_create_nonce( 'tpfw_js_error_nonce' ),
				'get_home_url'         => get_home_url(),
				'current_user_billing' => $this->get_current_user_billing_details(),
				'cart_total'           => WC()->cart->total,
				'payment_type'		   => Helper::get_setting( 'payment_type', 'tpfw_payment' ),
				'webhook_url'		   => esc_url( get_home_url() . '/wp-json/tpfw/v1/webhook' ),
				'initDate'		       => date('Y-m-dT H:i:s'),
				'endDate'		       => date('Y-m-dT H:i:s', strtotime("+5 min")),
				'tracking_id'		   => $tracking_id,
				'error_msg'		       => __( 'There have some error with rending the button, refresh the page and try again.', 'turinpay-plugin-for-woocommerce' ),
				'close_msg'		       => __( 'Close', 'turinpay-plugin-for-woocommerce' ),
				'pay_now_text'		   => Helper::get_setting( 'description', 'tpfw_payment' ),
			]
		);
	}

	/**
	 * Get current user billing details
	 *
	 * @since 1.4.0
	 *
	 * @return array
	 */
	public function get_current_user_billing_details() {
		if ( ! is_user_logged_in() ) {
			return;
		}

		$user = wp_get_current_user();

		if ( ! empty( $user->display_name ) ) {
			$details['name'] = $user->display_name;
		}

		if ( ! empty( $user->user_email ) ) {
			$details['email'] = $user->user_email;
		}

		return apply_filters( 'tpfw_current_user_billing_details', $details, get_current_user_id() );
	}

	/**
	 * Generate random string
	 *
	 * @param int $len Random string lenght.
	 *
	 * @return string
	 */
	public function random_string( $len = 64 ) {
		$chars  = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$max    = strlen( $chars ) - 1;
		$string = '';
		for ( $i = 0; $i < $len; $i ++ ) {
			$string .= $chars[ wp_rand( 0, $max ) ];
		}

		return $string;
	}
}
