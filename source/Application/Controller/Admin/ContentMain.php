<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Model\Content;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Html\HtmlSanitizerInterface;
use stdClass;
use OxidEsales\Eshop\Core\Str;
use Throwable;

/**
 * Admin content manager.
 * There is possibility to change content description, enter page text etc.
 * Admin Menu: Customerinformations -> Content.
 */
class ContentMain extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /** @inheritdoc */
    public function render()
    {
        $myConfig = Registry::getConfig();

        parent::render();

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();

        // categorie tree
        $oCatTree = oxNew(\OxidEsales\Eshop\Application\Model\CategoryList::class);
        $oCatTree->loadList();

        $oContent = oxNew(Content::class);
        if (isset($soxId) && $soxId != "-1") {
            // load object
            $oContent->loadInLang($this->_iEditLang, $soxId);

            $oOtherLang = $oContent->getAvailableInLangs();
            if (!isset($oOtherLang[$this->_iEditLang])) {
                $oContent->loadInLang(key($oOtherLang), $soxId);
            }

            // remove already created languages
            $aLang = array_diff(Registry::getLang()->getLanguageNames(), $oOtherLang);
            if (count($aLang)) {
                $this->_aViewData["posslang"] = $aLang;
            }
            foreach ($oOtherLang as $id => $language) {
                $oLang = new stdClass();
                $oLang->sLangDesc = $language;
                $oLang->selected = ($id == $this->_iEditLang);
                $this->_aViewData["otherlang"][$id] = clone $oLang;
            }
            // mark selected
            if ($oContent->oxcontents__oxcatid->value && isset($oCatTree[$oContent->oxcontents__oxcatid->value])) {
                $oCatTree[$oContent->oxcontents__oxcatid->value]->selected = 1;
            }
        } else {
            // create ident to make life easier
            $sUId = Registry::getUtilsObject()->generateUId();
            $oContent->oxcontents__oxloadid = new \OxidEsales\Eshop\Core\Field($sUId);
        }

        $this->_aViewData["edit"] = $oContent;
        $this->_aViewData["link"] = "[{ oxgetseourl ident=&quot;" . $oContent->oxcontents__oxloadid->value . "&quot; type=&quot;oxcontent&quot; }]";
        $this->_aViewData["cattree"] = $oCatTree;

        // generate editor
        $sCSS = "content.css";
        if ($oContent->oxcontents__oxsnippet->value == '1') {
            $sCSS = null;
        }

        $this->_aViewData["editor"] = $this->generateTextEditor("100%", 300, $oContent, "oxcontents__oxcontent", $sCSS);
        $this->_aViewData["afolder"] = $myConfig->getConfigParam('aCMSfolder');

        $this->_aViewData["activeSanitizer"] = ContainerFacade::getParameter('oxid_esales.html_sanitizer_enabled');

        return "content_main";
    }

    public function save()
    {
        parent::save();

        $contentId = $this->getEditObjectId();
        $requestParams = Registry::getRequest()->getRequestEscapedParameter("editval");

        if (isset($requestParams['oxcontents__oxloadid'])) {
            $requestParams['oxcontents__oxloadid'] = $this->prepareIdent($requestParams['oxcontents__oxloadid']);
        }

        if ($this->checkIdent($requestParams['oxcontents__oxloadid'], $contentId)) {
            $this->_aViewData["blLoadError"] = true;
            $this->handleSaveError($contentId, $requestParams);

            return;
        }

        if ($requestParams['oxcontents__oxtype'] == 0) {
            $requestParams['oxcontents__oxsnippet'] = 1;
        } else {
            $requestParams['oxcontents__oxsnippet'] = 0;
        }

        if ($requestParams['oxcontents__oxfolder'] === 'CMSFOLDER_NONE') {
            $requestParams['oxcontents__oxfolder'] = '';
        }

        $this->prepareAndSaveContent($requestParams, $contentId, $this->_iEditLang);
    }

    public function saveinnlang()
    {
        parent::save();

        $contentId = $this->getEditObjectId();
        $requestParams = Registry::getRequest()->getRequestEscapedParameter("editval");

        if (isset($requestParams['oxcontents__oxloadid'])) {
            $requestParams['oxcontents__oxloadid'] = $this->prepareIdent($requestParams['oxcontents__oxloadid']);
        }

        if ($this->checkIdent($requestParams['oxcontents__oxloadid'], $contentId)) {
            $this->_aViewData["blLoadError"] = true;
            $this->handleSaveError($contentId, $requestParams);

            return;
        }

        $this->prepareAndSaveContent(
            $requestParams,
            $contentId,
            Registry::getRequest()->getRequestEscapedParameter("new_lang")
        );
    }

    /**
     * Prepares ident (removes bad chars, leaves only thoose that fits in a-zA-Z0-9_ range)
     *
     * @param string $sIdent ident to filter
     *
     * @return string
     */
    protected function prepareIdent($sIdent)
    {
        if ($sIdent) {
            return Str::getStr()->preg_replace("/[^a-zA-Z0-9_]*/", "", $sIdent);
        }
    }

    /**
     * Check if ident is unique
     *
     * @param string $sIdent ident
     * @param string $sOxId  Object id
     *
     * @return null
     */
    protected function checkIdent($sIdent, $sOxId)
    {
        // We force reading from master to prevent issues with slow replications or open transactions (see ESDEV-3804).
        $masterDb = DatabaseProvider::getMaster();

        $blAllow = false;

        // null not allowed
        if (!strlen($sIdent)) {
            $blAllow = true;
        // We force reading from master to prevent issues with slow replications or open transactions (see ESDEV-3804).
        } elseif (
            $masterDb->getOne("select oxid from oxcontents where oxloadid = :oxloadid and oxid != :oxid and oxshopid = :oxshopid", [
            'oxloadid' => $sIdent,
            'oxid' => $sOxId,
            'oxshopid' => \OxidEsales\Eshop\Core\Registry::getConfig()->getShopId()
            ])
        ) {
            $blAllow = true;
        }

        return $blAllow;
    }

    private function prepareAndSaveContent(array $requestParams, $contentId, $lang): void
    {
        $requestParams['oxcontents__oxcontent'] = ContainerFacade::get(HtmlSanitizerInterface::class)
            ->sanitize($requestParams['oxcontents__oxcontent']);

        if (!isset($requestParams['oxcontents__oxactive'])) {
            $requestParams['oxcontents__oxactive'] = 0;
        }

        $content = oxNew(Content::class);

        if ($contentId != "-1") {
            $content->loadInLang($lang, $contentId);
        } else {
            $requestParams['oxcontents__oxid'] = null;
        }

        $content->setLanguage(0);
        $content->assign($requestParams);
        $content->setLanguage($lang);
        $content->save();

        $this->setEditObjectId($content->getId());
    }

    private function handleSaveError($contentId, $requestParams): void
    {
        $content = oxNew(Content::class);
        if ($contentId != '-1') {
            $content->load($contentId);
        }
        $content->assign($requestParams);
        $this->_aViewData["edit"] = $content;
    }
}
