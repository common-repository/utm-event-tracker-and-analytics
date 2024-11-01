<?php

namespace UTM_Event_Tracker;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Migrate class plugin
 */
final class Migrate {

	/** 
	 * Constructor 
	 * 
	 * @since 1.0.0
	 */
	public function __construct() {
		register_activation_hook(UTM_EVENT_TRACKER_FILE, [$this, 'activate']);
		register_activation_hook(UTM_EVENT_TRACKER_FILE, [$this, 'schedule_event']);

		add_action('utm_event_tracker/update_session_location', [$this, 'update_session_location']);
	}

	/**
	 * Create data table
	 * 
	 * @since 1.0
	 * @return void
	 */
	public function activate() {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		maybe_create_table($wpdb->utm_event_tracker_sessions_table, "CREATE TABLE $wpdb->utm_event_tracker_sessions_table (
			`id` INT NOT NULL AUTO_INCREMENT, 
			`session_id` VARCHAR(28) NOT NULL DEFAULT '',
			`utm_campaign` VARCHAR(50) NULL,
			`utm_medium` VARCHAR(50) NULL,
			`utm_source` VARCHAR(50) NULL,
			`utm_term` VARCHAR(50) NULL,
			`utm_content` VARCHAR(255) NULL,
			`fbclid` VARCHAR(100) NULL,
			`gclid` VARCHAR(100) NULL,
			`landing_page` VARCHAR(150) NOT NULL DEFAULT '',
			`ip_address` VARCHAR(50) NULL,
			`city` VARCHAR(50) NULL,
			`region` VARCHAR(50) NULL,
			`country` VARCHAR(5) NULL,
			`last_online` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`created_on` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`id`)
		);");

		maybe_create_table($wpdb->utm_event_tracker_views_table, "CREATE TABLE $wpdb->utm_event_tracker_views_table (
			`id` INT NOT NULL AUTO_INCREMENT, 
			`session_id` VARCHAR(100) NULL,
			`landing_page` VARCHAR(255) NULL,
			`created_on` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`id`)
		);");

		maybe_create_table($wpdb->utm_event_tracker_events_table, "CREATE TABLE $wpdb->utm_event_tracker_events_table (
			`id` INT NOT NULL AUTO_INCREMENT, 
			`session_id` VARCHAR(100) NULL,
			`type` VARCHAR(50) NULL,
			`currency` VARCHAR(3) NULL,
			`amount` FLOAT NOT NULL DEFAULT 0.00,
			`meta_data` MEDIUMTEXT NULL,
			`created_on` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`id`)
		);");
	}

	/**
	 * Schedule events
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	public function schedule_event() {
		if (!wp_next_scheduled('utm_event_tracker/update_session_location')) {
			wp_schedule_event(time(), 'hourly', 'utm_event_tracker/update_session_location');
		}
	}

	/**
	 * Update session location
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	public function update_session_location() {
		global $wpdb;
		$sessions = $wpdb->get_results("SELECT * FROM $wpdb->utm_event_tracker_sessions_table WHERE country IS null ORDER BY created_on DESC LIMIT 0, 100");

		array_walk($sessions, function ($item) {
			$session = new Session($item);
			$session->save();
		});
	}
}

new Migrate();
