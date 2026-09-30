/** Deterministic random generator, so generated art looks the same on every visit. */
export function rng(seed) {
    return () => {
        seed = (seed * 1664525 + 1013904223) >>> 0;
        return seed / 4294967296;
    };
}

export const lerp = (a, b, k) => a + (b - a) * k;

function hexToRgb(hex) {
    const n = parseInt(hex.slice(1), 16);
    return [(n >> 16) & 255, (n >> 8) & 255, n & 255];
}

/** Blend two hex colors; k = 0 gives a, k = 1 gives b. */
export function mix(a, b, k) {
    const A = hexToRgb(a);
    const B = hexToRgb(b);
    return `rgb(${A.map((v, i) => Math.round(lerp(v, B[i], k))).join(',')})`;
}
