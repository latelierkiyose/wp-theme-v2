const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const test = require('node:test');
const vm = require('node:vm');

const rootDir = path.resolve(__dirname, '..');
const decorativeRevealJs = path.join(
	rootDir,
	'latelierkiyose/assets/js/modules/decorative-reveal.js'
);
const kintsugiCss = path.join(rootDir, 'latelierkiyose/assets/css/components/kintsugi.css');

class FakeClassList {
	constructor(classes = []) {
		this.classes = new Set(classes);
	}

	add(...classNames) {
		classNames.forEach((className) => this.classes.add(className));
	}

	contains(className) {
		return this.classes.has(className);
	}
}

class FakeStyle {
	constructor() {
		this.properties = new Map();
		this.transform = '';
	}

	removeProperty(name) {
		this.properties.delete(name);
	}

	setProperty(name, value) {
		this.properties.set(name, value);
	}

	getPropertyValue(name) {
		return this.properties.get(name) || '';
	}
}

class FakeElement {
	constructor({ classes = [], rect = { height: 100, top: 100 } } = {}) {
		this.classList = new FakeClassList(classes);
		this.rect = rect;
		this.style = new FakeStyle();
	}

	getBoundingClientRect() {
		return this.rect;
	}
}

function createFakeDocument(shape) {
	return {
		querySelectorAll(selector) {
			if (selector === '.deco-shape') {
				return [shape];
			}

			return [];
		},
	};
}

function createFakeWindow() {
	return {
		eventListeners: new Map(),
		innerHeight: 600,
		addEventListener(eventName, handler) {
			const handlers = this.eventListeners.get(eventName) || [];
			handlers.push(handler);
			this.eventListeners.set(eventName, handlers);
		},
		dispatch(eventName) {
			const handlers = this.eventListeners.get(eventName) || [];
			handlers.forEach((handler) => handler());
		},
		matchMedia() {
			return { matches: false };
		},
		requestAnimationFrame(callback) {
			callback();
		},
	};
}

function loadDecorativeReveal({ document, intersectionObservers, window }) {
	class FakeIntersectionObserver {
		constructor(callback) {
			this.callback = callback;
			this.observed = [];
			intersectionObservers.push(this);
		}

		observe(element) {
			this.observed.push(element);
		}
	}

	vm.runInNewContext(
		fs.readFileSync(decorativeRevealJs, 'utf8'),
		{
			IntersectionObserver: FakeIntersectionObserver,
			document,
			window,
		},
		{ filename: decorativeRevealJs }
	);
}

test('decorativeReveal_whenShapeParallaxUpdates_preservesCssTransformScale', () => {
	// Given
	const shape = new FakeElement({
		classes: ['deco-shape'],
		rect: { height: 100, top: 100 },
	});
	const document = createFakeDocument(shape);
	const intersectionObservers = [];
	const window = createFakeWindow();

	loadDecorativeReveal({ document, intersectionObservers, window });
	const parallaxObserver = intersectionObservers.find((observer) => observer.observed.includes(shape));

	// When
	parallaxObserver.callback([{ isIntersecting: true, target: shape }]);
	window.dispatch('scroll');

	// Then
	assert.equal(shape.style.transform, '');
	assert.match(shape.style.getPropertyValue('--kiyose-deco-parallax-y'), /^-\d+(\.\d+)?px$/u);
});

test('kintsugiCss_whenShapeParallaxIsApplied_preservesMobileScale', () => {
	// Given
	const css = fs.readFileSync(kintsugiCss, 'utf8');

	// When
	const mobileRule = css.match(/@media \(width <= 767px\) \{[\s\S]*?\.deco-shape \{([\s\S]*?)\n\t\}/u);

	// Then
	assert.match(css, /transform: translateY\(var\(--kiyose-deco-parallax-y, 0\)\);/u);
	assert.notEqual(mobileRule, null);
	assert.match(
		mobileRule[1],
		/transform: translateY\(var\(--kiyose-deco-parallax-y, 0\)\) scale\(0\.5\);/u
	);
});
