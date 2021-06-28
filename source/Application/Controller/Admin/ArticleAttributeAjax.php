<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;

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
    protected function _getQuery() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sArtId = Registry::getRequest()->getRequestEscapedParameter('oxid');
        $sSynchArtId = Registry::getRequest()->getRequestEscapedParameter('synchoxid');

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
        $sOxid = Registry::getRequest()->getRequestEscapedParameter('oxid');
        if (Registry::getRequest()->getRequestEscapedParameter('all')) {
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
        $soxId = Registry::getRequest()->getRequestEscapedParameter('synchoxid');

        if (Registry::getRequest()->getRequestEscapedParameter('all')) {
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

        $articleId = Registry::getRequest()->getRequestEscapedParameter("oxid");
        $attributeId = Registry::getRequest()->getRequestEscapedParameter("attr_oxid");
        $attributeValue = Registry::getRequest()->getRequestEscapedParameter("attr_value");

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
                $objectToAttribute->setLanguage(Registry::getRequest()->getRequestEscapedParameter('editlanguage'));
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
