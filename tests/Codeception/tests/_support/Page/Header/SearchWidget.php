<?php
namespace Page\Header;

use Page\ProductSearchList;

trait SearchWidget
{
    public static $searchField = '#searchParam';

    public static $searchButton = '';

    public static $searchForm = '//form[name=search]';

    /**
     * @param string $value
     *
     * @return ProductSearchList
     */
    public function searchFor($value)
    {
        /** @var \AcceptanceTester $I */
        $I = $this->user;
        $I->fillField(self::$searchField, $value);
        $I->click('form[name=search] button[type=submit]');
        return new ProductSearchList($I);
    }
}
