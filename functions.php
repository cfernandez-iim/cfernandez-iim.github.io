<?php
/**
 * Infrastructure in Motion - theme setup, assets and small helpers.
 *
 * @package iim
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

define( 'IIM_VERSION', '1.7.1' );

/* -------------------------------------------------------------------------
 * How much of the site this theme owns
 *
 * DEFAULT: the theme owns the four pages outright and Elementor cannot
 * override them. This is what makes the site look like the design.
 *
 * The shortcodes [iim_map], [iim_mandates], [iim_projects] and [iim_stats]
 * are registered either way, so the custom pieces can be dropped into any
 * Elementor page whenever you want to move to a hybrid setup. Switch
 * IIM_THEME_OWNS_PAGES to false only once those Elementor pages exist.
 *
 * Both constants can be overridden from wp-config.php.
 * ---------------------------------------------------------------------- */

// true  = the theme forces its own templates on the home page and the three
//         interior pages, and Elementor cannot take them back. THIS IS THE
//         DEFAULT, because it is what makes the site look like the design.
// false = Elementor lays the pages out and the theme only supplies the
//         shortcodes. Do not set this to false until the Elementor versions of
//         those pages have actually been built, or they will render whatever
//         was on the old site.
if ( ! defined( 'IIM_THEME_OWNS_PAGES' ) ) {
	define( 'IIM_THEME_OWNS_PAGES', true );
}

// true  = the IIM top bar and footer, immune to Elementor Theme Builder.
// false = whatever Elementor or another plugin wants to supply.
if ( ! defined( 'IIM_THEME_OWNS_CHROME' ) ) {
	define( 'IIM_THEME_OWNS_CHROME', true );
}

require_once get_theme_file_path( 'inc/shortcodes.php' );


/* -------------------------------------------------------------------------
 * Theme supports
 * ---------------------------------------------------------------------- */

function iim_setup() {

	// Let WordPress print <title>. The static build had a hardcoded <title>
	// in every page; that tag is gone from header.php because of this.
	add_theme_support( 'title-tag' );

	add_theme_support( 'post-thumbnails' );

	// Real <nav>, <figure> etc. instead of WordPress's legacy XHTML output.
	add_theme_support(
		'html5',
		array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);

	add_theme_support( 'automatic-feed-links' );

	// Menu locations. The theme ships with its own hand-written nav so the
	// design is identical out of the box; assign a menu to either location
	// and it takes over. See iim_nav() in header.php / footer.php.
	register_nav_menus(
		array(
			'primary' => __( 'Primary (top bar)', 'iim' ),
			'footer'  => __( 'Footer', 'iim' ),
		)
	);
}
add_action( 'after_setup_theme', 'iim_setup' );


/* -------------------------------------------------------------------------
 * Styles and scripts
 *
 * The static pages carried <link rel="stylesheet"> and <script src> tags
 * directly. Those are removed from the markup and registered here instead,
 * so WordPress controls order, versioning and cache-busting.
 * ---------------------------------------------------------------------- */

function iim_assets() {

	$uri = get_template_directory_uri();

	wp_enqueue_style(
		'iim-style',
		get_stylesheet_uri(),
		array(),
		IIM_VERSION
	);

	// Interaction layer: mobile menu, IntersectionObserver reveals, counting
	// figures. Needed on every page. Loaded in the footer, matching where the
	// static build put it, because it expects the DOM to already exist.
	wp_enqueue_script(
		'iim-main',
		$uri . '/js/main.js',
		array(),
		IIM_VERSION,
		true
	);

	// Registered, not enqueued. The [iim_mandates] shortcode turns them on
	// where it is used, so a page without the track record never downloads the
	// 19 KB of mandate data.
	wp_register_script(
		'iim-mandates-data',
		$uri . '/js/mandates-data.js',
		array(),
		IIM_VERSION,
		true
	);

	wp_register_script(
		'iim-mandates',
		$uri . '/js/mandates.js',
		array( 'iim-mandates-data', 'iim-main' ),
		IIM_VERSION,
		true
	);

	// Not enqueued here on purpose. template-parts/mandates.php turns them on
	// where it is rendered, which cannot get out of step with the markup the
	// way a page-template or slug test can.
}
add_action( 'wp_enqueue_scripts', 'iim_assets' );


/**
 * Preload the display face.
 *
 * Fonts are self-hosted in the theme, so there is no third-party connection
 * to open and nothing to consent to. Only the one face used above the fold is
 * preloaded; preloading all four would compete with the stylesheet.
 */
function iim_preload_fonts() {
	printf(
		'<link rel="preload" href="%s/assets/fonts/outfit-latin.woff2" as="font" type="font/woff2" crossorigin>' . "\n",
		esc_url( get_template_directory_uri() )
	);
}
add_action( 'wp_head', 'iim_preload_fonts', 1 );


/* -------------------------------------------------------------------------
 * Helpers used by the templates
 * ---------------------------------------------------------------------- */

/**
 * Permalink for one of the theme's pages, by slug.
 *
 * The static build linked to about.html, mandates.html and contact.html.
 * Those filenames do not exist on WordPress, and hardcoding /about/ breaks
 * on a subdirectory install or if the slug is ever changed. This resolves the
 * real permalink and falls back to a sensible guess if the page has not been
 * created yet.
 *
 * @param string $slug     Page slug: 'about', 'track-record', 'contact'.
 * @param string $fragment Optional '#anchor' to append.
 * @return string
 */
function iim_page_url( $slug, $fragment = '' ) {

	$page = get_page_by_path( $slug );

	if ( $page instanceof WP_Post ) {
		return get_permalink( $page ) . $fragment;
	}

	return home_url( '/' . $slug . '/' ) . $fragment;
}

/**
 * Which of the four pages is being viewed.
 *
 * Drives aria-current in the top bar and the footer, and the skip link.
 *
 * @return string '' for the home page, otherwise the slug.
 */
function iim_current_key() {

	if ( is_front_page() || is_home() ) {
		return '';
	}

	foreach ( iim_page_slugs() as $slug ) {
		if ( is_page( $slug ) ) {
			return $slug;
		}
	}

	// Assigned by template rather than matched by slug.
	foreach ( iim_page_slugs() as $slug ) {
		if ( is_page_template( 'page-' . $slug . '.php' ) ) {
			return $slug;
		}
	}

	return '';
}

/**
 * aria-current="page" for the link to $slug, or nothing.
 *
 * @param string $slug Page slug the link points at.
 * @return string
 */
function iim_current_attr( $slug ) {
	return iim_current_key() === $slug ? ' aria-current="page"' : '';
}

/**
 * Skip-link target and label for the current page.
 *
 * Each page had its own: the home page skips to the practice section, the
 * track record skips past the filters to the results.
 *
 * @return array{0:string,1:string}
 */
function iim_skip_link() {

	switch ( iim_current_key() ) {
		case 'track-record':
			return array( '#results', __( 'Skip to mandates', 'iim' ) );
		case '':
			return array( '#practice', __( 'Skip to content', 'iim' ) );
		default:
			return array( '#main', __( 'Skip to content', 'iim' ) );
	}
}

/**
 * Print the theme's own nav markup, or a WordPress menu if one is assigned.
 *
 * Keeping the hand-written markup as the default is deliberate: the top bar
 * is a flex row of bare <a> elements with no <ul>, and wp_nav_menu's default
 * list markup would need CSS changes to match. Assign a menu only if you need
 * editable navigation, and expect to add a little CSS for the list wrapper.
 *
 * @param string $location 'primary' or 'footer'.
 * @return bool True if a WordPress menu was printed.
 */
function iim_nav( $location ) {

	if ( ! has_nav_menu( $location ) ) {
		return false;
	}

	wp_nav_menu(
		array(
			'theme_location' => $location,
			'container'      => false,
			'items_wrap'     => '%3$s',
			'depth'          => 1,
			'fallback_cb'    => false,
		)
	);

	return true;
}


/* -------------------------------------------------------------------------
 * Setup check
 *
 * This theme replaces an existing site, so activating it is not enough: the
 * pages have to point at its templates. Until they do, WordPress falls back to
 * page.php and renders whatever is in the editor, which looks like "the fonts
 * changed but the layout did not". This notice says so out loud instead of
 * leaving it to be discovered.
 * ---------------------------------------------------------------------- */

function iim_setup_notice() {

	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	$todo = array();

	// 1. Static front page.
	if ( 'page' !== get_option( 'show_on_front' ) ) {
		$todo[] = sprintf(
			/* translators: %s: link to the Reading settings screen. */
			__( 'Set <a href="%s">Settings &rarr; Reading</a> to "A static page" and choose a page for Homepage. Its content is ignored; the theme supplies the home page.', 'iim' ),
			esc_url( admin_url( 'options-reading.php' ) )
		);
	}

	// 2. One page per template, each actually using it.
	foreach ( iim_pages() as $slug => $info ) {

		// In hybrid mode only the page's existence matters; Elementor supplies
		// the layout, so which template it uses is not the theme's business.
		if ( ! IIM_THEME_OWNS_PAGES ) {
			if ( ! get_page_by_path( $slug ) instanceof WP_Post ) {
				$todo[] = sprintf(
					/* translators: %s: page slug. */
					__( 'No page at the slug <code>%s</code>. The navigation links to it, so it returns 404 until the page exists.', 'iim' ),
					esc_html( $slug )
				);
			}
			continue;
		}

		$label = 'IIM ' . $info[0];

		$page = get_page_by_path( $slug );

		if ( ! $page instanceof WP_Post ) {
			// Is the template assigned to some other page instead?
			$assigned = get_pages(
				array(
					'meta_key'   => '_wp_page_template',
					'meta_value' => 'page-' . $slug . '.php',
					'number'     => 1,
				)
			);
			if ( empty( $assigned ) ) {
				$todo[] = sprintf(
					/* translators: 1: page slug, 2: template name. */
					__( 'No page with the slug <code>%1$s</code>, and nothing using the "%2$s" template. Create the page, or assign the template to an existing one under Page Attributes.', 'iim' ),
					esc_html( $slug ),
					esc_html( $label )
				);
			}
			continue;
		}

		$template = get_page_template_slug( $page->ID );

		if ( '' !== $template && 'page-' . $slug . '.php' !== $template ) {
			$todo[] = sprintf(
				/* translators: 1: page title, 2: template name. */
				__( '<a href="%3$s">%1$s</a> is set to a different template. Set it to "%2$s", or to Default.', 'iim' ),
				esc_html( $page->post_title ),
				esc_html( $label ),
				esc_url( get_edit_post_link( $page->ID ) )
			);
		}
	}

	// 3. Elementor, if it is installed, competing for the same pages.
	if ( iim_has_elementor() ) {

		$overridden = iim_elementor_overridden_pages();

		if ( ! empty( $overridden ) ) {
			$todo[] = sprintf(
				/* translators: %s: comma-separated page titles. */
				__( 'These pages are set to an Elementor page template, so Elementor renders them and this theme never runs: <strong>%s</strong>. Edit each one and set Page Attributes &rarr; Template back to <em>Default</em>.', 'iim' ),
				esc_html( implode( ', ', $overridden ) )
			);
		}

		if ( iim_elementor_theme_parts( 'header' ) > 0 || iim_elementor_theme_parts( 'footer' ) > 0 ) {
			$todo[] = __( 'Elementor Theme Builder has a published header or footer template. It will replace or duplicate the top bar and footer supplied by this theme. Under Templates &rarr; Theme Builder, set those templates to draft or delete their display conditions.', 'iim' );
		}
	}

	if ( empty( $todo ) ) {
		return;
	}

	echo '<div class="notice notice-warning"><p><strong>'
		. esc_html__( 'Infrastructure in Motion theme: setup is not finished.', 'iim' )
		. '</strong> '
		. esc_html__( 'Until these are done, pages fall back to their editor content and the layout will not appear.', 'iim' )
		. '</p><ol>';

	foreach ( $todo as $item ) {
		// Built above from translated strings with escaped values in them.
		echo '<li>' . wp_kses( $item, array(
			'a'      => array( 'href' => array() ),
			'code'   => array(),
			'strong' => array(),
			'em'     => array(),
		) ) . '</li>';
	}

	echo '</ol>';

	// If the only thing missing is the pages themselves, offer to make them.
	$missing = false;
	foreach ( iim_pages() as $slug => $info ) {
		if ( ! get_page_by_path( $slug ) instanceof WP_Post ) {
			$missing = true;
			break;
		}
	}

	if ( $missing ) {
		printf(
			'<p><a class="button button-primary" href="%s">%s</a> <span class="description">%s</span></p>',
			esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=iim_create_pages' ), 'iim_create_pages' ) ),
			esc_html__( 'Create the missing pages', 'iim' ),
			esc_html__( 'Adds only the pages that are absent, each with its template already assigned. Existing pages are left alone.', 'iim' )
		);
	}

	echo '</div>';
}
add_action( 'admin_notices', 'iim_setup_notice' );


/* -------------------------------------------------------------------------
 * Elementor compatibility
 *
 * The site this theme replaces was built with Elementor, and Elementor can
 * override a theme in two ways that both look like "the theme did not apply":
 *
 *   1. A page set to an Elementor page template (Canvas, or Full Width) takes
 *      the template away from the theme entirely. Its markup never runs.
 *   2. Elementor Pro's Theme Builder can supply its own header and footer,
 *      which either replace or duplicate the ones in header.php / footer.php.
 *
 * Neither is a bug in either product; they are just two things competing to
 * own the same page. The checks below name whichever one is happening.
 * ---------------------------------------------------------------------- */

/**
 * Is Elementor running?
 *
 * @return bool
 */
function iim_has_elementor() {
	return did_action( 'elementor/loaded' ) > 0 || class_exists( '\Elementor\Plugin' );
}

/**
 * Published Elementor Theme Builder templates of a given type.
 *
 * @param string $type 'header' or 'footer'.
 * @return int Count.
 */
function iim_elementor_theme_parts( $type ) {

	if ( ! post_type_exists( 'elementor_library' ) ) {
		return 0;
	}

	$q = new WP_Query(
		array(
			'post_type'              => 'elementor_library',
			'post_status'            => 'publish',
			'posts_per_page'         => 1,
			'fields'                 => 'ids',
			'no_found_rows'          => false,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'meta_query'             => array(
				array(
					'key'   => '_elementor_template_type',
					'value' => $type,
				),
			),
		)
	);

	return (int) $q->found_posts;
}

/**
 * Page templates that hand the page to Elementor instead of the theme.
 *
 * @return array List of page titles.
 */
function iim_elementor_overridden_pages() {

	$hijacked = array( 'elementor_canvas', 'elementor_header_footer', 'elementor_theme' );
	$found    = array();

	$ids = array( (int) get_option( 'page_on_front' ) );
	foreach ( iim_page_slugs() as $slug ) {
		$page = get_page_by_path( $slug );
		if ( $page instanceof WP_Post ) {
			$ids[] = $page->ID;
		}
	}

	foreach ( array_filter( $ids ) as $id ) {
		if ( in_array( get_page_template_slug( $id ), $hijacked, true ) ) {
			$found[] = get_the_title( $id );
		}
	}

	return $found;
}


/* -------------------------------------------------------------------------
 * Template lock
 *
 * Elementor's "Canvas" and "Full Width" page templates are ordinary WordPress
 * page templates: Elementor registers them through the theme_page_templates
 * filter and then claims the page through template_include. Whichever filter
 * runs last wins, so this one runs at priority 999 and hands the four pages
 * back to the theme no matter what is stored against them.
 *
 * This is deliberately blunt. It means those four pages cannot be edited with
 * Elementor at all, which is the point: they are hand-built and there is no
 * Elementor representation of them to fall back to. Every other page on the
 * site is untouched and keeps working through page.php and the_content().
 *
 * TO TURN IT OFF for one page, or while migrating, add this to wp-config.php:
 *     define( 'IIM_LET_ELEMENTOR_WIN', true );
 * ---------------------------------------------------------------------- */

function iim_force_template( $template ) {

	// Off in hybrid mode: Elementor is meant to lay these pages out.
	if ( ! IIM_THEME_OWNS_PAGES ) {
		return $template;
	}

	// Escape hatch, so this is never a one-way door.
	if ( defined( 'IIM_LET_ELEMENTOR_WIN' ) && IIM_LET_ELEMENTOR_WIN ) {
		return $template;
	}

	// Never interfere with the editor, previews, feeds or REST.
	if ( is_admin() || is_feed() || is_preview() ) {
		return $template;
	}

	$forced = '';

	if ( is_front_page() ) {
		$forced = 'front-page.php';
	} else {
		foreach ( iim_pages() as $slug => $info ) {
			$file = $info[1];
			// is_page() matches slug, ID or title; the template may also be
			// assigned to a page under a different slug.
			if ( is_page( $slug ) || is_page_template( $file ) ) {
				$forced = $file;
				break;
			}
		}
	}

	if ( '' === $forced ) {
		return $template;
	}

	$path = get_theme_file_path( $forced );

	return file_exists( $path ) ? $path : $template;
}
add_filter( 'template_include', 'iim_force_template', 999 );


/**
 * Hide Elementor's page templates from the dropdown on the four owned pages.
 *
 * The lock above already ignores them, but leaving them selectable invites
 * someone to choose one, see no change, and assume the site is broken.
 *
 * @param array $templates Page templates offered in Page Attributes.
 * @return array
 */
function iim_filter_page_templates( $templates ) {

	if ( ! IIM_THEME_OWNS_PAGES || ( defined( 'IIM_LET_ELEMENTOR_WIN' ) && IIM_LET_ELEMENTOR_WIN ) ) {
		return $templates;
	}

	$screen_id = 0;
	if ( isset( $_GET['post'] ) ) {
		$screen_id = (int) $_GET['post'];
	} elseif ( isset( $_POST['post_ID'] ) ) {
		$screen_id = (int) $_POST['post_ID'];
	}

	if ( ! $screen_id ) {
		return $templates;
	}

	$owned = array( (int) get_option( 'page_on_front' ) );
	foreach ( iim_page_slugs() as $slug ) {
		$page = get_page_by_path( $slug );
		if ( $page instanceof WP_Post ) {
			$owned[] = $page->ID;
		}
	}

	if ( ! in_array( $screen_id, array_filter( $owned ), true ) ) {
		return $templates;
	}

	unset(
		$templates['elementor_canvas'],
		$templates['elementor_header_footer'],
		$templates['elementor_theme']
	);

	return $templates;
}
add_filter( 'theme_page_templates', 'iim_filter_page_templates', 999 );


/* -------------------------------------------------------------------------
 * Taking the header and footer back from Elementor
 *
 * Elementor Pro's Theme Builder replaces a theme header and footer by hooking
 * the get_header and get_footer ACTIONS, then buffering away whatever the
 * theme printed. Those actions only fire when get_header() and get_footer()
 * are called, so the templates call iim_header() and iim_footer() instead,
 * which load the files directly. No action fires, so there is nothing for
 * Elementor to hook, and no Elementor internals are relied on.
 *
 * wp_head(), wp_body_open() and wp_footer() still run normally from inside
 * header.php and footer.php, so every plugin that injects anything still gets
 * its chance. Only the get_header / get_footer actions are skipped, and almost
 * nothing legitimate uses those for output.
 *
 * TO GO BACK to stock behaviour, in wp-config.php:
 *     define( 'IIM_LET_ELEMENTOR_WIN', true );
 * ---------------------------------------------------------------------- */

/**
 * Load header.php without firing the get_header action.
 */
function iim_header() {

	if ( ! IIM_THEME_OWNS_CHROME || ( defined( 'IIM_LET_ELEMENTOR_WIN' ) && IIM_LET_ELEMENTOR_WIN ) ) {
		get_header();
		return;
	}

	// get_header() loads the file in global scope; a plain require inside a
	// function would not, so the usual globals are imported by hand.
	global $post, $wp_query, $wp_the_query, $wp, $wp_rewrite, $wpdb, $paged, $authordata;

	require get_theme_file_path( 'header.php' );
}

/**
 * Load footer.php without firing the get_footer action.
 */
function iim_footer() {

	if ( ! IIM_THEME_OWNS_CHROME || ( defined( 'IIM_LET_ELEMENTOR_WIN' ) && IIM_LET_ELEMENTOR_WIN ) ) {
		get_footer();
		return;
	}

	global $post, $wp_query, $wp_the_query, $wp, $wp_rewrite, $wpdb, $paged, $authordata;

	require get_theme_file_path( 'footer.php' );
}


/**
 * Put the photography switch on <body> from here rather than from header.php.
 *
 * It used to be set inline in header.php. That meant anything which replaced
 * the header also replaced the body tag, dropping the class with it, and the
 * page then rendered its text correctly with every photograph hidden, because
 * .proj__media defaults to display:none and the hero overlay only exists under
 * .with-photos. Setting it through the filter makes it survive whoever ends up
 * printing the tag, as long as they call body_class() at all.
 *
 * @param array $classes Body classes.
 * @return array
 */
function iim_body_class( $classes ) {

	if ( is_front_page() && ! in_array( 'with-photos', $classes, true ) ) {
		$classes[] = 'with-photos';
	}

	return $classes;
}
add_filter( 'body_class', 'iim_body_class' );


/* -------------------------------------------------------------------------
 * The three interior pages
 *
 * One source of truth for slug, title and template. The navigation, the
 * template lock, the setup notice and the page creator all read it, so
 * pointing the theme at differently named pages is a single edit here.
 *
 * If the site already has, say, an About page at the slug "about-us", change
 * the key below to "about-us" and everything follows.
 * ---------------------------------------------------------------------- */

function iim_pages() {
	return array(
		// slug          title              template
		'track-record' => array( 'Track Record', 'page-track-record.php' ),
		'about'        => array( 'About us',     'page-about.php' ),
		'contact'      => array( 'Contact us',   'page-contact.php' ),
	);
}

/**
 * Slugs from iim_pages(), in order.
 *
 * @return array
 */
function iim_page_slugs() {
	return array_keys( iim_pages() );
}


/* -------------------------------------------------------------------------
 * One-click page creation
 *
 * The navigation links to these three pages by slug. Until the pages exist
 * WordPress has nothing to serve and returns 404, which is what a fresh
 * install of this theme looks like on a site that did not already use those
 * slugs. Rather than a list of manual steps, the setup notice offers a button.
 *
 * Creation is explicit, never automatic on activation: this theme is being
 * installed over a site that already has pages, and silently adding more is
 * not something a theme should do behind your back.
 * ---------------------------------------------------------------------- */

function iim_create_missing_pages() {

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to do that.', 'iim' ) );
	}

	check_admin_referer( 'iim_create_pages' );

	$made = array();

	foreach ( iim_pages() as $slug => $info ) {

		list( $title, $template ) = $info;

		$existing = get_page_by_path( $slug );

		if ( $existing instanceof WP_Post ) {
			// In hybrid mode Elementor lays the page out, so leave the
			// template alone; only claim it when the theme owns pages.
			if ( IIM_THEME_OWNS_PAGES && get_page_template_slug( $existing->ID ) !== $template ) {
				update_post_meta( $existing->ID, '_wp_page_template', $template );
				$made[] = $slug . ' (template assigned)';
			}
			continue;
		}

		$meta = IIM_THEME_OWNS_PAGES
			? array( '_wp_page_template' => $template )
			: array();

		$id = wp_insert_post(
			array(
				'post_title'   => $title,
				'post_name'    => $slug,
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_content' => '',
				'meta_input'   => $meta,
			)
		);

		if ( $id && ! is_wp_error( $id ) ) {
			$made[] = $slug . ' (created)';
		}
	}

	// A new page needs the rewrite rules refreshed or it can still 404.
	flush_rewrite_rules( false );

	wp_safe_redirect(
		add_query_arg(
			'iim_made',
			rawurlencode( implode( ', ', $made ) ),
			admin_url( 'themes.php' )
		)
	);
	exit;
}
add_action( 'admin_post_iim_create_pages', 'iim_create_missing_pages' );


/**
 * Confirmation after the button has run.
 */
function iim_created_notice() {

	if ( empty( $_GET['iim_made'] ) ) {
		return;
	}

	$made = sanitize_text_field( rawurldecode( wp_unslash( $_GET['iim_made'] ) ) );

	printf(
		'<div class="notice notice-success is-dismissible"><p>%s %s</p></div>',
		esc_html__( 'Infrastructure in Motion theme:', 'iim' ),
		'' === $made
			? esc_html__( 'every page was already in place.', 'iim' )
			: esc_html( $made )
	);
}
add_action( 'admin_notices', 'iim_created_notice' );


/* -------------------------------------------------------------------------
 * Status panel: Appearance > IIM Theme Status
 *
 * Two rounds of "it still shows the old design" came down to not being able to
 * see which page WordPress was actually matching. This prints the truth: every
 * page the theme expects, whether it exists, what template is stored against
 * it, and which file will render it.
 * ---------------------------------------------------------------------- */

function iim_status_menu() {
	add_theme_page(
		__( 'IIM Theme Status', 'iim' ),
		__( 'IIM Theme Status', 'iim' ),
		'edit_theme_options',
		'iim-status',
		'iim_status_screen'
	);
}
add_action( 'admin_menu', 'iim_status_menu' );

function iim_status_screen() {

	echo '<div class="wrap"><h1>' . esc_html__( 'IIM Theme Status', 'iim' ) . '</h1>';

	// ---- mode ----
	printf(
		'<p><strong>%s</strong> %s<br><strong>%s</strong> %s</p>',
		'IIM_THEME_OWNS_PAGES:',
		IIM_THEME_OWNS_PAGES
			? esc_html__( 'true - the theme renders its own pages and Elementor cannot override them.', 'iim' )
			: esc_html__( 'false - Elementor lays the pages out. If you see the old design, this is why.', 'iim' ),
		'IIM_THEME_OWNS_CHROME:',
		IIM_THEME_OWNS_CHROME
			? esc_html__( 'true - the IIM top bar and footer are used.', 'iim' )
			: esc_html__( 'false - another plugin supplies the header and footer.', 'iim' )
	);

	// ---- the front page ----
	$front_id = (int) get_option( 'page_on_front' );
	echo '<h2>' . esc_html__( 'Home page', 'iim' ) . '</h2><table class="widefat striped"><tbody>';
	printf(
		'<tr><td>%s</td><td>%s</td></tr>',
		esc_html__( 'Reading setting', 'iim' ),
		'page' === get_option( 'show_on_front' )
			? esc_html__( 'static page - correct', 'iim' )
			: '<strong>' . esc_html__( 'showing latest posts - set Settings > Reading to a static page', 'iim' ) . '</strong>'
	);
	printf(
		'<tr><td>%s</td><td>%s</td></tr>',
		esc_html__( 'Front page', 'iim' ),
		$front_id
			? esc_html( get_the_title( $front_id ) ) . ' (ID ' . (int) $front_id . ', template: '
				. esc_html( get_page_template_slug( $front_id ) ? get_page_template_slug( $front_id ) : 'default' ) . ')'
			: esc_html__( 'not set', 'iim' )
	);
	printf(
		'<tr><td>%s</td><td><code>%s</code></td></tr>',
		esc_html__( 'Will render with', 'iim' ),
		IIM_THEME_OWNS_PAGES ? 'front-page.php' : esc_html__( 'whatever Elementor decides', 'iim' )
	);
	echo '</tbody></table>';

	// ---- the interior pages ----
	echo '<h2>' . esc_html__( 'Interior pages', 'iim' ) . '</h2>';
	echo '<table class="widefat striped"><thead><tr>'
		. '<th>' . esc_html__( 'Expected slug', 'iim' ) . '</th>'
		. '<th>' . esc_html__( 'Exists', 'iim' ) . '</th>'
		. '<th>' . esc_html__( 'Stored template', 'iim' ) . '</th>'
		. '<th>' . esc_html__( 'Will render with', 'iim' ) . '</th>'
		. '</tr></thead><tbody>';

	foreach ( iim_pages() as $slug => $info ) {

		$page   = get_page_by_path( $slug );
		$exists = $page instanceof WP_Post;
		$stored = $exists ? get_page_template_slug( $page->ID ) : '';

		printf(
			'<tr><td><code>%s</code></td><td>%s</td><td>%s</td><td><code>%s</code></td></tr>',
			esc_html( $slug ),
			$exists
				? esc_html__( 'yes', 'iim' ) . ' (ID ' . (int) $page->ID . ')'
				: '<strong>' . esc_html__( 'NO - the menu link will 404', 'iim' ) . '</strong>',
			esc_html( '' === $stored ? 'default' : $stored ),
			$exists
				? esc_html( IIM_THEME_OWNS_PAGES ? $info[1] : ( '' === $stored ? 'page.php' : $stored ) )
				: '-'
		);
	}
	echo '</tbody></table>';

	// ---- every page on the site, so mismatched slugs are visible ----
	echo '<h2>' . esc_html__( 'All pages on this site', 'iim' ) . '</h2>';
	echo '<p class="description">'
		. esc_html__( 'If one of these is your real About or Track Record page under a different slug, either rename its slug to match the list above, or edit iim_pages() in functions.php.', 'iim' )
		. '</p><table class="widefat striped"><thead><tr>'
		. '<th>' . esc_html__( 'Title', 'iim' ) . '</th>'
		. '<th>' . esc_html__( 'Slug', 'iim' ) . '</th>'
		. '<th>' . esc_html__( 'Template', 'iim' ) . '</th>'
		. '</tr></thead><tbody>';

	foreach ( get_pages( array( 'number' => 60 ) ) as $p ) {
		$t = get_page_template_slug( $p->ID );
		printf(
			'<tr><td><a href="%s">%s</a></td><td><code>%s</code></td><td>%s</td></tr>',
			esc_url( (string) get_edit_post_link( $p->ID ) ),
			esc_html( $p->post_title ),
			esc_html( $p->post_name ),
			esc_html( '' === $t ? 'default' : $t )
		);
	}
	echo '</tbody></table>';

	printf(
		'<p><a class="button button-primary" href="%s">%s</a></p>',
		esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=iim_create_pages' ), 'iim_create_pages' ) ),
		esc_html__( 'Create or repair the theme pages', 'iim' )
	);

	echo '</div>';
}
