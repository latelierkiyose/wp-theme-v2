#!/bin/bash
# Script to install git hooks for L'Atelier Kiyose theme

set -e

HOOKS_DIR=".git/hooks"
HOOK_SOURCE="bin/hooks/pre-commit"
HOOK_TARGET="$HOOKS_DIR/pre-commit"

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}Installing git hooks...${NC}\n"

# Check if .git directory exists
if [ ! -d ".git" ]; then
	echo -e "${YELLOW}Error: Not a git repository${NC}"
	exit 1
fi

# Create hooks directory if it doesn't exist
mkdir -p "$HOOKS_DIR"

# Copy pre-commit hook
if [ -f "$HOOK_SOURCE" ]; then
	cp "$HOOK_SOURCE" "$HOOK_TARGET"
	chmod +x "$HOOK_TARGET"
	echo -e "${GREEN}✓ Pre-commit hook installed${NC}"
else
	echo -e "${YELLOW}Warning: Hook source file not found at $HOOK_SOURCE${NC}"
	exit 1
fi

echo -e "\n${GREEN}Git hooks installed successfully!${NC}"
echo -e "${YELLOW}The pre-commit hook will now run:${NC}"
echo -e "  - PHPCS on staged PHP files"
echo -e "  - ESLint on staged JavaScript files"
echo -e "  - Stylelint on staged CSS files"
echo -e "\n${YELLOW}To bypass hooks (not recommended):${NC}"
echo -e "  git commit --no-verify"
