<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Component\Widget;

/**
 * Product Ratings widget.
 * Forms product ratings.
 */
class Rating extends \OxidEsales\Eshop\Application\Component\Widget\WidgetController
{
    /**
     * Names of components (classes) that are initiated and executed
     * before any other regular operation.
     * User component used in template.
     *
     * @var array
     */
    protected $_aComponentNames = [
        'oxcmp_user' => 1,
    ];

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'widget/reviews/rating.tpl';

    /**
     * Rating value.
     *
     * @var float
     */
    protected $_dRatingValue = null;

    /**
     * Rating count.
     *
     * @var int
     */
    protected $_iRatingCnt = null;

    /**
     * Executes parent::render().
     * Returns name of template file to render.
     *
     * @return string current template file name
     */
    public function render()
    {
        parent::render();

        return $this->_sThisTemplate;
    }

    /**
     * Template variable getter. Returns rating value.
     *
     * @return float
     */
    public function getRatingValue()
    {
        if (null === $this->_dRatingValue) {
            $this->_dRatingValue = (float)0;
            $dValue = $this->getViewParameter('dRatingValue');
            if ($dValue) {
                $this->_dRatingValue = round($dValue, 1);
            }
        }

        return (float)$this->_dRatingValue;
    }

    /**
     * Template variable getter. Returns rating count.
     *
     * @return int
     */
    public function getRatingCount()
    {
        return $dCount = $this->getViewParameter('dRatingCount');
    }

    /**
     * Template variable getter. Returns rating url.
     *
     * @return string
     */
    public function getRateUrl()
    {
        return $this->getViewParameter('sRateUrl');
    }

    /**
     * Template variable getter. Returns rating count.
     *
     * @return int
     */
    public function canRate()
    {
        return $this->getViewParameter('blCanRate');
    }

    /**
     * Template variable getter. Returns article nid.
     *
     * @return string
     */
    public function getArticleNId()
    {
        return $this->getViewParameter('anid');
    }
}
