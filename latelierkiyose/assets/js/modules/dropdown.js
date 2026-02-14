/**
 * Accessible dropdown navigation module
 * L'Atelier Kiyose
 *
 * Handles keyboard navigation and ARIA states for dropdown menus.
 */

export default class Dropdown {
	constructor() {
		this.menuItems = document.querySelectorAll('.main-nav__list .menu-item-has-children');
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

			// Handle click on parent link (toggle dropdown)
			link.addEventListener('click', (e) => {
				if (window.innerWidth >= 1024) {
					e.preventDefault();
					this.toggleDropdown(link, submenu);
				}
			});

			// Handle keyboard navigation
			link.addEventListener('keydown', (e) => {
				this.handleKeyDown(e, link, submenu, menuItem);
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
					link.focus();
				}
			});
		});
	}

	toggleDropdown(link, submenu) {
		const isExpanded = link.getAttribute('aria-expanded') === 'true';

		if (isExpanded) {
			this.closeDropdown(link, submenu);
		} else {
			this.openDropdown(link, submenu);
		}
	}

	openDropdown(link, submenu) {
		link.setAttribute('aria-expanded', 'true');
		submenu.classList.add('is-open');
	}

	closeDropdown(link, submenu) {
		link.setAttribute('aria-expanded', 'false');
		submenu.classList.remove('is-open');
	}

	handleKeyDown(e, link, submenu, menuItem) {
		const isExpanded = link.getAttribute('aria-expanded') === 'true';

		switch (e.key) {
			case 'Enter':
			case ' ':
				e.preventDefault();
				this.toggleDropdown(link, submenu);
				if (!isExpanded) {
					// Focus first item in submenu
					const firstLink = submenu.querySelector('a');
					if (firstLink) {
						firstLink.focus();
					}
				}
				break;

			case 'Escape':
				if (isExpanded) {
					e.preventDefault();
					this.closeDropdown(link, submenu);
				}
				break;

			case 'ArrowDown':
				if (isExpanded) {
					e.preventDefault();
					const firstLink = submenu.querySelector('a');
					if (firstLink) {
						firstLink.focus();
					}
				} else {
					e.preventDefault();
					this.openDropdown(link, submenu);
					const firstLink = submenu.querySelector('a');
					if (firstLink) {
						firstLink.focus();
					}
				}
				break;

			case 'ArrowUp':
				if (isExpanded) {
					e.preventDefault();
					const lastLink = submenu.querySelectorAll('a');
					if (lastLink.length) {
						lastLink[lastLink.length - 1].focus();
					}
				}
				break;
		}
	}
}
