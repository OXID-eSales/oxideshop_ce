<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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
