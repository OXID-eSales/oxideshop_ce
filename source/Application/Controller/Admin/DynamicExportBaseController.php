<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use oxDb;
use stdClass;

/**
 * Error constants
 */
DEFINE("ERR_SUCCESS", -2);
DEFINE("ERR_GENERAL", -1);
DEFINE("ERR_FILEIO", 1);

/**
 * DynExportBase framework class encapsulating a method for defining implementation class.
 * Performs export function according to user chosen categories.
 *
 * @subpackage dyn
 */
class DynamicExportBaseController extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Export class name
     *
     * @var string
     */
    public $sClassDo = "";

    /**
     * Export ui class name
     *
     * @var string
     */
    public $sClassMain = "";

    /**
     * Export output folder
     *
     * @var string
     */
    public $sExportPath = "export/";

    /**
     * Export file extension
     *
     * @var string
     */
    public $sExportFileType = "txt";

    /**
     * Export file name
     *
     * @var string
     */
    public $sExportFileName = "dynexport";

    /**
     * Export file resource
     *
     * @var object
     */
    public $fpFile = null;

    /**
     * Default number of records to export per tick
     * Used if not set in config
     *
     * @var int
     */
    public $iExportPerTick = 30;

    /**
     * Number of records to export per tick
     *
     * @var int
     */
    protected $_iExportPerTick = null;

    /**
     * Full export file path
     *
     * @var string
     */
    protected $_sFilePath = null;

    /**
     * Export result set
     *
     * @var array
     */
    protected $_aExportResultset = [];

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
     */
    public function __construct()
    {
        parent::__construct();

        // set generic frame template
        $this->_sFilePath = $this->getConfig()->getConfigParam('sShopDir') . "/" . $this->sExportPath . $this->sExportFileName . "." . $this->sExportFileType;
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
        $aClassVars = get_object_vars($this);
        foreach ($aClassVars as $name => $value) {
            $this->_aViewData[$name] = $value;
        }

        $this->_aViewData['sOutputFile'] = $this->_sFilePath;
        $this->_aViewData['sDownloadFile'] = $this->getConfig()->getConfigParam('sShopURL') . $this->sExportPath . $this->sExportFileName . "." . $this->sExportFileType;

        return $this->_sThisTemplate;
    }

    /**
     * Prepares and fill all data which all the dyn exports needs
     */
    public function createMainExportView()
    {
        // parent categorie tree
        $this->_aViewData["cattree"] = oxNew(\OxidEsales\Eshop\Application\Model\CategoryList::class);
        $this->_aViewData["cattree"]->loadList();

        $oLangObj = oxNew(\OxidEsales\Eshop\Core\Language::class);
        $aLangs = $oLangObj->getLanguageArray();
        foreach ($aLangs as $id => $language) {
            $language->selected = ($id == $this->_iEditLang);
            $this->_aViewData["aLangs"][$id] = clone $language;
        }
    }

    /**
     * Prepares Export
     */
    public function start()
    {
        // delete file, if its already there
        $this->fpFile = @fopen($this->_sFilePath, "w");
        if (!isset($this->fpFile) || !$this->fpFile) {
            // we do have an error !
            $this->stop(ERR_FILEIO);
        } else {
            $this->_aViewData['refresh'] = 0;
            $this->_aViewData['iStart'] = 0;
            fclose($this->fpFile);

            // prepare it
            $iEnd = $this->prepareExport();
            \OxidEsales\Eshop\Core\Registry::getSession()->setVariable("iEnd", $iEnd);
            $this->_aViewData['iEnd'] = $iEnd;
        }
    }

    /**
     * Stops Export
     *
     * @param integer $iError error number
     */
    public function stop($iError = 0)
    {
        if ($iError) {
            $this->_aViewData['iError'] = $iError;
        }

        // delete temporary heap table
        \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute("drop TABLE if exists " . $this->_getHeapTableName());
    }

    /**
     * virtual function must be overloaded
     *
     * @param integer $iCnt counter
     *
     * @return bool
     */
    public function nextTick($iCnt)
    {
        return false;
    }

    /**
     * writes one line into open export file
     *
     * @param string $sLine exported line
     */
    public function write($sLine)
    {
        $sLine = $this->removeSID($sLine);
        $sLine = str_replace(["\r\n", "\n"], "", $sLine);
        fwrite($this->fpFile, $sLine . "\r\n");
    }

    /**
     * Does Export
     */
    public function run()
    {
        $blContinue = true;
        $iExportedItems = 0;

        $this->fpFile = @fopen($this->_sFilePath, "a");
        if (!isset($this->fpFile) || !$this->fpFile) {
            // we do have an error !
            $this->stop(ERR_FILEIO);
        } else {
            // file is open
            $iStart = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("iStart");
            // load from session
            $this->_aExportResultset = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("aExportResultset");
            $iExportPerTick = $this->getExportPerTick();
            for ($i = $iStart; $i < $iStart + $iExportPerTick; $i++) {
                if (($iExportedItems = $this->nextTick($i)) === false) {
                    // end reached
                    $this->stop(ERR_SUCCESS);
                    $blContinue = false;
                    break;
                }
            }
            if ($blContinue) {
                // make ticker continue
                $this->_aViewData['refresh'] = 0;
                $this->_aViewData['iStart'] = $i;
                $this->_aViewData['iExpItems'] = $iExportedItems;
            }
            fclose($this->fpFile);
        }
    }

    /**
     * Returns how many articles should be exported per tick
     *
     * @return int
     */
    public function getExportPerTick()
    {
        if ($this->_iExportPerTick === null) {
            $this->_iExportPerTick = (int) \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam("iExportNrofLines");
            if (!$this->_iExportPerTick) {
                $this->_iExportPerTick = $this->iExportPerTick;
            }
        }

        return $this->_iExportPerTick;
    }

    /**
     * Sets how many articles should be exported per tick
     *
     * @param int $iCount articles count per tick
     */
    public function setExportPerTick($iCount)
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
    public function removeSid($sInput)
    {
        $sSid = $this->getSession()->getId();

        // remove sid from link
        $sOutput = str_replace("sid={$sSid}/", "", $sInput);
        $sOutput = str_replace("sid/{$sSid}/", "", $sOutput);
        $sOutput = str_replace("sid={$sSid}&amp;", "", $sOutput);
        $sOutput = str_replace("sid={$sSid}&", "", $sOutput);
        $sOutput = str_replace("sid={$sSid}", "", $sOutput);

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
    public function shrink($sInput, $iMaxSize, $blRemoveNewline = true)
    {
        if ($blRemoveNewline) {
            $sInput = str_replace("\r\n", " ", $sInput);
            $sInput = str_replace("\n", " ", $sInput);
        }

        $sInput = str_replace("\t", "    ", $sInput);

        // remove html entities, remove html tags
        $sInput = $this->_unHTMLEntities(strip_tags($sInput));

        $oStr = getStr();
        if ($oStr->strlen($sInput) > $iMaxSize - 3) {
            $sInput = $oStr->substr($sInput, 0, $iMaxSize - 5) . "...";
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
    public function getCategoryString($oArticle, $sSeparator = "/")
    {
        $sCatStr = '';

        $sLang = \OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage();
        $oDB = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $sCatView = getViewName('oxcategories', $sLang);
        $sO2CView = getViewName('oxobject2category', $sLang);

        //selecting category
        $sQ = "select $sCatView.oxleft, $sCatView.oxright, $sCatView.oxrootid from $sO2CView as oxobject2category left join $sCatView on $sCatView.oxid = oxobject2category.oxcatnid ";
        $sQ .= "where oxobject2category.oxobjectid = :oxobjectid and $sCatView.oxactive = 1 order by oxobject2category.oxtime ";

        $oRs = $oDB->select($sQ, [
            ':oxobjectid' => $oArticle->getId()
        ]);
        if ($oRs != false && $oRs->count() > 0) {
            $sLeft = $oRs->fields[0];
            $sRight = $oRs->fields[1];
            $sRootId = $oRs->fields[2];

            //selecting all parent category titles
            $sQ = "select oxtitle from $sCatView where oxright >= :oxright and oxleft <= :oxleft and oxrootid = :oxrootid order by oxleft ";

            $oRs = $oDB->select($sQ, [
                ':oxright' => $sRight,
                ':oxleft' => $sLeft,
                ':oxrootid' => $sRootId
            ]);
            if ($oRs != false && $oRs->count() > 0) {
                while (!$oRs->EOF) {
                    if ($sCatStr) {
                        $sCatStr .= $sSeparator;
                    }
                    $sCatStr .= $oRs->fields[0];
                    $oRs->fetchRow();
                }
            }
        }

        return $sCatStr;
    }

    /**
     * Loads article default category
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $oArticle Article object
     *
     * @return record set
     */
    public function getDefaultCategoryString($oArticle)
    {
        $sLang = \OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage();
        $oDB = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $sCatView = getViewName('oxcategories', $sLang);
        $sO2CView = getViewName('oxobject2category', $sLang);

        //selecting category
        $sQ = "select $sCatView.oxtitle from $sO2CView as oxobject2category left join $sCatView on $sCatView.oxid = oxobject2category.oxcatnid ";
        $sQ .= "where oxobject2category.oxobjectid = :oxobjectid and $sCatView.oxactive = 1 order by oxobject2category.oxtime ";

        return $oDB->getOne($sQ, [
            ':oxobjectid' => $oArticle->getId()
        ]);
    }

    /**
     * Converts field for CSV
     *
     * @param string $sInput input to process
     *
     * @return string
     */
    public function prepareCSV($sInput)
    {
        $sInput = \OxidEsales\Eshop\Core\Registry::getUtilsString()->prepareCSVField($sInput);

        return str_replace(["&nbsp;", "&euro;", "|"], [" ", "", ""], $sInput);
    }

    /**
     * Changes special chars to be XML compatible
     *
     * @param string $sInput string which have to be changed
     *
     * @return string
     */
    public function prepareXML($sInput)
    {
        $sOutput = str_replace("&", "&amp;", $sInput);
        $sOutput = str_replace("\"", "&quot;", $sOutput);
        $sOutput = str_replace(">", "&gt;", $sOutput);
        $sOutput = str_replace("<", "&lt;", $sOutput);
        $sOutput = str_replace("'", "&apos;", $sOutput);

        return $sOutput;
    }

    /**
     * Searches for deepest path to a categorie this article is assigned to
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $oArticle article object
     *
     * @return string
     */
    public function getDeepestCategoryPath($oArticle)
    {
        return $this->_findDeepestCatPath($oArticle);
    }

    /**
     * create export resultset
     *
     * @return int
     */
    public function prepareExport()
    {
        $oDB = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sHeapTable = $this->_getHeapTableName();

        // #1070 Saulius 2005.11.28
        // check mySQL version
        $oRs = $oDB->select("SHOW VARIABLES LIKE 'version'");
        $sTableCharset = $this->_generateTableCharSet($oRs->fields[1]);

        // create heap table
        if (!($this->_createHeapTable($sHeapTable, $sTableCharset))) {
            // error
            \OxidEsales\Eshop\Core\Registry::getUtils()->showMessageAndExit("Could not create HEAP Table {$sHeapTable}\n<br>");
        }

        $sCatAdd = $this->_getCatAdd(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("acat"));
        if (!$this->_insertArticles($sHeapTable, $sCatAdd)) {
            \OxidEsales\Eshop\Core\Registry::getUtils()->showMessageAndExit("Could not insert Articles in Table {$sHeapTable}\n<br>");
        }

        $this->_removeParentArticles($sHeapTable);
        $this->_setSessionParams();

        // get total cnt
        return $oDB->getOne("select count(*) from {$sHeapTable}");
    }

    /**
     * get's one oxid for exporting
     *
     * @param integer $iCnt       counter
     * @param bool    $blContinue false is used to stop exporting
     *
     * @return mixed
     */
    public function getOneArticle($iCnt, &$blContinue)
    {
        $myConfig = $this->getConfig();

        //[Alfonsas 2006-05-31] setting specific parameter
        //to be checked in oxarticle.php init() method
        $myConfig->setConfigParam('blExport', true);
        $blContinue = false;

        if (($oArticle = $this->_initArticle($this->_getHeapTableName(), $iCnt, $blContinue))) {
            $blContinue = true;
            $oArticle = $this->_setCampaignDetailLink($oArticle);
        }

        //[Alfonsas 2006-05-31] unsetting specific parameter
        //to be checked in oxarticle.php init() method
        $myConfig->setConfigParam('blExport', false);

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
    public function assureContent($sInput, $sReplace = null)
    {
        $oStr = getStr();
        if (!$oStr->strlen($sInput)) {
            if (!isset($sReplace) || !$oStr->strlen($sReplace)) {
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
    protected function _unHtmlEntities($sInput)
    {
        $aTransTbl = array_flip(get_html_translation_table(HTML_ENTITIES));

        return strtr($sInput, $aTransTbl);
    }

    /**
     * Create valid Heap table name
     *
     * @return string
     */
    protected function _getHeapTableName()
    {
        // table name must not start with any digit
        return "tmp_" . str_replace("0", "", md5($this->getSession()->getId()));
    }

    /**
     * generates table charset
     *
     * @param string $sMysqlVersion MySql version
     *
     * @return string
     */
    protected function _generateTableCharSet($sMysqlVersion)
    {
        $sTableCharset = "";

        //if MySQL >= 4.1.0 set charsets and collations
        if (version_compare($sMysqlVersion, '4.1.0', '>=') > 0) {
            $oDB = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC);
            $oRs = $oDB->select("SHOW FULL COLUMNS FROM `oxarticles` WHERE field like 'OXID'");
            if (isset($oRs->fields['Collation']) && ($sMysqlCollation = $oRs->fields['Collation'])) {
                $oRs = $oDB->select("SHOW COLLATION LIKE '{$sMysqlCollation}'");
                if (isset($oRs->fields['Charset']) && ($sMysqlCharacterSet = $oRs->fields['Charset'])) {
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
    protected function _createHeapTable($sHeapTable, $sTableCharset)
    {
        $blDone = false;
        $oDB = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sQ = "CREATE TABLE IF NOT EXISTS {$sHeapTable} ( `oxid` CHAR(32) NOT NULL default '' ) ENGINE=InnoDB {$sTableCharset}";
        if (($oDB->execute($sQ)) !== false) {
            $blDone = true;
            $oDB->execute("TRUNCATE TABLE {$sHeapTable}");
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
    protected function _getCatAdd($aChosenCat)
    {
        $sCatAdd = null;
        if (is_array($aChosenCat) && count($aChosenCat)) {
            $oDB = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
            $sCatAdd = " and ( ";
            $blSep = false;
            foreach ($aChosenCat as $sCat) {
                if ($blSep) {
                    $sCatAdd .= " or ";
                }
                $sCatAdd .= "oxobject2category.oxcatnid = " . $oDB->quote($sCat);
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
    protected function _insertArticles($sHeapTable, $sCatAdd)
    {
        $oDB = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $iExpLang = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("iExportLanguage");
        if (!isset($iExpLang)) {
            $iExpLang = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable("iExportLanguage");
        }

        $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        $oArticle->setLanguage($iExpLang);

        $sO2CView = getViewName('oxobject2category', $iExpLang);
        $sArticleTable = getViewName("oxarticles", $iExpLang);

        $insertQuery = "insert into {$sHeapTable} select {$sArticleTable}.oxid from {$sArticleTable}, {$sO2CView} as oxobject2category where ";
        $insertQuery .= $oArticle->getSqlActiveSnippet();

        if (!\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("blExportVars")) {
            $insertQuery .= " and {$sArticleTable}.oxid = oxobject2category.oxobjectid and {$sArticleTable}.oxparentid = '' ";
        } else {
            $insertQuery .= " and ( {$sArticleTable}.oxid = oxobject2category.oxobjectid or {$sArticleTable}.oxparentid = oxobject2category.oxobjectid ) ";
        }

        $sSearchString = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("search");
        if (isset($sSearchString)) {
            $insertQuery .= "and ( {$sArticleTable}.OXTITLE like " . $oDB->quote("%{$sSearchString}%");
            $insertQuery .= " or {$sArticleTable}.OXSHORTDESC like " . $oDB->quote("%$sSearchString%");
            $insertQuery .= " or {$sArticleTable}.oxsearchkeys like " . $oDB->quote("%$sSearchString%") . " ) ";
        }

        if ($sCatAdd) {
            $insertQuery .= $sCatAdd;
        }

        // add minimum stock value
        if ($this->getConfig()->getConfigParam('blUseStock') && ($dMinStock = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("sExportMinStock"))) {
            $dMinStock = str_replace([";", " ", "/", "'"], "", $dMinStock);
            $insertQuery .= " and {$sArticleTable}.oxstock >= " . $oDB->quote($dMinStock);
        }

        $insertQuery .= " group by {$sArticleTable}.oxid";

        return $oDB->execute($insertQuery) ? true : false;
    }

    /**
     * removes parent articles so that we only have variants itself
     *
     * @param string $sHeapTable table name
     */
    protected function _removeParentArticles($sHeapTable)
    {
        if (!(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("blExportMainVars"))) {
            $oDB = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
            $sArticleTable = getViewName('oxarticles');

            // we need to remove again parent articles so that we only have the variants itself
            $sQ = "select $sHeapTable.oxid from $sHeapTable, $sArticleTable where
                          $sHeapTable.oxid = $sArticleTable.oxparentid group by $sHeapTable.oxid";

            $oRs = $oDB->select($sQ);
            $sDel = "delete from $sHeapTable where oxid in ( ";
            $blSep = false;
            if ($oRs != false && $oRs->count() > 0) {
                while (!$oRs->EOF) {
                    if ($blSep) {
                        $sDel .= ",";
                    }
                    $sDel .= $oDB->quote($oRs->fields[0]);
                    $blSep = true;
                    $oRs->fetchRow();
                }
            }
            $sDel .= " )";
            $oDB->execute($sDel);
        }
    }

    /**
     * stores some info in session
     */
    protected function _setSessionParams()
    {
        // reset it from session
        \OxidEsales\Eshop\Core\Registry::getSession()->deleteVariable("sExportDelCost");
        $dDelCost = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("sExportDelCost");
        if (isset($dDelCost)) {
            $dDelCost = str_replace([";", " ", "/", "'"], "", $dDelCost);
            $dDelCost = str_replace(",", ".", $dDelCost);
            \OxidEsales\Eshop\Core\Registry::getSession()->setVariable("sExportDelCost", $dDelCost);
        }

        \OxidEsales\Eshop\Core\Registry::getSession()->deleteVariable("sExportMinPrice");
        $dMinPrice = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("sExportMinPrice");
        if (isset($dMinPrice)) {
            $dMinPrice = str_replace([";", " ", "/", "'"], "", $dMinPrice);
            $dMinPrice = str_replace(",", ".", $dMinPrice);
            \OxidEsales\Eshop\Core\Registry::getSession()->setVariable("sExportMinPrice", $dMinPrice);
        }

        // #827
        \OxidEsales\Eshop\Core\Registry::getSession()->deleteVariable("sExportCampaign");
        $sCampaign = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("sExportCampaign");
        if (isset($sCampaign)) {
            $sCampaign = str_replace([";", " ", "/", "'"], "", $sCampaign);
            \OxidEsales\Eshop\Core\Registry::getSession()->setVariable("sExportCampaign", $sCampaign);
        }

        // reset it from session
        \OxidEsales\Eshop\Core\Registry::getSession()->deleteVariable("blAppendCatToCampaign");
        // now retrieve it from get or post.
        $blAppendCatToCampaign = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("blAppendCatToCampaign");
        if ($blAppendCatToCampaign) {
            \OxidEsales\Eshop\Core\Registry::getSession()->setVariable("blAppendCatToCampaign", $blAppendCatToCampaign);
        }

        // reset it from session
        \OxidEsales\Eshop\Core\Registry::getSession()->deleteVariable("iExportLanguage");
        \OxidEsales\Eshop\Core\Registry::getSession()->setVariable("iExportLanguage", \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("iExportLanguage"));

        //setting the custom header
        \OxidEsales\Eshop\Core\Registry::getSession()->setVariable("sExportCustomHeader", \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("sExportCustomHeader"));
    }

    /**
     * Load all root cat's == all trees
     *
     * @return null
     */
    protected function _loadRootCats()
    {
        if ($this->_aCatLvlCache === null) {
            $this->_aCatLvlCache = [];

            $sCatView = getViewName('oxcategories');
            $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

            // Load all root cat's == all trees
            $sSQL = "select oxid from $sCatView where oxparentid = 'oxrootid'";
            $oRs = $oDb->select($sSQL);
            if ($oRs != false && $oRs->count() > 0) {
                while (!$oRs->EOF) {
                    // now load each tree
                    $sSQL = "SELECT s.oxid, s.oxtitle,
                             s.oxparentid, count( * ) AS LEVEL FROM $sCatView v,
                             $sCatView s WHERE s.oxrootid = :oxrootid and
                             v.oxrootid = :oxrootid and s.oxleft BETWEEN
                             v.oxleft AND v.oxright AND s.oxhidden = '0' GROUP BY s.oxleft order by level";

                    $oRs2 = $oDb->select($sSQL, [
                        ':oxrootid' => $oRs->fields[0]
                    ]);
                    if ($oRs2 != false && $oRs2->count() > 0) {
                        while (!$oRs2->EOF) {
                            // store it
                            $oCat = new stdClass();
                            $oCat->_sOXID = $oRs2->fields[0];
                            $oCat->oxtitle = $oRs2->fields[1];
                            $oCat->oxparentid = $oRs2->fields[2];
                            $oCat->ilevel = $oRs2->fields[3];
                            $this->_aCatLvlCache[$oCat->_sOXID] = $oCat;

                            $oRs2->fetchRow();
                        }
                    }
                    $oRs->fetchRow();
                }
            }
        }

        return $this->_aCatLvlCache;
    }

    /**
     * finds deepest category path
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $oArticle article object
     *
     * @return string
     */
    protected function _findDeepestCatPath($oArticle)
    {
        $sRet = "";

        // find deepest
        $aIds = $oArticle->getCategoryIds();
        if (is_array($aIds) && count($aIds)) {
            if ($aCatLvlCache = $this->_loadRootCats()) {
                $sIdMax = null;
                $dMaxLvl = 0;
                foreach ($aIds as $sCatId) {
                    if ($dMaxLvl < $aCatLvlCache[$sCatId]->ilevel) {
                        $dMaxLvl = $aCatLvlCache[$sCatId]->ilevel;
                        $sIdMax = $sCatId;
                        $sRet = $aCatLvlCache[$sCatId]->oxtitle;
                    }
                }

                // endless
                while (true) {
                    if (!isset($aCatLvlCache[$sIdMax]->oxparentid) || $aCatLvlCache[$sIdMax]->oxparentid == "oxrootid") {
                        break;
                    }
                    $sIdMax = $aCatLvlCache[$sIdMax]->oxparentid;
                    $sRet = $aCatLvlCache[$sIdMax]->oxtitle . "/" . $sRet;
                }
            }
        }

        return $sRet;
    }

    /**
     * initialize article
     *
     * @param string $sHeapTable heap table name
     * @param int    $iCnt       record number
     * @param bool   $blContinue false is used to stop exporting
     *
     * @return object
     */
    protected function _initArticle($sHeapTable, $iCnt, &$blContinue)
    {
        $oRs = $this->getDb()->selectLimit("select oxid from $sHeapTable", 1, $iCnt);
        if ($oRs != false && $oRs->count() > 0) {
            $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
            $oArticle->setLoadParentData(true);

            $oArticle->setLanguage(\OxidEsales\Eshop\Core\Registry::getSession()->getVariable("iExportLanguage"));

            if ($oArticle->load($oRs->fields[0])) {
                // if article exists, do not stop export
                $blContinue = true;
                // check price
                $dMinPrice = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("sExportMinPrice");
                if (!isset($dMinPrice) || (isset($dMinPrice) && ($oArticle->getPrice()->getBruttoPrice() >= $dMinPrice))) {
                    //Saulius: variant title added
                    $sTitle = $oArticle->oxarticles__oxvarselect->value ? " " . $oArticle->oxarticles__oxvarselect->value : "";
                    $oArticle->oxarticles__oxtitle->setValue($oArticle->oxarticles__oxtitle->value . $sTitle);

                    $oArticle = $this->updateArticle($oArticle);

                    return $oArticle;
                }
            }
        }
    }

    /**
     * sets detail link for campaigns
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $oArticle article object
     *
     * @return \OxidEsales\Eshop\Application\Model\Article
     */
    protected function _setCampaignDetailLink($oArticle)
    {
        // #827
        if ($sCampaign = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("sExportCampaign")) {
            // modify detaillink
            //#1166R - pangora - campaign
            $oArticle->appendLink("campaign={$sCampaign}");

            if (\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("blAppendCatToCampaign") &&
                ($sCat = $this->getCategoryString($oArticle))
            ) {
                $oArticle->appendLink("/$sCat");
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

    /**
     * Updates Article object. Method is used for overriding.
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $article
     *
     * @return \OxidEsales\Eshop\Application\Model\Article
     */
    protected function updateArticle($article)
    {
        return $article;
    }

    /**
     * Get the actual database.
     *
     * @return \OxidEsales\Eshop\Core\Database\Adapter\DatabaseInterface The database.
     */
    protected function getDb()
    {
        return \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
    }
}
