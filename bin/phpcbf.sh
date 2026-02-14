#!/bin/bash
#
# Wrapper pour exécuter PHPCBF (PHP Code Beautifier and Fixer) via Docker.
#
# Usage:
#   ./bin/phpcbf.sh
#

set -e

# Couleurs pour l'output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}🔧 Exécution de PHPCBF via Docker...${NC}"

# Détecter si on est dans un terminal interactif
if [ -t 0 ]; then
	DOCKER_FLAGS="-it"
else
	DOCKER_FLAGS="-i"
fi

# Exécuter PHPCBF dans un container PHP éphémère
docker run --rm \
	${DOCKER_FLAGS} \
	--volume "$PWD":/app \
	--workdir /app \
	php:8.1-cli \
	./vendor/bin/phpcbf "$@" || true

echo -e "${GREEN}✓ Terminé${NC}"
