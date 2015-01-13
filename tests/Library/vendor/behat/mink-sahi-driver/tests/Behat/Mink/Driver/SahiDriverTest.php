<?php

namespace Tests\Behat\Mink\Driver;

use Behat\Mink\Mink,
    Behat\Mink\Session;

use Behat\Mink\Driver\SahiDriver;

/**
 * @group sahidriver
 */
class SahiDriverTest extends JavascriptDriverTest
{
    protected static function getDriver()
    {
        return new SahiDriver($_SERVER['WEB_FIXTURES_BROWSER']);
    }

    /**
     * @group issue131
     */
    public function testIssue131()
    {
        $this->getSession()->visit($this->pathTo('/issue131.php'));
        $page = $this->getSession()->getPage();

        $page->selectFieldOption('foobar', 'Gimme some accentués characters');
    }

    public function testPrepareXPath()
    {
        $driver = $this->getSession()->getDriver();

        // Make the method accessible for testing purposes
        $method = new \ReflectionMethod(
          'Behat\Mink\Driver\SahiDriver', 'prepareXPath'
        );
        $method->setAccessible(true);

        $this->assertEquals('No quotes', $method->invokeArgs($driver, array('No quotes')));
        $this->assertEquals("Single quote'", $method->invokeArgs($driver, array("Single quote'")));
        $this->assertEquals('Double quote\"', $method->invokeArgs($driver, array('Double quote"')));
    }

    // Sahi doesn't support iFrames switching
    public function testIFrame() {}

    // Sahi doesn't support window switching
    public function testWindow() {}
}
