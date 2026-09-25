<?php
/**
 * Static front page.
 *
 * This file must exist. WordPress resolves a static front page as
 *     front-page.php -> page-{slug}.php -> page-{id}.php -> page.php -> index.php
 * so without it page.php answers first and renders the_content(), i.e. whatever
 * sits in the WordPress editor, instead of the home page markup.
 *
 * @package iim
 */

iim_header();
get_template_part( 'template-parts/home' );
iim_footer();
