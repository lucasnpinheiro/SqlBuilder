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
 * Class Limit
 * @package JBZoo\SqlBuilder\Block
 */
class Limit extends Block
{
    /**
     * @param array|string $elements
     * @param null         $extra
     */
    public function append($elements, $extra = null)
    {
        $this->_conditions = array();

        $length = (int)$elements;
        $offset = (int)$extra;

        if ($offset) {
            $this->_conditions = array($offset, $length);
        } elseif ($length) {
            $this->_conditions = array($length);
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if (!$this->_conditions) {
            return '';
        }

        return 'LIMIT ' . implode(', ', $this->_conditions);
    }
}
