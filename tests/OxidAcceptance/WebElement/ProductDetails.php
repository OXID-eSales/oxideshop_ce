<?php
namespace OxidEsales\EshopCommunity\Tests\OxidAcceptance\WebElement;

class ProductDetails
{
    // include url of current page
    public $URL = '';

    // include bread crumb of current page
    public $breadCrumb = '#breadcrumb';

    public $nextProductLink = '#linkNextArticle';

    public $previousProductLink = '#linkPrevArticle';

    public $productTitle = '#productTitle';

    public $productShortDesc = '#productShortdesc';

    public $productArtNum = '';

    public $productOldPrice = '.pricebox del';

    public $productPrice = '#productPrice';

    public $productUnitPrice = '#productPriceUnit';

    public $toBasketButton = '#toBasket';

    public $basketAmountField = '#amountToBasket';

    public $addToCompareListLink = '#addToCompare';

    public $addToWishListLink = '#linkToNoticeList';

    public $addToGiftRegistryLink = '#linkToWishList';

    public $reviewLoginLink = '#reviewsLogin';

    public $openReviewForm = '#writeNewReview';

    public $reviewTextForm = 'rvw_txt';

    public $ratingSelection = '//ul[@id="reviewRating"]/li[%s]';

    public $saveRatingAndReviewButton = '#reviewSave';

    public $productReviewAuthor = '//div[@id="reviewName_%s"]/div[2]/div/div[1]/span[1]';

    public $productReviewText = '#reviewText_%s';

    public $userProductRating = '//div[@id="reviewName_%s"]/div[2]/div/div[2]/div[1]/i[@class="fa fa-star"]';

    public $productSuggestionLink = '#suggest';

    public $priceAlertEmail = 'pa[email]';

    public $priceAlertSuggestedPrice = 'pa[price]';

    public $accessoriesProductTitle = '#accessories_%s';

    public $accessoriesProductPrice = '//form[@name="tobasketaccessories_%s"]/div/div[@class="price text-center"]';

    public $similarProductTitle = '#similar_%s';

    public $similarProductPrice = '//form[@name="tobasketsimilar_%s"]/div/div[@class="price text-center"]';

    public $crossSellingProductTitle = '#cross_%s';

    public $crossSellingProductPrice = '//form[@name="tobasketcross_%s"]/div/div[@class="price text-center"]';

    public $disabledBasketButton = '//button[@id="toBasket" and @disabled="disabled"]';

    public $variantSelection = '/descendant::button[@class="btn btn-default btn-sm dropdown-toggle"][%s]';

    public $amountPriceQuantity = '//div[@class="modal-content"]/div[2]/dl/dt[%s]';

    public $amountPriceValue = '//div[@class="modal-content"]/div[2]/dl/dd[%s]';

    public $amountPriceCloseButton = '//div[@class="modal-content"]/div[3]/button';

    public $selectionList = '#productSelections button';

    public $attributeName = '#attrTitle_%s';

    public $attributeValue = '#attrValue_%s';

}
