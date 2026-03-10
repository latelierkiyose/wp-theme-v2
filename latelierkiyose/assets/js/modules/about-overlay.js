/**
 * About Overlay
 *
 * Panneau flottant « À propos » déclenché au scroll.
 * Desktop uniquement (>= 768px).
 *
 * Comportement :
 * - Apparaît dès 30px de scroll (au-dessus de « Prochaines dates »).
 * - Disparaît quand le bord supérieur de .home-events atteint le viewport.
 * - Réapparaît quand l'utilisateur remonte au-dessus de ce seuil (sauf si fermé manuellement).
 * - Fermeture manuelle = ne revient pas jusqu'au prochain rechargement de la page.
 * - Pas de focus trap : le contenu derrière reste accessible au clavier.
 *
 * Coordination :
 * - Émet un CustomEvent 'kiyose:overlay-zone' sur document avec { zone: 'above' | 'below' }
 *   quand la zone de scroll change (au-dessus / en dessous de .home-events).
 * - Les deux overlays (about + newsletter) réagissent à ce même événement.
 *
 * @package Kiyose
 * @since   2.0.0
 */

(function () {
	'use strict';

	const SCROLL_THRESHOLD = 30;
	const DESKTOP_BREAKPOINT = 768;
	const HYSTERESIS_PX = 100;

	const overlay = document.getElementById('about-overlay');
	const closeBtn = document.getElementById('about-overlay-close');
	const announcement = document.getElementById('overlay-announcement');
	const eventsSection = document.querySelector('.home-events');

	if (!overlay || !closeBtn) {
		return;
	}

	const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	let isManuallyClosed = false;
	let isVisible = false;
	let focusedElementBeforeOverlay = null;
	let currentZone = null;

	/**
	 * Determine the current scroll zone relative to .home-events.
	 *
	 * @returns {'above'|'below'} The current zone.
	 */
	function getScrollZone() {
		if (!eventsSection) {
			return 'above';
		}
		const rect = eventsSection.getBoundingClientRect();
		if (currentZone === 'below') {
			return rect.top <= HYSTERESIS_PX ? 'below' : 'above';
		}
		return rect.top <= -HYSTERESIS_PX ? 'below' : 'above';
	}

	/**
	 * Show the overlay.
	 *
	 * @param {boolean} shouldAnimate Whether to play the arc entrance animation.
	 */
	function showOverlay(shouldAnimate) {
		if (isVisible) {
			return;
		}

		focusedElementBeforeOverlay = document.activeElement;
		overlay.removeAttribute('hidden');
		isVisible = true;

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

		if (announcement) {
			announcement.textContent = 'Le panneau À propos est maintenant visible.';
		}
	}

	/** Hide the overlay (scroll-triggered, not manual close). */
	function hideOverlay() {
		if (!isVisible) {
			return;
		}

		overlay.setAttribute('hidden', '');
		overlay.classList.remove('about-overlay--entering');
		isVisible = false;

		if (announcement) {
			announcement.textContent = '';
		}
	}

	/** Close the overlay manually and restore focus. */
	function closeOverlay() {
		hideOverlay();
		isManuallyClosed = true;

		if (focusedElementBeforeOverlay && typeof focusedElementBeforeOverlay.focus === 'function') {
			focusedElementBeforeOverlay.focus();
		}
	}

	/**
	 * Update overlay visibility based on current scroll zone.
	 *
	 * @param {string} zone Current zone ('above' or 'below').
	 * @param {boolean} shouldAnimate Whether to animate on show.
	 */
	function updateVisibility(zone, shouldAnimate) {
		if (window.innerWidth < DESKTOP_BREAKPOINT) {
			return;
		}

		if (zone === 'above' && window.scrollY >= SCROLL_THRESHOLD && !isManuallyClosed) {
			showOverlay(shouldAnimate);
		} else {
			hideOverlay();
		}
	}

	/** Scroll handler with zone detection and event dispatch. */
	function onScroll() {
		const zone = getScrollZone();

		if (zone !== currentZone) {
			currentZone = zone;
			document.dispatchEvent(new CustomEvent('kiyose:overlay-zone', { detail: { zone } }));
		}

		updateVisibility(zone, true);
	}

	// Initial state on page load.
	if (window.innerWidth >= DESKTOP_BREAKPOINT) {
		currentZone = getScrollZone();

		// Dispatch initial zone for newsletter overlay.
		document.dispatchEvent(new CustomEvent('kiyose:overlay-zone', { detail: { zone: currentZone } }));

		updateVisibility(currentZone, false);

		window.addEventListener('scroll', onScroll, { passive: true });
	}

	closeBtn.addEventListener('click', closeOverlay);

	document.addEventListener('keydown', (e) => {
		if (e.key === 'Escape' && isVisible) {
			closeOverlay();
		}
	});

	window.addEventListener('resize', () => {
		if (window.innerWidth < DESKTOP_BREAKPOINT && isVisible) {
			hideOverlay();
		}
	});
})();
