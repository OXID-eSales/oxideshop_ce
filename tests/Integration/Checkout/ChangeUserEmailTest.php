<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Checkout;

/**
 * Class ChangeUserEmailTest
 *
 * Test flow theme, user not wantign to create account during checkout should be able to
 * change his email address.
 *
 * @package OxidEsales\EshopCommunity\Tests\Integration\Checkout
 */
class ChangeUserEmailTest extends \OxidEsales\TestingLibrary\UnitTestCase
{
    const TEST_USER_ID = '_formtestuser';

    const TEST_ARTICLE_OXID = '_test_product';

    /**
     * Test set up.
     */
    public function setUp()
    {
        parent::setUp();

        \OxidEsales\Eshop\Core\Registry::getConfig()->setConfigParam('sTheme', 'flow');
        $this->replaceBlocks();

        $this->createTestProduct();
        $this->createBasket();
        \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Core\UtilsView::class)->getSmarty(true);
    }

    /**
     * Tear down.
     */
    public function tearDown()
    {
        $this->cleanUpTable('oxtplblocks', 'oxid');

        parent::tearDown();
    }

    /**
     * Test logged in user does not see email input form field when changing billing address in checkout.
     */
    public function testLoggedInUserDuringCheckout()
    {
        //Create user account, make it the session user
        $this->createTestUser();
        $content = $this->getTemplateOutput(\OxidEsales\Eshop\Application\Controller\UserController::class, 'form/user_checkout_change.tpl');

        $this->assertContains('name="invadr[oxuser__oxfname]"', $content);
        $this->assertNotContains('name="user_password"', $content);
        $this->assertNotContains('value="shopuser@oxid.de"', $content);
    }

    /**
     * Test logged in user does see email input form field when changing billing address in account.
     */
    public function testLoggedInUserAccount()
    {
        //Create user account, make it the session user
        $this->createTestUser();
        $content = $this->getTemplateOutput(\OxidEsales\Eshop\Application\Controller\AccountUserController::class, 'form/user.tpl');

        $this->assertContains('name="invadr[oxuser__oxfname]"', $content);
        $this->assertContains('name="user_password"', $content);
        $this->assertContains('value="shopuser@oxid.de"', $content);
    }

    /**
     * Test not logged in user sees email input form field.
     */
    public function testNoRegistrationUser()
    {
        //Create user account, make it the session user
        $this->createTestUser(false);
        $content = $this->getTemplateOutput(\OxidEsales\Eshop\Application\Controller\UserController::class, 'form/user_checkout_change.tpl');

        $this->assertContains('name="invadr[oxuser__oxfname]"', $content);
        $this->assertContains('name="invadr[oxuser__oxusername]"', $content);
        $this->assertNotContains('name="user_password"', $content);
        $this->assertContains('value="shopuser@oxid.de"', $content);
    }

    /**
     * Test helper to replace header and footer as they are not needed for our tests.
     */
    private function replaceBlocks()
    {
        $shopId = \OxidEsales\Eshop\Core\Registry::getConfig()->getShopId();
        $query = "INSERT INTO oxtplblocks (OXID, OXACTIVE, OXSHOPID, OXTEMPLATE, OXBLOCKNAME, OXPOS, OXFILE, OXMODULE) VALUES " .
                 "('_test_header', 1, '{$shopId}', 'layout/page.tpl', 'layout_header', 1, 'Tests/Integration/views/blocks/empty.tpl', 'oegdproptin'), " .
                 "('_test_footer', 1, '{$shopId}', 'layout/footer.tpl', 'footer_main', 1, 'Tests/Integration/views/blocks/empty.tpl', 'oegdproptin'), " .
                 "('_test_sidebar', 1, '{$shopId}', 'layout/sidebar.tpl', 'sidebar', 1, 'Tests/Integration/views/blocks/empty.tpl', 'oegdproptin'), " .
                 "('_test_sgvo_icons', 1, '{$shopId}', 'layout/base.tpl', 'theme_svg_icons', 1, 'Tests/Integration/views/blocks/empty.tpl', 'oegdproptin'), " .
                 "('_test_listitem', 1, '{$shopId}', 'page/review/review.tpl', 'widget_product_listitem_line_picturebox', 1, 'Tests/Integration/views/blocks/empty.tpl', 'oegdproptin')";

        \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute($query);
    }

    /**
     * Test helper to get template output.
     *
     * @param string $controllerName
     * @param string $template
     * @param null   $addViewData
     *
     * @return mixed
     */
    protected function getTemplateOutput($controllerName, $template, $addViewData = null)
    {
        $controller = oxNew($controllerName);
        $controller->init();

        return $this->doRender($controller, $template, $addViewData);
    }

    /**
     * Test helper to render output.
     *
     * @param object $controller
     * @param string $template
     *
     * @return string
     */
    protected function doRender($controller, $template)
    {
        //prepare output
        $output = oxNew(\OxidEsales\Eshop\Core\Output::class);

        $viewData = $output->processViewArray($controller->getViewData(), $controller->getClassName());
        $viewData['oxcmp_user'] = \OxidEsales\Eshop\Core\Registry::getSession()->getUser();
        $viewData['oxcmp_basket'] = \OxidEsales\Eshop\Core\Registry::getSession()->getBasket();
        $viewData['oConfig'] = \OxidEsales\Eshop\Core\Registry::getConfig();

        //trick: if Errors array is not empty, addess form will be visible.
        $this->addValidationError();

        $controller->setViewData($viewData);
        return \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Core\UtilsView::class)->getTemplateOutput($template, $controller);
    }

    /**
     * Test helper to create a user.
     * NOTE: the main difference between a noregistration (guest buy) user and a registered one is that
     * the latter has a password stored in database.
     *
     * @param bool $addPassword
     */
    private function createTestUser($addPassword = true)
    {
        $accountData = [
            'oxfname'     => 'Max',
            'oxlname'     => 'Mustermann',
            'oxusername'  => 'shopuser@oxid.de',
            'oxactive'    => 1,
            'oxshopid'    => 1,
            'oxcountryid' => 'a7c40f631fc920687.20179984',
            'oxboni'      => '600',
            'oxstreet'    => 'Teststreet',
            'oxstreetnr'  => '101',
            'oxcity'      => 'Freiburg',
            'oxzip'       => '79098'
        ];

        if ($addPassword) {
            $accountData['oxpassword'] = md5('shopuser');
        }

        $user = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        $user->setId(self::TEST_USER_ID);
        $user->assign($accountData);
        $user->save();

        //Ensure we have it in session and as active user
        $this->ensureActiveUser();
    }

    /**
     * Make sure we have the test user as active user.
     */
    private function ensureActiveUser()
    {
        $this->setSessionParam('usr', self::TEST_USER_ID);
        $this->setSessionParam('auth', self::TEST_USER_ID);

        $user = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        $user->load(self::TEST_USER_ID);
        \OxidEsales\Eshop\Core\Registry::getSession()->setUser($user);
        $user->setUser($user);
        $this->assertTrue($user->loadActiveUser());
    }

    /**
     * Create a test product.
     */
    private function createTestProduct()
    {
        $product = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        $product->setId(self::TEST_ARTICLE_OXID);
        $product->oxarticles__oxshopid = new \OxidEsales\Eshop\Core\Field(1);
        $product->oxarticles__oxtitle = new \OxidEsales\Eshop\Core\Field(self::TEST_ARTICLE_OXID);
        $product->oxarticles__oxprice = new \OxidEsales\Eshop\Core\Field(6.66);
        $product->save();

        $this->product = $product;
    }

    /**
     * Creates filled basket object and stores it in session.
     */
    private function createBasket()
    {
        \OxidEsales\Eshop\Core\Registry::getSession()->getBasket();
        $this->assertNull(\OxidEsales\Eshop\Core\Registry::getSession()->getVariable('_newitem'));

        $basketComponent = oxNew(\OxidEsales\Eshop\Application\Component\BasketComponent::class);
        $basketComponent->toBasket(self::TEST_ARTICLE_OXID, 1);
        $basket = $basketComponent->render();
        $this->assertEquals(1, $basket->getProductsCount());

        \OxidEsales\Eshop\Core\Registry::getSession()->setBasket($basket);
    }

    /**
     * Test helper to add some validation error to display.
     */
    private function addValidationError()
    {
        $inputValidator = $this->getMock(\OxidEsales\Eshop\Core\InputValidator::class, ['getFieldValidationErrors']);
        $inputValidator->expects($this->any())
            ->method('getFieldValidationErrors')
            ->will($this->returnValue(['some_error']));

        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\InputValidator::class, $inputValidator);
    }
}
