<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Exception;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Logger\LoggerServiceFactory;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\Context;

/**
 * Exception handler, deals with all high level exceptions (caught in oxShopControl)
 */
class ExceptionHandler
{
    /**
     * Shop debug
     *
     * @deprecated since v6.3 (2018-04-25); This functionality will be removed completely. Use an appropriate Monolog channel in the future.
     *
     * @var integer
     */
    protected $_iDebug = 0;

    /**
     * Class constructor
     *
     * @param integer $iDebug debug level
     */
    public function __construct($iDebug = 0)
    {
        $this->_iDebug = (int) $iDebug;
    }

    /**
     * Handler for uncaught exceptions. As this is the las resort no fancy business logic should be applied here.
     *
     * @param \Throwable $exception exception object
     *
     * @throws \Throwable
     **/
    public function handleUncaughtException(\Throwable $exception)
    {
        try {
            Registry::getLogger()->error(
                $exception->getMessage(),
                [$exception]
            );
        } catch (\Throwable $loggerException) {
            /**
             * Its not possible to get the logger from the DI container.
             * Try again to log original exception (without DI container) in order to show the root cause of a problem.
             */
            $loggerServiceFactory = new LoggerServiceFactory(new Context());
            $logger = $loggerServiceFactory->getLogger();
            $logger->error($exception);
        }

        if ($this->_iDebug || defined('OXID_PHP_UNIT') || php_sapi_name() === 'cli') {
            throw $exception;
        } else {
            \oxTriggerOfflinePageDisplay();
            $this->exitApplication();
        }
    }

    /**
     * @deprecated method will be removed in next major, use handleUncaughtException directly
     */
    public function handleDatabaseException(\OxidEsales\Eshop\Core\Exception\DatabaseException $exception)
    {
        $this->handleUncaughtException($exception);
    }


    /**
     * @deprecated method will be removed in next major, use PHP function directly
     */
    protected function exitApplication()
    {
        exit(1);
    }
}
