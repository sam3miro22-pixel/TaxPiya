@echo off
cd /d "%~dp0"
echo Instalando dependencias npm...
call npm.cmd install --legacy-peer-deps
if errorlevel 1 exit /b 1
echo Sincronizando Android...
call npx.cmd cap sync android
if errorlevel 1 exit /b 1
echo.
echo Listo. Abre en Android Studio:
echo   %~dp0android
echo.
echo Instrucciones: %~dp0COMPILAR-APK.txt
pause
