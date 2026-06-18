#!/bin/bash

echo ""
echo "========================================"
echo "  EO-ADS Project Setup"
echo "========================================"
echo ""

# 1. Backend
echo "[1/4] Setting up Laravel backend..."
cd backend
composer install
cp .env.example .env
php artisan key:generate
echo ""

# 2. Install starter kit
echo "[2/4] Installing EO-ADS starter kit..."
composer require eoads/eoads-starter-kit
php artisan eoads:install
echo ""

# 3. Frontend deps (already run by eoads:install, but just in case)
echo "[3/4] Installing frontend dependencies..."
cd ../frontend
if [ -f "package.json" ]; then
  npm install
fi
echo ""

# 4. Done
cd ..
echo "========================================"
echo "  Setup complete!"
echo ""
echo "  Start backend:  cd backend && php artisan serve"
echo "  Start frontend: cd frontend && npm run dev"
echo ""
echo "  Or open in Claude Code: claude ."
echo "========================================"
echo ""
