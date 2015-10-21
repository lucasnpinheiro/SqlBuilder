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
 * Class Union
 * @package JBZoo\SqlBuilder\Block
 */
class Union extends Block
{
    protected $_validMode = array('ALL', 'DISTINCT', '');

    /**
     * @param array|string $elements
     * @param null         $extra
     */
    public function append($elements, $extra = null)
    {
        $mode = strtoupper($extra);

        if (!in_array($mode, $this->_validMode, true)) {
            return;
        }

        if (!is_array($elements)) {
            $elements = array($elements);
        }

        foreach ($elements as $element) {
            if ($element instanceof \JBZoo\SqlBuilder\Query\Select) {
                $this->_conditions[] = array($element, $mode);
            }
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $result = array();
        foreach ($this->_conditions as $key => $condition) {
            /** @var Select $select */
            $select = $condition[0];
            $mode   = $condition[1];

            if ($key === 0) {
                $result[] = '(' . $select->__toString() . ')';

            } elseif (!$mode) {
                $result[] = 'UNION (' . $select->__toString() . ')';

            } else {
                $result[] = 'UNION ' . $mode . ' (' . $select->__toString() . ')';
            }
        }

        return implode(' ', $result);
    }
}
