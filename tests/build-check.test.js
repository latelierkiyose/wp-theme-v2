const assert = require('node:assert/strict');
const { spawnSync } = require('node:child_process');
const fs = require('node:fs');
const os = require('node:os');
const path = require('node:path');
const test = require('node:test');
const vm = require('node:vm');

const rootDir = path.resolve(__dirname, '..');
const aboutOverlayJs = path.join(rootDir, 'latelierkiyose/assets/js/modules/about-overlay.js');
const cssScript = path.join(rootDir, 'bin/minify-css.js');
const jsScript = path.join(rootDir, 'bin/minify-js.js');
const brevoOverrideCss = path.join(
	rootDir,
	'latelierkiyose/assets/css/components/brevo-override.css'
);
const carouselJs = path.join(rootDir, 'latelierkiyose/assets/js/modules/carousel.js');
const dropdownJs = path.join(rootDir, 'latelierkiyose/assets/js/modules/dropdown.js');
const headerCss = path.join(rootDir, 'latelierkiyose/assets/css/components/header.css');
const mobileMenuJs = path.join(rootDir, 'latelierkiyose/assets/js/modules/mobile-menu.js');
const navigationCss = path.join(rootDir, 'latelierkiyose/assets/css/components/navigation.css');
const newsletterOverlayJs = path.join(
	rootDir,
	'latelierkiyose/assets/js/modules/newsletter-overlay.js'
);
const shareLinkCopierJs = path.join(
	rootDir,
	'latelierkiyose/assets/js/modules/share-link-copier.js'
);

class FakeClassList {
	constructor(classes = []) {
		this.classes = new Set(classes);
	}

	add(...classNames) {
		classNames.forEach((className) => this.classes.add(className));
	}

	remove(...classNames) {
		classNames.forEach((className) => this.classes.delete(className));
	}

	contains(className) {
		return this.classes.has(className);
	}

	toggle(className, force) {
		if (force === true) {
			this.classes.add(className);
			return true;
		}

		if (force === false) {
			this.classes.delete(className);
			return false;
		}

		if (this.classes.has(className)) {
			this.classes.delete(className);
			return false;
		}

		this.classes.add(className);
		return true;
	}
}

class FakeElement {
	constructor({
		tagName = 'div',
		id = '',
		classes = [],
		children = [],
		attributes = {},
		dataset = {},
		textContent = '',
	} = {}) {
		this.attributes = new Map();
		this.children = [];
		this.classList = new FakeClassList(classes);
		this.className = classes.join(' ');
		this.dataset = { ...dataset };
		this.disabled = false;
		this.eventListeners = new Map();
		this.hasFocus = false;
		this.hidden = false;
		this.id = id;
		this.ownerDocument = null;
		this.parentElement = null;
		this.style = {};
		this.tagName = tagName.toLowerCase();
		this.textContent = textContent;
		this.value = '';

		if (id) {
			this.setAttribute('id', id);
		}

		Object.entries(attributes).forEach(([attributeName, value]) => {
			this.setAttribute(attributeName, value);
		});

		children.forEach((child) => this.appendChild(child));
	}

	appendChild(child) {
		child.parentElement = this;
		child.assignOwnerDocument(this.ownerDocument);
		this.children.push(child);
		return child;
	}

	removeChild(child) {
		this.children = this.children.filter((item) => item !== child);
		child.parentElement = null;
		return child;
	}

	remove() {
		if (this.parentElement) {
			this.parentElement.removeChild(this);
		}
	}

	addEventListener(eventName, handler) {
		const handlers = this.eventListeners.get(eventName) || [];
		handlers.push(handler);
		this.eventListeners.set(eventName, handlers);
	}

	removeEventListener(eventName, handler) {
		const handlers = this.eventListeners.get(eventName) || [];
		this.eventListeners.set(
			eventName,
			handlers.filter((registeredHandler) => registeredHandler !== handler)
		);
	}

	assignOwnerDocument(ownerDocument) {
		this.ownerDocument = ownerDocument;
		this.children.forEach((child) => child.assignOwnerDocument(ownerDocument));
	}

	contains(element) {
		if (!element) {
			return false;
		}

		let currentElement = element;
		while (currentElement) {
			if (currentElement === this) {
				return true;
			}
			currentElement = currentElement.parentElement;
		}

		return false;
	}

	dispatch(eventName, event) {
		const handlers = this.eventListeners.get(eventName) || [];
		const eventObject = event || {};
		if (!eventObject.target) {
			eventObject.target = this;
		}
		if (!eventObject.currentTarget) {
			eventObject.currentTarget = this;
		}
		handlers.forEach((handler) => handler(eventObject));
	}

	focus() {
		this.hasFocus = true;
		if (this.ownerDocument) {
			this.ownerDocument.activeElement = this;
		}

		let currentElement = this;
		while (currentElement) {
			currentElement.dispatch('focusin', { target: this });
			currentElement = currentElement.parentElement;
		}
	}

	getAttribute(attributeName) {
		return this.attributes.get(attributeName) || null;
	}

	removeAttribute(attributeName) {
		this.attributes.delete(attributeName);

		if (attributeName === 'hidden') {
			this.hidden = false;
		}
	}

	querySelector(selector) {
		return this.querySelectorAll(selector)[0] || null;
	}

	querySelectorAll(selector) {
		const selectors = selector.split(',').map((part) => part.trim());
		const matches = [];

		this.walk((element) => {
			if (element !== this && selectors.some((part) => element.matches(part))) {
				matches.push(element);
			}
		});

		return matches;
	}

	matches(selector) {
		const normalizedSelector = selector.trim().split(/\s+/u).pop();

		if (normalizedSelector === '*') {
			return true;
		}

		if (normalizedSelector.startsWith('#')) {
			return this.id === normalizedSelector.slice(1);
		}

		if (normalizedSelector.startsWith('.')) {
			return this.classList.contains(normalizedSelector.slice(1));
		}

		if (normalizedSelector === 'a') {
			return this.tagName === 'a';
		}

		if (normalizedSelector === 'a[href]') {
			return this.tagName === 'a' && this.attributes.has('href');
		}

		const notDisabledMatch = normalizedSelector.match(/^([a-z]+):not\(\[disabled\]\)$/u);
		if (notDisabledMatch) {
			return this.tagName === notDisabledMatch[1] && !this.disabled;
		}

		if (normalizedSelector === '[tabindex]:not([tabindex="-1"])') {
			return this.attributes.has('tabindex') && this.getAttribute('tabindex') !== '-1';
		}

		return this.tagName === normalizedSelector;
	}

	setAttribute(attributeName, value) {
		const stringValue = String(value);
		this.attributes.set(attributeName, stringValue);

		if (attributeName === 'id') {
			this.id = stringValue;
		}
		if (attributeName === 'hidden') {
			this.hidden = true;
		}
	}

	select() {
		this.hasSelectedText = true;
	}

	walk(callback) {
		callback(this);
		this.children.forEach((child) => child.walk(callback));
	}
}

function createFakeDocument(root = new FakeElement()) {
	const body = new FakeElement({ tagName: 'body' });
	const document = {
		activeElement: null,
		body,
		eventListeners: new Map(),
		addEventListener(eventName, handler) {
			const handlers = this.eventListeners.get(eventName) || [];
			handlers.push(handler);
			this.eventListeners.set(eventName, handlers);
		},
		createElement(tagName) {
			const element = new FakeElement({ tagName });
			element.assignOwnerDocument(this);
			return element;
		},
		dispatch(eventName, event) {
			const handlers = this.eventListeners.get(eventName) || [];
			const eventObject = event || {};
			handlers.forEach((handler) => handler(eventObject));
		},
		dispatchEvent(event) {
			this.dispatch(event.type, event);
		},
		execCommand() {
			return true;
		},
		getElementById(id) {
			return root.querySelector(`#${id}`) || (body.id === id ? body : null);
		},
		querySelector(selector) {
			if (body.matches(selector)) {
				return body;
			}
			return root.querySelector(selector);
		},
		querySelectorAll(selector) {
			const matches = root.querySelectorAll(selector);
			if (body.matches(selector)) {
				matches.unshift(body);
			}
			return matches;
		},
	};

	root.assignOwnerDocument(document);
	body.assignOwnerDocument(document);

	return document;
}

function makeFixtureDir(t, prefix) {
	const fixtureDir = fs.mkdtempSync(path.join(os.tmpdir(), prefix));
	t.after(() => fs.rmSync(fixtureDir, { recursive: true, force: true }));
	return fixtureDir;
}

function runNodeScript(scriptPath, args, env) {
	return spawnSync(process.execPath, [scriptPath, ...args], {
		cwd: rootDir,
		env: {
			...process.env,
			...env,
		},
		encoding: 'utf8',
	});
}

function createDropdownEvent(overrides = {}) {
	let isDefaultPrevented = false;

	return {
		...overrides,
		get defaultPrevented() {
			return isDefaultPrevented;
		},
		preventDefault() {
			isDefaultPrevented = true;
		},
	};
}

function createDropdownFixture() {
	const parentLink = new FakeElement({ tagName: 'a' });
	const firstChildLink = new FakeElement({ tagName: 'a' });
	const secondChildLink = new FakeElement({ tagName: 'a' });
	const submenu = new FakeElement({
		tagName: 'ul',
		classes: ['sub-menu'],
		children: [firstChildLink, secondChildLink],
	});
	const menuItem = new FakeElement({
		tagName: 'li',
		classes: ['menu-item-has-children'],
		children: [parentLink, submenu],
	});
	const document = {
		querySelectorAll(selector) {
			if (selector === '.main-nav__list .menu-item-has-children') {
				return [menuItem];
			}

			return [];
		},
	};

	return {
		document,
		firstChildLink,
		menuItem,
		parentLink,
		secondChildLink,
		submenu,
	};
}

function loadDropdown(document) {
	const source = fs
		.readFileSync(dropdownJs, 'utf8')
		.replace('export default class Dropdown', 'module.exports = class Dropdown');
	const sandbox = {
		document,
		module: { exports: {} },
		window: { innerWidth: 1280 },
	};

	vm.runInNewContext(source, sandbox, { filename: dropdownJs });

	return sandbox.module.exports;
}

function loadMobileMenu(document) {
	const source = fs
		.readFileSync(mobileMenuJs, 'utf8')
		.replace('export default class MobileMenu', 'module.exports = class MobileMenu');
	const sandbox = {
		document,
		module: { exports: {} },
	};

	vm.runInNewContext(source, sandbox, { filename: mobileMenuJs });

	return sandbox.module.exports;
}

function loadCarousel(document, { isReducedMotion = true } = {}) {
	const source = fs
		.readFileSync(carouselJs, 'utf8')
		.replace('export class KiyoseCarousel', 'module.exports = class KiyoseCarousel');
	const timers = {
		cleared: [],
		created: [],
	};
	const sandbox = {
		clearInterval(intervalId) {
			timers.cleared.push(intervalId);
		},
		document,
		module: { exports: {} },
		setInterval(handler, delay) {
			const intervalId = timers.created.length + 1;
			timers.created.push({ delay, handler, intervalId });
			return intervalId;
		},
		window: {
			matchMedia() {
				return { matches: isReducedMotion };
			},
		},
	};

	vm.runInNewContext(source, sandbox, { filename: carouselJs });

	return { Carousel: sandbox.module.exports, timers };
}

function loadShareLinkCopier(document, navigator, timerCallbacks = []) {
	const source = fs
		.readFileSync(shareLinkCopierJs, 'utf8')
		.replace('export default ShareLinkCopier;', 'module.exports = ShareLinkCopier;');
	const sandbox = {
		console,
		document,
		module: { exports: {} },
		navigator,
		setTimeout(callback, delay) {
			timerCallbacks.push({ callback, delay });
			return timerCallbacks.length;
		},
	};

	vm.runInNewContext(source, sandbox, { filename: shareLinkCopierJs });

	return sandbox.module.exports;
}

function createFakeWindow({ innerWidth = 1280, scrollY = 0, isReducedMotion = false } = {}) {
	return {
		eventListeners: new Map(),
		innerWidth,
		scrollY,
		addEventListener(eventName, handler) {
			const handlers = this.eventListeners.get(eventName) || [];
			handlers.push(handler);
			this.eventListeners.set(eventName, handlers);
		},
		dispatch(eventName, event) {
			const handlers = this.eventListeners.get(eventName) || [];
			handlers.forEach((handler) => handler(event || {}));
		},
		matchMedia() {
			return { matches: isReducedMotion };
		},
	};
}

function loadOverlayScript(scriptPath, document, window) {
	class FakeCustomEvent {
		constructor(type, options = {}) {
			this.detail = options.detail;
			this.type = type;
		}
	}

	vm.runInNewContext(fs.readFileSync(scriptPath, 'utf8'), {
		CustomEvent: FakeCustomEvent,
		document,
		window,
	}, { filename: scriptPath });
}

function createMobileMenuFixture() {
	const hamburgerButton = new FakeElement({
		tagName: 'button',
		classes: ['hamburger-button'],
	});
	const closeButton = new FakeElement({
		tagName: 'button',
		classes: ['mobile-menu__close'],
	});
	const firstLink = new FakeElement({
		tagName: 'a',
		attributes: { href: '/a-propos/' },
	});
	const lastLink = new FakeElement({
		tagName: 'a',
		attributes: { href: '/contact/' },
	});
	const mobileMenu = new FakeElement({
		id: 'mobile-menu',
		classes: ['mobile-menu'],
		children: [closeButton, firstLink, lastLink],
	});
	const overlay = new FakeElement({
		classes: ['mobile-menu-overlay'],
	});
	const root = new FakeElement({
		children: [hamburgerButton, overlay, mobileMenu],
	});
	const document = createFakeDocument(root);

	return {
		closeButton,
		document,
		firstLink,
		hamburgerButton,
		lastLink,
		mobileMenu,
		overlay,
	};
}

function createAboutOverlayFixture() {
	const closeButton = new FakeElement({
		id: 'about-overlay-close',
		tagName: 'button',
	});
	const overlay = new FakeElement({
		id: 'about-overlay',
		children: [closeButton],
	});
	overlay.setAttribute('hidden', '');
	const announcement = new FakeElement({ id: 'overlay-announcement' });
	const eventsSection = new FakeElement({ classes: ['home-events'] });
	eventsSection.getBoundingClientRect = () => ({ top: 300 });
	const focusBeforeOverlay = new FakeElement({ tagName: 'button' });
	const root = new FakeElement({
		children: [focusBeforeOverlay, overlay, announcement, eventsSection],
	});
	const document = createFakeDocument(root);
	focusBeforeOverlay.focus();

	return {
		announcement,
		closeButton,
		document,
		focusBeforeOverlay,
		overlay,
	};
}

function createNewsletterOverlayFixture() {
	const closeButton = new FakeElement({
		id: 'newsletter-overlay-close',
		tagName: 'button',
	});
	const overlay = new FakeElement({
		id: 'newsletter-overlay',
		children: [closeButton],
	});
	overlay.setAttribute('hidden', '');
	const announcement = new FakeElement({ id: 'overlay-announcement' });
	const root = new FakeElement({
		children: [overlay, announcement],
	});
	const document = createFakeDocument(root);

	return {
		announcement,
		closeButton,
		document,
		overlay,
	};
}

function createCarouselFixture() {
	const pauseIcon = new FakeElement({ classes: ['carousel__icon--pause'] });
	const playIcon = new FakeElement({ classes: ['carousel__icon--play'] });
	const pauseButton = new FakeElement({
		tagName: 'button',
		classes: ['carousel__pause'],
		children: [pauseIcon, playIcon],
	});
	const prevButton = new FakeElement({
		tagName: 'button',
		classes: ['carousel__prev'],
	});
	const nextButton = new FakeElement({
		tagName: 'button',
		classes: ['carousel__next'],
	});
	const firstLink = new FakeElement({
		tagName: 'a',
		attributes: { href: '/temoignage-1/' },
	});
	const secondLink = new FakeElement({
		tagName: 'a',
		attributes: { href: '/temoignage-2/' },
	});
	const firstSlide = new FakeElement({
		classes: ['carousel__slide'],
		children: [firstLink],
	});
	const secondSlide = new FakeElement({
		classes: ['carousel__slide'],
		children: [secondLink],
	});
	const track = new FakeElement({
		classes: ['carousel__track'],
		children: [firstSlide, secondSlide],
	});
	const carousel = new FakeElement({
		classes: ['carousel'],
		children: [prevButton, pauseButton, nextButton, track],
	});
	const document = createFakeDocument(carousel);

	return {
		carousel,
		document,
		firstLink,
		firstSlide,
		nextButton,
		pauseButton,
		pauseIcon,
		playIcon,
		prevButton,
		secondLink,
		secondSlide,
		track,
	};
}

function createShareLinkFixture({ url = 'https://example.com/article/' } = {}) {
	const button = new FakeElement({
		tagName: 'button',
		classes: ['js-copy-link'],
		dataset: url === null ? {} : { url },
	});
	button.setAttribute('aria-label', 'Copier le lien');
	const parent = new FakeElement({ children: [button] });
	const document = createFakeDocument(parent);

	return {
		button,
		document,
		parent,
	};
}

function extractBreakpoint(css, selector, direction) {
	const pattern = new RegExp(
		`@media \\(width ${direction} (\\d+)px\\) \\{[\\s\\S]*?${selector.replace('.', '\\.')}`,
		'u'
	);
	const match = css.match(pattern);

	assert.notEqual(match, null, `${selector} has no ${direction} responsive rule`);

	return Number(match[1]);
}

test('cssCheck_whenMinifiedFileIsMissing_failsWithoutWritingAssets', (t) => {
	// Given
	const fixtureDir = makeFixtureDir(t, 'kiyose-css-check-');
	const sourcePath = path.join(fixtureDir, 'sample.css');
	const minifiedPath = path.join(fixtureDir, 'sample.min.css');
	fs.writeFileSync(sourcePath, '.sample {\n\tcolor: #5d0505;\n}\n');

	// When
	const result = runNodeScript(cssScript, ['--check'], {
		KIYOSE_CSS_DIR: fixtureDir,
	});

	// Then
	assert.equal(result.status, 1);
	assert.match(result.stderr, /sample\.min\.css/);
	assert.equal(fs.existsSync(minifiedPath), false);
	assert.equal(fs.existsSync(`${minifiedPath}.map`), false);
});

test('cssCheck_whenMinifiedFileDiffers_fails', (t) => {
	// Given
	const fixtureDir = makeFixtureDir(t, 'kiyose-css-stale-');
	const sourcePath = path.join(fixtureDir, 'sample.css');
	const minifiedPath = path.join(fixtureDir, 'sample.min.css');
	const mapPath = `${minifiedPath}.map`;
	fs.writeFileSync(sourcePath, '.sample {\n\tcolor: #5d0505;\n}\n');
	fs.writeFileSync(minifiedPath, '.sample{color:#000000}');
	fs.writeFileSync(mapPath, '{}');

	// When
	const result = runNodeScript(cssScript, ['--check'], {
		KIYOSE_CSS_DIR: fixtureDir,
	});

	// Then
	assert.equal(result.status, 1);
	assert.match(result.stderr, /sample\.min\.css/);
	assert.match(result.stderr, /sample\.min\.css\.map/);
});

test('jsCheck_whenMinifiedFileIsMissing_failsWithoutWritingAssets', (t) => {
	// Given
	const fixtureDir = makeFixtureDir(t, 'kiyose-js-check-');
	const sourcePath = path.join(fixtureDir, 'sample.js');
	const minifiedPath = path.join(fixtureDir, 'sample.min.js');
	fs.writeFileSync(
		sourcePath,
		"function greet(name) {\n\treturn `Bonjour ${name}`;\n}\nwindow.greet = greet;\n"
	);

	// When
	const result = runNodeScript(jsScript, ['--check'], {
		KIYOSE_JS_DIR: fixtureDir,
	});

	// Then
	assert.equal(result.status, 1);
	assert.match(result.stderr, /sample\.min\.js/);
	assert.equal(fs.existsSync(minifiedPath), false);
	assert.equal(fs.existsSync(`${minifiedPath}.map`), false);
});

test('jsCheck_whenMinifiedFileDiffers_fails', (t) => {
	// Given
	const fixtureDir = makeFixtureDir(t, 'kiyose-js-stale-');
	const sourcePath = path.join(fixtureDir, 'sample.js');
	const minifiedPath = path.join(fixtureDir, 'sample.min.js');
	const mapPath = `${minifiedPath}.map`;
	fs.writeFileSync(
		sourcePath,
		"function greet(name) {\n\treturn `Bonjour ${name}`;\n}\nwindow.greet = greet;\n"
	);
	fs.writeFileSync(minifiedPath, 'window.greet=function(){return "stale"};');
	fs.writeFileSync(mapPath, '{}');

	// When
	const result = runNodeScript(jsScript, ['--check'], {
		KIYOSE_JS_DIR: fixtureDir,
	});

	// Then
	assert.equal(result.status, 1);
	assert.match(result.stderr, /sample\.min\.js/);
	assert.match(result.stderr, /sample\.min\.js\.map/);
});

test('cssCheck_whenGeneratedAssetIsCurrent_passes', (t) => {
	// Given
	const fixtureDir = makeFixtureDir(t, 'kiyose-css-current-');
	fs.writeFileSync(
		path.join(fixtureDir, 'sample.css'),
		'.sample {\n\tcolor: #5d0505;\n\tmargin: 0;\n}\n'
	);
	const env = { KIYOSE_CSS_DIR: fixtureDir };

	// When
	const buildResult = runNodeScript(cssScript, [], env);
	const checkResult = runNodeScript(cssScript, ['--check'], env);

	// Then
	assert.equal(buildResult.status, 0, buildResult.stderr);
	assert.equal(checkResult.status, 0, checkResult.stderr);
});

test('jsCheck_whenGeneratedAssetIsCurrent_passes', (t) => {
	// Given
	const fixtureDir = makeFixtureDir(t, 'kiyose-js-current-');
	fs.writeFileSync(
		path.join(fixtureDir, 'sample.js'),
		"function greet(name) {\n\treturn `Bonjour ${name}`;\n}\nwindow.greet = greet;\n"
	);
	const env = { KIYOSE_JS_DIR: fixtureDir };

	// When
	const buildResult = runNodeScript(jsScript, [], env);
	const checkResult = runNodeScript(jsScript, ['--check'], env);

	// Then
	assert.equal(buildResult.status, 0, buildResult.stderr);
	assert.equal(checkResult.status, 0, checkResult.stderr);
});

test('Dropdown_whenParentLinkIsClicked_doesNotPreventNavigation', () => {
	// Given
	const fixture = createDropdownFixture();
	const Dropdown = loadDropdown(fixture.document);
	new Dropdown();
	const clickEvent = createDropdownEvent();

	// When
	fixture.parentLink.dispatch('click', clickEvent);

	// Then
	assert.equal(clickEvent.defaultPrevented, false);
});

test('Dropdown_whenArrowDownIsPressed_opensSubmenuAndFocusesFirstChild', () => {
	// Given
	const fixture = createDropdownFixture();
	const Dropdown = loadDropdown(fixture.document);
	new Dropdown();
	const keydownEvent = createDropdownEvent({ key: 'ArrowDown' });

	// When
	fixture.parentLink.dispatch('keydown', keydownEvent);

	// Then
	assert.equal(keydownEvent.defaultPrevented, true);
	assert.equal(fixture.parentLink.getAttribute('aria-expanded'), 'true');
	assert.equal(fixture.submenu.classList.contains('is-open'), true);
	assert.equal(fixture.firstChildLink.hasFocus, true);
});

test('Dropdown_whenParentIsHovered_opensSubmenu', () => {
	// Given
	const fixture = createDropdownFixture();
	const Dropdown = loadDropdown(fixture.document);
	new Dropdown();

	// When
	fixture.menuItem.dispatch('mouseenter', createDropdownEvent());

	// Then
	assert.equal(fixture.parentLink.getAttribute('aria-expanded'), 'true');
	assert.equal(fixture.submenu.classList.contains('is-open'), true);
});

test('Dropdown_whenFocusEntersParent_opensSubmenu', () => {
	// Given
	const fixture = createDropdownFixture();
	const Dropdown = loadDropdown(fixture.document);
	new Dropdown();

	// When
	fixture.menuItem.dispatch('focusin', createDropdownEvent());

	// Then
	assert.equal(fixture.parentLink.getAttribute('aria-expanded'), 'true');
	assert.equal(fixture.submenu.classList.contains('is-open'), true);
});

test('Dropdown_whenEscapeIsPressed_closesSubmenu', () => {
	// Given
	const fixture = createDropdownFixture();
	const Dropdown = loadDropdown(fixture.document);
	new Dropdown();
	fixture.parentLink.dispatch('keydown', createDropdownEvent({ key: 'ArrowDown' }));
	const escapeEvent = createDropdownEvent({ key: 'Escape' });

	// When
	fixture.submenu.dispatch('keydown', escapeEvent);

	// Then
	assert.equal(fixture.parentLink.getAttribute('aria-expanded'), 'false');
	assert.equal(fixture.submenu.classList.contains('is-open'), false);
	assert.equal(fixture.parentLink.hasFocus, true);
});

test('MobileMenu_whenHamburgerIsClicked_opensDialogAndLocksScroll', () => {
	// Given
	const fixture = createMobileMenuFixture();
	const MobileMenu = loadMobileMenu(fixture.document);
	new MobileMenu();

	// When
	fixture.hamburgerButton.dispatch('click', createDropdownEvent());

	// Then
	assert.equal(fixture.hamburgerButton.getAttribute('aria-expanded'), 'true');
	assert.equal(fixture.mobileMenu.getAttribute('aria-hidden'), 'false');
	assert.equal(fixture.overlay.getAttribute('aria-hidden'), 'false');
	assert.equal(fixture.document.body.style.overflow, 'hidden');
	assert.equal(fixture.closeButton.hasFocus, true);
});

test('MobileMenu_whenEscapeIsPressed_closesDialogAndRestoresFocus', () => {
	// Given
	const fixture = createMobileMenuFixture();
	const MobileMenu = loadMobileMenu(fixture.document);
	new MobileMenu();
	fixture.hamburgerButton.dispatch('click', createDropdownEvent());

	// When
	fixture.document.dispatch('keydown', { key: 'Escape' });

	// Then
	assert.equal(fixture.hamburgerButton.getAttribute('aria-expanded'), 'false');
	assert.equal(fixture.mobileMenu.getAttribute('aria-hidden'), 'true');
	assert.equal(fixture.overlay.getAttribute('aria-hidden'), 'true');
	assert.equal(fixture.document.body.style.overflow, '');
	assert.equal(fixture.hamburgerButton.hasFocus, true);
});

test('MobileMenu_whenTabLeavesLastFocusable_wrapsToFirstFocusable', () => {
	// Given
	const fixture = createMobileMenuFixture();
	const MobileMenu = loadMobileMenu(fixture.document);
	new MobileMenu();
	fixture.hamburgerButton.dispatch('click', createDropdownEvent());
	fixture.lastLink.focus();
	const tabEvent = createDropdownEvent({ key: 'Tab' });

	// When
	fixture.mobileMenu.dispatch('keydown', tabEvent);

	// Then
	assert.equal(tabEvent.defaultPrevented, true);
	assert.equal(fixture.closeButton.hasFocus, true);
});

test('Carousel_whenReducedMotionIsPreferred_initializesWithoutAutoplay', () => {
	// Given
	const fixture = createCarouselFixture();
	const { Carousel, timers } = loadCarousel(fixture.document, { isReducedMotion: true });
	const carousel = new Carousel(fixture.carousel);

	// When
	carousel.init();

	// Then
	assert.equal(fixture.carousel.getAttribute('data-carousel-initialized'), 'true');
	assert.equal(fixture.carousel._kiyoseCarousel, carousel);
	assert.equal(fixture.firstSlide.classList.contains('carousel__slide--active'), true);
	assert.equal(fixture.firstSlide.getAttribute('aria-hidden'), 'false');
	assert.equal(fixture.secondSlide.getAttribute('aria-hidden'), 'true');
	assert.equal(fixture.firstLink.getAttribute('tabindex'), '0');
	assert.equal(fixture.secondLink.getAttribute('tabindex'), '-1');
	assert.equal(fixture.track.style.transform, 'translateX(0%)');
	assert.equal(carousel.isPlaying, false);
	assert.equal(timers.created.length, 0);
});

test('Carousel_goToSlide_whenIndexIsBeforeFirst_wrapsToLastSlide', () => {
	// Given
	const fixture = createCarouselFixture();
	const { Carousel } = loadCarousel(fixture.document, { isReducedMotion: true });
	const carousel = new Carousel(fixture.carousel);
	carousel.init();

	// When
	carousel.goToSlide(-1);

	// Then
	assert.equal(carousel.currentIndex, 1);
	assert.equal(fixture.secondSlide.classList.contains('carousel__slide--active'), true);
	assert.equal(fixture.track.style.transform, 'translateX(-100%)');
});

test('Carousel_whenArrowRightIsPressed_movesToNextSlide', () => {
	// Given
	const fixture = createCarouselFixture();
	const { Carousel } = loadCarousel(fixture.document, { isReducedMotion: true });
	const carousel = new Carousel(fixture.carousel);
	carousel.init();
	fixture.firstLink.focus();
	const keydownEvent = createDropdownEvent({ key: 'ArrowRight' });

	// When
	fixture.carousel.dispatch('keydown', keydownEvent);

	// Then
	assert.equal(keydownEvent.defaultPrevented, true);
	assert.equal(carousel.currentIndex, 1);
	assert.equal(fixture.secondLink.getAttribute('tabindex'), '0');
});

test('Carousel_startAutoplay_setsLiveRegionAndPauseState', () => {
	// Given
	const fixture = createCarouselFixture();
	const { Carousel, timers } = loadCarousel(fixture.document, { isReducedMotion: true });
	const carousel = new Carousel(fixture.carousel);
	carousel.init();

	// When
	carousel.startAutoplay();

	// Then
	assert.equal(carousel.isPlaying, true);
	assert.equal(fixture.track.getAttribute('aria-live'), 'off');
	assert.equal(fixture.pauseButton.getAttribute('aria-label'), 'Mettre en pause le défilement automatique');
	assert.equal(fixture.pauseIcon.hidden, false);
	assert.equal(fixture.playIcon.hidden, true);
	assert.equal(timers.created[0].delay, 5000);
});

test('Carousel_destroy_removesNavigationHandlers', () => {
	// Given
	const fixture = createCarouselFixture();
	const { Carousel } = loadCarousel(fixture.document, { isReducedMotion: true });
	const carousel = new Carousel(fixture.carousel);
	carousel.init();

	// When
	carousel.destroy();
	fixture.nextButton.dispatch('click', createDropdownEvent());

	// Then
	assert.equal(carousel.currentIndex, 0);
});

test('ShareLinkCopier_handleCopy_whenClipboardSucceeds_showsSuccessNotification', async () => {
	// Given
	const fixture = createShareLinkFixture();
	const writes = [];
	const ShareLinkCopier = loadShareLinkCopier(fixture.document, {
		clipboard: {
			writeText: async (url) => {
				writes.push(url);
			},
		},
	});
	const copier = new ShareLinkCopier();

	// When
	await copier.handleCopy({ currentTarget: fixture.button });

	// Then
	assert.deepEqual(writes, ['https://example.com/article/']);
	assert.equal(fixture.button.classList.contains('copied'), true);
	assert.equal(fixture.button.getAttribute('aria-label'), 'Lien copié !');
	assert.equal(fixture.parent.children[1].getAttribute('role'), 'status');
	assert.equal(
		fixture.parent.children[1].textContent,
		'Lien copié ! Vous pouvez maintenant le coller sur Instagram.'
	);
});

test('ShareLinkCopier_handleCopy_whenUrlIsMissing_doesNothing', async () => {
	// Given
	const fixture = createShareLinkFixture({ url: null });
	const writes = [];
	const ShareLinkCopier = loadShareLinkCopier(fixture.document, {
		clipboard: {
			writeText: async (url) => {
				writes.push(url);
			},
		},
	});
	const copier = new ShareLinkCopier();

	// When
	await copier.handleCopy({ currentTarget: fixture.button });

	// Then
	assert.deepEqual(writes, []);
	assert.equal(fixture.parent.children.length, 1);
});

test('ShareLinkCopier_handleCopy_whenClipboardFails_usesFallbackCopy', async () => {
	// Given
	const fixture = createShareLinkFixture();
	let didExecCommand = false;
	fixture.document.execCommand = () => {
		didExecCommand = true;
		return true;
	};
	const ShareLinkCopier = loadShareLinkCopier(fixture.document, {
		clipboard: {
			writeText: async () => {
				throw new Error('denied');
			},
		},
	});
	const copier = new ShareLinkCopier();

	// When
	await copier.handleCopy({ currentTarget: fixture.button });

	// Then
	assert.equal(didExecCommand, true);
	assert.equal(fixture.button.classList.contains('copied'), true);
	assert.equal(fixture.parent.children[1].getAttribute('role'), 'status');
});

test('AboutOverlay_whenPageLoadsAboveEventsAndScrolled_showsOverlay', () => {
	// Given
	const fixture = createAboutOverlayFixture();
	const window = createFakeWindow({ scrollY: 40 });

	// When
	loadOverlayScript(aboutOverlayJs, fixture.document, window);

	// Then
	assert.equal(fixture.overlay.hidden, false);
	assert.equal(
		fixture.announcement.textContent,
		'Le panneau À propos est maintenant visible.'
	);
});

test('AboutOverlay_whenEscapeIsPressed_closesOverlayAndRestoresFocus', () => {
	// Given
	const fixture = createAboutOverlayFixture();
	const window = createFakeWindow({ scrollY: 40 });
	loadOverlayScript(aboutOverlayJs, fixture.document, window);

	// When
	fixture.document.dispatch('keydown', { key: 'Escape' });

	// Then
	assert.equal(fixture.overlay.hidden, true);
	assert.equal(fixture.announcement.textContent, '');
	assert.equal(fixture.focusBeforeOverlay.hasFocus, true);
});

test('NewsletterOverlay_whenZoneChangesBelow_showsOverlay', () => {
	// Given
	const fixture = createNewsletterOverlayFixture();
	const window = createFakeWindow();
	loadOverlayScript(newsletterOverlayJs, fixture.document, window);

	// When
	fixture.document.dispatch('kiyose:overlay-zone', { detail: { zone: 'below' } });

	// Then
	assert.equal(fixture.overlay.hidden, false);
	assert.equal(
		fixture.announcement.textContent,
		'Le panneau Newsletter est maintenant visible.'
	);
});

test('NewsletterOverlay_whenManuallyClosed_staysClosedUntilZoneChangesAgain', () => {
	// Given
	const fixture = createNewsletterOverlayFixture();
	const window = createFakeWindow();
	loadOverlayScript(newsletterOverlayJs, fixture.document, window);
	fixture.document.dispatch('kiyose:overlay-zone', { detail: { zone: 'below' } });
	fixture.closeButton.dispatch('click', createDropdownEvent());

	// When
	fixture.document.dispatch('kiyose:overlay-zone', { detail: { zone: 'below' } });

	// Then
	assert.equal(fixture.overlay.hidden, true);

	// When
	fixture.document.dispatch('kiyose:overlay-zone', { detail: { zone: 'above' } });
	fixture.document.dispatch('kiyose:overlay-zone', { detail: { zone: 'below' } });

	// Then
	assert.equal(fixture.overlay.hidden, false);
});

test('NavigationCss_whenResponsiveBreakpointsChange_keepsHeaderAndNavigationAligned', () => {
	// Given
	const expectedMobileBreakpoint = 1179;
	const expectedDesktopBreakpoint = 1180;
	const headerSource = fs.readFileSync(headerCss, 'utf8');
	const navigationSource = fs.readFileSync(navigationCss, 'utf8');

	// When
	const headerMobileBreakpoint = extractBreakpoint(headerSource, '.hamburger-button', '<=');
	const headerDesktopBreakpoint = extractBreakpoint(headerSource, '.hamburger-button', '>=');
	const navigationMobileBreakpoint = extractBreakpoint(navigationSource, '.main-nav', '<=');
	const navigationDesktopBreakpoint = extractBreakpoint(navigationSource, '.mobile-menu', '>=');

	// Then
	assert.equal(headerMobileBreakpoint, expectedMobileBreakpoint);
	assert.equal(navigationMobileBreakpoint, expectedMobileBreakpoint);
	assert.equal(headerDesktopBreakpoint, expectedDesktopBreakpoint);
	assert.equal(navigationDesktopBreakpoint, expectedDesktopBreakpoint);
});

test('NavigationCss_whenDesktopStartsBeforeLargeDesktop_usesCompactIntermediateLayout', () => {
	// Given
	const headerSource = fs.readFileSync(headerCss, 'utf8');
	const navigationSource = fs.readFileSync(navigationCss, 'utf8');

	// When
	const hasHeaderIntermediateRule = headerSource.includes(
		'@media (width >= 1180px) and (width <= 1439px)'
	);
	const hasCompactLogoWidth = headerSource.includes('width: clamp(80px, 14vw, 190px);');
	const hasCompactNavigationMargin = headerSource.includes(
		'margin-left: calc(clamp(80px, 14vw, 190px) + var(--kiyose-spacing-md));'
	);
	const hasNavigationIntermediateRule = navigationSource.includes(
		'@media (width >= 1180px) and (width <= 1439px)'
	);
	const hasCompactNavigationGap = navigationSource.includes('gap: var(--kiyose-spacing-sm);');
	const hasCompactNavigationFontSize = navigationSource.includes(
		'font-size: var(--kiyose-font-size-sm);'
	);

	// Then
	assert.equal(hasHeaderIntermediateRule, true);
	assert.equal(hasCompactLogoWidth, true);
	assert.equal(hasCompactNavigationMargin, true);
	assert.equal(hasNavigationIntermediateRule, true);
	assert.equal(hasCompactNavigationGap, true);
	assert.equal(hasCompactNavigationFontSize, true);
});

test('NavigationCss_whenDropdownIsOpened_doesNotDelayVisibilityForKeyboardFocus', () => {
	// Given
	const navigationSource = fs.readFileSync(navigationCss, 'utf8');

	// When
	const transitionsVisibility = navigationSource.includes('visibility 0.2s ease');

	// Then
	assert.equal(transitionsVisibility, false);
});

test('packageScripts_includeReadOnlyBuildCheckCommands', () => {
	// Given
	const packageJson = JSON.parse(fs.readFileSync(path.join(rootDir, 'package.json'), 'utf8'));

	// When
	const scripts = packageJson.scripts;

	// Then
	assert.equal(scripts['test:build-check'], 'node --test tests/build-check.test.js');
	assert.equal(scripts['build:css:check'], 'node bin/minify-css.js --check');
	assert.equal(scripts['build:js:check'], 'node bin/minify-js.js --check');
	assert.equal(scripts['build:check'], 'npm run build:css:check && npm run build:js:check');
});

test('brevoOverrideCss_whenPluginOutputsLegacySignupForm_overridesInlineFieldStyles', () => {
	// Given
	const css = fs.readFileSync(brevoOverrideCss, 'utf8');

	// When
	const hasLegacyFormSelector = css.includes('form[id^="sib_signup_form_"] input[type="email"]');
	const hasThemeBorder = css.includes('border: 2px solid var(--kiyose-color-border) !important;');
	const hasThemeRadius = css.includes('border-radius: 6px !important;');

	// Then
	assert.equal(hasLegacyFormSelector, true);
	assert.equal(hasThemeBorder, true);
	assert.equal(hasThemeRadius, true);
});

test('brevoOverrideCss_whenPluginOutputsLegacySignupForm_overridesInlineButtonStyles', () => {
	// Given
	const css = fs.readFileSync(brevoOverrideCss, 'utf8');

	// When
	const hasLegacyButtonSelector = css.includes('form[id^="sib_signup_form_"] .sib-default-btn');
	const hasThemeBackground = css.includes(
		'background-color: var(--kiyose-color-primary) !important;'
	);
	const hasThemeButtonBorder = css.includes(
		'border: 2px solid var(--kiyose-color-primary) !important;'
	);

	// Then
	assert.equal(hasLegacyButtonSelector, true);
	assert.equal(hasThemeBackground, true);
	assert.equal(hasThemeButtonBorder, true);
});

test('brevoOverrideCss_whenLegacySignupFormIsInFooter_usesCompactNewsletterLayout', () => {
	// Given
	const css = fs.readFileSync(brevoOverrideCss, 'utf8');

	// When
	const hasFooterFormSelector = css.includes(
		'.site-footer__newsletter form[id^="sib_signup_form_"]'
	);
	const hasCompactMaxWidth = css.includes('max-width: 30rem;');
	const hasCompactControlSize = css.includes('font-size: var(--kiyose-font-size-sm) !important;');
	const preservesTapTarget = css.includes('min-height: 44px !important;');

	// Then
	assert.equal(hasFooterFormSelector, true);
	assert.equal(hasCompactMaxWidth, true);
	assert.equal(hasCompactControlSize, true);
	assert.equal(preservesTapTarget, true);
});

test('brevoOverrideCss_whenLegacySignupFormIsInNewsletterOverlay_usesCompactNewsletterLayout', () => {
	// Given
	const css = fs.readFileSync(brevoOverrideCss, 'utf8');

	// When
	const hasOverlayFormSelector = css.includes(
		'.newsletter-overlay__form form[id^="sib_signup_form_"]'
	);
	const hasOverlayCompactMaxWidth = css.includes(
		'.newsletter-overlay__form form[id^="sib_signup_form_"] {\n\tmax-width: 18rem;'
	);
	const hasCompactControlSize = css.includes('font-size: var(--kiyose-font-size-sm) !important;');
	const preservesTapTarget = css.includes('min-height: 44px !important;');

	// Then
	assert.equal(hasOverlayFormSelector, true);
	assert.equal(hasOverlayCompactMaxWidth, true);
	assert.equal(hasCompactControlSize, true);
	assert.equal(preservesTapTarget, true);
});
