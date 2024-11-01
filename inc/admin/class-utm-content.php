<?php

namespace UTM_Event_Tracker\Admin;

use UTM_Event_Tracker\Utils;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * UTM Campaign class
 */
final class UTM_Content {

	/** 
	 * Constructor 
	 * 
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action('utm_event_tracker/admin_menu', [$this, 'admin_menu'], 5);
		add_filter('utm_event_tracker/dashboard_widgets', [$this, 'dashboard_widget'], 5);		
	}

	/**
	 * Register submneu page for UTM campaign
	 * 
	 * @since 1.0.0
	 */
	public function admin_menu() {
		add_submenu_page(
			'utm-event-tracker',
			__('UTM Contents', 'utm-event-tracker'),
			__('Contents', 'utm-event-tracker'),
			'manage_options',
			'utm-event-tracker-contents',
			array($this, 'screen'),
			10
		);
	}

	/**
	 * UTM content page
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	public function screen() {
		include_once UTM_EVENT_TRACKER_PATH . '/template/utm-content.php';
	}

	/**
	 * Dashboard widget for short report
	 * 
	 * @since 1.0.0
	 * @return array
	 */
	public function dashboard_widget($widgets) {
		$widgets['utm_content'] = array(
			'priority' => 10,
			'placement' => 'right',
			'callback' => array($this, 'widget'),
			'title' => __('UTM Contents', 'utm-event-rtacker'),
		);

		return $widgets;
	}

	/**
	 * Widget template
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	public function widget() {
		echo '<utm-overview-widget param="utm_content" v-if="!widget_is_visible(\'utm_content\')">';
		echo '<template v-slot:header_left>';
		echo '<h3>' . esc_html__('UTM Contents', 'utm-event-tracker') . '</h3>';
		echo '</template>';
		echo '</utm-overview-widget>';
	}
}
