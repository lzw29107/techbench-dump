@echo off
cd /d "%~dp0"
set script=%~nx0

::If the Continue value is set to 1, even if /c is not specified, the dump will start from the existing file.
set Continue=0

if "%1"=="/c" (
    set Continue=1
    shift
)

if %Continue%==1 (
    if "%1" NEQ "" (
        set max=%1
        shift
    )
) else (
    if "%1" NEQ "" if "%2" == "" goto :Usage
    set min=%1
    set max=%2
    shift
    shift
)

if "%1"=="/f" (
    if "%2"=="" goto :Usage
    set FilePath=%2
) else (
    if "%1" NEQ "" goto :Usage
)

if %Continue%==1 (
    set parameter=-Continue
    if "%max%" NEQ "" (
        set parameter=-Continue -maxProdID %max%
    )
) else (
    if "%min%" NEQ "" if "%max%" NEQ "" (
        set parameter=-minProdID %min% -maxProdID %max%
    )
)

if "%FilePath%" NEQ "" (
    set parameter=%parameter% -Path %FilePath%
)

pwsh -version >nul
if %ERRORLEVEL% EQU 0 goto :v7

reg query HKLM\SOFTWARE\Microsoft\PowerShell\3\PowerShellEngine /v PowerShellVersion >nul
if %ERRORLEVEL% NEQ 0 (
    echo This script requires PowerShell. Please install PowerShell before running.
    pause
    exit /b
)

powershell ./tbdump_forv5.ps1 -ExecutionPolicy Bypass %Parameter%
if %ERRORLEVEL% EQU 0 echo. & pause 
exit /b

:v7
pwsh ./tbdump_forv7.ps1 -ExecutionPolicy Bypass %Parameter%
if %ERRORLEVEL% EQU 0 echo. & pause 
exit /b

:Usage
echo Usage: 
echo %script% [first_id] [last_id] [/f {Path for /f}] 
echo %script% [/c] [last_id] [/f {Path for /f}] 
echo [/c] Starting dump from existing dump file.
echo [/f] Path to dump file.
exit /b
