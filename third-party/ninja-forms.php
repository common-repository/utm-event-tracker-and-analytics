<?php

namespace UTM_Event_Tracker;

if (!defined('ABSPATH')) {
	exit;
}

if (!class_exists('\NF_Abstracts_MergeTags')) {
	return;
}

final class Ninja_Forms_Tags extends \NF_Abstracts_MergeTags {

	/**
	 * ID of ninja form merge tag
	 * 
	 * @var string
	 * @since 1.0.0
	 */
	protected $id = 'utm_event_tracker_merge_tags';

	/**
	 * Constructor.
	 * 
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();
		$this->title = __('UTM Event Tracker', 'utm-event-tracker');
		$this->ninja_form_tags();
	}

	/**
	 * Set ninja form tags
	 * 
	 * @since 1.0.0
	 */
	public function ninja_form_tags() {
		$session = Session::get_current_session();

		$parameters = Utils::get_all_parameters();
		foreach ($parameters as $key => $label) {
			$this->merge_tags[$key] = array(
				'id' => $key,
				'tag' => '{utm_event_tracker:' . $key . '}',
				'label' => $label,
				'callback' => function () use ($session, $key) {
					return $session->get($key);
				}
			);
		}
	}
}

/**
 * Add ninja form tags
 * 
 * @since 1.0.0
 */
function ninja_form_merge_tags() {
	Ninja_Forms()->merge_tags['utm_event_tracker_merge_tags'] = new Ninja_Forms_Tags();
}
add_action('ninja_forms_loaded', '\UTM_Event_Tracker\ninja_form_merge_tags');

/**
 * Add event after submitting ninja form
 * 
 * @since 1.0.0
 */
function ninja_forms_submission($form_data) {
	utm_event_tracker_add_event('ninja_form_submit', array(
		'meta_data' => array(
			'form_id' => $form_data['form_id']
		)
	));
}
add_action('ninja_forms_after_submission', '\UTM_Event_Tracker\ninja_forms_submission');


/**
 * Ninja form webhook submission
 * 
 * @since 1.0.0
 */
function ninja_forms_webhook_submission($form_data) {
	$data = array();
	foreach ($form_data['fields_by_key'] as $field) {
		if (isset($field['key'])) {
			$data[$field['key']] = $field['value'];
		}
	}

	Webhook::get_instance()->send($data);
}
add_action('ninja_forms_after_submission', '\UTM_Event_Tracker\ninja_forms_webhook_submission');
