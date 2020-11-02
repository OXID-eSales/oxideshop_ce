<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Content seo config class.
 */
class ContentSeo extends \OxidEsales\Eshop\Application\Controller\Admin\ObjectSeo
{
    /**
     * Returns url type.
     *
     * @return string
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "getType" in next major
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _getType()
    {
        return 'oxcontent';
    }

    /**
     * Returns current object type seo encoder object.
     *
     * @return \OxidEsales\Eshop\Application\Model\SeoEncoderContent
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "getEncoder" in next major
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _getEncoder()
    {
        return \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Application\Model\SeoEncoderContent::class);
    }

    /**
     * Returns seo uri.
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
