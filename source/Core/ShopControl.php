<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Core;

use OxidEsales\Eshop\Application\Controller\FrontendController;
use OxidEsales\Eshop\Core\Exception\DatabaseConnectionException;
use OxidEsales\Eshop\Core\Exception\RoutingException;
use OxidEsales\Eshop\Core\Exception\StandardException;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;
use oxOutput;
use oxSystemComponentException;
use PHPMailer\PHPMailer\PHPMailer;
use ReflectionMethod;
use OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\ViewRenderedEvent;
use OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\BeforeHeadersSendEvent;

/**
 * Main shop actions controller. Processes user actions, logs
 * them (if needed), controls output, redirects according to
 * processed methods logic. This class is initialized from index.php
 */
class ShopControl extends \OxidEsales\Eshop\Core\Base
{
    /**
     * Used to force handling, it allows other place like widget controller to skip it.
     *
     * @deprecated since v.6.0.0 (2017-10-11); Not used any more, was used in _setDefaultExceptionHandler()
     * which was already removed.
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
     * Path to the file, which holds the timestamp of the moment the last offline warning was sent.
     *
     * @var
     */
    protected $offlineWarningTimestampFile;

    /**
     * ShopControl constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->offlineWarningTimestampFile = OX_BASE_PATH . 'log/last-offline-warning-timestamp.log';
    }

    /**
     * Main shop manager, that sets shop status, executes configuration methods.
     * Executes \OxidEsales\Eshop\Core\ShopControl::_runOnce(), if needed sets default class (according
     * to admin or regular activities). Additionally its possible to pass class name,
     * function name and parameters array to view, which will be executed.
     *
     * @param string $controllerKey Key of the controller class to be processed
     * @param string $function      Function name
     * @param array  $parameters    Parameters array
     * @param array  $viewsChain    Array of views names that should be initialized also
     */
    public function start($controllerKey = null, $function = null, $parameters = null, $viewsChain = null)
    {
        try {
            $this->_runOnce();

            $function = !is_null($function) ? $function : \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('fnc');
            $controllerKey = !is_null($controllerKey) ? $controllerKey : $this->getStartControllerKey();
            $controllerClass = $this->getControllerClass($controllerKey);

            $this->_process($controllerClass, $function, $parameters, $viewsChain);
        } catch (\OxidEsales\Eshop\Core\Exception\SystemComponentException $exception) {
            $this->_handleSystemException($exception);
        } catch (\OxidEsales\Eshop\Core\Exception\CookieException $exception) {
            $this->_handleCookieException($exception);
        } catch (\OxidEsales\Eshop\Core\Exception\DatabaseException $exception) {
            $this->handleDatabaseException($exception);
        } catch (\OxidEsales\Eshop\Core\Exception\StandardException $exception) {
            $this->_handleBaseException($exception);
        }
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
     * @deprecated since v6.0 (2017-02-03). Use ShopControl::getStartControllerKey() instead.
     *
     * Returns controller class which should be loaded.
     *
     * @return string
     */
    protected function _getStartController()
    {
        return $this->getStartControllerKey();
    }

    /**
     * @deprecated since v6.0 (2017-02-03). Use ShopControl::getFrontendStartControllerKey() instead.
     *
     * Returns which controller should be loaded at shop start.
     * Check whether we have to display mall start screen or not.
     *
     * @return string
     */
    protected function _getFrontendStartController()
    {
        return 'start';
    }

    /**
     * Returns class id of controller which should be loaded.
     * When in doubt returns default start controller class.
     *
     * @return string
     */
    protected function getStartControllerKey()
    {
        $controllerKey = Registry::getConfig()->getRequestControllerId();

        // Use default route in case no controller id is given
        if (!$controllerKey) {
            $session = \OxidEsales\Eshop\Core\Registry::getSession();
            if ($this->isAdmin()) {
                $controllerKey = $session->getVariable("auth") ? 'admin_start' : 'login';
            } else {
                $controllerKey = $this->getFrontendStartControllerKey();
            }
            $session->setVariable('cl', $controllerKey);
        }

        return $controllerKey;
    }

    /**
     * Returns class id of controller which should be loaded.
     * When in doubt returns default start controller class.
     *
     * @param string $controllerKey Controller id
     *
     * @throws RoutingException
     * @return string
     */
    protected function resolveControllerClass($controllerKey)
    {
        $resolvedClass = Registry::getControllerClassNameResolver()->getClassNameById($controllerKey);

        // If unmatched controller id is requested throw exception
        if (!$resolvedClass) {
            throw new \OxidEsales\Eshop\Core\Exception\RoutingException($controllerKey);
        }

        return $resolvedClass;
    }

    /**
     * Returns id of controller that should be loaded at shop start.
     * Check whether we have to display mall start screen or not.
     *
     * @return string
     */
    protected function getFrontendStartControllerKey()
    {
        return 'start';
    }

    /**
     * Initiates object (object::init()), executes passed function
     * (\OxidEsales\Eshop\Core\ShopControl::executeFunction(), if method returns some string - will
     * redirect page and will call another function according to returned
     * parameters), renders object (object::render()). Performs output processing
     * \OxidEsales\Eshop\Core\Output::ProcessViewArray(). Passes template variables to template
     * engine witch generates output. Output is additionally processed
     * (\OxidEsales\Eshop\Core\Output::Process()), fixed links according search engines optimization
     * rules (configurable in Admin area). Finally echoes the output.
     *
     * @param string $class      Class name
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

        // starting resource monitor
        $this->_startMonitor();

        // Initialize view object and it's components.
        $view = $this->_initializeViewObject($class, $function, $parameters, $viewsChain);

        $this->executeAction($view, $view->getFncName());

        $output = $this->formOutput($view);

        $this->dispatchEvent(new ViewRenderedEvent($this));

        $outputManager = $this->_getOutputManager();
        $outputManager->setCharset($view->getCharSet());

        if ($config->getRequestParameter('renderPartial')) {
            $outputManager->setOutputFormat(\OxidEsales\Eshop\Core\Output::OUTPUT_FORMAT_JSON);
            $outputManager->output('errors', $this->_getFormattedErrors($view->getClassName()));
        }

        $this->dispatchEvent(new BeforeHeadersSendEvent($this, $view));

        $outputManager->sendHeaders();

        //Send headers that have been registered
        $header = \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Core\Header::class);
        $header->sendHeader();

        $this->sendAdditionalHeaders($view);

        $outputManager->output('content', $output);

        $config->pageClose();

        stopProfile('process');

        $this->_stopMonitor($view->getIsCallForCache(), false, $view->getViewId(), $view->getViewData(), $view);

        $outputManager->flushOutput();
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
        oxNew(\OxidEsales\Eshop\Application\Model\ArticleList::class)->updateUpcomingPrices();
        stopProfile('executeMaintenanceTasks');
    }

    /**
     * Executes provided function on view object.
     * If this function can not be executed (is protected or so), oxSystemComponentException exception is thrown.
     *
     * @param FrontendController $view
     * @param string             $functionName
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
     * @param FrontendController $view
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
     * @param FrontendController $view
     */
    protected function sendAdditionalHeaders($view)
    {
    }

    /**
     * Initialize and return view object.
     *
     * @param string $class      View class
     * @param string $function   Function name
     * @param array  $parameters Parameters array
     * @param array  $viewsChain Array of views names that should be initialized also
     *
     * @return FrontendController
     */
    protected function _initializeViewObject($class, $function, $parameters = null, $viewsChain = null)
    {
        $classKey = Registry::getControllerClassNameResolver()->getIdByClassName($class);
        $classKey = !is_null($classKey) ? $classKey : $class; //fallback

        /** @var FrontendController $view */
        $view = oxNew($class);

        $view->setClassKey($classKey);
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
     * @param FrontendController $view
     */
    protected function onViewCreation($view)
    {
    }

    /**
     * Check if method can be executed.
     *
     * @param FrontendController $view     View object to check if its method can be executed.
     * @param string             $function Method to check if it can be executed.
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
     * Format error messages from _getErrors and return as array.
     *
     * @param string $controllerName a class name
     *
     * @return array
     */
    protected function _getFormattedErrors($controllerName)
    {
        $errors = $this->_getErrors($controllerName);
        $formattedErrors = [];
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
     * Render BaseController object.
     *
     * @param FrontendController $view view object to render
     *
     * @return string
     */
    protected function _render($view)
    {
        $renderer = $this->getRenderer();

        // render it
        $templateName = $view->render();

        // check if template exists
        /** @var TemplateLoaderInterface $templateLoader */
        $templateLoader = $this->getContainer()->get('oxid_esales.templating.template.loader');
        if ($this->isAdmin()) {
            $templateLoader = $this->getContainer()->get('oxid_esales.templating.admin.template.loader');
        }

        if (!$templateLoader->exists($templateName)) {
            $ex = oxNew(\OxidEsales\Eshop\Core\Exception\SystemComponentException::class);
            $ex->setMessage('EXCEPTION_SYSTEMCOMPONENT_TEMPLATENOTFOUND' . ' ' . $templateName);
            $ex->setComponent($templateName);

            $templateName = "message/exception.tpl";

            if ($this->_isDebugMode()) {
                \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay($ex);
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
            \OxidEsales\Eshop\Core\Registry::getUtilsView()->passAllErrorsToView($viewData, $errors);
        }

        // passing current view object to smarty
        // TODO: remove it! Also in varnish module
        $renderer->oxobject = $view;

        $viewData['oxEngineTemplateId'] = $view->getViewId();
        $output = $renderer->renderTemplate($templateName, $viewData);

        //Output processing - useful for modules as sometimes you may want to process output manually.
        $output = $outputManager->process($output, $view->getClassName());

        return $outputManager->addVersionTags($output);
    }

    /**
     * @internal
     *
     * @return TemplateRendererInterface
     */
    private function getRenderer()
    {
        return $this->getContainer()
            ->get(TemplateRendererBridgeInterface::class)
            ->getTemplateRenderer();
    }

    /**
     * Return output handler.
     *
     * @return oxOutput
     */
    protected function _getOutputManager()
    {
        if (!$this->_oOutput) {
            $this->_oOutput = oxNew(\OxidEsales\Eshop\Core\Output::class);
        }

        return $this->_oOutput;
    }

    /**
     * Return page errors array.
     *
     * @param string $currentControllerName Class name
     *
     * @return array
     */
    protected function _getErrors($currentControllerName)
    {
        if (null === $this->_aErrors) {
            $this->_aErrors = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable('Errors');
            $this->_aControllerErrors = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable('ErrorController');
            if (null === $this->_aErrors) {
                $this->_aErrors = [];
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
            $this->_aAllErrors = [];
        }
        \OxidEsales\Eshop\Core\Registry::getSession()->setVariable('ErrorController', $this->_aControllerErrors);
        \OxidEsales\Eshop\Core\Registry::getSession()->setVariable('Errors', $this->_aAllErrors);

        return $this->_aErrors;
    }

    /**
     * This function is only executed one time here we perform checks if we
     * only need once per session.
     */
    protected function _runOnce()
    {
        $config = $this->getConfig();

        //Ensures config values are available, database connection is established,
        //session is started, a possible SeoUrl is decoded, globals and environment variables are set.
        $config->init();

        error_reporting($this->_getErrorReportingLevel());

        $runOnceExecuted = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable('blRunOnceExecuted');
        if (!$runOnceExecuted && !$this->isAdmin() && $config->isProductiveMode()) {
            // check if setup is still there
            if (file_exists($config->getConfigParam('sShopDir') . '/Setup/index.php')) {
                $tpl = 'message/err_setup.tpl';
                $activeView = oxNew(\OxidEsales\Eshop\Application\Controller\FrontendController::class);
                $context = [
                    "oViewConf" => $activeView->getViewConfig(),
                    "oView"     => $activeView
                ];
                $renderer = $this->getRenderer();
                $errorOutput = $renderer->renderTemplate($tpl, $context);
                \OxidEsales\Eshop\Core\Registry::getUtils()->showMessageAndExit($errorOutput);
            }

            \OxidEsales\Eshop\Core\Registry::getSession()->setVariable('blRunOnceExecuted', true);
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
     * Checks if shop is in debug mode.
     *
     * @return bool
     */
    protected function _isDebugMode()
    {
        return (bool) Registry::get(\OxidEsales\Eshop\Core\ConfigFile::class)->getVar('iDebug');
    }

    /**
     * Starts resource monitor.
     */
    protected function _startMonitor()
    {
        if ($this->_isDebugMode()) {
            $this->_dTimeStart = microtime(true);
        }
    }

    /**
     * Stops resource monitor, summarizes and outputs values.
     *
     * @deprecated on b-dev (2015-10-01); Use self::stopMonitoring() instead.
     *
     * @param bool               $isCallForCache Is content cache
     * @param bool               $isCached       Is content cached
     * @param string             $viewId         View ID
     * @param array              $viewData       View data
     * @param FrontendController $view           View object
     */
    protected function _stopMonitor($isCallForCache = false, $isCached = false, $viewId = null, $viewData = [], $view = null)
    {
        if (is_null($view)) {
            $controllerKey = $this->getStartControllerKey();
            $controllerClass = $this->getControllerClass($controllerKey);
            $view = oxNew($controllerClass);
        }
        $this->stopMonitoring($view);
    }

    /**
     * Stops resource monitor, summarizes and outputs values.
     *
     * @param FrontendController $view View object
     */
    protected function stopMonitoring($view)
    {
        if ($this->_isDebugMode() && !$this->isAdmin()) {
            $debugLevel = $this->getConfig()->getConfigParam('iDebug');
            $debugInfo = oxNew(\OxidEsales\Eshop\Core\DebugInfo::class);

            $logId = md5(time() . rand() . rand());
            $header = $debugInfo->formatGeneralInfo();
            $display = ($debugLevel == -1) ? 'none' : 'block';
            $monitorMessage = $this->formMonitorMessage($view);

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
     * Forms message for displaying monitoring information on the bottom of the page.
     *
     * @param FrontendController $view
     *
     * @return string
     */
    protected function formMonitorMessage($view)
    {
        $debugInfo = oxNew(\OxidEsales\Eshop\Core\DebugInfo::class);

        $debugLevel = $this->getConfig()->getConfigParam('iDebug');

        $message = '';

        // Outputting template params
        if ($debugLevel == 4) {
            $message .= $debugInfo->formatTemplateData($view->getViewData());
        }

        // Output timing
        $this->_dTimeEnd = microtime(true);

        $message .= $debugInfo->formatMemoryUsage();
        $message .= $debugInfo->formatTimeStamp();
        $message .= $debugInfo->formatExecutionTime($this->getTotalTime());

        return $message;
    }

    /**
     * Shows exceptionError page.
     * possible reason: class does not exist etc. --> just redirect to start page.
     *
     * @param \OxidEsales\Eshop\Core\Exception\StandardException $exception
     */
    protected function _handleSystemException($exception)
    {
        $exception->debugOut();

        if ($this->_isDebugMode()) {
            \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay($exception);
            $this->_process('exceptionError', 'displayExceptionError');
        } else {
            \OxidEsales\Eshop\Core\Registry::getUtils()->redirect($this->getConfig()->getShopHomeUrl() . 'cl=start', true, 302);
        }
    }

    /**
     * Handle routing exception, which is thrown, if the class name for the requested controller id could not be resolved.
     *
     * @param RoutingException $exception
     */
    protected function handleRoutingException($exception)
    {
        /**
         * @todo after removal of the BC layer this method will retrow the exception
         * throw $exception
         */
    }

    /**
     * Redirect to start page, in debug mode shows error message.
     *
     * @param \OxidEsales\Eshop\Core\Exception\StandardException $exception Exception
     */
    protected function _handleCookieException($exception)
    {
        if ($this->_isDebugMode()) {
            \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay($exception);
        }
        \OxidEsales\Eshop\Core\Registry::getUtils()->redirect($this->getConfig()->getShopHomeUrl() . 'cl=start', true, 302);
    }

    /**
     * Handle database exceptions
     * There is still space for improving this as a similar exception handling for database exceptions may be done in
     * \OxidEsales\EshopCommunity\Core\Config::init() and the current method may not be executed
     *
     * @param \OxidEsales\Eshop\Core\Exception\DatabaseException $exception Exception to handle
     */
    protected function handleDatabaseException(\OxidEsales\Eshop\Core\Exception\DatabaseException $exception)
    {
        /**
         * There may be some more exceptions, while trying to retrieve debug mode.
         * As we are already inside the exception handling process, we MUST catch any exception here.
         * The exception newly thrown will not be handled as we might end up in a loop.
         */
        try {
            $debugMode = $this->_isDebugMode();
        } catch (\Exception $newException) {
            $this->logException($newException);
            $debugMode = 0;
        }
        if ($exception instanceof \OxidEsales\Eshop\Core\Exception\DatabaseConnectionException) {
            try {
                $this->reportDatabaseConnectionException($exception);
            } catch (\Exception $newException) {
                $this->logException($newException);
            }
        }

        /**
         * Do not use oxNew here as this code forms already part of the exception handling process and there should at
         * least shop code called as possible.
         */
        $exceptionHandler = new \OxidEsales\Eshop\Core\Exception\ExceptionHandler($debugMode);
        $exceptionHandler->handleDatabaseException($exception);
    }

    /**
     * Handling other not caught exceptions.
     *
     * @param \OxidEsales\Eshop\Core\Exception\StandardException $exception
     */
    protected function _handleBaseException($exception)
    {
        $this->logException($exception);

        if ($this->_isDebugMode()) {
            \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay($exception);
            $this->_process('exceptionError', 'displayExceptionError');
        }
    }

    /**
     * Log an exception.
     *
     * This method forms part of the exception handling process. Any further exceptions must be caught.
     *
     * @param \Exception $exception
     */
    protected function logException(\Exception $exception)
    {
        if (!$exception instanceof \OxidEsales\Eshop\Core\Exception\StandardException) {
            $exception = new \OxidEsales\Eshop\Core\Exception\StandardException($exception->getMessage(), $exception->getCode(), $exception);
        }
        $exception->debugOut();
    }

    /**
     * Notify the shop owner about database connection problems.
     *
     * This method forms part of the exception handling process. Any further exceptions must be caught.
     *
     * @param DatabaseConnectionException $exception Database connection exception to report
     *
     * @return null
     */
    protected function reportDatabaseConnectionException(DatabaseConnectionException $exception)
    {
        /**
         * If the shop is not in debug mode, a "shop offline" warning is send to the shop admin.
         * In order not to spam the shop admin, the warning will be sent in a certain interval of time.
         */
        if ($this->messageWasSentWithinThreshold() || $this->_isDebugMode()) {
            return;
        }

        $result = $this->sendOfflineWarning($exception);
        if ($result) {
            file_put_contents($this->offlineWarningTimestampFile, time());
        }
    }

    /**
     * Return true, if a message was already sent within a given threshold.
     *
     * This method forms part of the exception handling process. Any further exceptions must be caught.
     *
     * @return bool
     */
    protected function messageWasSentWithinThreshold()
    {
        $wasSentWithinThreshold = false;

        /** @var int $threshold Threshold in seconds */
        $threshold = Registry::get(\OxidEsales\Eshop\Core\ConfigFile::class)->getVar('offlineWarningInterval');
        if (file_exists($this->offlineWarningTimestampFile)) {
            $lastSentTimestamp = (int) file_get_contents($this->offlineWarningTimestampFile);
            $lastSentBefore = time() - $lastSentTimestamp;
            if ($lastSentBefore < $threshold) {
                $wasSentWithinThreshold = true;
            }
        }

        return $wasSentWithinThreshold;
    }

    /**
     * Send an offline warning to the shop owner.
     * Currently an email is sent to the email address configured as 'sAdminEmail' in the eShop config file.
     *
     * This method forms part of the exception handling process. Any further exceptions must be caught.
     *
     * @param StandardException $exception
     *
     * @return bool Returns true, if the email was sent.
     */
    protected function sendOfflineWarning(\OxidEsales\Eshop\Core\Exception\StandardException $exception)
    {
        $result = false;
        /** @var  $emailAddress Email address to sent the message to */
        $emailAddress = Registry::get(\OxidEsales\Eshop\Core\ConfigFile::class)->getVar('sAdminEmail');

        if ($emailAddress) {
            /** As we are inside the exception handling process, any further exceptions must be caught */
            $failedShop = isset($_REQUEST['shp']) ? addslashes($_REQUEST['shp']) : 'Base shop';

            $date = date(DATE_RFC822); // RFC 822 (example: Mon, 15 Aug 05 15:52:01 +0000)
            $script = $_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING'];
            $referrer = $_SERVER['HTTP_REFERER'];

            //sending a message to admin
            $emailSubject = 'Offline warning!';
            $emailBody = "
                Database connection error in OXID eShop:
                Date: {$date}
                Shop: {$failedShop}

                mysql error: " . $exception->getMessage() . "
                mysql error no: " . $exception->getCode() . "

                Script: {$script}
                Referrer: {$referrer}";

            $mailer = new PHPMailer();
            $mailer->isMail();

            $mailer->setFrom($emailAddress);
            $mailer->addAddress($emailAddress);
            $mailer->Subject = $emailSubject;
            $mailer->Body = $emailBody;
            /** Set the priority of the message
             * For most clients expecting the Priority header:
             * 1 = High, 2 = Medium, 3 = Low
             * */
            $mailer->Priority = 1;
            /** MS Outlook custom header */
            $mailer->addCustomHeader("X-MSMail-Priority: Urgent");
            /** Set the Importance header: */
            $mailer->addCustomHeader("Importance: High");

            $result = $mailer->send();
        }

        return $result;
    }

    /**
     * Get controller class from key.
     * Fallback is to use key as class if no match can be found.
     *
     * @param string $controllerKey
     *
     * @return string
     */
    protected function getControllerClass($controllerKey)
    {
        /** Remove try catch block after routing BC is removed */
        try {
            $controllerClass = $this->resolveControllerClass($controllerKey);
        } catch (\OxidEsales\Eshop\Core\Exception\RoutingException $exception) {
            $this->handleRoutingException($exception);
            $controllerClass = $controllerKey;
        }

        return $controllerClass;
    }
}
