<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\Utility;

use OxidEsales\EshopCommunity\Core\DatabaseProvider;
use PDO;
use Psr\Log\LogLevel;
use Webmozart\PathUtil\Path;
use OxidEsales\Facts\Config\ConfigFile as FactsConfigFile;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\Exception\AdminUserNotFoundException;

class Context extends BasicContext implements ContextInterface
{
    /**
     * @var FactsConfigFile
     */
    private $factsConfigFile;

    /**
     * @return string
     */
    public function getLogLevel(): string
    {
        return $this->getConfigParameter('sLogLevel') ?? LogLevel::ERROR;
    }

    /**
     * @return string
     */
    public function getLogFilePath(): string
    {
        return Path::join(Registry::getConfig()->getLogsDir(), 'oxideshop.log');
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
        return Registry::getConfig()->getShopId();
    }

    /**
     * @return array
     */
    public function getAllShopIds(): array
    {
        $integerShopIds = [];

        foreach (Registry::getConfig()->getShopIds() as $shopId) {
            $integerShopIds[] = (int) $shopId;
        }

        return $integerShopIds;
    }

    /**
     * @return string
     */
    public function getConfigurationEncryptionKey(): string
    {
        return $this->getConfigParameter('sConfigKey');
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->isConfigLoaded() ? Registry::getConfig()->isAdmin() : isAdmin();
    }

    /**
     * @return bool
     */
    public function isEnabledAdminQueryLog(): bool
    {
        return (bool) $this->getFactsConfigFile()->getVar('blLogChangesInAdmin');
    }

    /**
     * @return string
     */
    public function getAdminLogFilePath(): string
    {
        return Path::join($this->getSourcePath(), 'log', 'oxadmin.log');
    }

    /**
     * We need to be careful when trying to fetch config parameters in this place as the
     * shop might still be bootstrapping.
     * The config must be already initialized before we can safely call Config::getConfigParam().
     *
     * @return array
     */
    public function getSkipLogTags(): array
    {
        $skipLogTags = [];
        if ($this->isConfigLoaded()) {
            $skipLogTags = Registry::getConfig()->getConfigParam('aLogSkipTags');
        }

        return (array) $skipLogTags;
    }

    /**
     * @return string
     */
    public function getAdminUserId(): string
    {
        $adminUserId = (string) Registry::getSession()->getVariable('auth');
        if (empty($adminUserId)) {
            throw new AdminUserNotFoundException();
        }

        return $adminUserId;
    }

    /**
     * @param string $name
     * @param null   $default
     *
     * @return mixed
     */
    private function getConfigParameter($name, $default = null)
    {
        $value = Registry::getConfig()->getConfigParam($name, $default);
        DatabaseProvider::getDb()->setFetchMode(PDO::FETCH_ASSOC);
        return $value;
    }

    /**
     * @return FactsConfigFile
     */
    private function getFactsConfigFile(): FactsConfigFile
    {
        if (!is_a($this->factsConfigFile, FactsConfigFile::class)) {
            $this->factsConfigFile = new FactsConfigFile();
        }

        return $this->factsConfigFile;
    }

    /**
     * @return bool
     */
    private function isConfigLoaded(): bool
    {
        return Registry::instanceExists(Config::class);
    }
}
