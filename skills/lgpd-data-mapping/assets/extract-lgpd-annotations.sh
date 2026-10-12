#!/usr/bin/env bash
# Extract @lgpd: annotations from PHP sources (migrations, models, controllers)
# to seed .lgpd/data-map.md
# Usage: ./extract-lgpd-annotations.sh app/ database/ routes/ > .lgpd/data-map-draft.md
#
# Fork de goul4rt/lgpd-skills — adaptado de Prisma schema para fontes PHP.

set -euo pipefail

SEARCH_PATHS=("$@")
if [[ ${#SEARCH_PATHS[@]} -eq 0 ]]; then
  SEARCH_PATHS=(app database routes)
fi

echo "# Data Map Draft — extracted from PHP sources"
echo
echo "**Generated**: $(date -Iseconds)"
echo

found_any=0
for path in "${SEARCH_PATHS[@]}"; do
  [[ -e "$path" ]] || continue
  # Anotações em docblocks (/** @lgpd:... */) ou comentários de linha (// @lgpd:...)
  while IFS= read -r file; do
    matches=$(grep -nE '@lgpd:' "$file" || true)
    if [[ -n "$matches" ]]; then
      found_any=1
      echo -e "\n## $file"
      echo "$matches" | sed 's/^[[:space:]]*//; s/^\([0-9]*\):[[:space:]]*/- L\1: /'
    fi
  done < <(find "$path" -name '*.php' -type f | sort)
done

if [[ $found_any -eq 0 ]]; then
  echo "_(no @lgpd: annotations found — run data-mapping manually)_"
fi
