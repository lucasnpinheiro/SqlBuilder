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

namespace JBZoo\PHPUnit;

use JBZoo\SqlBuilder\Query\Select;

/**
 * Class SelectTest
 * @package JBZoo\SqlBuilder
 */
class SelectTest extends PHPUnit
{
    /**
     * @param null $tableName
     * @return Select
     */
    protected function _select($tableName = null)
    {
        return new Select($tableName);
    }

    public function testCreateEmpty()
    {
        is('', (string)(new Select()));
        is('', (string)(new Select('')));
        is('', (string)(new Select(null)));
    }

    public function testFrom()
    {
        $select = $this->_select('table');
        is((string)$select, "SELECT * FROM `table`");

        $select = $this->_select('table');
        is((string)$select, "SELECT * FROM `table`");

        $select = $this->_select()->from('table');
        is((string)$select, "SELECT * FROM `table`");

        $select = $this->_select()->from('table', 'tTable');
        is((string)$select, "SELECT * FROM `table` AS tTable");

        $select = $this->_select(array('table', 'tTable'));
        is((string)$select, "SELECT * FROM `table` AS tTable");
    }

    public function testWhereSimple()
    {
        $select = new Select('table');

        $select->where('property = 1');
        is((string)$select, "SELECT * FROM `table` WHERE property = 1");

        $select->where('tTable.property = 2');
        is((string)$select, "SELECT * FROM `table` WHERE property = 1 AND tTable.property = 2");

        // empty conditions
        $select
            ->where(null)
            ->where('')
            ->where(false)
            ->where(0);

        is((string)$select, "SELECT * FROM `table` WHERE property = 1 AND tTable.property = 2");
    }

    public function testWhereEscapeIdentifiers()
    {
        $select = $this->_select('table')->where('tTable.property = ?n', 'property');
        is((string)$select, "SELECT * FROM `table` WHERE tTable.property = `property`");

        $select = $this->_select('table')->where('tTable.property = ?n', 'tTable.property');
        is((string)$select, "SELECT * FROM `table` WHERE tTable.property = `tTable`.`property`");

        $select = $this->_select('table')->where('tTable.property = ?n', 'tTable.property');
        is((string)$select, "SELECT * FROM `table` WHERE tTable.property = `tTable`.`property`");
    }

    public function testWhereEscapeInteger()
    {
        $select = $this->_select('table')->where('tTable.property = ?i', 10.1);
        is((string)$select, "SELECT * FROM `table` WHERE tTable.property = 10");

        $select = $this->_select('table')->where('tTable.property = ?i', -1);
        is((string)$select, "SELECT * FROM `table` WHERE tTable.property = -1");

        $select = $this->_select('table')->where('tTable.property = ?i', -1.05);
        is((string)$select, "SELECT * FROM `table` WHERE tTable.property = -1");

        $select = $this->_select('table')->where('tTable.property = ?i');
        is((string)$select, "SELECT * FROM `table` WHERE tTable.property = NULL");
    }

    public function testWhereEscapeFloat()
    {
        $select = $this->_select('table')->where('tTable.property = ?f', 10.1);
        is((string)$select, "SELECT * FROM `table` WHERE tTable.property = 10.1");

        $select = $this->_select('table')->where('tTable.property = ?f', '');
        is((string)$select, "SELECT * FROM `table` WHERE tTable.property = 0");
    }

    public function testWhereEscapeString()
    {
        $select = $this->_select('table')->where('tTable.property = ?s', 'string');
        is((string)$select, "SELECT * FROM `table` WHERE tTable.property = 'string'");

        $select = $this->_select('table')->where('tTable.property = ?s', ' !@#$%^&*()_+`"\\/?.,:;{}<>|~ ');
        is((string)$select, "SELECT * FROM `table` WHERE tTable.property = ' !@#$%^&*()_+`\\\"\\\\/?.,:;{}<>|~ '");
    }

    public function testWhereEscapeBoolean()
    {
        $select = $this->_select('table')->where('tTable.property = ?b', true);
        is((string)$select, "SELECT * FROM `table` WHERE tTable.property = TRUE");

        $select = $this->_select('table')->where('tTable.property = ?b', false);
        is((string)$select, "SELECT * FROM `table` WHERE tTable.property = FALSE");

        $select = $this->_select('table')->where('tTable.property = ?b');
        is((string)$select, "SELECT * FROM `table` WHERE tTable.property = FALSE");

        $select = $this->_select('table')->where('tTable.property = ?b', 1);
        is((string)$select, "SELECT * FROM `table` WHERE tTable.property = TRUE");
    }

    public function testWhereEscapeArray()
    {
        $select = $this->_select('table')->where('tTable.property IN ?a', array('0', 1, true, false, null, '"\''));
        is((string)$select, "SELECT * FROM `table` WHERE tTable.property IN ('0', '1', TRUE, FALSE, NULL, '\\\"\'')");

        $select = $this->_select('table')->where('tTable.property IN ?a', array());
        is((string)$select, "SELECT * FROM `table` WHERE tTable.property IN (NULL)");

        $select = $this->_select('table')->where('tTable.property IN ?a');
        is((string)$select, "SELECT * FROM `table` WHERE tTable.property IN (NULL)");

        $select = $this->_select('table')->where('tTable.property IN ?a', null);
        is((string)$select, "SELECT * FROM `table` WHERE tTable.property IN (NULL)");
    }

    public function testWhereEscapeUpdate()
    {
        $select = $this->_select('table')->where('?u', array('string' => 'string', 'float' => 123.456, 'int' => 654));
        is((string)$select, "SELECT * FROM `table` WHERE `string`='string', `float`='123.456', `int`='654'");

        $select = $this->_select('table')->where('?u', 'fail');
        is((string)$select, "SELECT * FROM `table` WHERE");

    }
}
