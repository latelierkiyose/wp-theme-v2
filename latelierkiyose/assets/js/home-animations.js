/**
 * Home Page Animations
 *
 * Manages scroll-triggered animations using Intersection Observer API.
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
		document.querySelectorAll('.hero-section__image, .service-card').forEach((el) => {
			el.classList.add('is-visible');
		});
		return;
	}

	// 1. Hero image animation on load
	window.addEventListener('load', () => {
		setTimeout(() => {
			const heroImage = document.querySelector('.hero-section__image');
			if (heroImage) {
				heroImage.classList.add('is-visible');
			}
		}, 200);
	});

	// 2. Services grid scroll animation (progressive based on scroll position)
	const servicesGrid = document.querySelector('.home-services__grid');
	if (servicesGrid) {
		const serviceCards = servicesGrid.querySelectorAll('.service-card');
		let isInView = false;

		// Use Intersection Observer to detect when section enters viewport
		const observer = new IntersectionObserver(
			((entries) => {
				entries.forEach((entry) => {
					isInView = entry.isIntersecting;
					if (!isInView) {
						// Reset cards when out of view
						serviceCards.forEach((card) => {
							card.classList.remove('is-visible');
						});
					}
				});
			}),
			{
				threshold: 0,
				rootMargin: '0px',
			}
		);

		observer.observe(servicesGrid);

		// Progressive animation based on scroll position
		const updateServicesAnimation = () => {
			if (!isInView) {
				return;
			}

			const rect = servicesGrid.getBoundingClientRect();
			const windowHeight = window.innerHeight;
			const sectionHeight = rect.height;

			// Determine end point based on section size
			let targetTop;

			if (sectionHeight < windowHeight) {
				// Case 1: Section smaller than viewport
				// Animation completes when section is perfectly centered
				const viewportCenter = windowHeight / 2;
				const sectionCenter = sectionHeight / 2;
				targetTop = viewportCenter - sectionCenter;
			} else {
				// Case 2: Section larger than viewport
				// Animation completes when top of section is at 25% from top
				targetTop = windowHeight * 0.25;
			}

			// Animation starts when bottom of section enters bottom of viewport
			const animationStart = windowHeight;
			// Animation ends when top reaches target position
			const animationEnd = targetTop;

			// Current position of section top
			const currentTop = rect.top;

			// Calculate scroll progress from 0 to 1
			// When currentTop = animationStart, progress = 0
			// When currentTop = animationEnd, progress = 1
			const totalDistance = animationStart - animationEnd;
			const traveledDistance = animationStart - currentTop;
			const scrollProgress =
				totalDistance > 0 ? Math.max(0, Math.min(1, traveledDistance / totalDistance)) : 0;

			if (scrollProgress > 0) {
				serviceCards.forEach((card, index) => {
					// Each card starts animating with a delay based on its index
					const cardDelay = index * 0.15; // 15% delay per card
					const cardProgress = Math.max(0, Math.min(1, (scrollProgress - cardDelay) / (1 - cardDelay)));

					if (cardProgress > 0) {
						card.classList.add('is-visible');
						// Apply progressive transform and opacity
						const translateX = 100 - cardProgress * 100;
						const opacity = cardProgress;
						card.style.transform = 'translateX(' + translateX + 'px)';
						card.style.opacity = opacity;
				} else {
					card.classList.remove('is-visible');
					card.style.transform = 'translateX(100px)';
					card.style.opacity = '0';
				}
			});
		}
	};

	// Listen to scroll events with throttling
		let ticking = false;
		window.addEventListener('scroll', () => {
			if (!ticking) {
				window.requestAnimationFrame(() => {
					updateServicesAnimation();
					ticking = false;
				});
				ticking = true;
			}
		});

		// Initial check
		updateServicesAnimation();
	}
})();
