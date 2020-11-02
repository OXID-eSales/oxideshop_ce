<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Exception\InputException;
use stdClass;

/**
 * Admin article main discount manager.
 * Performs collection and updating (on user submit) main item information.
 * Admin Menu: Shop Settings -> Discounts -> Main.
 */
class DiscountMain extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Executes parent method parent::render(), creates article category tree, passes
     * data to Smarty engine and returns name of template file "discount_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $sOxId = $this->_aViewData['oxid'] = $this->getEditObjectId();
        if (isset($sOxId) && '-1' !== $sOxId) {
            // load object
            $oDiscount = oxNew(\OxidEsales\Eshop\Application\Model\Discount::class);
            $oDiscount->loadInLang($this->_iEditLang, $sOxId);

            $oOtherLang = $oDiscount->getAvailableInLangs();
            if (!isset($oOtherLang[$this->_iEditLang])) {
                // echo "language entry doesn't exist! using: ".key($oOtherLang);
                $oDiscount->loadInLang(key($oOtherLang), $sOxId);
            }

            $this->_aViewData['edit'] = $oDiscount;

            //disabling derived items
            if ($oDiscount->isDerived()) {
                $this->_aViewData['readonly'] = true;
            }

            // remove already created languages
            $aLang = array_diff(\OxidEsales\Eshop\Core\Registry::getLang()->getLanguageNames(), $oOtherLang);

            if (\count($aLang)) {
                $this->_aViewData['posslang'] = $aLang;
            }

            foreach ($oOtherLang as $id => $language) {
                $oLang = new stdClass();
                $oLang->sLangDesc = $language;
                $oLang->selected = ($id === $this->_iEditLang);
                $this->_aViewData['otherlang'][$id] = clone $oLang;
            }
        }

        if (($iAoc = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('aoc'))) {
            if ('1' === $iAoc) {
                $oDiscountMainAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\DiscountMainAjax::class);
                $this->_aViewData['oxajax'] = $oDiscountMainAjax->getColumns();

                return 'popups/discount_main.tpl';
            } elseif ('2' === $iAoc) {
                // generating category tree for artikel choose select list
                $this->_createCategoryTree('artcattree');

                $oDiscountItemAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\DiscountItemAjax::class);
                $this->_aViewData['oxajax'] = $oDiscountItemAjax->getColumns();

                return 'popups/discount_item.tpl';
            }
        }

        return 'discount_main.tpl';
    }

    /**
     * Returns item discount product title.
     *
     * @return string
     */
    public function getItemDiscountProductTitle()
    {
        $sTitle = false;
        $sOxId = $this->getEditObjectId();
        if (isset($sOxId) && '-1' !== $sOxId) {
            $sViewName = getViewName('oxarticles', $this->_iEditLang);
            // Reading from slave is ok here (see ESDEV-3804 and ESDEV-3822).
            $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
            $sQ = "select concat( $sViewName.oxartnum, ' ', $sViewName.oxtitle ) from oxdiscount
                   left join $sViewName on $sViewName.oxid=oxdiscount.oxitmartid
                   where oxdiscount.oxitmartid != '' and oxdiscount.oxid = :oxid";
            $sTitle = $database->getOne($sQ, [
                ':oxid' => $sOxId,
            ]);
        }

        return $sTitle ?: ' -- ';
    }

    /**
     * Saves changed selected discount parameters.
     *
     * @return mixed
     */
    public function save()
    {
        parent::save();

        $sOxId = $this->getEditObjectId();
        $aParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('editval');

        $oDiscount = oxNew(\OxidEsales\Eshop\Application\Model\Discount::class);
        if ('-1' !== $sOxId) {
            $oDiscount->load($sOxId);
        } else {
            $aParams['oxdiscount__oxid'] = null;
        }

        // checkbox handling
        if (!isset($aParams['oxdiscount__oxactive'])) {
            $aParams['oxdiscount__oxactive'] = 0;
        }

        //disabling derived items
        if ($oDiscount->isDerived()) {
            return;
        }

        //$aParams = $oAttr->ConvertNameArray2Idx( $aParams);
        $oDiscount->setLanguage(0);
        $oDiscount->assign($aParams);
        $oDiscount->setLanguage($this->_iEditLang);
        $oDiscount = \OxidEsales\Eshop\Core\Registry::getUtilsFile()->processFiles($oDiscount);
        try {
            $oDiscount->save();
        } catch (InputException $exception) {
            $newException = oxNew(\OxidEsales\Eshop\Core\Exception\ExceptionToDisplay::class);
            $newException->setMessage($exception->getMessage());
            $this->addTplParam('discount_title', $aParams['oxdiscount__oxtitle']);

            if (false !== strpos($exception->getMessage(), 'DISCOUNT_ERROR_OXSORT')) {
                $messageArgument = \OxidEsales\Eshop\Core\Registry::getLang()->translateString('DISCOUNT_MAIN_SORT', \OxidEsales\Eshop\Core\Registry::getLang()->getTplLanguage(), true);
                $newException->setMessageArgs($messageArgument);
            }

            \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay($newException);

            return;
        }

        // set oxid if inserted
        $this->setEditObjectId($oDiscount->getId());
    }

    /**
     * Saves changed selected discount parameters in different language.
     */
    public function saveinnlang(): void
    {
        parent::save();

        $sOxId = $this->getEditObjectId();
        $aParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('editval');

        $oAttr = oxNew(\OxidEsales\Eshop\Application\Model\Discount::class);
        if ('-1' !== $sOxId) {
            $oAttr->load($sOxId);
        } else {
            $aParams['oxdiscount__oxid'] = null;
        }
        // checkbox handling
        if (!isset($aParams['oxdiscount__oxactive'])) {
            $aParams['oxdiscount__oxactive'] = 0;
        }

        //disabling derived items
        if ($oAttr->isDerived()) {
            return;
        }

        //$aParams = $oAttr->ConvertNameArray2Idx( $aParams);
        $oAttr->setLanguage(0);
        $oAttr->assign($aParams);
        $oAttr->setLanguage($this->_iEditLang);
        $oAttr = \OxidEsales\Eshop\Core\Registry::getUtilsFile()->processFiles($oAttr);
        $oAttr->save();

        // set oxid if inserted
        $this->setEditObjectId($oAttr->getId());
    }

    /**
     * Increment the maximum value of oxsort found in the database by certain amount and return it.
     *
     * @return int the incremented oxsort
     */
    public function getNextOxsort()
    {
        $shopId = \OxidEsales\Eshop\Core\Registry::getConfig()->getShopId();

        return oxNew(\OxidEsales\Eshop\Application\Model\Discount::class)->getNextOxsort($shopId);
    }
}
