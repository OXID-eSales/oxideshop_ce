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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

/**
 * @package core
 */
class oxObject2Group extends oxBase
{
    /**
     * Load the relation even if from other shop
     *
     * @var boolean
     */
    protected $_blDisableShopCheck = true;

    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxobject2group';

    /**
     * Class constructor, initiates parent constructor (parent::oxBase()).
     */
    public function __construct()
    {
        parent::__construct();
        $this->init( 'oxobject2group' );
        $this->oxobject2group__oxshopid = new oxField($this->getConfig()->getShopId(), oxField::T_RAW);
    }

    /**
     * extens the default save method
     *
     * @return mixed
     */
    public function save()
    {
        $oDb = oxDb::getDb();
        $sQ  = "select 1 from oxobject2group where oxgroupsid = ".$oDb->quote( $this->oxobject2group__oxgroupsid->value );
        $sQ .= " and oxobjectid = ". $oDb->quote( $this->oxobject2group__oxobjectid->value );

        // does not exist
        if ( !$oDb->getOne( $sQ, false, false ) ) {
            return parent::save();
        }
    }
}
