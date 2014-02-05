<?php

namespace Test\Behat\SahiClient\Accessor;

require_once __DIR__ . '/../AbstractConnectionTest.php';
use Test\Behat\SahiClient\AbstractConnectionTest;

use Behat\SahiClient\Accessor;

abstract class AbstractAccessorTest extends AbstractConnectionTest
{
    protected function assertRelations(Accessor\AbstractAccessor $accessor, $selectorStart)
    {
        $con = $accessor->getConnection();

        $accessor1 = new Accessor\ByClassNameAccessor('some_class1', 'p', array(), $con);
        $accessor2 = new Accessor\ByClassNameAccessor('some_class2', 'span', array(), $con);
        $accessor->near($accessor1)->under($accessor2);

        $this->assertEquals(
            $selectorStart .
                '_sahi._near(_sahi._byClassName("some_class1", "p")), ' .
                '_sahi._under(_sahi._byClassName("some_class2", "span")))',
            $accessor->getAccessor()
        );
    }
}
