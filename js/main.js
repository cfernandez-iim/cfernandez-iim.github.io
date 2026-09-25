/* Infrastructure in Motion - interaction layer.
   No window scroll listeners anywhere: position-dependent behaviour uses
   IntersectionObserver, and the progress bar is pure CSS. */
(function () {
  'use strict';

  var root = document.documentElement;
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var supportsIO = 'IntersectionObserver' in window;

  /* ---- Mobile menu ---------------------------------------------------- */
  var toggle = document.getElementById('nav-toggle');
  var links = document.getElementById('nav-links');

  if (toggle && links) {
    var setMenu = function (open) {
      toggle.setAttribute('aria-expanded', String(open));
      toggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
      links.classList.toggle('is-open', open);
    };
    toggle.addEventListener('click', function () {
      setMenu(toggle.getAttribute('aria-expanded') !== 'true');
    });
    // Any destination closes the panel behind you.
    links.addEventListener('click', function (e) {
      if (e.target.closest('a')) setMenu(false);
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') setMenu(false);
    });
  }


  /* ---- Mandate rail: buttons scroll by one card ----------------------- */
  var scroller = document.getElementById('work-scroller');
  if (scroller) {
    var buttons = [].slice.call(document.querySelectorAll('.rail-btn'));
    var cards = [].slice.call(scroller.querySelectorAll('.proj'));

    var step = function () {
      if (!cards.length) return scroller.clientWidth;
      var gap = parseFloat(getComputedStyle(scroller).columnGap) || 0;
      return cards[0].getBoundingClientRect().width + gap;
    };

    buttons.forEach(function (b) {
      b.addEventListener('click', function () {
        scroller.scrollBy({
          left: Number(b.dataset.dir) * step(),
          behavior: reduce ? 'auto' : 'smooth',
        });
      });
    });

    // On wide screens every card fits and the rail cannot scroll. Leaving two
    // permanently-disabled arrows on screen reads as broken, so hide them.
    var section = document.querySelector('.work');
    var syncRail = function () {
      var scrollable = scroller.scrollWidth - scroller.clientWidth > 2;
      section.classList.toggle('is-static', !scrollable);
    };
    syncRail();
    if ('ResizeObserver' in window) {
      new ResizeObserver(syncRail).observe(scroller);
    }

    // Disable an arrow when its end card is fully in view. Watching the end
    // cards avoids polling scroll position on every frame.
    if (supportsIO && cards.length > 1) {
      var edge = function (card, dir) {
        new IntersectionObserver(function (entries) {
          entries.forEach(function (entry) {
            buttons.forEach(function (b) {
              if (Number(b.dataset.dir) === dir) b.disabled = entry.intersectionRatio > 0.95;
            });
          });
        }, { root: scroller, threshold: [0, 0.95, 1] }).observe(card);
      };
      edge(cards[0], -1);
      edge(cards[cards.length - 1], 1);
    }
  }

  /* ---- Bar goes solid once the hero has passed ------------------------ */

  /* ---- Counted figures ------------------------------------------------ */
  var format = function (value, decimals) {
    return value.toFixed(decimals).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
  };

  var countUp = function (el) {
    var target = parseFloat(el.dataset.count);
    var decimals = Number(el.dataset.decimals || 0);
    var prefix = el.dataset.prefix || '';
    var suffix = el.dataset.suffix || '';
    var duration = 1400;
    var start = null;

    var frame = function (now) {
      if (start === null) start = now;
      var t = Math.min((now - start) / duration, 1);
      var eased = 1 - Math.pow(1 - t, 3);
      el.textContent = prefix + format(target * eased, decimals) + suffix;
      if (t < 1) requestAnimationFrame(frame);
    };
    requestAnimationFrame(frame);
  };

  var figures = [].slice.call(document.querySelectorAll('.stat__figure'));
  if (figures.length && supportsIO && !reduce) {
    var figureObserver = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        countUp(entry.target);
        figureObserver.unobserve(entry.target);
      });
    }, { threshold: 0.6 });
    figures.forEach(function (f) { figureObserver.observe(f); });
  }
  // Reduced motion and no-IO browsers keep the final values already in the HTML.

  /* ---- Scroll reveal -------------------------------------------------- */
  if (supportsIO) {
    root.classList.add('js');
    var revealObserver = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('is-visible');
        revealObserver.unobserve(entry.target);
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -6% 0px' });
    [].forEach.call(document.querySelectorAll('.reveal'), function (el) {
      revealObserver.observe(el);
    });
  }
})();
