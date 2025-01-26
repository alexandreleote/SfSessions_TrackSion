import { initHeaderScroll } from './headerScroll.js';
import { initModalHandlers } from './modalHandler.js';
import { initCards } from './cards.js';
import { initSidebar } from './sidebar.js';
import { initStats } from './stats.js';
import './collectionHandler.js';

document.addEventListener('DOMContentLoaded', function() {
    initHeaderScroll();
    initModalHandlers();
    initCards();
    initSidebar();
    initStats();
});