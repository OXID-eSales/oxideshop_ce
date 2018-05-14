<?php
namespace Page\Header;

trait LanguageMenu
{
    public static $languageMenuButton = "//div[@class='btn-group languages-menu']/button";

    public static $openLanguageMenu = "//div[@class='btn-group languages-menu open']";

    /**
     * @param string $language
     *
     * @return $this
     */
    public function switchLanguage($language)
    {
        /** @var \AcceptanceTester $I */
        $I = $this->user;
        $I->click(self::$languageMenuButton);
        $I->waitForElement(self::$openLanguageMenu);
        $I->click($language);
        $I->waitForElement(self::$languageMenuButton);
        return $this;
    }
}
