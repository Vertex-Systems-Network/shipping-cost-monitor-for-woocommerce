<?php
/**
 * Main plugin coordinator.
 *
 * @package WPEssentialShippingCostMonitor
 */

namespace WPEssential\ShippingCostMonitor;

defined( 'ABSPATH' ) || exit;

require_once WPE_SCM_DIR . 'includes/Admin/Page.php';

/**
 * Boots the plugin's admin-facing modules.
 */
final class Plugin {
	/**
	 * Plugin instance.
	 *
	 * @var Plugin|null
	 */
	private static ?Plugin $instance = null;

	/**
	 * Get the plugin instance.
	 */
	public static function instance(): Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Register plugin hooks.
	 */
	public function init(): void {
		$page = new Admin\Page();
		$page->register();
	}
}
