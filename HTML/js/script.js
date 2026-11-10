
function toggleConsejo(header) {
  const card = header.parentElement;
  const open = card.classList.toggle('tarjeta_consejo_abierta');
  clearTimeout(card._settle);
  card.classList.remove('tarjeta_consejo_asentada');
  header.setAttribute('aria-expanded', open);
  if (open) card._settle = setTimeout(() => card.classList.add('tarjeta_consejo_asentada'), 520);
}

(function () {
  const $ = (s, c = document) => c.querySelector(s);
  const $$ = (s, c = document) => Array.from(c.querySelectorAll(s));
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const body = document.body;
  const isHome = body.classList.contains('pagina_inicio');
  const isSilk = body.classList.contains('pagina_silksong');
  const inPages = location.pathname.includes('/pages/');
  const base = inPages ? '' : 'pages/';

  function makeParticles() {
    if (reduceMotion) return;
    const wrap = document.createElement('div');
    wrap.className = 'particulas_contenedor';
    wrap.setAttribute('aria-hidden', 'true');
    const total = window.innerWidth < 700 ? 16 : 34;
    for (let i = 0; i < total; i++) {
      const p = document.createElement('span');
      const left = Math.random() * 100;
      const ember = isHome ? left > 50 : isSilk;
      const size = ember ? 3 + Math.random() * 5 : 4 + Math.random() * 9;
      p.className = 'particula ' + (ember ? 'particula_brasa' : 'particula_azul');
      p.style.left = left + '%';
      p.style.width = size + 'px';
      p.style.height = (ember ? size * 1.7 : size) + 'px';
      p.style.animationDuration = (ember ? 7 + Math.random() * 8 : 12 + Math.random() * 14) + 's';
      p.style.animationDelay = (-Math.random() * 20) + 's';
      p.style.setProperty('--dx', (Math.random() * 120 - 60) + 'px');
      p.style.setProperty('--o', (0.35 + Math.random() * 0.55).toFixed(2));
      wrap.appendChild(p);
    }
    body.prepend(wrap);
  }

  function setupCards() {
    $$('.tarjeta_consejo_cabecera').forEach(h => {
      h.setAttribute('role', 'button');
      h.setAttribute('tabindex', '0');
      h.setAttribute('aria-expanded', 'false');
      h.addEventListener('keydown', e => {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); toggleConsejo(h); }
      });
    });
  }

  function openCard(card, scroll) {
    if (!card) return;
    const h = $('.tarjeta_consejo_cabecera', card);
    if (!card.classList.contains('tarjeta_consejo_abierta')) toggleConsejo(h);
    if (scroll) {
      card.classList.remove('aparicion_scroll');
      card.classList.add('aparicion_visible');
      setTimeout(() => card.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth', block: 'center' }), 60);
      card.classList.add('tarjeta_consejo_destello');
      setTimeout(() => card.classList.remove('tarjeta_consejo_destello'), 1700);
    }
  }

  function openFromHash() {
    const id = decodeURIComponent(location.hash.slice(1));
    if (!id) return;
    const card = document.getElementById(id);
    if (card && card.classList.contains('tarjeta_consejo')) openCard(card, true);
  }

  function setupTooltips() {
    $$('.tooltip_palabra').forEach(t => {
      t.addEventListener('click', e => {
        e.stopPropagation(); // no abre/cierra la tarjeta al tocar un tooltip
        const was = t.classList.contains('tooltip_visible');
        $$('.tooltip_palabra.tooltip_visible').forEach(o => o.classList.remove('tooltip_visible'));
        if (!was) t.classList.add('tooltip_visible');
      });
    });
    document.addEventListener('click', () => $$('.tooltip_palabra.tooltip_visible').forEach(o => o.classList.remove('tooltip_visible')));
  }

  function setupScroll() {
    const bar = $('#progress');
    const top = $('#to-top');
    const onScroll = () => {
      const h = document.documentElement;
      const max = h.scrollHeight - h.clientHeight;
      if (bar) bar.style.width = (max > 0 ? (h.scrollTop / max) * 100 : 0) + '%';
      if (top) top.classList.toggle('boton_subir_visible', h.scrollTop > 600);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
    const goTop = () => window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
    if (top) top.addEventListener('click', goTop);
    $$('.pie_boton_volver').forEach(b => b.addEventListener('click', goTop));
  }

  function setupReveal() {
    const els = $$('.aparicion_scroll');
    if (!('IntersectionObserver' in window)) { els.forEach(e => e.classList.add('aparicion_visible')); return; }
    const io = new IntersectionObserver(entries => {
      entries.forEach(en => {
        if (en.isIntersecting) { en.target.classList.add('aparicion_visible'); io.unobserve(en.target); }
      });
    }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });
    els.forEach(e => io.observe(e));
  }

  const norm = s => s.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');

  function setupToolbar() {
    const input = $('#search');
    if (!input) return;
    const cards = $$('.tarjeta_consejo');
    const count = $('#count');
    const none = $('#no-results');

    const update = () => {
      const q = norm(input.value.trim());
      let shown = 0;
      cards.forEach(c => {
        const ok = !q || norm(c.textContent).includes(q);
        c.hidden = !ok;
        if (ok) shown++;
      });
      body.classList.toggle('pagina_filtrando', !!q);
      count.textContent = q
        ? `Mostrando ${shown} de ${cards.length} consejos`
        : `${cards.length} consejos · toca uno para leerlo`;
      if (none) none.hidden = shown !== 0;
    };
    input.addEventListener('input', update);
    update();

    $('#expand-all').addEventListener('click', () => {
      cards.filter(c => !c.hidden && !c.classList.contains('tarjeta_consejo_abierta')).forEach(c => {
        c.classList.add('aparicion_visible');
        toggleConsejo($('.tarjeta_consejo_cabecera', c));
      });
    });
    $('#collapse-all').addEventListener('click', () => {
      cards.filter(c => c.classList.contains('tarjeta_consejo_abierta')).forEach(c => toggleConsejo($('.tarjeta_consejo_cabecera', c)));
    });
  }

  function setupCounters() {
    const nums = $$('[data-count]');
    if (!nums.length) return;
    const run = el => {
      const end = +el.dataset.count;
      if (reduceMotion) { el.textContent = end; return; }
      const t0 = performance.now(), dur = 1400;
      const step = t => {
        const k = Math.min((t - t0) / dur, 1);
        el.textContent = Math.round(end * (1 - Math.pow(1 - k, 3)));
        if (k < 1) requestAnimationFrame(step);
      };
      requestAnimationFrame(step);
    };
    const io = new IntersectionObserver(es => es.forEach(e => {
      if (e.isIntersecting) { run(e.target); io.unobserve(e.target); }
    }), { threshold: 0.5 });
    nums.forEach(n => io.observe(n));
  }

  const quotes = [
    { t: '¡El dash es para moverse con estilo, no para entrar en pánico!', a: 'Zote el Todopoderoso' },
    { t: 'Piensa antes de moverte, si es que tienes con qué pensar.', a: 'Zote el Todopoderoso' },
    { t: 'Yo, por supuesto, no muero, pero tú deberías gastarlo inmediatamente.', a: 'Zote el Todopoderoso' },
    { t: 'Mi victoria está escrita en las estrellas.', a: 'Zote el Todopoderoso' },
    { t: 'Ten los ojos bien abiertos en tus viajes. Este reino no perdona los descuidos.', a: 'Shakra' },
    { t: 'La verdadera gloria de Pharloom está en perderse y comprender este reino por tus propios medios.', a: 'Shakra' },
    { t: '¡Ve y escribe tu propia historia!', a: 'Shakra' }
  ];

  function setupQuotes() {
    const box = $('#quote-text');
    if (!box) return;
    const author = $('#quote-author');
    let last = -1;
    const show = () => {
      let i;
      do { i = Math.floor(Math.random() * quotes.length); } while (i === last);
      last = i;
      box.classList.add('frase_inicio_cambiando');
      setTimeout(() => {
        box.textContent = '“' + quotes[i].t + '”';
        author.textContent = '— ' + quotes[i].a;
        box.classList.remove('frase_inicio_cambiando');
      }, reduceMotion ? 0 : 320);
    };
    $('#quote-btn').addEventListener('click', show);
    show();
  }

  function randomAdvice() {
    const n = Math.ceil(Math.random() * 57);
    return n <= 27 ? base + 'hollow.html#zote-' + n : base + 'silksong.html#shakra-' + (n - 27);
  }
  function setupRandom() {
    $$('[data-random]').forEach(el => {
      el.addEventListener('click', e => {
        e.preventDefault();
        const url = randomAdvice();
        const samePage = url.split('#')[0] === (inPages ? location.pathname.split('/').pop() : '__none__');
        if (samePage) { location.hash = url.split('#')[1]; openFromHash(); }
        else location.href = url;
      });
    });
  }

  function setYear() { $$('.year').forEach(y => y.textContent = new Date().getFullYear()); }

  function init() {
    makeParticles();
    setupCards();
    setupTooltips();
    setupScroll();
    setupReveal();
    setupToolbar();
    setupCounters();
    setupQuotes();
    setupRandom();
    setYear();
    openFromHash();
    window.addEventListener('hashchange', openFromHash);
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();
})();
