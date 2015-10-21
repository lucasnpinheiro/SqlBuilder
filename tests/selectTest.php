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

    /**
     * @expectedException \JBZoo\SqlBuilder\Exception
     */
    public function testCreateEmptySelect()
    {
        $this->_select('');
    }

    public function testSelect()
    {
        $select = $this->_select('table');
        is('' . $select, "SELECT * FROM `table`");

        $select = $this->_select('table')->select('*');
        is('' . $select, "SELECT * FROM `table`");

        $select = $this->_select('table')->select('');
        is('' . $select, "SELECT * FROM `table`");

        $select = $this->_select('table')->select('id');
        is('' . $select, "SELECT `id` FROM `table`");

        $select = $this->_select('table')->select('table.id');
        is('' . $select, "SELECT `table`.`id` FROM `table`");

        $select = $this->_select('table')->select(array('*', 'table.id', 'category'));
        is('' . $select, "SELECT *, `table`.`id`, `category` FROM `table`");
    }

    public function testTools()
    {
        $select = $this->_select('table');
        is($select->quote("'qwerty'", false), "''qwerty''");
        is($select->quoteName("qwerty"), "`qwerty`");
        is($select->escape("'qwerty'"), "\\'qwerty\\'");
        is($select->clean("?i", 10.123), 10);
        is($select->clean("?n", 'test'), 'IS NOT NULL');
        is($select->clean("?n", 0), 'IS NULL');
    }

    public function testFrom()
    {
        $select = $this->_select('table');
        is('' . $select, "SELECT * FROM `table`");

        $select = $this->_select('table');
        is('' . $select, "SELECT * FROM `table`");

        $select = $this->_select(array('table', 'tTable'));
        is('' . $select, "SELECT * FROM `table` AS `tTable`");

        $select = $this->_select(array('table', 'tTable'))
            ->from('table2')
            ->from('table3', 't3');
        is('' . $select, "SELECT * FROM `table` AS `tTable`, `table2`, `table3` AS `t3`");
    }


    public function testWhereSimple()
    {
        $select = $this->_select('table');

        $select->where('property = 1');
        is('' . $select, "SELECT * FROM `table` WHERE property = 1");

        $select->where('tTable.property <> COUNT(`id`)');
        is('' . $select, "SELECT * FROM `table` WHERE property = 1 AND tTable.property <> COUNT(`id`)");

        // empty conditions
        $select
            ->where(null)
            ->where('')
            ->where(false)
            ->where(0);
        is('' . $select, "SELECT * FROM `table` WHERE property = 1 AND tTable.property <> COUNT(`id`)");

        $select->where(array('table.prop', 'IN', '?a'), array(1, 2.3, true, false, null), 'or');
        is('' . $select, "SELECT * FROM `table` WHERE property = 1 AND tTable.property <> COUNT(`id`) "
            . "OR `table`.`prop` IN ('1', '2.3', TRUE, FALSE, NULL)");
    }

    public function testHavingSimple()
    {
        $select = $this->_select('table');

        $select->having('property = 1');
        is('' . $select, "SELECT * FROM `table` HAVING property = 1");

        $select
            ->having('')
            ->having('COUNT(*) > 1');
        is('' . $select, "SELECT * FROM `table` HAVING property = 1 AND COUNT(*) > 1");
    }

    public function testWhereConditions()
    {
        $select = $this->_select('table')->where(1);
        is('' . $select, "SELECT * FROM `table` WHERE 1");


        $select = $this->_select('table')->where(null);
        is('' . $select, "SELECT * FROM `table`");


        $select = $this->_select('table')->where('1');
        is('' . $select, "SELECT * FROM `table` WHERE 1");

        $select = $this->_select('table')->where('prop > 1');
        is('' . $select, "SELECT * FROM `table` WHERE prop > 1");


        $select = $this->_select('table')->where('COUNT(`id`) > 0');
        is('' . $select, "SELECT * FROM `table` WHERE COUNT(`id`) > 0");


        $select = $this->_select('table')->where(array('prop', '<>', '?i'), 10);
        is('' . $select, "SELECT * FROM `table` WHERE `prop` <> 10");


        $select = $this->_select('table')->where(array('prop', '<> ?i'), 10);
        is('' . $select, "SELECT * FROM `table` WHERE `prop` <> 10");

        $select = $this->_select('table')->where('1')->where(array("group_4", "= ?s"), '"testo"', "or");
        is('' . $select, "SELECT * FROM `table` WHERE 1 OR `group_4` = '\\\"testo\\\"'");

        $select = $this->_select('table')
            ->where('prop_1 = 1')
            ->where('prop_2 = 2', null, 'OR')
            ->where('prop_3 = ?i', 3.1, 'AND')
            ->where('prop_4 = ?f', 3.50, 'OR')
            ->where('prop_5 = ?s', '"testo"');
        is('' . $select, "SELECT * FROM `table` WHERE prop_1 = 1 OR prop_2 = 2 "
            . "AND prop_3 = 3 OR prop_4 = 3.5 AND prop_5 = '\\\"testo\\\"'");
    }

    public function testWhereGroups()
    {
        $select = $this->_select('table')->whereGroup(array());
        is('' . $select, "SELECT * FROM `table`");


        $select = $this->_select('table')->whereGroup(array(
            'group_1 = 1',
            array('group_2 = 2', null, 'and'),
            array('group_3 = ?i', '3.1', 'or'),
            array(array('group_4', '= ?s'), '"testo"', 'or'),
        ));
        is('' . $select, "SELECT * FROM `table` WHERE "
            . "(group_1 = 1 AND group_2 = 2 OR group_3 = 3 OR `group_4` = '\\\"testo\\\"')");


        $select = $this->_select('table')
            ->where('prop_1 = 2')
            ->whereGroup(array(
                'group_1 = 1',
                array('group_2 = 2', null, 'and'),
                array('group_3 = ?i', 3, 'or'),
                array('group_4 = ?f', 3.50, 'or'),
                array('group_5 = ?s', '"testo"'),
            ), 'OR')
            ->whereOR('prop_2 = 3');
        is('' . $select, "SELECT * FROM `table` WHERE prop_1 = 2 "
            . "OR (group_1 = 1 AND group_2 = 2 OR group_3 = 3 OR group_4 = 3.5 AND group_5 = '\\\"testo\\\"') "
            . "OR prop_2 = 3");


        $select = $this->_select('table')
            ->whereGroup('group_1 = 1', 'OR')
            ->whereGroup(array(
                'group_2 = 1',
                'group_2 = 2',
            ), 'OR')
            ->whereGroup(array(
                array('group_3 = ?i', null),
                'group_3 = 3',
            ), 'AND')
            ->whereGroup(array(
                array('group_4 = ?s', '', 'OR'),
                array('group_4 = ?s', 0, 'OR'),
                array('group_4 = ?f', null, 'OR'),
            ), 'OR');
        is('' . $select, "SELECT * FROM `table` WHERE (group_1 = 1) "
            . "OR (group_2 = 1 AND group_2 = 2) "
            . "AND (group_3 = 0 AND group_3 = 3) "
            . "OR (group_4 = '' OR group_4 = '0' OR group_4 = 0)");
    }

    public function testWhereEscapeIdentifiers()
    {
        $select = $this->_select('table')->where('tTable.property = ?e', 'property');
        is('' . $select, "SELECT * FROM `table` WHERE tTable.property = `property`");

        $select = $this->_select('table')->where('tTable.property = ?e', 'tTable.property');
        is('' . $select, "SELECT * FROM `table` WHERE tTable.property = `tTable`.`property`");

        $select = $this->_select('table')->where('tTable.property = ?e', 'tTable.property');
        is('' . $select, "SELECT * FROM `table` WHERE tTable.property = `tTable`.`property`");
    }


    public function testWhereEscapeInteger()
    {
        $select = $this->_select('table')->where('tTable.property = ?i', 10.1);
        is('' . $select, "SELECT * FROM `table` WHERE tTable.property = 10");

        $select = $this->_select('table')->where('tTable.property = ?i', -1);
        is('' . $select, "SELECT * FROM `table` WHERE tTable.property = -1");

        $select = $this->_select('table')->where('tTable.property = ?i', -1.05);
        is('' . $select, "SELECT * FROM `table` WHERE tTable.property = -1");

        $select = $this->_select('table')->where('tTable.property = ?i');
        is('' . $select, "SELECT * FROM `table` WHERE tTable.property = 0");
    }

    public function testWhereEscapeFloat()
    {
        $select = $this->_select('table')->where('tTable.property = ?f', 10.1);
        is('' . $select, "SELECT * FROM `table` WHERE tTable.property = 10.1");

        $select = $this->_select('table')->where('tTable.property = ?f', '');
        is('' . $select, "SELECT * FROM `table` WHERE tTable.property = 0");

        $select = $this->_select('table')->where('tTable.property = ?f', null);
        is('' . $select, "SELECT * FROM `table` WHERE tTable.property = 0");
    }

    public function testWhereEscapeString()
    {
        $select = $this->_select('table')->where('tTable.property = ?s', 'string');
        is('' . $select, "SELECT * FROM `table` WHERE tTable.property = 'string'");

        $select = $this->_select('table')->where('tTable.property = ?s', ' !@#$%^&*()_+`"\\/?.,:;{}<>|~ ');
        is('' . $select, "SELECT * FROM `table` WHERE tTable.property = ' !@#$%^&*()_+`\\\"\\\\/?.,:;{}<>|~ '");
    }

    public function testWhereEscapeBoolean()
    {
        $select = $this->_select('table')->where('tTable.property = ?b', true);
        is('' . $select, "SELECT * FROM `table` WHERE tTable.property = TRUE");

        $select = $this->_select('table')->where('tTable.property = ?b', false);
        is('' . $select, "SELECT * FROM `table` WHERE tTable.property = FALSE");

        $select = $this->_select('table')->where('tTable.property = ?b');
        is('' . $select, "SELECT * FROM `table` WHERE tTable.property = FALSE");

        $select = $this->_select('table')->where('tTable.property = ?b', 1);
        is('' . $select, "SELECT * FROM `table` WHERE tTable.property = TRUE");
    }

    public function testWhereEscapeArray()
    {
        $select = $this->_select('table')->where('tTable.property IN ?a', array('0', 1, true, false, null, '"\''));
        is('' . $select, "SELECT * FROM `table` WHERE tTable.property IN ('0', '1', TRUE, FALSE, NULL, '\\\"\'')");

        $select = $this->_select('table')->where('tTable.property IN ?a', array());
        is('' . $select, "SELECT * FROM `table` WHERE tTable.property IN (NULL)");

        $select = $this->_select('table')->where('tTable.property IN ?a');
        is('' . $select, "SELECT * FROM `table` WHERE tTable.property IN (NULL)");

        $select = $this->_select('table')->where('tTable.property IN ?a', null);
        is('' . $select, "SELECT * FROM `table` WHERE tTable.property IN (NULL)");
    }

    public function testWhereEscapeUpdate()
    {
        $select = $this->_select('table')->where('?k', array(
            'string' => array('?s', 'string'),
            'float'  => array('?f', '123,456'),
            'int'    => 654,
        ));
        is('' . $select, "SELECT * FROM `table` WHERE `string`='string', `float`=123.456, `int`=654");

        $select = $this->_select('table')->where('?k', 'fail');
        is('' . $select, "SELECT * FROM `table`");
    }

    public function testLimit()
    {
        $select = $this->_select('table')->limit(0);
        is('' . $select, "SELECT * FROM `table`");

        $select = $this->_select('table')->limit(1, 0);
        is('' . $select, "SELECT * FROM `table` LIMIT 1");

        $select = $this->_select('table')->limit(1, 10);
        is('' . $select, "SELECT * FROM `table` LIMIT 10, 1");

        $select = $this->_select('table')->limit(1, 10)->limit(1);
        is('' . $select, "SELECT * FROM `table` LIMIT 1");
    }

    public function testJoin()
    {
        $select = $this->_select(array('table', 'tTable'))
            ->join('undefined', 'join_table', 'join_table.item_id = tTable.id');
        is('' . $select, "SELECT * FROM `table` AS `tTable`");

        $select = $this->_select(array('table', 'tTable'))
            ->leftJoin('join_table', 'join_table.item_id = tTable.id');
        is('' . $select, "SELECT * FROM `table` AS `tTable` "
            . "LEFT JOIN `join_table` ON (join_table.item_id = tTable.id)");

        $select = $this->_select(array('table', 'tTable'))
            ->leftJoin(
                array('join_table', 'tJoin'),
                'tJoin.item_id = tTable.id'
            );
        is('' . $select, "SELECT * FROM `table` AS `tTable` "
            . "LEFT JOIN `join_table` AS `tJoin` ON (tJoin.item_id = tTable.id)");

        $select = $this->_select(array('table', 'tTable'))
            ->leftJoin(
                array('join_table', 'tJoin'),
                array('tJoin.item_id = tTable.id', 'tJoin.cat_id = tTable.cat_id')
            );
        is('' . $select, "SELECT * FROM `table` AS `tTable` "
            . "LEFT JOIN `join_table` AS `tJoin` ON (tJoin.item_id = tTable.id AND tJoin.cat_id = tTable.cat_id)");


        $select = $this->_select(array('table', 'tTable'))
            ->rightJoin(
                array('join_table', 'tJoin'),
                array(
                    'tJoin.item_id = tTable.id',
                    'tJoin.item_id = tTable.id',
                )
            );
        is('' . $select, "SELECT * FROM `table` AS `tTable` "
            . "RIGHT JOIN `join_table` AS `tJoin` ON (tJoin.item_id = tTable.id)");


        $select = $this->_select(array('table', 'tTable'))
            ->leftJoin(
                array('join_table', 'tLeftJoin'),
                'tJoin.item_id = tTable.id'
            )
            ->rightJoin(
                array('join_table', 'tRightJoin'),
                array(
                    'tJoin.item_id = tTable.id',
                    'tJoin.cat_id = tTable.cat_id',
                )
            )
            ->innerJoin(
                array('join_table', 'tInnerJoin'),
                array('tJoin.item_id = tTable.id')
            );
        is('' . $select, "SELECT * FROM `table` AS `tTable` "
            . "LEFT JOIN `join_table` AS `tLeftJoin` ON (tJoin.item_id = tTable.id) "
            . "RIGHT JOIN `join_table` AS `tRightJoin` ON (tJoin.item_id = tTable.id AND tJoin.cat_id = tTable.cat_id) "
            . "INNER JOIN `join_table` AS `tInnerJoin` ON (tJoin.item_id = tTable.id)");
    }

    public function testPrefix()
    {
        $select = $this->_select(array('#__table'));
        is('' . $select, "SELECT * FROM `t_table`");
    }

    public function testGroup()
    {
        $select = $this->_select('table')->group('prop');
        is('' . $select, "SELECT * FROM `table` GROUP BY `prop`");

        $select = $this->_select('table')->group('prop', true);
        is('' . $select, "SELECT * FROM `table` GROUP BY `prop`");

        $select = $this->_select('table')->group('qwerty')->group('prop');
        is('' . $select, "SELECT * FROM `table` GROUP BY `qwerty`, `prop`");

        $select = $this->_select('table')->group('qwerty', false)->group('prop', false);
        is('' . $select, "SELECT * FROM `table` GROUP BY qwerty, prop");

        $select = $this->_select('table')->group('COUNT(`id`)', false);
        is('' . $select, "SELECT * FROM `table` GROUP BY COUNT(`id`)");
    }

    public function testOrder()
    {
        $select = $this->_select('table')->order('prop');
        is('' . $select, "SELECT * FROM `table` ORDER BY `prop` ASC");

        $select = $this->_select('table')->order('prop', 'desc');
        is('' . $select, "SELECT * FROM `table` ORDER BY `prop` DESC");

        $select = $this->_select('table')->order('prop', 'qwerty');
        is('' . $select, "SELECT * FROM `table`");

        $select = $this->_select('table')->order('COUNT(`id`)', 'desc', false);
        is('' . $select, "SELECT * FROM `table` ORDER BY COUNT(`id`) DESC");

        $select = $this->_select('table')
            ->order('prop1')
            ->order('prop2', 'desc')
            ->order('prop2', 'ASC')
            ->order('prop3', 'desc');
        is('' . $select, "SELECT * FROM `table` ORDER BY `prop1` ASC, `prop2` ASC, `prop3` DESC");
    }

    public function testOptions()
    {
        $select = $this->_select('table')->option('SQL_NO_CACHE');
        is('' . $select, "SELECT SQL_NO_CACHE * FROM `table`");

        $select = $this->_select('table')->option(array('SQL_CACHE', 'HIGH_PRIORITY'));
        is('' . $select, "SELECT SQL_CACHE HIGH_PRIORITY * FROM `table`");

        $select = $this->_select('table')
            ->option(array(
                'SQL_BUFFER_RESULT',
                'SQL_NO_CACHE',
                '',
            ))
            ->option(array(
                'DISTINCT',
            ));
        is('' . $select, "SELECT SQL_BUFFER_RESULT SQL_NO_CACHE DISTINCT * FROM `table`");
    }

    public function testExplain()
    {
        $select = $this->_select('table')->explain();
        is('' . $select, "EXPLAIN SELECT * FROM `table`");

        $select = $this->_select('table')->explain(true);
        is('' . $select, "EXPLAIN SELECT * FROM `table`");

        $select = $select->explain(false);
        is('' . $select, "SELECT * FROM `table`");
    }
}
