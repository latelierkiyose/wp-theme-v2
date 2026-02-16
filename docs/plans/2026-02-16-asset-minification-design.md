# Asset Minification - Makefile Integration

**Date:** 2026-02-16
**Status:** Approved

## Overview

Add Makefile targets to expose the existing npm build scripts for CSS and JavaScript minification. This integrates asset building into the standard development workflow alongside linting and testing.

## Context

The project already has:
- Minification scripts: `bin/minify-css.js` (using cssnano) and `bin/minify-js.js` (using Terser)
- npm scripts: `build`, `build:css`, `build:js`, `build:clean`
- Minified files committed to the repository (not in .gitignore)

The Makefile currently exposes tasks for linting, testing, and formatting, but not for building minified assets.

## Requirements

1. Expose all build-related npm scripts in the Makefile
2. Integrate build into both `lint` and `test` workflows automatically
3. Maintain consistency with existing Makefile style (colors, help text, French descriptions)
4. Avoid duplication when running `make test` (which calls `make lint`)

## Design

### New Makefile Targets

Four new targets will be added:

1. **`build-css`** - Minify all CSS files via `npm run build:css`
2. **`build-js`** - Minify all JavaScript files via `npm run build:js`
3. **`build`** - Run both `build-css` and `build-js` in sequence
4. **`build-clean`** - Remove all minified files via `npm run build:clean`

### Integration Strategy

**Approach:** Build as prerequisite of lint

The `lint` target will be updated to run `build` first:

```makefile
# Before
lint: lint-js lint-css

# After
lint: build lint-js lint-css
```

Since the `test` target already calls `lint`:
```makefile
test: phpcs phpunit lint
```

This creates the dependency chain: `test` → `lint` → `build`

**Benefits:**
- Build runs automatically for both `make lint` and `make test`
- No duplication - build runs only once even when test calls lint
- Clean, maintainable dependency structure

### Implementation Details

**Help text (French, consistent with existing tasks):**
```makefile
build-css: ## Minifier les fichiers CSS
build-js: ## Minifier les fichiers JavaScript
build: ## Minifier tous les assets (CSS + JS)
build-clean: ## Supprimer tous les fichiers minifiés
```

**Output formatting (using existing color variables):**
- Blue (`$(BLUE)`) for action messages: "🎨 Minification des CSS..."
- Green (`$(GREEN)`) for success messages: "✓ CSS minifiés"
- Emojis consistent with existing tasks: 🎨 (CSS), ⚡ (JS), 🧹 (clean)

**Error handling:**
- npm commands fail naturally on errors
- Make stops execution on failure (default behavior)
- Minification scripts already provide clear error messages

**Placement:**
- Insert after `format-check` task (line 62)
- Before `start` task (line 64)
- Groups "code quality" tasks together (lint, format, build)

**PHONY declaration:**
Update `.PHONY` line to include: `build-css build-js build build-clean`

### Complete Task Definitions

```makefile
build-css: ## Minifier les fichiers CSS
	@echo "$(BLUE)🎨 Minification des CSS...$(NC)"
	@npm run build:css
	@echo "$(GREEN)✓ CSS minifiés$(NC)"

build-js: ## Minifier les fichiers JavaScript
	@echo "$(BLUE)⚡ Minification des JS...$(NC)"
	@npm run build:js
	@echo "$(GREEN)✓ JS minifiés$(NC)"

build: build-css build-js ## Minifier tous les assets (CSS + JS)
	@echo "$(GREEN)✓ Tous les assets sont minifiés$(NC)"

build-clean: ## Supprimer tous les fichiers minifiés
	@echo "$(BLUE)🧹 Suppression des fichiers minifiés...$(NC)"
	@npm run build:clean
	@echo "$(GREEN)✓ Fichiers minifiés supprimés$(NC)"
```

## Usage Examples

```bash
# Build all assets
make build

# Build only CSS
make build-css

# Build only JavaScript
make build-js

# Clean minified files
make build-clean

# Lint (builds automatically first)
make lint

# Test (builds automatically via lint)
make test
```

## Testing

After implementation, verify:
1. `make build` successfully minifies all CSS and JS files
2. `make build-css` minifies only CSS files
3. `make build-js` minifies only JS files
4. `make build-clean` removes all .min.css and .min.js files
5. `make lint` runs build before linting
6. `make test` runs build only once (not twice)
7. Build failures stop the Make execution
8. `make help` displays the new tasks correctly

## Alternatives Considered

**Alternative 1: Build in both test and lint**
- Rejected due to duplication - would build twice when running `make test`

**Alternative 2: Makefile prerequisites**
- More complex syntax with `@$(MAKE)` calls
- Harder to read and maintain
- Not worth the added complexity

**Alternative 3: Manual build only**
- Doesn't meet requirement to integrate with lint/test workflows
- Easy to forget to rebuild after changes

## Non-Goals

- Automatic build on file watch (remains a manual/explicit operation)
- Source maps generation (not requested)
- Build performance optimization (scripts already performant)
- Changes to minification configuration (works well as-is)
