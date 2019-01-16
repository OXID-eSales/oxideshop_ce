<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Utility;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\EshopCommunity\Internal\Application\Utility\BasicContext;

/**
 * @internal
 */
class Context extends BasicContext implements ContextInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * Context constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getEnvironment(): string
    {
        return 'prod';
    }

    /**
     * @return string
     */
    public function getLogLevel(): string
    {
        return $this->getConfigParameter('sLogLevel', '');
    }

    /**
     * @return string
     */
    public function getLogFilePath(): string
    {
        return $this->config->getLogsDir() . 'oxideshop.log';
    }

    /**
     * @return array
     */
    public function getRequiredContactFormFields(): array
    {
        return $this->getConfigParameter('contactFormRequiredFields', []);
    }

    /**
     * @return int
     */
    public function getCurrentShopId(): int
    {
        return $this->config->getShopId();
    }

    /**
     * @return array
     */
    public function getAllShopIds(): array
    {
        return $this->config->getShopIds();
    }

    /**
     * @return string
     */
    public function getContainerCacheFile(): string
    {
        return $this->getConfigParameter('sCompileDir') . DIRECTORY_SEPARATOR . 'containercache.php';
    }

    /**
     * @return string
     */
    public function getConfigurationEncryptionKey(): string
    {
        return $this->getConfigParameter('sConfigKey');
    }

    /**
     * @param string $name
     * @param null   $default
     *
     * @return mixed
     */
    private function getConfigParameter($name, $default = null)
    {
        return $this->config->getConfigParam($name, $default);
    }
}
