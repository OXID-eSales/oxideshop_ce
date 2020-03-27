<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;

/**
 * Content seo config class
 */
class ContentSeo extends \OxidEsales\Eshop\Application\Controller\Admin\ObjectSeo
{
    /**
     * @deprecated use self::getType instead
     */
    protected function _getType() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return $this->getType();
    }
    /**
     * Returns url type
     *
     * @return string
     */
    protected function getType()
    {
        return 'oxcontent';
    }
    /**
     * @deprecated use self::getEncoder instead
     */
    protected function _getEncoder() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return $this->getEncoder();
    }

    /**
     * Returns current object type seo encoder object
     *
     * @return oxSeoEncoderContent
     */
    protected function getEncoder()
    {
        return \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Application\Model\SeoEncoderContent::class);
    }

    /**
     * Returns seo uri
     *
     * @return string
     */
    public function getEntryUri()
    {
        $oContent = oxNew(\OxidEsales\Eshop\Application\Model\Content::class);
        if ($oContent->load($this->getEditObjectId())) {
            return $this->_getEncoder()->getContentUri($oContent, $this->getEditLang());
        }
    }
}
