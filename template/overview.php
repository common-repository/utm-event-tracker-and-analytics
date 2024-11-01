<?php

namespace UTM_Event_Tracker;

if (!defined('ABSPATH')) {
	exit;
}

$utm_report_widgets = apply_filters('utm_event_tracker/dashboard_widgets', array());

$utm_report_widgets = array_filter($utm_report_widgets, function ($widget) {
	if (!isset($widget['callback'])) {
		return false;
	}

	return is_callable($widget['callback']);
});

$utm_widgets = array_map(function ($widget, $key) {
	if (empty($widget['title'])) {
		$widget['title'] = $key;
	}

	return wp_parse_args($widget, array('id' =>  $key, 'priority' => 10, 'placement' => 'top'));
}, $utm_report_widgets, array_keys($utm_report_widgets));

usort($utm_widgets, function ($a, $b) {
	return $a['priority'] - $b['priority'];
});

$overview_settings = get_option('utm_event_tracker_overview_settings', ''); ?>

<div class="utm-event-tracker-header">
	<h3><?php esc_html_e('Overview', 'utm-event-tracker'); ?></h3>
</div>

<div id="utm-overview-container" class="wrap wrap-utm-event-tracker" data-settings='<?php echo wp_json_encode($overview_settings); ?>'>
	<hr class="wp-header-end">

	<input ref="nonce" type="hidden" value="<?php echo esc_attr(wp_create_nonce('_nonce_utm_event_tracker_overview_settings')); ?>">

	<div class="utm-event-tracker-dashboard-widgets-grid">
		<div class="widgets-column widgets-column-full widget-container-top">
			<?php
			$top_widgets = array_filter($utm_widgets, function ($widget) {
				return 'top' === $widget['placement'];
			});

			foreach ($top_widgets as $widget) {
				call_user_func($widget['callback']);
			}
			?>
		</div>

		<div class="widgets-column widgets-container-left">
			<?php

			$left_widgets = array_filter($utm_widgets, function ($widget) {
				return 'left' === $widget['placement'];
			});

			foreach ($left_widgets as $widget) {
				call_user_func($widget['callback']);
			}
			?>
		</div>

		<div class="widgets-column widgets-container-right">
			<?php

			$right_widgets = array_filter($utm_widgets, function ($widget) {
				return 'right' === $widget['placement'];
			});

			foreach ($right_widgets as $widget) {
				call_user_func($widget['callback']);
			}
			?>
		</div>

		<div class="widgets-column widgets-column-full widgets-container-bottom">
			<?php
			$bottom_widgets = array_filter($utm_widgets, function ($widget) {
				return 'bottom' === $widget['placement'];
			});


			foreach ($bottom_widgets as $widget) {
				call_user_func($widget['callback']);
			} 

			?>
		</div>
	</div>

	<a @click.prevent="show_overview_setting = !show_overview_setting" href="#" class="btn-overview-settings utm-event-tracker-icon-settings"></a>

	<div id="overview-settings" :class="{'overview-settings-show': show_overview_setting}">
		<div class="popup-content">
			<a @click.prevent="show_overview_setting = false" href="#" class="btn-close utm-event-tracker-icon-close"></a>
			<h4><?php esc_html_e('Widgets Settings', 'utm-event-tracker'); ?></h4>
			<ul class="utm-overview-widget-list">
				<?php foreach ($utm_widgets as $widget_item) : ?>
					<li><?php echo esc_html($widget_item['title']); ?> <span @click="update_widget_visibility('<?php echo esc_attr($widget_item['id']); ?>')" :class="['btn-visibility-widget', get_visibility_class('<?php echo esc_attr($widget_item['id']); ?>')]"></span></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</div>