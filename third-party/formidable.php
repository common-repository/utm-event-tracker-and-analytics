<?php

namespace UTM_Event_Tracker;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Add Formidable plugin to plugin list
 * 
 * @since 1.0.0
 * @return array
 */
function formidable_add_form_plugin($forms) {
	$forms['formidable_form_submit'] = __('Formidable', 'utm-event-tracker');
	return $forms;
}
add_filter('utm_event_tracker/form_submit_plugins_name', '\UTM_Event_Tracker\formidable_add_form_plugin');


/**
 * Handle form submit
 * 
 * @since 1.0.0
 */
function formidable_add_submit_event($params, $errors, $form) {
	utm_event_tracker_add_event('formidable_form_submit', array(
		'meta_data' => array(
			'form_id' => $form->id
		)
	));
}
add_action('frm_process_entry', '\UTM_Event_Tracker\formidable_add_submit_event', 12, 3);

/**
 * Send data to webhook URL after submitting Formidable form
 * 
 * @since 1.0.0
 */
function formidable_webhook_submission($params, $errors, $form) {
	if (!Session::is_available()) {
		return;
	}

	$fields = \FrmFieldsHelper::get_form_fields($form->id, $errors);

	$data = array();
	foreach ($fields as $field) {
		$data[$field->name] = $_POST['item_meta'][$field->id];
	}

	Webhook::get_instance()->send($data);
}
add_action('frm_process_entry', '\UTM_Event_Tracker\formidable_webhook_submission', 12, 3);
