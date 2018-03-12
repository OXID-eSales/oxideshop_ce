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
        $isUserAllowedToManageOwnReviews = true;

        $userMock = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ['getId']);
        $userMock->expects($this->any())->method('getId')->will($this->returnValue("userId"));

        $accountReviewControllerMock = $this->getMock(
            \OxidEsales\Eshop\Application\Controller\AccountReviewController::class,
            ['getUser', 'isUserAllowedToManageOwnReviews', 'redirectToAccountDashboard']
        );
        $accountReviewControllerMock->expects($this->once())->method('isUserAllowedToManageOwnReviews')->will($this->returnValue($isUserAllowedToManageOwnReviews));
        $accountReviewControllerMock->expects($this->never())->method('redirectToAccountDashboard');
        $accountReviewControllerMock->expects($this->any())->method('getUser')->will($this->returnValue($userMock));

        $accountReviewControllerMock->init();
    }

    /**
     * @covers \OxidEsales\EshopCommunity\Application\Controller\AccountReviewController::init
     */
    public function testInitRedirectsIfFeatureIsDisabled()
    {
        $isUserAllowedToManageOwnReviews = false;

        $userMock = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ['getId']);
        $userMock->expects($this->any())->method('getId')->will($this->returnValue("userId"));

        $accountReviewControllerMock = $this->getMock(
            \OxidEsales\Eshop\Application\Controller\AccountReviewController::class,
            ['getUser', 'isUserAllowedToManageOwnReviews', 'redirectToAccountDashboard']
        );
        $accountReviewControllerMock->expects($this->once())->method('isUserAllowedToManageOwnReviews')->will($this->returnValue($isUserAllowedToManageOwnReviews));
        $accountReviewControllerMock->expects($this->once())->method('redirectToAccountDashboard');
        $accountReviewControllerMock->expects($this->any())->method('getUser')->will($this->returnValue($userMock));

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
        $isUserAllowedToManageOwnReviews = false;

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
            ['isUserAllowedToManageOwnReviews', 'redirectAfterLogin', 'getUser', 'isEnabledPrivateSales']
        );
        $accountReviewControllerMock->expects($this->any())->method('isUserAllowedToManageOwnReviews')->will($this->returnValue($isUserAllowedToManageOwnReviews));
        $accountReviewControllerMock->expects($this->once())->method("redirectAfterLogin")->will($this->returnValue(1));
        $accountReviewControllerMock->expects($this->any())->method("getUser")->will($this->returnValue($user));
        $accountReviewControllerMock->expects($this->any())->method('isEnabledPrivateSales')->will($this->returnValue(false));

        $accountReviewControllerMock->init();
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
        $expectedTemplateName = 'page/account/reviews.tpl';

        $user = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        $user->oxuser__oxpassword = new \oxField(1);

        /**
         * Get the template name from the AccountReviewController
         * This mocking is necessary in order to authenticate the user and not to get the login template
         */
        $accountReviewControllerMock = $this->getMock(
            \OxidEsales\Eshop\Application\Controller\AccountReviewController::class,
            ['isUserAllowedToManageOwnReviews', 'redirectAfterLogin', 'getUser', 'isEnabledPrivateSales']
        );
        $accountReviewControllerMock->expects($this->any())->method('isUserAllowedToManageOwnReviews')->will($this->returnValue($isUserAllowedToManageHisProductReviews));
        $accountReviewControllerMock->expects($this->any())->method("getUser")->will($this->returnValue($user));
        $accountReviewControllerMock->expects($this->any())->method('isEnabledPrivateSales')->will($this->returnValue(false));

        $actualTemplateName = $accountReviewControllerMock->render();

        $this->assertSame($expectedTemplateName, $actualTemplateName);
    }

    /**
     * @covers \OxidEsales\EshopCommunity\Application\Controller\AccountReviewController::getBreadCrumb
     */
    public function testGetBreadCrumbReturnsOwnBreadcrumbIfFeatureIsEnabled()
    {
        $user = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        $user->oxuser__oxpassword = new \oxField(1);

        $isUserAllowedToManageHisProductReviews = true;
        $languageId = \OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage();
        $expectedBreadCrumbTitle = \OxidEsales\Eshop\Core\Registry::getLang()->translateString('MY_REVIEWS', $languageId, false);

        $accountReviewControllerMock = $this->getMock(
            \OxidEsales\Eshop\Application\Controller\AccountReviewController::class,
            ['isUserAllowedToManageOwnReviews', 'getUser']
        );
        $accountReviewControllerMock->expects($this->once())->method('isUserAllowedToManageOwnReviews')->will($this->returnValue($isUserAllowedToManageHisProductReviews));
        $accountReviewControllerMock->expects($this->any())->method("getUser")->will($this->returnValue($user));

        $accountReviewControllerMock->init();

        $result = $accountReviewControllerMock->getBreadCrumb();

        $ownBreadCrumb = array_pop($result);

        $this->assertSame($expectedBreadCrumbTitle, $ownBreadCrumb['title']);
    }

    /**
     * @covers \OxidEsales\EshopCommunity\Application\Controller\AccountReviewController::getBreadCrumb
     */
    public function testGetBreadCrumbIfFeatureIsDisabled()
    {
        $user = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        $user->oxuser__oxpassword = new \oxField(1);

        $isUserAllowedToManageHisProductReviews = false;
        $languageId = \OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage();
        $expectedBreadCrumbTitle = \OxidEsales\Eshop\Core\Registry::getLang()->translateString('MY_REVIEWS', $languageId, false);

        $accountReviewControllerMock = $this->getMock(
            \OxidEsales\Eshop\Application\Controller\AccountReviewController::class,
            ['isUserAllowedToManageOwnReviews', 'getUser']
        );
        $accountReviewControllerMock->expects($this->once())->method('isUserAllowedToManageOwnReviews')->will($this->returnValue($isUserAllowedToManageHisProductReviews));
        $accountReviewControllerMock->expects($this->any())->method("getUser")->will($this->returnValue($user));

        $accountReviewControllerMock->init();

        $result = $accountReviewControllerMock->getBreadCrumb();

        $ownBreadCrumb = array_pop($result);

        $this->assertNotEquals($expectedBreadCrumbTitle, $ownBreadCrumb['title']);
    }

    /**
     * @covers \OxidEsales\EshopCommunity\Application\Controller\AccountReviewController::getReviewList
     */
    public function testGetReviewListReturnsNullForNoUser()
    {
        $expectedProductReviewList = null;

        $accountReviewControllerMock = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountReviewController::class, ['getUser']);
        $accountReviewControllerMock->expects($this->any())->method('getUser')->will($this->returnValue(false));

        $accountReviewControllerMock->init();

        $actualProductReviewList = $accountReviewControllerMock->getReviewList();

        $this->assertSame( $expectedProductReviewList, $actualProductReviewList);
    }

    /**
     * @covers \OxidEsales\EshopCommunity\Application\Controller\AccountReviewController::getReviewList
     */
    public function SKIP__testGetReviewListReturnsExpectedListForActiveUser()
    {
        $expectedReviewsList = new \Doctrine\Common\Collections\ArrayCollection();

        $userMock = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ['getId']);
        $userMock->expects($this->any())->method('getId')->will($this->returnValue("userId"));

        $accountReviewControllerMock = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountReviewController::class, ['getUser']);
        $accountReviewControllerMock->expects($this->any())->method('getUser')->will($this->returnValue($userMock));

        $userReviewAndRatingServiceMock = $this->getMockBuilder(\OxidEsales\EshopCommunity\Internal\Service\UserReviewAndRatingService::class)
            ->disableOriginalConstructor()
            ->setMethods(['getReviewAndRatingList'])
            ->getMock();
        $userReviewAndRatingServiceMock->expects($this->once())->method('getReviewAndRatingList')->will($this->returnValue($expectedReviewsList));
        \oxTestModules::addModuleObject(\OxidEsales\EshopCommunity\Internal\Service\UserReviewAndRatingService::class, $userReviewAndRatingServiceMock);

        $actualReviewsList = $accountReviewControllerMock->getReviewList();

        $this->assertSame( $expectedReviewsList, $actualReviewsList);
    }

    /**
     * @covers \OxidEsales\EshopCommunity\Application\Controller\AccountReviewController::deleteReviewAndRating
     */
    public function testDeleteReviewReturnsFalseOnNoSessionChallenge()
    {
        $userMock = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ['getId']);
        $userMock->expects($this->any())->method('getId')->will($this->returnValue("userId"));

        $sessionMock = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('checkSessionChallenge'));
        $sessionMock->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(false));

        $accountReviewControllerMock = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountReviewController::class, ['getSession', 'getUser']);
        $accountReviewControllerMock->expects($this->any())->method('getSession')->will($this->returnValue($sessionMock));
        $accountReviewControllerMock->expects($this->any())->method('getUser')->will($this->returnValue($userMock));

        $result = $accountReviewControllerMock->deleteReviewAndRating();

        $this->assertFalse($result);
    }

    /**
     * @covers \OxidEsales\EshopCommunity\Application\Controller\AccountReviewController::deleteReviewAndRating
     */
    public function testDeleteReviewReturnsFalseOnNoUser()
    {
        $sessionMock = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('checkSessionChallenge'));
        $sessionMock->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));

        $accountReviewControllerMock = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountReviewController::class, ['getSession', 'getUser']);
        $accountReviewControllerMock->expects($this->any())->method('getSession')->will($this->returnValue($sessionMock));
        $accountReviewControllerMock->expects($this->any())->method('getUser')->will($this->returnValue(false));

        $result = $accountReviewControllerMock->deleteReviewAndRating();

        $this->assertFalse($result);
    }

    /**
     * The method must return NULL on success
     * In this test all preconditions for a successful deletion are met
     *
     * @dataProvider dataProviderTestDeleteReview
     * @covers \OxidEsales\EshopCommunity\Application\Controller\AccountReviewController::deleteReviewAndRating
     */
    public function testDeleteReview($checkSessionChallenge,
                                     $userId,
                                     $ratingIdFromRequest,
                                     $ratingDeleted,
                                     $reviewIdFromRequest,
                                     $reviewDeleted,
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
            ['getSession', 'getUser', 'getRatingIdFromRequest', 'getReviewIdFromRequest']
        );
        $accountReviewControllerMock->expects($this->any())->method('getSession')->will($this->returnValue($sessionMock));
        $accountReviewControllerMock->expects($this->any())->method('getUser')->will($this->returnValue($userMock));

        $accountReviewControllerMock->expects($this->any())->method('getRatingIdFromRequest')->will($this->returnValue($ratingIdFromRequest));
        $accountReviewControllerMock->expects($this->any())->method('getReviewIdFromRequest')->will($this->returnValue($reviewIdFromRequest));

        $actualResult = $accountReviewControllerMock->deleteReviewAndRating();

        $this->assertSame($expectedResult, $actualResult, $message);
    }

    public function dataProviderTestDeleteReview()
    {
        return [
            'All conditions are met'                => [
                'checkSessionChallenge' => true,
                'userId'                => 'someUserId',
                'ratingIdFromRequest'   => 'someRatingId',
                'ratingDeleted'         => true,
                'reviewIdFromRequest'   => 'someReviewId',
                'reviewDeleted'         => true,
                'expectedResult'        => 'account_reviewlist',
                'message'               => 'Returns "account_reviewlist" on success'
            ],
            'Session challenge check fails'         => [
                'checkSessionChallenge' => false,
                'userId'                => 'someUserId',
                'ratingIdFromRequest'   => 'someRatingId',
                'ratingDeleted'         => true,
                'reviewIdFromRequest'   => 'someReviewId',
                'reviewDeleted'         => true,
                'expectedResult'        => false,
                'message'               => 'Returns false on failed session challenge check'
            ],
            'User id is not set'                    => [
                'checkSessionChallenge' => true,
                'userId'                => false,
                'ratingIdFromRequest'   => 'someRatingId',
                'ratingDeleted'         => true,
                'reviewIdFromRequest'   => 'someReviewId',
                'reviewDeleted'         => true,
                'expectedResult'        => false,
                'message'               => 'Returns false on failed userid check'
            ]
        ];
    }

    /**
     * @covers \OxidEsales\EshopCommunity\Application\Controller\AccountReviewController::deleteReviewAndRating
     */
    public function testDeleteReviewReturnsFalseOnNoReview()
    {
        $userMock = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ['getId']);
        $userMock->expects($this->any())->method('getId')->will($this->returnValue("userId"));

        $accountReviewControllerMock = $this->getMock(
            \OxidEsales\Eshop\Application\Controller\AccountReviewController::class,
            ['getUser']
        );
        $accountReviewControllerMock->expects($this->any())->method('getUser')->will($this->returnValue($userMock));

        $result = $accountReviewControllerMock->deleteReviewAndRating();

        $this->assertFalse($result);
    }

    /**
     * @covers \OxidEsales\EshopCommunity\Application\Controller\AccountReviewController::deleteReviewAndRating
     */
    public function testDeleteReviewReturnsFalseOnWrongReviewType()
    {
        $userMock = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ['getId']);
        $userMock->expects($this->any())->method('getId')->will($this->returnValue("userId"));

        $accountReviewControllerMock = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountReviewController::class, ['getUser']);
        $accountReviewControllerMock->expects($this->any())->method('getUser')->will($this->returnValue($userMock));

        $reviewsMock = $this->getMock(\OxidEsales\Eshop\Application\Model\Review::class, ['getObjectType','load']);
        $reviewsMock->expects($this->any())->method('load')->will($this->returnValue(true));
        $reviewsMock->expects($this->any())->method('getObjectType')->will($this->returnValue('oxrecommlist'));
        \oxTestModules::addModuleObject(\OxidEsales\Eshop\Application\Model\Review::class, $reviewsMock);

        $result = $accountReviewControllerMock->deleteReviewAndRating();

        $this->assertFalse($result);
    }

    /**
     * @covers \OxidEsales\EshopCommunity\Application\Controller\AccountReviewController::deleteReviewAndRating
     */
    public function testDeleteReviewReturnsFalseOnWrongReviewUser()
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

        $result = $accountReviewControllerMock->deleteReviewAndRating();

        $this->assertFalse($result);
    }
}
