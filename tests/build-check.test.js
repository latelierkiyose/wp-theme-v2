const assert = require('node:assert/strict');
const { spawnSync } = require('node:child_process');
const fs = require('node:fs');
const os = require('node:os');
const path = require('node:path');
const test = require('node:test');

const rootDir = path.resolve(__dirname, '..');
const cssScript = path.join(rootDir, 'bin/minify-css.js');
const jsScript = path.join(rootDir, 'bin/minify-js.js');
const brevoOverrideCss = path.join(
	rootDir,
	'latelierkiyose/assets/css/components/brevo-override.css'
);

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
