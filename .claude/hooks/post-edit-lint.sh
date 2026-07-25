#!/bin/bash
# Post-edit hook: lint les fichiers PHP et CSS après une édition de Claude.
# Reçoit du JSON sur stdin avec tool_input.file_path.
#
# Contrat PostToolUse: la sortie n'est renvoyée à Claude que sur exit code 2,
# et uniquement depuis stderr. Tout autre code non nul est invisible pour Claude.

INPUT=$(cat)
FILE_PATH=$(echo "$INPUT" | jq -r '.tool_input.file_path // empty')
[ -z "$FILE_PATH" ] && exit 0

cd "$CLAUDE_PROJECT_DIR" 2>/dev/null || exit 0

# Chemin relatif au projet: indispensable, les wrappers Docker montent $PWD sur /app
REL="${FILE_PATH#"$CLAUDE_PROJECT_DIR"/}"
[ "$REL" = "$FILE_PATH" ] && exit 0 # fichier hors projet

case "$REL" in
	*.css)
		# Binaire local d'abord (rapide), mais seulement si node est installé sur l'hôte:
		# node_modules/ peut avoir été créé par le conteneur via le volume monté.
		# Sinon wrapper Docker (npm install à chaque appel).
		if [ -x node_modules/.bin/stylelint ] && command -v node >/dev/null 2>&1; then
			OUT=$(node_modules/.bin/stylelint "$REL" 2>&1)
		elif [ -f ./bin/stylelint.sh ]; then
			OUT=$(./bin/stylelint.sh "$REL" 2>&1)
		else
			exit 0
		fi
		;;
	*.php)
		# bin/phpcs.sh n'installe pas les dépendances: sans vendor/ il échoue avec
		# "exec: ./vendor/bin/phpcs: not found", ce qui serait remonté comme une
		# fausse violation à chaque édition. On ne lance rien tant que vendor/ manque.
		[ -f vendor/bin/phpcs ] || exit 0
		# Idem CSS: vendor/ peut provenir du conteneur, php n'est pas forcément sur l'hôte.
		if command -v php >/dev/null 2>&1; then
			OUT=$(vendor/bin/phpcs --standard=phpcs.xml "$REL" 2>&1)
		elif [ -f ./bin/phpcs.sh ]; then
			OUT=$(./bin/phpcs.sh --standard=phpcs.xml "$REL" 2>&1)
		else
			exit 0
		fi
		;;
	*)
		exit 0
		;;
esac

STATUS=$?
[ "$STATUS" -eq 0 ] && exit 0

echo "$OUT" >&2
exit 2
