<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use oxDb;
use oxStr;
use Exception;

/**
 * Admin systeminfo manager.
 * Returns template, that arranges two other templates ("tools_list.tpl"
 * and "tools_main.tpl") to frame.
 */
class ToolsList extends \OxidEsales\Eshop\Application\Controller\Admin\AdminListController
{
    /**
     * Current class template name
     *
     * @var string
     */
    protected $_sThisTemplate = 'tools_list.tpl';

    /**
     * Performs full view update
     */
    public function updateViews()
    {
        //preventing edit for anyone except malladmin
        if (\OxidEsales\Eshop\Core\Registry::getSession()->getVariable("malladmin")) {
            $oMetaData = oxNew(\OxidEsales\Eshop\Core\DbMetaDataHandler::class);
            $this->_aViewData["blViewSuccess"] = $oMetaData->updateViews();
        }
    }

    /**
     * Method performs user passed SQL query
     */
    public function performsql()
    {
        $oAuthUser = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        $oAuthUser->loadAdminUser();
        if ($oAuthUser->oxuser__oxrights->value === "malladmin") {
            $sUpdateSQL = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("updatesql");
            $sUpdateSQLFile = $this->_processFiles();

            if ($sUpdateSQLFile && strlen($sUpdateSQLFile) > 0) {
                if (isset($sUpdateSQL) && strlen($sUpdateSQL)) {
                    $sUpdateSQL .= ";\r\n" . $sUpdateSQLFile;
                } else {
                    $sUpdateSQL = $sUpdateSQLFile;
                }
            }

            $sUpdateSQL = trim(stripslashes($sUpdateSQL));
            $oStr = getStr();
            $iLen = $oStr->strlen($sUpdateSQL);
            if ($this->_prepareSQL($sUpdateSQL, $iLen)) {
                $aQueries = $this->aSQLs;
                $this->_aViewData["aQueries"] = [];
                $aPassedQueries = [];
                $aQAffectedRows = [];
                $aQErrorMessages = [];
                $aQErrorNumbers = [];

                if (count($aQueries) > 0) {
                    $blStop = false;
                    $oDB = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
                    $iQueriesCounter = 0;
                    for ($i = 0; $i < count($aQueries); $i++) {
                        $sUpdateSQL = $aQueries[$i];
                        $sUpdateSQL = trim($sUpdateSQL);

                        if ($oStr->strlen($sUpdateSQL) > 0) {
                            $aPassedQueries[$iQueriesCounter] = nl2br(\OxidEsales\Eshop\Core\Str::getStr()->htmlentities($sUpdateSQL));
                            if ($oStr->strlen($aPassedQueries[$iQueriesCounter]) > 200) {
                                $aPassedQueries[$iQueriesCounter] = $oStr->substr($aPassedQueries[$iQueriesCounter], 0, 200) . "...";
                            }

                            while ($sUpdateSQL[$oStr->strlen($sUpdateSQL) - 1] == ";") {
                                $sUpdateSQL = $oStr->substr($sUpdateSQL, 0, ($oStr->strlen($sUpdateSQL) - 1));
                            }

                            $aQAffectedRows [$iQueriesCounter] = null;
                            $aQErrorMessages[$iQueriesCounter] = null;
                            $aQErrorNumbers [$iQueriesCounter] = null;

                            try {
                                $aQAffectedRows[$iQueriesCounter] = $oDB->execute($sUpdateSQL);
                            } catch (Exception $exception) {
                                // Report errors
                                $aQErrorMessages[$iQueriesCounter] = \OxidEsales\Eshop\Core\Str::getStr()->htmlentities($exception->getMessage());
                                $aQErrorNumbers[$iQueriesCounter] = \OxidEsales\Eshop\Core\Str::getStr()->htmlentities($exception->getCode());
                                // Trigger breaking the loop
                                $blStop = true;
                            }

                            $iQueriesCounter++;

                            // stopping on first error..
                            if ($blStop) {
                                break;
                            }
                        }
                    }
                }
                $this->_aViewData["aQueries"] = $aPassedQueries;
                $this->_aViewData["aAffectedRows"] = $aQAffectedRows;
                $this->_aViewData["aErrorMessages"] = $aQErrorMessages;
                $this->_aViewData["aErrorNumbers"] = $aQErrorNumbers;
            }
            $this->_iDefEdit = 1;
        }
    }

    /**
     * Processes files containing SQL queries
     *
     * @return mixed
     */
    protected function _processFiles()
    {
        if (isset($_FILES['myfile']['name'])) {
            // process all files
            foreach ($_FILES['myfile']['name'] as $key => $value) {
                $aSource = $_FILES['myfile']['tmp_name'];
                $sSource = $aSource[$key];
                $aFiletype = explode("@", $key);
                $key = $aFiletype[1];
                $sType = $aFiletype[0];
                $value = strtolower($value);
                // add type to name
                $aFilename = explode(".", $value);

                //hack?

                $aBadFiles = ["php", 'php4', 'php5', "jsp", "cgi", "cmf", "exe"];

                if (in_array($aFilename[1], $aBadFiles)) {
                    \OxidEsales\Eshop\Core\Registry::getUtils()->showMessageAndExit("File didn't pass our allowed files filter.");
                }

                //reading SQL dump file
                if ($sSource) {
                    $rHandle = fopen($sSource, "r");
                    $sContents = fread($rHandle, filesize($sSource));
                    fclose($rHandle);

                    //reading only one SQL dump file
                    return $sContents;
                }

                return;
            }
        }

        return;
    }

    /**
     * Method parses givent SQL queries string and returns array on success
     *
     * @param string  $sSQL    SQL queries
     * @param integer $iSQLlen query lenght
     *
     * @return mixed
     */
    protected function _prepareSQL($sSQL, $iSQLlen)
    {
        $sChar = "";
        $sStrStart = "";
        $blString = false;
        $oStr = getStr();

        //removing "mysqldump" application comments
        while ($oStr->preg_match("/^\-\-.*\n/", $sSQL)) {
            $sSQL = trim($oStr->preg_replace("/^\-\-.*\n/", "", $sSQL));
        }
        while ($oStr->preg_match("/\n\-\-.*\n/", $sSQL)) {
            $sSQL = trim($oStr->preg_replace("/\n\-\-.*\n/", "\n", $sSQL));
        }

        for ($iPos = 0; $iPos < $iSQLlen; ++$iPos) {
            $sChar = $sSQL[$iPos];
            if ($blString) {
                while (true) {
                    $iPos = $oStr->strpos($sSQL, $sStrStart, $iPos);
                    //we are at the end of string ?
                    if (!$iPos) {
                        $this->aSQLs[] = $sSQL;

                        return true;
                    } elseif ($sStrStart == '`' || $sSQL[$iPos - 1] != '\\') {
                        //found some query separators
                        $blString = false;
                        $sStrStart = "";
                        break;
                    } else {
                        $iNext = 2;
                        $blBackslash = false;
                        while ($iPos - $iNext > 0 && $sSQL[$iPos - $iNext] == '\\') {
                            $blBackslash = !$blBackslash;
                            $iNext++;
                        }
                        if ($blBackslash) {
                            $blString = false;
                            $sStrStart = "";
                            break;
                        } else {
                            $iPos++;
                        }
                    }
                }
            } elseif ($sChar == ";") {
                // delimiter found, appending query array
                $this->aSQLs[] = $oStr->substr($sSQL, 0, $iPos);
                $sSQL = ltrim($oStr->substr($sSQL, min($iPos + 1, $iSQLlen)));
                $iSQLlen = $oStr->strlen($sSQL);
                if ($iSQLlen) {
                    $iPos = -1;
                } else {
                    return true;
                }
            } elseif (($sChar == '"') || ($sChar == '\'') || ($sChar == '`')) {
                $blString = true;
                $sStrStart = $sChar;
            } elseif ($sChar == "#" || ($sChar == ' ' && $iPos > 1 && $sSQL[$iPos - 2] . $sSQL[$iPos - 1] == '--')) {
                // removing # commented query code
                $iCommStart = (($sSQL[$iPos] == "#") ? $iPos : $iPos - 2);
                $iCommEnd = ($oStr->strpos(' ' . $sSQL, "\012", $iPos + 2))
                    ? $oStr->strpos(' ' . $sSQL, "\012", $iPos + 2)
                    : $oStr->strpos(' ' . $sSQL, "\015", $iPos + 2);
                if (!$iCommEnd) {
                    if ($iCommStart > 0) {
                        $this->aSQLs[] = trim($oStr->substr($sSQL, 0, $iCommStart));
                    }

                    return true;
                } else {
                    $sSQL = $oStr->substr($sSQL, 0, $iCommStart) . ltrim($oStr->substr($sSQL, $iCommEnd));
                    $iSQLlen = $oStr->strlen($sSQL);
                    $iPos--;
                }
            } elseif (32358 < 32270 && ($sChar == '!' && $iPos > 1 && $sSQL[$iPos - 2] . $sSQL[$iPos - 1] == '/*')) {
                // removing comments like /**/
                $sSQL[$iPos] = ' ';
            }
        }

        if (!empty($sSQL) && $oStr->preg_match("/[^[:space:]]+/", $sSQL)) {
            $this->aSQLs[] = $sSQL;
        }

        return true;
    }
}
