# Makefile Build Tasks Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Add Makefile targets for CSS/JS minification and integrate with lint/test workflows

**Architecture:** Extend existing Makefile with four new build targets (build-css, build-js, build, build-clean) that wrap npm scripts. Integrate build into lint target to create automatic dependency chain for test workflow.

**Tech Stack:** GNU Make, npm

---

## Task 1: Update .PHONY Declaration

**Files:**
- Modify: `Makefile:4`

**Step 1: Add build targets to .PHONY**

Update line 4 from:
```makefile
.PHONY: help install install-hooks phpcs phpcs-fix phpunit lint-js lint-css lint lint-fix format-js format-check start stop clean test
```

To:
```makefile
.PHONY: help install install-hooks phpcs phpcs-fix phpunit lint-js lint-css lint lint-fix format-js format-check build-css build-js build build-clean start stop clean test
```

**Step 2: Verify syntax**

Run: `make help`
Expected: Help output displays without errors (no syntax errors in Makefile)

**Step 3: Commit**

```bash
git add Makefile
git commit -m "chore: add build targets to PHONY declaration"
```

---

## Task 2: Add build-css Target

**Files:**
- Modify: `Makefile` (after line 62, before `start:` target)

**Step 1: Add build-css target**

Insert after line 62 (after `format-check` target):

```makefile
build-css: ## Minifier les fichiers CSS
	@echo "$(BLUE)🎨 Minification des CSS...$(NC)"
	@npm run build:css
	@echo "$(GREEN)✓ CSS minifiés$(NC)"

```

Note: Include blank line after target for consistency with other targets.

**Step 2: Test build-css target**

Run: `make build-css`
Expected:
- Blue "🎨 Minification des CSS..." message
- npm runs cssnano minification
- Green "✓ CSS minifiés" success message
- All .css files get corresponding .min.css versions

**Step 3: Verify help text**

Run: `make help`
Expected: "build-css" appears in help output with description "Minifier les fichiers CSS"

**Step 4: Commit**

```bash
git add Makefile
git commit -m "feat: add build-css target to Makefile"
```

---

## Task 3: Add build-js Target

**Files:**
- Modify: `Makefile` (after build-css target)

**Step 1: Add build-js target**

Insert after build-css target:

```makefile
build-js: ## Minifier les fichiers JavaScript
	@echo "$(BLUE)⚡ Minification des JS...$(NC)"
	@npm run build:js
	@echo "$(GREEN)✓ JS minifiés$(NC)"

```

**Step 2: Test build-js target**

Run: `make build-js`
Expected:
- Blue "⚡ Minification des JS..." message
- npm runs Terser minification
- Green "✓ JS minifiés" success message
- All .js files get corresponding .min.js versions

**Step 3: Verify help text**

Run: `make help`
Expected: "build-js" appears in help output with description "Minifier les fichiers JavaScript"

**Step 4: Commit**

```bash
git add Makefile
git commit -m "feat: add build-js target to Makefile"
```

---

## Task 4: Add build Target

**Files:**
- Modify: `Makefile` (after build-js target)

**Step 1: Add build target**

Insert after build-js target:

```makefile
build: build-css build-js ## Minifier tous les assets (CSS + JS)
	@echo "$(GREEN)✓ Tous les assets sont minifiés$(NC)"

```

Note: This target depends on build-css and build-js, so Make will run them first.

**Step 2: Test build target**

Run: `make build`
Expected:
- build-css runs first (blue CSS message, minification, green success)
- build-js runs second (blue JS message, minification, green success)
- Final green "✓ Tous les assets sont minifiés" message
- All CSS and JS files have .min versions

**Step 3: Verify help text**

Run: `make help`
Expected: "build" appears in help output with description "Minifier tous les assets (CSS + JS)"

**Step 4: Commit**

```bash
git add Makefile
git commit -m "feat: add build target to Makefile"
```

---

## Task 5: Add build-clean Target

**Files:**
- Modify: `Makefile` (after build target)

**Step 1: Add build-clean target**

Insert after build target:

```makefile
build-clean: ## Supprimer tous les fichiers minifiés
	@echo "$(BLUE)🧹 Suppression des fichiers minifiés...$(NC)"
	@npm run build:clean
	@echo "$(GREEN)✓ Fichiers minifiés supprimés$(NC)"

```

**Step 2: Test build-clean target**

Run: `make build-clean`
Expected:
- Blue "🧹 Suppression des fichiers minifiés..." message
- npm removes all .min.css and .min.js files
- Green "✓ Fichiers minifiés supprimés" success message
- All .min.css and .min.js files are deleted

**Step 3: Rebuild and verify**

Run: `make build`
Expected: Minified files recreated successfully

**Step 4: Verify help text**

Run: `make help`
Expected: "build-clean" appears in help output with description "Supprimer tous les fichiers minifiés"

**Step 5: Commit**

```bash
git add Makefile
git commit -m "feat: add build-clean target to Makefile"
```

---

## Task 6: Integrate build into lint Workflow

**Files:**
- Modify: `Makefile:48` (lint target)

**Step 1: Update lint target**

Update line 48 from:
```makefile
lint: lint-js lint-css ## Valider tout le code (JS + CSS)
```

To:
```makefile
lint: build lint-js lint-css ## Valider tout le code (JS + CSS)
```

**Step 2: Test lint with build integration**

Run: `make lint`
Expected:
- build runs first (CSS minification, then JS minification)
- lint-js runs (ESLint validation)
- lint-css runs (Stylelint validation)
- Green "✓ Validation JS et CSS terminée" message

**Step 3: Test test workflow**

Run: `make test`
Expected:
- PHPCS runs
- PHPUnit runs
- build runs (via lint - should see minification output)
- lint-js runs
- lint-css runs
- Green "✓ Tous les tests sont passés" message
- Build runs ONLY ONCE (not duplicated)

**Step 4: Commit**

```bash
git add Makefile
git commit -m "feat: integrate build into lint workflow

Build now runs automatically before linting. Since test calls lint,
build also runs for make test. This ensures assets are always minified
before validation.
"
```

---

## Task 7: Final Verification

**Files:**
- Verify: `Makefile`

**Step 1: Verify all targets work independently**

```bash
make build-css    # Should minify CSS only
make build-js     # Should minify JS only
make build        # Should minify both
make build-clean  # Should remove minified files
make build        # Should rebuild after clean
```

Expected: All commands succeed with appropriate output

**Step 2: Verify integration**

```bash
make lint         # Should build, then lint
make test         # Should build once, then test
```

Expected: Build runs before linting, no duplication in test

**Step 3: Verify help output**

Run: `make help`
Expected: All four build targets appear in alphabetical order with French descriptions

**Step 4: Verify error handling**

```bash
# Temporarily break a CSS file to test error handling
echo "invalid css {" >> latelierkiyose/assets/css/main.css
make build-css
```

Expected:
- Error message from cssnano
- Make stops execution
- Non-zero exit code

```bash
# Restore the file
git restore latelierkiyose/assets/css/main.css
```

**Step 5: Final commit (if any cleanup needed)**

If any fixes were needed during verification:
```bash
git add Makefile
git commit -m "fix: final adjustments to build targets"
```

---

## Verification Checklist

After completing all tasks, verify:

- [ ] `make build` successfully minifies all CSS and JS files
- [ ] `make build-css` minifies only CSS files
- [ ] `make build-js` minifies only JS files
- [ ] `make build-clean` removes all .min.css and .min.js files
- [ ] `make lint` runs build before linting
- [ ] `make test` runs build only once (not twice)
- [ ] Build failures stop Make execution
- [ ] `make help` displays all new tasks with French descriptions
- [ ] Output formatting matches existing tasks (colors, emojis)
- [ ] .PHONY declaration includes all new targets

---

## Notes

- No unit tests needed (Makefile targets verified by execution)
- Manual verification is the testing strategy for build tools
- Error handling relies on npm script exit codes (already implemented)
- French help text maintains consistency with existing Makefile
