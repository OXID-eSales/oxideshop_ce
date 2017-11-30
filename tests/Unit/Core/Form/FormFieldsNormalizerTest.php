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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
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
