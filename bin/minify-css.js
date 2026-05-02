#!/usr/bin/env node

/**
 * Minify CSS files using cssnano.
 *
 * This script finds all .css files in latelierkiyose/assets/css/ and generates
 * .min.css versions in the same directory.
 *
 * Usage: node bin/minify-css.js [--check]
 */

const fs = require('fs');
const path = require('path');
const postcss = require('postcss');
const cssnano = require('cssnano');

const isCheckMode = process.argv.includes('--check');
const CSS_DIR = process.env.KIYOSE_CSS_DIR
	? path.resolve(process.env.KIYOSE_CSS_DIR)
	: path.join(__dirname, '../latelierkiyose/assets/css');

function relativePath(filePath) {
	return path.relative(process.cwd(), filePath);
}

/**
 * Recursively find all CSS files in a directory.
 *
 * @param {string} dir - Directory to search
 * @param {string[]} fileList - Accumulator for found files
 * @returns {string[]} Array of CSS file paths
 */
function findCssFiles(dir, fileList = []) {
	const files = fs.readdirSync(dir);

	files.forEach((file) => {
		const filePath = path.join(dir, file);
		const stat = fs.statSync(filePath);

		if (stat.isDirectory()) {
			findCssFiles(filePath, fileList);
		} else if (file.endsWith('.css') && !file.endsWith('.min.css')) {
			fileList.push(filePath);
		}
	});

	return fileList;
}

/**
 * Build the expected minified CSS and source map content for a source file.
 *
 * @param {string} inputPath - Path to source CSS file
 * @returns {Promise<{outputPath: string, mapPath: string, css: string, map: string}>}
 */
async function buildCssAsset(inputPath) {
	const outputPath = inputPath.replace(/\.css$/, '.min.css');
	const css = fs.readFileSync(inputPath, 'utf8');

	const result = await postcss([cssnano({ preset: 'default' })]).process(css, {
		from: inputPath,
		to: outputPath,
		map: { inline: false },
	});

	return {
		outputPath,
		mapPath: outputPath + '.map',
		css: result.css,
		map: result.map.toString(),
	};
}

function checkFile(expectedPath, expectedContent, errors) {
	if (!fs.existsSync(expectedPath)) {
		errors.push(`${relativePath(expectedPath)} is missing.`);
		return;
	}

	const actualContent = fs.readFileSync(expectedPath, 'utf8');

	if (actualContent !== expectedContent) {
		errors.push(`${relativePath(expectedPath)} is out of date.`);
	}
}

/**
 * Minify or check a single CSS file.
 *
 * @param {string} inputPath - Path to source CSS file
 * @param {string[]} errors - Accumulator for check errors
 * @returns {Promise<void>}
 */
async function minifyCss(inputPath, errors) {
	const asset = await buildCssAsset(inputPath);

	if (isCheckMode) {
		checkFile(asset.outputPath, asset.css, errors);
		checkFile(asset.mapPath, asset.map, errors);
		return;
	}

	fs.writeFileSync(asset.outputPath, asset.css);
	fs.writeFileSync(asset.mapPath, asset.map);

	const inputSize = (fs.statSync(inputPath).size / 1024).toFixed(2);
	const outputSize = (fs.statSync(asset.outputPath).size / 1024).toFixed(2);
	const savings = (((inputSize - outputSize) / inputSize) * 100).toFixed(1);

	console.log(`✓ ${relativePath(inputPath)} → ${relativePath(asset.outputPath)}`);
	console.log(`  ${inputSize} KB → ${outputSize} KB (${savings}% reduction)`);
}

/**
 * Main execution.
 */
async function main() {
	console.log(isCheckMode ? '🎨 Checking CSS minified files...\n' : '🎨 Minifying CSS files...\n');

	const cssFiles = findCssFiles(CSS_DIR);

	if (cssFiles.length === 0) {
		console.log('No CSS files found.');
		return;
	}

	const errors = [];

	for (const file of cssFiles) {
		try {
			await minifyCss(file, errors);
		} catch (error) {
			console.error(`✗ Error minifying ${file}:`, error.message);
			process.exit(1);
		}
	}

	if (errors.length > 0) {
		console.error('✗ CSS minified files are not up to date. Run npm run build:css.');
		errors.forEach((error) => console.error(`  - ${error}`));
		process.exit(1);
	}

	const action = isCheckMode ? 'Checked' : 'Minified';
	console.log(`\n✅ ${action} ${cssFiles.length} CSS file(s).`);
}

main();
