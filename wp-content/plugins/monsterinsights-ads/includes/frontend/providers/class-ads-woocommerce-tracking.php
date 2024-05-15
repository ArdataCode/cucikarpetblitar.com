<?php

/**
 * Class MonsterInsights_Ads_WooCoomerce
 */
class MonsterInsights_Ads_WooCoomerce extends MonsterInsights_Ads_eCommerce_Tracking_Integration {

	/**
	 * The order id.
	 *
	 * @var int|false
	 */
	public $order_id;

	/**
	 * The WooCommerce order that is being tracked.
	 *
	 * @var WC_Order|false
	 */
	public $order;

	/**
	 * Checks if we are on the thank you page and the conversion event should be sent.
	 *
	 * @return bool
	 */
	public function conversion_checks() {

		if ( function_exists( 'is_order_received_page' ) && is_order_received_page() ) {
			return true;
		}

		return false;

	}

	/**
	 * Attempt to grab the order number from the current query.
	 * Only works if called on the confirmation page - the conversion checks are passed.
	 *
	 * @return false|int
	 */
	public function get_order_number() {

		if ( ! isset( $this->order_id ) ) {
			$order_id = absint( get_query_var( 'order-received' ) );
			if ( empty( $order_id ) || 0 === $order_id ) {
				$this->order_id = false;
			} else {
				$this->order_id = $order_id;
			}
		}

		return $this->order_id;

	}

	/**
	 * Grab the order using the order number.
	 *
	 * @return bool|WC_Order|WC_Order_Refund
	 */
	public function get_order() {
		if ( ! isset( $this->order ) ) {
			if ( $this->get_order_number() ) {
				$this->order = wc_get_order( $this->get_order_number() );
			} else {
				$this->order = false;
			}
		}

		return $this->order;
	}

	/**
	 * Grab the order currency from the current order.
	 *
	 * @return false|string
	 */
	public function get_order_currency() {
		if ( $this->get_order() ) {
			return $this->get_order()->get_currency();
		}

		return false;
	}

	/**
	 * If the order is properly loaded grabs the order total.
	 *
	 * @return false|float
	 */
	public function get_order_total() {
		if ( $this->get_order() ) {
			return $this->get_order()->get_total();
		}

		return false;
	}

	/**
	 * Check if the order has already been tracked.
	 *
	 * @return bool
	 */
	public function already_tracked() {
		if ( $this->get_order() ) {
			$tracked = $this->get_order()->get_meta( 'monsterinsights_ads_conversion_tracked' );
		}

		return ! empty( $tracked ) && 'yes' === $tracked;
	}

	/**
	 * Add a meta to the order to mark it as tracked to avoid tracking it 2 times.
	 *
	 * @return bool|void
	 */
	public function mark_order_tracked() {
		if ( $this->get_order() ) {
			$this->get_order()->add_meta_data( 'monsterinsights_ads_conversion_tracked', 'yes', true );
			$this->get_order()->save_meta_data();
		}
	}

}
