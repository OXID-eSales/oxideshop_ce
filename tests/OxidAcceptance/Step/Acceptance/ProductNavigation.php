<?php
namespace OxidEsales\EshopCommunity\Tests\OxidAcceptance\Step\Acceptance;

use OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\ProductDetails;

class ProductNavigation extends \AcceptanceTester
{

    /**
     * Open product details page.
     *
     * @param string $productId The Id of the product
     *
     * @return ProductDetails
     */
    public function openProductDetailsPage($productId)
    {
        $I = $this;

        $I->amOnPage(ProductDetails::route($productId));
        return new ProductDetails($I);
    }
}