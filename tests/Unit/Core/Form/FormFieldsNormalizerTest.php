<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core\Form;

use OxidEsales\TestingLibrary\UnitTestCase;

use OxidEsales\Eshop\Core\Form\FormFields;
use OxidEsales\Eshop\Core\Form\FormFieldsNormalizer;
use OxidEsales\Eshop\Core\Form\FormFieldsTrimmer;

/**
 * Class FormFieldsNormalizerTest
 *
 * @package OxidEsales\EshopCommunity\Tests\Unit\Core\Form
 */
class FormFieldsNormalizerTest extends UnitTestCase
{
    public function testFormFieldsTrimming()
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

        $trimmer    = oxNew(FormFieldsTrimmer::class);
        $normalizer = oxNew(FormFieldsNormalizer::class, $trimmer);

        $this->assertEquals(
            $trimmedFields,
            $normalizer->normalize($untrimmedFields)
        );
    }
}
