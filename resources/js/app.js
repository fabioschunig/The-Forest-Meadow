import { initDecor } from './decor.js';
import { initGameEmbeds } from './game-embed.js';
import { initScene } from './scene.js';

const scene = document.getElementById('scene');
if (scene) initScene(scene);

initDecor();
initGameEmbeds();
