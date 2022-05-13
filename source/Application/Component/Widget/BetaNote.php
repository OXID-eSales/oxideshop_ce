<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace  OxidEsales\EshopCommunity\Application\Component\Widget;

/**
 * Beta note widget
 *
 * @deprecated since v6.5.3 (2020-03-23); Betanote is not used anymore.
 */
class BetaNote extends \OxidEsales\Eshop\Application\Component\Widget\WidgetController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'widget/header/betanote.tpl';

    protected $_sBetaNoteLink = '';

    /**
     * Gets beta note link
     *
     * @return string
     */
    public function getBetaNoteLink()
    {
        return $this->_sBetaNoteLink;
    }

    /**
     * Sets beta note link
     *
     * @param string $sLink link to set
     */
    public function setBetaNoteLink($sLink)
    {
        $this->_sBetaNoteLink = $sLink;
    }
}
