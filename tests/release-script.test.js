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

test('prepareRelease_whenVersionChangesWithoutCommit_defersTagCreation', () => {
	// Given
	const dir = createTempDir();
	const stylePath = createThemeStyle(dir, '1.0.2');
	initializeGitRepository(dir);

	// When
	const result = releaseScript.prepareRelease({
		argv: ['--version', '1.0.3'],
		cwd: dir,
		stylePath,
	});

	// Then
	assert.equal(result.targetVersion, '1.0.3');
	assert.equal(result.tagName, 'v1.0.3');
	assert.equal(result.isVersionUpdated, true);
	assert.equal(result.isTagCreated, false);
	assert.equal(result.isTagDeferred, true);
	assert.match(fs.readFileSync(stylePath, 'utf8'), /^Version: 1\.0\.3$/mu);
	assert.throws(() => execFileSync('git', ['rev-parse', 'v1.0.3'], { cwd: dir, stdio: 'pipe' }));
});

test('prepareRelease_whenVersionAlreadyCommitted_createsAnnotatedTag', () => {
	// Given
	const dir = createTempDir();
	const stylePath = createThemeStyle(dir, '1.0.3');
	initializeGitRepository(dir);

	// When
	const result = releaseScript.prepareRelease({
		argv: ['--version', '1.0.3'],
		cwd: dir,
		stylePath,
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
	const stylePath = createThemeStyle(dir, '1.0.3');
	initializeGitRepository(dir);

	// When
	const result = releaseScript.prepareRelease({
		argv: [],
		cwd: dir,
		stylePath,
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

test('prepareRelease_whenCurrentVersionIsAlreadyTagged_bumpsMinorAndDefersTagCreation', () => {
	// Given
	const dir = createTempDir();
	const stylePath = createThemeStyle(dir, '1.0.3');
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
	});

	// Then
	assert.equal(result.targetVersion, '1.1.0');
	assert.equal(result.isVersionUpdated, true);
	assert.equal(result.isTagCreated, false);
	assert.equal(result.isTagDeferred, true);
	assert.match(fs.readFileSync(stylePath, 'utf8'), /^Version: 1\.1\.0$/mu);
});

test('prepareRelease_whenCommitIsRequested_commitsVersionAndCreatesAnnotatedTag', () => {
	// Given
	const dir = createTempDir();
	const stylePath = createThemeStyle(dir, '1.0.3');
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

test('releaseScript_whenRunWithVersion_updatesThemeVersion', () => {
	// Given
	const dir = createTempDir();
	const stylePath = createThemeStyle(dir, '0.2.3');
	initializeGitRepository(dir);

	// When
	const result = spawnSync(process.execPath, [path.join(rootDir, 'bin/release.js'), '--version', '1.0.3'], {
		cwd: dir,
		encoding: 'utf8',
	});

	// Then
	assert.equal(result.status, 0);
	assert.match(fs.readFileSync(stylePath, 'utf8'), /^Version: 1\.0\.3$/mu);
	assert.match(result.stdout, /Version updated to 1\.0\.3/u);
	assert.match(result.stdout, /Tag deferred/u);
});
