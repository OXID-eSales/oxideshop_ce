<?php

/**
 * EMOS PHP Bib 2
 *
 * Copyright (c) 2004 - 2007 ECONDA GmbH Karlsruhe
 * All rights reserved.
 *
 * ECONDA GmbH
 * Haid-und-Neu-Str. 7
 * 76131 Karlsruhe
 * Tel. +49 (721) 6630350
 * Fax +49 (721) 66303510
 * info@econda.de
 * www.econda.de
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 * Redistributions of source code must retain the above copyright notice,
 * this list of conditions and the following disclaimer.
 * Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation
 * and/or other materials provided with the distribution.
 * Neither the name of the ECONDA GmbH nor the names of its contributors may
 * be used to endorse or promote products derived from this software without
 * specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * $Id$
 */

namespace OxidEsales\EshopCommunity\Core\Smarty\Plugin;

/**
 * PHP Helper Class to construct a ECONDA Monitor statement for the later
 * inclusion in a HTML/PHP Page.
 *
 * @deprecated since v6.5.6 (2020-07-29); moved the functionality to 'OXID personalization' module
 */
class Emos
{
    /**
     * the EMOS statement consists of 3 parts
     * 1.   the incScript :<code><script type="text/javascript" src="emos2.js"></script>
     * 2,3. a part before and after this incScript (preScript/postScript)</code>
     */

    /**
     * Here we store the call to the js lib
     *
     * @var string
     */
    protected $_sIncScript = "";

    /**
     * JS format init code goes here
     *
     * @var string
     */
    protected $_sPrescript = "";

    /**
     * JS format parameters goes here
     *
     * @var string
     */
    protected $_sPostscript = "";

    /**
     * path to the empos2.js script-file
     *
     * @var string
     */
    protected $_sPathToFile = "";

    /**
     * Name of the script-file
     *
     * @var string
     */
    protected $_sScriptFileName = "emos2.js";

    /**
     * tracker content
     *
     * @var string
     */
    protected $_content             = null;

    /**
     * order process step information
     *
     * @var string
     */
    protected $_orderProcess        = null;

    /**
     * site ID
     *
     * @var string
     */
    protected $_siteid              = null;

    /**
     * Language ID
     *
     * @var string
     */
    protected $_langid              = null;

    /**
     * Country ID
     *
     * @var string
     */
    protected $_countryid           = null;

    /**
     * Page ID
     *
     * @var string
     */
    protected $_pageid              = null;

    /**
     * Search Query string
     *
     * @var string
     */
    protected $_searchQuery         = null;

    /**
     * Number of search hits
     *
     * @var int
     */
    protected $_searchNumberOfHits   = null;

    /**
     * Register user hash
     *
     * @var string
     */
    protected $_registerUser        = null;

    /**
     * Registration result
     *
     * @var string
     */
    protected $_registerResult      = null;

    /**
     * Login user hash
     *
     * @var string
     */
    protected $_loginUser           = null;

    /**
     * Login result
     *
     * @var string
     */
    protected $_loginResult         = null;

    /**
     * Contact information
     *
     * @var string
     */
    protected $_scontact            = null;

    /**
     * Download file information
     *
     * @var string
     */
    protected $_download            = null;

    /**
     * Billing information
     *
     * @var array
     */
    protected $_billing            = null;

    /**
     * ec event array
     *
     * @var array
     */
    protected $_ecEvent             = null;

    /**
     * if we use pretty print, we will set the lineseparator
     *
     * @var string
     */
    protected $_br = "";

    /**
     * if we use pretty print, we will set the tab here
     *
     * @var string
     */
    protected $_tab = "";

    /**
     * Constructor
     * Sets the path to the emos2.js js-bib and prepares the later calls
     *
     * @param string $sPathToFile     The path to the js-bib (/opt/myjs)
     * @param string $sScriptFileName If we want to have annother Filename than emos2.js you can set it here
     */
    public function __construct($sPathToFile = "", $sScriptFileName = "emos2.js")
    {
        $this->_sPathToFile = $sPathToFile;
        $this->_sScriptFileName = $sScriptFileName;
    }

    /**
     * switch on pretty printing of generated code. If not called, the output
     * will be in one line of html.
     */
    public function prettyPrint()
    {
        $this->_br .= "\n";
        $this->_tab .= "\t";
    }

    /**
     * returns the whole statement
     *
     * @return string
     */
    public function toString()
    {
        $this->_prepareScript();

        return $this->_sPrescript .
               $this->_sIncScript .
               $this->_sPostscript;
    }

    /**
     * sets content tracking
     *
     * @param string $sContent content to add
     */
    public function addContent($sContent)
    {
        $this->_content = $sContent;
    }

    /**
     * sets orderprocess tracking
     *
     * @param string $sProcessStep process step to add
     */
    public function addOrderProcess($sProcessStep)
    {
        $this->_orderProcess = $sProcessStep;
    }

    /**
     * sets siteid tracking
     *
     * @param string $sIiteId site id to add
     */
    public function addSiteId($sIiteId)
    {
        $this->_siteid = $sIiteId;
    }

    /**
     * sets language tracking
     *
     * @param string $sLangId language id to add
     */
    public function addLangId($sLangId)
    {
        $this->_langid = $sLangId;
    }

    /**
     * sets country tracking
     *
     * @param string $sCountryId country id to add
     */
    public function addCountryId($sCountryId)
    {
        $this->_countryid = $sCountryId;
    }

    /**
     * adds tracker Page ID
     *
     * @param string $sPageId page id to add
     */
    public function addPageId($sPageId)
    {
        $this->_pageid = $sPageId;
    }

    /**
     * sets search tracking
     *
     * @param string $sQueryString  query string
     * @param int    $iNumberOfHits number of hits
     */
    public function addSearch($sQueryString, $iNumberOfHits)
    {
        // #4018: The emospro.search string is URL-encoded forwarded to econda instead of URL-escaped
        $this->_searchQuery = $this->_emos_DataFormat($sQueryString);
        $this->_searchNumberOfHits = $iNumberOfHits;
    }

    /**
     * sets registration tracking
     * The userid gets a md5() to fullfilll german datenschutzgesetz
     *
     * @param string $sUserId user id
     * @param string $sResult registration result
     */
    public function addRegister($sUserId, $sResult)
    {
        $this->_registerUser = md5($sUserId);
        $this->_registerResult = $sResult;
    }

    /**
     * sets login tracking
     * The userid gets a md5() to fullfilll german datenschutzgesetz
     *
     * @param string $sUserId user id
     * @param string $sResult login result
     */
    public function addLogin($sUserId, $sResult)
    {
        $this->_loginUser = md5($sUserId);
        $this->_loginResult = $sResult;
    }

    /**
     * sets contact tracking
     *
     * @param string $sContactType contant type
     */
    public function addContact($sContactType)
    {
        $this->_scontact = $sContactType;
    }

    /**
     * sets download tracking
     *
     * @param string $sDownloadLabel download label
     */
    public function addDownload($sDownloadLabel)
    {
        $this->_download = $sDownloadLabel;
    }

    /**
     * adds a emosBasket Page Array to the preScript
     *
     * @param array $aBasket basket items
     *
     * @return null
     */
    public function addEmosBasketPageArray($aBasket)
    {
        if (!is_array($aBasket)) {
            return;
        }

        $aBasketItems = [];
        foreach ($aBasket as $oItem) {
            $oItem = $this->_emos_ItemFormat($oItem);
            $aBasketItems[] = ["buy", $oItem->productId, $oItem->productName,
                                  $oItem->price, $oItem->productGroup, $oItem->quantity,
                                  $oItem->variant1, $oItem->variant2, $oItem->variant3 ];
        }

        $this->_ecEvent = $aBasketItems;
    }

    /**
     * adds a detailView to the preScript
     *
     * @param \OxidEsales\Eshop\Core\Smarty\Plugin\EmosItem $oItem item to add to view
     */
    public function addDetailView($oItem)
    {
        $this->_setEmosECPageArray($oItem, "view");
    }

    /**
     * adds a removeFromBasket to the preScript
     *
     * @param \OxidEsales\Eshop\Core\Smarty\Plugin\EmosItem $oItem item to remove from basket
     */
    public function removeFromBasket($oItem)
    {
        $this->_setEmosECPageArray($oItem, "c_rmv");
    }

    /**
     * adds a addToBasket to the preScript
     *
     * @param \OxidEsales\Eshop\Core\Smarty\Plugin\EmosItem $oItem item to add to basket
     */
    public function addToBasket($oItem)
    {
        $this->_setEmosECPageArray($oItem, "c_add");
    }

    /**
     * constructs a emosBillingPageArray of given $sEvent type
     *
     * @param string $sBillingId      billing id
     * @param string $sCustomerNumber customer number
     * @param int    $iTotal          total number
     * @param string $sCountry        customer country title
     * @param string $sCip            customer ip
     * @param string $sCity           customer city title
     */
    public function addEmosBillingPageArray($sBillingId = "", $sCustomerNumber = "", $iTotal = 0, $sCountry = "", $sCip = "", $sCity = "")
    {
        $this->_setEmosBillingArray($sBillingId, $sCustomerNumber, $iTotal, $sCountry, $sCip, $sCity);
    }

    /**
     * set a emosBillingArray
     *
     * @param string $sBillingId      billing id
     * @param string $sCustomerNumber customer number
     * @param int    $iTotal          total number
     * @param string $sCountry        customer country title
     * @param string $sCip            customer ip
     * @param string $sCity           customer city title
     * @deprecated underscore prefix violates PSR12, will be renamed to "setEmosBillingArray" in next major
     */
    protected function _setEmosBillingArray($sBillingId = "", $sCustomerNumber = "", $iTotal = 0, $sCountry = "", $sCip = "", $sCity = "") // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        /******************* prepare data *************************************/
        /* md5 the customer id to fullfill requirements of german datenschutzgeesetz */
        $sCustomerNumber = md5($sCustomerNumber);

        $sCountry = $this->_emos_DataFormat($sCountry);
        $sCip = $this->_emos_DataFormat($sCip) ;
        $sCity = $this->_emos_DataFormat($sCity);

        /* get a / separated location stzring for later drilldown */
        $ort = "";
        if ($sCountry) {
            $ort .= "$sCountry/";
        }

        if ($sCip) {
            $ort .= getStr()->substr($sCip, 0, 1) . "/" . getStr()->substr($sCip, 0, 2) . "/";
        }

        if ($sCity) {
            $ort .= "$sCity/";
        }

        if ($sCip) {
            $ort .= $sCip;
        }

        $this->_billing = [$sBillingId, $sCustomerNumber, $ort, $iTotal];
    }

    /**
     * constructs a emosECPageArray of given $sEvent type
     *
     * @param \OxidEsales\Eshop\Core\Smarty\Plugin\EmosItem $oItem      an instance of class EMOS_Item
     * @param string    $sEvent     Type of this event ("view","c_rmv","c_add")
     * @deprecated underscore prefix violates PSR12, will be renamed to "setEmosECPageArray" in next major
     */
    protected function _setEmosECPageArray($oItem, $sEvent) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $oItem = $this->_emos_ItemFormat($oItem);

        $this->_ecEvent = [[$sEvent, $oItem->productId, $oItem->productName,
                 $oItem->price, $oItem->productGroup,
                 $oItem->quantity, $oItem->variant1,
                 $oItem->variant2, $oItem->variant3]];
    }

    /**
     * formats data/values/params by eliminating named entities and xml-entities
     *
     * @param \OxidEsales\Eshop\Core\Smarty\Plugin\EmosItem $oItem item to format its parameters
     *
     * @return null
     * @deprecated underscore prefix violates PSR12, will be renamed to "emos_ItemFormat" in next major
     */
    protected function _emos_ItemFormat($oItem) // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps,PSR2.Methods.MethodDeclaration.Underscore
    {
        $oItem->productId = $this->_emos_DataFormat($oItem->productId);
        $oItem->productName = $this->_emos_DataFormat($oItem->productName);
        $oItem->productGroup = $this->_emos_DataFormat($oItem->productGroup);
        $oItem->variant1 = $this->_emos_DataFormat($oItem->variant1);
        $oItem->variant2 = $this->_emos_DataFormat($oItem->variant2);
        $oItem->variant3 = $this->_emos_DataFormat($oItem->variant3);

        return $oItem;
    }

    /**
     * formats data/values/params by eliminating named entities and xml-entities
     *
     * @param string $sStr data input to format
     *
     * @return null
     * @deprecated underscore prefix violates PSR12, will be renamed to "emos_DataFormat" in next major
     */
    protected function _emos_DataFormat($sStr) //phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps,PSR2.Methods.MethodDeclaration.Underscore
    {
        //null check
        if (is_null($sStr)) {
            return null;
        }

        //$sStr = urldecode($sStr);
        $sStr = htmlspecialchars_decode($sStr, ENT_QUOTES);
        $sStr = getStr()->html_entity_decode($sStr);
        $sStr = strip_tags($sStr);
        $sStr = trim($sStr);

        //2007-05-10 replace translated &nbsp; with spaces
        $nbsp = chr(0xa0);
        $sStr = str_replace($nbsp, " ", $sStr);
        $sStr = str_replace("\"", "", $sStr);
        $sStr = str_replace("'", "", $sStr);
        $sStr = str_replace("%", "", $sStr);
        $sStr = str_replace(",", "", $sStr);
        $sStr = str_replace(";", "", $sStr);
        /* remove unnecessary white spaces*/
        while (true) {
            $sStr_temp = $sStr;
            $sStr = str_replace("  ", " ", $sStr);

            if ($sStr == $sStr_temp) {
                break;
            }
        }
        $sStr = str_replace(" / ", "/", $sStr);
        $sStr = str_replace(" /", "/", $sStr);
        $sStr = str_replace("/ ", "/", $sStr);

        $sStr = getStr()->substr($sStr, 0, 254);
        //$sStr = rawurlencode( $sStr );
        return $sStr;
    }

    /**
     * formats up the connector script in a Econda ver 2 JS format
     * @deprecated will be renamed to "prepareScript" in next major
     */
    public function _prepareScript() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $this->_sPrescript =  '<script type="text/javascript">window.emosTrackVersion = 2;</script>' . $this->_br;

        $this->_sIncScript .= "<script type=\"text/javascript\" " .
        "src=\"" . $this->_sPathToFile . $this->_sScriptFileName . "\">" .
        "</script>" . $this->_br;

        $this->_sPostscript  = '<script type="text/javascript"><!--' . $this->_br;
        $this->_sPostscript .= $this->_tab . 'var emospro = {};' . $this->_br;

        $this->_sPostscript .= $this->_addJsFormat("content", $this->_content);
        $this->_sPostscript .= $this->_addJsFormat("orderProcess", $this->_orderProcess);
        $this->_sPostscript .= $this->_addJsFormat("siteid", $this->_siteid);
        $this->_sPostscript .= $this->_addJsFormat("langid", $this->_langid);
        $this->_sPostscript .= $this->_addJsFormat("countryid", $this->_countryid);
        $this->_sPostscript .= $this->_addJsFormat("pageId", $this->_pageid);
        $this->_sPostscript .= $this->_addJsFormat("scontact", $this->_scontact);
        $this->_sPostscript .= $this->_addJsFormat("download", $this->_download);
        $this->_sPostscript .= $this->_addJsFormat("billing", [$this->_billing]);

        $this->_sPostscript .= $this->_addJsFormat("search", [[$this->_searchQuery, $this->_searchNumberOfHits]]);
        $this->_sPostscript .= $this->_addJsFormat("register", [[$this->_registerUser, $this->_registerResult]]);
        $this->_sPostscript .= $this->_addJsFormat("login", [[$this->_loginUser, $this->_loginResult]]);

        $this->_sPostscript .= $this->_addJsFormat("ec_Event", $this->_ecEvent);

        $this->_sPostscript .= $this->_tab . 'window.emosPropertiesEvent(emospro);' . $this->_br;
        $this->_sPostscript .= '//-->' . $this->_br . '</script>' . $this->_br;
    }

    /**
     * Formats a line in JS format
     *
     * @param string $sVarName  Variable name
     * @param mixed  $mContents Variable value
     *
     * @return string
     * @deprecated underscore prefix violates PSR12, will be renamed to "addJsFormat" in next major
     */
    protected function _addJsFormat($sVarName, $mContents) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        //get the first non array $mContents element
        $mVal = $mContents;
        while (is_array($mVal)) {
            $mVal = $mVal[0];
        }

        if (is_null($mVal)) {
            return;
        }

        $sEncoded = $this->_jsEncode(($mContents));

        return $this->_tab . 'emospro.' . $sVarName . ' = ' . $sEncoded . ';' . $this->_br;
    }

    /**
     * Encode contents $mContents to string for JS export
     *
     * @param mixed $mContents Input contents
     *
     * @return string
     * @deprecated underscore prefix violates PSR12, will be renamed to "jsEncode" in next major
     */
    protected function _jsEncode($mContents) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return json_encode($mContents);
    }
}
