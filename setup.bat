@echo off
echo ============================================
echo   MobiCash - Installation
echo ============================================
echo.

REM Chercher php.exe dans C:\wamp64\bin\php
set "PHP_EXE="
for /d %%D in (C:\wamp64\bin\php\php*) do (
    if exist "%%D\php.exe" (
        set "PHP_EXE=%%D\php.exe"
        goto :found
    )
)

REM Si pas trouvé, chercher dans le PATH
where php >nul 2>&1
if %ERRORLEVEL%==0 (
    set "PHP_EXE=php"
    goto :found
)

echo ERREUR : php.exe introuvable.
echo Verifiez que Wamp64 est installe dans C:\wamp64
echo ou que php est dans votre PATH.
pause
exit /b 1

:found
echo PHP trouve : %PHP_EXE%
echo.

REM Creer writable/database si absent
if not exist "writable\database" (
    mkdir writable\database
    echo Repertoire writable\database cree.
)

REM Lancer les migrations
echo.
echo --- Migration de la base de données ---
"%PHP_EXE%" spark migrate
if %ERRORLEVEL% neq 0 (
    echo ERREUR lors des migrations !
    pause
    exit /b 1
)

REM Lancer le seeder
echo.
echo --- Insertion des données initiales ---
"%PHP_EXE%" spark db:seed AppelToutes
if %ERRORLEVEL% neq 0 (
    echo ERREUR lors du seeding !
    pause
    exit /b 1
)

echo.
echo ============================================
echo   Installation terminee avec succes !
echo ============================================
echo.
echo Identifiants :
echo   Operateur : admin@mobile.mg / admin123
echo   Client    : 0331234567 ou 0379876543
echo.
echo Lancez start.bat pour demarrer le serveur.
echo.
pause
