<?php

namespace UTM_Event_Tracker;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Add custom merge tags for gravity form
 * 
 * @since 1.0.0
 * @return array
 */
function gravity_form_utm_merge_tags($tags) {
	$parameters = Utils::get_all_parameters();
	foreach ($parameters as $key => $label) {
		$tags[] = array(
			'tag' => sprintf('{utm_event_tracker:%s}', $key),
			'label' => sprintf('%s - %s', esc_html__('UTM Event Tracker', 'utm-event-tracker'), $label)
		);
	}

	return $tags;
}
add_filter('gform_custom_merge_tags', '\UTM_Event_Tracker\gravity_form_utm_merge_tags');

/**
 * Replace custom merge tags
 * 
 * @since 1.0.0
 * @return stringadmin
 */
function gravity_form_replace_merge_tags($text) {
	$session = Session::get_current_session();

	$parameters = array_keys(Utils::get_all_parameters());
	while ($key = current($parameters)) {
		$text = str_replace("{utm_event_tracker:{$key}}", $session->{$key}, $text);
		next($parameters);
	}

	return $text;
}
add_filter('gform_replace_merge_tags', '\UTM_Event_Tracker\gravity_form_replace_merge_tags');

/**
 * Add action after gravity form submission
 * 
 * @since 1.0.0
 * @return void
 */
function gravity_form_submission($entry, $form) {
	utm_event_tracker_add_event('gravity_form_submission', array(
		'meta_data' => array(
			'form_id' => $form['id']
		)
	));
}
add_action('gform_after_submission', '\UTM_Event_Tracker\gravity_form_submission', 12, 2);


/**
 * Gravity form webhook submission
 * 
 * @since 1.0.0
 */
function gform_webhook_submission($entry, $form) {
	if (!Session::is_available()) {
		return;
	}

	$data = array();
	foreach ($form['fields'] as $field) {
		$inputs = $field->get_entry_inputs();
		if (is_array($inputs)) {
			foreach ($inputs as $input) {
				$value = rgar($entry, (string) $input['id']);
				$label = isset($input['adminLabel']) && '' != $input['adminLabel'] ? $input['adminLabel'] : 'input_' . $input['id'];
				$data[$label] = $value;
			}
		} else {
			$value = rgar($entry, (string) $field->id);
			$label = isset($field->adminLabel) && '' != $field->adminLabel ? $field->adminLabel : 'input_' . $field->id;
			$data[$label] = $value;
		}
	}

	Webhook::get_instance()->send($data);
}
add_action('gform_after_submission', '\UTM_Event_Tracker\gform_webhook_submission', 10, 2);
