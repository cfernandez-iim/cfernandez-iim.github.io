<?php
/**
 * Header: doctype through the sticky top bar.
 *
 * Changes from the static build, all of them required by WordPress:
 *   - <title> removed; add_theme_support('title-tag') prints it.
 *   - <link rel="canonical"> removed; WordPress prints its own, and two
 *     canonical tags on one page is worse than none.
 *   - <link rel="stylesheet"> and the font preload removed; both are
 *     registered in functions.php.
 *   - wp_head() added immediately before </head>.
 *   - asset paths now run through get_template_directory_uri().
 *
 * @package iim
 */

$iim_uri  = get_template_directory_uri();
$iim_skip = iim_skip_link();
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#0d1a33">

<?php
/*
 * Social and description metadata.
 *
 * DELETE THIS WHOLE BLOCK if you install Yoast, Rank Math or any other SEO
 * plugin. They output the same tags, and duplicates confuse the crawlers you
 * are trying to please.
 */
$iim_desc = get_bloginfo( 'description' );
if ( is_front_page() || '' === $iim_desc ) {
	$iim_desc = 'Boutique advisory for the generation, grid and transport assets that economies depend on. Transaction advisory, project finance, PPP and concessions across Iberia and Latin America.';
}
?>
<meta name="description" content="<?php echo esc_attr( $iim_desc ); ?>">

<meta property="og:type" content="website">
<meta property="og:site_name" content="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
<meta property="og:title" content="<?php echo esc_attr( wp_get_document_title() ); ?>">
<meta property="og:description" content="<?php echo esc_attr( $iim_desc ); ?>">
<meta property="og:url" content="<?php echo esc_url( is_singular() ? get_permalink() : home_url( '/' ) ); ?>">
<meta property="og:image" content="<?php echo esc_url( $iim_uri . '/assets/iim-logo.png' ); ?>">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo esc_attr( wp_get_document_title() ); ?>">
<meta name="twitter:description" content="<?php echo esc_attr( $iim_desc ); ?>">
<meta name="twitter:image" content="<?php echo esc_url( $iim_uri . '/assets/iim-logo.png' ); ?>">

<?php
// Falls back to the theme logo when no Site Icon is set in the Customizer.
if ( ! has_site_icon() ) :
	?>
	<link rel="icon" href="<?php echo esc_url( $iim_uri . '/assets/iim-logo.png' ); ?>">
	<?php
endif;
?>

<?php wp_head(); ?>
</head>

<?php
/*
 * "with-photos" is the switch that turns photography on. Remove it and the
 * site falls back to its fully typographic state, with no broken images and
 * no 404s. See the theme README.
 *
 * Only the home page carries it, matching the static build: the hero slab and
 * the four project cards are the only photographic slots on the site.
 *
 * It is added by iim_body_class() in functions.php rather than written in here,
 * so that it survives anything which replaces this file's body tag.
 */
?>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="<?php echo esc_attr( $iim_skip[0] ); ?>"><?php echo esc_html( $iim_skip[1] ); ?></a>

<!-- ===== Sticky navy top bar. Solidifies once the hero scrolls past.
     The accent hairline at its base tracks scroll progress. ===== -->
<header class="nav" id="nav">
  <div class="nav__inner">
    <a class="nav__brand" href="<?php echo esc_url( is_front_page() ? '#top' : home_url( '/' ) ); ?>" aria-label="Infrastructure in Motion, back to top">
      <img src="<?php echo esc_url( $iim_uri . '/assets/iim-logo.png' ); ?>" alt="Infrastructure in Motion" width="957" height="375">
    </a>

    <nav class="nav__links" id="nav-links" aria-label="Sections">
      <?php if ( ! iim_nav( 'primary' ) ) : ?>
      <a href="<?php echo esc_url( iim_page_url( 'about' ) ); ?>"<?php echo iim_current_attr( 'about' ); ?>>About us</a>
      <a href="<?php echo esc_url( is_front_page() ? '#practice' : home_url( '/#practice' ) ); ?>">Practice</a>
      <a href="<?php echo esc_url( iim_page_url( 'track-record' ) ); ?>"<?php echo iim_current_attr( 'track-record' ); ?>>Track Record</a>
      <a href="<?php echo esc_url( iim_page_url( 'contact' ) ); ?>"<?php echo iim_current_attr( 'contact' ); ?>>Contact us</a>
      <?php endif; ?>
    </nav>

    <a class="btn btn--sm nav__cta" href="mailto:advisory@infrastructureinmotion.com">Contact a partner</a>

    <button class="nav__toggle" id="nav-toggle" type="button"
            aria-expanded="false" aria-controls="nav-links" aria-label="Open menu">
      <span></span><span></span><span></span>
    </button>
  </div>
  <div class="nav__progress" aria-hidden="true"></div>
</header>
