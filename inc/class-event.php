<?php

namespace UTM_Event_Tracker;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Event class
 * 
 * @since 1.0.0
 */
class Event {

	/**
	 * ID of event
	 * 
	 * @since 1.0.0
	 * @var integer
	 */
	public $id = 0;

	/**
	 * Session ID of event
	 * 
	 * @since 1.0.0
	 * @var integer
	 */
	public $session_id  = 0;

	/**
	 * Event type
	 * 
	 * @since 1.0.0
	 * @var null|string
	 */
	public $type = null;

	/**
	 * Currency of amount
	 * 
	 * @since 1.0.0
	 * @var null|string
	 */
	public $currency = null;

	/**
	 * Hold amount of event
	 * 
	 * @since 1.0.0
	 * @var float
	 */
	public $amount = 0.00;

	/**
	 * Hold extra data of event
	 * 
	 * @since 1.0.0
	 * @var array
	 */
	public $meta_data = [];

	/**
	 * Hold created datetime of event
	 * 
	 * @since 1.0.0
	 * @var string
	 */
	public $created_on = '';

	/**
	 * Hold description of this event
	 * 
	 * @since 1.0.0
	 * @var string
	 */
	public $description = '';

	/**
	 * Constructor of event
	 * 
	 * @since 1.0.0
	 */
	public function __construct($event_data = null) {
		$this->created_on = current_time('mysql');
		if (!is_object($event_data)) {
			return;
		}

		$event_data = (array) $event_data;
		$this->meta_data = !empty($event_data['meta_data']) ? json_decode($event_data['meta_data'], true) : array();

		unset($event_data['meta_data']);

		foreach ($event_data as $key => $value) {
			$key = sanitize_key($key);
			if (empty($key)) {
				continue;
			}

			$this->$key = $value;
		}

		$this->id = absint($this->id);
		$this->set_description();
	}

	/**
	 * Add data into meta data
	 * 
	 * @since 1.0.0
	 */
	public function __set($key, $value) {
		$this->meta_data[$key] = $value;
	}

	/**
	 * Get value from meta_data
	 * 
	 * @since 1.0.0
	 * @return mixed
	 */
	public function __get($key) {
		return isset($this->meta_data[$key]) ? $this->meta_data[$key] : null;
	}

	/**
	 * Check the key exists within meta data
	 * 
	 * @since 1.0.0
	 * @return boolean
	 */
	public function __isset($key) {
		return isset($this->meta_data[$key]);
	}

	/**
	 * Set description of this event
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	public function set_description() {
		$description = __('Unknown event', 'utm-event-tracker');

		$meta_data = $this->meta_data;
		$event_timestamp = Utils::get_date($this->created_on, true);

		$event_date = sprintf(
			/* translators: %s for date of event */
			__('Date: %s', 'utm-event-tracker'),
			gmdate(get_option('date_format') . ' ' . get_option('time_format'), $event_timestamp)
		);

		if (!empty($meta_data['form_id'])) {
			$form_events = apply_filters('utm_event_tracker/form_submit_plugins_name', array(
				'gravity_form_submission' => 'Gravity Form',
				'ninja_form_submit' => 'Ninja Form',
				'contact_form_7_submit' => 'Contact Form 7',
			));

			/* translators: %1$d for form id, %2$s plugin name of form, %3$s for date */
			$description = __(
				'Form #%1$d (%2$s) has been submitted on %3$s.',
				'utm-event-tracker'
			);

			$plugin_name = 'Unknown';
			if (in_array($this->type, array_keys($form_events))) {
				$plugin_name = $form_events[$this->type];
			}

			$description = sprintf($description, absint($meta_data['form_id']), $plugin_name, gmdate(get_option('date_format') . ' ' . get_option('time_format'), $event_timestamp));
		}

		if ('woocommerce_add_to_cart' === $this->type) {
			$product_id = $this->product_id;
			$variation_id = 0;

			if (!empty($this->variation_id) && absint($this->variation_id) > 0) {
				$variation_id = $this->variation_id;
			}

			$descriptions[] = __('Added to cart:', 'utm-event-tracker');

			if (class_exists('WooCommerce', false)) {
				$product = wc_get_product($product_id);

				$descriptions[] = sprintf(
					/* translators: %s for product name with link */
					__('Product: %s', 'utm-event-tracker'),
					'<a target="_blank" href="' . $product->get_permalink() . '">' . $product->get_name() . '</a>'
				);

				if ($variation_id > 0) {
					$descriptions[] = sprintf(
						/* translators: %d variation id of product */
						__('Variation ID: %d', 'utm-event-tracker'),
						$variation_id
					);
				}

				$descriptions[] = sprintf(
					/* translators: %s for product cost */
					__('Amount: %s', 'utm-event-tracker'),
					number_format($this->amount, 2)
				);
			} else {
				$descriptions[] = sprintf(
					/* translators: %d for product ID */
					__('Product ID: %d', 'utm-event-tracker'),
					$product_id
				);

				if ($variation_id > 0) {
					$descriptions[] = sprintf(
						/* translators: %d for product variation ID */
						__('Variation ID: %d', 'utm-event-tracker'),
						$variation_id
					);
				}

				$descriptions[] = sprintf(
					/* translators: %s for product cost */
					__('Amount: %s', 'utm-event-tracker'),
					number_format($this->amount, 2)
				);
			}

			$descriptions[] = $event_date;

			$description = implode('<br>', $descriptions);
		}

		if ('woocommerce_checkout' === $this->type) {
			$descriptions[] = __('Order Placed:', 'utm-event-tracker');
			$descriptions[] = sprintf(
				/* translators: %s for product cost */
				__('Amount: %s', 'utm-event-tracker'),
				number_format($this->amount, 2)
			);

			$order_id = $this->order_id;

			if (absint($order_id) > 0) {
				if (class_exists('WooCommerce', false)) {
					$order = wc_get_order($order_id);

					if ($order) {
						$order_permalink = add_query_arg(array(
							'page' => 'wc-orders',
							'action' => 'edit',
							'id' => $order_id,
						), admin_url('admin.php'));

						$descriptions[] = sprintf(
							/* translators: %s order ID */
							__('Order ID: %s', 'utm-event-tracker'),
							'<a target="_blank" href="' . esc_url($order_permalink) . '">' . $order_id . '</a>'
						);
					}
				} else {
					$descriptions[] = sprintf(
						/* translators: %s order ID */
						__('Order ID: %d', 'utm-event-tracker'),
						$order_id
					);
				}
			}

			$descriptions[] = $event_date;

			$description = implode('<br>', $descriptions);
		}

		$this->description = apply_filters('utm_event_tracker/event_description', $description, $this->type, $meta_data, $this);
	}

	/**
	 * Get description of this event
	 * 
	 * @since 1.0.0
	 * @return string
	 */
	public function get_description() {
		return $this->description;
	}
}