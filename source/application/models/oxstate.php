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
 * State handler
 */
class oxState extends oxI18n
{

    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxstate';

    /**
     * Class constructor, initiates parent constructor (parent::oxI18n()).
     */
    public function __construct()
    {
        parent::__construct();
        $this->init("oxstates");
    }

    /**
     * Returns country id by code
     *
     * @param string $sCode      country code
     * @param string $sCountryId country id
     *
     * @return string
     */
    public function getIdByCode($sCode, $sCountryId)
    {
        $oDb = oxDb::getDb();

        return $oDb->getOne(
            "SELECT oxid FROM oxstates WHERE oxisoalpha2 = " . $oDb->quote(
                $sCode
            ) . " AND oxcountryid = " . $oDb->quote($sCountryId)
        );
    }

    /**
     * Get state title by id
     *
     * @param integer|string $iStateId
     *
     * @return string
     */
    public function getTitleById($iStateId)
    {
        $oDb = oxDb::getDb();
        $sQ = "SELECT oxtitle FROM " . getViewName("oxstates") . " WHERE oxid = " . $oDb->quote($iStateId);
        $sStateTitle = $oDb->getOne($sQ);

        return (string) $sStateTitle;
    }
}
