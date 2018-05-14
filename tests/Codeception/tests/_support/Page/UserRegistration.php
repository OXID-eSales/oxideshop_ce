<?php
namespace Page;

use Page\Header\AccountMenu;

class UserRegistration extends Page
{
    use UserForm, AccountMenu;

    // include url of current page
    public static $URL = '/en/open-account';

    // include bread crumb of current page
    public static $breadCrumb = ['id' => 'breadcrumb'];

    //save form button
    public static $saveFormButton = '#accUserSaveTop';

    /**
     * Basic route example for your current URL
     * You can append any additional parameter to URL
     * and use it in tests like: Page\Edit::route('/123-post');
     */
    public static function route($param)
    {
        return static::$URL.$param;
    }

    /**
     * @return $this
     */
    public function registerUser()
    {
        $I = $this->user;
        $I->click(self::$saveFormButton);
        $I->waitForElement(self::$breadCrumb);
        return $this;
    }
}
