import { lerp, mix, rng } from './utils.js';

/**
 * Animated opening scene: forest layers, ruins and a pedestal in the clearing, with
 * rays of light, grass moving in the wind and pollen that glows inside the rays.
 * Trees, pedestal and ruins are code placeholders meant to be replaced by drawings.
 */
export function initScene(canvas) {
    const ctx = canvas.getContext('2d');
    const toggle = canvas.parentElement.querySelector('.motion-toggle');
    const toggleLabel = toggle.querySelector('.label');
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');

    let W = 0, H = 0, DPR = 1;
    let layers = [], rays = [], blades = [], flowers = [], motes = [], occluders = [];
    let ruinsFar = null, ruinsNear = null;
    let paused = reduced.matches;
    let t = 0, last = performance.now(), frame = null;
    const pointer = { x: 0, tx: 0 };

    function offscreen(width) {
        const c = document.createElement('canvas');
        c.width = Math.ceil(width * DPR); c.height = Math.ceil(H * DPR);
        const g = c.getContext('2d'); g.scale(DPR, DPR);
        return [c, g];
    }

    // A trunk that bends as it rises, flares at the base and grows exposed roots.
    function gnarledTrunk(g, x, bottom, top, w, rand, bend, roots) {
        const steps = 14, left = [], right = [];
        const p1 = rand() * 6, p2 = rand() * 6;
        for (let i = 0; i <= steps; i++) {
            const k = i / steps;
            const y = lerp(bottom, top, k);
            const cx = x + Math.sin(k * 3.1 + p1) * bend + Math.sin(k * 7 + p2) * bend * 0.25;
            const half = w * (0.5 + 0.5 * Math.pow(1 - k, 3) * 0.9 + (1 - k) * 0.2) * (0.55 + 0.45 * (1 - k * 0.6));
            left.push([cx - half, y]); right.push([cx + half, y]);
        }
        g.beginPath(); g.moveTo(...left[0]);
        left.forEach(p => g.lineTo(...p));
        right.reverse().forEach(p => g.lineTo(...p));
        g.closePath(); g.fill();
        for (let r = 0; r < roots; r++) {
            const dir = r % 2 ? 1 : -1;
            const len = w * (1 + rand() * 1.6);
            const thick = w * (0.18 + rand() * 0.14);
            g.beginPath();
            g.moveTo(x - thick, bottom - w * 0.35);
            g.bezierCurveTo(x + dir * len * 0.3, bottom - w * 0.3, x + dir * len * 0.7, bottom - w * 0.05, x + dir * len, bottom + 2);
            g.lineTo(x + dir * len * 0.95, bottom + 4);
            g.bezierCurveTo(x + dir * len * 0.6, bottom + 2, x + dir * len * 0.3, bottom - w * 0.1, x + thick, bottom - w * 0.2);
            g.closePath(); g.fill();
        }
    }

    // Irregular leaf mass: many rotated ellipses give a jagged, organic edge.
    function foliage(g, x, y, r, rand) {
        const n = 10 + Math.floor(rand() * 8);
        for (let i = 0; i < n; i++) {
            const a = rand() * Math.PI * 2, d = rand() * r * 0.75;
            g.save();
            g.translate(x + Math.cos(a) * d, y + Math.sin(a) * d * 0.7);
            g.rotate(rand() * Math.PI);
            g.beginPath(); g.ellipse(0, 0, r * (0.25 + rand() * 0.3), r * (0.12 + rand() * 0.16), 0, 0, Math.PI * 2); g.fill();
            g.restore();
        }
    }

    function branch(g, x0, y0, x1, y1, w0, rand) {
        const mx = lerp(x0, x1, 0.5) + (rand() - 0.5) * 60, my = lerp(y0, y1, 0.5) - 30 - rand() * 40;
        const nx = -(y1 - y0), ny = x1 - x0, nl = Math.hypot(nx, ny);
        const ox = nx / nl, oy = ny / nl;
        g.beginPath();
        g.moveTo(x0 + ox * w0, y0 + oy * w0);
        g.quadraticCurveTo(mx + ox * w0 * 0.5, my + oy * w0 * 0.5, x1, y1);
        g.quadraticCurveTo(mx - ox * w0 * 0.5, my - oy * w0 * 0.5, x0 - ox * w0, y0 - oy * w0);
        g.closePath(); g.fill();
    }

    function buildLayer(depth, rand) {
        const margin = 60, width = W + margin * 2;
        const [c, g] = offscreen(width);
        const far = depth <= 1;
        const trunkColor = ['#2A5A49', '#1B4636', '#2A241B', '#1A150F'][depth];
        const leafColor = ['#3E6F55', '#2A5A3C', '#163F22', '#0B2410'][depth];
        const base = H * (0.6 + depth * 0.035);

        if (far) {
            g.fillStyle = trunkColor;
            const count = depth === 0 ? 26 : 16;
            for (let i = 0; i < count; i++) {
                const x = rand() * width, w = 5 + rand() * 8 + depth * 5;
                gnarledTrunk(g, x, base, -10, w, rand, 4 + depth * 4, depth);
            }
            g.fillStyle = leafColor;
            const masses = depth === 0 ? 34 : 24;
            for (let i = 0; i < masses; i++) {
                g.globalAlpha = 0.6 + rand() * 0.3;
                foliage(g, rand() * width, rand() * H * 0.36 - H * 0.04, 40 + rand() * 60, rand);
            }
            g.globalAlpha = 1;
            g.fillStyle = mix(leafColor, '#6D9C77', depth === 1 ? 0.15 : 0.3);
            for (let x = -20; x < width; x += 16 + rand() * 18) foliage(g, x, base - 10, 22 + rand() * 16, rand);
        } else {
            // Old trees framing the clearing from the sides, with branches reaching in.
            const sides = depth === 2 ? [0.1, 0.9] : [0.02, 0.98];
            for (const s of sides) {
                const x = s * width, w = depth === 2 ? 46 + rand() * 20 : 70 + rand() * 30;
                const bottom = H * (depth === 2 ? 0.9 : 1.02);
                g.fillStyle = trunkColor;
                gnarledTrunk(g, x, bottom, -20, w, rand, 18 + rand() * 12, 4);
                const inward = s < 0.5 ? 1 : -1;
                for (let b = 0; b < 3; b++) {
                    const y0 = H * (0.12 + b * 0.12 + rand() * 0.05);
                    branch(g, x, y0, x + inward * W * (0.16 + rand() * 0.14), y0 - H * (0.02 + rand() * 0.08), w * (0.22 - b * 0.04), rand);
                }
                g.fillStyle = leafColor;
                for (let i = 0; i < 10; i++) {
                    const fx = x + inward * rand() * W * 0.28, fy = rand() * H * 0.3 - H * 0.06;
                    foliage(g, fx, fy, 60 + rand() * 70, rand);
                }
            }
            if (depth === 3) {
                for (const side of [0, 1]) {
                    for (let i = 0; i < 22; i++) {
                        const cx = side ? width - rand() * W * 0.2 : rand() * W * 0.2;
                        const cy = H * (0.8 + rand() * 0.24);
                        const len = 40 + rand() * 70;
                        const ang = (side ? Math.PI + 0.5 : -0.5) + (rand() - 0.5) * 1.2;
                        g.fillStyle = rand() > 0.5 ? '#102A05' : '#1C3A0C';
                        g.save(); g.translate(cx, cy); g.rotate(ang);
                        g.beginPath(); g.ellipse(len * 0.5, 0, len * 0.5, len * 0.12, 0, 0, Math.PI * 2); g.fill();
                        g.restore();
                    }
                }
            }
        }
        return { canvas: c, depth, margin };
    }

    // Stone helpers: carved blocks with a lit top face, cracks and moss.
    function slab(g, cx, by, w, h, depth, rand, lit) {
        const top = by - h, inset = w * 0.06;
        g.fillStyle = lit ? '#BCC3BD' : '#9AA29C';
        g.beginPath();
        g.moveTo(cx - w / 2, top); g.lineTo(cx - w / 2 + inset, top - depth);
        g.lineTo(cx + w / 2 - inset, top - depth); g.lineTo(cx + w / 2, top); g.closePath(); g.fill();
        const fg = g.createLinearGradient(0, top, 0, by);
        fg.addColorStop(0, '#7E8780'); fg.addColorStop(1, '#4E5751');
        g.fillStyle = fg; g.fillRect(cx - w / 2, top, w, h);
        g.strokeStyle = 'rgba(20,26,22,.55)'; g.lineWidth = 1;
        for (let i = 0; i < 3; i++) {
            const x = cx - w / 2 + rand() * w;
            g.beginPath(); g.moveTo(x, top); g.lineTo(x + (rand() - 0.5) * 10, top + h * 0.5); g.lineTo(x + (rand() - 0.5) * 14, by); g.stroke();
        }
        g.fillStyle = 'rgba(90,127,42,.9)';
        for (let i = 0; i < 4; i++) {
            const x = cx - w / 2 + rand() * w;
            g.beginPath(); g.ellipse(x, top - depth * 0.3, 8 + rand() * 16, 3 + rand() * 3, 0, 0, Math.PI * 2); g.fill();
        }
    }

    function column(g, cx, by, w, h, rand) {
        const top = by - h;
        const cg = g.createLinearGradient(cx - w / 2, 0, cx + w / 2, 0);
        cg.addColorStop(0, '#5E6660'); cg.addColorStop(0.35, '#A3ABA5'); cg.addColorStop(1, '#4E5751');
        g.fillStyle = cg;
        g.beginPath(); g.moveTo(cx - w / 2, by);
        g.lineTo(cx - w / 2, top + 8);
        const teeth = 5;
        for (let i = 0; i <= teeth; i++) g.lineTo(cx - w / 2 + (w * i) / teeth, top + (i % 2 ? 0 : 6 + rand() * 14));
        g.lineTo(cx + w / 2, by); g.closePath(); g.fill();
        g.strokeStyle = 'rgba(30,36,32,.45)'; g.lineWidth = 1;
        for (let i = 1; i < 4; i++) { const x = cx - w / 2 + (w * i) / 4; g.beginPath(); g.moveTo(x, top + 16); g.lineTo(x, by - 4); g.stroke(); }
        g.fillStyle = '#6E766F'; g.fillRect(cx - w * 0.62, by - 10, w * 1.24, 10);
        g.fillStyle = 'rgba(90,127,42,.85)';
        g.beginPath(); g.ellipse(cx - w * 0.2, top + 10, w * 0.35, 5, -0.2, 0, Math.PI * 2); g.fill();
    }

    function buildRuins(rand) {
        // Far: a broken arch at the treeline, hazy like the far forest.
        const [cf, gf] = offscreen(W + 120);
        const ax = W * 0.8 + 60, ab = H * 0.63, aw = W * 0.11, ah = H * 0.2;
        gf.fillStyle = '#3F6457'; gf.globalAlpha = 0.85;
        gf.fillRect(ax - aw / 2, ab - ah, aw * 0.18, ah);
        gf.fillRect(ax + aw / 2 - aw * 0.18, ab - ah * 0.72, aw * 0.18, ah * 0.72);
        gf.beginPath();
        gf.moveTo(ax - aw / 2, ab - ah); gf.quadraticCurveTo(ax - aw * 0.1, ab - ah * 1.45, ax + aw * 0.12, ab - ah * 1.12);
        gf.lineTo(ax + aw * 0.06, ab - ah * 1.0); gf.quadraticCurveTo(ax - aw * 0.12, ab - ah * 1.22, ax - aw / 2 + aw * 0.18, ab - ah * 0.86);
        gf.closePath(); gf.fill();
        gf.globalAlpha = 1;
        ruinsFar = { canvas: cf, margin: 60, depth: 1.5 };

        // Near: pedestal in the center, columns, a fallen drum and loose blocks.
        const [cn, gn] = offscreen(W + 60);
        const o = 30, px = W * 0.5 + o, pb = H * 0.9;
        occluders = [];
        gn.fillStyle = 'rgba(8,24,6,.45)';
        gn.beginPath(); gn.ellipse(px, pb + 4, W * 0.17, H * 0.025, 0, 0, Math.PI * 2); gn.fill();
        slab(gn, px, pb, W * 0.3, H * 0.045, H * 0.03, rand, true);
        slab(gn, px, pb - H * 0.075, W * 0.2, H * 0.04, H * 0.025, rand, true);
        // Engraved ring on the pedestal top.
        gn.strokeStyle = 'rgba(40,48,42,.6)'; gn.lineWidth = 1.5;
        gn.beginPath(); gn.ellipse(px, pb - H * 0.128, W * 0.05, H * 0.009, 0, 0, Math.PI * 2); gn.stroke();
        occluders.push({ x0: W * 0.35, x1: W * 0.65, top: pb - H * 0.16, bottom: pb });

        column(gn, W * 0.3 + o, H * 0.81, W * 0.032, H * 0.15, rand);
        occluders.push({ x0: W * 0.284, x1: W * 0.316, top: H * 0.66, bottom: H * 0.81 });
        column(gn, W * 0.73 + o, H * 0.75, W * 0.028, H * 0.22, rand);
        occluders.push({ x0: W * 0.716, x1: W * 0.744, top: H * 0.53, bottom: H * 0.75 });

        // Fallen column drum.
        const fx = W * 0.68 + o, fy = H * 0.93, fl = W * 0.1, fr = H * 0.028;
        const dg = gn.createLinearGradient(0, fy - fr, 0, fy + fr);
        dg.addColorStop(0, '#A9B1AB'); dg.addColorStop(1, '#4E5751');
        gn.fillStyle = dg; gn.fillRect(fx - fl / 2, fy - fr, fl, fr * 2);
        gn.fillStyle = '#8A938C'; gn.beginPath(); gn.ellipse(fx + fl / 2, fy, fr * 0.45, fr, 0, 0, Math.PI * 2); gn.fill();
        gn.strokeStyle = 'rgba(30,36,32,.5)'; gn.beginPath(); gn.ellipse(fx + fl / 2, fy, fr * 0.25, fr * 0.6, 0, 0, Math.PI * 2); gn.stroke();

        // Loose carved blocks.
        for (const [bx, by, bw] of [[0.22, 0.95, 0.05], [0.8, 0.86, 0.035], [0.4, 0.97, 0.03]]) {
            slab(gn, W * bx + o, H * by, W * bw, H * 0.03, H * 0.012, rand, false);
        }
        ruinsNear = { canvas: cn, margin: 30, depth: 1.8 };
    }

    function build() {
        DPR = Math.min(window.devicePixelRatio || 1, 2);
        W = canvas.clientWidth; H = canvas.clientHeight;
        canvas.width = Math.round(W * DPR); canvas.height = Math.round(H * DPR);
        const rand = rng(20260930);

        layers = [0, 1, 2, 3].map(d => buildLayer(d, rand));
        buildRuins(rand);

        // Rays fan out from above the top-left corner and converge on the pedestal.
        const ox = W * 0.16, oy = -H * 0.25;
        rays = Array.from({ length: 6 }, (_, i) => ({
            ox: ox + (rand() - 0.5) * 40, oy,
            tx: W * (0.36 + i * 0.055 + rand() * 0.03), ty: H * (0.84 + rand() * 0.06),
            w0: 6 + rand() * 10, w1: 50 + rand() * 80 + (i % 2) * 40,
            base: 0.14 + rand() * 0.14, speed: 0.12 + rand() * 0.18, phase: rand() * 6.28, alpha: 0
        }));

        const meadowTop = H * 0.62;
        const lit = (x, y) => {
            const dx = (x - W * 0.5) / (W * 0.45), dy = (y - H * 0.82) / (H * 0.3);
            return Math.max(0, 1 - Math.sqrt(dx * dx + dy * dy));
        };
        const palette = ['#173407', '#32550D', '#517414', '#85A422', '#ADC938', '#CFE56B'];
        const hidden = (x, y) => occluders.some(o => x > o.x0 && x < o.x1 && y > o.top && y < o.bottom - 2);
        const count = Math.min(620, Math.floor(W / 1.8));
        blades = [];
        for (let i = 0; i < count; i++) {
            const y = meadowTop + Math.pow(rand(), 0.8) * (H - meadowTop);
            const x = rand() * W;
            if (hidden(x, y)) continue;
            const near = (y - meadowTop) / (H - meadowTop);
            blades.push({
                x, y, h: (7 + rand() * 12) * (0.6 + near * 1.3), lean: (rand() - 0.5) * 6,
                color: palette[Math.min(5, Math.floor(lit(x, y) * 5.2 + rand() * 1.4))]
            });
        }
        blades.sort((a, b) => a.y - b.y);

        flowers = [];
        for (let i = 0; i < Math.floor(W / 24); i++) {
            const y = meadowTop + H * 0.04 + rand() * (H - meadowTop - H * 0.04);
            const x = W * 0.1 + rand() * W * 0.8;
            if (!hidden(x, y)) flowers.push({ x, y, r: 1.1 + rand() * 1.5 * ((y - meadowTop) / (H - meadowTop) + 0.4) });
        }

        motes = Array.from({ length: Math.floor(W / 14) }, () => ({
            x: rand() * W, y: rand() * H * 0.9, r: 0.6 + rand() * 1.6,
            vx: (rand() - 0.3) * 6, vy: (rand() - 0.5) * 4, phase: rand() * 6.28
        }));
    }

    // How much of the rays' light reaches a point (0..1).
    function rayLight(x, y) {
        let light = 0;
        for (const r of rays) {
            const dx = r.tx - r.ox, dy = r.ty - r.oy;
            const k = ((x - r.ox) * dx + (y - r.oy) * dy) / (dx * dx + dy * dy);
            if (k < 0 || k > 1) continue;
            const d = Math.hypot(x - (r.ox + dx * k), y - (r.oy + dy * k));
            light += Math.max(0, 1 - d / (lerp(r.w0, r.w1, k) / 2)) * r.alpha * 3;
        }
        return Math.min(1, light);
    }

    function drawRays() {
        ctx.save();
        ctx.globalCompositeOperation = 'screen';
        for (const r of rays) {
            r.alpha = r.base * (0.65 + 0.35 * Math.sin(t * r.speed + r.phase));
            const dx = r.tx - r.ox, dy = r.ty - r.oy;
            const len = Math.hypot(dx, dy), nx = -dy / len, ny = dx / len;
            const g = ctx.createLinearGradient(r.ox, r.oy, r.tx, r.ty);
            g.addColorStop(0, 'rgba(231,250,192,0)');
            g.addColorStop(0.25, `rgba(210,240,200,${r.alpha})`);
            g.addColorStop(0.7, `rgba(147,194,164,${r.alpha * 0.7})`);
            g.addColorStop(1, 'rgba(147,194,164,0)');
            ctx.fillStyle = g;
            ctx.beginPath();
            ctx.moveTo(r.ox + nx * r.w0 / 2, r.oy + ny * r.w0 / 2);
            ctx.lineTo(r.tx + nx * r.w1 / 2, r.ty + ny * r.w1 / 2);
            ctx.lineTo(r.tx - nx * r.w1 / 2, r.ty - ny * r.w1 / 2);
            ctx.lineTo(r.ox - nx * r.w0 / 2, r.oy - ny * r.w0 / 2);
            ctx.closePath(); ctx.fill();
        }
        ctx.restore();
    }

    function drawMeadow() {
        const top = H * 0.62;
        const g = ctx.createRadialGradient(W * 0.5, H * 0.86, 0, W * 0.5, H * 0.86, W * 0.6);
        g.addColorStop(0, '#CFE56B'); g.addColorStop(0.2, '#ADC938');
        g.addColorStop(0.5, '#517414'); g.addColorStop(1, '#173407');
        ctx.fillStyle = g;
        ctx.beginPath(); ctx.moveTo(0, top + 10);
        for (let x = 0; x <= W; x += W / 12) ctx.lineTo(x, top + Math.sin(x * 0.01) * 6);
        ctx.lineTo(W, H); ctx.lineTo(0, H); ctx.closePath(); ctx.fill();
        const s = ctx.createLinearGradient(0, top, 0, top + H * 0.12);
        s.addColorStop(0, 'rgba(10,31,22,.55)'); s.addColorStop(1, 'rgba(10,31,22,0)');
        ctx.fillStyle = s; ctx.fillRect(0, top - 8, W, H * 0.14);
    }

    function wind(x) { return Math.sin(t * 1.1 + x * 0.018) * 3 + Math.sin(t * 0.37 + x * 0.004) * 4; }

    function drawGrass() {
        ctx.lineCap = 'round';
        const groups = {};
        for (const b of blades) (groups[b.color] ||= []).push(b);
        for (const color in groups) {
            ctx.strokeStyle = color; ctx.lineWidth = 1.3;
            ctx.beginPath();
            for (const b of groups[color]) {
                const sway = wind(b.x) * (b.h / 20);
                ctx.moveTo(b.x, b.y);
                ctx.quadraticCurveTo(b.x + b.lean * 0.3, b.y - b.h * 0.6, b.x + b.lean + sway, b.y - b.h);
            }
            ctx.stroke();
        }
        for (const f of flowers) {
            const sway = wind(f.x) * 0.35;
            ctx.fillStyle = 'rgba(244,247,238,.9)';
            ctx.beginPath(); ctx.arc(f.x + sway, f.y - 3, f.r, 0, Math.PI * 2); ctx.fill();
        }
    }

    function drawLayer(layer) {
        const shift = -layer.margin + pointer.x * (layer.depth + 1) * 7;
        ctx.drawImage(layer.canvas, shift, 0, layer.canvas.width / DPR, H);
    }

    function drawMotes(dt) {
        ctx.save();
        ctx.globalCompositeOperation = 'lighter';
        for (const m of motes) {
            if (dt) {
                m.x += (m.vx + Math.sin(t * 0.5 + m.phase) * 3) * dt;
                m.y += (m.vy + Math.cos(t * 0.4 + m.phase) * 2) * dt;
                if (m.x > W + 10) m.x = -10;
                if (m.x < -10) m.x = W + 10;
                if (m.y > H * 0.95) m.y = 0;
                if (m.y < 0) m.y = H * 0.95;
            }
            const light = rayLight(m.x, m.y);
            ctx.fillStyle = `rgba(231,250,192,${0.06 + light * 0.85})`;
            ctx.beginPath(); ctx.arc(m.x, m.y, m.r * (1 + light * 0.8), 0, Math.PI * 2); ctx.fill();
        }
        ctx.restore();
    }

    function render(dt) {
        ctx.setTransform(DPR, 0, 0, DPR, 0, 0);
        const bg = ctx.createLinearGradient(0, 0, 0, H);
        bg.addColorStop(0, '#0E2E24'); bg.addColorStop(0.6, '#18412F'); bg.addColorStop(1, '#0A1F16');
        ctx.fillStyle = bg; ctx.fillRect(0, 0, W, H);

        drawLayer(layers[0]);
        drawLayer(layers[1]);
        drawLayer(ruinsFar);
        drawRays();
        drawMeadow();
        drawLayer(ruinsNear);
        drawGrass();
        drawLayer(layers[2]);
        drawLayer(layers[3]);
        drawMotes(dt);

        const v = ctx.createRadialGradient(W * 0.5, H * 0.55, Math.min(W, H) * 0.35, W * 0.5, H * 0.55, Math.max(W, H) * 0.8);
        v.addColorStop(0, 'rgba(10,31,22,0)'); v.addColorStop(1, 'rgba(10,31,22,.6)');
        ctx.fillStyle = v; ctx.fillRect(0, 0, W, H);
    }

    function loop(now) {
        const dt = Math.min(0.05, (now - last) / 1000);
        last = now;
        t += dt;
        pointer.x = lerp(pointer.x, pointer.tx, 0.05);
        render(dt);
        frame = paused ? null : requestAnimationFrame(loop);
    }

    function setPaused(value) {
        paused = value;
        toggle.setAttribute('aria-pressed', String(paused));
        toggleLabel.textContent = paused ? toggle.dataset.resume : toggle.dataset.pause;
        if (!paused && !frame) { last = performance.now(); frame = requestAnimationFrame(loop); }
    }

    toggle.addEventListener('click', () => setPaused(!paused));
    canvas.parentElement.addEventListener('pointermove', e => {
        const rect = canvas.getBoundingClientRect();
        pointer.tx = ((e.clientX - rect.left) / rect.width - 0.5) * 2;
        if (paused) { pointer.x = pointer.tx; render(0); }
    });
    canvas.parentElement.addEventListener('pointerleave', () => { pointer.tx = 0; });

    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => { build(); render(0); }, 120);
    });

    build();
    t = 2.4;
    render(0);
    setPaused(paused);
}
