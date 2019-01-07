<?php
namespace Step\Acceptance;

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

        $page = new ProductDetails($I);

        $I->amOnPage($page->route($productId));
        return $page;
    }
}