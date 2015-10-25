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
 * Class WhereGroup
 * @package JBZoo\SqlBuilder
 */
class WhereGroup extends Block
{
    /**
     * @var Where
     */
    protected $_where = null;

    /**
     * Appends element parts to the internal list.
     * @param string|array $elements
     * @param mixed        $extra
     * @return $this
     */
    public function append($elements, $extra = null)
    {
        $conditions = (array)$elements;

        if (!$this->_where) {
            $this->_where = new Where();
        }

        foreach ($conditions as $condition) {
            $logicInner = 'AND';
            $value      = null;

            if (is_array($condition)) {
                $value      = isset($condition[1]) ? $condition[1] : null;
                $logicInner = isset($condition[2]) ? $condition[2] : 'AND';
                $condition  = $condition[0];
            }

            $this->_where->append(array($condition, $value), $logicInner);
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $conditions = $this->_where->getConditions();
        if (count($conditions) === 0) {
            return '';
        }

        return implode(' ', $conditions);
    }
}
