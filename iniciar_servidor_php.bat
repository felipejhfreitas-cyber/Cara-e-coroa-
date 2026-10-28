@echo off
title Servidor PHP - Cara e Coroa
chcp 65001 > nul
echo ===================================================
echo     Iniciando Servidor PHP Local para Cara e Coroa
echo ===================================================
echo.

where php >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [AVISO] PHP nao foi encontrado no PATH do sistema.
    echo.
    echo Se voce possui o XAMPP instalado, copie esta pasta para:
    echo C:\xampp\htdocs\cara_e_coroa\
    echo e acesse pelo navegador em: http://localhost/cara_e_coroa/
    echo.
    echo Abrindo a versao web direta (index.html)...
    start "" "index.html"
    pause
    exit /b
)

echo Servidor ativo em: http://localhost:8080
echo Pressione CTRL+C para encerrar o servidor.
start "" "http://localhost:8080/index.php"
php -S localhost:8080
pause
