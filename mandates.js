/* Mandates page: build the filter controls from the data, filter on input,
   and expand any card for its full description.
   Data comes from mandates-data.js (window.IIM_MANDATES / window.IIM_SECTORS). */
(function () {
  'use strict';

  var DATA = window.IIM_MANDATES || [];
  var SECTORS = window.IIM_SECTORS || [];

  var grid = document.getElementById('results-grid');
  var empty = document.getElementById('results-empty');
  var count = document.getElementById('count');
  var noteEl = document.getElementById('sector-note');
  var clearBtn = document.getElementById('f-clear');
  var searchEl = document.getElementById('f-search');
  var countryEl = document.getElementById('f-country');
  var sectorWrap = document.getElementById('f-sector');
  var typeWrap = document.getElementById('f-type');
  var sumEls = {
    mandates: document.getElementById('sum-mandates'),
    countries: document.getElementById('sum-countries'),
    sectors: document.getElementById('sum-sectors'),
  };
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  if (!grid || !DATA.length) return;

  /* Filter state lives in the URL so a filtered view can be sent to someone.
     Unrecognised values are ignored rather than yielding an empty page. */
  var params = new URLSearchParams(window.location.search);
  var wantedSector = params.get('sector') || '';
  var sectorExists = SECTORS.some(function (s) { return s.sector === wantedSector; });
  var wantedType = params.get('client') || '';

  var state = {
    q: params.get('q') || '',
    country: params.get('country') || '',
    sector: sectorExists ? wantedSector : '',
    type: (wantedType === 'Public' || wantedType === 'Private') ? wantedType : '',
    // Card grid or full-width detail rows. A view preference, not a filter,
    // so it stays out of the URL and survives Clear filters.
    detail: false,
  };

  function syncUrl() {
    var q = new URLSearchParams();
    if (state.q) q.set('q', state.q);
    if (state.country) q.set('country', state.country);
    if (state.sector) q.set('sector', state.sector);
    if (state.type) q.set('client', state.type);
    var qs = q.toString();
    history.replaceState(null, '',
      window.location.pathname + (qs ? '?' + qs : ''));
  }

  /* ---- Build the controls the data implies ---------------------------- */

  var countries = [];
  DATA.forEach(function (m) {
    m.countries.forEach(function (c) {
      if (countries.indexOf(c) === -1) countries.push(c);
    });
  });
  countries.sort();
  countries.forEach(function (c) {
    var o = document.createElement('option');
    o.value = c;
    o.textContent = c;
    countryEl.appendChild(o);
  });

  SECTORS.forEach(function (s) {
    var n = DATA.filter(function (m) { return m.sector === s.sector; }).length;
    var b = document.createElement('button');
    b.type = 'button';
    b.className = 'chip';
    b.dataset.value = s.sector;
    b.setAttribute('aria-pressed', 'false');
    b.innerHTML = escapeHtml(s.sector) + ' <span class="chip__n">' + n + '</span>';
    sectorWrap.appendChild(b);
  });

  /* ---- Rendering ------------------------------------------------------ */

  function escapeHtml(str) {
    return String(str).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }

  function cardHtml(m, i) {
    var hasDetail = !!m.description;
    var panelId = 'panel-' + m.id;
    // .mandate__main groups everything that is always on show. In detail view
    // the card becomes a two-track grid, with this wrapper in the first track
    // and the panel in the other two, so the description reads alongside the
    // mandate rather than under it.
    //
    // The panel is written for every mandate, including the four with no
    // description, so detail view has one uniform shape. It carries the hidden
    // attribute in card view rather than being styled away, so a screen reader
    // is told the same thing the page shows.
    return '' +
      '<li class="mandate reveal" style="--i:' + (i % 9) + '">' +
        '<div class="mandate__main">' +
          '<p class="mandate__sector">' + escapeHtml(m.sector) + '</p>' +
          '<h2 class="mandate__title">' + escapeHtml(m.project) + '</h2>' +
          '<dl class="mandate__facts">' +
            '<dt>Client</dt><dd>' + escapeHtml(m.client) + '</dd>' +
            '<dt>Geography</dt><dd>' + escapeHtml(m.geography) + '</dd>' +
            '<dt>Counterparty</dt><dd>' + escapeHtml(m.clientType) + '</dd>' +
          '</dl>' +
        '</div>' +
        '<div class="mandate__panel" id="' + panelId + '"' +
             (state.detail ? '' : ' hidden') + '>' +
          (hasDetail
            ? '<p>' + escapeHtml(m.description) + '</p>'
            : '<p class="mandate__nodetail">Detail available on request.</p>') +
        '</div>' +
      '</li>';
  }

  // Count the visible figure toward a new value. Cancels any tween already
  // running on that element so fast filtering cannot leave a stale number.
  var tweens = {}, timers = {};
  function tweenTo(el, key, to) {
    if (!el) return;
    if (tweens[key]) { cancelAnimationFrame(tweens[key]); tweens[key] = null; }

    var from = parseInt(el.textContent, 10);
    if (isNaN(from)) from = 0;

    /* Record the authoritative target on the element. A frame callback that
       was scheduled before the latest filter can still fire afterwards (it
       happens whenever frames are throttled, for instance while the element
       sits in a hidden container), and without this guard that stale frame
       finishes writing the PREVIOUS count over the current one, leaving the
       figure permanently one filter behind. */
    el.dataset.target = String(to);

    if (reduce || from === to) { el.textContent = to; return; }

    var start = null, dur = 420;
    function step(now) {
      if (el.dataset.target !== String(to)) return;   // superseded
      if (start === null) start = now;
      var t = Math.min((now - start) / dur, 1);
      var eased = 1 - Math.pow(1 - t, 3);
      if (t < 1) {
        el.textContent = Math.round(from + (to - from) * eased);
        tweens[key] = requestAnimationFrame(step);
      } else {
        el.textContent = to;                          // always land exactly
        tweens[key] = null;
      }
    }
    tweens[key] = requestAnimationFrame(step);

    /* Backstop. Frame callbacks can be starved (a loaded machine, a background
       tab, a hidden container), and a starved tween would leave the figure
       parked at a wrong intermediate number for as long as the starvation
       lasts. Timers keep running when frames do not, so this guarantees the
       figure reaches its target whatever the frame rate does. */
    if (timers[key]) clearTimeout(timers[key]);
    timers[key] = setTimeout(function () {
      if (el.dataset.target === String(to)) el.textContent = to;
    }, dur + 60);
  }

  function matches(m) {
    if (state.sector && m.sector !== state.sector) return false;
    if (state.type && m.clientType !== state.type) return false;
    if (state.country && m.countries.indexOf(state.country) === -1) return false;
    if (state.q) {
      var hay = (m.project + ' ' + m.client + ' ' + m.geography + ' ' +
                 m.sector + ' ' + m.description).toLowerCase();
      // Every word typed must appear somewhere in the record.
      var words = state.q.toLowerCase().split(/\s+/).filter(Boolean);
      for (var i = 0; i < words.length; i++) {
        if (hay.indexOf(words[i]) === -1) return false;
      }
    }
    return true;
  }

  function render() {
    var rows = DATA.filter(matches);

    grid.innerHTML = rows.map(cardHtml).join('');
    empty.hidden = rows.length > 0;

    count.textContent = rows.length === DATA.length
      ? DATA.length + ' mandates'
      : rows.length + ' of ' + DATA.length + ' mandates';

    // The shape of what is on screen, not just how many.
    var seenCountries = {}, seenSectors = {};
    rows.forEach(function (m) {
      m.countries.forEach(function (c) { seenCountries[c] = 1; });
      seenSectors[m.sector] = 1;
    });
    tweenTo(sumEls.mandates, 'mandates', rows.length);
    tweenTo(sumEls.countries, 'countries', Object.keys(seenCountries).length);
    tweenTo(sumEls.sectors, 'sectors', Object.keys(seenSectors).length);

    // Show the sector's own note only while that sector is the active filter.
    var note = '';
    if (state.sector) {
      SECTORS.forEach(function (s) { if (s.sector === state.sector) note = s.note; });
    }
    noteEl.textContent = note;
    noteEl.hidden = !note;

    var filtering = !!(state.q || state.country || state.sector || state.type);
    clearBtn.hidden = !filtering;

    syncUrl();

    // Newly rendered cards start hidden under the reveal rule, so show them.
    if (document.documentElement.classList.contains('js')) {
      requestAnimationFrame(function () {
        [].forEach.call(grid.querySelectorAll('.reveal'), function (el) {
          el.classList.add('is-visible');
        });
      });
    }
  }

  /* ---- Wiring --------------------------------------------------------- */

  function setChips(wrap, value) {
    [].forEach.call(wrap.querySelectorAll('.chip'), function (b) {
      var on = b.dataset.value === value;
      b.classList.toggle('is-on', on);
      b.setAttribute('aria-pressed', String(on));
    });
  }

  sectorWrap.addEventListener('click', function (e) {
    var b = e.target.closest('.chip');
    if (!b) return;
    state.sector = b.dataset.value;
    setChips(sectorWrap, state.sector);
    render();
  });

  typeWrap.addEventListener('click', function (e) {
    var b = e.target.closest('.chip');
    if (!b) return;
    state.type = b.dataset.value;
    setChips(typeWrap, state.type);
    render();
  });

  countryEl.addEventListener('change', function () {
    state.country = countryEl.value;
    render();
  });

  var debounce;
  searchEl.addEventListener('input', function () {
    clearTimeout(debounce);
    debounce = setTimeout(function () {
      state.q = searchEl.value.trim();
      render();
    }, 140);
  });

  clearBtn.addEventListener('click', function () {
    // Clear the filters, keep the view: someone reading in detail who narrows
    // the list and then clears it should not be thrown back into cards.
    state = { q: '', country: '', sector: '', type: '', detail: state.detail };
    searchEl.value = '';
    countryEl.value = '';
    setChips(sectorWrap, '');
    setChips(typeWrap, '');
    render();
    searchEl.focus();
  });

  // One control for the whole list. Re-rendering rather than unhiding panels
  // in place keeps a single source of truth: cardHtml decides, from
  // state.detail, whether a panel is written hidden, so a filter change after
  // the switch cannot bring back cards in the wrong view.
  var detailBtn = document.getElementById('f-detail');
  if (detailBtn) {
    detailBtn.addEventListener('click', function () {
      state.detail = !state.detail;
      detailBtn.setAttribute('aria-pressed', String(state.detail));
      detailBtn.querySelector('.viewbtn__label').textContent =
        state.detail ? 'Cards' : 'Detail';
      grid.classList.toggle('is-detail', state.detail);
      render();
    });
  }

  searchEl.value = state.q;
  countryEl.value = state.country;
  setChips(sectorWrap, state.sector);
  setChips(typeWrap, state.type);
  render();
})();
