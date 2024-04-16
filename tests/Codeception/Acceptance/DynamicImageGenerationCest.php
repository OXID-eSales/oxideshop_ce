<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace Acceptance;

use Codeception\Attribute\Group;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

#[Group('images')]
final class DynamicImageGenerationCest
{
    private string $pathToGeneratedImages = '/out/pictures/generated/product/1/500_500_75';

    public function fetchGeneratedImages(AcceptanceTester $I): void
    {
        $I->wantToTest('availability of dynamically generated images');

        $I->amGoingTo('fetch a generated image for an existing product picture');
        $existingImageFixture = 'test.png';
        $I->amOnPage("$this->pathToGeneratedImages/$existingImageFixture");
        $this->dontSeeAnyErrorsOnPage($I);

        $I->amGoingTo('check that a missing product picture will be replaced with a placeholder image');
        $someMissingImage = 'some-missing-image.png';
        $I->amOnPage("$this->pathToGeneratedImages/$someMissingImage");
        $this->dontSeeAnyErrorsOnPage($I);
    }

    private function dontSeeAnyErrorsOnPage(AcceptanceTester $I): void
    {
        $I->dontSee('Error');
        $I->dontSee('Not found');
        $I->dontSee('404');
    }
}
