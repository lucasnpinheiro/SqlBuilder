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
 * Class InsertMulti
 * @package JBZoo\SqlBuilder\Block
 */
class InsertMulti extends Block
{
    /**
     * @var array
     */
    protected $_columns = array();

    /**
     * Appends element parts to the internal list.
     * @param string|array $elements
     * @param mixed        $extra
     * @return void
     */
    public function append($elements, $extra = null)
    {
        $elements = (array)$elements;
        reset($elements);
        if ($header = current($elements)) {
            $this->_columns = array_keys($header);
        }

        if ($maxLength = count($this->_columns)) {
            $index = 0;
            foreach ($elements as $data) {
                $data      = array_values((array)$data);
                $dataCount = count($data);

                if ($dataCount < $maxLength) {
                    $data = $data + array_fill($dataCount, $maxLength - $dataCount, null);

                } elseif ($dataCount > $maxLength) {
                    $data = array_slice($data, 0, $maxLength);
                }

                $this->_conditions[$index++] = $data;
            }
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if (count($this->_conditions) === 0 || count($this->_columns) === 0) {
            return '';
        }

        $driver = $this->_getDriver();
        $colums = $driver->quoteName($this->_columns);

        $values = array();
        foreach ($this->_conditions as $data) {
            $values[] = '(' . implode(', ', $driver->quote($data)) . ')';
        }

        return '(' . implode(', ', $colums) . ') VALUES ' . implode(', ', $values);
    }
}
