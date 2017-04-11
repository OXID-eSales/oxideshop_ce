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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

/**
 * Shop guestbook page.
 * Manages, collects, denies user comments.
 *
 * @deprecated since 5.3.0 (2016-04-19); There will be a module oeguestbook instead.
 */
class GuestBook extends oxUBase
{

    /**
     * Number of possible pages.
     *
     * @var integer
     */
    protected $_iCntPages = null;

    /**
     * Boolean for showing login form instead of guestbook entries
     *
     * @var bool
     */
    protected $_blShowLogin = false;

    /**
     * Array of sorting columns
     *
     * @var array
     */
    protected $_aSortColumns = null;

    /**
     * Order by
     *
     * @var string
     */
    protected $_sListOrderBy = false;

    /**
     * Oreder directory
     *
     * @var string
     */
    protected $_sListOrderDir = false;

    /**
     * Flood protection
     *
     * @var bool
     */
    protected $_blFloodProtection = null;

    /**
     * Guestbook entries
     *
     * @var array
     */
    protected $_aEntries = null;

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'page/guestbook/guestbook.tpl';

    /**
     * Current class login template name
     *
     * @var string
     */
    protected $_sThisLoginTemplate = 'page/guestbook/guestbook_login.tpl';

    /**
     * Marked which defines if current view is sortable or not
     *
     * @var bool
     */
    protected $_blShowSorting = true;

    /**
     * Page navigation
     *
     * @var object
     */
    protected $_oPageNavigation = null;

    /**
     * Current view search engine indexing state
     *
     * @var int
     */
    protected $_iViewIndexState = VIEW_INDEXSTATE_NOINDEXNOFOLLOW;


    /**
     * Loads guestbook entries, forms guestbook naviagation URLS,
     * executes parent::render() and returns name of template to
     * render guestbook::_sThisTemplate.
     *
     * @return  string  $this->_sThisTemplate   current template file name
     */
    public function render()
    {
        parent::render();

        // #774C no user mail and password check in guesbook
        if ($this->_blShowLogin) {
            //no valid login
            return $this->_sThisLoginTemplate;
        }

        $this->getEntries();

        return $this->_sThisTemplate;
    }

    /**
     * Template variable getter. Returns sorting columns
     *
     * @return array
     */
    public function getSortColumns()
    {
        if ($this->_aSortColumns === null) {
            $this->setSortColumns(array('author', 'date'));
        }

        return $this->_aSortColumns;
    }

    /**
     * Template variable getter. Returns order by
     *
     * @return string
     */
    public function getGbSortBy()
    {
        return $this->_sListOrderBy;
    }

    /**
     * Template variable getter. Returns order directory
     *
     * @return void
     */
    public function getGbSortDir()
    {
        return $this->_sListOrderDir;
    }

    /**
     * Loads guestbook entries for active page and returns them.
     *
     * @return array $oEntries guestbook entries
     */
    public function getEntries()
    {
        if ($this->_aEntries === null) {
            $this->_aEntries = false;
            $iNrofCatArticles = (int) $this->getConfig()->getConfigParam('iNrofCatArticles');
            $iNrofCatArticles = $iNrofCatArticles ? $iNrofCatArticles : 10;

            // loading only if there is some data
            /** @var oxGbEntry $oEntries */
            $oEntries = oxNew('oxgbentry');
            if ($iCnt = $oEntries->getEntryCount()) {
                $this->_iCntPages = round($iCnt / $iNrofCatArticles + 0.49);
                $this->_aEntries = $oEntries->getAllEntries(
                    $this->getActPage() * $iNrofCatArticles,
                    $iNrofCatArticles,
                    $this->getSortingSql($this->getSortIdent())
                );
            }
        }

        return $this->_aEntries;
    }

    /**
     * Template variable getter. Returns boolean of flood protection
     *
     * @return bool
     */
    public function floodProtection()
    {
        if ($this->_blFloodProtection === null) {
            $this->_blFloodProtection = false;
            // is user logged in ?
            $sUserId = oxRegistry::getSession()->getVariable('usr');
            $sUserId = $sUserId ? $sUserId : 0;

            $oEntries = oxNew('oxgbentry');
            $this->_blFloodProtection = $oEntries->floodProtection($this->getConfig()->getShopId(), $sUserId);
        }

        return $this->_blFloodProtection;
    }

    /**
     * Returns sorted column parameter name
     *
     * @return string
     */
    public function getSortOrderByParameterName()
    {
        return 'gborderby';
    }

    /**
     * Returns sorted column direction parameter name
     *
     * @return string
     */
    public function getSortOrderParameterName()
    {
        return 'gborder';
    }

    /**
     * Returns page sort indentificator. It is used as intentificator in session variable aSorting[ident]
     *
     * @return string
     */
    public function getSortIdent()
    {
        return 'oxgb';
    }

    /**
     * Returns default category sorting for selected category
     *
     * @return array
     */
    public function getDefaultSorting()
    {
        $aSorting = array('sortby' => 'date', 'sortdir' => 'desc');

        return $aSorting;
    }

    /**
     * Template variable getter. Returns page navigation
     *
     * @return object
     */
    public function getPageNavigation()
    {
        if ($this->_oPageNavigation === null) {
            $this->_oPageNavigation = false;
            $this->_oPageNavigation = $this->generatePageNavigation();
        }

        return $this->_oPageNavigation;
    }

    /**
     * Method applies validation to entry and saves it to DB.
     * On error/success returns name of action to perform
     * (on error: "guestbookentry?error=x"", on success: "guestbook").
     *
     * @return string
     */
    public function saveEntry()
    {
        if (!oxRegistry::getSession()->checkSessionChallenge()) {
            return;
        }

        $sReviewText = trim(( string ) oxRegistry::getConfig()->getRequestParameter('rvw_txt', true));
        $sShopId = $this->getConfig()->getShopId();
        $sUserId = oxRegistry::getSession()->getVariable('usr');

        // guest book`s entry is validated
        $oUtilsView = oxRegistry::get("oxUtilsView");
        if (!$sUserId) {
            $oUtilsView->addErrorToDisplay('ERROR_MESSAGE_GUESTBOOK_ENTRY_ERR_LOGIN_TO_WRITE_ENTRY');

            //return to same page
            return;
        }

        if (!$sShopId) {
            $oUtilsView->addErrorToDisplay('ERROR_MESSAGE_GUESTBOOK_ENTRY_ERR_UNDEFINED_SHOP');

            return 'guestbookentry';
        }

        // empty entries validation
        if ('' == $sReviewText) {
            $oUtilsView->addErrorToDisplay('ERROR_MESSAGE_GUESTBOOK_ENTRY_ERR_REVIEW_CONTAINS_NO_TEXT');

            return 'guestbook';
        }

        // flood protection
        $oEntrie = oxNew('oxgbentry');
        if ($oEntrie->floodProtection($sShopId, $sUserId)) {
            $oUtilsView->addErrorToDisplay('ERROR_MESSAGE_GUESTBOOK_ENTRY_ERR_MAXIMUM_NUMBER_EXCEEDED');

            return 'guestbookentry';
        }

        // double click protection
        if ($this->canAcceptFormData()) {
            // here the guest book entry is saved
            $oEntry = oxNew('oxgbentry');
            $oEntry->oxgbentries__oxshopid = new oxField($sShopId);
            $oEntry->oxgbentries__oxuserid = new oxField($sUserId);
            $oEntry->oxgbentries__oxcontent = new oxField($sReviewText);
            $oEntry->save();
        }

        return 'guestbook';
    }

    /**
     * Returns Bread Crumb - you are here page1/page2/page3...
     *
     * @return array
     */
    public function getBreadCrumb()
    {
        $aPaths = array();
        $aPath = array();

        $iBaseLanguage = oxRegistry::getLang()->getBaseLanguage();
        $aPath['title'] = oxRegistry::getLang()->translateString('GUESTBOOK', $iBaseLanguage, false);
        $aPath['link'] = $this->getLink();
        $aPaths[] = $aPath;

        return $aPaths;
    }
}
