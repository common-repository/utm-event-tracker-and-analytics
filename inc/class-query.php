<?php

namespace UTM_Event_Tracker;

use UTM_Event_Tracker\Utils;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Query class
 */
final class Query {

	/** 
	 * Constructor 
	 * 
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action('wp_ajax_utm_event_tracker/get_sessions', [$this, 'get_sessions']);
		add_action('wp_ajax_utm_event_tracker/get_keywords_report', array($this, 'get_keywords_report'));
		add_action('wp_ajax_utm_event_tracker/get_date_report', array($this, 'get_date_report'));
		add_action('wp_ajax_utm_event_tracker/get_keywords_stats', array($this, 'get_keywords_stats'));
		add_action('wp_ajax_utm_event_tracker/update_overview_settings', array($this, 'update_overview_settings'));
	}

	/**
	 * Get session data of keywords
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	public function get_sessions() {
		$result = check_ajax_referer('_nonce_session_list_keywords', false, false);
		if (false === $result) {
			wp_send_json_error(array(
				'error' => __('Security failed.', 'utm-event-tracker')
			));
		}

		if (empty($_POST['start_date']) || empty($_POST['end_date'])) {
			wp_send_json_error(array(
				'error' => __('Missing dates information.', 'utm-event-tracker')
			));
		}

		$start_date = gmdate('Y-m-d 00:00:00', strtotime(sanitize_text_field($_POST['start_date'])));
		$end_date = gmdate('Y-m-d 23:59:59', strtotime(sanitize_text_field($_POST['end_date'])));
		$per_page = isset($_POST['per_page']) ? absint($_POST['per_page']) : 20;
		$page_no = isset($_POST['page']) ? absint($_POST['page']) : 1;

		global $wpdb;

		$utm_event_tracker_column = empty($_POST['column']) ? 'utm_campaign' : sanitize_text_field($_POST['column']);
		if (!in_array($utm_event_tracker_column, array('utm_campaign', 'utm_source', 'utm_term', 'utm_medium', 'utm_content'))) {
			$utm_event_tracker_column = 'utm_campaign';
		}

		$offset = ($page_no - 1) * $per_page;

		$sort_column = !empty($_POST['sort_column']) ? sanitize_text_field($_POST['sort_column']) : 'created_on';
		if (!in_array($sort_column, array('utm_campaign', 'utm_medium', 'utm_source', 'utm_term', 'utm_content', 'city', 'region', 'country', 'total_views', 'total_events'))) {
			$sort_column = 'created_on';
		}

		$keywords = !empty($_POST['keywords']) ? sanitize_text_field($_POST['keywords']) : '';

		$sort_type = !empty($_POST['sort_type']) ? sanitize_text_field($_POST['sort_type']) : 'DESC';

		if ('DESC' == $sort_type) {
			$items = $wpdb->get_results($wpdb->prepare(
				"SELECT *, %i AS keyword, created_on, city, region, country, 
				IFNULL(views.views, 0) AS total_views, IFNULL(events.events, 0) AS total_events
				FROM $wpdb->utm_event_tracker_sessions_table as sessions
				LEFT JOIN (
					SELECT session_id, count(*) as views FROM $wpdb->utm_event_tracker_views_table GROUP BY session_id
				) as views ON sessions.id = views.session_id
				LEFT JOIN (
					SELECT session_id, count(*) as events FROM $wpdb->utm_event_tracker_events_table GROUP BY session_id
				) as events ON sessions.id = events.session_id
				WHERE %i != '' AND %i LIKE %s AND created_on BETWEEN %s AND %s
				ORDER BY %i ASC LIMIT %d, %d",
				$utm_event_tracker_column,
				$utm_event_tracker_column,
				$utm_event_tracker_column,
				'%' . $wpdb->esc_like($keywords) . '%',
				$start_date,
				$end_date,
				$sort_column,
				$offset,
				$per_page
			));
		} else {
			$items = $wpdb->get_results($wpdb->prepare(
				"SELECT *, %i AS keyword, created_on, city, region, country, 
				IFNULL(views.views, 0) AS total_views, IFNULL(events.events, 0) AS total_events
				FROM $wpdb->utm_event_tracker_sessions_table as sessions
				LEFT JOIN (
					SELECT session_id, count(*) as views FROM $wpdb->utm_event_tracker_views_table GROUP BY session_id
				) as views ON sessions.id = views.session_id
				LEFT JOIN (
					SELECT session_id, count(*) as events FROM $wpdb->utm_event_tracker_events_table GROUP BY session_id
				) as events ON sessions.id = events.session_id
				WHERE %i != '' AND %i LIKE %s AND created_on BETWEEN %s AND %s
				ORDER BY %i DESC LIMIT %d, %d",
				$utm_event_tracker_column,
				$utm_event_tracker_column,
				$utm_event_tracker_column,
				'%' . $wpdb->esc_like($keywords) . '%',
				$start_date,
				$end_date,
				$sort_column,
				$offset,
				$per_page
			));
		}

		$one_week_ago = strtotime('-1 week');

		$parameters = array_keys(Utils::get_utm_parameters());

		array_walk($items, function (&$item) use ($one_week_ago, $wpdb, $parameters) {
			$na_text = esc_html__('N/A', 'utm-event-tracker');
			foreach ($parameters as $param) {
				if (empty($item->{$param})) {
					$item->{$param} = $na_text;
				}

				$item->{$param} = html_entity_decode($item->{$param});
			}

			$item->keyword = html_entity_decode($item->keyword);
			$item->country = Utils::get_country_name($item->country);

			$item->timestamp = Utils::get_date($item->created_on, true);
			$item->readable_time = human_time_diff($item->timestamp, current_time('timestamp')) . ' ' . __('ago', 'utm-event-tracker');
			$item->session_date = gmdate(get_option('date_format') . ' ' . get_option('time_format'), $item->timestamp);
			$item->show_readable_time = ($item->timestamp > $one_week_ago);
			$item->landing_page_url = home_url($item->landing_page);

			$item->journey = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->utm_event_tracker_views_table WHERE session_id = %d", $item->id));
			array_walk($item->journey, function (&$item) {
				$item->landing_page_url = home_url($item->landing_page);
			});

			$item->events = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->utm_event_tracker_events_table WHERE session_id = %d ORDER BY created_on DESC LIMIT 0, 2", $item->id));
			array_walk($item->events, function (&$event_data) {
				$event = new Event($event_data);
				$event_data->description = $event->get_description();
			});

			do_action('utm_event_tracker/get_sessions/session_item', $item);
		});


		$total_items = $wpdb->get_var($wpdb->prepare(
			"SELECT count(*) as total_items
			FROM $wpdb->utm_event_tracker_sessions_table as sessions
			WHERE %i != '' AND %i LIKE %s AND created_on BETWEEN %s AND %s
			ORDER BY created_on DESC",
			$utm_event_tracker_column,
			$utm_event_tracker_column,
			'%' . $wpdb->esc_like($keywords) . '%',
			$start_date,
			$end_date
		));

		wp_send_json_success(array(
			'items' => $items,
			'total' => absint($total_items)
		));
	}

	/**
	 * Get keywords based report
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	public function get_keywords_report() {
		global $wpdb;

		$result = check_ajax_referer('_nonce_utm_overview_widget', false, false);
		if (false === $result) {
			wp_send_json_error(array(
				'error' => __('Security failed.', 'utm-event-tracker')
			));
		}

		$supported_columns = array('utm_campaign', 'utm_medium', 'utm_term', 'utm_source', 'utm_content', 'fbclid', 'gclid');

		$utm_event_tracker_column = !empty($_POST['param']) ? sanitize_text_field($_POST['param']) : null;
		if (!in_array($utm_event_tracker_column, $supported_columns)) {
			wp_send_json_error(array(
				'error' => __('No supported parameter found.', 'utm-event-tracker')
			));
		}

		if (empty($_POST['start_date']) || empty($_POST['end_date'])) {
			wp_send_json_error(array(
				'error' => __('Missing dates information.', 'utm-event-tracker')
			));
		}

		$start_date = gmdate('Y-m-d 00:00:00', strtotime(sanitize_text_field($_POST['start_date'])));
		$end_date = gmdate('Y-m-d 23:59:59', strtotime(sanitize_text_field($_POST['end_date'])));

		$results = $wpdb->get_results($wpdb->prepare(
			"SELECT %i AS keyword, count(*) AS sessions, IFNULL(sum(views.views), 0) AS views, IFNULL(sum(events.events), 0) AS events
			FROM $wpdb->utm_event_tracker_sessions_table as sessions
			LEFT JOIN (
				SELECT session_id, count(*) as views FROM $wpdb->utm_event_tracker_views_table GROUP BY session_id
			) as views ON sessions.id = views.session_id
			LEFT JOIN (
				SELECT session_id, count(*) as events FROM $wpdb->utm_event_tracker_events_table GROUP BY session_id
			) as events ON sessions.id = events.session_id
			WHERE %i != '' AND created_on BETWEEN %s AND %s
			GROUP BY %i ORDER BY sessions DESC",
			$utm_event_tracker_column,
			$utm_event_tracker_column,
			$start_date,
			$end_date,
			$utm_event_tracker_column
		));

		array_walk($results, function (&$item) {
			$item->keyword = html_entity_decode($item->keyword);
		});

		wp_send_json_success($results);
	}

	/**
	 * Get date wise report for each keyword
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	public function get_date_report() {
		$result = check_ajax_referer('_nonce_utm_overview_widget', false, false);
		if (false === $result) {
			wp_send_json_error(array(
				'error' => __('Security failed.', 'utm-event-tracker')
			));
		}

		if (empty($_POST['start_date']) || empty($_POST['end_date'])) {
			wp_send_json_error(array(
				'error' => __('Missing dates information.', 'utm-event-tracker')
			));
		}

		if (empty($_POST['param'])) {
			wp_send_json_error(array(
				'error' => __('Missing parameter', 'utm-event-tracker')
			));
		}

		$start_date = gmdate('Y-m-d 00:00:00', strtotime(sanitize_text_field($_POST['start_date'])));
		$end_date = gmdate('Y-m-d 23:59:59', strtotime(sanitize_text_field($_POST['end_date'])));

		global $wpdb;
		$utm_event_tracker_column = sanitize_text_field($_POST['param']);

		$results = $wpdb->get_results($wpdb->prepare(
			"SELECT DATE(created_on) date, count(*) AS sessions, IFNULL(sum(views.views), 0) AS views, IFNULL(sum(events.events), 0) AS events
			FROM $wpdb->utm_event_tracker_sessions_table as sessions
			LEFT JOIN (
				SELECT session_id, count(*) as views FROM $wpdb->utm_event_tracker_views_table GROUP BY session_id
			) as views ON sessions.id = views.session_id
			LEFT JOIN (
				SELECT session_id, count(*) as events FROM $wpdb->utm_event_tracker_events_table GROUP BY session_id
			) as events ON sessions.id = events.session_id
			WHERE %i != '' AND created_on BETWEEN %s AND %s
			GROUP BY date ORDER BY date DESC",
			$utm_event_tracker_column,
			$start_date,
			$end_date
		));

		wp_send_json_success($results);
	}

	/**
	 * Get keywords stats
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	public function get_keywords_stats() {
		global $wpdb;

		$result = check_ajax_referer('_nonce_utm_keywords_stats', false, false);
		if (false === $result) {
			wp_send_json_error(array(
				'error' => __('Security failed.', 'utm-event-tracker')
			));
		}

		$stats_type = !empty($_POST['stats_type']) ? sanitize_text_field($_POST['stats_type']) : null;
		if (!in_array($stats_type, array('session', 'view', 'conversion'))) {
			wp_send_json_error(array(
				'error' => __('Keywords stats type is missing.', 'utm-event-tracker')
			));
		}

		$utm_event_tracker_column = !empty($_POST['parameter']) ? sanitize_text_field($_POST['parameter']) : null;
		$supported_columns = array('utm_campaign', 'utm_medium', 'utm_term', 'utm_source', 'utm_content');

		if (!in_array($utm_event_tracker_column, $supported_columns)) {
			wp_send_json_error(array(
				'error' => __('No supported parameter found.', 'utm-event-tracker')
			));
		}

		if (empty($_POST['start_date']) || empty($_POST['end_date'])) {
			wp_send_json_error(array(
				'error' => __('Missing dates information.', 'utm-event-tracker')
			));
		}

		$start_date = gmdate('Y-m-d 00:00:00', strtotime(sanitize_text_field($_POST['start_date'])));
		$end_date = gmdate('Y-m-d 23:59:59', strtotime(sanitize_text_field($_POST['end_date'])));

		global $wpdb;

		if ('session' === $stats_type) {
			$keywords = $wpdb->get_results($wpdb->prepare(
				"SELECT %i AS keyword, count(*) AS quantity FROM $wpdb->utm_event_tracker_sessions_table as sessions
				WHERE %i != '' AND created_on BETWEEN %s AND %s
				GROUP BY keyword HAVING quantity > 0 ORDER BY quantity DESC LIMIT 5",
				$utm_event_tracker_column,
				$utm_event_tracker_column,
				$start_date,
				$end_date
			));
		}

		if ('view' === $stats_type) {
			$keywords = $wpdb->get_results($wpdb->prepare(
				"SELECT %i AS keyword, IFNULL(views.views, 0) AS quantity FROM $wpdb->utm_event_tracker_sessions_table as sessions
				LEFT JOIN (
					SELECT session_id, count(*) as views FROM $wpdb->utm_event_tracker_views_table GROUP BY session_id
				) as views ON sessions.id = views.session_id
				WHERE %i != '' AND created_on BETWEEN %s AND %s
				GROUP BY keyword HAVING quantity > 0 ORDER BY quantity DESC LIMIT 5",
				$utm_event_tracker_column,
				$utm_event_tracker_column,
				$start_date,
				$end_date
			));
		}

		if ('conversion' === $stats_type) {
			$keywords = $wpdb->get_results($wpdb->prepare(
				"SELECT %i AS keyword, IFNULL(events.events, 0) AS quantity FROM $wpdb->utm_event_tracker_sessions_table as sessions
				LEFT JOIN (
					SELECT session_id, count(*) as events FROM $wpdb->utm_event_tracker_events_table GROUP BY session_id
				) as events ON sessions.id = events.session_id
				WHERE %i != '' AND created_on BETWEEN %s AND %s
				GROUP BY keyword HAVING quantity > 0 ORDER BY quantity DESC LIMIT 5",
				$utm_event_tracker_column,
				$utm_event_tracker_column,
				$start_date,
				$end_date
			));
		}

		if (!is_array($keywords)) {
			$keywords = [];
		}

		$total_quantity = array_sum(wp_list_pluck($keywords, 'quantity'));
		if ($total_quantity <= 0) {
			$total_quantity = 1;
		}

		array_walk($keywords, function (&$keyword) use ($total_quantity) {
			$keyword->keyword = html_entity_decode($keyword->keyword);
			$keyword->percentage = round(($keyword->quantity * 100) / $total_quantity, 2);
		});

		wp_send_json_success($keywords);
	}

	/**
	 * Save overview settings
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	public function update_overview_settings() {
		check_ajax_referer('_nonce_utm_event_tracker_overview_settings');

		$hide_widgets = isset($_POST['hide_widgets']) && is_array($_POST['hide_widgets']) ? array_map('sanitize_text_field', $_POST['hide_widgets']) : [];
		update_option('utm_event_tracker_overview_settings', array(
			'hide_widgets' => $hide_widgets
		));
		wp_send_json_success();
	}
}
