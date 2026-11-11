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

		if ( self::is_docs_view() ) {
			return;
		}

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
		$dashboard['docs_url']   = WPE_SCM_DOCS_URL;
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

		if ( self::is_docs_view() ) {
			$this->render_docs();
			return;
		}

		echo '<div class="wrap wpe-scm-wrap"><div id="wpe-scm-root"></div></div>';
	}

	/**
	 * URL of the built-in documentation, optionally at one chapter.
	 *
	 * @param string $chapter Chapter slug from chapters(); empty for the contents page.
	 */
	public static function docs_url( string $chapter = '' ): string {
		$args = array(
			'page' => 'shipping-cost-monitor-for-woocommerce',
			'tab'  => 'docs',
		);
		if ( '' !== $chapter && isset( self::chapters()[ $chapter ] ) ) {
			$args['chapter'] = $chapter;
		}
		return add_query_arg( $args, admin_url( 'admin.php' ) );
	}

	/**
	 * Documentation chapters bundled in assets/docs/, keyed by file slug.
	 *
	 * @return array<string,string>
	 */
	public static function chapters(): array {
		return array(
			'index'                 => __( 'Contents', 'shipping-cost-monitor-for-woocommerce' ),
			'01-install'            => __( '1. Install and first run', 'shipping-cost-monitor-for-woocommerce' ),
			'02-key-ideas'          => __( '2. Key ideas: evidence, tax and currency', 'shipping-cost-monitor-for-woocommerce' ),
			'03-record-a-cost'      => __( '3. Recording a cost on an order', 'shipping-cost-monitor-for-woocommerce' ),
			'04-dashboard'          => __( '4. Reading the dashboard', 'shipping-cost-monitor-for-woocommerce' ),
			'05-csv-export'         => __( '5. Exporting the report as CSV', 'shipping-cost-monitor-for-woocommerce' ),
			'06-pro-invoice-import' => __( '6. Pro: importing a courier invoice CSV', 'shipping-cost-monitor-for-woocommerce' ),
			'07-pro-shipstation'    => __( '7. Pro: ShipStation label costs', 'shipping-cost-monitor-for-woocommerce' ),
			'08-troubleshooting'    => __( '8. Troubleshooting and FAQ', 'shipping-cost-monitor-for-woocommerce' ),
			'09-developers'         => __( '9. For developers: hooks and data', 'shipping-cost-monitor-for-woocommerce' ),
		);
	}

	/**
	 * Whether the current request is for the documentation tab.
	 */
	private static function is_docs_view(): bool {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only view switch on a capability-checked admin screen.
		return isset( $_GET['tab'] ) && 'docs' === $_GET['tab'];
	}

	/**
	 * Render the bundled documentation with a chapter list.
	 */
	private function render_docs(): void {
		$chapters = self::chapters();
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only chapter selection; only whitelisted slugs are used.
		$requested = isset( $_GET['chapter'] ) ? sanitize_key( wp_unslash( $_GET['chapter'] ) ) : 'index';
		$chapter   = isset( $chapters[ $requested ] ) ? $requested : 'index';
		$file      = WPE_SCM_DIR . 'assets/docs/' . $chapter . '.html';
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Reads a bundled local documentation file chosen from a fixed list.
		$html = is_readable( $file ) ? (string) file_get_contents( $file ) : '';
		$html = str_replace(
			array( '%%IMAGES%%', '%%DOCS%%' ),
			array( esc_url( WPE_SCM_URL . 'assets/docs/images/' ), esc_url( self::docs_url() . '&chapter=' ) ),
			$html
		);

		echo '<div class="wrap wpe-scm-wrap wpe-scm-docs">';
		echo '<div class="wpe-scm-docs-head"><h1>' . esc_html__( 'Shipping Cost Monitor documentation', 'shipping-cost-monitor-for-woocommerce' ) . '</h1>';
		echo '<a class="button" href="' . esc_url( admin_url( 'admin.php?page=shipping-cost-monitor-for-woocommerce' ) ) . '">' . esc_html__( 'Back to dashboard', 'shipping-cost-monitor-for-woocommerce' ) . '</a></div>';
		echo '<div class="wpe-scm-docs-layout"><nav class="wpe-scm-docs-nav" aria-label="' . esc_attr__( 'Documentation chapters', 'shipping-cost-monitor-for-woocommerce' ) . '"><ul>';
		foreach ( $chapters as $slug => $title ) {
			$current = $slug === $chapter ? ' aria-current="page" class="is-current"' : '';
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $current is a fixed literal.
			echo '<li><a href="' . esc_url( self::docs_url( $slug ) ) . '"' . $current . '>' . esc_html( $title ) . '</a></li>';
		}
		echo '</ul></nav><article class="wpe-scm-docs-content">' . wp_kses_post( $html ) . '</article></div></div>';
	}
}
