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
    protected static $_driver = null;

    /**
     * @param string $type
     * @param mixed  $connection
     * @param string $tablePrefix
     * @return Driver\Driver
     * @throws Exception
     */
    public static function set($type, $connection = null, $tablePrefix = null)
    {
        $className = 'JBZoo\\SqlBuilder\\Driver\\' . ucfirst(strtolower($type));

        if (class_exists($className)) {
            self::$_driver = new $className($connection, $tablePrefix);
        } else {
            throw new Exception('Undefined driver type - "' . $type . '"');
        }

        return self::$_driver;
    }

    /**
     * @return Driver\Driver|null
     */
    public static function get()
    {
        return self::$_driver;
    }
}
