#!/usr/bin/env bash
set -Eeuo pipefail

SOURCE_ROOT="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/.." && pwd)"
TARGET="${1:-$HOME/blog-espelunca/theme/espelunca-blog}"
STAMP="$(date +%Y%m%d-%H%M%S)"
TARGET_PARENT="$(dirname -- "$TARGET")"
BACKUP="${TARGET}.backup-${STAMP}"

required=(style.css theme.json functions.php templates parts)
for item in "${required[@]}"; do
  if [[ ! -e "$SOURCE_ROOT/$item" ]]; then
    printf 'ERRO: arquivo/diretório obrigatório ausente na fonte: %s\n' "$item" >&2
    exit 1
  fi
done

if ! command -v rsync >/dev/null 2>&1; then
  printf 'ERRO: rsync não está instalado. Instale o pacote rsync antes do deploy para permitir sincronização segura.\n' >&2
  exit 1
fi

mkdir -p "$TARGET_PARENT"

if [[ -d "$TARGET" ]] && [[ -n "$(find "$TARGET" -mindepth 1 -maxdepth 1 -print -quit 2>/dev/null)" ]]; then
  cp -a -- "$TARGET" "$BACKUP"
  printf 'Backup do tema atual: %s\n' "$BACKUP"
else
  mkdir -p "$TARGET"
fi

rsync -a --delete \
  --exclude '.git/' \
  --exclude '.github/' \
  --exclude '.gitignore' \
  --exclude 'scripts/' \
  --exclude 'README.md' \
  --exclude 'CHANGELOG.md' \
  --exclude 'LICENSE' \
  "$SOURCE_ROOT/" "$TARGET/"

find "$TARGET" -type d -exec chmod 0755 {} +
find "$TARGET" -type f -exec chmod 0644 {} +

printf 'Tema sincronizado com sucesso em: %s\n' "$TARGET"
printf 'Próximo passo: valide e ative pelo WP-CLI da instalação do blog.\n'
