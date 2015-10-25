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
 * Class Order
 * @package JBZoo\SqlBuilder\Block
 */
class Order extends Block
{
    protected $_validList = array('ASC', 'DESC');

    /**
     * Appends element parts to the internal list.
     * @param string|array $elements
     * @param mixed        $extra
     * @return void
     */
    public function append($elements, $extra = null)
    {
        $column    = $elements[0];
        $direction = strtoupper($elements[1]);
        $quote     = (bool)$extra;

        if (!in_array($direction, $this->_validList, true)) {
            return;
        }

        if ($column) {
            if ($quote) {
                $column = $this->_getDriver()->quoteName($column);
            }

            $this->_conditions[$column] = $column . ' ' . $direction;
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if (count($this->_conditions) === 0) {
            return '';
        }

        return 'ORDER BY ' . implode(', ', $this->_conditions);
    }
}
