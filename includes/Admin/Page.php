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
		if ( '' === $this->page_hook || $this->page_hook !== $hook ) {
			return;
		}

		wp_enqueue_style(
			'wpe-scm-admin',
			WPE_SCM_URL . 'assets/css/admin.css',
			array(),
			WPE_SCM_VERSION
		);

		wp_enqueue_script(
			'wpe-scm-admin',
			WPE_SCM_URL . 'assets/js/admin.js',
			array( 'wp-element', 'wp-i18n' ),
			WPE_SCM_VERSION,
			true
		);

		wp_set_script_translations(
			'wpe-scm-admin',
			'wpessential-shipping-cost-monitor',
			WPE_SCM_DIR . 'languages'
		);
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
