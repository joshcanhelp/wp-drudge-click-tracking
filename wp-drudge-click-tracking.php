<?php
/*
 * Plugin Name: WP-Drudge Click Tracking
 * Plugin URI: http://wpdrudge.com/docs/extending-wp-drudge
 * Description: Built-in click-tracking for outbound links
 * Version: 2.7.2
 * Author: PROPER Web Development
 * Author URI: http://theproperweb.com
 * License: GPLv2 or later
 */

/**
 * Look for redirect action and process
 */

function wpd_click_track() {

	// Is there a redirect request? If not, stop

	if ( empty( $_GET['wpd_redirect'] ) || $_GET['wpd_redirect'] != 1 ) {
		return;
	}

	// Is there a post ID we can use? If not, stop

	if ( empty( $_GET['pid'] ) ) {
		return;
	}

	$pid = intval( $_GET['pid'] );

	// Get the Outbound Link. If none, stop

	$link = get_post_meta( $pid, 'link', TRUE );

	if ( empty( $link ) ) {
		return;
	}

	// Add a click to the click count

	$curr_count = get_post_meta( $pid, '_wpd_click_count', TRUE );
	$curr_count = empty( $curr_count ) ? 1 : absint( $curr_count ) + 1;
	update_post_meta( $pid, '_wpd_click_count', $curr_count );

	// Beat it

	wp_redirect( esc_url( $link ), 302 );
	exit();
}

add_action( 'plugins_loaded', 'wpd_click_track' );

/**
 * Change the actual link being used to point to the tracking processor
 *
 * @param $link
 * @param $post_id
 * @param $widget
 *
 * @return string
 */

function wpd_click_process_link( $link, $post_id, $widget ) {

	// If there is no outbound link, do nothing

	if ( get_post_meta( intval( $post_id ), 'link', TRUE ) ) {

		if ( $widget && function_exists( 'wpd_get_key' ) && ! wpd_get_key( 'wpd_display_interrupt' ) ) {
			return site_url() . '?wpd_redirect=1&amp;pid=' . $post_id;
		}
	}

	return $link;

}

add_filter( 'wpd_link_text', 'wpd_click_process_link', 1000, 3 );


/**
 * Change the outbound link value to point back to the site for tracking
 *
 * @param $link
 * @param $post_id
 *
 * @return string
 */

function wpd_click_process_link_out( $link, $post_id ) {

	// If there is no outbound link, do nothing

	if ( get_post_meta( intval( $post_id ), 'link', TRUE ) ) {
		return site_url() . '?wpd_redirect=1&amp;pid=' . $post_id;
	}

	return $link;

}

add_filter( 'wpd_link_out_text', 'wpd_click_process_link_out', 1000, 2 );


/**
 * Adds a column in the wp-admin that shows the click count for links
 *
 * @param array $defaults
 *
 * @return array
 */

function wpd_clicktrack_posts_column( $defaults ) {
	$defaults['wpd_click_count'] = 'Outbound Link Clicks';

	return $defaults;
}

add_filter( 'manage_post_posts_columns', 'wpd_clicktrack_posts_column', 10, 1 );


/**
 * Display function for the click count
 *
 * @param string $col
 * @param int    $post_id
 */

function wpd_clicktrack_posts_custom_column( $col, $post_id ) {

	if ( 'wpd_click_count' === $col ) {
		$click_count = intval( get_post_meta( $post_id, '_wpd_click_count', TRUE ) );
		echo $click_count ? absint( $click_count ) : 0;
	}
}

add_action( 'manage_posts_custom_column', 'wpd_clicktrack_posts_custom_column', 10, 2 );


/**
 * Include tracking for Google Analytics
 * This is both experimental and likely non-functioning
 *
 * TODO: Test and complete
 */

function wpd_ga_click_tracking() {

	wp_enqueue_script(
		'wpd-click-tracking',
		plugins_url() . '/wp-drudge-click-tracking/ga-event-tracking.js',
		FALSE,
		FALSE,
		TRUE
	);

}

// add_action( 'wp_enqueue_scripts', 'wpd_ga_click_tracking' );