#!/bin/bash
# Script pour exécuter Prettier via Docker
# Usage: ./bin/prettier.sh [options]

set -e

docker run --rm \
	-v "$(pwd):/app" \
	-w /app \
	node:18-alpine \
	sh -c "npm install --silent && npx prettier $*"
