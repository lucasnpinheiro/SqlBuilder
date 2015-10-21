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
 * Class Where
 * @package JBZoo\SqlBuilder\Block
 */
class Where extends Block
{
    protected $_type = 'WHERE';

    /**
     * @var array
     */
    protected $_logicValid = array('AND', 'OR');

    /**
     * Appends element parts to the internal list.
     * @param string|array $elements
     * @param mixed        $extra
     * @return void
     */
    public function append($elements, $extra = null)
    {
        $elements = $this->_getDriver()->clean($elements[0], $elements[1]);

        $elements = (array)$elements;
        $elements = array_filter(array_unique($elements));

        if (!$elements) {
            return;
        }

        $extra = trim(strtoupper($extra));
        if (in_array($extra, $this->_logicValid, true)) {
            if (count($this->_conditions) === 0) {
                $result = $elements[0]; // default

            } else {
                $result = $extra . ' ' . $elements[0]; // default
            }

            $this->_conditions = array_merge($this->_conditions, (array)$result);
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

        return $this->_type . ' ' . implode(' ', $this->_conditions);
    }
}
