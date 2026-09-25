<?php
/**
 * Fallback template, and the home page when no static front page is set.
 *
 * WordPress requires index.php in every theme. It renders the home page so the
 * site looks right whether or not Settings > Reading has been pointed at a
 * static page; front-page.php handles the case where it has.
 *
 * @package iim
 */

iim_header();
get_template_part( 'template-parts/home' );
iim_footer();
