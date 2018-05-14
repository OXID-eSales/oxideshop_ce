<?php
namespace Step\Acceptance\Compare;

use Page\ProductDetails;

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