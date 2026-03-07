/**
 * About Overlay
 *
 * Panneau flottant « À propos » déclenché au premier scroll.
 * Desktop uniquement (>= 768px).
 *
 * Comportement :
 * - Déclenché dès 30px de scroll.
 * - Affiché immédiatement (sans animation) si la page est déjà scrollée.
 * - Fermeture : bouton × ou Échap.
 * - Fermé = ne revient pas jusqu'au prochain rechargement de la page.
 * - Pas de focus trap : le contenu derrière reste accessible au clavier.
 *
 * @package Kiyose
 * @since   0.2.6
 */

(function () {
	'use strict';

	const SCROLL_THRESHOLD = 30;
	const DESKTOP_BREAKPOINT = 768;

	const overlay = document.getElementById('about-overlay');
	const closeBtn = document.getElementById('about-overlay-close');
	const announcement = document.getElementById('about-overlay-announcement');

	if (!overlay || !closeBtn) {
		return;
	}

	// Desktop only.
	if (window.innerWidth < DESKTOP_BREAKPOINT) {
		return;
	}

	const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	/** Set to true once the overlay is closed — resets on every page load. */
	let isClosed = false;

	/** Element focused before overlay appeared, for focus restoration on close. */
	let focusedElementBeforeOverlay = null;

	/**
	 * Show the overlay.
	 *
	 * @param {boolean} shouldAnimate Whether to play the arc entrance animation.
	 */
	function showOverlay(shouldAnimate) {
		// Snapshot current focus — restored on close.
		focusedElementBeforeOverlay = document.activeElement;

		overlay.removeAttribute('hidden');

		if (shouldAnimate && !prefersReducedMotion) {
			overlay.classList.add('about-overlay--entering');
			overlay.addEventListener(
				'animationend',
				() => {
					overlay.classList.remove('about-overlay--entering');
				},
				{ once: true }
			);
		}

		// Announce to screen readers via polite aria-live region.
		if (announcement) {
			announcement.textContent = 'Le panneau À propos est maintenant visible.';
		}

		window.removeEventListener('scroll', onScroll);
	}

	/** Close the overlay and restore focus to previous element. */
	function closeOverlay() {
		overlay.setAttribute('hidden', '');
		overlay.classList.remove('about-overlay--entering');

		isClosed = true;

		if (focusedElementBeforeOverlay && typeof focusedElementBeforeOverlay.focus === 'function') {
			focusedElementBeforeOverlay.focus();
		}

		if (announcement) {
			announcement.textContent = '';
		}
	}

	/** Scroll handler — shows overlay once scroll threshold is reached. */
	function onScroll() {
		if (window.scrollY >= SCROLL_THRESHOLD) {
			showOverlay(true);
		}
	}

	// Show immediately without animation if page is already scrolled (e.g., back navigation).
	if (window.scrollY >= SCROLL_THRESHOLD) {
		showOverlay(false);
	} else {
		window.addEventListener('scroll', onScroll, { passive: true });
	}

	closeBtn.addEventListener('click', closeOverlay);

	document.addEventListener('keydown', (e) => {
		if (e.key === 'Escape' && !overlay.hasAttribute('hidden')) {
			closeOverlay();
		}
	});

	// Hide overlay if viewport resizes below desktop breakpoint.
	window.addEventListener('resize', () => {
		if (window.innerWidth < DESKTOP_BREAKPOINT && !overlay.hasAttribute('hidden')) {
			overlay.setAttribute('hidden', '');
		}
	});
})();
