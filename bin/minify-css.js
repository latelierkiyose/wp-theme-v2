#!/usr/bin/env node

/**
 * Minify CSS files using cssnano.
 * 
 * This script finds all .css files in latelierkiyose/assets/css/ and generates
 * .min.css versions in the same directory.
 * 
 * Usage: node bin/minify-css.js
 */

const fs = require('fs');
const path = require('path');
const postcss = require('postcss');
const cssnano = require('cssnano');

const CSS_DIR = path.join(__dirname, '../latelierkiyose/assets/css');

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
 * Minify a single CSS file.
 * 
 * @param {string} inputPath - Path to source CSS file
 * @returns {Promise<void>}
 */
async function minifyCss(inputPath) {
	const outputPath = inputPath.replace(/\.css$/, '.min.css');
	const css = fs.readFileSync(inputPath, 'utf8');

	try {
		const result = await postcss([cssnano({ preset: 'default' })]).process(
			css,
			{
				from: inputPath,
				to: outputPath,
			}
		);

		fs.writeFileSync(outputPath, result.css);
		
		const inputSize = (fs.statSync(inputPath).size / 1024).toFixed(2);
		const outputSize = (fs.statSync(outputPath).size / 1024).toFixed(2);
		const savings = (((inputSize - outputSize) / inputSize) * 100).toFixed(1);

		console.log(`✓ ${path.relative(process.cwd(), inputPath)} → ${path.relative(process.cwd(), outputPath)}`);
		console.log(`  ${inputSize} KB → ${outputSize} KB (${savings}% reduction)`);
	} catch (error) {
		console.error(`✗ Error minifying ${inputPath}:`, error.message);
		process.exit(1);
	}
}

/**
 * Main execution.
 */
async function main() {
	console.log('🎨 Minifying CSS files...\n');

	const cssFiles = findCssFiles(CSS_DIR);

	if (cssFiles.length === 0) {
		console.log('No CSS files found.');
		return;
	}

	for (const file of cssFiles) {
		await minifyCss(file);
	}

	console.log(`\n✅ Minified ${cssFiles.length} CSS file(s).`);
}

main();
