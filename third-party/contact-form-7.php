<?php

namespace UTM_Event_Tracker;

if (!defined('ABSPATH')) {
	exit;
}

class Contact_Form_7 {

	/**
	 * Constructor.
	 * 
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action('wpcf7_init', [$this, 'add_tags']);
		add_action('wpcf7_submit', [$this, 'wpcf7_submit']);
		add_action('wpcf7_submit', [$this, 'webhook_submit']);
		add_action('wpcf7_admin_init', array($this, 'add_utm_tag_generator'), 52);
	}

	/**
	 * Add tag
	 * 
	 * @since 1.0.0
	 */
	public function add_tags() {
		wpcf7_add_form_tag(array('utm_event_tracker', 'utm_event_tracker*'), array($this, 'add_tag'), array('name-attr' => true));
	}

	/**
	 * Add tag
	 * 
	 * @since 1.0.0
	 */
	public function add_tag($tag) {
		$tag = new \WPCF7_FormTag($tag);
		if (empty($tag->name)) {
			return '';
		}

		$atts = array();
		$atts['id'] = $tag->get_id_option();
		$atts['name'] = $tag->name;

		$parameters = $tag->get_option('param');
		if (false === $parameters || count($parameters) == 0) {
			return;
		}

		$param = sanitize_text_field(reset($parameters));

		if (Session::is_available()) {
			$session = Session::get_current_session();

			$value = $session->{$param};
			if ('landing_page' == $param) {
				$value = $session->get_landing_page_url();
			}

			$atts['value'] = $value;
		}

		return !empty($atts['value']) ? sprintf('<input type="hidden" %s>', wpcf7_format_atts($atts)) : '';
	}

	/**
	 * Add tag generator item
	 * 
	 * @since 1.0.0
	 */
	public function add_utm_tag_generator() {
		$tag_generator = \WPCF7_TagGenerator::get_instance();
		$tag_generator->add('utm_event_tracker', __('UTM Event Tracker', 'utm-event-tracker'), array($this, 'utm_tag_generator'));
	}

	/**
	 * Add event after submitting the contact form 7
	 * 
	 * @since 1.0.0
	 */
	public function wpcf7_submit() {
		$submission = \WPCF7_Submission::get_instance();

		utm_event_tracker_add_event('contact_form_7_submit', array(
			'meta_data' => array(
				'form_id' => $submission->get_contact_form()->id()
			)
		));
	}

	/**
	 * Tag generator popup for UTM event tracker
	 * 
	 * @since 1.0.0
	 */
	public function utm_tag_generator($contact_form, $args = '') {
		$args = wp_parse_args($args, array());
		$parameters = Utils::get_all_parameters(); ?>
		<div class="control-box">

			<table class="form-table">
				<tbody>

					<tr>
						<th scope="row"><label for="<?php echo esc_attr($args['content'] . '-name'); ?>"><?php echo esc_html(__('Name', 'utm-event-tracker')); ?></label></th>
						<td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr($args['content'] . '-name'); ?>" /></td>
					</tr>

					<tr>
						<th scope="row"><label for="<?php echo esc_attr($args['content'] . '-param'); ?>"><?php echo esc_html(__('UTM Parameter', 'utm-event-tracker')); ?></label></th>
						<td>
							<select id="<?php echo esc_attr($args['content'] . '-param'); ?>">
								<?php
								foreach ($parameters as $key => $label) {
									printf('<option value="%s">%s</option>', esc_attr($key), esc_html($label));
								}
								?>
							</select>
							<input id="utm-event-tracker-param-holder" type="hidden" name="param" class="option" value="utm_campaign">
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div class="insert-box">
			<input type="text" name="utm_event_tracker" class="tag code" readonly="readonly" onfocus="this.select()" />
			<div class="submitbox">
				<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr(__('Insert Tag', 'utm-event-tracker')); ?>" />
			</div>
			<p class="description mail-tag">
				<label for="<?php echo esc_attr($args['content'] . '-mailtag'); ?>">
					<?php echo sprintf(
						/* translators: %s for tag */
						esc_html(__('To use the value input through this field in a mail field, you need to insert the corresponding mail-tag (%s) into the field on the Mail tab.', 'utm-event-tracker')),
						'<strong><span class="mail-tag"></span></strong>'
					);  ?>
					<input type="text" class="mail-tag code hidden" readonly="readonly" id="<?php echo esc_attr($args['content'] . '-mailtag'); ?>" />
				</label>
			</p>
		</div>

		<script>
			(function($) {
				$('#tag-generator-panel-utm_event_tracker-param').on('change', function() {
					const value = $(this).val();
					$('#utm-event-tracker-param-holder').val(value)
				}).trigger('change')

			})(jQuery)
		</script>
<?php
	}

	/**
	 * Submit data to webhook
	 * 
	 * @since 1.0.0
	 */
	public function webhook_submit() {
		if (!Session::is_available()) {
			return;
		}

		$submission = \WPCF7_Submission::get_instance();
		Webhook::get_instance()->send($submission->get_posted_data());
	}
}

new Contact_Form_7();
