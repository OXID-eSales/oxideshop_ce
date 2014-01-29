<?php

namespace Test\Behat\SahiClient\Accessor;

require_once 'AbstractAccessorTest.php';

use Behat\SahiClient\Accessor;

class TableAccessorTest extends AbstractAccessorTest
{
    private $con;

    public function setUp()
    {
        $this->con = $this->getConnectionMock();
    }

    public function testCell()
    {
        $accessor = new Accessor\Table\CellAccessor(2, array(), $this->con);

        $this->assertEquals('_sahi._cell(2)', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
        $this->assertRelations($accessor, '_sahi._cell(2, ');

        $table = new Accessor\Table\TableAccessor('tableId', array(), $this->con);
        $accessor = new Accessor\Table\CellAccessor(
            array($table, 'header2', 'value11'), array(), $this->con
        );

        $this->assertEquals(
            '_sahi._cell(_sahi._table("tableId"), "header2", "value11")', $accessor->getAccessor()
        );
        $this->assertSame($this->con, $accessor->getConnection());
        $this->assertRelations($accessor, '_sahi._cell(_sahi._table("tableId"), "header2", "value11", ');
    }

    public function testRow()
    {
        $accessor = new Accessor\Table\RowAccessor('text', array(), $this->con);

        $this->assertEquals('_sahi._row("text")', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
        $this->assertRelations($accessor, '_sahi._row("text", ');
    }

    public function testTable()
    {
        $accessor = new Accessor\Table\TableAccessor('tableId', array(), $this->con);

        $this->assertEquals('_sahi._table("tableId")', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
        $this->assertRelations($accessor, '_sahi._table("tableId", ');
    }

    public function testTableHeader()
    {
        $accessor = new Accessor\Table\TableHeaderAccessor('summ:', array(), $this->con);

        $this->assertEquals('_sahi._tableHeader("summ:")', $accessor->getAccessor());
        $this->assertSame($this->con, $accessor->getConnection());
        $this->assertRelations($accessor, '_sahi._tableHeader("summ:", ');
    }
}
