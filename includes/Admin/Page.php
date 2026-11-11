<?php
/**
 * WooCommerce admin page registration.
 *
 * @package WPEssentialShippingCostMonitor
 */

namespace WPEssential\ShippingCostMonitor\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Registers the React-powered dashboard shell.
 */
final class Page {
	/**
	 * Current admin screen hook.
	 *
	 * @var string
	 */
	private string $page_hook = '';

	/**
	 * Register the page and asset hooks.
	 */
	public function register(): void {
		add_action( 'admin_menu', array( $this, 'add_menu_page' ), 99 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Add the page beneath WooCommerce.
	 */
	public function add_menu_page(): void {
		$page_hook = add_submenu_page(
			'woocommerce',
			__( 'Shipping Cost Monitor', 'shipping-cost-monitor-for-woocommerce' ),
			__( 'Shipping Cost Monitor', 'shipping-cost-monitor-for-woocommerce' ),
			'manage_woocommerce',
			'shipping-cost-monitor-for-woocommerce',
			array( $this, 'render_page' )
		);

		$this->page_hook = is_string( $page_hook ) ? $page_hook : '';
	}

	/**
	 * Load the dashboard assets only on this screen.
	 *
	 * @param string $hook Current admin screen hook.
	 */
	public function enqueue_assets( string $hook ): void {
		if ( '' === $this->page_hook || $this->page_hook !== $hook || ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		wp_enqueue_style(
			'wpe-scm-admin',
			WPE_SCM_URL . 'assets/css/admin.css',
			array(),
			WPE_SCM_BUILD_ID
		);

		wp_enqueue_script(
			'wpe-scm-admin',
			WPE_SCM_URL . 'assets/js/admin.js',
			array( 'wp-element', 'wp-i18n' ),
			WPE_SCM_BUILD_ID,
			true
		);

		// phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput -- Read-only report date filter on a capability-checked admin screen; no state change. Length-capped and sanitized below.
		$requested_start_raw = isset( $_GET['wpe_scm_start'] ) && is_string( $_GET['wpe_scm_start'] )
			// phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput -- Read-only report date filter on a capability-checked admin screen; no state change. Length-capped and sanitized below.
			? wp_unslash( $_GET['wpe_scm_start'] )
			: null;
		// phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput -- Read-only report date filter on a capability-checked admin screen; no state change. Length-capped and sanitized below.
		$requested_end_raw = isset( $_GET['wpe_scm_end'] ) && is_string( $_GET['wpe_scm_end'] )
			// phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput -- Read-only report date filter on a capability-checked admin screen; no state change. Length-capped and sanitized below.
			? wp_unslash( $_GET['wpe_scm_end'] )
			: null;
		$requested_start = is_string( $requested_start_raw ) && strlen( $requested_start_raw ) <= 10
			? sanitize_text_field( $requested_start_raw )
			: null;
		$requested_end = is_string( $requested_end_raw ) && strlen( $requested_end_raw ) <= 10
			? sanitize_text_field( $requested_end_raw )
			: null;

		$dashboard = Summary::build( $requested_start, $requested_end );
		$export_url = add_query_arg(
			array(
				'action'        => 'wpe_scm_export_report',
				'wpe_scm_start' => $dashboard['period']['start'],
				'wpe_scm_end'   => $dashboard['period']['end'],
			),
			admin_url( 'admin-post.php' )
		);
		$dashboard['export_url'] = wp_nonce_url( $export_url, 'wpe_scm_export_report' );
		/**
		 * Filter the data passed to the dashboard script.
		 *
		 * Add-ons append their own keys (for example nonces); they must not remove core keys.
		 *
		 * @param array<string,mixed> $dashboard Dashboard data.
		 */
		$dashboard = apply_filters( 'wpe_scm_dashboard_data', $dashboard );

		wp_localize_script(
			'wpe-scm-admin',
			'wpeScmDashboard',
			$dashboard
		);

		/**
		 * Fires after the dashboard script is enqueued, so add-ons can enqueue scripts that depend on it.
		 */
		do_action( 'wpe_scm_dashboard_enqueued' );

		wp_set_script_translations( 'wpe-scm-admin', 'shipping-cost-monitor-for-woocommerce' );
	}

	/**
	 * Render the React mount point.
	 */
	public function render_page(): void {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die(
				esc_html__( 'You do not have permission to view shipping cost reports.', 'shipping-cost-monitor-for-woocommerce' ),
				esc_html__( 'Access denied', 'shipping-cost-monitor-for-woocommerce' ),
				array( 'response' => 403 )
			);
		}

		echo '<div class="wrap wpe-scm-wrap"><div id="wpe-scm-root"></div></div>';
	}
}
