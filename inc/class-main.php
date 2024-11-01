<?php

namespace UTM_Event_Tracker;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Main class plugin
 */
final class Main {

	/**
	 * The single instance of the class.
	 *
	 * @var Main
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main Instance.
	 *
	 * Ensures only one instance of Main is loaded or can be loaded.
	 *
	 * @since 2.1
	 * @static
	 * @return Main - Main instance.
	 */
	public static function get_instance() {
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong(__FUNCTION__, esc_html__('Cheating huh?', 'utm-event-tracker'), '1.0.0');
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong(__FUNCTION__, esc_html__('Unserializing instances of this class is forbidden.', 'utm-event-tracker'), '1.0.0');
	}

	/** 
	 * Constructor 
	 * 
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->add_tables();
		require_once UTM_EVENT_TRACKER_PATH . 'inc/utils.php';
		require_once UTM_EVENT_TRACKER_PATH . 'inc/class-migrate.php';

		if (version_compare(PHP_VERSION, UTM_EVENT_TRACKER_MIN_PHP_VERSION, '<')) {
			return add_action('admin_notices', array($this, 'php_version_missing'));
		}

		$this->include_files();
		$this->init();
	}

	/**
	 * Add tables variables at $wpdb 
	 * 
	 * @since 1.0.0
	 */
	public function add_tables() {
		global $wpdb;
		$wpdb->utm_event_tracker_sessions_table = $wpdb->prefix . 'utm_event_tracker_sessions';
		$wpdb->utm_event_tracker_views_table = $wpdb->prefix . 'utm_event_tracker_views';
		$wpdb->utm_event_tracker_events_table = $wpdb->prefix . 'utm_event_tracker_events';
	}

	public function php_version_missing() {
		$notice = sprintf(
			/* translators: 1 for plugin name, 2 for PHP, 3 for PHP version */
			esc_html__('%1$s need %2$s version %3$s or greater.', 'utm-event-tracker'),
			'<strong>' . __('UTM Event Tracker and Analytics', 'utm-event-tracker') . '</strong>',
			'<strong>' . __('PHP', 'utm-event-tracker') . '</strong>',
			UTM_EVENT_TRACKER_MIN_PHP_VERSION
		);

		printf('<div class="notice notice-warning"><p>%1$s</p></div>', wp_kses_post($notice));
	}

	/**
	 * Include required files
	 * 
	 * @since 1.0.0
	 */
	public function include_files() {
		require_once UTM_EVENT_TRACKER_PATH . 'inc/webhook.php';
		require_once UTM_EVENT_TRACKER_PATH . 'inc/class-event.php';
		require_once UTM_EVENT_TRACKER_PATH . 'inc/class-query.php';
		require_once UTM_EVENT_TRACKER_PATH . 'inc/class-session.php';

		require_once UTM_EVENT_TRACKER_PATH . 'third-party/wpforms.php';
		require_once UTM_EVENT_TRACKER_PATH . 'third-party/elementor.php';
		require_once UTM_EVENT_TRACKER_PATH . 'third-party/formidable.php';
		require_once UTM_EVENT_TRACKER_PATH . 'third-party/woocommerce.php';
		require_once UTM_EVENT_TRACKER_PATH . 'third-party/ninja-forms.php';
		require_once UTM_EVENT_TRACKER_PATH . 'third-party/gravity-form.php';
		require_once UTM_EVENT_TRACKER_PATH . 'third-party/contact-form-7.php';

		if (is_admin()) {
			require_once UTM_EVENT_TRACKER_PATH . 'inc/class-admin.php';
		}
	}

	/**
	 * Init the UTM analytics plugin
	 * 
	 * @since 1.0.0
	 */
	public function init() {
		add_action('wp', array($this, 'generate_session'));
		add_action('wp_enqueue_scripts', array($this, 'enqueue_script'));
		add_filter('plugin_action_links', array($this, 'add_plugin_links'), 10, 2);

		new Query();		
	}

	/**
	 * Generate user session
	 * 
	 * @since 1.0.0
	 */
	public function generate_session() {
		if (!Session::is_available()) {
			return;
		}

		$session = Session::get_current_session();

		$result = $session->save();
		if (!$result) {
			return;
		}

		if ($session->is_new() || empty($_COOKIE['utm_event_tracker_session'])) {
			$cookie_duration = sprintf('+%d days', Utils::get_settings_key('cookie_duration', 30));
			setcookie('utm_event_tracker_session', $session->get_session_id(), strtotime($cookie_duration), '/');
			$_COOKIE['utm_event_tracker_session'] = $session->get_session_id();
		}

		$session->add_view();
	}

	/**
	 * Add links at the plugin action
	 * 
	 * @since 1.0.0
	 * @return array $actions
	 */
	public function add_plugin_links($actions, $plugin_file) {
		if (UTM_EVENT_TRACKER_BASENAME == $plugin_file) {
			$new_links = array(
				'overview' => sprintf('<a href="%s">%s</a>', menu_page_url('utm-event-tracker', false), __('Overview', 'utm-event-tracker')),
				'get-pro' => '<a target="_blank" href="https://codiepress.com/plugins/utm-event-tracker-and-analytics-pro/?utm_campaign=utm+event+tracker&utm_source=plugins+page&utm_medium=get+pro">' . __('Get Pro', 'utm-event-tracker') . '</a>'
			);

			$actions = array_merge($new_links, $actions);
		}

		return $actions;
	}

	/**
	 * Enqueue script on frontend
	 * 
	 * @since 1.0.1
	 * @return void
	 */
	public function enqueue_script() {
		$session = Session::get_current_session();
		if (!Session::is_available()) {
			return;
		}

		wp_enqueue_script('utm-event-tracker', UTM_EVENT_TRACKER_URL . 'assets/frontend.min.js', ['jquery'], UTM_EVENT_TRACKER_VERSION, true);

		$utm_event_tracker_parameters = array();

		$parameters = array_keys(Utils::get_all_parameters());
		while ($key = current($parameters)) {
			$utm_event_tracker_parameters[$key] = $session->get($key);
			next($parameters);
		}

		wp_localize_script('utm-event-tracker', 'utm_event_tracker', array(
			'utm_parameters' => $utm_event_tracker_parameters
		));
	}
}
