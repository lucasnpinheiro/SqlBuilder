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
 * Class Values
 * @package JBZoo\SqlBuilder\Block
 */
class Values extends Element
{
    /**
     * @param string $name
     * @param array  $elements
     * @param string $glue
     */
    public function __construct($name, $elements = array(), $glue = ',')
    {
        parent::__construct('', $elements, ', ');
    }

    /**
     * Appends element parts to the internal list.
     * @param string       $name
     * @param string|array $elements
     * @param mixed        $extra
     * @return void
     */
    public function append($name, $elements, $extra = null)
    {
        $elements = (array)$this->_getDriver()->quote($elements);

        foreach ($elements as $element) {
            $this->_conditions[] = $element;
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return 'VALUES (' . implode($this->_glue, $this->_conditions) . ')';
    }
}
