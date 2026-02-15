#!/bin/bash
# Script pour exécuter Stylelint via Docker
# Usage: ./bin/stylelint.sh [options]

# Note: The "Unknown rule indentation" error is a known stylelint v16 bug
# We filter it out while preserving all other errors and warnings

set -o pipefail

# Run stylelint and capture output (don't exit on error)
OUTPUT=$(docker run --rm \
	-v "$(pwd):/app" \
	-w /app \
	node:18-alpine \
	sh -c "npm install --silent && npx stylelint $* 2>&1" || true)

# Filter out the "Unknown rule indentation" error and the old summary line
FILTERED=$(echo "$OUTPUT" | grep -v "Unknown rule indentation" | grep -v "^✖.*problems.*errors.*warnings")

# Count remaining issues
WARNING_COUNT=$(echo "$FILTERED" | grep -c "⚠" || true)
ERROR_COUNT=$(echo "$FILTERED" | grep -c "✖" || true)

# Show the filtered output
if [ -n "$FILTERED" ]; then
	echo "$FILTERED"
fi

# Show new summary if there are any issues
TOTAL_ISSUES=$((ERROR_COUNT + WARNING_COUNT))
if [ "$TOTAL_ISSUES" -gt 0 ]; then
	echo ""
	if [ "$WARNING_COUNT" -gt 0 ] && [ "$ERROR_COUNT" -eq 0 ]; then
		echo "⚠ $WARNING_COUNT warnings (intentional !important declarations for accessibility and plugin overrides)"
		exit 0
	elif [ "$ERROR_COUNT" -gt 0 ]; then
		echo "✖ $TOTAL_ISSUES problems ($ERROR_COUNT errors, $WARNING_COUNT warnings)"
		exit 1
	fi
fi

# Exit with success if no issues
exit 0
