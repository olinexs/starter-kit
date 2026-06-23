#!/bin/bash
#
# EO-ADS Starter Kit — one-command project bootstrap (Mac/Linux/Git-Bash).
#
# Creates a brand-new project from scratch:
#   project-root/
#   ├── backend/   ← fresh Laravel + eoads starter kit
#   └── frontend/  ← scaffolded automatically by eoads:install
#
# Usage:
#   ./create-project.sh <project-name>   # one command, no prompts
#   ./create-project.sh                  # will prompt for the name
#
set -e

echo ""
echo "========================================"
echo "  EO-ADS Project Bootstrap"
echo "========================================"
echo ""

# 0. Project name (from argument, or prompt if not given)
PROJECT="$1"
if [ -z "$PROJECT" ]; then
  read -rp "Project folder name: " PROJECT
fi
if [ -z "$PROJECT" ]; then
  echo "Project name is required. Aborting."
  exit 1
fi
if [ -e "$PROJECT" ]; then
  echo "'$PROJECT' already exists in this directory. Aborting."
  exit 1
fi

# 1. Project root
echo ""
echo "[1/4] Creating project root '$PROJECT'..."
mkdir "$PROJECT"
cd "$PROJECT"

# 2. Laravel backend
echo ""
echo "[2/4] Creating Laravel backend..."
laravel new backend --no-interaction
cd backend

# 3. Starter kit (installs + scaffolds frontend, runs npm install)
echo ""
echo "[3/4] Installing EO-ADS starter kit..."
# The starter-kit repo is public, so make any source fallback use HTTPS
# (no SSH key / GitHub token needed — avoids the credential prompt).
git config --global url."https://github.com/".insteadOf "git@github.com:" || true
# Try the normal install. Some corporate antivirus (e.g. Kaspersky) locks the
# downloaded dist zip mid-write, causing a "Permission denied" failure. If that
# happens, clear the cache and retry via source, which sidesteps the zip scan.
if ! composer require eoads/eoads-starter-kit; then
  echo ""
  echo "  Dist install failed (often a corporate antivirus file-lock). Retrying via source..."
  composer clear-cache
  composer require eoads/eoads-starter-kit --prefer-source
fi
php artisan eoads:install

# 4. Done
cd ..
echo ""
echo "========================================"
echo "  Setup complete!"
echo ""
echo "  Start backend:  cd $PROJECT/backend && php artisan serve"
echo "  Start frontend: cd $PROJECT/frontend && npm run dev"
echo ""
echo "  Or open in Claude Code: cd $PROJECT && claude ."
echo "========================================"
echo ""
