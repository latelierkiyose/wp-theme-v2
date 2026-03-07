#!/bin/bash
# Post-edit hook: runs linters after Claude edits PHP or CSS files.
# Receives JSON on stdin with tool_input.file_path.

INPUT=$(cat)
FILE_PATH=$(echo "$INPUT" | jq -r '.tool_input.file_path // empty')

if [ -z "$FILE_PATH" ]; then
	exit 0
fi

# Run Stylelint for CSS files
if [[ "$FILE_PATH" == *.css ]]; then
	"$CLAUDE_PROJECT_DIR"/node_modules/.bin/stylelint "$FILE_PATH" 2>&1
	exit $?
fi

# Run PHPCS for PHP files
if [[ "$FILE_PATH" == *.php ]]; then
	"$CLAUDE_PROJECT_DIR"/bin/phpcs.sh "$FILE_PATH" 2>&1
	exit $?
fi

exit 0
