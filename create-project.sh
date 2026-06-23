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
composer require eoads/eoads-starter-kit
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
