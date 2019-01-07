<?php
namespace OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page;


interface ProductDetailsInterface
{
    /**
     * Basic route example for your current URL
     * You can append any additional parameter to URL
     * and use it in tests like: Page\Edit::route('/123-post');
     */
    public function route($param);

    /**
     * Assert if user cannot buy current product
     *
     * @return $this
     */
    public function checkIfProductIsNotBuyable();

    /**
     * Assert if user can buy current product
     *
     * @return $this
     */
    public function checkIfProductIsBuyable();

    /**
     * @param int    $variant
     * @param string $variantValue
     * @param string $waitForText
     *
     * @return $this
     */
    public function selectVariant($variant, $variantValue, $waitForText = null);

    /**
     * @param int    $variant
     * @param string $variantValue
     *
     * @return $this
     */
    public function seeVariant($variant, $variantValue);

    /**
     * @param int    $variant
     * @param string $variantValue
     *
     * @return $this
     */
    public function dontSeeVariant($variant, $variantValue);

    /**
     * @return $this
     */
    public function addToCompareList();

    /**
     * @return $this
     */
    public function removeFromCompareList();

    /**
     * @return $this
     */
    public function addToWishList();

    /**
     * @return $this
     */
    public function removeFromWishList();

    /**
     * @return $this
     */
    public function addToGiftRegistryList();

    /**
     * @return $this
     */
    public function removeFromGiftRegistryList();
    /**
     * @param string $userName
     * @param string $userPassword
     *
     * @return $this
     */
    public function loginUserForReview($userName, $userPassword);

    /**
     * @param string $review
     * @param int    $rating
     *
     * @return $this
     */
    public function addReviewAndRating($review, $rating);

    /**
     * @param int    $reviewId The position of the review item.
     * @param string $userName
     * @param string $reviewText
     * @param int    $rating
     *
     * @return $this
     */
    public function seeUserProductReviewAndRating($reviewId, $userName, $reviewText, $rating);

    /**
     * Opens recommend page.
     *
     * @return ProductSuggestion
     */
    public function openProductSuggestionPage();

    /**
     * @param string $email
     * @param double $price
     *
     * @return $this
     */
    public function sendPriceAlert($email, $price);

    /**
     * @return $this
     */
    public function openPriceAlert();

    /**
     * @return $this
     */
    public function openAttributes();

    /**
     * @return $this
     */
    public function openDescription();

    /**
     * @param array $productData
     *
     * @return $this
     */
    public function seeProductData($productData);

    public function seeProductOldPrice($price);

    public function seeProductUnitPrice($price);

    /**
     * Add current product to basket
     *
     * @param int $amount
     *
     * @return $this
     */
    public function addProductToBasket($amount = 1);

    /**
     * @param array $productData
     * @param int   $position
     *
     * @return $this
     */
    public function seeAccessoryData($productData, $position = 1);

    /**
     * @param int   $position
     *
     * @return $this
     */
    public function openAccessoryDetailsPage($position = 1);

    /**
     * @param array $productData
     * @param int   $position
     *
     * @return $this
     */
    public function seeSimilarProductData($productData, $position = 1);

    /**
     * @param int   $position
     *
     * @return $this
     */
    public function openSimilarProductDetailsPage($position = 1);

    /**
     * @param array $productData
     * @param int   $position
     *
     * @return $this
     */
    public function seeCrossSellingData($productData, $position = 1);

    /**
     * @param int   $position
     *
     * @return $this
     */
    public function openCrossSellingDetailsPage($position = 1);

    /**
     * @param array $amountPrices
     *
     * @return $this
     */
    public function seeAmountPrices($amountPrices);

    /**
     * @return $this
     */
    public function openNextProduct();

    /**
     * @return $this
     */
    public function openPreviousProduct();

    /**
     * @return ProductSearchList
     */
    public function openProductSearchList();

    public function selectSelectionListItem($selectionItem);

    /**
     * @param string $attributeName
     * @param int    $attributeId
     *
     * @return $this
     */
    public function seeAttributeName($attributeName, $attributeId);

    /**
     * @param string $attributeValue
     * @param int    $attributeId
     *
     * @return $this
     */
    public function seeAttributeValue($attributeValue, $attributeId);
}
