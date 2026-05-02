/**
 * Accessible dropdown navigation module
 * L'Atelier Kiyose
 *
 * Handles keyboard navigation and ARIA states for dropdown menus.
 */

export default class Dropdown {
	constructor() {
		this.menuItems = document.querySelectorAll('.main-nav__list .menu-item-has-children');
		this.skipNextFocusOpen = new WeakSet();
		this.init();
	}

	init() {
		if (!this.menuItems.length) {
			return;
		}

		this.menuItems.forEach((menuItem) => {
			const link = menuItem.querySelector('a');
			const submenu = menuItem.querySelector('.sub-menu');

			if (!link || !submenu) {
				return;
			}

			// Set initial ARIA attributes
			link.setAttribute('aria-haspopup', 'true');
			link.setAttribute('aria-expanded', 'false');

			// Handle keyboard navigation
			link.addEventListener('keydown', (e) => {
				this.handleKeyDown(e, link, submenu);
			});

			menuItem.addEventListener('mouseenter', () => {
				this.openDropdown(link, submenu);
			});

			menuItem.addEventListener('mouseleave', () => {
				if (!menuItem.contains(document.activeElement)) {
					this.closeDropdown(link, submenu);
				}
			});

			menuItem.addEventListener('focusin', () => {
				if (this.skipNextFocusOpen.has(link)) {
					this.skipNextFocusOpen.delete(link);
					return;
				}

				this.openDropdown(link, submenu);
			});

			// Close dropdown when focus leaves the menu item
			menuItem.addEventListener('focusout', (e) => {
				// Check if focus is moving outside the menu item
				if (!menuItem.contains(e.relatedTarget)) {
					this.closeDropdown(link, submenu);
				}
			});

			// Handle escape key to close dropdown
			submenu.addEventListener('keydown', (e) => {
				if (e.key === 'Escape') {
					this.closeDropdown(link, submenu);
					this.skipNextFocusOpen.add(link);
					link.focus();
				}
			});
		});
	}

	openDropdown(link, submenu) {
		link.setAttribute('aria-expanded', 'true');
		submenu.classList.add('is-open');
	}

	closeDropdown(link, submenu) {
		link.setAttribute('aria-expanded', 'false');
		submenu.classList.remove('is-open');
	}

	focusFirstSubmenuLink(submenu) {
		const firstLink = submenu.querySelector('a');
		if (firstLink) {
			firstLink.focus();
		}
	}

	focusLastSubmenuLink(submenu) {
		const submenuLinks = submenu.querySelectorAll('a');
		if (submenuLinks.length) {
			submenuLinks[submenuLinks.length - 1].focus();
		}
	}

	handleKeyDown(e, link, submenu) {
		const isExpanded = link.getAttribute('aria-expanded') === 'true';

		switch (e.key) {
			case ' ':
				e.preventDefault();
				this.openDropdown(link, submenu);
				this.focusFirstSubmenuLink(submenu);
				break;

			case 'Escape':
				if (isExpanded) {
					e.preventDefault();
					this.closeDropdown(link, submenu);
					this.skipNextFocusOpen.add(link);
					link.focus();
				}
				break;

			case 'ArrowDown':
				e.preventDefault();
				this.openDropdown(link, submenu);
				this.focusFirstSubmenuLink(submenu);
				break;

			case 'ArrowUp':
				if (isExpanded) {
					e.preventDefault();
					this.focusLastSubmenuLink(submenu);
				}
				break;
		}
	}
}
