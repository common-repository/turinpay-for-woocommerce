<?php
/**
 * Turinpay Gateway
 *
 * @package turinpay-plugin-for-woocommerce
 * @since 0.0.1
 */

namespace TPFW\Admin;

use TPFW\Inc\Traits\Get_Instance;
use TPFW\Inc\Logger;
use TPFW\Inc\Helper;
use WC_Admin_Settings;

/**
 * Admin Controller - This class is used to update or delete Turinpay settings.
 *
 * @package turinpay-plugin-for-woocommerce
 * @since 0.0.1
 */
class Admin_Controller {

	use Get_Instance;

	/**
	 * Turinpay settings fields configuration array
	 *
	 * @var $settings_keys array
	 */
	private $settings_keys = [
		'tpfw_live_api_key',
		'tpfw_test_con_status',
		'tpfw_test_api_key',
		'tpfw_con_status',
		'tpfw_mode',
		'tpfw_live_webhook_secret',
		'tpfw_test_webhook_secret',
		'tpfw_debug_log',
		'tpfw_auto_connect',
	];

	/**
	 * Navigation links for the payment method pages.
	 *
	 * @var $navigation array
	 */
	public $navigation = [];

	/**
	 * Turinpay settings are stored in this array.
	 *
	 * @var $settings array
	 */
	private $settings = [];

	/**
	 * Constructor
	 *
	 * @since 0.0.1
	 */
	public function __construct() {
		$this->init();

		foreach ( $this->settings_keys as $key ) {
			$this->settings[ $key ] = get_option( $key );
		}

		$this->navigation = apply_filters(
			'tpfw_settings_navigation',
			[
				'tpfw_api_settings' => __( 'API Settings', 'turinpay-plugin-for-woocommerce' ),
				'tpfw_payment'       => __( 'Turinpay Settings', 'turinpay-plugin-for-woocommerce' ),
			]
		);
	}

	/**
	 * Init
	 *
	 * @since 0.0.1
	 */
	public function init() {
		add_filter( 'woocommerce_settings_tabs_array', [ $this, 'add_settings_tab' ], 50 );
		add_action( 'woocommerce_settings_tabs_tpfw_api_settings', [ $this, 'settings_tab' ] );
		add_action( 'woocommerce_update_options_tpfw_api_settings', [ $this, 'update_settings' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

		/* NEW METHODS */
		add_action( 'woocommerce_admin_field_tpfw_payment_connect', [ $this, 'payment_connect' ] );
		add_action( 'woocommerce_admin_field_tpfw_webhook_url', [ $this, 'webhook_url' ] );

		add_action( 'admin_init', [ $this, 'admin_options' ] );
		add_action( 'admin_init', [ $this, 'initialise_warnings' ] );

		add_action( 'wp_ajax_tpfw_test_payment_connection', [ $this, 'connection_test' ] );
		add_action( 'wp_ajax_tpfw_disconnect_account', [ $this, 'disconnect_account' ] );
		add_action( 'wp_ajax_tpfw_js_errors', [ $this, 'js_errors' ] );
		add_action( 'wp_ajax_nopriv_tpfw_js_errors', [ $this, 'js_errors' ] );

		add_filter( 'tpfw_settings', [ $this, 'filter_settings_fields' ], 1 );
		add_action( 'update_option_tpfw_mode', [ $this, 'update_mode' ], 10, 3 );

		add_action( 'admin_head', [ $this, 'add_custom_css' ] );
		add_action( 'woocommerce_sections_tpfw_api_settings', [ $this, 'add_breadcrumb' ] );

		add_filter( 'woocommerce_get_sections_checkout', [ $this, 'add_settings_links' ] );
		add_filter( 'woocommerce_get_sections_tpfw_api_settings', [ $this, 'add_settings_links' ] );
	}

	/**
	 * WooCommerce Init
	 *
	 * @since 0.0.1
	 */
	public function initialise_warnings() {
		// If no SSL bail.
		if ( 'live' === Helper::get_payment_mode() && ! is_ssl() ) {
			add_action( 'admin_notices', [ $this, 'ssl_not_connect' ] );
		}

		// IF Turinpay connection estabilished successfully .
		if ( isset( $_GET['tpfw_call'] ) && ! empty( $_GET['tpfw_call'] ) && 'success' === $_GET['tpfw_call'] ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			add_action( 'admin_notices', [ $this, 'connect_success_notice' ] );
		}

		// IF Turinpay connection not estabilished successfully.
		if ( isset( $_GET['tpfw_call'] ) && ! empty( $_GET['tpfw_call'] ) && 'failed' === $_GET['tpfw_call'] ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			add_action( 'admin_notices', [ $this, 'connect_failed_notice' ] );
		}
	}

	/**
	 * Enqueue Scripts
	 *
	 * @since 0.0.1
	 */
	public function enqueue_scripts() {
		$version               = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? time() : TPFW_VERSION;
		$allow_scripts_methods = apply_filters(
			'tpfw_allow_admin_scripts_methods',
			[
				'tpfw_payment',
			]
		);

		if ( isset( $_GET['page'] ) && 'wc-settings' === $_GET['page'] && isset( $_GET['tab'] ) && ( 'tpfw_api_settings' === $_GET['tab'] || isset( $_GET['section'] ) && ( in_array( sanitize_text_field( $_GET['section'] ), $allow_scripts_methods, true ) ) ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			wp_register_style( 'tpfw-admin-style', plugins_url( 'assets/css/admin.css', __FILE__ ), [], $version, 'all' );
			wp_enqueue_style( 'tpfw-admin-style' );

			wp_register_script( 'tpfw-admin-js', plugins_url( 'assets/js/admin.js', __FILE__ ), [ 'jquery' ], $version, true );
			wp_enqueue_script( 'tpfw-admin-js' );

			wp_localize_script(
				'tpfw-admin-js',
				'tpfw_ajax_object',
				apply_filters(
					'tpfw_admin_localize_script_args',
					[
						'site_url'                 => get_site_url() . '/wp-admin/admin.php?page=wc-settings',
						'ajax_url'                 => admin_url( 'admin-ajax.php' ),
						'tpfw_mode'                => Helper::get_payment_mode(),
						'admin_nonce'              => wp_create_nonce( 'tpfw_admin_nonce' ),
						'dashboard_url'            => admin_url( 'admin.php?page=wc-settings&tab=tpfw_api_settings' ),
						'generic_error'            => __( 'Something went wrong! Please reload the page and try again.', 'turinpay-plugin-for-woocommerce' ),
						'test_btn_label'           => __( 'Connect to Turinpay', 'turinpay-plugin-for-woocommerce' ),
						'payment_key_notice'        => __( 'Please enter all keys to connect to turinpay.', 'turinpay-plugin-for-woocommerce' ),
						'payment_key_error'         => __( 'You must enter your API keys or connect the plugin before performing a connection test. Mode:', 'turinpay-plugin-for-woocommerce' ),
						'payment_key_unavailable'   => __( 'Keys Unavailable.', 'turinpay-plugin-for-woocommerce' ),
						'payment_disconnect'        => __( 'Your Turinpay account has been disconnected.', 'turinpay-plugin-for-woocommerce' ),
						'payment_connect_other_acc' => __( 'You can connect other Turinpay account now.', 'turinpay-plugin-for-woocommerce' ),
						'is_connected'             => $this->is_payment_connected(),
						'is_manually_connected'    => isset( $_GET['connect'] ) ? sanitize_text_field( $_GET['connect'] ) : '', //phpcs:ignore WordPress.Security.NonceVerification.Recommended
						'tpfw_admin_settings_tab'  => isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : '', //phpcs:ignore WordPress.Security.NonceVerification.Recommended
						'tpfw_admin_current_page'  => isset( $_GET['section'] ) ? sanitize_text_field( $_GET['section'] ) : '', //phpcs:ignore WordPress.Security.NonceVerification.Recommended
					]
				)
			);
		}
	}

	/**
	 * Check for SSL and show warning.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function ssl_not_connect() {
		echo wp_kses_post( '<div class="notice notice-error"><p>' . __( 'No SSL was detected, Turinpay live mode requires SSL.', 'turinpay-plugin-for-woocommerce' ) . '</p></div>' );
	}

	/**
	 * Connection success notice.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function connect_success_notice() {
		echo wp_kses_post( '<div class="notice notice-success is-dismissible"><p>' . __( 'Your Turinpay account has been connected to your WooCommerce store. You may now accept payments in live and test mode.', 'turinpay-plugin-for-woocommerce' ) . '</p></div>' );
	}

	/**
	 * Connection failed notice.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function connect_failed_notice() {
		echo wp_kses_post( '<div class="notice notice-error is-dismissible"><p>' . __( 'We were not able to connect your Turinpay account. Please try again. ', 'turinpay-plugin-for-woocommerce' ) . '</p></div>' );
	}

	/**
	 * Insufficient permission notice.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function insufficient_permission() {
		echo wp_kses_post( '<div class="notice notice-error is-dismissible"><p>' . __( 'Error: The current user doesn’t have sufficient permissions to perform this action. Please reload the page and try again.', 'turinpay-plugin-for-woocommerce' ) . '</p></div>' );
	}

	/**
	 * This method is used to update turinpay options to the database.
	 *
	 * @since 1.0.0
	 *
	 * @param array $options settings array of the turinpay.
	 */
	public function update_options( $options ) {
		if ( ! is_array( $options ) ) {
			return false;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		foreach ( $options as $key => $value ) {
			update_option( $key, $value );
		}
	}

	/**
	 * This method is used to turinpay connect button.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Field name in string.
	 */
	public function payment_connect( $value ) {
		if ( true === $this->is_payment_connected() ) {
			return;
		}
	}

	/**
	 * This method is used to display block for Turinpay webhook url.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Name of the field.
	 */
	public function webhook_url( $value ) {
		$data         = WC_Admin_Settings::get_field_description( $value );
		$description  = $data['description'];
		$tooltip_html = $data['tooltip_html'];
		$option_value = (array) WC_Admin_Settings::get_option( $value['id'] );

		if ( $tooltip_html && 'checkbox' === $value['type'] ) {
			$tooltip_html = '<p class="description">' . $tooltip_html . '</p>';
		} elseif ( $tooltip_html ) {
			$tooltip_html = wc_help_tip( $tooltip_html );
		}
		?>
		<tr valign="top">
			<th scope="row">
				<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo wp_kses_post( $tooltip_html ); ?></label>
			</th>
			<td class="form-wc form-wc-<?php echo esc_attr( $value['class'] ); ?>">
				<fieldset>
					<strong><?php echo esc_url( get_home_url() . '/wp-json/tpfw/v1/webhook' ); ?></strong>
				</fieldset>
				<fieldset>
					<?php echo wp_kses_post( $value['desc'] ); ?>
				</fieldset>
			</td>
		</tr>
		<?php
	}

	/**
	 * This method is used to display Turinpay Account key information on the settings page.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Name of the field.
	 *
	 * @return void
	 */
	public function account_keys( $value ) {
		if ( empty( Helper::get_setting( 'tpfw_client_id' ) ) && empty( Helper::get_setting( 'tpfw_test_client_id' ) ) ) {
			return;
		}

		$data         = WC_Admin_Settings::get_field_description( $value );
		$description  = $data['description'];
		$tooltip_html = $data['tooltip_html'];

		$option_value = (array) WC_Admin_Settings::get_option( $value['id'] );

		if ( $tooltip_html && 'checkbox' === $value['type'] ) {
			$tooltip_html = '<p class="description">' . $tooltip_html . '</p>';
		} elseif ( $tooltip_html ) {
			$tooltip_html = wc_help_tip( $tooltip_html );
		}
		?>
		<tr valign="top">
			<th scope="row">
				<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo wp_kses_post( $tooltip_html ); ?></label>
			</th>
			<td class="form-wc form-wc-<?php echo esc_attr( $value['class'] ); ?>">
				<fieldset>
					<a href="javascript:void(0)" id="tpfw_account_keys"><span><?php esc_html_e( 'Clear all Turinpay account keys', 'turinpay-plugin-for-woocommerce' ); ?></span></a>
				</fieldset>
				<fieldset>
					<?php echo wp_kses_post( $value['desc'] ); ?>
				</fieldset>
			</td>
		</tr>
		<?php
	}

	/**
	 * This method is used to display block for Turinpay Connect Button.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Name of the field.
	 *
	 * @return void
	 */
	public function connect_button( $value ) {
		$data         = WC_Admin_Settings::get_field_description( $value );
		$description  = $data['description'];
		$tooltip_html = $data['tooltip_html'];
		$manual_link  = false;
		$option_value = (array) WC_Admin_Settings::get_option( $value['id'] );

		if ( $tooltip_html && 'checkbox' === $value['type'] ) {
			$tooltip_html = '<p class="description">' . $tooltip_html . '</p>';
		} elseif ( $tooltip_html ) {
			$tooltip_html = wc_help_tip( $tooltip_html );
		}

		if ( 'live' === Helper::get_payment_mode() && ! empty( Helper::get_setting( 'tpfw_client_id' ) ) ) {
			$label        = __( 'Re-Connect to Turinpay', 'turinpay-plugin-for-woocommerce' );
			$sec_var      = '&rec=yes';
			$label_status = '<span class="dashicons dashicons-yes stipe-connect-active"></span> ' . __( 'Your Turinpay account has been connected. You can now accept Live and Test payments. You can Re-Connect if you want to recycle your API keys for security.', 'turinpay-plugin-for-woocommerce' );
		} elseif ( 'test' === Helper::get_payment_mode() && ! empty( Helper::get_setting( 'tpfw_test_client_id' ) ) ) {
			$label        = __( 'Re-Connect to Turinpay', 'turinpay-plugin-for-woocommerce' );
			$sec_var      = '&rec=yes';
			$label_status = '<span class="dashicons dashicons-yes stipe-connect-active"></span> ' . __( 'Your Turinpay account has been connected. You can now accept Live and Test payments. You can Re-Connect if you want to recycle your API keys for security.', 'turinpay-plugin-for-woocommerce' );
		} else {
			$label        = __( 'Connect to Turinpay', 'turinpay-plugin-for-woocommerce' );
			$label_status = __( 'We make it easy to connect Turinpay to your site. Click the Connect button to go through our connect flow.', 'turinpay-plugin-for-woocommerce' );
			$sec_var      = '';
			$manual_link  = true;
		}

		/**
		 * Action before turinpay conect button with turinpay.
		 *
		 * @since 1.0.0
		 *
		 * @param array $value Connect button values.
		 * @param array $data Field description data.
		 */
		do_action( 'tpfw_before_payment_connect_button', $value, $data );
		?>
		<tr valign="top">
			<th scope="row">
				<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo wp_kses_post( $tooltip_html ); ?></label>
			</th>
			<td class="form-wc form-wc-<?php echo esc_attr( $value['class'] ); ?>">
				<fieldset>
					<a class="tpfw_connect_btn" href="<?php echo esc_url( $this->get_payment_connect_url() . $sec_var ); ?>">
						<span><?php echo esc_html( $label ); ?></span>
					</a>
				</fieldset>
				<fieldset>
					<?php echo wp_kses_post( $label_status ); ?>
					<?php if ( true === $manual_link ) { ?>
					<a class="tpfw_connect_mn_btn" href="<?php echo esc_url( admin_url() ); ?>admin.php?page=wc-settings&tab=tpfw_api_settings&connect=manually"><?php esc_html_e( 'Connect Manually', 'turinpay-plugin-for-woocommerce' ); ?></a>
					<?php } ?>
				</fieldset>
			</td>
		</tr>
		<?php

		/**
		 * Action after turinpay conect button with turinpay.
		 *
		 * @since 1.0.0
		 *
		 * @param array $value Connect button values.
		 * @param array $data Field description data.
		 */
		do_action( 'tpfw_after_payment_connect_button', $value, $data );
	}

	/**
	 * This method is used to initialize the Turinpay settings tab inside the WooCommerce settings.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings_tabs Adding settings tab to existing WooCommerce tabs array.
	 *
	 * @return mixed
	 */
	public function add_settings_tab( $settings_tabs ) {
		$settings_tabs['tpfw_api_settings'] = __( 'Turinpay', 'turinpay-plugin-for-woocommerce' );
		return $settings_tabs;
	}

	/**
	 * Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
	 *
	 * @since 1.0.0
	 *
	 * @uses woocommerce_admin_fields()
	 * @uses $this->get_settings()
	 *
	 * @return void
	 */
	public function settings_tab() {
		woocommerce_admin_fields( $this->get_settings() );
	}

	/**
	 * Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function update_settings() {
		woocommerce_update_options( $this->get_settings() );
	}

	/**
	 * Generates Turinpay Autorization URL for onboarding process
	 *
	 * @since x.x.x
	 */
	public function get_payment_connect_url() {
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

	/**
	 * This method is used to initialize all turinpay configuration fields.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function get_settings() {
		$settings = [
			'section_title'       => [
				'name' => __( 'Turinpay API Settings', 'turinpay-plugin-for-woocommerce' ),
				'type' => 'title',
				'id'   => 'tpfw_title',
			],
			'connection_status'   => [
				'name'  => __( 'Turinpay Connect', 'turinpay-plugin-for-woocommerce' ),
				'type'  => 'tpfw_payment_connect',
				'value' => '--',
				'class' => 'wc_tpfw_connect_btn',
				'id'    => 'tpfw_payment_connect',
			],
			'account_keys'        => [
				'name'  => __( 'Turinpay Account Keys', 'turinpay-plugin-for-woocommerce' ),
				'type'  => 'tpfw_account_keys',
				'class' => 'wc_payment_acc_keys',
				'desc'  => __( 'This will disable any connection to Turinpay.', 'turinpay-plugin-for-woocommerce' ),
				'id'    => 'tpfw_account_keys',
			],
			'connect_button'      => [
				'name'  => __( 'Connect Turinpay Account', 'turinpay-plugin-for-woocommerce' ),
				'type'  => 'tpfw_connect_btn',
				'class' => 'wc_tpfw_connect_btn',
				'desc'  => __( 'We make it easy to connect Turinpay to your site. Click the Connect button to go through our connect flow.', 'turinpay-plugin-for-woocommerce' ),
				'id'    => 'tpfw_connect_btn',
			],
			'live_api_key'      => [
				'name'     => __( 'Production API key', 'turinpay-plugin-for-woocommerce' ),
				'type'     => 'text',
				'desc_tip' => __( 'Your client id is used to initialize Turinpay assets.', 'turinpay-plugin-for-woocommerce' ),
				'id'       => 'tpfw_live_api_key',
			],
			'test_api_key'      => [
				'name'     => __( 'Development API key', 'turinpay-plugin-for-woocommerce' ),
				'type'     => 'text',
				'desc_tip' => __( 'Your test client id is used to initialize Turinpay assets.', 'turinpay-plugin-for-woocommerce' ),
				'id'       => 'tpfw_test_api_key',
			],
			'test_mode'           => [
				'name'     => __( 'Mode', 'turinpay-plugin-for-woocommerce' ),
				'type'     => 'select',
				'options'  => [
					'test' => 'Test',
					'live' => 'Live',
				],
				'desc'     => __( 'No live transactions are processed in test mode. To fully use test mode, you must have a sandbox (test) account for the payment gateway you are testing.', 'turinpay-plugin-for-woocommerce' ),
				'id'       => 'tpfw_mode',
				'desc_tip' => true,
			],
			'webhook_url'         => [
				'name'  => __( 'Webhook URL', 'turinpay-plugin-for-woocommerce' ),
				'type'  => 'tpfw_webhook_url',
				'class' => 'wc_tpfw_webhook_url',
				'desc'  => sprintf( __( 'Important: the webhook URL is called by Turinpay when events occur in your account, like a source becomes chargeable. %1$1sWebhook Guide%2$2s or create webhook on %3$3sturinpay dashboard%4$4s', 'turinpay-plugin-for-woocommerce' ), '<a href="https://turinlabs.gitbook.io/turinlabs/api-docs/turinpay/webhooks" target="_blank">', '</a>', '<a href="https://turinlabs.gitbook.io/turinlabs/api-docs/turinpay/webhooks" target="_blank">', '</a>' ),
				'id'    => 'tpfw_webhook_url',
			],
			'debug_log'           => [
				'name'        => __( 'Debug Log', 'turinpay-plugin-for-woocommerce' ),
				'type'        => 'checkbox',
				'desc'        => __( 'Log debug messages', 'turinpay-plugin-for-woocommerce' ),
				'description' => __( 'Your publishable key is used to initialize Turinpay assets.', 'turinpay-plugin-for-woocommerce' ),
				'id'          => 'tpfw_debug_log',
			],
			'section_end'         => [
				'type' => 'sectionend',
				'id'   => 'tpfw_api_settings_section_end',
			],
		];
		$settings = apply_filters( 'tpfw_settings', $settings );

		return $settings;
	}

	/**
	 * Checks for response after turinpay onboarding process
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function admin_options() {
		if ( ! isset( $_GET['tpfw_connect_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_GET['tpfw_connect_nonce'] ), 'turinpay-connect' ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			add_action( 'admin_notices', [ $this, 'insufficient_permission' ] );
			return;
		}

		$redirect_url = apply_filters( 'tpfw_payment_connect_redirect_url', admin_url( '/admin.php?page=wc-settings&tab=tpfw_api_settings' ) );

		// Check if user is being returned from Turinpay Connect.
		if ( isset( $_GET['error'] ) ) {
			$error = json_decode( base64_decode( wc_clean( $_GET['error'] ) ) ); //phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
			if ( property_exists( $error, 'message' ) ) {
				$message = $error->message;
			} elseif ( property_exists( $error, 'raw' ) ) {
				$message = $error->raw->message;
			} else {
				$message = __( 'Please try again.', 'turinpay-plugin-for-woocommerce' );
			}

			$this->settings['tpfw_con_status']      = 'failed';
			$this->settings['tpfw_test_con_status'] = 'failed';

			$this->update_options( $this->settings );
			$redirect_url = add_query_arg( 'tpfw_call', 'failed', $redirect_url );
			wp_safe_redirect( $redirect_url );
		} elseif ( isset( $_GET['response'] ) ) {
			$response = json_decode( base64_decode( $_GET['response'] ) ); //phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
			if ( ! empty( $response->live->payment_publishable_key ) && ! empty( $response->test->payment_publishable_key ) ) {
				$this->settings['tpfw_live_api_key']    = $response->live->payment_publishable_key;
				$this->settings['tpfw_test_api_key']  = $response->test->payment_publishable_key;
				$this->settings['tpfw_mode']            = 'test';
				$this->settings['tpfw_con_status']      = 'success';
				$this->settings['tpfw_test_con_status'] = 'success';
				$redirect_url                           = add_query_arg( 'tpfw_call', 'success', $redirect_url );
				wp_safe_redirect( $redirect_url );
			} else {
				$this->settings['tpfw_live_api_key']    = '';
				$this->settings['tpfw_test_api_key']    = '';
				$this->settings['tpfw_con_status']      = 'failed';
				$this->settings['tpfw_test_con_status'] = 'failed';
				$redirect_url                           = add_query_arg( 'tpfw_call', 'failed', $redirect_url );
				wp_safe_redirect( $redirect_url );
			}

			$this->settings['tpfw_auto_connect'] = 'yes';
			$this->settings['tpfw_debug_log']    = 'yes';
			$this->update_options( $this->settings );
			do_action( 'tpfw_after_connect_with_payment', $this->settings['tpfw_con_status'] );
		}
	}

	/**
	 * Perform a connection test
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function connection_test() {
		if ( ! isset( $_GET['_security'] ) || ! wp_verify_nonce( sanitize_text_field( $_GET['_security'] ), 'tpfw_admin_nonce' ) ) {
			return wp_send_json_error( [ 'message' => __( 'Error: Sorry, the nonce security check didn’t pass. Please reload the page and try again.', 'turinpay-plugin-for-woocommerce' ) ] );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return wp_send_json_error( [ 'message' => __( 'Error: The current user doesn’t have sufficient permissions to perform this action. Please reload the page and try again.', 'turinpay-plugin-for-woocommerce' ) ] );
		}

		$results = [];
		$keys    = [];

		if ( isset( $_GET['tpfw_test_sec_key'] ) && ! empty( trim( $_GET['tpfw_test_sec_key'] ) ) ) {
			$keys['test']   = sanitize_text_field( trim( $_GET['tpfw_test_sec_key'] ) );
			$test_client_id = sanitize_text_field( trim( $_GET['tpfw_test_client_id'] ) );
		} else {
			$results['test']['mode']    = __( 'Test Mode:', 'turinpay-plugin-for-woocommerce' );
			$results['test']['status']  = 'invalid';
			$results['test']['message'] = __( 'Please enter secret key to test.', 'turinpay-plugin-for-woocommerce' );
		}
		if ( isset( $_GET['tpfw_secret_key'] ) && ! empty( trim( $_GET['tpfw_secret_key'] ) ) ) {
			$keys['live']    = sanitize_text_field( trim( $_GET['tpfw_secret_key'] ) );
			$live_client_id  = sanitize_text_field( trim( $_GET['tpfw_client_id'] ) );
		} else {
			$results['live']['mode']    = __( 'Live Mode:', 'turinpay-plugin-for-woocommerce' );
			$results['live']['status']  = 'invalid';
			$results['live']['message'] = __( 'Please enter secret key to live.', 'turinpay-plugin-for-woocommerce' );
		}

		if ( empty( $keys ) ) {
			return wp_send_json_error( [ 'message' => __( 'Error: Empty String provided for keys', 'turinpay-plugin-for-woocommerce' ) ] );
		}

		foreach ( $keys as $mode => $key ) {
			if ( 'test' === $mode ) {
				$client_id = $test_client_id;
			} else {
				$client_id = $live_client_id;
			}

			$args = [
				'headers' => [
					'Authorization' => 'Basic ' . base64_encode( $client_id . ':' . $key )
				],
				'body' =>[
					'grant_type' => 'client_credentials',
				]
			 ];
			  
			$response = wp_remote_post( 'https://api-m.sandbox.turinpay.com/v1/oauth2/token', $args );
			$response = wp_remote_retrieve_body( $response );
			$response = json_decode( $response );

			if ( ! isset( $response->error ) ) {
				$results[ $mode ]['status']  = 'success';
				$results[ $mode ]['message'] = __( 'Connected to Stripe successfully', 'checkout-plugins-stripe-woo' );
			} else {
				$results[ $mode ]['status']  = 'failed';
				$results[ $mode ]['message'] = $response->error_description;
			}

			switch ( $mode ) {
				case 'test':
					$results[ $mode ]['mode'] = __( 'Test Mode:', 'turinpay-plugin-for-woocommerce' );
					break;

				case 'live':
					$results[ $mode ]['mode'] = __( 'Live Mode:', 'turinpay-plugin-for-woocommerce' );
					break;

				default:
					break;
			}
		}
		update_option( 'tpfw_auto_connect', 'no' );
		return wp_send_json_success( [ 'data' => apply_filters( 'tpfw_connection_test_results', $results ) ] );
	}

	/**
	 * Checks for response after turinpay onboarding process
	 *
	 * @since 1.0.0
	 *
	 * @return $mixed
	 */
	public function disconnect_account() {
		if ( ! isset( $_GET['_security'] ) || ! wp_verify_nonce( sanitize_text_field( $_GET['_security'] ), 'tpfw_admin_nonce' ) ) {
			return wp_send_json_error( [ 'message' => __( 'Error: Sorry, the nonce security check didn’t pass. Please reload the page and try again.', 'turinpay-plugin-for-woocommerce' ) ] );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return wp_send_json_error( [ 'message' => __( 'Error: The current user doesn’t have sufficient permissions to perform this action. Please reload the page and try again.', 'turinpay-plugin-for-woocommerce' ) ] );
		}

		foreach ( $this->settings_keys as $key ) {
			update_option( $key, '' );
		}
		return wp_send_json_success( [ 'message' => __( 'Turinpay keys are reset successfully.', 'turinpay-plugin-for-woocommerce' ) ] );
	}

	/**
	 * Logs js errors
	 *
	 * @since 1.0.0
	 *
	 * @return json
	 */
	public function js_errors() {
		if ( ! isset( $_POST['_security'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['_security'] ), 'tpfw_js_error_nonce' ) ) {
			return wp_send_json_error( [ 'message' => __( 'Invalid Nonce', 'turinpay-plugin-for-woocommerce' ) ] );
		}

		if ( isset( $_POST['error'] ) ) {
			$error         = sanitize_text_field( $_POST['error'] );
			$error_message = $error['message'] . ' (' . $error['type'] . ')';
			$error_message = Helper::get_localized_messages( $error['code'], $error_message );
			Logger::error( $error_message, true );
			return wp_send_json_success( [ 'message' => $error_message ] );
		}
		exit();
	}

	/**
	 * Apply filters on tpfw_settings var to filter settings fields.
	 *
	 * @since 1.0.0
	 *
	 * @param array $array tpfw_settings values array.
	 * @return $array array It returns tpfw_settings array.
	 */
	public function filter_settings_fields( $array = [] ) {
		if ( 'success' !== Helper::get_setting( 'tpfw_con_status' ) && 'success' !== Helper::get_setting( 'tpfw_test_con_status' ) ) {
			$webhook_options = apply_filters(
				'tpfw_webhook_options',
				[
					'tpfw_live_webhook_began_at',
					'tpfw_live_webhook_last_success_at',
					'tpfw_live_webhook_last_failure_at',
					'tpfw_live_webhook_last_error',
					'tpfw_test_webhook_began_at',
					'tpfw_test_webhook_last_success_at',
					'tpfw_test_webhook_last_failure_at',
					'tpfw_test_webhook_last_error',
				]
			);

			array_map( 'delete_option', $webhook_options );
		}
		return $array;
	}

	/**
	 * Checks for response after turinpay onboarding process
	 *
	 * @return $mixed
	 */
	public function are_keys_set() {
		if ( ( 'live' === $this->settings['tpfw_mode']
				&& empty( $this->settings['tpfw_client_id'] )
				&& empty(
					$this->settings['tpfw_secret_key']
				) )
			|| ( 'test' === $this->settings['tpfw_mode']
				&& empty( $this->settings['tpfw_test_client_id'] )
				&& empty( $this->settings['tpfw_test_secret_key'] )
			)
			|| ( empty( $this->settings['tpfw_mode'] )
				&& empty( $this->settings['tpfw_secret_key'] )
				&& empty( $this->settings['tpfw_test_secret_key'] )
			) ) {
			return false;
		}
		return true;
	}

	/**
	 * Checks if turinpay is connected or not.
	 *
	 * @since 1.0.0
	 *
	 * @return $mixed
	 */
	public function is_payment_connected() {
		return true;
	}

	/**
	 * Update the turinpay payment mode on submit.
	 *
	 * @since 1.0.0
	 *
	 * @param string $old_value Old value of the option.
	 * @param strign $value New value of the option.
	 *
	 * @return void
	 */
	public function update_mode( $old_value, $value ) {
		if ( 'yes' === Helper::get_setting( 'tpfw_auto_connect' ) ) {
			return;
		}

		if ( ! empty( Helper::get_setting( 'tpfw_secret_key' ) ) && empty( Helper::get_setting( 'tpfw_test_secret_key' ) ) ) {
			update_option( 'tpfw_mode', 'live' );
		} elseif ( ! empty( Helper::get_setting( 'tpfw_test_secret_key' ) ) && empty( Helper::get_setting( 'tpfw_secret_key' ) ) ) {
			update_option( 'tpfw_mode', 'test' );
		}
	}

	/**
	 * Adds custom css to hide navigation menu item.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_custom_css() {
		?>
		<style type="text/css">
			a[href='<?php echo esc_url( get_site_url() ); ?>/wp-admin/admin.php?page=wc-settings&tab=tpfw_api_settings'].nav-tab { display: none }
		</style>
		<?php
	}

	/**
	 * Adds custom breadcrumb on payment method's pages.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_breadcrumb() {
		if ( ! empty( $this->navigation ) ) {
			?>
		<ul class="subsubsub">
			<?php
			foreach ( $this->navigation as $key => $value ) {
				$current_class = '';
				$separator     = '';
				if ( isset( $_GET['tab'] ) && 'tpfw_api_settings' === $_GET['tab'] && 'tpfw_api_settings' === $key ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$current_class = 'current';
					echo wp_kses_post( '<li> <a href="' . get_site_url() . '/wp-admin/admin.php?page=wc-settings&tab=tpfw_api_settings" class="' . $current_class . '">' . $value . '</a> | </li>' );
				} else {
					if ( end( $this->navigation ) !== $value ) {
						$separator = ' | ';
					}
					echo wp_kses_post( '<li> <a href="' . get_site_url() . '/wp-admin/admin.php?page=wc-settings&tab=checkout&section=' . $key . '" class="' . $current_class . '">' . $value . '</a> ' . $separator . ' </li>' );
				}
			}
			?>
		</ul>
		<br class="clear" />
			<?php
		}
	}

	/**
	 * Adds settings link to the checkout section.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings_tab Settings tabs array.
	 *
	 * @return array $settings_tab Settings tabs array returned.
	 */
	public function add_settings_links( $settings_tab ) {
		if ( isset( $_GET['section'] ) && 0 === strpos( $_GET['section'], 'tpfw_' ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$settings_tab = array_merge( $settings_tab, $this->navigation );
		}
		array_shift( $settings_tab );
		return apply_filters( 'tpfw_setting_tabs', $settings_tab );
	}
}
