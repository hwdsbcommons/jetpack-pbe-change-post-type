<?php
/*
Plugin Name: Jetpack Post By Email - Post Presentation Slide
Description: Changes the post type of items created by Jetpack's Post By Email module to a Reveal JS Presentation slide.
Version: 0.1
Author: r-a-y
Author URI: https://profiles.wordpress.org/r-a-y
License: GPLv2 or later
*/

/**
 * Schedule a cronjob to change the post type of a Jetpack post-by-email item.
 *
 * Specifically, changes the post type to a Reveal JS slide.
 *
 * @param int $post_id The post ID.
 */
function jp_pbe_set_event( $post_id = 0 ) {
	// Check if Reveal JS Presentations is activated
	// https://github.com/cgrymala/reveal-js-presentations
	if ( ! class_exists( 'Reveal_Presentations' ) ) {
		return;
	}

	// Check if this is a JP post by email request
	if ( empty( $_REQUEST ) ) {
		return;
	}
	if ( 'jetpack' !== $_REQUEST['for'] ) {
		return;
	}

	wp_schedule_single_event( time() + 60, 'jp_pbe_cron_hook', array( $post_id ) );
}
add_action( 'save_post', 'jp_pbe_set_event' );

/**
 * Run our custom hook to change a JP post-by-email item to a slide.
 */
function jp_pbe_change_post_type_after_pbe( $post_id ){
	// Change to Reveal JS Presentation's post type!
	set_post_type( $post_id, 'slides'  );

	// Set presentation taxonomy to 'announcements'
	wp_set_object_terms( $post_id, 'announcements', 'presentation' );

	// Make sure the title is used for the slide
	update_post_meta( $post_id, '_rjs_slide_settings', array(
		'use-title' => true
	) );
}
add_action( 'jp_pbe_cron_hook', 'jp_pbe_change_post_type_after_pbe' );
