---
description: Naming conventions and coding style for the Kiyose theme
globs:
  - "latelierkiyose/**/*.php"
  - "latelierkiyose/**/*.css"
  - "latelierkiyose/**/*.js"
---

# Conventions Kiyose

## PHP
- Prefix all functions, hooks, handles with `kiyose_`
- Text domain: `kiyose` (for all `__()`, `_e()`, `esc_html__()`, etc.)
- Indentation: tabs (never spaces) — WPCS enforced

## CSS
- BEM methodology for class naming (`.block__element--modifier`)
- All custom properties MUST use `--kiyose-` prefix (Stylelint enforced: `^kiyose-.+`)
- Common mistake: creating `--my-var` instead of `--kiyose-my-var` — this will fail Stylelint

## JavaScript
- Vanilla ES6+ only (no jQuery, no frameworks)
- Prefix boolean variables with is/has/should
