<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

use Doctrine\DBAL\DriverManager;
use OxidEsales\EshopCommunity\Internal\Framework\Database\ConnectionParameterProvider;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;

use function array_fill_keys;
use function array_filter;
use function array_merge;
use function is_array;
use function unserialize;

class ShopIdCalculator
{
    public const BASE_SHOP_ID = 1;
    private array $urlMap;

    public function __construct(
        private readonly \OxidEsales\Eshop\Core\UtilsServer $utilsServer,
    ) {
    }

    public function getShopId(): int
    {
        return self::BASE_SHOP_ID;
    }

    protected function getShopUrlMap(): array
    {
        if (isset($this->urlMap)) {
            return $this->urlMap;
        }

        $urlMap = [];
        foreach ($this->fetchUrlsFromConfigTable() as $row) {
            $shopId = (int)$row['oxshopid'];
            $variableName = $row['oxvarname'];
            $urlValues = $row['oxvarvalue'];

            if ($variableName === 'aLanguageURLs' || $variableName === 'aLanguageSSLURLs') {
                $urls = unserialize($urlValues, ['allowed_classes' => false]);
                if (is_array($urls) && count($urls)) {
                    $urls = array_filter($urls);
                    $urls = array_fill_keys($urls, $shopId);
                    $urlMap = array_merge($urlMap, $urls);
                }
            } elseif ($urlValues) {
                $urlMap[$urlValues] = $shopId;
            }
        }
        $this->urlMap = $urlMap;

        return $urlMap;
    }

    private function fetchUrlsFromConfigTable(): array
    {
        $connection = DriverManager::getConnection(
            (new ConnectionParameterProvider(new BasicContext()))->getParameters()
        );
        $statement = $connection
            ->prepare(
                "SELECT oxshopid, oxvarname, oxvarvalue
                FROM oxconfig
                WHERE oxvarname IN ('aLanguageURLs', 'aLanguageSSLURLs', 'sMallShopURL','sMallSSLShopURL')"
            );

        return $statement->executeQuery()->fetchAllAssociative();
    }

    protected function getUtilsServer(): \OxidEsales\Eshop\Core\UtilsServer
    {
        return $this->utilsServer;
    }
}