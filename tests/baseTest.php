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

namespace JBZoo\SqlBuilder;

/**
 * Class BaseTest
 * @package JBZoo\SqlBuilder
 */
class BaseTest extends PHPUnit
{

    public function testDriverInit()
    {
        $driver = SqlBuilder::get();

        self::assertInstanceOf('\\JBZoo\\SqlBuilder\\Driver\\Driver', $driver);
    }
}
