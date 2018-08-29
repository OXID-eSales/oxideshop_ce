<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxField;

/**
 * Shopping basket item manager.
 * Manager class for shopping basket item (class may be overriden).
 *
 */
class UserBasketItem extends \OxidEsales\Eshop\Core\Model\BaseModel
{
    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxuserbasketitem';

    /**
     * Article object assigned to userbasketitem
     *
     * @var oxArticle
     */
    protected $_oArticle = null;

    /**
     * Variant parent "buyable" status
     *
     * @var bool
     */
    protected $_blParentBuyable = false;

    /**
     * Basket item selection list
     *
     * @var array
     */
    protected $_aSelList = null;

    /**
     * Basket item persistent parameters
     *
     * @var array
     */
    protected $_aPersParam = null;

    /**
     * Class constructor, initiates parent constructor (parent::oxBase()).
     */
    public function __construct()
    {
        $this->setVariantParentBuyable($this->getConfig()->getConfigParam('blVariantParentBuyable'));
        parent::__construct();
        $this->init('oxuserbasketitems');
    }

    /**
     * Variant parent "buyable" status setter
     *
     * @param bool $blBuyable parent "buyable" status
     */
    public function setVariantParentBuyable($blBuyable = false)
    {
        $this->_blParentBuyable = $blBuyable;
    }

    /**
     * Loads and returns the article for that basket item
     *
     * @param string $sItemKey the key that will be given to oxarticle setItemKey
     *
     * @throws oxArticleException article exception
     *
     * @return oxArticle
     */
    public function getArticle($sItemKey)
    {
        if (!$this->oxuserbasketitems__oxartid->value) {
            //this exception may not be caught, anyhow this is a critical exception
            $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\ArticleException::class);
            $oEx->setMessage('EXCEPTION_ARTICLE_NOPRODUCTID');
            throw $oEx;
        }

        if ($this->_oArticle === null) {
            $this->_oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);

            // performance
            /* removed due to #4178
             if ( $this->_blParentBuyable ) {
                $this->_oArticle->setNoVariantLoading( true );
            }
            */

            if (!$this->_oArticle->load($this->oxuserbasketitems__oxartid->value)) {
                return false;
            }

            $aSelList = $this->getSelList();
            if (($aSelectlist = $this->_oArticle->getSelectLists()) && is_array($aSelList)) {
                foreach ($aSelList as $iKey => $iSel) {
                    if (isset($aSelectlist[$iKey][$iSel])) {
                        // cloning select list information
                        $aSelectlist[$iKey][$iSel] = clone $aSelectlist[$iKey][$iSel];
                        $aSelectlist[$iKey][$iSel]->selected = 1;
                    }
                }
                $this->_oArticle->setSelectlist($aSelectlist);
            }

            // generating item key
            $this->_oArticle->setItemKey($sItemKey);
        }

        return $this->_oArticle;
    }

    /**
     * Does not return _oArticle var on serialisation
     *
     * @return array
     */
    public function __sleep()
    {
        $aRet = [];
        foreach (get_object_vars($this) as $sKey => $sVar) {
            if ($sKey != '_oArticle') {
                $aRet[] = $sKey;
            }
        }

        return $aRet;
    }

    /**
     * Basket item selection list getter
     *
     * @return array
     */
    public function getSelList()
    {
        if ($this->_aSelList == null && $this->oxuserbasketitems__oxsellist->value) {
            $this->_aSelList = unserialize($this->oxuserbasketitems__oxsellist->value);
        }

        return $this->_aSelList;
    }

    /**
     * Basket item selection list setter
     *
     * @param array $aSelList selection list
     */
    public function setSelList($aSelList)
    {
        $this->oxuserbasketitems__oxsellist = new \OxidEsales\Eshop\Core\Field(serialize($aSelList), \OxidEsales\Eshop\Core\Field::T_RAW);
    }

    /**
     * Basket item persistent parameters getter
     *
     * @return array
     */
    public function getPersParams()
    {
        if ($this->_aPersParam == null && $this->oxuserbasketitems__oxpersparam->value) {
            $this->_aPersParam = unserialize($this->oxuserbasketitems__oxpersparam->value);
        }

        return $this->_aPersParam;
    }

    /**
     * Basket item persistent parameters setter
     *
     * @param string $sPersParams persistent parameters
     */
    public function setPersParams($sPersParams)
    {
        $this->oxuserbasketitems__oxpersparam = new \OxidEsales\Eshop\Core\Field(serialize($sPersParams), \OxidEsales\Eshop\Core\Field::T_RAW);
    }

    /**
     * Sets data field value
     *
     * @param string $sFieldName index OR name (eg. 'oxarticles__oxtitle') of a data field to set
     * @param string $sValue     value of data field
     * @param int    $iDataType  field type
     *
     * @return null
     */
    protected function _setFieldData($sFieldName, $sValue, $iDataType = \OxidEsales\Eshop\Core\Field::T_TEXT)
    {
        if ('oxsellist' === strtolower($sFieldName) || 'oxuserbasketitems__oxsellist' === strtolower($sFieldName)
            || 'oxpersparam' === strtolower($sFieldName) || 'oxuserbasketitems__oxpersparam' === strtolower($sFieldName)
        ) {
            $iDataType = \OxidEsales\Eshop\Core\Field::T_RAW;
        }

        return parent::_setFieldData($sFieldName, $sValue, $iDataType);
    }
}
