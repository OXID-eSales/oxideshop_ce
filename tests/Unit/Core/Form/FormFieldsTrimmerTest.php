<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core\Form;

use OxidEsales\TestingLibrary\UnitTestCase;

use OxidEsales\Eshop\Core\Form\FormFields;
use OxidEsales\Eshop\Core\Form\FormFieldsTrimmer;

/**
 * Class FormFieldsTrimmerTest
 *
 * @package OxidEsales\EshopCommunity\Tests\Unit\Core\Form
 */
class FormFieldsTrimmerTest extends UnitTestCase
{
    public function testTrimming()
    {
        $untrimmedFields = oxNew(FormFields::class, [
            'zip'   => '  79098 ',
            'city'  => 'Freiburg im Breisgau ',
            [
                'year'  => ' 1986',
                'month' => '04 ',
            ],
        ]);

        $trimmedFields = new \ArrayIterator([
            'zip'   => '79098',
            'city'  => 'Freiburg im Breisgau',
            [
                'year'  => '1986',
                'month' => '04',
            ],
        ]);

        $trimmer = oxNew(FormFieldsTrimmer::class);
        $fieldsAfterTrimming = $trimmer->trim($untrimmedFields);

        $this->assertEquals(
            $trimmedFields,
            $fieldsAfterTrimming
        );
    }

    public function testMustTrimStringFieldsOnly()
    {
        $untrimmedFields = oxNew(FormFields::class, [
            'string'    => ' to trim',
            'bool'      => true,
            'int'       => 5,
        ]);

        $trimmedFields = [
            'string'    => 'to trim',
            'bool'      => true,
            'int'       => 5,
        ];

        $trimmer = oxNew(FormFieldsTrimmer::class);
        $fieldsAfterTrimming = (array) $trimmer->trim($untrimmedFields);

        $this->assertSame(
            $trimmedFields,
            $fieldsAfterTrimming
        );
    }
}
