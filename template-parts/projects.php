<?php
/**
 * The selected-mandates rail: horizontal scroll-snap carousel.
 *
 * Rendered by the theme templates and by the [iim_projects] shortcode, so a page built
 * in Elementor and a page built by the theme show exactly the same markup.
 *
 * @package iim
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="work__part">
  <div class="work__head">
    <h3 class="work__subtitle reveal">Selected mandates</h3>
    <div class="work__controls">
      <button class="rail-btn" type="button" data-dir="-1" aria-label="Previous projects">
        <span aria-hidden="true">&#8592;</span>
      </button>
      <button class="rail-btn" type="button" data-dir="1" aria-label="Next projects">
        <span aria-hidden="true">&#8594;</span>
      </button>
    </div>
  </div>

  <ul class="work__scroller" id="work-scroller" tabindex="0"
      aria-label="Selected mandates, scroll horizontally">
    <li class="proj" data-photo="sr400">
      <div class="proj__media" role="img" aria-label="Aerial view of a multi-lane managed-lanes corridor"></div>
      <h3 class="proj__title">SR400 Managed Lanes</h3>
      <ul class="meta">
        <li>USA</li>
        <li>Managed Lanes</li>
        <li>Macquarie / GDOT / John Laing</li>
      </ul>
    </li>
    <li class="proj" data-photo="bts">
      <div class="proj__media" role="img" aria-label="Highway corridor running beneath an elevated viaduct"></div>
      <h3 class="proj__title">Briceño-Tunja-Sogamoso</h3>
      <ul class="meta">
        <li>Colombia</li>
        <li>Toll Roads</li>
        <li>Macquarie / CSS</li>
      </ul>
    </li>
    <li class="proj" data-photo="austral">
      <div class="proj__media" role="img" aria-label="Airport apron at dusk"></div>
      <h3 class="proj__title">Airports Red Austral</h3>
      <ul class="meta">
        <li>Chile</li>
        <li>Airport</li>
        <li>Macquarie / MOP</li>
      </ul>
    </li>
    <li class="proj" data-photo="zambia">
      <div class="proj__media" role="img" aria-label="Rooftop solar array"></div>
      <h3 class="proj__title">Zambia Solar and Hydro</h3>
      <ul class="meta">
        <li>Zambia</li>
        <li>Solar</li>
        <li>IDP</li>
      </ul>
    </li>
  </ul>

  <!-- Tracks how far the rail has been scrolled. Driven by a CSS scroll
       timeline, so there is no scroll listener behind it. -->
  <div class="work__progress" aria-hidden="true"><span></span></div>

  <p class="more reveal"><a href="<?php echo esc_url( iim_page_url( 'track-record' ) ); ?>">All 40 mandates <span aria-hidden="true">&#8594;</span></a></p>
</div>
