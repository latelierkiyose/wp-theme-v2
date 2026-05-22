const assert = require('node:assert/strict');
const { execFileSync, spawnSync } = require('node:child_process');
const fs = require('node:fs');
const os = require('node:os');
const path = require('node:path');
const test = require('node:test');

const rootDir = path.resolve(__dirname, '..');
const releaseScript = require('../bin/release.js');

function createTempDir() {
	return fs.mkdtempSync(path.join(os.tmpdir(), 'kiyose-release-'));
}

function createThemeStyle(dir, version = '1.0.3') {
	const themeDir = path.join(dir, 'latelierkiyose');
	const stylePath = path.join(themeDir, 'style.css');
	fs.mkdirSync(themeDir, { recursive: true });
	fs.writeFileSync(
		stylePath,
		`/*
Theme Name: L'Atelier Kiyose
Version: ${version}
Text Domain: kiyose
*/
`,
		'utf8'
	);

	return stylePath;
}

function createThemeFunctions(dir, version = '1.0.3') {
	const themeDir = path.join(dir, 'latelierkiyose');
	const functionsPath = path.join(themeDir, 'functions.php');
	fs.mkdirSync(themeDir, { recursive: true });
	fs.writeFileSync(
		functionsPath,
		`<?php
// Theme version.
define( 'KIYOSE_VERSION', '${version}' );
`,
		'utf8'
	);

	return functionsPath;
}

function createPackageJson(dir, version = '1.0.3') {
	const packagePath = path.join(dir, 'package.json');
	fs.writeFileSync(
		packagePath,
		`{\n\t"name": "latelierkiyose-wp-theme",\n\t"version": "${version}",\n\t"private": true\n}\n`,
		'utf8'
	);

	return packagePath;
}

function createReleaseFixtures(dir, version = '1.0.3') {
	return {
		stylePath: createThemeStyle(dir, version),
		functionsPath: createThemeFunctions(dir, version),
		packagePath: createPackageJson(dir, version),
	};
}

function initializeGitRepository(dir) {
	execFileSync('git', ['init'], { cwd: dir, stdio: 'ignore' });
	execFileSync('git', ['config', 'user.name', 'Release Test'], { cwd: dir, stdio: 'ignore' });
	execFileSync('git', ['config', 'user.email', 'release-test@example.com'], {
		cwd: dir,
		stdio: 'ignore',
	});
	execFileSync('git', ['add', '.'], { cwd: dir, stdio: 'ignore' });
	execFileSync('git', ['commit', '-m', 'Initial commit'], { cwd: dir, stdio: 'ignore' });
}

test('resolveTargetVersion_ofDefaultBump_returnsNextMinor', () => {
	// Given
	const options = releaseScript.parseArguments([]);

	// When
	const version = releaseScript.resolveTargetVersion('1.0.3', options);

	// Then
	assert.equal(version, '1.1.0');
});

test('resolveTargetVersion_whenPatchBump_returnsNextPatch', () => {
	// Given
	const options = releaseScript.parseArguments(['patch']);

	// When
	const version = releaseScript.resolveTargetVersion('1.0.3', options);

	// Then
	assert.equal(version, '1.0.4');
});

test('writeThemeVersion_whenHeaderExists_updatesVersion', () => {
	// Given
	const dir = createTempDir();
	const stylePath = createThemeStyle(dir, '0.2.3');

	// When
	releaseScript.writeThemeVersion(stylePath, '1.0.3');

	// Then
	assert.match(fs.readFileSync(stylePath, 'utf8'), /^Version: 1\.0\.3$/mu);
});

test('writeFunctionsVersion_whenDefineExists_updatesVersion', () => {
	// Given
	const dir = createTempDir();
	const functionsPath = createThemeFunctions(dir, '0.2.4');

	// When
	releaseScript.writeFunctionsVersion(functionsPath, '1.0.3');

	// Then
	assert.match(
		fs.readFileSync(functionsPath, 'utf8'),
		/define\(\s*'KIYOSE_VERSION'\s*,\s*'1\.0\.3'\s*\);/u
	);
});

test('writeFunctionsVersion_whenDefineMissing_throws', () => {
	// Given
	const dir = createTempDir();
	const themeDir = path.join(dir, 'latelierkiyose');
	fs.mkdirSync(themeDir, { recursive: true });
	const functionsPath = path.join(themeDir, 'functions.php');
	fs.writeFileSync(functionsPath, "<?php\n// no version define here\n", 'utf8');

	// When / Then
	assert.throws(() => releaseScript.writeFunctionsVersion(functionsPath, '1.0.3'), /KIYOSE_VERSION/u);
});

test('writePackageVersion_whenFieldExists_updatesVersion', () => {
	// Given
	const dir = createTempDir();
	const packagePath = createPackageJson(dir, '2.0.0');

	// When
	releaseScript.writePackageVersion(packagePath, '2.1.0');

	// Then
	const updated = JSON.parse(fs.readFileSync(packagePath, 'utf8'));
	assert.equal(updated.version, '2.1.0');
});

test('writePackageVersion_whenFieldExists_preservesTabIndentation', () => {
	// Given
	const dir = createTempDir();
	const packagePath = createPackageJson(dir, '2.0.0');

	// When
	releaseScript.writePackageVersion(packagePath, '2.1.0');

	// Then
	const content = fs.readFileSync(packagePath, 'utf8');
	assert.match(content, /^\{\n\t"name":/u);
	assert.match(content, /^\t"version": "2\.1\.0",$/mu);
});

test('writePackageVersion_whenFieldMissing_throws', () => {
	// Given
	const dir = createTempDir();
	const packagePath = path.join(dir, 'package.json');
	fs.writeFileSync(packagePath, '{\n\t"name": "x"\n}\n', 'utf8');

	// When / Then
	assert.throws(() => releaseScript.writePackageVersion(packagePath, '1.0.3'), /"version"/u);
});

test('prepareRelease_whenNoCommitIsRequestedWithVersionChange_defersTagCreation', () => {
	// Given
	const dir = createTempDir();
	const { stylePath, functionsPath, packagePath } = createReleaseFixtures(dir, '1.0.2');
	initializeGitRepository(dir);

	// When
	const result = releaseScript.prepareRelease({
		argv: ['--version', '1.0.3', '--no-commit'],
		cwd: dir,
		stylePath,
		functionsPath,
		packagePath,
	});

	// Then
	assert.equal(result.targetVersion, '1.0.3');
	assert.equal(result.tagName, 'v1.0.3');
	assert.equal(result.isVersionUpdated, true);
	assert.equal(result.isTagCreated, false);
	assert.equal(result.isTagDeferred, true);
	assert.match(fs.readFileSync(stylePath, 'utf8'), /^Version: 1\.0\.3$/mu);
	assert.match(fs.readFileSync(functionsPath, 'utf8'), /'KIYOSE_VERSION'\s*,\s*'1\.0\.3'/u);
	assert.equal(JSON.parse(fs.readFileSync(packagePath, 'utf8')).version, '1.0.3');
	assert.throws(() => execFileSync('git', ['rev-parse', 'v1.0.3'], { cwd: dir, stdio: 'pipe' }));
});

test('prepareRelease_whenVersionAlreadyCommitted_createsAnnotatedTag', () => {
	// Given
	const dir = createTempDir();
	const { stylePath, functionsPath, packagePath } = createReleaseFixtures(dir, '1.0.3');
	initializeGitRepository(dir);

	// When
	const result = releaseScript.prepareRelease({
		argv: ['--version', '1.0.3'],
		cwd: dir,
		stylePath,
		functionsPath,
		packagePath,
	});

	// Then
	assert.equal(result.isVersionUpdated, false);
	assert.equal(result.isTagCreated, true);
	assert.equal(result.isTagDeferred, false);
	assert.equal(
		execFileSync('git', ['tag', '--list', 'v1.0.3'], { cwd: dir, encoding: 'utf8' }).trim(),
		'v1.0.3'
	);
	assert.equal(
		execFileSync('git', ['cat-file', '-t', 'v1.0.3'], { cwd: dir, encoding: 'utf8' }).trim(),
		'tag'
	);
});

test('prepareRelease_whenCurrentVersionIsUntaggedAndClean_createsCurrentVersionTag', () => {
	// Given
	const dir = createTempDir();
	const { stylePath, functionsPath, packagePath } = createReleaseFixtures(dir, '1.0.3');
	initializeGitRepository(dir);

	// When
	const result = releaseScript.prepareRelease({
		argv: [],
		cwd: dir,
		stylePath,
		functionsPath,
		packagePath,
	});

	// Then
	assert.equal(result.targetVersion, '1.0.3');
	assert.equal(result.isVersionUpdated, false);
	assert.equal(result.isTagCreated, true);
	assert.equal(
		execFileSync('git', ['tag', '--list', 'v1.0.3'], { cwd: dir, encoding: 'utf8' }).trim(),
		'v1.0.3'
	);
});

test('prepareRelease_whenNoCommitIsRequestedAndCurrentVersionIsAlreadyTagged_bumpsMinorAndDefersTagCreation', () => {
	// Given
	const dir = createTempDir();
	const { stylePath, functionsPath, packagePath } = createReleaseFixtures(dir, '1.0.3');
	initializeGitRepository(dir);
	execFileSync('git', ['tag', '-a', 'v1.0.3', '-m', 'Release 1.0.3'], {
		cwd: dir,
		stdio: 'ignore',
	});

	// When
	const result = releaseScript.prepareRelease({
		argv: ['--no-commit'],
		cwd: dir,
		stylePath,
		functionsPath,
		packagePath,
	});

	// Then
	assert.equal(result.targetVersion, '1.1.0');
	assert.equal(result.isVersionUpdated, true);
	assert.equal(result.isTagCreated, false);
	assert.equal(result.isTagDeferred, true);
	assert.match(fs.readFileSync(stylePath, 'utf8'), /^Version: 1\.1\.0$/mu);
	assert.match(fs.readFileSync(functionsPath, 'utf8'), /'KIYOSE_VERSION'\s*,\s*'1\.1\.0'/u);
	assert.equal(JSON.parse(fs.readFileSync(packagePath, 'utf8')).version, '1.1.0');
});

test('prepareRelease_whenCurrentVersionIsAlreadyTagged_bumpsMinorCommitsAndCreatesAnnotatedTag', () => {
	// Given
	const dir = createTempDir();
	const { stylePath, functionsPath, packagePath } = createReleaseFixtures(dir, '1.0.3');
	initializeGitRepository(dir);
	execFileSync('git', ['tag', '-a', 'v1.0.3', '-m', 'Release 1.0.3'], {
		cwd: dir,
		stdio: 'ignore',
	});

	// When
	const result = releaseScript.prepareRelease({
		argv: [],
		cwd: dir,
		stylePath,
		functionsPath,
		packagePath,
	});

	// Then
	assert.equal(result.targetVersion, '1.1.0');
	assert.equal(result.isVersionUpdated, true);
	assert.equal(result.isCommitCreated, true);
	assert.equal(result.isTagCreated, true);
	assert.equal(result.isTagDeferred, false);
	assert.match(fs.readFileSync(stylePath, 'utf8'), /^Version: 1\.1\.0$/mu);
	assert.match(fs.readFileSync(functionsPath, 'utf8'), /'KIYOSE_VERSION'\s*,\s*'1\.1\.0'/u);
	assert.equal(JSON.parse(fs.readFileSync(packagePath, 'utf8')).version, '1.1.0');
	assert.equal(
		execFileSync('git', ['log', '-1', '--pretty=%s'], { cwd: dir, encoding: 'utf8' }).trim(),
		'chore(release): v1.1.0'
	);
	const changedFiles = execFileSync(
		'git',
		['show', '--name-only', '--pretty=format:', 'HEAD'],
		{ cwd: dir, encoding: 'utf8' }
	)
		.split('\n')
		.filter(Boolean)
		.sort();
	assert.deepEqual(changedFiles, ['latelierkiyose/functions.php', 'latelierkiyose/style.css', 'package.json']);
	assert.equal(
		execFileSync('git', ['tag', '--list', 'v1.1.0'], { cwd: dir, encoding: 'utf8' }).trim(),
		'v1.1.0'
	);
	assert.equal(
		execFileSync('git', ['cat-file', '-t', 'v1.1.0'], { cwd: dir, encoding: 'utf8' }).trim(),
		'tag'
	);
});

test('prepareRelease_whenCommitIsRequested_commitsVersionAndCreatesAnnotatedTag', () => {
	// Given
	const dir = createTempDir();
	const { stylePath, functionsPath, packagePath } = createReleaseFixtures(dir, '1.0.3');
	initializeGitRepository(dir);
	execFileSync('git', ['tag', '-a', 'v1.0.3', '-m', 'Release 1.0.3'], {
		cwd: dir,
		stdio: 'ignore',
	});

	// When
	const result = releaseScript.prepareRelease({
		argv: ['patch', '--commit'],
		cwd: dir,
		stylePath,
		functionsPath,
		packagePath,
	});

	// Then
	assert.equal(result.targetVersion, '1.0.4');
	assert.equal(result.isCommitCreated, true);
	assert.equal(result.isTagCreated, true);
	assert.equal(result.isTagDeferred, false);
	assert.equal(
		execFileSync('git', ['log', '-1', '--pretty=%s'], { cwd: dir, encoding: 'utf8' }).trim(),
		'chore(release): v1.0.4'
	);
	assert.equal(
		execFileSync('git', ['tag', '--list', 'v1.0.4'], { cwd: dir, encoding: 'utf8' }).trim(),
		'v1.0.4'
	);
});

test('releaseScript_whenRunWithVersion_updatesThemeVersionCommitsAndTags', () => {
	// Given
	const dir = createTempDir();
	const { stylePath, functionsPath, packagePath } = createReleaseFixtures(dir, '0.2.3');
	initializeGitRepository(dir);

	// When
	const result = spawnSync(process.execPath, [path.join(rootDir, 'bin/release.js'), '--version', '1.0.3'], {
		cwd: dir,
		encoding: 'utf8',
	});

	// Then
	assert.equal(result.status, 0);
	assert.match(fs.readFileSync(stylePath, 'utf8'), /^Version: 1\.0\.3$/mu);
	assert.match(fs.readFileSync(functionsPath, 'utf8'), /'KIYOSE_VERSION'\s*,\s*'1\.0\.3'/u);
	assert.equal(JSON.parse(fs.readFileSync(packagePath, 'utf8')).version, '1.0.3');
	assert.match(result.stdout, /Version updated to 1\.0\.3/u);
	assert.match(result.stdout, /Release commit created for v1\.0\.3/u);
	assert.match(result.stdout, /Created tag v1\.0\.3/u);
	assert.doesNotMatch(result.stdout, /Tag deferred/u);
	assert.equal(
		execFileSync('git', ['log', '-1', '--pretty=%s'], { cwd: dir, encoding: 'utf8' }).trim(),
		'chore(release): v1.0.3'
	);
	assert.equal(
		execFileSync('git', ['tag', '--list', 'v1.0.3'], { cwd: dir, encoding: 'utf8' }).trim(),
		'v1.0.3'
	);
});
