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
 * View utility class
 */
class oxUtilsView extends oxSuperCfg
{

    /**
     * Template processor object (smarty)
     *
     * @var Smarty
     */
    protected static $_oSmarty = null;

    /**
     * Templates directories array
     *
     * @var array
     */
    protected $_aTemplateDir = array();

    /**
     * Templates directories array
     *
     * @var array
     */
    protected $_blIsTplBlocks = null;

    /**
     * Active module Ids array
     *
     * @var array
     */
    protected $_aActiveModuleInfo = null;

    /**
     * returns existing or creates smarty object
     * Returns smarty object. If object not yet initiated - creates it. Sets such
     * default parameters, like cache lifetime, cache/templates directory, etc.
     *
     * @param bool $blReload set true to force smarty reload
     *
     * @return smarty
     */
    public function getSmarty($blReload = false)
    {
        if (!self::$_oSmarty || $blReload) {
            self::$_oSmarty = new Smarty();
            $this->_fillCommonSmartyProperties(self::$_oSmarty);
            $this->_smartyCompileCheck(self::$_oSmarty);
        }

        return self::$_oSmarty;
    }

    /**
     * Returns rendered template output. According to debug configuration outputs
     * debug information.
     *
     * @param string $templateName template file name
     * @param object $oObject      object, witch template we wish to output
     *
     * @return string
     */
    public function getTemplateOutput($templateName, $oObject)
    {
        $smarty = $this->getSmarty();
        $debugMode = $this->getConfig()->getConfigParam('iDebug');

        // assign
        $viewData = $oObject->getViewData();
        if (is_array($viewData)) {
            foreach (array_keys($viewData) as $viewName) {
                // show debug information
                if ($debugMode == 4) {
                    echo("TemplateData[$viewName] : \n");
                    var_export($viewData[$viewName]);
                }
                $smarty->assign_by_ref($viewName, $viewData[$viewName]);
            }
        }

        return $smarty->fetch($templateName);
    }

    /**
     * adds the given errors to the view array
     *
     * @param array &$aView view data array
     * @param array $errors array of errors to pass to view
     */
    public function passAllErrorsToView(&$aView, $errors)
    {
        if (count($errors) > 0) {
            foreach ($errors as $sLocation => $aEx2) {
                foreach ($aEx2 as $sKey => $oEr) {
                    $aView['Errors'][$sLocation][$sKey] = unserialize($oEr);
                }
            }
        }
    }

    /**
     * adds a exception to the array of displayed exceptions for the view
     * by default is displayed in the inc_header, but with the custom destination set to true
     * the exception won't be displayed by default but can be displayed where ever wanted in the tpl
     *
     * @param exception $oEr                  a exception object or just a language local (string) which will be converted into a oxExceptionToDisplay object
     * @param bool      $blFull               if true the whole object is add to display (default false)
     * @param bool      $useCustomDestination true if the exception shouldn't be displayed at the default position (default false)
     * @param string    $customDestination    defines a name of the view variable containing the messages, overrides Parameter 'CustomError' ("default")
     * @param string    $activeController     defines a name of the controller, which should handle the error.
     */
    public function addErrorToDisplay($oEr, $blFull = false, $useCustomDestination = false, $customDestination = "", $activeController = "")
    {
        if ($useCustomDestination && (oxRegistry::getConfig()->getRequestParameter('CustomError') || $customDestination != '')) {
            // check if the current request wants do display exceptions on its own
            $destination = oxRegistry::getConfig()->getRequestParameter('CustomError');
            if ($customDestination != '') {
                $destination = $customDestination;
            }
        } else {
            //default
            $destination = 'default';
        }

        //starting session if not yet started as all exception
        //messages are stored in session
        $session = $this->getSession();
        if (!$session->getId() && !$session->isHeaderSent()) {
            $session->setForceNewSession();
            $session->start();
        }

        $aEx = oxRegistry::getSession()->getVariable('Errors');
        if ($oEr instanceof oxException) {
            $oEx = oxNew('oxExceptionToDisplay');
            $oEx->setMessage($oEr->getMessage());
            $oEx->setExceptionType(get_class($oEr));

            if ($oEr instanceof oxSystemComponentException) {
                $oEx->setMessageArgs($oEr->getComponent());
            }

            $oEx->setValues($oEr->getValues());
            $oEx->setStackTrace($oEr->getTraceAsString());
            $oEx->setDebug($blFull);
            $oEr = $oEx;
        } elseif ($oEr && !($oEr instanceof oxIDisplayError)) {
            // assuming that a string was given
            $sTmp = $oEr;
            $oEr = oxNew('oxDisplayError');
            $oEr->setMessage($sTmp);
        } elseif ($oEr instanceof oxIDisplayError) {
            // take the object
        } else {
            $oEr = null;
        }

        if ($oEr) {
            $aEx[$destination][] = serialize($oEr);
            oxRegistry::getSession()->setVariable('Errors', $aEx);

            if ($activeController == '') {
                $activeController = oxRegistry::getConfig()->getRequestParameter('actcontrol');
            }
            if ($activeController) {
                $aControllerErrors[$destination] = $activeController;
                oxRegistry::getSession()->setVariable('ErrorController', $aControllerErrors);
            }
        }
    }

    /**
     * Runs long description through smarty. If you pass array of data
     * to process, array will be returned, if you pass string - string
     * will be passed as result
     *
     * @param mixed  $sDesc       description or array of descriptions ( array( [] => array( _ident_, _value_to_process_ ) ) )
     * @param string $sOxid       current object id
     * @param oxview $oActView    view data to use its view data (optional)
     * @param bool   $blRecompile force to recompile if found in cache
     *
     * @return mixed
     */
    public function parseThroughSmarty($sDesc, $sOxid = null, $oActView = null, $blRecompile = false)
    {
        if (oxRegistry::getConfig()->isDemoShop()) {
            return $sDesc;
        }

        startProfile("parseThroughSmarty");

        if (!is_array($sDesc) && strpos($sDesc, "[{") === false) {
            stopProfile("parseThroughSmarty");

            return $sDesc;
        }

        $activeLanguageId = oxRegistry::getLang()->getTplLanguage();

        // now parse it through smarty
        $smarty = clone $this->getSmarty();

        // save old tpl data
        $tplVars = $smarty->_tpl_vars;
        $forceRecompile = $smarty->force_compile;

        $smarty->force_compile = $blRecompile;

        if (!$oActView) {
            $oActView = oxNew('oxUBase');
            $oActView->addGlobalParams();
        }

        $viewData = $oActView->getViewData();
        foreach (array_keys($viewData) as $name) {
            $smarty->assign_by_ref($name, $viewData[$name]);
        }

        if (is_array($sDesc)) {
            foreach ($sDesc as $name => $aData) {
                $smarty->oxidcache = new oxField($aData[1], oxField::T_RAW);
                $result[$name] = $smarty->fetch("ox:" . $aData[0] . $activeLanguageId);
            }
        } else {
            $smarty->oxidcache = new oxField($sDesc, oxField::T_RAW);
            $result = $smarty->fetch("ox:{$sOxid}{$activeLanguageId}");
        }

        // restore tpl vars for continuing smarty processing if it is in one
        $smarty->_tpl_vars = $tplVars;
        $smarty->force_compile = $forceRecompile;

        stopProfile("parseThroughSmarty");

        return $result;
    }

    /**
     * Templates directory setter
     *
     * @param string $templatesDirectory templates path
     */
    public function setTemplateDir($templatesDirectory)
    {
        if ($templatesDirectory && !in_array($templatesDirectory, $this->_aTemplateDir)) {
            $this->_aTemplateDir[] = $templatesDirectory;
        }
    }

    /**
     * Initializes and returns templates directory info array
     *
     * @return array
     */
    public function getTemplateDirs()
    {
        $config = oxRegistry::getConfig();

        // buffer for CE (main) edition templates
        $mainTemplatesDirectory = $config->getTemplateDir($this->isAdmin());

        // main templates directory has not much priority anymore
        $this->setTemplateDir($mainTemplatesDirectory);

        // out directory can have templates too
        if (!$this->isAdmin()) {
            $this->setTemplateDir($this->addActiveThemeId($config->getOutDir(true)));
        }

        return $this->_aTemplateDir;
    }

    /**
     * Get template compile id.
     *
     * @return string
     */
    public function getTemplateCompileId()
    {
        $shopId = $this->getConfig()->getShopId();
        $templateDirectories = $this->getTemplateDirs();
        $templateDirectory = reset($templateDirectories);

        return md5($templateDirectory . '__' . $shopId);
    }

    /**
     * Returns a full path to Smarty compile dir
     *
     * @return string
     */
    public function getSmartyDir()
    {
        $config = $this->getConfig();

        //check for the Smarty dir
        $compileDir = $config->getConfigParam('sCompileDir');
        $smartyDir = $compileDir . "/smarty/";
        if (!is_dir($smartyDir)) {
            @mkdir($smartyDir);
        }

        if (!is_writable($smartyDir)) {
            $smartyDir = $compileDir;
        }

        return $smartyDir;
    }

    /**
     * sets properties of smarty object
     *
     * @param Smarty $smarty template processor object (smarty)
     */
    protected function _fillCommonSmartyProperties($smarty)
    {
        $config = $this->getConfig();
        $smarty->left_delimiter = '[{';
        $smarty->right_delimiter = '}]';

        $smarty->register_resource(
            'ox',
            array(
                'ox_get_template',
                'ox_get_timestamp',
                'ox_get_secure',
                'ox_get_trusted'
            )
        );

        $smartyDir = $this->getSmartyDir();

        $smarty->caching = false;
        $smarty->compile_dir = $smartyDir;
        $smarty->cache_dir = $smartyDir;
        $smarty->template_dir = $this->getTemplateDirs();
        $smarty->compile_id = $this->getTemplateCompileId();
        $smarty->default_template_handler_func = array(oxRegistry::get("oxUtilsView"), '_smartyDefaultTemplateHandler');
        array_unshift($smarty->plugins_dir, $config->getConfigParam('sShopDir') . 'Core/smarty/plugins');

        include_once dirname(__FILE__) . '/smarty/plugins/prefilter.oxblock.php';
        $smarty->register_prefilter('smarty_prefilter_oxblock');

        $debugMode = $config->getConfigParam('iDebug');
        if ($debugMode == 1 || $debugMode == 3 || $debugMode == 4) {
            $smarty->debugging = true;
        }

        if ($debugMode == 8 && !$config->isAdmin()) {
            include_once getShopBasePath() . 'Core/smarty/plugins/prefilter.oxtpldebug.php';
            $smarty->register_prefilter('smarty_prefilter_oxtpldebug');
        }

        //demo shop security
        if (!$config->isDemoShop()) {
            $smarty->php_handling = (int) $config->getConfigParam('iSmartyPhpHandling');
            $smarty->security = false;
        } else {
            $smarty->php_handling = SMARTY_PHP_REMOVE;
            $smarty->security = true;
            $smarty->security_settings['IF_FUNCS'][] = 'XML_ELEMENT_NODE';
            $smarty->security_settings['IF_FUNCS'][] = 'is_int';
            $smarty->security_settings['MODIFIER_FUNCS'][] = 'round';
            $smarty->security_settings['MODIFIER_FUNCS'][] = 'floor';
            $smarty->security_settings['MODIFIER_FUNCS'][] = 'trim';
            $smarty->security_settings['MODIFIER_FUNCS'][] = 'implode';
            $smarty->security_settings['MODIFIER_FUNCS'][] = 'is_array';
            $smarty->security_settings['ALLOW_CONSTANTS'] = true;
            $smarty->secure_dir = $smarty->template_dir;
        }
    }

    /**
     * Sets compile check property to smarty object.
     *
     * @param object $smarty template processor object (smarty)
     */
    protected function _smartyCompileCheck($smarty)
    {
        $config = $this->getConfig();
        $smarty->compile_check = $config->getConfigParam('blCheckTemplates');
    }

    /**
     * is called when a template cannot be obtained from its resource.
     *
     * @param string $resourceType       template type
     * @param string $resourceName       template file name
     * @param string &$resourceContent   template file content
     * @param int    &$resourceTimestamp template file timestamp
     * @param object $smarty             template processor object (smarty)
     *
     * @return bool
     */
    public function _smartyDefaultTemplateHandler($resourceType, $resourceName, &$resourceContent, &$resourceTimestamp, $smarty)
    {
        $config = oxRegistry::getConfig();
        if ($resourceType == 'file' && !is_readable($resourceName)) {
            $resourceName = $config->getTemplatePath($resourceName, $config->isAdmin());
            $resourceContent = $smarty->_read_file($resourceName);
            $resourceTimestamp = filemtime($resourceName);

            return is_file($resourceName) && is_readable($resourceName);
        }

        return false;
    }

    /**
     * retrieve module block contents
     *
     * @param string $moduleName module name
     * @param string $fileName   module block file name without .tpl ending
     *
     * @see getTemplateBlocks
     * @throws oxException if block is not found
     *
     * @return string
     */
    protected function _getTemplateBlock($moduleName, $fileName)
    {
        $moduleInfo = $this->_getActiveModuleInfo();
        $modulePath = $moduleInfo[$moduleName];
        // for 4.5 modules, since 4.6 insert in oxtplblocks the full file name
        if (substr($fileName, -4) != '.tpl') {
            $fileName = $fileName . ".tpl";
        }
        // for < 4.6 modules, since 4.7/5.0 insert in oxtplblocks the full file name and path
        if (basename($fileName) == $fileName) {
            $fileName = "out/blocks/$fileName";
        }
        $filePath = $this->getConfig()->getConfigParam('sShopDir') . "/modules/$modulePath/$fileName";
        if (file_exists($filePath) && is_readable($filePath)) {
            return file_get_contents($filePath);
        } else {
            /** @var oxException $oException */
            $oException = oxNew("oxException", "Template block file ($filePath) not found for '$moduleName' module.");
            throw $oException;
        }
    }

    /**
     * template blocks getter: retrieve sorted blocks for overriding in templates
     *
     * @param string $fileName filename of rendered template
     *
     * @see smarty_prefilter_oxblock
     *
     * @return array
     */
    public function getTemplateBlocks($fileName)
    {
        $config = $this->getConfig();

        $tplDir = trim($config->getConfigParam('_sTemplateDir'), '/\\');
        $fileName = str_replace(array('\\', '//'), '/', $fileName);
        if (preg_match('@/' . preg_quote($tplDir, '@') . '/(.*)$@', $fileName, $m)) {
            $fileName = $m[1];
        }

        $db = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);
        $fileParam = $db->quote($fileName);
        $shpIdParam = $db->quote($config->getShopId());
        $ret = array();

        if ($this->_blIsTplBlocks === null) {
            $this->_blIsTplBlocks = false;
            $ids = $this->_getActiveModuleInfo();
            if (count($ids)) {
                $sSql = "select COUNT(*) from oxtplblocks where oxactive=1 and oxshopid=$shpIdParam and oxmodule in ( " . implode(", ", oxDb::getInstance()->quoteArray(array_keys($ids))) . " ) ";
                $rs = $db->getOne($sSql);
                if ($rs) {
                    $this->_blIsTplBlocks = true;
                }
            }
        }

        if ($this->_blIsTplBlocks) {
            $ids = $this->_getActiveModuleInfo();
            if (count($ids)) {
                $sSql = "select * from oxtplblocks where oxactive=1 and oxshopid=$shpIdParam and oxtemplate=$fileParam and oxmodule in ( " . implode(", ", oxDb::getInstance()->quoteArray(array_keys($ids))) . " ) order by oxpos asc";
                $db->setFetchMode(oxDb::FETCH_MODE_ASSOC);
                $rs = $db->select($sSql);

                if ($rs != false && $rs->recordCount() > 0) {
                    while (!$rs->EOF) {
                        try {
                            if (!is_array($ret[$rs->fields['OXBLOCKNAME']])) {
                                $ret[$rs->fields['OXBLOCKNAME']] = array();
                            }
                            $ret[$rs->fields['OXBLOCKNAME']][] = $this->_getTemplateBlock($rs->fields['OXMODULE'], $rs->fields['OXFILE']);
                        } catch (oxException $exception) {
                            $exception->debugOut();
                        }
                        $rs->moveNext();
                    }
                }
            }
        }

        return $ret;
    }

    /**
     * Returns active module Ids
     *
     * @return array
     */
    protected function _getActiveModuleInfo()
    {
        if ($this->_aActiveModuleInfo === null) {
            $modulelist = oxNew('oxmodulelist');
            $this->_aActiveModuleInfo = $modulelist->getActiveModuleInfo();
        }

        return $this->_aActiveModuleInfo;
    }

    /**
     * Add active theme at the end of theme path to form full path to templates.
     *
     * @param string $themePath
     *
     * @return string
     */
    protected function addActiveThemeId($themePath)
    {
        $themeId = $this->getConfig()->getConfigParam('sTheme');
        if ($this->isAdmin()) {
            $themeId = 'admin';
        }

        $fullThemePath = $themePath . $themeId . "/tpl/";

        return $fullThemePath;
    }
}
