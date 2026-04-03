@echo off
setlocal
set PORT=8000
set HOST=localhost
php -S %HOST%:%PORT% -t public
