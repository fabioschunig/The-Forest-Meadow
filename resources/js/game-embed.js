/** Swaps a game's placeholder for its iframe only when the visitor asks to play. */
export function initGameEmbeds() {
    document.querySelectorAll('[data-game-src]').forEach(embed => {
        const button = embed.querySelector('button');

        button?.addEventListener('click', () => {
            const iframe = document.createElement('iframe');
            iframe.src = embed.dataset.gameSrc;
            iframe.title = embed.dataset.gameTitle || '';
            iframe.allow = 'fullscreen; gamepad; autoplay';
            iframe.setAttribute('allowfullscreen', '');
            iframe.setAttribute('sandbox', 'allow-scripts allow-same-origin allow-pointer-lock allow-popups');
            embed.querySelector('.game-gate')?.remove();
            embed.append(iframe);
            iframe.focus();
        });
    });
}
