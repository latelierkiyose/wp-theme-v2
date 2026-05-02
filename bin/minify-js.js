#!/usr/bin/env node

/**
 * Minify JavaScript files using Terser.
 *
 * This script finds all .js files in latelierkiyose/assets/js/ and generates
 * .min.js versions in the same directory.
 *
 * Usage: node bin/minify-js.js [--check]
 */

const fs = require('fs');
const path = require('path');
const { minify } = require('terser');

const isCheckMode = process.argv.includes('--check');
const JS_DIR = process.env.KIYOSE_JS_DIR
	? path.resolve(process.env.KIYOSE_JS_DIR)
	: path.join(__dirname, '../latelierkiyose/assets/js');

function relativePath(filePath) {
	return path.relative(process.cwd(), filePath);
}

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
 * Build the expected minified JavaScript and source map content for a source file.
 *
 * @param {string} inputPath - Path to source JS file
 * @returns {Promise<{outputPath: string, mapPath: string, code: string, map: string}>}
 */
async function buildJsAsset(inputPath) {
	const outputPath = inputPath.replace(/\.js$/, '.min.js');
	const code = fs.readFileSync(inputPath, 'utf8');

	const outputBasename = path.basename(outputPath);

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
		sourceMap: {
			filename: outputBasename,
			url: outputBasename + '.map',
		},
	});

	if (!result.code) {
		throw new Error('Minification produced no output');
	}

	return {
		outputPath,
		mapPath: outputPath + '.map',
		code: result.code,
		map: result.map,
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
 * Minify or check a single JavaScript file.
 *
 * @param {string} inputPath - Path to source JS file
 * @param {string[]} errors - Accumulator for check errors
 * @returns {Promise<void>}
 */
async function minifyJs(inputPath, errors) {
	const asset = await buildJsAsset(inputPath);

	if (isCheckMode) {
		checkFile(asset.outputPath, asset.code, errors);
		checkFile(asset.mapPath, asset.map, errors);
		return;
	}

	fs.writeFileSync(asset.outputPath, asset.code);
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
	console.log(
		isCheckMode ? '⚡ Checking JavaScript minified files...\n' : '⚡ Minifying JavaScript files...\n'
	);

	const jsFiles = findJsFiles(JS_DIR);

	if (jsFiles.length === 0) {
		console.log('No JavaScript files found.');
		return;
	}

	const errors = [];

	for (const file of jsFiles) {
		try {
			await minifyJs(file, errors);
		} catch (error) {
			console.error(`✗ Error minifying ${file}:`, error.message);
			process.exit(1);
		}
	}

	if (errors.length > 0) {
		console.error('✗ JavaScript minified files are not up to date. Run npm run build:js.');
		errors.forEach((error) => console.error(`  - ${error}`));
		process.exit(1);
	}

	const action = isCheckMode ? 'Checked' : 'Minified';
	console.log(`\n✅ ${action} ${jsFiles.length} JavaScript file(s).`);
}

main();
