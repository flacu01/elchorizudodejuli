<?php $raiz = "../"; require __DIR__ . "/../includes/sesion.php"; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Zoteling Rush · Minijuego secreto</title>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/home.css">
  <style>
    .mj { max-width: 920px; margin: 0 auto; padding: 1.2rem; text-align: center; }
    .mj a { color: var(--accent); }
    .mj h1 { font-family: 'Cinzel', serif; margin: .6rem 0; }
    #arena { position: relative; width: 100%; max-width: 900px; margin: 0 auto; border: 1px solid var(--line); border-radius: 14px; overflow: hidden;
             background: #0b0d14 url('../Imagenes/imagenes_hollow/fondo-hollow2.webp') center/cover; }
    #c { display: block; width: 100%; height: auto; cursor: crosshair; touch-action: none; }
    #overlay { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: .6rem; background: rgba(5, 6, 12, .82); padding: 1rem; }
    #overlay h2 { font-family: 'Cinzel', serif; margin: 0; }
    #overlay p { margin: 0; max-width: 520px; }
    .mj_rank { max-width: 420px; margin: 1.2rem auto; text-align: left; }
    .mj_rank li { padding: .25rem 0; }
    .mj_rank span { float: right; color: var(--accent); }
  </style>
</head>
<body class="pagina_inicio">
  <main class="mj">
    <a href="hollow.php#zote-18">← Volver a Hollow Knight</a>
    <h1>Zoteling Rush</h1>
    <div id="arena">
      <canvas id="c" width="900" height="540"></canvas>
      <div id="overlay">
        <h2 id="ot">Zoteling Rush</h2>
        <p id="op">Defendé al Caballero. <strong>Click</strong> sobre los Zotes y el ácido para golpearlos. <strong>Barra espaciadora</strong> (o click en el Caballero) para saltar las ondas y esquivar a Head of Zote.</p>
        <button id="boton" class="boton_general" type="button">Jugar</button>
      </div>
    </div>
    <section class="mj_rank panel_general" style="padding:1rem">
      <h3 style="text-align:center">Mejores puntajes</h3>
      <ol id="rank"><li>Cargando…</li></ol>
    </section>
  </main>
  <script>
  const LOGUEADO = <?= usuario_actual() ? 'true' : 'false' ?>, CSRF = '<?= h(csrf_token()) ?>';
  const cv = document.getElementById('c'), cx = cv.getContext('2d'), W = 900, H = 540, GY = 470, KX = 450, SP = '../Imagenes/minijuego/';
  // Tipos: archivo, alto, vida, puntos, kills para desbloquear
  const T = {
    eternal:  {f:'zote-eternal',      h:100, hp:2, pts:10, u:0},
    winged:   {f:'winged-zoteling',   h:80,  hp:1, pts:15, u:4},
    hopping:  {f:'hopping-zoteling',  h:80,  hp:1, pts:15, u:9},
    heavy:    {f:'heavy-zoteling',    h:90,  hp:4, pts:40, u:15},
    volatile: {f:'volatile-zoteling', h:80,  hp:5, pts:30, u:22},
    lanky:    {f:'lanky-zoteling',    h:130, hp:2, pts:30, u:30},
    turret:   {f:'turret-zoteling',   h:140, hp:2, pts:35, u:38},
    head:     {f:'head-of-zote',      h:90,  hp:2, pts:25, u:47},
    curse:    {f:'zote-curse',        h:90,  hp:2, pts:30, u:57},
    fluke:    {f:'fluke-zoteling',    h:80,  hp:1, pts:25, u:68}
  };
  const IMG = {};
  for (const k in T) { IMG[k] = new Image(); IMG[k].src = SP + T[k].f + '.webp'; }
  IMG.knight = new Image(); IMG.knight.src = SP + 'caballerito.webp';   // imagen del Caballero
  const R = (a, b) => a + Math.random() * (b - a), sg = n => n < 0 ? -1 : 1;
  let st = 'menu', E, WV, FX, score, kills, lives, inv, t, spawnT, jy, jv, shake, last;

  function start() {
    E = []; WV = []; FX = []; score = 0; kills = 0; lives = 3; inv = 0; t = 0; spawnT = 1; jy = 0; jv = 0; shake = 0;
    st = 'play'; document.getElementById('overlay').style.display = 'none'; last = performance.now(); requestAnimationFrame(loop);
  }
  function add(k, o) {
    const d = T[k] || {h:30, hp:1};
    const e = Object.assign({k, hp:d.hp, h:d.h, t:0, fl:0, x:0, y:0, vx:0, vy:0}, o);
    const im = IMG[k]; e.w = im && im.naturalWidth ? d.h * im.naturalWidth / im.naturalHeight : d.h * .8;
    E.push(e); return e;
  }
  function acid(x, y, vx, vy, g) { const a = add('acid', {x, y, vx, vy, g:g||0, w:26, h:26}); a.hp = 1; }
  function spawn() {
    const un = Object.keys(T).filter(k => kills >= T[k].u), k = Math.random() < .45 ? un[un.length - 1] : un[Math.floor(Math.random() * un.length)];
    const s = Math.random() < .5 ? -1 : 1, sx = s < 0 ? -50 : W + 50, h = T[k].h, gy = GY - h / 2;
    if (k === 'eternal' || k === 'volatile' || k === 'lanky') add(k, {x:sx, y:gy, ph:'walk'});
    else if (k === 'winged') add(k, {x:sx, y:R(70, 260)});
    else if (k === 'hopping' || k === 'heavy') add(k, {x:sx, y:gy, pause:.3});
    else if (k === 'turret') { if (E.filter(e => e.k === 'turret').length < 2) add(k, {x:R(120, 780), y:h / 2 - 20, shot:1, n:0}); }
    else if (k === 'head') add(k, {x:KX, y:-80, ph:'warn'});
    else if (k === 'curse') add(k, {a:R(0, 6.28), r:430});
    else if (k === 'fluke') add(k, {x:KX + sg(R(-1, 1)) * R(130, 330), y:GY - 18, ph:'mound'});
  }
  function hurt() {
    if (inv > 0) return; lives--; inv = 1.3; shake = .3;
    if (lives <= 0) over();
  }
  function kill(e, pts) {
    E.splice(E.indexOf(e), 1);
    if (pts !== false) { const p = e.k === 'acid' ? 2 : T[e.k].pts; score += p; if (e.k !== 'acid') kills++; FX.push({x:e.x, y:e.y - 20, tx:'+' + p, l:.7}); }
  }
  function touchKnight(e) { return Math.abs(e.x - KX) < 38 + e.w / 4 && Math.abs(e.y - (GY - 45 - jy)) < 55; }

  function update(dt) {
    t += dt; inv = Math.max(0, inv - dt); shake = Math.max(0, shake - dt);
    jv += 1900 * dt; jy = Math.max(0, jy - jv * dt); if (jy === 0) jv = 0;
    spawnT -= dt;
    if (spawnT <= 0) { spawn(); if (t > 60 && Math.random() < .35) spawn(); spawnT = Math.max(.5, 1.8 - t * .014); }
    const sp = Math.min(1.8, 1 + t / 120);
    for (const e of E.slice()) {
      e.t += dt; e.fl = Math.max(0, e.fl - dt);
      const d = KX - e.x, dir = sg(d);
      switch (e.k) {
        case 'eternal': e.x += dir * 70 * sp * dt; break;
        case 'volatile': e.x += dir * 45 * sp * dt; break;
        case 'winged': { const dy = GY - 50 - e.y, m = Math.hypot(d, dy) || 1; e.x += d / m * 110 * sp * dt; e.y += dy / m * 110 * sp * dt + Math.sin(e.t * 7) * 40 * dt; break; }
        case 'hopping': case 'heavy': {
          const hv = e.k === 'heavy', gy = GY - e.h / 2;
          if (e.y >= gy && e.vy >= 0) {
            e.y = gy;
            if (e.air) { e.air = false; e.pause = .4; if (hv) { WV.push({x:e.x, d:-1, l:2.5}, {x:e.x, d:1, l:2.5}); shake = .15; } }
            e.vx = 0; e.pause -= dt;
            if (e.pause <= 0) { e.vy = hv ? -460 : -560; e.vx = dir * (hv ? 110 : 170) * sp; e.air = true; }
          } else { e.vy += 1400 * dt; }
          e.x += e.vx * dt; e.y += e.vy * dt; break; }
        case 'lanky':
          if (e.ph === 'walk') { e.x += dir * 80 * sp * dt; if (Math.abs(d) < 320) { e.ph = 'wind'; e.t = 0; } }
          else if (e.ph === 'wind') { e.x -= dir * 40 * dt; e.sh = Math.sin(e.t * 60) * 3; if (e.t > .8) { e.ph = 'dash'; e.sh = 0; } }
          else e.x += dir * 700 * dt;
          break;
        case 'turret':
          if (e.t > e.shot * 1.7) { e.shot++; acid(e.x, e.y + e.h / 2, 0, 0, 0); const a = E[E.length - 1], m = Math.hypot(KX - a.x, GY - 50 - a.y); a.vx = (KX - a.x) / m * 240; a.vy = (GY - 50 - a.y) / m * 240; }
          if (e.t > 9) E.splice(E.indexOf(e), 1);
          break;
        case 'head':
          if (e.ph === 'warn') { if (e.t > 1.3) { e.ph = 'fall'; e.x = KX; } }
          else { e.y += 720 * dt; if (e.y + e.h / 2 >= GY) { if (jy < 40) hurt(); shake = .2; E.splice(E.indexOf(e), 1); } }
          break;
        case 'curse': e.a += 1.3 * dt; e.r -= 38 * sp * dt; e.x = KX + Math.cos(e.a) * e.r; e.y = Math.min(GY - e.h / 2, GY - 70 + Math.sin(e.a) * e.r * .6); break;
        case 'fluke':
          if (e.t > 1.2) { for (let i = 0; i < 9; i++) acid(e.x, GY - 30, (KX - e.x) * R(.4, 1.1) + R(-70, 70), -R(380, 620), 900); E.splice(E.indexOf(e), 1); shake = .15; }
          break;
        case 'acid':
          e.vy += e.g * dt; e.x += e.vx * dt; e.y += e.vy * dt;
          if (e.y > GY || e.x < -40 || e.x > W + 40) E.splice(E.indexOf(e), 1);
          break;
      }
      if (!E.includes(e)) continue;
      const dmg = e.k !== 'head' && e.k !== 'turret' && e.k !== 'fluke' && touchKnight(e);
      if (dmg) { hurt(); E.splice(E.indexOf(e), 1); if (e.k === 'volatile') shake = .3; }
      if (st !== 'play') return;
    }
    for (const w of WV.slice()) {
      w.x += w.d * 240 * dt; w.l -= dt;
      if (w.l <= 0) WV.splice(WV.indexOf(w), 1);
      else if (Math.abs(w.x - KX) < 22 && jy < 25) { hurt(); WV.splice(WV.indexOf(w), 1); }
    }
    for (const f of FX) { f.l -= dt; f.y -= 30 * dt; }
    FX = FX.filter(f => f.l > 0);
  }

  function sprite(img, x, y, w, h, fl) {
    if (fl) cx.globalAlpha = .55;
    cx.drawImage(img, x - w / 2, y - h / 2, w, h); cx.globalAlpha = 1;
  }
  function draw() {
    cx.clearRect(0, 0, W, H); cx.save();
    if (shake > 0) cx.translate(R(-4, 4), R(-4, 4));
    cx.fillStyle = 'rgba(8,10,18,.75)'; cx.fillRect(0, GY, W, H - GY);
    cx.strokeStyle = 'rgba(160,190,255,.35)'; cx.beginPath(); cx.moveTo(0, GY); cx.lineTo(W, GY); cx.stroke();
    for (const w of WV) { cx.fillStyle = 'rgba(200,220,255,.8)'; cx.beginPath(); cx.ellipse(w.x, GY - 8, 10, 22, 0, 0, 7); cx.fill(); }
    for (const e of E) {
      if (e.k === 'acid') { cx.fillStyle = '#9dff4d'; cx.shadowColor = '#9dff4d'; cx.shadowBlur = 10; cx.beginPath(); cx.arc(e.x, e.y, 10, 0, 7); cx.fill(); cx.shadowBlur = 0; continue; }
      if (e.k === 'head' && e.ph === 'warn') { cx.fillStyle = '#ff5a5a'; cx.font = 'bold 54px Cinzel, serif'; cx.textAlign = 'center'; cx.fillText('!', KX, GY - 120 - (jy)); continue; }
      if (e.k === 'fluke') { cx.fillStyle = '#2a2f45'; cx.beginPath(); cx.ellipse(e.x, GY, 36 + e.t * 12, 12 + e.t * 10, 0, Math.PI, 0); cx.fill(); }
      const sc = e.k === 'volatile' ? 1 + (T.volatile.hp - e.hp) * .09 : 1, im = IMG[e.k];
      if (!im.naturalWidth) continue;
      cx.save(); cx.translate(e.x + (e.sh || 0), e.y); cx.scale(sc * (e.k === 'turret' ? 1 : 1), sc);
      if (e.k === 'turret') cx.translate(0, 0);
      sprite(im, 0, e.k === 'fluke' ? 10 - Math.min(1, e.t / 1.2) * 30 + 20 : 0, e.w, e.h, e.fl); cx.restore();
    }
    // Caballero
    const ky = GY - 45 - jy;
    if (!(inv > 0 && Math.floor(t * 14) % 2)) {
      if (IMG.knight.naturalWidth) sprite(IMG.knight, KX, ky, 90 * IMG.knight.naturalWidth / IMG.knight.naturalHeight, 90, 0);
      else { // silueta por defecto
        cx.fillStyle = '#10121c'; cx.beginPath(); cx.ellipse(KX, ky + 18, 22, 28, 0, 0, 7); cx.fill();
        cx.fillStyle = '#e9edf7'; cx.beginPath(); cx.ellipse(KX, ky - 14, 22, 24, 0, 0, 7); cx.fill();
        cx.strokeStyle = '#e9edf7'; cx.lineWidth = 4; cx.beginPath(); cx.moveTo(KX - 12, ky - 32); cx.lineTo(KX - 22, ky - 52); cx.moveTo(KX + 12, ky - 32); cx.lineTo(KX + 22, ky - 52); cx.stroke();
        cx.fillStyle = '#10121c'; cx.beginPath(); cx.ellipse(KX - 8, ky - 12, 5, 8, 0, 0, 7); cx.ellipse(KX + 8, ky - 12, 5, 8, 0, 0, 7); cx.fill();
      }
    }
    for (const f of FX) { cx.globalAlpha = Math.min(1, f.l * 2); cx.fillStyle = f.tx ? '#fff' : '#cfe'; cx.font = 'bold 22px Cinzel, serif'; cx.textAlign = 'center'; if (f.tx) cx.fillText(f.tx, f.x, f.y); else { cx.strokeStyle = '#fff'; cx.beginPath(); cx.arc(f.x, f.y, 26 - f.l * 60, 0, 7); cx.stroke(); } cx.globalAlpha = 1; }
    cx.restore();
    cx.fillStyle = '#fff'; cx.font = 'bold 24px Cinzel, serif'; cx.textAlign = 'left'; cx.fillText('♥'.repeat(Math.max(0, lives)), 16, 34);
    cx.textAlign = 'right'; cx.fillText('Puntos: ' + score, W - 16, 34);
  }
  function loop(now) {
    if (st !== 'play') return;
    const dt = Math.min(.05, (now - last) / 1000); last = now; update(dt); if (st === 'play') { draw(); requestAnimationFrame(loop); }
  }

  function pos(ev) { const r = cv.getBoundingClientRect(); return {x:(ev.clientX - r.left) * W / r.width, y:(ev.clientY - r.top) * H / r.height}; }
  cv.addEventListener('pointerdown', ev => {
    if (st !== 'play') return; const p = pos(ev); FX.push({x:p.x, y:p.y, l:.15});
    for (let i = E.length - 1; i >= 0; i--) {
      const e = E[i], rx = (e.k === 'acid' ? 22 : e.w / 2 + 6), ry = (e.k === 'acid' ? 22 : e.h / 2 + 6);
      if (e.k === 'head' && e.ph === 'warn') continue;
      if (Math.abs(p.x - e.x) < rx && Math.abs(p.y - e.y) < ry) { e.hp--; e.fl = .12; if (e.hp <= 0) kill(e); return; }
    }
    if (Math.abs(p.x - KX) < 40 && Math.abs(p.y - (GY - 45 - jy)) < 55) jump();
  });
  function jump() { if (jy === 0) jv = -620; }
  addEventListener('keydown', ev => { if (ev.code === 'Space' && st === 'play') { ev.preventDefault(); jump(); } });

  function over() {
    st = 'over'; const ov = document.getElementById('overlay'); ov.style.display = 'flex';
    document.getElementById('ot').textContent = 'Fin del juego';
    document.getElementById('boton').textContent = 'Reintentar';
    const op = document.getElementById('op'); op.textContent = 'Puntos: ' + score + ' · Zotes derrotados: ' + kills + (LOGUEADO ? '' : ' · Ingresá a tu cuenta para guardar tu puntaje en el ranking.');
    if (LOGUEADO) rank({puntos:score, kills:kills}); 
  }
  function rank(body) {
    const o = body ? {method:'POST', headers:{'Content-Type':'application/json', 'X-CSRF':CSRF}, body:JSON.stringify(body)} : {};
    fetch('../puntaje.php', o).then(r => r.json()).then(j => {
      const ol = document.getElementById('rank'); ol.innerHTML = '';
      if (j.error) { ol.innerHTML = '<li></li>'; ol.firstChild.textContent = j.error; return; }
      if (!j.ranking.length) ol.innerHTML = '<li>Todavía no hay puntajes.</li>';
      j.ranking.forEach(r => { const li = document.createElement('li'), s = document.createElement('span'); li.textContent = r.usuario; s.textContent = r.puntos; li.appendChild(s); ol.appendChild(li); });
    }).catch(() => { document.getElementById('rank').innerHTML = '<li>No se pudo cargar el ranking.</li>'; });
  }
  document.getElementById('boton').addEventListener('click', start);
  rank();
  </script>
</body>
</html>
