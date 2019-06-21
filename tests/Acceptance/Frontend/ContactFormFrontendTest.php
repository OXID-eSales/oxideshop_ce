<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Acceptance\Frontend;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\ConfigFile;
use OxidEsales\Facts\Facts;

class ContactFormFrontendTest extends \OxidEsales\EshopCommunity\Tests\Acceptance\FlowThemeTestCase
{
    private $requiredClass = 'req';

    private $contactUrl = 'index.php?cl=contact';

    private $emailInputFieldXpathLocator = '//*[@id="contactEmail"]';

    private $emailLabelXpathLocator = '//label[@for="contactEmail"]';

    private $configuredRequiredInputFieldXpathLocator = '//*[@id="editval[oxuser__oxfname]"]';

    private $configuredRequiredFieldLabelXpathLocator = '//label[@for="editval[oxuser__oxfname]"]';

    /**
     * @group flow-theme
     */
    public function testContactFormRequiresEmailFieldToBeFilledWithoutConfiguration()
    {
        $this->openContactForm();

        $this->assertFieldIsRequired(
            $this->emailInputFieldXpathLocator,
            $this->emailLabelXpathLocator
        );

        $this->assertFieldIsNotRequired(
            $this->configuredRequiredInputFieldXpathLocator,
            $this->configuredRequiredFieldLabelXpathLocator
        );
    }

    /**
     * @group flow-theme
     */
    public function testContactFormRequiresConfiguredFieldToBeFilled()
    {
        $this->insertRequiredFields(['firstName']);
        $this->openContactForm();

        $this->assertFieldIsRequired(
            $this->configuredRequiredInputFieldXpathLocator,
            $this->configuredRequiredFieldLabelXpathLocator
        );

        $this->assertFieldIsNotRequired(
            $this->emailInputFieldXpathLocator,
            $this->emailLabelXpathLocator
        );
    }

    private function openContactForm()
    {
        $this->openNewWindow($this->contactUrl);
    }

    private function insertRequiredFields(array $requiredFields)
    {
        $facts = new Facts();
        $configFile = new ConfigFile($facts->getSourcePath() . '/config.inc.php');
        $configKey = is_null($configFile->getVar('sConfigKey')) ? Config::DEFAULT_CONFIG_KEY : $configFile->getVar('sConfigKey');
        $rawValue = serialize($requiredFields);

        $query = "
        UPDATE `oxconfig`
        SET
          `OXVARVALUE` = ENCODE(?,?)
        WHERE `OXSHOPID`= 1
        AND   `OXVARNAME` = 'contactFormRequiredFields'
        ";

        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $database->execute($query, [$rawValue, $configKey]);
    }

    private function assertFieldIsNotRequired(string $notRequiredInputFieldLocator, string $notRequiredFieldLabelLocator)
    {
        $configuredInputField = $this->getElement($notRequiredInputFieldLocator);
        $this->assertNull(
            $configuredInputField->getAttribute('required'),
            'The input field ' . $notRequiredInputFieldLocator . ' does not have the attribute "required"'
        );

        $configuredLabel = $this->getElement($notRequiredFieldLabelLocator);
        $this->assertFalse(
            in_array($this->requiredClass, explode(' ', $configuredLabel->getAttribute('class'))),
            'The field label ' . $notRequiredFieldLabelLocator . ' is not marked as "required"'
        );
    }

    private function assertFieldIsRequired(string $requiredInputFieldLocator, string $requiredFieldLabelLocator)
    {
        $requiredInputField = $this->getElement($requiredInputFieldLocator);
        $this->assertTrue(
            $requiredInputField->hasAttribute('required'),
            'The input field ' . $requiredInputFieldLocator . ' has the attribute "required"'
        );

        $requiredLabel = $this->getElement($requiredFieldLabelLocator);
        $this->assertTrue(
            $requiredLabel->hasClass($this->requiredClass),
            'The field label ' . $requiredFieldLabelLocator . ' is marked as "required"'
        );
    }
}
