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
 * Class PerformanceTest
 * @package JBZoo\SqlBuilder
 */
class PerformanceTest extends PHPUnit
{
    protected $_max = 1000;

    public function testLeakMemoryCreate()
    {
        if ($this->isXDebug()) {
            return;
        }

        $this->startProfiler();
        for ($i = 0; $i < $this->_max; $i++) {
            $select = new Select(array('table', 'tTable'));

            $sql = $select->__toString();
            unset($select);
            unset($sql);
        }

        alert($this->loopProfiler($this->_max), 'Create - min');
    }

    public function testLeakMemoryMin()
    {
        if ($this->isXDebug()) {
            return;
        }

        $this->startProfiler();
        for ($i = 0; $i < $this->_max; $i++) {
            $select = new Select(array('table', 'tTable'));

            $select
                ->select('*')
                ->where('property = ?s', '"testo2"')
                ->where('?k', array('string' => 'string', 'float' => 123.456, 'int' => 654))
                ->whereGroup('group_1 = 1', 'OR')
                ->whereGroup(array(
                    array('group_4 = ?s', '', 'OR'),
                    array('group_4 = ?s', 0, 'OR'),
                    array('group_4 = ?f', null, 'OR'),
                ), 'OR')
                // limit
                ->limit(1, 10)
                ->rightJoin(
                    array('join_table', 'tRightJoin'),
                    array(
                        'tJoin.item_id = tTable.id',
                        'tJoin.cat_id = tTable.cat_id',
                    )
                );

            $sql = $select->__toString();
            unset($select);
            unset($sql);
        }

        alert($this->loopProfiler($this->_max), 'Create - Normal');
    }

    public function testLeakMemoryMax()
    {
        if ($this->isXDebug()) {
            return;
        }

        $this->startProfiler();
        for ($i = 0; $i < $this->_max; $i++) {
            $select = new Select(array('table', 'tTable'));

            $select
                ->from('table2')
                ->from('#__table', 't3')
                // select
                ->select('*')
                ->select(array('table.id', 'category'))
                // where
                ->where('property = 1')
                ->where('property = ?n', 'test_prop')
                ->where('property = ?i', '10')
                ->where('property = ?f', '10.1')
                ->where('property = ?s', '`testo`')
                ->where('property = ?s', '"testo2"', 'or')
                ->where('property = ?s', '"testo2"')
                ->where('tTable.property = ?b', true)
                ->where('tTable.property IN ?a', array('0', 1, true, false, null, '"\''))
                ->where('(?k)', array('string' => 'string', 'float' => 123.456, 'int' => 654))
                ->where(null)
                ->where('')
                ->where(false)
                ->where(0)
                // where group
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
                ), 'OR')
                // limit
                ->limit(1, 10)
                ->limit(1)
                ->limit(10, 1)
                // join
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
                )
                ->group('qwerty', false)
                ->group('prop', false)
                ->order('prop1')
                ->order('prop2', 'desc')
                ->order('prop3', 'ASC', false)
                ->order('prop4', 'desc', true)
                ->option(array(
                    'SQL_BUFFER_RESULT',
                    'SQL_NO_CACHE',
                    '',
                ))
                ->option(array(
                    'DISTINCT',
                ))
                ->explain();

            $sql = $select->__toString();
            unset($select);
            unset($sql);
        }

        alert($this->loopProfiler($this->_max), 'Create - Big');
    }
}
