@echo off
REM START SELENIUM RC
start java -jar C:/htdocs/oxideshop/eshop/library/Testing/selenium-server.jar -multiWindow

set PHPBIN=php.exe
"php.exe" -d memory_limit=512M -d include_path=".;C:/htdocs/oxideshop/eshop/library/" "C:/htdocs/oxideshop/eshop/library/PHPUnit/phpunit.php" --dbreset=TF21 %1 %2 %3 %4