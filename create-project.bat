@echo off
::
:: EO-ADS Starter Kit -- one-command project bootstrap (Windows).
::
:: Creates a brand-new project from scratch:
::   project-root\
::   |-- backend\   <- fresh Laravel + eoads starter kit
::   \-- frontend\  <- scaffolded automatically by eoads:install
::
:: Usage:
::   create-project.bat <project-name>   (one command, no prompts)
::   create-project.bat                   (will prompt for the name)
::
setlocal

echo.
echo ========================================
echo   EO-ADS Project Bootstrap
echo ========================================
echo.

:: 0. Project name (from argument, or prompt if not given)
set "PROJECT=%~1"
if "%PROJECT%"=="" set /p PROJECT="Project folder name: "
if "%PROJECT%"=="" (
  echo Project name is required. Aborting.
  exit /b 1
)
if exist "%PROJECT%" (
  echo '%PROJECT%' already exists in this directory. Aborting.
  exit /b 1
)

:: 1. Project root
echo.
echo [1/4] Creating project root '%PROJECT%'...
mkdir "%PROJECT%"
cd "%PROJECT%"

:: 2. Laravel backend
echo.
echo [2/4] Creating Laravel backend...
call laravel new backend --no-interaction
if errorlevel 1 ( echo Laravel install failed. Aborting. & exit /b 1 )
cd backend

:: 3. Starter kit (installs + scaffolds frontend, runs npm install)
echo.
echo [3/4] Installing EO-ADS starter kit...
:: The starter-kit repo is public, so make any source fallback use HTTPS
:: (no SSH key / GitHub token needed -- avoids the credential prompt).
call git config --global url."https://github.com/".insteadOf "git@github.com:"
:: Try the normal install. Some corporate antivirus (e.g. Kaspersky) locks the
:: downloaded dist zip mid-write, causing a "Permission denied" failure. If that
:: happens, clear the cache and retry via source, which sidesteps the zip scan.
call composer require eoads/eoads-starter-kit
if errorlevel 1 (
  echo.
  echo   Dist install failed ^(often a corporate antivirus file-lock^). Retrying via source...
  call composer clear-cache
  call composer require eoads/eoads-starter-kit --prefer-source
)
if errorlevel 1 ( echo composer require failed. Aborting. & exit /b 1 )
call php artisan eoads:install

:: 4. Done
cd ..
echo.
echo ========================================
echo   Setup complete!
echo.
echo   Start backend:  cd %PROJECT%\backend ^&^& php artisan serve
echo   Start frontend: cd %PROJECT%\frontend ^&^& npm run dev
echo.
echo   Or open in Claude Code: cd %PROJECT% ^&^& claude .
echo ========================================
echo.

endlocal
