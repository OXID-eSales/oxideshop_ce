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
 * Shop meta data manager.
 *
 * @package core
 */
class oxShopMetaData extends oxSuperCfg
{
    /**
     * oxShopMetaData class instance.
     *
     * @var oxutils instance
     */
    private static $_instance = null;

    /**
     * Shop field set size, limited to 64bit by MySQL
     *
     * @var int
     */
    const SHOP_FIELD_SET_SIZE = 64;

    /**
     * resturns a single instance of this class
     *
     * @deprecated since v5.0 (2012-08-10); Use oxRegistry::get("oxShopMetaData") instead
     *
     * @return oxShopMetaData
     */
    public static function getInstance()
    {
        return oxRegistry::get("oxShopMetadata");
    }

    /**
     * Returns integer number with bit set according to $iShopId.
     * The action performed could be represented as pow(2, $iShopId - 1)
     * We use mySQL to calculate that, as currently php int size is only 32 bit.
     *
     * @param int $iShopId current shop id
     *
     * @return int
     */
    public function getShopBit( $iShopId )
    {
        // get shop field set id for shop field set
        $iShopId = $this->_getShopFieldSetId((int) $iShopId);

        //this works for large numbers when $sShopNr is up to (inclusive) 128
        $iRes = oxDb::getDb()->getOne( "select 1 << ( $iShopId - 1 ) as shopbit" );

        //as php ints supports only 32 bits, we return string.
        return $iRes;
    }

    /**
     * Returns array of shop bits
     *
     * @param int $iShopId current shop id
     *
     * @return array[int]int
     */
    public function getShopBits( $iShopId )
    {
        $aShopBits = array();
        $iFieldCount = $this->_getShopFieldCount();
        if ($iFieldCount) {
            // Fill array with 0 values
            $aShopBits = array_fill(0, $iFieldCount, 0);
            // Calculate shop bit for current field set
            $aShopBits[$this->getShopFieldSet($iShopId)] = $this->getShopBit($iShopId);
        }
        return $aShopBits;
    }

    /**
     * Returns array filled with max 64bit integers
     *
     * @return array[int]int
     */
    public function getMultiShopBits()
    {
        $aShopBits = array();
        $iFieldCount = $this->_getShopFieldCount();
        if ($iFieldCount) {
            // Fill array with max 64bit int values
            $aShopBits = array_fill(0, $iFieldCount, MAX_64BIT_INTEGER);
        }
        return $aShopBits;
    }

    /**
     * Returns shop sets bit of inherited shop up to uninherited parent
     *
     * @param string $sShopId       current shop id
     * @param bool   $blIsInherited if this table is inherited
     *
     * @return int
     */
    public function getParentShopBits( $sShopId, $blIsInherited = null )
    {
        $iCnt  = 0;
        $iMax  = $this->_getMaxShopId();
        $aShopSetBits = array();
        do {
            //collects inherited shop set bit array up to uninherited parent.
            $aInheritedShops = array();
            $sQ = 'select oxid, oxparentid, oxisinherited from oxshops where oxid = "'.$sShopId.'" ';
            $rs = oxDb::getDb()->select( $sQ );
            if ( $rs && $rs->recordCount()> 0 && $rs->fields[0] ) {
                $iOXID     = $rs->fields[0];
                $sParentID = $rs->fields[1];

                //the default value is taking from the shop settings
                if ( is_null($blIsInherited) ) {
                    $blIsInherited = $rs->fields[2];
                }
                $aShopSetBits[$this->getShopFieldSet($iOXID)][] = $this->getShopBit($iOXID);
            }
            $sShopId = $sParentID;

        } while ( ( $blIsInherited && $sParentID ) && $iCnt++ < $iMax );

        $aFieldSets = $this->_combineShopSetBits($aShopSetBits);
        return $aFieldSets;
    }

    /**
     * Return true if specified table record is included in gives shop
     *
     * @param int    $iShopId shop id
     * @param string $sTable  mall table name
     * @param string $sOXID   record id
     *
     * @return bool
     */
    public function isIncludedInShop($iShopId, $sTable, $sOXID)
    {
        $oDb      = oxDb::getDb();
        $sField   = $this->getShopFieldName('oxshopincl', $iShopId);
        $iShopBit = $this->getShopBit($iShopId);
        $sSelect  = "select ({$iShopBit} & {$sField}) as isIncluded from {$sTable} where oxid = ".$oDb->quote( $sOXID );

        return (bool)  $oDb->getOne( $sSelect );
    }

    /**
     * Return true if specified table record is excluded from gives shop
     *
     * @param int    $iShopId shop id
     * @param string $sTable  mall table name
     * @param string $sOXID   record id
     *
     * @return bool
     */
    public function isExcludedFromShop($iShopId, $sTable, $sOXID)
    {
        $oDb      = oxDb::getDb();
        $sField   = $this->getShopFieldName('oxshopexcl', $iShopId);
        $iShopBit = $this->getShopBit($iShopId);
        $sSelect  = "select ({$iShopBit} & {$sField}) as isExcluded from {$sTable} where oxid = ".$oDb->quote( $sOXID );

        return (bool)  $oDb->getOne( $sSelect );
    }

    /**
     * Returns shop field offset
     *
     * @param int $iShopId Shop ID
     *
     * @return int
     */
    public function getShopFieldSet($iShopId)
    {
        return ceil($iShopId / self::SHOP_FIELD_SET_SIZE)-1;
    }

    /**
     * Returns all shop field sets (oxshopincl, oxshopexcl, oxshopincl1, oxshopexcl2, ...)
     *
     * @return array
     */
    public function getShopFields()
    {
        $iCount = $this->_getShopFieldCount();
        $aFields = array();
        for ($iSet=0; $iSet<$iCount; $iSet++) {
            $aFields[] = $this->getShopFieldSetName('oxshopincl', $iSet);
            $aFields[] = $this->getShopFieldSetName('oxshopexcl', $iSet);
        }
        return $aFields;
    }



    /**
     * Returns true if shop field sets (oxshopincl, oxshopexcl) exist for given table
     *
     * @param int $iShopId current shop id
     * @param int $sTable  table name (default 'oxarticles')
     *
     * @return bool
     */
    public function shopFieldSetExist( $iShopId, $sTable = 'oxarticles')
    {
        $blExists = true;
        $sFieldSet = $this->getShopFieldSet($iShopId);
        if ( $sFieldSet > 0 ) {
            $sFieldName = "oxshopincl".$sFieldSet;
            $oDbMetadata = oxNew('oxDbMetaDataHandler');
            $blExists = $oDbMetadata->tableExists( $sTable ) && $oDbMetadata->fieldExists( $sFieldName, $sTable );
        }
        return $blExists;
    }

    /**
     * Returns array of shop bits
     *
     * @param int $iShopId current shop id
     *
     * @return bool
     */
    public function addShopFieldSets( $iShopId )
    {
        set_time_limit(0);
        $aSql = array();
        $aMultiShopTables = $this->getConfig()->getConfigParam( 'aMultiShopTables' );
        $iFieldSet = $this->getShopFieldSet($iShopId);
        $oDbMetadata = oxNew('oxDbMetaDataHandler');

        foreach ( $aMultiShopTables as $sTable ) {
            foreach ( array( "OXSHOPINCL", "OXSHOPEXCL" ) as $sField ) {
                $sNewFieldName = $sField . $iFieldSet;
                if ($iFieldSet > 1) {
                    $iPrevLang = $iFieldSet-1;
                    $sPrevField = $sField.$iPrevLang;
                } else {
                    $sPrevField = $sField;
                }
                if ( !$oDbMetadata->fieldExists( $sNewFieldName, $sTable ) ) {

                    //getting add field sql
                    $aSql[] = $oDbMetadata->getAddFieldSql( $sTable, $sField, $sNewFieldName, $sPrevField );


                    //getting add index sql on added field
                    $aSql = array_merge($aSql, (array) $oDbMetadata->getAddFieldIndexSql($sTable, $sField, $sNewFieldName));
                }
            }
        }
        $oDbMetadata->executeSql($aSql);
    }

    /**
     * Gets shop field setsuffix and optionally appends ti to given field name.
     *
     * @param int $iSet Shop field set index
     *
     * @return string
     */
    public function getShopFieldSetSuffix( $iSet )
    {
        $sSuffix = ( $iSet > 0 ) ? $iSet : '';
        return $sSuffix;
    }

    /**
     * Returns shop field (empty when calculated shop field set is zero).
     *
     * @param int $iShopId Shop ID
     *
     * @return string
     */
    public function getShopFieldSuffix( $iShopId )
    {
        $iSet = $this->getShopFieldSet( $iShopId );
        return $this->getShopFieldSetSuffix($iSet);
    }

    /**
     * Gets shop field setsuffix and optionally appends ti to given field name.
     *
     * @param string $sField Field name
     * @param int    $iSet   Shop field set index
     *
     * @return string
     */
    public function getShopFieldSetName( $sField, $iSet )
    {
        $sSuffix = $this->getShopFieldSetSuffix($iSet);
        return $sField.$sSuffix;
    }

    /**
     * Gets shop field suffix and optionally appends ti to given field name.
     *
     * @param string $sField  Field name
     * @param int    $iShopId Shop ID
     *
     * @return string
     */
    public function getShopFieldName( $sField, $iShopId )
    {
        $iSet = $this->getShopFieldSet( $iShopId );
        return $this->getShopFieldSetName($sField, $iSet);
    }

    /**
     * Returns SQL snippet for setting oxshopincl fields
     *
     * @param int $iShopId shop ID
     *
     * @return string
     *
     */
    public function getSqlSetIncludeSnippet( $iShopId )
    {
        $iBit     = $this->getShopBit( $iShopId );
        $sField   = $this->getShopFieldName( 'oxshopincl', $iShopId);
        $sSnippet = "{$sField} = {$sField} | $iBit";

        return $sSnippet;
    }

    /**
     * Returns SQL snippet for setting oxshopincl fields
     *
     * @param int $iShopId shop ID
     *
     * @return string
     *
     */
    public function getSqlUnsetIncludeSnippet( $iShopId )
    {
        $iBit     = $this->getShopBit( $iShopId );
        $sField   = $this->getShopFieldName( 'oxshopincl', $iShopId );
        $sSnippet = "{$sField} = {$sField} & ~{$iBit}";

        return $sSnippet;
    }

    /**
     * Returns SQL snippet for setting oxshopexcl fields
     *
     * @param int $iShopId shop ID
     *
     * @return string
     *
     */
    public function getSqlSetExcludeSnippet( $iShopId )
    {
        $iBit     = $this->getShopBit( $iShopId );
        $sField   = $this->getShopFieldName( 'oxshopexcl', $iShopId );
        $sSnippet = "{$sField} = {$sField} | $iBit";

        return $sSnippet;
    }

    /**
     * Returns SQL snippet for unsetting oxshopexcl fields
     *
     * @param int $iShopId shop ID
     *
     * @return string
     *
     */
    public function getSqlUnsetExcludeSnippet( $iShopId )
    {
        $iBit     = $this->getShopBit( $iShopId );
        $iField   = $this->getShopFieldName( 'oxshopexcl', $iShopId);
        $sSnippet = "{$iField} = {$iField} & ~{$iBit}";

        return $sSnippet;
    }

   /**
     * Returns shop field offset
     *
     * @param int $iShopId Shop ID
     *
     * @return int
     */
    protected function _getShopFieldSetId($iShopId)
    {
        return 1 + (($iShopId - 1) % self::SHOP_FIELD_SET_SIZE);
    }

    /**
     * Returns shop field count
     *
     * @return int
     */
    protected function _getShopFieldCount()
    {
        return 1 + $this->getShopFieldSet($this->_getMaxShopId());
    }

    /**
     * Returns maaximum existing shop id
     *
     * @return int
     */
    protected function _getMaxShopId()
    {
        return oxDb::getDb()->getOne( "select max(oxid) as maxid from oxshops" );
    }

    /**
     * Combines shop set bit arrays to one bit using binary OR.
     * We use mySQL to calculate that, as currently php integer size is only 32 bits.
     *
     * @param array $aShopSetBits shop id bits
     *
     * @return array
     */
    protected function _combineShopSetBits( $aShopSetBits )
    {
        $aFieldSets = array_fill(0, $this->_getShopFieldCount(), 0);
        foreach ( $aShopSetBits as $iShopSet => $aBits ) {
            //this works for large numbers when $sShopNr is up to (inclusive) 64
            $iRes = oxDb::getDb()->getOne( "select (".implode(" | ", $aBits).") as bitwiseOr" );
            $aFieldSets[$iShopSet] = $iRes;
        }

        //for more than 64 shop, we return array.
        return $aFieldSets;
    }
}
