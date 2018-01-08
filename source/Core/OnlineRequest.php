<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * Online check base request class.
 *
 * @internal Do not make a module extension for this class.
 * @see      https://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 *
 * @ignore   This class will not be included in documentation.
 */
class OnlineRequest
{
    /**
     * OXID eShop servers cluster id.
     *
     * @var string
     */
    public $clusterId;

    /**
     * OXID eShop edition.
     *
     * @var string
     */
    public $edition;

    /**
     * Shops version number.
     *
     * @var string
     */
    public $version;

    /**
     * @var string
     */
    public $shopUrl;

    /**
     * Web service protocol version.
     *
     * @var string
     */
    public $pVersion;

    /**
     * Product ID. Intended for possible partner modules in future.
     *
     * @var string
     */
    public $productId = 'eShop';

    /**
     * Class constructor, initiates public class parameters.
     */
    public function __construct()
    {
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $this->clusterId = $this->_getClusterId();
        $this->edition = $oConfig->getEdition();
        $this->version = ShopVersion::getVersion();
        $this->shopUrl = $oConfig->getShopUrl();
    }

    /**
     * Returns cluster id.
     * Takes cluster id from configuration if set, otherwise generates it.
     *
     * @return string
     */
    private function _getClusterId()
    {
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $sBaseShop = $oConfig->getBaseShopId();
        $sClusterId = $oConfig->getShopConfVar('sClusterId', $sBaseShop);
        if (!$sClusterId) {
            $oUUIDGenerator = oxNew(\OxidEsales\Eshop\Core\UniversallyUniqueIdGenerator::class);
            $sClusterId = $oUUIDGenerator->generate();
            $oConfig->saveShopConfVar("str", 'sClusterId', $sClusterId, $sBaseShop);
        }

        return $sClusterId;
    }
}
