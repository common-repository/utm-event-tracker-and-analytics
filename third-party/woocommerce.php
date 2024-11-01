<?php

namespace UTM_Event_Tracker;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * WooCommerce class for hooks
 */
class WooCommerce {

	/**
	 * Constructor
	 * 
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action('add_meta_boxes', array($this, 'order_meta_boxes'));
		add_action('woocommerce_thankyou', array($this, 'add_utm_vars'));
		add_action('woocommerce_add_to_cart', array($this, 'add_to_cart'), 10, 4);
	}

	/**
	 * Add UTM vars at order
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	public function add_utm_vars($order_id) {
		if (!Session::is_available() || !$order_id) {
			return;
		}

		$order = wc_get_order($order_id);
		if (!$order) {
			return;
		}

		$session = Session::get_current_session();

		update_post_meta($order_id, 'utm_event_tracker_session', $session->get_id());

		$event_added = get_post_meta($order_id, 'utm_event_tracker_event_added', true) === 'yes';
		if ($event_added) {
			return;
		}

		$session->add_event(array(
			'type' => 'woocommerce_checkout',
			'currency' => $order->get_currency(),
			'amount' => $order->get_total(),
			'created_on' => gmdate('Y-m-d H:i:s'),
			'meta_data' => array(
				'order_id' => $order_id
			)
		));

		update_post_meta($order_id, 'utm_event_tracker_event_added', 'yes');
	}

	/**
	 * Add event after adding product to cart
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	public function add_to_cart($cart_item_key, $product_id, $quantity, $variation_id) {
		$cart_items = WC()->cart->get_cart();
		if (!isset($cart_items[$cart_item_key])) {
			return;
		}

		$current_item = $cart_items[$cart_item_key];

		utm_event_tracker_add_event('woocommerce_add_to_cart', array(
			'currency' => get_woocommerce_currency(),
			'amount' => $current_item['data']->get_price(),
			'meta_data' => array(
				'product_id' => $product_id,
				'variation_id' => $variation_id
			)
		));
	}

	/**
	 * Register meta boxes for order
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	public function order_meta_boxes() {
		add_meta_box(
			'utm-event-tracker-order-metabox',
			__('UTM Event Tracker', 'utm-event-tracker'),
			array($this, 'order_metabox'),
			'woocommerce_page_wc-orders',
			'side',
			'high'
		);
	}

	/**
	 * Show UTM session data at order page
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	public function order_metabox($order) {
		$session_id = get_post_meta($order->ID, 'utm_event_tracker_session', true);
		$session = Session::get_by_id($session_id);

		echo '<div class="order-attribution-metabox">';

		echo '<h4>' . esc_html__('UTM Campaign', 'utm-event-tracker') . '</h4>';
		echo '<span class="utm-event-tracker-word-break">' . esc_html($session->get('utm_campaign', 'N/A')) . '</span>';

		echo '<h4>' . esc_html__('UTM Source', 'utm-event-tracker') . '</h4>';
		echo '<span class="utm-event-tracker-word-break">' . esc_html($session->get('utm_source', 'N/A')) . '</span>';

		echo '<h4>' . esc_html__('UTM Medium', 'utm-event-tracker') . '</h4>';
		echo '<span class="utm-event-tracker-word-break">' . esc_html($session->get('utm_medium', 'N/A')) . '</span>';

		echo '<h4>' . esc_html__('UTM Content', 'utm-event-tracker') . '</h4>';
		echo '<span class="utm-event-tracker-word-break">' . esc_html($session->get('utm_content', 'N/A')) . '</span>';

		echo '<h4>' . esc_html__('UTM Term', 'utm-event-tracker') . '</h4>';
		echo '<span class="utm-event-tracker-word-break">' . esc_html($session->get('utm_term', 'N/A')) . '</span>';

		echo '<h4>' . esc_html__('Google Click ID', 'utm-event-tracker') . '</h4>';
		echo '<span class="utm-event-tracker-word-break">' . esc_html($session->get('gclid', 'N/A')) . '</span>';

		echo '<h4>' . esc_html__('Facebook Click ID', 'utm-event-tracker') . '</h4>';
		echo '<span class="utm-event-tracker-word-break">' . esc_html($session->get('fbclid', 'N/A')) . '</span>';

		echo '<h4>' . esc_html__('City', 'utm-event-tracker') . '</h4>';
		echo '<span>' . esc_html($session->get('city', 'N/A')) . '</span>';

		echo '<h4>' . esc_html__('Province/Region', 'utm-event-tracker') . '</h4>';
		echo '<span class="utm-event-tracker-word-break">' . esc_html($session->get('region', 'N/A')) . '</span>';

		echo '<h4>' . esc_html__('Country', 'utm-event-tracker') . '</h4>';
		echo '<span class="utm-event-tracker-word-break">' . esc_html(Utils::get_country_name($session->get('country'))) . '</span>';

		echo '<h4>' . esc_html__('IP Address', 'utm-event-tracker') . '</h4>';

		if ($session->is_exists()) {
			echo '<span>' . esc_html($session->get('ip_address', 'N/A')) . '</span>';
		} else {
			echo '<span>N/A</span>';
		}

		echo '</div>';

		echo '<style>.utm-event-tracker-word-break {word-wrap: break-word}</style>';
	}
}

new WooCommerce();
