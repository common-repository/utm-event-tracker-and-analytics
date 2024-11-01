<?php

namespace UTM_Event_Tracker;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Session class
 * 
 * @since 1.0.0
 */
final class Session {

	/**
	 * Check if sessoin available
	 * 
	 * @since 1.0.0
	 * @return boolean
	 */
	public static function is_available() {
		return Utils::is_utm_parameter_available() || !empty($_COOKIE['utm_event_tracker_session']);
	}

	/**
	 * Get session by id
	 * 
	 * @since 1.0.0
	 * @return Session
	 */
	public static function get_by_id($session_id) {
		global $wpdb;
		$session = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->utm_event_tracker_sessions_table WHERE id = %d", $session_id));
		return new self($session);
	}


	/**
	 * Get current session from cookie
	 * 
	 * @since 1.0.0
	 * @return Session
	 */
	public static function get_current_session() {
		$session_id = '';
		if (!empty($_COOKIE['utm_event_tracker_session'])) {
			$session_id = sanitize_text_field($_COOKIE['utm_event_tracker_session']);
		}

		global $wpdb;
		$session_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM $wpdb->utm_event_tracker_sessions_table WHERE session_id = %s", $session_id));
		return self::get_by_id($session_id);
	}

	/**
	 * ID of session
	 * 
	 * @var integer
	 */
	private $id = 0;

	/**
	 * Hold the session_id of the session
	 * 
	 * @var string
	 */
	private $session_id = '';

	/**
	 * Hold the UTM campaign value of the session
	 * 
	 * @var string|null
	 */
	public $utm_campaign = null;

	/**
	 * Hold the UTM medium value of the session
	 * 
	 * @var string|null
	 */
	public $utm_medium = null;

	/**
	 * Hold the UTM source value of the session
	 * 
	 * @var string|null
	 */
	public $utm_source = null;

	/**
	 * Hold the UTM term value of the session
	 * 
	 * @var string|null
	 */
	public $utm_term = null;

	/**
	 * Hold the UTM content value of the session
	 * 
	 * @var string|null
	 */
	public $utm_content = null;

	/**
	 * Hold the facebook click id of the session
	 * 
	 * @var string|null
	 */
	public $fbclid = null;

	/**
	 * Hold the google click id of the session
	 * 
	 * @var string|null
	 */
	public $gclid = null;

	/**
	 * Hold the google click id of the session
	 * 
	 * @var string
	 */
	public $landing_page = '';

	/**
	 * Hold the user IP address
	 * 
	 * @var string|null
	 */
	public $ip_address = null;

	/**
	 * Hold the city of current session
	 * 
	 * @var string|null
	 */
	public $city = null;

	/**
	 * Hold the region of current session
	 * 
	 * @var string|null
	 */
	public $region = null;

	/**
	 * Hold the country of current session
	 * 
	 * @var string|null
	 */
	public $country = null;

	/**
	 * Hold the date of latest update
	 * 
	 * @var string
	 */
	public $last_online = '';

	/**
	 * Hold the date time of the sessoin
	 * 
	 * @var string
	 */
	public $created_on = '';

	/**
	 * Hold the hash of current session
	 * 
	 * @var string
	 */
	private $hash = '';

	/**
	 * Hold the extra data of the sessoin
	 * 
	 * @var array
	 */
	public $dirty_data = array();

	/**
	 * Constructor of session
	 * 
	 * @since 1.0.0
	 */
	public function __construct($session_data = array()) {
		$this->landing_page = $this->get_landing_page();
		$this->last_online = gmdate('Y-m-d H:i:s');
		$this->created_on = gmdate('Y-m-d H:i:s');

		$session_data = (array) $session_data;
		foreach ($session_data as $key => $value) {
			$key = sanitize_key($key);
			if (empty($key)) {
				continue;
			}

			$this->$key = $value;
		}

		$this->id = absint($this->id);
		$this->hash = $this->get_hash();
		if (false === $this->validate_session_id($this->session_id)) {
			$this->session_id = $this->generate_session_id();
		}

		$this->get_utm_data();
		if (empty($this->ip_address)) {
			$this->ip_address = $this->get_client_ip_address();
		}
	}

	/**
	 * Set extra data to dirty data var
	 * 
	 * @since 1.0.0
	 */
	public function __set($key, $value) {
		$this->dirty_data[$key] = $value;
	}

	/**
	 * Get extra data from dirty data var
	 * 
	 * @since 1.0.0
	 * @return mixed
	 */
	public function __get($key) {
		return isset($this->dirty_data[$key]) ? $this->dirty_data[$key] : null;
	}

	/**
	 * Check the key exists within dirty data
	 * 
	 * @since 1.0.0
	 * @return boolean
	 */
	public function __isset($key) {
		return isset($this->dirty_data[$key]);
	}

	/**
	 * Get ID column of session
	 * 
	 * @since 1.0.0
	 * @return int
	 */
	public function get_id() {
		if (isset($this->new_id) && absint($this->new_id) > 0) {
			return absint($this->new_id);
		}

		return $this->id;
	}

	/**
	 * Check if this is new sesion
	 * 
	 * @since 1.0.0
	 * @return bolean
	 */
	public function is_new() {
		return 0 == $this->id;
	}

	/**
	 * Check session id already exists
	 * 
	 * @since 1.0.0
	 * @return bolean
	 */
	public function is_exists() {
		return !$this->is_new();
	}

	/**
	 * Get the current session id
	 * 
	 * @return string
	 */
	public function get_session_id() {
		return $this->session_id;
	}

	/**
	 * Generate session data from parameter
	 * 
	 * @since 1.0.0
	 */
	public function get_utm_data() {
		if (empty($_GET)) {
			return;
		}

		$parameters = array_keys(Utils::get_utm_parameters());
		foreach ($parameters as $param_key) {
			if (!empty($_GET[$param_key])) {
				$this->{$param_key} = sanitize_text_field($_GET[$param_key]);
			}
		}
	}

	/**
	 * Get client IP address
	 * 
	 * @since 1.0.0
	 * @return string
	 */
	public function get_client_ip_address() {
		$ipaddress = null;
		if (getenv('HTTP_CLIENT_IP')) {
			$ipaddress = getenv('HTTP_CLIENT_IP');
		} else if (getenv('HTTP_X_FORWARDED_FOR')) {
			$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
		} else if (getenv('HTTP_X_FORWARDED')) {
			$ipaddress = getenv('HTTP_X_FORWARDED');
		} else if (getenv('HTTP_FORWARDED_FOR')) {
			$ipaddress = getenv('HTTP_FORWARDED_FOR');
		} else if (getenv('HTTP_FORWARDED')) {
			$ipaddress = getenv('HTTP_FORWARDED');
		} else if (getenv('REMOTE_ADDR')) {
			$ipaddress = getenv('REMOTE_ADDR');
		}

		if (defined('UTM_EVENT_TRACKER_DEV_MODE')) {
			$ipaddress = sprintf('%s.%s.%s.%s', wp_rand(0, 255), wp_rand(0, 255), wp_rand(0, 255), wp_rand(0, 255));
		}

		return $ipaddress;
	}

	/**
	 * Generate Session ID
	 * 
	 * @since 1.0.0
	 * @return string
	 */
	public function generate_session_id() {
		return implode('-', array(
			bin2hex(random_bytes(3)),
			bin2hex(random_bytes(2)),
			bin2hex(random_bytes(2)),
			bin2hex(random_bytes(2)),
			bin2hex(random_bytes(3)),
		));
	}

	/**
	 * Validate the session ID
	 * 
	 * @since 1.0.0
	 * @return boolean
	 */
	public function validate_session_id($session_id) {
		$session_strings = explode('-', $session_id);
		if (!empty($session_strings[5])) {
			return false;
		}

		if (empty($session_strings[0]) || empty($session_strings[1]) || empty($session_strings[2]) || empty($session_strings[3]) || empty($session_strings[4])) {
			return false;
		}

		if (strlen($session_strings[0]) !== 6) {
			return false;
		}

		if (strlen($session_strings[1]) !== 4) {
			return false;
		}

		if (strlen($session_strings[2]) !== 4) {
			return false;
		}

		if (strlen($session_strings[3]) !== 4) {
			return false;
		}

		if (strlen($session_strings[4]) !== 6) {
			return false;
		}

		return true;
	}

	/**
	 * Get landing page path
	 * 
	 * @since 1.0.0
	 * @return string
	 */
	public function get_landing_page() {
		if (!(is_singular() || is_home() || is_archive())) {
			return '/';
		}

		global $wp;
		$current_path = str_replace(home_url(), '', home_url($wp->request));
		return empty($current_path) ? '/' : $current_path;
	}

	/**
	 * Get hash of current session
	 * 
	 * @since 1.0.0
	 */
	public function get_hash() {
		$session_data = get_object_vars($this);
		unset($session_data['hash'], $session_data['dirty_data']);
		return md5(wp_json_encode($session_data));
	}

	/**
	 * Update the session
	 * 
	 * @since 1.0.0
	 */
	public function update($session_data) {
		global $wpdb;
		$wpdb->update($wpdb->utm_event_tracker_sessions_table, $session_data, array(
			'id' => $this->get_id()
		));
	}

	/**
	 * Save the session
	 * 
	 * @since 1.0.0
	 * @return false|int
	 */
	public function save() {
		if (null == $this->country) {
			$this->set_location();
		}

		$session_data = get_object_vars($this);
		unset($session_data['hash'], $session_data['dirty_data']);
		if ($this->hash == $this->get_hash()) {
			return true;
		}

		$parameters = Utils::get_utm_parameters();
		while ($param = current($parameters)) {
			if (isset($session_data[$param])) {
				$session_data[$param] = sanitize_text_field($session_data[$param]);
			}

			next($parameters);
		}

		global $wpdb;
		$result = $wpdb->replace($wpdb->utm_event_tracker_sessions_table, $session_data);
		if ($result) {
			$this->new_id =  $wpdb->insert_id;
			return  $wpdb->insert_id;
		}

		return false;
	}

	/**
	 * Add view
	 * 
	 * @since 1.0.0
	 */
	public function add_view() {
		if (!$this->is_available()) {
			return;
		}

		if ($this->is_new() || wp_doing_ajax()) {
			return;
		}

		if (!(is_singular() || is_archive() || is_post_type_archive())) {
			return;
		}

		global $wpdb;
		$wpdb->insert($wpdb->utm_event_tracker_views_table, array(
			'session_id' => $this->get_id(),
			'landing_page' => $this->get_landing_page()
		));

		$this->update(array('last_online' => current_time('mysql')));
	}

	/**
	 * Save event data
	 * 
	 * @since 1.0.0
	 */
	public function add_event($event_data) {
		if (!$this->is_available() || !$this->is_exists()) {
			return;
		}

		$event_data['session_id'] = $this->get_id();

		$meta_data = isset($event_data['meta_data']) && is_array($event_data['meta_data']) ? $event_data['meta_data'] : null;
		if (is_array($meta_data)) {
			$event_data['meta_data'] = wp_json_encode($meta_data);
		}

		$event_data['created_on'] = gmdate('Y-m-d H:i:s');

		global $wpdb;
		$wpdb->insert($wpdb->utm_event_tracker_events_table, $event_data);
	}

	/**
	 * Get value from property
	 * 
	 * @since 1.0.0
	 * @return mixed
	 */
	public function get($key, $default = null) {
		return $this->$key ? $this->$key : $default;
	}

	/**
	 * Set session location
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	public function set_location() {
		if (empty($this->ip_address)) {
			return;
		}

		$ipinfo_token = Utils::get_settings_key('ipinfo_token');
		if (empty($ipinfo_token)) {
			return;
		}

		$response = wp_remote_get(sprintf('https://ipinfo.io/%s?token=%s', sanitize_text_field($this->ip_address), sanitize_text_field($ipinfo_token)));
		if ((is_wp_error($response)) || (200 !== wp_remote_retrieve_response_code($response))) {
			return;
		}

		$result = json_decode(wp_remote_retrieve_body($response), true);
		if (!is_array($result)) {
			return;
		}

		if (!empty($result['city'])) {
			$this->city = $result['city'];
		}

		if (!empty($result['region'])) {
			$this->region = $result['region'];
		}

		if (!empty($result['country'])) {
			$this->country = $result['country'];
		}
	}

	/**
	 * Get full landing page URL of the session
	 * 
	 * @since 1.0.0
	 * @return string
	 */
	public function get_landing_page_url() {
		return home_url($this->landing_page);
	}
}
