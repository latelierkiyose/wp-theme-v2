/**
 * Home Page Animations
 *
 * Manages the hero image entrance animation.
 * Respects prefers-reduced-motion for accessibility.
 *
 * @package Kiyose
 * @since   0.2.4
 */

(function () {
	'use strict';

	// Respect prefers-reduced-motion
	const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	if (prefersReducedMotion) {
		// Skip all animations if user prefers reduced motion
		document.querySelectorAll('.hero-section__image').forEach((el) => {
			el.classList.add('is-visible');
		});
		return;
	}

	// Hero image animation on load
	window.addEventListener('load', () => {
		setTimeout(() => {
			const heroImage = document.querySelector('.hero-section__image');
			if (heroImage) {
				heroImage.classList.add('is-visible');
			}
		}, 200);
	});
})();
