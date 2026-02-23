/**
 * Decorative Overlap Resolution
 *
 * Detects and resolves overlaps between decorative elements (scribbles and
 * shapes) on the homepage. Runs before decorative-reveal.js so that elements
 * are nudged into non-overlapping positions before being revealed.
 *
 * Uses the CSS `translate` property (independent from `transform`) so that
 * existing transform-based animations (parallax, float, reveal) are
 * unaffected.
 *
 * @package Kiyose
 * @since   2.0.0
 */

(function () {
	'use strict';

	const PADDING = 24;
	const MAX_NUDGE = 160;
	const MAX_PASSES = 3;
	const RESIZE_DEBOUNCE = 300;
	const MOBILE_BREAKPOINT = 767;

	/**
	 * Determine if the viewport is mobile-sized.
	 *
	 * @return {boolean} True when viewport width is at or below the mobile breakpoint.
	 */
	function isMobile() {
		return window.innerWidth <= MOBILE_BREAKPOINT;
	}

	/**
	 * Parse a percentage string (e.g. "12%") relative to a reference length.
	 *
	 * @param {string} value     CSS percentage string.
	 * @param {number} reference Reference dimension in pixels.
	 * @return {number} Computed pixel value.
	 */
	function parsePercent(value, reference) {
		if (typeof value === 'string' && value.includes('%')) {
			return (parseFloat(value) / 100) * reference;
		}
		return parseFloat(value) || 0;
	}

	/**
	 * Parse a computed CSS length value. getComputedStyle returns pixel values
	 * for resolved lengths, but percentages for unresolved ones.
	 *
	 * @param {string} value     CSS value string (e.g. "150px" or "-3%").
	 * @param {number} reference Reference dimension for percentage resolution.
	 * @return {number} Computed pixel value.
	 */
	function parseCSSLength(value, reference) {
		if (typeof value === 'string' && value.includes('%')) {
			return (parseFloat(value) / 100) * reference;
		}
		return parseFloat(value) || 0;
	}

	/**
	 * Compute the intended rect for a scribble (.deco-element).
	 *
	 * Scribbles are positioned absolutely within their parent section using
	 * inline styles (top/right/bottom/left percentages).
	 *
	 * @param {Element} el        The scribble SVG element.
	 * @param {number}  svgWidth  The SVG width attribute value.
	 * @param {number}  svgHeight The SVG height attribute value.
	 * @param {boolean} mobile    Whether we are on a mobile viewport.
	 * @return {{ x: number, y: number, width: number, height: number }}
	 */
	function getScribbleRect(el, svgWidth, svgHeight, mobile) {
		const scale = mobile ? 0.7 : 1.0;
		const w = svgWidth * scale;
		const h = svgHeight * scale;

		const section = el.closest('section') || el.parentElement;
		const parentRect = section.getBoundingClientRect();
		const scrollY = window.pageYOffset || document.documentElement.scrollTop;
		const parentTop = parentRect.top + scrollY;
		const parentLeft = parentRect.left;

		const { style } = el;
		let x = parentLeft;
		let y = parentTop;

		if (style.left) {
			x = parentLeft + parsePercent(style.left, parentRect.width);
		} else if (style.right) {
			x = parentLeft + parentRect.width - parsePercent(style.right, parentRect.width) - w;
		}

		if (style.top) {
			y = parentTop + parsePercent(style.top, parentRect.height);
		} else if (style.bottom) {
			y = parentTop + parentRect.height - parsePercent(style.bottom, parentRect.height) - h;
		}

		return { x, y, width: w, height: h };
	}

	/**
	 * Compute the intended rect for a shape (.deco-shape).
	 *
	 * Shapes are positioned absolutely within the .deco-page-shapes container
	 * using CSS classes (margin-left/margin-right) and a --kiyose-deco-top
	 * custom property for vertical positioning.
	 *
	 * @param {Element} el        The shape SVG element.
	 * @param {number}  svgWidth  The SVG width attribute value.
	 * @param {number}  svgHeight The SVG height attribute value.
	 * @param {boolean} mobile    Whether we are on a mobile viewport.
	 * @return {{ x: number, y: number, width: number, height: number }}
	 */
	function getShapeRect(el, svgWidth, svgHeight, mobile) {
		const scale = mobile ? 0.5 : 1.0;
		const w = svgWidth * scale;
		const h = svgHeight * scale;

		const container = el.closest('.deco-page-shapes') || el.parentElement;
		const containerRect = container.getBoundingClientRect();
		const scrollY = window.pageYOffset || document.documentElement.scrollTop;
		const containerTop = containerRect.top + scrollY;
		const containerLeft = containerRect.left;

		const computed = getComputedStyle(el);
		let x = containerLeft;
		let y = containerTop;

		const leftVal = computed.left;
		const rightVal = computed.right;
		const topVal = computed.top;

		if (leftVal && leftVal !== 'auto') {
			x = containerLeft + parseCSSLength(leftVal, containerRect.width);
		} else if (rightVal && rightVal !== 'auto') {
			x = containerLeft + containerRect.width - parseCSSLength(rightVal, containerRect.width) - w;
		}

		if (topVal && topVal !== 'auto') {
			y = containerTop + parseCSSLength(topVal, containerRect.height);
		}

		return { x, y, width: w, height: h };
	}

	/**
	 * Compute the intended bounding rect of a decorative element in its
	 * fully revealed state, without modifying the DOM.
	 *
	 * @param {Element} el A .deco-element (scribble) or .deco-shape (shape).
	 * @return {{ x: number, y: number, width: number, height: number }}
	 */
	function getIntendedRect(el) {
		const svgWidth = parseFloat(el.getAttribute('width')) || 0;
		const svgHeight = parseFloat(el.getAttribute('height')) || 0;
		const mobile = isMobile();

		if (el.classList.contains('deco-element')) {
			return getScribbleRect(el, svgWidth, svgHeight, mobile);
		}

		return getShapeRect(el, svgWidth, svgHeight, mobile);
	}

	/**
	 * Test whether two rectangles overlap, including a padding margin.
	 *
	 * @param {{ x: number, y: number, width: number, height: number }} a First rect.
	 * @param {{ x: number, y: number, width: number, height: number }} b Second rect.
	 * @return {boolean} True if the padded rects overlap.
	 */
	function rectsOverlap(a, b) {
		return !(
			a.x + a.width + PADDING <= b.x ||
			b.x + b.width + PADDING <= a.x ||
			a.y + a.height + PADDING <= b.y ||
			b.y + b.height + PADDING <= a.y
		);
	}

	/**
	 * Compute the minimum translation vector (MTV) to separate the movable
	 * rect from the fixed rect. Computes the exact displacement needed so
	 * that rectsOverlap() returns false after applying the nudge.
	 *
	 * @param {{ x: number, y: number, width: number, height: number }} movable Rect to move.
	 * @param {{ x: number, y: number, width: number, height: number }} fixed   Rect that stays.
	 * @return {{ dx: number, dy: number }} Displacement vector.
	 */
	function computeNudge(movable, fixed) {
		const movableCenterX = movable.x + movable.width / 2;
		const fixedCenterX = fixed.x + fixed.width / 2;
		const movableCenterY = movable.y + movable.height / 2;
		const fixedCenterY = fixed.y + fixed.height / 2;

		let dxOption;
		let dyOption;

		if (movableCenterX >= fixedCenterX) {
			// Push right: movable.x must be >= fixed right edge + padding.
			dxOption = fixed.x + fixed.width + PADDING - movable.x;
		} else {
			// Push left: movable right edge must be <= fixed.x - padding.
			dxOption = fixed.x - PADDING - (movable.x + movable.width);
		}

		if (movableCenterY >= fixedCenterY) {
			// Push down: movable.y must be >= fixed bottom edge + padding.
			dyOption = fixed.y + fixed.height + PADDING - movable.y;
		} else {
			// Push up: movable bottom edge must be <= fixed.y - padding.
			dyOption = fixed.y - PADDING - (movable.y + movable.height);
		}

		if (Math.abs(dxOption) <= Math.abs(dyOption)) {
			return { dx: dxOption, dy: 0 };
		}
		return { dx: 0, dy: dyOption };
	}

	/**
	 * Main overlap resolution algorithm.
	 *
	 * Shapes have higher visual priority (anchored background layer) and are
	 * only moved to resolve shape-vs-shape overlaps. Scribbles are moved to
	 * resolve both scribble-vs-scribble and scribble-vs-shape overlaps.
	 *
	 * Priority order: shapes first (sorted by area desc), then scribbles
	 * (sorted by area desc). Each element is only checked against elements
	 * with higher priority (lower index).
	 */
	function resolveAll() {
		const shapes = Array.from(document.querySelectorAll('.deco-shape'));
		const scribbles = Array.from(document.querySelectorAll('.deco-element'));
		const all = shapes.concat(scribbles);

		if (all.length < 2) {
			return;
		}

		// Reset previous nudges.
		all.forEach((el) => {
			el.style.translate = '';
			el.classList.remove('deco-overlap-hidden');
			el._rect = getIntendedRect(el);
			el._nudgeX = 0;
			el._nudgeY = 0;
			el._isShape = el.classList.contains('deco-shape');
		});

		// Sort: shapes first (immovable against scribbles), then scribbles.
		// Within each category, sort by area descending.
		all.sort((a, b) => {
			if (a._isShape !== b._isShape) {
				return a._isShape ? -1 : 1;
			}
			return b._rect.width * b._rect.height - a._rect.width * a._rect.height;
		});

		// Iterative resolution.
		for (let pass = 0; pass < MAX_PASSES; pass++) {
			let overlapsFound = false;

			for (let i = 1; i < all.length; i++) {
				for (let j = 0; j < i; j++) {
					// Skip shape-vs-shape (shapes don't move against each other
					// since they are manually placed with known positions).
					if (all[i]._isShape && all[j]._isShape) {
						continue;
					}

					if (rectsOverlap(all[i]._rect, all[j]._rect)) {
						const nudge = computeNudge(all[i]._rect, all[j]._rect);
						all[i]._nudgeX += nudge.dx;
						all[i]._nudgeY += nudge.dy;
						all[i]._rect.x += nudge.dx;
						all[i]._rect.y += nudge.dy;
						overlapsFound = true;
					}
				}
			}

			if (!overlapsFound) {
				break;
			}
		}

		// Apply results (only to scribbles — shapes stay put).
		all.forEach((el) => {
			if (!el._isShape) {
				if (Math.abs(el._nudgeX) > MAX_NUDGE || Math.abs(el._nudgeY) > MAX_NUDGE) {
					el.classList.add('deco-overlap-hidden');
				} else if (el._nudgeX !== 0 || el._nudgeY !== 0) {
					el.style.translate = el._nudgeX + 'px ' + el._nudgeY + 'px';
				}
			}

			// Clean up temporary properties.
			delete el._rect;
			delete el._nudgeX;
			delete el._nudgeY;
			delete el._isShape;
		});
	}

	// Run on load.
	resolveAll();

	// Re-run on resize (debounced).
	let resizeTimer;
	window.addEventListener('resize', () => {
		clearTimeout(resizeTimer);
		resizeTimer = setTimeout(resolveAll, RESIZE_DEBOUNCE);
	});
})();
