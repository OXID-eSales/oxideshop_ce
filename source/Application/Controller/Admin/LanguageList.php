<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use oxDb;
use Exception;

/**
 * Admin selectlist list manager.
 */
class LanguageList extends \OxidEsales\Eshop\Application\Controller\Admin\AdminListController
{
    /**
     * Default sorting parameter.
     *
     * @var string
     */
    protected $_sDefSortField = 'sort';

    /**
     * Default sorting order.
     *
     * @var string
     */
    protected $_sDefSortOrder = 'asc';

    /**
     * Checks for Malladmin rights
     *
     * @return null
     */
    public function deleteEntry()
    {
        $myConfig = $this->getConfig();
        $sOxId = $this->getEditObjectId();

        $aLangData['params'] = $myConfig->getConfigParam('aLanguageParams');
        $aLangData['lang'] = $myConfig->getConfigParam('aLanguages');
        $aLangData['urls'] = $myConfig->getConfigParam('aLanguageURLs');
        $aLangData['sslUrls'] = $myConfig->getConfigParam('aLanguageSSLURLs');

        $iBaseId = (int) $aLangData['params'][$sOxId]['baseId'];

        // preventing deleting main language with base id = 0
        if ($iBaseId == 0) {
            $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\ExceptionToDisplay::class);
            $oEx->setMessage('LANGUAGE_DELETINGMAINLANG_WARNING');
            \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay($oEx);

            return;
        }

        // unsetting selected lang from languages arrays
        unset($aLangData['params'][$sOxId]);
        unset($aLangData['lang'][$sOxId]);
        unset($aLangData['urls'][$iBaseId]);
        unset($aLangData['sslUrls'][$iBaseId]);

        //saving languages info back to DB
        $myConfig->saveShopConfVar('aarr', 'aLanguageParams', $aLangData['params']);
        $myConfig->saveShopConfVar('aarr', 'aLanguages', $aLangData['lang']);
        $myConfig->saveShopConfVar('arr', 'aLanguageURLs', $aLangData['urls']);
        $myConfig->saveShopConfVar('arr', 'aLanguageSSLURLs', $aLangData['sslUrls']);

        //if deleted language was default, setting defalt lang to 0
        if ($iBaseId == $myConfig->getConfigParam('sDefaultLang')) {
            $myConfig->saveShopConfVar('str', 'sDefaultLang', 0);
        }
    }

    /**
     * Executes parent method parent::render() and returns name of template
     * file "selectlist_list.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();
        $this->_aViewData['mylist'] = $this->_getLanguagesList();

        return "language_list.tpl";
    }

    /**
     * Collects shop languages list.
     *
     * @return array
     */
    protected function _getLanguagesList()
    {
        $aLangParams = $this->getConfig()->getConfigParam('aLanguageParams');
        $aLanguages = \OxidEsales\Eshop\Core\Registry::getLang()->getLanguageArray();
        $sDefaultLang = $this->getConfig()->getConfigParam('sDefaultLang');

        foreach ($aLanguages as $sKey => $sValue) {
            $sOxId = $sValue->oxid;
            $aLanguages[$sKey]->active = (!isset($aLangParams[$sOxId]["active"])) ? 1 : $aLangParams[$sOxId]["active"];
            $aLanguages[$sKey]->default = ($aLangParams[$sOxId]["baseId"] == $sDefaultLang) ? true : false;
            $aLanguages[$sKey]->sort = $aLangParams[$sOxId]["sort"];
        }

        if (is_array($aLangParams)) {
            $aSorting = $this->getListSorting();

            if (is_array($aSorting)) {
                foreach ($aSorting as $aFieldSorting) {
                    foreach ($aFieldSorting as $sField => $sDir) {
                        $this->_sDefSortField = $sField;
                        $this->_sDefSortOrder = $sDir;

                        if ($sField == 'active') {
                            //reverting sort order for field 'active'
                            $this->_sDefSortOrder = 'desc';
                        }
                        break 2;
                    }
                }
            }

            uasort($aLanguages, [$this, '_sortLanguagesCallback']);
        }

        return $aLanguages;
    }

    /**
     * Callback function for sorting languages objects. Sorts array according
     * 'sort' parameter
     *
     * @param object $oLang1 language object
     * @param object $oLang2 language object
     *
     * @return bool
     */
    protected function _sortLanguagesCallback($oLang1, $oLang2)
    {
        $sSortParam = $this->_sDefSortField;
        $sVal1 = is_string($oLang1->$sSortParam) ? strtolower($oLang1->$sSortParam) : $oLang1->$sSortParam;
        $sVal2 = is_string($oLang2->$sSortParam) ? strtolower($oLang2->$sSortParam) : $oLang2->$sSortParam;

        if ($this->_sDefSortOrder == 'asc') {
            return ($sVal1 < $sVal2) ? -1 : 1;
        } else {
            return ($sVal1 > $sVal2) ? -1 : 1;
        }
    }

    /**
     * Resets all multilanguage fields with specific language id
     * to default value in all tables.
     *
     * @param string $iLangId language ID
     */
    protected function _resetMultiLangDbFields($iLangId)
    {
        $iLangId = (int) $iLangId;

        //skipping reseting language with id = 0
        if ($iLangId) {
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->startTransaction();

            try {
                $oDbMeta = oxNew(\OxidEsales\Eshop\Core\DbMetaDataHandler::class);
                $oDbMeta->resetLanguage($iLangId);

                \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->commitTransaction();
            } catch (Exception $oEx) {
                // if exception, rollBack everything
                \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->rollbackTransaction();

                //show warning
                $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\ExceptionToDisplay::class);
                $oEx->setMessage('LANGUAGE_ERROR_RESETING_MULTILANG_FIELDS');
                \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay($oEx);
            }
        }
    }
}
