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
namespace OxidEsales\Core;

use oxConnectionException;
use oxCookieException;
use oxDb;
use oxException;
use OxidEsales\Enterprise\Core\Cache\DynamicContent\ContentCache;
use oxOutput;
use oxRegistry;
use oxSystemComponentException;
use oxUBase;
use ReflectionMethod;

/**
 * Main shop actions controller. Processes user actions, logs
 * them (if needed), controls output, redirects according to
 * processed methods logic. This class is initialized from index.php
 */
class ShopControl extends \oxSuperCfg
{
    /**
     * Used to force handling, it allows other place like widget controller to skip it.
     *
     * @var bool
     */
    protected $_blHandlerSet = null;

    /**
     * Used to force handling, it allows other place like widget controller to skip it.
     *
     * @var bool
     */
    protected $_blMainTasksExecuted = null;

    /**
     * Profiler start time
     *
     * @var double
     */
    protected $_dTimeStart = null;

    /**
     * Profiler end time
     *
     * @var double
     */
    protected $_dTimeEnd = null;

    /**
     * errors to be displayed/returned
     *
     * @see _getErrors
     *
     * @var array
     */
    protected $_aErrors = null;

    /**
     * same as errors in session
     *
     * @see _getErrors
     *
     * @var array
     */
    protected $_aAllErrors = null;

    /**
     * same as controller errors in session
     *
     * @see _getErrors
     *
     * @var array
     */
    protected $_aControllerErrors = null;


    /**
     * output handler object
     *
     * @see _getOuput
     *
     * @var oxOutput
     */
    protected $_oOutput = null;

    /**
     * Cache manager instance
     *
     * @var ContentCache
     */
    protected $_oCache = null;

    /**
     * Main shop manager, that sets shop status, executes configuration methods.
     * Executes oxShopControl::_runOnce(), if needed sets default class (according
     * to admin or regular activities). Additionally its possible to pass class name,
     * function name and parameters array to view, which will be executed.
     *
     * @param string $class      Class name
     * @param string $function   Function name
     * @param array  $parameters Parameters array
     * @param array  $viewsChain Array of views names that should be initialized also
     */
    public function start($class = null, $function = null, $parameters = null, $viewsChain = null)
    {
        //sets default exception handler
        $this->_setDefaultExceptionHandler();

        try {
            $this->_runOnce();

            $function = !is_null($function) ? $function : oxRegistry::getConfig()->getRequestParameter('fnc');
            $class = !is_null($class) ? $class : $this->_getStartController();

            $this->_process($class, $function, $parameters, $viewsChain);
        } catch (oxSystemComponentException $ex) {
            $this->_handleSystemException($ex);
        } catch (oxCookieException $ex) {
            $this->_handleCookieException($ex);
        }
        catch (oxConnectionException $ex) {
            $this->_handleDbConnectionException($ex);
        } catch (oxException $ex) {
            $this->_handleBaseException($ex);
        }
    }

    /**
     * Sets default exception handler.
     * Ideally all exceptions should be handled with try catch and default exception should never be reached.
     *
     * @return null
     */
    protected function _setDefaultExceptionHandler()
    {
        if (isset($this->_blHandlerSet)) {
            return;
        }

        set_exception_handler(array(oxNew('oxexceptionhandler', $this->_isDebugMode()), 'handleUncaughtException'));
    }

    /**
     * Logs user performed actions to DB. Skips action logging if
     * it's search engine.
     *
     * @param string $sClass Name of class
     * @param string $fnc   Name of executed class method
     */
    protected function _log($sClass, $fnc)
    {
        $db = oxDb::getDb();
        $oConfig = $this->getConfig();
        $session = $this->getSession();

        $shopID = $session->getVariable('actshop');
        $time = date('Y-m-d H:i:s');
        $sidQuoted = $db->quote($session->getId());
        $userIDQuoted = $db->quote($session->getVariable('usr'));

        $cnid = $oConfig->getRequestParameter('cnid');
        $anid = $oConfig->getRequestParameter('aid') ? $oConfig->getRequestParameter('aid') : $oConfig->getRequestParameter('anid');

        $parameter = '';

        if ($sClass == 'content') {
            $parameter = str_replace('.tpl', '', $oConfig->getRequestParameter('tpl'));
        } elseif ($sClass == 'search') {
            $parameter = $oConfig->getRequestParameter('searchparam');
        }

        $fncQuoted = $db->quote($fnc);
        $classQuoted = $db->quote($sClass);
        $parameterQuoted = $db->quote($parameter);

        $sQ = "insert into oxlogs (oxtime, oxshopid, oxuserid, oxsessid, oxclass, oxfnc, oxcnid, oxanid, oxparameter) " .
              "values( '$time', '$shopID', $userIDQuoted, $sidQuoted, $classQuoted, $fncQuoted, " . $db->quote($cnid) . ", " . $db->quote($anid) . ", $parameterQuoted )";

        $db->execute($sQ);
    }

    // OXID : add timing
    /**
     * Starts resource monitor
     */
    protected function _startMonitor()
    {
        if ($this->_isDebugMode()) {
            $this->_dTimeStart = microtime(true);
        }
    }

    /**
     * Stops resource monitor, summarizes and outputs values
     *
     * @param bool   $isCallForCache Is content cache
     * @param bool   $isCached       Is content cached
     * @param string $viewId         View ID
     * @param array  $viewData       View data
     */
    protected function _stopMonitor($isCallForCache = false, $isCached = false, $viewId = null, $viewData = array())
    {
        $this->stopMonitoring($viewId, $viewData);
    }

    /**
     * Stops resource monitor, summarizes and outputs values
     *
     * @param string $viewId   View Id
     * @param array  $viewData View data
     */
    protected function stopMonitoring($viewId = null, $viewData = array())
    {
        if ($this->_isDebugMode() && !$this->isAdmin()) {
            $debugLevel = $this->getConfig()->getConfigParam('iDebug');
            $debugInfo = oxNew('oxDebugInfo');

            $logId = md5(time() . rand() . rand());
            $header = $debugInfo->formatGeneralInfo();
            $display = ($debugLevel == -1) ? 'none' : 'block';
            $monitorMessage = $this->formMonitorMessage($viewId, $viewData);

            $logMessage = "
                <div id='oxidDebugInfo_$logId'>
                    <div style='color:#630;margin:15px 0 0;cursor:pointer'
                         onclick='var el=document.getElementById(\"debugInfoBlock_$logId\"); if (el.style.display==\"block\")el.style.display=\"none\"; else el.style.display = \"block\";'>
                          $header(show/hide)
                    </div>
                    <div id='debugInfoBlock_$logId' style='display:$display' class='debugInfoBlock' align='left'>
                        $monitorMessage
                    </div>
                    <script type='text/javascript'>
                        var b = document.getElementById('oxidDebugInfo_$logId');
                        var c = document.body;
                        if (c) { c.appendChild(b.parentNode.removeChild(b));}
                    </script>
                </div>";

            $this->_getOutputManager()->output('debuginfo', $logMessage);
        }
    }

    /**
     * @param string $viewId
     * @param array  $viewData
     *
     * @return string
     */
    protected function formMonitorMessage($viewId, $viewData)
    {
        $debugInfo = oxNew('oxDebugInfo');

        $debugLevel = $this->getConfig()->getConfigParam('iDebug');

        $message = '';

        // Outputting template params
        if ($debugLevel == 4) {
            $message .= $debugInfo->formatTemplateData($viewData);
        }

        // Output timing
        $this->_dTimeEnd = microtime(true);

        $message .= $debugInfo->formatMemoryUsage();
        $message .= $debugInfo->formatTimeStamp();
        $message .= $debugInfo->formatExecutionTime($this->getTotalTime());

        if ($debugLevel == 7) {
            $message .= $debugInfo->formatDbInfo();
        }

        if ($debugLevel == 2 || $debugLevel == 3 || $debugLevel == 4) {
            $message .= $debugInfo->formatAdoDbPerf();
        }

        return $message;
    }

    /**
     * Returns the difference between stored profiler end time and start time. Works only after _stopMonitor() is called, otherwise returns 0.
     *
     * @return double
     */
    public function getTotalTime()
    {
        if ($this->_dTimeEnd && $this->_dTimeStart) {
            return $this->_dTimeEnd - $this->_dTimeStart;
        }

        return 0;
    }

    /**
     * Executes regular maintenance functions..
     *
     * @return null
     */
    protected function _executeMaintenanceTasks()
    {
        if (isset($this->_blMainTasksExecuted)) {
            return;
        }

        startProfile('executeMaintenanceTasks');
        oxNew("oxArticleList")->updateUpcomingPrices();
        stopProfile('executeMaintenanceTasks');
    }

    /**
     * Initiates object (object::init()), executes passed function
     * (oxShopControl::executeFunction(), if method returns some string - will
     * redirect page and will call another function according to returned
     * parameters), renders object (object::render()). Performs output processing
     * oxOutput::ProcessViewArray(). Passes template variables to template
     * engine witch generates output. Output is additionally processed
     * (oxOutput::Process()), fixed links according search engines optimization
     * rules (configurable in Admin area). Finally echoes the output.
     *
     * @param string $class      Name of class
     * @param string $function   Name of function
     * @param array  $parameters Parameters array
     * @param array  $viewsChain Array of views names that should be initialized also
     */
    protected function _process($class, $function, $parameters = null, $viewsChain = null)
    {
        startProfile('process');
        $config = $this->getConfig();

        // executing maintenance tasks
        $this->_executeMaintenanceTasks();

        $utils = oxRegistry::getUtils();

        if (!$utils->isSearchEngine() &&
            !($this->isAdmin() || !$config->getConfigParam('blLogging'))
        ) {
            $this->_log($class, $function);
        }

        // starting resource monitor
        $this->_startMonitor();

        // Initialize view object and it's components.
        $view = $this->_initializeViewObject($class, $function, $parameters, $viewsChain);

        $this->executeAction($view, $view->getFncName());

        $output = $this->formOutput($view);

        $outputManager = $this->_getOutputManager();
        $outputManager->setCharset($view->getCharSet());

        if ($config->getRequestParameter('renderPartial')) {
            $outputManager->setOutputFormat(oxOutput::OUTPUT_FORMAT_JSON);
            $outputManager->output('errors', $this->_getFormattedErrors($view->getClassName()));
        }

        $outputManager->sendHeaders();

        $this->sendAdditionalHeaders($view);

        $outputManager->output('content', $output);

        $config->pageClose();

        stopProfile('process');

        $isCached = false;
        $this->_stopMonitor($view->getIsCallForCache(), $isCached, $view->getViewId(), $view->getViewData());

        $outputManager->flushOutput();
    }

    /**
     * Executes provided function on view object.
     * If this function can not be executed (is protected or so), oxSystemComponentException exception is thrown.
     *
     * @param oxUBase $view
     * @param string  $functionName
     *
     * @throws oxSystemComponentException
     */
    protected function executeAction($view, $functionName)
    {
        if (!$this->_canExecuteFunction($view, $functionName)) {
            throw oxNew('oxSystemComponentException', 'Non public method cannot be accessed');
        }

        $view->executeFunction($functionName);
    }

    /**
     * Forms output from view object.
     *
     * @param oxUBase $view
     *
     * @return string
     */
    protected function formOutput($view)
    {
        return $this->_render($view);
    }

    /**
     * Method for sending any additional headers on every page requests.
     *
     * @param oxUBase $view
     */
    protected function sendAdditionalHeaders($view)
    {
    }

    /**
     * initialize and return view object
     *
     * @param string $class      View name
     * @param string $function   Function name
     * @param array  $parameters Parameters array
     * @param array  $viewsChain Array of views names that should be initialized also
     *
     * @return oxUBase
     */
    protected function _initializeViewObject($class, $function, $parameters = null, $viewsChain = null)
    {
        /** @var oxUBase $view */
        $view = oxNew($class);

        $view->setClassName($class);
        $view->setFncName($function);
        $view->setViewParameters($parameters);

        $this->getConfig()->setActiveView($view);

        $this->onViewCreation($view);

        $view->init();

        return $view;
    }

    /**
     * Event for any actions during view creation.
     *
     * @param oxUBase $view
     */
    protected function onViewCreation($view)
    {
    }

    /**
     * Check if method can be executed.
     *
     * @param oxUBase $view     View object to check if its method can be executed.
     * @param string  $function Method to check if it can be executed.
     *
     * @return bool
     */
    protected function _canExecuteFunction($view, $function)
    {
        $canExecute = true;

        if (method_exists($view, $function)) {
            $reflectionMethod = new ReflectionMethod($view, $function);
            if (!$reflectionMethod->isPublic()) {
                $canExecute = false;
            }
        }

        return $canExecute;
    }

    /**
     * format error messages from _getErrors and return as array
     *
     * @param string $controllerName a class name
     *
     * @return array
     */
    protected function _getFormattedErrors($controllerName)
    {
        $errors = $this->_getErrors($controllerName);
        $formattedErrors = array();
        if (is_array($errors) && count($errors)) {
            foreach ($errors as $location => $ex2) {
                foreach ($ex2 as $key => $er) {
                    $error = unserialize($er);
                    $formattedErrors[$location][$key] = $error->getOxMessage();
                }
            }
        }

        return $formattedErrors;
    }

    /**
     * render oxUBase object
     *
     * @param oxUBase $view view object to render
     *
     * @return string
     */
    protected function _render($view)
    {
        // get Smarty is important here as it sets template directory correct
        $smarty = oxRegistry::get("oxUtilsView")->getSmarty();

        // render it
        $templateName = $view->render();

        // check if template dir exists
        $templateFile = $this->getConfig()->getTemplatePath($templateName, $this->isAdmin());
        if (!file_exists($templateFile)) {
            $ex = oxNew('oxSystemComponentException');
            $ex->setMessage('EXCEPTION_SYSTEMCOMPONENT_TEMPLATENOTFOUND');
            $ex->setComponent($templateName);

            $templateName = "message/exception.tpl";

            if ($this->_isDebugMode()) {
                oxRegistry::get("oxUtilsView")->addErrorToDisplay($ex);
            }
            $ex->debugOut();
        }

        // Output processing. This is useful for modules. As sometimes you may want to process output manually.
        $outputManager = $this->_getOutputManager();
        $viewData = $outputManager->processViewArray($view->getViewData(), $view->getClassName());
        $view->setViewData($viewData);

        //add all exceptions to display
        $errors = $this->_getErrors($view->getClassName());
        if (is_array($errors) && count($errors)) {
            oxRegistry::get("oxUtilsView")->passAllErrorsToView($viewData, $errors);
        }

        foreach (array_keys($viewData) as $viewName) {
            $smarty->assign_by_ref($viewName, $viewData[$viewName]);
        }

        // passing current view object to smarty
        $smarty->oxobject = $view;

        $output = $smarty->fetch($templateName, $view->getViewId());

        //Output processing - useful for modules as sometimes you may want to process output manually.
        $output = $outputManager->process($output, $view->getClassName());

        return $outputManager->addVersionTags($output);
    }

    /**
     * return output handler
     *
     * @return oxOutput
     */
    protected function _getOutputManager()
    {
        if (!$this->_oOutput) {
            $this->_oOutput = oxNew('oxOutput');
        }

        return $this->_oOutput;
    }

    /**
     * return page errors array
     *
     * @param string $currentControllerName a class name
     *
     * @return array
     */
    protected function _getErrors($currentControllerName)
    {
        if (null === $this->_aErrors) {
            $this->_aErrors = oxRegistry::getSession()->getVariable('Errors');
            $this->_aControllerErrors = oxRegistry::getSession()->getVariable('ErrorController');
            if (null === $this->_aErrors) {
                $this->_aErrors = array();
            }
            $this->_aAllErrors = $this->_aErrors;
        }
        // resetting errors of current controller or widget from session
        if (is_array($this->_aControllerErrors) && !empty($this->_aControllerErrors)) {
            foreach ($this->_aControllerErrors as $errorName => $controllerName) {
                if ($controllerName == $currentControllerName) {
                    unset($this->_aAllErrors[$errorName]);
                    unset($this->_aControllerErrors[$errorName]);
                }
            }
        } else {
            $this->_aAllErrors = array();
        }
        oxRegistry::getSession()->setVariable('ErrorController', $this->_aControllerErrors);
        oxRegistry::getSession()->setVariable('Errors', $this->_aAllErrors);

        return $this->_aErrors;
    }

    /**
     * This function is only executed one time here we perform checks if we
     * only need once per session
     *
     * @return null
     */
    protected function _runOnce()
    {
        $config = $this->getConfig();

        error_reporting($this->_getErrorReportingLevel());


        $runOnceExecuted = oxRegistry::getSession()->getVariable('blRunOnceExecuted');
        if (!$runOnceExecuted && !$this->isAdmin() && $config->isProductiveMode()) {
            // check if setup is still there
            if (file_exists($config->getConfigParam('sShopDir') . '/setup/index.php')) {
                $tpl = 'message/err_setup.tpl';
                $activeView = oxNew('oxUBase');
                $smarty = oxRegistry::get("oxUtilsView")->getSmarty();
                $smarty->assign('oView', $activeView);
                $smarty->assign('oViewConf', $activeView->getViewConfig());
                oxRegistry::getUtils()->showMessageAndExit($smarty->fetch($tpl));
            }

            oxRegistry::getSession()->setVariable('blRunOnceExecuted', true);
        }
    }

    /**
     * Returns error reporting level.
     * Returns disabled error logging if server is misconfigured #2015 E_NONE replaced with 0.
     *
     * @return int
     */
    protected function _getErrorReportingLevel()
    {
        $oldReporting = error_reporting();

        $errorReporting = E_ALL ^ E_NOTICE;
        // some 3rd party libraries still use deprecated functions
        if (defined('E_DEPRECATED')) {
            $errorReporting = $errorReporting ^ E_DEPRECATED;
        }

        if ($this->getConfig()->isProductiveMode() && !ini_get('log_errors')) {
            $errorReporting = 0;
        }


        return $errorReporting;
    }

    /**
     * Checks if shop is in debug mode
     *
     * @return bool
     */
    protected function _isDebugMode()
    {
        if (oxRegistry::get("OxConfigFile")->getVar('iDebug')) {
            return true;
        }

        return false;
    }

    /**
     * Shows exceptionError page.
     * possible reason: class does not exist etc. --> just redirect to start page.
     *
     * @param oxException $exception
     */
    protected function _handleSystemException($exception)
    {
        $exception->debugOut();

        if ($this->_isDebugMode()) {
            oxRegistry::get("oxUtilsView")->addErrorToDisplay($exception);
            $this->_process('exceptionError', 'displayExceptionError');
        } else {
            oxRegistry::getUtils()->redirect($this->getConfig()->getShopHomeUrl() . 'cl=start', true, 302);
        }
    }

    /**
     * Redirect to start page, in debug mode shows error message.
     *
     * @param oxException $exception Exception
     */
    protected function _handleCookieException($exception)
    {
        if ($this->_isDebugMode()) {
            oxRegistry::get("oxUtilsView")->addErrorToDisplay($exception);
        }
        oxRegistry::getUtils()->redirect($this->getConfig()->getShopHomeUrl() . 'cl=start', true, 302);
    }

    /**
     * R&R handling -> redirect to error msg, also, can call _process again, specifying error handler view class.
     *
     * @param oxException $exception Exception
     */
    protected function _handleAccessRightsException($exception)
    {
        oxRegistry::getUtils()->redirect($this->getConfig()->getShopHomeUrl() . 'cl=content&tpl=err_accessdenied.tpl', true, 302);
    }

    /**
     * Shows exception message if debug mode is enabled, redirects otherwise.
     *
     * @param oxConnectionException $exception message to show on exit
     */
    protected function _handleDbConnectionException($exception)
    {
        $exception->debugOut();

        if ($this->_isDebugMode()) {
            oxRegistry::getUtils()->showMessageAndExit($exception->getString());
        }
        oxRegistry::getUtils()->redirectOffline();
    }

    /**
     * Catching other not caught exceptions.
     *
     * @param oxException $exception
     */
    protected function _handleBaseException($exception)
    {
        $exception->debugOut();

        if ($this->_isDebugMode()) {
            oxRegistry::get("oxUtilsView")->addErrorToDisplay($exception);
            $this->_process('exceptionError', 'displayExceptionError');
        }
    }

    /**
     * Returns controller class which should be loaded.
     *
     * @return string
     */
    protected function _getStartController()
    {
        $class = oxRegistry::getConfig()->getRequestParameter('cl');

        if (!$class) {
            $session = oxRegistry::getSession();
            if ($this->isAdmin()) {
                $class = $session->getVariable("auth") ? 'admin_start' : 'login';
            } else {
                $class = $this->_getFrontendStartController();
            }
            $session->setVariable('cl', $class);
        }

        return $class;
    }

    /**
     * Returns which controller should be loaded at shop start.
     * Check whether we have to display mall start screen or not.
     *
     * @return string
     */
    protected function _getFrontendStartController()
    {
        $class = 'start';
        if ($this->getConfig()->isMall()) {
            $class = $this->_getFrontendMallStartController();
        }

        return $class;
    }

    /**
     * Returns start controller class name for frontend mall.
     * If no class specified, we need to change back to base shop
     *
     * @return string
     */
    protected function _getFrontendMallStartController()
    {
        $activeShopsCount = oxDb::getDb()->getOne('select count(*) from oxshops where oxactive = 1');

        $mallShopURL = $this->getConfig()->getConfigParam('sMallShopURL');

        $class = 'start';
        if ($activeShopsCount && $activeShopsCount > 1 && $this->getConfig()->getConfigParam('iMallMode') != 0 && !$mallShopURL) {
            $class = 'mallstart';
        }

        return $class;
    }
}
