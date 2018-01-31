<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use stdClass;
use \OxidEsales\Eshop\Application\Model\Actions;
use \OxidEsales\Eshop\Core\Registry;
use \OxidEsales\Eshop\Core\Request;

/**
 * Admin article main actions manager.
 * There is possibility to change actions description, assign articles to
 * this actions, etc.
 * Admin Menu: Manage Products -> actions -> Main.
 */
class ActionsMain extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Loads article actionss info, passes it to Smarty engine and
     * returns name of template file "actions_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();

        if ($this->isNewEditObject() !== true) {
            $oAction = oxNew(Actions::class);
            $oAction->loadInLang($this->_iEditLang, $soxId);

            $oOtherLang = $oAction->getAvailableInLangs();
            if (!isset($oOtherLang[$this->_iEditLang])) {
                $oAction->loadInLang(key($oOtherLang), $soxId);
            }

            $this->_aViewData["edit"] = $oAction;

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
        }

        if (Registry::getConfig()->getRequestParameter("aoc")) {
            // generating category tree for select list
            $this->_createCategoryTree("artcattree", $soxId);

            $oActionsMainAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\ActionsMainAjax::class);
            $this->_aViewData['oxajax'] = $oActionsMainAjax->getColumns();

            return "popups/actions_main.tpl";
        }


        if (($oPromotion = $this->getViewDataElement("edit"))) {
            if (($oPromotion->oxactions__oxtype->value == 2) || ($oPromotion->oxactions__oxtype->value == 3)) {
                if ($iAoc = Registry::getConfig()->getRequestParameter("oxpromotionaoc")) {
                    $sPopup = false;
                    switch ($iAoc) {
                        case 'article':
                            // generating category tree for select list
                            $this->_createCategoryTree("artcattree", $soxId);

                            if ($oArticle = $oPromotion->getBannerArticle()) {
                                $this->_aViewData['actionarticle_artnum'] = $oArticle->oxarticles__oxartnum->value;
                                $this->_aViewData['actionarticle_title'] = $oArticle->oxarticles__oxtitle->value;
                            }

                            $sPopup = 'actions_article';
                            break;
                        case 'groups':
                            $sPopup = 'actions_groups';
                            break;
                    }

                    if ($sPopup) {
                        $oActionsArticleAjax = oxNew($sPopup . '_ajax');
                        $this->_aViewData['oxajax'] = $oActionsArticleAjax->getColumns();

                        return "popups/{$sPopup}.tpl";
                    }
                } else {
                    if ($oPromotion->oxactions__oxtype->value == 2) {
                        $this->_aViewData["editor"] = $this->_generateTextEditor(
                            "100%",
                            300,
                            $oPromotion,
                            "oxactions__oxlongdesc",
                            "details.tpl.css"
                        );
                    }
                }
            }
        }

        return "actions_main.tpl";
    }

    /**
     * Saves Promotions
     */
    public function save()
    {
        parent::save();

        $action = oxNew(Actions::class);

        if ($this->isNewEditObject() !== true) {
            $action->load($this->getEditObjectId());
        }

        if ($this->checkAccessToEditAction($action) === true) {
            $action->assign($this->getActionFormData());
            $action->setLanguage($this->_iEditLang);
            $action = Registry::getUtilsFile()->processFiles($action);
            $action->save();

            $this->setEditObjectId($action->getId());
        }
    }

    /**
     * Saves changed selected action parameters in different language.
     */
    public function saveinnlang()
    {
        $this->save();
    }

    /**
     * Checks access to edit Action.
     *
     * @param Actions $action
     *
     * @return bool
     */
    protected function checkAccessToEditAction(Actions $action)
    {
        return true;
    }

    /**
     * Returns form data for Action.
     *
     * @return array
     */
    private function getActionFormData()
    {
        $request    = oxNew(Request::class);
        $formData   = $request->getRequestEscapedParameter("editval");
        $formData   = $this->normalizeActionFormData($formData);

        return $formData;
    }

    /**
     * Normalizes form data for Action.
     *
     * @param   array $formData
     *
     * @return  array
     */
    private function normalizeActionFormData($formData)
    {
        if ($this->isNewEditObject() === true) {
            $formData['oxactions__oxid'] = null;
        }

        if (!$formData['oxactions__oxactive']) {
            $formData['oxactions__oxactive'] = 0;
        }

        return $formData;
    }
}
