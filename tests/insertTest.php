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

    public function testRow()
    {
        $insert = $this->_insert('table')->row(array('name' => 'Agent'));
        is('' . $insert, "INSERT INTO `table` (`name`) VALUES ('Agent')");

        $insert = $this->_insert('table')->row(array('name' => 'Agent', 'surname' => 'Smith'));
        is('' . $insert, "INSERT INTO `table` (`name`, `surname`) VALUES ('Agent', 'Smith')");

        $insert = $this->_insert('table')
            ->row(array('name' => 'Agent', 'surname' => 'Smith'))
            ->row(array('string' => "'qwerty'", 'bool' => true, 'null' => null));
        is('' . $insert, "INSERT INTO `table` (`name`, `surname`, `string`, `bool`, `null`) "
            . "VALUES ('Agent', 'Smith', '\\'qwerty\\'', TRUE, NULL)");

        $insert = $this->_insert('table')
            ->row(array(
                'name'    => 'Agent',
                'surname' => 'Smith',
                'qwerty'  => null,
            ))
            ->row(array(
                'qwerty'  => "'qwerty'",
                'name'    => "Vasya",
                'surname' => 'Pupkin',
                'prop'    => false,
            ));
        is('' . $insert, "INSERT INTO `table` (`name`, `surname`, `qwerty`, `prop`) "
            . "VALUES ('Vasya', 'Pupkin', '\\'qwerty\\'', FALSE)");
    }

    public function testMulti()
    {
        $insert = $this->_insert('table')->multi(array());
        is('' . $insert, "INSERT INTO `table`");

        $insert = $this->_insert('table')
            ->multi(array(
                array('name' => 'Agent', 'surname' => 'Smith', 'string' => '1', 'bool' => false),
                array("'qwerty'", null, true, false),
                array("'qwerty'", null, 123, -456.987),
                array(true, "'qwerty'", null),
                array(),
                array(0),
                array(0, 1),
                array(0, 1, 2),
                array(0, 1, 2, 3),
                array(0, 1, 2, 3, 4),
            ));

        is('' . $insert, "INSERT INTO `table` (`name`, `surname`, `string`, `bool`) VALUES "
            . "('Agent', 'Smith', '1', FALSE), "
            . "('\'qwerty\'', NULL, TRUE, FALSE), "
            . "('\'qwerty\'', NULL, '123', '-456.987'), "
            . "(TRUE, '\'qwerty\'', NULL, NULL), "
            . "(NULL, NULL, NULL, NULL), "
            . "('0', NULL, NULL, NULL), "
            . "('0', '1', NULL, NULL), "
            . "('0', '1', '2', NULL), "
            . "('0', '1', '2', '3'), "
            . "('0', '1', '2', '3')");
    }

    public function testOptions()
    {
        $insert = $this->_insert('table')
            ->option('DELAYED')
            ->option('UNDEFINED')
            ->option(array('IGNORE'));

        is('' . $insert, "INSERT DELAYED IGNORE INTO `table`");
    }
}
