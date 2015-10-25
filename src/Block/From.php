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

use JBZoo\SqlBuilder\Exception;

/**
 * Class From
 * @package JBZoo\SqlBuilder\Block
 */
class From extends Block
{
    /**
     * @param array|string $elements
     * @param null         $extra
     * @throws Exception
     */
    public function append($elements, $extra = null)
    {
        $alias = $extra;

        if (is_array($elements)) {
            if (count($elements) == 2) {
                $tableName = $elements[0];
                $alias     = $elements[1];
            } else {
                $tableName = $elements[0];
            }
        } else {
            $tableName = $elements;
        }

        if (!$tableName) {
            throw new Exception('Table name is undefined');
        }

        $tableName = $this->_getDriver()->quoteName($tableName);
        if ($alias) {
            $tableName .= ' AS ' . $this->_getDriver()->quoteName($alias);
        }

        $this->_conditions[] = $tableName;
    }

    /**
     * Magic function to convert the query element to a string
     * @return string
     */
    public function __toString()
    {
        if (count($this->_conditions) === 0) {
            return '';
        }

        return 'FROM ' . implode(', ', $this->_conditions);
    }
}
