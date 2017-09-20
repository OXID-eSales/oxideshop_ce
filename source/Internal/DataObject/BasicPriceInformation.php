<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 08.08.17
 * Time: 13:48
 */

namespace OxidEsales\EshopCommunity\Internal\DataObject;


class BasicPriceInformation
{

    /** @var string $articleId */
    private $articleId;

    /** @var double $basePrice */
    private $basePrice;

    /** @var array $groupPrices */
    private $groupPrices = ['a' => null, 'b' => null, 'c' => null];

    /** @var double $recommendedRetailPrice */
    private $recommendedRetailPrice;

    /** @var  double $vat */
    private $vat = null;

    public function __construct(array $childData, array $parentData)
    {
        $this->articleId = $childData['oxid'];
        $this->basePrice = $childData['oxprice'] > 0.0 ? $childData['oxprice'] : $parentData['oxprice'];
        $this->groupPrices['a'] = $childData['oxpricea'] > 0.0 ? $childData['oxpricea'] : $parentData['oxpricea'];
        $this->groupPrices['b'] = $childData['oxpriceb'] > 0.0 ? $childData['oxpriceb'] : $parentData['oxpriceb'];
        $this->groupPrices['c'] = $childData['oxpricec'] > 0.0 ? $childData['oxpricec'] : $parentData['oxpricec'];
        $this->recommendedRetailPrice = $childData['oxtprice'] > 0.0 ? $childData['oxtprice'] : $parentData['oxtprice'];
        $this->vat = $childData['oxvat'] > 0.0 ? $childData['oxvat'] : $parentData['oxvat'];
    }

    public function getArticleId()
    {
        return $this->articleId;
    }

    public function getBasePrice()
    {
        return $this->basePrice;
    }

    public function getGroupPrice($group)
    {

        if (!array_key_exists($group, $this->groupPrices)) {
            throw new \Exception("Group $group is not a valid price group.");
        }

        return $this->groupPrices[$group];
    }

    public function getRecommendedRetailPrice()
    {
        return $this->recommendedRetailPrice;
    }

    public function getVat()
    {
        return $this->vat;
    }

}
