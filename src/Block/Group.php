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
 * Class Group
 * @package JBZoo\SqlBuilder\Block
 */
class Group extends Block
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
        $quote  = (bool)$extra;
        $column = $elements;

        if ($column) {
            if ($quote) {
                $column = $this->_getDriver()->quoteName($elements);
            }

            $this->_conditions[] = $column;
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return 'GROUP BY ' . implode(', ', $this->_conditions);
    }
}
