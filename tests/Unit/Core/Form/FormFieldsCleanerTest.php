<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core\Form;

use OxidEsales\TestingLibrary\UnitTestCase;
use OxidEsales\Eshop\Core\Form\FormFields;
use OxidEsales\Eshop\Core\Form\FormFieldsCleaner;

class FormFieldsCleanerTest extends UnitTestCase
{
    public function testGetAllFieldsWhenNothingInWhiteList()
    {
        $fieldsToClean = ['oxid' => 'some value 1', 'user_name' => 'some value 2'];

        $emptyUpdatableFieldsList = oxNew(FormFields::class, []);

        $cleanedFieldsList = $this->getCleanList($emptyUpdatableFieldsList, $fieldsToClean);

        $this->assertSame($fieldsToClean, $cleanedFieldsList);
    }

    public function testGetEmptyArrayWhenNoneInWhiteList()
    {
        $fieldsToClean = ['none_existing_1' => 'some value 1', 'none_existing_2' => 'some value 2'];

        $updatableFieldsList = $this->getUpdatableFields();

        $cleanedFieldsList = $this->getCleanList($updatableFieldsList, $fieldsToClean);

        $this->assertSame([], $cleanedFieldsList);
    }

    public function providerGetSameCaseSensitiveDataAsRequested()
    {
        return [
            [['userName' => 'some value 1', 'none_existing_2' => 'some value 2'], ['userName' => 'some value 1']],
            [['username' => 'some value 1', 'none_existing_2' => 'some value 2'], ['username' => 'some value 1']],
            [['USERNAME' => 'some value 1', 'none_existing_2' => 'some value 2'], ['USERNAME' => 'some value 1']],
            [['oxuser__username' => 'user name', 'oxuser__notexisting' => 'some value'], ['oxuser__username' => 'user name']],
        ];
    }

    /**
     * @param array $fieldsToClean
     * @param array $expectedFields
     * @dataProvider providerGetSameCaseSensitiveDataAsRequested
     */
    public function testGetSameCaseSensitiveDataAsRequested($fieldsToClean, $expectedFields)
    {
        $updatableFieldsList = $this->getUpdatableFields();

        $cleanedFieldsList = $this->getCleanList($updatableFieldsList, $fieldsToClean);

        $this->assertSame($expectedFields, $cleanedFieldsList);
    }

    private function getUpdatableFields()
    {
        $userUpdatableFieldsList = ['oxuser__username', 'oxuser__userpassword', 'username', 'userpassword'];
        $userUpdatableFields = oxNew(FormFields::class, $userUpdatableFieldsList);

        return $userUpdatableFields;
    }

    /**
     * @param array $updatableFields
     * @param array $fields
     * @return array
     */
    private function getCleanList($updatableFields, $fields)
    {
        $cleaner = oxNew(FormFieldsCleaner::class, $updatableFields);
        $cleanedFieldsList = $cleaner->filterByUpdatableFields($fields);
        return $cleanedFieldsList;
    }
}
