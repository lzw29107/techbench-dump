@echo off
if [%1] NEQ [] if [%2]==[] echo Usage: %~nx0 [first_id] [last_id] & exit /b

if [%PROCESSOR_ARCHITECTURE%] == [AMD64] (
set binDir=bin\x64
) ELSE (
set binDir=bin
)

set "PATH=%PATH%;%~dp0%binDir%"
set "WIN_WRAPPED=1"

busybox ash -c "./tbdump.sh md %*"
if %ERRORLEVEL% EQU 0 echo. & pause 
exit /b
