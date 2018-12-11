<?php
namespace OxidEsales\EshopCommunity\Tests\OxidAcceptance\Step\Acceptance\Compare;

use OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\ProductDetails;

class ProductCompareList extends \AcceptanceTester
{

    /**
     * Open product details page.
     *
     * @param ProductDetails $detailsPage
     * @param int            $itemCount
     *
     * @return ProductDetails
     */
    public function addProductToCompareList($detailsPage, $itemCount)
    {
        //add to compare list
        $detailsPage = $detailsPage->addToCompareList();
        $detailsPage->openAccountMenu()->checkCompareListItemCount($itemCount)->closeAccountMenu();
        return $detailsPage;
    }
}