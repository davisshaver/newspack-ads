<?php
/**
 * Newspack Ads Bidder - OpenX
 *
 * The required Prebid.js modules for OpenX are included in the Newspack
 * Ads plugin. For additional partners you must recompile Prebid.js and replace
 * the `newspack-ads-prebid` enqueued script.
 *
 * See Newspack_Ads_Bidding::enqueue_scripts() and 
 * `newspack-ads/src/prebid/index.js` for additional reference.
 *
 * More information on Prebid.js: https://github.com/prebid/Prebid.js/
 *
 * @package Newspack
 */

defined( 'ABSPATH' ) || exit;

/**
 * Newspack Ads Bidder OpenX Class.
 */
class Newspack_Ads_Bidder_OpenX {

	/**
	 * Register bidder and its hooks.
	 */
	public static function init() {

		// Require environment variable due to its experimental nature.
		if ( ! defined( 'NEWSPACK_ADS_EXPERIMENTAL_BIDDERS' ) ) {
			return;
		}

		newspack_register_ads_bidder(
			'openx',
			[
				'name'       => 'OpenX',
				'active_key' => 'openx_platform',
				'settings'   => [
					[
						'description' => __( 'OpenX Platform ID', 'newspack-ads' ),
						'help'        => __( 'Platform id provided by your OpenX representative. E.g.: 555not5a-real-plat-form-id0123456789', 'newspack-ads' ),
						'key'         => 'openx_platform',
						'type'        => 'string',
					],
				],
			]
		);
		add_filter( 'newspack_ads_openx_ad_unit_bid', [ __CLASS__, 'set_openx_ad_unit_bid' ], 10, 4 );
	}

	/**
	 * Set OpenX bid configuration for an ad unit.
	 *
	 * Assumes bidder configuration exists, e.g. `openx_platform`, since a bid
	 * shouldn't be available otherwise.
	 *
	 * @param array|null $bid                 The bid configuration.
	 * @param array      $bidder              Bidder configuration.
	 * @param string     $bidder_placement_id The bidder placement ID for this ad unit.
	 * @param array      $data                Ad unit data.
	 *
	 * @return array The bid configuration.
	 */
	public static function set_openx_ad_unit_bid( $bid, $bidder, $bidder_placement_id, $data ) {
		return [
			'bidder' => 'openx',
			'params' => [
				'platform' => $bidder['data']['openx_platform'],
				'unit'     => $bidder_placement_id,
			],
		];
	}
}
Newspack_Ads_Bidder_OpenX::init();