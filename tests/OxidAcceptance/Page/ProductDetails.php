<?php
namespace OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page;

use OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Account\UserLogin;
use OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Footer\ServiceWidget;
use OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Header\AccountMenu;
use OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Header\LanguageMenu;
use OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Header\MiniBasket;
use OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Header\Navigation;

class ProductDetails extends Page implements ProductDetailsInterface
{
    use AccountMenu, LanguageMenu, MiniBasket, Navigation, ServiceWidget;

    protected $webElementName = 'WebElement\ProductDetails';

    /**
     * Basic route example for your current URL
     * You can append any additional parameter to URL
     * and use it in tests like: Page\Edit::route('/123-post');
     */
    public function route($param)
    {
        return $this->webElement->URL.'/index.php?'.http_build_query(['cl' => 'details', 'anid' => $param]);
    }

    /**
     * Assert if user cannot buy current product
     *
     * @return $this
     */
    public function checkIfProductIsNotBuyable()
    {
        $I = $this->user;
        $I->seeElement($this->webElement->disabledBasketButton);
        return $this;
    }

    /**
     * Assert if user can buy current product
     *
     * @return $this
     */
    public function checkIfProductIsBuyable()
    {
        $I = $this->user;
        $I->dontSeeElement($this->webElement->disabledBasketButton);
        return $this;
    }

    /**
     * @param int    $variant
     * @param string $variantValue
     * @param string $waitForText
     *
     * @return $this
     */
    public function selectVariant($variant, $variantValue, $waitForText = null)
    {
        $I = $this->user;
        $I->click(sprintf($this->webElement->variantSelection, $variant));
        $I->click($variantValue);
        //wait for JS to finish
        $I->waitForJS("return $.active == 0;",10);
        return $this;
    }

    /**
     * @param int    $variant
     * @param string $variantValue
     *
     * @return $this
     */
    public function seeVariant($variant, $variantValue)
    {
        $I = $this->user;
        $I->click(sprintf($this->webElement->variantSelection, $variant));
        $I->see($variantValue);
        $I->click(sprintf($this->webElement->variantSelection, $variant));
        return $this;
    }

    /**
     * @param int    $variant
     * @param string $variantValue
     *
     * @return $this
     */
    public function dontSeeVariant($variant, $variantValue)
    {
        $I = $this->user;
        $I->click(sprintf($this->webElement->variantSelection, $variant));
        $I->dontSee($variantValue);
        $I->click(sprintf($this->webElement->variantSelection, $variant));
        return $this;
    }

    /**
     * @return $this
     */
    public function addToCompareList()
    {
        $I = $this->user;
        $I->click($this->webElement->addToCompareListLink);
        return $this;
    }

    /**
     * @return $this
     */
    public function removeFromCompareList()
    {
        $I = $this->user;
        //TODO: not like in azure
        $I->click($this->webElement->addToCompareListLink);
        return $this;
    }

    /**
     * @return $this
     */
    public function addToWishList()
    {
        $I = $this->user;
        $I->click($this->webElement->addToWishListLink);
        return $this;
    }

    /**
     * @return $this
     */
    public function removeFromWishList()
    {
        $I = $this->user;
        $I->click($this->webElement->addToWishListLink);
        return $this;
    }

    /**
     * @return $this
     */
    public function addToGiftRegistryList()
    {
        $I = $this->user;
        $I->click($this->webElement->addToGiftRegistryLink);
        return $this;
    }

    /**
     * @return $this
     */
    public function removeFromGiftRegistryList()
    {
        $I = $this->user;
        $I->click($this->webElement->addToGiftRegistryLink);
        return $this;
    }

    /**
     * @param string $userName
     * @param string $userPassword
     *
     * @return $this
     */
    public function loginUserForReview($userName, $userPassword)
    {
        $I = $this->user;
        $I->click($this->webElement->reviewLoginLink);
        $breadCrumb = $I->translate('YOU_ARE_HERE').':'.$I->translate('LOGIN');
        $I->see($breadCrumb, UserLogin::$breadCrumb);
        $userLoginPage = new UserLogin($I);
        $userLoginPage->login($userName, $userPassword);
        return $this;
    }

    /**
     * @param string $review
     * @param int    $rating
     *
     * @return $this
     */
    public function addReviewAndRating($review, $rating)
    {
        $I = $this->user;
        $I->click($this->webElement->openReviewForm);
       // $I->waitForElement(self::$reviewTextForm);
        $I->fillField($this->webElement->reviewTextForm, $review);
        $I->click(sprintf($this->webElement->ratingSelection, $rating));
        $I->click($this->webElement->saveRatingAndReviewButton);
        return $this;
    }

    /**
     * @param int    $reviewId The position of the review item.
     * @param string $userName
     * @param string $reviewText
     * @param int    $rating
     *
     * @return $this
     */
    public function seeUserProductReviewAndRating($reviewId, $userName, $reviewText, $rating)
    {
        $I = $this->user;
        $I->see($userName, sprintf($this->webElement->productReviewAuthor, $reviewId));
        $I->see($reviewText, sprintf($this->webElement->productReviewText, $reviewId));
        $I->seeNumberOfElements(sprintf($this->webElement->userProductRating, $reviewId), $rating);
        return $this;
    }

    /**
     * Opens recommend page.
     *
     * @return ProductSuggestion
     */
    public function openProductSuggestionPage()
    {
        $I = $this->user;
        $I->click($this->webElement->productSuggestionLink);
        $breadCrumb = $I->translate('YOU_ARE_HERE').':'.$I->translate('RECOMMEND_PRODUCT');
        $I->see($breadCrumb, ProductSuggestion::$breadCrumb);
        $I->see($I->translate('RECOMMEND_PRODUCT'), ProductSuggestion::$headerTitle);
        return new ProductSuggestion($I);
    }

    /**
     * @param string $email
     * @param double $price
     *
     * @return $this
     */
    public function sendPriceAlert($email, $price)
    {
        $I = $this->user;
        $this->openPriceAlert();
        $I->fillField($this->webElement->priceAlertEmail, $email);
        $I->fillField($this->webElement->priceAlertSuggestedPrice, $price);
        $I->click($I->translate('SEND'));
        return $this;
    }

    /**
     * @return $this
     */
    public function openPriceAlert()
    {
        $I = $this->user;
        $I->click($I->translate('PRICE_ALERT'));
        $I->see($I->translate('MESSAGE_PRICE_ALARM_PRICE_CHANGE'));
        return $this;
    }

    /**
     * @return $this
     */
    public function openAttributes()
    {
        $I = $this->user;
        $I->click($I->translate('SPECIFICATION'));
        return $this;
    }

    /**
     * @return $this
     */
    public function openDescription()
    {
        $I = $this->user;
        $I->click($I->translate('DESCRIPTION'));
        return $this;
    }

    /**
     * @param array $productData
     *
     * @return $this
     */
    public function seeProductData($productData)
    {
        $I = $this->user;
        $I->see($productData['title'], $this->webElement->productTitle);
        $I->see($productData['desc'], $this->webElement->productShortDesc);
        $I->see($productData['id']);
        $I->see($productData['price'], $this->webElement->productPrice);
        return $this;
    }

    public function seeProductOldPrice($price)
    {
        $I = $this->user;
        $I->see($price, $this->webElement->productOldPrice);
        return $this;
    }

    public function seeProductUnitPrice($price)
    {
        $I = $this->user;
        $I->see($price, $this->webElement->productUnitPrice);
        return $this;
    }

    /**
     * Add current product to basket
     *
     * @param int $amount
     *
     * @return $this
     */
    public function addProductToBasket($amount = 1)
    {
        $I = $this->user;
        $I->fillField($this->webElement->basketAmountField, $amount);
        $I->click($this->webElement->toBasketButton);
        return $this;
    }

    /**
     * @param array $productData
     * @param int   $position
     *
     * @return $this
     */
    public function seeAccessoryData($productData, $position = 1)
    {
        $I = $this->user;
        $I->see($productData['title'], sprintf($this->webElement->accessoriesProductTitle, $position));
        $I->see($productData['price'], sprintf($this->webElement->accessoriesProductPrice, $position));
        return $this;
    }

    /**
     * @param int   $position
     *
     * @return $this
     */
    public function openAccessoryDetailsPage($position = 1)
    {
        $I = $this->user;
        $I->click(sprintf($this->webElement->accessoriesProductTitle, $position));
        return $this;
    }

    /**
     * @param array $productData
     * @param int   $position
     *
     * @return $this
     */
    public function seeSimilarProductData($productData, $position = 1)
    {
        $I = $this->user;
        $I->see($productData['title'], sprintf($this->webElement->similarProductTitle, $position));
        $I->see($productData['price'], sprintf($this->webElement->similarProductPrice, $position));
        return $this;
    }

    /**
     * @param int   $position
     *
     * @return $this
     */
    public function openSimilarProductDetailsPage($position = 1)
    {
        $I = $this->user;
        $I->click(sprintf($this->webElement->similarProductTitle, $position));
        return $this;
    }

    /**
     * @param array $productData
     * @param int   $position
     *
     * @return $this
     */
    public function seeCrossSellingData($productData, $position = 1)
    {
        $I = $this->user;
        $I->see($productData['title'], sprintf($this->webElement->crossSellingProductTitle, $position));
        $I->see($productData['price'], sprintf($this->webElement->crossSellingProductPrice, $position));
        return $this;
    }

    /**
     * @param int   $position
     *
     * @return $this
     */
    public function openCrossSellingDetailsPage($position = 1)
    {
        $I = $this->user;
        $I->click(sprintf($this->webElement->crossSellingProductTitle, $position));
        return $this;
    }

    /**
     * @param array $amountPrices
     *
     * @return $this
     */
    public function seeAmountPrices($amountPrices)
    {
        $I = $this->user;
        $I->click($I->translate('BLOCK_PRICE'));
        $I->waitForElementVisible(sprintf($this->webElement->amountPriceQuantity, 1));
        $itemPosition = 1;
        foreach ($amountPrices as $key => $discount) {
            $fromAmount = $I->translate('FROM').' '.$key.' '.$I->translate('PCS');
            $I->see($fromAmount, sprintf($this->webElement->amountPriceQuantity, $itemPosition));
            $I->see($discount, sprintf($this->webElement->amountPriceValue, $itemPosition));
            $itemPosition++;
        }
        $I->click($this->webElement->amountPriceCloseButton);
        return $this;
    }

    /**
     * @return $this
     */
    public function openNextProduct()
    {
        $I = $this->user;
        $I->click($this->webElement->nextProductLink);
        return $this;
    }

    /**
     * @return $this
     */
    public function openPreviousProduct()
    {
        $I = $this->user;
        $I->click($this->webElement->previousProductLink);
        return $this;
    }

    /**
     * @return ProductSearchList
     */
    public function openProductSearchList()
    {
        $I = $this->user;
        $I->click($I->translate('BACK_TO_OVERVIEW'));
        return new ProductSearchList($I);
    }

    public function selectSelectionListItem($selectionItem)
    {
        $I = $this->user;
        $I->click($this->webElement->selectionList);
        $I->click($selectionItem);
        $I->see($selectionItem, $this->webElement->selectionList);
        return $this;
    }

    /**
     * @param string $attributeName
     * @param int    $attributeId
     *
     * @return $this
     */
    public function seeAttributeName($attributeName, $attributeId)
    {
        $I = $this->user;
        $I->see($attributeName, sprintf($this->webElement->attributeName, $attributeId));
        return $this;
    }

    /**
     * @param string $attributeValue
     * @param int    $attributeId
     *
     * @return $this
     */
    public function seeAttributeValue($attributeValue, $attributeId)
    {
        $I = $this->user;
        $I->see($attributeValue, sprintf($this->webElement->attributeValue, $attributeId));
        return $this;
    }
}
