#!/usr/bin/env node
'use strict';

const { execFileSync } = require('node:child_process');
const fs = require('node:fs');
const path = require('node:path');

const BUMP_TYPES = new Set(['major', 'minor', 'patch']);
const SEMVER_PATTERN = /^(0|[1-9]\d*)\.(0|[1-9]\d*)\.(0|[1-9]\d*)$/u;
const THEME_VERSION_PATTERN = /^Version:\s*(\S+)\s*$/mu;

function validateVersion(version) {
	if (!SEMVER_PATTERN.test(version)) {
		throw new Error(`Invalid version "${version}". Expected MAJOR.MINOR.PATCH.`);
	}
}

function parseArguments(argv = []) {
	const options = {
		bump: 'minor',
		isCommitRequested: false,
		isHelpRequested: false,
		version: null,
	};
	let hasBumpOption = false;

	for (let index = 0; index < argv.length; index += 1) {
		const argument = argv[index];

		if (argument === '--help' || argument === '-h') {
			options.isHelpRequested = true;
			continue;
		}

		if (argument === '--commit') {
			options.isCommitRequested = true;
			continue;
		}

		if (argument === '--version') {
			index += 1;
			if (!argv[index]) {
				throw new Error('--version requires a MAJOR.MINOR.PATCH value.');
			}
			options.version = argv[index];
			continue;
		}

		if (argument.startsWith('--version=')) {
			options.version = argument.slice('--version='.length);
			continue;
		}

		if (argument === '--bump') {
			index += 1;
			if (!argv[index]) {
				throw new Error('--bump requires major, minor, or patch.');
			}
			options.bump = argv[index];
			hasBumpOption = true;
			continue;
		}

		if (argument.startsWith('--bump=')) {
			options.bump = argument.slice('--bump='.length);
			hasBumpOption = true;
			continue;
		}

		if (BUMP_TYPES.has(argument)) {
			options.bump = argument;
			hasBumpOption = true;
			continue;
		}

		throw new Error(`Unknown argument "${argument}".`);
	}

	if (!BUMP_TYPES.has(options.bump)) {
		throw new Error(`Invalid bump "${options.bump}". Expected major, minor, or patch.`);
	}

	if (options.version) {
		validateVersion(options.version);
		if (hasBumpOption) {
			throw new Error('Use either --version or a bump type, not both.');
		}
	}

	return options;
}

function parseVersion(version) {
	validateVersion(version);
	const [, major, minor, patch] = version.match(SEMVER_PATTERN);

	return {
		major: Number(major),
		minor: Number(minor),
		patch: Number(patch),
	};
}

function resolveTargetVersion(currentVersion, options) {
	if (options.version) {
		return options.version;
	}

	const version = parseVersion(currentVersion);

	if (options.bump === 'major') {
		return `${version.major + 1}.0.0`;
	}

	if (options.bump === 'minor') {
		return `${version.major}.${version.minor + 1}.0`;
	}

	return `${version.major}.${version.minor}.${version.patch + 1}`;
}

function readThemeVersion(stylePath) {
	const content = fs.readFileSync(stylePath, 'utf8');
	const match = content.match(THEME_VERSION_PATTERN);

	if (!match) {
		throw new Error(`Theme version header not found in ${stylePath}.`);
	}

	validateVersion(match[1]);
	return match[1];
}

function writeThemeVersion(stylePath, version) {
	validateVersion(version);
	const content = fs.readFileSync(stylePath, 'utf8');

	if (!THEME_VERSION_PATTERN.test(content)) {
		throw new Error(`Theme version header not found in ${stylePath}.`);
	}

	fs.writeFileSync(stylePath, content.replace(THEME_VERSION_PATTERN, `Version: ${version}`), 'utf8');
}

function runGit(cwd, args, options = {}) {
	return execFileSync('git', args, {
		cwd,
		encoding: 'utf8',
		stdio: options.stdio || ['ignore', 'pipe', 'pipe'],
	});
}

function isGitRepository(cwd) {
	try {
		runGit(cwd, ['rev-parse', '--is-inside-work-tree']);
		return true;
	} catch (error) {
		return false;
	}
}

function getGitStatus(cwd) {
	if (!isGitRepository(cwd)) {
		return null;
	}

	return runGit(cwd, ['status', '--porcelain']).trim();
}

function isGitWorkTreeClean(cwd) {
	return getGitStatus(cwd) === '';
}

function gitTagExists(cwd, tagName) {
	try {
		runGit(cwd, ['rev-parse', '--quiet', '--verify', `refs/tags/${tagName}`]);
		return true;
	} catch (error) {
		return false;
	}
}

function createGitTag(cwd, version) {
	const tagName = `v${version}`;

	if (gitTagExists(cwd, tagName)) {
		throw new Error(`Git tag ${tagName} already exists.`);
	}

	runGit(cwd, ['tag', '-a', tagName, '-m', `Release ${version}`]);
	return tagName;
}

function createReleaseCommit(cwd, stylePath, version) {
	runGit(cwd, ['add', '--', stylePath]);
	runGit(cwd, ['commit', '-m', `chore(release): v${version}`]);
}

function prepareRelease({
	argv = [],
	cwd = process.cwd(),
	stylePath = path.join(cwd, 'latelierkiyose/style.css'),
} = {}) {
	const options = parseArguments(argv);
	const currentVersion = readThemeVersion(stylePath);
	const hasCurrentVersionTag = isGitRepository(cwd) && gitTagExists(cwd, `v${currentVersion}`);
	const shouldResolveRequestedTarget = Boolean(options.version) || hasCurrentVersionTag;
	const targetVersion = shouldResolveRequestedTarget
		? resolveTargetVersion(currentVersion, options)
		: currentVersion;
	const tagName = `v${targetVersion}`;
	const isCleanBeforeUpdate = isGitWorkTreeClean(cwd);
	let isVersionUpdated = false;
	let isCommitCreated = false;
	let isTagCreated = false;
	let isTagDeferred = false;

	if (currentVersion !== targetVersion) {
		if (options.isCommitRequested && !isCleanBeforeUpdate) {
			throw new Error('Working tree must be clean before using --commit.');
		}

		writeThemeVersion(stylePath, targetVersion);
		isVersionUpdated = true;
	}

	if (options.isCommitRequested && isVersionUpdated) {
		createReleaseCommit(cwd, stylePath, targetVersion);
		isCommitCreated = true;
	}

	if (isGitWorkTreeClean(cwd)) {
		createGitTag(cwd, targetVersion);
		isTagCreated = true;
	} else {
		isTagDeferred = true;
	}

	return {
		currentVersion,
		isCommitCreated,
		isTagCreated,
		isTagDeferred,
		isVersionUpdated,
		tagName,
		targetVersion,
	};
}

function usage() {
	return `Usage:
  npm run release
  npm run release -- patch
  npm run release -- --bump major
  npm run release -- --version 1.0.3
  npm run release -- --version 1.0.3 --commit

Options:
  major|minor|patch      Bump type. Defaults to minor.
  --bump <type>          Same as the positional bump type.
  --version <version>    Set an explicit MAJOR.MINOR.PATCH version.
  --commit               Create chore(release) commit before tagging.
`;
}

function main() {
	try {
		const argv = process.argv.slice(2);
		if (argv.includes('--help') || argv.includes('-h')) {
			process.stdout.write(usage());
			return;
		}

		const result = prepareRelease({ argv });

		if (result.isVersionUpdated) {
			process.stdout.write(`Version updated to ${result.targetVersion} in latelierkiyose/style.css\n`);
		} else {
			process.stdout.write(`Theme version already set to ${result.targetVersion}\n`);
		}

		if (result.isCommitCreated) {
			process.stdout.write(`Release commit created for ${result.tagName}\n`);
		}

		if (result.isTagCreated) {
			process.stdout.write(`Created tag ${result.tagName}\n`);
		}

		if (result.isTagDeferred) {
			process.stdout.write(
				`Tag deferred: commit the version update, then rerun "npm run release -- --version ${result.targetVersion}" to create ${result.tagName}.\n`
			);
		}
	} catch (error) {
		process.stderr.write(`${error.message}\n`);
		process.exitCode = 1;
	}
}

if (require.main === module) {
	main();
}

module.exports = {
	createGitTag,
	gitTagExists,
	parseArguments,
	prepareRelease,
	readThemeVersion,
	resolveTargetVersion,
	writeThemeVersion,
};
