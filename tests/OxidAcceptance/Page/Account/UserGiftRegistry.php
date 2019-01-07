<?php
namespace OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Account;

use OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\GiftRegistry;
use OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Header\AccountMenu;
use OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Header\MiniBasket;
use OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Page;
use OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\ProductDetails;

class UserGiftRegistry extends Page
{
    use MiniBasket, AccountMenu;

    protected $webElementName = 'WebElement\UserGiftRegistry';

    /**
     * @param string $userName
     *
     * @return $this
     */
    public function searchForGiftRegistry($userName)
    {
        $I = $this->user;
        $I->fillField($this->webElement->giftRegistrySearch, $userName);
        $I->click($I->translate('SEARCH'));
        return $this;
    }

    /**
     * Opens gift-registry page
     *
     * @return GiftRegistry
     */
    public function openFoundGiftRegistryList()
    {
        $I = $this->user;
        $I->click($this->webElement->foundListLink);
        $breadCrumb = $I->translate('YOU_ARE_HERE').':'.$I->translate('PUBLIC_GIFT_REGISTRIES');
        $I->see($breadCrumb, GiftRegistry::$breadCrumb);
        return new GiftRegistry($I);
    }

    /**
     * @param string $email
     * @param string $recipient
     * @param string $message
     *
     * @return $this
     */
    public function sendGiftRegistryEmail($email, $recipient, $message)
    {
        $I = $this->user;
        $this->openGiftRegistryEmailForm();
        $I->fillField($this->webElement->recipientName, $recipient);
        $I->fillField($this->webElement->recipientEmail, $email);
        $I->fillField($this->webElement->emailMessage, $message);
        $I->click($I->translate('SUBMIT'));
        return $this;
    }

    /**
     * @return $this
     */
    public function openGiftRegistryEmailForm()
    {
        $I = $this->user;
        $I->click($I->translate('MESSAGE_SEND_GIFT_REGISTRY'));
        $I->waitForText($I->translate('SEND_GIFT_REGISTRY'));
        $breadCrumb = $I->translate('YOU_ARE_HERE').':'.$I->translate('MY_ACCOUNT').$I->translate('MY_GIFT_REGISTRY');
        $I->see($breadCrumb, $this->webElement->breadCrumb);
        return $this;
    }

    /**
     * @param int $itemPosition
     *
     * @return $this
     */
    public function removeFromGiftRegistry($itemPosition)
    {
        $I = $this->user;
        $I->click(sprintf($this->webElement->removeFromGitRegistry, $itemPosition));
        return $this;
    }

    /**
     * @return $this
     */
    public function makeListSearchable()
    {
        $I = $this->user;
        $I->selectOption($this->webElement->publicSelection, 1);
        $I->click($I->translate('SAVE'));
        return $this;
    }

    /**
     * @return $this
     */
    public function makeListNotSearchable()
    {
        $I = $this->user;
        $I->selectOption($this->webElement->publicSelection, 0);
        $I->click($I->translate('SAVE'));
        return $this;
    }

    /**
     * @param array $productData
     * @param int   $itemPosition
     *
     * @return $this
     */
    public function seeProductData($productData, $itemPosition = 1)
    {
        $I = $this->user;
        $I->see($productData['title'], sprintf($this->webElement->productTitle, $itemPosition));
        $I->see($productData['desc'], sprintf($this->webElement->productDescription, $itemPosition));
        $I->see($productData['price'], sprintf($this->webElement->productPrice, $itemPosition));
        return $this;
    }

    /**
     * @param integer $itemPosition
     *
     * @return ProductDetails
     */
    public function openProductDetailsPage($itemPosition)
    {
        $I = $this->user;
        $I->click(sprintf($this->webElement->productTitle, $itemPosition));
        return new ProductDetails($I);
    }

    /**
     * @param integer $itemPosition
     * @param integer $amount
     *
     * @return $this
     */
    public function addProductToBasket($itemPosition, $amount)
    {
        $I = $this->user;
        $I->fillField(sprintf($this->webElement->basketAmount, $itemPosition), $amount);
        $I->click(sprintf($this->webElement->toBasketButton, $itemPosition));
        return $this;
    }

}
