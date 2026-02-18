#!/bin/bash
#
# Wrapper pour exécuter PHPCS via Docker sans installation locale de PHP.
#
# Usage:
#   ./bin/phpcs.sh
#   ./bin/phpcs.sh --report=summary
#

set -e

# Couleurs pour l'output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}🔍 Exécution de PHPCS via Docker...${NC}"

# Détecter si on est dans un terminal interactif
if [ -t 0 ]; then
	DOCKER_FLAGS="-it"
else
	DOCKER_FLAGS="-i"
fi

# Exécuter PHPCS dans un container PHP éphémère
docker run --rm \
	${DOCKER_FLAGS} \
	--volume "$PWD":/app \
	--workdir /app \
	php:8.3-cli \
	./vendor/bin/phpcs "$@"

echo -e "${GREEN}✓ Terminé${NC}"
