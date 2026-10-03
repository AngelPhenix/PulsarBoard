@echo off
CLS
echo ===================================================
echo   Configuration Automatique - GameAsset (Herd)
echo ===================================================

:: 1. Copie du .env si absent
if not exist .env (
    echo [1/6] Creation du fichier .env...
    copy .env.example .env
) else (
    echo [1/6] Fichier .env deja present.
)

:: 2. Installation de Composer (avec contournement du blocage)
echo [2/6] Installation des dependances PHP (Composer)...
set COMPOSER_NO_BLOCKING=1
call composer install
if %errorlevel% neq 0 (
    echo [ATTENTION] Tentative avec composer update...
    call composer update --no-interaction
    if %errorlevel% neq 0 (
        echo [ERREUR] L'installation Composer a echoue !
        goto fin
    )
)

:: 3. Generation de la clef Laravel
echo [3/6] Generation de la clef d'application...
call php artisan key:generate

:: 4. Creation de la base SQLite si absente
if not exist database\database.sqlite (
    echo [4/6] Creation de la base de donnees SQLite...
    type nul > database\database.sqlite
) else (
    echo [4/6] Base SQLite deja presente.
)

:: 5. Migrations de la base de donnees
echo [5/6] Execution des migrations et seeders...
call php artisan migrate:fresh --seed --force

:: 6. Installation et build NPM
echo [6/6] Installation et compilation des assets Front-end (NPM)...
call npm install
call npm run build

:fin
echo ===================================================
echo   Installation terminee avec succes ! Bon code !
echo ===================================================
pause