<?php
/**
 * Template Name: IIM Track Record
 *
 * Assign this template to the "Track Record" page in the page editor,
 * under Page Attributes > Template. It also applies automatically to a
 * page whose slug is "track-record".
 */

iim_header();
?>
<main class="page">

  <!-- ===== Page head ===== -->
  <section class="phead">
    <div class="phead__inner">
      <p class="eyebrow">Track Record</p>
      <h1 class="phead__title">Forty mandates, eleven countries.</h1>
      <p class="phead__sub">Revenue-risk roads, airports, transit, fibre, hospitals and
        generation, advised for sponsors, lenders and public authorities. Filter the list,
        or switch it to detail to read every description.</p>
    </div>
  </section>

  <!-- ===== Filters. Rendered here in markup so the page works before JS runs. ===== -->
  <?php get_template_part( 'template-parts/mandates' ); ?>

  <!-- ===== Closing CTA ===== -->
  <section class="cta">
    <h2 class="cta__title reveal">Tell us about the asset.</h2>
    <p class="cta__body reveal" style="--i:1">Send the term sheet, the tender notice, or the
      one-line question you cannot get a straight answer to. A partner replies, not a form.</p>
    <div class="cta__actions reveal" style="--i:2">
      <a class="btn btn--lg" href="mailto:advisory@infrastructureinmotion.com">Contact a partner</a>
      <a class="cta__email" href="mailto:advisory@infrastructureinmotion.com">advisory@infrastructureinmotion.com</a>
    </div>
  </section>

</main>

<?php
iim_footer();
