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
 * Error constants
 */
DEFINE("ERR_SUCCESS", -2);
DEFINE("ERR_GENERAL", -1);
DEFINE("ERR_FILEIO", 1);

/**
 * DynExportBase framework class encapsulating a method for defining implementation class.
 * Performs export function according to user chosen categories.
 * @package admin
 * @subpackage dyn
 */
class DynExportBase extends oxAdminDetails
{
    /**
     * Export class name
     *
     * @var string
     */
    public $sClassDo            = "";

    /**
     * Export ui class name
     *
     * @var string
     */
    public $sClassMain          = "";

    /**
     * Export output folder
     *
     * @var string
     */
    public $sExportPath          = "export/";

    /**
     * Export file extension
     *
     * @var string
     */
    public $sExportFileType      = "txt";

    /**
     * Export file name
     *
     * @var string
     */
    public $sExportFileName      = "dynexport";

    /**
     * Export file resource
     *
     * @var object
     */
    public $fpFile               = null;

    /**
     * Default number of records to export per tick
     * Used if not set in config
     *
     * @var int
     */
    public $iExportPerTick       = 30;

    /**
     * Number of records to export per tick
     *
     * @var int
     */
    protected $_iExportPerTick   = null;

    /**
     * Full export file path
     *
     * @var string
     */
    protected $_sFilePath        = null;

    /**
     * Export result set
     *
     * @var array
     */
    protected $_aExportResultset = array();

    /**
     * View template name
     *
     * @var string
     */
    protected $_sThisTemplate = "dynexportbase.tpl";

    /**
     * Category data cache
     *
     * @var array
     */
    protected $_aCatLvlCache = null;

    /**
     * Calls parent costructor and initializes $this->_sFilePath parameter
     *
     * @return null
     */
    public function __construct()
    {
        parent::__construct();

        // set generic frame template
        $this->_sFilePath = $this->getConfig()->getConfigParam( 'sShopDir' ) . "/". $this->sExportPath . $this->sExportFileName . "." . $this->sExportFileType;
    }

    /**
     * Calls parent rendering methods, sends implementation class names to template
     * and returns default template name
     *
     * @return string
    */
    public function render()
    {
        parent::render();

        // assign all member variables to template
        $aClassVars = get_object_vars( $this );
        while ( list( $name, $value ) = each( $aClassVars ) ) {
            $this->_aViewData[$name] = $value;
        }

        $this->_aViewData['sOutputFile']     = $this->_sFilePath;
        $this->_aViewData['sDownloadFile']   = $this->getConfig()->getConfigParam( 'sShopURL' ) . $this->sExportPath . $this->sExportFileName . "." . $this->sExportFileType;

        return $this->_sThisTemplate;
    }

    /**
     * Prepares and fill all data which all the dyn exports needs
     *
     * @return null
    */
    public function createMainExportView()
    {
        // parent categorie tree
        $this->_aViewData["cattree"] = oxNew( "oxCategoryList" );
        $this->_aViewData["cattree"]->loadList();

        $oLangObj = oxNew( 'oxLang' );
        $aLangs = $oLangObj->getLanguageArray();
        foreach ( $aLangs as $id => $language) {
            $language->selected = ($id == $this->_iEditLang);
            $this->_aViewData["aLangs"][$id] = clone $language;
        }
    }

    /**
     * Prepares Export
     *
     * @return null
     */
    public function start()
    {
        // delete file, if its already there
        $this->fpFile = @fopen( $this->_sFilePath, "w" );
        if ( !isset( $this->fpFile ) || !$this->fpFile ) {
            // we do have an error !
            $this->stop( ERR_FILEIO );
        } else {
            $this->_aViewData['refresh'] = 0;
            $this->_aViewData['iStart']  = 0;
            fclose( $this->fpFile );

            // prepare it
            $iEnd = $this->prepareExport();
            oxSession::setVar( "iEnd", $iEnd );
            $this->_aViewData['iEnd'] = $iEnd;
        }
    }

    /**
     * Stops Export
     *
     * @param integer $iError error number
     *
     * @return null
     */
    public function stop( $iError = 0 )
    {
        if ( $iError ) {
            $this->_aViewData['iError'] = $iError;
        }

        // delete temporary heap table
        oxDb::getDb()->execute( "drop TABLE if exists ". $this->_getHeapTableName() );
    }

    /**
     * virtual function must be overloaded
     *
     * @param integer $iCnt counter
     *
     * @return bool
     */
    public function nextTick( $iCnt)
    {
        return false;
    }

    /**
     * writes one line into open export file
     *
     * @param string $sLine exported line
     *
     * @return null
     */
    public function write( $sLine )
    {
        $sLine = $this->removeSID( $sLine );
        $sLine = str_replace( array("\r\n","\n"), "", $sLine);
        fwrite( $this->fpFile, $sLine."\r\n");
    }

    /**
     * Does Export
     *
     * @return null
     */
    public function run()
    {
        $blContinue = true;
        $iExportedItems = 0;

        $this->fpFile = @fopen( $this->_sFilePath, "a");
        if ( !isset( $this->fpFile) || !$this->fpFile) {
            // we do have an error !
            $this->stop( ERR_FILEIO);
        } else {
            // file is open
            $iStart = oxConfig::getParameter("iStart");
            // load from session
            $this->_aExportResultset = oxConfig::getParameter( "aExportResultset");
            $iExportPerTick = $this->getExportPerTick();
            for ( $i = $iStart; $i < $iStart + $iExportPerTick; $i++) {
                if ( ( $iExportedItems = $this->nextTick( $i ) ) === false ) {
                    // end reached
                    $this->stop( ERR_SUCCESS);
                    $blContinue = false;
                    break;
                }
            }
            if ( $blContinue) {
                // make ticker continue
                $this->_aViewData['refresh'] = 0;
                $this->_aViewData['iStart']  = $i;
                $this->_aViewData['iExpItems'] = $iExportedItems;
            }
            fclose( $this->fpFile);
        }
    }

    /**
     * Returns how many articles should be exported per tick
     *
     * @return int
     */
    public function getExportPerTick()
    {
        if ( $this->_iExportPerTick === null ) {
            $this->_iExportPerTick = (int) oxRegistry::getConfig()->getConfigParam("iExportNrofLines");
            if ( !$this->_iExportPerTick ) {
                $this->_iExportPerTick = $this->iExportPerTick;
            }
        }
        return $this->_iExportPerTick;
    }

    /**
     * Sets how many articles should be exported per tick
     *
     * @param int $iCount articles count per tick
     *
     * @return int
     */
    public function setExportPerTick( $iCount )
    {
        $this->_iExportPerTick = $iCount;
    }

    /**
     * Removes Session ID from $sInput
     *
     * @param string $sInput Input to process
     *
     * @return null
     */
    public function removeSid( $sInput )
    {
        $sSid = $this->getSession()->getId();

        // remove sid from link
        $sOutput = str_replace( "sid={$sSid}/", "", $sInput);
        $sOutput = str_replace( "sid/{$sSid}/", "", $sOutput);
        $sOutput = str_replace( "sid={$sSid}&amp;", "", $sOutput);
        $sOutput = str_replace( "sid={$sSid}&", "", $sOutput);
        $sOutput = str_replace( "sid={$sSid}", "", $sOutput);

        return $sOutput;
    }

    /**
     * Removes tags, shortens a string to $iMaxSize adding "..."
     *
     * @param string  $sInput          input to process
     * @param integer $iMaxSize        maximum output size
     * @param bool    $blRemoveNewline if true - \n and \r will be replaced by " "
     *
     * @return string
     */
    public function shrink( $sInput, $iMaxSize, $blRemoveNewline = true )
    {
        if ( $blRemoveNewline ) {
            $sInput = str_replace( "\r\n", " ", $sInput );
            $sInput = str_replace( "\n", " ", $sInput );
        }

        $sInput = str_replace( "\t", "    ", $sInput );

        // remove html entities, remove html tags
        $sInput = $this->_unHTMLEntities( strip_tags( $sInput ) );

        $oStr = getStr();
        if ( $oStr->strlen( $sInput ) > $iMaxSize - 3 ) {
            $sInput = $oStr->substr( $sInput, 0, $iMaxSize - 5 ) . "...";
        }

        return $sInput;
    }

    /**
     * Loads all article parent categories and returns titles separated by "/"
     *
     * @param object $oArticle   Article object
     * @param string $sSeparator separator (default "/")
     *
     * @return string
     */
    public function getCategoryString( $oArticle, $sSeparator = "/" )
    {
        $sCatStr = '';

        $sLang = oxRegistry::getLang()->getBaseLanguage();
        $oDB = oxDb::getDb();

        $sCatView = getViewName( 'oxcategories', $sLang );
        $sO2CView = getViewName( 'oxobject2category', $sLang );

        //selecting category
        $sQ  = "select $sCatView.oxleft, $sCatView.oxright, $sCatView.oxrootid from $sO2CView as oxobject2category left join $sCatView on $sCatView.oxid = oxobject2category.oxcatnid ";
        $sQ .= "where oxobject2category.oxobjectid=".$oDB->quote( $oArticle->getId() )." and $sCatView.oxactive = 1 order by oxobject2category.oxtime ";

        $oRs = $oDB->execute( $sQ );
        if ( $oRs != false && $oRs->recordCount() > 0 ) {
            $sLeft   = $oRs->fields[0];
            $sRight  = $oRs->fields[1];
            $sRootId = $oRs->fields[2];

            //selecting all parent category titles
            $sQ = "select oxtitle from $sCatView where oxright >= {$sRight} and oxleft <= {$sLeft} and oxrootid = '{$sRootId}' order by oxleft ";

            $oRs = $oDB->execute( $sQ );
            if ( $oRs != false && $oRs->recordCount() > 0 ) {
                while ( !$oRs->EOF ) {
                    if ( $sCatStr ) {
                        $sCatStr .= $sSeparator;
                    }
                    $sCatStr .= $oRs->fields[0];
                    $oRs->moveNext();
                }
            }
        }

        return $sCatStr;
    }

    /**
     * Loads article default category
     *
     * @param oxarticle $oArticle Article object
     *
     * @return record set
     */
    public function getDefaultCategoryString( $oArticle )
    {
        $sLang = oxRegistry::getLang()->getBaseLanguage();
        $oDB = oxDb::getDb();

        $sCatView = getViewName( 'oxcategories', $sLang );
        $sO2CView = getViewName( 'oxobject2category', $sLang );

        //selecting category
        $sQ =  "select $sCatView.oxtitle from $sO2CView as oxobject2category left join $sCatView on $sCatView.oxid = oxobject2category.oxcatnid ";
        $sQ .= "where oxobject2category.oxobjectid=".$oDB->quote( $oArticle->getId() )." and $sCatView.oxactive = 1 order by oxobject2category.oxtime ";

        return $oDB->getOne( $sQ);
    }

    /**
     * Converts field for CSV
     *
     * @param string $sInput input to process
     *
     * @return string
     */
    public function prepareCSV( $sInput )
    {
        $sInput = oxRegistry::get("oxUtilsString")->prepareCSVField( $sInput );
        return str_replace( array( "&nbsp;", "&euro;", "|" ), array( " ", "", "" ), $sInput );
    }

    /**
     * Changes special chars to be XML compatible
     *
     * @param string $sInput string which have to be changed
     *
     * @return string
     */
    public function prepareXML( $sInput )
    {
        $sOutput = str_replace( "&", "&amp;", $sInput );
        $sOutput = str_replace( "\"", "&quot;", $sOutput );
        $sOutput = str_replace( ">", "&gt;", $sOutput );
        $sOutput = str_replace( "<", "&lt;", $sOutput );
        $sOutput = str_replace( "'", "&apos;", $sOutput );

        return $sOutput;
    }

    /**
     * Searches for deepest path to a categorie this article is assigned to
     *
     * @param oxarticle $oArticle article object
     *
     * @return string
     */
    public function getDeepestCategoryPath( $oArticle )
    {
        return $this->_findDeepestCatPath( $oArticle );
    }

    /**
     * create export resultset
     *
     * @return int
     */
    public function prepareExport()
    {
        $oDB = oxDb::getDb();
        $sHeapTable = $this->_getHeapTableName();

        // #1070 Saulius 2005.11.28
        // check mySQL version
        $oRs = $oDB->execute( "SHOW VARIABLES LIKE 'version'" );
        $sTableCharset = $this->_generateTableCharSet( $oRs->fields[1] );

        // create heap table
        if ( !( $this->_createHeapTable( $sHeapTable, $sTableCharset ) ) ) {
            // error
            oxRegistry::getUtils()->showMessageAndExit( "Could not create HEAP Table {$sHeapTable}\n<br>" );
        }

        $sCatAdd = $this->_getCatAdd( oxConfig::getParameter( "acat" ) );
        if ( !$this->_insertArticles( $sHeapTable, $sCatAdd ) ) {
            oxRegistry::getUtils()->showMessageAndExit( "Could not insert Articles in Table {$sHeapTable}\n<br>" );
        }

        $this->_removeParentArticles( $sHeapTable );
        $this->_setSessionParams();

        // get total cnt
        return $oDB->getOne( "select count(*) from {$sHeapTable}" );
    }

     /**
     * get's one oxid for exporting
     *
     * @param integer $iCnt        counter
     * @param bool    &$blContinue false is used to stop exporting
     *
     * @return mixed
     */
    public function getOneArticle( $iCnt, & $blContinue )
    {
        $myConfig  = $this->getConfig();

        //[Alfonsas 2006-05-31] setting specific parameter
        //to be checked in oxarticle.php init() method
        $myConfig->setConfigParam( 'blExport', true );
        $blContinue = false;

        if ( ( $oArticle = $this->_initArticle( $this->_getHeapTableName(), $iCnt, $blContinue ) ) ) {
            $blContinue = true;
            $oArticle = $this->_setCampaignDetailLink( $oArticle );
        }

        //[Alfonsas 2006-05-31] unsetting specific parameter
        //to be checked in oxarticle.php init() method
        $myConfig->setConfigParam( 'blExport', false );

        return $oArticle;
    }

    /**
     * Make sure that string is never empty.
     *
     * @param string $sInput   string that will be replaced
     * @param string $sReplace string that will replace
     *
     * @return string
     */
    public function assureContent( $sInput, $sReplace = null)
    {
        $oStr = getStr();
        if ( !$oStr->strlen( $sInput ) ) {
            if ( !isset( $sReplace ) || !$oStr->strlen( $sReplace ) ) {
                $sReplace = "-";
            }
            $sInput = $sReplace;
        }
        return $sInput;
    }

    /**
     * Replace HTML Entities
     * Replacement for html_entity_decode which is only available from PHP 4.3.0 onj
     *
     * @param string $sInput string to replace
     *
     * @return string
     */
    protected function _unHtmlEntities( $sInput )
    {
        $aTransTbl = array_flip( get_html_translation_table( HTML_ENTITIES ) );
        return strtr( $sInput, $aTransTbl );
    }

    /**
     * Create valid Heap table name
     *
     * @return string
     */
    protected function _getHeapTableName()
    {
        // table name must not start with any digit
        return "tmp_".str_replace( "0", "", md5( $this->getSession()->getId() ) );
    }

    /**
     * generates table charset
     *
     * @param string $sMysqlVersion MySql version
     *
     * @return string
     */
    protected function _generateTableCharSet( $sMysqlVersion )
    {
        $sTableCharset = "";

        //if MySQL >= 4.1.0 set charsets and collations
        if ( version_compare( $sMysqlVersion, '4.1.0', '>=' ) > 0 ) {
            $oDB = oxDb::getDb( oxDB::FETCH_MODE_ASSOC );
            $oRs = $oDB->execute( "SHOW FULL COLUMNS FROM `oxarticles` WHERE field like 'OXID'" );
            if ( isset( $oRs->fields['Collation'] ) && ( $sMysqlCollation = $oRs->fields['Collation'] ) ) {
                $oRs = $oDB->execute( "SHOW COLLATION LIKE '{$sMysqlCollation}'" );
                if ( isset( $oRs->fields['Charset'] ) && ( $sMysqlCharacterSet = $oRs->fields['Charset'] ) ) {
                    $sTableCharset = "DEFAULT CHARACTER SET {$sMysqlCharacterSet} COLLATE {$sMysqlCollation}";
                }
            }

        }
        return $sTableCharset;
    }

    /**
     * creates heaptable
     *
     * @param string $sHeapTable    table name
     * @param string $sTableCharset table charset
     *
     * @return bool
     */
    protected function _createHeapTable( $sHeapTable, $sTableCharset )
    {
        $blDone = false;

        $oDB = oxDb::getDb();
        $sQ = "CREATE TABLE IF NOT EXISTS {$sHeapTable} ( `oxid` CHAR(32) NOT NULL default '' ) ENGINE=InnoDB {$sTableCharset}";
        if ( ( $oDB->execute( $sQ ) ) !== false ) {
            $blDone = true;
            $oDB->execute( "TRUNCATE TABLE {$sHeapTable}" );
        }

        return $blDone;
    }

    /**
     * creates additional cat string
     *
     * @param array $aChosenCat Selected category array
     *
     * @return string
     */
    protected function _getCatAdd( $aChosenCat )
    {
        $sCatAdd = null;
        if ( is_array( $aChosenCat ) && count( $aChosenCat ) ) {
            $oDB = oxDb::getDb();
            $sCatAdd = " and ( ";
            $blSep = false;
            foreach ( $aChosenCat as $sCat ) {
                if ( $blSep ) {
                    $sCatAdd .= " or ";
                }
                $sCatAdd .= "oxobject2category.oxcatnid = ".$oDB->quote( $sCat );
                $blSep = true;
            }
            $sCatAdd .= ")";
        }
        return $sCatAdd;
    }

    /**
     * inserts articles into heaptable
     *
     * @param string $sHeapTable heap table name
     * @param string $sCatAdd    category id filter (part of sql)
     *
     * @return bool
     */
    protected function _insertArticles( $sHeapTable, $sCatAdd )
    {
        $oDB = oxDb::getDb();

        $iExpLang = oxConfig::getParameter( "iExportLanguage" );
        if (!isset($iExpLang)) {
            $iExpLang = oxSession::getVar( "iExportLanguage" );
        }

        $oArticle = oxNew( 'oxarticle' );
        $oArticle->setLanguage( $iExpLang );

        $sO2CView = getViewName( 'oxobject2category', $iExpLang );
        $sArticleTable = getViewName( "oxarticles", $iExpLang );

        $sSelect  = "insert into {$sHeapTable} select {$sArticleTable}.oxid from {$sArticleTable}, {$sO2CView} as oxobject2category where ";
        $sSelect .= $oArticle->getSqlActiveSnippet();

        if ( ! oxConfig::getParameter( "blExportVars" ) ) {
            $sSelect .= " and {$sArticleTable}.oxid = oxobject2category.oxobjectid and {$sArticleTable}.oxparentid = '' ";
        } else {
            $sSelect .= " and ( {$sArticleTable}.oxid = oxobject2category.oxobjectid or {$sArticleTable}.oxparentid = oxobject2category.oxobjectid ) ";
        }

        $sSearchString = oxConfig::getParameter( "search" );
        if ( isset( $sSearchString ) ) {
            $sSelect .= "and ( {$sArticleTable}.OXTITLE like ".$oDB->quote( "%{$sSearchString}%" );
            $sSelect .= " or {$sArticleTable}.OXSHORTDESC like ".$oDB->quote( "%$sSearchString%" );
            $sSelect .= " or {$sArticleTable}.oxsearchkeys like ".$oDB->quote( "%$sSearchString%" ) ." ) ";
        }

        if ( $sCatAdd ) {
            $sSelect .= $sCatAdd;
        }

            if ( !$sCatAdd ) {
                $sShopID = $this->getConfig()->getShopId();
                $sSelect .= " and {$sArticleTable}.oxshopid = '$sShopID' ";
            }

        // add minimum stock value
        if ( $this->getConfig()->getConfigParam( 'blUseStock' ) && ( $dMinStock = oxConfig::getParameter( "sExportMinStock" ) ) ) {
            $dMinStock = str_replace( array( ";", " ", "/", "'"), "", $dMinStock );
            $sSelect .= " and {$sArticleTable}.oxstock >= ".$oDB->quote( $dMinStock );
        }

        $sSelect .= " group by {$sArticleTable}.oxid";

        return $oDB->execute( $sSelect ) ? true : false;
    }

    /**
     * removes parent articles so that we only have variants itself
     *
     * @param string $sHeapTable table name
     *
     * @return null
     */
    protected function _removeParentArticles( $sHeapTable )
    {
        if ( !( oxConfig::getParameter( "blExportMainVars" ) ) ) {

            $oDB = oxDb::getDb();
            $sArticleTable = getViewName('oxarticles');

            // we need to remove again parent articles so that we only have the variants itself
            $sQ = "select $sHeapTable.oxid from $sHeapTable, $sArticleTable where
                          $sHeapTable.oxid = $sArticleTable.oxparentid group by $sHeapTable.oxid";

            $oRs = $oDB->execute( $sQ );
            $sDel = "delete from $sHeapTable where oxid in ( ";
            $blSep = false;
            if ($oRs != false && $oRs->recordCount() > 0) {
                while ( !$oRs->EOF ) {
                    if ( $blSep ) {
                        $sDel .= ",";
                    }
                    $sDel .= $oDB->quote( $oRs->fields[0] );
                    $blSep = true;
                    $oRs->moveNext();
                }
            }
            $sDel .= " )";
            $oDB->execute( $sDel );
        }
    }

    /**
     * stores some info in session
     *
     * @return null
     *
     */
    protected function _setSessionParams()
    {
        // reset it from session
        oxSession::deleteVar( "sExportDelCost" );
        $dDelCost = oxConfig::getParameter( "sExportDelCost");
        if ( isset( $dDelCost ) ) {
            $dDelCost = str_replace( array( ";", " ", "/", "'"), "", $dDelCost );
            $dDelCost = str_replace( ",", ".", $dDelCost );
            oxSession::setVar( "sExportDelCost", $dDelCost );
        }

        oxSession::deleteVar( "sExportMinPrice" );
        $dMinPrice = oxConfig::getParameter( "sExportMinPrice" );
        if ( isset( $dMinPrice ) ) {
            $dMinPrice = str_replace( array( ";", " ", "/", "'"), "", $dMinPrice);
            $dMinPrice = str_replace( ",", ".", $dMinPrice);
            oxSession::setVar( "sExportMinPrice", $dMinPrice);
        }

        // #827
        oxSession::deleteVar( "sExportCampaign" );
        $sCampaign = oxConfig::getParameter( "sExportCampaign" );
        if ( isset( $sCampaign ) ) {
            $sCampaign = str_replace( array( ";", " ", "/", "'"), "", $sCampaign );
            oxSession::setVar( "sExportCampaign", $sCampaign );
        }

        // reset it from session
        oxSession::deleteVar("blAppendCatToCampaign" );
        // now retrieve it from get or post.
        $blAppendCatToCampaign = oxConfig::getParameter( "blAppendCatToCampaign" );
        if ( $blAppendCatToCampaign ) {
            oxSession::setVar( "blAppendCatToCampaign", $blAppendCatToCampaign );
        }

        // reset it from session
        oxSession::deleteVar("iExportLanguage" );
        oxSession::setVar( "iExportLanguage", oxConfig::getParameter( "iExportLanguage" ) );

        //setting the custom header
        oxSession::setVar("sExportCustomHeader", oxConfig::getParameter( "sExportCustomHeader" ));
    }

    /**
     * Load all root cat's == all trees
     *
     * @return null
     */
    protected function _loadRootCats()
    {
        if ( $this->_aCatLvlCache === null ) {
            $this->_aCatLvlCache = array();

            $sCatView = getViewName('oxcategories');
            $oDb = oxDb::getDb();

            // Load all root cat's == all trees
            $sSQL = "select oxid from $sCatView where oxparentid = 'oxrootid'";
            $oRs = $oDb->execute( $sSQL);
            if ( $oRs != false && $oRs->recordCount() > 0 ) {
                while ( !$oRs->EOF ) {
                    // now load each tree
                    $sSQL = "SELECT s.oxid, s.oxtitle,
                             s.oxparentid, count( * ) AS LEVEL FROM $sCatView v,
                             $sCatView s WHERE s.oxrootid = '".$oRs->fields[0]."' and
                             v.oxrootid='".$oRs->fields[0]."' and s.oxleft BETWEEN
                             v.oxleft AND v.oxright AND s.oxhidden = '0' GROUP BY s.oxleft order by level";

                    $oRs2 = $oDb->Execute( $sSQL );
                    if ( $oRs2 != false && $oRs2->recordCount() > 0 ) {
                        while ( !$oRs2->EOF ) {
                            // store it
                            $oCat = new stdClass();
                            $oCat->_sOXID     = $oRs2->fields[0];
                            $oCat->oxtitle    = $oRs2->fields[1];
                            $oCat->oxparentid = $oRs2->fields[2];
                            $oCat->ilevel     = $oRs2->fields[3];
                            $this->_aCatLvlCache[$oCat->_sOXID] = $oCat;

                            $oRs2->moveNext();
                        }
                    }
                    $oRs->moveNext();
                }
            }
        }

        return $this->_aCatLvlCache;
    }

    /**
     * finds deepest category path
     *
     * @param oxarticle $oArticle article object
     *
     * @return string
     */
    protected function _findDeepestCatPath( $oArticle )
    {
        $sRet = "";

        // find deepest
        $aIds = $oArticle->getCategoryIds();
        if ( is_array( $aIds ) && count( $aIds ) ) {
            if ( $aCatLvlCache = $this->_loadRootCats() ) {
                $sIdMax  = null;
                $dMaxLvl = 0;
                foreach ( $aIds as $sCatId ) {
                    if ( $dMaxLvl < $aCatLvlCache[$sCatId]->ilevel ) {
                        $dMaxLvl = $aCatLvlCache[$sCatId]->ilevel;
                        $sIdMax  = $sCatId;
                        $sRet    = $aCatLvlCache[$sCatId]->oxtitle;
                    }
                }

                // endless
                for ( ;; ) {
                    if ( !isset( $aCatLvlCache[$sIdMax]->oxparentid ) || $aCatLvlCache[$sIdMax]->oxparentid == "oxrootid" ) {
                        break;
                    }
                    $sIdMax = $aCatLvlCache[$sIdMax]->oxparentid;
                    $sRet = $aCatLvlCache[$sIdMax]->oxtitle."/".$sRet;
                }
            }
        }
        return $sRet;
    }

    /**
     * initialize article
     *
     * @param string $sHeapTable  heap table name
     * @param int    $iCnt        record number
     * @param bool   &$blContinue false is used to stop exporting
     *
     * @return object
     */
    protected function _initArticle( $sHeapTable, $iCnt, & $blContinue )
    {


        $oRs = oxDb::getDb()->selectLimit( "select oxid from $sHeapTable", 1, $iCnt );
        if ( $oRs != false && $oRs->recordCount() > 0 ) {
            $oArticle = oxNew( 'oxarticle' );
            $oArticle->setLoadParentData( true );

            $oArticle->setLanguage( oxSession::getVar( "iExportLanguage" ) );

            if ( $oArticle->load( $oRs->fields[0] ) ) {
                // if article exists, do not stop export
                $blContinue = true;
                // check price
                $dMinPrice = oxConfig::getParameter( "sExportMinPrice" );
                if ( !isset( $dMinPrice ) || ( isset( $dMinPrice ) && ( $oArticle->getPrice()->getBruttoPrice() >= $dMinPrice ) ) ) {

                    //Saulius: variant title added
                    $sTitle = $oArticle->oxarticles__oxvarselect->value ? " " .$oArticle->oxarticles__oxvarselect->value : "";
                    $oArticle->oxarticles__oxtitle->setValue( $oArticle->oxarticles__oxtitle->value . $sTitle );


                    return $oArticle;
                }
            }
        }
    }

    /**
     * sets detail link for campaigns
     *
     * @param oxarticle $oArticle article object
     *
     * @return oxarticle
     */
    protected function _setCampaignDetailLink( $oArticle )
    {
        // #827
        if ( $sCampaign = oxConfig::getParameter( "sExportCampaign" ) ) {
            // modify detaillink
            //#1166R - pangora - campaign
            $oArticle->appendLink( "campaign={$sCampaign}" );

            if ( oxConfig::getParameter( "blAppendCatToCampaign") &&
                 ( $sCat = $this->getCategoryString( $oArticle ) ) ) {
                $oArticle->appendLink( "/$sCat" );
            }
        }
        return $oArticle;
    }

    /**
     * Returns view id ('dyn_interface')
     *
     * @return string
     */
    public function getViewId()
    {
        return 'dyn_interface';
    }
}

