<?php

/**
 * Plugin Name: UTM Event Tracker and Analytics
 * Description: Unlocking the Power of UTM Event Tracker and Analytics for Enhanced Marketing Insights
 * Version: 1.0.3
 * Author: Repon Hossain
 * Author URI: https://workwithrepon.com
 * Text Domain: utm-event-tracker
 * 
 * Requires at least: 6.2
 * Requires PHP: 7.4.3
 * Tested up to: 6.6.2
 * 
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */

if (!defined('ABSPATH')) {
	exit;
}

define('UTM_EVENT_TRACKER_FILE', __FILE__);
define('UTM_EVENT_TRACKER_VERSION', '1.0.3');
define('UTM_EVENT_TRACKER_BASENAME', plugin_basename(__FILE__));
define('UTM_EVENT_TRACKER_URL', trailingslashit(plugins_url('/', __FILE__)));
define('UTM_EVENT_TRACKER_PATH', trailingslashit(plugin_dir_path(__FILE__)));
define('UTM_EVENT_TRACKER_MIN_PHP_VERSION', '7.4.3');

define('UTM_EVENT_TRACKER_API_URI', 'https://codiepress.com');
define('UTM_EVENT_TRACKER_PLUGIN_ID', 726);


/**
 * Add event function for adding UTM event
 * 
 * @since 1.0.2
 * @param string $type
 * @param array $event_data
 */
function utm_event_tracker_add_event($type, $event_data = array()) {
	$event_data['type'] = $type;
	$session = \UTM_Event_Tracker\Session::get_current_session();
	$session->add_event($event_data);
}

require_once UTM_EVENT_TRACKER_PATH . 'inc/class-main.php';

/**
 * Load textdomain of this plugin
 * 
 * @since 1.0.0
 */
function utm_event_tracker_load_textdomain() {
	load_plugin_textdomain('utm-event-tracker');
}
add_action('init', 'utm_event_tracker_load_textdomain');

UTM_Event_Tracker\Main::get_instance();


require __DIR__ . '/vendor/autoload.php';

/**
 * Initialize the plugin tracker
 *
 * @return void
 */
function appsero_init_tracker_utm_event_tracker_and_analytics() {

	if (!class_exists('Appsero\Client')) {
		require_once __DIR__ . '/appsero/src/Client.php';
	}

	$client = new Appsero\Client('56642f81-e34e-4683-83be-a96d00f5fac1', 'UTM Event Tracker and Analytics, UTM Grabber', __FILE__);

	// Active insights
	$client->insights()->init();
}

appsero_init_tracker_utm_event_tracker_and_analytics();