<?php
/**
 * JBZoo SqlBuilder
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package   SqlBuilder
 * @license   MIT
 * @copyright Copyright (C) JBZoo.com,  All rights reserved.
 * @link      https://github.com/JBZoo/SqlBuilder
 * @author    Denis Smetannikov <denis@jbzoo.com>
 */

namespace JBZoo\SqlBuilder;

use JBZoo\SqlBuilder\Query\Select;

/**
 * Class SelectTest
 * @package JBZoo\SqlBuilder
 */
class SelectTest extends PHPUnit
{

    protected function _select($tableName = null)
    {
        return new Select($tableName);
    }

    public function testCreateEmpty()
    {
        self::assertEquals('', (string)(new Select()));
        self::assertEquals('', (string)(new Select('')));
        self::assertEquals('', (string)(new Select(null)));
    }

    public function testFrom()
    {
        $select = $this->_select('table');
        self::assertEquals((string)$select, "SELECT * FROM `table`");

        $select = $this->_select('table');
        self::assertEquals((string)$select, "SELECT * FROM `table`");

        $select = $this->_select()->from('table');
        self::assertEquals((string)$select, "SELECT * FROM `table`");

        $select = $this->_select()->from('table', 'tTable');
        self::assertEquals((string)$select, "SELECT * FROM `table` AS tTable");
    }

    public function testWhereSimple()
    {
        $select = new Select('table');

        $select->where('property = 1');
        self::assertEquals((string)$select, "SELECT * FROM `table` WHERE property = 1");

        $select->where('tTable.property = 2');
        self::assertEquals((string)$select, "SELECT * FROM `table` WHERE property = 1 AND tTable.property = 2");
    }

    public function testWhereEscapeIdentifiers()
    {
        $select = $this->_select('table')->where('tTable.property = ?n', 'property');
        self::assertEquals((string)$select, "SELECT * FROM `table` WHERE tTable.property = `property`");

        $select = $this->_select('table')->where('tTable.property = ?n', 'tTable.property');
        self::assertEquals((string)$select, "SELECT * FROM `table` WHERE tTable.property = `tTable`.`property`");

        $select = $this->_select('table')->where('tTable.property = ?n', 'tTable.property');
        self::assertEquals((string)$select, "SELECT * FROM `table` WHERE tTable.property = `tTable`.`property`");
    }

    public function testWhereEscapeInteger()
    {
        $select = $this->_select('table')->where('tTable.property = ?i', 10.1);
        self::assertEquals((string)$select, "SELECT * FROM `table` WHERE tTable.property = 10");

        $select = $this->_select('table')->where('tTable.property = ?i', -1);
        self::assertEquals((string)$select, "SELECT * FROM `table` WHERE tTable.property = -1");

        $select = $this->_select('table')->where('tTable.property = ?i', -1.05);
        self::assertEquals((string)$select, "SELECT * FROM `table` WHERE tTable.property = -1");

        $select = $this->_select('table')->where('tTable.property = ?i');
        self::assertEquals((string)$select, "SELECT * FROM `table` WHERE tTable.property = NULL");
    }

    public function testWhereEscapeFloat()
    {
        $select = $this->_select('table')->where('tTable.property = ?f', 10.1);
        self::assertEquals((string)$select, "SELECT * FROM `table` WHERE tTable.property = 10.1");

        $select = $this->_select('table')->where('tTable.property = ?f', '');
        self::assertEquals((string)$select, "SELECT * FROM `table` WHERE tTable.property = 0");
    }

    public function testWhereEscapeString()
    {
        $select = $this->_select('table')->where('tTable.property = ?s', 'string');
        self::assertEquals((string)$select, "SELECT * FROM `table` WHERE tTable.property = 'string'");

        $select = $this->_select('table')->where('tTable.property = ?s', ' !@#$%^&*()_+`"\\/?.,:;{}<>|~ ');
        $this->assertEquals((string)$select, "SELECT * FROM `table` WHERE tTable.property = ' !@#$%^&*()_+`\\\"\\\\/?.,:;{}<>|~ '");
    }

    public function testWhereEscapeBoolean()
    {
        $select = $this->_select('table')->where('tTable.property = ?b', true);
        $this->assertEquals((string)$select, "SELECT * FROM `table` WHERE tTable.property = TRUE");

        $select = $this->_select('table')->where('tTable.property = ?b', false);
        $this->assertEquals((string)$select, "SELECT * FROM `table` WHERE tTable.property = FALSE");

        $select = $this->_select('table')->where('tTable.property = ?b');
        $this->assertEquals((string)$select, "SELECT * FROM `table` WHERE tTable.property = FALSE");

        $select = $this->_select('table')->where('tTable.property = ?b', 1);
        $this->assertEquals((string)$select, "SELECT * FROM `table` WHERE tTable.property = TRUE");
    }

    public function testWhereEscapeArray()
    {
        $select = $this->_select('table')->where('tTable.property IN ?a', array('0', 1, true, false, null, '"\''));
        $this->assertEquals((string)$select, "SELECT * FROM `table` WHERE tTable.property IN ('0', '1', TRUE, FALSE, NULL, '\\\"\'')");

        $select = $this->_select('table')->where('tTable.property IN ?a', array());
        $this->assertEquals((string)$select, "SELECT * FROM `table` WHERE tTable.property IN (NULL)");

        $select = $this->_select('table')->where('tTable.property IN ?a');
        $this->assertEquals((string)$select, "SELECT * FROM `table` WHERE tTable.property IN (NULL)");

        $select = $this->_select('table')->where('tTable.property IN ?a', null);
        $this->assertEquals((string)$select, "SELECT * FROM `table` WHERE tTable.property IN (NULL)");
    }

    public function testWhereEscapeUpdate()
    {
        $select = $this->_select('table')->where('?u', array('string' => 'string', 'float' => 123.456, 'int' => 654));
        $this->assertEquals((string)$select, "SELECT * FROM `table` WHERE `string`='string', `float`='123.456', `int`='654'");

        $select = $this->_select('table')->where('?u', 'fail');
        $this->assertEquals((string)$select, "SELECT * FROM `table` WHERE");

    }
}
