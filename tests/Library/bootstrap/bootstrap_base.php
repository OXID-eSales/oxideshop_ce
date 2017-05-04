<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 *
 * @link      http://www.oxid-esales.com
 * @package   tests
 * @copyright (c) OXID eSales AG 2003-#OXID_VERSION_YEAR#
 * @version   SVN: $Id: $
 */

if (!defined('oxPATH') || oxPATH == '') {
    die('Path to tested shop (oxPATH) is not defined');
}

require_once TEST_LIBRARY_PATH.'oxServiceCaller.php';
require_once TEST_LIBRARY_PATH.'oxFileCopier.php';

if (!is_dir(oxCCTempDir)) {
    mkdir(oxCCTempDir, 0777, 1);
} else {
    /**
     * Deletes given directory content
     *
     * @param string $dir       Path to directory.
     * @param bool   $rmBaseDir Whether to delete base directory.
     */
    function delTree($dir, $rmBaseDir = false)
    {
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? delTree("$dir/$file", true) : @unlink("$dir/$file");
        }
        if ($rmBaseDir) {
            @rmdir($dir);
        }
    }
    delTree(oxCCTempDir);
}

if (COPY_SERVICES_TO_SHOP) {
    $oFileCopier = new oxFileCopier();
    $sTarget = REMOTE_DIR ? REMOTE_DIR.'/Services' : oxPATH.'/Services';
    $oFileCopier->copyFiles(TEST_LIBRARY_PATH.'/Services', $sTarget, true);
}

if (RESTORE_SHOP_AFTER_TEST_SUITE) {
    // dumping original database
    $oServiceCaller = new oxServiceCaller();
    $oServiceCaller->setParameter('dumpDB', true);
    $oServiceCaller->setParameter('dump-prefix', 'orig_db_dump');
    try {
        $oServiceCaller->callService('ShopPreparation', 1);
    } catch (Exception $e) {
        define('RESTORE_SHOP_AFTER_TEST_SUITE_ERROR', true);
    }
}

register_shutdown_function(function () {
    if (RESTORE_SHOP_AFTER_TEST_SUITE && !defined('RESTORE_SHOP_AFTER_TEST_SUITE_ERROR')) {
        $oServiceCaller = new oxServiceCaller();
        $oServiceCaller->setParameter('restoreDB', true);
        $oServiceCaller->setParameter('dump-prefix', 'orig_db_dump');
        $oServiceCaller->callService('ShopPreparation', 1);
    }
});
