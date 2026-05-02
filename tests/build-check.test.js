const assert = require('node:assert/strict');
const { spawnSync } = require('node:child_process');
const fs = require('node:fs');
const os = require('node:os');
const path = require('node:path');
const test = require('node:test');
const vm = require('node:vm');

const rootDir = path.resolve(__dirname, '..');
const cssScript = path.join(rootDir, 'bin/minify-css.js');
const jsScript = path.join(rootDir, 'bin/minify-js.js');
const brevoOverrideCss = path.join(
	rootDir,
	'latelierkiyose/assets/css/components/brevo-override.css'
);
const dropdownJs = path.join(rootDir, 'latelierkiyose/assets/js/modules/dropdown.js');
const headerCss = path.join(rootDir, 'latelierkiyose/assets/css/components/header.css');
const navigationCss = path.join(rootDir, 'latelierkiyose/assets/css/components/navigation.css');

class FakeClassList {
	constructor(classes = []) {
		this.classes = new Set(classes);
	}

	add(className) {
		this.classes.add(className);
	}

	remove(className) {
		this.classes.delete(className);
	}

	contains(className) {
		return this.classes.has(className);
	}
}

class FakeElement {
	constructor({ tagName = 'div', classes = [], children = [] } = {}) {
		this.attributes = new Map();
		this.children = [];
		this.classList = new FakeClassList(classes);
		this.eventListeners = new Map();
		this.hasFocus = false;
		this.parentElement = null;
		this.tagName = tagName.toLowerCase();

		children.forEach((child) => this.appendChild(child));
	}

	appendChild(child) {
		child.parentElement = this;
		this.children.push(child);
	}

	addEventListener(eventName, handler) {
		const handlers = this.eventListeners.get(eventName) || [];
		handlers.push(handler);
		this.eventListeners.set(eventName, handlers);
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
		handlers.forEach((handler) => handler(event));
	}

	focus() {
		this.hasFocus = true;

		let currentElement = this;
		while (currentElement) {
			currentElement.dispatch('focusin', { target: this });
			currentElement = currentElement.parentElement;
		}
	}

	getAttribute(attributeName) {
		return this.attributes.get(attributeName) || null;
	}

	querySelector(selector) {
		return this.querySelectorAll(selector)[0] || null;
	}

	querySelectorAll(selector) {
		const matches = [];

		this.walk((element) => {
			if (element !== this && element.matches(selector)) {
				matches.push(element);
			}
		});

		return matches;
	}

	matches(selector) {
		if (selector === 'a') {
			return this.tagName === 'a';
		}

		if (selector === '.sub-menu') {
			return this.classList.contains('sub-menu');
		}

		return false;
	}

	setAttribute(attributeName, value) {
		this.attributes.set(attributeName, value);
	}

	walk(callback) {
		callback(this);
		this.children.forEach((child) => child.walk(callback));
	}
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
