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
 */

namespace JBZoo\SqlBuilder;

/**
 * Class Exception
 * @package JBZoo\SqlBuilder
 */
class BaseTest extends PHPUnit
{

    public function testShouldDoSomeStreetMagic()
    {
        $obj = new SqlBuilder();
        self::assertEquals('street magic', $obj->doSomeStreetMagic());
    }

    /**
     * @expectedException \JBZoo\SqlBuilder\Exception
     */
    public function testShouldShowException()
    {
        throw new Exception('Test message');
    }
}
