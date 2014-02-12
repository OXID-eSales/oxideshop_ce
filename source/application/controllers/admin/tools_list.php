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
 * Admin systeminfo manager.
 * Returns template, that arranges two other templates ("tools_list.tpl"
 * and "tools_main.tpl") to frame.
 * @package admin
 */
class Tools_List extends oxAdminList
{
    /**
     * Current class template name
     *
     * @var string
     */
    protected $_sThisTemplate = 'tools_list.tpl';

    /**
     * Performs full view update
     *
     * @return mixed
     */
    public function updateViews()
    {
        //preventing edit for anyone except malladmin
        if ( oxRegistry::getSession()->getVariable( "malladmin" ) ) {
            $oMetaData = oxNew('oxDbMetaDataHandler');
            $this->_aViewData["blViewSuccess"] = $oMetaData->updateViews();
        }
    }

    /**
     * Method performs user passed SQL query
     *
     * @return null
     */
    public function performsql()
    {
        $oAuthUser = oxNew( 'oxuser' );
        $oAuthUser->loadAdminUser();
        if ( $oAuthUser->oxuser__oxrights->value === "malladmin" ) {

            $sUpdateSQL = oxConfig::getParameter("updatesql");
            $sUpdateSQLFile = $this->_processFiles();

            if ( $sUpdateSQLFile && strlen( $sUpdateSQLFile ) > 0 ) {
                if ( isset( $sUpdateSQL ) && strlen( $sUpdateSQL ) )
                    $sUpdateSQL .= ";\r\n".$sUpdateSQLFile;
                else
                    $sUpdateSQL  = $sUpdateSQLFile;
            }

            $sUpdateSQL = trim( stripslashes( $sUpdateSQL ) );
            $oStr = getStr();
            $iLen = $oStr->strlen( $sUpdateSQL );
            if ( $this->_prepareSQL( $sUpdateSQL, $iLen ) ) {
                $aQueries = $this->aSQLs;
                $this->_aViewData["aQueries"] = array();
                $aPassedQueries  = array();
                $aQAffectedRows  = array();
                $aQErrorMessages = array();
                $aQErrorNumbers  = array();

                if ( count( $aQueries ) > 0 ) {
                    $blStop = false;
                    $oDB = oxDb::getDb();
                    $iQueriesCounter = 0;
                    for ( $i = 0; $i < count( $aQueries ); $i++ ) {
                        $sUpdateSQL = $aQueries[$i];
                        $sUpdateSQL = trim( $sUpdateSQL );

                        if ( $oStr->strlen( $sUpdateSQL ) > 0 ) {
                            $aPassedQueries[$iQueriesCounter] = nl2br( htmlentities( $sUpdateSQL ) );
                            if ( $oStr->strlen( $aPassedQueries[$iQueriesCounter] ) > 200 )
                                $aPassedQueries[$iQueriesCounter] = $oStr->substr( $aPassedQueries[$iQueriesCounter], 0, 200 )."...";

                            while ( $sUpdateSQL[ $oStr->strlen( $sUpdateSQL)-1] == ";") {
                                $sUpdateSQL = $oStr->substr( $sUpdateSQL, 0, ( $oStr->strlen( $sUpdateSQL)-1));
                            }

                            try {
                                $oDB->execute( $sUpdateSQL );
                            } catch ( Exception $oExcp ) {
                                // catching exception ...
                                $blStop = true;
                            }

                            $aQAffectedRows [$iQueriesCounter] = null;
                            $aQErrorMessages[$iQueriesCounter] = null;
                            $aQErrorNumbers [$iQueriesCounter] = null;

                            $iErrorNum = $oDB->ErrorNo();
                            if ( $iAffectedRows = $oDB->affected_Rows() !== false && $iErrorNum == 0 ) {
                                $aQAffectedRows[$iQueriesCounter] =  $iAffectedRows;
                            } else {
                                $aQErrorMessages[$iQueriesCounter] = htmlentities( $oDB->errorMsg() );
                                $aQErrorNumbers[$iQueriesCounter]  = htmlentities( $iErrorNum );
                            }
                            $iQueriesCounter++;

                            // stopping on first error..
                            if ( $blStop ) {
                                break;
                            }
                        }
                    }
                }
                $this->_aViewData["aQueries"]       = $aPassedQueries;
                $this->_aViewData["aAffectedRows"]  = $aQAffectedRows;
                $this->_aViewData["aErrorMessages"] = $aQErrorMessages;
                $this->_aViewData["aErrorNumbers"]  = $aQErrorNumbers;
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
        if ( isset( $_FILES['myfile']['name'] ) ) {
            // process all files
            while ( list( $key, $value ) = each( $_FILES['myfile']['name'] ) ) {
                $aSource = $_FILES['myfile']['tmp_name'];
                $sSource = $aSource[$key];
                $aFiletype = explode( "@", $key );
                $key    = $aFiletype[1];
                $sType  = $aFiletype[0];
                $value = strtolower( $value );
                // add type to name
                $aFilename = explode( ".", $value );

                //hack?

                $aBadFiles = array( "php", 'php4', 'php5', "jsp", "cgi", "cmf", "exe" );

                if ( in_array( $aFilename[1], $aBadFiles ) ) {
                    oxRegistry::getUtils()->showMessageAndExit( "We don't play this game, go away" );
                }

                //reading SQL dump file
                if ( $sSource ) {
                    $rHandle   = fopen( $sSource, "r");
                    $sContents = fread( $rHandle, filesize ( $sSource ) );
                    fclose( $rHandle );
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
    protected function _prepareSQL( $sSQL, $iSQLlen )
    {
        $sChar = "";
        $sStrStart = "";
        $blString  = false;
        $oStr = getStr();

        //removing "mysqldump" application comments
        while ( $oStr->preg_match( "/^\-\-.*\n/", $sSQL ) )
            $sSQL = trim( $oStr->preg_replace( "/^\-\-.*\n/", "", $sSQL ) );
        while ( $oStr->preg_match( "/\n\-\-.*\n/", $sSQL ) )
            $sSQL = trim( $oStr->preg_replace( "/\n\-\-.*\n/", "\n", $sSQL ) );

        for ( $iPos = 0; $iPos < $iSQLlen; ++$iPos ) {
            $sChar = $sSQL[$iPos];
            if ( $blString ) {
                while ( true ) {
                    $iPos = $oStr->strpos( $sSQL, $sStrStart, $iPos );
                    //we are at the end of string ?
                    if ( !$iPos ) {
                        $this->aSQLs[] = $sSQL;
                        return true;
                    } elseif ( $sStrStart == '`' || $sSQL[$iPos-1] != '\\' ) {
                        //found some query separators
                        $blString  = false;
                        $sStrStart = "";
                        break;
                    } else {
                        $iNext = 2;
                        $blBackslash = false;
                        while ( $iPos-$iNext > 0 && $sSQL[$iPos-$iNext] == '\\' ) {
                            $blBackslash = !$blBackslash;
                            $iNext++;
                        }
                        if ( $blBackslash ) {
                            $blString  = false;
                            $sStrStart = "";
                            break;
                        } else
                            $iPos++;
                    }
                }
            } elseif ( $sChar == ";" ) {
                // delimiter found, appending query array
                $this->aSQLs[] = $oStr->substr( $sSQL, 0, $iPos );
                $sSQL = ltrim( $oStr->substr( $sSQL, min( $iPos + 1, $iSQLlen ) ) );
                $iSQLlen = $oStr->strlen( $sSQL );
                if ( $iSQLlen )
                    $iPos      = -1;
                else
                    return true;
            } elseif ( ( $sChar == '"') || ( $sChar == '\'') || ( $sChar == '`')) {
                $blString  = true;
                $sStrStart = $sChar;
            } elseif ( $sChar == "#" || ( $sChar == ' ' && $iPos > 1 && $sSQL[$iPos-2] . $sSQL[$iPos-1] == '--')) {
                // removing # commented query code
                $iCommStart = (( $sSQL[$iPos] == "#") ? $iPos : $iPos-2);
                $iCommEnd = ($oStr->strpos(' ' . $sSQL, "\012", $iPos+2))
                           ? $oStr->strpos(' ' . $sSQL, "\012", $iPos+2)
                           : $oStr->strpos(' ' . $sSQL, "\015", $iPos+2);
                if ( !$iCommEnd ) {
                    if ( $iCommStart > 0 )
                        $this->aSQLs[] = trim( $oStr->substr( $sSQL, 0, $iCommStart ) );
                    return true;
                } else {
                    $sSQL = $oStr->substr( $sSQL, 0, $iCommStart ).ltrim( $oStr->substr( $sSQL, $iCommEnd ) );
                    $iSQLlen = $oStr->strlen( $sSQL );
                    $iPos--;
                }
            } elseif ( 32358 < 32270 && ($sChar == '!' && $iPos > 1  && $sSQL[$iPos-2] . $sSQL[$iPos-1] == '/*'))  // removing comments like /**/
                $sSQL[$iPos] = ' ';
        }

        if ( !empty( $sSQL ) && $oStr->preg_match( "/[^[:space:]]+/", $sSQL ) ) {
            $this->aSQLs[] = $sSQL;
        }
        return true;
    }
}
