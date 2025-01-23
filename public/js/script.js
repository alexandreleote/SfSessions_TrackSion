import { initHeaderScroll } from './headerScroll.js';
import { initModalHandlers } from './modalHandler.js';
import './collectionHandler.js';

document.addEventListener('DOMContentLoaded', function() {
    initHeaderScroll();
    initModalHandlers();
});