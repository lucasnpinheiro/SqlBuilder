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
        is("''qwerty''", $select->quote("'qwerty'", false));
        is("\\'qwerty\\'", $select->escape("'qwerty'"));
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

        $select->where('tTable.property = 2');
        is('' . $select, "SELECT * FROM `table` WHERE property = 1 AND tTable.property = 2");

        // empty conditions
        $select
            ->where(null)
            ->where('')
            ->where(false)
            ->where(0);

        is('' . $select, "SELECT * FROM `table` WHERE property = 1 AND tTable.property = 2");
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
        $select = $this->_select('table')
            ->where('prop_1 = 1')
            ->where('prop_2 = 2', null, 'OR')
            ->where('prop_3 = ?i', 3, 'AND')
            ->where('prop_4 = ?f', 3.50, 'OR')
            ->where('prop_5 = ?s', '"testo"');

        is('' . $select, "SELECT * FROM `table` WHERE prop_1 = 1 OR prop_2 = 2 "
            . "AND prop_3 = 3 OR prop_4 = 3.5 AND prop_5 = '\\\"testo\\\"'");

        $select = $this->_select('table')
            ->where('prop_1 = 2')
            ->whereGroup(array(
                'group_1 = 1',
                array('group_2 = 2', null, 'and'),
                array('group_3 = ?i', 3, 'or'),
                array('group_4 = ?f', 3.50, 'or'),
                array('group_5 = ?s', '"testo"'),
            ), 'OR')
            ->where('prop_2 = 3', null, 'OR');
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
            . "AND (group_3 = NULL AND group_3 = 3) "
            . "OR (group_4 = '' OR group_4 = '0' OR group_4 = NULL)");
    }

    public function testWhereEscapeIdentifiers()
    {
        $select = $this->_select('table')->where('tTable.property = ?n', 'property');
        is('' . $select, "SELECT * FROM `table` WHERE tTable.property = `property`");

        $select = $this->_select('table')->where('tTable.property = ?n', 'tTable.property');
        is('' . $select, "SELECT * FROM `table` WHERE tTable.property = `tTable`.`property`");

        $select = $this->_select('table')->where('tTable.property = ?n', 'tTable.property');
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
        is('' . $select, "SELECT * FROM `table` WHERE tTable.property = NULL");
    }

    public function testWhereEscapeFloat()
    {
        $select = $this->_select('table')->where('tTable.property = ?f', 10.1);
        is('' . $select, "SELECT * FROM `table` WHERE tTable.property = 10.1");

        $select = $this->_select('table')->where('tTable.property = ?f', '');
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
        $select = $this->_select('table')->where('?u', array('string' => 'string', 'float' => 123.456, 'int' => 654));
        is('' . $select, "SELECT * FROM `table` WHERE `string`='string', `float`='123.456', `int`='654'");

        $select = $this->_select('table')->where('?u', 'fail');
        is('' . $select, "SELECT * FROM `table` WHERE");
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
    }

    public function testOrder()
    {
        $select = $this->_select('table')->order('prop');
        is('' . $select, "SELECT * FROM `table` ORDER BY `prop` ASC");

        $select = $this->_select('table')->order('prop', 'asc', true);
        is('' . $select, "SELECT * FROM `table` ORDER BY `prop` ASC");

        $select = $this->_select('table')->order('prop', 'desc', false);
        is('' . $select, "SELECT * FROM `table` ORDER BY prop DESC");

        $select = $this->_select('table')
            ->order('prop1')
            ->order('prop2', 'desc')
            ->order('prop3', 'ASC', false)
            ->order('prop4', 'desc', true);
        is('' . $select, "SELECT * FROM `table` ORDER BY `prop1` ASC, `prop2` DESC, prop3 ASC, `prop4` DESC");
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
