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
 * Class MySQLi
 * @package JBZoo\SqlBuilder\Driver
 */
class MySQLi extends Driver
{
    /**
     * {@inheritdoc}
     */
    public function escape($text, $extra = false)
    {
        $result = mysqli_real_escape_string($this->_connection, (string)$text);

        if ($extra) {
            $result = addcslashes($result, '%_');
        }

        return $result;
    }
}
