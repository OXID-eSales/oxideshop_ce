<?php

namespace Test\Behat\SahiClient\Accessor;

require_once 'AbstractAccessorTest.php';

use Behat\SahiClient\Accessor;

class AccessorTest extends AbstractAccessorTest
{
    private $con;

    public function setUp()
    {
        $this->con = $this->getConnectionMock();
    }

    public function testAccessor()
    {
        $accessor = new Accessor\DomAccessor('document.formName.elementName', $this->con);

        $this->assertEquals('_sahi._accessor("document.formName.elementName")', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
    }

    public function testByClassName()
    {
        $accessor = new Accessor\ByClassNameAccessor('some_class', 'div', array(), $this->con);

        $this->assertEquals('_sahi._byClassName("some_class", "div")', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
        $this->assertRelations($accessor, '_sahi._byClassName("some_class", "div", ');
    }

    public function testById()
    {
        $accessor = new Accessor\ByIdAccessor('some_id', $this->con);

        $this->assertEquals('_sahi._byId("some_id")', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
    }

    public function testByText()
    {
        $accessor = new Accessor\ByTextAccessor('span text', 'span', $this->con);

        $this->assertEquals('_sahi._byText("span text", "span")', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
    }

    public function testByXPath()
    {
        $accessor = new Accessor\ByXPathAccessor('//tr[1]/td[2]', array(), $this->con);

        $this->assertEquals('_sahi._byXPath("//tr[1]/td[2]")', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
        $this->assertRelations($accessor, '_sahi._byXPath("//tr[1]/td[2]", ');
    }

    public function testDiv()
    {
        $accessor = new Accessor\DivAccessor(1, array(), $this->con);

        $this->assertEquals('_sahi._div(1)', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
        $this->assertRelations($accessor, '_sahi._div(1, ');
    }

    public function testHeading()
    {
        $accessor = new Accessor\HeadingAccessor(2, 3, array(), $this->con);
        $this->assertEquals('_sahi._heading2(3)', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
        $this->assertRelations($accessor, '_sahi._heading2(3, ');

        $accessor = new Accessor\HeadingAccessor(null, null, array(), $this->con);
        $this->assertEquals('_sahi._heading1(0)', $accessor->getAccessor());
        $this->assertRelations($accessor, '_sahi._heading1(0, ');
    }

    public function testImage()
    {
        $accessor = new Accessor\ImageAccessor('add.gif', array(), $this->con);
        $this->assertEquals('_sahi._image("add.gif")', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
        $this->assertRelations($accessor, '_sahi._image("add.gif", ');
    }

    public function testLabel()
    {
        $accessor = new Accessor\LabelAccessor('Checkbox:', array(), $this->con);
        $this->assertEquals('_sahi._label("Checkbox:")', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
        $this->assertRelations($accessor, '_sahi._label("Checkbox:", ');
    }

    public function testLink()
    {
        $accessor = new Accessor\LinkAccessor('visible text', array(), $this->con);
        $this->assertEquals('_sahi._link("visible text")', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
        $this->assertRelations($accessor, '_sahi._link("visible text", ');
    }

    public function testListItem()
    {
        $accessor = new Accessor\ListItemAccessor('image', array(), $this->con);
        $this->assertEquals('_sahi._listItem("image")', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
        $this->assertRelations($accessor, '_sahi._listItem("image", ');
    }

    public function testSpan()
    {
        $accessor = new Accessor\SpanAccessor(1, array(), $this->con);

        $this->assertEquals('_sahi._span(1)', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
        $this->assertRelations($accessor, '_sahi._span(1, ');
    }
}
