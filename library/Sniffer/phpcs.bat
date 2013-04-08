@echo off
REM PHP_CodeSniffer tokenises PHP code and detects violations of a
REM defined set of coding standards.
REM
REM PHP version 5
REM
REM @category  PHP
REM @package   PHP_CodeSniffer
REM @author    Greg Sherwood <gsherwood@squiz.net>
REM @author    Marc McIntyre <mmcintyre@squiz.net>
REM @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
REM @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
REM @version   CVS: $Id: phpcs.bat,v 1.3 2007-11-04 22:02:16 squiz Exp $
REM @link      http://pear.php.net/package/PHP_CodeSniffer

REM php.exe -d display_errors=0 -d include_path="C:\htdocs\oxideshop\eshop\trunk\eshop\library\Sniffer" -f "C:\htdocs\oxideshop\eshop\trunk\eshop\library\Sniffer\phpcs" -- -n --extensions=php --ignore="*/dtaus/*,*/ERP/*,*/wysiwigpro/*,*/smarty/*,*/adodblite/*,*/facebook/*,*/ccval/*,*/tcpdf/*,*/phpmailer/*,*/emailvalidation/*,*/openid/*,*/jpgraph/*" --report=xml --standard=Oxid --tab-width=4 "C:\htdocs\oxideshop\eshop\trunk\eshop\source\%*" > "C:\htdocs\oxideshop\eshop\trunk\eshop\library\Sniffer\result.xml"
php -d include_path=".;C:\htdocs\oxideshop\eshop\trunk\eshop\library\Sniffer" -f phpcs -- %*