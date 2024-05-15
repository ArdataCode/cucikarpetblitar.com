<?php

class MonsterInsights_Ads_eCommerce_Tracking_Integration {

	private $conversion_id;

	private $conversion_label;

	public function __construct() {
		$this->hooks();
	}

	public function hooks() {
		add_action( 'monsterinsights_frontend_tracking_gtag_after_pageview', array( $this, 'load_conversion_code' ) );
	}

	public function load_conversion_code() {
		// Filter for custom confirmation page implementations.
		if ( apply_filters( 'monsterinsights_ads_is_conversion_page', $this->conversion_checks(), $this ) ) {
			$this->pixel_code();
		}
	}

	/**
	 * This should be replaced in each integration to ensure we
	 * only load the conversion code on the right page.
	 *
	 * @return false
	 */
	public function conversion_checks() {
		return false;
	}

	public function pixel_code() {
		// Don't track the same order 2 times.
		if ( $this->already_tracked() || $this->get_conversion_label() === '' ) {
			return;
		}
		// wp_json_encode not used due to supported WP version.
		echo stripslashes( "\n__gtagTracker( 'event', 'conversion', " . json_encode( $this->get_conversion_data() ) . " );\n" );

		$this->mark_order_tracked();
	}

	public function get_conversion_data() {

		$conversion_data = array(
			'send_to' => $this->get_conversion_id_and_label(),
		);
		if ( false !== $this->get_order_total() ) {
			$conversion_data['value'] = $this->get_order_total();
		}
		if ( false !== $this->get_order_number() ) {
			$conversion_data['transaction_id'] = $this->get_order_number();
		}
		if ( false !== $this->get_order_currency() ) {
			$conversion_data['currency'] = $this->get_order_currency();
		}

		return apply_filters( 'monsterinsights_ads_get_conversion_data', $conversion_data );

	}

	public function get_conversion_id_and_label() {
		return $this->get_conversion_id() . '/' . $this->get_conversion_label();
	}

	public function get_conversion_id() {
		if ( ! isset( $this->conversion_id ) ) {
			$conversion_id = monsterinsights_get_option( 'gtag_ads_conversion_id' );
			if ( ! empty( $conversion_id ) && ! monsterinsights_is_valid_gt( $conversion_id ) ) {
				// Make sure we always have the AW- prefix.
				$conversion_id = str_replace( 'AW-', '', $conversion_id );
				$conversion_id = 'AW-' . $conversion_id;
			}
			$this->conversion_id = $conversion_id;
		}

		return $this->conversion_id;
	}

	public function get_conversion_label() {
		if ( ! isset( $this->conversion_label ) ) {
			$this->conversion_label = monsterinsights_get_option( 'gtag_ads_conversion_label' );
		}

		return $this->conversion_label;
	}

	public function get_order_total() {
		return false;
	}

	public function get_order_currency() {
		return false;
	}

	public function get_order_number() {
		return false;
	}

	public function already_tracked() {
		return true;
	}

	public function mark_order_tracked() {
		return true;
	}
}
