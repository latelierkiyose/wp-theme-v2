/**
 * KiyoseCarousel - Accessible carousel component
 * Conforming to WCAG 2.2 AA standards
 *
 * @package Kiyose
 * @since   0.1.7
 */

export class KiyoseCarousel {
	/**
	 * @param {HTMLElement} element - The carousel container element
	 */
	constructor(element) {
		this.carousel = element;
		this.track = element.querySelector('.carousel__track');
		this.slides = Array.from(element.querySelectorAll('.carousel__slide'));
		this.prevButton = element.querySelector('.carousel__prev');
		this.nextButton = element.querySelector('.carousel__next');
		this.pauseButton = element.querySelector('.carousel__pause');

		this.currentIndex = 0;
		this.isPlaying = false;
		this.autoplayInterval = null;
		this.autoplayDelay = 5000; // 5 seconds

		// Check for prefers-reduced-motion
		this.prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

		// Bind methods
		this.handlePrevClick = this.handlePrevClick.bind(this);
		this.handleNextClick = this.handleNextClick.bind(this);
		this.handlePauseClick = this.handlePauseClick.bind(this);
		this.handleKeydown = this.handleKeydown.bind(this);
	}

	/**
	 * Initialize the carousel
	 */
	init() {
		if (!this.track || this.slides.length === 0) {
			return;
		}

		// Mark carousel as initialized
		this.carousel.setAttribute('data-carousel-initialized', 'true');

		// Setup initial state
		this.updateSlides();
		this.updateControls();

		// Add event listeners
		if (this.prevButton) {
			this.prevButton.addEventListener('click', this.handlePrevClick);
		}
		if (this.nextButton) {
			this.nextButton.addEventListener('click', this.handleNextClick);
		}
		if (this.pauseButton) {
			this.pauseButton.addEventListener('click', this.handlePauseClick);
		}
		this.carousel.addEventListener('keydown', this.handleKeydown);

		// Start autoplay if motion is not reduced
		if (!this.prefersReducedMotion) {
			this.startAutoplay();
		}
	}

	/**
	 * Navigate to a specific slide
	 * @param {number} index - The slide index
	 */
	goToSlide(index) {
		// Wrap around
		if (index < 0) {
			index = this.slides.length - 1;
		} else if (index >= this.slides.length) {
			index = 0;
		}

		this.currentIndex = index;
		this.updateSlides();
		this.updateControls();
	}

	/**
	 * Go to the next slide
	 */
	nextSlide() {
		this.goToSlide(this.currentIndex + 1);
	}

	/**
	 * Go to the previous slide
	 */
	prevSlide() {
		this.goToSlide(this.currentIndex - 1);
	}

	/**
	 * Update slides visibility and ARIA attributes
	 */
	updateSlides() {
		this.slides.forEach((slide, index) => {
			const isActive = index === this.currentIndex;
			slide.classList.toggle('carousel__slide--active', isActive);
			slide.setAttribute('aria-hidden', !isActive);
			slide.setAttribute('aria-label', `${index + 1} sur ${this.slides.length}`);

			// Update tabindex for focusable elements within slides
			const focusableElements = slide.querySelectorAll('a, button, input, textarea, select');
			focusableElements.forEach((el) => {
				el.setAttribute('tabindex', isActive ? '0' : '-1');
			});
		});

		// Update track position
		const offset = -this.currentIndex * 100;
		this.track.style.transform = `translateX(${offset}%)`;
	}

	/**
	 * Update control buttons state
	 */
	updateControls() {
		// Update prev/next buttons (always enabled for wrapping carousel)
		// For non-wrapping: disable prev on first slide, next on last slide
	}

	/**
	 * Start autoplay
	 */
	startAutoplay() {
		if (this.isPlaying) {
			return;
		}

		this.isPlaying = true;
		if (this.track) {
			this.track.setAttribute('aria-live', 'off');
		}
		if (this.pauseButton) {
			this.pauseButton.setAttribute('aria-label', 'Mettre en pause le défilement automatique');
			this.pauseButton.textContent = '⏸';
		}

		this.autoplayInterval = setInterval(() => {
			this.nextSlide();
		}, this.autoplayDelay);
	}

	/**
	 * Stop autoplay
	 */
	stopAutoplay() {
		if (!this.isPlaying) {
			return;
		}

		this.isPlaying = false;
		if (this.track) {
			this.track.setAttribute('aria-live', 'polite');
		}
		if (this.pauseButton) {
			this.pauseButton.setAttribute('aria-label', 'Reprendre le défilement automatique');
			this.pauseButton.textContent = '▶';
		}

		if (this.autoplayInterval) {
			clearInterval(this.autoplayInterval);
			this.autoplayInterval = null;
		}
	}

	/**
	 * Toggle autoplay
	 */
	toggleAutoplay() {
		if (this.isPlaying) {
			this.stopAutoplay();
		} else {
			this.startAutoplay();
		}
	}

	/**
	 * Handle previous button click
	 */
	handlePrevClick() {
		this.stopAutoplay();
		this.prevSlide();
	}

	/**
	 * Handle next button click
	 */
	handleNextClick() {
		this.stopAutoplay();
		this.nextSlide();
	}

	/**
	 * Handle pause button click
	 */
	handlePauseClick() {
		this.toggleAutoplay();
	}

	/**
	 * Handle keyboard navigation
	 * @param {KeyboardEvent} event
	 */
	handleKeydown(event) {
		// Only handle arrow keys when carousel has focus
		if (!this.carousel.contains(document.activeElement)) {
			return;
		}

		switch (event.key) {
			case 'ArrowLeft':
				event.preventDefault();
				this.stopAutoplay();
				this.prevSlide();
				break;
			case 'ArrowRight':
				event.preventDefault();
				this.stopAutoplay();
				this.nextSlide();
				break;
		}
	}

	/**
	 * Destroy the carousel and clean up
	 */
	destroy() {
		this.stopAutoplay();
		if (this.prevButton) {
			this.prevButton.removeEventListener('click', this.handlePrevClick);
		}
		if (this.nextButton) {
			this.nextButton.removeEventListener('click', this.handleNextClick);
		}
		if (this.pauseButton) {
			this.pauseButton.removeEventListener('click', this.handlePauseClick);
		}
		this.carousel.removeEventListener('keydown', this.handleKeydown);
	}
}
