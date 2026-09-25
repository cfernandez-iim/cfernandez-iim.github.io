<?php
/**
 * Shortcodes: the parts of this site Elementor has no widget for.
 *
 * Hybrid model. Elementor owns page layout and all the copy anyone will
 * realistically want to edit. These shortcodes supply the four pieces that are
 * hand-built and have no Elementor equivalent, so they can be dropped into an
 * Elementor page and keep working:
 *
 *   [iim_map]       the world map, with the mandate count per country
 *   [iim_mandates]  the filterable 40-mandate track record
 *   [iim_projects]  the selected-mandates rail
 *   [iim_stats]     the figures band, counting up on first view
 *
 * Each one renders the same template part the theme's own pages use, so the
 * two can never drift apart.
 *
 * In Elementor: add a "Shortcode" widget and type the shortcode into it. Set
 * the containing section to full width with no padding, because these blocks
 * bring their own gutter and maximum width.
 *
 * @package iim
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render a template part into a string.
 *
 * Shortcodes must return their markup, never echo it: anything echoed lands at
 * the top of the page instead of where the shortcode sits.
 *
 * @param string $part Template part name under template-parts/.
 * @return string
 */
function iim_capture_part( $part ) {

	$file = get_theme_file_path( 'template-parts/' . $part . '.php' );

	if ( ! file_exists( $file ) ) {
		return '';
	}

	ob_start();
	require $file;

	return (string) ob_get_clean();
}

/**
 * Guard against the same block being placed twice on one page.
 *
 * These blocks carry element ids that the JavaScript looks up by name, so a
 * second copy would leave two elements answering to one id and the controls
 * would drive whichever came first. Rendering the second one as nothing is
 * less confusing than rendering it broken.
 *
 * @param string $key Block key.
 * @return bool True the first time only.
 */
function iim_claim_once( $key ) {

	static $seen = array();

	if ( isset( $seen[ $key ] ) ) {
		return false;
	}

	$seen[ $key ] = true;

	return true;
}


/* -------------------------------------------------------------------------
 * [iim_map]
 * ---------------------------------------------------------------------- */

function iim_shortcode_map() {

	if ( ! iim_claim_once( 'map' ) ) {
		return '';
	}

	return '<div class="iim-embed iim-embed--map">' . iim_capture_part( 'map' ) . '</div>';
}
add_shortcode( 'iim_map', 'iim_shortcode_map' );


/* -------------------------------------------------------------------------
 * [iim_stats]
 * ---------------------------------------------------------------------- */

function iim_shortcode_stats() {

	if ( ! iim_claim_once( 'stats' ) ) {
		return '';
	}

	return '<div class="iim-embed">' . iim_capture_part( 'stats' ) . '</div>';
}
add_shortcode( 'iim_stats', 'iim_shortcode_stats' );


/* -------------------------------------------------------------------------
 * [iim_projects]
 * ---------------------------------------------------------------------- */

function iim_shortcode_projects() {

	if ( ! iim_claim_once( 'projects' ) ) {
		return '';
	}

	// The rail lives inside .work, which supplies the horizontal bleed and the
	// scroll timeline the progress bar reads.
	return '<div class="iim-embed"><section class="work">'
		. iim_capture_part( 'projects' )
		. '</section></div>';
}
add_shortcode( 'iim_projects', 'iim_shortcode_projects' );


/* -------------------------------------------------------------------------
 * [iim_mandates]
 *
 * The only one that needs JavaScript beyond main.js. The template part
 * enqueues those scripts itself, so a page without this block never downloads
 * the 19 KB of mandate data, and the scripts cannot get out of step with the
 * markup they drive.
 * ---------------------------------------------------------------------- */

function iim_shortcode_mandates() {

	if ( ! iim_claim_once( 'mandates' ) ) {
		return '';
	}

	// The template part enqueues its own scripts.
	return '<div class="iim-embed">' . iim_capture_part( 'mandates' ) . '</div>';
}
add_shortcode( 'iim_mandates', 'iim_shortcode_mandates' );
