<?php
/*
Plugin Name: URI Countdown
Plugin URI: https://www.uri.edu
Description: Creates a countdown widget
Version: 1.0
Author: URI Web Communications
Author URI:
@author: John Pennypacker <jpennypacker@uri.edu>
@author: Brandon Fuller <bjcfuller@uri.edu>
*/

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');


/**
 * Returns a string to be used for cache busting
 *
 * @return str
 */
function _uri_countdown_cache_buster() {
	static $cache_buster;
	if ( empty( $cache_buster ) ) {
    if( ! function_exists( 'get_plugin_data' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    }
		$cache_buster = get_plugin_data( __FILE__ )['Version'];
		$cache_buster = date(time());
	}
	return $cache_buster;
}


/**
 * Loads the javascript.
 */
function uri_countdown_scripts() {
	// return FALSE; // the script is empty; @todo: update front end in real time
	wp_register_script( 'uri-countdown', plugins_url( '/js/countdown.js', __FILE__ ) );
	wp_enqueue_script( 'uri-countdown' );
}


/**
 * Loads the css.
 */
function uri_countdown_styles() {
	wp_register_style( 'uri-countdown', plugins_url( '/css/countdown.css', __FILE__ ), false, _uri_countdown_cache_buster() );
	wp_enqueue_style( 'uri-countdown' );
}


/**
 * The callback for the shortcode.
 */
function uri_countdown_shortcode( $attributes, $content, $shortcode ) {

	uri_countdown_scripts();
	uri_countdown_styles();

	// supplement supplied attributes with defaults
	$attributes = _uri_countdown_atts( $attributes );

	$deadline = strtotime( $attributes['deadline'] );
	$today = time();
	$difference = $deadline - $today;

	$left = _uri_countdown_time_left_units( $difference );
	$message = _uri_countdown_time_left_in_words( $left, $attributes );

	$output = NULL;

	if( ! empty( $message ) ) {
		$hash = md5($attributes['deadline'] . $attributes['event'] . $attributes['link']);
		if( ! empty( $attributes['link'] ) ) {
			$output = '<div class="' . $attributes['class'] . '" data-hash="' . $hash . '"><a href="' . $attributes['link'] . '">' . $message . '</a>';
			if ( $attributes['dismissable'] === TRUE ) {
				$output .= '<div class="dismiss" title="Dismiss">Dismiss message</div>';
			}
			$output .= '</div>';
		} else {
			$output = '<div class="' . $attributes['class'] . '" data-hash="' . $hash . '">' . $message . '</div>';
		}
	}


	return $output;

}
add_shortcode( 'uri-countdown', 'uri_countdown_shortcode' );


/**
 * Wrapper for shortcode_atts to set defaults.
 * @param arr $attributes are the shortcode attributes
 * @return arr
 */
function _uri_countdown_atts( $attributes ) {
	$a = shortcode_atts(
		array(
			'deadline' => '',
			'event' => 'the deadline',
			'show_expired' => FALSE,
			'dismissable' => TRUE,
			'until' => 'until',
			'is_today' => 'is today',
			'passed' => 'passed',
			'link' => '',
			'class' => ''
		), $attributes );

	$classes = array( 'uri-countdown' );
	if ( ! empty( $a['class'] ) ) {
		$classes[] = $a['class'];
	}
	$a['class'] = implode( ' ', $classes );

	return $a;

}



/**
 * Get a plain language sentence that announces the time remaining.
 * @param arr $a an array with seconds, days, hours, minutes defined
 * @param arr $attributes are the shortcode attributes
 * @return str
 */
function _uri_countdown_time_left_in_words( $a, $attributes ) {

	$attributes = _uri_countdown_atts( $attributes );

	$days = $a['days'];
	$day_string = ($days == 1 || $days == -1) ? 'day' : 'days';

	$hours = $a['hours'];
	$hour_string = ($hours == 1 || $hours == -1) ? 'hour' : 'hours';

	$minutes = $a['minutes'];;
	$minute_string = ($minutes == 1 || $minutes == -1) ? 'minute' : 'minutes';

	$seconds = $a['seconds'];
	$second_string = ($seconds == 1 || $seconds == -1) ? 'second' : 'seconds';

	// we're loose with months and years since it's so far away
	$years = round( $days / 365 );
	$year_string = $years == 1 ? 'year' : 'years';
	$months = round( $days / 30 );
	$month_string = $months == 1 ? 'month' : 'months';

	if ( $days == 0 ) {
		// if we want to get really granular, this will do it.
		// caching more or less defeats the purpose of getting so granular
// 		if ( $hours > 1 ) {
// 			$message = sprintf('<span class="time-left-number">%d</span> %s until <span class="time-left-event">%s</span>.', $hours, $hour_string, $attributes['event'] );
// 		} elseif ( $minutes > 0 ) {
// 			$message = sprintf('<span class="time-left-number">%d</span> %s until <span class="time-left-event">%s</span>.', $minutes, $minute_string, $attributes['event'] );
// 		} else {
// 			$message = sprintf('<span class="time-left-number">%d</span> %s until <span class="time-left-event">%s</span>.', $seconds, $second_string, $attributes['event'] );
// 		}
		$capitalized_event = ucfirst( $attributes['event'] );
		$message = sprintf('<span class="time-left-event">%s</span> %s.', $capitalized_event, $attributes['is_today'] );
	} elseif ( $days > 59 ) {
		if ( $years > 0 ) {
			$message = sprintf( '<span class="time-left"><span class="time-left-number">%d</span> <span class="time-left-unit">%s</span></span> %s <span class="time-left-event">%s</span>.', $years, $year_string, $attributes['until'], $attributes['event'] );
		} else {
			$message = sprintf( '<span class="time-left"><span class="time-left-number">%d</span> <span class="time-left-unit">%s</span></span> %s <span class="time-left-event">%s</span>.', $months, $month_string, $attributes['until'], $attributes['event'] );
		}
	} else {
		$message = sprintf( '<span class="time-left"><span class="time-left-number">%d</span> <span class="time-left-unit">%s</span></span> %s <span class="time-left-event">%s</span>.', $days, $day_string, $attributes['until'], $attributes['event'] );
	}

	if ( $days < 0 ) {
		if ( $attributes['show_expired'] !== FALSE ) {
			if ( $days < -30 ) {
				if ( $years < 0 ) {
					$message = sprintf( '<span class="time-left-event">%s</span> %s <span class="time-left"><span class="time-left-number">%d</span> <span class="time-left-unit">%s</span></span> ago.', $attributes['event'], $attributes['passed'], abs($years), $year_string );
				} else {
					$message = sprintf( '<span class="time-left-event">%s</span> %s <span class="time-left"><span class="time-left-number">%d</span> <span class="time-left-unit">%s</span></span> ago.', $attributes['event'], $attributes['passed'], abs($months), $month_string );
				}
			} elseif ( $days < -1 ) {
				$message = sprintf( '<span class="time-left-event">%s</span> %s <span class="time-left"><span class="time-left-number">%d</span> <span class="time-left-unit">%s</span></span> ago.', $attributes['event'], $attributes['passed'], abs($days), $day_string );
			} else {
				$message = sprintf( '<span class="time-left-event">%s</span> has %s.', $attributes['event'], $attributes['passed'] );
			}
		} else {
			$message = FALSE;
		}
	}


  return $message;

}

/**
 * Return an array with seconds, days, hours, minutes defined.
 * @param int $secs the number of seconds togo
 * @return arr
 */
function _uri_countdown_time_left_units( $secs ) {
	$seconds = (int)$secs;
	$days = floor($seconds / 86400);
	$hours = floor(($seconds - ($days * 86400)) / 3600);
	$minutes = floor(($seconds - $days * 86400 - $hours * 3600) / 60);
	$seconds = floor($seconds - ($days * 86400) - ($hours * 3600) - ($minutes * 60));

	return array(
		'seconds' => $seconds,
		'minutes' => $minutes,
		'hours' => $hours,
		'days' => $days,
		'tense' => ( $secs >= 0 ) ? 'future' : 'past'
	);
}
