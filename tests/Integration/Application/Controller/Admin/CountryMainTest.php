<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace Integration\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\CountryMain;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;

final class CountryMainTest extends IntegrationTestCase
{
    private bool $allowSharedEdit;

    public function setUp(): void
    {
        parent::setUp();

        $this->allowSharedEdit = (bool) Registry::getConfig()->getConfigParam('blAllowSharedEdit');
        Registry::getConfig()->setConfigParam('blAllowSharedEdit', true);
    }

    public function tearDown(): void
    {
        Registry::getConfig()->setConfigParam('blAllowSharedEdit', $this->allowSharedEdit);

        parent::tearDown();
    }

    public function testSaveWithEmptyVatPrefixAndVatStatusIsActivated(): void
    {
        $this->mockSubmitFormData([
            'oxcountry__oxvatstatus' => '1',
            'oxcountry__oxvatinprefix' => '',
        ]);

        $countryMain = new CountryMain();
        $countryMain->save();

        $this->assertStringContainsString(
            'ERROR_MESSAGE_INPUT_VAT_PREFIX_EMPTY',
            Registry::getSession()->getVariable('Errors')['default'][0]
        );
    }

    public function testSaveWithEmptyVatPrefixAndVatStatusIsDisabled(): void
    {
        $this->mockSubmitFormData([
            'oxcountry__oxvatstatus' => '0',
            'oxcountry__oxvatinprefix' => '',
        ]);

        $countryMain = new CountryMain();
        $countryMain->save();

        $this->assertEmpty(Registry::getSession()->getVariable('Errors'));
    }

    #[RunInSeparateProcess]
    public function testSaveWithNotAllowedFileTypeShowsTheMessage(): void
    {
        $this->mockSubmitFormData([
            'oxcountry__oxtitle' => 'Country with upload',
            'oxcountry__oxvatstatus' => '0',
        ]);
        $_FILES['myfile'] = [
            'name' => ['M1@oxcountry__oxpic' => 'script.php'],
            'tmp_name' => ['M1@oxcountry__oxpic' => '/tmp/script.php'],
            'error' => ['M1@oxcountry__oxpic' => UPLOAD_ERR_OK],
        ];

        (new CountryMain())->executeFunction('save');

        $this->assertSame(
            [Registry::getLang()->translateString('ERROR_MESSAGE_WRONG_IMAGE_FILE_TYPE')],
            array_map(
                static fn (string $error): string => unserialize($error)->getOxMessage(),
                Registry::getSession()->getVariable('Errors')['default']
            )
        );
    }

    private function mockSubmitFormData(array $data): void
    {
        $_POST['editval'] = $data;
    }
}
