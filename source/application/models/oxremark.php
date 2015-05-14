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
 * Remark manager.
 *
 */
class oxRemark extends oxBase
{

    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxremark';

    /**
     * Skip update fields
     *
     * @var array
     */
    protected $_aSkipSaveFields = array('oxtimestamp');

    /**
     * Class constructor, initiates parent constructor (parent::oxBase()).
     *
     * @return null
     */
    public function __construct()
    {
        parent::__construct();
        $this->init('oxremark');
    }

    /**
     * Loads object information from DB. Returns true on success.
     *
     * @param string $oxID ID of object to load
     *
     * @return bool
     */
    public function load($oxID)
    {
        if ($blRet = parent::load($oxID)) {
            // convert date's to international format
            $this->oxremark__oxcreate = new oxField(oxRegistry::get("oxUtilsDate")->formatDBDate($this->oxremark__oxcreate->value), oxField::T_RAW);
        }

        return $blRet;
    }

    /**
     * Inserts object data fields in DB. Returns true on success.
     *
     * @return bool
     */
    protected function _insert()
    {
        // set oxcreate
        $sNow = date('Y-m-d H:i:s', oxRegistry::get("oxUtilsDate")->getTime());
        $this->oxremark__oxcreate = new oxField($sNow, oxField::T_RAW);
        $this->oxremark__oxheader = new oxField($sNow, oxField::T_RAW);

        return parent::_insert();
    }
}
