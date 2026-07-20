@echo off
echo ============================================
echo   MobiCash - Demarrage du serveur
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
pause
exit /b 1

:found
echo PHP trouve : %PHP_EXE%
echo.
echo Demarrage du serveur sur http://localhost:8081
echo Appuyez sur Ctrl+C pour arreter.
echo.
"%PHP_EXE%" spark serve
