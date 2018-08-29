<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use oxDb;
use oxField;

/**
 * Class controls article assignment to attributes
 */
class ArticleAttributeAjax extends \OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax
{
    /**
     * Columns array
     *
     * @var array
     */
    protected $_aColumns = ['container1' => [ // field , table,         visible, multilanguage, ident
        ['oxtitle', 'oxattribute', 1, 1, 0],
        ['oxid', 'oxattribute', 0, 0, 1]
    ],
                                 'container2' => [
                                     ['oxtitle', 'oxattribute', 1, 1, 0],
                                     ['oxid', 'oxobject2attribute', 0, 0, 1],
                                     ['oxvalue', 'oxobject2attribute', 0, 1, 1],
                                     ['oxattrid', 'oxobject2attribute', 0, 0, 1],
                                 ]
    ];

    /**
     * Returns SQL query for data to fetc
     *
     * @return string
     */
    protected function _getQuery()
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sArtId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('oxid');
        $sSynchArtId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('synchoxid');

        $sAttrViewName = $this->_getViewName('oxattribute');
        $sO2AViewName = $this->_getViewName('oxobject2attribute');
        if ($sArtId) {
            // all categories article is in
            $sQAdd = " from {$sO2AViewName} left join {$sAttrViewName} " .
                     "on {$sAttrViewName}.oxid={$sO2AViewName}.oxattrid " .
                     " where {$sO2AViewName}.oxobjectid = " . $oDb->quote($sArtId) . " ";
        } else {
            $sQAdd = " from {$sAttrViewName} where {$sAttrViewName}.oxid not in ( select {$sO2AViewName}.oxattrid " .
                     "from {$sO2AViewName} left join {$sAttrViewName} " .
                     "on {$sAttrViewName}.oxid={$sO2AViewName}.oxattrid " .
                     " where {$sO2AViewName}.oxobjectid = " . $oDb->quote($sSynchArtId) . " ) ";
        }

        return $sQAdd;
    }

    /**
     * Removes article attributes.
     */
    public function removeAttr()
    {
        $aChosenArt = $this->_getActionIds('oxobject2attribute.oxid');
        $sOxid = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('oxid');
        if (\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('all')) {
            $sO2AViewName = $this->_getViewName('oxobject2attribute');
            $sQ = $this->_addFilter("delete $sO2AViewName.* " . $this->_getQuery());
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->Execute($sQ);
        } elseif (is_array($aChosenArt)) {
            $sChosenArticles = implode(", ", \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quoteArray($aChosenArt));
            $sQ = "delete from oxobject2attribute where oxobject2attribute.oxid in ({$sChosenArticles}) ";
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->Execute($sQ);
        }

        $this->onArticleAttributeRelationChange($sOxid);
    }

    /**
     * Adds attributes to article.
     */
    public function addAttr()
    {
        $aAddCat = $this->_getActionIds('oxattribute.oxid');
        $soxId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('synchoxid');

        if (\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('all')) {
            $sAttrViewName = $this->_getViewName('oxattribute');
            $aAddCat = $this->_getAll($this->_addFilter("select $sAttrViewName.oxid " . $this->_getQuery()));
        }

        if ($soxId && $soxId != "-1" && is_array($aAddCat)) {
            foreach ($aAddCat as $sAdd) {
                $oNew = oxNew(\OxidEsales\Eshop\Core\Model\BaseModel::class);
                $oNew->init("oxobject2attribute");
                $oNew->oxobject2attribute__oxobjectid = new \OxidEsales\Eshop\Core\Field($soxId);
                $oNew->oxobject2attribute__oxattrid = new \OxidEsales\Eshop\Core\Field($sAdd);
                $oNew->save();
            }

            $this->onArticleAttributeRelationChange($soxId);
        }
    }

    /**
     * Saves attribute value
     *
     * @return null
     */
    public function saveAttributeValue()
    {
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $this->resetContentCache();

        $articleId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("oxid");
        $attributeId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("attr_oxid");
        $attributeValue = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("attr_value");

        $article = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        if ($article->load($articleId)) {
            if ($article->isDerived()) {
                return;
            }

            $this->onAttributeValueChange($article);

            if (isset($attributeId) && ("" != $attributeId)) {
                $viewName = $this->_getViewName("oxobject2attribute");
                $quotedArticleId = $database->quote($article->oxarticles__oxid->value);
                $select = "select * from {$viewName} where {$viewName}.oxobjectid= {$quotedArticleId} and
                            {$viewName}.oxattrid= " . $database->quote($attributeId);
                $objectToAttribute = oxNew(\OxidEsales\Eshop\Core\Model\MultiLanguageModel::class);
                $objectToAttribute->setLanguage(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('editlanguage'));
                $objectToAttribute->init("oxobject2attribute");
                if ($objectToAttribute->assignRecord($select)) {
                    $objectToAttribute->oxobject2attribute__oxvalue->setValue($attributeValue);
                    $objectToAttribute->save();
                }
            }
        }
    }

    /**
     * Method is used to bind to attribute and article relation change action.
     *
     * @param string $articleId
     */
    protected function onArticleAttributeRelationChange($articleId)
    {
    }

    /**
     * Method is used to bind to attribute value change.
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $article
     */
    protected function onAttributeValueChange($article)
    {
    }
}
