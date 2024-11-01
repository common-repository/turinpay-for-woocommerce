<?php
/**
 * Notice helper.
 *
 * @package turinpay-plugin-for-woocommerce
 *
 * @since x.x.x
 */

namespace TPFW\Inc;

use TPFW\Inc\Traits\Get_Instance;

/**
 * Class that represents admin notices.
 *
 * @since x.x.x
 */
class Notice {

	use Get_Instance;

	/**
	 * Notices (array)
	 *
	 * @var array
	 */
	public $notices = [];

	/**
	 * Constructor
	 *
	 * @since x.x.x
	 */
	public function __construct() {
		add_action( 'admin_notices', [ $this, 'get_notices' ] );
		add_action( 'wp_loaded', [ $this, 'hide' ] );
	}

	/**
	 * Allow this class and other classes to add slug keyed notices (to avoid duplication).
	 *
	 * @since x.x.x
	 *
	 * @param string $slug Class slug.
	 * @param string $class CSS class name.
	 * @param string $message Notice message.
	 * @param string $dismissible Dismissible icon.
	 *
	 * @return void
	 */
	public function add( $slug, $class, $message, $dismissible = false ) {
		$this->notices[ $slug ] = apply_filters(
			'tpfw_notices_add_args',
			[
				'class'       => $class,
				'message'     => $message,
				'dismissible' => $dismissible,
			]
		);
	}

	/**
	 * Display any notices we've collected thus far.
	 *
	 * @since x.x.x
	 *
	 * @return void
	 */
	public function get_notices() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		foreach ( (array) $this->notices as $notice_key => $notice ) {
			echo '<div class="' . esc_attr( $notice['class'] ) . '" style="position:relative;">';

			if ( $notice['dismissible'] ) {
				?>
				<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'tpfw-payment-hide-notice', $notice_key ), 'tpfw_payment_hide_notices_nonce', '_tpfw_payment_notice_nonce' ) ); ?>" class="woocommerce-message-close notice-dismiss" style="position:relative;float:right;padding:9px 0px 9px 9px 9px;text-decoration:none;"></a>
				<?php
			}

			echo '<p>';
			echo wp_kses(
				$notice['message'],
				[
					'a' => [
						'href'   => [],
						'target' => [],
					],
				]
			);
			echo '</p></div>';
		}
	}

	/**
	 * Hides any notice.
	 *
	 * @since x.x.x
	 *
	 * @return void
	 */
	public function hide() {
		if (
			! isset( $_GET['_tpfw_payment_notice_nonce'] )
			|| ! wp_verify_nonce( $_GET['_tpfw_payment_notice_nonce'], 'tpfw_payment_hide_notices_nonce' )
		) {
			return;
		}

		$notice = wc_clean( wp_unslash( $_GET['tpfw-payment-hide-notice'] ) );

		update_option( 'tpfw_show_' . $notice . '_notice', 'no' );
	}

	/**
	 * Check current page is cpsw setting page.
	 *
	 * @since x.x.x
	 *
	 * @param string $section gateway section.
	 *
	 * @return boolean
	 */
	public function is_tpfw_section( $section ) {
		if ( isset( $_GET['page'] ) && 'wc-settings' === sanitize_text_field( $_GET['page'] ) && isset( $_GET['tab'] ) && isset( $_GET['section'] ) && sanitize_text_field( $_GET['section'] ) === $section ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return true;
		}

		return false;
	}
}
