<?php
namespace OxidEsales\EshopCommunity\Tests\OxidAcceptance\WebElement\Account;

class UserChangePassword
{
    // include url of current page
    public $URL = '/en/my-password/';

    // include bread crumb of current page
    public $breadCrumb = '#breadcrumb';

    public $userOldPassword = '#passwordOld';

    public $userNewPassword = '#passwordNew';

    public $userConfirmNewPassword = '#passwordNewConfirm';

    public $userChangePasswordButton = '#savePass';

    public $errorMessage = '//div[@class="alert alert-danger"]';

}
