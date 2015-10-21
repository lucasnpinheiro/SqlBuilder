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

use JBZoo\SqlBuilder\Query\Replace;

/**
 * Class ReplaceTest
 * @package JBZoo\SqlBuilder
 */
class ReplaceTest extends PHPUnit
{
    /**
     * @param null $tableName
     * @return Replace
     */
    protected function _replace($tableName = null)
    {
        return new Replace($tableName);
    }

    /**
     * @expectedException \JBZoo\SqlBuilder\Exception
     */
    public function testCreateEmptySelect()
    {
        $this->_replace('');
    }

    public function testInto()
    {
        $replace = $this->_replace('table');
        is('' . $replace, "REPLACE INTO `table`");

        $replace->into('table2');
        is('' . $replace, "REPLACE INTO `table2`");
    }

    public function testRow()
    {
        $replace = $this->_replace('table')->row(array('name' => 'Agent'));
        is('' . $replace, "REPLACE INTO `table` (`name`) VALUES ('Agent')");

        $replace = $this->_replace('table')->row(array('name' => 'Agent', 'surname' => 'Smith'));
        is('' . $replace, "REPLACE INTO `table` (`name`, `surname`) VALUES ('Agent', 'Smith')");

        $replace = $this->_replace('table')
            ->row(array('name' => 'Agent', 'surname' => 'Smith'))
            ->row(array('string' => "'qwerty'", 'bool' => true, 'null' => null));
        is('' . $replace, "REPLACE INTO `table` (`name`, `surname`, `string`, `bool`, `null`) "
            . "VALUES ('Agent', 'Smith', '\\'qwerty\\'', TRUE, NULL)");

        $replace = $this->_replace('table')
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
        is('' . $replace, "REPLACE INTO `table` (`name`, `surname`, `qwerty`, `prop`) "
            . "VALUES ('Vasya', 'Pupkin', '\\'qwerty\\'', FALSE)");
    }

    public function testMulti()
    {
        $replace = $this->_replace('table')->multi(array());
        is('' . $replace, "REPLACE INTO `table`");

        $replace = $this->_replace('table')
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

        is('' . $replace, "REPLACE INTO `table` (`name`, `surname`, `string`, `bool`) VALUES "
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
        $replace = $this->_replace('table')
            ->option('DELAYED')
            ->option('UNDEFINED')
            ->option(array('IGNORE'));

        is('' . $replace, "REPLACE DELAYED IGNORE INTO `table`");
    }
}
