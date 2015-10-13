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

namespace JBZoo\SqlBuilder\Driver;

/**
 * Class DriverMySQLi
 * @package JBZoo\SqlBuilder\Driver
 */
class MySQLi extends Driver
{
    /**
     * @param string     $text
     * @param bool|false $extra
     * @return string
     */
    public function escape($text, $extra = false)
    {
        $result = mysqli_real_escape_string($this->connection, (string)$text);

        if ($extra) {
            $result = addcslashes($result, '%_');
        }

        return $result;
    }

}
