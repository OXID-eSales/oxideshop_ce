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
 * Class handeling csv import
 * @package core
 */
class oxErpCsv extends oxERPBase
{
    /**
     * Supported versions by current interface
     * @var array
     */
    protected $_aSupportedVersions = array("0.1", "1.0", "1.1", "2.0");

    /**
     * Csv importer version mapping data
     * @var string
     */
    protected $_aCsv2BaseVersionsMap = array("0.1" => "1", "1.0" => "1", "1.1"=>"1.1", "2.0" => "2");

    /**
     * version of the file which is imported right now
     * @var string
     */
    protected $_sCurrVersion = "";

    /**
     * Imported data array
     * @var array
     */
    protected $_aData = array();

    /**
     * count of import rows
     * @var int
     */
    protected $_iRetryRows = 0;

    /**
     * Return message after import
     * @var string
     */
    protected $_sReturn;

    /**
     * Import file location
     * @var string
     */
    protected $_sPath;

    /**
     * Imported Actions to Article data array
     * @var array
     */
    protected $_aImportedActions2Article = array();

    /**
     * Imported Object to Category data array
     * @var array
     */
    protected $_aImportedObject2Category = array();

    /**
     * Imported Accessoire to Article data array
     * @var array
     */
    protected $_aImportedAccessoire2Article = array();

    /**
     * Before import event (abstract function)
     *
     * @param string $sType import type
     *
     * @return null
     */
    protected function _beforeExport($sType)
    {
    }

    /**
     * After import event (abstract function)
     *
     * @param string $sType import type
     *
     * @return null
     */
    protected function _afterExport($sType)
    {
    }

    /**
     * Import type getter (abstract function)
     *
     * @param array &$aData import data
     *
     * @return null
     */
    protected function _getImportType( &$aData )
    {
    }

    /**
     * Import mode getter (abstract function)
     *
     * @param array $aData import data
     *
     * @return null
     */
    protected function _getImportMode( $aData )
    {
    }

    /**
     * Data modifier (abstract function)
     *
     * @param object $aData data to modify
     * @param object $oType type object
     *
     * @return null
     */
    protected function _modifyData($aData, $oType)
    {
    }

    /**
     * Session loader (abstract function)
     *
     * @param object $sSessionID session identifier
     *
     * @return null
     */
    public function loadSessionData( $sSessionID )
    {
    }

    /**
     * parses and replaces special chars
     *
     * @param string $sText  input text
     * @param bool   $blMode true = Text2CSV, false = CSV2Text
     *
     * @return string
     */
    protected function _csvTextConvert( $sText, $blMode )
    {
        $aSearch  = array( chr(13), chr(10), '\'', '"' );
        $aReplace = array( '&#13;', '&#10;', '&#39;', '&#34;' );

        if ( $blMode ) {
            $sText = str_replace( $aSearch, $aReplace, $sText );
        } else {
            $sText = str_replace( $aReplace, $aSearch, $sText );
        }

        return $sText;
    }

    /**
     * Performs import action
     *
     * @return null
     */
    public function import()
    {
        $this->_beforeImport();

        do {
            while( $this->_importOne() );
        } while ( !$this->_afterImport() );
    }

    /**
     * Performs before import actions
     *
     * @return null
     */
    protected function _beforeImport()
    {
        if ( !$this->_iRetryRows ) {
            //convert all text
            foreach ($this->_aData as $key => $value) {
                $this->_aData[$key] = $this->_csvTextConvert($value, false);
            }
        }

    }

    /**
     * Performs after import actions
     *
     * @return bool
     */
    protected function _afterImport()
    {
        //check if there have been no errors or failures
        $aStatistics = $this->getStatistics();
        $iRetryRows  = 0;

        foreach ( $aStatistics as $key => $value) {
            if ( $value['r'] == false ) {
                $iRetryRows++;
                $this->_sReturn .= "File[".$this->_sPath."] - dataset number: $key - Error: ".$value['m']." ---<br> ".PHP_EOL;
            }
        }

        if ( $iRetryRows != $this->_iRetryRows && $iRetryRows>0 ) {
            $this->_resetIdx();
            $this->_iRetryRows = $iRetryRows;
            $this->_sReturn    = '';

            return false;
        }

        return true;
    }

    /**
     * Returns import data cor current index
     *
     * @param string $iIdx array index value
     *
     * @return mixed
     */
    public function getImportData($iIdx = null)
    {
        return $this->_aData[$this->_iIdx];
    }

    /**
     * Returns order article field list
     * due to compatibility reasons, the field list of V0.1
     *
     * @return array
     */
    private function getOldOrderArticleFieldList()
    {
        $aFieldList = array(
            'OXID'          => 'OXID',
            'OXORDERID'     => 'OXORDERID',
            'OXAMOUNT'      => 'OXAMOUNT',
            'OXARTID'       => 'OXARTID',
            'OXARTNUM'      => 'OXARTNUM',
            'OXTITLE'       => 'OXTITLE',
            'OXSELVARIANT'  => 'OXSELVARIANT',
            'OXNETPRICE'    => 'OXNETPRICE',
            'OXBRUTPRICE'   => 'OXBRUTPRICE',
            'OXVAT'         => 'OXVAT',
            'OXPERSPARAM'   => 'OXPERSPARAM',
            'OXPRICE'       => 'OXPRICE',
            'OXBPRICE'      => 'OXBPRICE',
            'OXTPRICE'      => 'OXTPRICE',
            'OXWRAPID'      => 'OXWRAPID',
            'OXSTOCK'       =>  'OXSTOCK',
            'OXORDERSHOPID' => 'OXORDERSHOPID',
            'OXTOTALVAT'    => 'OXTOTALVAT'
        );

        return $aFieldList;
    }

    /**
     * Returns order field list
     * due to compatibility reasons, the field list of V0.1
     *
     * @return array
     */
    private function getOldOrderFielsList()
    {
         $aFieldList = array(
            'OXID'             => 'OXID',
            'OXSHOPID'         => 'OXSHOPID',
            'OXUSERID'         => 'OXUSERID',
            'OXORDERDATE'     => 'OXORDERDATE',
            'OXORDERNR'         => 'OXORDERNR',
            'OXBILLCOMPANY'     => 'OXBILLCOMPANY',
            'OXBILLEMAIL'     => 'OXBILLEMAIL',
            'OXBILLFNAME'     => 'OXBILLFNAME',
            'OXBILLLNAME'     => 'OXBILLLNAME',
            'OXBILLSTREET'     => 'OXBILLSTREET',
            'OXBILLSTREETNR' => 'OXBILLSTREETNR',
            'OXBILLADDINFO'     => 'OXBILLADDINFO',
            'OXBILLUSTID'     => 'OXBILLUSTID',
            'OXBILLCITY'     => 'OXBILLCITY',
            'OXBILLCOUNTRY'     => 'OXBILLCOUNTRY',
            'OXBILLZIP'         => 'OXBILLZIP',
            'OXBILLFON'         => 'OXBILLFON',
            'OXBILLFAX'         => 'OXBILLFAX',
            'OXBILLSAL'         => 'OXBILLSAL',
            'OXDELCOMPANY'     => 'OXDELCOMPANY',
            'OXDELFNAME'     => 'OXDELFNAME',
            'OXDELLNAME'     => 'OXDELLNAME',
            'OXDELSTREET'     => 'OXDELSTREET',
            'OXDELSTREETNR'     => 'OXDELSTREETNR',
            'OXDELADDINFO'     => 'OXDELADDINFO',
            'OXDELCITY'         => 'OXDELCITY',
            'OXDELCOUNTRY'     => 'OXDELCOUNTRY',
            'OXDELZIP'         => 'OXDELZIP',
            'OXDELFON'         => 'OXDELFON',
            'OXDELFAX'         => 'OXDELFAX',
            'OXDELSAL'         => 'OXDELSAL',
            'OXDELCOST'         => 'OXDELCOST',
            'OXDELVAT'         => 'OXDELVAT',
            'OXPAYCOST'         => 'OXPAYCOST',
            'OXPAYVAT'         => 'OXPAYVAT',
            'OXWRAPCOST'     => 'OXWRAPCOST',
            'OXWRAPVAT'         => 'OXWRAPVAT',
            'OXCARDID'         => 'OXCARDID',
            'OXCARDTEXT'     => 'OXCARDTEXT',
            'OXDISCOUNT'     => 'OXDISCOUNT',
            'OXBILLNR'         => 'OXBILLNR',
            'OXREMARK'         => 'OXREMARK',
            'OXVOUCHERDISCOUNT'         => 'OXVOUCHERDISCOUNT',
            'OXCURRENCY'     => 'OXCURRENCY',
            'OXCURRATE'         => 'OXCURRATE',
            'OXTRANSID'         => 'OXTRANSID',
            'OXPAID'         => 'OXPAID',
            'OXIP'             => 'OXIP',
            'OXTRANSSTATUS'     => 'OXTRANSSTATUS',
            'OXLANG'         => 'OXLANG',
            'OXDELTYPE'         => 'OXDELTYPE'
            );

            return $aFieldList;
    }

    /**
     * Checks if id field is valid
     *
     * @param string $sID field check id
     *
     * @return null
     */
    protected  function _checkIDField( $sID )
    {
        if ( !isset( $sID ) || !$sID ) {
            throw new Exception("ERROR: Articlenumber/ID missing!");
        } elseif ( strlen( $sID) > 32 ) {
            throw new Exception( "ERROR: Articlenumber/ID longer then allowed (32 chars max.)!");
        }
    }

    /**
     * method overridden to allow olf Order and OrderArticle types
     *
     * @param string $sType instance type
     *
     * @return object
     */
    protected function _getInstanceOfType( $sType)
    {
        //due to backward compatibility
        if ( $sType == 'oldOrder' ) {
            $oType = parent::_getInstanceOfType('order');
            $oType->setFieldList($this->getOldOrderFielsList());
            $oType->setFunctionSuffix('OldOrder');
        } elseif ( $sType == 'oldOrderArticle' ) {
            $oType = parent::_getInstanceOfType('orderarticle');
            $oType->setFieldList($this->getOldOrderArticleFieldList());
            $oType->setFunctionSuffix('OldOrderArticle');
        } elseif ( $sType == 'article2vendor' ) {
            $oType = parent::_getInstanceOfType('article');
            $oType->setFieldList(array("OXID", "OXVENDORID"));
        } elseif ( $sType == 'mainarticle2categroy') {
            $oType = parent::_getInstanceOfType('article2category');
            $oType->setFieldList(array("OXOBJECTID", "OXCATNID", "OXTIME"));
            $oType->setFunctionSuffix('mainarticle2category');
        } else {
            $oType = parent::_getInstanceOfType($sType);
        }

        return $oType;
    }

    // --------------------------------------------------------------------------
    //
    // Import Handler
    // One _Import* method needed for each object defined in /objects/ folder, all these objects  can be imported
    //
    // --------------------------------------------------------------------------

    /**
     * Imports article. Returns import status
     *
     * @param object $oType type object
     * @param object $aRow  db row array
     *
     * @return string $oxid on success, bool FALSE on failure
     */
    protected function _importArticle( oxERPType $oType, $aRow)
    {
        if ( $this->_sCurrVersion == "0.1" ) {
            $myConfig = oxRegistry::getConfig();
            //to allow different shopid without consequences (ignored fields)
            $myConfig->setConfigParam('blMallCustomPrice', false);
        }

        if ( isset($aRow['OXID'] ) ) {
            $this->_checkIDField($aRow['OXID']);
        }
        // #0004426
        /*else {
            $this->_checkIDField($aRow['OXARTNUM']);
            $aRow['OXID'] = $aRow['OXARTNUM'];
        }*/

        $sResult = $this->_save( $oType, $aRow, $this->_sCurrVersion == "0.1"); // V0.1 allowes the shopid to be set no matter which login
        return $sResult;
    }

    /**
     * Imports accessorie. Returns import status
     *
     * @param object $oType type object
     * @param object $aRow  db row array
     *
     * @return string $oxid on success, bool FALSE on failure
     */
    protected function _importAccessoire( oxERPType $oType, $aRow)
    {
        // deleting old relations before import in V0.1
        if ( $this->_sCurrVersion == "0.1" && !isset($this->_aImportedAccessoire2Article[$aRow['OXARTICLENID']] ) ) {
            $myConfig = oxRegistry::getConfig();
            $oDb = oxDb::getDb();
            $oDb->execute( "delete from oxaccessoire2article where oxarticlenid = ".$oDb->quote( $aRow['OXARTICLENID'] ) );
            $this->_aImportedAccessoire2Article[$aRow['OXARTICLENID']] = 1;
        }

        $sResult = $this->_save( $oType, $aRow);
        return $sResult;
    }

    /**
     * Imports article 2 action relation. Returns import status
     *
     * @param object $oType type object
     * @param object $aRow  db row array
     *
     * @return string $oxid on success, bool FALSE on failure
     */
    protected function _importArticle2Action( oxERPType $oType, $aRow)
    {

        if ( $this->_sCurrVersion == "0.1" && !isset( $this->_aImportedActions2Article[$aRow['OXARTID']] ) ) {
            //only in V0.1 and only once per import/article
            $myConfig = oxRegistry::getConfig();
            $oDb = oxDb::getDb();
            $oDb->execute( "delete from oxactions2article where oxartid = ".$oDb->quote( $aRow['OXARTID'] ) );
            $this->_aImportedActions2Article[$aRow['OXARTID']] = 1;
        }

        $sResult = $this->_save( $oType, $aRow, $this->_sCurrVersion == "0.1");
        return $sResult;
    }

    /**
     * Imports article 2 category relation. Returns import status
     *
     * @param object $oType type object
     * @param object $aRow  db row array
     *
     * @return string $oxid on success, bool FALSE on failure
     */
    protected function _importArticle2Category( oxERPType $oType, $aRow)
    {
        // deleting old relations before import in V0.1
        if ( $this->_sCurrVersion == "0.1" && !isset( $this->_aImportedObject2Category[$aRow['OXOBJECTID']] ) ) {
            $myConfig = oxRegistry::getConfig();
            $oDb = oxDb::getDb();
            $oDb->execute( "delete from oxobject2category where oxobjectid = ".$oDb->quote( $aRow['OXOBJECTID'] ) );
            $this->_aImportedObject2Category[$aRow['OXOBJECTID']] = 1;
        }

        $sResult = $this->_save( $oType, $aRow);
        return $sResult;
    }

    /**
     * Imports main article 2 category relation. Returns import status
     *
     * @param object $oType type object
     * @param object $aRow  db row array
     *
     * @return string $oxid on success, bool FALSE on failure
     */
    protected function _importMainArticle2Category( oxERPType $oType, $aRow)
    {
        $aRow['OXTIME'] = 0;

        $myConfig = oxRegistry::getConfig();
        $oDb = oxDb::getDb();

        $sSql = "select OXID from oxobject2category where oxobjectid = ".$oDb->quote( $aRow['OXOBJECTID'] )." and OXCATNID = ".$oDb->quote( $aRow['OXCATNID'] );
        $aRow['OXID'] = $oDb->getOne( $sSql, false, false );

        $sResult = $this->_save( $oType, $aRow);
        if ((boolean) $sResult) {
            $sSql = "Update oxobject2category set oxtime = oxtime+10 where oxobjectid = ".$oDb->quote( $aRow['OXOBJECTID'] ) ." and oxcatnid != ".$oDb->quote( $aRow['OXCATNID'] ) ." and oxshopid = '".$myConfig->getShopId()."'";
            $oDb->Execute($sSql);
        }

        return $sResult;
    }

    /**
     * Imports category. Returns import status
     *
     * @param object $oType type object
     * @param object $aRow  db row array
     *
     * @return string $oxid on success, bool FALSE on failure
     */
    protected function _importCategory( oxERPType $oType, $aRow)
    {
        $sResult = $this->_save( $oType, $aRow, $this->_sCurrVersion == "0.1");
        return $sResult;
    }

    /**
     * Imports crosselling. Returns import status
     *
     * @param object $oType type object
     * @param object $aRow  db row array
     *
     * @return string $oxid on success, bool FALSE on failure
     */
    protected function _importCrossselling( oxERPType $oType, $aRow)
    {
        // deleting old relations before import in V0.1
        if ( $this->_sCurrVersion == "0.1" && !isset($this->_aImportedObject2Article[$aRow['OXARTICLENID']] ) ) {
            $myConfig = oxRegistry::getConfig();
            $oDb = oxDb::getDb();
            $oDb->Execute( "delete from oxobject2article where oxarticlenid = ".$oDb->quote( $aRow['OXARTICLENID'] ) );
            $this->aImportedObject2Article[$aRow['OXARTICLENID']] = 1;
        }

        $sResult = $this->_save( $oType, $aRow);
        return $sResult;
    }

    /**
     * Imports scale price. Returns import status
     *
     * @param object $oType type object
     * @param object $aRow  db row array
     *
     * @return string $oxid on success, bool FALSE on failure
     */
    protected function _importScaleprice( oxERPType $oType, $aRow)
    {
        $sResult = $this->_save( $oType, $aRow, $this->_sCurrVersion == "0.1");
        return $sResult;
    }

    /**
     * Imports order. Returns import status
     *
     * @param object $oType type object
     * @param object $aRow  db row array
     *
     * @return string $oxid on success, bool FALSE on failure
     */
    protected function _importOrder( oxERPType $oType, $aRow)
    {
        $sResult = $this->_save( $oType, $aRow);
        return $sResult;
        //MAFI a unavoidable hack as oxorder->update() does always return null !!! a hotfix is needed
        //hotfix was added? since it's working with proper return now
    }

    /**
     * Imports order article. Returns import status
     *
     * @param object $oType type object
     * @param object $aRow  db row array
     *
     * @return string $oxid on success, bool FALSE on failure
     */
    protected function _importOrderArticle( oxERPType $oType, $aRow)
    {
        $sResult = $this->_save( $oType, $aRow);
        return $sResult;
    }

    /**
     * Imports order status. Returns import status (TRUE if success)
     *
     * @param object $oType type object
     * @param object $aRow  db row array
     *
     * @return bool
     */
    protected function _importOrderStatus( oxERPType $oType, $aRow)
    {
        $oOrderArt = oxNew( "oxorderarticle", "core");
        $oOrderArt->load( $aRow['OXID']);

        if ( $oOrderArt->getId()) {

            try {
                if ( $this->_sCurrVersion != "0.1") {
                    $oType->checkWriteAccess($oOrderArt->getId());
                }

                    // store status
                $aStatuses = unserialize( $oOrderArt->oxorderarticles__oxerpstatus->value );

                $oStatus = new stdClass();
                $oStatus->STATUS = $aRow['OXERPSTATUS_STATUS'];
                $oStatus->date = $aRow['OXERPSTATUS_TIME'];
                $oStatus->trackingid = $aRow['OXERPSTATUS_TRACKID'];

                $aStatuses[$aRow['OXERPSTATUS_TIME']] = $oStatus;
                $oOrderArt->oxorderarticles__oxerpstatus = new oxField(serialize( $aStatuses), oxField::T_RAW);
                $oOrderArt->save();
                return true;
            } catch (Exception $ex) {
                return false;
            }
        }

        return false;
    }

    /**
     * Imports user. Returns import status
     *
     * @param object $oType type object
     * @param object $aRow  db row array
     *
     * @return string $oxid on success, bool FALSE on failure
     */
    protected function _importUser( oxERPType $oType, $aRow)
    {
        //Speciall check for user
        if ( isset($aRow['OXUSERNAME'] ) ) {
            $sID = $aRow['OXID'];
            $sUserName = $aRow['OXUSERNAME'];

            $oUser = oxNew( "oxuser", "core");
            $oUser->oxuser__oxusername = new oxField($sUserName, oxField::T_RAW);

            //If user exists with and modifies OXID, throw an axception
            //throw new Exception( "USER {$sUserName} already exists!");
            if ( $oUser->exists( $sID) && $sID != $oUser->getId() ) {
                throw new Exception( "USER $sUserName already exists!");
            }

        }

        $sResult  = $this->_save( $oType, $aRow);
        return $sResult;
    }

    /**
     * Imports vendor. Returns import status (TRUE if success)
     *
     * @param object $oType type object
     * @param object $aRow  db row array
     *
     * @return bool
     */
    protected function _importVendor( oxERPType $oType, $aRow)
    {
        $sResult = $this->_save( $oType, $aRow, $this->_sCurrVersion == "0.1");
        return $sResult;
    }

    /**
     * Imports article extended info. Returns import status (TRUE if success)
     *
     * @param object $oType type object
     * @param object $aRow  db row array
     *
     * @return bool
     */
    protected function _importArtextends( oxERPType $oType, $aRow)
    {
        if ( oxERPBase::getRequestedVersion() < 2 ) {
            return false;
        }
        $sResult = $this->_save( $oType, $aRow);
        return $sResult;
    }

    /**
     * Imports country object. Returns import status
     *
     * @param object $oType type object
     * @param object $aRow  db row array
     *
     * @return string $oxid on success, bool FALSE on failure
     */
    protected function _importCountry( oxERPType $oType, $aRow)
    {
        $sResult = $this->_save( $oType, $aRow);
        return $sResult;
    }

    /**
     * Imports article stock. Returns import status
     *
     * @param object $oType type object
     * @param object $aRow  db row array
     *
     * @return string $oxid on success, bool FALSE on failure
     */
    protected function _importArticleStock( oxERPType $oType, $aRow )
    {
        $sResult = $this->_save( $oType, $aRow);
        return $sResult;
    }

    /** gets count of imported rows, total, during import
     *
     * @return int $_iImportedRowCount
     */
    public function getImportedRowCount()
    {
        return count ( $this->_aImportedIds );
    }

    /** adds true to $_aImportedIds where key is given
     *
     * @param mixed $key - given key
     *
     * @return null
     */
    public function setImportedIds( $key )
    {
        if ( !array_key_exists( $key, $this->_aImportedIds ) )
            $this->_aImportedIds[$key] = true;
    }
}
