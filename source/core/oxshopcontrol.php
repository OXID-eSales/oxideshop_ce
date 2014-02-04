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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

/**
 * Main shop actions controller. Processes user actions, logs
 * them (if needed), controlls output, redirects according to
 * processed methods logic. This class is initalized from index.php
 */
class oxShopControl extends oxSuperCfg
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

    protected $_oCache = null;

    /**
     * Main shop manager, that sets shop status, executes configuration methods.
     * Executes oxShopControl::_runOnce(), if needed sets default class (according
     * to admin or regular activities). Additionaly its possible to pass class name,
     * function name and parameters array to view, which will be executed.
     *
     * Session variables:
     * <b>actshop</b>
     *
     * @param string $sClass      Class name
     * @param string $sFunction   Funtion name
     * @param array  $aParams     Parameters array
     * @param array  $aViewsChain Array of views names that should be initialized also
     *
     * @return null
     */
    public function start( $sClass = null, $sFunction = null, $aParams = null, $aViewsChain = null )
    {
        //sets default exception handler
        $this->_setDefaultExceptionHandler();
        try {
            //perform tasks once per session
            $this->_runOnce();
            $sFunction = ( isset( $sFunction ) ) ? $sFunction : oxRegistry::getConfig()->getRequestParameter( 'fnc' );
            $sClass = $this->_getControllerToLoad( $sClass );
            $this->_process( $sClass, $sFunction, $aParams, $aViewsChain );
        } catch( oxSystemComponentException $oEx ) {
            $this->_handleSystemException( $oEx );
        } catch ( oxCookieException $oEx ) {
            $this->_handleCookieException( $oEx );
        }
        catch ( oxConnectionException $oEx) {
            $this->_handleDbConnectionException( $oEx );
        }
        catch ( oxException $oEx) {
            $this->_handleBaseException( $oEx );
        }
    }

    /**
     * Sets default exception handler.
     * Ideally all exceptions should be handled with try catch and default exception should never be reached.
     *
     * @return null;
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
     * @param string $sFnc   Name of executed class method
     *
     * @return null
     */
    protected function _log( $sClass, $sFnc )
    {
        $oDb = oxDb::getDb();
        $sShopID = oxSession::getVar( 'actshop' );
        $sTime   = date( 'Y-m-d H:i:s' );
        $sSidQuoted    = $oDb->quote( $this->getSession()->getId() );
        $sUserIDQuoted = $oDb->quote( oxSession::getVar( 'usr' ) );
        $sCnid = oxConfig::getParameter( 'cnid' );
        $sAnid = oxConfig::getParameter( 'aid' ) ? oxConfig::getParameter( 'aid' ) : oxConfig::getParameter( 'anid' );
        $sParameter = '';

        if ( $sClass == 'content' ) {
            $sParameter = str_replace( '.tpl', '', oxConfig::getParameter('tpl') );
        } elseif ( $sClass == 'search' ) {
            $sParameter = oxConfig::getParameter( 'searchparam' );
        }

        $sFncQuoted = $oDb->quote( $sFnc );
        $sClassQuoted = $oDb->quote( $sClass );
        $sParameterQuoted = $oDb->quote( $sParameter );


        $sQ = "insert into oxlogs (oxtime, oxshopid, oxuserid, oxsessid, oxclass, oxfnc, oxcnid, oxanid, oxparameter) ".
              "values( '$sTime', '$sShopID', $sUserIDQuoted, $sSidQuoted, $sClassQuoted, $sFncQuoted, ".$oDb->quote( $sCnid ).", ".$oDb->quote( $sAnid ).", $sParameterQuoted )";

        $oDb->execute( $sQ );
    }

    // OXID : add timing
    /**
     * Starts resource monitor
     *
     * @return null
     */
    protected function _startMonitor()
    {
        if ( $this->_isDebugMode() ) {
            $this->_dTimeStart = microtime(true);
        }
    }

    /**
     * Stops resource monitor, summarizes and outputs values
     *
     * @param bool  $blIsCache  Is content cache
     * @param bool  $blIsCached Is content cached
     * @param bool  $sViewID    View ID
     * @param array $aViewData  View data
     *
     * @return null
     */
    protected function _stopMonitor( $blIsCache = false, $blIsCached = false, $sViewID = null, $aViewData = array() )
    {
        if ( $this->_isDebugMode() && !$this->isAdmin() ) {
            /* @var $oDebugInfo oxDebugInfo */
            $iDebug = $this->getConfig()->getConfigParam( 'iDebug' );
            $oDebugInfo = oxNew('oxDebugInfo');

            $blHidden = ($iDebug == -1);

            $sLog = '';
            $sLogId = md5(time().rand().rand());
            $sLog .= "<div id='oxidDebugInfo_$sLogId'>";

            $sLog .= "<div style='color:#630;margin:15px 0 0;cursor:pointer' onclick='var el=document.getElementById(\"debugInfoBlock_$sLogId\"); if (el.style.display==\"block\")el.style.display=\"none\"; else el.style.display = \"block\";'> ".$oDebugInfo->formatGeneralInfo()."(show/hide)</div>";
            $sLog .= "<div id='debugInfoBlock_$sLogId' style='display:".($blHidden?'none':'block')."' class='debugInfoBlock' align='left'>";


            // outputting template params
            if ( $iDebug == 4 ) {
                $sLog .= $oDebugInfo->formatTemplateData($aViewData);
            }

            // output timing
            $this->_dTimeEnd = microtime(true);


            $sLog .= $oDebugInfo->formatMemoryUsage();

            $sLog .= $oDebugInfo->formatTimeStamp();


            $sLog .= $oDebugInfo->formatExecutionTime($this->getTotalTime());

            if ( $iDebug == 7 ) {
                $sLog .= $oDebugInfo->formatDbInfo();
            }

            if ( $iDebug == 2 || $iDebug == 3 || $iDebug == 4 ) {
                $sLog .= $oDebugInfo->formatAdoDbPerf();
            }

            $sLog .= '</div>';

            $sLog .= "<script type='text/javascript'>
                var b = document.getElementById('oxidDebugInfo_$sLogId');
                var c = document.body;
                if (c) { c.appendChild(b.parentNode.removeChild(b));}
            </script>";

            $sLog .= "</div>";

            $this->_getOutputManager()->output('debuginfo', $sLog);
        }
    }

    /**
     * Returns the difference between stored profiler end time and start time. Works only after _stopMonitor() is called, otherwise returns 0.
     *
     * @return  double
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
     * @param string $sClass      Name of class
     * @param string $sFunction   Name of function
     * @param array  $aParams     Parameters array
     * @param array  $aViewsChain Array of views names that should be initialized also
     *
     * @return null
     */
    protected function _process( $sClass, $sFunction, $aParams = null, $aViewsChain = null )
    {
        startProfile('process');
        $myConfig = $this->getConfig();

        // executing maintenance tasks
        $this->_executeMaintenanceTasks();

        $oUtils  = oxRegistry::getUtils();
        $sViewID = null;

        if ( !$oUtils->isSearchEngine() &&
             !( $this->isAdmin() || !$myConfig->getConfigParam( 'blLogging' ) ) ) {
            $this->_log( $sClass, $sFunction );
        }

        // starting resource monitor
        $this->_startMonitor();

        // caching params ...
        $sOutput      = null;
        $blIsCached   = false;

        // Initialize view object and it's components.
        $oViewObject = $this->_initializeViewObject($sClass, $sFunction, $aParams, $aViewsChain);

        if ( !$this->_canExecuteFunction( $oViewObject, $oViewObject->getFncName() ) ) {
            throw oxNew( 'oxSystemComponentException', 'Non public method cannot be accessed' );
        }

        // executing user defined function
        $oViewObject->executeFunction( $oViewObject->getFncName() );



        // if no cache was stored before we should generate it
        if ( !$blIsCached ) {
            $sOutput = $this->_render($oViewObject);
        }


        $oOutput = $this->_getOutputManager();
        $oOutput->setCharset($oViewObject->getCharSet());

        if (oxConfig::getParameter('renderPartial')) {
            $oOutput->setOutputFormat(oxOutput::OUTPUT_FORMAT_JSON);
            $oOutput->output('errors', $this->_getFormattedErrors( $oViewObject->getClassName() ));
        }

       $oOutput->sendHeaders();


        $oOutput->output('content', $sOutput);

        $myConfig->pageClose();

        stopProfile('process');

        // stopping resource monitor
        $this->_stopMonitor( $oViewObject->getIsCallForCache(), $blIsCached, $sViewID, $oViewObject->getViewData() );

        // flush output (finalize)
        $oOutput->flushOutput();
    }

    /**
     * initialize and return view object
     *
     * @param string $sClass      view name
     * @param string $sFunction   function name
     * @param array  $aParams     parameters array
     * @param array  $aViewsChain array of views names that should be initialized also
     *
     * @return oxView
     */
    protected function _initializeViewObject($sClass, $sFunction, $aParams = null, $aViewsChain = null)
    {
        $myConfig = $this->getConfig();

        // creating current view object
        $oViewObject = oxNew( $sClass );

        // store this call
        $oViewObject->setClassName( $sClass );
        $oViewObject->setFncName( $sFunction );
        $oViewObject->setViewParameters( $aParams );

        $myConfig->setActiveView( $oViewObject );


        // init class
        $oViewObject->init();

        return $oViewObject;
    }

    /**
     * Check if method can be executed.
     *
     * @param object $oClass object to check if its method can be executed.
     * @param string $sFunction method to check if it can be executed.
     * @return bool
     */
    protected function _canExecuteFunction( $oClass, $sFunction )
    {
        $blCanExecute = true;

        if ( method_exists( $oClass, $sFunction ) ) {
            $oReflectionMethod = new ReflectionMethod( $oClass, $sFunction );
            if ( !$oReflectionMethod->isPublic() ) {
                $blCanExecute = false;
            }
        }
        return $blCanExecute;
    }


    /**
     * format error messages from _getErrors and return as array
     *
     * @param string $sControllerName a class name
     *
     * @return array
     */
    protected function _getFormattedErrors( $sControllerName )
    {
        $aErrors = $this->_getErrors( $sControllerName );
        $aFmtErrors = array();
        if ( is_array($aErrors) && count($aErrors) ) {
            foreach ( $aErrors as $sLocation => $aEx2 ) {
                foreach ( $aEx2 as $sKey => $oEr ) {
                    $oErr = unserialize( $oEr );
                    $aFmtErrors[$sLocation][$sKey] = $oErr->getOxMessage();
                }
            }
        }
        return $aFmtErrors;
    }

    /**
     * render oxView object
     *
     * @param oxView $oViewObject view object to render
     *
     * @return string
     */
    protected function _render($oViewObject)
    {
        // get Smarty is important here as it sets template directory correct
        $oSmarty = oxRegistry::get("oxUtilsView")->getSmarty();

        // render it
        $sTemplateName = $oViewObject->render();

        // check if template dir exists
        $sTemplateFile = $this->getConfig()->getTemplatePath( $sTemplateName, $this->isAdmin() ) ;
        if ( !file_exists( $sTemplateFile)) {

            $oEx = oxNew( 'oxSystemComponentException' );
            $oEx->setMessage( 'EXCEPTION_SYSTEMCOMPONENT_TEMPLATENOTFOUND' );
            $oEx->setComponent( $sTemplateName );

            $sTemplateName = "message/exception.tpl";

            if ( $this->_isDebugMode() ) {
                oxRegistry::get("oxUtilsView")->addErrorToDisplay( $oEx );
            }
            $oEx->debugOut();
        }

        // Output processing. This is useful for modules. As sometimes you may want to process output manually.
        $oOutput = $this->_getOutputManager();
        $aViewData = $oOutput->processViewArray( $oViewObject->getViewData(), $oViewObject->getClassName() );
        $oViewObject->setViewData( $aViewData );

        //add all exceptions to display
        $aErrors = $this->_getErrors( $oViewObject->getClassName() );
        if ( is_array($aErrors) && count($aErrors) ) {
            oxRegistry::get("oxUtilsView")->passAllErrorsToView( $aViewData, $aErrors );
        }

        foreach ( array_keys( $aViewData ) as $sViewName ) {
            $oSmarty->assign_by_ref( $sViewName, $aViewData[$sViewName] );
        }

        // passing current view object to smarty
        $oSmarty->oxobject = $oViewObject;


        $sOutput = $oSmarty->fetch( $sTemplateName, $oViewObject->getViewId() );

        //Output processing - useful for modules as sometimes you may want to process output manually.
        $sOutput = $oOutput->process( $sOutput, $oViewObject->getClassName() );
        return $oOutput->addVersionTags( $sOutput );
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
     * @param string $sCurrentControllerName a class name
     *
     * @return array
     */
    protected function _getErrors( $sCurrentControllerName )
    {
        if ( null === $this->_aErrors ) {
            $this->_aErrors = oxRegistry::getSession()->getVariable( 'Errors' );
            $this->_aControllerErrors = oxRegistry::getSession()->getVariable( 'ErrorController' );
            if ( null === $this->_aErrors ) {
                $this->_aErrors = array();
            }
            $this->_aAllErrors = $this->_aErrors;
        }
        // resetting errors of current controller or widget from session
        if ( is_array($this->_aControllerErrors) && !empty($this->_aControllerErrors) ) {
            foreach ( $this->_aControllerErrors as $sErrorName => $sControllerName ) {
                if ( $sControllerName == $sCurrentControllerName ) {
                    unset( $this->_aAllErrors[$sErrorName] );
                    unset( $this->_aControllerErrors[$sErrorName] );
                }
            }
        } else {
            $this->_aAllErrors = array();
        }
        oxRegistry::getSession()->setVariable( 'ErrorController', $this->_aControllerErrors );
        oxRegistry::getSession()->setVariable( 'Errors', $this->_aAllErrors );
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
        $myConfig = $this->getConfig();
        $blProductive = true;
        $blRunOnceExecuted = oxSession::getVar( 'blRunOnceExecuted' );

            $iErrorReporting = error_reporting();
            if ( defined( 'E_DEPRECATED' ) ) {
                // some 3rd party libraries still use deprecated functions
                $iErrorReporting = E_ALL ^ E_NOTICE ^ E_DEPRECATED;
            } else {
                $iErrorReporting = E_ALL ^ E_NOTICE;
            }
            // A. is it the right place for this code ?
            // productive mode ?
            if ( ! ( $blProductive = $myConfig->isProductiveMode() ) ) {
                if ( is_null($myConfig->getConfigParam( 'iDebug' )) ) {
                    $myConfig->setConfigParam( 'iDebug', -1 );
                }
            } else {
                // disable error logging if server is misconfigured
                // #2015 E_NONE replaced with 0
                if ( !ini_get( 'log_errors' ) ) {
                    $iErrorReporting = 0;
                }
            }
            error_reporting($iErrorReporting);


        if ( !$blRunOnceExecuted && !$this->isAdmin() && $blProductive ) {

            $sTpl = false;
            // perform stuff - check if setup is still there
            if ( file_exists( $myConfig->getConfigParam( 'sShopDir' ) . '/setup/index.php' ) ) {
                $sTpl = 'message/err_setup.tpl';
            }

            if ( $sTpl ) {
                $oActView = oxNew( 'oxubase' );
                $oSmarty = oxRegistry::get("oxUtilsView")->getSmarty();
                $oSmarty->assign('oView', $oActView );
                $oSmarty->assign('oViewConf', $oActView->getViewConfig() );
                oxRegistry::getUtils()->showMessageAndExit( $oSmarty->fetch( $sTpl ) );
            }

            oxSession::setVar( 'blRunOnceExecuted', true );
        }
    }

    /**
     * Checks if shop is in debug mode
     *
     * @return bool
     */
    protected function _isDebugMode()
    {
        if ( OxRegistry::get("OxConfigFile")->getVar('iDebug') ) {
            return true;
        }

        return false;
    }


    /**
     * Shows exceptionError page.
     * possible reason: class does not exist etc. --> just redirect to start page.
     *
     * @param $oEx
     */
    protected function _handleSystemException( $oEx )
    {
        //possible reason: class does not exist etc. --> just redirect to start page
        if ( $this->_isDebugMode() ) {
            oxRegistry::get("oxUtilsView")->addErrorToDisplay( $oEx );
            $this->_process( 'exceptionError', 'displayExceptionError' );
        }
        $oEx->debugOut();

        $myConfig = $this->getConfig();
        if ( !$myConfig->getConfigParam( 'iDebug' ) ) {
            oxRegistry::getUtils()->redirect( $myConfig->getShopHomeUrl() .'cl=start', true, 302 );
        }
    }

    /**
     * Redirect to start page, in debug mode shows error message.
     *
     * @param $oEx
     */
    protected function _handleCookieException( $oEx )
    {
        if ( $this->_isDebugMode() ) {
            oxRegistry::get("oxUtilsView")->addErrorToDisplay( $oEx );
        }
        oxRegistry::getUtils()->redirect( $this->getConfig()->getShopHomeUrl() .'cl=start', true, 302 );
    }

    /**
     * R&R handling -> redirect to error msg, also, can call _process again, specifying error handler view class.
     *
     * @param $oEx
     */
    protected function _handleAccessRightsException( $oEx )
    {
        oxRegistry::getUtils()->redirect( $this->getConfig()->getShopHomeUrl() .'cl=content&tpl=err_accessdenied.tpl', true, 302 );
    }

    /**
     * Shows exception message if debug mode is enabled, redirects otherwise.
     *
     * @param oxConnectionException $oEx message to show on exit
     */
    protected function _handleDbConnectionException( $oEx )
    {
        $oEx->debugOut();
        if ( $this->_isDebugMode() ) {
            oxUtils::getInstance()->showMessageAndExit( $oEx->getString() );
        } else {
            header( "HTTP/1.1 500 Internal Server Error");
            header( "Location: offline.html");
            header( "Connection: close");
        }
    }

    /**
     * Catching other not cought exceptions.
     *
     * @param oxException $oEx
     */
    protected function _handleBaseException( $oEx )
    {
        $oEx->debugOut();
        if ( $this->_isDebugMode() ) {
            oxRegistry::get("oxUtilsView")->addErrorToDisplay( $oEx );
            $this->_process( 'exceptionError', 'displayExceptionError' );
        }
    }

    /**
     * Returns controller class which should be loaded.
     *
     * @param string $sClass
     * 
     * @return string
     */
    protected function _getControllerToLoad( $sClass = null )
    {
        $oConfig = $this->getConfig();
        $sClass = ( isset( $sClass ) ) ? $sClass : oxRegistry::getConfig()->getRequestParameter( 'cl' );
        if ( !$sClass ) {

            if ( !$this->isAdmin() ) {

                // first start of the shop
                // check wether we have to display mall startscreen or not
                if ( $oConfig->isMall() ) {

                    $iShopCount = oxDb::getDb()->getOne( 'select count(*) from oxshops where oxactive = 1' );

                    $sMallShopURL = $oConfig->getConfigParam( 'sMallShopURL' );
                    if ( $iShopCount && $iShopCount > 1 && $oConfig->getConfigParam( 'iMallMode' ) != 0 && !$sMallShopURL ) {
                        // no class specified so we need to change back to baseshop
                        $sClass = 'mallstart';
                    }
                }

                if ( !$sClass ) {
                    $sClass = 'start';
                }
            } else {
                $sClass = 'login';
            }

            oxRegistry::getSession()->setVariable( 'cl', $sClass );
        }

        return $sClass;
    }
}
