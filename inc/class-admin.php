<?php

namespace UTM_Event_Tracker;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Main class plugin
 */
final class Admin {

	/**
	 * Hold the error object
	 * 
	 * @var WP_Error
	 */
	public $error = null;

	/**
	 * Hold the instance of Report Widget
	 * 
	 * @var Admin\Report_Widgets
	 */
	public $report_widgets = null;

	/** 
	 * Constructor 
	 * 
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->load();
		$this->init();


		$this->error = new \WP_Error();

		add_action('admin_menu', [$this, 'admin_menu'], 0);
		add_action('admin_footer', [$this, 'include_components']);
		add_action('init', array($this, 'handle_settings_form'));
		add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
	}

	/**
	 * Load files
	 * 
	 * @since 1.0.0
	 */
	public function load() {
		require_once UTM_EVENT_TRACKER_PATH . 'inc/admin/class-utm-sessions.php';
		require_once UTM_EVENT_TRACKER_PATH . 'inc/admin/class-utm-campaign.php';
		require_once UTM_EVENT_TRACKER_PATH . 'inc/admin/class-utm-medium.php';
		require_once UTM_EVENT_TRACKER_PATH . 'inc/admin/class-utm-source.php';
		require_once UTM_EVENT_TRACKER_PATH . 'inc/admin/class-utm-content.php';
		require_once UTM_EVENT_TRACKER_PATH . 'inc/admin/class-utm-term.php';
	}

	/**
	 * Initialize classes
	 * 
	 * @since 1.0.0
	 */
	public function init() {
		new Admin\UTM_Sessions();
		new Admin\UTM_Campaign();
		new Admin\UTM_Medium();
		new Admin\UTM_Source();
		new Admin\UTM_Content();
		new Admin\UTM_Term();
	}

	/**
	 * Handle submitted settings form
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	public function handle_settings_form() {
		if (!isset($_POST['_wpnonce'])) {
			return;
		}

		if (!wp_verify_nonce(sanitize_text_field($_POST['_wpnonce']), '_nonce_utm_event_tracker_settings')) {
			return;
		}

		$settings = isset($_POST['settings']) && is_array($_POST['settings']) ? array_map('sanitize_text_field', $_POST['settings']) : array();

		$webhook_url = isset($settings['webhook_url']) ? sanitize_text_field($settings['webhook_url']) : false;
		if ($webhook_url && filter_var($webhook_url, FILTER_VALIDATE_URL) === false) {
			$this->error->add('webhook_url', __('Please enter a valid webhook URL.', 'utm-event-tracker'));
		}

		if ($this->error->has_errors()) {
			return;
		}

		update_option('utm_event_tracker_settings', apply_filters('utm_event_tracker_setting/save_settings', $settings));

		if (!isset($_POST['_wp_http_referer'])) {
			return;
		}

		$http_referer = sanitize_text_field($_POST['_wp_http_referer']);
		wp_safe_redirect($http_referer); //phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect
		exit;
	}

	/**
	 * Register admin page
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	public function admin_menu() {
		add_menu_page(__('UTM Analytics', 'utm-event-tracker'), __('UTM Analytics', 'utm-event-tracker'), 'manage_options', 'utm-event-tracker', array($this, 'screen_overview'), 'dashicons-chart-bar', 25);
		add_submenu_page('utm-event-tracker', __('UTM Analytics', 'utm-event-tracker'), __('Overview', 'utm-event-tracker'), 'manage_options', 'utm-event-tracker', [$this, 'screen_overview'], 0);
		do_action('utm_event_tracker/admin_menu');
		add_submenu_page('utm-event-tracker', __('UTM Analytics Settings', 'utm-event-tracker'), __('Settings', 'utm-event-tracker'), 'manage_options', 'utm-event-tracker-settings', array($this, 'screen_settings'));
	}

	/**
	 * Enqueue scripts
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	public function admin_enqueue_scripts() {
		$screen = get_current_screen();
		preg_match('/(utm-event-tracker)/', $screen->id, $matches);
		if (empty($matches)) {
			return;
		}

		if (defined('UTM_EVENT_TRACKER_DEV_MODE')) {
			wp_register_script('utm-event-tracker-vue', UTM_EVENT_TRACKER_URL . 'assets/vue.js', [], '3.4.21', true);
		} else {
			wp_register_script('utm-event-tracker-vue', UTM_EVENT_TRACKER_URL . 'assets/vue.min.js', [], '3.4.21', true);
		}

		wp_register_style('utm-event-tracker-icons', UTM_EVENT_TRACKER_URL . 'assets/utm-event-tracker-icons/iconly.min.css', [], UTM_EVENT_TRACKER_VERSION);
		wp_register_style('daterangepicker', UTM_EVENT_TRACKER_URL . 'assets/daterangepicker.css');
		wp_enqueue_style('utm-event-tracker-admin', UTM_EVENT_TRACKER_URL . 'assets/admin.css', ['daterangepicker', 'utm-event-tracker-icons'], UTM_EVENT_TRACKER_VERSION);

		wp_register_script('daterangepicker', UTM_EVENT_TRACKER_URL . 'assets/daterangepicker.min.js', ['moment'], 3.1, true);
		do_action('utm_event_tracker/admin_enqueue_scripts');
		wp_enqueue_script('utm-event-tracker', UTM_EVENT_TRACKER_URL . 'assets/admin.min.js', ['utm-event-tracker-vue', 'wp-hooks', 'daterangepicker'], UTM_EVENT_TRACKER_VERSION, true);
		wp_localize_script('utm-event-tracker', 'utm_event_tracker', array(
			'ajax_url' => admin_url('admin-ajax.php'),
		));
	}

	/**
	 * Implement overview page
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	public function screen_overview() {
		include_once UTM_EVENT_TRACKER_PATH . '/template/overview.php';
	}

	/**
	 * Implement settings page
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	public function screen_settings() {
		include_once UTM_EVENT_TRACKER_PATH . '/template/settings.php';
	}

	/**
	 * Add component templates for vuejs
	 * 
	 * @since 1.0.0
	 */
	public function include_components() {
		echo '<template id="utm-pagination">';
		include_once UTM_EVENT_TRACKER_PATH . '/component/utm-pagination.php';
		echo '</template>';

		echo '<template id="utm-keywords-stats">';
		include_once UTM_EVENT_TRACKER_PATH . '/component/utm-keywords-stats.php';
		echo '</template>';

		echo '<template id="session-list">';
		include_once UTM_EVENT_TRACKER_PATH . '/component/session-list.php';
		echo '</template>';

		echo '<template id="session-list-keywords">';
		include_once UTM_EVENT_TRACKER_PATH . '/component/session-list-keywords.php';
		echo '</template>';

		echo '<template id="session-summary">';
		include_once UTM_EVENT_TRACKER_PATH . '/component/session-summary.php';
		echo '</template>';

		echo '<template id="utm-overview-widget">';
		include_once UTM_EVENT_TRACKER_PATH . '/component/utm-overview-widget.php';
		echo '</template>';
	}
}

new Admin();
