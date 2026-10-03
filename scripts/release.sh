#!/usr/bin/env bash
#
# Builds a deployable .zip of the current commit for the cPanel host (no SSH).
#
# The package is extracted over the app folder on the server, so it must never
# contain .env or public/uploads: both live only on the server and survive deploys.
# Config/route/view caches are not built here because they store absolute paths
# of the machine that generates them.
#
# Usage: scripts/release.sh

set -euo pipefail

root="$(git rev-parse --show-toplevel)"
cd "$root"

if [ -n "$(git status --porcelain)" ]; then
    echo "Working tree has uncommitted changes. Commit or stash them first:" >&2
    git status --short >&2
    exit 1
fi

commit="$(git rev-parse --short HEAD)"
name="the-forest-meadow-$(date +%Y%m%d-%H%M)-${commit}"
work="$(mktemp -d)"
trap 'rm -rf "${work:?}"' EXIT

echo "→ Exporting commit ${commit}"
mkdir "${work}/app"
# Only tracked files: no .env, public/uploads, node_modules or local leftovers.
git archive HEAD | tar -x -C "${work}/app"
cd "${work}/app"

echo "→ Installing production PHP dependencies"
composer install --no-dev --optimize-autoloader --no-interaction --no-progress --quiet

echo "→ Publishing Filament assets (not tracked by git)"
php artisan filament:assets --quiet

echo "→ Building front-end assets"
npm ci --silent
npm run build --silent

echo "→ Removing development-only files"
rm -rf node_modules tests
rm -f phpunit.xml docker-compose.yml .editorconfig .npmrc CLAUDE.md

echo "→ Creating package"
mkdir -p "${root}/release"
zip -rq "${root}/release/${name}.zip" .

echo
echo "Package: release/${name}.zip ($(du -h "${root}/release/${name}.zip" | cut -f1))"
echo "Next: run pending migrations with 'php artisan migrate --env=production --force',"
echo "then upload the .zip to the app folder on the server and extract it over the old files."
