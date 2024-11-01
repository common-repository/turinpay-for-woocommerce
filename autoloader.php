<?php
/**
 * Auto Loader.
 *
 * @package turinpay-plugin-for-woocommerce
 * @since 0.0.1
 */

namespace TPFW;


use TPFW\Gateway\Turinpay\Turinpay_Payments;
use TPFW\Admin\Admin_Controller;
use TPFW\Gateway\Turinpay\Webhook;
use TPFW\Gateway\Turinpay\Frontend_Scripts;

/**
 * TPFW_Loader
 *
 * @since 0.0.1
 */
class TPFW_Loader {

	/**
	 * Instance
	 *
	 * @access private
	 * @var object Class Instance.
	 * @since 0.0.1
	 */
	private static $instance;

	/**
	 * Initiator
	 *
	 * @since 0.0.1
	 * @return object initialized object of class.
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Autoload classes.
	 *
	 * @param string $class class name.
	 */
	public function autoload( $class ) {
		if ( 0 !== strpos( $class, __NAMESPACE__ ) ) {
			return;
		}

		$class_to_load = $class;

		$filename = strtolower(
			preg_replace(
				[ '/^' . __NAMESPACE__ . '\\\/', '/([a-z])([A-Z])/', '/_/', '/\\\/' ],
				[ '', '$1-$2', '-', DIRECTORY_SEPARATOR ],
				$class_to_load
			)
		);

		$file = TPFW_DIR . $filename . '.php';

		// if the file redable, include it.
		if ( is_readable( $file ) ) {
			require_once $file;
		}
	}

	/**
	 * Constructor
	 *
	 * @since 0.0.1
	 */
	public function __construct() {
		// Activation hook.
		register_activation_hook( TPFW_FILE, [ $this, 'install' ] );

		spl_autoload_register( [ $this, 'autoload' ] );

		$this->setup_classes();
		add_action( 'plugins_loaded', [ $this, 'load_classes' ] );
		add_filter( 'plugin_action_links_' . TPFW_BASE, [ $this, 'action_links' ] );
		add_action( 'woocommerce_init', [ $this, 'frontend_scripts' ] );
		add_action( 'plugins_loaded', [ $this, 'load_tpfw_textdomain' ] );
	}

	/**
	 * Sets up base classes.
	 *
	 * @return void
	 */
	public function setup_classes() {
		Admin_Controller::get_instance();
	}

	/**
	 * Includes frontend scripts.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function frontend_scripts() {
		if ( is_admin() ) {
			return;
		}

		Frontend_Scripts::get_instance();
	}

	/**
	 * Adds links in Plugins page
	 *
	 * @param array $links existing links.
	 * @return array
	 * @since 1.0.0
	 */
	public function action_links( $links ) {
		$plugin_links = apply_filters(
			'tpfw_plugin_action_links',
			[
				'tpfw_settings'      => '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=tpfw_api_settings' ) . '">' . __( 'Settings', 'turinpay-plugin-for-woocommerce' ) . '</a>',
				'tpfw_documentation' => '<a href="' . esc_url( 'https://turinlabs.gitbook.io/turinlabs/api-docs/turinpay' ) . '" target="_blank" >' . __( 'Documentation', 'turinpay-plugin-for-woocommerce' ) . '</a>',
			]
		);

		return array_merge( $plugin_links, $links );
	}

	/**
	 * Loads classes on plugins_loaded hook.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function load_classes() {
		if ( ! class_exists( 'woocommerce' ) ) {
			add_action( 'admin_notices', [ $this, 'wc_is_not_active' ] );
			return;
		}
		// Initializing Gateways.
		Turinpay_Payments::get_instance();
		Webhook::get_instance();
	}

	/**
	 * Loads classes on plugins_loaded hook.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function wc_is_not_active() {
		$install_url = wp_nonce_url(
			add_query_arg(
				array(
					'action' => 'install-plugin',
					'plugin' => 'woocommerce',
				),
				admin_url( 'update.php' )
			),
			'install-plugin_woocommerce'
		);
		echo '<div class="notice notice-error is-dismissible"><p>';
		// translators: 1$-2$: opening and closing <strong> tags, 3$-4$: link tags, takes to woocommerce plugin on wp.org, 5$-6$: opening and closing link tags, leads to plugins.php in admin.
		echo sprintf( esc_html__( '%1$sTurinPay Plugins for WooCommerce is inactive.%2$s The %3$sWooCommerce plugin%4$s must be active for TurinPay Plugins for WooCommerce to work. Please %5$sinstall & activate WooCommerce &raquo;%6$s', 'turinpay-plugin-for-woocommerce' ), '<strong>', '</strong>', '<a href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>', '<a href="' . esc_url( $install_url ) . '">', '</a>' );
		echo '</p></div>';
	}

	/**
	 * Checks for installation routine
	 * Loads plugins translation file
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function install() {
		update_option( 'tpfw_start_onboarding', true );
	}

	/**
	 * Loads plugins translation file
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function load_tpfw_textdomain() {
		// Default languages directory.
		$lang_dir = TPFW_DIR . 'languages/';

		// Traditional WordPress plugin locale filter.
		global $wp_version;

		$get_locale = get_locale();

		if ( $wp_version >= 4.7 ) {
			$get_locale = get_user_locale();
		}

		$locale = apply_filters( 'plugin_locale', $get_locale, 'turinpay-plugin-for-woocommerce' );
		$mofile = sprintf( '%1$s-%2$s.mo', 'turinpay-plugin-for-woocommerce', $locale );

		// Setup paths to current locale file.
		$mofile_local  = $lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/plugins/' . $mofile;

		if ( file_exists( $mofile_global ) ) {
			// Look in global /wp-content/languages/turinpay-plugin-for-woocommerce/ folder.
			load_textdomain( 'turinpay-plugin-for-woocommerce', $mofile_global );
		} elseif ( file_exists( $mofile_local ) ) {
			// Look in local /wp-content/plugins/turinpay-plugin-for-woocommerce/languages/ folder.
			load_textdomain( 'turinpay-plugin-for-woocommerce', $mofile_local );
		} else {
			// Load the default language files.
			load_plugin_textdomain( 'turinpay-plugin-for-woocommerce', false, $lang_dir );
		}
	}
}

/**
 * Kicking this off by calling 'get_instance()' method
 */
TPFW_Loader::get_instance();

