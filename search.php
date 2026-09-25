<?php
/**
 * Search results.
 *
 * Also not in the original site. Search engines and bots hit /?s=... on every
 * WordPress install; without this file those requests render index.php, i.e.
 * the whole home page. This is a brochure site with no posts, so the honest
 * answer is a short results list or nothing.
 *
 * @package iim
 */

iim_header();
?>

<main class="page" id="main">
  <section class="phead">
    <div class="phead__inner">
      <p class="eyebrow">Search</p>
      <h1 class="phead__title"><?php echo esc_html( get_search_query() ); ?></h1>
    </div>
  </section>

  <section class="results">
    <?php if ( have_posts() ) : ?>
      <ul class="results__grid">
        <?php while ( have_posts() ) : the_post(); ?>
          <li class="mandate">
            <div class="mandate__main">
              <h2 class="mandate__title">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
              </h2>
            </div>
          </li>
        <?php endwhile; ?>
      </ul>
    <?php else : ?>
      <p class="lede">Nothing matched that search. The mandate filters on the
        Track Record page are the better way to search this site.</p>
    <?php endif; ?>
  </section>
</main>

<?php
iim_footer();
