<?php
/**
 * The filterable mandate list: controls plus the results grid.
 * Cards are rendered client-side by js/mandates.js.
 *
 * Rendered by the theme templates and by the [iim_mandates] shortcode, so a page built
 * in Elementor and a page built by the theme show exactly the same markup.
 *
 * @package iim
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * The scripts follow the markup rather than being decided elsewhere.
 *
 * This used to be a condition in functions.php testing is_page_template() and
 * a hardcoded slug. Both miss on a page whose stored template is
 * elementor_canvas, or whose slug is not exactly "track-record", and the only
 * symptom was an empty grid: mandates.js bails silently when the data is not
 * there. Enqueuing at the point of use cannot drift from where the markup is.
 *
 * Both are registered in functions.php and sit in the footer, so this runs in
 * time no matter where on the page the block appears.
 */
wp_enqueue_script( 'iim-mandates-data' );
wp_enqueue_script( 'iim-mandates' );
?>
<section class="filters" aria-label="Filter mandates">
  <div class="filters__inner">

    <div class="filters__row">
      <label class="field">
        <span class="field__label">Search</span>
        <input class="field__input" type="search" id="f-search"
               placeholder="Project, client or country" autocomplete="off">
      </label>

      <label class="field field--select">
        <span class="field__label">Country</span>
        <select class="field__input" id="f-country">
          <option value="">All countries</option>
        </select>
      </label>

      <div class="field field--chips">
        <span class="field__label" id="lbl-type">Client</span>
        <div class="chips" role="group" aria-labelledby="lbl-type" id="f-type">
          <button class="chip is-on" type="button" data-value="" aria-pressed="true">All</button>
          <button class="chip" type="button" data-value="Public" aria-pressed="false">Public</button>
          <button class="chip" type="button" data-value="Private" aria-pressed="false">Private</button>
        </div>
      </div>
    </div>

    <div class="field field--chips">
      <span class="field__label" id="lbl-sector">Sector</span>
      <div class="chips" role="group" aria-labelledby="lbl-sector" id="f-sector">
        <button class="chip is-on" type="button" data-value="" aria-pressed="true">All sectors</button>
      </div>
    </div>

    <div class="filters__foot">
      <!-- Recomputed on every filter change. The figures animate to their
           new values so the effect of a filter is visible, not just read. -->
      <div class="summary" id="summary">
        <p class="summary__item">
          <span class="summary__n" id="sum-mandates">40</span>
          <span class="summary__l">mandates</span>
        </p>
        <p class="summary__item">
          <span class="summary__n" id="sum-countries">11</span>
          <span class="summary__l">countries</span>
        </p>
        <p class="summary__item">
          <span class="summary__n" id="sum-sectors">7</span>
          <span class="summary__l">sectors</span>
        </p>
      </div>
      <div class="filters__acts">
        <button class="linkbtn" type="button" id="f-clear" hidden>Clear filters</button>
        <!-- One control for the whole list, not one per card: it switches
             every result showing from the card grid to a full-width row with
             its description alongside. -->
        <button class="viewbtn" type="button" id="f-detail"
                aria-pressed="false" aria-controls="results-grid">
          <span class="viewbtn__label">Detail</span>
          <span class="viewbtn__icon" aria-hidden="true"></span>
        </button>
      </div>
    </div>

    <p class="count sr-only" id="count" role="status">40 mandates</p>

    <p class="sector-note" id="sector-note" hidden></p>
  </div>
</section>

<!-- ===== Results. Cards are rendered by mandates.js. ===== -->
<section class="results" id="results">
  <ul class="results__grid" id="results-grid"></ul>

  <p class="results__empty" id="results-empty" hidden>
    No mandates match those filters.
  </p>

  <noscript>
    <p class="results__empty">This list needs JavaScript to filter. The full set of
      mandates is available on request at
      <a href="mailto:advisory@infrastructureinmotion.com">advisory@infrastructureinmotion.com</a>.</p>
  </noscript>
</section>
