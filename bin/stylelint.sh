#!/bin/bash
# Script pour exécuter Stylelint via Docker
# Usage: ./bin/stylelint.sh [options]

set -o pipefail

docker run --rm \
	-v "$(pwd):/app" \
	-w /app \
	node:20-alpine \
	sh -c "npm install --silent && npx stylelint $*"
