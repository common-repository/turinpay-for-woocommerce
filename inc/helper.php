<?php
/**
 * payment Gateway webhook.
 *
 * @package turinpay-plugin-for-woocommerce
 * @since 0.0.1
 */

namespace TPFW\Inc;

/**
 * payment Webhook.
 */
class Helper {

	/**
	 * Default global values
	 *
	 * @var array
	 */
	private static $global_defaults = [
		'tpfw_test_client_id'      => '',
		'tpfw_pub_key'             => '',
		'tpfw_test_secret_key'     => '',
		'tpfw_secret_key'          => '',
		'tpfw_test_con_status'     => '',
		'tpfw_con_status'          => '',
		'tpfw_mode'                => 'test',
		'tpfw_live_webhook_secret' => '',
		'tpfw_test_webhook_secret' => '',
		'tpfw_account_id'          => '',
		'tpfw_debug_log'           => 'yes',
	];

	/**
	 * Constructor
	 *
	 * @since 0.0.1
	 */
	public function __construct() {
	}

	/**
	 * payment get all settings
	 *
	 * @return $global_settings array It returns all payment settings in an array.
	 */
	public static function get_settings() {
		$response = [];
		foreach ( self::$global_defaults as $key => $default_data ) {
			$response[ $key ] = self::get_global_setting( $key );
		}
		return apply_filters( 'tpfw_settings', $response );
	}

	/**
	 * payment get all settings
	 *
	 * @return $global_settings array It returns all payment settings in an array.
	 */
	public static function get_gateway_defaults() {
		return apply_filters(
			'tpfw_payment_gateway_defaults_settings',
			[
				'woocommerce_tpfw_payment_settings' => [
					'enabled'      => 'no',
					'payment_type' => 'smart',
					'charge_type'  => 'automatic',
				],
			]
		);
	}

	/**
	 * Get all settings of a particular gateway
	 *
	 * @param string $gateway gateway id.
	 * @return array
	 */
	public static function get_gateway_settings( $gateway = 'tpfw_payment' ) {
		$default_settings = [];
		$setting_name     = 'woocommerce_' . $gateway . '_settings';
		$saved_settings   = is_array( get_option( $setting_name, [] ) ) ? get_option( $setting_name, [] ) : [];
		$gateway_defaults = self::get_gateway_defaults();

		if ( isset( $gateway_defaults[ $setting_name ] ) ) {
			$default_settings = $gateway_defaults[ $setting_name ];
		}

		$settings = array_merge( $default_settings, $saved_settings );

		return apply_filters( 'tpfw_gateway_settings', $settings );
	}

	/**
	 * Get value of gateway option parameter
	 *
	 * @param string $key key name.
	 * @param string $gateway gateway id.
	 * @return mixed
	 */
	public static function get_gateway_setting( $key = '', $gateway = 'tpfw_payment' ) {
		$settings = self::get_gateway_settings( $gateway );
		$value    = false;

		if ( isset( $settings[ $key ] ) ) {
			$value = $settings[ $key ];
		}

		return $value;
	}

	/**
	 * Get value of global option
	 *
	 * @param string $key value of global setting.
	 * @return mixed
	 */
	public static function get_global_setting( $key ) {
		$db_data = get_option( $key );
		$default = isset( self::$global_defaults[ $key ] ) ? self::$global_defaults[ $key ] : '';
		return $db_data ? $db_data : $default;
	}

	/**
	 * payment get settings value by key.
	 *
	 * @param string $key Name of the key to get the value.
	 * @param mixed  $gateway Name of the payment gateway to get options from the database.
	 *
	 * @return array $global_settings It returns all payment settings in an array.
	 */
	public static function get_setting( $key = '', $gateway = false ) {
		$result = false;
		if ( false !== $gateway ) {
			$result = self::get_gateway_setting( $key, $gateway );
		} else {
			$result = self::get_global_setting( $key );
		}
		return is_array( $result ) || $result ? apply_filters( $key, $result ) : false;
	}

	/**
	 * payment get current mode
	 *
	 * @return $mode string It returns current mode of the payment payment gateway.
	 */
	public static function get_payment_mode() {
		return apply_filters( 'tpfw_payment_mode', self::get_setting( 'tpfw_mode' ) );
	}

	/**
	 * Get webhook secret key.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public static function get_webhook_secret() {
		$endpoint_secret = '';

		if ( 'live' === self::get_payment_mode() ) {
			$endpoint_secret = self::get_setting( 'tpfw_live_webhook_secret' );
		} elseif ( 'test' === self::get_payment_mode() ) {
			$endpoint_secret = self::get_setting( 'tpfw_test_webhook_secret' );
		}

		if ( empty( trim( $endpoint_secret ) ) ) {
			return false;
		}

		return $endpoint_secret;
	}

	/**
	 * Localize payment messages based on code
	 *
	 * @since 1.0.0
	 *
	 * @param string $code payment error code.
	 * @param string $message payment error message.
	 *
	 * @return string
	 */
	public static function get_localized_messages( $code = '', $message = '' ) {
		$localized_messages = apply_filters(
			'tpfw_payment_localized_messages',
			[
				'account_country_invalid_address'        => __( 'The business address that you provided does not match the country set in your account. Please enter an address that falls within the same country.', 'turinpay-plugin-for-woocommerce' ),
				'account_invalid'                        => __( 'The account ID provided in the payment-Account header is invalid. Please check that your requests specify a valid account ID.', 'turinpay-plugin-for-woocommerce' ),
			]
		);

		// if need all messages.
		if ( empty( $code ) ) {
			return $localized_messages;
		}

		return isset( $localized_messages[ $code ] ) ? $localized_messages[ $code ] : $message;
	}
}
