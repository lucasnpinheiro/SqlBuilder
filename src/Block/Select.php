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
 * Class Select
 * @package JBZoo\SqlBuilder\Block
 */
class Select extends Block
{
    /**
     * @param array|string $elements
     * @param null         $extra
     */
    public function append($elements, $extra = null)
    {
        if ($elements) {
            $quote  = (bool)$extra;
            $select = (array)$elements;

            if ($quote) {
                $select = $this->_getDriver()->quoteName($select);
            }

            $this->_conditions += $select;
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if (count($this->_conditions) === 0) {
            return '*';
        }

        return implode(', ', $this->_conditions);
    }
}
