<?php

namespace UTM_Event_Tracker;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Add wpforms into the form plugin list
 * 
 * @since 1.0.0
 * @return array
 */
function wpforms_add_form_plugin($forms) {
	$forms['wpforms_submission'] = __('WPForms', 'utm-event-tracker');
	return $forms;
}
add_filter('utm_event_tracker/form_submit_plugins_name', '\UTM_Event_Tracker\wpforms_add_form_plugin');

/**
 * Add custom merge tags for gravity form
 * 
 * @since 1.0.0
 * @return array
 */
function wpforms_add_smart_tags($tags) {
	$parameters = Utils::get_all_parameters();
	foreach ($parameters as $key => $label) {
		$tags['utm_event_tracker_' . $key] = esc_html__('UTM Event Tracker', 'utm_event_tracker') . ' - ' . esc_html($label);
	}

	return $tags;
}
add_filter('wpforms_smart_tags', '\UTM_Event_Tracker\wpforms_add_smart_tags');

/**
 * Replace smart tags value
 * 
 * @since 1.0.0
 * @return string
 */
function wpforms_smart_tags_value($content, $tag) {
	$session = Session::get_current_session();

	$parameters = array_keys(Utils::get_all_parameters());
	while ($key = current($parameters)) {
		$smart_tag_key  = 'utm_event_tracker_' . $key;
		if ($smart_tag_key === $tag) {
			$content = str_replace('{' . $smart_tag_key . '}', $session->get($key), $content);
		}

		next($parameters);
	}

	return $content;
}
add_filter('wpforms_smart_tag_process', '\UTM_Event_Tracker\wpforms_smart_tags_value', 100, 2);

/** 
 * Add event after form submission
 * 
 * @since 1.0.0
 * @return void
 */
function wpforms_process_complete($fields, $entry, $form_data) {
	$session = Session::get_current_session();
	$session->add_event(array(
		'type' => 'wpforms_submission',
		'meta_data' => array(
			'form_id' => $form_data['id']
		)
	));
}
add_action('wpforms_process_complete', '\UTM_Event_Tracker\wpforms_process_complete', 10, 3);

/** 
 * Submit data to webhook URL
 * 
 * @since 1.0.0
 * @return void
 */
function wpforms_webhook_submission($fields, $entry, $form_data) {
	if (!Session::is_available()) {
		return;
	}

	$data = array();
	foreach ($fields as $field_item) {
		$data[$field_item['name']] = $field_item['value'];
	}

	Webhook::get_instance()->send($data);
}
add_action('wpforms_process_complete', '\UTM_Event_Tracker\wpforms_webhook_submission', 10, 3);
