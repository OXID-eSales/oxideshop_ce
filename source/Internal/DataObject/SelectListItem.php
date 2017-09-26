<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 25.09.17
 * Time: 15:40
 */

namespace OxidEsales\EshopCommunity\Internal\DataObject;


class SelectListItem
{
    const DELTA_TYPE_PERCENT = 1;
    const DELTA_TYPE_ABSOLUTE = 2;

    /** @var  string $articleId */
    private $articleId;
    /** @var  string $fieldKey */
    private $fieldKey;
    /** @var  double $priceDelta */
    private $priceDelta;
    /** @var  int $deltaType */
    private $deltaType;

    /**
     * SelectListItem constructor.
     *
     * @param string $articleId
     * @param string $fieldKey
     * @param double $priceDelta
     * @param int $deltaType
     */
    public function __construct($articleId, $fieldKey, $priceDelta, $deltaType) {

        $this->articleId = $articleId;
        $this->fieldKey = $fieldKey;
        $this->priceDelta = $priceDelta;
        $this->deltaType = $deltaType;
    }

    /**
     * @return string
     */
    public  function getArticleId() {
        return $this->articleId;
    }

    /**
     * @return string
     */
    public  function getFieldKey() {
        return $this->fieldKey;
    }

    /**
     * @return float
     */
    public  function getPriceDelta() {
        return $this->priceDelta;
    }

    /**
     * @return int
     */
    public  function getDeltaType() {
        return $this->deltaType;
    }

}