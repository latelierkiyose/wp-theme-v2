#!/bin/bash
# Script pour exécuter ESLint via Docker
# Usage: ./bin/eslint.sh [options]

set -e

docker run --rm \
	-v "$(pwd):/app" \
	-w /app \
	node:18-alpine \
	sh -c "npm install --silent && npx eslint $*"
