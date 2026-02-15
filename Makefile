# L'Atelier Kiyose - WordPress Theme v2
# Makefile pour simplifier les commandes de développement

.PHONY: help install phpcs phpcs-fix lint-js lint-css lint lint-fix format-js format-check start stop clean test

# Couleurs pour l'aide
BLUE := \033[0;34m
GREEN := \033[0;32m
YELLOW := \033[1;33m
NC := \033[0m # No Color

help: ## Afficher cette aide
	@echo "$(BLUE)L'Atelier Kiyose - Commandes disponibles:$(NC)"
	@echo ""
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  $(GREEN)%-15s$(NC) %s\n", $$1, $$2}'
	@echo ""

install: ## Installer les dépendances Composer via Docker
	@echo "$(BLUE)📦 Installation des dépendances Composer...$(NC)"
	@./bin/composer.sh install
	@echo "$(GREEN)✓ Dépendances installées$(NC)"

update: ## Mettre à jour les dépendances Composer
	@echo "$(BLUE)📦 Mise à jour des dépendances...$(NC)"
	@./bin/composer.sh update
	@echo "$(GREEN)✓ Dépendances mises à jour$(NC)"

phpcs: ## Valider le code avec PHPCS
	@./bin/phpcs.sh

phpcs-fix: ## Corriger automatiquement les violations PHPCS
	@./bin/phpcbf.sh

lint-js: ## Valider le code JavaScript avec ESLint
	@./bin/eslint.sh 'latelierkiyose/assets/js/**/*.js'

lint-css: ## Valider le code CSS avec Stylelint
	@./bin/stylelint.sh 'latelierkiyose/**/*.css'

lint: lint-js lint-css ## Valider tout le code (JS + CSS)
	@echo "$(GREEN)✓ Validation JS et CSS terminée$(NC)"

lint-fix: ## Corriger automatiquement les violations JS et CSS
	@echo "$(BLUE)🔧 Correction automatique JS...$(NC)"
	@./bin/eslint.sh 'latelierkiyose/assets/js/**/*.js' --fix || true
	@echo "$(BLUE)🔧 Correction automatique CSS...$(NC)"
	@./bin/stylelint.sh 'latelierkiyose/**/*.css' --fix || true
	@echo "$(GREEN)✓ Corrections appliquées$(NC)"

format-js: ## Formatter le code JavaScript avec Prettier
	@./bin/prettier.sh --write 'latelierkiyose/assets/js/**/*.js'

format-check: ## Vérifier le formatage JavaScript sans modifier
	@./bin/prettier.sh --check 'latelierkiyose/assets/js/**/*.js'

start: ## Démarrer l'environnement WordPress (Docker)
	@echo "$(BLUE)🚀 Démarrage de WordPress...$(NC)"
	@docker compose up -d
	@echo "$(GREEN)✓ WordPress démarré sur http://127.0.0.1:8000$(NC)"
	@echo "$(GREEN)✓ PHPMyAdmin disponible sur http://127.0.0.1:40001$(NC)"

stop: ## Arrêter l'environnement WordPress
	@echo "$(BLUE)🛑 Arrêt de WordPress...$(NC)"
	@docker compose down
	@echo "$(GREEN)✓ WordPress arrêté$(NC)"

logs: ## Afficher les logs de WordPress
	@docker compose logs -f wordpress

clean: ## Nettoyer les fichiers générés (vendor/, cache)
	@echo "$(BLUE)🧹 Nettoyage...$(NC)"
	@rm -rf vendor/
	@rm -rf node_modules/
	@echo "$(GREEN)✓ Nettoyage terminé$(NC)"

test: phpcs lint ## Exécuter tous les tests (PHPCS + linters)
	@echo "$(GREEN)✓ Tous les tests sont passés$(NC)"

.DEFAULT_GOAL := help
