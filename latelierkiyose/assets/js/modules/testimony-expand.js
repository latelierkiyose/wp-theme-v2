/**
 * TestimonyExpand - Expand/collapse for truncated testimony cards in carousels.
 *
 * Detects overflow on line-clamped quotes and shows a "Voir plus" toggle.
 * Expanded cards overlay content below without shifting page layout.
 * Conforms to WCAG 2.2 AA: focus trap, Escape to close, aria-expanded.
 *
 * @package Kiyose
 * @since   0.3.0
 */

const CLOSE_ICON_SVG =
	'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>';

const VOIR_PLUS_TEXT = 'Voir plus';
const FERMER_LABEL = 'Fermer';
const RESIZE_DEBOUNCE_MS = 250;

export class TestimonyExpand {
	constructor() {
		this.expandedCard = null;
		this.expandedToggle = null;
		this.expandedCarouselEl = null;
		this.expandedSlide = null;
		this.boundHandleKeydown = this.handleKeydown.bind(this);
		this.boundHandleOutsideClick = this.handleOutsideClick.bind(this);
		this.resizeTimer = null;
	}

	init() {
		this.checkAllOverflows();

		window.addEventListener('resize', () => {
			clearTimeout(this.resizeTimer);
			this.resizeTimer = setTimeout(() => this.checkAllOverflows(), RESIZE_DEBOUNCE_MS);
		});
	}

	/**
	 * Check overflow on all carousel testimony quotes.
	 */
	checkAllOverflows() {
		const quotes = document.querySelectorAll('.carousel__slide .testimony-card__quote');
		quotes.forEach((quoteEl) => this.checkOverflow(quoteEl));
	}

	/**
	 * Compare scrollHeight vs clientHeight to detect truncation.
	 *
	 * @param {HTMLElement} quoteEl The .testimony-card__quote element.
	 */
	checkOverflow(quoteEl) {
		const card = quoteEl.closest('.testimony-card');
		if (!card) {
			return;
		}

		const toggle = card.querySelector('.testimony-card__toggle');
		if (!toggle) {
			return;
		}

		// Skip check on expanded cards
		if (card.classList.contains('testimony-card--expanded')) {
			return;
		}

		if (quoteEl.scrollHeight > quoteEl.clientHeight) {
			toggle.hidden = false;
			this.bindToggle(toggle, card);
		} else {
			toggle.hidden = true;
			toggle.setAttribute('aria-expanded', 'false');
		}
	}

	/**
	 * Attach click listener to a toggle button (idempotent).
	 *
	 * @param {HTMLButtonElement} toggle The toggle button.
	 * @param {HTMLElement}       card   The .testimony-card element.
	 */
	bindToggle(toggle, card) {
		if (toggle._kiyoseToggleBound) {
			return;
		}
		toggle._kiyoseToggleBound = true;

		toggle.addEventListener('click', () => {
			const isExpanded = card.classList.contains('testimony-card--expanded');
			if (isExpanded) {
				this.collapse();
			} else {
				this.expand(card, toggle);
			}
		});
	}

	/**
	 * Expand a testimony card.
	 *
	 * @param {HTMLElement}       card   The .testimony-card element.
	 * @param {HTMLButtonElement} toggle The toggle button.
	 */
	expand(card, toggle) {
		// Collapse any previously expanded card first
		if (this.expandedCard) {
			this.collapse();
		}

		const carouselEl = card.closest('.carousel');

		this.expandedCard = card;
		this.expandedToggle = toggle;
		this.expandedCarouselEl = carouselEl;

		// Lock slide height before card becomes absolute-positioned
		const slide = card.closest('.carousel__slide');
		if (slide) {
			slide.style.height = `${slide.offsetHeight}px`;
			this.expandedSlide = slide;
		}

		// Add expanded modifiers
		card.classList.add('testimony-card--expanded');
		if (carouselEl) {
			carouselEl.classList.add('carousel--has-expanded');
		}

		// Update toggle to close button
		toggle.setAttribute('aria-expanded', 'true');
		toggle.setAttribute('aria-label', FERMER_LABEL);
		toggle.innerHTML = CLOSE_ICON_SVG;

		// Stop carousel autoplay and disable navigation
		if (carouselEl) {
			const carouselInstance = carouselEl._kiyoseCarousel;
			if (carouselInstance) {
				carouselInstance.stopAutoplay();
				carouselInstance.disableNavigation();
			}
		}

		// Install close listeners
		document.addEventListener('keydown', this.boundHandleKeydown);
		// Delay outside click listener to avoid closing immediately from the same click
		requestAnimationFrame(() => {
			document.addEventListener('mousedown', this.boundHandleOutsideClick);
		});

		// Focus the close button
		toggle.focus();
	}

	/**
	 * Collapse the currently expanded testimony card.
	 */
	collapse() {
		const card = this.expandedCard;
		const toggle = this.expandedToggle;
		const carouselEl = this.expandedCarouselEl;

		if (!card || !toggle) {
			return;
		}

		// Remove expanded modifiers
		card.classList.remove('testimony-card--expanded');
		if (carouselEl) {
			carouselEl.classList.remove('carousel--has-expanded');
		}

		// Restore toggle to "Voir plus"
		toggle.setAttribute('aria-expanded', 'false');
		toggle.removeAttribute('aria-label');
		toggle.textContent = VOIR_PLUS_TEXT;

		// Re-enable carousel navigation (do NOT restart autoplay)
		if (carouselEl) {
			const carouselInstance = carouselEl._kiyoseCarousel;
			if (carouselInstance) {
				carouselInstance.enableNavigation();
			}
		}

		// Remove close listeners
		document.removeEventListener('keydown', this.boundHandleKeydown);
		document.removeEventListener('mousedown', this.boundHandleOutsideClick);

		// Restore slide height
		if (this.expandedSlide) {
			this.expandedSlide.style.height = '';
			this.expandedSlide = null;
		}

		// Clear state
		this.expandedCard = null;
		this.expandedToggle = null;
		this.expandedCarouselEl = null;

		// Return focus to the toggle
		toggle.focus();
	}

	/**
	 * Handle keydown events when a card is expanded.
	 *
	 * @param {KeyboardEvent} event
	 */
	handleKeydown(event) {
		if (!this.expandedCard) {
			return;
		}

		if (event.key === 'Escape') {
			event.preventDefault();
			this.collapse();
			return;
		}

		// Focus trap: keep Tab/Shift+Tab within the expanded card
		if (event.key === 'Tab') {
			const focusable = this.getFocusableElements(this.expandedCard);
			if (focusable.length === 0) {
				event.preventDefault();
				return;
			}

			const first = focusable[0];
			const last = focusable[focusable.length - 1];

			if (event.shiftKey && document.activeElement === first) {
				event.preventDefault();
				last.focus();
			} else if (!event.shiftKey && document.activeElement === last) {
				event.preventDefault();
				first.focus();
			}
		}
	}

	/**
	 * Handle clicks outside the expanded card.
	 *
	 * @param {MouseEvent} event
	 */
	handleOutsideClick(event) {
		if (!this.expandedCard) {
			return;
		}

		if (!this.expandedCard.contains(event.target)) {
			this.collapse();
		}
	}

	/**
	 * Get all focusable elements within a container.
	 *
	 * @param {HTMLElement} container
	 * @return {HTMLElement[]}
	 */
	getFocusableElements(container) {
		const selectors =
			'a[href], button:not([disabled]), input:not([disabled]), textarea:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex="-1"])';
		return Array.from(container.querySelectorAll(selectors)).filter(
			(el) => !el.hidden && el.offsetParent !== null
		);
	}
}
