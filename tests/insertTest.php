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

use JBZoo\SqlBuilder\Query\Insert;

/**
 * Class InsertTest
 * @package JBZoo\SqlBuilder
 */
class InsertTest extends PHPUnit
{
    /**
     * @param null $tableName
     * @return Insert
     */
    protected function _insert($tableName = null)
    {
        return new Insert($tableName);
    }

    /**
     * @expectedException \JBZoo\SqlBuilder\Exception
     */
    public function testCreateEmptySelect()
    {
        $this->_insert('');
    }

    public function testInto()
    {
        $insert = $this->_insert('table');
        is('' . $insert, "INSERT INTO `table`");

        $insert->into('table2');
        is('' . $insert, "INSERT INTO `table2`");
    }

    public function testColumns()
    {
        $insert = $this->_insert('table')->columns(array('col1', 'col2', 'col3'));
        is('' . $insert, "INSERT INTO `table` (`col1`, `col2`, `col3`)");

        $insert->columns('col4');
        is('' . $insert, "INSERT INTO `table` (`col1`, `col2`, `col3`, `col4`)");
    }

    public function testValues()
    {
        $insert = $this->_insert('table')->values('Agent');
        is('' . $insert, "INSERT INTO `table` VALUES ('Agent')");

        $insert = $this->_insert('table')->values(array('Agent'));
        is('' . $insert, "INSERT INTO `table` VALUES ('Agent')");

        $insert = $this->_insert('table')->values(array('Agent', 'Smith'));
        is('' . $insert, "INSERT INTO `table` VALUES ('Agent', 'Smith')");

        $insert = $this->_insert('table')->values(array('Agent'))->values('Smith');
        is('' . $insert, "INSERT INTO `table` VALUES ('Agent', 'Smith')");

        $insert = $this->_insert('table')->values(array(
            'Agent Smith', "'qwerty'", 123, 123.456, true, false, null,
        ));
        is('' . $insert, "INSERT INTO `table` VALUES ("
            . "'Agent Smith', '\\'qwerty\\'', '123', '123.456', TRUE, FALSE, NULL)");
    }

    public function testData()
    {
        skip('Not ready');
        $insert = $this->_insert('table')->data(array('name' => 'Agent'));
        is('' . $insert, "INSERT INTO `table` (`name`) VALUES ('Agent')");

        $insert = $this->_insert('table')->data(array('name' => 'Agent', 'surname' => 'Smith'));
        is('' . $insert, "INSERT INTO `table` (`name`, `surname`) VALUES ('Agent', 'Smith')");

        $insert = $this->_insert('table')
            ->data(array('name' => 'Agent', 'surname' => 'Smith'))
            ->data(array('string' => "'qwerty'", 'bool' => true, 'null' => null));
        is('' . $insert, "INSERT INTO `table` (`name`, `surname`, `string`, `bool`, `null`) "
            . "VALUES ('Agent', 'Smith', '\\'qwerty\\'', TRUE, NULL)");

        $insert = $this->_insert('table')
            ->data(array('name' => 'Agent', 'qwerty' => null, 'surname' => 'Smith'))
            ->data(array('qwerty' => "'qwerty'", 'name' => "Vasya", 'surname' => 'Pupkin'));
        is('' . $insert, "INSERT INTO `table` (`name`, `surname`, `qwerty`) "
            . "VALUES ('Vasya', 'Pupkin', '\\'qwerty\\'')");
    }
}
