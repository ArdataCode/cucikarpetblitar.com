<?php

/**
 * Class MonsterInsights_Ads_MemberPress
 */
class MonsterInsights_Ads_MemberPress extends MonsterInsights_Ads_eCommerce_Tracking_Integration {

	/**
	 * The order id.
	 *
	 * @var int|false
	 */
	public $order_id;

	/**
	 * The MemberPress order that is being tracked.
	 *
	 * @var MeprTransaction|false
	 */
	public $order;

	/**
	 * Checks if we are on the thank you page and the conversion event should be sent.
	 *
	 * @return bool
	 */
	public function conversion_checks() {

		$conversion = false;
		if ( isset( $_GET['trans_num'] ) ) {
			$conversion = true;
		} else if ( isset( $_GET['action'] ) && $_GET['action'] === 'gifts' && isset( $_GET['txn'] ) ) {
			$txn = new MeprTransaction( (int) $_GET['txn'] );
			if ( $txn->id ) {
				$_REQUEST['trans_num'] = $txn->trans_num;
				$conversion            = true;
			}
		}

		return $conversion;

	}

	/**
	 * Attempt to grab the order number from the current query.
	 * Only works if called on the confirmation page - the conversion checks are passed.
	 *
	 * @return false|int
	 */
	public function get_order_number() {

		if ( ! isset( $this->order_id ) ) {
			if ( empty( $_REQUEST['trans_num'] ) ) {
				$this->order    = false;
				$this->order_id = false;
			} else if ( empty( $_REQUEST['subscr_id'] ) ) {
				$txn  = new MeprTransaction();
				$data = MeprTransaction::get_one_by_trans_num( sanitize_key( $_REQUEST['trans_num'] ) );
				$txn->load_data( $data );
				if ( ! $txn->id || ! $txn->product_id ) {
					$this->order_id = false;
				}
				$this->order    = $txn;
				$this->order_id = 'charge_' . $txn->id;
			} else {
				$sub = MeprSubscription::get_one_by_subscr_id( sanitize_key( $_REQUEST['subscr_id'] ) );
				if ( $sub === false || ! $sub->id || ! $sub->product_id ) {
					$this->order_id = false;
				}
				$this->order    = $sub;
				$this->order_id = 'sub_' . $sub->id;
			}
		}

		return $this->order_id;

	}

	/**
	 * Grab the order using the order number.
	 *
	 * @return bool|MeprTransaction
	 */
	public function get_order() {
		if ( ! isset( $this->order ) ) {
			if ( $this->get_order_number() ) {
				// Order is set in get_order_number()
				//$this->order = new MeprTransaction( $this->get_order_number()
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
		$mepr_options = MeprOptions::fetch();

		return $mepr_options->currency_code;
	}

	/**
	 * If the order is properly loaded grabs the order total.
	 *
	 * @return false|float
	 */
	public function get_order_total() {
		if ( $this->get_order() ) {
			$order = $this->get_order();

			if ( $order instanceof MeprSubscription ) {
				return $order->trial ? $order->trial_total : $order->total;
			} else {
				return $order->total;
			}
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
			$this->get_order()->get_meta( 'monsterinsights_ads_conversion_tracked', true );
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
			$this->get_order()->update_meta( 'monsterinsights_ads_conversion_tracked', 'yes' );
		}
	}

}
