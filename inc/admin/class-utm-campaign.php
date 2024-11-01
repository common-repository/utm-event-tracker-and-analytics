<?php

namespace UTM_Event_Tracker\Admin;

use UTM_Event_Tracker\Utils;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * UTM Campaign class
 */
final class UTM_Campaign {

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
			__('UTM Campaigns', 'utm-event-tracker'),
			__('Campaigns', 'utm-event-tracker'),
			'manage_options',
			'utm-event-tracker-campaigns',
			array($this, 'screen'),
			10
		);
	}

	/**
	 * UTM campaign page
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	public function screen() {
		include_once UTM_EVENT_TRACKER_PATH . '/template/utm-campaign.php';
	}

	/**
	 * Dashboard widget for short report
	 * 
	 * @since 1.0.0
	 * @return array
	 */
	public function dashboard_widget($widgets) {
		$widgets['utm_campaign'] = array(
			'title' => __('UTM Campaign', 'utm-event-tracker'),
			'aside' => 'left',
			'priority' => 10,
			'placement' => 'left',
			'callback' => array($this, 'widget'),
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
		echo '<utm-overview-widget param="utm_campaign" v-if="!widget_is_visible(\'utm_campaign\')">';
		echo '<template v-slot:header_left>';
		echo '<h3>' . esc_html__('UTM Campaigns', 'utm-event-tracker') . '</h3>';
		echo '</template>';
		echo '</utm-overview-widget>';
	}
}
