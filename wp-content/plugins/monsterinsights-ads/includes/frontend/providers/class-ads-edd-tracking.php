<?php

/**
 * Class MonsterInsights_Ads_EDD
 */
class MonsterInsights_Ads_EDD extends MonsterInsights_Ads_eCommerce_Tracking_Integration {

	/**
	 * The order id.
	 *
	 * @var int|false
	 */
	public $order_id;

	/**
	 * The EDD order that is being tracked.
	 *
	 * @var EDD_Payment|false
	 */
	public $order;

	/**
	 * Checks if we are on the thank you page and the conversion event should be sent.
	 *
	 * @return bool
	 */
	public function conversion_checks() {

		if ( function_exists( 'edd_is_success_page' ) && edd_is_success_page() ) {
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
			$session = edd_get_purchase_session();
			if ( ! empty( $session['purchase_key'] ) ) {
				$payment_key = $session['purchase_key'];
				$order_id    = edd_get_purchase_id_by_key( $payment_key );
			}
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
	 * @return bool|EDD_Payment
	 */
	public function get_order() {
		if ( ! isset( $this->order ) ) {
			if ( $this->get_order_number() ) {
				$this->order = edd_get_payment( $this->get_order_number() );
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
		if ( $this->get_order_number() ) {
			return edd_get_payment_currency_code( $this->get_order_number() );
		}

		return false;
	}

	/**
	 * If the order is properly loaded grabs the order total.
	 *
	 * @return false|float
	 */
	public function get_order_total() {
		if ( $this->get_order_number() ) {
			return edd_get_payment_amount( $this->get_order_number() );
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
			$tracked = $this->get_order()->get_meta('monsterinsights_ads_conversion_tracked');
		}

		return ! empty( $tracked ) && 'yes' === $tracked;
	}

	/**
	 * Add a meta to the order to mark it as tracked to avoid tracking it 2 times.
	 *
	 * @return bool|void
	 */
	public function mark_order_tracked() {
		if ( $this->get_order_number() ) {
			$this->get_order()->update_meta('monsterinsights_ads_conversion_tracked', 'yes' );
		}
	}

}
