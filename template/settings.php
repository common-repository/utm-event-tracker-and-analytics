<?php

namespace UTM_Event_Tracker;

if (!defined('ABSPATH')) {
	exit;
}

$settings = array();
if (isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_text_field($_POST['_wpnonce']), '_nonce_utm_event_tracker_settings')) {
	$settings = isset($_POST['settings']) && is_array($_POST['settings']) ?  array_map('sanitize_text_field', $_POST['settings']) : array();
}

$settings = wp_parse_args($settings, Utils::get_settings());

?>

<div class="wrap wrap-utm-event-tracker">
	<h1 class="wp-heading-inline"><?php esc_html_e('UTM Event Tracker Settings', 'utm-event-tracker'); ?></h1>
	<hr class="wp-header-end">

	<?php do_action('utm_event_tracker/before_settings_form'); ?>

	<form method="post">
		<?php wp_nonce_field('_nonce_utm_event_tracker_settings'); ?>

		<?php
		if ($this->error->has_errors()) {
			echo '<div class="notice notice-error">';
			echo '<p>' . esc_html($this->error->get_error_message()) . '</p>';
			echo '</div>';
		}
		?>

		<div class="utm-event-tracker-box">
			<div class="utm-event-tracker-heading">
				<h2><?php esc_html_e('Settings', 'utm-event-tracker'); ?></h2>
			</div>

			<table class="form-table">
				<?php do_action('utm_event_tracker/before_settings'); ?>
				<tr>
					<th>
						<label for="cookie-duration"><?php esc_html_e('Cookie Duration', 'utm-event-tracker'); ?></label>
						<p class="field-note"><?php esc_html_e('Specify the days of cookie duration. Default is 30 days.', 'utm-event-tracker'); ?></p>
					</th>
					<td>
						<input style="width: 60px;padding-right: 0" name="settings[cookie_duration]" type="number" id="cookie-duration" value="<?php echo esc_attr($settings['cookie_duration']); ?>">
						<?php esc_html_e('days', 'utm-event-tracker'); ?>
					</td>
				</tr>

				<tr>
					<th>
						<label for="ipinfo-token"><?php esc_html_e('IP Info Token', 'utm-event-tracker'); ?></label>

						<?php
						$note_text = sprintf(
							/* translators: 1 for ipinfo link */
							__('Get token from %s. 50k requests free per month.', 'utm-event-tracker'),
							'<a target="_blank" href="https://ipinfo.io/pricing">IP Info</a>'
						);
						?>

						<p class="field-note"><?php echo wp_kses($note_text, array('a' => array('href' => true, 'target' => true))); ?></p>
					</th>
					<td>
						<input name="settings[ipinfo_token]" type="password" id="ipinfo-token" placeholder="<?php esc_html_e('Enter your IP Info Token', 'utm-event-tracker'); ?>" value="<?php echo esc_attr($settings['ipinfo_token']); ?>">
					</td>
				</tr>

				<tr>
					<th>
						<label class="no-label-description" for="webhook-url"><?php esc_html_e('Webhook URL', 'utm-event-tracker'); ?></label>
					</th>
					<td>
						<input name="settings[webhook_url]" type="text" id="webhook-url" placeholder="<?php esc_html_e('Enter your webhook URL', 'utm-event-tracker'); ?>" value="<?php echo esc_attr($settings['webhook_url']); ?>">
					</td>
				</tr>

				<?php do_action('utm_event_tracker/after_settings'); ?>
			</table>
		</div>

		<div class="form-footer">
			<button class="button button-primary" name="submit" value="save"><?php esc_html_e('Save Changes', 'utm-event-tracker'); ?></button>
		</div>
	</form>
</div>