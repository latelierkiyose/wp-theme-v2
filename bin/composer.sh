#!/bin/bash
#
# Wrapper pour exécuter Composer via Docker sans installation locale de PHP.
#
# Usage:
#   ./bin/composer.sh install
#   ./bin/composer.sh update
#   ./bin/composer.sh require <package>
#

set -e

# Couleurs pour l'output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}🐳 Exécution de Composer via Docker...${NC}"

# Détecter si on est dans un terminal interactif
if [ -t 0 ]; then
	DOCKER_FLAGS="-it"
else
	DOCKER_FLAGS="-i"
fi

# Exécuter composer dans un container Docker éphémère
docker run --rm \
	${DOCKER_FLAGS} \
	--volume "$PWD":/app \
	--user "$(id -u):$(id -g)" \
	--env COMPOSER_ALLOW_SUPERUSER=1 \
	--env HOME=/tmp \
	composer:2 \
	sh -c "git config --global --add safe.directory /app 2>/dev/null || true; composer $*"

echo -e "${GREEN}✓ Terminé${NC}"
