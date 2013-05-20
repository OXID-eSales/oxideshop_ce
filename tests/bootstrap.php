<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   tests
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 * @version   SVN: $Id: $
 */

if (getenv('oxPATH')) {
    define ('oxPATH', getenv('oxPATH'));
} else {
}

if (!defined('oxPATH')) {
        die('oxPATH is not defined');
}



if (!defined('OXID_VERSION_SUFIX')) {
    define('OXID_VERSION_SUFIX', '');
}


require_once 'unit/test_config.inc.php';

define('oxADMIN_LOGIN', oxDb::getDb()->getOne("select OXUSERNAME from oxuser where oxid='oxdefaultadmin'"));
if (getenv('oxADMIN_PASSWD')) {
    define('oxADMIN_PASSWD', getenv('oxADMIN_PASSWD'));
} else {
    define('oxADMIN_PASSWD', 'admin');
}


if ( getenv('CODECOVERAGE') && isset(PHPUnit_Util_Filter::$addUncoveredFilesFromWhitelist) ) {

    // PHPUnit_Util_Filter configuration
    /*PHPUnit_Util_Filter::$addUncoveredFilesFromWhitelist = true;

    PHPUnit_Util_Filter::addDirectoryToFilter(oxPATH);


    //PHPUnit_Util_Filter::addDirectoryToWhitelist(oxPATH.'/admin');
    PHPUnit_Util_Filter::addDirectoryToWhitelist(oxPATH.'/core');
    PHPUnit_Util_Filter::addDirectoryToWhitelist(oxPATH.'/application');

    //PHPUnit_Util_Filter::addDirectoryToWhitelist(oxPATH.'/views/');

   //PHPUnit_Util_Filter::removeDirectoryFromWhitelist(oxPATH.'/admin/inc/');
        PHPUnit_Util_Filter::removeDirectoryFromWhitelist(oxPATH.'/core/phpdtaus/');
        PHPUnit_Util_Filter::removeDirectoryFromWhitelist(oxPATH.'/core/jpgraph/');
        PHPUnit_Util_Filter::removeDirectoryFromWhitelist(oxPATH.'/application/controllers/admin/reports/');

    //PHPUnit_Util_Filter::removeDirectoryFromWhitelist(oxPATH.'/core/openid/');
    PHPUnit_Util_Filter::removeDirectoryFromWhitelist(oxPATH.'/core/adodblite/');
    PHPUnit_Util_Filter::removeDirectoryFromWhitelist(oxPATH.'/core/tcpdf/');
    PHPUnit_Util_Filter::removeDirectoryFromWhitelist(oxPATH.'/core/phpmailer/');
    PHPUnit_Util_Filter::removeDirectoryFromWhitelist(oxPATH.'/core/smarty/');
    PHPUnit_Util_Filter::removeDirectoryFromWhitelist(oxPATH.'/core/utils/');
    PHPUnit_Util_Filter::removeDirectoryFromWhitelist(oxPATH.'/core/facebook/');
    PHPUnit_Util_Filter::removeDirectoryFromWhitelist(oxPATH.'/application/views/');
    PHPUnit_Util_Filter::removeDirectoryFromWhitelist(oxPATH.'/out/');
    PHPUnit_Util_Filter::removeDirectoryFromWhitelist(oxPATH.'/tmp/');
    PHPUnit_Util_Filter::removeDirectoryFromWhitelist(oxPATH.'/core/objects/');

    /*PHPUnit_Util_Filter::removeFileFromWhitelist(oxPATH.'/admin/index.php');
    PHPUnit_Util_Filter::removeFileFromWhitelist(oxPATH.'/core/oxerpbase.php');
    PHPUnit_Util_Filter::removeFileFromWhitelist(oxPATH.'/core/oxerpcsv.php');
    */
    //PHPUnit_Util_Filter::removeFileFromWhitelist(oxPATH.'/core/oxopeniddb.php');
    //PHPUnit_Util_Filter::removeFileFromWhitelist(oxPATH.'/core/oxopenidhttpfetcher.php');
    //PHPUnit_Util_Filter::removeFileFromWhitelist(oxPATH.'/core/oxopenidgenericconsumer.php');


/*
    // add separate files
    PHPUnit_Util_Filter::addFileToWhitelist(oxPATH.'/core/smarty/plugins/block.oxhasrights.php');
    PHPUnit_Util_Filter::addFileToWhitelist(oxPATH.'/core/smarty/plugins/emos.php');
    PHPUnit_Util_Filter::addFileToWhitelist(oxPATH.'/core/smarty/plugins/function.oxcontent.php');
    PHPUnit_Util_Filter::addFileToWhitelist(oxPATH.'/core/smarty/plugins/function.oxgetseourl.php');
    PHPUnit_Util_Filter::addFileToWhitelist(oxPATH.'/core/smarty/plugins/function.oxid_include_dynamic.php');
    PHPUnit_Util_Filter::addFileToWhitelist(oxPATH.'/core/smarty/plugins/function.oxinputhelp.php');
    PHPUnit_Util_Filter::addFileToWhitelist(oxPATH.'/core/smarty/plugins/function.oxmultilang.php');
    PHPUnit_Util_Filter::addFileToWhitelist(oxPATH.'/core/smarty/plugins/function.oxscript.php');
    PHPUnit_Util_Filter::addFileToWhitelist(oxPATH.'/core/smarty/plugins/function.oxvariantselect.php');
    PHPUnit_Util_Filter::addFileToWhitelist(oxPATH.'/core/smarty/plugins/insert.oxid_cmplogin.php');
    PHPUnit_Util_Filter::addFileToWhitelist(oxPATH.'/core/smarty/plugins/insert.oxid_cssmanager.php');
    PHPUnit_Util_Filter::addFileToWhitelist(oxPATH.'/core/smarty/plugins/insert.oxid_newbasketitem.php');
    PHPUnit_Util_Filter::addFileToWhitelist(oxPATH.'/core/smarty/plugins/insert.oxid_nocache.php');
    PHPUnit_Util_Filter::addFileToWhitelist(oxPATH.'/core/smarty/plugins/insert.oxid_tracker.php');
    PHPUnit_Util_Filter::addFileToWhitelist(oxPATH.'/core/smarty/plugins/modifier.oxaddparams.php');
    PHPUnit_Util_Filter::addFileToWhitelist(oxPATH.'/core/smarty/plugins/modifier.oxaddslashes.php');
    PHPUnit_Util_Filter::addFileToWhitelist(oxPATH.'/core/smarty/plugins/modifier.oxenclose.php');
    PHPUnit_Util_Filter::addFileToWhitelist(oxPATH.'/core/smarty/plugins/modifier.oxformdate.php');
    PHPUnit_Util_Filter::addFileToWhitelist(oxPATH.'/core/smarty/plugins/modifier.oxlower.php');
    PHPUnit_Util_Filter::addFileToWhitelist(oxPATH.'/core/smarty/plugins/modifier.oxmultilangassign.php');
    PHPUnit_Util_Filter::addFileToWhitelist(oxPATH.'/core/smarty/plugins/modifier.oxmultilangsal.php');
    PHPUnit_Util_Filter::addFileToWhitelist(oxPATH.'/core/smarty/plugins/modifier.oxnumberformat.php');
    PHPUnit_Util_Filter::addFileToWhitelist(oxPATH.'/core/smarty/plugins/modifier.oxtruncate.php');
    PHPUnit_Util_Filter::addFileToWhitelist(oxPATH.'/core/smarty/plugins/modifier.oxupper.php');
    PHPUnit_Util_Filter::addFileToWhitelist(oxPATH.'/core/smarty/plugins/modifier.oxwordwrap.php');
    PHPUnit_Util_Filter::addFileToWhitelist(oxPATH.'/core/smarty/plugins/oxemosadapter.php');
    */
}


