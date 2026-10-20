/* global tarteaucitron */

window.tarteaucitronCustomText = {
	'engage-brevoform':
		"Pour afficher le formulaire et recevoir l'ebook, merci d'autoriser la protection anti-spam de Google (reCAPTCHA).",
};

tarteaucitron.init({
	privacyUrl: '/politique-de-confidentialite/',
	hashtag: '#tarteaucitron',
	cookieName: 'tarteaucitron',
	orientation: 'bottom',
	showAlertSmall: false,
	cookieslist: true,
	showIcon: true,
	iconPosition: 'BottomLeft',
	DenyAllCta: true,
	AcceptAllCta: true,
	highPrivacy: true,
	handleBrowserDNTRequest: false,
	removeCredit: false,
	moreInfoLink: true,
	mandatory: true,
});

/* ---------- Formulaire ebook (iframe Brevo + Google reCAPTCHA) ---------- */
tarteaucitron.services.brevoform = {
	key: 'brevoform',
	type: 'other',
	name: 'Formulaire ebook (protection anti-spam Google reCAPTCHA)',
	uri: 'https://www.brevo.com/fr/legal/privacypolicy/',
	needConsent: true,
	cookies: ['_GRECAPTCHA'],
	js: function () {
		'use strict';
		tarteaucitron.fallback(['brevo-iframe'], (x) => {
			const width = x.getAttribute('data-width'),
				height = x.getAttribute('data-height'),
				url = x.getAttribute('data-url');
			return (
				'<iframe src="' +
				url +
				'" width="' +
				width +
				'" height="' +
				height +
				'" frameborder="0" scrolling="auto" allowfullscreen style="display:block;margin:0 auto;max-width:100%;"></iframe>'
			);
		});
	},
	fallback: function () {
		'use strict';
		tarteaucitron.fallback(['brevo-iframe'], () => {
			return tarteaucitron.engage('brevoform');
		});
	},
};
(tarteaucitron.job = tarteaucitron.job || []).push('brevoform');
