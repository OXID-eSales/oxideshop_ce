<?php

namespace Test\Behat\SahiClient\Accessor;

require_once 'AbstractAccessorTest.php';

use Behat\SahiClient\Accessor;

class FormAccessorTest extends AbstractAccessorTest
{
    private $con;

    public function setUp()
    {
        $this->con = $this->getConnectionMock();
    }

    public function testButton()
    {
        $accessor = new Accessor\Form\ButtonAccessor("Cancel", array(), $this->con);

        $this->assertEquals('_sahi._button("Cancel")', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
        $this->assertRelations($accessor, '_sahi._button("Cancel", ');
    }

    public function testOption()
    {
        $accessor = new Accessor\Form\OptionAccessor("Man", array(), $this->con);

        $this->assertEquals('_sahi._option("Man")', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
        $this->assertRelations($accessor, '_sahi._option("Man", ');
    }

    public function testRadio()
    {
        $accessor = new Accessor\Form\RadioAccessor("id", array(), $this->con);

        $this->assertEquals('_sahi._radio("id")', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
        $this->assertActionStep('_sahi._check(_sahi._radio("id"))', array($accessor, 'check'));
        $this->assertActionJavascript(
            '_sahi._radio("id").checked', 'true',
            array($accessor, 'isChecked'),
            array(), true
        );
        $this->assertRelations($accessor, '_sahi._radio("id", ');
    }

    public function testCheckbox()
    {
        $accessor = new Accessor\Form\CheckboxAccessor("id", array(), $this->con);

        $this->assertEquals('_sahi._checkbox("id")', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
        $this->assertActionStep('_sahi._check(_sahi._checkbox("id"))', array($accessor, 'check'));
        $this->assertActionStep('_sahi._uncheck(_sahi._checkbox("id"))', array($accessor, 'uncheck'));
        $this->assertActionJavascript(
            '_sahi._checkbox("id").checked', 'true',
            array($accessor, 'isChecked'),
            array(), true
        );
        $this->assertRelations($accessor, '_sahi._checkbox("id", ');
    }

    public function testFile()
    {
        $accessor = new Accessor\Form\FileAccessor("id", array(), $this->con);

        $this->assertEquals('_sahi._file("id")', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
        $this->assertActionStep(
            '_sahi._setFile(_sahi._file("id"), "/tmp/simple.gif")',
            array($accessor, 'setFile'),
            array('/tmp/simple.gif')
        );
        $this->assertRelations($accessor, '_sahi._file("id", ');
    }

    public function testHidden()
    {
        $accessor = new Accessor\Form\HiddenAccessor("_csrf_token", array(), $this->con);

        $this->assertEquals('_sahi._hidden("_csrf_token")', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
        $this->assertRelations($accessor, '_sahi._hidden("_csrf_token", ');
    }

    public function testImageSubmitButton()
    {
        $accessor = new Accessor\Form\ImageSubmitButtonAccessor("Cancel", array(), $this->con);

        $this->assertEquals('_sahi._imageSubmitButton("Cancel")', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
        $this->assertRelations($accessor, '_sahi._imageSubmitButton("Cancel", ');
    }

    public function testPassword()
    {
        $accessor = new Accessor\Form\PasswordAccessor("New pass", array(), $this->con);

        $this->assertEquals('_sahi._password("New pass")', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
        $this->assertRelations($accessor, '_sahi._password("New pass", ');
    }

    public function testReset()
    {
        $accessor = new Accessor\Form\ResetAccessor("New pass", array(), $this->con);

        $this->assertEquals('_sahi._reset("New pass")', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
        $this->assertRelations($accessor, '_sahi._reset("New pass", ');
    }

    public function testSelect()
    {
        $accessor = new Accessor\Form\SelectAccessor("city", array(), $this->con);

        $this->assertEquals('_sahi._select("city")', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
        $this->assertActionJavascript(
            '_sahi._getSelectedText(_sahi._select("city"))', 'New York',
            array($accessor, 'getSelectedText')
        );

        $this->assertActionStep(
            '_sahi._setSelected(_sahi._select("city"), "Moscow")',
            array($accessor, 'choose'),
            array('Moscow')
        );

        $this->assertActionStep(
            '_sahi._setSelected(_sahi._select("city"), "Minsk", true)',
            array($accessor, 'choose'),
            array('Minsk', true)
        );

        $this->assertActionStep(
            '_sahi._setSelected(_sahi._select("city"), "New York", false)',
            array($accessor, 'choose'),
            array('New York', false)
        );

        $this->assertRelations($accessor, '_sahi._select("city", ');
    }

    public function testSubmit()
    {
        $accessor = new Accessor\Form\SubmitAccessor("Save", array(), $this->con);

        $this->assertEquals('_sahi._submit("Save")', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
        $this->assertRelations($accessor, '_sahi._submit("Save", ');
    }

    public function testTextarea()
    {
        $accessor = new Accessor\Form\TextareaAccessor("about me", array(), $this->con);

        $this->assertEquals('_sahi._textarea("about me")', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
        $this->assertRelations($accessor, '_sahi._textarea("about me", ');
    }

    public function testTextbox()
    {
        $accessor = new Accessor\Form\TextboxAccessor("q", array(), $this->con);

        $this->assertEquals('_sahi._textbox("q")', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
        $this->assertRelations($accessor, '_sahi._textbox("q", ');
    }
}
