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

use JBZoo\SqlBuilder\Query\Update;

/**
 * Class UpdateTest
 * @package JBZoo\SqlBuilder
 */
class UpdateTest extends PHPUnit
{
    /**
     * @param null $tableName
     * @return Update
     */
    protected function _update($tableName = null)
    {
        return new Update($tableName);
    }

    public function testCreate()
    {
        $update = $this->_update('table');
        is('' . $update, "UPDATE `table`");

        $update = $this->_update(array('table', 'tTable'));
        is('' . $update, "UPDATE `table` AS `tTable`");
    }

    public function testExpr()
    {
        $update = $this->_update('persondata')->set(array());
        is('' . $update, "UPDATE `persondata`");

        $update = $this->_update('persondata')
            ->set('age', 'age*2')
            ->set('qqqqq')// ignore
            ->set(array(
                'age'         => 'age+1',
                'items.price' => 'month.price',
                'bool'        => true,
                'null'        => null,
            ))
            ->set(array(
                'table.prop' => 'age+1',
                'prop_i'     => array('?i', 10.10),
                'prop_f'     => array('?f', "-15,680"),
                'prop_s'     => array('?s', "'querty'"),
                'prop_b'     => array('?b', 0),
            ))
            ->where(array('items.id', '= ?n'), 'month.id')
            ->order('items.id', 'desc')
            ->option('ignore')
            ->limit(10);

        is('' . $update, "UPDATE IGNORE `persondata`"
            . " SET"

            . " `age`=age*2,"
            . " `age`=age+1,"
            . " `items`.`price`=month.price,"
            . " `bool`=TRUE,"
            . " `null`=NULL,"

            . " `table`.`prop`=age+1,"
            . " `prop_i`=10,"
            . " `prop_f`=-15.68,"
            . " `prop_s`='\\'querty\\'',"
            . " `prop_b`=FALSE"

            . " WHERE `items`.`id` = `month`.`id`"
            . " ORDER BY `items`.`id` DESC"
            . " LIMIT 10"
        );
    }
}
