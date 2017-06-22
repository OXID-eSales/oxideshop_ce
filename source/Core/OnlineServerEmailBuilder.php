<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * Class OnlineServerEmailBuilder is responsible for generation of email with specific message
 * when it's not possible to make OLIS call via CURL.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
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
