<?php
/**
 * The figures band. Counts up on first view, via main.js.
 *
 * Rendered by the theme templates and by the [iim_stats] shortcode, so a page built
 * in Elementor and a page built by the theme show exactly the same markup.
 *
 * @package iim
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="stats" aria-label="Scale of the practice">
  <ul class="stats__grid">
    <li class="stat reveal" style="--i:0">
      <span class="stat__figure" data-count="14.6" data-decimals="1" data-prefix="$" data-suffix="B">$14.6B</span>
      <span class="stat__label">advised</span>
    </li>
    <li class="stat reveal" style="--i:1">
      <span class="stat__figure" data-count="4240">4,240</span>
      <span class="stat__label">MW</span>
    </li>
    <li class="stat reveal" style="--i:2">
      <span class="stat__figure" data-count="3180">3,180</span>
      <span class="stat__label">km</span>
    </li>
    <!-- Derived from the credentials rather than the older brand deck, which
         claimed 42 closes and 9 countries against 40 mandates in 11 countries. -->
    <li class="stat reveal" style="--i:3">
      <span class="stat__figure" data-count="40">40</span>
      <span class="stat__label">mandates</span>
    </li>
    <li class="stat reveal" style="--i:4">
      <span class="stat__figure" data-count="11">11</span>
      <span class="stat__label">countries</span>
    </li>
  </ul>
</section>
