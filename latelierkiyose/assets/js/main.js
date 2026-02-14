/**
 * L'Atelier Kiyose - Main scripts.
 *
 * @package Kiyose
 * @since   0.1.0
 */

import Dropdown from './modules/dropdown.js';
import MobileMenu from './modules/mobile-menu.js';
import ShareLinkCopier from './modules/share-link-copier.js';

document.addEventListener('DOMContentLoaded', () => {
	// Initialize accessible dropdown navigation
	new Dropdown();

	// Initialize mobile menu
	new MobileMenu();

	// Initialize share link copier (Instagram)
	new ShareLinkCopier();
});