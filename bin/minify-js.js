#!/usr/bin/env node

/**
 * Minify JavaScript files using Terser.
 * 
 * This script finds all .js files in latelierkiyose/assets/js/ and generates
 * .min.js versions in the same directory.
 * 
 * Usage: node bin/minify-js.js
 */

const fs = require('fs');
const path = require('path');
const { minify } = require('terser');

const JS_DIR = path.join(__dirname, '../latelierkiyose/assets/js');

/**
 * Recursively find all JS files in a directory.
 * 
 * @param {string} dir - Directory to search
 * @param {string[]} fileList - Accumulator for found files
 * @returns {string[]} Array of JS file paths
 */
function findJsFiles(dir, fileList = []) {
	const files = fs.readdirSync(dir);

	files.forEach((file) => {
		const filePath = path.join(dir, file);
		const stat = fs.statSync(filePath);

		if (stat.isDirectory()) {
			findJsFiles(filePath, fileList);
		} else if (file.endsWith('.js') && !file.endsWith('.min.js')) {
			fileList.push(filePath);
		}
	});

	return fileList;
}

/**
 * Minify a single JavaScript file.
 * 
 * @param {string} inputPath - Path to source JS file
 * @returns {Promise<void>}
 */
async function minifyJs(inputPath) {
	const outputPath = inputPath.replace(/\.js$/, '.min.js');
	const code = fs.readFileSync(inputPath, 'utf8');

	try {
		const result = await minify(code, {
			compress: {
				drop_console: false, // Keep console.log for debugging
				drop_debugger: true,
				passes: 2,
			},
			mangle: true,
			format: {
				comments: false, // Remove all comments
			},
		});

		if (result.code) {
			fs.writeFileSync(outputPath, result.code);

			const inputSize = (fs.statSync(inputPath).size / 1024).toFixed(2);
			const outputSize = (fs.statSync(outputPath).size / 1024).toFixed(2);
			const savings = (((inputSize - outputSize) / inputSize) * 100).toFixed(1);

			console.log(`✓ ${path.relative(process.cwd(), inputPath)} → ${path.relative(process.cwd(), outputPath)}`);
			console.log(`  ${inputSize} KB → ${outputSize} KB (${savings}% reduction)`);
		} else {
			throw new Error('Minification produced no output');
		}
	} catch (error) {
		console.error(`✗ Error minifying ${inputPath}:`, error.message);
		process.exit(1);
	}
}

/**
 * Main execution.
 */
async function main() {
	console.log('⚡ Minifying JavaScript files...\n');

	const jsFiles = findJsFiles(JS_DIR);

	if (jsFiles.length === 0) {
		console.log('No JavaScript files found.');
		return;
	}

	for (const file of jsFiles) {
		await minifyJs(file);
	}

	console.log(`\n✅ Minified ${jsFiles.length} JavaScript file(s).`);
}

main();
