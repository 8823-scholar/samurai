@echo off
rem 
rem samurai.bat
rem 
rem command line gateway to the samurai generator
rem 

rem set PHP command
setlocal
if "%PHP_COMMAND%" == "" (
    if exist "@PHP-BIN@" (
        set PHP_COMMAND="@PHP-BIN@"
    ) else (
        set PHP_COMMAND="php"
    )
)

rem set Samurai dir
if "%SAMURAI_DIR%" == "" (
    set SAMURAI_DIR="@PEAR-DIR@\Samurai"
)

rem execute Generator
set SAMURAI_GENERATOR="%SAMURAI_DIR%\generator\generator.php"
%PHP_COMMAND% -d html_errors=off -qC %SAMURAI_GENERATOR% %*
endlocal

