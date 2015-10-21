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

use JBZoo\SqlBuilder\Query\Delete;

/**
 * Class DeleteTest
 * @package JBZoo\SqlBuilder
 */
class DeleteTest extends PHPUnit
{
    /**
     * @param null $tableName
     * @return Delete
     */
    protected function _delete($tableName = null)
    {
        return new Delete($tableName);
    }

    /**
     * @expectedException \JBZoo\SqlBuilder\Exception
     */
    public function testCreateEmptySelect()
    {
        $this->_delete('');
    }

    public function testFrom()
    {
        $delete = $this->_delete('table');
        is('' . $delete, "DELETE FROM `table`");

        $delete = $this->_delete('table');
        is('' . $delete, "DELETE FROM `table`");

        $delete = $this->_delete(array('table', 'tTable'));
        is('' . $delete, "DELETE FROM `table` AS `tTable`");

        $delete = $this->_delete(array('table', 'tTable'))
            ->from('table2')
            ->from('table3', 't3');
        is('' . $delete, "DELETE FROM `table` AS `tTable`, `table2`, `table3` AS `t3`");
    }

    public function testOptions()
    {
        $delete = $this->_delete('table')
            ->option('LOW_PRIORITY')
            ->option('UNDEFINED')
            ->option(array('QUICK'));
        is('' . $delete, "DELETE LOW_PRIORITY QUICK FROM `table`");
    }

    public function testWhere()
    {
        $delete = $this->_delete('table')
            ->where(array('table.prop', 'IN', '?a'), array(1, 2.3, true, false, null));
        is('' . $delete, "DELETE FROM `table` WHERE `table`.`prop` IN ('1', '2.3', TRUE, FALSE, NULL)");
    }

    public function testOrder()
    {
        $delete = $this->_delete('table')->order('table.prop', 'desc');
        is('' . $delete, "DELETE FROM `table` ORDER BY `table`.`prop` DESC");

        $delete = $delete->order('table.prop2', 'ASC');
        is('' . $delete, "DELETE FROM `table` ORDER BY `table`.`prop` DESC, `table`.`prop2` ASC");
    }

    public function testLimit()
    {
        $delete = $this->_delete('table')->order('table.prop', 'desc')->limit(1);
        is('' . $delete, "DELETE FROM `table` ORDER BY `table`.`prop` DESC LIMIT 1");
    }
}
