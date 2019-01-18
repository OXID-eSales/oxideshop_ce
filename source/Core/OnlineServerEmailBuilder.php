<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * Class OnlineServerEmailBuilder is responsible for generation of email with specific message
 * when it's not possible to make OLIS call via CURL.
 *
 * @internal Do not make a module extension for this class.
 * @see      https://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 *
 * @ignore   This class will not be included in documentation.
 */
class OnlineServerEmailBuilder extends \OxidEsales\Eshop\Core\EmailBuilder
{
    const OLC_EMAIL = 'olc@oxid-esales.com';

    /**
     * @inheritdoc
     *
     * @return string
     */
    protected function getBody()
    {
        return $this->buildParam;
    }

    /**
     * @inheritdoc
     *
     * @return string
     */
    protected function getSubject()
    {
        return \OxidEsales\Eshop\Core\Registry::getLang()->translateString(
            'SUBJECT_UNABLE_TO_SEND_VIA_CURL',
            null,
            true
        );
    }

    /**
     * @inheritdoc
     *
     * @return string
     */
    protected function getRecipient()
    {
        return self::OLC_EMAIL;
    }
}
