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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

class oxAutoLoader
{
    /** @var string */
    private static $sBasePath = null;

    /** @var array */
    private static $aClassDirs = null;

    /** @var array Preventing infinite loop*/
    private static $aTriedClasses = array();

    /**
     * Includes $sClass class file
     *
     * @param string $sClass classname
     *
     * @return null
     */
    public function autoload($sClass)
    {
        startProfile("oxAutoload");
        $sClass = basename($sClass);
        $sClass = strtolower($sClass);

        //loading very base classes. We can do this as we know they exists,
        //moreover even further method code could not execute without them
        $sBaseClassLocation = null;
        $aBaseClasses = array("oxutils", "oxsupercfg", "oxutilsobject");
        if (in_array($sClass, $aBaseClasses)) {
            $sFilename = getShopBasePath() . "core/" . $sClass . ".php";
            include $sFilename;

            return;
        }

        static $aClassPaths;

        if (isset($aClassPaths[$sClass])) {
            stopProfile("oxAutoload");
            include $aClassPaths[$sClass];

            return;
        }

        self::$sBasePath = getShopBasePath();


        // initializing paths
        if (self::$aClassDirs == null) {
            self::$aClassDirs = $this->getClassDirs(self::$sBasePath);
        }

        foreach (self::$aClassDirs as $sDir) {
            $sFilename = $sDir . $sClass . '.php';
            if (file_exists($sFilename)) {
                if (!isset($aClassPaths[$sClass])) {
                    $aClassPaths[$sClass] = $sFilename;
                }
                stopProfile("oxAutoload");
                include $sFilename;

                return;
            }
        }


        // Files registered by modules
        //$aModuleFiles = oxRegistry::getConfig()->getConfigParam( 'aModuleFiles' );
        $aModuleFiles = oxUtilsObject::getInstance()->getModuleVar('aModuleFiles');
        if (is_array($aModuleFiles)) {
            self::$sBasePath = getShopBasePath();
            $oModuleList = oxNew('oxModuleList');
            $aActiveModuleInfo = $oModuleList->getActiveModuleInfo();
            if (is_array($aActiveModuleInfo)) {
                foreach ($aModuleFiles as $sModuleId => $aModules) {
                    if (isset($aModules[$sClass]) && isset($aActiveModuleInfo[$sModuleId])) {
                        $sPath = $aModules[$sClass];
                        $sFilename = self::$sBasePath . 'modules/' . $sPath;
                        if (file_exists($sFilename)) {
                            if (!isset($aClassPaths[$sClass])) {
                                $aClassPaths[$sClass] = $sFilename;
                            }
                            stopProfile("oxAutoload");
                            include $sFilename;

                            return;
                        }
                    }
                }
            }
        }

        // in case module parent class (*_parent) is required
        $sClass = preg_replace('/_parent$/i', '', $sClass);

        // special case
        if (!in_array($sClass, self::$aTriedClasses) && is_array($aModules = oxUtilsObject::getInstance()->getModuleVar('aModules'))) {

            $myUtilsObject = oxUtilsObject::getInstance();
            $sClass = preg_quote ($sClass, '/');
            foreach ($aModules as $sParentName => $sModuleName) {
                // looking for module parent class
                if (preg_match('/\b' . $sClass . '($|\&)/i', $sModuleName)) {
                    $myUtilsObject->getClassName($sParentName);
                    break;
                }
                self::$aTriedClasses[] = $sClass;
            }
        }

        stopProfile("oxAutoload");
    }


    /**
     * Return array with classes paths.
     *
     * @param string $sBasePath path to shop base ddirectory.
     *
     * @return array
     */
    function getClassDirs($sBasePath)
    {
        $aClassDirs = array($sBasePath . 'core/',
            $sBasePath . 'application/components/widgets/',
            $sBasePath . 'application/components/services/',
            $sBasePath . 'application/components/',
            $sBasePath . 'application/models/',
            $sBasePath . 'application/controllers/',
            $sBasePath . 'application/controllers/admin/',
            $sBasePath . 'application/controllers/admin/reports/',
            $sBasePath . 'views/',
            $sBasePath . 'core/exception/',
            $sBasePath . 'core/interface/',
            $sBasePath . 'core/cache/',
            $sBasePath . 'core/cache/connectors/',
            $sBasePath . 'core/wysiwigpro/',
            $sBasePath . 'admin/reports/',
            $sBasePath . 'admin/',
            $sBasePath . 'modules/',
            $sBasePath
        );

        return $aClassDirs;
    }
}
