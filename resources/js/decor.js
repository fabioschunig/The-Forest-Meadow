import { lerp, mix, rng } from './utils.js';

const SVG = 'http://www.w3.org/2000/svg';

function el(name, attrs) {
    const node = document.createElementNS(SVG, name);
    for (const key in attrs) node.setAttribute(key, attrs[key]);
    return node;
}

/** Ruined wall divider: intact in the middle, crumbling toward both ends. */
function drawRuinWall(svg) {
    const width = svg.clientWidth || 800;
    svg.setAttribute('viewBox', `0 0 ${width} 44`);
    svg.replaceChildren();
    const rand = rng(Number(svg.dataset.seed) || 7);
    const rows = [{ y: 24, h: 18 }, { y: 6, h: 18 }];

    rows.forEach((row, ri) => {
        let x = ri ? 20 + rand() * 30 : 0;
        while (x < width) {
            const w = 38 + rand() * 46;
            const edge = Math.min(x, width - x - w) / (width * 0.5);
            const keep = ri === 0 ? edge > 0.05 || rand() > 0.5 : edge > 0.28 && rand() > 0.12;
            if (keep && x + w < width) {
                svg.append(el('rect', { x: x + 1, y: row.y, width: w - 2, height: row.h, rx: 1.5, fill: mix('#4E5751', '#8A938C', 0.35 + rand() * 0.25) }));
                svg.append(el('rect', { x: x + 1, y: row.y, width: w - 2, height: 2.5, fill: 'rgba(255,255,255,.12)' }));
                if (rand() > 0.72) {
                    svg.append(el('ellipse', { cx: x + w * (0.2 + rand() * 0.6), cy: row.y + 1, rx: 6 + rand() * 12, ry: 2.5 + rand() * 2, fill: 'rgba(90,127,42,.85)' }));
                }
            } else if (ri === 0 && rand() > 0.5) {
                svg.append(el('rect', { x: x + w * 0.3, y: row.y + row.h - 6, width: w * 0.4, height: 6, rx: 2, fill: '#4E5751' }));
            }
            x += w;
        }
    });
}

/** Devlog root: a taproot thinning downward, one branch per entry, the newest bud lit. */
function drawRoot(box) {
    let svg = box.querySelector(':scope > svg.root');
    if (!svg) {
        svg = el('svg', { class: 'root', 'aria-hidden': 'true' });
        box.prepend(svg);
    }
    svg.replaceChildren();
    box.classList.add('has-root');

    const height = box.clientHeight;
    const rand = rng(97);
    const xAt = y => 20 + Math.sin(y * 0.035) * 5 + Math.sin(y * 0.011 + 1) * 4;

    const points = [];
    for (let y = -8; y <= height; y += 12) points.push([xAt(y), y]);
    for (let i = 0; i < points.length - 1; i++) {
        const k = i / points.length;
        const [x1, y1] = points[i];
        const [x2, y2] = points[i + 1];
        svg.append(el('line', { x1, y1, x2, y2, stroke: '#3E3428', 'stroke-width': lerp(9, 3, k), 'stroke-linecap': 'round' }));
        svg.append(el('line', { x1: x1 - 1.5, y1, x2: x2 - 1.5, y2, stroke: 'rgba(107,90,69,.55)', 'stroke-width': lerp(2.5, 0.8, k), 'stroke-linecap': 'round' }));
    }
    const endX = xAt(height);
    for (const dx of [-12, 4, 14]) {
        svg.append(el('path', { d: `M${endX} ${height - 6} q ${dx * 0.5} 10 ${dx} 22`, stroke: '#3E3428', 'stroke-width': 2, fill: 'none', 'stroke-linecap': 'round' }));
    }

    box.querySelectorAll('.entry').forEach(entry => {
        const y = entry.offsetTop + 10;
        const x0 = xAt(y - 16);
        const xEnd = 46;
        const fresh = entry.classList.contains('is-newest');
        svg.append(el('path', { d: `M${x0} ${y - 16} C ${x0 + 10} ${y - 16}, ${xEnd - 14} ${y + 2}, ${xEnd} ${y}`, stroke: '#3E3428', 'stroke-width': 3.2, fill: 'none', 'stroke-linecap': 'round' }));
        svg.append(el('path', { d: `M${x0 + 8} ${y - 13} q 4 -9 1 -16`, stroke: '#3E3428', 'stroke-width': 1.6, fill: 'none', 'stroke-linecap': 'round' }));
        if (fresh) svg.append(el('circle', { cx: xEnd, cy: y, r: 9, fill: 'rgba(207,229,107,.25)' }));
        svg.append(el('ellipse', { cx: xEnd, cy: y, rx: 4.5, ry: 3.2, transform: `rotate(-25 ${xEnd} ${y})`, fill: fresh ? '#CFE56B' : '#6B5A45' }));
        if (!fresh && rand() > 0.3) {
            svg.append(el('ellipse', { cx: xEnd - 12, cy: y - 5, rx: 3.5, ry: 1.8, transform: `rotate(-40 ${xEnd - 12} ${y - 5})`, fill: '#517414' }));
        }
    });
}

export function initDecor() {
    const draw = () => {
        document.querySelectorAll('svg.ruin').forEach(drawRuinWall);
        document.querySelectorAll('.devlog').forEach(drawRoot);
    };

    draw();
    // Web fonts change line heights, which moves the devlog entries.
    document.fonts?.ready.then(draw);

    let timer;
    window.addEventListener('resize', () => {
        clearTimeout(timer);
        timer = setTimeout(draw, 120);
    });
}
