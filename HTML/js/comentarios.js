(function () {
  const form = document.getElementById('comment-form');
  const list = document.getElementById('comments-list');
  if (!form || !list) return;

  const cfg = window.COMENTARIOS_CONFIG || {};
  const remote = !!(cfg.projectId && cfg.apiKey);
  const MAX = 500, COOLDOWN = 30000, LS_LIST = 'comentarios-local', LS_LAST = 'comentarios-ultimo';
  const msg = document.getElementById('form-msg');
  const note = document.getElementById('comments-mode');
  const counter = document.getElementById('char-count');
  const area = form.elements.texto;
  const btn = form.querySelector('button[type="submit"]');

  const base = remote
    ? 'https://firestore.googleapis.com/v1/projects/' + encodeURIComponent(cfg.projectId) + '/databases/(default)/documents'
    : '';
  const key = remote ? '?key=' + encodeURIComponent(cfg.apiKey) : '';

  if (!remote && note) {
    note.hidden = false;
    note.textContent = 'Modo de prueba: por ahora los comentarios solo se ven en este navegador. Conecta Firebase para que todos los vean.';
  }

  const lsGet = (k, d) => { try { return JSON.parse(localStorage.getItem(k)) ?? d; } catch (e) { return d; } };
  const lsSet = (k, v) => { try { localStorage.setItem(k, JSON.stringify(v)); } catch (e) {} };

  function mask(email) {
    const [user, dom] = email.toLowerCase().split('@');
    return user.slice(0, user.length > 3 ? 3 : 1) + '***@' + dom;
  }
  const isGmail = e => /^[a-z0-9._%+-]+@(gmail|googlemail)\.com$/i.test(e);

  function ago(iso) {
    const d = new Date(iso); if (isNaN(d)) return '';
    const s = Math.max(0, (Date.now() - d) / 1000);
    if (s < 60) return 'hace un momento';
    if (s < 3600) return 'hace ' + Math.floor(s / 60) + ' min';
    if (s < 86400) return 'hace ' + Math.floor(s / 3600) + ' h';
    return d.toLocaleDateString('es', { day: 'numeric', month: 'short', year: 'numeric' });
  }

  function say(text, ok) {
    msg.textContent = text;
    msg.className = 'comentarios_mensaje_formulario ' + (ok ? 'comentarios_mensaje_ok' : 'comentarios_mensaje_error');
  }

  async function fetchComments() {
    if (!remote) return lsGet(LS_LIST, []);
    const r = await fetch(base + '/comentarios' + key + '&orderBy=' + encodeURIComponent('fecha desc') + '&pageSize=100');
    if (!r.ok) throw new Error('No se pudieron cargar');
    const j = await r.json();
    return (j.documents || []).map(d => ({
      autor: d.fields.autor?.stringValue || 'anónimo',
      texto: d.fields.texto?.stringValue || '',
      fecha: d.fields.fecha?.timestampValue || ''
    }));
  }

  async function saveComment(email, texto) {
    const fecha = new Date().toISOString();
    const autor = mask(email);
    if (!remote) {
      const all = lsGet(LS_LIST, []);
      all.unshift({ autor, texto, fecha });
      lsSet(LS_LIST, all.slice(0, 100));
      return;
    }
    const post = (col, fields) => fetch(base + '/' + col + key, {
      method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ fields })
    });
    const r = await post('comentarios', {
      autor: { stringValue: autor }, texto: { stringValue: texto }, fecha: { timestampValue: fecha }
    });
    if (!r.ok) throw new Error('No se pudo publicar');
    post('correos', { email: { stringValue: email.toLowerCase() }, fecha: { timestampValue: fecha } }).catch(() => {});
  }

  function render(items) {
    list.textContent = '';
    if (!items.length) {
      const p = document.createElement('p');
      p.className = 'comentarios_vacio';
      p.textContent = 'Todavía no hay comentarios. ¡Sé el primero en opinar!';
      list.appendChild(p);
      return;
    }
    items.forEach(c => {
      const art = document.createElement('article'); art.className = 'comentario_item panel_general';
      const av = document.createElement('div'); av.className = 'comentario_avatar'; av.textContent = (c.autor[0] || '?').toUpperCase();
      const body = document.createElement('div'); body.className = 'comentario_cuerpo';
      const head = document.createElement('div'); head.className = 'comentario_encabezado';
      const who = document.createElement('strong'); who.textContent = c.autor;
      const when = document.createElement('time'); when.textContent = ago(c.fecha);
      head.append(who, when);
      const txt = document.createElement('p'); txt.textContent = c.texto;
      body.append(head, txt); art.append(av, body); list.appendChild(art);
    });
  }

  async function load() {
    try { render(await fetchComments()); }
    catch (e) { list.textContent = ''; const p = document.createElement('p'); p.className = 'comentarios_vacio'; p.textContent = 'No se pudieron cargar los comentarios. Intenta de nuevo en un rato.'; list.appendChild(p); }
  }

  area.addEventListener('input', () => { counter.textContent = area.value.length + ' / ' + MAX; });

  form.addEventListener('submit', async ev => {
    ev.preventDefault();
    if (form.elements.web.value) return;        
    const email = form.elements.email.value.trim();
    const texto = area.value.trim();
    if (!isGmail(email)) return say('Escribe un Gmail válido (termina en @gmail.com).', false);
    if (texto.length < 3) return say('Escribe tu comentario antes de publicar.', false);
    if (texto.length > MAX) return say('El comentario es muy largo (máximo ' + MAX + ' caracteres).', false);
    const wait = COOLDOWN - (Date.now() - lsGet(LS_LAST, 0));
    if (wait > 0) return say('Espera ' + Math.ceil(wait / 1000) + ' s antes de publicar otro comentario.', false);

    btn.disabled = true; say('Publicando…', true);
    try {
      await saveComment(email, texto);
      lsSet(LS_LAST, Date.now());
      area.value = ''; counter.textContent = '0 / ' + MAX;
      say('¡Gracias! Tu comentario ya está publicado.', true);
      await load();
    } catch (e) {
      say('No se pudo publicar. Intenta de nuevo en un rato.', false);
    } finally { btn.disabled = false; }
  });

  load();
  if (remote) setInterval(load, 45000);
})();
