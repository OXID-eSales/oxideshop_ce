<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Component\Widget;

/**
 * Product reviews widget
 */
class Review extends \OxidEsales\Eshop\Application\Component\Widget\WidgetController
{
    /**
     * Names of components (classes) that are initiated and executed
     * before any other regular operation.
     * User component used in template.
     *
     * @var array
     */
    protected $_aComponentNames = ['oxcmp_user' => 1];

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'widget/reviews/reviews.tpl';

    /**
     * Executes parent::render().
     * Returns name of template file to render.
     *
     * @return  string  current template file name
     */
    public function render()
    {
        parent::render();

        return $this->_sThisTemplate;
    }

    /**
     * Template variable getter. Returns review type
     *
     * @return string
     */
    public function getReviewType()
    {
        return strtolower($this->getViewParameter('type'));
    }

    /**
     * Template variable getter. Returns article id
     *
     * @return string
     */
    public function getArticleId()
    {
        return $this->getViewParameter('aid');
    }

    /**
     * Template variable getter. Returns article nid
     *
     * @return string
     */
    public function getArticleNId()
    {
        return $this->getViewParameter('anid');
    }

    /**
     * Template variable getter. Returns recommlist id
     *
     * @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
     *
     * @return string
     */
    public function getRecommListId()
    {
        return $this->getViewParameter('recommid');
    }

    /**
     * Template variable getter. Returns whether user can rate
     *
     * @return string
     */
    public function canRate()
    {
        return $this->getViewParameter('canrate');
    }

    /**
     * Template variable getter. Returns review user id
     *
     * @return string
     */
    public function getReviewUserHash()
    {
        return $this->getViewParameter('reviewuserhash');
    }

    /**
     * Template variable getter. Returns active object's reviews from parent class
     *
     * @return array
     */
    public function getReviews()
    {
        $oReview = $this->getConfig()->getTopActiveView();

        return $oReview->getReviews();
    }
}
