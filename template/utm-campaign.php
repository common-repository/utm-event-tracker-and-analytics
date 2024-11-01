<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly 
}
?>
<div class="utm-event-tracker-header">
	<h3><?php esc_html_e('UTM Campaign', 'utm-event-tracker'); ?></h3>
</div>

<div id="utm-campaigns-session-report" class="wrap wrap-utm-event-tracker">
	<hr class="wp-header-end">

	<div class="utm-report-filter-row">
		<div class="left-column">
			<input class="filter-keyword" type="text" placeholder="<?php esc_html_e('Search keywords...', 'utm-event-tracker'); ?>" v-model="keywords">
		</div>
		<input ref="datepicker" type="text" class="utm-event-tracker-date-picker-input">
		<span class="btn-reload dashicons dashicons-update" @click="reload()"></span>
	</div>

	<div class="utm-keywords-stats-container">
		<utm-keywords-stats param="utm_campaign" type="session" :dates="dates">
			<template #heading="{count}">
				<h4><?php esc_html_e('Top {{count}} UTM Campaigns by Sessions', 'utm-event-tracker'); ?></h4>
			</template>
		</utm-keywords-stats>

		<utm-keywords-stats param="utm_campaign" type="view" :dates="dates">
			<template #heading="{count}">
				<h4><?php esc_html_e('Top {{count}} UTM Camapaigns by Views', 'utm-event-tracker'); ?></h4>
			</template>
		</utm-keywords-stats>

		<utm-keywords-stats param="utm_campaign" type="conversion" :dates="dates">
			<template #heading="{count}">
				<h4><?php esc_html_e('Top {{count}} UTM Camapaigns by Conversions', 'utm-event-tracker'); ?></h4>
			</template>
		</utm-keywords-stats>
	</div>

	<session-list-keywords ref="keyword_list_table" column="utm_campaign" :dates="dates" :keywords="keywords"></session-list-keywords>
</div>