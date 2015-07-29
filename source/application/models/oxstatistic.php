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
 * Statistics manager.
 */
class oxStatistic extends oxBase
{

    /**
     * @var string Name of current class
     */
    protected $_sClassName = 'oxstatistic';

    /**
     * Class constructor, initiates paren constructor (parent::oxBase()).
     */
    public function __construct()
    {
        parent::__construct();
        $this->init('oxstatistics');
    }

    /**
     * Sets reports array to current statistics object
     *
     * @param array $aVal array of reports to set in current statistics object
     */
    public function setReports($aVal)
    {
        $this->oxstatistics__oxvalue = new oxField(serialize($aVal), oxField::T_RAW);
    }

    /**
     * Returns array of reports assigned to current statistics object
     *
     * @return array
     */
    public function getReports()
    {
        return unserialize($this->oxstatistics__oxvalue->value);
    }

    /**
     * Sets data field value
     *
     * @param string $sFieldName index OR name (eg. 'oxarticles__oxtitle') of a data field to set
     * @param string $sValue     value of data field
     * @param int    $iDataType  field type
     *
     * @return null
     */
    protected function _setFieldData($sFieldName, $sValue, $iDataType = oxField::T_TEXT)
    {
        if ('oxvalue' === $sFieldName) {
            $iDataType = oxField::T_RAW;
        }

        return parent::_setFieldData($sFieldName, $sValue, $iDataType);
    }
}
