<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Adapter;

use OxidEsales\Eshop\Core\MailValidator;
use OxidEsales\Eshop\Core\Registry;

/**
 * @internal
 */
class ShopAdapter implements ShopAdapterInterface
{
    /**
     * @param string $email
     * @return bool
     */
    public function isValidEmail($email)
    {
        $emailValidator = oxNew(MailValidator::class);

        return $emailValidator->isValidEmail($email);
    }

    /**
     * @param string $string
     * @return string
     */
    public function translateString($string)
    {
        $lang = Registry::getLang();

        return $lang->translateString($string);
    }
}
