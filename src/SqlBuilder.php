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
 * Class SqlBuilder
 * @package JBZoo\SqlBuilder
 */
class SqlBuilder
{
    /**
     * @var Driver\Driver
     */
    protected static $driver = null;

    /**
     * @param string $type
     * @param mixed  $connection
     * @return Driver\Driver
     * @throws Exception
     */
    static public function set($type, $connection)
    {
        $className = 'JBZoo\\SqlBuilder\\Driver\\' . ucfirst(strtolower($type));

        if (class_exists($className)) {
            self::$driver = new $className($connection);
        } else {
            throw new Exception('Undefined driver type - "' . $type . '"');
        }

        return self::$driver;
    }

    /**
     * @return Driver\Driver|null
     */
    static public function get()
    {
        return self::$driver;
    }

}
