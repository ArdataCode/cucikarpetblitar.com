<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add Ads Conversion ID to gtag.
 *
 * @return string
 * @see /plugins/monsterinsights/includes/frontend/tracking/class-tracking-gtag.php Line: 255
 *
 * @since 17.5.0
 *
 * @uses Hook: monsterinsights_frontend_tracking_gtag_after_pageview
 */
function monsterinsights_add_conversion_id_to_gtag_tracking() {
	$aw_id = esc_attr( monsterinsights_get_option( 'gtag_ads_conversion_id' ) );

	if ( ! empty( $aw_id ) ) {
		echo "__gtagTracker( 'config', '" . $aw_id . "' );";
	}
}

add_action( 'monsterinsights_frontend_tracking_gtag_after_pageview', 'monsterinsights_add_conversion_id_to_gtag_tracking' );
