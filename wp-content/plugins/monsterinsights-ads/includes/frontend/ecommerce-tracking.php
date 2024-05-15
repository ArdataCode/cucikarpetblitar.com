<?php

/**
 * Class MonsterInsights_Ads_eCommerce_Tracking
 */
class MonsterInsights_Ads_eCommerce_Tracking {

	/**
	 * MonsterInsights_Ads_eCommerce_Tracking constructor.
	 */
	public function __construct() {

		$this->load_files();
		$this->init();

	}

	/**
	 * Load the eCommerce tracking classes.
	 */
	public function load_files() {
		require_once 'providers/class-ads-ecommerce-tracking.php';
		require_once 'providers/class-ads-woocommerce-tracking.php';
		require_once 'providers/class-ads-edd-tracking.php';
		require_once 'providers/class-ads-memberpress-tracking.php';
	}

	/**
	 * Do the checks and load the classes as needed.
	 */
	public function init() {

		if ( class_exists( 'WooCommerce' ) && apply_filters( 'monsterinsights_ads_track_conversion_woocommerce', true ) ) {
			new MonsterInsights_Ads_WooCoomerce();
		}

		if ( class_exists( 'Easy_Digital_Downloads' ) && apply_filters( 'monsterinsights_ads_track_conversion_edd', true ) ) {
			new MonsterInsights_Ads_EDD();
		}

		if ( defined( 'MEPR_VERSION' ) && version_compare( MEPR_VERSION, '1.3.43', '>' ) && apply_filters( 'monsterinsights_ads_track_conversion_memberpress', true ) ) {
			new MonsterInsights_Ads_MemberPress();
		}

	}

}

new MonsterInsights_Ads_eCommerce_Tracking();
