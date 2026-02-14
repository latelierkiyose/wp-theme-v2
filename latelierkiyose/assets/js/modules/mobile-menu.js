/**
 * L'Atelier Kiyose - Mobile Menu Module.
 *
 * Handles mobile menu (hamburger) with accessibility features:
 * - Focus trap
 * - Escape key to close
 * - Overlay click to close
 * - Body scroll lock when menu is open
 * - Respects prefers-reduced-motion
 *
 * @package Kiyose
 * @since   0.1.0
 */

export default class MobileMenu {
	constructor() {
		this.hamburgerButton = document.querySelector('.hamburger-button');
		this.mobileMenu = document.getElementById('mobile-menu');
		this.overlay = document.querySelector('.mobile-menu-overlay');
		this.closeButton = document.querySelector('.mobile-menu__close');

		if (!this.hamburgerButton || !this.mobileMenu || !this.overlay) {
			return;
		}

		this.isOpen = false;
		this.focusableElements = [];
		this.firstFocusableElement = null;
		this.lastFocusableElement = null;

		this.init();
	}

	init() {
		// Bind event listeners
		this.hamburgerButton.addEventListener('click', () => this.open());
		this.closeButton?.addEventListener('click', () => this.close());
		this.overlay.addEventListener('click', () => this.close());

		// Escape key to close
		document.addEventListener('keydown', (e) => {
			if (e.key === 'Escape' && this.isOpen) {
				this.close();
			}
		});

		// Focus trap
		this.mobileMenu.addEventListener('keydown', (e) => {
			if (e.key === 'Tab' && this.isOpen) {
				this.handleTab(e);
			}
		});

		// Update focusable elements when menu is opened
		this.updateFocusableElements();
	}

	/**
	 * Get all focusable elements inside the mobile menu.
	 */
	updateFocusableElements() {
		const focusableSelectors = [
			'a[href]',
			'button:not([disabled])',
			'input:not([disabled])',
			'textarea:not([disabled])',
			'select:not([disabled])',
			'[tabindex]:not([tabindex="-1"])'
		].join(', ');

		this.focusableElements = Array.from(
			this.mobileMenu.querySelectorAll(focusableSelectors)
		);

		if (this.focusableElements.length > 0) {
			this.firstFocusableElement = this.focusableElements[0];
			this.lastFocusableElement = this.focusableElements[this.focusableElements.length - 1];
		}
	}

	/**
	 * Handle Tab key for focus trap.
	 *
	 * @param {KeyboardEvent} e - The keyboard event.
	 */
	handleTab(e) {
		if (this.focusableElements.length === 0) {
			return;
		}

		// Shift + Tab (backwards)
		if (e.shiftKey) {
			if (document.activeElement === this.firstFocusableElement) {
				e.preventDefault();
				this.lastFocusableElement.focus();
			}
		} else {
			// Tab (forwards)
			if (document.activeElement === this.lastFocusableElement) {
				e.preventDefault();
				this.firstFocusableElement.focus();
			}
		}
	}

	/**
	 * Open the mobile menu.
	 */
	open() {
		if (this.isOpen) {
			return;
		}

		this.isOpen = true;

		// Update ARIA attributes
		this.hamburgerButton.setAttribute('aria-expanded', 'true');
		this.hamburgerButton.setAttribute('aria-label', 'Fermer le menu de navigation');
		this.mobileMenu.setAttribute('aria-hidden', 'false');
		this.overlay.setAttribute('aria-hidden', 'false');

		// Lock body scroll
		document.body.style.overflow = 'hidden';

		// Update focusable elements and focus close button
		this.updateFocusableElements();
		if (this.closeButton) {
			this.closeButton.focus();
		} else if (this.firstFocusableElement) {
			this.firstFocusableElement.focus();
		}
	}

	/**
	 * Close the mobile menu.
	 */
	close() {
		if (!this.isOpen) {
			return;
		}

		this.isOpen = false;

		// Update ARIA attributes
		this.hamburgerButton.setAttribute('aria-expanded', 'false');
		this.hamburgerButton.setAttribute('aria-label', 'Ouvrir le menu de navigation');
		this.mobileMenu.setAttribute('aria-hidden', 'true');
		this.overlay.setAttribute('aria-hidden', 'true');

		// Unlock body scroll
		document.body.style.overflow = '';

		// Return focus to hamburger button
		this.hamburgerButton.focus();
	}
}
