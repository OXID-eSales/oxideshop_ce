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

/**
 * Generates class chains for extended classes by modules.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://wiki.oxidforge.org/Tutorials/Core_OXID_eShop_classes:_must_not_be_extended
 */
class oxModuleChainsGenerator
{
    /** @var oxModuleVariablesLocator */
    private $moduleVariablesLocator;

    /**
     * @param oxModuleVariablesLocator $moduleVariablesLocator
     */
    public function __construct($moduleVariablesLocator)
    {
        $this->moduleVariablesLocator = $moduleVariablesLocator;
    }

    /**
     * Creates given class chains.
     *
     * @param string $class      Class name.
     * @param string $classAlias Class alias, used for searching module extensions.
     *
     * @return string
     */
    public function createClassChain($class, $classAlias)
    {
        $variablesLocator = $this->getModuleVariablesLocator();
        $modules = (array) $variablesLocator->getModuleVar('aModules');
        $modules = array_change_key_case($modules);

        if (array_key_exists($classAlias, $modules)) {
            $fullChain = explode("&", $modules[$classAlias]);
            $activeChain = $this->filterInactiveModuleChain($fullChain);

            if (!empty($activeChain)) {
                $class = $this->makeSafeModuleClassParents($activeChain, $class);

                // check if there is a path, if yes, remove it
                if (strpos($class, '/') !== false) {
                    $class = basename($class);
                }
            }
        }

        return $class;
    }

    /**
     * Checks if module is disabled, added to aDisabledModules config.
     *
     * @param array $aClassChain Module names
     *
     * @return array
     */
    public function filterInactiveModuleChain($aClassChain)
    {
        $variablesLocator = $this->getModuleVariablesLocator();
        $aDisabledModules = $variablesLocator->getModuleVar('aDisabledModules');
        $aModulePaths = $variablesLocator->getModuleVar('aModulePaths');

        if (is_array($aDisabledModules) && count($aDisabledModules) > 0) {
            foreach ($aDisabledModules as $sId) {
                $sPath = $sId;
                if (is_array($aModulePaths) && array_key_exists($sId, $aModulePaths)) {
                    $sPath = $aModulePaths[$sId];
                    if (!isset($sPath)) {
                        $sPath = $sId;
                    }
                }
                foreach ($aClassChain as $sKey => $sModuleClass) {
                    if (strpos($sModuleClass, $sPath . "/") === 0) {
                        unset($aClassChain[$sKey]);
                    } elseif (strpos($sPath, ".")) {
                        // If module consists of one file without own dir (getting module.php as id, instead of module)
                        if (strpos($sPath, strtolower($sModuleClass)) === 0) {
                            unset($aClassChain[$sKey]);
                        }
                    }
                }
            }
        }

        return $aClassChain;
    }

    /**
     * Creates middle classes if needed.
     *
     * @param array  $aClassChain Module names
     * @param string $sBaseModule Oxid base class
     *
     * @throws oxSystemComponentException missing system component exception
     *
     * @return string
     */
    protected function makeSafeModuleClassParents($aClassChain, $sBaseModule)
    {
        //security: just preventing string termination
        $sClassName = str_replace(chr(0), '', $sBaseModule);
        $sParent = $sClassName;

        //building middle classes if needed
        foreach ($aClassChain as $sModule) {
            //creating middle classes
            //e.g. class suboutput1_parent extends oxoutput {}
            //     class suboutput2_parent extends suboutput1 {}
            //$sModuleClass = $this->getClassName($sModule);

            //security: just preventing string termination
            $sModule = str_replace(chr(0), '', $sModule);

            //get parent and module class names from sub/suboutput2
            $sModuleClass = basename($sModule);

            if (!class_exists($sModuleClass, false)) {
                $sParentClass = basename($sParent);
                $sModuleParentClass = $sModuleClass . "_parent";

                //initializing middle class
                if (!class_exists($sModuleParentClass, false)) {
                    // If possible using alias instead if eval (since php 5.3).
                    if (function_exists('class_alias')) {
                        class_alias($sParentClass, $sModuleParentClass);
                    } else {
                        eval("abstract class $sModuleParentClass extends $sParentClass {}");
                    }
                }
                $sParentPath = oxRegistry::get("oxConfigFile")->getVar("sShopDir") . "/modules/" . $sModule . ".php";

                //including original file
                if (file_exists($sParentPath)) {
                    include_once $sParentPath;
                } elseif (!class_exists($sModuleClass)) {
                    // special case is when oxconfig class is extended: we cant call "_disableModule" as it requires valid config object
                    // but we can't create it as module class extending it does not exist. So we will use original oxConfig object instead.
                    if ($sParentClass == "oxconfig") {
                        $oConfig = new oxConfig();
                        oxRegistry::set("oxConfig", $oConfig);
                    }

                    // disable module if extended class is not found
                    $blDisableModuleOnError = !oxRegistry::get("oxConfigFile")->getVar("blDoNotDisableModuleOnError");
                    if ($blDisableModuleOnError) {
                        $this->disableModule($sModule);
                    } else {
                        //to avoid problems with unitest and only throw a exception if class does not exists MAFI
                        /** @var oxSystemComponentException $oEx */
                        $oEx = oxNew("oxSystemComponentException");
                        $oEx->setMessage("EXCEPTION_SYSTEMCOMPONENT_CLASSNOTFOUND");
                        $oEx->setComponent($sModuleClass);
                        throw $oEx;
                    }
                    continue;
                }
            }
            $sParent = $sModule;
            $sClassName = $sModule;
        }

        //returning the last module from the chain
        return $sClassName;
    }

    /**
     * Disables module, adds to aDisabledModules config.
     *
     * @param array $sModule Module name
     */
    public function disableModule($sModule)
    {
        /** @var oxModule $oModule */
        $oModule = oxNew("oxModule");
        $sModuleId = $oModule->getIdByPath($sModule);
        $oModule->load($sModuleId);

        /** @var oxModuleCache $oModuleCache */
        $oModuleCache = oxNew('oxModuleCache', $oModule);
        /** @var oxModuleInstaller $oModuleInstaller */
        $oModuleInstaller = oxNew('oxModuleInstaller', $oModuleCache);

        $oModuleInstaller->deactivate($oModule);
    }

    /**
     * @return oxModuleVariablesLocator
     */
    public function getModuleVariablesLocator()
    {
        return $this->moduleVariablesLocator;
    }
}
