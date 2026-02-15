/**
 * Share Link Copier
 *
 * Handles copying article URLs to clipboard for social sharing (Instagram).
 *
 * @since 0.1.0
 */

class ShareLinkCopier {
	constructor() {
		this.buttons = document.querySelectorAll('.js-copy-link');
		this.init();
	}

	init() {
		if (this.buttons.length === 0) {
			return;
		}

		this.buttons.forEach((button) => {
			button.addEventListener('click', (e) => this.handleCopy(e));
		});
	}

	async handleCopy(event) {
		const button = event.currentTarget;
		const url = button.dataset.url;

		if (!url) {
			return;
		}

		try {
			// Use modern Clipboard API
			await navigator.clipboard.writeText(url);
			this.showSuccess(button);
		} catch (error) {
			// Fallback for older browsers
			this.fallbackCopy(url, button);
		}
	}

	fallbackCopy(text, button) {
		const textarea = document.createElement('textarea');
		textarea.value = text;
		textarea.style.position = 'fixed';
		textarea.style.opacity = '0';
		document.body.appendChild(textarea);
		textarea.focus();
		textarea.select();

		try {
			document.execCommand('copy');
			this.showSuccess(button);
		} catch (error) {
			// eslint-disable-next-line no-console
			console.error('Failed to copy link:', error);
			this.showError(button);
		}

		document.body.removeChild(textarea);
	}

	showSuccess(button) {
		// Add visual feedback
		button.classList.add('copied');

		// Update aria-label temporarily
		const originalLabel = button.getAttribute('aria-label');
		button.setAttribute('aria-label', 'Lien copié !');

		// Create and show tooltip/notification
		const notification = this.createNotification(
			'Lien copié ! Vous pouvez maintenant le coller sur Instagram.'
		);
		button.parentElement.appendChild(notification);

		// Reset after 3 seconds
		setTimeout(() => {
			button.classList.remove('copied');
			button.setAttribute('aria-label', originalLabel);
			notification.remove();
		}, 3000);
	}

	showError(button) {
		const notification = this.createNotification('Erreur lors de la copie du lien.', 'error');
		button.parentElement.appendChild(notification);

		setTimeout(() => {
			notification.remove();
		}, 3000);
	}

	createNotification(message, type = 'success') {
		const notification = document.createElement('div');
		notification.className = `copy-notification copy-notification--${type}`;
		notification.setAttribute('role', 'status');
		notification.setAttribute('aria-live', 'polite');
		notification.textContent = message;
		return notification;
	}
}

export default ShareLinkCopier;
