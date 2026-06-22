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
::   create-project.bat
::
setlocal

echo.
echo ========================================
echo   EO-ADS Project Bootstrap
echo ========================================
echo.

:: 0. Project name
set /p PROJECT="Project folder name: "
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
call composer require eoads/eoads-starter-kit
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
