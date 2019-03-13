<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Utility;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\EshopCommunity\Core\DatabaseProvider;
use OxidEsales\EshopCommunity\Internal\Application\Utility\BasicContext;
use PDO;

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
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getLogLevel()
    {
        return $this->getConfigParameter('sLogLevel');
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
        $contactFormRequiredFields = $this->getConfigParameter('contactFormRequiredFields');

        return $contactFormRequiredFields === null ? [] : $contactFormRequiredFields;
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
        $integerShopIds = [];

        foreach ($this->config->getShopIds() as $shopId) {
            $integerShopIds[] = (int) $shopId;
        }

        return $integerShopIds;
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
        $value = $this->config->getConfigParam($name, $default);
        DatabaseProvider::getDb()->setFetchMode(PDO::FETCH_ASSOC);
        return $value;
    }
}
