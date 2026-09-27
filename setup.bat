@echo off
CLS
echo ===================================================
echo   Configuration Automatique - Pulsar Board (Herd)
echo ===================================================

:: 1. Copie du .env si absent
if not exist .env (
    echo [1/6] Creation du fichier .env...
    copy .env.example .env
) else (
    echo [1/6] Fichier .env deja present.
)

:: 2. Installation de Composer
echo [2/6] Installation des dependances PHP (Composer)...
call composer install
if %errorlevel% neq 0 (
    echo.
    echo [ERREUR] L'installation Composer a echoue ! Verifie ton installation PHP/Composer.
    pause
    exit /b %errorlevel%
)

:: 3. Generation de la clef Laravel
echo [3/6] Generation de la clef d'application...
php artisan key:generate

:: 4. Creation de la base SQLite si absente
if not exist database\database.sqlite (
    echo [4/6] Creation de la base de donnees SQLite...
    type nul > database\database.sqlite
) else (
    echo [4/6] Base SQLite deja presente.
)

:: 5. Migrations de la base de donnees
echo [5/6] Execution des migrations et seeders...
php artisan migrate:fresh --seed --force

:: 6. Installation et build NPM
echo [6/6] Installation et compilation des assets Front-end (NPM)...
call npm install
call npm run build

echo ===================================================
echo   Installation terminee avec succes ! Bon code !
echo ===================================================
pause