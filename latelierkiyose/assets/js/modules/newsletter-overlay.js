/**
 * Newsletter Overlay
 *
 * Panneau flottant « Newsletter » déclenché au scroll.
 * Desktop uniquement (>= 768px).
 *
 * Comportement :
 * - Apparaît quand le bord supérieur de .home-events atteint le viewport (zone = 'below').
 * - Disparaît quand l'utilisateur remonte au-dessus (zone = 'above').
 * - Fermeture manuelle = masque l'overlay, mais il réapparaît au prochain passage
 *   vers la zone 'below' (non permanent, contrairement à l'overlay « À propos »).
 * - Pas de focus trap : le contenu derrière reste accessible au clavier.
 *
 * Coordination :
 * - Écoute le CustomEvent 'kiyose:overlay-zone' émis par about-overlay.js.
 * - Ne gère pas son propre scroll listener.
 *
 * @package Kiyose
 * @since   2.0.0
 */

(function () {
	'use strict';

	const DESKTOP_BREAKPOINT = 768;

	const overlay = document.getElementById('newsletter-overlay');
	const closeBtn = document.getElementById('newsletter-overlay-close');
	const announcement = document.getElementById('overlay-announcement');
	const overlayFormSlot = overlay ? overlay.querySelector('[data-newsletter-overlay-form]') : null;
	const footerFormSlot = document.querySelector('[data-newsletter-footer-form]');

	if (!overlay || !closeBtn) {
		return;
	}

	const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	let isVisible = false;
	let isManuallyClosed = false;
	let hasAnimatedOnce = false;
	let lastZone = null;

	/**
	 * Move the single Brevo form instance between footer and desktop overlay.
	 *
	 * @param {Element|null} targetSlot Destination slot.
	 */
	function moveNewsletterForm(targetSlot) {
		if (!targetSlot || !overlayFormSlot || !footerFormSlot) {
			return;
		}

		const sourceSlot = targetSlot === overlayFormSlot ? footerFormSlot : overlayFormSlot;

		while (sourceSlot.firstChild) {
			targetSlot.appendChild(sourceSlot.firstChild);
		}
	}

	/**
	 * Show the overlay.
	 *
	 * @param {boolean} shouldAnimate Whether to play the arc entrance animation.
	 */
	function showOverlay(shouldAnimate) {
		if (isVisible) {
			moveNewsletterForm(overlayFormSlot);
			return;
		}

		moveNewsletterForm(overlayFormSlot);
		overlay.removeAttribute('hidden');
		isVisible = true;

		if (shouldAnimate && !prefersReducedMotion && !hasAnimatedOnce) {
			hasAnimatedOnce = true;
			overlay.classList.add('newsletter-overlay--entering');
			overlay.addEventListener(
				'animationend',
				() => {
					overlay.classList.remove('newsletter-overlay--entering');
				},
				{ once: true }
			);
		}

		if (announcement) {
			announcement.textContent = 'Le panneau Newsletter est maintenant visible.';
		}
	}

	/** Hide the overlay (zone transition, not manual close). */
	function hideOverlay() {
		moveNewsletterForm(footerFormSlot);

		if (!isVisible) {
			return;
		}

		overlay.setAttribute('hidden', '');
		overlay.classList.remove('newsletter-overlay--entering');
		isVisible = false;

		if (announcement) {
			announcement.textContent = '';
		}
	}

	/** Close the overlay manually and restore focus. */
	function closeOverlay() {
		hideOverlay();
		isManuallyClosed = true;
	}

	/**
	 * Handle zone change from about-overlay's CustomEvent.
	 *
	 * @param {CustomEvent} event The kiyose:overlay-zone event.
	 */
	function onZoneChange(event) {
		const zone = event.detail && event.detail.zone;

		if (window.innerWidth < DESKTOP_BREAKPOINT) {
			moveNewsletterForm(footerFormSlot);
			return;
		}

		// On zone transition, reset manual close so overlay can reappear.
		if (zone !== lastZone && zone === 'below') {
			isManuallyClosed = false;
		}

		lastZone = zone;

		if (zone === 'below' && !isManuallyClosed) {
			showOverlay(true);
		} else {
			hideOverlay();
		}
	}

	document.addEventListener('kiyose:overlay-zone', onZoneChange);

	closeBtn.addEventListener('click', closeOverlay);

	document.addEventListener('keydown', (e) => {
		if (e.key === 'Escape' && isVisible) {
			closeOverlay();
		}
	});

	window.addEventListener('resize', () => {
		if (window.innerWidth < DESKTOP_BREAKPOINT) {
			hideOverlay();
		}
	});
})();
