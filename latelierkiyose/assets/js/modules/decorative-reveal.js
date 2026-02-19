/**
 * Decorative Reveal - Collage Créatif
 *
 * Scroll-triggered reveal of micro-doodle decorative elements.
 * Uses Intersection Observer to add .is-revealed class.
 * Respects prefers-reduced-motion.
 *
 * @package Kiyose
 * @since   2.0.0
 */

(function () {
	'use strict';

	const micros = document.querySelectorAll('.collage-micro');
	if (!micros.length) {
		return;
	}

	const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	if (prefersReducedMotion) {
		micros.forEach((el) => {
			el.classList.add('is-revealed');
		});
		return;
	}

	// Group micros by their parent section.
	const sectionMap = new Map();
	micros.forEach((el) => {
		const section = el.closest('section');
		if (!section) {
			return;
		}
		if (!sectionMap.has(section)) {
			sectionMap.set(section, []);
		}
		sectionMap.get(section).push(el);
	});

	const observer = new IntersectionObserver(
		((entries) => {
			entries.forEach((entry) => {
				if (!entry.isIntersecting) {
					return;
				}

				const sectionMicros = sectionMap.get(entry.target);
				if (!sectionMicros) {
					return;
				}

				sectionMicros.forEach((el) => {
					el.style.willChange = 'opacity';
				});

				// Reveal with cascade (delay is handled via inline CSS transition-delay).
				requestAnimationFrame(() => {
					sectionMicros.forEach((el) => {
						el.classList.add('is-revealed');
					});

					// Clean up will-change after transitions complete.
					const maxDelay = sectionMicros.length * 100 + 300;
					setTimeout(() => {
						sectionMicros.forEach((el) => {
							el.style.willChange = '';
						});
					}, maxDelay);
				});

				// Stop observing this section.
				observer.unobserve(entry.target);
			});
		}),
		{
			threshold: 0.15,
			rootMargin: '0px 0px -50px 0px',
		}
	);

	// Observe each section that contains micro-elements.
	sectionMap.forEach((_micros, section) => {
		observer.observe(section);
	});
})();
