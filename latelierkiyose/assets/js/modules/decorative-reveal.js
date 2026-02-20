/**
 * Decorative Reveal Animations
 *
 * Handles scroll-triggered reveal animations for decorative elements.
 * Uses Intersection Observer for viewport detection and requestAnimationFrame
 * for parallax scrolling. Respects prefers-reduced-motion for accessibility.
 *
 * @package Kiyose
 * @since   2.0.0
 */

(function () {
	'use strict';

	const MAX_FLOATING = 3;
	const STAGGER_DELAY = 150;
	const REVEAL_DURATION = 500;

	const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	// If reduced motion, reveal everything immediately and bail out.
	if (prefersReducedMotion) {
		document.querySelectorAll('.deco-element').forEach((el) => {
			el.classList.add('is-revealed');
		});
		return;
	}

	// --- Scroll Reveal ---

	/**
	 * Get the stagger index for an element among its siblings within the same
	 * parent section. Returns the index of this element among all .deco-element
	 * children of the nearest section parent.
	 *
	 * @param {Element} el The decorative element.
	 * @return {number} Zero-based stagger index.
	 */
	function getStaggerIndex(el) {
		const parent = el.closest('section') || el.parentElement;
		const siblings = parent.querySelectorAll('.deco-element');
		for (let i = 0; i < siblings.length; i++) {
			if (siblings[i] === el) {
				return i;
			}
		}
		return 0;
	}

	/**
	 * Reveal a single decorative element with an optional stagger delay.
	 *
	 * @param {Element} el           The element to reveal.
	 * @param {number}  staggerIndex Zero-based sibling index for stagger timing.
	 */
	function revealElement(el, staggerIndex) {
		const delay = staggerIndex * STAGGER_DELAY;

		setTimeout(() => {
			el.classList.add('is-revealed');

			// After the reveal transition completes, start floating if applicable.
			if (el.classList.contains('deco-element--float')) {
				setTimeout(() => {
					applyFloating(el);
				}, REVEAL_DURATION);
			}
		}, delay);
	}

	// --- Float Management ---

	const floatingElements = [];

	/**
	 * Apply the floating class to an element if the max concurrent floating
	 * limit has not been reached.
	 *
	 * @param {Element} el The element to float.
	 */
	function applyFloating(el) {
		if (floatingElements.length < MAX_FLOATING) {
			el.classList.add('is-floating');
			floatingElements.push(el);
		}
	}

	/**
	 * Remove the floating class from an element that has left the viewport.
	 *
	 * @param {Element} el The element to stop floating.
	 */
	function removeFloating(el) {
		el.classList.remove('is-floating');
		const idx = floatingElements.indexOf(el);
		if (idx !== -1) {
			floatingElements.splice(idx, 1);
		}
	}

	// --- Intersection Observers ---

	// Observer for reveal animations on .deco-element.
	const revealObserver = new IntersectionObserver(
		((entries) => {
			entries.forEach((entry) => {
				const el = entry.target;

				if (entry.isIntersecting) {
					if (!el.classList.contains('is-revealed')) {
						revealElement(el, getStaggerIndex(el));
					}

					// Re-apply floating when element scrolls back into view.
					if (
						el.classList.contains('is-revealed') &&
						el.classList.contains('deco-element--float') &&
						!el.classList.contains('is-floating')
					) {
						applyFloating(el);
					}
				} else {
					// Pause floating when element leaves viewport.
					if (el.classList.contains('is-floating')) {
						removeFloating(el);
					}
				}
			});
		}),
		{
			threshold: 0,
			rootMargin: '0px',
		}
	);

	document.querySelectorAll('.deco-element').forEach((el) => {
		revealObserver.observe(el);
	});

	// --- Parallax ---

	const parallaxElements = [];
	let ticking = false;

	/**
	 * Track which .deco-shape elements are currently in the viewport so the
	 * scroll handler only processes visible elements.
	 */
	const parallaxObserver = new IntersectionObserver(
		((entries) => {
			entries.forEach((entry) => {
				const el = entry.target;
				const idx = parallaxElements.indexOf(el);

				if (entry.isIntersecting && idx === -1) {
					parallaxElements.push(el);
				} else if (!entry.isIntersecting && idx !== -1) {
					parallaxElements.splice(idx, 1);
					el.style.transform = '';
				}
			});
		}),
		{
			threshold: 0,
			rootMargin: '50px',
		}
	);

	document.querySelectorAll('.deco-shape').forEach((el) => {
		parallaxObserver.observe(el);
	});

	/**
	 * Apply a subtle vertical parallax translation to visible .deco-shape
	 * elements based on their scroll position within the viewport.
	 */
	function updateParallax() {
		const windowHeight = window.innerHeight;

		parallaxElements.forEach((el) => {
			const rect = el.getBoundingClientRect();
			const elementHeight = rect.height || 1;

			// scrollProgress: 0 when element enters bottom, 1 when it exits top.
			let scrollProgress = 1 - (rect.top + elementHeight) / (windowHeight + elementHeight);
			scrollProgress = Math.max(0, Math.min(1, scrollProgress));

			const translateY = scrollProgress * -0.05 * elementHeight;
			el.style.transform = 'translateY(' + translateY + 'px)';
		});
	}

	window.addEventListener('scroll', () => {
		if (!ticking) {
			window.requestAnimationFrame(() => {
				updateParallax();
				ticking = false;
			});
			ticking = true;
		}
	});

	// Initial parallax check.
	updateParallax();
})();
