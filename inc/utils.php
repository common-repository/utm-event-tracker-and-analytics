<?php

namespace UTM_Event_Tracker;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Utils class
 * 
 * @since 1.0.0
 */
class Utils {

	/**
	 * Get country list
	 * 
	 * @since 1.0.0
	 * @return array
	 */
	public static function get_countries() {
		return array(
			'AF' => __('Afghanistan', 'shopify-checkout-assistant'),
			'AL' => __('Albania', 'shopify-checkout-assistant'),
			'DZ' => __('Algeria', 'shopify-checkout-assistant'),
			'AS' => __('American Samoa', 'shopify-checkout-assistant'),
			'AD' => __('Andorra', 'shopify-checkout-assistant'),
			'AO' => __('Angola', 'shopify-checkout-assistant'),
			'AI' => __('Anguilla', 'shopify-checkout-assistant'),
			'AQ' => __('Antarctica', 'shopify-checkout-assistant'),
			'AG' => __('Antigua and Barbuda', 'shopify-checkout-assistant'),
			'AR' => __('Argentina', 'shopify-checkout-assistant'),
			'AM' => __('Armenia', 'shopify-checkout-assistant'),
			'AW' => __('Aruba', 'shopify-checkout-assistant'),
			'AU' => __('Australia', 'shopify-checkout-assistant'),
			'AT' => __('Austria', 'shopify-checkout-assistant'),
			'AZ' => __('Azerbaijan', 'shopify-checkout-assistant'),
			'BS' => __('Bahamas', 'shopify-checkout-assistant'),
			'BH' => __('Bahrain', 'shopify-checkout-assistant'),
			'BD' => __('Bangladesh', 'shopify-checkout-assistant'),
			'BB' => __('Barbados', 'shopify-checkout-assistant'),
			'BY' => __('Belarus', 'shopify-checkout-assistant'),
			'BE' => __('Belgium', 'shopify-checkout-assistant'),
			'BZ' => __('Belize', 'shopify-checkout-assistant'),
			'BJ' => __('Benin', 'shopify-checkout-assistant'),
			'BM' => __('Bermuda', 'shopify-checkout-assistant'),
			'BT' => __('Bhutan', 'shopify-checkout-assistant'),
			'BO' => __('Bolivia', 'shopify-checkout-assistant'),
			'BA' => __('Bosnia and Herzegovina', 'shopify-checkout-assistant'),
			'BW' => __('Botswana', 'shopify-checkout-assistant'),
			'BV' => __('Bouvet Island', 'shopify-checkout-assistant'),
			'BR' => __('Brazil', 'shopify-checkout-assistant'),
			'BQ' => __('British Antarctic Territory', 'shopify-checkout-assistant'),
			'IO' => __('British Indian Ocean Territory', 'shopify-checkout-assistant'),
			'VG' => __('British Virgin Islands', 'shopify-checkout-assistant'),
			'BN' => __('Brunei', 'shopify-checkout-assistant'),
			'BG' => __('Bulgaria', 'shopify-checkout-assistant'),
			'BF' => __('Burkina Faso', 'shopify-checkout-assistant'),
			'BI' => __('Burundi', 'shopify-checkout-assistant'),
			'KH' => __('Cambodia', 'shopify-checkout-assistant'),
			'CM' => __('Cameroon', 'shopify-checkout-assistant'),
			'CA' => __('Canada', 'shopify-checkout-assistant'),
			'CT' => __('Canton and Enderbury Islands', 'shopify-checkout-assistant'),
			'CV' => __('Cape Verde', 'shopify-checkout-assistant'),
			'KY' => __('Cayman Islands', 'shopify-checkout-assistant'),
			'CF' => __('Central African Republic', 'shopify-checkout-assistant'),
			'TD' => __('Chad', 'shopify-checkout-assistant'),
			'CL' => __('Chile', 'shopify-checkout-assistant'),
			'CN' => __('China', 'shopify-checkout-assistant'),
			'CX' => __('Christmas Island', 'shopify-checkout-assistant'),
			'CC' => __('Cocos [Keeling] Islands', 'shopify-checkout-assistant'),
			'CO' => __('Colombia', 'shopify-checkout-assistant'),
			'KM' => __('Comoros', 'shopify-checkout-assistant'),
			'CG' => __('Congo - Brazzaville', 'shopify-checkout-assistant'),
			'CD' => __('Congo - Kinshasa', 'shopify-checkout-assistant'),
			'CK' => __('Cook Islands', 'shopify-checkout-assistant'),
			'CR' => __('Costa Rica', 'shopify-checkout-assistant'),
			'HR' => __('Croatia', 'shopify-checkout-assistant'),
			'CU' => __('Cuba', 'shopify-checkout-assistant'),
			'CY' => __('Cyprus', 'shopify-checkout-assistant'),
			'CZ' => __('Czech Republic', 'shopify-checkout-assistant'),
			'CI' => __('Côte d\'Ivoire', 'shopify-checkout-assistant'),
			'DK' => __('Denmark', 'shopify-checkout-assistant'),
			'DJ' => __('Djibouti', 'shopify-checkout-assistant'),
			'DM' => __('Dominica', 'shopify-checkout-assistant'),
			'DO' => __('Dominican Republic', 'shopify-checkout-assistant'),
			'NQ' => __('Dronning Maud Land', 'shopify-checkout-assistant'),
			'DD' => __('East Germany', 'shopify-checkout-assistant'),
			'EC' => __('Ecuador', 'shopify-checkout-assistant'),
			'EG' => __('Egypt', 'shopify-checkout-assistant'),
			'SV' => __('El Salvador', 'shopify-checkout-assistant'),
			'GQ' => __('Equatorial Guinea', 'shopify-checkout-assistant'),
			'ER' => __('Eritrea', 'shopify-checkout-assistant'),
			'EE' => __('Estonia', 'shopify-checkout-assistant'),
			'ET' => __('Ethiopia', 'shopify-checkout-assistant'),
			'FK' => __('Falkland Islands', 'shopify-checkout-assistant'),
			'FO' => __('Faroe Islands', 'shopify-checkout-assistant'),
			'FJ' => __('Fiji', 'shopify-checkout-assistant'),
			'FI' => __('Finland', 'shopify-checkout-assistant'),
			'FR' => __('France', 'shopify-checkout-assistant'),
			'GF' => __('French Guiana', 'shopify-checkout-assistant'),
			'PF' => __('French Polynesia', 'shopify-checkout-assistant'),
			'TF' => __('French Southern Territories', 'shopify-checkout-assistant'),
			'FQ' => __('French Southern and Antarctic Territories', 'shopify-checkout-assistant'),
			'GA' => __('Gabon', 'shopify-checkout-assistant'),
			'GM' => __('Gambia', 'shopify-checkout-assistant'),
			'GE' => __('Georgia', 'shopify-checkout-assistant'),
			'DE' => __('Germany', 'shopify-checkout-assistant'),
			'GH' => __('Ghana', 'shopify-checkout-assistant'),
			'GI' => __('Gibraltar', 'shopify-checkout-assistant'),
			'GR' => __('Greece', 'shopify-checkout-assistant'),
			'GL' => __('Greenland', 'shopify-checkout-assistant'),
			'GD' => __('Grenada', 'shopify-checkout-assistant'),
			'GP' => __('Guadeloupe', 'shopify-checkout-assistant'),
			'GU' => __('Guam', 'shopify-checkout-assistant'),
			'GT' => __('Guatemala', 'shopify-checkout-assistant'),
			'GG' => __('Guernsey', 'shopify-checkout-assistant'),
			'GN' => __('Guinea', 'shopify-checkout-assistant'),
			'GW' => __('Guinea-Bissau', 'shopify-checkout-assistant'),
			'GY' => __('Guyana', 'shopify-checkout-assistant'),
			'HT' => __('Haiti', 'shopify-checkout-assistant'),
			'HM' => __('Heard Island and McDonald Islands', 'shopify-checkout-assistant'),
			'HN' => __('Honduras', 'shopify-checkout-assistant'),
			'HK' => __('Hong Kong SAR China', 'shopify-checkout-assistant'),
			'HU' => __('Hungary', 'shopify-checkout-assistant'),
			'IS' => __('Iceland', 'shopify-checkout-assistant'),
			'IN' => __('India', 'shopify-checkout-assistant'),
			'ID' => __('Indonesia', 'shopify-checkout-assistant'),
			'IR' => __('Iran', 'shopify-checkout-assistant'),
			'IQ' => __('Iraq', 'shopify-checkout-assistant'),
			'IE' => __('Ireland', 'shopify-checkout-assistant'),
			'IM' => __('Isle of Man', 'shopify-checkout-assistant'),
			'IL' => __('Israel', 'shopify-checkout-assistant'),
			'IT' => __('Italy', 'shopify-checkout-assistant'),
			'JM' => __('Jamaica', 'shopify-checkout-assistant'),
			'JP' => __('Japan', 'shopify-checkout-assistant'),
			'JE' => __('Jersey', 'shopify-checkout-assistant'),
			'JT' => __('Johnston Island', 'shopify-checkout-assistant'),
			'JO' => __('Jordan', 'shopify-checkout-assistant'),
			'KZ' => __('Kazakhstan', 'shopify-checkout-assistant'),
			'KE' => __('Kenya', 'shopify-checkout-assistant'),
			'KI' => __('Kiribati', 'shopify-checkout-assistant'),
			'KW' => __('Kuwait', 'shopify-checkout-assistant'),
			'KG' => __('Kyrgyzstan', 'shopify-checkout-assistant'),
			'LA' => __('Laos', 'shopify-checkout-assistant'),
			'LV' => __('Latvia', 'shopify-checkout-assistant'),
			'LB' => __('Lebanon', 'shopify-checkout-assistant'),
			'LS' => __('Lesotho', 'shopify-checkout-assistant'),
			'LR' => __('Liberia', 'shopify-checkout-assistant'),
			'LY' => __('Libya', 'shopify-checkout-assistant'),
			'LI' => __('Liechtenstein', 'shopify-checkout-assistant'),
			'LT' => __('Lithuania', 'shopify-checkout-assistant'),
			'LU' => __('Luxembourg', 'shopify-checkout-assistant'),
			'MO' => __('Macau SAR China', 'shopify-checkout-assistant'),
			'MK' => __('Macedonia', 'shopify-checkout-assistant'),
			'MG' => __('Madagascar', 'shopify-checkout-assistant'),
			'MW' => __('Malawi', 'shopify-checkout-assistant'),
			'MY' => __('Malaysia', 'shopify-checkout-assistant'),
			'MV' => __('Maldives', 'shopify-checkout-assistant'),
			'ML' => __('Mali', 'shopify-checkout-assistant'),
			'MT' => __('Malta', 'shopify-checkout-assistant'),
			'MH' => __('Marshall Islands', 'shopify-checkout-assistant'),
			'MQ' => __('Martinique', 'shopify-checkout-assistant'),
			'MR' => __('Mauritania', 'shopify-checkout-assistant'),
			'MU' => __('Mauritius', 'shopify-checkout-assistant'),
			'YT' => __('Mayotte', 'shopify-checkout-assistant'),
			'FX' => __('Metropolitan France', 'shopify-checkout-assistant'),
			'MX' => __('Mexico', 'shopify-checkout-assistant'),
			'FM' => __('Micronesia', 'shopify-checkout-assistant'),
			'MI' => __('Midway Islands', 'shopify-checkout-assistant'),
			'MD' => __('Moldova', 'shopify-checkout-assistant'),
			'MC' => __('Monaco', 'shopify-checkout-assistant'),
			'MN' => __('Mongolia', 'shopify-checkout-assistant'),
			'ME' => __('Montenegro', 'shopify-checkout-assistant'),
			'MS' => __('Montserrat', 'shopify-checkout-assistant'),
			'MA' => __('Morocco', 'shopify-checkout-assistant'),
			'MZ' => __('Mozambique', 'shopify-checkout-assistant'),
			'MM' => __('Myanmar [Burma]', 'shopify-checkout-assistant'),
			'NA' => __('Namibia', 'shopify-checkout-assistant'),
			'NR' => __('Nauru', 'shopify-checkout-assistant'),
			'NP' => __('Nepal', 'shopify-checkout-assistant'),
			'NL' => __('Netherlands', 'shopify-checkout-assistant'),
			'AN' => __('Netherlands Antilles', 'shopify-checkout-assistant'),
			'NT' => __('Neutral Zone', 'shopify-checkout-assistant'),
			'NC' => __('New Caledonia', 'shopify-checkout-assistant'),
			'NZ' => __('New Zealand', 'shopify-checkout-assistant'),
			'NI' => __('Nicaragua', 'shopify-checkout-assistant'),
			'NE' => __('Niger', 'shopify-checkout-assistant'),
			'NG' => __('Nigeria', 'shopify-checkout-assistant'),
			'NU' => __('Niue', 'shopify-checkout-assistant'),
			'NF' => __('Norfolk Island', 'shopify-checkout-assistant'),
			'KP' => __('North Korea', 'shopify-checkout-assistant'),
			'VD' => __('North Vietnam', 'shopify-checkout-assistant'),
			'MP' => __('Northern Mariana Islands', 'shopify-checkout-assistant'),
			'NO' => __('Norway', 'shopify-checkout-assistant'),
			'OM' => __('Oman', 'shopify-checkout-assistant'),
			'PC' => __('Pacific Islands Trust Territory', 'shopify-checkout-assistant'),
			'PK' => __('Pakistan', 'shopify-checkout-assistant'),
			'PW' => __('Palau', 'shopify-checkout-assistant'),
			'PS' => __('Palestinian Territories', 'shopify-checkout-assistant'),
			'PA' => __('Panama', 'shopify-checkout-assistant'),
			'PZ' => __('Panama Canal Zone', 'shopify-checkout-assistant'),
			'PG' => __('Papua New Guinea', 'shopify-checkout-assistant'),
			'PY' => __('Paraguay', 'shopify-checkout-assistant'),
			'YD' => __('People\'s Democratic Republic of Yemen', 'shopify-checkout-assistant'),
			'PE' => __('Peru', 'shopify-checkout-assistant'),
			'PH' => __('Philippines', 'shopify-checkout-assistant'),
			'PN' => __('Pitcairn Islands', 'shopify-checkout-assistant'),
			'PL' => __('Poland', 'shopify-checkout-assistant'),
			'PT' => __('Portugal', 'shopify-checkout-assistant'),
			'PR' => __('Puerto Rico', 'shopify-checkout-assistant'),
			'QA' => __('Qatar', 'shopify-checkout-assistant'),
			'RO' => __('Romania', 'shopify-checkout-assistant'),
			'RU' => __('Russia', 'shopify-checkout-assistant'),
			'RW' => __('Rwanda', 'shopify-checkout-assistant'),
			'BL' => __('Saint Barthélemy', 'shopify-checkout-assistant'),
			'SH' => __('Saint Helena', 'shopify-checkout-assistant'),
			'KN' => __('Saint Kitts and Nevis', 'shopify-checkout-assistant'),
			'LC' => __('Saint Lucia', 'shopify-checkout-assistant'),
			'MF' => __('Saint Martin', 'shopify-checkout-assistant'),
			'PM' => __('Saint Pierre and Miquelon', 'shopify-checkout-assistant'),
			'VC' => __('Saint Vincent and the Grenadines', 'shopify-checkout-assistant'),
			'WS' => __('Samoa', 'shopify-checkout-assistant'),
			'SM' => __('San Marino', 'shopify-checkout-assistant'),
			'SA' => __('Saudi Arabia', 'shopify-checkout-assistant'),
			'SN' => __('Senegal', 'shopify-checkout-assistant'),
			'RS' => __('Serbia', 'shopify-checkout-assistant'),
			'CS' => __('Serbia and Montenegro', 'shopify-checkout-assistant'),
			'SC' => __('Seychelles', 'shopify-checkout-assistant'),
			'SL' => __('Sierra Leone', 'shopify-checkout-assistant'),
			'SG' => __('Singapore', 'shopify-checkout-assistant'),
			'SK' => __('Slovakia', 'shopify-checkout-assistant'),
			'SI' => __('Slovenia', 'shopify-checkout-assistant'),
			'SB' => __('Solomon Islands', 'shopify-checkout-assistant'),
			'SO' => __('Somalia', 'shopify-checkout-assistant'),
			'ZA' => __('South Africa', 'shopify-checkout-assistant'),
			'GS' => __('South Georgia and the South Sandwich Islands', 'shopify-checkout-assistant'),
			'KR' => __('South Korea', 'shopify-checkout-assistant'),
			'ES' => __('Spain', 'shopify-checkout-assistant'),
			'LK' => __('Sri Lanka', 'shopify-checkout-assistant'),
			'SD' => __('Sudan', 'shopify-checkout-assistant'),
			'SR' => __('Suriname', 'shopify-checkout-assistant'),
			'SJ' => __('Svalbard and Jan Mayen', 'shopify-checkout-assistant'),
			'SZ' => __('Swaziland', 'shopify-checkout-assistant'),
			'SE' => __('Sweden', 'shopify-checkout-assistant'),
			'CH' => __('Switzerland', 'shopify-checkout-assistant'),
			'SY' => __('Syria', 'shopify-checkout-assistant'),
			'ST' => __('São Tomé and Príncipe', 'shopify-checkout-assistant'),
			'TW' => __('Taiwan', 'shopify-checkout-assistant'),
			'TJ' => __('Tajikistan', 'shopify-checkout-assistant'),
			'TZ' => __('Tanzania', 'shopify-checkout-assistant'),
			'TH' => __('Thailand', 'shopify-checkout-assistant'),
			'TL' => __('Timor-Leste', 'shopify-checkout-assistant'),
			'TG' => __('Togo', 'shopify-checkout-assistant'),
			'TK' => __('Tokelau', 'shopify-checkout-assistant'),
			'TO' => __('Tonga', 'shopify-checkout-assistant'),
			'TT' => __('Trinidad and Tobago', 'shopify-checkout-assistant'),
			'TN' => __('Tunisia', 'shopify-checkout-assistant'),
			'TR' => __('Turkey', 'shopify-checkout-assistant'),
			'TM' => __('Turkmenistan', 'shopify-checkout-assistant'),
			'TC' => __('Turks and Caicos Islands', 'shopify-checkout-assistant'),
			'TV' => __('Tuvalu', 'shopify-checkout-assistant'),
			'UM' => __('U.S. Minor Outlying Islands', 'shopify-checkout-assistant'),
			'PU' => __('U.S. Miscellaneous Pacific Islands', 'shopify-checkout-assistant'),
			'VI' => __('U.S. Virgin Islands', 'shopify-checkout-assistant'),
			'UG' => __('Uganda', 'shopify-checkout-assistant'),
			'UA' => __('Ukraine', 'shopify-checkout-assistant'),
			'SU' => __('Union of Soviet Socialist Republics', 'shopify-checkout-assistant'),
			'AE' => __('United Arab Emirates', 'shopify-checkout-assistant'),
			'GB' => __('United Kingdom', 'shopify-checkout-assistant'),
			'US' => __('United States', 'shopify-checkout-assistant'),
			'ZZ' => __('Unknown or Invalid Region', 'shopify-checkout-assistant'),
			'UY' => __('Uruguay', 'shopify-checkout-assistant'),
			'UZ' => __('Uzbekistan', 'shopify-checkout-assistant'),
			'VU' => __('Vanuatu', 'shopify-checkout-assistant'),
			'VA' => __('Vatican City', 'shopify-checkout-assistant'),
			'VE' => __('Venezuela', 'shopify-checkout-assistant'),
			'VN' => __('Vietnam', 'shopify-checkout-assistant'),
			'WK' => __('Wake Island', 'shopify-checkout-assistant'),
			'WF' => __('Wallis and Futuna', 'shopify-checkout-assistant'),
			'EH' => __('Western Sahara', 'shopify-checkout-assistant'),
			'YE' => __('Yemen', 'shopify-checkout-assistant'),
			'ZM' => __('Zambia', 'shopify-checkout-assistant'),
			'ZW' => __('Zimbabwe', 'shopify-checkout-assistant'),
			'AX' => __('Åland Islands', 'shopify-checkout-assistant'),
		);
	}

	/**
	 * Get country name from country code
	 * 
	 * @since 1.0.0
	 * @return string
	 */
	public static function get_country_name($country_code) {
		$countries = self::get_countries();
		return isset($countries[$country_code]) ? $countries[$country_code] : '';
	}

	/**
	 * Sanitize data
	 * 
	 * @since 1.0.0
	 * @return mixed
	 */
	public static function sanitize_data($data) {
		if (is_array($data)) {
			return array_map(array('self', 'sanitize_data'), $data);
		}

		return is_scalar($data) ? sanitize_text_field($data) : $data;
	}

	/**
	 * Get Settings
	 * 
	 * @since 1.0.0
	 * @return array
	 */
	public static function get_settings() {
		$settings = wp_parse_args(get_option('utm_event_tracker_settings'), array(
			'cookie_duration' => 30,
			'webhook_url' => '',
			'ipinfo_token' => '',
		));

		if (absint($settings['cookie_duration']) === 0) {
			$settings['cookie_duration'] = 30;
		}

		$settings['cookie_duration'] = absint($settings['cookie_duration']);

		return apply_filters('utm_event_tracker_and_analytics/get_settings', $settings);
	}

	/**
	 * Get value from settings
	 * 
	 * @since 1.0.0
	 * @param $key string 
	 * @param $default get default value if not avaialble
	 * @return mixed
	 */
	public static function get_settings_key($key, $default = null) {
		$settings = self::get_settings();
		return !empty($settings[$key]) ? $settings[$key] : $default;
	}

	/**
	 * Get all supported parameters
	 * 
	 * @since 1.0.0
	 * @return array
	 */
	public static function get_utm_parameters() {
		return array(
			'utm_campaign' => __('UTM Campaign', 'utm-event-tracker'),
			'utm_medium' => __('UTM Medium', 'utm-event-tracker'),
			'utm_source'  => __('UTM Source', 'utm-event-tracker'),
			'utm_term' => __('UTM Terms', 'utm-event-tracker'),
			'utm_content' => __('UTM Content', 'utm-event-tracker'),
			'fbclid' => __('Facebook ads Click ID', 'utm-event-tracker'),
			'gclid' => __('Google ads Click ID', 'utm-event-tracker'),
		);
	}

	/**
	 * Get all available parameters
	 * 
	 * @since 1.0.0
	 * @return array
	 */
	public static function get_all_parameters() {
		return array_merge(self::get_utm_parameters(), array(
			'ip_address' => __('IP Address', 'utm-event-tracker'),
			'landing_page' => __('Landing Page', 'utm-event-tracker'),
		));
	}

	/**
	 * Check if UTM parameter available
	 * 
	 * @since 1.0.0
	 * @return boolean
	 */
	public static function is_utm_parameter_available() {
		$parameters = array_keys(self::get_utm_parameters());

		$is_exists = false;
		while ($key = current($parameters)) {
			if (isset($_GET[$key])) {
				$is_exists = true;
			}

			next($parameters);
		}

		return $is_exists;
	}

	/**
	 * Get date of selected timezone of settings
	 * 
	 * @since 1.0.0
	 * @param string mysql date
	 * @param boolean timestamp
	 * @param string date format
	 * @return string|integer
	 */
	public static function get_date($date, $timestamp = false, $format = 'Y-m-d H:i:s') {
		$date = wp_date($format, strtotime($date));
		if ($timestamp) {
			return strtotime($date);
		}

		return $date;
	}
}
