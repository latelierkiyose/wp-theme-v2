/**
 * Masonry layout for testimonials grid.
 *
 * Distributes testimony cards across columns using a "shortest column first"
 * greedy algorithm, so each card keeps its natural height and columns stay
 * visually balanced regardless of content length or source order.
 *
 * @package Kiyose
 * @since   2.2.0
 */

(function () {
	'use strict';

	const COLUMN_CLASS = 'testimonials-grid__column';
	const MASONRY_CLASS = 'testimonials-grid--masonry';
	const CARD_SELECTOR = '.testimony-card';

	/**
	 * Parse the target column count from the grid's BEM modifier class.
	 *
	 * @param {HTMLElement} grid The .testimonials-grid element.
	 * @return {number} Requested column count (1-4).
	 */
	function getTargetColumns(grid) {
		const match = grid.className.match(/testimonials-grid--columns-(\d)/);
		return match ? parseInt(match[1], 10) : 2;
	}

	/**
	 * Determine the effective column count based on viewport breakpoints.
	 * Mirrors the responsive rules from testimonials-grid.css.
	 *
	 * @param {number} target Requested column count.
	 * @return {number} Effective column count for the current viewport.
	 */
	function effectiveColumns(target) {
		const width = window.innerWidth;

		if (target <= 1 || width < 768) {
			return 1;
		}
		if (target >= 3 && width < 1024) {
			return 2;
		}
		return target;
	}

	/**
	 * Collect all testimony cards from the grid, removing any existing
	 * column wrappers created by a previous layout pass.
	 *
	 * @param {HTMLElement} grid The .testimonials-grid element.
	 * @return {HTMLElement[]} Ordered list of testimony cards.
	 */
	function collectCards(grid) {
		const cards = Array.from(grid.querySelectorAll(CARD_SELECTOR));

		grid.querySelectorAll('.' + COLUMN_CLASS).forEach((col) => {
			col.remove();
		});

		cards.forEach((card) => {
			grid.appendChild(card);
		});

		return cards;
	}

	/**
	 * Run the masonry layout on a single grid element.
	 *
	 * @param {HTMLElement} grid The .testimonials-grid element.
	 */
	function layout(grid) {
		const cards = collectCards(grid);

		if (cards.length === 0) {
			return;
		}

		const colCount = effectiveColumns(getTargetColumns(grid));

		if (colCount <= 1) {
			grid.classList.remove(MASONRY_CLASS);
			return;
		}

		// Build column containers.
		const columns = [];
		const heights = [];

		for (let i = 0; i < colCount; i++) {
			const col = document.createElement('div');
			col.className = COLUMN_CLASS;
			columns.push(col);
			heights.push(0);
		}

		// Measure heights while cards are still in the DOM.
		const cardHeights = cards.map((card) => {
			return card.offsetHeight;
		});

		// Greedy distribution: each card goes into the shortest column.
		cards.forEach((card, i) => {
			const shortest = heights.indexOf(Math.min.apply(null, heights));
			columns[shortest].appendChild(card);
			heights[shortest] += cardHeights[i];
		});

		grid.classList.add(MASONRY_CLASS);
		columns.forEach((col) => {
			grid.appendChild(col);
		});
	}

	/**
	 * Initialise masonry on all testimonials grids.
	 */
	function init() {
		const grids = document.querySelectorAll('.testimonials-grid');

		if (grids.length === 0) {
			return;
		}

		grids.forEach(layout);

		let resizeTimer;
		window.addEventListener('resize', () => {
			clearTimeout(resizeTimer);
			resizeTimer = setTimeout(() => {
				grids.forEach(layout);
			}, 200);
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
