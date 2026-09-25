<?php
/**
 * Generic page fallback.
 *
 * Not in the original four-page site, but WordPress needs it. Without this
 * file, a page whose slug does not match one of the page-*.php templates
 * falls all the way through to index.php and renders the entire home page
 * under the wrong title. This renders whatever is in the editor instead, so
 * a mis-slugged page looks empty and obviously wrong rather than looking
 * plausible and being wrong.
 *
 * @package iim
 */

iim_header();
?>

<main class="page" id="main">
  <section class="phead">
    <div class="phead__inner">
      <h1 class="phead__title"><?php the_title(); ?></h1>
    </div>
  </section>

  <section class="results">
    <?php
    while ( have_posts() ) :
      the_post();
      the_content();
    endwhile;
    ?>
  </section>
</main>

<?php
iim_footer();
