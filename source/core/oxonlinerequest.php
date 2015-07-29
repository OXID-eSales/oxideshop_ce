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
 * @copyright (C) OXID eSales AG 2003-2015
 * @version   OXID eShop CE
 */

/**
 * Online check base request class.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://wiki.oxidforge.org/Tutorials/Core_OXID_eShop_classes:_must_not_be_extended
 *
 * @ignore   This class will not be included in documentation.
 */
class oxOnlineRequest
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
        $oConfig = oxRegistry::getConfig();
        $this->clusterId = $this->_getClusterId();
        $this->edition = $oConfig->getEdition();
        $this->version = $oConfig->getVersion();
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
        $oConfig = oxRegistry::getConfig();
        $sBaseShop = $oConfig->getBaseShopId();
        $sClusterId = $oConfig->getShopConfVar('sClusterId', $sBaseShop);
        if (!$sClusterId) {
            $oUUIDGenerator = oxNew('oxUniversallyUniqueIdGenerator');
            $sClusterId = $oUUIDGenerator->generate();
            $oConfig->saveShopConfVar("str", 'sClusterId', $sClusterId, $sBaseShop);
        }

        return $sClusterId;
    }
}
