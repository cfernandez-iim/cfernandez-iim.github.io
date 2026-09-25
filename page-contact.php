<?php
/**
 * Template Name: IIM Contact
 *
 * Assign this template to the "Contact" page in the page editor,
 * under Page Attributes > Template. It also applies automatically to a
 * page whose slug is "contact".
 */

iim_header();
?>
<main id="main">

  <section class="phead">
    <div class="phead__inner">
      <p class="eyebrow">Contact us</p>
      <h1 class="phead__title">Tell us about the asset.</h1>
      <p class="phead__sub">Send the term sheet, the tender notice, or the one-line
        question you cannot get a straight answer to. A partner replies, not a form.</p>
    </div>
  </section>

  <section class="section" id="reach">
    <p class="intro__body reveal">Write to us directly and a partner will come back to
      you. If it is a live tender with a deadline, say so in the subject line.</p>
    <div class="cta__actions reveal" style="--i:1">
      <a class="btn btn--lg" href="mailto:advisory@infrastructureinmotion.com">Contact a partner</a>
      <a class="cta__email" href="mailto:advisory@infrastructureinmotion.com">advisory@infrastructureinmotion.com</a>
    </div>
  </section>

  <!-- ===== Offices. TODO: street addresses and phone numbers still needed. A
       panel checking whether the firm is real looks for exactly those. ===== -->
  <section class="offices" id="offices">
    <h2 class="offices__title reveal">Four offices, one desk.</h2>
    <ul class="offices__grid">
      <li class="office reveal" style="--i:0"><span class="office__city">Madrid</span>
        <span class="office__meta">Spain</span></li>
      <li class="office reveal" style="--i:1"><span class="office__city">Washington DC</span>
        <span class="office__meta">United States</span></li>
      <li class="office reveal" style="--i:2"><span class="office__city">Bogot&aacute;</span>
        <span class="office__meta">Colombia</span></li>
      <li class="office reveal" style="--i:3"><span class="office__city">Santiago</span>
        <span class="office__meta">Chile</span></li>
    </ul>
  </section>

</main>

<?php
iim_footer();
