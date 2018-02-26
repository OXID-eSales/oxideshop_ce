<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

/**
 * Class AccountReviewControllerTests
 *
 * Test the correct behavior of the recommendation management feature in the account controller
 *
 * @package OxidEsales\EshopCommunity\Tests\Unit\Application\Controller
 */
class AccountReviewControllerTest extends \OxidEsales\TestingLibrary\UnitTestCase
{

    /**
     * @covers \OxidEsales\EshopCommunity\Application\Controller\AccountReviewController::init
     */
    public function testInitDoesNotRedirectIfFeatureIsEnabled()
    {
        $isUserAllowedToManageHisProductReviews = true;

        $accountReviewControllerMock = $this->getMock(
            \OxidEsales\Eshop\Application\Controller\AccountReviewController::class,
            ['isUserAllowedToManageHisProductReviews', 'redirectToAccountDashboard']
        );
        $accountReviewControllerMock->expects($this->once())->method('isUserAllowedToManageHisProductReviews')->will($this->returnValue($isUserAllowedToManageHisProductReviews));
        $accountReviewControllerMock->expects($this->never())->method('redirectToAccountDashboard');

        $accountReviewControllerMock->init();
    }

    /**
     * @covers \OxidEsales\EshopCommunity\Application\Controller\AccountReviewController::init
     */
    public function testInitRedirectsIfFeatureIsDisabled()
    {
        $isUserAllowedToManageHisProductReviews = false;

        $accountReviewControllerMock = $this->getMock(
            \OxidEsales\Eshop\Application\Controller\AccountReviewController::class,
            ['isUserAllowedToManageHisProductReviews', 'redirectToAccountDashboard']
        );
        $accountReviewControllerMock->expects($this->once())->method('isUserAllowedToManageHisProductReviews')->will($this->returnValue($isUserAllowedToManageHisProductReviews));
        $accountReviewControllerMock->expects($this->once())->method('redirectToAccountDashboard');

        $accountReviewControllerMock->init();
    }


    /**
     * @covers \OxidEsales\EshopCommunity\Application\Controller\AccountReviewController::render
     */
    public function testRenderReturnsParentTemplateNameIfFeatureNotEnabled()
    {
        /**
         * method isUserAllowedToManageHisProductReviews() will return false
         */
        $isUserAllowedToManageHisProductReviews = false;

        $user = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        $user->oxuser__oxpassword = new \oxField(1);

        /**
         * Get the template name from the parent controller.
         * This mocking is necessary in order to authenticate the user and not to get the login template
         */
        $accountControllerMock = $this->getMock(
            \OxidEsales\Eshop\Application\Controller\AccountController::class,
            ["redirectAfterLogin", "getUser", "isEnabledPrivateSales"]
        );
        $accountControllerMock->expects($this->once())->method("redirectAfterLogin")->will($this->returnValue(1));
        $accountControllerMock->expects($this->once())->method("getUser")->will($this->returnValue($user));
        $accountControllerMock->expects($this->any())->method('isEnabledPrivateSales')->will($this->returnValue(false));

        $expectedTemplateName = $accountControllerMock->render();

        /**
         * Get the template name from the AccountReviewController
         * This mocking is necessary in order to authenticate the user and not to get the login template
         */
        $accountReviewControllerMock = $this->getMock(
            \OxidEsales\Eshop\Application\Controller\AccountReviewController::class,
            ['isUserAllowedToManageHisProductReviews', 'redirectAfterLogin', 'getUser', 'isEnabledPrivateSales']
        );
        $accountReviewControllerMock->expects($this->any())->method('isUserAllowedToManageHisProductReviews')->will($this->returnValue($isUserAllowedToManageHisProductReviews));
        $accountReviewControllerMock->expects($this->once())->method("redirectAfterLogin")->will($this->returnValue(1));
        $accountReviewControllerMock->expects($this->once())->method("getUser")->will($this->returnValue($user));
        $accountReviewControllerMock->expects($this->any())->method('isEnabledPrivateSales')->will($this->returnValue(false));

        $actualTemplateName = $accountReviewControllerMock->render();

        $this->assertSame($expectedTemplateName, $actualTemplateName);
    }

    /**
     * @covers \OxidEsales\EshopCommunity\Application\Controller\AccountReviewController::render
     */
    public function testRenderReturnsOwnTemplateNameIfFeatureIsEnabled()
    {
        /**
         * method isUserAllowedToManageHisProductReviews() will return false
         */
        $isUserAllowedToManageHisProductReviews = true;
        $expectedTemplateName = 'page/account/productreviews.tpl';

        $user = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        $user->oxuser__oxpassword = new \oxField(1);

        /**
         * Get the template name from the AccountReviewController
         * This mocking is necessary in order to authenticate the user and not to get the login template
         */
        $accountReviewControllerMock = $this->getMock(
            \OxidEsales\Eshop\Application\Controller\AccountReviewController::class,
            ['isUserAllowedToManageHisProductReviews', 'redirectAfterLogin', 'getUser', 'isEnabledPrivateSales']
        );
        $accountReviewControllerMock->expects($this->any())->method('isUserAllowedToManageHisProductReviews')->will($this->returnValue($isUserAllowedToManageHisProductReviews));
        $accountReviewControllerMock->expects($this->once())->method("getUser")->will($this->returnValue($user));
        $accountReviewControllerMock->expects($this->any())->method('isEnabledPrivateSales')->will($this->returnValue(false));

        $actualTemplateName = $accountReviewControllerMock->render();

        $this->assertSame($expectedTemplateName, $actualTemplateName);
    }

    /**
     * @covers \OxidEsales\EshopCommunity\Application\Controller\AccountReviewController::getBreadCrumb
     */
    public function testGetBreadCrumbReturnsOwnBreadcrumbIfFeatureIsEnabled()
    {
        $isUserAllowedToManageHisProductReviews = true;
        $languageId = \OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage();
        $expectedBreadCrumbTitle = \OxidEsales\Eshop\Core\Registry::getLang()->translateString('MY_PRODUCT_REVIEWS', $languageId, false);

        $accountReviewControllerMock = $this->getMock(
            \OxidEsales\Eshop\Application\Controller\AccountReviewController::class,
            ['isUserAllowedToManageHisProductReviews',]
        );
        $accountReviewControllerMock->expects($this->once())->method('isUserAllowedToManageHisProductReviews')->will($this->returnValue($isUserAllowedToManageHisProductReviews));

        $result = $accountReviewControllerMock->getBreadCrumb();

        $ownBreadCrumb = array_pop($result);

        $this->assertSame($expectedBreadCrumbTitle, $ownBreadCrumb['title']);
    }

    /**
     * @covers \OxidEsales\EshopCommunity\Application\Controller\AccountReviewController::getBreadCrumb
     */
    public function testGetBreadIfFeatureIsDisabled()
    {
        $isUserAllowedToManageHisProductReviews = false;
        $languageId = \OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage();
        $expectedBreadCrumbTitle = \OxidEsales\Eshop\Core\Registry::getLang()->translateString('MY_PRODUCT_REVIEWS', $languageId, false);

        $accountReviewControllerMock = $this->getMock(
            \OxidEsales\Eshop\Application\Controller\AccountReviewController::class,
            ['isUserAllowedToManageHisProductReviews',]
        );
        $accountReviewControllerMock->expects($this->once())->method('isUserAllowedToManageHisProductReviews')->will($this->returnValue($isUserAllowedToManageHisProductReviews));

        $result = $accountReviewControllerMock->getBreadCrumb();

        $ownBreadCrumb = array_pop($result);

        $this->assertNotEquals($expectedBreadCrumbTitle, $ownBreadCrumb['title']);
    }

    /**
     * @covers \OxidEsales\EshopCommunity\Application\Controller\AccountReviewController::getProductReviewList
     */
    public function testGetProductReviewListReturnsNullForNoUser()
    {
        $expectedProductReviewList = null;

        $accountReviewControllerMock = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountReviewController::class, ['getUser']);
        $accountReviewControllerMock->expects($this->any())->method('getUser')->will($this->returnValue(false));

        $actualProductReviewList = $accountReviewControllerMock->getProductReviewList();

        $this->assertSame( $expectedProductReviewList, $actualProductReviewList);
    }

    /**
     * @covers \OxidEsales\EshopCommunity\Application\Controller\AccountReviewController::getProductReviewList
     */
    public function testGetProductReviewListReturnsExpectedListForActiveUser()
    {
        $expectedProductReviewsList = new \OxidEsales\Eshop\Core\Model\ListModel();

        $userMock = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ['getId']);
        $userMock->expects($this->any())->method('getId')->will($this->returnValue("userId"));

        $accountReviewControllerMock = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountReviewController::class, ['getUser']);
        $accountReviewControllerMock->expects($this->any())->method('getUser')->will($this->returnValue($userMock));

        $reviewsMock = $this->getMock(\OxidEsales\Eshop\Application\Model\Review::class, ['getProductReviewsByUserId']);
        $reviewsMock->expects($this->any())->method('getProductReviewsByUserId')->will($this->returnValue($expectedProductReviewsList));
        \oxTestModules::addModuleObject(\OxidEsales\Eshop\Application\Model\Review::class, $reviewsMock);

        $actualProductReviewsList = $accountReviewControllerMock->getProductReviewList();

        $this->assertSame( $expectedProductReviewsList, $actualProductReviewsList);
    }

    /**
     * @covers \OxidEsales\EshopCommunity\Application\Controller\AccountReviewController::deleteProductReviewAndRating
     */
    public function testDeleteProductReviewReturnsFalseOnNoSessionChallenge()
    {
        $sessionMock = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('checkSessionChallenge'));
        $sessionMock->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(false));

        $accountReviewControllerMock = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountReviewController::class, ['getSession']);
        $accountReviewControllerMock->expects($this->any())->method('getSession')->will($this->returnValue($sessionMock));

        $result = $accountReviewControllerMock->deleteProductReviewAndRating();

        $this->assertFalse($result);
    }

    /**
     * @covers \OxidEsales\EshopCommunity\Application\Controller\AccountReviewController::deleteProductReviewAndRating
     */
    public function testDeleteProductReviewReturnsFalseOnNoUser()
    {
        $sessionMock = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('checkSessionChallenge'));
        $sessionMock->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));

        $accountReviewControllerMock = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountReviewController::class, ['getSession', 'getUser']);
        $accountReviewControllerMock->expects($this->any())->method('getSession')->will($this->returnValue($sessionMock));
        $accountReviewControllerMock->expects($this->any())->method('getUser')->will($this->returnValue(false));

        $result = $accountReviewControllerMock->deleteProductReviewAndRating();

        $this->assertFalse($result);
    }

    /**
     * The method must return NULL on success
     * In this test all preconditions for a successful deletion are met
     *
     * @dataProvider dataProviderTestDeleteProductReview
     * @covers \OxidEsales\EshopCommunity\Application\Controller\AccountReviewController::deleteProductReviewAndRating
     */
    public function testDeleteProductReview($checkSessionChallenge,
                                            $userId,
                                            $articleIdFromRequest,
                                            $productRatingDeleted,
                                            $reviewIdFromRequest,
                                            $productReviewDeleted,
                                            $expectedResult,
                                            $message)
    {
        /** CSFR protection: Session challenge check must pass */
        $sessionMock = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('checkSessionChallenge'));
        $sessionMock->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue($checkSessionChallenge));

        /** Is user logged in? User with userId must be present */
        $userMock = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ['getId']);
        $userMock->expects($this->any())->method('getId')->will($this->returnValue($userId));

        $accountReviewControllerMock = $this->getMock(
            \OxidEsales\Eshop\Application\Controller\AccountReviewController::class,
            ['getSession', 'getUser', 'getArticleIdFromRequest','deleteProductRating', 'getReviewIdFromRequest', 'deleteProductReview']
        );
        $accountReviewControllerMock->expects($this->any())->method('getSession')->will($this->returnValue($sessionMock));
        $accountReviewControllerMock->expects($this->any())->method('getUser')->will($this->returnValue($userMock));
        /** Article Id must be set in the HTTP REQUEST */
        $accountReviewControllerMock->expects($this->any())->method('getArticleIdFromRequest')->will($this->returnValue($articleIdFromRequest));
        /** Rating must be successfully deleted */
        $accountReviewControllerMock->expects($this->any())->method('deleteProductRating')->will($this->returnValue($productRatingDeleted));
        /** Review Id must be set in the HTTP REQUEST */
        $accountReviewControllerMock->expects($this->any())->method('getReviewIdFromRequest')->will($this->returnValue($reviewIdFromRequest));
        /** AND review must be successfully deleted */
        $accountReviewControllerMock->expects($this->any())->method('deleteProductReview')->will($this->returnValue($productReviewDeleted));

        $actualResult = $accountReviewControllerMock->deleteProductReviewAndRating();

        $this->assertSame($expectedResult, $actualResult, $message);
    }

    public function dataProviderTestDeleteProductReview()
    {
        return [
            'All conditions are met'                => [
                'checkSessionChallenge' => true,
                'userId'                => 'someUserId',
                'articleIdFromRequest'  => 'someArticleId',
                'productRatingDeleted'  => true,
                'reviewIdFromRequest'   => 'someReviewId',
                'productReviewDeleted'  => true,
                'expectedResult'        => null,
                'message'               => 'Returns null on success'
            ],
            'Session challenge check fails'         => [
                'checkSessionChallenge' => false,
                'userId'                => 'someUserId',
                'articleIdFromRequest'  => 'someArticleId',
                'productRatingDeleted'  => true,
                'reviewIdFromRequest'   => 'someReviewId',
                'productReviewDeleted'  => true,
                'expectedResult'        => false,
                'message'               => 'Returns false on failed session challenge check'
            ],
            'User id is not set'                    => [
                'checkSessionChallenge' => true,
                'userId'                => false,
                'articleIdFromRequest'  => 'someArticleId',
                'productRatingDeleted'  => true,
                'reviewIdFromRequest'   => 'someReviewId',
                'productReviewDeleted'  => true,
                'expectedResult'        => false,
                'message'               => 'Returns false on failed userid check'
            ],
            'No article ID is given in the request' => [
                'checkSessionChallenge' => true,
                'userId'                => 'someUserId',
                'articleIdFromRequest'  => false,
                'productRatingDeleted'  => true,
                'reviewIdFromRequest'   => 'someReviewId',
                'productReviewDeleted'  => true,
                'expectedResult'        => false,
                'message'               => 'Returns false, if parameter aid is not given in the HTTP REQUEST'
            ],
            'Product rating could not be deleted'   => [
                'checkSessionChallenge' => true,
                'userId'                => 'someUserId',
                'articleIdFromRequest'  => 'someArticleId',
                'productRatingDeleted'  => false,
                'reviewIdFromRequest'   => 'someReviewId',
                'productReviewDeleted'  => true,
                'expectedResult'        => false,
                'message'               => 'Returns false, if product rating could not be deleted'
            ],
            'No review ID is given in the request'  => [
                'checkSessionChallenge' => true,
                'userId'                => 'someUserId',
                'articleIdFromRequest'  => 'someArticleId',
                'productRatingDeleted'  => true,
                'reviewIdFromRequest'   => false,
                'productReviewDeleted'  => true,
                'expectedResult'        => false,
                'message'               => 'Returns false, if parameter reviewId is not given in the HTTP REQUEST'
            ],
            'Product review could not be deleted'   => [
                'checkSessionChallenge' => true,
                'userId'                => 'someUserId',
                'articleIdFromRequest'  => 'someArticleId',
                'productRatingDeleted'  => true,
                'reviewIdFromRequest'   => 'someReviewId',
                'productReviewDeleted'  => false,
                'expectedResult'        => false,
                'message'               => 'Returns false, if product rating could not be deleted'
            ],
        ];
    }

    /**
     * @covers \OxidEsales\EshopCommunity\Application\Controller\AccountReviewController::deleteProductReviewAndRating
     */
    public function testDeleteProductReviewReturnsFalseOnNoReview()
    {
        $userMock = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ['getId']);
        $userMock->expects($this->any())->method('getId')->will($this->returnValue("userId"));

        $accountReviewControllerMock = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountReviewController::class, ['getUser']);
        $accountReviewControllerMock->expects($this->any())->method('getUser')->will($this->returnValue($userMock));

        $result = $accountReviewControllerMock->deleteProductReviewAndRating();

        $this->assertFalse($result);
    }

    /**
     * @covers \OxidEsales\EshopCommunity\Application\Controller\AccountReviewController::deleteProductReviewAndRating
     */
    public function testDeleteProductReviewReturnsFalseOnWrongReviewType()
    {
        $userMock = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ['getId']);
        $userMock->expects($this->any())->method('getId')->will($this->returnValue("userId"));

        $accountReviewControllerMock = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountReviewController::class, ['getUser']);
        $accountReviewControllerMock->expects($this->any())->method('getUser')->will($this->returnValue($userMock));

        $reviewsMock = $this->getMock(\OxidEsales\Eshop\Application\Model\Review::class, ['getObjectType','load']);
        $reviewsMock->expects($this->any())->method('load')->will($this->returnValue(true));
        $reviewsMock->expects($this->any())->method('getObjectType')->will($this->returnValue('oxrecommlist'));
        \oxTestModules::addModuleObject(\OxidEsales\Eshop\Application\Model\Review::class, $reviewsMock);

        $result = $accountReviewControllerMock->deleteProductReviewAndRating();

        $this->assertFalse($result);
    }

    /**
     * @covers \OxidEsales\EshopCommunity\Application\Controller\AccountReviewController::deleteProductReviewAndRating
     */
    public function testDeleteProductReviewReturnsFalseOnWrongReviewUser()
    {
        $userMock = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ['getId']);
        $userMock->expects($this->any())->method('getId')->will($this->returnValue("someId"));

        $reviewUserMock = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ['getId']);
        $reviewUserMock->expects($this->any())->method('getId')->will($this->returnValue("otherId"));

        $accountReviewControllerMock = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountReviewController::class, ['getUser']);
        $accountReviewControllerMock->expects($this->any())->method('getUser')->will($this->returnValue($userMock));

        $reviewsMock = $this->getMock(\OxidEsales\Eshop\Application\Model\Review::class, ['getObjectType','load','getUser']);
        $reviewsMock->expects($this->any())->method('load')->will($this->returnValue(true));
        $reviewsMock->expects($this->any())->method('getObjectType')->will($this->returnValue('oxarticle'));
        $reviewsMock->expects($this->any())->method('getUser')->will($this->returnValue($reviewUserMock));
        \oxTestModules::addModuleObject(\OxidEsales\Eshop\Application\Model\Review::class, $reviewsMock);

        $result = $accountReviewControllerMock->deleteProductReviewAndRating();

        $this->assertFalse($result);
    }
}
