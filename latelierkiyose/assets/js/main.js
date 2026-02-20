/**
 * L'Atelier Kiyose - Main scripts.
 *
 * @package Kiyose
 * @since   0.1.0
 */

import Dropdown from './modules/dropdown.js';
import MobileMenu from './modules/mobile-menu.js';
import ShareLinkCopier from './modules/share-link-copier.js';
import { KiyoseCarousel } from './modules/carousel.js';

function init() {
	new Dropdown();
	new MobileMenu();
	new ShareLinkCopier();

	const carousels = document.querySelectorAll('.carousel');
	carousels.forEach((carouselElement) => {
		const carousel = new KiyoseCarousel(carouselElement);
		carousel.init();
	});
}

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', init);
} else {
	init();
}
