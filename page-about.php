<?php
/**
 * Template Name: IIM About
 *
 * Assign this template to the "About" page in the page editor,
 * under Page Attributes > Template. It also applies automatically to a
 * page whose slug is "about".
 */

iim_header();
?>
<main id="main">

  <section class="phead">
    <div class="phead__inner">
      <p class="eyebrow">About us</p>
      <h1 class="phead__title">A partner-led advisory practice.</h1>
      <p class="phead__sub">Infrastructure in Motion advises sponsors, lenders and public
        authorities on the assets that carry traffic and power. Forty mandates across
        eleven countries, run out of four offices.</p>
    </div>
  </section>

  <section class="section" id="firm">
    <p class="intro__body reveal">We work where the revenue risk sits. Managed lanes and
      toll roads where the traffic forecast is the deal, airports and transit where the
      concession structure is, and generation where the offtake is. The work is the same
      either way: build the number, defend it, and close.</p>
  </section>

  <!-- ===== How we work. TODO: this copy was written from the voice already in
       the site, not supplied by the firm. A partner should confirm these are
       claims IIM stands behind before it goes public. ===== -->
  <section class="section section--tint" id="values">
    <div class="section__head reveal">
      <h2 class="section__title">How we work</h2>
    </div>
    <div class="values">
        <div class="value reveal" style="--i:0">
          <h3 class="value__title">Partner-led</h3>
          <p class="value__body">A partner runs the mandate and answers the phone. Nothing is passed down to a team you have not met.</p>
        </div>
        <div class="value reveal" style="--i:1">
          <h3 class="value__title">Built for review</h3>
          <p class="value__body">Traffic, load and revenue models are built to survive lender and rating-agency scrutiny, because that is where they end up.</p>
        </div>
        <div class="value reveal" style="--i:2">
          <h3 class="value__title">Both sides of the table</h3>
          <p class="value__body">We advise sponsors and bidders, and we advise the authorities that tender to them. Each one makes us better at the other.</p>
        </div>
    </div>
  </section>

  <!-- ===== Principals. TODO: names, sectors and profile text are placeholders. -->
  <section class="section" id="principals">
    <div class="section__head reveal">
      <h2 class="section__title">Principals</h2>
    </div>
    <ul class="people">
      <li class="person reveal" style="--i:0">
        <h3 class="person__name">Partner Name</h3>
        <p class="person__role"><span>Sector 1</span><span>Madrid</span></p>
        <p class="person__bio">Profile description</p>
      </li>
      <li class="person reveal" style="--i:1">
        <h3 class="person__name">Partner Name</h3>
        <p class="person__role"><span>Sector 2</span><span>Santiago</span></p>
        <p class="person__bio">Profile description</p>
      </li>
      <li class="person reveal" style="--i:2">
        <h3 class="person__name">Partner Name</h3>
        <p class="person__role"><span>Sector 3</span><span>Washington DC</span></p>
        <p class="person__bio">Profile description</p>
      </li>
      <li class="person reveal" style="--i:3">
        <h3 class="person__name">Partner Name</h3>
        <p class="person__role"><span>Sector 4</span><span>Bogot&aacute;</span></p>
        <p class="person__bio">Profile description</p>
      </li>
    </ul>
  </section>

  <section class="cta" id="contact">
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
