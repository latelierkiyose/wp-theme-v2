import js from "@eslint/js";
import globals from "globals";
import eslintConfigPrettier from "eslint-config-prettier/flat";
import { defineConfig, globalIgnores } from "eslint/config";

export default defineConfig([
	globalIgnores([
		"node_modules/",
		"vendor/",
		"uploads/",
		"**/*.min.js",
		"latelierkiyose/assets/js/vendor/",
	]),

	{
		files: ["latelierkiyose/assets/js/**/*.js"],
		plugins: { js },
		extends: ["js/recommended"],
		languageOptions: {
			ecmaVersion: "latest",
			sourceType: "module",
			globals: {
				...globals.browser,
			},
		},
		rules: {
			"no-unused-vars": "warn",
			"no-console": "warn",
			eqeqeq: ["error", "always"],
			curly: ["error", "all"],
			camelcase: ["error", { properties: "never" }],
			"no-var": "error",
			"prefer-const": "error",
			"prefer-arrow-callback": "error",
			// arrow-spacing supprimée : règle de formatage retirée dans ESLint 10, Prettier gère
			"no-duplicate-imports": "error",
		},
	},

	eslintConfigPrettier,
]);
