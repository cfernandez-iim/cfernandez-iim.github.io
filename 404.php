<?php
/**
 * Not found.
 *
 * Also not in the original site. Without it a bad URL renders index.php,
 * which would serve the full home page with a 404 status code.
 *
 * @package iim
 */

iim_header();
?>

<main class="page" id="main">
  <section class="phead">
    <div class="phead__inner">
      <p class="eyebrow">404</p>
      <h1 class="phead__title">That page is not here.</h1>
      <p class="phead__sub">The link may be out of date. The track record, the
        practice and the contact details are all a click away.</p>
      <p class="more"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Back to the home page
        <span aria-hidden="true">&#8594;</span></a></p>
    </div>
  </section>
</main>

<?php
iim_footer();
