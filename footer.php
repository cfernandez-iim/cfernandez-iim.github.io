<?php
/**
 * Footer: the footer block through the closing tags.
 *
 * Changes from the static build:
 *   - <script src="main.js"> and friends removed; enqueued in functions.php.
 *   - wp_footer() added immediately before </body>. Every plugin that injects
 *     anything depends on it, so it is not optional.
 *
 * @package iim
 */

$iim_uri = get_template_directory_uri();
?>

<footer class="foot">
  <div class="foot__inner">
    <img class="foot__logo" src="<?php echo esc_url( $iim_uri . '/assets/iim-logo.png' ); ?>" alt="Infrastructure in Motion"
         width="957" height="375" loading="lazy">
    <nav class="foot__nav" aria-label="Footer">
      <?php if ( ! iim_nav( 'footer' ) ) : ?>
      <a href="<?php echo esc_url( iim_page_url( 'about' ) ); ?>"<?php echo iim_current_attr( 'about' ); ?>>About us</a>
      <a href="<?php echo esc_url( is_front_page() ? '#practice' : home_url( '/#practice' ) ); ?>">Practice</a>
      <a href="<?php echo esc_url( iim_page_url( 'track-record' ) ); ?>"<?php echo iim_current_attr( 'track-record' ); ?>>Track Record</a>
      <a href="<?php echo esc_url( iim_page_url( 'contact' ) ); ?>"<?php echo iim_current_attr( 'contact' ); ?>>Contact us</a>
      <a href="mailto:advisory@infrastructureinmotion.com">Contact a partner</a>
      <?php endif; ?>
    </nav>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
