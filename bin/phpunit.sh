#!/bin/bash
set -e

# Détermine le répertoire du projet (où se trouve le Makefile)
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

# Couleurs pour l'affichage
BLUE='\033[0;34m'
GREEN='\033[0;32m'
NC='\033[0m' # No Color

echo -e "${BLUE}🧪 Exécution de PHPUnit via Docker...${NC}"

# Exécute PHPUnit dans le conteneur Docker
docker run --rm \
	-v "$PROJECT_ROOT:/app" \
	-w /app \
	-u "$(id -u):$(id -g)" \
	composer:2 \
	./vendor/bin/phpunit "$@"

echo -e "${GREEN}✓ Terminé${NC}"
