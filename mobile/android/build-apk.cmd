@echo off
setlocal EnableDelayedExpansion

cd /d "%~dp0"

echo [TaxPiya] Buscando JDK para Gradle...

set "JAVA_HOME="
for %%P in (
  "%LOCALAPPDATA%\Programs\Android Studio\jbr"
  "%ProgramFiles%\Android\Android Studio\jbr"
  "%ProgramFiles%\JetBrains\Android Studio\jbr"
  "%ProgramFiles%\Microsoft\jdk-21*"
  "%ProgramFiles%\Eclipse Adoptium\jdk-21*"
) do (
  if exist "%%~P\bin\java.exe" (
    set "JAVA_HOME=%%~P"
    goto :found
  )
)

echo ERROR: No se encontro JDK 17/21.
echo Instala Android Studio o Microsoft OpenJDK 21.
echo Luego en Android Studio: File - Settings - Gradle - Gradle JDK = jbr-21
exit /b 1

:found
echo [TaxPiya] JAVA_HOME=!JAVA_HOME!
set "PATH=!JAVA_HOME!\bin;%PATH%"

if not exist "..\node_modules\@capacitor\android\capacitor" (
  echo [TaxPiya] Capacitor no instalado. Ejecutando npm + cap sync...
  cd ..
  call npm.cmd install --legacy-peer-deps
  if errorlevel 1 exit /b 1
  call npx.cmd cap sync android
  if errorlevel 1 exit /b 1
  cd android
)

echo [TaxPiya] Compilando APK debug...
call gradlew.bat assembleDebug --no-daemon
if errorlevel 1 (
  echo.
  echo BUILD FALLIDO. Abre mobile\android en Android Studio y usa Build APK.
  exit /b 1
)

echo.
echo OK: mobile\android\app\build\outputs\apk\debug\app-debug.apk
exit /b 0
