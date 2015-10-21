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

namespace JBZoo\SqlBuilder\Block;

/**
 * Class Join
 * @package JBZoo\SqlBuilder\Block
 */
class Join extends Block
{
    /**
     * @var array
     */
    protected $_validTypes = array(
        'LEFT',
        'RIGHT',
        'INNER',
        'STRAIGHT_JOIN',
        'NATURAL LEFT',
        'NATURAL RIGHT',
    );

    /**
     * Appends element parts to the internal list.
     * @param string|array $elements
     * @param mixed        $extra
     * @return void
     */
    public function append($elements, $extra = null)
    {
        $type = strtoupper($extra);
        if (!in_array($type, $this->_validTypes, true)) {
            return;
        }

        $driver    = $this->_getDriver();
        $table     = $elements[0];
        $condition = $elements[1];

        if (is_array($table)) {
            $table = $driver->quoteName($table[0]) . ' AS ' . $driver->quoteName($table[1]);
        } else {
            $table = $driver->quoteName($table);
        }

        // cleanup and build conditions
        $condition = (array)$condition;
        $condition = array_filter(array_unique($condition));
        $condition = $type . ' JOIN ' . $table . ' ON (' . implode(' AND ', $condition) . ')';

        $this->_conditions[] = $condition;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return implode(' ', $this->_conditions);
    }
}
