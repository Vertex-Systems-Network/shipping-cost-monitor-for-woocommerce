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
			__( 'Shipping Cost Monitor', 'wpessential-shipping-cost-monitor' ),
			__( 'Shipping Cost Monitor', 'wpessential-shipping-cost-monitor' ),
			'manage_woocommerce',
			'wpessential-shipping-cost-monitor',
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
		$dashboard['ajax_url'] = admin_url( 'admin-ajax.php' );
		$dashboard['match_nonce'] = wp_create_nonce( 'wpe_scm_match_preview' );
		$dashboard['import_nonce'] = wp_create_nonce( 'wpe_scm_commit_invoice_rows' );
		$dashboard['carrier_nonce'] = wp_create_nonce( 'wpe_scm_carrier_manage' );
		$dashboard['carrier_sync_nonce'] = wp_create_nonce( 'wpe_scm_carrier_sync' );
		$dashboard['build_id'] = WPE_SCM_BUILD_ID;
		$dashboard['carrier_adapters'] = ( new \WPEssential\ShippingCostMonitor\Integrations\AdapterRegistry() )->get_public_status();

		wp_localize_script(
			'wpe-scm-admin',
			'wpeScmDashboard',
			$dashboard
		);

		wp_set_script_translations( 'wpe-scm-admin', 'wpessential-shipping-cost-monitor' );
	}

	/**
	 * Render the React mount point.
	 */
	public function render_page(): void {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die(
				esc_html__( 'You do not have permission to view shipping cost reports.', 'wpessential-shipping-cost-monitor' ),
				esc_html__( 'Access denied', 'wpessential-shipping-cost-monitor' ),
				array( 'response' => 403 )
			);
		}

		echo '<div class="wrap wpe-scm-wrap"><div id="wpe-scm-root"></div></div>';
	}
}
