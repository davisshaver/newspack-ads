<?php
/**
 * Gutenberg Blocks setup
 *
 * Adapted from https://github.com/10up/publisher-media-kit/.
 *
 * @package Newspack
 */

namespace Newspack_Ads;

/**
 * Newspack Ads Media Kit Page Class.
 */
final class Media_Kit_Block_Patterns {
	/**
	 * Initialize settings.
	 */
	public static function init() {
		add_action( 'init', [ __CLASS__, 'register_block_patterns_and_categories' ] );
		add_action( 'upgrader_process_complete', [ __CLASS__, 'clear_pattern_cache' ] );
	}

	/**
	 * Clear the cached pattern content.
	 */
	public static function clear_pattern_cache() {
		delete_transient( 'newspack_ads_media_kit_patterns' );
	}

	/**
	 * Register block patterns.
	 */
	public static function register_block_patterns_and_categories() {
		// Register block pattern category for Publisher Media Kit.
		register_block_pattern_category(
			'newspack-ads',
			array( 'label' => __( 'Newspack Media Kit', 'newspack-ads' ) )
		);

		$pattern_content = self::get_cached_pattern_content();
		
		$patterns = [
			'intro' => [
				'title'       => __( 'Media Kit - Intro', 'newspack-ads' ),
				'description' => __( 'The intro section for the Media Kit page.', 'newspack-ads' ),
			],
			'audience' => [
				'title'       => __( 'Media Kit - Audience', 'newspack-ads' ),
				'description' => __( 'A 3-column layout showing the audience.', 'newspack-ads' ),
			],
			'why-us' => [
				'title'       => __( 'Media Kit - Why Us?', 'newspack-ads' ),
				'description' => __( 'A 2-column layout for the "Why Us?" section.', 'newspack-ads' ),
			],
			'ad-specs' => [
				'title'       => __( 'Media Kit - Ad Specs', 'newspack-ads' ),
				'description' => __( 'Ad Specs tabular structure with tabs management.', 'newspack-ads' ),
			],
			'rates' => [
				'title'       => __( 'Media Kit - Rates', 'newspack-ads' ),
				'description' => __( 'Rates tabular structure with tabs management.', 'newspack-ads' ),
			],
			'packages' => [
				'title'       => __( 'Media Kit - Packages', 'newspack-ads' ),
				'description' => __( 'Packages layout with a short note and a 3-column layout.', 'newspack-ads' ),
			],
			'contact-compact' => [
				'title'       => __( 'Media Kit - Contact (Compact)', 'newspack-ads' ),
				'description' => __( 'A compact Call-To-Action to get in touch.', 'newspack-ads' ),
			],
			'contact' => [
				'title'       => __( 'Media Kit - Contact', 'newspack-ads' ),
				'description' => __( 'A Call-To-Action to get in touch.', 'newspack-ads' ),
			],
		];

		foreach ( $patterns as $slug => $config ) {
			if ( isset( $pattern_content[ $slug ] ) ) {
				register_block_pattern(
					'newspack-ads/' . $slug,
					array(
						'title'       => $config['title'],
						'description' => $config['description'],
						'categories'  => [ 'newspack-ads' ],
						'content'     => $pattern_content[ $slug ],
					)
				);
			}
		}
	}

	/**
	 * Get cached block pattern content to avoid repeated file I/O and processing.
	 *
	 * @return array Array of pattern content keyed by slug.
	 */
	private static function get_cached_pattern_content() {
		// Use WordPress transients for caching with a 1-hour expiration.
		$cache_key = 'newspack_ads_media_kit_patterns';
		$cached_content = get_transient( $cache_key );
		
		if ( false !== $cached_content ) {
			return $cached_content;
		}

		// Load all pattern files in a single operation.
		$pattern_files = [
			'intro' => 'intro.php',
			'audience' => 'audience.php',
			'why-us' => 'why-us.php',
			'ad-specs' => 'ad-specs.php',
			'rates' => 'rates.php',
			'packages' => 'packages.php',
			'contact-compact' => 'contact-compact.php',
			'contact' => 'contact.php',
		];

		$pattern_content = [];
		
		// Single ob_start/ob_get_clean cycle for all patterns.
		ob_start();
		
		foreach ( $pattern_files as $slug => $filename ) {
			$file_path = NEWSPACK_ADS_MEDIA_KIT_BLOCK_PATTERNS . $filename;
			if ( file_exists( $file_path ) ) {
				// Capture output for this specific pattern.
				ob_start();
				include $file_path;
				$pattern_content[ $slug ] = wp_kses_post( ob_get_clean() );
			}
		}
		
		// Clean up the outer buffer.
		ob_end_clean();

		// Cache the processed content for 1 hour.
		set_transient( $cache_key, $pattern_content, HOUR_IN_SECONDS );
		
		return $pattern_content;
	}
}

Media_Kit_Block_Patterns::init();
