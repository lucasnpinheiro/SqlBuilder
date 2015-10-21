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

use JBZoo\SqlBuilder\Query\Union;
use JBZoo\SqlBuilder\Query\Select;

/**
 * Class UnionTest
 * @package JBZoo\SqlBuilder
 */
class UnionTest extends PHPUnit
{
    public function testUnion()
    {
        $union = new Union();
        is('' . $union, '');

        $select1 = new Select('table1');
        $select2 = new Select('table2');
        $select3 = new Select('table3');
        $select4 = new Select('table4');

        $union
            ->union($select1, 'ALL')
            ->union(array($select2, $select3), 'distinct')
            ->union($select4)
            ->union($select4, 'undefined')
            ->limit(10)
            ->order('id');

        is('' . $union, "(SELECT * FROM `table1`)"
            . " UNION DISTINCT (SELECT * FROM `table2`)"
            . " UNION DISTINCT (SELECT * FROM `table3`)"
            . " UNION (SELECT * FROM `table4`)"
            . " ORDER BY `id` ASC"
            . " LIMIT 10");
    }
}
